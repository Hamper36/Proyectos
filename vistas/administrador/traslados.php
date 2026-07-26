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

                    Gestión de Traslados

                </h2>

                <p>

                    Consulte todos los traslados registrados en el sistema.

                </p>

            </div>

            <!-- FILTROS -->

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card card-form">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                           <h5 class="mb-0 fw-bold text-white">
    <i class="bi bi-search me-2"></i>


                                Buscar traslados

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        Buscar

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Cliente o chofer">

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Estado

                                    </label>

                                    <select class="form-select">

                                        <option>Todos</option>

                                        <option>Pendiente</option>

                                        <option>En curso</option>

                                        <option>Finalizado</option>

                                        <option>Cancelado</option>

                                    </select>

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Fecha

                                    </label>

                                    <input
                                        type="date"
                                        class="form-control">

                                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">

                                    <button class="btn btn-decarrerita w-100">

                                     <i class="bi bi-search me-2"></i>



                                        Buscar

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

                  <!-- LISTADO DE TRASLADOS -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="card">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                           <h5 class="mb-0 fw-bold text-white">

    <i class="bi bi-list-check me-2"></i>

                                Traslados registrados

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th>Fecha</th>

                                            <th>Cliente</th>

                                            <th>Chofer</th>

                                            <th>Origen</th>

                                            <th>Destino</th>

                                            <th>Estado</th>

                                            <th>Pago</th>

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

                                                María González

                                            </td>

                                            <td>

                                                José Pérez

                                            </td>

                                            <td>

                                                Centro

                                            </td>

                                            <td>

                                                Terminal

                                            </td>

                                            <td>

                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>

                                            </td>

                                            <td>

                                                $12.50

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

                                                Carlos Ruiz

                                            </td>

                                            <td>

                                                Ana Gómez

                                            </td>

                                            <td>

                                                Hospital

                                            </td>

                                            <td>

                                                Universidad

                                            </td>

                                            <td>

                                                <span class="badge bg-primary">

                                                    En curso

                                                </span>

                                            </td>

                                            <td>

                                                $18.00

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

                                                18/07/2026

                                            </td>

                                            <td>

                                                Laura Rodríguez

                                            </td>

                                            <td>

                                                Miguel Torres

                                            </td>

                                            <td>

                                                Aeropuerto

                                            </td>

                                            <td>

                                                Centro Comercial

                                            </td>

                                            <td>

                                                <span class="badge bg-success">

                                                    Finalizado

                                                </span>

                                            </td>

                                            <td>

                                                $25.00

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