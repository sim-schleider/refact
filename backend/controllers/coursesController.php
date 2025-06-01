<?php
// procesa las solicitudes HTTP - materias

require_once("./models/courses.php");

function handleGet($conn) { // GET
    $input = json_decode(file_get_contents("php://input"), true);
    if (isset($input['id'])) {
        $course = getCourseById($conn, $_GET['id']);
        echo json_encode($course);
    } else {
        $courses = getAllCourses($conn);
        echo json_encode($courses);
    }
}

function handlePost($conn) { // POST
    $input = json_decode(file_get_contents("php://input"), true);
    try {
        $result = createCourse($conn, $input['name']);
        if ($result['inserted'] > 0) {
            echo json_encode(["message" => "Materia creada correctamente"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo crear"]);
        }
    } catch (exception $e) {
        http_response_code(500);
        echo json_encode([  "error" => "No se pudo crear",
                            "errno" => $e->getCode() ]);
    }
}

function handlePut($conn) { // PUT
    $input = json_decode(file_get_contents("php://input"), true);
    try {
        $result = updateCourse($conn, $input['id'], $input['name']);
        if ($result['updated'] > 0) { // caso 1: la sentencia actualizó al menos una materia
            echo json_encode(["message" => "Materia actualizada correctamente"]);
        } else if (courseExists($conn, $input['id'])) { // caso 2: la sentencia no actualizó la materia porque se mantuvo igual
            echo json_encode(["message" => "Materia sin cambios"]);
        } else { // caso 3: la sentencia no actualizó la materia por alguna razón, pero no ocurrió ningún error interno - ejemplo: la id no existe
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    } catch (exception $e) { // caso 4: ocurrió un error al realizar la sentencia - ejemplo: el nombre ya existe
        http_response_code(500);
        echo json_encode([  "error" => "No se pudo actualizar",
                            "errno" => $e->getCode() ]);
    }
}

function handleDelete($conn) { // DELETE
    $input = json_decode(file_get_contents("php://input"), true);
    try {
        $result = deleteCourse($conn, $input['id']);
        if ($result['deleted'] > 0)
            echo json_encode(["message" => "Eliminado correctamente"]);
        else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo eliminar"]);
        }
    } catch (exception $e) {
        http_response_code(500);
        echo json_encode([  "error" => "No se pudo eliminar",
                            "errno" => $e->getCode() ]);
    }
}
?>