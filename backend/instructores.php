<?php
require 'db.php';
header('Content-Type: application/json');

// Función para registrar un nuevo instructor
function registrarInstructor($nombre, $especialidad, $telefono, $correo, $salario) {
    try {
        global $pdo;

        $sql = "INSERT INTO instructores (id_instructor, nombre, especialidad, telefono, correo, salario)
                VALUES (instructores_seq.NEXTVAL, :nombre, :especialidad, :telefono, :correo, :salario)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'especialidad' => $especialidad,
            'telefono' => $telefono,
            'correo' => $correo,
            'salario' => $salario
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para obtener todos los instructores
function obtenerInstructores() {
    try {
        global $pdo;

        $sql = "SELECT * FROM instructores ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Función para actualizar un instructor
function actualizarInstructor($idInstructor, $nombre, $especialidad, $telefono, $correo, $salario) {
    try {
        global $pdo;

        $sql = "UPDATE instructores 
                SET nombre = :nombre, especialidad = :especialidad, telefono = :telefono, correo = :correo, salario = :salario
                WHERE id_instructor = :id_instructor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_instructor' => $idInstructor,
            'nombre' => $nombre,
            'especialidad' => $especialidad,
            'telefono' => $telefono,
            'correo' => $correo,
            'salario' => $salario
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para eliminar un instructor
function eliminarInstructor($idInstructor) {
    try {
        global $pdo;

        // Verificar si el instructor tiene clases asignadas
        $sql = "SELECT COUNT(*) FROM clases WHERE id_instructor = :id_instructor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_instructor' => $idInstructor]);
        $clasesAsociadas = $stmt->fetchColumn();

        if ($clasesAsociadas > 0) {
            return "No se puede eliminar, el instructor tiene clases asignadas.";
        }

        // Si no tiene clases, proceder a eliminar
        $sql = "DELETE FROM instructores WHERE id_instructor = :id_instructor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_instructor' => $idInstructor]);

        return $stmt->rowCount() > 0 ? true : "Error al eliminar el instructor.";
    } catch (Exception $e) {
        return "Error al eliminar el instructor: " . $e->getMessage();
    }
}

// Manejo de solicitudes HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $instructores = obtenerInstructores();
        echo json_encode($instructores);

        default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;


    case 'POST':
        if (isset($_POST['nombre']) && isset($_POST['especialidad']) && isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['salario'])) {
            $nombre = $_POST['nombre'];
            $especialidad = $_POST['especialidad'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $salario = $_POST['salario'];

            if (registrarInstructor($nombre, $especialidad, $telefono, $correo, $salario)) {
                echo json_encode(["success" => "Instructor registrado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error registrando el instructor"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['idInstructor']) && isset($data['nombre']) && isset($data['especialidad']) && isset($data['telefono']) && isset($data['correo']) && isset($data['salario'])) {
            $idInstructor = $data['idInstructor'];
            $nombre = $data['nombre'];
            $especialidad = $data['especialidad'];
            $telefono = $data['telefono'];
            $correo = $data['correo'];
            $salario = $data['salario'];

            if (actualizarInstructor($idInstructor, $nombre, $especialidad, $telefono, $correo, $salario)) {
                echo json_encode(["success" => "Instructor actualizado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando el instructor"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['idInstructor'])) {
            $idInstructor = $data['idInstructor'];
            $resultado = eliminarInstructor($idInstructor);

            if ($resultado === true) {
                echo json_encode(["success" => "Instructor eliminado exitosamente"]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => $resultado]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de instructor no proporcionado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

