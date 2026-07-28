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

$res_p = $conexion->query("SELECT COALESCE(SUM(monto_pagado), 0) AS total FROM pago_chofer");
$total_pagado = $res_p->fetch_assoc()["total"];

$res_i = $conexion->query("SELECT COALESCE(SUM(monto_empresa), 0) AS total FROM traslado WHERE estado_traslado = 'Finalizado'");
$total_ingresos = $res_i->fetch_assoc()["total"];

// La ganancia de la empresa es su propio 30%, no se resta lo pagado a choferes (eso es dinero aparte, del chofer)
$ganancia_neta = $total_ingresos;

/*=========================
TRASLADOS FINALIZADOS SIN PAGAR
(igual que en personal/pagos.php, para mantener
 la trazabilidad pago-traslado en todo el sistema)
=========================*/

$sql = "SELECT t.id_traslado, t.fecha_hora, t.monto_chofer, t.punto_origen, t.punto_destino,
               uc.nombres AS chofer_nombres, uc.apellidos AS chofer_apellidos,
               ucl.nombres AS cliente_nombres, ucl.apellidos AS cliente_apellidos
        FROM traslado t
        INNER JOIN chofer c ON c.id_chofer = t.id_chofer
        INNER JOIN usuario uc ON uc.id_usuario = c.id_usuario
        INNER JOIN cliente cl ON cl.id_cliente = t.id_cliente
        INNER JOIN usuario ucl ON ucl.id_usuario = cl.id_usuario
        WHERE t.estado_traslado = 'Finalizado'
        AND t.id_traslado NOT IN (
            SELECT id_traslado FROM pago_chofer WHERE id_traslado IS NOT NULL
        )
        ORDER BY t.fecha_hora ASC";

$sin_pagar = $conexion->query($sql);

/*=========================
FILTROS DE BÚSQUEDA (historial)
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
} elseif (!empty($fecha_desde)) {
    $where_clauses[] = "p.fecha_pago >= ?";
    $params[] = $fecha_desde;
    $types .= "s";
} elseif (!empty($fecha_hasta)) {
    $where_clauses[] = "p.fecha_pago <= ?";
    $params[] = $fecha_hasta;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

/*=========================
HISTORIAL DE PAGOS
(con datos del traslado/cliente vinculado)
=========================*/

