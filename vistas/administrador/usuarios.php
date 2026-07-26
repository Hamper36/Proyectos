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

                    Gestión de Usuarios

                </h2>

                <p>

                    Administre los usuarios registrados en el sistema Decarrerita.

                </p>

            </div>

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card card-form">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                          <h5 class="mb-0 fw-bold text-white">

                        <i class="bi bi-search me-2"></i>


                                Buscar usuarios

                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-5 mb-3">

                                    <label class="form-label">

                                        Buscar

                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           placeholder="Nombre, correo o cédula">

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Tipo de usuario

                                    </label>

                                    <select class="form-select">

                                        <option>Todos</option>

                                        <option>Cliente</option>

                                        <option>Chofer</option>

                                        <option>Personal Administrativo</option>

                                        <option>Administrador</option>

                                    </select>

                                </div>

                                <div class="col-md-2 mb-3">

                                    <label class="form-label">

                                        Estado

                                    </label>

                                    <select class="form-select">

                                        <option>Todos</option>

                                        <option>Activo</option>

                                        <option>Pendiente</option>

                                        <option>Suspendido</option>

                                        <option>Rechazado</option>

                                    </select>

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

                        <!-- LISTADO DE USUARIOS -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="card">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                           <h5 class="mb-0 fw-bold text-white">


    <i class="bi bi-person-lines-fill me-2"></i>

    Usuarios registrados

</h5>

                        </div>


                        <div class="card-body">


                            <div class="table-responsive">


                                <table class="table table-hover align-middle">


                                    <thead class="table-light">


                                        <tr>


                                            <th>

                                                Nombre

                                            </th>


                                            <th>

                                                Correo

                                            </th>


                                            <th>

                                                Tipo

                                            </th>


                                            <th>

                                                Estado

                                            </th>


                                            <th>

                                                Fecha registro

                                            </th>


                                            <th class="text-center">

                                                Acción

                                            </th>


                                        </tr>


                                    </thead>


                                    <tbody>


                                        <tr>


                                            <td>

                                                María González

                                            </td>


                                            <td>

                                                maria@gmail.com

                                            </td>


                                            <td>


                                                <span class="badge bg-primary">

                                                    Cliente

                                                </span>


                                            </td>


                                            <td>


                                                <span class="badge bg-success">

                                                    Activo

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


                                                <button class="btn btn-sm btn-outline-warning">


                                                    <i class="bi bi-pencil"></i>


                                                    Estado


                                                </button>


                                            </td>


                                        </tr>




                                        <tr>


                                            <td>

                                                José Pérez

                                            </td>


                                            <td>

                                                jose@gmail.com

                                            </td>


                                            <td>


                                                <span class="badge bg-success">

                                                    Chofer

                                                </span>


                                            </td>


                                            <td>


                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>


                                            </td>


                                            <td>

                                                15/07/2026

                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-outline-primary me-1">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                                <button class="btn btn-sm btn-outline-warning">


                                                    <i class="bi bi-pencil"></i>


                                                    Estado


                                                </button>


                                            </td>


                                        </tr>




                                        <tr>


                                            <td>

                                                Laura Rodríguez

                                            </td>


                                            <td>

                                                laura@decarrerita.com

                                            </td>


                                            <td>


                                                <span class="badge bg-dark">

                                                    Personal Administrativo

                                                </span>


                                            </td>


                                            <td>


                                                <span class="badge bg-success">

                                                    Activo

                                                </span>


                                            </td>


                                            <td>

                                                02/06/2026

                                            </td>


                                            <td class="text-center">


                                                <button class="btn btn-sm btn-outline-primary me-1">


                                                    <i class="bi bi-eye"></i>


                                                    Ver


                                                </button>


                                                <button class="btn btn-sm btn-outline-warning">


                                                    <i class="bi bi-pencil"></i>


                                                    Estado


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