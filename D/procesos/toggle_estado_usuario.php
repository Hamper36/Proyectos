<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$id_usuario = $_POST["id_usuario"];
$nuevo_estado = $_POST["nuevo_estado"];

/*=========================
SOLO PERMITIR ALTERNAR ENTRE
Activo <-> Inactivo
(no toca Pendiente ni Rechazado,
 esos dependen de sus propios flujos)
=========================*/

if (!in_array($nuevo_estado, ["Activo", "Inactivo"])) {
    die("Estado inválido.");
}

$sql = "UPDATE usuario SET estado_usuario = ? WHERE id_usuario = ? AND estado_usuario IN ('Activo', 'Inactivo')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $nuevo_estado, $id_usuario);
$stmt->execute();

$_SESSION["mensaje_exito"] = "Estado del usuario actualizado correctamente.";
header("Location: ../vistas/administrador/usuarios.php");
exit();
?>