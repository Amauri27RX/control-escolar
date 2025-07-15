<?php
require_once __DIR__ . '/../../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y validar datos
$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$required = ['id_pago', 'monto_pagado', 'fecha_pago', 'metodo_pago'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Campo $field es requerido"]);
        exit;
    }
}

// Preparar variables para bind_param
$id_pago = (int)$data['id_pago'];
$monto_pagado = (float)$data['monto_pagado'];
$fecha_pago = $data['fecha_pago'];
$metodo_pago = $data['metodo_pago'];
$referencia_pago = $data['referencia_pago'] ?? null;

try {
    $conn->begin_transaction();

    // Registrar el pago
    $query = "UPDATE planpagos SET 
                monto_pagado = ?,
                fecha_pago = ?,
                metodo_pago = ?,
                referencia_pago = ?,
                estado_pago = 'Pagado'
              WHERE id_pago = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }

    // Usar variables definidas en bind_param
    $stmt->bind_param("dsssi", 
        $monto_pagado,
        $fecha_pago,
        $metodo_pago,
        $referencia_pago,
        $id_pago
    );

    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }

    // Registrar en historial (opcional)
    $queryHistorial = "INSERT INTO historial_pagos 
                      (id_pago, matricula, monto, fecha_pago, metodo_pago, referencia)
                      SELECT id_pago, matricula, ?, ?, ?, ?
                      FROM planpagos
                      WHERE id_pago = ?";
    
    $stmtHistorial = $conn->prepare($queryHistorial);
    if (!$stmtHistorial) {
        throw new Exception('Error al preparar historial: ' . $conn->error);
    }

    $stmtHistorial->bind_param("dsssi", 
        $monto_pagado,
        $fecha_pago,
        $metodo_pago,
        $referencia_pago,
        $id_pago
    );

    if (!$stmtHistorial->execute()) {
        throw new Exception('Error al registrar historial: ' . $stmtHistorial->error);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>