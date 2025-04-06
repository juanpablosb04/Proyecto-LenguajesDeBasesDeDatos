<?php
require 'db.php';

function ProductoRegistry($nombre_producto, $precio, $stock, $tipo_producto) {
    try {
        global $conn;

        $sql = "BEGIN registrar_producto(:nombre_producto, :precio, :stock, :tipo_producto); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':nombre_producto', $nombre_producto);
        oci_bind_by_name($stmt, ':precio', $precio);
        oci_bind_by_name($stmt, ':stock', $stock);
        oci_bind_by_name($stmt, ':tipo_producto', $tipo_producto);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;
    } catch (Exception $e) {
        error_log("Error registrando el producto: " . $e->getMessage());
        return false;
    }
}

function getProductos()
{
    global $conn;

    try {
        $sql = "BEGIN :cursor := obtener_productos(); END;";
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

function updateProducto($id_producto, $nombre_producto, $precio, $stock, $tipo_producto) {
    try {
        global $conn;

        $sql = "BEGIN actualizar_producto(:id_producto, :nombre_producto, :precio, :stock, :tipo_producto); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_producto', $id_producto);
        oci_bind_by_name($stmt, ':nombre_producto', $nombre_producto);
        oci_bind_by_name($stmt, ':precio', $precio);
        oci_bind_by_name($stmt, ':stock', $stock);
        oci_bind_by_name($stmt, ':tipo_producto', $tipo_producto);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;
    } catch (Exception $e) {
        error_log("Error actualizando el producto: " . $e->getMessage());
        return false;
    }
}

function deleteProductoByID($id_producto) {
    try {
        global $conn;

        $sql = "BEGIN eliminar_producto(:id_producto); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ':id_producto', $id_producto);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return true;
    } catch (Exception $e) {
        error_log("Error eliminando el producto: " . $e->getMessage());
        return false;
    }
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id_producto'])) {
            $id_producto = $_GET['id_producto'];
            $producto = getProductoByID($id_producto);

            if ($producto) {
                echo json_encode($producto);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Producto no encontrado"]);
            }
        } else {
            $productos = getProductos();
            echo json_encode($productos);
        }
        break;

    case 'POST':

        if (isset($_POST['nombre_producto']) && isset($_POST['precio']) && isset($_POST['stock']) && isset($_POST['tipo_producto'])) {

            $nombre_producto = $_POST['nombre_producto'];
            $precio = $_POST['precio'];
            $stock = $_POST['stock'];
            $tipo_producto = $_POST['tipo_producto'];

            if (ProductoRegistry($nombre_producto, $precio, $stock, $tipo_producto)) {
                echo json_encode(["success" => "Producto registrado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error registrando el Producto"]);
            }

        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }

        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_producto']) && isset($data['nombre_producto']) && isset($data['precio']) && isset($data['stock']) && isset($data['tipo_producto'])) {
            $id_producto = $data['id_producto'];
            $nombre_producto = $data['nombre_producto'];
            $precio = $data['precio'];
            $stock = $data['stock'];
            $tipo_producto = $data['tipo_producto'];

            if (updateProducto($id_producto, $nombre_producto, $precio, $stock, $tipo_producto)) {
                echo json_encode(["success" => "Producto actualizado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando el Producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_producto'])) {
            $id_producto = $data['id_producto'];

            if (deleteProductoByID($id_producto)) {
                echo json_encode(["success" => "Producto eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error eliminando el Producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Producto no proporcionado"]);
        }
        break;

}
