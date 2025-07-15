<?php
require_once '../../conexion.php'; // Ajusta si tu conexion.php está en otro lugar
$matricula = $_GET['matricula'] ?? '';
$stmt = $conn->prepare("SELECT nota_control FROM inscripcion_alumno WHERE matricula = ?");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$stmt->bind_result($nota);
$stmt->fetch();
$stmt->close();
echo json_encode(['success'=>true, 'nota'=>$nota ?: '']);
?>