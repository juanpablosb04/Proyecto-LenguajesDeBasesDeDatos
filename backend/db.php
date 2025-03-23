<?php

$host = 'localhost'; 
$port = '1521'; 
$service_name = 'orcl'; 
$user = 'JuanP'; 
$password = 'Fide123';

try {

    $pdo = new PDO("oci:dbname=//$host:$port/$service_name", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
}

