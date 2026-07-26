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

                    Mis Pagos

                </h2>

                <p>

                    Consulte los pagos realizados por la empresa durante un período determinado.

                </p>

            </div>

           <!-- FILTROS -->

<div class="row justify-content-center">

    <div class="col-lg-11">

        <div class="card card-money">

            <div class="card-header text-white"
                 style="background:#1E2E4F; padding:12px 18px;">

                <h5 class="mb-0 fw-bold text-white">

                    <i class="bi bi-search me-2"></i>
                    Buscar Pagos

                </h5>

            </div>


            <div class="card-body py-3">

                <div class="row align-items-end">


                    <div class="col-md-5 mb-2">

                        <label class="form-label">

                            Desde

                        </label>

                        <input type="date"
                               class="form-control">

                    </div>


                    <div class="col-md-5 mb-2">

                        <label class="form-label">

                            Hasta

                        </label>

                        <input type="date"
                               class="form-control">

                    </div>


                    <div class="col-md-2 mb-2">

                        <button class="btn btn-decarrerita w-100">

                            <i class="bi bi-search"></i>

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

      <i class="bi bi-wallet2 me-2"></i>


        Historial de Pagos

    </h5>

</div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

<thead style="background:#1E2E4F;color:white;">
                                        <tr>

                                            <th>Fecha</th>

                                            <th>Referencia</th>

                                            <th>Monto</th>

                                            <th class="text-center">

                                                Acción

                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>20/07/2026</td>

                                            <td>TRX-458721</td>

                                            <td>$185.40</td>

                                            

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Detalle

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>15/07/2026</td>

                                            <td>TRX-458102</td>

                                            <td>$142.80</td>

                                            

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Detalle

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>08/07/2026</td>

                                            <td>TRX-457633</td>

                                            <td>$96.50</td>

                                           

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Detalle

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>30/06/2026</td>

                                            <td>TRX-456920</td>

                                            <td>$210.30</td>

                                           

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

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

</div>

            <?php
include("../../includes/footer.php");
?>