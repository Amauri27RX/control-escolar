<?php
// --- Configuración de la Base de Datos ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'control_escolar';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Error desconocido al procesar la inscripción.'];

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
    $matricula = $_POST['matricula'] ?? null;
    $dgp_programa = $_POST['dgp'] ?? null;

    // --- 1. Insertar en la tabla `alumno` ---
    $nombre_alumno = $_POST['nombre'] ?? null;
    $apellido_paterno_alumno = $_POST['apellido_paterno'] ?? null;
    $apellido_materno_alumno = $_POST['apellido_materno'] ?? null;
    $curp_alumno = $_POST['curp'] ?? null;
    $cura_alumno = $_POST['cura'] ?? null;
    $fecha_inicio_alumno = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : null;
    
$genero_input = $_POST['genero'] ?? ''; // Obtendrá 'M', 'F', 'O', o ""
$genero_db = null;

if (in_array($genero_input, ['M', 'F', 'O'])) {
    $genero_db = $genero_input;
} else {
    // Si el género es un campo obligatorio y el valor no es válido (ej. "" por no seleccionar)
    // deberías lanzar un error o asignar un valor por defecto si tu ENUM lo permite y tiene sentido.
    // Dado que tu ENUM es NOT NULL, un valor vacío o inválido causará un error SQL.
    // Es mejor validar en el frontend o aquí que se haya seleccionado una opción válida.
    // Para este ejemplo, si no es M, F, O, y se requiere un valor, lanzamos error.
    // Si el campo fuera opcional y permitiera NULL, $genero_db = null; sería adecuado.
    if (empty($genero_input)) { // Si no se seleccionó nada y el value es ""
        // Decide cómo manejar esto: ¿es obligatorio? ¿Hay un default?
        // Por ahora, si es obligatorio y no se seleccionó, es un error.
        throw new Exception("Por favor, seleccione un género válido de la lista.");
    } else {
        // Si se envió algo pero no es M, F, O (no debería pasar con el select)
        throw new Exception("Valor de género inválido: " . htmlspecialchars($genero_input));
    }
}
    
    $generacion_alumno = $_POST['ciclo_inicio'] ?? null; 
    $correo_institucional_alumno = null; 
    $nacionalidad_alumno_table = $_POST['nacionalidad'] ?? null; 

    $sql_alumno = "INSERT INTO alumno (
        matricula, dgp, nombre, apellido_paterno, apellido_materno, curp, genero, cura, 
        correo_institucional, fecha_inicio, generacion, nacionalidad
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_alumno = $conn->prepare($sql_alumno);
    if ($stmt_alumno === false) throw new Exception("Error al preparar consulta alumno: " . $conn->error);
    $stmt_alumno->bind_param("ssssssssssss", 
        $matricula, $dgp_programa, $nombre_alumno, $apellido_paterno_alumno, $apellido_materno_alumno, 
        $curp_alumno, $genero_db, $cura_alumno, $correo_institucional_alumno, 
        $fecha_inicio_alumno, $generacion_alumno, $nacionalidad_alumno_table
    );
    if (!$stmt_alumno->execute()) throw new Exception("Error al insertar en alumno: " . $stmt_alumno->error . " (Matrícula: ".$matricula.")");
    $stmt_alumno->close();

    // --- 2. Insertar en la tabla `alumno_info_personal` ---
    $pais_nacimiento_form = $_POST['pais_nacimiento'] ?? null;
    $ciudad_nacimiento_form = $_POST['ciudad_nacimiento'] ?? null;
    $nacionalidad_form = $_POST['nacionalidad'] ?? null;
    $fecha_nacimiento_form = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $correo_personal_form = $_POST['correo_personal'] ?? null;
    $telefono_personal_form = $_POST['telefono_personal'] ?? null;

    $sql_info_personal = "INSERT INTO alumno_info_personal (
        matricula, pais_nacimiento, estado_ciudad_nacimiento, nacionalidad, 
        fecha_nacimiento, correo_personal, telefono
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_info_personal = $conn->prepare($sql_info_personal);
    if ($stmt_info_personal === false) throw new Exception("Error al preparar consulta info_personal: " . $conn->error);
    $stmt_info_personal->bind_param("sssssss",
        $matricula, 
        $pais_nacimiento_form, 
        $ciudad_nacimiento_form, 
        $nacionalidad_form,      
        $fecha_nacimiento_form, 
        $correo_personal_form,
        $telefono_personal_form
    );
    if (!$stmt_info_personal->execute()) throw new Exception("Error al insertar en alumno_info_personal: " . $stmt_info_personal->error);
    $stmt_info_personal->close();

    // --- 3. Insertar en la tabla `alumno_ubicacion` ---
    $pais_residencia_form = $_POST['pais_residencia'] ?? null;
    $ciudad_residencia_form = $_POST['ciudad_residencia'] ?? null;
    $colonia_residencia_form = $_POST['colonia_residencia'] ?? null;
    $calle_residencia_form = $_POST['calle_residencia'] ?? null;
    $num_int_residencia_form = $_POST['num_int_residencia'] ?? null;
    $num_ext_residencia_form = $_POST['num_ext_residencia'] ?? null;
    $codigo_postal_form = null; // No está en el formulario actual

    $sql_ubicacion = "INSERT INTO alumno_ubicacion (
        matricula, pais, estado_ciudad, colonia_localidad, calle, 
        num_interno, num_externo, codigo_postal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_ubicacion = $conn->prepare($sql_ubicacion);
    if ($stmt_ubicacion === false) throw new Exception("Error al preparar consulta ubicacion: " . $conn->error);
    $stmt_ubicacion->bind_param("ssssssss",
        $matricula, 
        $pais_residencia_form, 
        $ciudad_residencia_form, 
        $colonia_residencia_form, 
        $calle_residencia_form,
        $num_int_residencia_form, 
        $num_ext_residencia_form, 
        $codigo_postal_form 
    );
    if (!$stmt_ubicacion->execute()) throw new Exception("Error al insertar en alumno_ubicacion: " . $stmt_ubicacion->error);
    $stmt_ubicacion->close();

    // --- 4. Insertar en la tabla `alumno_laboral` (datos principales) ---
    $nombre_empresa_form = $_POST['nombre_empresa'] ?? null;
    $puesto_trabajo_form = $_POST['puesto_trabajo'] ?? null;
    $area_trabajo_form = $_POST['area_trabajo'] ?? null;
    $telefono_trabajo_form = $_POST['telefono_trabajo'] ?? null;
    $correo_trabajo_form = $_POST['correo_trabajo'] ?? null;
    
    $sql_laboral = "INSERT INTO alumno_laboral (
        matricula, nombre_empresa, puesto, area_departamento, telefono, correo_corporativo
    ) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_laboral = $conn->prepare($sql_laboral);
    if ($stmt_laboral === false) throw new Exception("Error al preparar consulta laboral: " . $conn->error);
    $stmt_laboral->bind_param("ssssss",
        $matricula, 
        $nombre_empresa_form, 
        $puesto_trabajo_form, 
        $area_trabajo_form,
        $telefono_trabajo_form, 
        $correo_trabajo_form
    );
    if (!empty($nombre_empresa_form)) { // Insertar solo si hay nombre de empresa
      if (!$stmt_laboral->execute()) throw new Exception("Error al insertar en alumno_laboral: " . $stmt_laboral->error);
    }
    $stmt_laboral->close();

    // --- 5. Insertar en la tabla `alumno_laboral_ubicacion` (dirección laboral) ---
    $pais_trabajo_form = $_POST['pais_trabajo'] ?? null;
    $ciudad_trabajo_form = $_POST['ciudad_trabajo'] ?? null;
    $colonia_trabajo_form = $_POST['colonia_trabajo'] ?? null;
    $calle_trabajo_form = $_POST['calle_trabajo'] ?? null;
    $num_int_trabajo_form = $_POST['num_int_trabajo'] ?? null;
    $num_ext_trabajo_form = $_POST['num_ext_trabajo'] ?? null;

    $sql_laboral_ubicacion = "INSERT INTO alumno_laboral_ubicacion (
        matricula, pais, estado_ciudad, colonia, calle, num_interno, num_externo
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_laboral_ubicacion = $conn->prepare($sql_laboral_ubicacion);
    if ($stmt_laboral_ubicacion === false) throw new Exception("Error al preparar consulta laboral_ubicacion: " . $conn->error);
    $stmt_laboral_ubicacion->bind_param("sssssss",
        $matricula, 
        $pais_trabajo_form, 
        $ciudad_trabajo_form, 
        $colonia_trabajo_form, 
        $calle_trabajo_form,
        $num_int_trabajo_form, 
        $num_ext_trabajo_form
    );
    if (!empty($pais_trabajo_form) || !empty($ciudad_trabajo_form) || !empty($calle_trabajo_form) || !empty($nombre_empresa_form) ) { // Insertar si hay datos de empresa o dirección
         if (!$stmt_laboral_ubicacion->execute()) throw new Exception("Error al insertar en alumno_laboral_ubicacion: " . $stmt_laboral_ubicacion->error);
    }
    $stmt_laboral_ubicacion->close();

    // --- 6. Insertar en la tabla `antecedente_academico` ---
    $nivel_anterior_form = $_POST['nivel_anterior'] ?? null;
    $nombre_institucion_form = $_POST['nombre_institucion'] ?? null;
    $ubicacion_institucion_form = $_POST['ubicacion_institucion'] ?? null;
    $fecha_inicio_antecedentes_form = !empty($_POST['fecha_inicio_antecedentes']) ? $_POST['fecha_inicio_antecedentes'] : null;
    $fecha_finalizacion_form = !empty($_POST['fecha_finalizacion']) ? $_POST['fecha_finalizacion'] : null;

    $sql_antecedente = "INSERT INTO antecedente_academico (
        matricula, nivel_educativo_anterior, nombre_institucion, ciudad_institucion, fecha_inicio, fecha_fin
    ) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_antecedente = $conn->prepare($sql_antecedente);
    if ($stmt_antecedente === false) throw new Exception("Error al preparar consulta antecedente: " . $conn->error);
    $stmt_antecedente->bind_param("ssssss",
        $matricula, 
        $nivel_anterior_form, 
        $nombre_institucion_form, 
        $ubicacion_institucion_form, 
        $fecha_inicio_antecedentes_form,
        $fecha_finalizacion_form
    );
    if (!empty($nivel_anterior_form) || !empty($nombre_institucion_form)) { 
        if (!$stmt_antecedente->execute()) throw new Exception("Error al insertar en antecedente_academico: " . $stmt_antecedente->error);
    }
    $stmt_antecedente->close();

    // --- 7. Insertar en la tabla `inscripcion_alumno` ---
    $permanencia_form = $_POST['permanencia'] ?? null;
    $promocion_form = $_POST['promocion'] ?? null;
    $modalidad_titulacion_form = $_POST['modalidad_titulacion'] ?? null;
    $ciclo_inicio_form = $_POST['ciclo_inicio'] ?? null;
    $ciclo_fin_form = $_POST['ciclo_fin'] ?? null;
    $modalidad_alumno_form = $_POST['modalidad'] ?? null;
    $estatus_alumno_form = $_POST['estatus_alumno'] ?? 'Activo'; 

    $estatus_validos = ['Activo', 'Irregular', 'Baja', 'Egresado', 'Suspendido'];
