<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_traslado = intval($_POST["id_traslado"] ?? 0);
    $nuevo_estado = trim($_POST["nuevo_estado"] ?? '');

    $id_usuario = $_SESSION["id_usuario"];
    $res_ch = mysqli_query($conexion, "SELECT id_chofer FROM chofer WHERE id_usuario = $id_usuario");
    $id_chofer = mysqli_fetch_assoc($res_ch)['id_chofer'] ?? 0;

    $estados_validos = ['En curso', 'Finalizado', 'Cancelado'];

    if ($id_traslado > 0 && $id_chofer > 0 && in_array($nuevo_estado, $estados_validos)) {
        // Verify this traslado belongs to the chofer
        $stmt_check = $conexion->prepare("SELECT id_traslado, costo, monto_chofer, estado_traslado FROM traslado WHERE id_traslado = ? AND id_chofer = ?");
        $stmt_check->bind_param("ii", $id_traslado, $id_chofer);
        $stmt_check->execute();
        $traslado = $stmt_check->get_result()->fetch_assoc();

        if ($traslado) {
            $stmt_upd = $conexion->prepare("UPDATE traslado SET estado_traslado = ? WHERE id_traslado = ?");
            $stmt_upd->bind_param("si", $nuevo_estado, $id_traslado);
            if ($stmt_upd->execute()) {
                $_SESSION["mensaje_exito"] = "El estado del traslado #$id_traslado ha sido cambiado a '$nuevo_estado'.";
            } else {
                $_SESSION["mensaje_error"] = "Error al actualizar el estado del traslado.";
            }
        }
    }
}

header("Location: ../vistas/chofer/mis_traslados.php");
exit();
