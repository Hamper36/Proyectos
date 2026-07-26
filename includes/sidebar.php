<?php
$actual_file = basename($_SERVER['PHP_SELF'] ?? '');
?>
<div class="col-md-2 sidebar min-vh-100 p-3">

    <div class="text-center mb-4">
        <i class="bi bi-person-workspace" style="font-size:60px;"></i>
        <h5 class="mt-2">Administrador</h5>
        <small class="text-light">Sistema Decarrerita</small>
    </div>

    <hr class="text-light">

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'menu.php') ? 'active' : ''; ?>" href="menu.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'usuarios.php') ? 'active' : ''; ?>" href="usuarios.php">
                <i class="bi bi-people-fill"></i> Usuarios
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'vehiculos.php') ? 'active' : ''; ?>" href="vehiculos.php">
                <i class="bi bi-car-front-fill"></i> Vehículos
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'traslados.php') ? 'active' : ''; ?>" href="traslados.php">
                <i class="bi bi-taxi-front-fill"></i> Traslados
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'pagos.php') ? 'active' : ''; ?>" href="pagos.php">
                <i class="bi bi-cash-stack"></i> Pagos
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'reportes.php') ? 'active' : ''; ?>" href="reportes.php">
                <i class="bi bi-graph-up"></i> Reportes
            </a>
        </li>
    </ul>

    <hr class="text-light">

    <a href="../../index.php" class="btn btn-danger w-100">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>

</div>