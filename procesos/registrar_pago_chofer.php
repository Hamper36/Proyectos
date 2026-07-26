<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || ($_SESSION["id_rol"] != 1 && $_SESSION["id_rol"] != 2)) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_chofer = intval($_POST["id_chofer"] ?? 0);
    $numero_referencia = trim($_POST["numero_referencia"] ?? '');
    $monto_pagado = floatval($_POST["monto_pagado"] ?? 0);
    $fecha_pago = trim($_POST["fecha_pago"] ?? date('Y-m-d'));

    // Get id_personal for logged in user, or create one if admin
    $id_usuario = $_SESSION["id_usuario"];
    $id_personal = null;

    $res_p = mysqli_query($conexion, "SELECT id_personal FROM personal_administrativo WHERE id_usuario = $id_usuario");
    if ($r = mysqli_fetch_assoc($res_p)) {
        $id_personal = $r["id_personal"];
    } else {
        // Find any existing personal_administrativo ID
        $res_any = mysqli_query($conexion, "SELECT id_personal FROM personal_administrativo LIMIT 1");
        if ($r_any = mysqli_fetch_assoc($res_any)) {
            $id_personal = $r_any["id_personal"];
        }
    }

    if ($id_chofer > 0 && !empty($numero_referencia) && $monto_pagado > 0 && $id_personal !== null) {
        $stmt = $conexion->prepare("INSERT INTO pago_chofer (id_chofer, id_personal, fecha_pago, numero_referencia, monto_pagado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissd", $id_chofer, $id_personal, $fecha_pago, $numero_referencia, $monto_pagado);

        if ($stmt->execute()) {
            $_SESSION["mensaje_exito"] = "Pago registrado exitosamente con la referencia " . htmlspecialchars($numero_referencia) . ".";
        } else {
            $_SESSION["mensaje_error"] = "Error al registrar el pago: " . $conexion->error;
        }
    } else {
        $_SESSION["mensaje_error"] = "Por favor complete todos los datos requeridos correctamente.";
    }
}

header("Location: ../vistas/administrador/pagos.php");
exit();
