<?php
// obtener_lista_alumnos.php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'control_escolar';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'No se pudo obtener la lista de alumnos.', 'alumnos' => []];

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) { /* ... manejo de error ... */ exit(); }
$conn->set_charset("utf8mb4");

// Obtener parámetros de filtro
$id_institucion_filtro = isset($_GET['institucion']) ? trim($_GET['institucion']) : '';
$dgp_programa_filtro = isset($_GET['programa']) ? trim($_GET['programa']) : '';
$generacion_filtro = isset($_GET['generacion']) ? trim($_GET['generacion']) : ''; // Este sería ia.ciclo_inicio
$termino_busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

$sql = "SELECT
            a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, a.curp,
            p.nombre_programa, a.fecha_inicio,
            ia.ciclo_inicio, ia.ciclo_fin, ia.estatus_alumno,
            aip.telefono AS telefono_personal,
            a.correo_institucional, aip.correo_personal
        FROM alumno a
        LEFT JOIN programa p ON a.dgp = p.dgp
        LEFT JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
        LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula";

$conditions = [];
$params = [];
$types = "";

if (!empty($id_institucion_filtro)) {
    $conditions[] = "p.id_institucion = ?";
    $params[] = $id_institucion_filtro;
    $types .= "s";
}
if (!empty($dgp_programa_filtro)) {
    $conditions[] = "a.dgp = ?";
    $params[] = $dgp_programa_filtro;
    $types .= "s";
}
if (!empty($generacion_filtro)) {
    $conditions[] = "ia.ciclo_inicio = ?"; // Filtrar por ciclo_inicio para la generación
    $params[] = $generacion_filtro;
    $types .= "s";
}
if (!empty($termino_busqueda)) {
    $like_termino = "%" . $termino_busqueda . "%";
    $conditions[] = "(a.nombre LIKE ? OR a.apellido_paterno LIKE ? OR a.apellido_materno LIKE ? OR a.matricula LIKE ? OR a.curp LIKE ?)";
    for ($i = 0; $i < 5; $i++) { // Añadir el término 5 veces para los 5 campos LIKE
        $params[] = $like_termino;
        $types .= "s";
    }
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY a.apellido_paterno ASC, a.apellido_materno ASC, a.nombre ASC";
// Considerar añadir LIMIT y OFFSET para paginación en el futuro

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $alumnos_array = [];
        while ($row = $result->fetch_assoc()) {
            $alumnos_array[] = $row;
        }
        $response['success'] = true;
        $response['alumnos'] = $alumnos_array;
        $response['message'] = 'Alumnos obtenidos exitosamente.';
        if (empty($alumnos_array)) {
            $response['message'] = 'No hay alumnos que coincidan con los criterios de búsqueda.';
        }
    } else {
        $response['message'] = 'Error al ejecutar la consulta de alumnos: ' . $stmt->error;
        http_response_code(500);
    }
    $stmt->close();
} else {
    $response['message'] = 'Error al preparar la consulta de alumnos: ' . $conn->error;
    http_response_code(500);
}

$conn->close();
echo json_encode($response);
?>