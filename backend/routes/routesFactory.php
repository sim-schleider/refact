<?php
// delega las peticiones generales

function routeRequest($conn, $customHandlers = [], $prefix = 'handle') { // controla los métodos - $prefix es para los nombres de funciones (handleGet, handlePost, etc.)
    $method = $_SERVER['REQUEST_METHOD']; // guarda el método que fue utilizado para acceder a la página

    $defaultHandlers = [ // guarda valores (handleGet) y los asigna a una clave (GET) - una clave es un índice no numérico
        'GET'    => $prefix . 'Get', 
        'POST'   => $prefix . 'Post',
        'PUT'    => $prefix . 'Put', 
        'DELETE' => $prefix . 'Delete'
    ];

    $handlers = array_merge($defaultHandlers, $customHandlers); // en el caso de haber '$customHandlers', sobreescribe el array '$defaultHandlers'

    if (!isset($handlers[$method])) { // si el método no existe en el array (la clave corresponde a un valor nulo)
        http_response_code(405); // error 405 - método no permitido
        echo json_encode(["error" => "Método $method no permitido"]);
        return;
    }

    $handler = $handlers[$method]; // de lo contrario, guarda el método correspondiente

    if (is_callable($handler)) { // si la función puede ser llamada (handleGet, handlePost, etc.)
        $handler($conn); // invoca la función
    } else {
        http_response_code(500); // error 500 - error interno de servidor
        echo json_encode(["error" => "Handler para $method no es válido"]);
    }
}
?>