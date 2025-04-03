<?php
require 'db.php';


function ClientRegistry($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)
{
    global $conn;

    try {
        
        $sql = "BEGIN insertar_cliente(:cedula, :nombre, :apellido, :correo, :telefono, :direccion, TO_DATE(:fecha_registro, 'YYYY-MM-DD')); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':apellido', $apellido);
        oci_bind_by_name($stmt, ':correo', $correo);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_bind_by_name($stmt, ':direccion', $direccion);
        oci_bind_by_name($stmt, ':fecha_registro', $fecha_registro);

        if (oci_execute($stmt)) {
            oci_free_statement($stmt);
            return true;
        } else {
            $error = oci_error($stmt);
            error_log("Error en ClientRegistry: " . $error['message']);
            oci_free_statement($stmt);
            return false;
        }
    } catch (Exception $e) {
        error_log("Error en ClientRegistry: " . $e->getMessage());
        return false;
    }
}

function getUserByCedula($cedula)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_cliente_por_cedula(:cedula); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":cedula", $cedula);
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
        error_log("Error en getUserByCedula: " . $e->getMessage());
        return null;
    }
}

function updateUser($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)
{
    global $conn;
    try {
        $sql = "BEGIN actualizar_cliente(:cedula, :nombre, :apellido, :correo, :telefono, :direccion, TO_DATE(:fecha_registro, 'YYYY-MM-DD')); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':apellido', $apellido);
        oci_bind_by_name($stmt, ':correo', $correo);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_bind_by_name($stmt, ':direccion', $direccion);
        oci_bind_by_name($stmt, ':fecha_registro', $fecha_registro);

        if (oci_execute($stmt)) {
            oci_free_statement($stmt);
            return true;
        } else {
            $error = oci_error($stmt);
            error_log("Error en updateUser: " . $error['message']);
            oci_free_statement($stmt);
            return false;
        }
    } catch (Exception $e) {
        error_log("Error en updateUser: " . $e->getMessage());
        return false;
    }
}


function deleteUserByCedula($cedula)
{
    global $conn;

    try {
        $sql = "BEGIN eliminar_cliente(:cedula); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':cedula', $cedula);

        if (oci_execute($stmt)) {
            $rowsAffected = oci_num_rows($stmt);
            oci_free_statement($stmt);
            return $rowsAffected > 0;
        } else {
            $error = oci_error($stmt);
            error_log("Error en deleteUserByCedula: " . $error['message']);
            oci_free_statement($stmt);
            return false;
        }
    } catch (Exception $e) {
        error_log("Error en deleteUserByCedula: " . $e->getMessage());
        return false;
    }
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['cedula'])) {
            $cedula = $_GET['cedula'];
            $user = getUserByCedula($cedula);

            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Usuario no encontrado"]);
            }
        }
        break;


    case 'POST':

        if (isset($_POST['cedula']) && isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['correo']) && isset($_POST['telefono']) && isset($_POST['direccion']) && isset($_POST['fecha_registro'])) {
        
            $cedula = $_POST['cedula'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $fecha_registro = $_POST['fecha_registro'];
    
            if (ClientRegistry($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)) {
                echo json_encode(["success" => "Usuario registrado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error registrando el usuario"]);
            }
    
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }

        break;

    case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
    
            if (isset($data['cedula']) && isset($data['nombre']) && isset($data['apellido']) && isset($data['correo']) && isset($data['telefono']) && isset($data['direccion']) && isset($data['fecha_registro'])) {
                $cedula = $data['cedula'];
                $nombre = $data['nombre'];
                $apellido = $data['apellido'];
                $correo = $data['correo'];
                $telefono = $data['telefono'];
                $direccion = $data['direccion'];
                $fecha_registro = $data['fecha_registro'];
    
                if (updateUser($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)) {
                    echo json_encode(["success" => "Usuario actualizado exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error actualizando el usuario"]);
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
        
                    if (deleteUserByCedula($cedula)) {
                        echo json_encode(["success" => "Usuario eliminado exitosamente"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Error eliminando el usuario"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Cédula no proporcionada"]);
                }
            break;
}



