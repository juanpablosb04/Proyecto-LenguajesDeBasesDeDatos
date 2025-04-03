<?php
require 'db.php';

function registrarSucursal($nombre_sucursal, $direccion, $telefono, $ciudad) {
    global $conn;

    try {
        $sql = "BEGIN registrar_sucursal(:nombre_sucursal, :direccion, :telefono, :ciudad); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':nombre_sucursal', $nombre_sucursal);
        oci_bind_by_name($stmt, ':direccion', $direccion);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_bind_by_name($stmt, ':ciudad', $ciudad);

        oci_execute($stmt);
        oci_free_statement($stmt);

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