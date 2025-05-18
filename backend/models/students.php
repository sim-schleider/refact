<?php
// accede a la base de datos

function getAllStudents($conn) { // obtiene todos los estudiantes
    $sql = "SELECT * FROM students"; // guarda en '$sql' una consulta SQL
    return $conn->query($sql); // devuelve el resultado de la consulta - no hay ingreso por parte del usuario por lo que no hay riesgo de inyección
}

function getStudentById($conn, $id) { // obtiene un estudiante
    $sql = "SELECT * FROM students WHERE id = ?"; // guarda en '$sql' la plantilla de una consulta SQL - los '?' son parametros que se agregan luego (por seguridad)
    $stmt = $conn->prepare($sql); // prepara la consulta y la guarda en $stmt
    $stmt->bind_param("i", $id); // agrega el id a la consulta preparada - se explicita que es un entero mediante el tipo 'i' (integer) - esto evita inyecciones
    $stmt->execute(); // ejecuta la consulta
    return $stmt->get_result(); // devuelve el resultado de la consulta
}

function createStudent($conn, $fullname, $email, $age) { // crea un estudiante
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age); // 's' (string), 'i' (integer)
    return $stmt->execute();
}

function updateStudent($conn, $id, $fullname, $email, $age) { // actualiza un estudiante
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    return $stmt->execute();
}

function deleteStudent($conn, $id) { // elimina un estudiante
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>