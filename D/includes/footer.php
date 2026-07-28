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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>