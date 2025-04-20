<?php
session_start();
require 'db.php';

function login($username, $password){
    try {
        global $conn;

        $sql = "SELECT * FROM empleados WHERE username = :username";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ":username", $username);

        oci_execute($stmt);

        $user = oci_fetch_assoc($stmt);

        if ($user && password_verify($password, $user['PASSWORD'])) {
            $_SESSION['user_id'] = $user['ID'];
            return true;
        }

        return false;

    } catch(Exception $e) {
        logError($e->getMessage());
        return false;
    }
}

function logError($message) {
    echo "ERROR: $message\n";
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (login($username, $password)) {
            http_response_code(200);
            echo json_encode(["message" => "Login exitoso"]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Usuario o password incorrecto"]);
        }

    } else {
        http_response_code(400);
        echo json_encode(["error" => "Username y password son requeridos"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>

