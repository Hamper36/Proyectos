<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || ($_SESSION["id_rol"] != 1 && $_SESSION["id_rol"] != 2)) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = intval($_POST["id_usuario"] ?? 0);
    $nuevo_estado = trim($_POST["estado_usuario"] ?? '');

    $estados_validos = ['Activo', 'Pendiente', 'Suspendido', 'Rechazado'];

    if ($id_usuario > 0 && in_array($nuevo_estado, $estados_validos)) {
        $stmt = $conexion->prepare("UPDATE usuario SET estado_usuario = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nuevo_estado, $id_usuario);
        if ($stmt->execute()) {
            $_SESSION["mensaje_exito"] = "El estado del usuario ha sido actualizado correctamente.";
        } else {
            $_SESSION["mensaje_error"] = "Error al actualizar el estado del usuario.";
        }
    }
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '../vistas/administrador/usuarios.php';
header("Location: " . $redirect);
exit();
