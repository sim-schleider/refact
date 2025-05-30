<?php
// procesa las solicitudes HTTP - estudiantes-materias

require_once("./models/studentsCourses.php");

function handleGet($conn) { // GET
    $studentsCourses = getAllCoursesStudents($conn);
    echo json_encode($studentsCourses);
}

function handlePost($conn) { // POST
    $input = json_decode(file_get_contents("php://input"), true);
    try {
        $result = assignCourseToStudent($conn, $input['student_id'], $input['course_id'], $input['passed']);
        if ($result['inserted'] > 0) {
            echo json_encode(["message" => "Asignación realizada"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Error al asignar"]);
        }
    } catch (exception $e) {
        http_response_code(500);
        echo json_encode([  "error" => "Error al asignar",
                            "errno" => $e->getCode() ]);
    }
}

function handlePut($conn) { // PUT
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['course_id'], $input['passed'])) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    try {
        $result = updateStudentCourse($conn, $input['id'], $input['student_id'], $input['course_id'], $input['passed']);
        if ($result['updated'] > 0) {
            echo json_encode(["message" => "Actualización correcta"]);
        } else if (relationExists($conn, $input['id'])) {
            echo json_encode(["message" => "Relación sin cambios"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se pudo actualizar"]);
        }
    } catch (exception $e) {
        http_response_code(500);
        echo json_encode([  "error" => "No se pudo actualizar",
                            "errno" => $e->getCode() ]);
    }
}

function handleDelete($conn) { // DELETE
    $input = json_decode(file_get_contents("php://input"), true);
    $result = removeStudentCourse($conn, $input['id']);
    if ($result['deleted'] > 0) {
        echo json_encode(["message" => "Relación eliminada"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
