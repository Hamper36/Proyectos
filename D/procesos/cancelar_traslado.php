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

/*=========================
DETECTAR SI LA PETICIÓN VIENE DE AJAX (fetch)
=========================*/
$es_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$id_traslado = $_POST["id_traslado"];
$motivo = trim($_POST["motivo_cancelacion"]);

/*=========================
OBTENER DATOS DEL TRASLADO
=========================*/

$sql = "SELECT id_cliente, costo, estado_traslado FROM traslado WHERE id_traslado = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_traslado);
$stmt->execute();
$traslado = $stmt->get_result()->fetch_assoc();

if (!$traslado || !in_array($traslado["estado_traslado"], ["Pendiente", "En curso"])) {
    if ($es_ajax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Este traslado ya no puede cancelarse."]);
        exit();
    }
    die("<h3 style='color:red;'>Este traslado ya no puede cancelarse.</h3>");
}

$id_cliente = $traslado["id_cliente"];
$costo = $traslado["costo"];

/*=========================
CANCELAR TRASLADO
=========================*/

$sql = "UPDATE traslado SET estado_traslado = 'Cancelado', motivo_cancelacion = ? WHERE id_traslado = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $motivo, $id_traslado);
$stmt->execute();

/*=========================
DEVOLVER SALDO AL CLIENTE
=========================*/

$sql = "UPDATE cliente SET saldo = saldo + ? WHERE id_cliente = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("di", $costo, $id_cliente);
$stmt->execute();

/*=========================
RESPUESTA
=========================*/

if ($es_ajax) {
    // La petición vino por fetch/AJAX: respondemos JSON, sin redirigir a ninguna página
    header('Content-Type: application/json');
    echo json_encode([
        "success" => true,
        "message" => "El traslado fue cancelado correctamente y el saldo fue devuelto al cliente."
    ]);
    exit();
}

/*=========================
REDIRECCIÓN SEGÚN ROL (solo si NO vino por AJAX)
(admin -> vistas/administrador/traslados.php | personal -> vistas/personal/traslados.php)
=========================*/

$redirect = ($_SESSION["id_rol"] == 1) ? "../vistas/administrador/traslados.php" : "../vistas/personal/traslados.php";
header("Location: $redirect?ok=1");
exit();
?>