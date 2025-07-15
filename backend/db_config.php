<?php
// backend/php/db_config.php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'control_escolar';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    // En un script real, no harías echo aquí, sino que lo manejarías en el script que lo incluye.
    // Pero para que funcione rápidamente:
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos desde db_config: ' . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8mb4");
// No cierres la conexión aquí, se usará en el script que lo incluye
?>