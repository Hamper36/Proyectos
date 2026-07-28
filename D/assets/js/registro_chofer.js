//====================================
// VARIABLES
//====================================

let currentStep = 1;

const totalSteps = 5;

let contactosActuales = 2;


//====================================
// MOSTRAR PASO
//====================================

function showStep(step) {


    // Ocultar todas las páginas

    document.querySelectorAll(".wizard-page").forEach(function(page) {

        page.classList.add("d-none");

    });


    // Mostrar página actual

    const pagina = document.getElementById("step" + step);


    if(pagina){

        pagina.classList.remove("d-none");

    }


    actualizarWizard(step);

}



//====================================
// SIGUIENTE PASO
//====================================

async function validarCampoAJAX(campo, valor) {
    try {
        const formData = new FormData();
        formData.append("campo", campo);
        formData.append("valor", valor);
        const resp = await fetch("../../procesos/verificar_disponibilidad.php", {
            method: "POST",
            body: formData
        });
        const res = await resp.json();
        if (res.existe) {
            alert(res.mensaje);
            return false;
        }
    } catch(e) {
        console.error(e);
    }
    return true;
}

async function nextStep(step) {

    // Validar el paso actual antes de avanzar
    const campos = document.querySelectorAll(
        "#step" + currentStep + " input"
    );

    for(let campo of campos){
        if(!campo.value.trim()){
            campo.classList.add("is-invalid");
            alert("Debe completar todos los campos antes de continuar.");
            campo.focus();
            return;
        } else {
            campo.classList.remove("is-invalid");
        }
    }

    // Validación de formato de campos según el paso actual
    if(currentStep === 1){
        const cedulaField = document.querySelector('[name="cedula"]');
        if(cedulaField && !/^\d{7,8}$/.test(cedulaField.value.trim())){
            cedulaField.classList.add("is-invalid");
            alert("La cédula debe contener entre 7 y 8 números.");
            cedulaField.focus();
            return;
        }

        const telefonoField = document.querySelector('[name="telefono"]');
        if(telefonoField && !/^\d{11}$/.test(telefonoField.value.trim())){
            telefonoField.classList.add("is-invalid");
            alert("El teléfono debe contener exactamente 11 números.");
            telefonoField.focus();
            return;
        }

        if(cedulaField){
            const ok = await validarCampoAJAX("cedula", cedulaField.value.trim());
            if(!ok){ cedulaField.classList.add("is-invalid"); cedulaField.focus(); return; }
        }
        if(telefonoField){
            const ok = await validarCampoAJAX("telefono", telefonoField.value.trim());
            if(!ok){ telefonoField.classList.add("is-invalid"); telefonoField.focus(); return; }
        }
        const correoField = document.querySelector('[name="correo"]');
        if(correoField){
            const ok = await validarCampoAJAX("correo", correoField.value.trim());
            if(!ok){ correoField.classList.add("is-invalid"); correoField.focus(); return; }
        }
    }

    // Validación especial de contraseña y usuario
    if(currentStep === 2){
        const usrField = document.querySelector('[name="nombre_usuario"]');
        if(usrField){
            const ok = await validarCampoAJAX("nombre_usuario", usrField.value.trim());
            if(!ok){ usrField.classList.add("is-invalid"); usrField.focus(); return; }
        }

        const password = document.querySelector('[name="password"]').value;
        const confirmar = document.querySelector('[name="confirmar_password"]').value;

        if(password.length < 4){
            alert("Su contraseña debe ser de 4 caracteres o más.");
            return;
        }

        if(password !== confirmar){
            alert("Las contraseñas no coinciden.");
            return;
        }
    }

    if(currentStep === 4){
        const placaField = document.querySelector('[name="placa"]');
        if(placaField && !/^[a-zA-Z0-9]{1,7}$/.test(placaField.value.trim())){
            placaField.classList.add("is-invalid");
            alert("La placa debe contener hasta 7 caracteres (números y letras).");
            placaField.focus();
            return;
        }

        if(placaField){
            const ok = await validarCampoAJAX("placa", placaField.value.trim());
            if(!ok){ placaField.classList.add("is-invalid"); placaField.focus(); return; }
        }
    }

    if(currentStep === 5){
        const contactoTelefonos = document.querySelectorAll('[name="contacto_telefono[]"]');
        for(let tel of contactoTelefonos){
            if(!/^\d{11}$/.test(tel.value.trim())){
                tel.classList.add("is-invalid");
                alert("Cada teléfono de contacto debe contener exactamente 11 números.");
                tel.focus();
                return;
            }
        }
    }

    // Si todo está correcto, avanzar
    currentStep = step;
    showStep(step);
}

//====================================
// PASO ANTERIOR
//====================================

function previousStep(step) {

    currentStep = step;

    showStep(step);

}


//====================================
// ACTUALIZAR WIZARD
//====================================

function actualizarWizard(step) {


    const pasos = document.querySelectorAll(".wizard-step");


    pasos.forEach(function(item, index) {


        item.classList.remove("active");

        item.classList.remove("completed");


        const numero = index + 1;


        const circulo = item.querySelector(".wizard-circle");


        if(numero < step) {


            item.classList.add("completed");


            if(circulo){

                circulo.innerHTML = "✔";

            }


        } else {


            if(circulo){

                circulo.innerHTML = numero;

            }

        }


        if(numero === step) {


            item.classList.add("active");


        }


    });



    // Actualizar barra de progreso


    const progreso = ((step - 1) / (totalSteps - 1)) * 100;


    const barra = document.getElementById("progressBar");


    if(barra){

        barra.style.width = progreso + "%";

    }


}



//====================================
// INICIO DE LA PÁGINA
//====================================

document.addEventListener("DOMContentLoaded", function() {


    // Mostrar primer paso

    showStep(1);



    // Activar botón agregar contacto


    const botonContacto = document.getElementById("btnAgregarContacto");


    if(botonContacto){


        botonContacto.addEventListener(
            "click",
            agregarContacto
        );


    }


});



//====================================
// AGREGAR CONTACTOS DINÁMICOS
//====================================

function agregarContacto() {


    // Máximo 4 contactos

    if(contactosActuales >= 4) {


        return;

    }



    contactosActuales++;



    const contenedor = document.getElementById("contactosExtra");



    if(!contenedor){

        return;

    }



    const nuevoContacto = document.createElement("div");



    nuevoContacto.className =
        "border rounded p-3 mb-3";



    nuevoContacto.innerHTML = `


        <h6 class="text-primary">

            Contacto de Emergencia #${contactosActuales}

        </h6>



        <div class="row">


            <div class="col-md-4 mb-3">


                <label class="form-label">

                    Nombre

                </label>


                <input 
                    type="text"
                    name="contacto_nombre[]"
                    class="form-control"
                    required>


            </div>




            <div class="col-md-4 mb-3">


                <label class="form-label">

                    Teléfono

                </label>


                <input
                    type="text"
                    name="contacto_telefono[]"
                    class="form-control"
                    maxlength="11"
                    pattern="[0-9]{11}"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)"
                    required>


            </div>




            <div class="col-md-4 mb-3">


                <label class="form-label">

                    Parentesco

                </label>


                <input
                    type="text"
                    name="contacto_parentesco[]"
                    class="form-control"
                    required>


            </div>



        </div>


    `;



    contenedor.appendChild(nuevoContacto);




    // Ocultar botón al llegar a 4


    if(contactosActuales >= 4) {


        const boton =
            document.getElementById("btnAgregarContacto");


        if(boton){

            boton.style.display = "none";

        }


    }


}