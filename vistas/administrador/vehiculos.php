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

                    Gestión de Vehículos

                </h2>

                <p>

                    Consulte los vehículos registrados y supervise su estado dentro del sistema.

                </p>

            </div>

            <!-- FILTROS -->

            <div class="row justify-content-center">

                <div class="col-lg-8">

                    <div class="card card-form">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

<h5 class="mb-0 fw-bold text-white">

    <i class="bi bi-search me-2"></i>


                                Buscar vehículos

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                          <div class="col-md-6 mb-3">


                                    <label class="form-label">

                                        Buscar

                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           placeholder="Placa, modelo o propietario">

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Estado

                                    </label>

                                    <select class="form-select">

                                        <option>Todos</option>

                                        <option>Apto</option>

                                        <option>Pendiente</option>

                                        <option>No Apto</option>

                                        <option>Retirado</option>

                                    </select>

                                </div>

        

                                      <div class="col-md-3 mb-3 d-flex align-items-end">


                                    <button class="btn btn-decarrerita w-100">

                                       <i class="bi bi-search me-1"></i>



                                        Buscar

                                    </button>

                                </div>

                            </div>



                        </div>

                    </div>

                </div>

            </div>

                  <!-- LISTADO DE VEHÍCULOS -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="card">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                         <h5 class="mb-0 fw-bold text-white">



                      <i class="bi bi-car-front me-2"></i>
          

                                Vehículos registrados

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th>Placa</th>

                                            <th>Propietario</th>

                                            <th>Modelo</th>

                                            <th>Año</th>

                                            <th>Estado</th>

                                            <th>Última revisión</th>

                                            <th class="text-center">

                                                Acción

                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>

                                                AB123CD

                                            </td>

                                            <td>

                                                José Pérez

                                            </td>

                                            <td>

                                                Toyota Corolla

                                            </td>

                                            <td>

                                                2022

                                            </td>

                                            <td>

                                                <span class="badge bg-success">

                                                    Apto

                                                </span>

                                            </td>

                                            <td>

                                                10/07/2026

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary me-1">

                                                    <i class="bi bi-eye"></i>

                                                    Ver

                                                </button>

                                                <button class="btn btn-sm btn-outline-secondary">

                                                    <i class="bi bi-clock-history"></i>

                                                    Historial

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>

                                                XY456EF

                                            </td>

                                            <td>

                                                Ana Gómez

                                            </td>

                                            <td>

                                                Chevrolet Aveo

                                            </td>

                                            <td>

                                                2021

                                            </td>

                                            <td>

                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>

                                            </td>

                                            <td>

                                                05/07/2025

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary me-1">

                                                    <i class="bi bi-eye"></i>

                                                    Ver

                                                </button>

                                                <button class="btn btn-sm btn-outline-secondary">

                                                    <i class="bi bi-clock-history"></i>

                                                    Historial

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>

                                                GH789IJ

                                            </td>

                                            <td>

                                                Carlos Ruiz

                                            </td>

                                            <td>

                                                Hyundai Accent

                                            </td>

                                            <td>

                                                2020

                                            </td>

                                            <td>

                                                <span class="badge bg-danger">

                                                    No Apto

                                                </span>

                                            </td>

                                            <td>

                                                02/07/2026

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary me-1">

                                                    <i class="bi bi-eye"></i>

                                                    Ver

                                                </button>

                                                <button class="btn btn-sm btn-outline-secondary">

                                                    <i class="bi bi-clock-history"></i>

                                                    Historial

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






