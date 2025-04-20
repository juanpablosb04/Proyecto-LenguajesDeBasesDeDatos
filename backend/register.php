<?php
require 'db.php';

function userRegistry($username, $password, $email)
{
    try {
        global $conn;

        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

        $sql = "BEGIN registrar_empleado(:username, :password, :email); END;";
        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ":username", $username);
        oci_bind_by_name($stmt, ":password", $passwordHashed);
        oci_bind_by_name($stmt, ":email", $email);

        $result = oci_execute($stmt);

        if (!$result) {
            throw new Exception("Error ejecutando procedimiento");
        }

        logDebug("Usuario Registrado");
        return true;

    } catch (Exception $e) {
        logError("Ocurrió un error: " . $e->getMessage());
        return false;
    }
}

function logDebug($message) {
    echo "DEBUG: $message\n";
}

function logError($message) {
    echo "ERROR: $message\n";
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (userRegistry($username, $password, $email)) {
            header("Location: ../login.html");
            exit();
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error registrando el usuario"]);
        }

    } else {
        http_response_code(400);
        echo json_encode(["error" => "Username, email y password son requeridos"]);
    }

} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>

