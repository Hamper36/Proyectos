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

<?php if(isset($_GET["registro"]) && $_GET["registro"]=="ok"){ ?>

<div class="alert alert-success text-center">
    <i class="bi bi-check-circle-fill"></i>
    <strong>¡Solicitud enviada correctamente!</strong>
    <br>
    Su solicitud fue enviada con éxito.
    Espere la aprobación del personal administrativo para activar su cuenta.
</div>

<?php } ?>

<?php if(isset($_GET["estado"]) && $_GET["estado"]=="pendiente"){ ?>

<div class="alert alert-danger text-center">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Cuenta no activada</strong>
    <br>
    Su cuenta aún no ha sido activada por el personal administrativo.
</div>

<?php } ?>

<?php if(isset($_GET["error"]) && $_GET["error"]=="credenciales"){ ?>

<div class="alert alert-danger text-center">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Usuario o contraseña incorrectos</strong>
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