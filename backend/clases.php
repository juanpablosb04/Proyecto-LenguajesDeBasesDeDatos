<?php
require 'db.php';

function registrarClase($nombre_clase, $descripcion, $id_instructor)
{
    try {
        global $pdo;

        $sql = "BEGIN registrar_clase(:nombre_clase, :descripcion, :id_instructor); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre_clase' => $nombre_clase,
            'descripcion' => $descripcion,
            'id_instructor' => $id_instructor
        ]);

        return true;

    } catch (Exception $e) {
        error_log("Error registrando la clase: " . $e->getMessage());
        return "Error registrando la clase: " . $e->getMessage();
    }
}

function getClaseById($id)
{
    try {
        global $pdo;

        $sql = "SELECT id_clase, nombre_clase, descripcion, id_instructor FROM clases WHERE id_clase = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Error al obtener clase: " . $e->getMessage());
        return null;
    }
}

function getClases()
{
    try {
        global $pdo;

        $sql = "SELECT id_clase, nombre_clase, descripcion, id_instructor FROM clases";
        $stmt = $pdo->query($sql);
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $clases;

    } catch (Exception $e) {
        error_log("Error al obtener instructores: " . $e->getMessage());
        return [];
    }
}

function updateClase($id, $nombre_clase, $descripcion, $id_instructor)
{
    try {
        global $pdo;

        $sql = "BEGIN actualizar_clase(:id, :nombre_clase, :descripcion, :id_instructor); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nombre_clase' => $nombre_clase,
            'descripcion' => $descripcion,
            'id_instructor' => $id_instructor
        ]);

        return true;

    } catch (Exception $e) {
        error_log("Error al actualizar clase: " . $e->getMessage());
        return false;
    }
}

function deleteClaseById($id_clase)
{
    try {
        global $pdo;

        $sql = "BEGIN eliminar_clase(:id_clase); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_clase' => $id_clase]);

        return $stmt->rowCount() > 0;

    } catch (Exception $e) {
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

        if (isset($_GET['id'])) {
            $nombre_clase = getClaseById($_GET['id']);
            echo json_encode($nombre_clase);
        } else {
            $clases = getClases();
            echo json_encode($clases);
        }

        break;

    case 'POST':
        error_log("Datos recibidos: " . print_r($_POST, true));

        if (isset($_POST['nombre_clase']) && isset($_POST['descripcion']) && isset($_POST['id_instructor'])) {
            $nombre_clase = $_POST['nombre_clase'];
            $descripcion = $_POST['descripcion'];
            $id_instructor = $_POST['id_instructor'];

            $resultado = registrarClase($nombre_clase, $descripcion, $id_instructor);

            if ($resultado === true) {
                echo json_encode(["success" => "Clase registrada exitosamente"]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => $resultado]);
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

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_clase']) && isset($data['nombre_clase']) && isset($data['descripcion']) && isset($data['id_instructor'])) {
            $id_clase = $data['id_clase'];
            $nombre_clase = $data['nombre_clase'];
            $descripcion = $data['descripcion'];
            $id_instructor = $data['id_instructor'];

            if (updateClase($id_clase, $nombre_clase, $descripcion, $id_instructor)) {
                echo json_encode(["success" => "Clase actualizada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando la clase"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;
        ;

    case 'DELETE':

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id_clase'])) {
            $id_clase = $data['id_clase'];

            if (deleteClaseById($id_clase)) {
                echo json_encode(["success" => "Clase eliminada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error eliminando la clase"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Clase no proporcionada"]);
        }
        break;

}

