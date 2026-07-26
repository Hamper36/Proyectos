<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
MÉTRICAS FINANCIERAS REALES
=========================*/
$res_p = mysqli_query($conexion, "SELECT COALESCE(SUM(monto_pagado), 0) AS total FROM pago_chofer");
$total_pagado = mysqli_fetch_assoc($res_p)['total'] ?? 0;

$res_i = mysqli_query($conexion, "SELECT COALESCE(SUM(monto_empresa), 0) AS total FROM traslado WHERE estado_traslado = 'Finalizado'");
$total_ingresos = mysqli_fetch_assoc($res_i)['total'] ?? 0;

$ganancia_neta = $total_ingresos - $total_pagado;

/*=========================
FILTROS DE BÚSQUEDA
=========================*/
$buscar = trim($_GET['buscar'] ?? '');
$fecha_desde = trim($_GET['fecha_desde'] ?? '');
$fecha_hasta = trim($_GET['fecha_hasta'] ?? '');

$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($buscar)) {
    $where_clauses[] = "(u.nombres LIKE ? OR u.apellidos LIKE ? OR p.numero_referencia LIKE ?)";
    $search_param = "%$buscar%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($fecha_desde) && !empty($fecha_hasta)) {
    $where_clauses[] = "p.fecha_pago BETWEEN ? AND ?";
    $params[] = $fecha_desde;
    $params[] = $fecha_hasta;
    $types .= "ss";
} else if (!empty($fecha_desde)) {
    $where_clauses[] = "p.fecha_pago >= ?";
    $params[] = $fecha_desde;
    $types .= "s";
} else if (!empty($fecha_hasta)) {
    $where_clauses[] = "p.fecha_pago <= ?";
    $params[] = $fecha_hasta;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT p.*, 
               u.nombres AS chofer_nombres, u.apellidos AS chofer_apellidos, u.cedula,
               b.nombre_banco, ch.numero_cuenta,
               up.nombres AS adm_nombres, up.apellidos AS adm_apellidos
        FROM pago_chofer p
        INNER JOIN chofer ch ON ch.id_chofer = p.id_chofer
        INNER JOIN usuario u ON u.id_usuario = ch.id_usuario
        LEFT JOIN banco b ON b.id_banco = ch.id_banco
        LEFT JOIN personal_administrativo pa ON pa.id_personal = p.id_personal
        LEFT JOIN usuario up ON up.id_usuario = pa.id_usuario
        WHERE $where_sql
        ORDER BY p.id_pago DESC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_pagos = $stmt->get_result();

$pagos_list = [];
while ($row = $result_pagos->fetch_assoc()) {
    $pagos_list[] = $row;
}

// Get list of choferes for New Payment modal
$res_choferes_list = mysqli_query($conexion, "SELECT ch.id_chofer, u.nombres, u.apellidos, u.cedula, b.nombre_banco 
                                             FROM chofer ch 
                                             INNER JOIN usuario u ON u.id_usuario = ch.id_usuario 
                                             LEFT JOIN banco b ON b.id_banco = ch.id_banco");

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

            <!-- TÍTULO Y REGISTRO -->
            <div class="modulo-header">
                <h2>Gestión de Pagos</h2>
                <p>Consulte y supervise todos los pagos realizados a los choferes.</p>
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

            <!-- BOTÓN NUEVO PAGO -->
            <div class="row justify-content-center mb-3">
                <div class="col-lg-11 d-flex justify-content-end">
                    <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoPago">
                        <i class="bi bi-cash-stack me-1"></i> Registrar Nuevo Pago
                    </button>
                </div>
            </div>

            <!-- RESUMEN FINANCIERO -->
            <div class="row justify-content-center mt-2">
                <div class="col-lg-11">
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <div class="card card-money text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-cash-stack" style="font-size:45px;color:#198754;"></i>
                                    <h3 class="mt-2 text-success">$<?php echo number_format($total_pagado, 2); ?></h3>
                                    <h5>Total pagado</h5>
                                    <p class="text-secondary mb-0">A choferes</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card card-usuarios text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-graph-up-arrow" style="font-size:45px;color:#0d6efd;"></i>
                                    <h3 class="mt-2 text-primary">$<?php echo number_format($total_ingresos, 2); ?></h3>
                                    <h5>Ingresos</h5>
                                    <p class="text-secondary mb-0">Empresa (Traslados)</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card card-ganancias text-center h-100">
                                <div class="card-body">
                                    <i class="bi bi-piggy-bank-fill" style="font-size:45px;color:#dc3545;"></i>
                                    <h3 class="mt-2 text-danger">$<?php echo number_format($ganancia_neta, 2); ?></h3>
                                    <h5>Ganancia neta</h5>
                                    <p class="text-secondary mb-0">Acumulada</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- FILTROS CON DESDE Y HASTA -->
            <div class="row justify-content-center mt-3">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-search me-2"></i> Buscar pagos
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="pagos.php" class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Buscar</label>
                                    <input type="text" name="buscar" class="form-control" placeholder="Nombre de chofer o n° de referencia" value="<?php echo htmlspecialchars($buscar); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Fecha Desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" value="<?php echo htmlspecialchars($fecha_desde); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Buscar
                                    </button>
                                    <?php if (!empty($buscar) || !empty($fecha_desde) || !empty($fecha_hasta)): ?>
                                        <a href="pagos.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTADO DE PAGOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-cash-coin me-2"></i> Pagos registrados (<?php echo count($pagos_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Chofer</th>
                                            <th>Banco / N° Cuenta</th>
                                            <th>Referencia</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pagos_list)): ?>
                                            <?php foreach ($pagos_list as $p): ?>
                                                <?php 
                                                // Clean "REF-" prefix from reference display if present
                                                $ref_limpia = preg_replace('/^REF-?/i', '', $p['numero_referencia']);
                                                ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($p['chofer_nombres'] . ' ' . $p['chofer_apellidos']); ?></div>
                                                        <small class="text-muted">C.I: <?php echo htmlspecialchars($p['cedula']); ?></small>
                                                    </td>
                                                    <td>
                                                        <div><?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificado'); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($p['numero_cuenta'] ?? 'N/A'); ?></small>
                                                    </td>
                                                    <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($ref_limpia); ?></span></td>
                                                    <td class="fw-bold text-success">$<?php echo number_format($p['monto_pagado'], 2); ?></td>
                                                    <td><span class="badge bg-success">Procesado</span></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalVerPago<?php echo $p['id_pago']; ?>">
                                                            <i class="bi bi-eye"></i> Detalle
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron pagos registrados con los filtros seleccionados.
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
<?php foreach ($pagos_list as $p): ?>
    <?php $ref_limpia = preg_replace('/^REF-?/i', '', $p['numero_referencia']); ?>
    <!-- MODAL DETALLE DE PAGO -->
    <div class="modal fade" id="modalVerPago<?php echo $p['id_pago']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Comprobante de Pago #<?php echo $p['id_pago']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>N° Referencia:</strong> <span class="badge bg-secondary font-monospace fs-6"><?php echo htmlspecialchars($ref_limpia); ?></span></p>
                    <p><strong>Fecha de Pago:</strong> <?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></p>
                    <p><strong>Chofer Beneficiario:</strong> <?php echo htmlspecialchars($p['chofer_nombres'] . ' ' . $p['chofer_apellidos']); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($p['cedula']); ?></p>
                    <p><strong>Banco:</strong> <?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificado'); ?></p>
                    <p><strong>N° Cuenta:</strong> <?php echo htmlspecialchars($p['numero_cuenta'] ?? 'N/A'); ?></p>
                    <hr>
                    <p><strong>Monto Pagado:</strong> <span class="fs-4 text-success fw-bold">$<?php echo number_format($p['monto_pagado'], 2); ?></span></p>
                    <p><strong>Procesado por:</strong> <?php echo htmlspecialchars(($p['adm_nombres'] ?? 'Sistema') . ' ' . ($p['adm_apellidos'] ?? '')); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- MODAL REGISTRAR NUEVO PAGO -->
<div class="modal fade" id="modalNuevoPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="../../procesos/registrar_pago_chofer.php">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Registrar Pago a Chofer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Seleccionar Chofer *</label>
                        <select name="id_chofer" class="form-select" required>
                            <option value="">-- Seleccione un chofer --</option>
                            <?php while ($ch_item = mysqli_fetch_assoc($res_choferes_list)): ?>
                                <option value="<?php echo $ch_item['id_chofer']; ?>">
                                    <?php echo htmlspecialchars($ch_item['nombres'] . ' ' . $ch_item['apellidos'] . ' (' . ($ch_item['nombre_banco'] ?? 'S/B') . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Número de Referencia *</label>
                        <input type="text" name="numero_referencia" class="form-control" placeholder="Ej: 987654" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Monto a Pagar ($) *</label>
                        <input type="number" step="0.01" min="0.01" name="monto_pagado" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Fecha de Pago *</label>
                        <input type="date" name="fecha_pago" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold">Procesar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>