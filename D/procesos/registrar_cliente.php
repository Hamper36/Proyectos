<?php

include("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

/*=========================
RECIBIR DATOS DEL FORMULARIO
=========================*/

$nombres   = trim($_POST["nombre"]);
$apellidos = trim($_POST["apellido"]);
$cedula    = trim($_POST["cedula"]);
$telefono  = trim($_POST["telefono"]);
$correo    = trim($_POST["correo"]);
$usuario   = trim($_POST["usuario"]);
$direccion = trim($_POST["direccion"]);

$password   = $_POST["password"];
$confirmar  = $_POST["confirmar_password"];

/*=========================
VALIDAR CONTRASEÑAS
=========================*/

if ($password != $confirmar) {
    die("<h3 style='color:red;'>Las contraseñas no coinciden.</h3>");
}

if (strlen($password) < 4) {
    die("<h3 style='color:red;'>Su contraseña debe ser de 4 caracteres o más.</h3>");
}

/*=========================
VALIDAR FORMATO DE DATOS
=========================*/

if (!preg_match('/^[0-9]{7,8}$/', $cedula)) {
    die("<h3 style='color:red;'>La cédula debe contener entre 7 y 8 números.</h3>");
}

if (!preg_match('/^[0-9]{11}$/', $telefono)) {
    die("<h3 style='color:red;'>El teléfono debe contener exactamente 11 números.</h3>");
}

/*=========================
VALIDAR USUARIO REPETIDO
=========================*/

$sql = "SELECT id_usuario
        FROM usuario
        WHERE nombre_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>Ese nombre de usuario ya existe.</h3>");
}

/*=========================
VALIDAR CORREO REPETIDO
=========================*/

$sql = "SELECT id_usuario
        FROM usuario
        WHERE correo = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>Ese correo ya está registrado.</h3>");
}

/*=========================
VALIDAR CÉDULA REPETIDA
=========================*/

$sql = "SELECT id_usuario
        FROM usuario
        WHERE cedula = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $cedula);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>La cédula ya está registrada.</h3>");
}

/*=========================
VALIDAR TELÉFONO REPETIDO
=========================*/

$sql = "SELECT id_usuario
        FROM usuario
        WHERE telefono = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $telefono);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>El número de teléfono ya está registrado.</h3>");
}

/*=========================
REGISTRAR USUARIO
=========================*/

$id_rol = 3;
$estado_usuario = "Activo";

$sql = "INSERT INTO usuario
(
id_rol,
nombre_usuario,
estado_usuario,
contrasena,
correo,
cedula,
nombres,
apellidos,
telefono,
direccion
)
VALUES
(
?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
"isssssssss",
$id_rol,
$usuario,
$estado_usuario,
$password,
$correo,
$cedula,
$nombres,
$apellidos,
$telefono,
$direccion
);

$stmt->execute();

/*=========================
OBTENER ID DEL USUARIO
=========================*/

$id_usuario = $conexion->insert_id;

/*=========================
REGISTRAR CLIENTE
=========================*/

$sql = "INSERT INTO cliente
(
id_usuario,
saldo,
fecha_registro
)
VALUES
(
?,
0,
CURDATE()
)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

/*=========================
REDIRECCIÓN
=========================*/

header("Location: ../vistas/login/login.php?registro=cliente_ok");
exit();

?>
