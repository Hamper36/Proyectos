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
                    Gestión de Pagos
                </h2>

                <p>
                    Consulte y supervise todos los pagos realizados a los choferes.
                </p>

            </div>




            <!-- RESUMEN -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="row">


                        <div class="col-md-4 mb-3">

                            <div class="card card-money text-center">

                                <div class="card-body">

                                    <i class="bi bi-cash-stack"
                                       style="font-size:50px;color:#198754;">
                                    </i>


                                    <h3 class="mt-3">
                                        $18,500
                                    </h3>


                                    <h5>
                                        Total pagado
                                    </h5>


                                    <p class="text-secondary mb-0">
                                        A choferes
                                    </p>


                                </div>

                            </div>

                        </div>




                        <div class="col-md-4 mb-3">

                            <div class="card card-usuarios text-center">

                                <div class="card-body">


                                    <i class="bi bi-graph-up-arrow"
                                       style="font-size:50px;color:#0d6efd;">
                                    </i>


                                    <h3 class="mt-3">
                                        $26,300
                                    </h3>


                                    <h5>
                                        Ingresos
                                    </h5>


                                    <p class="text-secondary mb-0">
                                        Empresa
                                    </p>


                                </div>

                            </div>

                        </div>




                        <div class="col-md-4 mb-3">

                            <div class="card card-ganancias text-center">

                                <div class="card-body">


                                    <i class="bi bi-piggy-bank-fill"
                                       style="font-size:50px;color:#dc3545;">
                                    </i>


                                    <h3 class="mt-3">
                                        $7,800
                                    </h3>


                                    <h5>
                                        Ganancia neta
                                    </h5>


                                    <p class="text-secondary mb-0">
                                        Acumulada
                                    </p>


                                </div>

                            </div>

                        </div>


                    </div>

                </div>

            </div>






            <!-- FILTROS -->


            <div class="row justify-content-center mt-4">


                <div class="col-lg-11">


                    <div class="card card-form">


                        <div class="card-header text-white"
                             style="background:#1E2E4F;">


                            <h5 class="mb-0 fw-bold text-white">

                                <i class="bi bi-search me-2"></i>

                                Buscar pagos

                            </h5>


                        </div>



                        <div class="card-body">


                            <div class="row">


                                <div class="col-md-4 mb-3">


                                    <label class="form-label">
                                        Buscar
                                    </label>


                                    <input type="text"
                                           class="form-control"
                                           placeholder="Chofer o referencia">


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
                                            Procesado
                                        </option>


                                        <option>
                                            Pendiente
                                        </option>


                                    </select>


                                </div>




                                <div class="col-md-3 mb-3">


                                    <label class="form-label">
                                        Fecha
                                    </label>


                                    <input type="date"
                                           class="form-control">


                                </div>




                              <div class="col-md-2 mb-3 d-flex align-items-end">



                                    <button class="btn btn-decarrerita w-100">

                                        <i class="bi bi-search"></i>

                                        Buscar

                                    </button>


                                </div>


                            </div>


                        </div>


                    </div>


                </div>


            </div>







            <!-- LISTADO DE PAGOS -->


            <div class="row justify-content-center mt-4">


                <div class="col-lg-11">


                    <div class="card">


                        <div class="card-header text-white"
                             style="background:#1E2E4F;">


                            <h5 class="mb-0 fw-bold text-white">

                                <i class="bi bi-cash-coin me-2"></i>

                                Pagos registrados

                            </h5>


                        </div>




                        <div class="card-body">


                            <div class="table-responsive">


                                <table class="table table-hover align-middle">


                                    <thead class="table-light">


                                        <tr>

                                            <th>
                                                Fecha
                                            </th>


                                            <th>
                                                Chofer
                                            </th>


                                            <th>
                                                Banco
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
                                                21/07/2026
                                            </td>


                                            <td>
                                                José Pérez
                                            </td>


                                            <td>
                                                Banco de Venezuela
                                            </td>


                                            <td>
                                                $120.00
                                            </td>


                                            <td>

                                                <span class="badge bg-success">

                                                    Procesado

                                                </span>

                                            </td>


                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Ver detalle

                                                </button>

                                            </td>


                                        </tr>





                                        <tr>


                                            <td>
                                                20/07/2026
                                            </td>


                                            <td>
                                                Ana Gómez
                                            </td>


                                            <td>
                                                Banesco
                                            </td>


                                            <td>
                                                $95.50
                                            </td>


                                            <td>

                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>

                                            </td>


                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Ver detalle

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





            <div class="mb-5"></div>



        </div>


    </div>


</div>



<?php
include("../../includes/footer.php");
?>