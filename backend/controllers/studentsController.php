<?php
// procesa las solicitudes HTTP - estudiantes

require_once("./models/students.php"); // importa las funciones de acceso a la base de datos

function handleGet($conn) { // GET - obtiene valores
    $input = json_decode(file_get_contents("php://input"), true); // (?) ¿está bien?
    if (isset($input['id'])) { // revisa si el id es obtenido
        $student = getStudentById($conn, $input['id']); // obtiene la fila con esa id
        echo json_encode($student); // muestra la fila como un json - fetch_assoc() obtiene los datos y los tranforma en un array (asociativo)
    } else {
        $students = getAllStudents($conn); // obtiene todos los estudiantes
        echo json_encode($students); // muestra las filas como un json
    }
}

function handlePost($conn) { // POST - crea valores
    $input = json_decode(file_get_contents("php://input"), true); // obtiene la solicitud y la convierte en un array
    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']); // crea un estudiante con los datos ingresados - (*) podrían validarse
    if ($result['inserted'] > 0) {
        echo json_encode(["message" => "Estudiante agregado correctamente"]); // envía un mensaje al frontend accesible via network
    } else {
        http_response_code(500); // marca un error
        echo json_encode(["error" => "No se pudo agregar"]); 
    }
}

function handlePut($conn) { // PUT - actualiza valores
    $input = json_decode(file_get_contents("php://input"), true);
    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0) { // actualiza un estudiante con los datos ingresados
        echo json_encode(["message" => "Actualizado correctamente"]); 
    } else if (studentExists($conn, $input['id'])) {
            echo json_encode(["message" => "Estudiante sin cambios"]);
    } else {
        http_response_code(500); 
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) { // DELETE - elimina valores
    $input = json_decode(file_get_contents("php://input"), true); 
    try {
        $result = deleteStudent($conn, $input['id']);
        if ($result['deleted'] > 0)
            echo json_encode(["message" => "Eliminado correctamente"]);
        else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    } catch (exception $e) { // detecta errores enviados por el sistema de base de datos
        http_response_code(409); // error 409 - error con el estado actual del recurso
        echo json_encode([  "error" => "No se pudo eliminar",
                            "errno" => $e->getCode() ]); // obtiene el código del error de MySQL
    }
}
?>