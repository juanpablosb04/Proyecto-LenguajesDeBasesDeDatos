<?php
require 'db.php';

// Función para registrar una nueva clase
function registrarClase($nombre_clase, $descripcion, $id_instructor) {
    try {
        global $pdo;

        $sql = "INSERT INTO clases (id_clase, nombre_clase, descripcion, id_instructor)
                VALUES (clases_seq.NEXTVAL, :nombre_clase, :descripcion, :id_instructor)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre_clase' => $nombre_clase,
            'descripcion' => $descripcion,
            'id_instructor' => $id_instructor
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para obtener todas las clases
function obtenerClases() {
    try {
        global $pdo;

        $sql = "SELECT c.id_clase, c.nombre_clase, c.descripcion, c.id_instructor, i.nombre AS instructor_nombre
                FROM clases c
                LEFT JOIN instructores i ON c.id_instructor = i.id_instructor
                ORDER BY c.nombre_clase ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Función para actualizar una clase
function actualizarClase($id_clase, $nombre_clase, $descripcion, $id_instructor) {
    try {
        global $pdo;

        $sql = "UPDATE clases 
                SET nombre_clase = :nombre_clase, descripcion = :descripcion, id_instructor = :id_instructor
                WHERE id_clase = :id_clase";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_clase' => $id_clase,
            'nombre_clase' => $nombre_clase,
            'descripcion' => $descripcion,
            'id_instructor' => $id_instructor
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para eliminar una clase
function eliminarClase($id_clase) {
    try {
        global $pdo;

        $sql = "DELETE FROM clases WHERE id_clase = :id_clase";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_clase' => $id_clase]);

        return $stmt->rowCount() > 0 ? true : "Error al eliminar la clase.";
    } catch (Exception $e) {
        return "Error al eliminar la clase: " . $e->getMessage();
    }
}

// Manejo de solicitudes HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $clases = obtenerClases();
        echo json_encode($clases);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['nombreClase']) && isset($data['descripcion']) && isset($data['idInstructor'])) {
            $nombre_clase = $data['nombreClase'];
            $descripcion = $data['descripcion'];
            $id_instructor = $data['idInstructor'];

            if (registrarClase($nombre_clase, $descripcion, $id_instructor)) {
                echo json_encode(["success" => "Clase registrada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error registrando la clase"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['idClase']) && isset($data['nombreClase']) && isset($data['descripcion']) && isset($data['idInstructor'])) {
            $id_clase = $data['idClase'];
            $nombre_clase = $data['nombreClase'];
            $descripcion = $data['descripcion'];
            $id_instructor = $data['idInstructor'];

            if (actualizarClase($id_clase, $nombre_clase, $descripcion, $id_instructor)) {
                echo json_encode(["success" => "Clase actualizada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando la clase"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['idClase'])) {
            $id_clase = $data['idClase'];
            $resultado = eliminarClase($id_clase);

            if ($resultado === true) {
                echo json_encode(["success" => "Clase eliminada exitosamente"]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => $resultado]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de clase no proporcionado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
