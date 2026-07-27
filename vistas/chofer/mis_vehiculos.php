<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Get id_chofer
$stmt_ch = $conexion->prepare("SELECT id_chofer FROM chofer WHERE id_usuario = ?");
$stmt_ch->bind_param("i", $id_usuario);
$stmt_ch->execute();
$id_chofer = $stmt_ch->get_result()->fetch_assoc()["id_chofer"] ?? 0;

// Fetch chofer's vehicles with latest inspection status
$sql = "SELECT v.*, rv.resultado AS estado_revision, rv.fecha_revision 
        FROM vehiculo v 
        LEFT JOIN revision_vehiculo rv ON (rv.id_vehiculo = v.id_vehiculo AND rv.id_revision = (
            SELECT MAX(id_revision) FROM revision_vehiculo WHERE id_vehiculo = v.id_vehiculo
        )) 
        WHERE v.id_chofer = ? 
        ORDER BY v.id_vehiculo DESC";

$stmt_v = $conexion->prepare($sql);
$stmt_v->bind_param("i", $id_chofer);
$stmt_v->execute();
$result_veh = $stmt_v->get_result();

$vehiculos_list = [];
while ($row = $result_veh->fetch_assoc()) {
    $vehiculos_list[] = $row;
}

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar_chofer.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <!-- LOGO -->
            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <!-- TÍTULO -->
            <div class="modulo-header">
                <h2>Mis Vehículos Registrados</h2>
                <p>Gestione los vehículos asociados a su perfil para la prestación del servicio de traslados.</p>
            </div>

            <?php if (isset($_SESSION["mensaje_exito"])): ?>
                <div class="alert alert-success alert-dismissible fade show col-lg-11 mx-auto" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION["mensaje_exito"]; unset($_SESSION["mensaje_exito"]); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION["mensaje_error"])): ?>
                <div class="alert alert-danger alert-dismissible fade show col-lg-11 mx-auto" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_SESSION["mensaje_error"]; unset($_SESSION["mensaje_error"]); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- BOTÓN REGISTRAR VEHÍCULO -->
            <div class="row justify-content-center mb-3">
                <div class="col-lg-11 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoVehiculoChofer">
                        <i class="bi bi-plus-circle me-1"></i> Registrar Nuevo Vehículo
                    </button>
                </div>
            </div>

            <!-- TABLA -->
            <div class="row justify-content-center mt-2">
                <div class="col-lg-11">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-car-front-fill me-2"></i> Lista de Mis Vehículos (<?php echo count($vehiculos_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Placa</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Año</th>
                                            <th>Color</th>
                                            <th>Estado Inspección</th>
                                            <th>Última Revisión</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vehiculos_list)): ?>
                                            <?php foreach ($vehiculos_list as $v): ?>
                                                <tr>
                                                    <td><span class="badge bg-dark fs-6"><?php echo htmlspecialchars($v['placa']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($v['marca']); ?></td>
                                                    <td><?php echo htmlspecialchars($v['modelo']); ?></td>
                                                    <td><?php echo htmlspecialchars($v['anio']); ?></td>
                                                    <td><?php echo htmlspecialchars($v['color']); ?></td>
                                                    <td>
                                                        <?php
                                                        $est = $v['estado_revision'] ?? 'Pendiente';
                                                        $badge_est = 'bg-warning text-dark';
                                                        if ($est == 'Apto') $badge_est = 'bg-success';
                                                        if ($est == 'No Apto') $badge_est = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?php echo $badge_est; ?>"><?php echo htmlspecialchars($est); ?></span>
                                                    </td>
                                                    <td><?php echo !empty($v['fecha_revision']) ? date('d/m/Y', strtotime($v['fecha_revision'])) : 'Pendiente'; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No tiene vehículos registrados en su cuenta.
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

<!-- MODAL REGISTRAR NUEVO VEHÍCULO PARA EL CHOFER -->
<div class="modal fade" id="modalNuevoVehiculoChofer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="../../procesos/registrar_vehiculo_chofer.php">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-car-front-fill me-2"></i>Agregar Vehículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Placa *</label>
                        <input type="text" name="placa" class="form-control" placeholder="Ej: AB123CD" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Marca *</label>
                        <input type="text" name="marca" class="form-control" placeholder="Ej: Toyota" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Modelo *</label>
                        <input type="text" name="modelo" class="form-control" placeholder="Ej: Corolla" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Año *</label>
                        <input type="number" name="anio" class="form-control" min="1980" max="2100" value="<?php echo date('Y'); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Color *</label>
                        <input type="text" name="color" class="form-control" placeholder="Ej: Blanco" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Vehículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>
