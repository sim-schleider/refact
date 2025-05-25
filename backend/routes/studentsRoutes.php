<?php
// controla las peticiones - tabla estudiantes

require_once("./config/databaseConfig.php"); // incluye el archivo de configuración de la bd - crea la connexión '$conn'
require_once("./routes/routesFactory.php"); // incluye el archivo general que delega las peticiones
require_once("./controllers/studentsController.php"); // incluye el archivo con la lógica 'handleGet()', 'handlePost()', etc.

routeRequest($conn, [ // ejemplo específico de validación
    'POST' => function($conn) { // sobreescribe la clave 'POST' en el array de handlers
        $input = json_decode(file_get_contents("php://input"), true); // obtiene el resultado del formulario - 'file_get_contents' obtiene el JSON completo y 'json_decode' lo transforma en un array
        if (empty($input['fullname'])) { // si el nombre no fue ingresado
            http_response_code(400);
            echo json_encode(["error" => "Falta el nombre"]);
            return;
        }
        handlePost($conn); // de lo contrario, invoca el handler del controlador
    }
]);
?>