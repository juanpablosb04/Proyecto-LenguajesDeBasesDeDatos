<?php

$host = 'localhost';
$port = '1521';
$service_name = 'orcl';
$user = 'JuanP';
$password = 'Fide123';


$connection_string = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port)))(CONNECT_DATA=(SERVICE_NAME=$service_name)))";

try {
    
    $conn = oci_connect($user, $password, $connection_string);
    
    if (!$conn) {
        $e = oci_error();
        echo json_encode(["error" => "Error de conexión: " . $e['message']]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
}


