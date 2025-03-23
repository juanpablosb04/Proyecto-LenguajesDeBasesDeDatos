<?php
require 'db.php';


function userRegistry($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)
{
    global $pdo;
    
    try {
        
        $sql = "BEGIN insertar_cliente(:cedula, :nombre, :apellido, :correo, :telefono, :direccion, TO_DATE(:fecha_registro, 'YYYY-MM-DD')); END;";
        $stmt = $pdo->prepare($sql);
         $stmt->execute([
             'nombre' => $nombre,
             'cedula' => $cedula,
             'apellido' => $apellido,
             'correo' => $correo,
             'telefono' => $telefono,
             'direccion' => $direccion,
             'fecha_registro' => $fecha_registro
         ]);
 
         return true;
 
     } catch (Exception $e) {
        error_log("Error en userRegistry: " . $e->getMessage());
        return false;
    }
 }

function getUserByCedula($cedula)
{
    try {
        global $pdo;

        $sql = "SELECT * FROM clientes WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        return null;
    }
}

function updateUser($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)
{
    try {
        global $pdo;

        $sql = "UPDATE clientes 
                SET nombre = :nombre, apellido = :apellido, correo = :correo, telefono = :telefono, direccion = :direccion, fecha_registro = TO_DATE(:fecha_registro, 'YYYY-MM-DD')
                WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cedula' => $cedula,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'fecha_registro' => $fecha_registro
        ]);

        return true;

    } catch (Exception $e) {
        return false;
    }
}

function deleteUserByCedula($cedula)
{
    try {
        global $pdo;

        $sql = "DELETE FROM clientes WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);

        return $stmt->rowCount() > 0;

    } catch (Exception $e) {
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
    
            if (userRegistry($cedula, $nombre, $apellido, $correo, $telefono, $direccion, $fecha_registro)) {
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



