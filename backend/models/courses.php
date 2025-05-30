<?php
// accede a la base de datos - tabla materias

function getAllCourses($conn) { // obtiene todas las materias
    $sql = "SELECT * FROM courses";
    
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getCourseById($conn, $id) { // obtiene una materia
    $sql = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc(); 
}

function createCourse($conn, $name) { // crea una materia
    $sql = "INSERT INTO courses (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();

    return [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
}

function updateCourse($conn, $id, $name) { // actualiza una materia
    $sql = "UPDATE courses SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();

    return ['updated' => $stmt->affected_rows];
}

function deleteCourse($conn, $id) { // elimina una materia
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    return ['deleted' => $stmt->affected_rows];
}
?>
