<?php
$paginaActual = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-2 sidebar min-vh-100 p-3">

    <div class="text-center mb-4">

        <i class="bi bi-person-circle" style="font-size:60px;"></i>

        <h5 class="mt-2">Cliente</h5>

        <small class="text-light">
            Sistema Decarrerita
        </small>

    </div>

    <hr class="text-light">


    <ul class="nav flex-column">


        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($paginaActual == 'menu.php') ? 'active' : ''; ?>" href="menu.php">

                <i class="bi bi-speedometer2"></i>

                Dashboard

            </a>
        </li>


        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($paginaActual == 'solicitar_traslado.php') ? 'active' : ''; ?>" href="solicitar_traslado.php">

                <i class="bi bi-taxi-front-fill"></i>

                Solicitar traslado

            </a>
        </li>


        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($paginaActual == 'mis_traslados.php') ? 'active' : ''; ?>" href="mis_traslados.php">

                <i class="bi bi-map"></i>

                Mis traslados

            </a>
        </li>


        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($paginaActual == 'recargar_saldo.php') ? 'active' : ''; ?>" href="recargar_saldo.php">

                <i class="bi bi-cash-coin"></i>

                Recargar saldo

            </a>
        </li>


        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($paginaActual == 'historial_recargas.php') ? 'active' : ''; ?>" href="historial_recargas.php">

                <i class="bi bi-clock-history"></i>

                Historial de recargas

            </a>
        </li>


        <li class="nav-item mb-2">
            <a class="nav-link text-white <?php echo ($paginaActual == 'mi_perfil.php') ? 'active' : ''; ?>" href="mi_perfil.php">

                <i class="bi bi-person-fill"></i>

                Mi perfil

            </a>
        </li>


    </ul>


    <hr class="text-light">


    <a href="../../index.php" class="btn btn-danger w-100">

        <i class="bi bi-box-arrow-right"></i>

        Cerrar sesión

    </a>


</div>