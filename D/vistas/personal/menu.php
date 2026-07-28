<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
CHOFERES REGISTRADOS (total)
=========================*/

$sql = "SELECT COUNT(*) AS total FROM chofer";
$choferes = $conexion->query($sql)->fetch_assoc()["total"];

/*=========================
EVALUACIONES PENDIENTES
(choferes cuya cuenta sigue en estado "Pendiente",
es decir, aún no se les ha cargado evaluación/revisión)
=========================*/

$sql = "SELECT COUNT(*) AS total
        FROM chofer c
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        WHERE u.estado_usuario = 'Pendiente'";
$evaluaciones_pendientes = $conexion->query($sql)->fetch_assoc()["total"];

/*=========================
REVISIONES PROGRAMADAS
(vehículos que aún no tienen ninguna revisión registrada)
=========================*/

$sql = "SELECT COUNT(*) AS total FROM vehiculo WHERE estado_vehiculo = 'Pendiente'";
$revisiones_programadas = $conexion->query($sql)->fetch_assoc()["total"];

/*=========================
PAGOS PROCESADOS (suma total)
=========================*/

$sql = "SELECT COALESCE(SUM(monto_pagado), 0) AS total FROM pago_chofer";
$pagos_procesados = $conexion->query($sql)->fetch_assoc()["total"];

/*=========================
PAGOS PENDIENTES
(traslados finalizados que aún no tienen pago registrado)
=========================*/

$sql = "SELECT COUNT(*) AS total
        FROM traslado t
        WHERE t.estado_traslado = 'Finalizado'
        AND t.id_traslado NOT IN (SELECT id_traslado FROM pago_chofer WHERE id_traslado IS NOT NULL)";
$pagos_pendientes = $conexion->query($sql)->fetch_assoc()["total"];

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_personal.php"); ?>

        <div class="col-md-10 p-4 dashboard">

            <div class="text-center mt-3 mb-3">
                <img src="../../assets/img/logo.png" width="370" class="logo-dashboard">
            </div>

            <div class="text-center mb-4">
                <h2 class="fw-bold">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION["nombres"]); ?>
                </h2>
                <p class="text-secondary">
                    Administra evaluaciones, revisiones, pagos y el registro de choferes.
                </p>
            </div>

            <div class="row">

                <div class="col-md-3 mb-4">
                    <div class="card card-usuarios text-center">
                        <div class="card-body">
                            <img src="../../assets/img/personal/choferes.png" width="80" class="mb-3">
                            <h3><?php echo $choferes; ?></h3>
                            <h5>Choferes</h5>
                            <p class="text-secondary mb-0">Registrados</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card card-vehiculos text-center">
                        <div class="card-body">
                            <img src="../../assets/img/personal/evaluaciones.png" width="70" class="mb-3">
                            <h3><?php echo $evaluaciones_pendientes; ?></h3>
                            <h5>Evaluaciones</h5>
                            <p class="text-secondary mb-0">Pendientes</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card card-traslados text-center">
                        <div class="card-body">
                            <img src="../../assets/img/personal/revisiones.png" width="70" class="mb-3">
                            <h3><?php echo $revisiones_programadas; ?></h3>
                            <h5>Revisiones</h5>
                            <p class="text-secondary mb-0">Programadas</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card card-Pagos text-center">
                        <div class="card-body">
                            <img src="../../assets/img/personal/Pagos.png" width="80" class="mb-3">
                            <h3>$<?php echo number_format($pagos_procesados, 2); ?></h3>
                            <h5>Pagos</h5>
                            <p class="text-secondary mb-0">Procesados</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row justify-content-center mt-3">
                <div class="col-md-4">
                    <div class="card shadow card-Pagos h-100">
                        <div class="card-body text-center">
                            <img src="../../assets/img/personal/Pagos.png" width="80" class="mb-3">
                            <h3><?php echo $pagos_pendientes; ?></h3>
                            <h5>Pagos</h5>
                            <p class="text-secondary mb-0">Pendientes</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>