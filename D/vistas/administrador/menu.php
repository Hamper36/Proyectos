<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
OBTENER ESTADÍSTICAS REALES
=========================*/

// Total Usuarios
$res_usr = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM usuario");
$total_usuarios = mysqli_fetch_assoc($res_usr)['total'] ?? 0;

// Total Vehículos
$res_veh = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM vehiculo");
$total_vehiculos = mysqli_fetch_assoc($res_veh)['total'] ?? 0;

// Total Traslados
$res_tras = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM traslado");
$total_traslados = mysqli_fetch_assoc($res_tras)['total'] ?? 0;

// Ganancias Totales Empresa
$res_gan = mysqli_query($conexion, "SELECT COALESCE(SUM(monto_empresa), 0) AS total FROM traslado WHERE estado_traslado = 'Finalizado'");
$total_ganancias = mysqli_fetch_assoc($res_gan)['total'] ?? 0;

include("../../includes/header.php");
?>

<div class="container-fluid">

    <div class="row">

        <?php include("../../includes/sidebar.php"); ?>

        <div class="col-md-10 p-4 dashboard">

            <!-- Logo -->
            <div class="text-center mt-3 mb-3">
                <img src="../../assets/img/logo.png" width="370" class="logo-dashboard" alt="Decarrerita Logo">
            </div>

            <!-- Bienvenida -->
            <div class="text-center mb-4">
                <h2 class="fw-bold">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION["nombres"] ?? 'Administrador'); ?>
                </h2>
                <p class="text-secondary">
                    Gestiona usuarios, vehículos, traslados y pagos desde este panel.
                </p>
            </div>

            <?php if (isset($_SESSION["mensaje_exito"])): ?>
                <div class="alert alert-success alert-dismissible fade show col-md-11 mx-auto" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION["mensaje_exito"]; unset($_SESSION["mensaje_exito"]); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Tarjetas -->
            <div class="row mt-2 justify-content-center">

                <!-- Usuarios -->
                <div class="col-md-3 mb-4">
                    <a href="usuarios.php" class="text-decoration-none text-dark">
                        <div class="card card-usuarios text-center h-100">
                            <div class="card-body">
                                <img src="../../assets/img/usuarios.png" width="70" class="mb-3" alt="Usuarios">
                                <h3><?php echo number_format($total_usuarios); ?></h3>
                                <h5>Usuarios</h5>
                                <p class="text-secondary mb-0">Registrados</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Vehículos -->
                <div class="col-md-3 mb-4">
                    <a href="vehiculos.php" class="text-decoration-none text-dark">
                        <div class="card card-vehiculos text-center h-100">
                            <div class="card-body">
                                <img src="../../assets/img/vehiculos.png" width="70" class="mb-3" alt="Vehículos">
                                <h3><?php echo number_format($total_vehiculos); ?></h3>
                                <h5>Vehículos</h5>
                                <p class="text-secondary mb-0">Registrados</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Traslados -->
                <div class="col-md-3 mb-4">
                    <a href="traslados.php" class="text-decoration-none text-dark">
                        <div class="card card-traslados text-center h-100">
                            <div class="card-body">
                                <img src="../../assets/img/traslados.png" width="70" class="mb-3" alt="Traslados">
                                <h3><?php echo number_format($total_traslados); ?></h3>
                                <h5>Traslados</h5>
                                <p class="text-secondary mb-0">Realizados</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Ganancias -->
                <div class="col-md-3 mb-4">
                    <a href="pagos.php" class="text-decoration-none text-dark">
                        <div class="card card-ganancias text-center h-100">
                            <div class="card-body">
                                <img src="../../assets/img/ganancias.png" width="70" class="mb-3" alt="Ganancias">
                                <h3>$<?php echo number_format($total_ganancias, 2); ?></h3>
                                <h5>Ganancias</h5>
                                <p class="text-secondary mb-0">Totales</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

<?php include("../../includes/footer.php"); ?>