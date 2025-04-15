<?php
require 'db.php';

function registrarVentaTienda($cedula, $id_producto, $cantidad, $total, $fecha_venta)
{
    try {
        global $conn;

        $sql = "SELECT COUNT(*) AS count FROM clientes WHERE cedula = :cedula";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

        if ($row['COUNT'] == 0) {
            return ["error" => "Cliente no encontrado"];
        }

        $sql = "SELECT COUNT(*) AS count FROM productos_tienda WHERE id_producto = :id_producto";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id_producto', $id_producto);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

        if ($row['COUNT'] == 0) {
            return ["error" => "Producto no encontrado"];
        }

        $sql = "BEGIN registrar_venta(:cedula, :id_producto, :cantidad, :total, TO_DATE(:fecha_venta, 'YYYY-MM-DD')); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':id_producto', $id_producto);
        oci_bind_by_name($stmt, ':cantidad', $cantidad);
        oci_bind_by_name($stmt, ':total', $total);
        oci_bind_by_name($stmt, ':fecha_venta', $fecha_venta);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return ["success" => "Venta registrada. El Stock fue actualizado."];
    } catch (Exception $e) {
        error_log("Error registrando la venta: " . $e->getMessage());
        return ["error" => "Error registrando la venta: " . $e->getMessage()];
    }
}


function getVentaProductoByID($id_venta)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_venta_por_id(:id_venta); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":id_venta", $id_venta);
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
        error_log("Error en getProductoByID: " . $e->getMessage());
        return null;
    }
}

function getProductos()
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_producto_nombre(); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        oci_bind_by_name($stmt, ':cursor', $cursor, -1, OCI_B_CURSOR);

        oci_execute($stmt);
        oci_execute($cursor);

        $productos = [];
        while (($row = oci_fetch_assoc($cursor)) !== false) {
            $productos[] = $row;
        }

        oci_free_statement($stmt);
        oci_free_statement($cursor);

        return $productos;

    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return [];
    }
}

function getProductoByID($id_producto)
{
    global $conn;
    try {
        $sql = "BEGIN :cursor := obtener_producto_por_id(:id_producto); END;";
        $stmt = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);
        
        oci_bind_by_name($stmt, ":id_producto", $id_producto);
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
        error_log("Error en getProductoByID: " . $e->getMessage());
        return null;
    }
}

function calcularTotalVenta($id_producto, $cantidad)
{
    try {
        global $conn;

        $sql = "BEGIN :resultado := calcular_totalDeLaVenta(:id_producto, :cantidad); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_producto', $id_producto);
        oci_bind_by_name($stmt, ':cantidad', $cantidad);
        oci_bind_by_name($stmt, ':resultado', $resultado, 32);

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            return ["error" => "Error al ejecutar la función: " . $e['message']];
        }

        oci_free_statement($stmt);

        return ["total" => $resultado];

    } catch (Exception $e) {
        error_log("Error al calcular total de venta: " . $e->getMessage());
        return ["error" => "Error al calcular total de venta: " . $e->getMessage()];
    }
}

function actualizarVentaTienda($id_venta, $cedula, $id_producto, $cantidad, $total, $fecha_venta) {
    try {
        global $conn;

        $sql = "BEGIN actualizar_venta_tienda(:id_venta, :cedula, :id_producto, :cantidad, :total, TO_DATE(:fecha_venta, 'YYYY-MM-DD')); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_venta', $id_venta);
        oci_bind_by_name($stmt, ':cedula', $cedula);
        oci_bind_by_name($stmt, ':id_producto', $id_producto);
        oci_bind_by_name($stmt, ':cantidad', $cantidad);
        oci_bind_by_name($stmt, ':total', $total);
        oci_bind_by_name($stmt, ':fecha_venta', $fecha_venta);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;
    } catch (Exception $e) {
        error_log("Error actualizando la venta: " . $e->getMessage());
        return ["error" => "Error actualizando la venta: " . $e->getMessage()];
    }
}

function deleteVentaById($id_venta) {
    try {
        global $conn;

        $sql = "BEGIN eliminar_venta_tienda(:id_venta); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_venta', $id_venta);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;
    } catch (Exception $e) {
        error_log("Error eliminando la venta: " . $e->getMessage());
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id_venta'])) {
            $id_venta = $_GET['id_venta'];
            $venta = getVentaProductoByID($id_venta);
    
            if ($venta) {
                echo json_encode($venta);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Venta no encontrada"]);
            }
            
        } elseif (isset($_GET['id_producto']) && isset($_GET['cantidad'])) {
            $id_producto = $_GET['id_producto'];
            $cantidad = $_GET['cantidad'];
    
            $resultado = calcularTotalVenta($id_producto, $cantidad);
    
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(["error" => $resultado['error']]);
            } else {
                echo json_encode(["total" => $resultado['total']]);
            }
        } elseif (isset($_GET['action']) && $_GET['action'] == 'getProductos') {
            $productos = getProductos();
            echo json_encode($productos);
        }elseif (isset($_GET['id_producto'])) {
            $id_producto = $_GET['id_producto'];
            $producto = getProductoByID($id_producto);
            
            if ($producto) {
                echo json_encode($producto);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Producto no encontrado"]);
            }
        }
        break;

    case 'POST':
        error_log("Datos recibidos: " . print_r($_POST, true));

        if (
            isset($_POST['cedula']) && isset($_POST['id_producto']) && isset($_POST['cantidad']) &&
            isset($_POST['total']) && isset($_POST['fecha_venta'])
        ) {
            $cedula = $_POST['cedula'];
            $id_producto = $_POST['id_producto'];
            $cantidad = $_POST['cantidad'];
            $total = $_POST['total'];
            $fecha_venta = $_POST['fecha_venta'];

            $resultado = registrarVentaTienda($cedula, $id_producto, $cantidad, $total, $fecha_venta);

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

        if (isset($data['id_venta'], $data['cedula'], $data['id_producto'], $data['cantidad'], $data['total'], $data['fecha_venta'])) {
            $id_venta = $data['id_venta'];
            $cedula = $data['cedula'];
            $id_producto = $data['id_producto'];
            $cantidad = $data['cantidad'];
            $total = $data['total'];
            $fecha_venta = $data['fecha_venta'];

            $resultado = actualizarVentaTienda($id_venta, $cedula, $id_producto, $cantidad, $total, $fecha_venta);

            if (!isset($resultado['error'])) {
                echo json_encode(["success" => "Venta actualizada exitosamente"]);
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

        if (isset($data['id_venta'])) {
            $id_venta = $data['id_venta'];

            if (deleteVentaById($id_venta)) {
                echo json_encode(["success" => "Venta eliminada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error eliminando la venta"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID de venta no proporcionado"]);
        }
        break;

}