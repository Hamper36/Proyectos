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

$nombres = trim($_POST["nombres"]);
$apellidos = trim($_POST["apellidos"]);
$cedula = trim($_POST["cedula"]);
$nombre_usuario = trim($_POST["nombre_usuario"]);
$correo = trim($_POST["correo"]);
$contrasena = $_POST["contrasena"];
$telefono = trim($_POST["telefono"]);
$direccion = trim($_POST["direccion"]);

if (!preg_match('/^[0-9]{7,8}$/', $cedula)) {
    $_SESSION["mensaje_error"] = "La cédula debe contener entre 7 y 8 números.";
    header("Location: ../vistas/administrador/usuarios.php");
    exit();
}

if ($telefono !== "" && !preg_match('/^[0-9]{11}$/', $telefono)) {
    $_SESSION["mensaje_error"] = "El teléfono debe contener exactamente 11 números.";
    header("Location: ../vistas/administrador/usuarios.php");
    exit();
}

if (strlen($contrasena) < 4) {
    $_SESSION["mensaje_error"] = "Su contraseña debe ser de 4 caracteres o más.";
    header("Location: ../vistas/administrador/usuarios.php");
    exit();
}

/*=========================
VALIDAR DUPLICADOS
=========================*/

$sql = "SELECT id_usuario FROM usuario WHERE nombre_usuario = ? OR correo = ? OR cedula = ? OR (telefono != '' AND telefono = ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $nombre_usuario, $correo, $cedula, $telefono);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $_SESSION["mensaje_error"] = "Ya existe un usuario con ese nombre de usuario, correo o cédula.";
    header("Location: ../vistas/administrador/usuarios.php");
    exit();
}

/*=========================
REGISTRAR USUARIO (Personal Administrativo, Activo)
=========================*/

$id_rol = 2;
$estado_usuario = "Activo";

$sql = "INSERT INTO usuario
(id_rol, nombre_usuario, estado_usuario, contrasena, correo, cedula, nombres, apellidos, telefono, direccion)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "isssssssss",
    $id_rol,
    $nombre_usuario,
    $estado_usuario,
    $contrasena,
    $correo,
    $cedula,
    $nombres,
    $apellidos,
    $telefono,
    $direccion
);
$stmt->execute();

$id_usuario = $conexion->insert_id;

/*=========================
REGISTRAR EN personal_administrativo
=========================*/

$sql = "INSERT INTO personal_administrativo (id_usuario) VALUES (?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$_SESSION["mensaje_exito"] = "Personal administrativo registrado correctamente.";
header("Location: ../vistas/administrador/usuarios.php");
exit();
?>