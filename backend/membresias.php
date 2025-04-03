<?php
require 'db.php';

function registrarMembresia($cedula, $costo_mensual)
{
    global $conn; 
    try {
        $sql = "BEGIN registrar_membresia(:cedula, :costo_mensual); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':costo_mensual', $costo_mensual);

        if (oci_execute($stmt)) {
            $rowsAffected = oci_num_rows($stmt);
            oci_free_statement($stmt);
            return ($rowsAffected > 0) ? true : "Cliente no encontrado";
        } else {
            $error = oci_error($stmt);
            error_log("Error en registrarMembresia: " . $error['message']);
            oci_free_statement($stmt);
            return "Error registrando la membresía: " . $error['message'];
        }
    } catch (Exception $e) {
        error_log("Error en registrarMembresia: " . $e->getMessage());
        return "Error registrando la membresía: " . $e->getMessage();
    }
}


function getMembresiaByCedula($cedula)
{
    try {
        global $pdo;
        $sql = "SELECT * FROM miembros WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        return null;
    }
}

function updateMembresia($cedula, $costo_mensual, $estado)
{
    global $conn;
    try {
        $sql = "BEGIN actualizar_membresia(:cedula, :costo_mensual, :estado); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':costo_mensual', $costo_mensual);
        oci_bind_by_name($stmt, ':estado', $estado);

        if (oci_execute($stmt)) {
            oci_free_statement($stmt);
            return true;
        } else {
            $error = oci_error($stmt);
            error_log("Error en updateMembresia: " . $error['message']);
            oci_free_statement($stmt);
            return false;
        }
    } catch (Exception $e) {
        error_log("Error en updateMembresia: " . $e->getMessage());
        return false;
    }
}


function deleteMembresiaByCedula($cedula)
{
    global $conn;
    try {
        $sql = "BEGIN eliminar_membresia(:cedula); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':cedula', $cedula);

        if (oci_execute($stmt)) {
            $rowsAffected = oci_num_rows($stmt);
            oci_free_statement($stmt);
            return $rowsAffected > 0;
        } else {
            $error = oci_error($stmt);
            error_log("Error en deleteMembresiaByCedula: " . $error['message']);
            oci_free_statement($stmt);
            return false;
        }
    } catch (Exception $e) {
        error_log("Error en deleteMembresiaByCedula: " . $e->getMessage());
        return false;
    }
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['cedula'])) {
            $cedula = $_GET['cedula'];
            $membresia = getMembresiaByCedula($cedula);

            if ($membresia) {
                echo json_encode($membresia);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Membresía no encontrada"]);
            }
        }
        break;

    case 'POST':
        
        error_log("Datos recibidos: " . print_r($_POST, true));

        if (isset($_POST['cedula']) && isset($_POST['costo_mensual'])) {
            $cedula = $_POST['cedula'];
            $costo_mensual = $_POST['costo_mensual'];

            $resultado = registrarMembresia($cedula, $costo_mensual);

            if ($resultado === true) {
                echo json_encode(["success" => "Membresía registrada exitosamente"]);
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

        if (isset($data['cedula']) && isset($data['costo_mensual']) && isset($data['estado'])) {
            $cedula = $data['cedula'];
            $costo_mensual = $data['costo_mensual'];
            $estado = $data['estado'];

            if (updateMembresia($cedula, $costo_mensual, $estado)) {
                echo json_encode(["success" => "Membresía actualizada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando la membresía"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
        
            if (isset($data['cedula'])) {
                $cedula = $data['cedula'];
        
                if (deleteMembresiaByCedula($cedula)) {
                    echo json_encode(["success" => "Membresía eliminada exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error eliminando la membresía"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Cédula no proporcionada"]);
            }
            break;
}