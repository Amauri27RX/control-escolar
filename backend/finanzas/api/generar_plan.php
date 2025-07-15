
<?php

header('Content-Type: application/json; charset=utf-8');
require_once '../../conexion.php';

$response = [
    'success' => false,
    'message' => '',
    'data'    => null
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Método no permitido';
    echo json_encode($response);
    exit;
}

/* ───────────────────── Leer el cuerpo de la petición ───────────────────── */
$payload = file_get_contents('php://input');
$data    = json_decode($payload, true);

/* Fallback: aceptar x‑www‑form‑urlencoded */
if (!$data || !is_array($data)) {
    $data = $_POST;
}

/* Comprobar que al menos exista matrícula */
if (empty($data['matricula'])) {
    http_response_code(400); // Bad Request
    $response['message'] = 'Datos incompletos o mal formateados.';
    echo json_encode($response);
    exit;
}

/* ───────────────────── Validar campos obligatorios ───────────────────── */
$required = ['matricula', 'nivel_educativo', 'programa', 'duracion_meses', 'fecha_inicio'];
$missing  = array_diff($required, array_keys($data));
if (!empty($missing)) {
    http_response_code(400);
    $response['message'] = 'Faltan parámetros: ' . implode(', ', $missing);
    echo json_encode($response);
    exit;
}

/* ───────────────────── Asignar y sanitizar variables ───────────────────── */
$matricula             = $data['matricula'];
$nivel_educativo       = $data['nivel_educativo'];
$programa              = $data['programa'];
$duracion_meses        = (int) $data['duracion_meses'];
$fecha_inicio          = $data['fecha_inicio'];

$descuento_inscripcion = isset($data['descuento_inscripcion'])
                       ? (float) $data['descuento_inscripcion'] : 0;

$descuento_colegiatura = isset($data['descuento_colegiatura'])
                       ? (float) $data['descuento_colegiatura'] : 0;

$descuento_reinscripcion = isset($data['descuento_reinscripcion'])
                         ? (float) $data['descuento_reinscripcion'] : 0;

try {
    $conn->begin_transaction();

    /* ───── Limpiar planes anteriores ───── */
    $stmt = $conn->prepare("DELETE FROM historial_pagos WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM planpagos WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $stmt->close();

    /* ───── Obtener costos por nivel ───── */
    $stmt = $conn->prepare("SELECT * FROM costospornivel WHERE nivel_educativo = ?");
    $stmt->bind_param("s", $nivel_educativo);
    $stmt->execute();
    $costos = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$costos) {
        throw new Exception("No se encontraron costos para el nivel educativo especificado.");
    }

    /* ------------------ INSCRIPCIÓN ------------------ */
    $montoInscripcion = $costos['costo_inscripcion_std'];
    if ($descuento_inscripcion > 0) {
        $montoInscripcion *= (100 - $descuento_inscripcion) / 100;
    }

    $fechaVencimientoInsc = date('Y-m-d', strtotime($fecha_inicio . ' + 7 days'));
    $conceptoInscripcion  = "Inscripción $nivel_educativo - $programa";
    $tipoInscripcion      = 'Inscripción';

    $stmt = $conn->prepare("INSERT INTO planpagos 
        (matricula, concepto, monto_regular, fecha_vencimiento, tipo_pago, generado_automatico) 
        VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssdss",
        $matricula,
        $conceptoInscripcion,
        $montoInscripcion,
        $fechaVencimientoInsc,
        $tipoInscripcion
    );
    $stmt->execute();
    $stmt->close();

    /* ------------------ MENSUALIDADES ------------------ */
    $montoMensualidad = $costos['costo_colegiatura_std'];
    if ($descuento_colegiatura > 0) {
        $montoMensualidad *= (100 - $descuento_colegiatura) / 100;
    }

    $stmtMens = $conn->prepare("INSERT INTO planpagos 
        (matricula, concepto, monto_regular, fecha_vencimiento, tipo_pago, numero_mensualidad, generado_automatico) 
        VALUES (?, ?, ?, ?, ?, ?, 1)");
    $tipoMensualidad = 'Mensualidad';

    for ($i = 1; $i <= $duracion_meses; $i++) {
        $fechaVencimiento  = date('Y-m-d', strtotime($fecha_inicio . " +$i months"));
        $conceptoMensual   = "Mensualidad $i/$duracion_meses - $programa";
        $stmtMens->bind_param("ssdssi",
            $matricula,
            $conceptoMensual,
            $montoMensualidad,
            $fechaVencimiento,
            $tipoMensualidad,
            $i
        );
        $stmtMens->execute();
    }
    $stmtMens->close();

    /* ------------------ REINSCRIPCIÓN (cada 4 meses) ------------------ */
    if (in_array($nivel_educativo, ['Licenciatura', 'Maestria', 'Doctorado'])) {

        $montoReinscripcion = $costos['costo_reinscripcion_std'];
        if ($descuento_reinscripcion > 0) {
            $montoReinscripcion *= (100 - $descuento_reinscripcion) / 100;
        }

        $totalReinscripcion = 0;                     // para el resumen
        $cuatrimestres      = (int) ceil($duracion_meses / 4);

        /*  Cuatrimestre 1 = inscripción inicial (mes 0)
            Cuatrimestres 2…N  = reinscripción            */
        for ($c = 2; $c <= $cuatrimestres; $c++) {
            $offsetMeses         = ($c - 1) * 4;      // 4, 8, 12, …
            $fechaReinscripcion  = date('Y-m-d', strtotime($fecha_inicio . " +{$offsetMeses} months"));
            $conceptoReinscripcion = "Reinscripción $nivel_educativo - $programa (Cuat. $c)";
            $tipoReinscripcion     = 'Reinscripción';

            $stmt = $conn->prepare("INSERT INTO planpagos
                (matricula, concepto, monto_regular, fecha_vencimiento, tipo_pago, generado_automatico)
                VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssdss",
                $matricula,
                $conceptoReinscripcion,
                $montoReinscripcion,
                $fechaReinscripcion,
                $tipoReinscripcion
            );
            $stmt->execute();
            $stmt->close();

            $totalReinscripcion += $montoReinscripcion;
        }
    }

    /* ─────────── Confirmar transacción ─────────── */
    $conn->commit();

    $response['success'] = true;
    $response['message'] = 'Plan generado exitosamente';
    $response['data']    = [
        'total_mensualidades' => $duracion_meses,
        'monto_total'         => $montoInscripcion + ($montoMensualidad * $duracion_meses),
        'primer_vencimiento'  => $fechaVencimientoInsc
    ];
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
