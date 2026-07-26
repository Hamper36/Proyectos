<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 2) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
CONTADORES PARA TARJETAS
=========================*/

$sql = "SELECT COUNT(*) AS total FROM usuario WHERE id_rol = 4 AND estado_usuario = 'Pendiente'";
$pendientes = $conexion->query($sql)->fetch_assoc()["total"];

$sql = "SELECT COUNT(*) AS total FROM usuario WHERE id_rol = 4 AND estado_usuario = 'Activo'";
$activos = $conexion->query($sql)->fetch_assoc()["total"];

$sql = "SELECT COUNT(*) AS total FROM usuario WHERE id_rol = 4 AND estado_usuario = 'Rechazado'";
$rechazados = $conexion->query($sql)->fetch_assoc()["total"];

/*=========================
LISTA DE BANCOS (para el select)
=========================*/

$bancos = $conexion->query("SELECT id_banco, nombre_banco FROM banco ORDER BY nombre_banco");

/*=========================
LISTA DE TODOS LOS CHOFERES
=========================*/

$sql = "SELECT c.id_chofer, u.id_usuario, u.nombres, u.apellidos, u.cedula, u.correo, u.telefono,
               u.direccion, u.estado_usuario, c.fecha_registro,
               v.id_vehiculo, v.placa, v.marca, v.modelo, v.anio, v.color
        FROM chofer c
        INNER JOIN usuario u ON u.id_usuario = c.id_usuario
        LEFT JOIN vehiculo v ON v.id_chofer = c.id_chofer
        ORDER BY c.fecha_registro DESC";

