<?php
// accede a la base de datos - tabla estudiantes

function getAllStudents($conn) { // obtiene todos los estudiantes
    $sql = "SELECT * FROM students"; // guarda en '$sql' una consulta SQL

    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getStudentById($conn, $id) { // obtiene un estudiante
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?"); // guarda la plantilla de una consulta SQL - los '?' son parametros que se agregan luego (por seguridad)
    $stmt->bind_param("i", $id); // agrega el id a la consulta preparada - se explicita que es un entero mediante el tipo 'i' (integer) - esto evita inyecciones
    $stmt->execute(); // ejecuta la consulta
    $result = $stmt->get_result(); // guarda el resultado de la consulta

    return $result->fetch_assoc(); // devuelve un array asociativo a partir del resultado
}

function createStudent($conn, $fullname, $email, $age) { // crea un estudiante
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age); // 's' (string), 'i' (integer)

    $stmt->execute();
    return [
        'inserted' => $stmt->affected_rows,        
        'id' => $conn->insert_id
    ];
}

function updateStudent($conn, $id, $fullname, $email, $age) { // actualiza un estudiante
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    $stmt->execute();

    return ['updated' => $stmt->affected_rows];
}

function deleteStudent($conn, $id) { // elimina un estudiante
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    return ['deleted' => $stmt->affected_rows];
}
?>