if (!in_array($estatus_alumno_form, $estatus_validos)) {
    // Considera un valor por defecto o lanzar un error si es inválido
    $estatus_alumno_form = 'Activo'; // Opcional: Forzar un default seguro
    // throw new Exception("Valor de estatus de alumno inválido.");
}

$sql_inscripcion_alumno = "INSERT INTO inscripcion_alumno (
    matricula, permanencia, promocion_aplicada, modalidad_titulacion, 
    ciclo_inicio, ciclo_fin, modalidad_alumno, estatus_alumno
    -- fecha_inscripcion tiene DEFAULT CURRENT_DATE
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; // 8 placeholders ahora
$stmt_inscripcion_alumno = $conn->prepare($sql_inscripcion_alumno);
if ($stmt_inscripcion_alumno === false) throw new Exception("Error al preparar consulta inscripcion_alumno: " . $conn->error);
$stmt_inscripcion_alumno->bind_param("ssssssss", // 8 's'
    $matricula,
    $permanencia_form,
    $promocion_form, 
    $modalidad_titulacion_form,
    $ciclo_inicio_form,
    $ciclo_fin_form,
    $modalidad_alumno_form,
    $estatus_alumno_form // Nueva variable
);
if (!$stmt_inscripcion_alumno->execute()) throw new Exception("Error al insertar en inscripcion_alumno: " . $stmt_inscripcion_alumno->error);
$stmt_inscripcion_alumno->close();

    // --- 8. Insertar en la tabla `documentos_alumno` ---
    $acta_nacimiento_doc_form = $_POST['acta_nacimiento_doc'] ?? null;
    $curp_doc_form = $_POST['curp_doc'] ?? null;
    $certificado_estudios_doc_form = $_POST['certificado_estudios_doc'] ?? null;
    $titulo_universitario_doc_form = $_POST['titulo_universitario_doc'] ?? null;
    $comprobante_domicilio_doc_form = $_POST['comprobante_domicilio_doc'] ?? null;
    $carta_otem_doc_form = $_POST['carta_otem_doc'] ?? null;

    $sql_documentos = "INSERT INTO documentos_alumno (
        matricula, acta_nacimiento, curp_doc, certificado_estudios, 
        titulo_universitario, comprobante_domicilio, carta_otem
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_documentos = $conn->prepare($sql_documentos);
    if ($stmt_documentos === false) throw new Exception("Error al preparar consulta documentos_alumno: " . $conn->error);
    $stmt_documentos->bind_param("sssssss",
        $matricula,
        $acta_nacimiento_doc_form, 
        $curp_doc_form,            
        $certificado_estudios_doc_form,
        $titulo_universitario_doc_form,
        $comprobante_domicilio_doc_form,
        $carta_otem_doc_form      
    );
    if (!$stmt_documentos->execute()) throw new Exception("Error al insertar en documentos_alumno: " . $stmt_documentos->error);
    $stmt_documentos->close();

    $codigo_ciclo_inicio = $_POST['ciclo_inicio'] ?? null; 
    inscribirAlumnoEnMaterias($conn, $matricula, $dgp_programa, $codigo_ciclo_inicio);
    
    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Inscripción procesada exitosamente con el nuevo esquema completo.';
    http_response_code(200);

} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'Error en el proceso de inscripción: ' . $e->getMessage();
    http_response_code(500); 
}

