<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER id_cliente
=========================*/

$sql = "SELECT id_cliente FROM cliente WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_cliente = $stmt->get_result()->fetch_assoc()["id_cliente"];

/*=========================
OBTENER TRASLADOS DEL CLIENTE
CON DATOS DEL CHOFER Y VEHÍCULO
=========================*/

$sql = "SELECT t.id_traslado, t.fecha_hora, t.punto_origen, t.punto_destino, t.costo, t.estado_traslado, t.motivo_cancelacion,
               u.nombres AS chofer_nombres, u.apellidos AS chofer_apellidos,
               v.marca, v.modelo, v.placa
        FROM traslado t
        INNER JOIN chofer c ON c.id_chofer = t.id_chofer
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        INNER JOIN vehiculo v ON v.id_vehiculo = t.id_vehiculo
        WHERE t.id_cliente = ?
        ORDER BY t.id_traslado DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$traslados = $stmt->get_result();

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_cliente.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard">
            </div>

            <div class="modulo-header">
                <h2>Mis Traslados</h2>
                <p>Consulte el estado e historial de todos los traslados realizados.</p>
            </div>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "1") { ?>
                <div class="alert alert-success text-center">Traslado solicitado correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "cancelado") { ?>
                <div class="alert alert-success text-center">Traslado cancelado. Se devolvió el saldo a su cuenta.</div>
            <?php } ?>

            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill"></i>
                                Historial de Traslados
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background:#1E2E4F;color:white;">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Chofer</th>
                                            <th>Vehículo</th>
                                            <th>Costo</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($traslados->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-secondary">
                                                    Aún no ha realizado ningún traslado.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($fila = $traslados->fetch_assoc()) { ?>

                                            <?php
                                            switch ($fila["estado_traslado"]) {
                                                case "Finalizado":
                                                    $clase_badge = "bg-success";
                                                    break;
                                                case "En curso":
                                                    $clase_badge = "bg-primary";
                                                    break;
                                                case "Cancelado":
                                                    $clase_badge = "bg-danger";
                                                    break;
                                                default:
                                                    $clase_badge = "bg-warning text-dark";
                                            }
                                            ?>

                                            <tr>
                                                <td><?php echo date("d/m/Y H:i", strtotime($fila["fecha_hora"])); ?></td>
                                                <td><?php echo htmlspecialchars($fila["punto_origen"]); ?></td>
                                                <td><?php echo htmlspecialchars($fila["punto_destino"]); ?></td>
                                                <td><?php echo htmlspecialchars($fila["chofer_nombres"] . " " . $fila["chofer_apellidos"]); ?></td>
                                                <td><?php echo htmlspecialchars($fila["marca"] . " " . $fila["modelo"] . " (" . $fila["placa"] . ")"); ?></td>
                                                <td>$<?php echo number_format($fila["costo"], 2); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $clase_badge; ?>">
                                                        <?php echo $fila["estado_traslado"]; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <?php if ($fila["estado_traslado"] == "Pendiente") { ?>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalCancelar<?php echo $fila['id_traslado']; ?>">
                                                            <i class="bi bi-x-circle"></i>
                                                            Cancelar
                                                        </button>
                                                    <?php } ?>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalle<?php echo $fila['id_traslado']; ?>">
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
// Modales de detalle
$traslados->data_seek(0);
while ($fila = $traslados->fetch_assoc()) {
?>

<div class="modal fade" id="modalDetalle<?php echo $fila['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header text-white" style="background:#1E2E4F;">
                <h5 class="modal-title">Detalle del Traslado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><strong>Chofer:</strong> <?php echo htmlspecialchars($fila["chofer_nombres"] . " " . $fila["chofer_apellidos"]); ?></p>
                <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($fila["marca"] . " " . $fila["modelo"] . " (" . $fila["placa"] . ")"); ?></p>
                <hr>
                <p><strong>Fecha:</strong> <?php echo date("d/m/Y H:i", strtotime($fila["fecha_hora"])); ?></p>
                <p><strong>Origen:</strong> <?php echo htmlspecialchars($fila["punto_origen"]); ?></p>
                <p><strong>Destino:</strong> <?php echo htmlspecialchars($fila["punto_destino"]); ?></p>
                <p><strong>Costo:</strong> $<?php echo number_format($fila["costo"], 2); ?></p>

                <?php if ($fila["estado_traslado"] == "Cancelado") { ?>
                    <hr>
                    <p><strong>Motivo de cancelación:</strong> <?php echo htmlspecialchars($fila["motivo_cancelacion"]); ?></p>
                <?php } ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<?php } ?>

<?php
// Modales de cancelación (solo para "Pendiente")
$traslados->data_seek(0);
while ($fila = $traslados->fetch_assoc()) {
    if ($fila["estado_traslado"] == "Pendiente") {
?>

<div class="modal fade" id="modalCancelar<?php echo $fila['id_traslado']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/cancelar_traslado_cliente.php" method="POST">
                <input type="hidden" name="id_traslado" value="<?php echo $fila['id_traslado']; ?>">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Cancelar Traslado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Se cancelará su solicitud y se devolverá el saldo a su cuenta.</p>
                    <div class="mb-3">
                        <label class="form-label">Motivo (opcional)</label>
                        <textarea name="motivo_cancelacion" class="form-control" rows="2"></textarea>
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

<?php
    }
}
?>

<?php include("../../includes/footer.php"); ?>