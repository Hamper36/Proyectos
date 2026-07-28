<?php
$paginaActual = basename($_SERVER['PHP_SELF']);

if (isset($_SESSION["id_usuario"]) && $_SESSION["id_rol"] == 4 && isset($conexion)) {
    $stmt_check_chofer = $conexion->prepare("SELECT estado_usuario FROM usuario WHERE id_usuario = ?");
    $stmt_check_chofer->bind_param("i", $_SESSION["id_usuario"]);
    $stmt_check_chofer->execute();
    $res_check = $stmt_check_chofer->get_result();
    if ($row_check = $res_check->fetch_assoc()) {
        if ($row_check["estado_usuario"] != "Activo") {
            unset($_SESSION["id_usuario"]);
            unset($_SESSION["id_rol"]);
            header("Location: ../login/login.php?estado=pendiente");
            exit();
        }
    }
}
?>

<div class="col-md-2 text-white min-vh-100 p-3" style="background:#1E2E4F;">


    <div class="text-center mb-4">

        <i class="bi bi-person-circle" style="font-size:60px;"></i>

        <h5 class="mt-2">

            Chofer

        </h5>

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

            <a class="nav-link text-white <?php echo ($paginaActual == 'mis_traslados.php') ? 'active' : ''; ?>" href="mis_traslados.php">

                <i class="bi bi-taxi-front-fill"></i>

                Mis traslados

            </a>

        </li>


        <li class="nav-item mb-2">

            <a class="nav-link text-white <?php echo ($paginaActual == 'historial_viajes.php') ? 'active' : ''; ?>" href="historial_viajes.php">

                <i class="bi bi-clock-history"></i>

                Historial de viajes

            </a>

        </li>


        <li class="nav-item mb-2">

            <a class="nav-link text-white <?php echo ($paginaActual == 'mis_pagos.php') ? 'active' : ''; ?>" href="mis_pagos.php">

                <i class="bi bi-cash-stack"></i>

                Mis pagos

            </a>

        </li>


        <li class="nav-item mb-2">

            <a class="nav-link text-white <?php echo ($paginaActual == 'mis_vehiculos.php') ? 'active' : ''; ?>" href="mis_vehiculos.php">

                <i class="bi bi-car-front-fill"></i>

                Mis vehículos

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