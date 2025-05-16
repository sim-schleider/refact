<?php
// conecta la base de datos - incluye el controlador (?) - delega las peticiones según el método

require_once("./config/databaseConfig.php"); // incluye el archivo de configuración de la bd - crea la connexión '$conn' (?)
require_once("./controllers/studentsController.php"); // incluye el archivo con la lógica 'handleGet()', 'handlePost()', etc.

switch ($_SERVER['REQUEST_METHOD']) { // lee el método de la solicitud mediante '$_SERVER['REQUEST_METHOD']'
    case 'GET': 
        handleGet($conn); // (*) para trabajar con distintos módulos, se debe cambiar la forma de tratar cada uno
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        http_response_code(405); // si el método no está permitido, responde con el error 405 
        echo json_encode(["error" => "Método no permitido"]); // si el método no está permitido, deja un mensaje JSON
        break;
}
?>