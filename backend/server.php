<?php
// punto de ingreso al backend

ini_set('display_errors', 1); // muestra los errores en pantalla - (*) esto debería pasar a 'ini_set('display_errors', 0)' y los errores deberían guardarse en un log
error_reporting(E_ALL); // habilita todos los errores a ser mostrados
// esto debe desactivarse en producción 

header("Access-Control-Allow-Origin: *"); // permite a todo frontend acceder al backend - necesario si no están en el mismo dominio - es parte del CORS (?)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // indica al navegador qué métodos son permitidos por el backend
header("Access-Control-Allow-Headers: Content-Type"); // permite al navegador enviar encabezados (?) (JSON) 
// esto permite al fetch() funcionar correctamente

function sendCodeMessage($code, $message = "") {
    http_response_code($code);
    echo json_encode(["message" => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendCodeMessage(200);
}

// Obtener el módulo desde la query string
$uri = parse_url($_SERVER['REQUEST_URI']);
$query = $uri['query'] ?? '';
parse_str($query, $query_array);
$module = $query_array['module'] ?? null;

// Validación de existencia del módulo
if (!$module) {
    sendCodeMessage(400, "Módulo no especificado");
}

// Validación de caracteres seguros: solo letras, números y guiones bajos
if (!preg_match('/^\w+$/', $module)) {
    sendCodeMessage(400, "Nombre de módulo inválido");
}

// Buscar el archivo de ruta correspondiente
$routeFile = __DIR__ . "/routes/{$module}Routes.php";

if (file_exists($routeFile)) {
    require_once($routeFile); // incluye el archivo (una vez) que define las rutas - el archivo analiza la URL, el método y decide qué controlador (?) invocar
} else {
    sendCodeMessage(404, "Ruta para el módulo '{$module}' no encontrada");
}
?>