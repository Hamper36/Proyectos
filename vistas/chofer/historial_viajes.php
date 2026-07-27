<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Get id_chofer
$stmt_ch = $conexion->prepare("SELECT id_chofer FROM chofer WHERE id_usuario = ?");
$stmt_ch->bind_param("i", $id_usuario);
$stmt_ch->execute();
$id_chofer = $stmt_ch->get_result()->fetch_assoc()["id_chofer"] ?? 0;

/*=========================
FILTROS DE BÚSQUEDA POR PERÍODO DE TIEMPO
=========================*/
$desde = trim($_GET['desde'] ?? '');
$hasta = trim($_GET['hasta'] ?? '');
$estado_filtro = trim($_GET['estado'] ?? 'Todos');

$where_clauses = ["t.id_chofer = ?"];
$params = [$id_chofer];
$types = "i";

if (!empty($desde)) {
    $where_clauses[] = "DATE(t.fecha_hora) >= ?";
    $params[] = $desde;
    $types .= "s";
}

if (!empty($hasta)) {
    $where_clauses[] = "DATE(t.fecha_hora) <= ?";
    $params[] = $hasta;
    $types .= "s";
}

if ($estado_filtro !== 'Todos' && !empty($estado_filtro)) {
    $where_clauses[] = "t.estado_traslado = ?";
    $params[] = $estado_filtro;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT t.*, u.nombres AS cliente_nombres, u.apellidos AS cliente_apellidos, v.placa, v.marca, v.modelo 
        FROM traslado t 
        INNER JOIN cliente c ON c.id_cliente = t.id_cliente 
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario 
        INNER JOIN vehiculo v ON v.id_vehiculo = t.id_vehiculo 
        WHERE $where_sql 
        ORDER BY t.id_traslado DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result_viajes = $stmt->get_result();

$viajes_list = [];
while ($r = $result_viajes->fetch_assoc()) {
    $viajes_list[] = $r;
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
                <h2>Historial de Viajes</h2>
                <p>Consulte y filtre el historial de traslados realizados por período de tiempo.</p>
            </div>

            <!-- FILTROS DE BÚSQUEDA POR PERÍODO -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-funnel me-2"></i> Filtrar viajes por período de tiempo
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="historial_viajes.php" class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Desde</label>
                                    <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Hasta</label>
                                    <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Estado del Viaje</label>
                                    <select name="estado" class="form-select">
                                        <option value="Todos" <?php echo ($estado_filtro == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Finalizado" <?php echo ($estado_filtro == 'Finalizado') ? 'selected' : ''; ?>>Finalizados (Realizados)</option>
                                        <option value="Cancelado" <?php echo ($estado_filtro == 'Cancelado') ? 'selected' : ''; ?>>Cancelados</option>
                                        <option value="Pendiente" <?php echo ($estado_filtro == 'Pendiente') ? 'selected' : ''; ?>>Pendientes por realizar</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Filtrar
                                    </button>
                                    <?php if (!empty($desde) || !empty($hasta) || $estado_filtro !== 'Todos'): ?>
                                        <a href="historial_viajes.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLA HISTORIAL -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-clock-history me-2"></i> Historial de Traslados (<?php echo count($viajes_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha y Hora</th>
                                            <th>Cliente</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Monto Ganado</th>
                                            <th>Estado</th>
                                            <th class="text-center">Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($viajes_list)): ?>
                                            <?php foreach ($viajes_list as $v): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($v['fecha_hora'])); ?></td>
                                                    <td><?php echo htmlspecialchars($v['cliente_nombres'] . ' ' . $v['cliente_apellidos']); ?></td>
                                                    <td><small><?php echo htmlspecialchars($v['punto_origen']); ?></small></td>
                                                    <td><small><?php echo htmlspecialchars($v['punto_destino']); ?></small></td>
                                                    <td class="fw-bold text-success">$<?php echo number_format($v['monto_chofer'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        $badge_v = 'bg-secondary';
                                                        if ($v['estado_traslado'] == 'Finalizado') $badge_v = 'bg-success';
                                                        if ($v['estado_traslado'] == 'Cancelado') $badge_v = 'bg-danger';
                                                        if ($v['estado_traslado'] == 'Pendiente') $badge_v = 'bg-warning text-dark';
                                                        if ($v['estado_traslado'] == 'En curso') $badge_v = 'bg-primary';
                                                        ?>
                                                        <span class="badge <?php echo $badge_v; ?>"><?php echo htmlspecialchars($v['estado_traslado']); ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalHis<?php echo $v['id_traslado']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron viajes en el período seleccionado.
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

<!-- MODALS RENDERED OUTSIDE TABLE -->
<?php foreach ($viajes_list as $v): ?>
    <div class="modal fade" id="modalHis<?php echo $v['id_traslado']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-journal-text me-2"></i>Detalle de Viaje #<?php echo $v['id_traslado']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Fecha y Hora:</strong> <?php echo date('d/m/Y H:i A', strtotime($v['fecha_hora'])); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($v['cliente_nombres'] . ' ' . $v['cliente_apellidos']); ?></p>
                    <p><strong>Punto de Origen:</strong> <?php echo htmlspecialchars($v['punto_origen']); ?></p>
                    <p><strong>Punto de Destino:</strong> <?php echo htmlspecialchars($v['punto_destino']); ?></p>
                    <p><strong>Vehículo Utilizado:</strong> <?php echo htmlspecialchars($v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')'); ?></p>
                    <hr>
                    <p><strong>Ganancia para Chofer (70%):</strong> <span class="fs-5 text-success fw-bold">$<?php echo number_format($v['monto_chofer'], 2); ?></span></p>
                    <p><strong>Comisión Empresa (30%):</strong> <span class="text-secondary">$<?php echo number_format($v['monto_empresa'], 2); ?></span></p>
                    <p><strong>Estado del Viaje:</strong> <?php echo htmlspecialchars($v['estado_traslado']); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>