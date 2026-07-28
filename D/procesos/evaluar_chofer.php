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

$id_chofer = $_POST["id_chofer"];
$id_usuario_chofer = $_POST["id_usuario"];
$id_vehiculo = $_POST["id_vehiculo"];

$fecha_evaluacion = $_POST["fecha_evaluacion"];
$calificacion_evaluacion = $_POST["calificacion_evaluacion"];
$observacion_evaluacion = trim($_POST["observacion_evaluacion"]);

$fecha_revision = isset($_POST["fecha_revision"]) ? $_POST["fecha_revision"] : null;
$calificacion_revision = isset($_POST["calificacion_revision"]) ? $_POST["calificacion_revision"] : null;
$observacion_revision = isset($_POST["observacion_revision"]) ? trim($_POST["observacion_revision"]) : "";

$id_banco = isset($_POST["id_banco"]) ? $_POST["id_banco"] : null;
$numero_cuenta = isset($_POST["numero_cuenta"]) ? trim($_POST["numero_cuenta"]) : null;

if ($calificacion_evaluacion < 0 || $calificacion_evaluacion > 100) {
    die("<h3 style='color:red;'>La calificación de la evaluación debe estar entre 0 y 100.</h3>");
}

if ($calificacion_revision !== null && $calificacion_revision !== "" && ($calificacion_revision < 0 || $calificacion_revision > 100)) {
    die("<h3 style='color:red;'>La calificación de la revisión debe estar entre 0 y 100.</h3>");
}

if ($numero_cuenta !== null && $numero_cuenta !== "") {
    if (!preg_match('/^[0-9]{1,20}$/', $numero_cuenta)) {
        die("<h3 style='color:red;'>El número de cuenta debe contener solo números y un máximo de 20 dígitos.</h3>");
    }
}

$sql = "SELECT id_personal FROM personal_administrativo WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario_sesion);
$stmt->execute();
$id_personal = $stmt->get_result()->fetch_assoc()["id_personal"];

/*=========================
REGISTRAR EVALUACIÓN PSICOLÓGICA
=========================*/

$sql = "INSERT INTO evaluacion_psicologica
        (id_chofer, id_personal, fecha_evaluacion, calificacion, observacion, resultado)
        VALUES (?, ?, ?, ?, ?, '')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iisis", $id_chofer, $id_personal, $fecha_evaluacion, $calificacion_evaluacion, $observacion_evaluacion);
$stmt->execute();

$apto_evaluacion = ($calificacion_evaluacion >= 73);
$apto_revision = true;

/*=========================
REGISTRAR REVISIÓN VEHICULAR (SI SE ENVIÓ)
=========================*/

if (!empty($fecha_revision) && !empty($calificacion_revision)) {
    $sql = "INSERT INTO revision_vehiculo
            (id_vehiculo, id_personal, fecha_revision, calificacion, observacion, resultado)
            VALUES (?, ?, ?, ?, ?, '')";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iisis", $id_vehiculo, $id_personal, $fecha_revision, $calificacion_revision, $observacion_revision);
    $stmt->execute();

    $apto_revision = ($calificacion_revision >= 65);
    $estado_vehiculo = $apto_revision ? "Apto" : "No Apto";

    $sql = "UPDATE vehiculo SET estado_vehiculo = ? WHERE id_vehiculo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $estado_vehiculo, $id_vehiculo);
    $stmt->execute();
} else {
    // Modo renovación: verificar si el vehículo ya está Apto
    $sql_v = "SELECT estado_vehiculo FROM vehiculo WHERE id_vehiculo = ?";
    $stmt_v = $conexion->prepare($sql_v);
    $stmt_v->bind_param("i", $id_vehiculo);
    $stmt_v->execute();
    $est_veh = $stmt_v->get_result()->fetch_assoc()["estado_vehiculo"];
    $apto_revision = ($est_veh == "Apto");
}

/*=========================
ACTUALIZAR DATOS BANCARIOS DEL CHOFER (SI SE ENVIARON)
=========================*/

if (!empty($id_banco) && !empty($numero_cuenta)) {
    $sql = "UPDATE chofer SET id_banco = ?, numero_cuenta = ? WHERE id_chofer = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isi", $id_banco, $numero_cuenta, $id_chofer);
    $stmt->execute();
}

/*=========================
DETERMINAR SI CUMPLE PARA ACTIVAR LA CUENTA
=========================*/

if ($apto_evaluacion && $apto_revision) {
    $nuevo_estado = "Activo";
} else {
    $nuevo_estado = "Rechazado";
}

$sql = "UPDATE usuario SET estado_usuario = ? WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $nuevo_estado, $id_usuario_chofer);
$stmt->execute();

header("Location: ../vistas/personal/gestionar_choferes.php?ok=1");
exit();
?>