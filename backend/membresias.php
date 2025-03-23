<?php
require 'db.php';

function registrarMembresia($cedula, $costo_mensual)
{
    try {
        global $pdo;

        $sql = "SELECT COUNT(*) FROM clientes WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);

        if ($stmt->fetchColumn() === 0) {
            return "Cliente no encontrado";
        }

        $sql = "INSERT INTO miembros (id_miembro, cedula, costo_mensual)
                VALUES (miembros_seq.NEXTVAL, :cedula, :costo_mensual)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cedula' => $cedula,
            'costo_mensual' => $costo_mensual
        ]);

        return true;

    } catch (Exception $e) {
        error_log("Error registrando la membresía: " . $e->getMessage());
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
    try {
        global $pdo;

        $sql = "UPDATE miembros 
                SET costo_mensual = :costo_mensual, activo = :estado
                WHERE cedula = :cedula";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'cedula' => $cedula,
            'costo_mensual' => $costo_mensual,
            'estado' => $estado
        ]);

        return true;

    } catch (Exception $e) {
        return false;
    }
}

function deleteMembresiaByCedula($cedula)
{
    try {
        global $pdo;

        $sql = "DELETE FROM miembros WHERE cedula = :cedula";
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