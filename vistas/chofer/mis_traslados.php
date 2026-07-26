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

                    Mis Traslados

                </h2>

                <p>

                    Consulte los traslados que actualmente tiene asignados.

                </p>

            </div>



            <!-- RESUMEN DE TRASLADOS -->

            <div class="row justify-content-center mt-4">

              <div class="col-lg-11">

    <div class="row">

        <div class="col-md-4 mb-3 mx-auto">
                            
                    
                    <div class="card card-ganancias text-center">


                                <div class="card-body">


                                    <i class="bi bi-cash-stack"
                                       style="font-size:45px;color:#198754;"></i>



                                    <h3 class="mt-3">

                                        $48.50

                                    </h3>



                                    <h5>

                                        Ganancia del día

                                    </h5>



                                    <p class="text-secondary mb-0">

                                        Acumulada

                                    </p>



                                </div>


                            </div>


                        </div>



                    </div>


                </div>


            </div>




            <!-- LISTADO DE TRASLADOS -->


            <div class="row justify-content-center mt-4">


                <div class="col-lg-11">


                    <div class="card">


                        <div class="card-header text-white"
                             style="background:#1E2E4F; padding:18px;">


                            <h5 class="mb-0 fw-bold text-white">


                                <i class="bi bi-car-front-fill me-2"></i>


                                Mis traslados asignados


                            </h5>


                        </div>


                        <div class="card-body">


                            <div class="table-responsive">


                                <table class="table table-hover align-middle">


                                    <thead class="table-light">


                                        <tr>


                                            <th>Fecha</th>

                                            <th>Cliente</th>

                                            <th>Origen</th>

                                            <th>Destino</th>

                                            <th>Costo</th>

                                            <th>Estado</th>

                                        <th class="text-center">

                                                Acciones

                                                </th>

                                        </tr>


                                    </thead>

    <tbody>

<!-- TRASLADO PENDIENTE -->

<tr>

    <td>21/07/2026</td>

    <td>María González</td>

    <td>Centro</td>

    <td>Hospital Central</td>

    <td>$12.50</td>

    <td>

        <span class="badge bg-warning text-dark">

            Pendiente

        </span>

    </td>

    <td class="text-center text-nowrap">

        <button
            class="btn btn-success btn-sm me-1"
            onclick="return confirm('¿Desea aceptar este traslado?');">

            <i class="bi bi-check-circle"></i>

            Aceptar

        </button>

        <button class="btn btn-outline-primary btn-sm">

            <i class="bi bi-eye"></i>

            Detalle

        </button>

    </td>

</tr>


<!-- TRASLADO EN CURSO -->

<tr>

    <td>21/07/2026</td>

    <td>Carlos Rodríguez</td>

    <td>Terminal</td>

    <td>Universidad</td>

    <td>$8.00</td>

    <td>

        <span class="badge bg-primary">

            En curso

        </span>

    </td>

    <td class="text-center text-nowrap">

        <button
            class="btn btn-danger btn-sm me-1"
            onclick="return confirm('¿Está seguro de finalizar este traslado?');">

            <i class="bi bi-flag-fill"></i>

            Finalizar

        </button>

        <button class="btn btn-outline-primary btn-sm">

            <i class="bi bi-eye"></i>

            Detalle

        </button>

    </td>

</tr>


<!-- TRASLADO FINALIZADO -->

<tr>

    <td>20/07/2026</td>

    <td>Ana Martínez</td>

    <td>Las Cocuizas</td>

    <td>Centro Comercial</td>

    <td>$10.75</td>

    <td>

        <span class="badge bg-success">

            Finalizado

        </span>

    </td>

    <td class="text-center text-nowrap">

        <button
            class="btn btn-outline-secondary btn-sm"
            disabled>

            <i class="bi bi-check2-all"></i>

            Completado

        </button>

        <button class="btn btn-outline-primary btn-sm">

            <i class="bi bi-eye"></i>

            Detalle

        </button>

    </td>

</tr>


<!-- TRASLADO CANCELADO -->

<tr>

    <td>19/07/2026</td>

    <td>Pedro López</td>

    <td>La Floresta</td>

    <td>Aeropuerto</td>

    <td>$18.30</td>

    <td>

        <span class="badge bg-danger">

            Cancelado

        </span>

    </td>

    <td class="text-center text-nowrap">

        <button
            class="btn btn-outline-secondary btn-sm"
            disabled>

            <i class="bi bi-x-circle"></i>

            Cancelado

        </button>

        <button class="btn btn-outline-primary btn-sm">

            <i class="bi bi-eye"></i>

            Detalle

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


</div>


