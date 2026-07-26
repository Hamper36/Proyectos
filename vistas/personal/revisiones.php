<?php
include("../../includes/header.php");
?>

<div class="container-fluid">

    <div class="row">

        <?php
        include("../../includes/sidebar_personal.php");
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

                    Revisiones Vehiculares

                </h2>

                <p>

                    Administre las solicitudes y revisiones técnicas de los vehículos registrados.

                </p>

            </div>



            <!-- FILTROS -->

<div class="row justify-content-center">

    <div class="col-lg-11">

        <div class="card card-form">


            <div class="card-header text-white"
                 style="background:#1E2E4F; padding:10px 15px;">

                <h5 class="mb-0 fw-bold text-white">

                    <i class="bi bi-funnel-fill me-2"></i>

                    Filtros de búsqueda

                </h5>

            </div>



            <div class="card-body py-2">


                <div class="row align-items-end">


    <div class="col-md-3 mb-1">

        <label class="form-label mb-1">
            Chofer
        </label>

        <input type="text"
               class="form-control"
               placeholder="Nombre del chofer">

    </div>


    <div class="col-md-2 mb-1">

        <label class="form-label mb-1">
            Desde
        </label>

        <input type="date"
               class="form-control">

    </div>


    <div class="col-md-2 mb-1">

        <label class="form-label mb-1">
            Hasta
        </label>

        <input type="date"
               class="form-control">

    </div>


    <div class="col-md-3 mb-1">

        <label class="form-label mb-1">
            Estado
        </label>

        <select class="form-select">

            <option>Todos</option>
            <option>Pendiente</option>
            <option>Apto</option>
            <option>No Apto</option>

        </select>

    </div>


    <div class="col-md-2 mb-1 d-grid">

        <button class="btn btn-decarrerita btn-sm">

            <i class="bi bi-search"></i>
            Buscar

        </button>

    </div>


</div>


        </div>


    </div>


</div>





            <!-- SOLICITUDES VEHICULARES -->


            <div class="row justify-content-center mt-4">


                <div class="col-lg-11">


                    <div class="card">



                        <div class="card-header text-white"
                             style="background:#1E2E4F; padding:15px;">


                            <h5 class="mb-0 fw-bold text-white">


                                <i class="bi bi-clipboard-check me-2"></i>


                                Solicitudes pendientes de vehículos


                            </h5>


                        </div>




                        <div class="card-body">


                            <div class="table-responsive">


                                <table class="table table-hover align-middle">



                                    <thead class="table-light">


                                        <tr>


                                            <th>Chofer</th>


                                            <th>Vehículo</th>


                                            <th>Placa</th>


                                            <th>Tipo solicitud</th>


                                            <th>Fecha</th>


                                            <th>Estado</th>


                                            <th class="text-center">

                                                Acción

                                            </th>


                                        </tr>


                                    </thead>



                                    <tbody>



                                        <tr>


                                            <td>

                                                José Pérez

                                            </td>


                                            <td>

                                                Toyota Corolla

                                            </td>


                                            <td>

                                                AB123CD

                                            </td>


                                            <td>


                                                <span class="badge bg-primary">

                                                    Incorporación

                                                </span>


                                            </td>


                                            <td>

                                                18/07/2026

                                            </td>



                                            <td>


                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-success me-1">


                                                    <i class="bi bi-check-circle"></i>


                                                    Revisar


                                                </button>



                                                <button class="btn btn-sm btn-outline-primary">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                            </td>



                                        </tr>





                                        <tr>


                                            <td>

                                                Ana Gómez

                                            </td>


                                            <td>

                                                Chevrolet Aveo

                                            </td>


                                            <td>

                                                CD456EF

                                            </td>


                                            <td>


                                                <span class="badge bg-danger">

                                                    Retiro

                                                </span>


                                            </td>


                                            <td>

                                                22/07/2026

                                            </td>



                                            <td>


                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-success me-1">


                                                    <i class="bi bi-check-circle"></i>


                                                    Revisar


                                                </button>



                                                <button class="btn btn-sm btn-outline-primary">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                            </td>



                                        </tr>



                                    </tbody>



                                </table>


                            </div>


                        </div>


                    </div>


                </div>


            </div>


                     <!-- HISTORIAL DE REVISIONES -->


            <div class="row justify-content-center mt-4">


                <div class="col-lg-11">


                    <div class="card">



                        <div class="card-header text-white"
                             style="background:#1E2E4F; padding:15px;">


                            <h5 class="mb-0 fw-bold text-white">


                                <i class="bi bi-car-front-fill me-2"></i>


                                Historial de revisiones vehiculares


                            </h5>


                        </div>




                        <div class="card-body">


                            <div class="table-responsive">


                                <table class="table table-hover align-middle">


                                    <thead class="table-light">


                                        <tr>


                                            <th>Chofer</th>


                                            <th>Vehículo</th>


                                            <th>Fecha</th>


                                            <th>Calificación</th>


                                            <th>Resultado</th>


                                            <th class="text-center">

                                                Acción

                                            </th>


                                        </tr>


                                    </thead>



                                    <tbody>



                                        <tr>


                                            <td>

                                                José Pérez

                                            </td>


                                            <td>

                                                Toyota Corolla

                                            </td>


                                            <td>

                                                12/01/2026

                                            </td>


                                            <td>

                                                94

                                            </td>


                                            <td>


                                                <span class="badge bg-success">

                                                    Apto

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-outline-primary">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                            </td>


                                        </tr>





                                        <tr>


                                            <td>

                                                Ana Gómez

                                            </td>


                                            <td>

                                                Chevrolet Aveo

                                            </td>


                                            <td>

                                                05/02/2026

                                            </td>


                                            <td>

                                                88

                                            </td>


                                            <td>


                                                <span class="badge bg-success">

                                                    Apto

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-outline-primary">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                            </td>


                                        </tr>





                                        <tr>


                                            <td>

                                                Carlos Ruiz

                                            </td>


                                            <td>

                                                Hyundai Accent

                                            </td>


                                            <td>

                                                20/01/2026

                                            </td>


                                            <td>

                                                60

                                            </td>


                                            <td>


                                                <span class="badge bg-danger">

                                                    No Apto

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-outline-primary">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                            </td>


                                        </tr>



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




<?php
include("../../includes/footer.php");
?>   