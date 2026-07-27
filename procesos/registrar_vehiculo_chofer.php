<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION["id_usuario"];
    $placa = strtoupper(trim($_POST["placa"] ?? ''));
    $marca = trim($_POST["marca"] ?? '');
    $modelo = trim($_POST["modelo"] ?? '');
    $anio = intval($_POST["anio"] ?? 0);
    $color = trim($_POST["color"] ?? '');

    $res_ch = mysqli_query($conexion, "SELECT id_chofer FROM chofer WHERE id_usuario = $id_usuario");
    $id_chofer = mysqli_fetch_assoc($res_ch)['id_chofer'] ?? 0;

    if ($id_chofer > 0 && !empty($placa) && !empty($marca) && !empty($modelo) && $anio > 1900 && !empty($color)) {
        $stmt = $conexion->prepare("INSERT INTO vehiculo (id_chofer, placa, marca, modelo, anio, color) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssis", $id_chofer, $placa, $marca, $modelo, $anio, $color);

        if ($stmt->execute()) {
            $_SESSION["mensaje_exito"] = "Vehículo con placa $placa registrado exitosamente. Queda pendiente por revisión técnica.";
        } else {
            $_SESSION["mensaje_error"] = "Error al registrar vehículo: " . $conexion->error;
        }
    } else {
        $_SESSION["mensaje_error"] = "Por favor complete todos los datos requeridos del vehículo.";
    }
}

header("Location: ../vistas/chofer/mis_vehiculos.php");
exit();
