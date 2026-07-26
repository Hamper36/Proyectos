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

$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($buscar)) {
    $where_clauses[] = "(v.placa LIKE ? OR v.marca LIKE ? OR v.modelo LIKE ? OR u.nombres LIKE ? OR u.apellidos LIKE ?)";
    $search_param = "%$buscar%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sssss";
}

if ($estado_filtro !== 'Todos' && !empty($estado_filtro)) {
    if ($estado_filtro == 'Pendiente') {
        $where_clauses[] = "(rv.resultado IS NULL OR rv.resultado = 'Pendiente')";
    } else {
        $where_clauses[] = "rv.resultado = ?";
        $params[] = $estado_filtro;
        $types .= "s";
    }
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT v.*, u.nombres, u.apellidos, u.telefono, u.correo,
               rv.resultado AS estado_revision, rv.fecha_revision, rv.calificacion, rv.observacion
        FROM vehiculo v
        INNER JOIN chofer ch ON ch.id_chofer = v.id_chofer
        INNER JOIN usuario u ON u.id_usuario = ch.id_usuario
        LEFT JOIN revision_vehiculo rv ON (rv.id_vehiculo = v.id_vehiculo AND rv.id_revision = (
            SELECT MAX(id_revision) FROM revision_vehiculo WHERE id_vehiculo = v.id_vehiculo
        ))
        WHERE $where_sql
        ORDER BY v.id_vehiculo DESC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_vehiculos = $stmt->get_result();

$vehiculos_list = [];
while ($row = $result_vehiculos->fetch_assoc()) {
    $vehiculos_list[] = $row;
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
                <h2>Gestión de Vehículos</h2>
                <p>Consulte los vehículos registrados y supervise su estado técnico dentro del sistema.</p>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-search me-2"></i> Buscar vehículos
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="vehiculos.php" class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Buscar</label>
                                    <input type="text" name="buscar" class="form-control" placeholder="Placa, marca, modelo o propietario" value="<?php echo htmlspecialchars($buscar); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Estado Inspección</label>
                                    <select name="estado" class="form-select">
                                        <option value="Todos" <?php echo ($estado_filtro == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Apto" <?php echo ($estado_filtro == 'Apto') ? 'selected' : ''; ?>>Apto</option>
                                        <option value="Pendiente" <?php echo ($estado_filtro == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="No Apto" <?php echo ($estado_filtro == 'No Apto') ? 'selected' : ''; ?>>No Apto</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Buscar
                                    </button>
                                    <?php if (!empty($buscar) || $estado_filtro !== 'Todos'): ?>
                                        <a href="vehiculos.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTADO DE VEHÍCULOS EN COLUMNAS SEPARADAS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front me-2"></i> Vehículos registrados (<?php echo count($vehiculos_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Placa</th>
                                            <th>Propietario / Chofer</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Año</th>
                                            <th>Color</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vehiculos_list)): ?>
                                            <?php foreach ($vehiculos_list as $v): ?>
                                                <tr>
                                                    <td><span class="badge bg-dark fs-6"><?php echo htmlspecialchars($v['placa']); ?></span></td>
                                                    <td class="text-start">
                                                        <div class="fw-bold"><?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apellidos']); ?></div>
                                                        <small class="text-muted"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($v['telefono'] ?? 'S/N'); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($v['marca']); ?></td>
                                                    <td><?php echo htmlspecialchars($v['modelo']); ?></td>
                                                    <td><?php echo htmlspecialchars($v['anio']); ?></td>
                                                    <td><?php echo htmlspecialchars($v['color']); ?></td>
                                                    <td>
                                                        <?php
                                                        $est = $v['estado_revision'] ?? 'Pendiente';
                                                        $badge_est = 'bg-warning text-dark';
                                                        if ($est == 'Apto') $badge_est = 'bg-success';
                                                        if ($est == 'No Apto') $badge_est = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?php echo $badge_est; ?>"><?php echo htmlspecialchars($est); ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalVerVehiculo<?php echo $v['id_vehiculo']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>

                                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalHistorialVehiculo<?php echo $v['id_vehiculo']; ?>">
                                                            <i class="bi bi-clock-history"></i> Historial
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron vehículos con los filtros aplicados.
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
<?php foreach ($vehiculos_list as $v): ?>

    <!-- MODAL DETALLE VEHÍCULO -->
    <div class="modal fade" id="modalVerVehiculo<?php echo $v['id_vehiculo']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-car-front-fill me-2"></i>Detalle del Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Placa:</strong> <span class="badge bg-dark fs-6"><?php echo htmlspecialchars($v['placa']); ?></span></p>
                    <p><strong>Marca:</strong> <?php echo htmlspecialchars($v['marca']); ?></p>
                    <p><strong>Modelo:</strong> <?php echo htmlspecialchars($v['modelo']); ?></p>
                    <p><strong>Año:</strong> <?php echo htmlspecialchars($v['anio']); ?></p>
                    <p><strong>Color:</strong> <?php echo htmlspecialchars($v['color']); ?></p>
                    <hr>
                    <p><strong>Propietario/Chofer:</strong> <?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apellidos']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($v['telefono'] ?? 'S/N'); ?></p>
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($v['correo']); ?></p>
                    <hr>
                    <p><strong>Estado de Inspección Actual:</strong> <?php echo htmlspecialchars($v['estado_revision'] ?? 'Pendiente'); ?></p>
                    <?php if (!empty($v['observacion'])): ?>
                        <p><strong>Observación de Inspección:</strong> <?php echo htmlspecialchars($v['observacion']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL HISTORIAL DE REVISIONES -->
    <div class="modal fade" id="modalHistorialVehiculo<?php echo $v['id_vehiculo']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Historial de Inspecciones - Placa <?php echo htmlspecialchars($v['placa']); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <?php
                    $stmt_h = $conexion->prepare("SELECT rv.*, u.nombres, u.apellidos 
                                                FROM revision_vehiculo rv 
                                                INNER JOIN personal_administrativo pa ON pa.id_personal = rv.id_personal 
                                                INNER JOIN usuario u ON u.id_usuario = pa.id_usuario 
                                                WHERE rv.id_vehiculo = ? 
                                                ORDER BY rv.fecha_revision DESC");
                    $stmt_h->bind_param("i", $v['id_vehiculo']);
                    $stmt_h->execute();
                    $historial = $stmt_h->get_result();
                    ?>
                    <?php if ($historial->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Inspector</th>
                                        <th>Calificación</th>
                                        <th>Resultado</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($h = $historial->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($h['fecha_revision'])); ?></td>
                                            <td><?php echo htmlspecialchars($h['nombres'] . ' ' . $h['apellidos']); ?></td>
                                            <td><?php echo $h['calificacion']; ?> / 10</td>
                                            <td>
                                                <span class="badge <?php echo ($h['resultado'] == 'Apto') ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo htmlspecialchars($h['resultado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($h['observacion']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i> Este vehículo aún no tiene inspecciones técnicas registradas.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>
