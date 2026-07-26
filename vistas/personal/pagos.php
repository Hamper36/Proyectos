<?php
include("../../includes/header.php");
?>

<div class="container-fluid">

    <div class="row">

        <?php
        include("../../includes/sidebar_personal.php");
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

                    Gestión de Pagos

                </h2>

                <p>

                    Registre pagos realizados a los choferes y consulte el historial de pagos efectuados.

                </p>

            </div>

            <!-- FORMULARIO -->

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card card-money">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                      <h5 class="mb-0 fw-bold text-white">


    <i class="bi bi-cash-stack me-2"></i>

    Registrar pago al chofer

</h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Chofer

                                    </label>

                                    <select class="form-select">

                                        <option>Seleccione un chofer</option>

                                        <option>José Pérez</option>

                                        <option>Ana Gómez</option>

                                        <option>Carlos Ruiz</option>

                                    </select>

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Fecha

                                    </label>

                                    <input type="date"
                                           class="form-control">

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="form-label">

                                        Referencia

                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           placeholder="N° Referencia">

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        Monto a pagar

                                    </label>

                                    <input type="number"
                                           class="form-control"
                                           placeholder="$0.00">

                                </div>

                                <div class="col-md-8 mb-3">

                                    <label class="form-label">

                                        Observación

                                    </label>

                                    <input type="text"
                                           class="form-control"
                                           placeholder="Observación del pago">

                                </div>

                            </div>

                            <div class="text-end">

                                <button class="btn btn-decarrerita">

                                    <i class="bi bi-cash-stack"></i>

                                    Registrar pago

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

                  <!-- HISTORIAL DE PAGOS -->

            <div class="row justify-content-center mt-4">

                <div class="col-lg-11">

                    <div class="card">

                        <div class="card-header text-white"
                             style="background:#1E2E4F;">

                       <h5 class="mb-0 fw-bold text-white">


    <i class="bi bi-clock-history me-2"></i>

    Historial de pagos

</h5>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th>Chofer</th>

                                            <th>Fecha</th>

                                            <th>Referencia</th>

                                            <th>Monto</th>

                                            <th>Estado</th>

                                            <th class="text-center">

                                                Acción

                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>José Pérez</td>

                                            <td>18/07/2026</td>

                                            <td>TRX-584721</td>

                                            <td>$185.00</td>

                                            <td>

                                                <span class="badge bg-success">

                                                    Pagado

                                                </span>

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Ver

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>Ana Gómez</td>

                                            <td>15/07/2026</td>

                                            <td>TRX-584530</td>

                                            <td>$220.00</td>

                                            <td>

                                                <span class="badge bg-success">

                                                    Pagado

                                                </span>

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Ver

                                                </button>

                                            </td>

                                        </tr>

                                        <tr>

                                            <td>Carlos Ruiz</td>

                                            <td>12/07/2026</td>

                                            <td>TRX-584320</td>

                                            <td>$96.00</td>

                                            <td>

                                                <span class="badge bg-warning text-dark">

                                                    Pendiente

                                                </span>

                                            </td>

                                            <td class="text-center">

                                                <button class="btn btn-sm btn-outline-primary">

                                                    <i class="bi bi-eye"></i>

                                                    Ver

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

<?php
include("../../includes/footer.php");
?>

