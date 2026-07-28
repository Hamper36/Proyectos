<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$id_usuario = $_SESSION["id_usuario"];
$id_traslado = $_POST["id_traslado"];
$motivo = trim($_POST["motivo_cancelacion"]);

/*=========================
OBTENER id_cliente
=========================*/

$sql = "SELECT id_cliente FROM cliente WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_cliente = $stmt->get_result()->fetch_assoc()["id_cliente"];

/*=========================
OBTENER DATOS DEL TRASLADO
(solo si pertenece a este cliente y sigue "Pendiente")
=========================*/

$sql = "SELECT costo FROM traslado WHERE id_traslado = ? AND id_cliente = ? AND estado_traslado = 'Pendiente'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_traslado, $id_cliente);
$stmt->execute();
$traslado = $stmt->get_result()->fetch_assoc();

if (!$traslado) {
    die("<h3 style='color:red;'>Este traslado ya no puede cancelarse.</h3>");
}

$costo = $traslado["costo"];
$motivo_completo = "Cancelado por el cliente: " . $motivo;

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

header("Location: ../vistas/cliente/mis_traslados.php?ok=cancelado");
exit();
?>