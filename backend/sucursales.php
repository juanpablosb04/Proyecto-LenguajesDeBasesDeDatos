<?php
require 'db.php';

function registrarSucursal($nombre_sucursal, $direccion, $telefono, $ciudad) {
    global $pdo;

    try {
        $sql = "INSERT INTO sucursales (id_gimnasio, nombre_sucursal, direccion, telefono, ciudad)
                VALUES (sucursales_seq.NEXTVAL, :nombre_sucursal, :direccion, :telefono, :ciudad)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre_sucursal' => $nombre_sucursal,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'ciudad' => $ciudad
        ]);

        return ["success" => "Sucursal registrada exitosamente"];

    } catch (Exception $e) {
        error_log("Error registrando la sucursal: " . $e->getMessage());
        return ["error" => "Error registrando la sucursal: " . $e->getMessage()];
    }
}

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'POST':
        error_log("Datos recibidos: " . print_r($_POST, true));

        if (isset($_POST['nombre_sucursal']) && isset($_POST['direccion']) && isset($_POST['telefono']) && isset($_POST['ciudad'])) {
            $nombre_sucursal = $_POST['nombre_sucursal'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $ciudad = $_POST['ciudad'];

            $resultado = registrarSucursal($nombre_sucursal, $direccion, $telefono, $ciudad);

            if (isset($resultado['success'])) {
                echo json_encode($resultado);
            } else {
                http_response_code(400);
                echo json_encode($resultado);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}