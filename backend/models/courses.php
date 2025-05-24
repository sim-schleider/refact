<?php
function getAllCourses($conn) 
{
    $sql = "SELECT * FROM courses";
    return $conn->query($sql);
}

function getCourseById($conn, $id) 
{
    $sql = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

function createCourse($conn, $name) 
{
    $sql = "INSERT INTO courses (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    return $stmt->execute();
}

function updateCourse($conn, $id, $name) 
{
    $sql = "UPDATE courses SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);
    return $stmt->execute();
}

function deleteCourse($conn, $id) 
{
    $sql = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
