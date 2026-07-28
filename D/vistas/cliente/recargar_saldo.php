<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
OBTENER LISTA DE BANCOS
=========================*/

$sql = "SELECT id_banco, nombre_banco FROM banco ORDER BY nombre_banco";
$resultado_bancos = $conexion->query($sql);

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
                <h2>Recargar Saldo</h2>
                <p>Registre una nueva recarga para aumentar el saldo disponible en su cuenta.</p>
            </div>

            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success text-center">Recarga registrada correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["error"])) { ?>
                <div class="alert alert-danger text-center">Ocurrió un error al registrar la recarga.</div>
            <?php } ?>

            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card card-form">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-cash-coin"></i>
                                Datos de la Recarga
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="../../procesos/registrar_recarga.php" method="POST">

                                <div class="mb-3">
                                    <label class="form-label">Banco</label>
                                    <select class="form-select" name="id_banco" required>
                                        <option value="" selected disabled>Seleccione un banco</option>
                                        <?php while ($banco = $resultado_bancos->fetch_assoc()) { ?>
                                            <option value="<?php echo $banco['id_banco']; ?>">
                                                <?php echo htmlspecialchars($banco['nombre_banco']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Número de referencia</label>
                                    <input type="text" name="numero_referencia" class="form-control"
                                           placeholder="Ingrese el número de referencia" maxlength="13" pattern="[0-9]{1,13}" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de la transferencia</label>
                                        <input type="date" name="fecha_recarga" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Monto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="monto" step="0.01" min="0.01"
                                                   class="form-control" placeholder="0.00" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-decarrerita">
                                        <i class="bi bi-cash-coin"></i>
                                        Registrar Recarga
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