<?php
include("../config/conexion.php");

header("Content-Type: application/json; charset=utf-8");

$campo = isset($_POST["campo"]) ? trim($_POST["campo"]) : "";
$valor = isset($_POST["valor"]) ? trim($_POST["valor"]) : "";

$respuesta = ["existe" => false, "mensaje" => ""];

if ($campo != "" && $valor != "") {
    if (in_array($campo, ["nombre_usuario", "correo", "cedula", "telefono"])) {
        $sql = "SELECT id_usuario FROM usuario WHERE $campo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $valor);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $labels = [
                "nombre_usuario" => "El nombre de usuario",
                "correo" => "El correo electrónico",
                "cedula" => "La cédula",
                "telefono" => "El teléfono"
            ];
            $lbl = isset($labels[$campo]) ? $labels[$campo] : $campo;
            $respuesta["existe"] = true;
            $respuesta["mensaje"] = "$lbl ya se encuentra registrado por otro usuario.";
        }
    } elseif ($campo == "placa") {
        $sql = "SELECT id_vehiculo FROM vehiculo WHERE placa = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $valor);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $respuesta["existe"] = true;
            $respuesta["mensaje"] = "Esa placa ya se encuentra registrada en el sistema.";
        }
    }
}

echo json_encode($respuesta);
exit();
?>
