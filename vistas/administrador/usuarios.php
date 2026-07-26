<?php
session_start();
include("../../config/conexion.php");

if (!isset($_SESSION["id_usuario"]) || $_SESSION["id_rol"] != 1) {
    header("Location: ../login/login.php");
    exit();
}

/*=========================
FILTROS DE BÚSQUEDA
=========================*/
$buscar = trim($_GET['buscar'] ?? '');
$rol_filtro = trim($_GET['rol'] ?? 'Todos');
$estado_filtro = trim($_GET['estado'] ?? 'Todos');

$where_clauses = ["1=1"];
$params = [];
$types = "";

if (!empty($buscar)) {
    $where_clauses[] = "(u.nombres LIKE ? OR u.apellidos LIKE ? OR u.correo LIKE ? OR u.cedula LIKE ? OR u.nombre_usuario LIKE ?)";
    $search_param = "%$buscar%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sssss";
}

if ($rol_filtro !== 'Todos' && !empty($rol_filtro)) {
    $where_clauses[] = "r.nombre_rol = ?";
    $params[] = $rol_filtro;
    $types .= "s";
}

if ($estado_filtro !== 'Todos' && !empty($estado_filtro)) {
    $where_clauses[] = "u.estado_usuario = ?";
    $params[] = $estado_filtro;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$sql = "SELECT u.*, r.nombre_rol 
        FROM usuario u 
        INNER JOIN rol r ON r.id_rol = u.id_rol 
        WHERE $where_sql 
        ORDER BY u.id_usuario DESC";

$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_usuarios = $stmt->get_result();

$usuarios_list = [];
while ($row = $result_usuarios->fetch_assoc()) {
    $usuarios_list[] = $row;
}

include("../../includes/header.php");
?>

<div class="container-fluid">
    <div class="row">
        <?php include("../../includes/sidebar.php"); ?>

        <div class="col-md-10 dashboard p-4">

            <!-- LOGO -->
            <div class="text-center mt-3">
                <img src="../../assets/img/logo.png" width="220" class="logo-dashboard" alt="Logo">
            </div>

            <!-- TÍTULO -->
            <div class="modulo-header">
                <h2>Gestión de Usuarios</h2>
                <p>Administre los usuarios registrados en el sistema Decarrerita.</p>
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

            <!-- BOTÓN NUEVO USUARIO -->
            <div class="row justify-content-center mb-3">
                <div class="col-lg-11 d-flex justify-content-end">
                    <button type="button" class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                        <i class="bi bi-person-plus-fill me-1"></i> Registrar Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- FILTROS -->
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-form">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-search me-2"></i> Buscar usuarios
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="usuarios.php" class="row">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label fw-bold">Buscar</label>
                                    <input type="text" name="buscar" class="form-control" placeholder="Nombre, correo, cédula o usuario" value="<?php echo htmlspecialchars($buscar); ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Tipo de usuario</label>
                                    <select name="rol" class="form-select">
                                        <option value="Todos" <?php echo ($rol_filtro == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Cliente" <?php echo ($rol_filtro == 'Cliente') ? 'selected' : ''; ?>>Cliente</option>
                                        <option value="Chofer" <?php echo ($rol_filtro == 'Chofer') ? 'selected' : ''; ?>>Chofer</option>
                                        <option value="Personal Administrativo" <?php echo ($rol_filtro == 'Personal Administrativo') ? 'selected' : ''; ?>>Personal Administrativo</option>
                                        <option value="Administrador" <?php echo ($rol_filtro == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label fw-bold">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="Todos" <?php echo ($estado_filtro == 'Todos') ? 'selected' : ''; ?>>Todos</option>
                                        <option value="Activo" <?php echo ($estado_filtro == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                        <option value="Pendiente" <?php echo ($estado_filtro == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="Suspendido" <?php echo ($estado_filtro == 'Suspendido') ? 'selected' : ''; ?>>Suspendido</option>
                                        <option value="Rechazado" <?php echo ($estado_filtro == 'Rechazado') ? 'selected' : ''; ?>>Rechazado</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-decarrerita w-100">
                                        <i class="bi bi-search me-1"></i> Buscar
                                    </button>
                                    <?php if (!empty($buscar) || $rol_filtro !== 'Todos' || $estado_filtro !== 'Todos'): ?>
                                        <a href="usuarios.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LISTADO DE USUARIOS -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    <div class="card">
                        <div class="card-header text-white" style="background:#1E2E4F;">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="bi bi-person-lines-fill me-2"></i> Usuarios registrados (<?php echo count($usuarios_list); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre Completo</th>
                                            <th>Correo</th>
                                            <th>Tipo</th>
                                            <th>Estado</th>
                                            <th>Fecha Registro</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($usuarios_list)): ?>
                                            <?php foreach ($usuarios_list as $u): ?>
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']); ?></div>
                                                        <small class="text-muted">@<?php echo htmlspecialchars($u['nombre_usuario']); ?> - C.I: <?php echo htmlspecialchars($u['cedula']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($u['correo']); ?></td>
                                                    <td>
                                                        <?php
                                                        $badge_rol = 'bg-secondary';
                                                        if ($u['nombre_rol'] == 'Cliente') $badge_rol = 'bg-primary';
                                                        if ($u['nombre_rol'] == 'Chofer') $badge_rol = 'bg-info text-dark';
                                                        if ($u['nombre_rol'] == 'Personal Administrativo') $badge_rol = 'bg-dark';
                                                        if ($u['nombre_rol'] == 'Administrador') $badge_rol = 'bg-warning text-dark';
                                                        ?>
                                                        <span class="badge <?php echo $badge_rol; ?>"><?php echo htmlspecialchars($u['nombre_rol']); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badge_est = 'bg-secondary';
                                                        if ($u['estado_usuario'] == 'Activo') $badge_est = 'bg-success';
                                                        if ($u['estado_usuario'] == 'Pendiente') $badge_est = 'bg-warning text-dark';
                                                        if ($u['estado_usuario'] == 'Suspendido') $badge_est = 'bg-danger';
                                                        if ($u['estado_usuario'] == 'Rechazado') $badge_est = 'bg-secondary';
                                                        ?>
                                                        <span class="badge <?php echo $badge_est; ?>"><?php echo htmlspecialchars($u['estado_usuario']); ?></span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($u['fecha_creacion'])); ?></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalVerUser<?php echo $u['id_usuario']; ?>">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </button>

                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalEstadoUser<?php echo $u['id_usuario']; ?>">
                                                            <i class="bi bi-pencil"></i> Estado
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No se encontraron usuarios con los criterios de búsqueda especificados.
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

<!-- MODALS RENDERED OUTSIDE TABLE TO PREVENT FLICKERING -->
<?php foreach ($usuarios_list as $u): ?>

    <!-- MODAL VER DETALLE USUARIO -->
    <div class="modal fade" id="modalVerUser<?php echo $u['id_usuario']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Detalles del Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>ID Usuario:</strong> <?php echo $u['id_usuario']; ?></p>
                    <p><strong>Nombres y Apellidos:</strong> <?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']); ?></p>
                    <p><strong>Nombre de Usuario:</strong> <?php echo htmlspecialchars($u['nombre_usuario']); ?></p>
                    <p><strong>Cédula:</strong> <?php echo htmlspecialchars($u['cedula']); ?></p>
                    <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($u['correo']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($u['telefono'] ?? 'No registrado'); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($u['direccion'] ?? 'No registrada'); ?></p>
                    <p><strong>Rol:</strong> <?php echo htmlspecialchars($u['nombre_rol']); ?></p>
                    <p><strong>Estado Actual:</strong> <?php echo htmlspecialchars($u['estado_usuario']); ?></p>
                    <p><strong>Fecha de Registro:</strong> <?php echo date('d/m/Y H:i', strtotime($u['fecha_creacion'])); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CAMBIAR ESTADO USUARIO -->
    <div class="modal fade" id="modalEstadoUser<?php echo $u['id_usuario']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="../../procesos/cambiar_estado_usuario.php">
                    <div class="modal-header text-white" style="background:#1E2E4F;">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Cambiar Estado de Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">
                        <p class="mb-2"><strong>Usuario:</strong> <?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']); ?></p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Seleccione el nuevo estado:</label>
                            <select name="estado_usuario" class="form-select" required>
                                <option value="Activo" <?php echo ($u['estado_usuario'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="Pendiente" <?php echo ($u['estado_usuario'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Suspendido" <?php echo ($u['estado_usuario'] == 'Suspendido') ? 'selected' : ''; ?>>Suspendido</option>
                                <option value="Rechazado" <?php echo ($u['estado_usuario'] == 'Rechazado') ? 'selected' : ''; ?>>Rechazado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<!-- MODAL REGISTRAR NUEVO USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="../../procesos/registrar_usuario_admin.php">
                <div class="modal-header text-white" style="background:#1E2E4F;">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3 text-start">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombres *</label>
                        <input type="text" name="nombres" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Apellidos *</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Cédula *</label>
                        <input type="text" name="cedula" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombre de Usuario *</label>
                        <input type="text" name="nombre_usuario" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Correo Electrónico *</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Contraseña *</label>
                        <input type="password" name="contrasena" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo de Usuario *</label>
                        <select name="id_rol" class="form-select" required>
                            <option value="3">Cliente</option>
                            <option value="4">Chofer</option>
                            <option value="2">Personal Administrativo</option>
                            <option value="1">Administrador</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Estado *</label>
                        <select name="estado_usuario" class="form-select" required>
                            <option value="Activo">Activo</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Suspendido">Suspendido</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Registrar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>