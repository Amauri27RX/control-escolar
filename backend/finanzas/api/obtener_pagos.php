<?php
require_once '../../conexion.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['matricula']) || empty($_GET['matricula'])) {
        throw new Exception('Matrícula no especificada');
    }

    $matricula = $conn->real_escape_string($_GET['matricula']);
    
    $query = "SELECT 
                id_pago,
                concepto,
                monto_regular,
                DATE_FORMAT(fecha_vencimiento, '%Y-%m-%d') as fecha_vencimiento,
                estado_pago,
                DATE_FORMAT(fecha_pago, '%Y-%m-%d') as fecha_pago,
                monto_pagado
              FROM planpagos 
              WHERE matricula = '$matricula'
              ORDER BY fecha_vencimiento";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Error en la consulta: ' . $conn->error);
    }
    
    $pagos = [];
    while ($row = $result->fetch_assoc()) {
        // Asegurar que los montos son numéricos
        $row['monto_regular'] = (float)$row['monto_regular'];
        $row['monto_pagado'] = $row['monto_pagado'] ? (float)$row['monto_pagado'] : null;
        $pagos[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $pagos]); // <-- Asegúrate de que esta línea esté presente y correcta

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]); // <-- Y esta para errores
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>