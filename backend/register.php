<?php
require 'db.php';

function userRegistry($username, $password, $email)
{
    try {
        global $pdo;
        
        $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

        
        $sql = "INSERT INTO empleados (id, username, password, email) 
                VALUES (empleados_seq.NEXTVAL, :username, :password, :email)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'password' => $passwordHashed,
            'email' => $email
        ]);

       
        logDebug("Usuario Registrado");

        return true;

    } catch (Exception $e) {
        logError("Ocurrio un error: " . $e->getMessage());
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

    
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $username = $_POST['email']; 
        $password = $_POST['password'];

        
        if (userRegistry($username, $password, $username)) {
           
            header("Location: ../login.html");
            exit(); 
            
        } else {
            
            http_response_code(500);
            echo json_encode(["error" => "Error registrando el usuario"]);
        }

    } else {
        
        http_response_code(400);
        echo json_encode(["error" => "Email y password son requeridos"]);
    }

} else {
    
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>
