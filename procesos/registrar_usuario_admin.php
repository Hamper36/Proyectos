<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST["nombre_usuario"] ?? '');
    $contrasena     = trim($_POST["contrasena"] ?? '');
    $id_rol         = intval($_POST["id_rol"] ?? 0);
    $correo         = trim($_POST["correo"] ?? '');
    $cedula         = trim($_POST["cedula"] ?? '');
    $nombres        = trim($_POST["nombres"] ?? '');
    $apellidos      = trim($_POST["apellidos"] ?? '');
    $telefono       = trim($_POST["telefono"] ?? '');
    $direccion      = trim($_POST["direccion"] ?? '');
    $estado_usuario = trim($_POST["estado_usuario"] ?? 'Activo');

    if (empty($nombre_usuario) || empty($contrasena) || empty($correo) || empty($cedula) || empty($nombres) || empty($apellidos) || $id_rol <= 0) {
        $_SESSION["mensaje_error"] = "Todos los campos obligatorios deben ser completados.";
        header("Location: ../vistas/administrador/usuarios.php");
        exit();
    }

    // Insert into usuario
    $sql = "INSERT INTO usuario (id_rol, nombre_usuario, contrasena, correo, cedula, nombres, apellidos, telefono, direccion, estado_usuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isssssssss", $id_rol, $nombre_usuario, $contrasena, $correo, $cedula, $nombres, $apellidos, $telefono, $direccion, $estado_usuario);

    if ($stmt->execute()) {
        $nuevo_id_usuario = $stmt->insert_id;

        if ($id_rol == 3) { // Cliente
            $stmt_c = $conexion->prepare("INSERT INTO cliente (id_usuario, saldo) VALUES (?, 0.00)");
            $stmt_c->bind_param("i", $nuevo_id_usuario);
            $stmt_c->execute();
        } else if ($id_rol == 4) { // Chofer
            $stmt_ch = $conexion->prepare("INSERT INTO chofer (id_usuario, id_banco, numero_cuenta) VALUES (?, 1, '00000000000000000000')");
            $stmt_ch->bind_param("i", $nuevo_id_usuario);
            $stmt_ch->execute();
        } else if ($id_rol == 2) { // Personal Administrativo
            $stmt_p = $conexion->prepare("INSERT INTO personal_administrativo (id_usuario) VALUES (?)");
            $stmt_p->bind_param("i", $nuevo_id_usuario);
            $stmt_p->execute();
        }

        $_SESSION["mensaje_exito"] = "Usuario registrado exitosamente.";
    } else {
        $_SESSION["mensaje_error"] = "Error al registrar usuario: " . $conexion->error;
    }
}

header("Location: ../vistas/administrador/usuarios.php");
exit();
