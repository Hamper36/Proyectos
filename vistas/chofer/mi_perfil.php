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

                    Mi Perfil

                </h2>

                <p>

                    Consulte y administre la información registrada de su cuenta como chofer.

                </p>

            </div>



            <!-- PERFIL PRINCIPAL -->

            <div class="row justify-content-center">

                <div class="col-lg-8">


                    <div class="card shadow-sm">


                        <div class="card-header text-white"
                             style="background:#1E2E4F; padding:18px;">


                            <h5 class="mb-0 fw-bold text-white">

                                <i class="bi bi-person-vcard me-2"></i>

                                Perfil del chofer

                            </h5>


                        </div>



                        <div class="card-body">


                            <div class="text-center mb-4">


                               <i class="bi bi-person-circle"
   style="font-size:70px;color:#1E2E4F;"></i>



                                <h3 class="mt-2">

                                    José Pérez

                                </h3>



                                <span class="badge bg-success">

                                    Chofer Activo

                                </span>


                            </div>



                            <hr>



                            <div class="row text-center">


                                <div class="col-md-4 mb-3">


                                    <i class="bi bi-clipboard-check-fill text-success"
                                       style="font-size:30px;"></i>


                                    <h6 class="mt-2">

                                        Evaluación psicológica

                                    </h6>


                                    <span class="badge bg-success">

                                        Apto

                                    </span>


                                </div>



                                <div class="col-md-4 mb-3">


                                    <i class="bi bi-car-front-fill text-primary"
                                       style="font-size:30px;"></i>


                                    <h6 class="mt-2">

                                        Vehículo

                                    </h6>


                                    <span class="badge bg-primary">

                                        Toyota Corolla

                                    </span>


                                </div>




                                <div class="col-md-4 mb-3">


                                    <i class="bi bi-calendar-check-fill text-warning"
                                       style="font-size:30px;"></i>


                                    <h6 class="mt-2">

                                        Registro

                                    </h6>


                                    <span>

                                        15/07/2026

                                    </span>


                                </div>


                            </div>



                        </div>


                    </div>


                </div>


            </div>





<!-- DATOS PERSONALES -->

<div class="row justify-content-center mt-4">

    <div class="col-lg-8">

        <div class="card shadow-sm">


            <div class="card-header text-white"
                 style="background:#1E2E4F; padding:18px;">


                <h5 class="mb-0 fw-bold text-white">

                    <i class="bi bi-person-fill me-2"></i>

                    Datos personales

                </h5>


            </div>



            <div class="card-body">


                <div class="row">


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Nombres

                        </label>


                        <input type="text"
                               class="form-control"
                               value="José"
                               readonly>

                    </div>



                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Apellidos

                        </label>


                        <input type="text"
                               class="form-control"
                               value="Pérez González"
                               readonly>

                    </div>



                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Cédula

                        </label>


                        <input type="text"
                               class="form-control"
                               value="V-12345678"
                               readonly>

                    </div>



                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Teléfono

                        </label>


                        <input type="text"
                               class="form-control"
                               value="0412-1234567"
                               readonly>

                    </div>



                    <div class="col-md-12">

                        <label class="form-label fw-bold">

                            Dirección

                        </label>


                        <textarea class="form-control"
                                  rows="2"
                                  readonly>Avenida Bolívar, Maturín, Monagas.</textarea>

                    </div>



                </div>


            </div>


        </div>


    </div>


</div>




<!-- CUENTA E INFORMACIÓN BANCARIA -->


<div class="row justify-content-center mt-4">


    <div class="col-lg-8">


        <div class="card shadow-sm">


            <div class="card-header text-white"
                 style="background:#1E2E4F; padding:18px;">


                <h5 class="mb-0 fw-bold text-white">


                    <i class="bi bi-bank me-2"></i>

                    Cuenta e información bancaria


                </h5>


            </div>



            <div class="card-body">


                <div class="row">



                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Usuario

                        </label>


                        <input type="text"
                               class="form-control"
                               value="jperez"
                               readonly>


                    </div>



                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Correo electrónico

                        </label>


                        <input type="email"
                               class="form-control"
                               value="jperez@correo.com"
                               readonly>


                    </div>



                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Banco

                        </label>


                        <input type="text"
                               class="form-control"
                               value="Banco de Venezuela"
                               readonly>


                    </div>



                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Número de cuenta

                        </label>


                        <input type="text"
                               class="form-control"
                               value="0102-0123-45-1234567890"
                               readonly>


                    </div>



                    <div class="col-md-6">


                        <label class="form-label fw-bold">

                            Estado de la cuenta

                        </label>


                        <input type="text"
                               class="form-control"
                               value="Activa"
                               readonly>


                    </div>



                    <div class="col-md-6">


                        <label class="form-label fw-bold">

                            Fecha de registro

                        </label>


                        <input type="text"
                               class="form-control"
                               value="15/07/2026"
                               readonly>


                    </div>



                </div>


            </div>


        </div>


    </div>


</div>
            
                   <!-- CONTACTOS DE EMERGENCIA -->

<div class="row justify-content-center mt-4">

    <div class="col-lg-8">

        <div class="card shadow-sm">


            <div class="card-header text-white"
                 style="background:#1E2E4F; padding:18px;">


                <h5 class="mb-0 fw-bold text-white">

                    <i class="bi bi-people-fill me-2"></i>

                    Contactos de emergencia

                </h5>


            </div>



            <div class="card-body">


                <div class="row">


                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Contacto 1

                        </label>


                        <input type="text"
                               class="form-control"
                               value="María Pérez - 0412-5551122"
                               readonly>


                    </div>



                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Contacto 2

                        </label>


                        <input type="text"
                               class="form-control"
                               value="Carlos Pérez - 0424-3334455"
                               readonly>


                    </div>


                </div>


            </div>


        </div>


    </div>


</div>





<!-- ESTADO DEL CHOFER -->


<div class="row justify-content-center mt-4">


    <div class="col-lg-8">


        <div class="card shadow-sm">


            <div class="card-header text-white"
                 style="background:#1E2E4F; padding:18px;">


                <h5 class="mb-0 fw-bold text-white">


                    <i class="bi bi-shield-check me-2"></i>

                    Estado del chofer


                </h5>


            </div>



            <div class="card-body">


                <div class="row">


                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Evaluación psicológica

                        </label>


                        <div class="input-group">


                            <span class="input-group-text">

                                <i class="bi bi-check-circle-fill text-success"></i>

                            </span>


                            <input type="text"
                                   class="form-control"
                                   value="Apto"
                                   readonly>


                        </div>


                    </div>




                    <div class="col-md-6 mb-3">


                        <label class="form-label fw-bold">

                            Vehículo principal

                        </label>


                        <div class="input-group">


                            <span class="input-group-text">

                                <i class="bi bi-car-front-fill text-primary"></i>

                            </span>


                            <input type="text"
                                   class="form-control"
                                   value="Toyota Corolla - Apto"
                                   readonly>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </div>


</div>


<div class="mb-5"></div>



<?php
include("../../includes/footer.php");
?>