<?php
// backend/php/verificar_correo_disponible.php
require_once 'db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'is_available' => false, 'message' => 'No se proporcionó correo.'];

if (isset($_GET['correo'])) {
    $correo_a_verificar = trim($_GET['correo']); // Ej: pbernal@unac.edu.mx

    if (empty($correo_a_verificar) || !filter_var($correo_a_verificar, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Formato de correo inválido.';
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT COUNT(*) as total FROM alumno WHERE correo_institucional = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $correo_a_verificar);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            $response['success'] = true;
            if ($row['total'] == 0) {
                $response['is_available'] = true;
                $response['message'] = 'Correo disponible.';
            } else {
                $response['is_available'] = false;
                $response['message'] = 'Correo NO disponible.';
            }
        } else {
            $response['message'] = 'Error al verificar correo: ' . $stmt->error;
            http_response_code(500);
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error al preparar la verificación: ' . $conn->error;
        http_response_code(500);
    }
}

$conn->close();
echo json_encode($response);
?>