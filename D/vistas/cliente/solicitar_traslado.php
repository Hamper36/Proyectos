<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER SALDO DEL CLIENTE
=========================*/

$sql = "SELECT saldo FROM cliente WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$saldo = $resultado->fetch_assoc()["saldo"];

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_cliente.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard">
            </div>

            <div class="modulo-header">
                <h2>Solicitar Traslado</h2>
                <p>Complete la información para solicitar un traslado dentro de la ciudad.</p>
            </div>

            <?php if (isset($_GET["error"]) && $_GET["error"] == "saldo") { ?>
                <div class="alert alert-danger text-center">Saldo insuficiente para solicitar un traslado.</div>
            <?php } ?>

            <?php if (isset($_GET["error"]) && $_GET["error"] == "sin_choferes") { ?>
                <div class="alert alert-danger text-center">No hay choferes disponibles en este momento.</div>
            <?php } ?>

            <?php if (isset($_GET["error"]) && $_GET["error"] == "fecha_invalida") { ?>
                <div class="alert alert-danger text-center">No puede seleccionar una fecha anterior a la de hoy.</div>
            <?php } ?>

            <!-- TARJETA SALDO -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-4">
                    <div class="card card-money text-center">
                        <div class="card-body">
                            <img src="../../assets/img/ganancias.png" width="60" class="mb-3">
                            <h3>$<?php echo number_format($saldo, 2); ?></h3>
                            <h5>Saldo Disponible</h5>
                            <p class="text-secondary mb-0">
                                Este saldo será utilizado para cancelar el costo del traslado.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-form">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill"></i>
                                Datos del Traslado
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="../../procesos/registrar_traslado.php" method="POST">

                                <div class="mb-3">
                                    <label class="form-label">Punto de origen</label>
                                    <input type="text" name="punto_origen" class="form-control"
                                           placeholder="Ej.: Centro Comercial Galerías" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Punto de destino</label>
                                    <input type="text" name="punto_destino" class="form-control"
                                           placeholder="Ej.: Hospital Central" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha del traslado</label>
                                        <input type="date" name="fecha_traslado" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Hora aproximada</label>
                                        <input type="time" name="hora_traslado" class="form-control" required>
                                    </div>
                                </div>

                                <div class="alert alert-info text-center">
                                    Costo fijo del traslado: <strong>$40.00</strong>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-decarrerita">
                                        <i class="bi bi-taxi-front-fill"></i>
                                        Solicitar Traslado
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>