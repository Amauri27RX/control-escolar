<?php
require_once '../../conexion.php';

header('Content-Type: application/json');

if (!isset($_GET['matricula'])) {
    echo json_encode(['error' => 'Matrícula no especificada']);
    exit;
}

$matricula = $_GET['matricula'];

try {
    $query = "SELECT 
                SUM(monto_regular) AS total_pagar,
                SUM(CASE WHEN estado_pago = 'Pagado' THEN monto_pagado ELSE 0 END) AS total_pagado,
                SUM(CASE WHEN estado_pago != 'Pagado' THEN monto_regular ELSE 0 END) AS saldo_pendiente
              FROM planpagos
              WHERE matricula = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Asegurarnos que los valores son numéricos
    $response = [
        'total_pagar' => (float)$data['total_pagar'],
        'total_pagado' => (float)$data['total_pagado'],
        'saldo_pendiente' => (float)$data['saldo_pendiente']
    ];

    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>