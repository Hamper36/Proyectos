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
GANANCIA DEL DÍA
=========================*/

$sql = "SELECT COALESCE(SUM(monto_chofer), 0) AS total
        FROM traslado
        WHERE id_chofer = ? AND estado_traslado = 'Finalizado' AND DATE(fecha_hora) = CURDATE()";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$ganancia_dia = $stmt->get_result()->fetch_assoc()["total"];

/*=========================
LISTADO DE TRASLADOS DEL CHOFER
(Pendiente y En curso — los finalizados/cancelados
 se consultan en Historial de Viajes)
=========================*/

$sql = "SELECT t.id_traslado, t.fecha_hora, t.punto_origen, t.punto_destino,
               t.costo, t.monto_empresa, t.monto_chofer, t.estado_traslado,
               u.nombres, u.apellidos, u.telefono, u.correo
        FROM traslado t
        INNER JOIN cliente cl ON cl.id_cliente = t.id_cliente
        INNER JOIN usuario u ON u.id_usuario = cl.id_usuario
        WHERE t.id_chofer = ? AND t.estado_traslado IN ('Pendiente', 'En curso')
        ORDER BY t.id_traslado DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$traslados = $stmt->get_result();

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_chofer.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard">
            </div>

            <div class="modulo-header">
                <h2>Mis Traslados</h2>
                <p>Consulte los traslados que actualmente tiene asignados.</p>
            </div>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "1") { ?>
                <div class="alert alert-success text-center">Traslado finalizado correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "aceptado") { ?>
                <div class="alert alert-success text-center">Traslado aceptado. Ya puede iniciar el viaje.</div>
            <?php } ?>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "rechazado") { ?>
                <div class="alert alert-success text-center">Traslado rechazado. Se devolvió el saldo al cliente.</div>
            <?php } ?>

            <!-- RESUMEN -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="row">
                        <div class="col-md-4 mb-3 mx-auto">
                            <div class="card card-ganancias text-center">
                                <div class="card-body">
                                    <i class="bi bi-cash-stack" style="font-size:45px;color:#198754;"></i>
                                    <h3 class="mt-3">$<?php echo number_format($ganancia_dia, 2); ?></h3>
                                    <h5>Ganancia del día</h5>
                                    <p class="text-secondary mb-0">Acumulada</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTADO -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Mis traslados asignados
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Costo</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($traslados->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-secondary">
                                                    No tiene traslados asignados en este momento.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($t = $traslados->fetch_assoc()) { ?>

                                            <tr>
                                                <td><?php echo date("d/m/Y", strtotime($t["fecha_hora"])); ?></td>
                                                <td><?php echo htmlspecialchars($t["nombres"] . " " . $t["apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($t["punto_origen"]); ?></td>
                                                <td><?php echo htmlspecialchars($t["punto_destino"]); ?></td>
                                                <td>$<?php echo number_format($t["costo"], 2); ?></td>
                                                <td>
                                                    <?php if ($t["estado_traslado"] == "Pendiente") { ?>
                                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-primary">En curso</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center text-nowrap">

                                                    <?php if ($t["estado_traslado"] == "Pendiente") { ?>
                                                        <form action="../../procesos/aceptar_traslado.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="id_traslado" value="<?php echo $t['id_traslado']; ?>">
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="bi bi-check-circle"></i>
                                                                Aceptar
                                                            </button>
                                                        </form>
                                                        <button class="btn btn-outline-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalRechazar<?php echo $t['id_traslado']; ?>">
                                                            <i class="bi bi-x-circle"></i>
                                                            Rechazar
                                                        </button>
                                                    <?php } else { ?>
                                                        <form action="../../procesos/finalizar_traslado.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="id_traslado" value="<?php echo $t['id_traslado']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('¿Está seguro de finalizar este traslado?');">
                                                                <i class="bi bi-flag-fill"></i>
                                                                Finalizar
                                                            </button>
                                                        </form>
                                                    <?php } ?>

                                                    <button class="btn btn-outline-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalle<?php echo $t['id_traslado']; ?>">
                                                        <i class="bi bi-eye"></i>
                                                        Detalle
                                                    </button>

                                                </td>
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

<?php
// Modales de detalle
$traslados->data_seek(0);
while ($t = $traslados->fetch_assoc()) {
?>

<div class="modal fade" id="modalDetalle<?php echo $t['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title">Detalle del Traslado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($t["nombres"] . " " . $t["apellidos"]); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($t["telefono"]); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($t["correo"]); ?></p>
                <hr>
                <p><strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($t["fecha_hora"])); ?></p>
                <p><strong>Origen:</strong> <?php echo htmlspecialchars($t["punto_origen"]); ?></p>
                <p><strong>Destino:</strong> <?php echo htmlspecialchars($t["punto_destino"]); ?></p>
                <hr>
                <p><strong>Costo total:</strong> $<?php echo number_format($t["costo"], 2); ?></p>
                <p><strong>Monto empresa (30%):</strong> $<?php echo number_format($t["monto_empresa"], 2); ?></p>
                <p><strong>Monto para usted (70%):</strong> $<?php echo number_format($t["monto_chofer"], 2); ?></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<?php } ?>

<?php
// Modales de rechazo (solo para los "Pendiente")
$traslados->data_seek(0);
while ($t = $traslados->fetch_assoc()) {
    if ($t["estado_traslado"] == "Pendiente") {
?>
<div class="modal fade" id="modalRechazar<?php echo $t['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/rechazar_traslado.php" method="POST">
                <input type="hidden" name="id_traslado" value="<?php echo $t['id_traslado']; ?>">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Rechazar Traslado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Se cancelará el traslado y se devolverá el saldo al cliente.</p>
                    <div class="mb-3">
                        <label class="form-label">Motivo del rechazo</label>
                        <textarea name="motivo_rechazo" class="form-control" rows="3" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('¿Está seguro de rechazar este traslado?');">
                        Confirmar Rechazo
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php
    }
}
?>

<?php include("../../includes/footer.php"); ?>