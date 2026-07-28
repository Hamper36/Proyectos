<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
LEER FILTROS
=========================*/

$filtro_desde = isset($_GET["desde"]) ? trim($_GET["desde"]) : "";
$filtro_hasta = isset($_GET["hasta"]) ? trim($_GET["hasta"]) : "";
$filtro_estado = isset($_GET["estado"]) ? trim($_GET["estado"]) : "";

/*=========================
CONSULTA CON FILTROS
(incluye Finalizado y Cancelado, y datos extra para el modal)
=========================*/

$sql = "SELECT t.id_traslado, t.fecha_hora, t.punto_origen, t.punto_destino,
               t.costo, t.monto_chofer, t.monto_empresa, t.estado_traslado, t.motivo_cancelacion,
               u.nombres, u.apellidos, u.telefono, u.correo,
               p.id_pago, p.fecha_pago, p.numero_referencia
        FROM traslado t
        INNER JOIN cliente cl ON cl.id_cliente = t.id_cliente
        INNER JOIN usuario u ON u.id_usuario = cl.id_usuario
        LEFT JOIN pago_chofer p ON p.id_traslado = t.id_traslado
        WHERE t.id_chofer = ? AND t.estado_traslado IN ('Finalizado', 'Cancelado')";

$params = [$id_chofer];
$tipos = "i";

if ($filtro_desde != "") {
    $sql .= " AND DATE(t.fecha_hora) >= ?";
    $params[] = $filtro_desde;
    $tipos .= "s";
}
if ($filtro_hasta != "") {
    $sql .= " AND DATE(t.fecha_hora) <= ?";
    $params[] = $filtro_hasta;
    $tipos .= "s";
}
if ($filtro_estado == "Pagado") {
    $sql .= " AND p.id_pago IS NOT NULL";
} elseif ($filtro_estado == "Pendiente de pago") {
    $sql .= " AND p.id_pago IS NULL AND t.estado_traslado = 'Finalizado'";
} elseif ($filtro_estado == "Cancelado") {
    $sql .= " AND t.estado_traslado = 'Cancelado'";
}

$sql .= " ORDER BY t.id_traslado DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($tipos, ...$params);
$stmt->execute();
$traslados = $stmt->get_result();

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_chofer.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard">
            </div>

            <div class="modulo-header">
                <h2>Historial de Viajes</h2>
                <p>Consulte los traslados realizados durante un período de tiempo.</p>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-search me-2"></i>
                                Buscar traslados
                            </h5>
                        </div>

                        <div class="card-body">
                            <form method="GET">
                                <div class="row align-items-end">

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Desde</label>
                                        <input type="date" name="desde" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" name="hasta" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Estado</label>
                                        <select name="estado" class="form-select">
                                            <option <?php echo $filtro_estado == "" ? "selected" : ""; ?>>Todos</option>
                                            <option <?php echo $filtro_estado == "Pendiente de pago" ? "selected" : ""; ?>>Pendiente de pago</option>
                                            <option <?php echo $filtro_estado == "Pagado" ? "selected" : ""; ?>>Pagado</option>
                                            <option <?php echo $filtro_estado == "Cancelado" ? "selected" : ""; ?>>Cancelado</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <button type="submit" class="btn btn-decarrerita w-100">
                                            <i class="bi bi-search me-1"></i>
                                            Consultar
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

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Historial de traslados
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background:#1E2E4F;color:white;">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($traslados->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-secondary">
                                                    No se encontraron traslados.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($t = $traslados->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo date("d/m/Y", strtotime($t["fecha_hora"])); ?></td>
                                                <td><?php echo htmlspecialchars($t["nombres"] . " " . $t["apellidos"]); ?></td>
                                                <td><i class="bi bi-geo-alt-fill text-primary me-1"></i> <?php echo htmlspecialchars($t["punto_origen"]); ?></td>
                                                <td><i class="bi bi-flag-fill text-danger me-1"></i> <?php echo htmlspecialchars($t["punto_destino"]); ?></td>
                                                <td>$<?php echo number_format($t["monto_chofer"], 2); ?></td>
                                                <td>
                                                    <?php if ($t["estado_traslado"] == "Cancelado") { ?>
                                                        <span class="badge bg-danger">Cancelado</span>
                                                    <?php } elseif ($t["id_pago"]) { ?>
                                                        <span class="badge bg-success">Pagado</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-warning text-dark">Pendiente de pago</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-outline-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalle<?php echo $t['id_traslado']; ?>">
                                                        <i class="bi bi-eye me-1"></i>
                                                        Detalle
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
$traslados->data_seek(0);
while ($t = $traslados->fetch_assoc()) {
?>

<div class="modal fade" id="modalDetalle<?php echo $t['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title">Detalle del Traslado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($t["nombres"] . " " . $t["apellidos"]); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($t["telefono"]); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($t["correo"]); ?></p>
                <hr>
                <p><strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($t["fecha_hora"])); ?></p>
                <p><strong>Origen:</strong> <?php echo htmlspecialchars($t["punto_origen"]); ?></p>
                <p><strong>Destino:</strong> <?php echo htmlspecialchars($t["punto_destino"]); ?></p>
                <hr>
                <p><strong>Costo total:</strong> $<?php echo number_format($t["costo"], 2); ?></p>
                <p><strong>Monto empresa (30%):</strong> $<?php echo number_format($t["monto_empresa"], 2); ?></p>
                <p><strong>Monto para usted (70%):</strong> $<?php echo number_format($t["monto_chofer"], 2); ?></p>

                <?php if ($t["estado_traslado"] == "Cancelado") { ?>
                    <hr>
                    <p><strong>Motivo de cancelación:</strong> <?php echo htmlspecialchars($t["motivo_cancelacion"]); ?></p>
                <?php } else { ?>
                    <hr>
                    <?php if ($t["id_pago"]) { ?>
                        <p><strong>Estado de pago:</strong> <span class="badge bg-success">Pagado</span></p>
                        <p><strong>Fecha de pago:</strong> <?php echo date("d/m/Y", strtotime($t["fecha_pago"])); ?></p>
                        <p><strong>Referencia:</strong> <?php echo htmlspecialchars($t["numero_referencia"]); ?></p>
                    <?php } else { ?>
                        <p><strong>Estado de pago:</strong> <span class="badge bg-warning text-dark">Pendiente de pago</span></p>
                    <?php } ?>
                <?php } ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<?php } ?>

<?php include("../../includes/footer.php"); ?>