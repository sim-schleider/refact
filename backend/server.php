<?php
// punto de ingreso al backend

ini_set('display_errors', 1); // muestra los errores en pantalla - los errores deberían guardarse en un log (*)
error_reporting(E_ALL); // habilita todos los errores a ser mostrados
// esto debe desactivarse en producción ('ini_set('display_errors', 0)')

header("Access-Control-Allow-Origin: *"); // permite a todo frontend acceder al backend - necesario si no están en el mismo dominio - es parte del CORS (?)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // indica al navegador qué métodos son permitidos por el backend
header("Access-Control-Allow-Headers: Content-Type"); // permite al navegador enviar encabezados (?) (JSON) 
// esto permite al fetch() funcionar correctamente

function sendCodeMessage($code, $message = "") { // muestra un mensaje y un código de estado HTTP
    http_response_code($code);
    echo json_encode(["message" => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { // si el método es 'OPTIONS' - 'OPTIONS' se utiliza para obtener información sobre cómo interactuar con el servidor (*)
    sendCodeMessage(200); // código 200 - solicitud procesada con éxito
}

$uri = parse_url($_SERVER['REQUEST_URI']); // guarda la URI del servidor
$query = $uri['query'] ?? ''; // guarda la query (?id=1) de la URI - el operador de coalescencia '??' devuelve el segundo valor si el primero es nulo
parse_str($query, $query_array); // transforma la query en un array
$module = $query_array['module'] ?? null; // guarda el módulo especificado en la query

if (!$module) { // si el módulo no existe
    sendCodeMessage(400, "Módulo no especificado"); // error 400 - petición mal formada
}

if (!preg_match('/^\w+$/', $module)) { // si el módulo tiene caracteres especiales - '\w+' indica que el string contenga únicamente texto ('\w' - words)
    sendCodeMessage(400, "Nombre de módulo inválido");
}

$routeFile = __DIR__ . "/routes/{$module}Routes.php"; // construye la ruta del archivo - es una dirección absoluta (*)

if (file_exists($routeFile)) {
    require_once($routeFile); // incluye el archivo (una vez) que define las rutas - el archivo analiza la URI, el método y decide qué controlador invocar
} else {
    sendCodeMessage(404, "Ruta para el módulo '{$module}' no encontrada"); // error 404 - dirección no encontrada
}
?>