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
               u.nombres, u.apellidos
        FROM evaluacion_psicologica e
        INNER JOIN chofer c ON c.id_chofer = e.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
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
$evaluaciones = $stmt->get_result();

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
                <h2>Evaluaciones Psicológicas</h2>
                <p>Consulte el historial de evaluaciones psicológicas realizadas a los choferes.</p>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">

                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-funnel-fill me-2"></i>
                                Filtros de búsqueda
                            </h5>
                        </div>

                        <div class="card-body">
                            <form method="GET">
                                <div class="row">

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Chofer</label>
                                        <input type="text" name="chofer" class="form-control"
                                               placeholder="Nombre del chofer"
                                               value="<?php echo htmlspecialchars($filtro_chofer); ?>">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Desde</label>
                                        <input type="date" name="desde" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" name="hasta" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Resultado</label>
                                        <select name="resultado" class="form-select">
                                            <option <?php echo $filtro_resultado == "" ? "selected" : ""; ?>>Todos</option>
                                            <option <?php echo $filtro_resultado == "Apto" ? "selected" : ""; ?>>Apto</option>
                                            <option <?php echo $filtro_resultado == "No Apto" ? "selected" : ""; ?>>No Apto</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-decarrerita w-100">
                                            <i class="bi bi-search me-2"></i>
                                            Buscar
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
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-clipboard2-pulse-fill me-2"></i>
                                Historial de evaluaciones psicológicas
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Fecha</th>
                                            <th>Calificación</th>
                                            <th>Resultado</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($evaluaciones->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary">
                                                    No se encontraron evaluaciones.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($fila = $evaluaciones->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($fila["nombres"] . " " . $fila["apellidos"]); ?></td>
                                                <td><?php echo date("d/m/Y", strtotime($fila["fecha_evaluacion"])); ?></td>
                                                <td><?php echo $fila["calificacion"]; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $fila["resultado"] == "Apto" ? "bg-success" : "bg-danger"; ?>">
                                                        <?php echo $fila["resultado"]; ?>
                                                    </span>
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

<?php include("../../includes/footer.php"); ?>