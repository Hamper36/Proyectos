<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
TRASLADOS "PENDIENTE" O "EN CURSO"
=========================*/

$sql = "SELECT t.id_traslado, t.fecha_hora, t.costo, t.punto_origen, t.punto_destino, t.estado_traslado,
               uc.nombres AS chofer_nombres, uc.apellidos AS chofer_apellidos,
               ucl.nombres AS cliente_nombres, ucl.apellidos AS cliente_apellidos
        FROM traslado t
        INNER JOIN chofer c ON c.id_chofer = t.id_chofer
        INNER JOIN usuario uc ON uc.id_usuario = c.id_usuario
        INNER JOIN cliente cl ON cl.id_cliente = t.id_cliente
        INNER JOIN usuario ucl ON ucl.id_usuario = cl.id_usuario
        WHERE t.estado_traslado IN ('Pendiente', 'En curso')
        ORDER BY t.fecha_hora DESC";

$activos = $conexion->query($sql);

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
                <h2>Traslados Activos</h2>
                <p>Consulte los traslados pendientes y en curso, y cancele en caso de alguna incidencia.</p>
            </div>

            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success text-center">Traslado cancelado correctamente. Se devolvió el saldo al cliente.</div>
            <?php } ?>

            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Traslados pendientes y en curso
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Chofer</th>
                                            <th>Origen → Destino</th>
                                            <th>Costo</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($activos->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-secondary">
                                                    No hay traslados pendientes ni en curso.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($t = $activos->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo date("d/m/Y H:i", strtotime($t["fecha_hora"])); ?></td>
                                                <td><?php echo htmlspecialchars($t["cliente_nombres"] . " " . $t["cliente_apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($t["chofer_nombres"] . " " . $t["chofer_apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($t["punto_origen"] . " → " . $t["punto_destino"]); ?></td>
                                                <td>$<?php echo number_format($t["costo"], 2); ?></td>
                                                <td>
                                                    <?php if ($t["estado_traslado"] == "Pendiente") { ?>
                                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-primary">En curso</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalCancelar<?php echo $t['id_traslado']; ?>">
                                                        <i class="bi bi-x-circle"></i>
                                                        Cancelar
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
$activos->data_seek(0);
while ($t = $activos->fetch_assoc()) {
?>

<div class="modal fade" id="modalCancelar<?php echo $t['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/cancelar_traslado.php" method="POST">
                <input type="hidden" name="id_traslado" value="<?php echo $t['id_traslado']; ?>">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Cancelar Traslado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($t["cliente_nombres"] . " " . $t["cliente_apellidos"]); ?></p>
                    <p><strong>Chofer:</strong> <?php echo htmlspecialchars($t["chofer_nombres"] . " " . $t["chofer_apellidos"]); ?></p>
                    <p><strong>Trayecto:</strong> <?php echo htmlspecialchars($t["punto_origen"] . " → " . $t["punto_destino"]); ?></p>

                    <div class="alert alert-warning">
                        Se devolverán $<?php echo number_format($t["costo"], 2); ?> al saldo del cliente.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motivo de la cancelación</label>
                        <textarea name="motivo_cancelacion" class="form-control" rows="3" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('¿Está seguro de cancelar este traslado?');">
                        Confirmar Cancelación
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php } ?>

<?php include("../../includes/footer.php"); ?>