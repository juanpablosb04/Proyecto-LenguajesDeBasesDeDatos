<?php
require 'db.php';

function registrarClase($nombre_clase, $descripcion, $id_instructor)
{
    global $conn;

    try {
        $sql = "BEGIN registrar_clase(:nombre_clase, :descripcion, :id_instructor); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':nombre_clase', $nombre_clase);
        oci_bind_by_name($stmt, ':descripcion', $descripcion);
        oci_bind_by_name($stmt, ':id_instructor', $id_instructor);
        oci_execute($stmt);

        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error registrando la clase: " . $e->getMessage());
        return "Error registrando la clase: " . $e->getMessage();
    }
}


function getClaseById($id)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_clase_por_id(:id); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":id", $id);
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
        error_log("Error en getClaseById: " . $e->getMessage());
        return null;
    }
}

function getClases()
{
    global $conn;

    try {
        $sql = "BEGIN :cursor := obtener_clases(); END;";
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
        error_log("Error al obtener clases: " . $e->getMessage());
        return [];
    }
}

function updateClase($id, $nombre_clase, $descripcion, $id_instructor)
{
    global $conn;

    try {
        $sql = "BEGIN actualizar_clase(:id, :nombre_clase, :descripcion, :id_instructor); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id', $id);
        oci_bind_by_name($stmt, ':nombre_clase', $nombre_clase);
        oci_bind_by_name($stmt, ':descripcion', $descripcion);
        oci_bind_by_name($stmt, ':id_instructor', $id_instructor);
        oci_execute($stmt);

        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error al actualizar clase: " . $e->getMessage());
        return false;
    }
}

function deleteClaseById($id_clase)
{
    global $conn;

    try {
        $sql = "BEGIN eliminar_clase(:id_clase); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id_clase', $id_clase);

        oci_execute($stmt);

        oci_free_statement($stmt);

        return true;

    } catch (Exception $e) {
        error_log("Error al eliminar clase: " . $e->getMessage());
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

