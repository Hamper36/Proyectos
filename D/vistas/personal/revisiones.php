<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario_sesion = $_SESSION["id_usuario"];

$sql = "SELECT id_personal FROM personal_administrativo WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario_sesion);
$stmt->execute();
$id_personal = $stmt->get_result()->fetch_assoc()["id_personal"];

/*=========================
VEHÍCULOS PENDIENTES DE REVISIÓN
=========================*/

$sql = "SELECT v.id_vehiculo, v.placa, v.marca, v.modelo, u.nombres, u.apellidos,
               (SELECT MAX(fecha_revision) FROM revision_vehiculo r WHERE r.id_vehiculo = v.id_vehiculo) AS ultima_revision
        FROM vehiculo v
        INNER JOIN chofer c ON c.id_chofer = v.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        WHERE v.estado_vehiculo = 'Pendiente'
        ORDER BY v.id_vehiculo DESC";

$pendientes = $conexion->query($sql);

/*=========================
HISTORIAL DE REVISIONES YA REALIZADAS
(con filtros opcionales)
=========================*/

$filtro_chofer = isset($_GET["chofer"]) ? trim($_GET["chofer"]) : "";
$filtro_desde = isset($_GET["desde"]) ? trim($_GET["desde"]) : "";
$filtro_hasta = isset($_GET["hasta"]) ? trim($_GET["hasta"]) : "";
$filtro_resultado = isset($_GET["resultado"]) ? trim($_GET["resultado"]) : "";

$sql = "SELECT r.id_revision, r.fecha_revision, r.calificacion, r.observacion, r.resultado,
               v.id_vehiculo, v.placa, v.marca, v.modelo, v.anio, v.color, v.estado_vehiculo,
               u.nombres, u.apellidos, u.cedula, u.correo, u.telefono,
               pa.nombres AS p_nombres, pa.apellidos AS p_apellidos
        FROM revision_vehiculo r
        INNER JOIN vehiculo v ON v.id_vehiculo = r.id_vehiculo
        INNER JOIN chofer c ON c.id_chofer = v.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        LEFT JOIN personal_administrativo p ON p.id_personal = r.id_personal
        LEFT JOIN usuario pa ON pa.id_usuario = p.id_usuario
        WHERE 1=1";

$params = [];
$tipos = "";

if ($filtro_chofer != "") {
    $sql .= " AND CONCAT(u.nombres, ' ', u.apellidos) LIKE ?";
    $params[] = "%" . $filtro_chofer . "%";
    $tipos .= "s";
}
if ($filtro_desde != "") {
    $sql .= " AND r.fecha_revision >= ?";
    $params[] = $filtro_desde;
    $tipos .= "s";
}
if ($filtro_hasta != "") {
    $sql .= " AND r.fecha_revision <= ?";
    $params[] = $filtro_hasta;
    $tipos .= "s";
}
if ($filtro_resultado != "" && $filtro_resultado != "Todos") {
    $sql .= " AND r.resultado = ?";
    $params[] = $filtro_resultado;
    $tipos .= "s";
}

$sql .= " ORDER BY r.fecha_revision DESC";

