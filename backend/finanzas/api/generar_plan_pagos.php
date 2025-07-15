<?php
require_once '../../conexion.php'; // Archivo con la conexión a la DB

function generarPlanPagos($matricula) {
    global $conn;
    
    // Obtener información del alumno y programa
    $query = "SELECT a.dgp, p.nivel_educativo 
              FROM alumno a
              JOIN programa p ON a.dgp = p.dgp
              WHERE a.matricula = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno = $result->fetch_assoc();
    
    if (!$alumno) {
        return ["success" => false, "message" => "Alumno no encontrado"];
    }
    
    // Determinar número de mensualidades según nivel educativo
    switch($alumno['nivel_educativo']) {
        case 'Especialidad': $meses = 12; break;
        case 'Licenciatura': $meses = 36; break;
        case 'Maestria': $meses = 16; break;
        case 'Doctorado': $meses = 24; break;
        default: $meses = 12;
    }
    
    // Obtener costos estándar
    $query = "SELECT costo_inscripcion_std, costo_colegiatura_std, costo_reinscripcion_std
              FROM costospornivel
              WHERE nivel_educativo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $alumno['nivel_educativo']);
    $stmt->execute();
    $result = $stmt->get_result();
    $costos = $result->fetch_assoc();
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Insertar pago de inscripción
        $query = "INSERT INTO planpagos (matricula, tipo_pago, concepto, monto_regular, fecha_vencimiento, estado_pago)
                  VALUES (?, 'Inscripción', CONCAT('Inscripción ', ?), ?, DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Pendiente')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssd", $matricula, $alumno['nivel_educativo'], $costos['costo_inscripcion_std']);
        $stmt->execute();
        
        // Insertar mensualidades
        $fecha_vencimiento = date('Y-m-d', strtotime('+1 month'));
        for ($i = 1; $i <= $meses; $i++) {
            $query = "INSERT INTO planpagos (matricula, tipo_pago, numero_mensualidad, concepto, 
                      monto_regular, fecha_vencimiento, estado_pago)
                      VALUES (?, 'Mensualidad', ?, CONCAT('Mensualidad ', ?, '/', ?, ' - ', ?), 
                      ?, ?, 'Pendiente')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("siisds", $matricula, $i, $i, $meses, $alumno['nivel_educativo'], 
                             $costos['costo_colegiatura_std'], $fecha_vencimiento);
            $stmt->execute();
            
            $fecha_vencimiento = date('Y-m-d', strtotime($fecha_vencimiento . ' +1 month'));
        }
        
        // Insertar reinscripción (si aplica)
        if (in_array($alumno['nivel_educativo'], ['Licenciatura', 'Maestria', 'Doctorado'])) {
            $query = "INSERT INTO planpagos (matricula, tipo_pago, concepto, monto_regular, 
                      fecha_vencimiento, estado_pago)
                      VALUES (?, 'Reinscripción', CONCAT('Reinscripción ', ?), 
                      ?, DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 'Pendiente')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssd", $matricula, $alumno['nivel_educativo'], $costos['costo_reinscripcion_std']);
            $stmt->execute();
        }
        
        $conn->commit();
        return ["success" => true, "message" => "Plan de pagos generado correctamente"];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error al generar plan de pagos: " . $e->getMessage()];
    }
}

// Ejemplo de uso:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matricula'])) {
    $result = generarPlanPagos($_POST['matricula']);
    echo json_encode($result);
    exit;
}
?>