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
$id_vehiculo = $_POST["id_vehiculo"];

/*=========================
OBTENER id_chofer
=========================*/

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
MARCAR COMO INACTIVO
(solo si el vehículo pertenece a este chofer)
=========================*/

$sql = "UPDATE vehiculo SET estado_vehiculo = 'Inactivo' WHERE id_vehiculo = ? AND id_chofer = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_vehiculo, $id_chofer);
$stmt->execute();

header("Location: ../vistas/chofer/mis_vehiculos.php?ok=retiro");
exit();
?>