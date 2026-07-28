<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$id_usuario_sesion = $_SESSION["id_usuario"];
$id_vehiculo = $_POST["id_vehiculo"];
$fecha_revision = $_POST["fecha_revision"];
$calificacion = intval($_POST["calificacion"]);
$observacion = trim($_POST["observacion"]);

if ($calificacion < 0 || $calificacion > 100) {
    die("<h3 style='color:red;'>La calificación debe estar entre 0 y 100.</h3>");
}

$sql = "SELECT id_personal FROM personal_administrativo WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario_sesion);
$stmt->execute();
$id_personal = $stmt->get_result()->fetch_assoc()["id_personal"];

/*=========================
REGISTRAR REVISIÓN
(el trigger calcula "resultado" automáticamente)
=========================*/

$sql = "INSERT INTO revision_vehiculo (id_vehiculo, id_personal, fecha_revision, calificacion, observacion, resultado)
        VALUES (?, ?, ?, ?, ?, '')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iisis", $id_vehiculo, $id_personal, $fecha_revision, $calificacion, $observacion);
$stmt->execute();

$nuevo_estado = ($calificacion >= 65) ? "Apto" : "No Apto";

$sql = "UPDATE vehiculo SET estado_vehiculo = ? WHERE id_vehiculo = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $nuevo_estado, $id_vehiculo);
$stmt->execute();

/*=========================
ACTIVAR CUENTA SI AMBOS REQUISITOS (EVALUACIÓN Y REVISIÓN) ESTÁN APROBADOS
=========================*/

if ($nuevo_estado == "Apto") {
    $sql_c = "SELECT c.id_chofer, c.id_usuario FROM vehiculo v INNER JOIN chofer c ON c.id_chofer = v.id_chofer WHERE v.id_vehiculo = ?";
    $stmt_c = $conexion->prepare($sql_c);
    $stmt_c->bind_param("i", $id_vehiculo);
    $stmt_c->execute();
    $chofer_info = $stmt_c->get_result()->fetch_assoc();

    if ($chofer_info) {
        $id_chof = $chofer_info["id_chofer"];
        $id_usua = $chofer_info["id_usuario"];

        $sql_e = "SELECT calificacion FROM evaluacion_psicologica WHERE id_chofer = ? ORDER BY id_evaluacion DESC LIMIT 1";
        $stmt_e = $conexion->prepare($sql_e);
        $stmt_e->bind_param("i", $id_chof);
        $stmt_e->execute();
        $res_e = $stmt_e->get_result();

        if ($res_e->num_rows > 0) {
            $eval = $res_e->fetch_assoc();
            if ($eval["calificacion"] >= 73) {
                $sql_u = "UPDATE usuario SET estado_usuario = 'Activo' WHERE id_usuario = ?";
                $stmt_u = $conexion->prepare($sql_u);
                $stmt_u->bind_param("i", $id_usua);
                $stmt_u->execute();
            }
        }
    }
}

header("Location: ../vistas/personal/revisiones.php?ok=1");
exit();
?>