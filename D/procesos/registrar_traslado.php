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

$hoy = date('Y-m-d');
if ($fecha_traslado < $hoy) {
    header("Location: ../vistas/cliente/solicitar_traslado.php?error=fecha_invalida");
    exit();
}

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
VALIDAR QUE NO TENGA OTRO TRASLADO ACTIVO
(Pendiente o En curso)
=========================*/

$sql = "SELECT id_traslado FROM traslado WHERE id_cliente = ? AND estado_traslado IN ('Pendiente', 'En curso')";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../vistas/cliente/solicitar_traslado.php?error=ya_tiene");
    exit();
}

/*=========================
BUSCAR CHOFERES DISPONIBLES
(activo, vehículo apto, sin traslados "En curso")
=========================*/

$sql = "SELECT c.id_chofer, v.id_vehiculo
        FROM chofer c
        INNER JOIN vehiculo v ON v.id_chofer = c.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        WHERE u.estado_usuario = 'Activo'
        AND v.estado_vehiculo = 'Apto'
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
(nace en "Pendiente", esperando que el chofer lo acepte;
 el trigger trg_traslado_montos calcula los montos)
=========================*/

$sql = "INSERT INTO traslado
        (id_cliente, id_chofer, id_vehiculo, fecha_hora, punto_origen, punto_destino, costo, monto_empresa, monto_chofer, estado_traslado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 'Pendiente')";

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