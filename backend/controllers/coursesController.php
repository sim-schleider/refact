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
            http_response_code(500);
            echo json_encode(["error" => "No se pudo crear"]);
        }
    } catch (exception $e) {
        http_response_code(409);
        echo json_encode([  "error" => "No se pudo crear",
                            "errno" => $e->getCode() ]);
    }
}

function handlePut($conn) { // PUT
    $input = json_decode(file_get_contents("php://input"), true);
    try {
        $result = updateCourse($conn, $input['id'], $input['name']);
        if ($result['updated'] > 0) {
            echo json_encode(["message" => "Materia actualizada correctamente"]);
        } else if (courseExists($conn, $input['id'])) {
            echo json_encode(["message" => "Materia sin cambios"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    } catch (exception $e) {
        http_response_code(409);
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
        http_response_code(409);
        echo json_encode([  "error" => "No se pudo eliminar",
                            "errno" => $e->getCode() ]);
    }
}
?>