<?php
session_start();
include("../../includes/header.php");
?>

<div class="container login-container">

    <div class="login-card p-4">

        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="../../assets/img/logo.png"
                 class="login-logo"
                 alt="Logo Decarrerita">
        </div>

        <!-- Título -->
        <h2 class="text-center login-title">
            Bienvenido
        </h2>

        <p class="text-center login-text mb-4">
            Accede a tu cuenta para continuar.
        </p>




<?php if(isset($_GET["registro"]) && $_GET["registro"]=="cliente_ok"){ ?>

<div class="alert alert-success text-center">

    <i class="bi bi-check-circle-fill"></i>

    <strong>
        ¡Cuenta creada correctamente!
    </strong>

    <br>

    Tu registro fue completado exitosamente.

    <br>

    Ya puedes iniciar sesión en Decarrerita.

</div>

<?php } ?>



<?php if(isset($_GET["error"]) && $_GET["error"]=="credenciales"){ ?>

<div class="alert alert-danger text-center">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Los datos ingresados son incorrectos</strong>
</div>

<?php } ?>

<?php if(isset($_GET["estado"]) && $_GET["estado"]=="pendiente"){ ?>

<div class="card p-3 mb-4 border-0" style="background-color: #d1e7dd; color: #0f5132; border-radius: 8px;">
    <h6 class="fw-bold mb-2">Confirmación de solicitud</h6>
    <p class="mb-2" style="font-size: 0.9rem;">Para finalizar su proceso de ingreso o renovación anual deberá entregar al personal administrativo:</p>
    <ul class="mb-2" style="font-size: 0.9rem;">
        <li>Evaluación psicológica.</li>
        <li>Revisión vehicular.</li>
    </ul>
    <p class="mb-2" style="font-size: 0.85rem;">Una vez que los documentos sean validados y las evaluaciones correspondientes sean registradas en el sistema, se procederá con la activación de su cuenta si cumple con todos los requisitos establecidos. El resultado de su solicitud será enviado al correo electrónico registrado.</p>
    <div class="text-center mt-2 pt-2 border-top border-success-subtle">
        <small><i class="bi bi-envelope-fill"></i> Contacto: administracion@gmail.com</small>
    </div>
</div>

<?php } ?>

        <!-- Formulario -->
        <form action="../../procesos/validar_login.php" method="POST">

            <!-- Usuario -->
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        name="usuario"
                        placeholder="Ingrese su usuario"
                        required>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="mb-4">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input
                        type="password"
                        class="form-control"
                        name="password"
                        placeholder="Ingrese su contraseña"
                        required>
                </div>
            </div>

            <!-- Botón -->
            <button
                type="submit"
                class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right"></i>
                Iniciar sesión
            </button>

        </form>

        <!-- Registrarse -->
        <hr class="my-4">
        <p class="text-center mb-0">
            ¿No tienes una cuenta?
            <br>
            <a href="../registro/index.php" class="register-link">
                Registrarse
            </a>
        </p>

    </div>
</div>

<?php include("../../includes/footer.php"); ?>