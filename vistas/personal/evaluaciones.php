<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
LEER FILTROS (si se enviaron)
=========================*/
$filtro_chofer = isset($_GET["chofer"]) ? trim($_GET["chofer"]) : "";
$filtro_desde = isset($_GET["desde"]) ? trim($_GET["desde"]) : "";
$filtro_hasta = isset($_GET["hasta"]) ? trim($_GET["hasta"]) : "";
$filtro_resultado = isset($_GET["resultado"]) ? trim($_GET["resultado"]) : "";

/*=========================
CONSTRUIR CONSULTA DINÁMICA
=========================*/
$sql = "SELECT e.id_evaluacion, e.fecha_evaluacion, e.calificacion, e.observacion, e.resultado,
               u.nombres, u.apellidos, u.cedula, adm.nombres AS adm_nombres, adm.apellidos AS adm_apellidos 
        FROM evaluacion_psicologica e
        INNER JOIN chofer c ON c.id_chofer = e.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        LEFT JOIN personal_administrativo pa ON pa.id_personal = e.id_personal 
        LEFT JOIN usuario adm ON adm.id_usuario = pa.id_usuario 
        WHERE 1=1";

$params = [];
$tipos = "";

if ($filtro_chofer != "") {
    $sql .= " AND CONCAT(u.nombres, ' ', u.apellidos) LIKE ?";
    $params[] = "%" . $filtro_chofer . "%";
    $tipos .= "s";
}

if ($filtro_desde != "") {
    $sql .= " AND e.fecha_evaluacion >= ?";
    $params[] = $filtro_desde;
    $tipos .= "s";
}

if ($filtro_hasta != "") {
    $sql .= " AND e.fecha_evaluacion <= ?";
    $params[] = $filtro_hasta;
    $tipos .= "s";
}

if ($filtro_resultado != "" && $filtro_resultado != "Todos") {
    $sql .= " AND e.resultado = ?";
    $params[] = $filtro_resultado;
    $tipos .= "s";
}

$sql .= " ORDER BY e.fecha_evaluacion DESC";

$stmt = $conexion->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();
$result_ev = $stmt->get_result();

$evaluaciones_list = [];
while ($r = $result_ev->fetch_assoc()) {
    $evaluaciones_list[] = $r;
}

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar_personal.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <div class="modulo-header">
                <h2>Evaluaciones Psicológicas</h2>
                <p>Consulte el historial de evaluaciones psicológicas realizadas a los postulantes a chofer (Mínimo aprobatorio: 73 pts).</p>
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
                            <form method="GET">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Chofer</label>
                                        <input type="text" name="chofer" class="form-control"
                                               placeholder="Nombre del chofer"
                                               value="<?php echo htmlspecialchars($filtro_chofer); ?>">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label fw-bold">Desde</label>
                                        <input type="date" name="desde" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label fw-bold">Hasta</label>
                                        <input type="date" name="hasta" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">Resultado</label>
                                        <select name="resultado" class="form-select">
                                            <option value="Todos" <?php echo $filtro_resultado == "Todos" || $filtro_resultado == "" ? "selected" : ""; ?>>Todos</option>
                                            <option value="Apto" <?php echo $filtro_resultado == "Apto" ? "selected" : ""; ?>>Apto (>= 73 pts)</option>
                                            <option value="No Apto" <?php echo $filtro_resultado == "No Apto" ? "selected" : ""; ?>>No Apto (< 73 pts)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-decarrerita w-100">
                                            <i class="bi bi-search me-2"></i> Buscar
                                        </button>
                                    </div>
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
                                <i class="bi bi-clipboard2-pulse-fill me-2"></i> Historial de evaluaciones psicológicas (<?php echo count($evaluaciones_list); ?>)
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Cédula</th>
                                            <th>Fecha Evaluación</th>
                                            <th>Calificación (min 73)</th>
                                            <th>Resultado</th>
                                            <th class="text-center">Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($evaluaciones_list)): ?>
                                            <?php foreach ($evaluaciones_list as $ev): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($ev["nombres"] . " " . $ev["apellidos"]); ?></td>
                                                    <td><?php echo htmlspecialchars($ev["cedula"]); ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($ev["fecha_evaluacion"])); ?></td>
                                                    <td class="fw-bold"><?php echo htmlspecialchars($ev["calificacion"]); ?> pts</td>
                                                    <td>
                                                        <span class="badge <?php echo $ev["resultado"] == "Apto" ? "bg-success" : "bg-danger"; ?>">
                                                            <?php echo htmlspecialchars($ev["resultado"]); ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalEvPsi<?php echo $ev['id_evaluacion']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron evaluaciones psicológicas.
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
<?php foreach ($evaluaciones_list as $ev): ?>
    <div class="modal fade" id="modalEvPsi<?php echo $ev['id_evaluacion']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-clipboard2-pulse me-2"></i>Detalle de Evaluación Psicológica #<?php echo $ev['id_evaluacion']; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Chofer Postulante:</strong> <?php echo htmlspecialchars($ev['nombres'] . ' ' . $ev['apellidos']); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($ev['cedula']); ?></p>
                    <p><strong>Fecha de Evaluación:</strong> <?php echo date('d/m/Y', strtotime($ev['fecha_evaluacion'])); ?></p>
                    <hr>
                    <p><strong>Calificación Obtenida:</strong> <span class="fs-5 fw-bold"><?php echo htmlspecialchars($ev['calificacion']); ?> / 100</span> (Mínimo aprobatorio: 73)</p>
                    <p><strong>Resultado Final:</strong> <span class="badge <?php echo ($ev['resultado'] == 'Apto') ? 'bg-success' : 'bg-danger'; ?> fs-6"><?php echo htmlspecialchars($ev['resultado']); ?></span></p>
                    <p><strong>Observaciones:</strong> <?php echo !empty($ev['observacion']) ? htmlspecialchars($ev['observacion']) : 'Sin observaciones registradas.'; ?></p>
                    <p><strong>Evaluador:</strong> <?php echo htmlspecialchars(($ev['adm_nombres'] ?? 'Personal') . ' ' . ($ev['adm_apellidos'] ?? '')); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include("../../includes/footer.php"); ?>