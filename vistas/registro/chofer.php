<?php include("../../includes/header.php"); ?>

<div class="container login-container">

    <div class="login-card p-4" style="max-width:950px; width:100%;">

        <!-- Logo -->

        <div class="text-center mb-4">

            <img src="../../assets/img/logo.png"
                 class="login-logo"
                 alt="Logo Decarrerita">

        </div>

        <!-- Título -->

        <h2 class="text-center login-title">

            Registro de Chofer

        </h2>

        <p class="text-center login-text mb-4">

            Completa la información para enviar tu solicitud de ingreso.

        </p>

        <!-- =======================================================
                            WIZARD
        ======================================================== -->

        <div class="wizard mb-5">

            <div class="wizard-progress">

                <div class="wizard-progress-bar"
                     id="progressBar">

                </div>

            </div>

            <div class="wizard-steps">

                <div class="wizard-step active"
                     id="stepIndicator1">

                    <div class="wizard-circle">

                        1

                    </div>

                    <span>

                        Datos personales

                    </span>

                </div>

                <div class="wizard-step"
                     id="stepIndicator2">

                    <div class="wizard-circle">

                        2

                    </div>

                    <span>

                        Datos de acceso

                    </span>

                </div>

                <div class="wizard-step"
                     id="stepIndicator3">

                    <div class="wizard-circle">

                        3

                    </div>

                    <span>

                        Vehículo

                    </span>

                </div>

                <div class="wizard-step"
                     id="stepIndicator4">

                    <div class="wizard-circle">

                        4

                    </div>

                    <span>

                        Contactos 

                    </span>

                </div>

                <div class="wizard-step"
                     id="stepIndicator5">

                    <div class="wizard-circle">

                        5

                    </div>

                    <span>

                        Confirmación

                    </span>

                </div>

            </div>

        </div>

        <!-- =======================================================
                            FORMULARIO
        ======================================================== -->

        <form action="../../procesos/registrar_chofer.php"
              method="POST"
              id="wizardForm">

            <!-- ==========================
                    PASO 1
            =========================== -->

            <div class="wizard-page"
     id="step1">

    <div class="card shadow-sm border-0">


        

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="bi bi-person-vcard me-2"></i>


                        Datos Personales

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <!-- Cédula -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Cédula

                            </label>

                            <input type="text"
                                   name="cedula"
                                   class="form-control"
                                   maxlength="10"
                                   required>

                        </div>

                        <!-- Correo -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Correo Electrónico

                            </label>

                            <input type="email"
                                   name="correo"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- Nombres -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Nombres

                            </label>

                            <input type="text"
                                   name="nombres"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- Apellidos -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Apellidos

                            </label>

                            <input type="text"
                                   name="apellidos"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- Teléfono -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Teléfono

                            </label>

                            <input type="text"
                                   name="telefono"
                                   class="form-control"
                                   required>

                        </div>

                        <!-- Dirección -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Dirección

                            </label>

                            <input type="text"
                                   name="direccion"
                                   class="form-control"
                                   required>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Botón siguiente -->

            <div class="text-end mt-4">

                <button type="button"
                        class="btn btn-primary btn-lg"
                        onclick="nextStep(2)">

                    Siguiente

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>

            </div>

        </div>

        <!-- ==========================
                PASO 2
        =========================== -->

        <div class="wizard-page d-none"
             id="step2">

                         <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-person-lock me-2"></i>

                        Datos de Acceso

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Nombre de Usuario

                            </label>

                            <input type="text"
                                   name="nombre_usuario"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Contraseña

                            </label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Confirmar Contraseña

                            </label>

                            <input type="password"
                                   name="confirmar_password"
                                   class="form-control"
                                   required>

                        </div>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-between mt-4">

                <button type="button"
                        class="btn btn-secondary btn-lg"
                        onclick="previousStep(1)">

                    <i class="bi bi-arrow-left me-2"></i>

                    Anterior

                </button>

                <button type="button"
                        class="btn btn-primary btn-lg"
                        onclick="nextStep(3)">

                    Siguiente

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>

            </div>

        </div>

        <!-- ==========================
                PASO 3
        =========================== -->

        <div class="wizard-page d-none"
             id="step3">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-car-front-fill me-2"></i>

                        Información del Vehículo

                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Placa

                            </label>

                            <input type="text"
                                   name="placa"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Marca

                            </label>

                            <input type="text"
                                   name="marca"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Modelo

                            </label>

                            <input type="text"
                                   name="modelo"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Año

                            </label>

                            <input type="number"
                                   name="anio"
                                   class="form-control"
                                   min="1980"
                                   max="2100"
                                   required>

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label">

                                Color

                            </label>

                            <input type="text"
                                   name="color"
                                   class="form-control"
                                   required>

                        </div>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-between mt-4">

                <button type="button"
                        class="btn btn-secondary btn-lg"
                        onclick="previousStep(2)">

                    <i class="bi bi-arrow-left me-2"></i>

                    Anterior

                </button>

                <button type="button"
                        class="btn btn-primary btn-lg"
                        onclick="nextStep(4)">

                    Siguiente

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>

            </div>

        </div>

        <!-- ==========================
                PASO 4
        =========================== -->

        <div class="wizard-page d-none"
             id="step4">

                         <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-telephone-fill me-2"></i>

                        Contactos de Emergencia

                    </h5>

                </div>

                <div class="card-body">

                    <div class="alert alert-info">

                        Debe registrar como mínimo <strong>dos contactos de emergencia</strong>.
                        Puede agregar hasta <strong>cuatro contactos</strong> si lo desea.

                    </div>

                    <!-- =======================
                            CONTACTO 1
                    ======================== -->

                    <div class="border rounded p-3 mb-3">

                        <h6 class="text-primary">

                            Contacto de Emergencia #1

                        </h6>

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Nombre

                                </label>

                                <input type="text"
                                       name="contacto_nombre[]"
                                       class="form-control"
                                       required>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Teléfono

                                </label>

                                <input type="text"
                                       name="contacto_telefono[]"
                                       class="form-control"
                                       required>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Parentesco

                                </label>

                                <input type="text"
                                       name="contacto_parentesco[]"
                                       class="form-control"
                                       required>

                            </div>

                        </div>

                    </div>

                    <!-- =======================
                            CONTACTO 2
                    ======================== -->

                    <div class="border rounded p-3 mb-3">

                        <h6 class="text-primary">

                            Contacto de Emergencia #2

                        </h6>

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Nombre

                                </label>

                                <input type="text"
                                       name="contacto_nombre[]"
                                       class="form-control"
                                       required>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Teléfono

                                </label>

                                <input type="text"
                                       name="contacto_telefono[]"
                                       class="form-control"
                                       required>

                            </div>

                            <div class="col-md-4 mb-3">

                                <label class="form-label">

                                    Parentesco

                                </label>

                                <input type="text"
                                       name="contacto_parentesco[]"
                                       class="form-control"
                                       required>

                            </div>

                        </div>

                    </div>

                    <!-- Aquí aparecerán los contactos opcionales -->

                    <div id="contactosExtra">

                    </div>

                    <div class="text-center">

                        <button type="button"
                                id="btnAgregarContacto"
                                class="btn btn-outline-primary">

                            <i class="bi bi-plus-circle"></i>

                            Agregar otro contacto

                        </button>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-between mt-4">

                <button type="button"
                        class="btn btn-secondary btn-lg"
                        onclick="previousStep(3)">

                    <i class="bi bi-arrow-left me-2"></i>

                    Anterior

                </button>

                <button type="button"
                        class="btn btn-success btn-lg"
                        onclick="nextStep(5)">

                    Siguiente

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>

            </div>

        </div>

        <!-- ==========================
                PASO 5
        =========================== -->

        <div class="wizard-page d-none"
             id="step5">

                         <div class="card shadow-sm border-0">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-check-circle-fill me-2"></i>

                        Confirmación de Solicitud

                    </h5>

                </div>

                <div class="card-body">

                    <!-- Resumen -->

                    <div class="alert alert-secondary">

                        <h5 class="mb-3">

                            Resumen de la solicitud

                        </h5>

                        <p class="mb-1">

                            ✔ Datos personales

                        </p>

                        <p class="mb-1">

                            ✔ Datos de acceso

                        </p>

                        <p class="mb-1">

                            ✔ Información del vehículo

                        </p>

                        <p class="mb-3">

                            ✔ Contactos de emergencia

                        </p>

                    </div>

                    <!-- Mensaje -->

                    <div class="alert alert-success">

                        <h5>

                            Confirmación de solicitud

                        </h5>

                        <p>

                            Para finalizar su proceso de ingreso deberá entregar al personal administrativo:

                        </p>

                        <ul>

                            <li>

                                Evaluación psicológica.

                            </li>

                            <li>

                                Revisión vehicular.

                            </li>

                            <li>

                                Constancia bancaria donde se indique:

                                <ul>

                                    <li>

                                        Entidad bancaria.

                                    </li>

                                    <li>

                                        Número de cuenta.

                                    </li>

                                </ul>

                            </li>

                        </ul>

                        <p class="mb-0">

  Una vez validados los documentos y registradas las evaluaciones correspondientes en el sistema, su cuenta será activada si cumple con todos los requisitos establecidos. Le notificaremos el resultado de su solicitud a través de su correo electrónico registrado.

                        </p>

                    </div>

                    <div class="text-center mt-4">

                        <i class="bi bi-envelope-paper-fill text-primary"
                           style="font-size:65px;"></i>

                        <h5 class="mt-3">

                            Contacto

                        </h5>

                        <p>

                            administracion@decarrerita.com

                        </p>

                    </div>

                </div>

            </div>

            <!-- BOTONES -->

            <div class="d-flex justify-content-between mt-4">

                <button type="button"
                        class="btn btn-secondary btn-lg"
                        onclick="previousStep(4)">

                    <i class="bi bi-arrow-left me-2"></i>

                    Anterior

                </button>

                <button type="submit"
                        class="btn btn-success btn-lg">

                    <i class="bi bi-send-check me-2"></i>

                    Enviar Solicitud

                </button>

            </div>

        </div>

    </form>

</div>

</div>

<script src="../../assets/js/registro_chofer.js"></script>


<?php include("../../includes/footer.php"); ?>