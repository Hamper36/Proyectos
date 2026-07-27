<?php
$project_root = str_replace('\\', '/', dirname(__DIR__));
$script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
$sub_path = trim(str_replace($project_root, '', $script_file), '/');
$dir_name = dirname($sub_path);
$depth = ($dir_name === '.' || $dir_name === '') ? 0 : count(explode('/', $dir_name));
$base_path = str_repeat('../', $depth);
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Decarrerita</title>

    <!-- Bootstrap Local -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/bootstrap.min.css">

    <!-- Bootstrap Icons Local -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/estilos.css">

</head>

<body>