<?php
require 'db.php';

function registrarPago($id_miembro, $monto, $metodo_pago, $fecha_pago)
{
    try {
        global $pdo;

        $sql = "SELECT COUNT(*) FROM miembros WHERE id_miembro = :id_miembro";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_miembro' => $id_miembro]);

        if ($stmt->fetchColumn() === 0) {
            return ["error" => "Miembro no encontrado"];
        }

        $sql = "BEGIN registrar_pago(:id_miembro, :fecha_pago, :monto, :metodo_pago); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_miembro' => $id_miembro,
            'fecha_pago' => $fecha_pago,
            'monto' => $monto,
            'metodo_pago' => $metodo_pago 
        ]);


        return ["success" => "Pago registrado exitosamente. Estado del miembro actualizado."];

    } catch (Exception $e) {
        error_log("Error registrando el pago: " . $e->getMessage());
        return ["error" => "Error registrando el pago: " . $e->getMessage()];
    }
}

function getPagoByID($id_pago)
{
    try {
        global $pdo;

        $sql = "SELECT * FROM pagos WHERE id_pago = :id_pago";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_pago' => $id_pago]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        return null;
    }
}

function actualizarPago($id_pago, $id_miembro, $monto, $metodo_pago, $fecha_pago)
{
    try {
        global $pdo;

        $sql = "BEGIN actualizar_pago(:id_pago, :id_miembro, :monto, :metodo_pago, :fecha_pago); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_pago' => $id_pago,
            'id_miembro' => $id_miembro,
            'monto' => $monto,
            'metodo_pago' => $metodo_pago,
            'fecha_pago' => $fecha_pago
        ]);

        return ["success" => "Pago actualizado exitosamente. Estado del miembro actualizado."];

    } catch (Exception $e) {
        error_log("Error actualizando el pago: " . $e->getMessage());
        return ["error" => "Error actualizando el pago: " . $e->getMessage()];
    }
}

function deletePagoById($id_pago)
{
    try {
        global $pdo;

        $sql = "BEGIN eliminar_pago(:id_pago); END;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_pago' => $id_pago]);

        return $stmt->rowCount() > 0;

    } catch (Exception $e) {
        return false;
    }
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id_pago'])) {
            $id_pago = $_GET['id_pago'];
            $pago = getPagoByID($id_pago);

            if ($pago) {
                echo json_encode($pago);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Pago no encontrado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Se requiere el ID del pago"]);
        }
        break;

    case 'POST':
        error_log("Datos recibidos: " . print_r($_POST, true));

        if (
            isset($_POST['id_miembro']) && isset($_POST['fecha_pago']) &&
            isset($_POST['monto']) && isset($_POST['metodo_pago'])
        ) {
            $id_miembro = $_POST['id_miembro'];
            $fecha_pago = $_POST['fecha_pago'];
            $monto = $_POST['monto'];
            $metodo_pago = $_POST['metodo_pago'];

            $resultado = registrarPago(
                $id_miembro,
                $monto,
                $metodo_pago,
                $fecha_pago
            );

            if (isset($resultado['success'])) {
                echo json_encode($resultado);
            } else {
                http_response_code(400);
                echo json_encode($resultado);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_pago'], $data['id_miembro'], $data['monto'], $data['metodo_pago'], $data['fecha_pago'])) {
            $id_pago = $data['id_pago'];
            $id_miembro = $data['id_miembro'];
            $monto = $data['monto'];
            $metodo_pago = $data['metodo_pago'];
            $fecha_pago = $data['fecha_pago'];

            $resultado = actualizarPago($id_pago, $id_miembro, $monto, $metodo_pago, $fecha_pago);

            if (isset($resultado['success'])) {
                echo json_encode($resultado);
            } else {
                http_response_code(500);
                echo json_encode($resultado);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_pago'])) {
            $id_pago = $data['id_pago'];

            if (deletePagoById($id_pago)) {
                echo json_encode(["success" => "Pago eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error eliminando el Pago"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de Pago no proporcionado"]);
        }
        break;

}