<?php
require 'db.php';

function instructorRegistry($nombre, $especialidad, $telefono, $correo, $salario)
{
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

function getInstructores()
{
    try {
        global $pdo;

        $sql = "SELECT id_instructor, nombre, especialidad, telefono, correo, salario FROM instructores";
        $stmt = $pdo->query($sql);
        $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $instructores;

    } catch (Exception $e) {
        error_log("Error al obtener instructores: " . $e->getMessage());
        return [];
    }
}

function getInstructorById($id)
{
    try {
        global $pdo;

        $sql = "SELECT id_instructor, nombre, especialidad, telefono, correo, salario FROM instructores WHERE id_instructor = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Error al obtener instructor: " . $e->getMessage());
        return null;
    }
}

function updateInstructor($id, $nombre, $especialidad, $telefono, $correo, $salario)
{
    try {
        global $pdo;

        $sql = "UPDATE instructores 
                SET nombre = :nombre, especialidad = :especialidad, telefono = :telefono, correo = :correo, salario = :salario 
                WHERE id_instructor = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'especialidad' => $especialidad,
            'telefono' => $telefono,
            'correo' => $correo,
            'salario' => $salario
        ]);

        return true;

    } catch (Exception $e) {
        error_log("Error al actualizar instructor: " . $e->getMessage());
        return false;
    }
}

function deleteInstructorById($id_instructor)
{
    try {
        global $pdo;

        $sql = "DELETE FROM instructores WHERE id_instructor = :id_instructor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_instructor' => $id_instructor]);

        return $stmt->rowCount() > 0;

    } catch (Exception $e) {
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':

        if (isset($_GET['id'])) {
            $instructor = getInstructorById($_GET['id']);
            echo json_encode($instructor);
        } else {
            $instructores = getInstructores();
            echo json_encode($instructores);
        }

        break;
    case 'POST':

        if (isset($_POST['nombre']) && isset($_POST['especialidad']) && isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['salario'])) {

            $nombre = $_POST['nombre'];
            $especialidad = $_POST['especialidad'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $salario = $_POST['salario'];

            if (instructorRegistry($nombre, $especialidad, $telefono, $correo, $salario)) {
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
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['id_instructor'], $data['nombre'], $data['especialidad'], $data['telefono'], $data['correo'], $data['salario'])) {
            if (updateInstructor($data['id_instructor'], $data['nombre'], $data['especialidad'], $data['telefono'], $data['correo'], $data['salario'])) {
                header('Content-Type: application/json');
                echo json_encode(["success" => "Instructor actualizado exitosamente"]);
            } else {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(["error" => "Error actualizando el instructor"]);
            }
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Datos incompletos"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['id_instructor'])) {
            $id_instructor = $data['id_instructor'];

            if (deleteInstructorById($id_instructor)) {
                echo json_encode(["success" => "Instructor eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error eliminando el Instructor"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Instructor no proporcionado"]);
        }
        break;
}

