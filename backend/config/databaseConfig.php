<?php
// establece la conexión con MySQLi

$host = "localhost"; // (*) para ocultar credenciales se puede utilizar '$host = getenv("DB_HOST")'
$user = "students_user";
$password = "12345";
$database = "students_db";
// credenciales de conexión

$conn = new mysqli($host, $user, $password, $database); // crea un objeto con las credenciales

if ($conn->connect_error) { // verifica si hubo un error de conexión
    http_response_code(500);
    die(json_encode(["error" => "Database connection failed"]));
}
?>