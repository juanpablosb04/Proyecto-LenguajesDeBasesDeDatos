<?php
require 'db.php';

function registrarEquipos_gimnasio($nombre, $tipo, $estado, $fecha_compra, $id_gimnasio) {
    try {
        global $pdo;

        $sql = "BEGIN registrar_equipo_gimnasio(:nombre, :tipo, :estado, :fecha_compra, :id_gimnasio); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'tipo' => $tipo,
            'estado' => $estado,
            'fecha_compra' => $fecha_compra,
            'id_gimnasio' => $id_gimnasio
        ]);

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