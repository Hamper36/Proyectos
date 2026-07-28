<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
TRASLADOS FINALIZADOS SIN PAGAR
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
HISTORIAL DE PAGOS
(con datos del traslado si está vinculado)
=========================*/

$sql = "SELECT p.id_pago, p.fecha_pago, p.numero_referencia, p.monto_pagado,
               uc.nombres AS chofer_nombres, uc.apellidos AS chofer_apellidos,
               up.nombres AS personal_nombres, up.apellidos AS personal_apellidos,
               t.punto_origen, t.punto_destino,
               ucl.nombres AS cliente_nombres, ucl.apellidos AS cliente_apellidos
        FROM pago_chofer p
        INNER JOIN chofer c ON c.id_chofer = p.id_chofer
        INNER JOIN usuario uc ON uc.id_usuario = c.id_usuario
        INNER JOIN personal_administrativo pa ON pa.id_personal = p.id_personal
        INNER JOIN usuario up ON up.id_usuario = pa.id_usuario
        LEFT JOIN traslado t ON t.id_traslado = p.id_traslado
        LEFT JOIN cliente cl ON cl.id_cliente = t.id_cliente
        LEFT JOIN usuario ucl ON ucl.id_usuario = cl.id_usuario
        ORDER BY p.fecha_pago DESC";

$pagos = $conexion->query($sql);

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
                <h2>Gestión de Pagos</h2>
                <p>Registre pagos realizados a los choferes y consulte el historial de pagos efectuados.</p>
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

            <!-- TRASLADOS SIN PAGAR -->
            <div class="row justify-content-center">
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
                                                    <button class="btn btn-sm btn-decarrerita"
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

            <!-- HISTORIAL DE PAGOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-clock-history me-2"></i>
                                Historial de pagos
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Chofer</th>
                                            <th>Fecha</th>
                                            <th>Referencia</th>
                                            <th>Monto</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($pagos->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-secondary">
                                                    No se han registrado pagos.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($p = $pagos->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($p["chofer_nombres"] . " " . $p["chofer_apellidos"]); ?></td>
                                                <td><?php echo date("d/m/Y", strtotime($p["fecha_pago"])); ?></td>
                                                <td><?php echo htmlspecialchars($p["numero_referencia"]); ?></td>
                                                <td>$<?php echo number_format($p["monto_pagado"], 2); ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalPago<?php echo $p['id_pago']; ?>">
                                                        <i class="bi bi-eye"></i>
                                                        Ver
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
// Modales para pagar cada traslado pendiente
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
                        <input type="date" name="fecha_pago" class="form-control" required>
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

<?php
// Modales de detalle por cada pago del historial
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
                <p><strong>Chofer:</strong> <?php echo htmlspecialchars($p["chofer_nombres"] . " " . $p["chofer_apellidos"]); ?></p>
                <p><strong>Cliente:</strong> <?php echo $p["cliente_nombres"] ? htmlspecialchars($p["cliente_nombres"] . " " . $p["cliente_apellidos"]) : "—"; ?></p>
                <p><strong>Trayecto:</strong> <?php echo $p["punto_origen"] ? htmlspecialchars($p["punto_origen"] . " → " . $p["punto_destino"]) : "—"; ?></p>
                <p><strong>Fecha del pago:</strong> <?php echo date("d/m/Y", strtotime($p["fecha_pago"])); ?></p>
                <p><strong>Número de referencia:</strong> <?php echo htmlspecialchars($p["numero_referencia"]); ?></p>
                <p><strong>Monto pagado:</strong> $<?php echo number_format($p["monto_pagado"], 2); ?></p>
                <p><strong>Registrado por:</strong> <?php echo htmlspecialchars($p["personal_nombres"] . " " . $p["personal_apellidos"]); ?></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<?php } ?>

<?php include("../../includes/footer.php"); ?>