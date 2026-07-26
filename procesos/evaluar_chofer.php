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

$fecha_revision = $_POST["fecha_revision"];
$calificacion_revision = $_POST["calificacion_revision"];
$observacion_revision = trim($_POST["observacion_revision"]);

$id_banco = $_POST["id_banco"];
$numero_cuenta = trim($_POST["numero_cuenta"]);

/*=========================
OBTENER id_personal DEL
PERSONAL ADMINISTRATIVO LOGUEADO
=========================*/

$sql = "SELECT id_personal FROM personal_administrativo WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario_sesion);
$stmt->execute();
$id_personal = $stmt->get_result()->fetch_assoc()["id_personal"];

/*=========================
REGISTRAR EVALUACIÓN PSICOLÓGICA
(el trigger calcula automáticamente
 el campo "resultado": Apto/No Apto)
=========================*/

$sql = "INSERT INTO evaluacion_psicologica
        (id_chofer, id_personal, fecha_evaluacion, calificacion, observacion, resultado)
        VALUES (?, ?, ?, ?, ?, '')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iisis", $id_chofer, $id_personal, $fecha_evaluacion, $calificacion_evaluacion, $observacion_evaluacion);
$stmt->execute();

/*=========================
REGISTRAR REVISIÓN VEHICULAR
(el trigger calcula automáticamente
 el campo "resultado": Apto/No Apto)
=========================*/

$sql = "INSERT INTO revision_vehiculo
        (id_vehiculo, id_personal, fecha_revision, calificacion, observacion, resultado)
        VALUES (?, ?, ?, ?, ?, '')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iisis", $id_vehiculo, $id_personal, $fecha_revision, $calificacion_revision, $observacion_revision);
$stmt->execute();

/*=========================
ACTUALIZAR DATOS BANCARIOS DEL CHOFER
=========================*/

$sql = "UPDATE chofer SET id_banco = ?, numero_cuenta = ? WHERE id_chofer = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("isi", $id_banco, $numero_cuenta, $id_chofer);
$stmt->execute();

/*=========================
DETERMINAR SI AMBOS RESULTADOS
SON "APTO" PARA ACTIVAR LA CUENTA
=========================*/

$apto_evaluacion = ($calificacion_evaluacion >= 73);
$apto_revision = ($calificacion_revision >= 65);

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