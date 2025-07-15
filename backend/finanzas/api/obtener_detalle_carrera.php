<?php
// backend/php/obtener_detalle_carrera.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Parámetros incompletos.'];

if (isset($_GET['dgp'])) {
    $dgp = trim($_GET['dgp']);
    $estatus_filtro = isset($_GET['estatus']) ? trim($_GET['estatus']) : '';
    $busqueda_filtro = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    
    $detalle_data = [];

    try {
        $conn->begin_transaction();

        // --- 1. Obtener Nombre de la Carrera y KPIs generales ---
        $stmt_prog = $conn->prepare("SELECT nombre_programa FROM programa WHERE dgp = ?");
        $stmt_prog->bind_param("s", $dgp);
        $stmt_prog->execute();
        $result_prog = $stmt_prog->get_result();
        if ($result_prog->num_rows === 0) throw new Exception("Programa no encontrado.");
        $detalle_data['nombre_programa'] = $result_prog->fetch_assoc()['nombre_programa'];
        $stmt_prog->close();

        // --- 2. Calcular Resumen Financiero para ESTA CARRERA ---
        $resumen_carrera = [];
        
        // Total Cartera Vencida de la Carrera
        $stmt_vencido = $conn->prepare("SELECT SUM(pp.monto_regular) AS total FROM planpagos pp JOIN alumno a ON pp.matricula = a.matricula WHERE a.dgp = ? AND pp.estado_pago IN ('Pendiente', 'Vencido') AND pp.fecha_vencimiento < CURDATE()");
        $stmt_vencido->bind_param("s", $dgp);
        $stmt_vencido->execute();
        $resumen_carrera['total_vencido'] = $stmt_vencido->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt_vencido->close();

        // Total por Cobrar (Próximos 10 días) de la Carrera
        $stmt_por_cobrar = $conn->prepare("SELECT SUM(pp.monto_regular) AS total FROM planpagos pp JOIN alumno a ON pp.matricula = a.matricula WHERE a.dgp = ? AND pp.estado_pago = 'Pendiente' AND pp.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY)");
        $stmt_por_cobrar->bind_param("s", $dgp);
        $stmt_por_cobrar->execute();
        $resumen_carrera['total_por_cobrar'] = $stmt_por_cobrar->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt_por_cobrar->close();

        // Promedio de Días de Vencimiento de la Carrera
        $stmt_avg_vencido = $conn->prepare("SELECT AVG(DATEDIFF(CURDATE(), pp.fecha_vencimiento)) AS promedio_dias FROM planpagos pp JOIN alumno a ON pp.matricula = a.matricula WHERE a.dgp = ? AND pp.estado_pago IN ('Pendiente', 'Vencido') AND pp.fecha_vencimiento < CURDATE()");
        $stmt_avg_vencido->bind_param("s", $dgp);
        $stmt_avg_vencido->execute();
        $resumen_carrera['promedio_dias_vencido'] = $stmt_avg_vencido->get_result()->fetch_assoc()['promedio_dias'] ?? 0;
        $stmt_avg_vencido->close();
        
        $detalle_data['resumen_carrera'] = $resumen_carrera;


        // --- 3. Obtener la lista de alumnos enriquecida ---
        $sql_alumnos = "
            WITH ranked_pagos AS (
                SELECT
                    pp.matricula, pp.concepto, pp.fecha_vencimiento, pp.monto_regular,
                    CASE
                        WHEN pp.estado_pago IN ('Pendiente', 'Vencido') AND pp.fecha_vencimiento < CURDATE() THEN 'Vencido'
                        WHEN pp.estado_pago = 'Pendiente' AND pp.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 10 DAY) THEN 'Proximo a Vencer'
                        ELSE 'Al Corriente'
                    END as estatus_pago_calculado,
                    ROW_NUMBER() OVER(PARTITION BY pp.matricula ORDER BY pp.fecha_vencimiento ASC) as rn
                FROM planpagos pp WHERE pp.estado_pago = 'Pendiente' OR (pp.estado_pago = 'Vencido' AND pp.fecha_vencimiento < CURDATE())
            ),
            ultimo_pago AS (
                SELECT matricula, MAX(fecha_pago) AS fecha_ultimo_pago,
                       (SELECT monto FROM historial_pagos WHERE matricula = h.matricula AND fecha_pago = MAX(h.fecha_pago) LIMIT 1) as monto_ultimo_pago
                FROM historial_pagos h GROUP BY matricula
            )
            SELECT
                a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno,
                aip.telefono AS telefono_personal, aip.correo_personal,
                ia.promocion_aplicada,
                up.fecha_ultimo_pago, up.monto_ultimo_pago,
                rp.concepto, rp.fecha_vencimiento, rp.monto_regular, rp.estatus_pago_calculado
            FROM alumno a
            JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
            LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
            LEFT JOIN ranked_pagos rp ON a.matricula = rp.matricula AND rp.rn = 1
            LEFT JOIN ultimo_pago up ON a.matricula = up.matricula
            WHERE a.dgp = ? AND ia.estatus_alumno = 'Activo'
        ";

        $params = [$dgp];
        $types = "s";
        $conditions = [];

        if (!empty($estatus_filtro)) {
            $conditions[] = "rp.estatus_pago_calculado = ?";
            $params[] = $estatus_filtro;
            $types .= "s";
        }
        if (!empty($busqueda_filtro)) {
            $like_busqueda = "%" . $busqueda_filtro . "%";
            $conditions[] = "(CONCAT_WS(' ', a.nombre, a.apellido_paterno, a.apellido_materno) LIKE ? OR a.matricula LIKE ?)";
            $params[] = $like_busqueda;
            $params[] = $like_busqueda;
            $types .= "ss";
        }

        if (count($conditions) > 0) {
            $sql_alumnos .= " AND " . implode(" AND ", $conditions);
        }
        $sql_alumnos .= " ORDER BY rp.fecha_vencimiento ASC, a.apellido_paterno ASC";
        
        $stmt_alumnos = $conn->prepare($sql_alumnos);
        if(!empty($types)) $stmt_alumnos->bind_param($types, ...$params);
        $stmt_alumnos->execute();
        $result_alumnos = $stmt_alumnos->get_result();
        $detalle_data['alumnos'] = $result_alumnos->fetch_all(MYSQLI_ASSOC);
        $stmt_alumnos->close();
        
        $conn->commit();
        $response['success'] = true;
        $response['data'] = $detalle_data;

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        $response['message'] = $e->getMessage();
    }

    $conn->close();
    echo json_encode($response);
}
?>