<?php
require 'db.php';

function registrarMantenimientos_equipos($id_equipo, $fecha_mantenimiento, $descripcion, $estado) {
    try {
        global $conn;

        $sql = "BEGIN registrar_mantenimiento(:id_equipo, TO_DATE(:fecha_mantenimiento, 'YYYY-MM-DD'), :descripcion, :estado); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_equipo', $id_equipo);
        oci_bind_by_name($stmt, ':fecha_mantenimiento', $fecha_mantenimiento);
        oci_bind_by_name($stmt, ':descripcion', $descripcion);
        oci_bind_by_name($stmt, ':estado', $estado);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return ["success" => "Mantenimiento registrado exitosamente"];

    } catch (Exception $e) {
        error_log("Error en registrarMantenimientos_equipos: " . $e->getMessage());
        return ["error" => "Error registrando el mantenimiento: " . $e->getMessage()];
    }
}

function getMantenimientoByID($id_mantenimiento)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_mantenimiento_por_id(:id_mantenimiento); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":id_mantenimiento", $id_mantenimiento);
        oci_bind_by_name($stmt, ":cursor", $cursor, -1, OCI_B_CURSOR);
        
        oci_execute($stmt);

        oci_execute($cursor);
        $result = oci_fetch_assoc($cursor);

        if ($result) {
            return $result;
        } else {
            return null;
        }

    } catch (Exception $e) {
        error_log("Error en getMantenimientoByID: " . $e->getMessage());
        return null;
    }
}

function actualizarMantenimientoEquipo($id_mantenimiento, $id_equipo, $fecha_mantenimiento, $descripcion, $estado) {
    try {
        global $conn;

        $sql = "BEGIN actualizar_mantenimiento(:id_mantenimiento, :id_equipo, TO_DATE(:fecha_mantenimiento, 'YYYY-MM-DD'), :descripcion, :estado); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_mantenimiento', $id_mantenimiento);
        oci_bind_by_name($stmt, ':id_equipo', $id_equipo);
        oci_bind_by_name($stmt, ':fecha_mantenimiento', $fecha_mantenimiento);
        oci_bind_by_name($stmt, ':descripcion', $descripcion);
        oci_bind_by_name($stmt, ':estado', $estado);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error actualizando el mantenimiento: " . $e->getMessage());
        return ["error" => "Error actualizando el mantenimiento: " . $e->getMessage()];
    }
}

function deleteMantenimientoById($id_mantenimiento) {
    try {
        global $conn;

        $sql = "BEGIN eliminar_mantenimiento(:id_mantenimiento); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_mantenimiento', $id_mantenimiento);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error eliminando el mantenimiento: " . $e->getMessage());
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id_mantenimiento'])) {
            $id_mantenimiento = $_GET['id_mantenimiento'];
            $mantenimiento = getMantenimientoByID($id_mantenimiento);

            if ($mantenimiento) {
                echo json_encode($mantenimiento);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Mantenimiento no encontrado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de mantenimiento no proporcionado"]);
        }
        break;

        case 'POST':
            error_log("Datos recibidos: " . print_r($_POST, true));
    
            if (isset($_POST['id_equipo']) && isset($_POST['fecha_mantenimiento']) && isset($_POST['descripcion']) && isset($_POST['estado'])) {
                $id_equipo = $_POST['id_equipo'];
                $fecha_mantenimiento = $_POST['fecha_mantenimiento'];
                $descripcion = $_POST['descripcion'];
                $estado = $_POST['estado'];
    
                $resultado = registrarMantenimientos_equipos($id_equipo, $fecha_mantenimiento, $descripcion, $estado);
    
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

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
        
            if (isset($data['id_mantenimiento'], $data['id_equipo'], $data['fecha_mantenimiento'], 
                     $data['descripcion'], $data['estado'])) {
                $id_mantenimiento = $data['id_mantenimiento'];
                $id_equipo = $data['id_equipo'];
                $fecha_mantenimiento = $data['fecha_mantenimiento'];
                $descripcion = $data['descripcion'];
                $estado = $data['estado'];
        
                $resultado = actualizarMantenimientoEquipo($id_mantenimiento, $id_equipo, 
                              $fecha_mantenimiento, $descripcion, $estado);
        
                if (!isset($resultado['error'])) {
                    echo json_encode(["success" => "Mantenimiento actualizado exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode($resultado);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Todos los campos son requeridos"]);
            }
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
    
            if (isset($data['id_mantenimiento'])) {
                $id_mantenimiento = $data['id_mantenimiento'];
    
                if (deleteMantenimientoById($id_mantenimiento)) {
                    echo json_encode(["success" => "Mantenimiento eliminado exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error eliminando el mantenimiento"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "ID de mantenimiento no proporcionado"]);
            }
            break;

}