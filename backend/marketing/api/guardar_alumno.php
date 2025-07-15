<?php
// backend/php/guardar_alumno.php (Corregido)
require_once '../../db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'error' => 'Error desconocido.'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Método de solicitud no válido.';
    http_response_code(405); // Method Not Allowed
    echo json_encode($response);
    exit();
}

$conn->begin_transaction();
try {
    // --- Datos del Alumno ---
    $matricula = $_POST['matricula'] ?? null;
    $dgp = $_POST['dgp'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $apellido_paterno = $_POST['apellido_paterno'] ?? null;
    $apellido_materno = $_POST['apellido_materno'] ?? '';
    
    // --- Conversión de Género (251/250 a M/F) ---
    $genero_form_val = $_POST['genero'] ?? '';
    $genero_db = null;
    if ($genero_form_val === '251') $genero_db = 'M';
    elseif ($genero_form_val === '250') $genero_db = 'F';
    
    // --- Validación de Campos Obligatorios ---
    if (empty($nombre) || empty($dgp) || empty($matricula) || empty($genero_db)) {
        throw new Exception('Faltan campos obligatorios (Nombre, Programa, Matrícula, Género).');
    }
    
    $fecha_inicio_form = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d');

    // --- 1. Insertar en tabla `alumno` ---
    // Se han quitado 'curp' y 'nacionalidad' de la inserción
    $sql_alumno = "INSERT INTO alumno (matricula, dgp, nombre, apellido_paterno, apellido_materno, genero, fecha_inicio) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)"; // 7 placeholders
    $stmt_alumno = $conn->prepare($sql_alumno);
    if ($stmt_alumno === false) throw new Exception("Error SQL (alumno): " . $conn->error);
    // La cadena de tipos debe coincidir: 7 placeholders -> 7 's'
    $stmt_alumno->bind_param("sssssss", $matricula, $dgp, $nombre, $apellido_paterno, $apellido_materno, $genero_db, $fecha_inicio_form);
    if (!$stmt_alumno->execute()) throw new Exception("Error al guardar alumno: " . $stmt_alumno->error);
    $stmt_alumno->close();

    // --- 2. Insertar en `alumno_info_personal` (solo con matrícula) ---
    // Se ha quitado la nacionalidad de aquí también.
    $sql_aip = "INSERT INTO alumno_info_personal (matricula) VALUES (?)"; // 1 placeholder
    $stmt_aip = $conn->prepare($sql_aip);
    if ($stmt_aip === false) throw new Exception("Error SQL (info_personal): " . $conn->error);
    $stmt_aip->bind_param("s", $matricula); // 1 's'
    if (!$stmt_aip->execute()) throw new Exception("Error al guardar info personal: " . $stmt_aip->error);
    $stmt_aip->close();

    // --- 3. Insertar en `inscripcion_alumno` ---
    $ciclo_inicio_insc = date('Y', strtotime($fecha_inicio_form)) . '-' . (ceil(date('n', strtotime($fecha_inicio_form)) / 4));
    $modalidad_programa = $_POST['modalidad'] ?? null;

    $sql_insc_alumno = "INSERT INTO inscripcion_alumno (matricula, ciclo_inicio, fecha_inscripcion, estatus_alumno, modalidad_alumno) 
                        VALUES (?, ?, CURDATE(), 'Activo', ?)";
    $stmt_insc_alumno = $conn->prepare($sql_insc_alumno);
    if ($stmt_insc_alumno === false) throw new Exception("Error SQL (insc_alumno): " . $conn->error);
    $stmt_insc_alumno->bind_param("sss", $matricula, $ciclo_inicio_insc, $modalidad_programa);
    if (!$stmt_insc_alumno->execute()) throw new Exception("Error al guardar inscripción: " . $stmt_insc_alumno->error);
    $stmt_insc_alumno->close();
    
    // --- 4. Manejo de Archivos ---
    // (Este bloque se mantiene igual, pero recuerda que tu tabla `documentos_alumno` no tiene columnas para 'solicitud' o 'cedula')
    $upload_dir = 'uploads/'; // Asumiendo que 'uploads' está en la misma carpeta que este script PHP
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $documentos_data = ['matricula' => $matricula];
    $file_fields_map = [
        'acta_nacimiento' => 'acta_nacimiento',
        'curp' => 'curp_doc',
        'certificado' => 'certificado_estudios',
        'titulo' => 'titulo_universitario',
        'otem' => 'carta_otem',
        'comprobante_inscripcion' => 'comprobante_domicilio'
    ];

    foreach ($file_fields_map as $form_field_id => $db_column_name) {
        $documentos_data[$db_column_name] = null; // Inicializar en null
        if (isset($_FILES[$form_field_id]) && $_FILES[$form_field_id]['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES[$form_field_id]["tmp_name"];
            $original_name = basename($_FILES[$form_field_id]["name"]);
            $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $new_file_name = $matricula . "_" . $db_column_name . "_" . uniqid() . "." . $file_extension;
            if (move_uploaded_file($tmp_name, $upload_dir . $new_file_name)) {
                $documentos_data[$db_column_name] = $new_file_name;
            } else {
                error_log("Error al mover el archivo subido: " . $original_name);
            }
        }
    }
    
    $sql_docs = "INSERT INTO documentos_alumno (matricula, acta_nacimiento, curp_doc, certificado_estudios, titulo_universitario, comprobante_domicilio, carta_otem) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"; // 7 placeholders
    $stmt_docs = $conn->prepare($sql_docs);
    if ($stmt_docs === false) throw new Exception("Error SQL (documentos): " . $conn->error);
    $stmt_docs->bind_param("sssssss", // 7 's'
        $documentos_data['matricula'], 
        $documentos_data['acta_nacimiento'], 
        $documentos_data['curp_doc'], 
        $documentos_data['certificado_estudios'],
        $documentos_data['titulo_universitario'],
        $documentos_data['comprobante_domicilio'],
        $documentos_data['carta_otem']
    );
    if (!$stmt_docs->execute()) throw new Exception("Error al guardar documentos: " . $stmt_docs->error);
    $stmt_docs->close();

    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Alumno preinscrito correctamente con matrícula: ' . $matricula;

} catch (Exception $e) {
    $conn->rollback();
    $response['error'] = $e->getMessage();
    http_response_code(400); 
}

$conn->close();
echo json_encode($response);
?>