
<?php
include("../../includes/header.php");
?>

<div class="container-fluid">

    <div class="row">

        <?php
        include("../../includes/sidebar_chofer.php");
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

                    Historial de Viajes

                </h2>

                <p>

                    Consulte los traslados realizados durante un período de tiempo.

                </p>

            </div>


            <!-- FILTROS -->

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card card-form">


                        <div class="card-header text-white"
                             style="background:#1E2E4F; padding:18px;">


                            <h5 class="mb-0 fw-bold text-white">

                                <i class="bi bi-search me-2"></i>

                                Buscar traslados

                            </h5>


                        </div>


                        <div class="card-body">


                            <div class="row align-items-end">


                                <div class="col-md-3 mb-3">


                                    <label class="form-label">

                                        Desde

                                    </label>


                                    <input type="date"
                                           class="form-control">


                                </div>



                                <div class="col-md-3 mb-3">


                                    <label class="form-label">

                                        Hasta

                                    </label>


                                    <input type="date"
                                           class="form-control">


                                </div>



                               <div class="col-md-3 mb-3">


                                    <label class="form-label">

                                        Estado

                                    </label>


                                   <select class="form-select">

    <option>
        Todos
    </option>

    <option>
        Pendiente de pago
    </option>

    <option>
        Pagado
    </option>

</select>

                                </div>



                               <div class="col-md-2 mb-3">


                                    <button class="btn btn-decarrerita w-100">


                                        <i class="bi bi-search me-1"></i>


                                        Consultar


                                    </button>


                                </div>


                            </div>


                        </div>


                    </div>


                </div>


            </div>


                        <!-- TABLA -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">


                    <div class="card">


                        <div class="card-header text-white"
                             style="background:#1E2E4F; padding:18px;">


                            <h5 class="mb-0 fw-bold text-white">


                                <i class="bi bi-car-front-fill me-2"></i>

                                Historial de traslados


                            </h5>


                        </div>



                        <div class="card-body">


                            <div class="table-responsive">


                                <table class="table table-hover align-middle">


                                    <thead style="background:#1E2E4F;color:white;">


                                        <tr>


                                            <th>

                                                Fecha

                                            </th>


                                            <th>

                                                Cliente

                                            </th>


                                            <th>

                                                Origen

                                            </th>


                                            <th>

                                                Destino

                                            </th>


                                            <th>

                                                Monto

                                            </th>


                                            <th>

                                                Estado

                                            </th>


                                            <th class="text-center">

                                                Acción

                                            </th>


                                        </tr>


                                    </thead>



                                    <tbody>



                                        <tr>


                                            <td>

                                                18/07/2026

                                            </td>


                                            <td>

                                                María González

                                            </td>


                                            <td>

                                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>

                                                Centro

                                            </td>


                                            <td>

                                                <i class="bi bi-flag-fill text-danger me-1"></i>

                                                Hospital Central

                                            </td>


                                            <td>

                                                $12.50

                                            </td>


                                            <td>


                                                <span class="badge bg-success">

                                                    Pagado

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-outline-primary btn-sm">


                                                    <i class="bi bi-eye me-1"></i>

                                                    Detalle


                                                </button>


                                            </td>


                                        </tr>




                                        <tr>


                                            <td>

                                                17/07/2026

                                            </td>


                                            <td>

                                                Carlos Rodríguez

                                            </td>


                                            <td>

                                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>

                                                Terminal

                                            </td>


                                            <td>

                                                <i class="bi bi-flag-fill text-danger me-1"></i>

                                                Universidad

                                            </td>


                                            <td>

                                                $8.00

                                            </td>


                                            <td>


                                                <span class="badge bg-warning text-dark">

                                                    Pendiente de pago

                                                </span>


                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-outline-primary btn-sm">


                                                    <i class="bi bi-eye me-1"></i>

                                                    Detalle


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

<?php
include("../../includes/footer.php");
?>