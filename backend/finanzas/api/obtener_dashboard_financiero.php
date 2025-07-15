<?php
// backend/php/obtener_dashboard_financiero.php
require_once '../../db_config.php'; // Asegúrate que conecte a 'control_escolar'

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error al obtener datos financieros.'];
$dashboard_data = [];

try {
    // --- 1. Calcular KPIs ---
    
    // a) Ingresos del Mes Actual
    $sql_ingresos = "SELECT SUM(monto) AS total_ingresos_mes FROM historial_pagos WHERE MONTH(fecha_pago) = MONTH(CURDATE()) AND YEAR(fecha_pago) = YEAR(CURDATE())";
    $result_ingresos = $conn->query($sql_ingresos);
    $ingresos_mes = $result_ingresos->fetch_assoc()['total_ingresos_mes'] ?? 0;
    $dashboard_data['kpi']['ingresos_mes_actual'] = number_format($ingresos_mes, 2);

    // b) Cartera Vencida (Suma del monto regular de pagos pendientes y vencidos)
    $sql_vencida = "SELECT SUM(monto_regular) AS total_vencido FROM planpagos WHERE estado_pago IN ('Pendiente', 'Vencido') AND fecha_vencimiento < CURDATE()";
    $result_vencida = $conn->query($sql_vencida);
    $cartera_vencida = $result_vencida->fetch_assoc()['total_vencido'] ?? 0;
    $dashboard_data['kpi']['cartera_vencida'] = number_format($cartera_vencida, 2);

    // c) Próximos a Vencer (Conteo de pagos que vencen en los próximos 7 días)
    $sql_proximos = "SELECT COUNT(*) AS total_proximos FROM planpagos WHERE estado_pago = 'Pendiente' AND fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    $result_proximos = $conn->query($sql_proximos);
    $dashboard_data['kpi']['proximos_a_vencer'] = $result_proximos->fetch_assoc()['total_proximos'] ?? 0;

    // d) % Cartera al Corriente (Total de pagos Pagados / Total de pagos que ya deberían haberse pagado)
    $sql_total_pagos_vencidos = "SELECT COUNT(*) as total FROM planpagos WHERE fecha_vencimiento <= CURDATE()";
    $sql_total_pagados_a_la_fecha = "SELECT COUNT(*) as total FROM planpagos WHERE estado_pago = 'Pagado' AND fecha_vencimiento <= CURDATE()";
    $total_deberian_pagarse = $conn->query($sql_total_pagos_vencidos)->fetch_assoc()['total'] ?? 0;
    $total_pagados = $conn->query($sql_total_pagados_a_la_fecha)->fetch_assoc()['total'] ?? 0;
    $porcentaje_corriente = ($total_deberian_pagarse > 0) ? ($total_pagados / $total_deberian_pagarse) * 100 : 100;
    $dashboard_data['kpi']['porcentaje_cartera_corriente'] = round($porcentaje_corriente);


    // --- 2. Calcular Resumen Financiero por Programa ---
    // Esta consulta es compleja. Agrupa por institución y programa y cuenta alumnos por estado de pago.
    $sql_resumen = "
        SELECT 
            i.id_institucion,
            i.nombre AS nombre_institucion,
            p.dgp,
            p.nombre_programa,
            COUNT(DISTINCT a.matricula) AS total_alumnos,
            SUM(CASE WHEN pp_agg.vencidos = 0 AND pp_agg.pendientes > 0 THEN 1 ELSE 0 END) AS al_corriente,
            SUM(CASE WHEN pp_agg.proximos_a_vencer > 0 AND pp_agg.vencidos = 0 THEN 1 ELSE 0 END) AS proximos_a_vencer,
            SUM(CASE WHEN pp_agg.vencidos > 0 THEN 1 ELSE 0 END) AS vencidos
        FROM institucion i
        JOIN programa p ON i.id_institucion = p.id_institucion
        JOIN alumno a ON p.dgp = a.dgp
        LEFT JOIN (
            SELECT 
                matricula,
                COUNT(CASE WHEN estado_pago = 'Vencido' OR (estado_pago = 'Pendiente' AND fecha_vencimiento < CURDATE()) THEN 1 END) as vencidos,
                COUNT(CASE WHEN estado_pago = 'Pendiente' AND fecha_vencimiento >= CURDATE() THEN 1 END) as pendientes,
                COUNT(CASE WHEN estado_pago = 'Pendiente' AND fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as proximos_a_vencer
            FROM planpagos
            GROUP BY matricula
        ) AS pp_agg ON a.matricula = pp_agg.matricula
        GROUP BY i.id_institucion, i.nombre, p.dgp, p.nombre_programa
        ORDER BY i.nombre, p.nombre_programa;
    ";
    $result_resumen = $conn->query($sql_resumen);
    $resumen_financiero = [];
    if ($result_resumen) {
        while ($row = $result_resumen->fetch_assoc()) {
            // Agrupar por institución
            $resumen_financiero[$row['nombre_institucion']][] = $row;
        }
    }
    $dashboard_data['resumen_financiero'] = $resumen_financiero;


    // --- 3. Obtener Alertas de Pagos Críticos ---
    // Lista de alumnos con pagos vencidos, ordenados por los más vencidos.
    $sql_alertas = "
        SELECT a.nombre, a.apellido_paterno, a.apellido_materno, p.nombre_programa, DATEDIFF(CURDATE(), pp.fecha_vencimiento) as dias_vencido
        FROM planpagos pp
        JOIN alumno a ON pp.matricula = a.matricula
        JOIN programa p ON a.dgp = p.dgp
        WHERE pp.estado_pago IN ('Pendiente', 'Vencido') AND pp.fecha_vencimiento < CURDATE()
        ORDER BY dias_vencido DESC
        LIMIT 5;
    ";
    $result_alertas = $conn->query($sql_alertas);
    $alertas_criticas = [];
    if ($result_alertas) {
        while ($row = $result_alertas->fetch_assoc()) {
            $alertas_criticas[] = $row;
        }
    }
    $dashboard_data['alertas_criticas'] = $alertas_criticas;


    $response['success'] = true;
    $response['data'] = $dashboard_data;
    
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>