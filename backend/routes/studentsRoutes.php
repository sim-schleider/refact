<?php
// conecta la base de datos - incluye el controlador (?) - delega las peticiones según el método

require_once("./config/databaseConfig.php"); // incluye el archivo de configuración de la bd - crea la connexión '$conn' (?)
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsController.php"); // incluye el archivo con la lógica 'handleGet()', 'handlePost()', etc.

/**
 * Ejemplo de como se extiende un archivo de rutas 
 * para casos particulares
 * o validaciones:
 */
routeRequest($conn, [
    'POST' => function($conn) 
    {
        // Validación o lógica extendida
        $input = json_decode(file_get_contents("php://input"), true);
        if (empty($input['fullname'])) 
        {
            http_response_code(400);
            echo json_encode(["error" => "Falta el nombre"]);
            return;
        }
        handlePost($conn);
    }
]);
?>