<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
FILTROS DE BÚSQUEDA
=========================*/
$filtro_chofer = trim($_GET['chofer'] ?? '');
$filtro_desde = trim($_GET['desde'] ?? '');
$filtro_hasta = trim($_GET['hasta'] ?? '');
$filtro_estado = trim($_GET['estado'] ?? 'Todos');

$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($filtro_chofer)) {
    $where_clauses[] = "CONCAT(u.nombres, ' ', u.apellidos) LIKE ?";
    $params[] = "%" . $filtro_chofer . "%";
    $types .= "s";
}

if (!empty($filtro_desde)) {
    $where_clauses[] = "rv.fecha_revision >= ?";
    $params[] = $filtro_desde;
    $types .= "s";
}

if (!empty($filtro_hasta)) {
    $where_clauses[] = "rv.fecha_revision <= ?";
    $params[] = $filtro_hasta;
    $types .= "s";
}

if ($filtro_estado !== 'Todos' && !empty($filtro_estado)) {
    $where_clauses[] = "rv.resultado = ?";
    $params[] = $filtro_estado;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT rv.*, v.placa, v.marca, v.modelo, v.anio, v.color, u.nombres, u.apellidos, u.cedula, adm.nombres AS adm_nombres, adm.apellidos AS adm_apellidos 
        FROM revision_vehiculo rv 
        INNER JOIN vehiculo v ON v.id_vehiculo = rv.id_vehiculo 
        INNER JOIN chofer c ON c.id_chofer = v.id_chofer 
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario 
        LEFT JOIN personal_administrativo pa ON pa.id_personal = rv.id_personal 
        LEFT JOIN usuario adm ON adm.id_usuario = pa.id_usuario 
        WHERE $where_sql 
        ORDER BY rv.id_revision DESC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_rev = $stmt->get_result();

$revisiones_list = [];
while ($r = $result_rev->fetch_assoc()) {
    $revisiones_list[] = $r;
}

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar_personal.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <!-- LOGO -->
            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <!-- TÍTULO -->
            <div class="modulo-header">
                <h2>Revisiones Vehiculares</h2>
                <p>Consulte el historial de revisiones técnicas realizadas a la flota de vehículos.</p>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-funnel-fill me-2"></i> Filtros de búsqueda
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="revisiones.php" class="row align-items-end">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Chofer</label>
                                    <input type="text" name="chofer" class="form-control" placeholder="Nombre del chofer" value="<?php echo htmlspecialchars($filtro_chofer); ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label fw-bold">Desde</label>
                                    <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label fw-bold">Hasta</label>
                                    <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label fw-bold">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="Todos" <?php echo ($filtro_estado == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Apto" <?php echo ($filtro_estado == 'Apto') ? 'selected' : ''; ?>>Apto (>= 65 pts)</option>
                                        <option value="No Apto" <?php echo ($filtro_estado == 'No Apto') ? 'selected' : ''; ?>>No Apto (< 65 pts)</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 d-grid">
                                    <button type="submit" class="btn btn-decarrerita">
                                        <i class="bi bi-search me-1"></i> Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL DE REVISIONES -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i> Historial de revisiones vehiculares (<?php echo count($revisiones_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Placa</th>
                                            <th>Vehículo</th>
                                            <th>Fecha Revisión</th>
                                            <th>Calificación (min 65)</th>
                                            <th>Resultado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($revisiones_list)): ?>
                                            <?php foreach ($revisiones_list as $rv): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($rv['nombres'] . ' ' . $rv['apellidos']); ?></td>
                                                    <td><span class="badge bg-dark fs-6"><?php echo htmlspecialchars($rv['placa']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($rv['marca'] . ' ' . $rv['modelo'] . ' (' . $rv['anio'] . ')'); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($rv['fecha_revision'])); ?></td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($rv['calificacion']); ?> pts</td>
                                                    <td>
                                                        <span class="badge <?php echo ($rv['resultado'] == 'Apto') ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo htmlspecialchars($rv['resultado']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalRev<?php echo $rv['id_revision']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron revisiones vehiculares con los filtros indicados.
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

<!-- MODALS RENDERED OUTSIDE TABLE -->
<?php foreach ($revisiones_list as $rv): ?>
    <div class="modal fade" id="modalRev<?php echo $rv['id_revision']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-shield-check me-2"></i>Detalle de Revisión Vehicular #<?php echo $rv['id_revision']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Propietario / Chofer:</strong> <?php echo htmlspecialchars($rv['nombres'] . ' ' . $rv['apellidos']); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($rv['cedula']); ?></p>
                    <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($rv['marca'] . ' ' . $rv['modelo'] . ' ' . $rv['anio'] . ' - Color ' . $rv['color']); ?></p>
                    <p><strong>Placa:</strong> <span class="badge bg-dark font-monospace fs-6"><?php echo htmlspecialchars($rv['placa']); ?></span></p>
                    <hr>
                    <p><strong>Fecha de Revisión:</strong> <?php echo date('d/m/Y', strtotime($rv['fecha_revision'])); ?></p>
                    <p><strong>Calificación Obtendida:</strong> <span class="fs-5 fw-bold"><?php echo htmlspecialchars($rv['calificacion']); ?> / 100</span> (Mínimo aprobatorio: 65)</p>
                    <p><strong>Resultado Final:</strong> <span class="badge <?php echo ($rv['resultado'] == 'Apto') ? 'bg-success' : 'bg-danger'; ?> fs-6"><?php echo htmlspecialchars($rv['resultado']); ?></span></p>
                    <p><strong>Observaciones:</strong> <?php echo !empty($rv['observacion']) ? htmlspecialchars($rv['observacion']) : 'Sin observaciones registradas.'; ?></p>
                    <p><strong>Evaluador:</strong> <?php echo htmlspecialchars(($rv['adm_nombres'] ?? 'Personal') . ' ' . ($rv['adm_apellidos'] ?? '')); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>