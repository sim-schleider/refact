<?php
// procesa las solicitudes HTTP - materias

require_once("./models/courses.php");

function handleGet($conn) { // GET
    if (isset($_GET['id'])) {
        $result = getCourseById($conn, $_GET['id']);
        echo json_encode($result->fetch_assoc());
    } else {
        $result = getAllCourses($conn);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}

function handlePost($conn) { // POST
    $input = json_decode(file_get_contents("php://input"), true);
    if (createCourse($conn, $input['name'])) {
        echo json_encode(["message" => "Materia creada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear"]);
    }
}

function handlePut($conn) { // PUT
    $input = json_decode(file_get_contents("php://input"), true);
    if (updateCourse($conn, $input['id'], $input['name'])) {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) { // DELETE
    $input = json_decode(file_get_contents("php://input"), true);
    try {
        deleteCourse($conn, $input['id']);
        echo json_encode(["message" => "Eliminado correctamente"]); 
    } catch (exception $e) {
        http_response_code(409);
        echo json_encode([  "error" => "No se pudo eliminar",
                            "errno" => $e->getCode() ]);
    }
}
?>