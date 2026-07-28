<?php include("../../includes/header.php"); ?>

<div class="container login-container">

    <div class="login-card p-4">

        <!-- Logo -->

        <div class="text-center mb-4">

            <img src="../../assets/img/logo.png"
                 class="login-logo"
                 alt="Logo Decarrerita">

        </div>

        <h2 class="text-center login-title">

            Crear una cuenta

        </h2>

        <p class="text-center login-text mb-4">

            Selecciona el tipo de cuenta que deseas registrar.

        </p>

        <div class="row">

            <!-- Cliente -->

            <div class="col-12 mb-3">

                <div class="card text-center p-3">

                    <i class="bi bi-person-fill"
                       style="font-size:55px;color:#1E2E4F;"></i>

                    <h4 class="mt-3">

                        Cliente

                    </h4>

                    <p class="text-secondary">

                        Solicita viajes y consulta tu historial.

                    </p>

                    <a href="cliente.php"
                       class="btn btn-login">

                        Registrarme

                    </a>

                </div>

            </div>

            <!-- Chofer -->

            <div class="col-12">

                <div class="card text-center p-3">

                    <i class="bi bi-taxi-front-fill"
                       style="font-size:55px;color:#1E2E4F;"></i>

                    <h4 class="mt-3">

                        Chofer

                    </h4>

                    <p class="text-secondary">

                        Forma parte del equipo Decarrerita.

                    </p>

                    <a href="chofer.php"
                       class="btn btn-login">

                        Registrarme

                    </a>

                </div>

            </div>

        </div>

        <hr class="my-4">

        <p class="text-center mb-0">

            ¿Ya tienes una cuenta?

            <br>

           <a href="../login/login.php">Iniciar sesión</a>

              

            </a>

        </p>

    </div>

</div>

<?php include("../../includes/footer.php"); ?>