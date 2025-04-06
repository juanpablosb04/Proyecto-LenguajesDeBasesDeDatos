<?php
require 'db.php';


function getEquipos()
{
    global $conn;

    try {
        $sql = "BEGIN :cursor := obtener_equipos_G1(); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        oci_bind_by_name($stmt, ':cursor', $cursor, -1, OCI_B_CURSOR);

        oci_execute($stmt);
        oci_execute($cursor);

        $clases = [];
        while (($row = oci_fetch_assoc($cursor)) !== false) {
            $clases[] = $row;
        }

        oci_free_statement($stmt);
        oci_free_statement($cursor);

        return $clases;

    } catch (Exception $e) {
        error_log("Error al obtener equipos: " . $e->getMessage());
        return [];
    }
}


function getEquipoByID($id_equipo)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_equipos_por_id(:id_equipo); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":id_equipo", $id_equipo);
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
        error_log("Error en getEquipoByID: " . $e->getMessage());
        return null;
    }
}

function updateEquipo($id_equipo, $nombre, $tipo, $estado, $fecha_compra, $id_gimnasio) {
    try {
        global $conn;

        $sql = "BEGIN actualizar_equipo(:id_equipo, :nombre, :tipo, :estado, TO_DATE(:fecha_compra, 'YYYY-MM-DD'), :id_gimnasio); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_equipo', $id_equipo);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':tipo', $tipo);
        oci_bind_by_name($stmt, ':estado', $estado);
        oci_bind_by_name($stmt, ':fecha_compra', $fecha_compra);
        oci_bind_by_name($stmt, ':id_gimnasio', $id_gimnasio);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error actualizando el equipo: " . $e->getMessage());
        return false;
    }
}

function deleteEquipoByID($id_equipo) {
    try {
        global $conn;

        $sql = "BEGIN eliminar_equipo(:id_equipo); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_equipo', $id_equipo);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error eliminando el equipo: " . $e->getMessage());
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];


switch ($method) {
    case 'GET':
        if (isset($_GET['id_equipo'])) {
            $id_equipo = $_GET['id_equipo'];
            $equipo = getEquipoByID($id_equipo);

            if ($equipo) {
                echo json_encode($equipo);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Equipo no encontrado"]);
            }
        } else {
            $equipos = getEquipos();
            echo json_encode($equipos);
        }
        break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
        
            if (isset($data['id_equipo']) && isset($data['nombre']) && isset($data['tipo']) && isset($data['estado']) && isset($data['fecha_compra']) && isset($data['id_gimnasio'])) {
                $id_equipo = $data['id_equipo'];
                $nombre = $data['nombre'];
                $tipo = $data['tipo'];
                $estado = $data['estado'];
                $fecha_compra = $data['fecha_compra'];
                $id_gimnasio = $data['id_gimnasio'];
        
                if (updateEquipo($id_equipo, $nombre, $tipo, $estado, $fecha_compra, $id_gimnasio)) {
                    echo json_encode(["success" => "Equipo actualizado exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error actualizando el equipo"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Todos los campos son requeridos"]);
            }
            break;
        
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
        
            if (isset($data['id_equipo'])) {
                $id_equipo = $data['id_equipo'];
        
                if (deleteEquipoByID($id_equipo)) {
                    echo json_encode(["success" => "Equipo eliminado exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error eliminando el equipo"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "ID del equipo no proporcionado"]);
            }
            break;
    }