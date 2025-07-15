<?php
// --- Configuración de la Base de Datos ---
$db_host = 'localhost';
$db_user = 'root'; // Reemplaza si es necesario
$db_pass = '';     // Reemplaza si es necesario
$db_name = 'control_escolar';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Matrícula no proporcionada.'];

if (isset($_GET['matricula'])) {
    $matricula_a_buscar = $_GET['matricula'];

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        $response['message'] = 'Error de conexión a la base de datos: ' . $conn->connect_error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }
    $conn->set_charset("utf8mb4");

    // Consulta SQL actualizada para la nueva estructura de BD
    $sql = "SELECT
                -- Tabla: alumno
                a.matricula, a.dgp, a.nombre, a.apellido_paterno, a.apellido_materno, a.curp, a.genero, a.cura,
                a.correo_institucional, a.fecha_inicio AS fecha_ingreso, a.generacion,
                a.nacionalidad AS nacionalidad_tabla_alumno, -- Nacionalidad de la tabla alumno (si se usa de forma distinta)

                -- Tabla: programa (para detalles del programa del alumno)
                p.nivel_educativo AS nivel_educativo, -- ID para el select 'nivel_educativo' del form
                p.rvoe, p.fecha_rvoe,
                p.id_institucion AS id_institucion_programa, -- ID para el select 'institucion' del form

                -- Tabla: alumno_info_personal
                aip.pais_nacimiento, aip.estado_ciudad_nacimiento AS ciudad_nacimiento,
                aip.nacionalidad, -- Este es el que usualmente se mapea al campo 'nacionalidad' del form
                aip.fecha_nacimiento, aip.correo_personal, aip.telefono AS telefono_personal,

                -- Tabla: alumno_ubicacion (Residencia del alumno)
                au.pais AS pais_residencia, au.estado_ciudad AS ciudad_residencia,
                au.colonia_localidad AS colonia_residencia, au.calle AS calle_residencia,
                au.num_interno AS num_int_residencia, au.num_externo AS num_ext_residencia,
                au.codigo_postal AS cp_residencia, -- El formulario no lo tiene, pero se recupera

                -- Tabla: alumno_laboral (Datos principales del trabajo)
                al.nombre_empresa, al.puesto AS puesto_trabajo, al.area_departamento AS area_trabajo,
                al.telefono AS telefono_trabajo, al.correo_corporativo AS correo_trabajo,

                -- Tabla: alumno_laboral_ubicacion (Dirección del trabajo)
                alu.pais AS pais_trabajo, alu.estado_ciudad AS ciudad_trabajo,
                alu.colonia AS colonia_trabajo, alu.calle AS calle_trabajo,
                alu.num_interno AS num_int_trabajo, alu.num_externo AS num_ext_trabajo,

                -- Tabla: antecedente_academico
                aa.nivel_educativo_anterior AS nivel_anterior,
                aa.nombre_institucion, -- Coincide con el id 'nombre_institucion' del form
                aa.ciudad_institucion AS ubicacion_institucion, -- Coincide con 'ubicacion_institucion'
                aa.fecha_inicio AS fecha_inicio_antecedentes,
                aa.fecha_fin AS fecha_finalizacion,

                -- Tabla: inscripcion_alumno
                ia.permanencia, ia.promocion_aplicada AS promocion, ia.modalidad_titulacion,
                ia.ciclo_inicio, ia.ciclo_fin,
                ia.modalidad_alumno AS modalidad, -- Asumiendo que añadiste 'modalidad_alumno' a 'inscripcion_alumno'
                ia.estatus_alumno, -- NUEVO CAMPO SELECCIONADO
                ia.fecha_inscripcion,

                -- Tabla: documentos_alumno
                doc.acta_nacimiento AS acta_nacimiento_doc, doc.curp_doc,
                doc.certificado_estudios AS certificado_estudios_doc,
                doc.titulo_universitario AS titulo_universitario_doc,
                doc.comprobante_domicilio AS comprobante_domicilio_doc,
                doc.carta_otem AS carta_otem_doc
            FROM alumno a
            LEFT JOIN programa p ON a.dgp = p.dgp
            LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
            LEFT JOIN alumno_ubicacion au ON a.matricula = au.matricula
            LEFT JOIN alumno_laboral al ON a.matricula = al.matricula
            LEFT JOIN alumno_laboral_ubicacion alu ON a.matricula = alu.matricula
            LEFT JOIN antecedente_academico aa ON a.matricula = aa.matricula
            LEFT JOIN inscripcion_alumno ia ON a.matricula = ia.matricula -- Ya estaba
            LEFT JOIN documentos_alumno doc ON a.matricula = doc.matricula
            WHERE a.matricula = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $matricula_a_buscar);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            // Los alias en la consulta SQL están diseñados para coincidir con los IDs de los campos del formulario
            // que usa 'formStructure' en JavaScript para el autocompletado.
            $response = ['success' => true, 'data' => $data];
        } else {
            $response['message'] = 'Alumno no encontrado con esa matrícula.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Error al preparar la consulta para obtener datos del alumno: ' . $conn->error;
        http_response_code(500);
    }
    $conn->close();
}

echo json_encode($response);
?>