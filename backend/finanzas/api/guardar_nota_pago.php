<?php
require_once '../../conexion.php'; // Ajusta según tu estructura
$data = json_decode(file_get_contents('php://input'), true);
$matricula = $data['matricula'] ?? '';
$nota = $data['nota'] ?? '';
$stmt = $conn->prepare("UPDATE inscripcion_alumno SET nota_control=? WHERE matricula=?");
$stmt->bind_param("ss", $nota, $matricula);
$success = $stmt->execute();
$stmt->close();
echo json_encode(['success' => $success]);
?>