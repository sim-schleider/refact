<?php
// accede a la base de datos - tabla estudiantes-materias

function assignCourseToStudent($conn, $student_id, $course_id, $passed) { // asigna una materia a un estudiante (por id)
    $sql = "INSERT INTO students_courses (student_id, course_id, passed) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $course_id, $passed);
    $stmt->execute();

    return [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
}

function getAllCoursesStudents($conn) { // obtiene todas las relaciones
    $sql = "SELECT students_courses.id,
                students_courses.student_id,
                students_courses.course_id,
                students_courses.passed,
                students.fullname AS student_fullname,
                courses.name AS course_name
            FROM students_courses
            JOIN courses ON students_courses.course_id = courses.id
            JOIN students ON students_courses.student_id = students.id";
    
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getCoursesByStudent($conn, $student_id) { // obtiene una materia basado en el estudiante
    $sql = "SELECT sc.course_id, c.name, sc.passed
            FROM students_courses sc
            JOIN courses c ON sc.course_id = c.id
            WHERE sc.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateStudentCourse($conn, $id, $student_id, $course_id, $passed) { // actualiza la relación
    $sql = "UPDATE students_courses 
            SET student_id = ?, course_id = ?, passed = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $course_id, $passed, $id);
    $stmt->execute();

    return ['updated' => $stmt->affected_rows];
}

function removeStudentCourse($conn, $id) { // elimina la relación
    $sql = "DELETE FROM students_courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return ['deleted' => $stmt->affected_rows];
}

function relationExists($conn, $id) {
    $sql = "SELECT 1 FROM students_courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result(); // (?)

    return $stmt->num_rows > 0;
}
?>