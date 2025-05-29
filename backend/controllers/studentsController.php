<?php
// procesa las solicitudes HTTP - estudiantes

require_once("./models/students.php"); // importa las funciones de acceso a la base de datos

function handleGet($conn) { // GET - obtiene valores
    if (isset($_GET['id'])) { // revisa si el id es obtenido
        $result = getStudentById($conn, $_GET['id']); // obtiene la fila con esa id
        echo json_encode($result->fetch_assoc()); // muestra la fila como un json - fetch_assoc() obtiene los datos y los tranforma en un array (asociativo)
    } else {
        $result = getAllStudents($conn); // obtiene todos los estudiantes
        $data = []; // crea un array vacío
        while ($row = $result->fetch_assoc()) { // mientras la fila obtenida no sea nula
            $data[] = $row; // obtiene las filas
        }
        echo json_encode($data); // muestra las filas como un json
    }
}

function handlePost($conn) { // POST - crea valores
    $input = json_decode(file_get_contents("php://input"), true); // obtiene la solicitud y la convierte en un array
    if (createStudent($conn, $input['fullname'], $input['email'], $input['age'])) { // crea un estudiante con los datos ingresados - (*) podrían validarse
        echo json_encode(["message" => "Estudiante agregado correctamente"]); // (?) muestra un mensaje
    } else {
        http_response_code(500); // marca un error
        echo json_encode(["error" => "No se pudo agregar"]); 
    }
}

function handlePut($conn) { // PUT - actualiza valores
    $input = json_decode(file_get_contents("php://input"), true); 
    if (updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age'])) { // actualiza un estudiante con los datos ingresados
        echo json_encode(["message" => "Actualizado correctamente"]); 
    } else {
        http_response_code(500); 
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) { // DELETE - elimina valores
    $input = json_decode(file_get_contents("php://input"), true); 
    try {
        deleteStudent($conn, $input['id']);
        echo json_encode(["message" => "Eliminado correctamente"]); 
    } catch (exception $e) { // detecta errores enviados por el sistema de base de datos
        http_response_code(409); // error 409 - error con el estado actual del recurso
        echo json_encode([  "error" => "No se pudo eliminar",
                            "errno" => $e->getCode() ]); // obtiene el código del error de MySQL
    }
}
?>