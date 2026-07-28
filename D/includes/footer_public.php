<?php
if (!isset($pathToRoot)) {
    $pathToRoot = "";
    for ($i = 0; $i < 5; $i++) {
        if (file_exists($pathToRoot . "assets/css/estilos.css")) {
            break;
        }
        $pathToRoot .= "../";
    }
}
?>
<footer class="footer-deca py-4 mt-5">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-md-7 text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                    <img src="<?php echo $pathToRoot; ?>assets/img/decarrerita_logo.png" alt="Decarrerita Logo" style="height: 65px; width: auto; filter: brightness(0) invert(1);">
                </div>
                <p class="mb-1 text-white-50" style="font-size: 0.85rem;">
                    Decarrerita Transporte Urbano, C.A. &copy; Copyright 2026. Todos los derechos reservados.
                </p>
            </div>

            <div class="col-md-5 d-flex justify-content-center justify-content-md-end">
                <div class="d-flex gap-2">
                    <div class="social-icon-circle"><img src="<?php echo $pathToRoot; ?>assets/img/logo_facebook.png" alt="Facebook"></div>
                    <div class="social-icon-circle"><img src="<?php echo $pathToRoot; ?>assets/img/logo_x.png" alt="X"></div>
                    <div class="social-icon-circle"><img src="<?php echo $pathToRoot; ?>assets/img/logo_instagram.png" alt="Instagram"></div>
                    <div class="social-icon-circle"><img src="<?php echo $pathToRoot; ?>assets/img/logo_linkedin.png" alt="LinkedIn"></div>
                    <div class="social-icon-circle"><img src="<?php echo $pathToRoot; ?>assets/img/logo_youtube.png" alt="YouTube"></div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>