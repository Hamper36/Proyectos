<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 4) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER DATOS DEL CHOFER Y USUARIO
=========================*/
$sql = "SELECT u.*, ch.id_chofer, ch.numero_cuenta, ch.fecha_registro AS fecha_reg_chofer, b.nombre_banco 
        FROM usuario u 
        INNER JOIN chofer ch ON ch.id_usuario = u.id_usuario 
        LEFT JOIN banco b ON b.id_banco = ch.id_banco 
        WHERE u.id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$chofer = $stmt->get_result()->fetch_assoc();

$id_chofer = $chofer["id_chofer"] ?? 0;

/*=========================
OBTENER CONTACTOS DE EMERGENCIA
=========================*/
$stmt_ce = $conexion->prepare("SELECT * FROM contacto_emergencia WHERE id_chofer = ?");
$stmt_ce->bind_param("i", $id_chofer);
$stmt_ce->execute();
$result_ce = $stmt_ce->get_result();

$contactos = [];
while ($r = $result_ce->fetch_assoc()) {
    $contactos[] = $r;
}

/*=========================
OBTENER EVALUACIÓN Y VEHÍCULO
=========================*/
$stmt_ev = $conexion->prepare("SELECT resultado FROM evaluacion_psicologica WHERE id_chofer = ? ORDER BY id_evaluacion DESC LIMIT 1");
$stmt_ev->bind_param("i", $id_chofer);
$stmt_ev->execute();
$evaluacion = $stmt_ev->get_result()->fetch_assoc();

$stmt_vh = $conexion->prepare("SELECT v.*, rv.resultado AS estado_rev 
                               FROM vehiculo v 
                               LEFT JOIN revision_vehiculo rv ON (rv.id_vehiculo = v.id_vehiculo AND rv.id_revision = (
                                   SELECT MAX(id_revision) FROM revision_vehiculo WHERE id_vehiculo = v.id_vehiculo
                               )) 
                               WHERE v.id_chofer = ? LIMIT 1");
$stmt_vh->bind_param("i", $id_chofer);
$stmt_vh->execute();
$vehiculo = $stmt_vh->get_result()->fetch_assoc();

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
                <h2>Mi Perfil</h2>
                <p>Consulte y administre la información registrada de su cuenta como chofer.</p>
            </div>

            <!-- PERFIL PRINCIPAL -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-person-vcard me-2"></i> Perfil del chofer
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="bi bi-person-circle" style="font-size:70px;color:#1E2E4F;"></i>
                                <h3 class="mt-2"><?php echo htmlspecialchars($chofer['nombres'] . ' ' . $chofer['apellidos']); ?></h3>
                                <span class="badge <?php echo ($chofer['estado_usuario'] == 'Activo') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                    Chofer <?php echo htmlspecialchars($chofer['estado_usuario']); ?>
                                </span>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <i class="bi bi-clipboard-check-fill text-success" style="font-size:30px;"></i>
                                    <h6 class="mt-2">Evaluación psicológica</h6>
                                    <span class="badge <?php echo (($evaluacion['resultado'] ?? '') == 'Apto') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo htmlspecialchars($evaluacion['resultado'] ?? 'Pendiente'); ?>
                                    </span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <i class="bi bi-car-front-fill text-primary" style="font-size:30px;"></i>
                                    <h6 class="mt-2">Vehículo principal</h6>
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars(($vehiculo['marca'] ?? 'Sin') . ' ' . ($vehiculo['modelo'] ?? 'Vehículo')); ?>
                                    </span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <i class="bi bi-calendar-check-fill text-warning" style="font-size:30px;"></i>
                                    <h6 class="mt-2">Registro</h6>
                                    <span><?php echo date('d/m/Y', strtotime($chofer['fecha_creacion'])); ?></span>
                                </div>
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
                                <i class="bi bi-person-fill me-2"></i> Datos personales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nombres</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['nombres']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Apellidos</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['apellidos']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cédula</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['cedula']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['telefono'] ?? 'N/A'); ?>" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Dirección</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['direccion'] ?? 'N/A'); ?>" readonly>
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
                                <i class="bi bi-bank me-2"></i> Cuenta e información bancaria
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Usuario</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['nombre_usuario']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Correo electrónico</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($chofer['correo']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Entidad Bancaria</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['nombre_banco'] ?? 'No registrada'); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Número de cuenta</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($chofer['numero_cuenta'] ?? 'No registrada'); ?>" readonly>
                                </div>
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
                                <i class="bi bi-people-fill me-2"></i> Contactos de emergencia (Mínimo 2)
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($contactos)): ?>
                                <div class="row g-3">
                                    <?php foreach ($contactos as $idx => $c): ?>
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light">
                                                <h6 class="fw-bold text-primary mb-1"><i class="bi bi-person-heart me-1"></i> Contacto #<?php echo ($idx + 1); ?></h6>
                                                <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($c['nombre']); ?></p>
                                                <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($c['telefono']); ?></p>
                                                <p class="mb-0"><strong>Parentesco:</strong> <?php echo htmlspecialchars($c['parentesco']); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0"><i class="bi bi-exclamation-circle me-1"></i> No se encontraron contactos de emergencia registrados.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5"></div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>