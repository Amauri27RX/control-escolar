<?php
// backend/php/obtener_reporte_carreras.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error al obtener el reporte.'];
$reporte_data = [];

try {
    // --- 1. Definir Filtros y Rango de Fechas ---
    $id_institucion = $_GET['institucion'] ?? '';
    $nivel_educativo = $_GET['nivel'] ?? '';
    $periodo = $_GET['periodo'] ?? 'mes_actual';
    $fecha_inicio = $_GET['fecha_inicio'] ?? '';
    $fecha_fin = $_GET['fecha_fin'] ?? '';

    $fecha_inicio_sql = '';
    $fecha_fin_sql = '';

    switch ($periodo) {
        case 'mes_pasado':
            $fecha_inicio_sql = date('Y-m-01', strtotime('first day of last month'));
            $fecha_fin_sql = date('Y-m-t', strtotime('last day of last month'));
            break;
        case 'trimestre_actual':
            $mes_actual = date('n');
            $trimestre = ceil($mes_actual / 3);
            $inicio_trimestre_mes = ($trimestre - 1) * 3 + 1;
            $fecha_inicio_sql = date('Y-' . $inicio_trimestre_mes . '-01');
            $fin_trimestre_mes = $inicio_trimestre_mes + 2;
            $fecha_fin_sql = date('Y-m-t', strtotime(date('Y') . '-' . $fin_trimestre_mes . '-01'));
            break;
        case 'personalizado':
            $fecha_inicio_sql = $fecha_inicio;
            $fecha_fin_sql = $fecha_fin;
            break;
        case 'mes_actual':
        default:
            $fecha_inicio_sql = date('Y-m-01');
            $fecha_fin_sql = date('Y-m-t');
            break;
    }

    // --- 2. Calcular KPIs (tarjetas superiores) ---
    $kpis = [];
    $sql_activos = "SELECT COUNT(*) as total FROM inscripcion_alumno WHERE estatus_alumno = 'Activo'"; // Asumiendo que estatus está en alumno
    $kpis['total_alumnos_activos'] = $conn->query($sql_activos)->fetch_assoc()['total'] ?? 0;

    $sql_ingresos = "SELECT SUM(monto) AS total FROM historial_pagos WHERE fecha_pago BETWEEN '$fecha_inicio_sql' AND '$fecha_fin_sql'";
    $kpis['ingresos_periodo'] = $conn->query($sql_ingresos)->fetch_assoc()['total'] ?? 0;

    $sql_vencido = "SELECT SUM(monto_regular) AS total FROM planpagos WHERE estado_pago IN ('Pendiente', 'Vencido') AND fecha_vencimiento < CURDATE()";
    $kpis['total_vencido'] = $conn->query($sql_vencido)->fetch_assoc()['total'] ?? 0;
    
    $sql_proximos = "SELECT COUNT(*) AS total FROM planpagos WHERE estado_pago = 'Pendiente' AND fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)";
    $kpis['proximos_a_vencer'] = $conn->query($sql_proximos)->fetch_assoc()['total'] ?? 0;
    
    $reporte_data['kpis'] = $kpis;


    // --- 3. Calcular Resumen por Carrera (Tabla principal) ---
    // Esta consulta determina el estado de cada alumno y luego lo agrupa por programa.
$sql_tabla = "
    SELECT
        p.dgp,
        p.nombre_programa,
        COUNT(student_summary.matricula) AS inscritos,
        SUM(CASE WHEN student_summary.financial_status = 'Al Corriente' THEN 1 ELSE 0 END) AS al_corriente,
        SUM(CASE WHEN student_summary.financial_status = 'Proximo a Vencer' THEN 1 ELSE 0 END) AS proximos_a_vencer,
        SUM(CASE WHEN student_summary.financial_status = 'Vencido' THEN 1 ELSE 0 END) AS vencidos
    FROM programa p
    JOIN alumno a ON p.dgp = a.dgp
    JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
    JOIN (
        SELECT
            matricula,
            CASE
                WHEN SUM(CASE WHEN estado_pago IN ('Pendiente', 'Vencido') AND fecha_vencimiento < CURDATE() THEN 1 ELSE 0 END) > 0 THEN 'Vencido'
                WHEN SUM(CASE WHEN estado_pago = 'Pendiente' AND fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY) THEN 1 ELSE 0 END) > 0 THEN 'Proximo a Vencer'
                ELSE 'Al Corriente'
            END AS financial_status
        FROM planpagos
        GROUP BY matricula
    ) AS student_summary ON a.matricula = student_summary.matricula
";
    $conditions = [];
    $params = [];
    $types = "";

// Condición base obligatoria para contar solo alumnos activos
    $conditions[] = "ia.estatus_alumno = 'Activo'";


    if (!empty($id_institucion)) {
        $sql_tabla .= " AND p.id_institucion = ?";
        $params[] = $id_institucion;
        $types .= "s";
    }
    if (!empty($nivel_educativo)) {
        $sql_tabla .= " AND p.nivel_educativo = ?"; // Nombre de columna en tu BD
        $params[] = $nivel_educativo;
        $types .= "s";
    }

    if (count($conditions) > 0) {
    $sql_tabla .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql_tabla .= " GROUP BY p.dgp, p.nombre_programa ORDER BY p.nombre_programa";
    
    $stmt = $conn->prepare($sql_tabla);
    if ($stmt) {
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $reporte_data['tabla_carreras'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        throw new Exception("Error al preparar la consulta de reporte por carrera.");
    }

    $response['success'] = true;
    $response['data'] = $reporte_data;

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>