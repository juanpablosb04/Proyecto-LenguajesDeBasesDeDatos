<?php
require 'db.php';

function registrarPago($id_miembro, $monto, $metodo_pago, $fecha_pago)
{
    try {
        global $conn;

        $sql = "SELECT COUNT(*) AS COUNT FROM miembros WHERE id_miembro = :id_miembro";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id_miembro', $id_miembro);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

        if ($row['COUNT'] == 0) {
            return ["error" => "Miembro no encontrado"];
        }

        $sql = "BEGIN registrar_pago(:id_miembro, TO_DATE(:fecha_pago, 'YYYY-MM-DD'), :monto, :metodo_pago); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_miembro', $id_miembro);
        oci_bind_by_name($stmt, ':fecha_pago', $fecha_pago);
        oci_bind_by_name($stmt, ':monto', $monto);
        oci_bind_by_name($stmt, ':metodo_pago', $metodo_pago);

        
        oci_execute($stmt);
        oci_free_statement($stmt);

        return ["success" => "Pago registrado. Estado del miembro actualizado."];

    } catch (Exception $e) {
        error_log("Error registrando el pago: " . $e->getMessage());
        return ["error" => "Error registrando el pago: " . $e->getMessage()];
    }
}

function getPagoByID($id_pago)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_pago_por_id(:id_pago); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":id_pago", $id_pago);
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
        error_log("Error en getPagoByID: " . $e->getMessage());
        return null;
    }
}

function actualizarPago($id_pago, $id_miembro, $monto, $metodo_pago, $fecha_pago) {
    try {
        global $conn;

        $sql = "BEGIN actualizar_pago(:id_pago, :id_miembro, :monto, :metodo_pago, TO_DATE(:fecha_pago, 'YYYY-MM-DD')); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_pago', $id_pago);
        oci_bind_by_name($stmt, ':id_miembro', $id_miembro);
        oci_bind_by_name($stmt, ':monto', $monto);
        oci_bind_by_name($stmt, ':metodo_pago', $metodo_pago);
        oci_bind_by_name($stmt, ':fecha_pago', $fecha_pago);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return ["success" => "Pago actualizado exitosamente. Estado del miembro actualizado."];
    } catch (Exception $e) {
        error_log("Error actualizando el pago: " . $e->getMessage());
        return ["error" => "Error actualizando el pago: " . $e->getMessage()];
    }
}

function deletePagoById($id_pago) {
    try {
        global $conn;

        $sql = "BEGIN eliminar_pago(:id_pago); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_pago', $id_pago);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;
    } catch (Exception $e) {
        error_log("Error eliminando el pago: " . $e->getMessage());
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