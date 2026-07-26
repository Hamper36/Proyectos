<?php
session_start();
include("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$usuario = trim($_POST["usuario"]);
$password = trim($_POST["password"]);

/*=========================
BUSCAR USUARIO
=========================*/

$sql = "SELECT id_usuario, id_rol, nombre_usuario, contrasena, nombres, apellidos, estado_usuario
        FROM usuario
        WHERE nombre_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: ../vistas/login/login.php?error=credenciales");
    exit();
}

$fila = $resultado->fetch_assoc();

/*=========================
VALIDAR CONTRASEÑA
=========================*/

if ($password != $fila["contrasena"]) {
    header("Location: ../vistas/login/login.php?error=credenciales");
    exit();
}

/*=========================
VALIDAR ESTADO DE LA CUENTA
=========================*/

if ($fila["estado_usuario"] != "Activo") {
    header("Location: ../vistas/login/login.php?estado=pendiente");
    exit();
}

/*=========================
GUARDAR DATOS EN SESIÓN
=========================*/

$_SESSION["id_usuario"] = $fila["id_usuario"];
$_SESSION["id_rol"] = $fila["id_rol"];
$_SESSION["nombre_usuario"] = $fila["nombre_usuario"];
$_SESSION["nombres"] = $fila["nombres"];
$_SESSION["apellidos"] = $fila["apellidos"];

/*=========================
REDIRIGIR SEGÚN ROL
=========================*/

switch ($fila["id_rol"]) {
    case 1: // Administrador
        header("Location: ../vistas/administrador/menu.php");
        break;
    case 2: // Personal Administrativo
        header("Location: ../vistas/personal/menu.php");
        break;
    case 3: // Cliente
        header("Location: ../vistas/cliente/menu.php");
        break;
    case 4: // Chofer
        header("Location: ../vistas/chofer/menu.php");
        break;
    default:
        header("Location: ../vistas/login/login.php?error=rol");
}
exit();
?>