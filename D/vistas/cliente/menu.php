<?php
session_start();
include("../../config/conexion.php");

/*=========================
PROTEGER LA PÁGINA
=========================*/

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER DATOS DEL CLIENTE
=========================*/

$sql = "SELECT c.id_cliente, c.saldo, u.estado_usuario
        FROM cliente c
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        WHERE c.id_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$cliente = $resultado->fetch_assoc();

$id_cliente = $cliente["id_cliente"];
$saldo = $cliente["saldo"];
$estado = $cliente["estado_usuario"];

/*=========================
CONTAR TRASLADOS REALIZADOS
=========================*/

$sql = "SELECT COUNT(*) AS total FROM traslado WHERE id_cliente = ? AND estado_traslado = 'Finalizado'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();
$viajes = $resultado->fetch_assoc()["total"];

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_cliente.php"); ?>

        <div class="col-md-10 p-4 dashboard">

            <div class="text-center mt-3 mb-3">
                <img src="../../assets/img/logo.png" width="370" class="logo-dashboard">
            </div>

            <div class="text-center mb-4">
                <h2 class="fw-bold">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION["nombres"]); ?>
                </h2>
                <p class="text-secondary">
                    Consulta tus viajes y administra tu información desde este panel.
                </p>
            </div>

            <div class="row justify-content-center g-4">

                <!-- Saldo -->
                <div class="col-md-4 col-lg-3">
                    <div class="card card-vehiculos text-center">
                        <div class="card-body">
                            <img src="../../assets/img/ganancias.png" width="70" class="mb-3">
                            <h3>$<?php echo number_format($saldo, 2); ?></h3>
                            <h5>Saldo</h5>
                            <p class="text-secondary mb-0">Disponible</p>
                        </div>
                    </div>
                </div>

                <!-- Viajes -->
                <div class="col-md-4 col-lg-3">
                    <div class="card card-traslados text-center">
                        <div class="card-body">
                            <img src="../../assets/img/traslados.png" width="70" class="mb-3">
                            <h3><?php echo $viajes; ?></h3>
                            <h5>Viajes</h5>
                            <p class="text-secondary mb-0">Realizados</p>
                        </div>
                    </div>
                </div>

                <!-- Estado -->
                <div class="col-md-4 col-lg-3">
                    <div class="card card-ganancias text-center">
                        <div class="card-body">
                            <img src="../../assets/img/cliente/estado.png" width="70" class="mb-3">
                            <h3><?php echo htmlspecialchars($estado); ?></h3>
                            <h5>Estado</h5>
                            <p class="text-secondary mb-0">Cuenta</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>