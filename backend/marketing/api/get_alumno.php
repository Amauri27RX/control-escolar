<?php
header("Content-Type: application/json");
include("../../conexion.php");

$matricula = $_GET['matricula'] ?? '';

$query = "SELECT * FROM alumno WHERE matricula = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$alumno = $stmt->get_result()->fetch_assoc();

if ($alumno) {
    $programa = [];
    if (!empty($alumno['dgp'])) {
        $query = "SELECT * FROM programa WHERE dgp = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $alumno['dgp']);
        $stmt->execute();
        $programa = $stmt->get_result()->fetch_assoc();
    }
    
    echo json_encode([
        'success' => true,
        'alumno' => $alumno,
        'programa' => $programa
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Alumno no encontrado'
    ]);
}

$conn->close();
?>