<?php
require 'db.php';

function registrarEquipos_gimnasio($nombre, $tipo, $estado, $fecha_compra, $id_gimnasio) {
    try {
        global $conn;

        $sql = "BEGIN registrar_equipo_gimnasio(:nombre, :tipo, :estado, TO_DATE(:fecha_compra, 'YYYY-MM-DD'), :id_gimnasio); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':tipo', $tipo);
        oci_bind_by_name($stmt, ':estado', $estado);
        oci_bind_by_name($stmt, ':fecha_compra', $fecha_compra);
        oci_bind_by_name($stmt, ':id_gimnasio', $id_gimnasio);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return ["success" => "Equipo de gimnasio registrado exitosamente"];

    } catch (Exception $e) {
        error_log("Error registrando el equipo: " . $e->getMessage());
        return ["error" => "Error registrando el equipo: " . $e->getMessage()];
    }
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        
        break;

        case 'POST':
            error_log("Datos recibidos: " . print_r($_POST, true));
    
            if (isset($_POST['nombre']) && isset($_POST['tipo']) && isset($_POST['estado']) && isset($_POST['fecha_compra']) && isset($_POST['id_gimnasio'])) {
                $nombre = $_POST['nombre'];
                $tipo = $_POST['tipo'];
                $estado = $_POST['estado'];
                $fecha_compra = $_POST['fecha_compra'];
                $id_gimnasio = $_POST['id_gimnasio'];
    
                $resultado = registrarEquipos_gimnasio($nombre, $tipo, $estado, $fecha_compra, $id_gimnasio);
    
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

}