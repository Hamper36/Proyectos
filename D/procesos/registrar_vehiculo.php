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
$placa = trim($_POST["placa"]);
$marca = trim($_POST["marca"]);
$modelo = trim($_POST["modelo"]);
$anio = trim($_POST["anio"]);
$color = trim($_POST["color"]);

if (!preg_match('/^[a-zA-Z0-9]{1,7}$/', $placa)) {
    die("<h3 style='color:red;'>La placa debe contener máximo 7 caracteres (números y letras).</h3>");
}

/*=========================
OBTENER id_chofer
=========================*/

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
VALIDAR PLACA REPETIDA
=========================*/

$sql = "SELECT id_vehiculo FROM vehiculo WHERE placa = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $placa);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../vistas/chofer/mis_vehiculos.php?error=placa");
    exit();
}

/*=========================
REGISTRAR VEHÍCULO
(estado_vehiculo queda "Pendiente" por defecto)
=========================*/

$sql = "INSERT INTO vehiculo (id_chofer, placa, marca, modelo, anio, color)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("isssis", $id_chofer, $placa, $marca, $modelo, $anio, $color);
$stmt->execute();

header("Location: ../vistas/chofer/mis_vehiculos.php?ok=registro");
exit();
?>