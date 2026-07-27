<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
FILTROS DE REPORTES
=========================*/
$tipo = $_GET['tipo'] ?? 'General';
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

/*=========================
CÁLCULO DE ESTADÍSTICAS GENERALES
=========================*/
// 1. Traslados por Estado
$stmt_t = $conexion->prepare("SELECT estado_traslado, COUNT(*) AS cantidad, COALESCE(SUM(monto_empresa), 0) AS ganancias 
                             FROM traslado 
                             WHERE DATE(fecha_hora) BETWEEN ? AND ? 
                             GROUP BY estado_traslado");
$stmt_t->bind_param("ss", $desde, $hasta);
$stmt_t->execute();
$res_t = $stmt_t->get_result();

$traslados_stats = ['Pendiente' => 0, 'En curso' => 0, 'Finalizado' => 0, 'Cancelado' => 0];
$ganancias_periodo = 0;
while ($row = $res_t->fetch_assoc()) {
    $traslados_stats[$row['estado_traslado']] = (int)$row['cantidad'];
    if ($row['estado_traslado'] == 'Finalizado') {
        $ganancias_periodo = (float)$row['ganancias'];
    }
}

// 2. Usuarios por Rol
$res_u = mysqli_query($conexion, "SELECT r.nombre_rol, COUNT(u.id_usuario) AS cantidad 
                                  FROM rol r 
                                  LEFT JOIN usuario u ON u.id_rol = r.id_rol 
                                  GROUP BY r.id_rol, r.nombre_rol");
$usuarios_stats = [];
while ($row = mysqli_fetch_assoc($res_u)) {
    $usuarios_stats[$row['nombre_rol']] = (int)$row['cantidad'];
}

// 3. Vehículos por Inspección
$res_v = mysqli_query($conexion, "SELECT COALESCE(rv.resultado, 'Pendiente') AS estado, COUNT(v.id_vehiculo) AS cantidad 
                                  FROM vehiculo v 
                                  LEFT JOIN revision_vehiculo rv ON (rv.id_vehiculo = v.id_vehiculo AND rv.id_revision = (
                                      SELECT MAX(id_revision) FROM revision_vehiculo WHERE id_vehiculo = v.id_vehiculo
                                  ))
                                  GROUP BY estado");
$vehiculos_stats = ['Apto' => 0, 'Pendiente' => 0, 'No Apto' => 0];
while ($row = mysqli_fetch_assoc($res_v)) {
    $vehiculos_stats[$row['estado']] = (int)$row['cantidad'];
}

// 4. Pagos procesados en el periodo
$stmt_p = $conexion->prepare("SELECT COUNT(*) AS total_pagos, COALESCE(SUM(monto_pagado), 0) AS suma_pagos 
                             FROM pago_chofer 
                             WHERE fecha_pago BETWEEN ? AND ?");
$stmt_p->bind_param("ss", $desde, $hasta);
$stmt_p->execute();
$pagos_info = $stmt_p->get_result()->fetch_assoc();

/*=========================
CONSULTAS DETALLADAS SEGÚN TIPO
=========================*/
$reporte_detallado = [];

if ($tipo == 'Usuarios') {
    $stmt_det = $conexion->prepare("SELECT u.*, r.nombre_rol FROM usuario u JOIN rol r ON r.id_rol = u.id_rol WHERE DATE(u.fecha_creacion) BETWEEN ? AND ? ORDER BY u.id_usuario DESC");
    $stmt_det->bind_param("ss", $desde, $hasta);
    $stmt_det->execute();
    $reporte_detallado = $stmt_det->get_result()->fetch_all(MYSQLI_ASSOC);
} else if ($tipo == 'Traslados') {
    $stmt_det = $conexion->prepare("SELECT t.*, uc.nombres AS c_nombres, uc.apellidos AS c_apellidos, uch.nombres AS ch_nombres, uch.apellidos AS ch_apellidos, v.placa 
                                   FROM traslado t 
                                   JOIN cliente c ON c.id_cliente = t.id_cliente 
                                   JOIN usuario uc ON uc.id_usuario = c.id_usuario 
                                   JOIN chofer ch ON ch.id_chofer = t.id_chofer 
                                   JOIN usuario uch ON uch.id_usuario = ch.id_usuario 
                                   JOIN vehiculo v ON v.id_vehiculo = t.id_vehiculo 
                                   WHERE DATE(t.fecha_hora) BETWEEN ? AND ? 
                                   ORDER BY t.id_traslado DESC");
    $stmt_det->bind_param("ss", $desde, $hasta);
    $stmt_det->execute();
    $reporte_detallado = $stmt_det->get_result()->fetch_all(MYSQLI_ASSOC);
} else if ($tipo == 'Vehículos') {
    $res_det = mysqli_query($conexion, "SELECT v.*, u.nombres, u.apellidos, COALESCE(rv.resultado, 'Pendiente') AS estado_rev 
                                       FROM vehiculo v 
                                       JOIN chofer ch ON ch.id_chofer = v.id_chofer 
                                       JOIN usuario u ON u.id_usuario = ch.id_usuario 
                                       LEFT JOIN revision_vehiculo rv ON (rv.id_vehiculo = v.id_vehiculo AND rv.id_revision = (
                                           SELECT MAX(id_revision) FROM revision_vehiculo WHERE id_vehiculo = v.id_vehiculo
                                       )) ORDER BY v.id_vehiculo DESC");
    $reporte_detallado = mysqli_fetch_all($res_det, MYSQLI_ASSOC);
} else if ($tipo == 'Pagos') {
    $stmt_det = $conexion->prepare("SELECT p.*, u.nombres, u.apellidos, b.nombre_banco 
                                   FROM pago_chofer p 
                                   JOIN chofer ch ON ch.id_chofer = p.id_chofer 
                                   JOIN usuario u ON u.id_usuario = ch.id_usuario 
                                   LEFT JOIN banco b ON b.id_banco = ch.id_banco 
                                   WHERE p.fecha_pago BETWEEN ? AND ? 
                                   ORDER BY p.id_pago DESC");
    $stmt_det->bind_param("ss", $desde, $hasta);
    $stmt_det->execute();
    $reporte_detallado = $stmt_det->get_result()->fetch_all(MYSQLI_ASSOC);
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
                <h2>Reportes del Sistema</h2>
                <p>Consulte indicadores generales y genere reportes estadísticos de la empresa.</p>
            </div>

            <!-- FILTROS SIN BOTÓN DE IMPRIMIR -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i> Generar reporte
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="reportes.php" class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Tipo de Reporte</label>
                                    <select name="tipo" class="form-select">
                                        <option value="General" <?php echo ($tipo == 'General') ? 'selected' : ''; ?>>General</option>
                                        <option value="Usuarios" <?php echo ($tipo == 'Usuarios') ? 'selected' : ''; ?>>Usuarios</option>
                                        <option value="Traslados" <?php echo ($tipo == 'Traslados') ? 'selected' : ''; ?>>Traslados</option>
                                        <option value="Vehículos" <?php echo ($tipo == 'Vehículos') ? 'selected' : ''; ?>>Vehículos</option>
                                        <option value="Pagos" <?php echo ($tipo == 'Pagos') ? 'selected' : ''; ?>>Pagos</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Desde</label>
                                    <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Hasta</label>
                                    <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta); ?>">
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Generar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TARJETAS DE MÉTRICAS CLAVE -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card text-center p-3 border-start border-primary border-4 shadow-sm">
                                <h6 class="text-muted mb-1">Traslados Finalizados</h6>
                                <h3 class="fw-bold text-primary mb-0"><?php echo $traslados_stats['Finalizado']; ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center p-3 border-start border-success border-4 shadow-sm">
                                <h6 class="text-muted mb-1">Ganancias Período</h6>
                                <h3 class="fw-bold text-success mb-0">$<?php echo number_format($ganancias_periodo, 2); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center p-3 border-start border-warning border-4 shadow-sm">
                                <h6 class="text-muted mb-1">Vehículos Aptos</h6>
                                <h3 class="fw-bold text-warning mb-0"><?php echo $vehiculos_stats['Apto']; ?> / <?php echo array_sum($vehiculos_stats); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center p-3 border-start border-danger border-4 shadow-sm">
                                <h6 class="text-muted mb-1">Pagos a Choferes</h6>
                                <h3 class="fw-bold text-danger mb-0">$<?php echo number_format($pagos_info['suma_pagos'] ?? 0, 2); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VISTA DETALLADA DEL REPORTE GENERADO -->
            <?php if ($tipo != 'General'): ?>
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-11">
                        <div class="card shadow-sm">
                            <div class="card-header text-white" style="background:#1E2E4F;">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-table me-2"></i> Reporte Detallado de <?php echo htmlspecialchars($tipo); ?> (<?php echo count($reporte_detallado); ?> registros)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <?php if ($tipo == 'Usuarios'): ?>
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha Registro</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Usuario</th>
                                                    <th>Correo</th>
                                                    <th>Rol</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reporte_detallado as $item): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($item['fecha_creacion'])); ?></td>
                                                        <td><?php echo htmlspecialchars($item['nombres'] . ' ' . $item['apellidos']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['nombre_usuario']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['correo']); ?></td>
                                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['nombre_rol']); ?></span></td>
                                                        <td><span class="badge bg-success"><?php echo htmlspecialchars($item['estado_usuario']); ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php elseif ($tipo == 'Traslados'): ?>
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha y Hora</th>
                                                    <th>Cliente</th>
                                                    <th>Chofer</th>
                                                    <th>Placa</th>
                                                    <th>Costo Total</th>
                                                    <th>Comisión Empresa</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reporte_detallado as $item): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($item['fecha_hora'])); ?></td>
                                                        <td><?php echo htmlspecialchars($item['c_nombres'] . ' ' . $item['c_apellidos']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['ch_nombres'] . ' ' . $item['ch_apellidos']); ?></td>
                                                        <td><span class="badge bg-dark"><?php echo htmlspecialchars($item['placa']); ?></span></td>
                                                        <td class="fw-bold text-success">$<?php echo number_format($item['costo'], 2); ?></td>
                                                        <td class="fw-bold text-primary">$<?php echo number_format($item['monto_empresa'], 2); ?></td>
                                                        <td><span class="badge bg-success"><?php echo htmlspecialchars($item['estado_traslado']); ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php elseif ($tipo == 'Vehículos'): ?>
                                        <table class="table table-hover align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Placa</th>
                                                    <th>Propietario / Chofer</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Año</th>
                                                    <th>Color</th>
                                                    <th>Estado Revisión</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reporte_detallado as $item): ?>
                                                    <tr>
                                                        <td><span class="badge bg-dark"><?php echo htmlspecialchars($item['placa']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($item['nombres'] . ' ' . $item['apellidos']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['marca']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['modelo']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['anio']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['color']); ?></td>
                                                        <td><span class="badge <?php echo ($item['estado_rev'] == 'Apto') ? 'bg-success' : 'bg-warning text-dark'; ?>"><?php echo htmlspecialchars($item['estado_rev']); ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php elseif ($tipo == 'Pagos'): ?>
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Chofer</th>
                                                    <th>Banco</th>
                                                    <th>Referencia</th>
                                                    <th>Monto Pagado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reporte_detallado as $item): ?>
                                                    <?php $ref = preg_replace('/^REF-?/i', '', $item['numero_referencia']); ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($item['fecha_pago'])); ?></td>
                                                        <td><?php echo htmlspecialchars($item['nombres'] . ' ' . $item['apellidos']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['nombre_banco'] ?? 'N/A'); ?></td>
                                                        <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($ref); ?></span></td>
                                                        <td class="fw-bold text-success">$<?php echo number_format($item['monto_pagado'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- GRÁFICOS Y TABLAS ESTADÍSTICAS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="row g-4">

                        <!-- Gráfico 1: Estado de Traslados -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header text-white" style="background:#1E2E4F;">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i>Traslados por Estado</h6>
                                </div>
                                <div class="card-body d-flex align-items-center justify-content-center p-4">
                                    <canvas id="chartTraslados" style="max-height: 260px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico 2: Distribución de Usuarios por Rol -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header text-white" style="background:#1E2E4F;">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill me-2"></i>Usuarios Registrados por Rol</h6>
                                </div>
                                <div class="card-body d-flex align-items-center justify-content-center p-4">
                                    <canvas id="chartUsuarios" style="max-height: 260px;"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Chart.js Local -->
<script src="<?php echo $base_path; ?>assets/js/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Traslados
    const ctxTraslados = document.getElementById('chartTraslados').getContext('2d');
    new Chart(ctxTraslados, {
        type: 'doughnut',
        data: {
            labels: ['Pendiente', 'En curso', 'Finalizado', 'Cancelado'],
            datasets: [{
                data: [
                    <?php echo $traslados_stats['Pendiente']; ?>,
                    <?php echo $traslados_stats['En curso']; ?>,
                    <?php echo $traslados_stats['Finalizado']; ?>,
                    <?php echo $traslados_stats['Cancelado']; ?>
                ],
                backgroundColor: ['#ffc107', '#0d6efd', '#198754', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Chart Usuarios
    const ctxUsuarios = document.getElementById('chartUsuarios').getContext('2d');
    new Chart(ctxUsuarios, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($usuarios_stats)); ?>,
            datasets: [{
                label: 'Cantidad de Usuarios',
                data: <?php echo json_encode(array_values($usuarios_stats)); ?>,
                backgroundColor: ['#0d6efd', '#1E2E4F', '#198754', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>

<?php include("../../includes/footer.php"); ?>