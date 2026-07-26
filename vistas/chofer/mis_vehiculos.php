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

                    Mis Vehículos

                </h2>

                <p>

                    Consulte los vehículos registrados para prestar el servicio de transporte.

                </p>

            </div>

            <!-- BOTONES -->

            <div class="row justify-content-center mb-4">

                <div class="col-lg-11 text-end">

                    <button class="btn btn-decarrerita me-2">

                        <i class="bi bi-plus-circle"></i>

                        Registrar vehículo

                    </button>

                    <button class="btn btn-outline-danger">

                        <i class="bi bi-trash"></i>

                        Solicitar retiro

                    </button>

                </div>

            </div>

            <!-- TABLA -->

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                           <h5 class="mb-0 fw-bold text-white">

    <i class="bi bi-car-front-fill me-2"></i>

    Vehículos registrados

</h5>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                  <thead style="background:#1E2E4F;color:white;">

                                        <tr>

                                            <th>Placa</th>

                                            <th>Marca</th>

                                            <th>Modelo</th>

                                            <th>Año</th>

                                            <th>Última revisión</th>

                                            <th>Estado</th>

                                            <th class="text-center">

                                                Acción

                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>AB123CD</td>

                                            <td>Toyota</td>

                                            <td>Corolla</td>

                                            <td>2022</td>

                                            <td>18/07/2026</td>

                                            <td>

                                                <span class="badge bg-success">

                                                    Apto

                                                </span>

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Detalle

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>XY456ZT</td>

                                            <td>Chevrolet</td>

                                            <td>Spark</td>

                                            <td>2020</td>

                                            <td>—</td>

                                            <td>

                                                <span class="badge bg-warning text-dark">

                                                    Pendiente de revisión

                                                </span>

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Detalle

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>JK789LM</td>

                                            <td>Hyundai</td>

                                            <td>Accent</td>

                                            <td>2018</td>

                                            <td>05/07/2026</td>

                                            <td>

                                                <span class="badge bg-danger">

                                                    No Apto

                                                </span>

                                            </td>

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

                  <!-- INFORMACIÓN -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="card card-info">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                            <h5 class="mb-0 fw-bold text-white">

    

    Información Importante

</h5>

                        </div>

                        <div class="card-body">

                            <div class="mb-3">

                                <i class="bi bi-check-circle-fill text-success me-2"></i>

                                Puede registrar varios vehículos para prestar el servicio de transporte.

                            </div>

                            <div class="mb-3">

                                <i class="bi bi-check-circle-fill text-success me-2"></i>

                                Todo vehículo nuevo deberá ser revisado por el personal administrativo antes de quedar habilitado para prestar servicio.

                            </div>

                            <div class="mb-3">

                                <i class="bi bi-check-circle-fill text-success me-2"></i>

                                Las revisiones técnicas se realizan anualmente y el estado del vehículo se actualizará automáticamente según su resultado.

                            </div>

                            <div class="mb-3">

                                <i class="bi bi-check-circle-fill text-success me-2"></i>

                                Si solicita eliminar un vehículo, la petición será enviada al personal administrativo para su procesamiento.

                            </div>

                            <div>

                                <i class="bi bi-info-circle-fill text-primary me-2"></i>

                                Cuando el sistema esté conectado a la base de datos, las solicitudes de registro y eliminación serán gestionadas automáticamente por el sistema.

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
