<?php
// backend/php/obtener_expediente.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Matrícula no proporcionada.'];

if (isset($_GET['matricula'])) {
    $matricula = trim($_GET['matricula']);
    $expediente = [];

    try {
        // --- 1. OBTENER INFORMACIÓN GENERAL ---
        // (Esta parte se mantiene como estaba, consultando alumno, programa, etc.)
        $sql_general = "SELECT
                            a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, a.curp, a.genero,
                            p.nombre_programa, p.nivel_educativo AS nivel_educativo,
                            aip.fecha_nacimiento, aip.estado_ciudad_nacimiento AS lugar_nacimiento, aip.nacionalidad,
                            aip.telefono AS telefono_personal, aip.correo_personal, a.correo_institucional,
                            i.nombre AS nombre_institucion,
                            ia.fecha_inscripcion, ia.estatus_alumno, ia.modalidad_alumno AS modalidad,
                            ia.ciclo_inicio, ia.ciclo_fin
                        FROM alumno a
                        LEFT JOIN programa p ON a.dgp = p.dgp
                        LEFT JOIN institucion i ON p.id_institucion = i.id_institucion
                        LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
                        LEFT JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
                        WHERE a.matricula = ?";
        $stmt_general = $conn->prepare($sql_general);
        if ($stmt_general) {
            $stmt_general->bind_param("s", $matricula);
            $stmt_general->execute();
            $result_general = $stmt_general->get_result();
            if ($result_general->num_rows > 0) {
                $expediente['info_general'] = $result_general->fetch_assoc();
            } else {
                throw new Exception("No se encontró información general para la matrícula " . htmlspecialchars($matricula));
            }
            $stmt_general->close();
        } else {
            throw new Exception("Error al preparar la consulta general.");
        }


        // --- 2. OBTENER SITUACIÓN FINANCIERA (DATOS REALES) ---
        $financiera = [];
        
        // Pagos Pendientes y Vencidos
        $sql_pendientes = "SELECT 
                                id_pago, concepto, fecha_vencimiento, monto_regular AS saldo,
                                CASE 
                                    WHEN fecha_vencimiento < CURDATE() AND estado_pago = 'Pendiente' THEN 'Vencido'
                                    ELSE estado_pago 
                                END AS status,
                                DATEDIFF(CURDATE(), fecha_vencimiento) as dias_vencido
                           FROM planpagos 
                           WHERE matricula = ? AND estado_pago IN ('Pendiente', 'Vencido')
                           ORDER BY fecha_vencimiento ASC";
        $stmt_pendientes = $conn->prepare($sql_pendientes);
        $stmt_pendientes->bind_param("s", $matricula);
        $stmt_pendientes->execute();
        $result_pendientes = $stmt_pendientes->get_result();
        $financiera['pagos_pendientes'] = $result_pendientes->fetch_all(MYSQLI_ASSOC);
        $stmt_pendientes->close();

        // Pagos Realizados (Historial)
        $sql_realizados = "SELECT h.id_pago, p.concepto, h.fecha_pago, h.monto, h.metodo_pago 
                           FROM historial_pagos h
                           JOIN planpagos p ON h.id_pago = p.id_pago
                           WHERE h.matricula = ?
                           ORDER BY h.fecha_pago DESC";
        $stmt_realizados = $conn->prepare($sql_realizados);
        $stmt_realizados->bind_param("s", $matricula);
        $stmt_realizados->execute();
        $result_realizados = $stmt_realizados->get_result();
        $financiera['pagos_realizados'] = $result_realizados->fetch_all(MYSQLI_ASSOC);
        $stmt_realizados->close();
        
        $expediente['situacion_financiera'] = $financiera;


        // --- 3. OBTENER DOCUMENTOS (DATOS REALES) ---
        // (Se mantiene como estaba)
        $sql_docs = "SELECT acta_nacimiento, curp_doc, certificado_estudios, titulo_universitario, comprobante_domicilio, carta_otem 
                     FROM documentos_alumno WHERE matricula = ?";
        $stmt_docs = $conn->prepare($sql_docs);
        if($stmt_docs){
            $stmt_docs->bind_param("s", $matricula);
            $stmt_docs->execute();
            $result_docs = $stmt_docs->get_result();
            $expediente['documentos'] = $result_docs->fetch_assoc() ?: [];
            $stmt_docs->close();
        }

        // --- 4. OBTENER CALIFICACIONES (DATOS REALES) ---
        // Ahora se obtienen de la tabla 'materias_alumno'
        $sql_calificaciones = "
    SELECT 
        m.nombre AS nombre_materia, 
        c.codigo AS ciclo,
        im.calificacion,
        CONCAT(d.nombres, ' ', d.apellidos) AS nombre_docente
    FROM inscripcion_materia im
    JOIN oferta_materia o ON im.id_oferta = o.id_oferta
    JOIN materia m ON o.clave_materia = m.clave_materia
    JOIN ciclo_escolar c ON o.id_ciclo = c.id_ciclo
    LEFT JOIN docente d ON o.id_docente = d.id_docente
    WHERE im.matricula = ?
    ORDER BY c.fecha_inicio DESC, m.nombre ASC";

$stmt_calificaciones = $conn->prepare($sql_calificaciones);
if($stmt_calificaciones){
    $stmt_calificaciones->bind_param("s", $matricula);
    $stmt_calificaciones->execute();
    $result_calificaciones = $stmt_calificaciones->get_result();
    $expediente['calificaciones'] = $result_calificaciones->fetch_all(MYSQLI_ASSOC);
    $stmt_calificaciones->close();
}

        // --- 5. OBTENER OBSERVACIONES (SIMULADO) ---
        // Todavía no hay tabla para esto, se mantiene simulado
      $sql_obs = "SELECT observacion, tipo, autor, fecha_creacion FROM observaciones_alumno WHERE matricula = ? ORDER BY fecha_creacion DESC";
        $stmt_obs = $conn->prepare($sql_obs);
        if ($stmt_obs) {
            $stmt_obs->bind_param("s", $matricula);
            $stmt_obs->execute();
            $result_obs = $stmt_obs->get_result();
            $expediente['observaciones'] = $result_obs->fetch_all(MYSQLI_ASSOC);
            $stmt_obs->close();
        }

        
        // --- 6. OBTENER COMPROBANTES FISCALES (SIMULADO) ---
        // No hay tabla para esto, se mantiene simulado
        $expediente['comprobantes_fiscales'] = [
             ['folio' => '19938Digital', 'fecha_pago' => '2025-05-28', 'importe' => '500.00', 'uuid' => 'f614cfee-eceb-4645-b139-ae7cc364e5a1', 'fecha_emision' => '2025-05-28 18:10:23', 'status' => 'Timbrado']
        ];


        // Si todo fue bien:
        $response = ['success' => true, 'expediente' => $expediente];

    } catch (Exception $e) {
        http_response_code(404);
        $response['message'] = $e->getMessage();
    }

    $conn->close();
    echo json_encode($response);
}
?>