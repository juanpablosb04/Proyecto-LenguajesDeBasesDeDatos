<?php
require 'db.php';

function getEquipos()
{
    try {
        global $pdo;

        $sql = "SELECT id_equipo, nombre, tipo, estado, fecha_compra, id_gimnasio FROM equipos_gimnasio WHERE id_gimnasio = 2";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ["error" => "Error al obtener productos"];
    }
}

function getEquipoByID($id_equipo)
{
    try {
        global $pdo;

        $sql = "SELECT * FROM equipos_gimnasio WHERE id_equipo = :id_equipo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_equipo' => $id_equipo]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        return null;
    }
}

function updateEquipo($id_equipo, $nombre, $tipo, $estado, $fecha_compra, $id_gimnasio) {
    try {
        global $pdo;

        $sql = "BEGIN actualizar_equipo(:id_equipo, :nombre, :tipo, :estado, :fecha_compra, :id_gimnasio); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_equipo' => $id_equipo,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'estado' => $estado,
            'fecha_compra' => $fecha_compra,
            'id_gimnasio' => $id_gimnasio
        ]);

        return true;

    } catch (Exception $e) {
        error_log("Error actualizando el equipo: " . $e->getMessage());
        return false;
    }
}

function deleteEquipoByID($id_equipo) {
    try {
        global $pdo;

        $sql = "BEGIN eliminar_equipo(:id_equipo); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_equipo' => $id_equipo]);

        return $stmt->rowCount() > 0;

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