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

function nextStep(step) {


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