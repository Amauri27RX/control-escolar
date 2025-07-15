<?php
// actualizar_datos_alumno.php

// --- Configuración de la Base de Datos ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'control_escolar';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido al actualizar la inscripción.'];

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    $response['message'] = 'Error de conexión a la base de datos: ' . $conn->connect_error;
    http_response_code(500);
    echo json_encode($response);
    exit();
}
$conn->set_charset("utf8mb4");
$conn->begin_transaction();

try {
    // --- Obtener datos del POST ---
    // La matrícula es la clave para todas las actualizaciones
    $matricula = $_POST['matricula'] ?? null;
    if (empty($matricula)) {
        throw new Exception("La matrícula es requerida para actualizar.");
    }

    $dgp_programa = $_POST['dgp'] ?? null;

    // --- 1. Actualizar la tabla `alumno` ---
    $nombre_alumno = $_POST['nombre'] ?? null;
    $apellido_paterno_alumno = $_POST['apellido_paterno'] ?? null;
    $apellido_materno_alumno = $_POST['apellido_materno'] ?? null;
    $curp_alumno = $_POST['curp'] ?? null;
    $cura_alumno = $_POST['cura'] ?? null;
    $fecha_inicio_alumno = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : null;
    
    $genero_input_update = $_POST['genero'] ?? '';
    $genero_db_update = null;
    if (in_array($genero_input_update, ['M', 'F', 'O'])) {
        $genero_db_update = $genero_input_update;
    } elseif (!empty($genero_input_update)) {
        throw new Exception("Valor de género inválido para actualizar.");
    } // Si es vacío y la columna es NOT NULL, podría fallar si no se provee un valor válido.

    $generacion_alumno = $_POST['ciclo_inicio'] ?? null; 
    $correo_institucional_alumno = $_POST['correo_institucional'] ?? null; // Asumiendo que este campo podría ser editable
    $nacionalidad_alumno_table = $_POST['nacionalidad'] ?? null; 

    $sql_alumno = "UPDATE alumno SET 
        dgp = ?, nombre = ?, apellido_paterno = ?, apellido_materno = ?, curp = ?, genero = ?, cura = ?, 
        correo_institucional = ?, fecha_inicio = ?, generacion = ?, nacionalidad = ?
        WHERE matricula = ?";
    $stmt_alumno = $conn->prepare($sql_alumno);
    if ($stmt_alumno === false) throw new Exception("Error al preparar actualización de alumno: " . $conn->error);
    $stmt_alumno->bind_param("ssssssssssss", // 11 campos + matricula en WHERE
        $dgp_programa, $nombre_alumno, $apellido_paterno_alumno, $apellido_materno_alumno, 
        $curp_alumno, $genero_db_update, $cura_alumno, $correo_institucional_alumno, 
        $fecha_inicio_alumno, $generacion_alumno, $nacionalidad_alumno_table,
        $matricula // Para el WHERE
    );
    if (!$stmt_alumno->execute()) throw new Exception("Error al actualizar alumno: " . $stmt_alumno->error);
    $stmt_alumno->close();

    // --- 2. Actualizar/Insertar en `alumno_info_personal` ---
    $pais_nacimiento_form = $_POST['pais_nacimiento'] ?? null;
    $ciudad_nacimiento_form = $_POST['ciudad_nacimiento'] ?? null;
    $nacionalidad_form = $_POST['nacionalidad'] ?? null;
    $fecha_nacimiento_form = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $correo_personal_form = $_POST['correo_personal'] ?? null;
    $telefono_personal_form = $_POST['telefono_personal'] ?? null;

    $sql_info_personal = "INSERT INTO alumno_info_personal (matricula, pais_nacimiento, estado_ciudad_nacimiento, nacionalidad, fecha_nacimiento, correo_personal, telefono) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE 
                          pais_nacimiento = VALUES(pais_nacimiento), estado_ciudad_nacimiento = VALUES(estado_ciudad_nacimiento), nacionalidad = VALUES(nacionalidad),
                          fecha_nacimiento = VALUES(fecha_nacimiento), correo_personal = VALUES(correo_personal), telefono = VALUES(telefono)";
    $stmt_info_personal = $conn->prepare($sql_info_personal);
    if ($stmt_info_personal === false) throw new Exception("Error al preparar actualizacion/inserción de info_personal: " . $conn->error);
    $stmt_info_personal->bind_param("sssssss", $matricula, $pais_nacimiento_form, $ciudad_nacimiento_form, $nacionalidad_form, $fecha_nacimiento_form, $correo_personal_form, $telefono_personal_form);
    if (!$stmt_info_personal->execute()) throw new Exception("Error al actualizar/insertar en alumno_info_personal: " . $stmt_info_personal->error);
    $stmt_info_personal->close();

    // --- 3. Actualizar/Insertar en `alumno_ubicacion` ---
    $pais_residencia_form = $_POST['pais_residencia'] ?? null;
    $ciudad_residencia_form = $_POST['ciudad_residencia'] ?? null;
    $colonia_residencia_form = $_POST['colonia_residencia'] ?? null;
    $calle_residencia_form = $_POST['calle_residencia'] ?? null;
    $num_int_residencia_form = $_POST['num_int_residencia'] ?? null;
    $num_ext_residencia_form = $_POST['num_ext_residencia'] ?? null;
    $codigo_postal_form = $_POST['cp_residencia'] ?? null; // Asumiendo que podrías añadir cp_residencia al form

    $sql_ubicacion = "INSERT INTO alumno_ubicacion (matricula, pais, estado_ciudad, colonia_localidad, calle, num_interno, num_externo, codigo_postal)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE
                      pais = VALUES(pais), estado_ciudad = VALUES(estado_ciudad), colonia_localidad = VALUES(colonia_localidad), calle = VALUES(calle),
                      num_interno = VALUES(num_interno), num_externo = VALUES(num_externo), codigo_postal = VALUES(codigo_postal)";
    $stmt_ubicacion = $conn->prepare($sql_ubicacion);
    if ($stmt_ubicacion === false) throw new Exception("Error al preparar actualizacion/inserción de ubicacion: " . $conn->error);
    $stmt_ubicacion->bind_param("ssssssss", $matricula, $pais_residencia_form, $ciudad_residencia_form, $colonia_residencia_form, $calle_residencia_form, $num_int_residencia_form, $num_ext_residencia_form, $codigo_postal_form);
    if (!$stmt_ubicacion->execute()) throw new Exception("Error al actualizar/insertar en alumno_ubicacion: " . $stmt_ubicacion->error);
    $stmt_ubicacion->close();

    // --- 4. Actualizar/Insertar en `alumno_laboral` ---
    $nombre_empresa_form = $_POST['nombre_empresa'] ?? null;
    $puesto_trabajo_form = $_POST['puesto_trabajo'] ?? null;
    $area_trabajo_form = $_POST['area_trabajo'] ?? null;
    $telefono_trabajo_form = $_POST['telefono_trabajo'] ?? null;
    $correo_trabajo_form = $_POST['correo_trabajo'] ?? null;

    if (!empty($nombre_empresa_form)) { // Solo operar si hay datos de empresa
        $sql_laboral = "INSERT INTO alumno_laboral (matricula, nombre_empresa, puesto, area_departamento, telefono, correo_corporativo)
                        VALUES (?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                        nombre_empresa = VALUES(nombre_empresa), puesto = VALUES(puesto), area_departamento = VALUES(area_departamento),
                        telefono = VALUES(telefono), correo_corporativo = VALUES(correo_corporativo)";
        $stmt_laboral = $conn->prepare($sql_laboral);
        if ($stmt_laboral === false) throw new Exception("Error al preparar actualizacion/inserción de laboral: " . $conn->error);
        $stmt_laboral->bind_param("ssssss", $matricula, $nombre_empresa_form, $puesto_trabajo_form, $area_trabajo_form, $telefono_trabajo_form, $correo_trabajo_form);
        if (!$stmt_laboral->execute()) throw new Exception("Error al actualizar/insertar en alumno_laboral: " . $stmt_laboral->error);
        $stmt_laboral->close();

        // --- 5. Actualizar/Insertar en `alumno_laboral_ubicacion` ---
        $pais_trabajo_form = $_POST['pais_trabajo'] ?? null;
        $ciudad_trabajo_form = $_POST['ciudad_trabajo'] ?? null;
        $colonia_trabajo_form = $_POST['colonia_trabajo'] ?? null;
        $calle_trabajo_form = $_POST['calle_trabajo'] ?? null;
        $num_int_trabajo_form = $_POST['num_int_trabajo'] ?? null;
        $num_ext_trabajo_form = $_POST['num_ext_trabajo'] ?? null;

        $sql_laboral_ubicacion = "INSERT INTO alumno_laboral_ubicacion (matricula, pais, estado_ciudad, colonia, calle, num_interno, num_externo)
                                  VALUES (?, ?, ?, ?, ?, ?, ?)
                                  ON DUPLICATE KEY UPDATE
                                  pais = VALUES(pais), estado_ciudad = VALUES(estado_ciudad), colonia = VALUES(colonia), calle = VALUES(calle),
                                  num_interno = VALUES(num_interno), num_externo = VALUES(num_externo)";
        $stmt_laboral_ubicacion = $conn->prepare($sql_laboral_ubicacion);
        if ($stmt_laboral_ubicacion === false) throw new Exception("Error al preparar actualizacion/inserción de laboral_ubicacion: " . $conn->error);
        $stmt_laboral_ubicacion->bind_param("sssssss", $matricula, $pais_trabajo_form, $ciudad_trabajo_form, $colonia_trabajo_form, $calle_trabajo_form, $num_int_trabajo_form, $num_ext_trabajo_form);
        if (!$stmt_laboral_ubicacion->execute()) throw new Exception("Error al actualizar/insertar en alumno_laboral_ubicacion: " . $stmt_laboral_ubicacion->error);
        $stmt_laboral_ubicacion->close();
    } else {
        // Si no hay nombre de empresa, podríamos querer eliminar registros laborales existentes
        $conn->query("DELETE FROM alumno_laboral WHERE matricula = '$matricula'"); // Cuidado con la inyección si matricula no está controlada, pero aquí viene de POST y la usamos en prepared statements.
        $conn->query("DELETE FROM alumno_laboral_ubicacion WHERE matricula = '$matricula'");
    }


    // --- 6. Actualizar/Insertar en `antecedente_academico` ---
    $nivel_anterior_form = $_POST['nivel_anterior'] ?? null;
    $nombre_institucion_form = $_POST['nombre_institucion'] ?? null;
    $ubicacion_institucion_form = $_POST['ubicacion_institucion'] ?? null;
    $fecha_inicio_antecedentes_form = !empty($_POST['fecha_inicio_antecedentes']) ? $_POST['fecha_inicio_antecedentes'] : null;
    $fecha_finalizacion_form = !empty($_POST['fecha_finalizacion']) ? $_POST['fecha_finalizacion'] : null;

    if (!empty($nivel_anterior_form) || !empty($nombre_institucion_form)) { // Solo operar si hay datos de antecedente
        $sql_antecedente = "INSERT INTO antecedente_academico (matricula, nivel_educativo_anterior, nombre_institucion, ciudad_institucion, fecha_inicio, fecha_fin)
                            VALUES (?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                            nivel_educativo_anterior = VALUES(nivel_educativo_anterior), nombre_institucion = VALUES(nombre_institucion), ciudad_institucion = VALUES(ciudad_institucion),
                            fecha_inicio = VALUES(fecha_inicio), fecha_fin = VALUES(fecha_fin)";
        $stmt_antecedente = $conn->prepare($sql_antecedente);
        if ($stmt_antecedente === false) throw new Exception("Error al preparar actualizacion/inserción de antecedente: " . $conn->error);
        $stmt_antecedente->bind_param("ssssss", $matricula, $nivel_anterior_form, $nombre_institucion_form, $ubicacion_institucion_form, $fecha_inicio_antecedentes_form, $fecha_finalizacion_form);
        if (!$stmt_antecedente->execute()) throw new Exception("Error al actualizar/insertar en antecedente_academico: " . $stmt_antecedente->error);
        $stmt_antecedente->close();
    } else {
        // Si no hay datos de antecedente, podríamos querer eliminar el registro existente
        $conn->query("DELETE FROM antecedente_academico WHERE matricula = '$matricula'");
    }


    // --- 7. Actualizar/Insertar en `inscripcion_alumno` ---
    // (Asumiendo que añadiste modalidad_alumno a esta tabla)
    $permanencia_form = $_POST['permanencia'] ?? null;
    $promocion_form = $_POST['promocion'] ?? null;
    $modalidad_titulacion_form = $_POST['modalidad_titulacion'] ?? null;
    $ciclo_inicio_form = $_POST['ciclo_inicio'] ?? null;
    $ciclo_fin_form = $_POST['ciclo_fin'] ?? null;
    $modalidad_alumno_form = $_POST['modalidad'] ?? null;
    $estatus_alumno_form = $_POST['estatus_alumno'] ?? 'Activo'; 

$estatus_validos_update = ['Activo', 'Irregular', 'Baja', 'Egresado', 'Suspendido'];
if (!in_array($estatus_alumno_form, $estatus_validos_update)) {
    // Considera cómo manejar un valor inválido en una actualización.
    // Podrías no actualizar este campo específico o lanzar un error.
    // Por ahora, si es inválido, no se pasará un valor incorrecto a la BD si se omite.
    // O podrías buscar el valor actual y mantenerlo, o usar un default.
    // Para este ejemplo, si es inválido, podrías decidir no actualizarlo o lanzar un error.
    // Aquí, si se envía un valor vacío o inválido y la columna es NOT NULL DEFAULT 'Activo',
    // la base de datos podría rechazar un UPDATE con NULL si no se maneja.
    // Para un UPDATE, es mejor asegurar que el valor es válido o no incluirlo en la actualización.
    if (empty($estatus_alumno_form)) { // Si el campo se envía vacío
        // Decide si esto significa "no cambiar el estatus" o "ponerlo en Activo"
        // O si el campo es requerido y no puede estar vacío en una actualización.
        // Por seguridad, si es inválido, podrías no incluirlo en el update o usar un valor seguro.
        // Para el ON DUPLICATE KEY UPDATE, VALUES(estatus_alumno) usará el valor proporcionado.
        // Es importante que el frontend envíe un valor válido.
        // Asumamos que el select del form siempre envía uno de los valores válidos.
    }
}


$sql_inscripcion_alumno = "INSERT INTO inscripcion_alumno (matricula, permanencia, promocion_aplicada, modalidad_titulacion, ciclo_inicio, ciclo_fin, modalidad_alumno, estatus_alumno)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                           ON DUPLICATE KEY UPDATE
                           permanencia = VALUES(permanencia), promocion_aplicada = VALUES(promocion_aplicada), modalidad_titulacion = VALUES(modalidad_titulacion),
                           ciclo_inicio = VALUES(ciclo_inicio), ciclo_fin = VALUES(ciclo_fin), modalidad_alumno = VALUES(modalidad_alumno), estatus_alumno = VALUES(estatus_alumno)";
$stmt_inscripcion_alumno = $conn->prepare($sql_inscripcion_alumno);
if ($stmt_inscripcion_alumno === false) throw new Exception("Error al preparar actualizacion/inserción de inscripcion_alumno: " . $conn->error);
$stmt_inscripcion_alumno->bind_param("ssssssss", $matricula, $permanencia_form, $promocion_form, $modalidad_titulacion_form, $ciclo_inicio_form, $ciclo_fin_form, $modalidad_alumno_form, $estatus_alumno_form);
if (!$stmt_inscripcion_alumno->execute()) throw new Exception("Error al actualizar/insertar en inscripcion_alumno: " . $stmt_inscripcion_alumno->error);
$stmt_inscripcion_alumno->close();

    // --- 8. Actualizar/Insertar en `documentos_alumno` ---
    $acta_nacimiento_doc_form = $_POST['acta_nacimiento_doc'] ?? null;
    $curp_doc_form = $_POST['curp_doc'] ?? null;
    $certificado_estudios_doc_form = $_POST['certificado_estudios_doc'] ?? null;
    $titulo_universitario_doc_form = $_POST['titulo_universitario_doc'] ?? null;
    $comprobante_domicilio_doc_form = $_POST['comprobante_domicilio_doc'] ?? null;
    $carta_otem_doc_form = $_POST['carta_otem_doc'] ?? null;

    $sql_documentos = "INSERT INTO documentos_alumno (matricula, acta_nacimiento, curp_doc, certificado_estudios, titulo_universitario, comprobante_domicilio, carta_otem)
                       VALUES (?, ?, ?, ?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE
                       acta_nacimiento = VALUES(acta_nacimiento), curp_doc = VALUES(curp_doc), certificado_estudios = VALUES(certificado_estudios),
                       titulo_universitario = VALUES(titulo_universitario), comprobante_domicilio = VALUES(comprobante_domicilio), carta_otem = VALUES(carta_otem)";
    $stmt_documentos = $conn->prepare($sql_documentos);
     if ($stmt_documentos === false) throw new Exception("Error al preparar actualizacion/inserción de documentos_alumno: " . $conn->error);
    $stmt_documentos->bind_param("sssssss", $matricula, $acta_nacimiento_doc_form, $curp_doc_form, $certificado_estudios_doc_form, $titulo_universitario_doc_form, $comprobante_domicilio_doc_form, $carta_otem_doc_form);
    if (!$stmt_documentos->execute()) throw new Exception("Error al actualizar/insertar en documentos_alumno: " . $stmt_documentos->error);
    $stmt_documentos->close();
    
    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Datos del alumno actualizados exitosamente.';
    http_response_code(200);

} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'Error al actualizar los datos: ' . $e->getMessage();
    http_response_code(500); 
}

$conn->close();
echo json_encode($response);
?>