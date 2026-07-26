<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
FILTROS DE BÚSQUEDA
=========================*/
$buscar = trim($_GET['buscar'] ?? '');
$estado_filtro = trim($_GET['estado'] ?? 'Todos');
$fecha_filtro = trim($_GET['fecha'] ?? '');

$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($buscar)) {
    $where_clauses[] = "(uc.nombres LIKE ? OR uc.apellidos LIKE ? OR uch.nombres LIKE ? OR uch.apellidos LIKE ?)";
    $search_param = "%$buscar%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

if ($estado_filtro !== 'Todos' && !empty($estado_filtro)) {
    $where_clauses[] = "t.estado_traslado = ?";
    $params[] = $estado_filtro;
    $types .= "s";
}

if (!empty($fecha_filtro)) {
    $where_clauses[] = "DATE(t.fecha_hora) = ?";
    $params[] = $fecha_filtro;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT t.*, 
               uc.nombres AS cliente_nombres, uc.apellidos AS cliente_apellidos, uc.telefono AS cliente_telefono,
               uch.nombres AS chofer_nombres, uch.apellidos AS chofer_apellidos, uch.telefono AS chofer_telefono,
               v.placa, v.marca, v.modelo
        FROM traslado t
        INNER JOIN cliente c ON c.id_cliente = t.id_cliente
        INNER JOIN usuario uc ON uc.id_usuario = c.id_usuario
        INNER JOIN chofer ch ON ch.id_chofer = t.id_chofer
        INNER JOIN usuario uch ON uch.id_usuario = ch.id_usuario
        INNER JOIN vehiculo v ON v.id_vehiculo = t.id_vehiculo
        WHERE $where_sql
        ORDER BY t.id_traslado DESC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_traslados = $stmt->get_result();

$traslados_list = [];
while ($row = $result_traslados->fetch_assoc()) {
    $traslados_list[] = $row;
}

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <!-- LOGO -->
            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <!-- TÍTULO -->
            <div class="modulo-header">
                <h2>Gestión de Traslados</h2>
                <p>Consulte todos los traslados registrados en el sistema.</p>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-search me-2"></i> Buscar traslados
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="traslados.php" class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Buscar</label>
                                    <input type="text" name="buscar" class="form-control" placeholder="Nombre de cliente o chofer" value="<?php echo htmlspecialchars($buscar); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="Todos" <?php echo ($estado_filtro == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Pendiente" <?php echo ($estado_filtro == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="En curso" <?php echo ($estado_filtro == 'En curso') ? 'selected' : ''; ?>>En curso</option>
                                        <option value="Finalizado" <?php echo ($estado_filtro == 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                                        <option value="Cancelado" <?php echo ($estado_filtro == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($fecha_filtro); ?>">
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Buscar
                                    </button>
                                    <?php if (!empty($buscar) || $estado_filtro !== 'Todos' || !empty($fecha_filtro)): ?>
                                        <a href="traslados.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTADO DE TRASLADOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-list-check me-2"></i> Traslados registrados (<?php echo count($traslados_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha y Hora</th>
                                            <th>Cliente</th>
                                            <th>Chofer</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Estado</th>
                                            <th>Monto</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($traslados_list)): ?>
                                            <?php foreach ($traslados_list as $t): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($t['fecha_hora'])); ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($t['cliente_nombres'] . ' ' . $t['cliente_apellidos']); ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($t['chofer_nombres'] . ' ' . $t['chofer_apellidos']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($t['marca'] . ' ' . $t['modelo'] . ' (' . $t['placa'] . ')'); ?></small>
                                                    </td>
                                                    <td><small><?php echo htmlspecialchars($t['punto_origen']); ?></small></td>
                                                    <td><small><?php echo htmlspecialchars($t['punto_destino']); ?></small></td>
                                                    <td>
                                                        <?php
                                                        $badge_t = 'bg-secondary';
                                                        if ($t['estado_traslado'] == 'Pendiente') $badge_t = 'bg-warning text-dark';
                                                        if ($t['estado_traslado'] == 'En curso') $badge_t = 'bg-primary';
                                                        if ($t['estado_traslado'] == 'Finalizado') $badge_t = 'bg-success';
                                                        if ($t['estado_traslado'] == 'Cancelado') $badge_t = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?php echo $badge_t; ?>"><?php echo htmlspecialchars($t['estado_traslado']); ?></span>
                                                    </td>
                                                    <td class="fw-bold text-success">$<?php echo number_format($t['costo'], 2); ?></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalVerTraslado<?php echo $t['id_traslado']; ?>">
                                                            <i class="bi bi-eye"></i> Ver detalle
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron traslados registrados con los filtros seleccionados.
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

            <div class="mb-5"></div>
        </div>
    </div>
</div>

<!-- MODALS RENDERED OUTSIDE TABLE CONTAINER -->
<?php foreach ($traslados_list as $t): ?>
    <?php
    $badge_t = 'bg-secondary';
    if ($t['estado_traslado'] == 'Pendiente') $badge_t = 'bg-warning text-dark';
    if ($t['estado_traslado'] == 'En curso') $badge_t = 'bg-primary';
    if ($t['estado_traslado'] == 'Finalizado') $badge_t = 'bg-success';
    if ($t['estado_traslado'] == 'Cancelado') $badge_t = 'bg-danger';
    ?>
    <!-- MODAL VER DETALLE TRASLADO -->
    <div class="modal fade" id="modalVerTraslado<?php echo $t['id_traslado']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-map me-2"></i>Detalle del Traslado #<?php echo $t['id_traslado']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3 text-start">
                    <div class="col-md-6">
                        <p><strong>Fecha y Hora:</strong> <?php echo date('d/m/Y H:i A', strtotime($t['fecha_hora'])); ?></p>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($t['cliente_nombres'] . ' ' . $t['cliente_apellidos']); ?></p>
                        <p><strong>Teléfono Cliente:</strong> <?php echo htmlspecialchars($t['cliente_telefono'] ?? 'S/N'); ?></p>
                        <hr>
                        <p><strong>Punto Origen:</strong> <?php echo htmlspecialchars($t['punto_origen']); ?></p>
                        <p><strong>Punto Destino:</strong> <?php echo htmlspecialchars($t['punto_destino']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Chofer Asignado:</strong> <?php echo htmlspecialchars($t['chofer_nombres'] . ' ' . $t['chofer_apellidos']); ?></p>
                        <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($t['marca'] . ' ' . $t['modelo'] . ' - Placa: ' . $t['placa']); ?></p>
                        <p><strong>Teléfono Chofer:</strong> <?php echo htmlspecialchars($t['chofer_telefono'] ?? 'S/N'); ?></p>
                        <hr>
                        <p><strong>Costo Total Traslado:</strong> <span class="fs-5 text-success fw-bold">$<?php echo number_format($t['costo'], 2); ?></span></p>
                        <p><strong>Comisión Empresa:</strong> <span class="text-primary fw-bold">$<?php echo number_format($t['monto_empresa'], 2); ?></span></p>
                        <p><strong>Ganancia Chofer:</strong> <span class="text-info fw-bold">$<?php echo number_format($t['monto_chofer'], 2); ?></span></p>
                        <p><strong>Estado Actual:</strong> <span class="badge <?php echo $badge_t; ?>"><?php echo htmlspecialchars($t['estado_traslado']); ?></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>