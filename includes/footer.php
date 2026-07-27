<?php
if (!isset($base_path)) {
    $project_root = str_replace('\\', '/', dirname(__DIR__));
    $script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $sub_path = trim(str_replace($project_root, '', $script_file), '/');
    $dir_name = dirname($sub_path);
    $depth = ($dir_name === '.' || $dir_name === '') ? 0 : count(explode('/', $dir_name));
    $base_path = str_repeat('../', $depth);
}
?>
<script src="<?php echo $base_path; ?>assets/js/bootstrap.bundle.min.js"></script>

<script>
const elemFecha = document.getElementById("fecha");
if (elemFecha) {
    const opciones = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    elemFecha.innerHTML = new Date().toLocaleDateString("es-ES", opciones);
}
</script>

</body>
</html>