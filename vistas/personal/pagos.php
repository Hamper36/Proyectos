<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
FILTROS DE BÚSQUEDA Y PERÍODO DE TIEMPO
=========================*/
$filtro_chofer = intval($_GET['id_chofer'] ?? 0);
$filtro_desde = trim($_GET['desde'] ?? date('Y-m-01'));
$filtro_hasta = trim($_GET['hasta'] ?? date('Y-m-d'));

/*=========================
CÁLCULO DE GANANCIAS EMPRESA Y PAGOS EN EL PERÍODO
=========================*/
// 1. Recaudado por la empresa en el período
$stmt_rec = $conexion->prepare("SELECT COALESCE(SUM(monto_empresa), 0) AS total FROM traslado WHERE estado_traslado = 'Finalizado' AND DATE(fecha_hora) BETWEEN ? AND ?");
$stmt_rec->bind_param("ss", $filtro_desde, $filtro_hasta);
$stmt_rec->execute();
$recaudado_empresa = $stmt_rec->get_result()->fetch_assoc()['total'] ?? 0;

// 2. Total pagado en el período (opcionalmente filtrado por chofer)
$where_p = ["p.fecha_pago BETWEEN ? AND ?"];
$params_p = [$filtro_desde, $filtro_hasta];
$types_p = "ss";

if ($filtro_chofer > 0) {
    $where_p[] = "p.id_chofer = ?";
    $params_p[] = $filtro_chofer;
    $types_p .= "i";
}

$where_p_sql = implode(" AND ", $where_p);

$stmt_pag = $conexion->prepare("SELECT COALESCE(SUM(p.monto_pagado), 0) AS total FROM pago_chofer p WHERE $where_p_sql");
$stmt_pag->bind_param($types_p, ...$params_p);
$stmt_pag->execute();
$total_cancelado_periodo = $stmt_pag->get_result()->fetch_assoc()['total'] ?? 0;

/*=========================
CONSULTA DE HISTORIAL DE PAGOS
=========================*/
$sql_hist = "SELECT p.*, u.nombres, u.apellidos, u.cedula, b.nombre_banco 
             FROM pago_chofer p 
             INNER JOIN chofer ch ON ch.id_chofer = p.id_chofer 
             INNER JOIN usuario u ON u.id_usuario = ch.id_usuario 
             LEFT JOIN banco b ON b.id_banco = ch.id_banco 
             WHERE $where_p_sql 
             ORDER BY p.id_pago DESC";

$stmt_list = $conexion->prepare($sql_hist);
$stmt_list->bind_param($types_p, ...$params_p);
$stmt_list->execute();
$result_pagos = $stmt_list->get_result();

$pagos_list = [];
while ($r = $result_pagos->fetch_assoc()) {
    $pagos_list[] = $r;
}

