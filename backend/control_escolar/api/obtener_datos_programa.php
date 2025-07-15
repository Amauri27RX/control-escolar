<?php
// obtener_datos_programa.php

// --- Configuración de la Base de Datos ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'control_escolar'; // Asegúrate que esta sea la base de datos correcta

header('Content-Type: application/json');
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8mb4");

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'get_instituciones') {
    $sql = "SELECT id_institucion, nombre FROM institucion ORDER BY nombre ASC";
    $result = $conn->query($sql);
    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode(['success' => true, 'instituciones' => $items]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al obtener instituciones: ' . $conn->error]);
    }
} elseif ($action === 'get_todos_los_programas') { // Nueva acción para todos los programas
    $sql = "SELECT DISTINCT dgp, nombre_programa FROM programa ORDER BY nombre_programa ASC";
    $result = $conn->query($sql);
    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode(['success' => true, 'programas' => $items]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al obtener todos los programas: ' . $conn->error]);
    }
} elseif ($action === 'get_todas_las_generaciones') { // Nueva acción para todas las generaciones
    $sql = "SELECT DISTINCT ciclo_inicio FROM inscripcion_alumno WHERE ciclo_inicio IS NOT NULL AND ciclo_inicio != '' ORDER BY ciclo_inicio DESC";
    $result = $conn->query($sql);
    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row; // $row será ['ciclo_inicio' => 'valor']
        }
        echo json_encode(['success' => true, 'generaciones' => $items]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al obtener las generaciones: ' . $conn->error]);
    }
} elseif ($action === 'get_programas_por_nivel') { // Esta acción es para el formulario de inscripción
    $nivel_educativo = isset($_GET['nivel']) ? $_GET['nivel'] : '';
    $id_institucion = isset($_GET['institucion']) ? $_GET['institucion'] : '';

    if (empty($nivel_educativo) || empty($id_institucion)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nivel educativo e institución son requeridos para esta acción.']);
        exit();
    }
    // Recuerda que en 'control_escolar.sql', la columna es 'nivel_academico'
    $sql = "SELECT dgp, nombre_programa, rvoe, fecha_rvoe FROM programa WHERE nivel_educativo = ? AND id_institucion = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) { /* ... manejo de error ... */ exit(); }
    $stmt->bind_param("ss", $nivel_educativo, $id_institucion);
    if ($stmt->execute()) {
        $result_stmt = $stmt->get_result();
        $programas = [];
        while ($row = $result_stmt->fetch_assoc()) {
            $programas[] = $row;
        }
        echo json_encode(['success' => true, 'programas' => $programas]);
    } else { /* ... manejo de error ... */ }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Acción no válida para obtener_datos_programa.php']);
}

$conn->close();
?>