$choferes = $conexion->query($sql);

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">

        <?php include("../../includes/sidebar_personal.php"); ?>

        <div class="col-md-10 p-4 dashboard">

            <div class="container-fluid mt-4 mb-5">

                <div class="text-center mt-3">
                    <img src="../../assets/img/logo.png" width="220" class="logo-dashboard">
                </div>

                <div class="text-center mb-4">
                    <h2 class="fw-bold">
                        <i class="bi bi-person-check-fill" style="color:#1E2E4F;"></i>
                        Gestionar Choferes
                    </h2>
                    <p class="text-muted">
                        Revise las solicitudes, registre las evaluaciones y active automáticamente las cuentas.
                    </p>
                </div>

                <?php if (isset($_GET["ok"])) { ?>
                    <div class="alert alert-success text-center">Evaluación registrada correctamente.</div>
                <?php } ?>

                <!-- TARJETAS -->
                <div class="row justify-content-center g-4 mb-5">

                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow card-usuarios h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-hourglass-split text-warning" style="font-size:60px;"></i>
                                <h2 class="mt-3"><?php echo $pendientes; ?></h2>
                                <h5>Pendientes</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow card-vehiculos h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-person-check-fill text-success" style="font-size:60px;"></i>
                                <h2 class="mt-3"><?php echo $activos; ?></h2>
                                <h5>Activos</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card shadow card-ganancias h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-person-x-fill text-danger" style="font-size:60px;"></i>
                                <h2 class="mt-3"><?php echo $rechazados; ?></h2>
                                <h5>Rechazados</h5>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- TABLA -->
                <div class="card shadow">

                    <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="bi bi-list-check me-2"></i>
                            Solicitudes de Choferes
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead style="background:#1E2E4F;color:white;">
                                    <tr>
                                        <th>Chofer</th>
                                        <th>Cédula</th>
                                        <th>Fecha Registro</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php while ($ch = $choferes->fetch_assoc()) { ?>

                                        <?php
                                        switch ($ch["estado_usuario"]) {
                                            case "Activo":
                                                $badge = "bg-success";
                                                break;
                                            case "Rechazado":
                                                $badge = "bg-danger";
                                                break;
                                            default:
                                                $badge = "bg-warning";
                                        }
                                        ?>

                                        <tr>
                                            <td><?php echo htmlspecialchars($ch["nombres"] . " " . $ch["apellidos"]); ?></td>
                                            <td><?php echo htmlspecialchars($ch["cedula"]); ?></td>
                                            <td><?php echo date("d/m/Y", strtotime($ch["fecha_registro"])); ?></td>
                                            <td><span class="badge <?php echo $badge; ?>"><?php echo $ch["estado_usuario"]; ?></span></td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalChofer<?php echo $ch['id_chofer']; ?>">
                                                    <i class="bi bi-search"></i>
                                                    <?php echo $ch["estado_usuario"] == "Pendiente" ? "Revisar" : "Ver"; ?>
                                                </button>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php
                // Reiniciar el puntero del resultado para generar los modales
                $choferes->data_seek(0);
                while ($ch = $choferes->fetch_assoc()) {
                ?>

                <!-- MODAL PARA ESTE CHOFER -->
                <div class="modal fade" id="modalChofer<?php echo $ch['id_chofer']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">

                            <form action="../../procesos/evaluar_chofer.php" method="POST">
                                <input type="hidden" name="id_chofer" value="<?php echo $ch['id_chofer']; ?>">
                                <input type="hidden" name="id_usuario" value="<?php echo $ch['id_usuario']; ?>">
                                <input type="hidden" name="id_vehiculo" value="<?php echo $ch['id_vehiculo']; ?>">

                                <div class="modal-header text-white" style="background:#1E2E4F;">
                                    <h4 class="modal-title">
                                        <i class="bi bi-person-vcard me-2"></i>
                                        Revisión de Solicitud
                                    </h4>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <!-- DATOS PERSONALES -->
                                    <div class="card mb-4">
                                        <div class="card-header" style="background:#e9ecef;">
                                            <strong>Datos Personales</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($ch["nombres"] . " " . $ch["apellidos"]); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($ch["cedula"]); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($ch["correo"]); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($ch["telefono"]); ?></p>
                                                </div>
                                                <div class="col-12">
                                                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($ch["direccion"]); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- VEHÍCULO -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <strong>Vehículo</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4"><p><strong>Placa:</strong> <?php echo htmlspecialchars($ch["placa"]); ?></p></div>
                                                <div class="col-md-4"><p><strong>Marca:</strong> <?php echo htmlspecialchars($ch["marca"]); ?></p></div>
                                                <div class="col-md-4"><p><strong>Modelo:</strong> <?php echo htmlspecialchars($ch["modelo"]); ?></p></div>
                                                <div class="col-md-4"><p><strong>Año:</strong> <?php echo htmlspecialchars($ch["anio"]); ?></p></div>
                                                <div class="col-md-4"><p><strong>Color:</strong> <?php echo htmlspecialchars($ch["color"]); ?></p></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CONTACTOS -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <strong>Contactos de Emergencia</strong>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                <?php
                                                $sql_contactos = "SELECT nombre, telefono, parentesco FROM contacto_emergencia WHERE id_chofer = ?";
                                                $stmt_c = $conexion->prepare($sql_contactos);
                                                $stmt_c->bind_param("i", $ch['id_chofer']);
                                                $stmt_c->execute();
                                                $contactos = $stmt_c->get_result();
                                                while ($contacto = $contactos->fetch_assoc()) {
                                                ?>
                                                    <li class="list-group-item">
                                                        <?php echo htmlspecialchars($contacto["nombre"] . " - " . $contacto["parentesco"] . " - " . $contacto["telefono"]); ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <?php if ($ch["estado_usuario"] == "Pendiente") { ?>

                                    <!-- EVALUACIÓN PSICOLÓGICA -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background:#1E2E4F;">
                                            <strong>Evaluación Psicológica</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Fecha de Evaluación</label>
                                                    <input type="date" name="fecha_evaluacion" class="form-control" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Calificación</label>
                                                    <input type="number" name="calificacion_evaluacion" class="form-control" min="0" max="100" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Observación</label>
                                                    <textarea name="observacion_evaluacion" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- REVISIÓN VEHICULAR -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background:#1E2E4F;">
                                            <strong>Revisión Vehicular</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Fecha de Revisión</label>
                                                    <input type="date" name="fecha_revision" class="form-control" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Calificación</label>
                                                    <input type="number" name="calificacion_revision" class="form-control" min="0" max="100" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Observación</label>
                                                    <textarea name="observacion_revision" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- DATOS BANCARIOS -->
                                    <div class="card mb-4">
                                        <div class="card-header text-white" style="background:#1E2E4F;">
                                            <strong>Datos Bancarios</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Banco</label>
                                                    <select name="id_banco" class="form-select" required>
                                                        <option value="" selected disabled>Seleccione...</option>
                                                        <?php
                                                        $bancos->data_seek(0);
                                                        while ($banco = $bancos->fetch_assoc()) {
                                                        ?>
                                                            <option value="<?php echo $banco['id_banco']; ?>">
                                                                <?php echo htmlspecialchars($banco['nombre_banco']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Número de Cuenta</label>
                                                    <input type="text" name="numero_cuenta" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-light border">
                                        <strong>Resultado automático del sistema:</strong><br><br>
                                        ✔ Evaluación psicológica mayor o igual a 73 puntos.<br>
                                        ✔ Revisión vehicular mayor o igual a 65 puntos.<br><br>
                                        Si ambos requisitos se cumplen, la cuenta se activará automáticamente. Si alguno falla, la solicitud quedará rechazada.
                                    </div>

                                    <?php } else { ?>

                                    <div class="alert alert-secondary">
                                        Esta solicitud ya fue procesada. Estado actual:
                                        <span class="badge <?php echo $ch["estado_usuario"] == "Activo" ? "bg-success" : "bg-danger"; ?>">
                                            <?php echo $ch["estado_usuario"]; ?>
                                        </span>
                                    </div>

                                    <?php } ?>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <?php if ($ch["estado_usuario"] == "Pendiente") { ?>
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle me-2"></i>
                                            Guardar Evaluación
                                        </button>
                                    <?php } ?>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

                <?php } ?>

            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>