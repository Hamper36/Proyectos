<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$id_usuario = $_SESSION["id_usuario"];
$id_traslado = $_POST["id_traslado"];
$motivo = trim($_POST["motivo_rechazo"]);

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
OBTENER DATOS DEL TRASLADO
(solo si pertenece a este chofer y sigue "Pendiente")
=========================*/

$sql = "SELECT id_cliente, costo FROM traslado WHERE id_traslado = ? AND id_chofer = ? AND estado_traslado = 'Pendiente'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_traslado, $id_chofer);
$stmt->execute();
$traslado = $stmt->get_result()->fetch_assoc();

if (!$traslado) {
    die("<h3 style='color:red;'>Este traslado ya no puede rechazarse.</h3>");
}

$id_cliente = $traslado["id_cliente"];
$costo = $traslado["costo"];
$motivo_completo = "Rechazado por el chofer: " . $motivo;

/*=========================
CANCELAR Y DEVOLVER SALDO
=========================*/

$sql = "UPDATE traslado SET estado_traslado = 'Cancelado', motivo_cancelacion = ? WHERE id_traslado = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $motivo_completo, $id_traslado);
$stmt->execute();

$sql = "UPDATE cliente SET saldo = saldo + ? WHERE id_cliente = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("di", $costo, $id_cliente);
$stmt->execute();

header("Location: ../vistas/chofer/mis_traslados.php?ok=rechazado");
exit();
?>