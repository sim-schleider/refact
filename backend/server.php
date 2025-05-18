<?php
// punto de ingreso al backend

ini_set('display_errors', 1); // muestra los errores en pantalla - (*) esto debería pasar a 'ini_set('display_errors', 0)' y los errores deberían guardarse en un log
error_reporting(E_ALL); // habilita todos los errores a ser mostrados
// esto debe desactivarse en producción 

header("Access-Control-Allow-Origin: *"); // permite a todo frontend acceder al backend - necesario si no están en el mismo dominio - es parte del CORS (?)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // indica al navegador qué métodos son permitidos por el backend
header("Access-Control-Allow-Headers: Content-Type"); // permite al navegador enviar encabezados (?) (JSON) 
// esto permite al fetch() funcionar correctamente

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once("./routes/studentsRoutes.php"); // incluye el archivo (una vez) que define las rutas - el archivo analiza la URL, el método y decide qué controlador (?) invocar - (*) debería validarse
?>