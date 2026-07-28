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
$id_banco = trim($_POST["id_banco"]);
$numero_referencia = trim($_POST["numero_referencia"]);
$fecha_recarga = trim($_POST["fecha_recarga"]);
$monto = trim($_POST["monto"]);

if (!preg_match('/^[0-9]{1,13}$/', $numero_referencia)) {
    die("<h3 style='color:red;'>El número de referencia debe contener solo números y máximo 13 dígitos.</h3>");
}

/*=========================
OBTENER id_cliente
=========================*/

$sql = "SELECT id_cliente FROM cliente WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$id_cliente = $resultado->fetch_assoc()["id_cliente"];

/*=========================
VALIDAR REFERENCIA REPETIDA
=========================*/

$sql = "SELECT id_recarga FROM recarga_saldo WHERE numero_referencia = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $numero_referencia);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    header("Location: ../vistas/cliente/recargar_saldo.php?error=1");
    exit();
}

/*=========================
REGISTRAR RECARGA
(el trigger trg_actualizar_saldo_recarga
 se encarga de sumar el monto al saldo del cliente)
=========================*/

$sql = "INSERT INTO recarga_saldo (id_cliente, id_banco, fecha_recarga, numero_referencia, monto)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iissd", $id_cliente, $id_banco, $fecha_recarga, $numero_referencia, $monto);
$stmt->execute();

header("Location: ../vistas/cliente/recargar_saldo.php?ok=1");
exit();
?>