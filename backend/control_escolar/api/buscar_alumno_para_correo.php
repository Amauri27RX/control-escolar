<?php
// backend/php/buscar_alumno_para_correo.php
require_once 'db_config.php'; // Contiene la conexión $conn

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'No se proporcionó término de búsqueda.', 'alumnos' => []];

if (isset($_GET['termino'])) {
    $termino = trim($_GET['termino']);

    if (empty($termino)) {
        $response['message'] = 'El término de búsqueda no puede estar vacío.';
        echo json_encode($response);
        exit();
    }

    // Intentar buscar por matrícula primero, luego por nombre/apellidos
    $sql = "SELECT matricula, nombre, apellido_paterno, apellido_materno, correo_institucional
            FROM alumno 
            WHERE matricula = ? 
            OR CONCAT_WS(' ', nombre, apellido_paterno, apellido_materno) LIKE ?
            OR CONCAT_WS(' ', nombre, apellido_paterno) LIKE ? 
            OR nombre LIKE ? 
            OR apellido_paterno LIKE ?
            OR apellido_materno LIKE ?
            LIMIT 10"; // Limitar resultados para búsquedas amplias

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $like_termino = "%" . $termino . "%";
        $stmt->bind_param("ssssss", $termino, $like_termino, $like_termino, $like_termino, $like_termino, $like_termino);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $alumnos_encontrados = [];
            while ($row = $result->fetch_assoc()) {
                $alumnos_encontrados[] = $row;
            }
            
            if (count($alumnos_encontrados) > 0) {
                $response['success'] = true;
                $response['alumnos'] = $alumnos_encontrados; // Devolver una lista, JS elegirá si hay múltiples
                $response['message'] = 'Alumnos encontrados.';
            } else {
                $response['message'] = 'No se encontraron alumnos con ese criterio.';
            }
        } else {
            $response['message'] = 'Error al ejecutar la búsqueda: ' . $stmt->error;
            http_response_code(500);
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error al preparar la búsqueda: ' . $conn->error;
        http_response_code(500);
    }
}

$conn->close();
echo json_encode($response);
?>