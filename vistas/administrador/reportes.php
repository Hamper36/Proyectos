<?php
include("../../includes/header.php");
?>

<div class="container-fluid">

    <div class="row">

        <?php
        include("../../includes/sidebar.php");
        ?>

        <div class="col-md-10 dashboard p-4">

            <!-- LOGO -->

            <div class="text-center mt-3">

                <img src="../../assets/img/logo.png"
                     width="220"
                     class="logo-dashboard">

            </div>

            <!-- TÍTULO -->

            <div class="modulo-header">

                <h2>

                    Reportes del Sistema

                </h2>

                <p>

                    Consulte indicadores generales y genere reportes de la empresa.

                </p>

            </div>


            <!-- FILTROS -->

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card card-form">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

<h5 class="mb-0 fw-bold text-white">
        <i class="bi bi-file-earmark-bar-graph me-2"></i>

                                Generar reporte

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Tipo

                                    </label>

                                    <select class="form-select">

                                        <option>General</option>

                                        <option>Usuarios</option>

                                        <option>Traslados</option>

                                        <option>Vehículos</option>

                                        <option>Pagos</option>

                                    </select>

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Desde

                                    </label>

                                    <input
                                        type="date"
                                        class="form-control">

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Hasta

                                    </label>

                                    <input
                                        type="date"
                                        class="form-control">

                                </div>

                         <div class="col-md-3 mb-3 d-flex align-items-end">

    <button class="btn btn-decarrerita w-100">

        <i class="bi bi-file-earmark-bar-graph me-2"></i>

        Generar

    </button>

</div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

                

            <!-- GRÁFICOS -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="card">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

<h5 class="mb-0 fw-bold text-white">

    <i class="bi bi-bar-chart-fill me-2"></i>

                                Resumen gráfico

                            </h5>

                        </div>

                        <div class="card-body text-center py-5">

                            <i class="bi bi-bar-chart-line-fill"
                               style="font-size:80px;color:#1E2E4F;"></i>

                            <h4 class="mt-3">

                                Espacio reservado para gráficos

                            </h4>

                            <p class="text-secondary">

                                Aquí se mostrarán gráficos estadísticos cuando el sistema esté conectado a la base de datos.

                            </p>

                        </div>

                    </div>

                </div>

            </div>
            
                 

        </div>

    </div>

</div>

<?php
include("../../includes/footer.php");
?>     