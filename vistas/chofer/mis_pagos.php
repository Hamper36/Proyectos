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
FILTROS DE BÚSQUEDA DE PAGOS
=========================*/
$desde = trim($_GET['desde'] ?? '');
$hasta = trim($_GET['hasta'] ?? '');

$where_clauses = ["p.id_chofer = ?"];
$params = [$id_chofer];
$types = "i";

if (!empty($desde)) {
    $where_clauses[] = "p.fecha_pago >= ?";
    $params[] = $desde;
    $types .= "s";
}

if (!empty($hasta)) {
    $where_clauses[] = "p.fecha_pago <= ?";
    $params[] = $hasta;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT p.*, b.nombre_banco, u.nombres AS adm_nombres, u.apellidos AS adm_apellidos 
        FROM pago_chofer p 
        INNER JOIN chofer ch ON ch.id_chofer = p.id_chofer 
        LEFT JOIN banco b ON b.id_banco = ch.id_banco 
        LEFT JOIN personal_administrativo pa ON pa.id_personal = p.id_personal 
        LEFT JOIN usuario u ON u.id_usuario = pa.id_usuario 
        WHERE $where_sql 
        ORDER BY p.id_pago DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result_pagos = $stmt->get_result();

$pagos_list = [];
$total_recibido = 0;
while ($r = $result_pagos->fetch_assoc()) {
    $pagos_list[] = $r;
    $total_recibido += (float)$r['monto_pagado'];
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
                <h2>Mis Pagos Recibidos</h2>
                <p>Consulte los pagos y transferencias procesadas por la empresa.</p>
            </div>

            <!-- TARJETA TOTAL -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-4">
                    <div class="card card-money text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-cash-stack" style="font-size:45px;color:#198754;"></i>
                            <h3 class="mt-2 text-success">$<?php echo number_format($total_recibido, 2); ?></h3>
                            <h5>Total Recibido</h5>
                            <p class="text-secondary mb-0">Transferencias de la empresa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTROS POR FECHA -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-calendar-range me-2"></i> Filtrar por período de tiempo
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="mis_pagos.php" class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-bold">Desde</label>
                                    <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde); ?>">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-bold">Hasta</label>
                                    <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta); ?>">
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Filtrar
                                    </button>
                                    <?php if (!empty($desde) || !empty($hasta)): ?>
                                        <a href="mis_pagos.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLA -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-receipt me-2"></i> Historial de Pagos (<?php echo count($pagos_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Banco</th>
                                            <th>N° Referencia</th>
                                            <th>Monto Pagado</th>
                                            <th>Estado</th>
                                            <th class="text-center">Comprobante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pagos_list)): ?>
                                            <?php foreach ($pagos_list as $p): ?>
                                                <?php $ref = preg_replace('/^REF-?/i', '', $p['numero_referencia']); ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td>
                                                    <td><?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificado'); ?></td>
                                                    <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($ref); ?></span></td>
                                                    <td class="fw-bold text-success">$<?php echo number_format($p['monto_pagado'], 2); ?></td>
                                                    <td><span class="badge bg-success">Procesado</span></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalPagoChofer<?php echo $p['id_pago']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No tienes pagos registrados en este período.
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
<?php foreach ($pagos_list as $p): ?>
    <?php $ref = preg_replace('/^REF-?/i', '', $p['numero_referencia']); ?>
    <div class="modal fade" id="modalPagoChofer<?php echo $p['id_pago']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Comprobante de Pago #<?php echo $p['id_pago']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Fecha de Pago:</strong> <?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></p>
                    <p><strong>Entidad Bancaria:</strong> <?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificada'); ?></p>
                    <p><strong>N° Referencia:</strong> <span class="badge bg-secondary font-monospace fs-6"><?php echo htmlspecialchars($ref); ?></span></p>
                    <hr>
                    <p><strong>Monto Recibido:</strong> <span class="fs-4 text-success fw-bold">$<?php echo number_format($p['monto_pagado'], 2); ?></span></p>
                    <p><strong>Procesado por:</strong> <?php echo htmlspecialchars(($p['adm_nombres'] ?? 'Administración') . ' ' . ($p['adm_apellidos'] ?? '')); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>