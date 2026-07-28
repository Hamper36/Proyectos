<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
DATOS DEL CHOFER + USUARIO + BANCO
=========================*/

$sql = "SELECT u.nombres, u.apellidos, u.cedula, u.telefono, u.direccion, u.correo,
               u.nombre_usuario, u.estado_usuario, c.id_chofer, c.fecha_registro,
               c.numero_cuenta, b.nombre_banco
        FROM usuario u
        INNER JOIN chofer c ON c.id_usuario = u.id_usuario
        LEFT JOIN banco b ON b.id_banco = c.id_banco
        WHERE u.id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$chofer = $stmt->get_result()->fetch_assoc();

$id_chofer = $chofer["id_chofer"];

/*=========================
ÚLTIMA EVALUACIÓN PSICOLÓGICA
=========================*/

$sql = "SELECT resultado FROM evaluacion_psicologica WHERE id_chofer = ? ORDER BY fecha_evaluacion DESC LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$evaluacion = $stmt->get_result()->fetch_assoc();

/*=========================
TODOS LOS VEHÍCULOS (activos, no "Inactivo")
=========================*/

$sql = "SELECT placa, marca, modelo, estado_vehiculo FROM vehiculo 
        WHERE id_chofer = ? AND estado_vehiculo != 'Inactivo' 
        ORDER BY id_vehiculo ASC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$vehiculos = $stmt->get_result();
$total_vehiculos = $vehiculos->num_rows;

/*=========================
CONTACTOS DE EMERGENCIA
=========================*/

$sql = "SELECT nombre, telefono FROM contacto_emergencia WHERE id_chofer = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_chofer);
$stmt->execute();
$contactos = $stmt->get_result();

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
                <h2>Mi Perfil</h2>
                <p>Consulte y administre la información registrada de su cuenta como chofer.</p>
            </div>

            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success text-center">Información actualizada correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["error"])) { ?>
                <div class="alert alert-danger text-center">Ese correo ya está en uso por otra cuenta.</div>
            <?php } ?>

            <!-- PERFIL PRINCIPAL -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-person-vcard me-2"></i>
                                Perfil del chofer
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-person-circle" style="font-size:70px;color:#1E2E4F;"></i>
                                <h3 class="mt-2"><?php echo htmlspecialchars($chofer["nombres"] . " " . $chofer["apellidos"]); ?></h3>
                                <span class="badge <?php echo $chofer["estado_usuario"] == "Activo" ? "bg-success" : "bg-warning text-dark"; ?>">
                                    Chofer <?php echo htmlspecialchars($chofer["estado_usuario"]); ?>
                                </span>
                            </div>

                            <hr>

                            <div class="row text-center">

                                <div class="col-md-6 mb-3">
                                    <i class="bi bi-clipboard-check-fill text-success" style="font-size:30px;"></i>
                                    <h6 class="mt-2">Evaluación psicológica</h6>
                                    <span class="badge <?php echo ($evaluacion && $evaluacion["resultado"] == "Apto") ? "bg-success" : "bg-secondary"; ?>">
                                        <?php echo $evaluacion ? $evaluacion["resultado"] : "Sin evaluar"; ?>
                                    </span>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <i class="bi bi-calendar-check-fill text-warning" style="font-size:30px;"></i>
                                    <h6 class="mt-2">Registro</h6>
                                    <span><?php echo date("d/m/Y", strtotime($chofer["fecha_registro"])); ?></span>
                                </div>

                            </div>

                            <div class="text-center mt-3">
                                <button class="btn btn-decarrerita" data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="bi bi-pencil-square"></i>
                                    Editar información
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- DATOS PERSONALES -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-person-fill me-2"></i>
                                Datos personales
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombres</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer["nombres"]); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Apellidos</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer["apellidos"]); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Cédula</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer["cedula"]); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer["telefono"]); ?>" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Dirección</label>
                                    <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($chofer["direccion"]); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CUENTA E INFORMACIÓN BANCARIA -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-bank me-2"></i>
                                Cuenta e información bancaria
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Usuario</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer["nombre_usuario"]); ?>" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Correo electrónico</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($chofer["correo"]); ?>" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Banco</label>
                                    <input type="text" class="form-control" value="<?php echo $chofer["nombre_banco"] ? htmlspecialchars($chofer["nombre_banco"]) : "No asignado"; ?>" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Número de cuenta</label>
                                    <input type="text" class="form-control" value="<?php echo $chofer["numero_cuenta"] ? htmlspecialchars($chofer["numero_cuenta"]) : "No asignado"; ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estado de la cuenta</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer["estado_usuario"]); ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fecha de registro</label>
                                    <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($chofer["fecha_registro"])); ?>" readonly>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MIS VEHÍCULOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Mis vehículos (<?php echo $total_vehiculos; ?>)
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Placa</th>
                                            <th>Marca / Modelo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if ($total_vehiculos == 0) { ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-secondary">No tiene vehículos registrados.</td>
                                            </tr>
                                        <?php } ?>

                                        <?php while ($v = $vehiculos->fetch_assoc()) { ?>
                                            <?php
                                            $badge = $v["estado_vehiculo"] == "Apto" ? "bg-success" : ($v["estado_vehiculo"] == "No Apto" ? "bg-danger" : "bg-warning text-dark");
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($v["placa"]); ?></td>
                                                <td><?php echo htmlspecialchars($v["marca"] . " " . $v["modelo"]); ?></td>
                                                <td><span class="badge <?php echo $badge; ?>"><?php echo $v["estado_vehiculo"]; ?></span></td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-2">
                                <a href="mis_vehiculos.php" class="btn btn-sm btn-outline-primary">
                                    Ir a Mis Vehículos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTACTOS DE EMERGENCIA -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-people-fill me-2"></i>
                                Contactos de emergencia
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <?php while ($c = $contactos->fetch_assoc()) { ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Contacto</label>
                                        <input type="text" class="form-control"
                                               value="<?php echo htmlspecialchars($c["nombre"] . " - " . $c["telefono"]); ?>" readonly>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5"></div>

        </div>
    </div>
</div>

<!-- MODAL EDITAR PERFIL -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="../../procesos/editar_perfil_chofer.php" method="POST">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Editar Información</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-control"
                               value="<?php echo htmlspecialchars($chofer["correo"]); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="<?php echo htmlspecialchars($chofer["telefono"]); ?>" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="<?php echo htmlspecialchars($chofer["direccion"]); ?>" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-decarrerita">Guardar cambios</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>