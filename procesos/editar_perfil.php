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
$correo = trim($_POST["correo"]);
$telefono = trim($_POST["telefono"]);
$direccion = trim($_POST["direccion"]);

/*=========================
VALIDAR QUE EL CORREO NO ESTÉ
USADO POR OTRO USUARIO
=========================*/

$sql = "SELECT id_usuario FROM usuario WHERE correo = ? AND id_usuario != ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $correo, $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../vistas/cliente/mi_perfil.php?error=1");
    exit();
}

/*=========================
ACTUALIZAR DATOS
=========================*/

$sql = "UPDATE usuario SET correo = ?, telefono = ?, direccion = ? WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssi", $correo, $telefono, $direccion, $id_usuario);
$stmt->execute();

header("Location: ../vistas/cliente/mi_perfil.php?ok=1");
exit();
?>