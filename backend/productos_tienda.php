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
    try {
        global $pdo;

        $sql = "SELECT id_producto, nombre_producto, precio, stock, tipo_producto FROM productos_tienda";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return ["error" => "Error al obtener productos"];
    }
}

function getProductoByID($id_producto)
{
    try {
        global $pdo;

        $sql = "SELECT * FROM productos_tienda WHERE id_producto = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_producto' => $id_producto]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
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
