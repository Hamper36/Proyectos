<?php
include("includes/header.php");
?>

<div class="container mt-5">
    <section class="hero-section p-4 p-lg-5 mb-5 rounded-4 shadow-sm bg-white">
        <div class="row align-items-center gy-4">

    
            <div class="col-lg-6">
                <span class="badge-deca mb-3">Bienvenido a Decarrerita</span>
                <h1 class="display-5 fw-bold">Tu sistema de transporte urbano eficiente y seguro</h1>
                <p class="lead text-muted">Solicita conductores, vehículos y traslados en una sola plataforma con estadísticas claras y acceso rápido desde cualquier dispositivo.</p>
                <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                    <a href="vistas/login/login.php" class="btn btn-primary btn-lg">Ingresar al sistema</a>
                    
                </div>
                <div class="row g-3 mt-4">
                    <div class="col-6">
                        <div class="info-box p-3 rounded-4 bg-light">
                            <h5 class="mb-1">+1200</h5>
                            <small>Conductores activos</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box p-3 rounded-4 bg-light">
                            <h5 class="mb-1">+3500</h5>
                            <small>Viajes registrados</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image p-4 rounded-4 bg-gradient position-relative overflow-hidden">
                    <div id="driverCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#driverCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#driverCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        </div>
                        <div class="carousel-inner rounded-3 shadow-sm">
                            <div class="carousel-item active">
                                <img src="assets/img/conductora.png" class="d-block w-100" alt="Conductora Decarrerita" style="height: 380px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/img/conductor.png" class="d-block w-100" alt="Conductor Decarrerita" style="height: 380px; object-fit: cover;">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#driverCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#driverCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="mb-5">
        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <div class="card feature-card h-100 p-4 text-center">
                    <img src="assets/img/usuarios.png" class="mx-auto mb-3" width="65" alt="Conductores">
                    <h5>Gestión de conductores</h5>
                    <p class="text-muted">Control completo de perfiles, permisos y horarios de tu equipo.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card feature-card h-100 p-4 text-center">
                    <img src="assets/img/vehiculos.png" class="mx-auto mb-3" width="65" alt="Vehículos">
                    <h5>Flota de vehículos</h5>
                    <p class="text-muted">Monitorea estado, mantenimiento y disponibilidad en tiempo real.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card feature-card h-100 p-4 text-center">
                    <img src="assets/img/traslados.png" class="mx-auto mb-3" width="65" alt="Traslados">
                    <h5>Control de traslados</h5>
                    <p class="text-muted">Registra y organiza cada viaje con un flujo rápido y claro.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card feature-card h-100 p-4 text-center">
                    <img src="assets/img/ganancias.png" class="mx-auto mb-3" width="65" alt="Reportes">
                    <h5>Reportes y métricas</h5>
                    <p class="text-muted">Visualiza estadísticas clave para tomar decisiones inteligentes.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const myCarousel = document.querySelector('#driverCarousel');
    if (myCarousel && typeof bootstrap !== 'undefined') {
        new bootstrap.Carousel(myCarousel, {
            interval: 4000,
            ride: 'carousel',
            wrap: true
        });
    }
});
</script>

<?php
include("includes/footer.php");
?>
