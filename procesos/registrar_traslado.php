<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../vistas/login/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Acceso no permitido.");
}

$id_usuario = $_SESSION["id_usuario"];
$punto_origen = trim($_POST["punto_origen"]);
$punto_destino = trim($_POST["punto_destino"]);
$fecha_traslado = trim($_POST["fecha_traslado"]);
$hora_traslado = trim($_POST["hora_traslado"]);
$fecha_hora = $fecha_traslado . " " . $hora_traslado . ":00";
$costo = 40.00; // Tarifa fija

/*=========================
OBTENER id_cliente Y SALDO
=========================*/

$sql = "SELECT id_cliente, saldo FROM cliente WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$cliente = $resultado->fetch_assoc();

$id_cliente = $cliente["id_cliente"];
$saldo = $cliente["saldo"];

/*=========================
VALIDAR SALDO SUFICIENTE
=========================*/

if ($saldo < $costo) {
    header("Location: ../vistas/cliente/solicitar_traslado.php?error=saldo");
    exit();
}

/*=========================
BUSCAR CHOFERES DISPONIBLES
(sin traslados "En curso" actualmente)
=========================*/

$sql = "SELECT c.id_chofer, v.id_vehiculo
        FROM chofer c
        INNER JOIN vehiculo v ON v.id_chofer = c.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        WHERE u.estado_usuario = 'Activo'
        AND c.id_chofer NOT IN (
            SELECT id_chofer FROM traslado WHERE estado_traslado = 'En curso'
        )";

$resultado = $conexion->query($sql);
$choferes_disponibles = [];

while ($fila = $resultado->fetch_assoc()) {
    $choferes_disponibles[] = $fila;
}

if (count($choferes_disponibles) == 0) {
    header("Location: ../vistas/cliente/solicitar_traslado.php?error=sin_choferes");
    exit();
}

/*=========================
ASIGNAR CHOFER ALEATORIO
=========================*/

$chofer_asignado = $choferes_disponibles[array_rand($choferes_disponibles)];
$id_chofer = $chofer_asignado["id_chofer"];
$id_vehiculo = $chofer_asignado["id_vehiculo"];

/*=========================
REGISTRAR TRASLADO
(el trigger trg_traslado_montos calcula
 monto_empresa y monto_chofer automáticamente)
=========================*/

$sql = "INSERT INTO traslado
        (id_cliente, id_chofer, id_vehiculo, fecha_hora, punto_origen, punto_destino, costo, monto_empresa, monto_chofer, estado_traslado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 'En curso')";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiisssd", $id_cliente, $id_chofer, $id_vehiculo, $fecha_hora, $punto_origen, $punto_destino, $costo);
$stmt->execute();

/*=========================
DESCONTAR SALDO DEL CLIENTE
=========================*/

$sql = "UPDATE cliente SET saldo = saldo - ? WHERE id_cliente = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("di", $costo, $id_cliente);
$stmt->execute();

header("Location: ../vistas/cliente/mis_traslados.php?ok=1");
exit();
?>