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

/*=========================
HISTORIAL DE PAGOS DEL CHOFER
(con datos del traslado vinculado, si existe)
=========================*/

$sql = "SELECT p.id_pago, p.fecha_pago, p.numero_referencia, p.monto_pagado,
               t.punto_origen, t.punto_destino, t.fecha_hora,
               ucl.nombres AS cliente_nombres, ucl.apellidos AS cliente_apellidos
        FROM pago_chofer p
        LEFT JOIN traslado t ON t.id_traslado = p.id_traslado
        LEFT JOIN cliente cl ON cl.id_cliente = t.id_cliente
        LEFT JOIN usuario ucl ON ucl.id_usuario = cl.id_usuario
        WHERE p.id_chofer = ?";

$params = [$id_chofer];
$tipos = "i";

if ($filtro_desde != "") {
    $sql .= " AND p.fecha_pago >= ?";
    $params[] = $filtro_desde;
    $tipos .= "s";
}
if ($filtro_hasta != "") {
    $sql .= " AND p.fecha_pago <= ?";
    $params[] = $filtro_hasta;
    $tipos .= "s";
}

$sql .= " ORDER BY p.fecha_pago DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($tipos, ...$params);
$stmt->execute();
$pagos = $stmt->get_result();

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
                <h2>Mis Pagos</h2>
                <p>Consulte los pagos realizados por la empresa durante un período determinado.</p>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-money">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:12px 18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-search me-2"></i>
                                Buscar Pagos
                            </h5>
                        </div>

                        <div class="card-body py-3">
                            <form method="GET">
                                <div class="row align-items-end">

                                    <div class="col-md-5 mb-2">
                                        <label class="form-label">Desde</label>
                                        <input type="date" name="desde" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_desde); ?>">
                                    </div>

                                    <div class="col-md-5 mb-2">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" name="hasta" class="form-control"
                                               value="<?php echo htmlspecialchars($filtro_hasta); ?>">
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <button type="submit" class="btn btn-decarrerita w-100">
                                            <i class="bi bi-search"></i>
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
                                <i class="bi bi-wallet2 me-2"></i>
                                Historial de Pagos
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background:#1E2E4F;color:white;">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Referencia</th>
                                            <th>Monto</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($pagos->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary">
                                                    No se encontraron pagos.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($p = $pagos->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo date("d/m/Y", strtotime($p["fecha_pago"])); ?></td>
                                                <td><?php echo htmlspecialchars($p["numero_referencia"]); ?></td>
                                                <td>$<?php echo number_format($p["monto_pagado"], 2); ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalPago<?php echo $p['id_pago']; ?>">
                                                        <i class="bi bi-eye"></i>
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
$pagos->data_seek(0);
while ($p = $pagos->fetch_assoc()) {
?>

<div class="modal fade" id="modalPago<?php echo $p['id_pago']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title">Detalle del Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Fecha de pago:</strong> <?php echo date("d/m/Y", strtotime($p["fecha_pago"])); ?></p>
                <p><strong>Referencia:</strong> <?php echo htmlspecialchars($p["numero_referencia"]); ?></p>
                <p><strong>Monto pagado:</strong> $<?php echo number_format($p["monto_pagado"], 2); ?></p>

                <?php if ($p["cliente_nombres"]) { ?>
                    <hr>
                    <p><strong>Corresponde al traslado:</strong></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($p["cliente_nombres"] . " " . $p["cliente_apellidos"]); ?></p>
                    <p><strong>Trayecto:</strong> <?php echo htmlspecialchars($p["punto_origen"] . " → " . $p["punto_destino"]); ?></p>
                    <p><strong>Fecha del traslado:</strong> <?php echo date("d/m/Y", strtotime($p["fecha_hora"])); ?></p>
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