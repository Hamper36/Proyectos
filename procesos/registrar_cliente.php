<?php

include("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    die("Acceso no permitido.");

}

/*=========================
RECIBIR DATOS DEL FORMULARIO
=========================*/

$nombres = trim($_POST["nombre"]);
$apellidos = trim($_POST["apellido"]);
$cedula = trim($_POST["cedula"]);
$telefono = trim($_POST["telefono"]);
$correo = trim($_POST["correo"]);
$usuario = trim($_POST["usuario"]);
$direccion = trim($_POST["direccion"]);

$password = $_POST["password"];
$confirmar = $_POST["confirmar_password"];

/*=========================
VALIDAR CONTRASEÑAS
=========================*/

if ($password != $confirmar) {

    die("<h3 style='color:red;'>Las contraseñas no coinciden.</h3>");

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
OBTENER ID DEL USUARIO CREADO
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

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

/*=========================
MENSAJE DE ÉXITO
=========================*/

echo "

<div style='text-align:center;margin-top:70px;'>

<h2 style='color:green;'>

Cuenta creada correctamente

</h2>

<p>

Tu cuenta ha sido registrada exitosamente.

</p>

<p>

Ya puedes iniciar sesión en Decarrerita.

</p>

<br>

<a href='../vistas/login/login.php' class='btn btn-login'>

Ir al inicio de sesión

</a>

</div>

";
?>

