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
$stmt_ch = $conexion->prepare("SELECT id_chofer FROM chofer WHERE id_usuario = ?");
$stmt_ch->bind_param("i", $id_usuario);
$stmt_ch->execute();
$id_chofer = $stmt_ch->get_result()->fetch_assoc()["id_chofer"] ?? 0;

/*=========================
GANANCIA DEL DÍA / ACUMULADA
=========================*/
$stmt_g = $conexion->prepare("SELECT COALESCE(SUM(monto_chofer), 0) AS total FROM traslado WHERE id_chofer = ? AND estado_traslado = 'Finalizado' AND DATE(fecha_hora) = CURDATE()");
$stmt_g->bind_param("i", $id_chofer);
$stmt_g->execute();
$ganancia_dia = $stmt_g->get_result()->fetch_assoc()["total"] ?? 0;

/*=========================
OBTENER TRASLADOS DEL CHOFER
=========================*/
$stmt_t = $conexion->prepare("SELECT t.*, u.nombres AS cliente_nombres, u.apellidos AS cliente_apellidos, u.telefono AS cliente_telefono, v.placa, v.marca, v.modelo 
                             FROM traslado t 
                             INNER JOIN cliente c ON c.id_cliente = t.id_cliente 
                             INNER JOIN usuario u ON u.id_usuario = c.id_usuario 
                             INNER JOIN vehiculo v ON v.id_vehiculo = t.id_vehiculo 
                             WHERE t.id_chofer = ? 
                             ORDER BY t.id_traslado DESC");
$stmt_t->bind_param("i", $id_chofer);
$stmt_t->execute();
$result_t = $stmt_t->get_result();

$traslados_list = [];
while ($row = $result_t->fetch_assoc()) {
    $traslados_list[] = $row;
}

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar_chofer.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <!-- LOGO -->
            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <!-- TÍTULO -->
            <div class="modulo-header">
                <h2>Mis Traslados</h2>
                <p>Consulte y gestione los traslados que actualmente tiene asignados.</p>
            </div>

            <?php if (isset($_SESSION["mensaje_exito"])): ?>
                <div class="alert alert-success alert-dismissible fade show col-lg-11 mx-auto" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION["mensaje_exito"]; unset($_SESSION["mensaje_exito"]); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION["mensaje_error"])): ?>
                <div class="alert alert-danger alert-dismissible fade show col-lg-11 mx-auto" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_SESSION["mensaje_error"]; unset($_SESSION["mensaje_error"]); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- RESUMEN -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="row">
                        <div class="col-md-4 mb-3 mx-auto">
                            <div class="card card-ganancias text-center">
                                <div class="card-body">
                                    <i class="bi bi-cash-stack" style="font-size:45px;color:#198754;"></i>
                                    <h3 class="mt-3 text-success">$<?php echo number_format($ganancia_dia, 2); ?></h3>
                                    <h5>Ganancia del día</h5>
                                    <p class="text-secondary mb-0">Acumulada hoy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTADO DE TRASLADOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i> Mis traslados asignados (<?php echo count($traslados_list); ?>)
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
                                            <th>Tu Ganancia (70%)</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($traslados_list)): ?>
                                            <?php foreach ($traslados_list as $t): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($t['fecha_hora'])); ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($t['cliente_nombres'] . ' ' . $t['cliente_apellidos']); ?></div>
                                                        <small class="text-muted"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($t['cliente_telefono'] ?? 'S/N'); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($t['punto_origen']); ?></td>
                                                    <td><?php echo htmlspecialchars($t['punto_destino']); ?></td>
                                                    <td class="fw-bold text-success">$<?php echo number_format($t['monto_chofer'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        $badge_est = 'bg-secondary';
                                                        if ($t['estado_traslado'] == 'Pendiente') $badge_est = 'bg-warning text-dark';
                                                        if ($t['estado_traslado'] == 'En curso') $badge_est = 'bg-primary';
                                                        if ($t['estado_traslado'] == 'Finalizado') $badge_est = 'bg-success';
                                                        if ($t['estado_traslado'] == 'Cancelado') $badge_est = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?php echo $badge_est; ?>"><?php echo htmlspecialchars($t['estado_traslado']); ?></span>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <?php if ($t['estado_traslado'] == 'Pendiente'): ?>
                                                            <form method="POST" action="../../procesos/cambiar_estado_traslado.php" class="d-inline">
                                                                <input type="hidden" name="id_traslado" value="<?php echo $t['id_traslado']; ?>">
                                                                <input type="hidden" name="nuevo_estado" value="En curso">
                                                                <button type="submit" class="btn btn-success btn-sm me-1" onclick="return confirm('¿Desea aceptar este traslado?');">
                                                                    <i class="bi bi-check-circle me-1"></i> Aceptar
                                                                </button>
                                                            </form>
                                                        <?php elseif ($t['estado_traslado'] == 'En curso'): ?>
                                                            <form method="POST" action="../../procesos/cambiar_estado_traslado.php" class="d-inline">
                                                                <input type="hidden" name="id_traslado" value="<?php echo $t['id_traslado']; ?>">
                                                                <input type="hidden" name="nuevo_estado" value="Finalizado">
                                                                <button type="submit" class="btn btn-danger btn-sm me-1" onclick="return confirm('¿Está seguro de finalizar este traslado?');">
                                                                    <i class="bi bi-flag-fill me-1"></i> Finalizar
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-outline-secondary btn-sm me-1" disabled>
                                                                <i class="bi bi-check2-all"></i> <?php echo $t['estado_traslado']; ?>
                                                            </button>
                                                        <?php endif; ?>

                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalDetalleChofer<?php echo $t['id_traslado']; ?>">
                                                            <i class="bi bi-eye"></i> Detalle
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No tienes traslados asignados actualmente.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
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

<!-- MODALS RENDERED OUTSIDE TABLE CONTAINER -->
<?php foreach ($traslados_list as $t): ?>
    <div class="modal fade" id="modalDetalleChofer<?php echo $t['id_traslado']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detalle de Traslado #<?php echo $t['id_traslado']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i A', strtotime($t['fecha_hora'])); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($t['cliente_nombres'] . ' ' . $t['cliente_apellidos']); ?></p>
                    <p><strong>Teléfono Cliente:</strong> <?php echo htmlspecialchars($t['cliente_telefono'] ?? 'S/N'); ?></p>
                    <hr>
                    <p><strong>Punto de Origen:</strong> <?php echo htmlspecialchars($t['punto_origen']); ?></p>
                    <p><strong>Punto de Destino:</strong> <?php echo htmlspecialchars($t['punto_destino']); ?></p>
                    <p><strong>Vehículo Utilizado:</strong> <?php echo htmlspecialchars($t['marca'] . ' ' . $t['modelo'] . ' (' . $t['placa'] . ')'); ?></p>
                    <hr>
                    <p><strong>Costo Total del Viaje:</strong> <span class="fw-bold">$<?php echo number_format($t['costo'], 2); ?></span></p>
                    <p><strong>Tu Ganancia (70%):</strong> <span class="fs-5 text-success fw-bold">$<?php echo number_format($t['monto_chofer'], 2); ?></span></p>
                    <p><strong>Comisión Empresa (30%):</strong> <span class="text-secondary">$<?php echo number_format($t['monto_empresa'], 2); ?></span></p>
                    <p><strong>Estado:</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($t['estado_traslado']); ?></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>
