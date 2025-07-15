<?php
// backend/php/guardar_observacion.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Datos inválidos.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'] ?? null;
    $observacionTexto = $_POST['observacionTexto'] ?? null;
    $tipoObservacion = $_POST['tipoObservacion'] ?? 'General';
    
    // En un sistema real, el autor vendría de la sesión del usuario logueado.
    // Por ahora, lo dejaremos como un valor fijo.
    $autor = 'Control Escolar'; 

    if (empty($matricula) || empty($observacionTexto)) {
        $response['message'] = 'La matrícula y el texto de la observación son requeridos.';
        echo json_encode($response);
        exit();
    }

    try {
        $sql = "INSERT INTO observaciones_alumno (matricula, observacion, tipo, autor) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error al preparar la inserción: " . $conn->error);
        }

        $stmt->bind_param("ssss", $matricula, $observacionTexto, $tipoObservacion, $autor);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Observación guardada exitosamente.';
        } else {
            throw new Exception("Error al guardar la observación: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        http_response_code(500);
        $response['message'] = $e->getMessage();
    }

    $conn->close();
    echo json_encode($response);
}
?>