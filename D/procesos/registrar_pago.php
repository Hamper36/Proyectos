<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || !in_array($_SESSION["id_rol"], [1, 2])) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$id_usuario_sesion = $_SESSION["id_usuario"];
$id_traslado = $_POST["id_traslado"];
$fecha_pago = $_POST["fecha_pago"];
$numero_referencia = trim($_POST["numero_referencia"]);

if (!preg_match('/^[0-9]{1,13}$/', $numero_referencia)) {
    die("<h3 style='color:red;'>El número de referencia debe contener solo números y máximo 13 dígitos.</h3>");
}

/*=========================
OBTENER id_personal
=========================*/

$sql = "SELECT id_personal FROM personal_administrativo WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario_sesion);
$stmt->execute();
$id_personal = $stmt->get_result()->fetch_assoc()["id_personal"];

/*=========================
OBTENER DATOS DEL TRASLADO
(el monto SIEMPRE se calcula aquí, nunca se confía
 en un valor que venga del formulario)
=========================*/

$sql = "SELECT id_chofer, monto_chofer, estado_traslado FROM traslado WHERE id_traslado = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_traslado);
$stmt->execute();
$traslado = $stmt->get_result()->fetch_assoc();

if (!$traslado || $traslado["estado_traslado"] != "Finalizado") {
    die("<h3 style='color:red;'>Traslado inválido.</h3>");
}

$id_chofer = $traslado["id_chofer"];
$monto_pagado = $traslado["monto_chofer"];

/*=========================
VALIDAR QUE NO SE HAYA PAGADO YA
=========================*/

$sql = "SELECT id_pago FROM pago_chofer WHERE id_traslado = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_traslado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../vistas/personal/pagos.php?error=ya_pagado");
    exit();
}

/*=========================
VALIDAR REFERENCIA REPETIDA
=========================*/

$sql = "SELECT id_pago FROM pago_chofer WHERE numero_referencia = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $numero_referencia);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../vistas/personal/pagos.php?error=referencia");
    exit();
}

/*=========================
REGISTRAR PAGO
(monto tomado directamente del traslado,
 no del formulario, para evitar errores humanos)
=========================*/

$sql = "INSERT INTO pago_chofer (id_chofer, id_personal, id_traslado, fecha_pago, numero_referencia, monto_pagado)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiissd", $id_chofer, $id_personal, $id_traslado, $fecha_pago, $numero_referencia, $monto_pagado);
$stmt->execute();

/*=========================
REDIRIGIR SEGÚN ROL
(personal administrativo vuelve a su pantalla,
 administrador a la suya)
=========================*/

if ($_SESSION["id_rol"] == 1) {
    header("Location: ../vistas/administrador/pagos.php?ok=1");
} else {
    header("Location: ../vistas/personal/pagos.php?ok=1");
}
exit();
?>