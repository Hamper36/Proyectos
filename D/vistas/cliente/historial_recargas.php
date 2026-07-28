<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER id_cliente
=========================*/

$sql = "SELECT id_cliente FROM cliente WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_cliente = $stmt->get_result()->fetch_assoc()["id_cliente"];

/*=========================
OBTENER RECARGAS DEL CLIENTE
=========================*/

$sql = "SELECT r.fecha_recarga, r.numero_referencia, r.monto, b.nombre_banco
        FROM recarga_saldo r
        INNER JOIN banco b ON b.id_banco = r.id_banco
        WHERE r.id_cliente = ?
        ORDER BY r.fecha_recarga DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$recargas = $stmt->get_result();

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
                <h2>Historial de Recargas</h2>
                <p>Consulte todas las recargas realizadas a su cuenta.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-wallet2"></i>
                                Historial de Recargas
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background:#1E2E4F;color:white;">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Banco</th>
                                            <th>Referencia</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($recargas->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary">
                                                    Aún no ha realizado ninguna recarga.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($fila = $recargas->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo date("d/m/Y", strtotime($fila["fecha_recarga"])); ?></td>
                                                <td><?php echo htmlspecialchars($fila["nombre_banco"]); ?></td>
                                                <td><?php echo htmlspecialchars($fila["numero_referencia"]); ?></td>
                                                <td>$<?php echo number_format($fila["monto"], 2); ?></td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>