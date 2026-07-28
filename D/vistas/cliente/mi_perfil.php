<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 3) {
    header("Location: ../login/login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

/*=========================
OBTENER DATOS DEL CLIENTE
=========================*/

$sql = "SELECT u.nombres, u.apellidos, u.cedula, u.correo, u.telefono, u.direccion,
               u.estado_usuario, c.fecha_registro
        FROM usuario u
        INNER JOIN cliente c ON c.id_usuario = u.id_usuario
        WHERE u.id_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();

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
                <h2>Mi Perfil</h2>
                <p>Consulte y administre la información de su cuenta.</p>
            </div>

            <?php if (isset($_GET["ok"])) { ?>
                <div class="alert alert-success text-center">Información actualizada correctamente.</div>
            <?php } ?>

            <?php if (isset($_GET["error"])) { ?>
                <div class="alert alert-danger text-center">Ese correo ya está en uso por otra cuenta.</div>
            <?php } ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">

                        <div class="card-header text-white" style="background:#1E2E4F; padding:18px;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-person-fill"></i>
                                Información Personal
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="text-center mb-4">
                                <i class="bi bi-person-circle" style="font-size:80px;color:#1E2E4F;"></i>
                                <h3 class="mt-3">
                                    <?php echo htmlspecialchars($cliente["nombres"] . " " . $cliente["apellidos"]); ?>
                                </h3>
                                <span class="badge <?php echo $cliente["estado_usuario"] == "Activo" ? "bg-success" : "bg-warning text-dark"; ?>">
                                    Cliente <?php echo htmlspecialchars($cliente["estado_usuario"]); ?>
                                </span>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Cédula</strong><br>
                                    <?php echo htmlspecialchars($cliente["cedula"]); ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Correo electrónico</strong><br>
                                    <?php echo htmlspecialchars($cliente["correo"]); ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Teléfono</strong><br>
                                    <?php echo htmlspecialchars($cliente["telefono"]); ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Fecha de registro</strong><br>
                                    <?php echo date("d/m/Y", strtotime($cliente["fecha_registro"])); ?>
                                </div>
                                <div class="col-12">
                                    <strong>Dirección</strong><br>
                                    <?php echo htmlspecialchars($cliente["direccion"]); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="text-center mt-4">
                                <button class="btn btn-decarrerita" data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="bi bi-pencil-square"></i>
                                    Editar información
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL EDITAR PERFIL -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="../../procesos/editar_perfil.php" method="POST">

                <div class="modal-header" style="background:#1E2E4F;">
                    <h5 class="modal-title text-white">Editar Información</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-control"
                               value="<?php echo htmlspecialchars($cliente["correo"]); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="<?php echo htmlspecialchars($cliente["telefono"]); ?>" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="<?php echo htmlspecialchars($cliente["direccion"]); ?>" required>
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