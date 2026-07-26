<?php
$actual_file = basename($_SERVER['PHP_SELF'] ?? '');
?>
<div class="col-md-2 sidebar min-vh-100 p-3">
    <div class="text-center mb-4">
        <i class="bi bi-person-workspace" style="font-size:60px;"></i>
        <h5 class="mt-2">Personal Administrativo</h5>
        <small class="text-secondary">Panel Principal</small>
    </div>
    <hr class="text-secondary">
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'menu.php') ? 'active' : ''; ?>" href="menu.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'gestionar_choferes.php') ? 'active' : ''; ?>" href="gestionar_choferes.php">
                <i class="bi bi-person-vcard-fill"></i> Gestionar Choferes
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'evaluaciones.php') ? 'active' : ''; ?>" href="evaluaciones.php">
                <i class="bi bi-clipboard2-pulse-fill"></i> Evaluaciones
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'revisiones.php') ? 'active' : ''; ?>" href="revisiones.php">
                <i class="bi bi-car-front-fill"></i> Revisiones
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($actual_file == 'pagos.php') ? 'active' : ''; ?>" href="pagos.php">
                <i class="bi bi-cash-stack"></i> Pagos
            </a>
        </li>
    </ul>
    <hr class="text-secondary">
    <a href="../../index.php" class="btn btn-danger w-100">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
</div>