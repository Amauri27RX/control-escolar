<?php
// backend/php/get_programa.php 
require_once '../../db_config.php';

header('Content-Type: application/json');
$response_data = []; // Devolver un array vacío si no hay resultados o error

$nivel_educativo = isset($_GET['nivel']) ? $_GET['nivel'] : null;
$id_institucion = isset($_GET['institucion']) ? $_GET['institucion'] : null;

if ($nivel_educativo && $id_institucion) {
    try {
        // Usar 'nivel_academico' como en la tabla 'programa' de control_escolar
        $sql = "SELECT dgp, nombre_programa, rvoe, modalidades, fecha_rvoe 
                FROM programa 
                WHERE nivel_educativo = ? AND id_institucion = ?
                ORDER BY nombre_programa ASC";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error al preparar consulta de programas: " . $conn->error);
        }
        $stmt->bind_param("ss", $nivel_educativo, $id_institucion);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $response_data[] = $row;
            }
        } else {
            throw new Exception("Error al ejecutar consulta de programas: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        // http_response_code(500); // Podrías querer manejar esto de forma diferente
        // $response_data = ['error' => $e->getMessage()]; // O enviar un array vacío
    }
}

$conn->close();
echo json_encode($response_data); // Devuelve directamente el array de programas
?>