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

            Registro de Cliente

        </h2>

        <p class="text-center login-text mb-4">

            Completa el formulario para crear tu cuenta.

        </p>

        <form action="../../procesos/registrar_cliente.php" method="POST">

            <!-- Nombre -->

            <div class="mb-3">

                <label class="form-label">Nombre</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-person-fill"></i>

                    </span>

                    <input type="text"
                           name="nombre"
                           class="form-control"
                           placeholder="Ingrese su nombre"
                           required>

                </div>

            </div>

            <!-- Apellido -->

            <div class="mb-3">

                <label class="form-label">Apellido</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-person-fill"></i>

                    </span>

                    <input type="text"
                           name="apellido"
                           class="form-control"
                           placeholder="Ingrese su apellido"
                           required>

                </div>

            </div>

            <!-- Cédula -->

            <div class="mb-3">

                <label class="form-label">Cédula</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-person-vcard-fill"></i>

                    </span>

                    <input type="text"
                           name="cedula"
                           class="form-control"
                           placeholder="Ej: 12345678"
                           maxlength="8"
                           minlength="7"
                           pattern="[0-9]{7,8}"
                           inputmode="numeric"
                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 8)"
                           required>

                </div>

            </div>

            <!-- Teléfono -->

            <div class="mb-3">

                <label class="form-label">Teléfono</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-telephone-fill"></i>

                    </span>

                    <input type="tel"
                           name="telefono"
                           class="form-control"
                           placeholder="Ej: 04121234567"
                           maxlength="11"
                           pattern="[0-9]{11}"
                           inputmode="numeric"
                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)"
                           required>

                </div>

            </div>

<!-- Dirección -->

<div class="mb-3">

    <label class="form-label">

        Dirección

    </label>

    <div class="input-group">

        <span class="input-group-text">

            <i class="bi bi-geo-alt-fill"></i>

        </span>

        <input
            type="text"
            name="direccion"
            class="form-control"
            placeholder="Ingrese su dirección"
            required>

    </div>

</div>

            <!-- Correo -->

            <div class="mb-3">

                <label class="form-label">Correo Electrónico</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-envelope-fill"></i>

                    </span>

                    <input type="email"
                           name="correo"
                           class="form-control"
                           placeholder="correo@ejemplo.com"
                           required>

                </div>

            </div>

            <!-- Usuario -->

            <div class="mb-3">

                <label class="form-label">Usuario</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-person-circle"></i>

                    </span>

                    <input type="text"
                           name="usuario"
                           class="form-control"
                           placeholder="Nombre de usuario"
                           required>

                </div>

            </div>

            <!-- Contraseña -->

            <div class="mb-3">

                <label class="form-label">Contraseña</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-lock-fill"></i>

                    </span>

                    <input type="password"
                           name="password"
                           id="cliente_password"
                           class="form-control"
                           placeholder="Ingrese una contraseña"
                           minlength="4"
                           required>

                </div>

            </div>

            <!-- Confirmar -->

            <div class="mb-4">

                <label class="form-label">Confirmar Contraseña</label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-shield-lock-fill"></i>

                    </span>

                    <input type="password"
                           name="confirmar_password"
                           class="form-control"
                           placeholder="Repita la contraseña"
                           required>

                </div>

            </div>

            <button type="submit"
                    class="btn btn-login w-100">

                <i class="bi bi-person-plus-fill"></i>

                Registrarme

            </button>

        </form>

        <hr class="my-4">

        <p class="text-center mb-0">

    ¿Ya tienes una cuenta?

    <br>

    <a href="../login/login.php">Iniciar sesión</a>

</p>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    if(form){
        form.addEventListener("submit", async function(e) {
            e.preventDefault();
            const pass = document.getElementById("cliente_password");
            if(pass && pass.value.length < 4){
                alert("Su contraseña debe ser de 4 caracteres o más");
                pass.focus();
                return;
            }

            const camposValidar = [
                { name: "cedula", label: "cedula" },
                { name: "telefono", label: "telefono" },
                { name: "correo", label: "correo" },
                { name: "usuario", label: "nombre_usuario" }
            ];

            for (let item of camposValidar) {
                const el = form.querySelector(`[name="${item.name}"]`);
                if (el && el.value.trim() !== "") {
                    const formData = new FormData();
                    formData.append("campo", item.label);
                    formData.append("valor", el.value.trim());
                    try {
                        const resp = await fetch("../../procesos/verificar_disponibilidad.php", {
                            method: "POST",
                            body: formData
                        });
                        const res = await resp.json();
                        if (res.existe) {
                            alert(res.mensaje);
                            el.focus();
                            return;
                        }
                    } catch(err) {
                        console.error(err);
                    }
                }
            }

            form.submit();
        });
    }
});
</script>

<?php include("../../includes/footer.php"); ?>