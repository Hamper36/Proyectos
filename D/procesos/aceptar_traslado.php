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

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
ACEPTAR TRASLADO
(solo si pertenece a este chofer y sigue "Pendiente")
=========================*/

$sql = "UPDATE traslado 
        SET estado_traslado = 'En curso' 
        WHERE id_traslado = ? AND id_chofer = ? AND estado_traslado = 'Pendiente'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_traslado, $id_chofer);
$stmt->execute();

header("Location: ../vistas/chofer/mis_traslados.php?ok=aceptado");
exit();
?>