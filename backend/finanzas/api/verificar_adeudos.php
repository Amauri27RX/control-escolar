<?php
require_once '../../conexion.php';

header('Content-Type: application/json');

$matricula = $_GET['matricula'] ?? '';

try {
    // Verificar si hay pagos pendientes o vencidos
    $query = "SELECT COUNT(*) as pendientes 
              FROM planpagos 
              WHERE matricula = ? 
              AND estado_pago IN ('Pendiente', 'Vencido')";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'tiene_adeudos' => $row['pendientes'] > 0,
        'pagos_pendientes' => $row['pendientes']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>