$stmt = $conexion->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($tipos, ...$params);
}
$stmt->execute();
$historial = $stmt->get_result();

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_personal.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard">
            </div>

            <div class="modulo-header">
                <h2>Revisiones Vehiculares</h2>
                <p>Administre las solicitudes y revisiones técnicas de los vehículos registrados.</p>
            </div>

            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success text-center">Revisión registrada correctamente.</div>
            <?php } ?>

            <!-- FILTROS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card card-form">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:10px 15px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-funnel-fill me-2"></i>
                                Filtros de búsqueda
                            </h5>
                        </div>

                        <div class="card-body py-2">
                            <form method="GET">
                                <div class="row align-items-end">

                                    <div class="col-md-3 mb-1">
                                        <label class="form-label mb-1">Chofer</label>
                                        <input type="text" name="chofer" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_chofer); ?>">
                                    </div>

                                    <div class="col-md-2 mb-1">
                                        <label class="form-label mb-1">Desde</label>
                                        <input type="date" name="desde" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                    </div>

                                    <div class="col-md-2 mb-1">
                                        <label class="form-label mb-1">Hasta</label>
                                        <input type="date" name="hasta" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                    </div>

                                    <div class="col-md-3 mb-1">
                                        <label class="form-label mb-1">Resultado</label>
                                        <select name="resultado" class="form-select">
                                            <option <?php echo $filtro_resultado == "" ? "selected" : ""; ?>>Todos</option>
                                            <option <?php echo $filtro_resultado == "Apto" ? "selected" : ""; ?>>Apto</option>
                                            <option <?php echo $filtro_resultado == "No Apto" ? "selected" : ""; ?>>No Apto</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-1 d-grid">
                                        <button type="submit" class="btn btn-decarrerita btn-sm">
                                            <i class="bi bi-search me-1"></i>
                                            Buscar
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:15px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Historial de revisiones vehiculares
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Vehículo</th>
                                            <th>Fecha</th>
                                            <th>Calificación</th>
                                            <th>Resultado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($historial->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-secondary">
                                                    No se encontraron revisiones.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($h = $historial->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($h["nombres"] . " " . $h["apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($h["marca"] . " " . $h["modelo"]); ?></td>
                                                <td><?php echo date("d/m/Y", strtotime($h["fecha_revision"])); ?></td>
                                                <td><?php echo $h["calificacion"]; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $h["resultado"] == "Apto" ? "bg-success" : "bg-danger"; ?>">
                                                        <?php echo $h["resultado"]; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalVerRevision<?php echo $h['id_revision']; ?>">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </button>

                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalHistorialVehiculo<?php echo $h['id_vehiculo']; ?>">
                                                        <i class="bi bi-clock-history"></i> Historial
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

            <!-- SOLICITUDES PENDIENTES -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:15px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Vehículos pendientes de revisión
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Vehículo</th>
                                            <th>Placa</th>
                                            <th>Última revisión</th>
                                            <th>Motivo</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($pendientes->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-secondary">
                                                    No hay vehículos pendientes de revisión.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($p = $pendientes->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($p["nombres"] . " " . $p["apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($p["marca"] . " " . $p["modelo"]); ?></td>
                                                <td><?php echo htmlspecialchars($p["placa"]); ?></td>
                                                <td><?php echo $p["ultima_revision"] ? date("d/m/Y", strtotime($p["ultima_revision"])) : "—"; ?></td>
                                                <td>
                                                    <?php if ($p["ultima_revision"]) { ?>
                                                        <span class="badge bg-warning text-dark">Revisión vencida</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-info text-dark">Vehículo nuevo</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalRevisar<?php echo $p['id_vehiculo']; ?>">
                                                        <i class="bi bi-check-circle"></i>
                                                        Revisar
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

        </div>
    </div>
</div>

<?php
// Modales para ver detalle e historial de cada revisión realizada
$historial->data_seek(0);
while ($h = $historial->fetch_assoc()) {
?>

<!-- MODAL DETALLE DE REVISIÓN -->
<div class="modal fade" id="modalVerRevision<?php echo $h['id_revision']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title"><i class="bi bi-car-front-fill me-2"></i>Detalle de Revisión Vehicular</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <p><strong>Chofer:</strong> <?php echo htmlspecialchars($h['nombres'] . ' ' . $h['apellidos']); ?></p>
                <p><strong>Cédula:</strong> <?php echo htmlspecialchars($h['cedula']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($h['telefono'] ?? 'S/N'); ?></p>
                <hr>
                <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($h['marca'] . ' ' . $h['modelo']); ?> (Año <?php echo htmlspecialchars($h['anio']); ?> - <?php echo htmlspecialchars($h['color']); ?>)</p>
                <p><strong>Placa:</strong> <span class="badge bg-dark fs-6"><?php echo htmlspecialchars($h['placa']); ?></span></p>
                <hr>
                <p><strong>Fecha de Revisión:</strong> <?php echo date("d/m/Y", strtotime($h['fecha_revision'])); ?></p>
                <p><strong>Calificación:</strong> <?php echo $h['calificacion']; ?> / 100</p>
                <p><strong>Resultado:</strong> <span class="badge <?php echo $h['resultado'] == 'Apto' ? 'bg-success' : 'bg-danger'; ?>"><?php echo htmlspecialchars($h['resultado']); ?></span></p>
                <p><strong>Revisado por:</strong> <?php echo !empty($h['p_nombres']) ? htmlspecialchars($h['p_nombres'] . ' ' . $h['p_apellidos']) : 'Personal Administrativo'; ?></p>
                <hr>
                <p><strong>Observación:</strong></p>
                <div class="text-muted bg-light p-3 rounded"><?php echo !empty($h['observacion']) ? nl2br(htmlspecialchars($h['observacion'])) : 'Sin observaciones registradas.'; ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HISTORIAL COMPLETO DEL VEHÍCULO -->
<div class="modal fade" id="modalHistorialVehiculo<?php echo $h['id_vehiculo']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Historial de Inspecciones - Placa <?php echo htmlspecialchars($h['placa']); ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Calificación</th>
                                <th>Resultado</th>
                                <th>Evaluador</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_hv = "SELECT r.fecha_revision, r.calificacion, r.resultado, r.observacion,
                                              pa.nombres AS p_nombres, pa.apellidos AS p_apellidos
                                       FROM revision_vehiculo r
                                       LEFT JOIN personal_administrativo p ON p.id_personal = r.id_personal
                                       LEFT JOIN usuario pa ON pa.id_usuario = p.id_usuario
                                       WHERE r.id_vehiculo = ?
                                       ORDER BY r.fecha_revision DESC, r.id_revision DESC";
                            $stmt_hv = $conexion->prepare($sql_hv);
                            $stmt_hv->bind_param("i", $h['id_vehiculo']);
                            $stmt_hv->execute();
                            $res_hv = $stmt_hv->get_result();

                            if ($res_hv->num_rows == 0) {
                                echo "<tr><td colspan='5' class='text-muted'>Sin historial de inspecciones.</td></tr>";
                            } else {
                                while ($rev = $res_hv->fetch_assoc()) {
                                    $badge_r = ($rev['resultado'] == 'Apto') ? 'bg-success' : 'bg-danger';
                                    echo "<tr>";
                                    echo "<td>" . date("d/m/Y", strtotime($rev['fecha_revision'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($rev['calificacion']) . " / 100</td>";
                                    echo "<td><span class='badge {$badge_r}'>" . htmlspecialchars($rev['resultado']) . "</span></td>";
                                    echo "<td>" . (!empty($rev['p_nombres']) ? htmlspecialchars($rev['p_nombres'] . ' ' . $rev['p_apellidos']) : 'Personal Admin') . "</td>";
                                    echo "<td class='text-start'>" . (!empty($rev['observacion']) ? htmlspecialchars($rev['observacion']) : '—') . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php } ?>

<?php
// Modales para revisar cada vehículo pendiente
$pendientes->data_seek(0);
while ($p = $pendientes->fetch_assoc()) {
?>

<div class="modal fade" id="modalRevisar<?php echo $p['id_vehiculo']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/revisar_vehiculo.php" method="POST">
                <input type="hidden" name="id_vehiculo" value="<?php echo $p['id_vehiculo']; ?>">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Revisar Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p><strong>Chofer:</strong> <?php echo htmlspecialchars($p["nombres"] . " " . $p["apellidos"]); ?></p>
                    <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($p["marca"] . " " . $p["modelo"]); ?> (<?php echo htmlspecialchars($p["placa"]); ?>)</p>

                    <div class="mb-3">
                        <label class="form-label">Fecha de Revisión</label>
                        <input type="date" name="fecha_revision" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calificación (0 - 100)</label>
                        <input type="number" name="calificacion" class="form-control" min="0" max="100" oninput="if(this.value !== '') { if(parseInt(this.value) < 0) this.value = 0; if(parseInt(this.value) > 100) this.value = 100; }" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observación</label>
                        <textarea name="observacion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="alert alert-light border mt-3 mb-0">
                        <strong>Resultado automático del sistema:</strong><br><br>
                        ✔ Revisión del vehículo mayor o igual a 65 puntos.<br><br>
                        Si se cumple con el requisito de calificación, la cuenta se activará automáticamente. Si falla, la solicitud quedará rechazada.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Revisión</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php } ?>

<?php include("../../includes/footer.php"); ?>