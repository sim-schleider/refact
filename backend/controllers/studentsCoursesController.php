<?php
require_once("./models/studentsCourses.php");

function handleGet($conn) 
{
    $result = getAllCoursesStudents($conn);
    $data = [];
    while ($row = $result->fetch_assoc()) 
    {
        $data[] = $row;
    }
    echo json_encode($data);
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (assignCourseToStudent($conn, $input['student_id'], $input['course_id'], $input['passed'])) 
    {
        echo json_encode(["message" => "Asignación realizada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['course_id'], $input['passed'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    if (updateStudentCourse($conn, $input['id'], $input['student_id'], $input['course_id'], $input['passed'])) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (removeStudentCourse($conn, $input['id'])) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
