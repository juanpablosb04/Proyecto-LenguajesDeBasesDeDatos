<?php
require 'db.php';

// Función para registrar un nuevo producto
function registrarProducto($nombre, $precio, $stock, $tipo) {
    try {
        global $pdo;

        $sql = "INSERT INTO productos_tienda (id_producto, nombre_producto, precio, stock, tipo_producto)
                VALUES (productos_tienda_seq.NEXTVAL, :nombre, :precio, :stock, :tipo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'precio' => $precio,
            'stock' => $stock,
            'tipo' => $tipo
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para obtener todos los productos
function obtenerProductos() {
    try {
        global $pdo;
        $sql = "SELECT * FROM productos_tienda ORDER BY nombre_producto ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en obtenerProductos: " . $e->getMessage()); // Registra el error
        return [];
    }
}

// Función para actualizar un producto
function actualizarProducto($id_producto, $nombre, $precio, $stock, $tipo) {
    try {
        global $pdo;

        $sql = "UPDATE productos_tienda 
                SET nombre_producto = :nombre, precio = :precio, stock = :stock, tipo_producto = :tipo
                WHERE id_producto = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_producto' => $id_producto,
            'nombre' => $nombre,
            'precio' => $precio,
            'stock' => $stock,
            'tipo' => $tipo
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para eliminar un producto
function eliminarProducto($id_producto) {
    try {
        global $pdo;

        $sql = "DELETE FROM productos_tienda WHERE id_producto = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_producto' => $id_producto]);

        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Manejo de solicitudes HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $productos = obtenerProductos();
        var_dump($productos); // Verifica los datos obtenidos
        echo json_encode($productos);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['nombre']) && isset($data['precio']) && isset($data['stock']) && isset($data['tipo'])) {
            $nombre = $data['nombre'];
            $precio = $data['precio'];
            $stock = $data['stock'];
            $tipo = $data['tipo'];

            if (registrarProducto($nombre, $precio, $stock, $tipo)) {
                echo json_encode(["success" => "Producto registrado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error registrando el producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todos los campos son requeridos"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_producto']) && isset($data['nombre']) && isset($data['precio']) && isset($data['stock']) && isset($data['tipo'])) {
            $id_producto = $data['id_producto'];
            $nombre = $data['nombre'];
            $precio = $data['precio'];
            $stock = $data['stock'];
            $tipo = $data['tipo'];

            if (actualizarProducto($id_producto, $nombre, $precio, $stock, $tipo)) {
                echo json_encode(["success" => "Producto actualizado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error actualizando el producto"]);
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

            if (eliminarProducto($id_producto)) {
                echo json_encode(["success" => "Producto eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error eliminando el producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID del producto no proporcionado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
