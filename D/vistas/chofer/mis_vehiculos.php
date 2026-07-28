<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER id_chofer
=========================*/

$sql = "SELECT id_chofer FROM chofer WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$id_chofer = $stmt->get_result()->fetch_assoc()["id_chofer"];

/*=========================
OBTENER VEHÍCULOS (activos, no "Inactivo")
CON FECHA DE ÚLTIMA REVISIÓN
=========================*/

$sql = "SELECT v.id_vehiculo, v.placa, v.marca, v.modelo, v.anio, v.color, v.estado_vehiculo,
               (SELECT MAX(fecha_revision) FROM revision_vehiculo r WHERE r.id_vehiculo = v.id_vehiculo) AS ultima_revision
        FROM vehiculo v
        WHERE v.id_chofer = ? AND v.estado_vehiculo != 'Inactivo'
        ORDER BY v.id_vehiculo DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$vehiculos = $stmt->get_result();

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
                <h2>Mis Vehículos</h2>
                <p>Consulte los vehículos registrados para prestar el servicio de transporte.</p>
            </div>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "registro") { ?>
                <div class="alert alert-success text-center">Vehículo registrado correctamente. Queda pendiente de revisión.</div>
            <?php } ?>

            <?php if (isset($_GET["ok"]) && $_GET["ok"] == "retiro") { ?>
                <div class="alert alert-success text-center">Vehículo retirado correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["error"]) && $_GET["error"] == "placa") { ?>
                <div class="alert alert-danger text-center">Esa placa ya está registrada.</div>
            <?php } ?>

            <!-- BOTONES -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-11 text-end">
                    <button class="btn btn-decarrerita me-2" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                        <i class="bi bi-plus-circle"></i>
                        Registrar vehículo
                    </button>
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalRetirar">
                        <i class="bi bi-trash"></i>
                        Solicitar retiro
                    </button>
                </div>
            </div>

            <!-- TABLA -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Vehículos registrados
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead style="background:#1E2E4F;color:white;">
                                        <tr>
                                            <th>Placa</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Año</th>
                                            <th>Última revisión</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($vehiculos->num_rows == 0) { ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-secondary">
                                                    No tiene vehículos registrados.
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($v = $vehiculos->fetch_assoc()) { ?>

                                            <?php
                                            switch ($v["estado_vehiculo"]) {
                                                case "Apto":
                                                    $badge = "bg-success";
                                                    break;
                                                case "No Apto":
                                                    $badge = "bg-danger";
                                                    break;
                                                default:
                                                    $badge = "bg-warning text-dark";
                                            }
                                            ?>

                                            <tr>
                                                <td><?php echo htmlspecialchars($v["placa"]); ?></td>
                                                <td><?php echo htmlspecialchars($v["marca"]); ?></td>
                                                <td><?php echo htmlspecialchars($v["modelo"]); ?></td>
                                                <td><?php echo htmlspecialchars($v["anio"]); ?></td>
                                                <td><?php echo $v["ultima_revision"] ? date("d/m/Y", strtotime($v["ultima_revision"])) : "—"; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $badge; ?>">
                                                        <?php
                                                        if ($v["estado_vehiculo"] == "Pendiente") {
                                                            echo $v["ultima_revision"] ? "Revisión vencida" : "Pendiente de revisión";
                                                        } else {
                                                            echo $v["estado_vehiculo"];
                                                        }
                                                        ?>
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

            <!-- INFORMACIÓN -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card card-info">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">Información Importante</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Puede registrar varios vehículos para prestar el servicio de transporte.
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Todo vehículo nuevo deberá ser revisado por el personal administrativo antes de quedar habilitado para prestar servicio.
                            </div>
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Las revisiones técnicas se realizan anualmente. Si pasa un año sin una nueva revisión, el vehículo vuelve automáticamente a estado "Pendiente" y no podrá usarse para traslados hasta que el personal administrativo lo revise de nuevo.
                            </div>
                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Puede retirar un vehículo en cualquier momento desde esta pantalla.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL REGISTRAR VEHÍCULO -->
<div class="modal fade" id="modalRegistrar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/registrar_vehiculo.php" method="POST">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Registrar Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Placa</label>
                        <input type="text" name="placa" class="form-control" maxlength="7" pattern="[a-zA-Z0-9]{1,7}" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 7)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <input type="text" name="marca" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" name="anio" class="form-control" min="1980" max="2100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-decarrerita">Registrar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- MODAL RETIRAR VEHÍCULO -->
<div class="modal fade" id="modalRetirar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../procesos/retirar_vehiculo.php" method="POST">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Retirar Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el vehículo a retirar</label>
                        <select name="id_vehiculo" class="form-select" required>
                            <option value="" selected disabled>Seleccione...</option>
                            <?php
                            $vehiculos->data_seek(0);
                            while ($v = $vehiculos->fetch_assoc()) {
                            ?>
                                <option value="<?php echo $v['id_vehiculo']; ?>">
                                    <?php echo htmlspecialchars($v['placa'] . " - " . $v['marca'] . " " . $v['modelo']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        Esta acción marcará el vehículo como inactivo. No podrá usarlo para nuevos traslados.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Retirar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>