<?php

include("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

/*=========================
RECIBIR DATOS DEL FORMULARIO
=========================*/

// Paso 1: Datos personales
$cedula = trim($_POST["cedula"]);
$correo = trim($_POST["correo"]);
$nombres = trim($_POST["nombres"]);
$apellidos = trim($_POST["apellidos"]);
$telefono = trim($_POST["telefono"]);
$direccion = trim($_POST["direccion"]);

// Paso 2: Datos de acceso
$nombre_usuario = trim($_POST["nombre_usuario"]);
$password = $_POST["password"];
$confirmar = $_POST["confirmar_password"];

// Paso 3: Vehículo
$placa = trim($_POST["placa"]);
$marca = trim($_POST["marca"]);
$modelo = trim($_POST["modelo"]);
$anio = trim($_POST["anio"]);
$color = trim($_POST["color"]);

// Paso 4: Contactos de emergencia (arrays)
$contacto_nombre = $_POST["contacto_nombre"];
$contacto_telefono = $_POST["contacto_telefono"];
$contacto_parentesco = $_POST["contacto_parentesco"];

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

if (!preg_match('/^[a-zA-Z0-9]{1,7}$/', $placa)) {
    die("<h3 style='color:red;'>La placa debe contener máximo 7 caracteres alfanuméricos.</h3>");
}

if (is_array($contacto_telefono)) {
    foreach ($contacto_telefono as $tel_c) {
        if (!preg_match('/^[0-9]{11}$/', trim($tel_c))) {
            die("<h3 style='color:red;'>Cada teléfono de contacto debe contener exactamente 11 números.</h3>");
        }
    }
}

/*=========================
VALIDAR MÍNIMO 2 CONTACTOS
=========================*/

if (count($contacto_nombre) < 2) {
    die("<h3 style='color:red;'>Debe registrar al menos dos contactos de emergencia.</h3>");
}

/*=========================
VALIDAR USUARIO REPETIDO
=========================*/

$sql = "SELECT id_usuario FROM usuario WHERE nombre_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>Ese nombre de usuario ya existe.</h3>");
}

/*=========================
VALIDAR CORREO REPETIDO
=========================*/

$sql = "SELECT id_usuario FROM usuario WHERE correo = ?";
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

$sql = "SELECT id_usuario FROM usuario WHERE cedula = ?";
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

$sql = "SELECT id_usuario FROM usuario WHERE telefono = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $telefono);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>El número de teléfono ya está registrado.</h3>");
}

/*=========================
VALIDAR PLACA REPETIDA
=========================*/

$sql = "SELECT id_vehiculo FROM vehiculo WHERE placa = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("<h3 style='color:red;'>Esa placa ya está registrada.</h3>");
}

/*=========================
REGISTRAR USUARIO (Pendiente)
=========================*/

$id_rol = 4; // Chofer
$estado_usuario = "Pendiente";

$sql = "INSERT INTO usuario
(id_rol, nombre_usuario, estado_usuario, contrasena, correo, cedula, nombres, apellidos, telefono, direccion)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "isssssssss",
    $id_rol,
    $nombre_usuario,
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

$id_usuario = $conexion->insert_id;

/*=========================
REGISTRAR CHOFER
(id_banco y numero_cuenta quedan NULL,
los carga el personal administrativo después)
=========================*/

$sql = "INSERT INTO chofer (id_usuario, fecha_registro) VALUES (?, CURDATE())";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$id_chofer = $conexion->insert_id;

/*=========================
REGISTRAR VEHÍCULO
=========================*/

$sql = "INSERT INTO vehiculo (id_chofer, placa, marca, modelo, anio, color)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "isssis",
    $id_chofer,
    $placa,
    $marca,
    $modelo,
    $anio,
    $color
);
$stmt->execute();

/*=========================
REGISTRAR CONTACTOS DE EMERGENCIA
=========================*/

$sql = "INSERT INTO contacto_emergencia (id_chofer, nombre, telefono, parentesco)
        VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);

for ($i = 0; $i < count($contacto_nombre); $i++) {

    $nombre_c = trim($contacto_nombre[$i]);
    $telefono_c = trim($contacto_telefono[$i]);
    $parentesco_c = trim($contacto_parentesco[$i]);

    // Ignorar contactos opcionales vacíos (3 y 4 si no se llenaron)
    if ($nombre_c == "" || $telefono_c == "" || $parentesco_c == "") {
        continue;
    }

    $stmt->bind_param("isss", $id_chofer, $nombre_c, $telefono_c, $parentesco_c);
    $stmt->execute();
}

/*=========================
MENSAJE DE ÉXITO
=========================*/

header("Location: ../vistas/login/login.php?registro=ok");
exit();

?>