// Lista de choferes para los selects
$res_choferes_select = mysqli_query($conexion, "SELECT ch.id_chofer, u.nombres, u.apellidos, u.cedula 
                                               FROM chofer ch 
                                               INNER JOIN usuario u ON u.id_usuario = ch.id_usuario 
                                               ORDER BY u.nombres ASC");

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
                <h2>Gestión de Pagos a Choferes</h2>
                <p>Registre pagos realizados a los choferes y consulte reportes de recaudación por período de tiempo.</p>
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

            <!-- RESUMEN DE GANANCIAS Y LO CANCELADO DADO UN PERÍODO DE TIEMPO -->
            <div class="row justify-content-center mt-2">
                <div class="col-lg-11">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card card-usuarios text-center h-100 p-2 shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-graph-up-arrow" style="font-size:40px;color:#0d6efd;"></i>
                                    <h3 class="mt-2 text-primary">$<?php echo number_format($recaudado_empresa, 2); ?></h3>
                                    <h5>Recaudado por la Empresa (30%)</h5>
                                    <p class="text-secondary mb-0">En el período (<?php echo date('d/m/Y', strtotime($filtro_desde)); ?> - <?php echo date('d/m/Y', strtotime($filtro_hasta)); ?>)</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-money text-center h-100 p-2 shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-cash-stack" style="font-size:40px;color:#198754;"></i>
                                    <h3 class="mt-2 text-success">$<?php echo number_format($total_cancelado_periodo, 2); ?></h3>
                                    <h5>Total Cancelado a Choferes</h5>
                                    <p class="text-secondary mb-0">
                                        <?php echo ($filtro_chofer > 0) ? 'Al chofer seleccionado' : 'En el período filtrado'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REGISTRAR PAGO AL CHOFER -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card card-money shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-cash-stack me-2"></i> Registrar pago al chofer
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="../../procesos/registrar_pago_chofer.php" class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Chofer *</label>
                                    <select name="id_chofer" class="form-select" required>
                                        <option value="" selected disabled>Seleccione un chofer...</option>
                                        <?php 
                                        mysqli_data_seek($res_choferes_select, 0);
                                        while ($ch = mysqli_fetch_assoc($res_choferes_select)): 
                                        ?>
                                            <option value="<?php echo $ch['id_chofer']; ?>">
                                                <?php echo htmlspecialchars($ch['nombres'] . ' ' . $ch['apellidos'] . ' (' . $ch['cedula'] . ')'); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Fecha de Pago *</label>
                                    <input type="date" name="fecha_pago" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">N° Referencia *</label>
                                    <input type="text" name="numero_referencia" class="form-control" placeholder="Ej: 987654" required>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Monto a pagar ($) *</label>
                                    <input type="number" step="0.01" min="0.01" name="monto_pagado" class="form-control" placeholder="0.00" required>
                                </div>

                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-decarrerita fw-bold">
                                        <i class="bi bi-cash-stack me-1"></i> Registrar pago
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTRO POR PERÍODO Y CHOFER ESPECÍFICO -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card card-form shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-funnel me-2"></i> Consultar lo cancelado por período y chofer
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="pagos.php" class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Chofer Específico</label>
                                    <select name="id_chofer" class="form-select">
                                        <option value="0">-- Todos los choferes --</option>
                                        <?php 
                                        mysqli_data_seek($res_choferes_select, 0);
                                        while ($ch = mysqli_fetch_assoc($res_choferes_select)): 
                                        ?>
                                            <option value="<?php echo $ch['id_chofer']; ?>" <?php echo ($filtro_chofer == $ch['id_chofer']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($ch['nombres'] . ' ' . $ch['apellidos']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Desde</label>
                                    <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Hasta</label>
                                    <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                </div>

                                <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Filtrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL DE PAGOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-clock-history me-2"></i> Historial de pagos efectuados (<?php echo count($pagos_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Fecha de Pago</th>
                                            <th>Entidad Bancaria</th>
                                            <th>Referencia</th>
                                            <th>Monto Pagado</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pagos_list)): ?>
                                            <?php foreach ($pagos_list as $p): ?>
                                                <?php $ref = preg_replace('/^REF-?/i', '', $p['numero_referencia']); ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']); ?></div>
                                                        <small class="text-muted">C.I: <?php echo htmlspecialchars($p['cedula']); ?></small>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td>
                                                    <td><?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificado'); ?></td>
                                                    <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($ref); ?></span></td>
                                                    <td class="fw-bold text-success">$<?php echo number_format($p['monto_pagado'], 2); ?></td>
                                                    <td><span class="badge bg-success">Pagado</span></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalPagoPersonal<?php echo $p['id_pago']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No hay pagos registrados con los filtros seleccionados.
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
<?php foreach ($pagos_list as $p): ?>
    <?php $ref = preg_replace('/^REF-?/i', '', $p['numero_referencia']); ?>
    <div class="modal fade" id="modalPagoPersonal<?php echo $p['id_pago']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Detalle de Pago #<?php echo $p['id_pago']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Chofer:</strong> <?php echo htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($p['cedula']); ?></p>
                    <p><strong>Fecha de Pago:</strong> <?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></p>
                    <p><strong>Entidad Bancaria:</strong> <?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificada'); ?></p>
                    <p><strong>N° Referencia:</strong> <span class="badge bg-secondary font-monospace fs-6"><?php echo htmlspecialchars($ref); ?></span></p>
                    <hr>
                    <p><strong>Monto Pagado:</strong> <span class="fs-4 text-success fw-bold">$<?php echo number_format($p['monto_pagado'], 2); ?></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>
