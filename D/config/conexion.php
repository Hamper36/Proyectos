<?php

$host = "localhost";
$usuario = "root";
$password = "";
$bd = "decarrerita";

$conexion = mysqli_connect($host, $usuario, $password, $bd);

if (!$conexion) {

    die("❌ Error de conexión: " . mysqli_connect_error());

}

mysqli_set_charset($conexion, "utf8mb4");

/*=========================================================
VERIFICACIÓN AUTOMÁTICA DE REVISIONES VENCIDAS
(se ejecuta en cada carga de página, ya que el sistema
no cuenta con tareas programadas/cron)

Si un vehículo está "Apto" pero su última revisión_vehiculo
tiene más de 1 año, vuelve a estado "Pendiente" para que
reingrese automáticamente a la cola de revisiones de
personal administrativo (vistas/personal/revisiones.php).
===========================================================*/

$sql_vencimiento = "UPDATE vehiculo v
    INNER JOIN (
        SELECT id_vehiculo, MAX(fecha_revision) AS ultima_revision
        FROM revision_vehiculo
        GROUP BY id_vehiculo
    ) r ON r.id_vehiculo = v.id_vehiculo
    INNER JOIN chofer c ON c.id_chofer = v.id_chofer
    INNER JOIN usuario u ON u.id_usuario = c.id_usuario
    SET v.estado_vehiculo = 'Pendiente', u.estado_usuario = 'Pendiente'
    WHERE v.estado_vehiculo = 'Apto'
    AND r.ultima_revision <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";

mysqli_query($conexion, $sql_vencimiento);

?>