$sql = "SELECT p.id_pago, p.fecha_pago, p.numero_referencia, p.monto_pagado,
               u.nombres AS chofer_nombres, u.apellidos AS chofer_apellidos, u.cedula,
               b.nombre_banco, ch.numero_cuenta,
               up.nombres AS adm_nombres, up.apellidos AS adm_apellidos,
               t.punto_origen, t.punto_destino,
               ucl.nombres AS cliente_nombres, ucl.apellidos AS cliente_apellidos
        FROM pago_chofer p
        INNER JOIN chofer ch ON ch.id_chofer = p.id_chofer
        INNER JOIN usuario u ON u.id_usuario = ch.id_usuario
        LEFT JOIN banco b ON b.id_banco = ch.id_banco
        LEFT JOIN personal_administrativo pa ON pa.id_personal = p.id_personal
        LEFT JOIN usuario up ON up.id_usuario = pa.id_usuario
        LEFT JOIN traslado t ON t.id_traslado = p.id_traslado
        LEFT JOIN cliente cl ON cl.id_cliente = t.id_cliente
        LEFT JOIN usuario ucl ON ucl.id_usuario = cl.id_usuario
        WHERE $where_sql
        ORDER BY p.id_pago DESC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$pagos = $stmt->get_result();

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <div class="modulo-header">
                <h2>Gestión de Pagos</h2>
                <p>Consulte y supervise todos los pagos realizados a los choferes.</p>
            </div>

            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success text-center">Pago registrado correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["error"]) && $_GET["error"] == "referencia") { ?>
                <div class="alert alert-danger text-center">Ese número de referencia ya fue utilizado.</div>
            <?php } ?>

            <?php if (isset($_GET["error"]) && $_GET["error"] == "ya_pagado") { ?>
                <div class="alert alert-danger text-center">Este traslado ya fue pagado anteriormente.</div>
            <?php } ?>

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

            <!-- TRASLADOS PENDIENTES DE PAGO -->
            <div class="row justify-content-center mt-2">
                <div class="col-lg-11">
                    <div class="card card-money">

                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-cash-stack me-2"></i>
                                Traslados pendientes de pago
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Cliente</th>
                                            <th>Origen → Destino</th>
                                            <th>Fecha</th>
                                            <th>Monto a pagar</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($sin_pagar->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-secondary">
                                                    No hay traslados pendientes de pago.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($tr = $sin_pagar->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($tr["chofer_nombres"] . " " . $tr["chofer_apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($tr["cliente_nombres"] . " " . $tr["cliente_apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($tr["punto_origen"] . " → " . $tr["punto_destino"]); ?></td>
                                                <td><?php echo date("d/m/Y", strtotime($tr["fecha_hora"])); ?></td>
                                                <td>$<?php echo number_format($tr["monto_chofer"], 2); ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalPagar<?php echo $tr['id_traslado']; ?>">
                                                        <i class="bi bi-cash-stack"></i>
                                                        Pagar
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

            <!-- FILTROS -->
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
                                    <?php if (!empty($buscar) || !empty($fecha_desde) || !empty($fecha_hasta)) { ?>
                                        <a href="pagos.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php } ?>
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
                                <i class="bi bi-cash-coin me-2"></i> Pagos registrados (<?php echo $pagos->num_rows; ?>)
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
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($pagos->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron pagos registrados con los filtros seleccionados.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($p = $pagos->fetch_assoc()) { ?>
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
                                                <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($p['numero_referencia']); ?></span></td>
                                                <td class="fw-bold text-success">$<?php echo number_format($p['monto_pagado'], 2); ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalVerPago<?php echo $p['id_pago']; ?>">
                                                        <i class="bi bi-eye"></i> Detalle
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

            <div class="mb-5"></div>
        </div>
    </div>
</div>

<!-- MODALES: PAGAR TRASLADO PENDIENTE -->
<?php
$sin_pagar->data_seek(0);
while ($tr = $sin_pagar->fetch_assoc()) {
?>
<div class="modal fade" id="modalPagar<?php echo $tr['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/registrar_pago.php" method="POST">
                <input type="hidden" name="id_traslado" value="<?php echo $tr['id_traslado']; ?>">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Pagar Traslado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p><strong>Chofer:</strong> <?php echo htmlspecialchars($tr["chofer_nombres"] . " " . $tr["chofer_apellidos"]); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($tr["cliente_nombres"] . " " . $tr["cliente_apellidos"]); ?></p>
                    <p><strong>Trayecto:</strong> <?php echo htmlspecialchars($tr["punto_origen"] . " → " . $tr["punto_destino"]); ?></p>
                    <p><strong>Monto a pagar:</strong> $<?php echo number_format($tr["monto_chofer"], 2); ?></p>

                    <div class="mb-3">
                        <label class="form-label">Fecha de pago</label>
                        <input type="date" name="fecha_pago" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número de referencia</label>
                        <input type="text" name="numero_referencia" class="form-control" maxlength="13" pattern="[0-9]{1,13}" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Pago</button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php } ?>

<!-- MODALES: DETALLE DE PAGO -->
<?php
$pagos->data_seek(0);
while ($p = $pagos->fetch_assoc()) {
?>
<div class="modal fade" id="modalVerPago<?php echo $p['id_pago']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Comprobante de Pago #<?php echo $p['id_pago']; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <p><strong>N° Referencia:</strong> <span class="badge bg-secondary font-monospace fs-6"><?php echo htmlspecialchars($p['numero_referencia']); ?></span></p>
                <p><strong>Fecha de Pago:</strong> <?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></p>
                <p><strong>Chofer Beneficiario:</strong> <?php echo htmlspecialchars($p['chofer_nombres'] . ' ' . $p['chofer_apellidos']); ?></p>
                <p><strong>Cédula:</strong> <?php echo htmlspecialchars($p['cedula']); ?></p>
                <p><strong>Banco:</strong> <?php echo htmlspecialchars($p['nombre_banco'] ?? 'No especificado'); ?></p>
                <p><strong>N° Cuenta:</strong> <?php echo htmlspecialchars($p['numero_cuenta'] ?? 'N/A'); ?></p>
                <?php if ($p['cliente_nombres']) { ?>
                    <hr>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($p['cliente_nombres'] . ' ' . $p['cliente_apellidos']); ?></p>
                    <p><strong>Trayecto:</strong> <?php echo htmlspecialchars($p['punto_origen'] . ' → ' . $p['punto_destino']); ?></p>
                <?php } ?>
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
<?php } ?>

<?php include("../../includes/footer.php"); ?>