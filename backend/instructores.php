<?php
require 'db.php';

function instructorRegistry($nombre, $especialidad, $telefono, $correo, $salario)
{
    global $conn;

    try {
        $sql = "BEGIN registrar_instructor(:nombre, :especialidad, :telefono, :correo, :salario); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':especialidad', $especialidad);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_bind_by_name($stmt, ':correo', $correo);
        oci_bind_by_name($stmt, ':salario', $salario);

        oci_execute($stmt);

        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error en instructorRegistry: " . $e->getMessage());
        return false;
    }
}


function getInstructores()
{
    global $conn;

    try {
        $sql = "BEGIN :cursor := obtener_instructores(); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        oci_bind_by_name($stmt, ':cursor', $cursor, -1, OCI_B_CURSOR);

        oci_execute($stmt);
        oci_execute($cursor);

        $instructores = [];
        while (($row = oci_fetch_assoc($cursor)) !== false) {
            $instructores[] = $row;
        }

        oci_free_statement($stmt);
        oci_free_statement($cursor);

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
    global $conn;

    try {
        $sql = "BEGIN actualizar_instructor(:id, :nombre, :especialidad, :telefono, :correo, :salario); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id', $id);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':especialidad', $especialidad);
        oci_bind_by_name($stmt, ':telefono', $telefono);

        oci_bind_by_name($stmt, ':correo', $correo);
        oci_bind_by_name($stmt, ':salario', $salario);

        oci_execute($stmt);

        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error en updateInstructor: " . $e->getMessage());
        return false;
    }
}


function deleteInstructorById($id_instructor)
{
    global $conn;

    try {
        $sql = "BEGIN eliminar_instructor(:id_instructor); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_instructor', $id_instructor);
        oci_execute($stmt);

        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error en deleteInstructorById: " . $e->getMessage());
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

