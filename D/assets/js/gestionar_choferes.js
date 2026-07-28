function estadoPendiente(){


    document.getElementById("estadoChofer").innerHTML = `

        <span class="badge bg-warning fs-6">

            Pendiente

        </span>

    `;


    document.getElementById("motivoRechazo").style.display = "none";


}




function estadoActivo(){


    document.getElementById("estadoChofer").innerHTML = `

        <span class="badge bg-success fs-6">

            Activo

        </span>

    `;


    document.getElementById("motivoRechazo").style.display = "none";


}




function estadoRechazado(motivo){


    document.getElementById("estadoChofer").innerHTML = `

        <span class="badge bg-danger fs-6">

            Rechazado

        </span>

    `;



    document.getElementById("motivoRechazo").style.display = "block";



    document.getElementById("motivoTexto").innerHTML = motivo;


}