$conn->close();
echo json_encode($response);


function inscribirAlumnoEnMaterias($conn, $matricula, $dgp, $codigo_ciclo) {
    if (empty($matricula) || empty($dgp) || empty($codigo_ciclo)) {
        throw new Exception("Datos insuficientes para la inscripción automática de materias.");
    }

    // 1. Encontrar el ID del ciclo escolar
    $stmt_ciclo = $conn->prepare("SELECT id_ciclo FROM ciclo_escolar WHERE codigo = ?");
    $stmt_ciclo->bind_param("s", $codigo_ciclo);
    $stmt_ciclo->execute();
    $result_ciclo = $stmt_ciclo->get_result();
    if ($result_ciclo->num_rows === 0) {
        // Si el ciclo no existe, no podemos continuar.
        // Podrías crear el ciclo aquí o lanzar un error. Por ahora, lanzamos error.
        throw new Exception("El ciclo escolar '{$codigo_ciclo}' no existe. Por favor, créelo en la gestión de ciclos.");
    }
    $id_ciclo = $result_ciclo->fetch_assoc()['id_ciclo'];
    $stmt_ciclo->close();

    // 2. Obtener todas las claves de materia para el programa del alumno
    // Usando la estructura de tabla 'materia' con la columna 'programa_dgp'
    $stmt_materias = $conn->prepare("SELECT clave_materia FROM materia WHERE programa_dgp = ?");
    $stmt_materias->bind_param("s", $dgp);
    $stmt_materias->execute();
    $result_materias = $stmt_materias->get_result();
    $claves_materias = $result_materias->fetch_all(MYSQLI_ASSOC);
    $stmt_materias->close();

    if (empty($claves_materias)) {
        // No hay materias en el plan de estudios, no hacemos nada.
        return; 
    }

    // 3. Preparar la inserción en inscripcion_materia
    $sql_insert = "INSERT INTO inscripcion_materia (matricula, id_oferta) 
                   SELECT ?, o.id_oferta
                   FROM oferta_materia o
                   WHERE o.id_ciclo = ? AND o.clave_materia = ?";
    $stmt_insert = $conn->prepare($sql_insert);

    // 4. Iterar e inscribir al alumno en la oferta de cada materia para ese ciclo
    foreach ($claves_materias as $materia) {
        $clave_materia = $materia['clave_materia'];
        
        // El 'bind_param' debe hacerse dentro del bucle ya que la clave_materia cambia
        $stmt_insert->bind_param("sis", $matricula, $id_ciclo, $clave_materia);
        
        if (!$stmt_insert->execute()) {
            // Esto puede fallar si no hay una oferta para esa materia en ese ciclo.
            // Es importante registrar esto como una advertencia en lugar de detener todo.
            error_log("ADVERTENCIA: No se pudo inscribir la materia '{$clave_materia}' para la matrícula '{$matricula}' en el ciclo '{$codigo_ciclo}'. Verifique que la materia tenga una oferta creada para ese ciclo.");
        }
    }
    $stmt_insert->close();
}
?>