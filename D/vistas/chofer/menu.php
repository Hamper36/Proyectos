<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER id_chofer
=========================*/

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
VIAJES ASIGNADOS (En curso)
=========================*/

$sql = "SELECT COUNT(*) AS total FROM traslado WHERE id_chofer = ? AND estado_traslado = 'En curso'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$viajes_asignados = $stmt->get_result()->fetch_assoc()["total"];

/*=========================
VIAJES COMPLETADOS (Finalizado)
=========================*/

$sql = "SELECT COUNT(*) AS total FROM traslado WHERE id_chofer = ? AND estado_traslado = 'Finalizado'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$viajes_completados = $stmt->get_result()->fetch_assoc()["total"];

/*=========================
GANANCIAS TOTALES
(suma de monto_chofer de traslados finalizados)
=========================*/

$sql = "SELECT COALESCE(SUM(monto_chofer), 0) AS total FROM traslado WHERE id_chofer = ? AND estado_traslado = 'Finalizado'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$ganancias_totales = $stmt->get_result()->fetch_assoc()["total"];

/*=========================
YA PAGADO
=========================*/

$sql = "SELECT COALESCE(SUM(monto_pagado), 0) AS total FROM pago_chofer WHERE id_chofer = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$ya_pagado = $stmt->get_result()->fetch_assoc()["total"];

/*=========================
PENDIENTE POR COBRAR
=========================*/

$pendiente_cobrar = $ganancias_totales - $ya_pagado;

/*=========================
ESTADO ACTUAL
(Disponible si no tiene traslados "En curso")
=========================*/

$estado_actual = ($viajes_asignados > 0) ? "En viaje" : "Disponible";

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_chofer.php"); ?>

        <div class="col-md-10 p-4 dashboard">

            <div class="text-center mt-3 mb-3">
                <img src="../../assets/img/logo.png" width="370" class="logo-dashboard">
            </div>

            <div class="text-center mb-4">
                <h2 class="fw-bold">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION["nombres"]); ?>
                </h2>
                <p class="text-secondary">
                    Consulta tus viajes asignados, revisa tu historial y administra tu información.
                </p>
            </div>

            <div class="row">

               <div class="col mb-4">
                    <div class="card card-usuarios text-center">
                        <div class="card-body">
                            <img src="../../assets/img/traslados.png" width="60" class="mb-3">
                            <h3><?php echo $viajes_asignados; ?></h3>
                            <h5>Viajes</h5>
                            <p class="text-secondary mb-0">Asignados</p>
                        </div>
                    </div>
                </div>

                <div class="col mb-4">
                    <div class="card card-vehiculos text-center">
                        <div class="card-body">
                            <img src="../../assets/img/chofer/completados.png" width="60" class="mb-3">
                            <h3><?php echo $viajes_completados; ?></h3>
                            <h5>Viajes</h5>
                            <p class="text-secondary mb-0">Completados</p>
                        </div>
                    </div>
                </div>

               <div class="col mb-4">
    <div class="card card-ganancias text-center">
        <div class="card-body">
            <img src="../../assets/img/ganancias.png" width="60" class="mb-3">
            <h3>$<?php echo number_format($ganancias_totales, 2); ?></h3>
            <h5>Ganancias</h5>
            <p class="text-secondary mb-0">Totales</p>
        </div>
    </div>
</div>

<div class="col mb-4">
    <div class="card card-ganancias text-center">
        <div class="card-body">
            <img src="../../assets/img/ganancias.png" width="60" class="mb-3">
            <h3>$<?php echo number_format($pendiente_cobrar, 2); ?></h3>
            <h5>Pendiente</h5>
            <p class="text-secondary mb-0">Por cobrar</p>
        </div>
    </div>
</div>

                <div class="col mb-4">
                    <div class="card card-traslados text-center">
                        <div class="card-body">
                            <img src="../../assets/img/chofer/disponible.png" width="60" class="mb-3">
                            <h3><?php echo $estado_actual; ?></h3>
                            <h5>Estado</h5>
                            <p class="text-secondary mb-0">Actual</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>