<?php
// backend/php/escuela_options.php
require_once '../../db_config.php'; // Asumiremos que tienes un archivo db_config.php

header('Content-Type: application/json');
$response_data = [];

try {
    $sql = "SELECT id_institucion, nombre FROM institucion ORDER BY nombre ASC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // main.js espera claves 'id' y 'nombre'
            $response_data[] = ['id' => $row['id_institucion'], 'nombre' => $row['nombre']];
        }
    } else {
        throw new Exception("Error al obtener instituciones: " . $conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    // No envíes un JSON aquí si el header ya se envió o si hay otros echos.
    // Es mejor manejar errores de forma consistente.
    // Para simplificar, si hay error, un array vacío o un mensaje de error en JSON.
    // $response_data = ['error' => $e->getMessage()];
    // Para este caso, el JS espera un array, así que un array vacío en caso de error es más simple.
}

$conn->close();
echo json_encode($response_data); // Devuelve directamente el array de instituciones
?>