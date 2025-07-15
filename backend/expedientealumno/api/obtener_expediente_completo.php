<?php
// backend/php/obtener_expediente_completo.php
require_once '../../db_config.php'; // Asegúrate que este archivo conecte a 'control_escolar'

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Matrícula no proporcionada.'];

if (isset($_GET['matricula'])) {
    $matricula = trim($_GET['matricula']);

    try {
        // Esta es la consulta principal que une todas las tablas necesarias
        $sql = "SELECT
                    -- De la tabla 'alumno'
                    a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, a.genero,
                    a.curp, a.cura, a.fecha_inicio,

                    -- De la tabla 'alumno_info_personal'
                    aip.pais_nacimiento, aip.estado_ciudad_nacimiento, aip.nacionalidad,
                    aip.fecha_nacimiento, aip.telefono AS telefono_personal, aip.correo_personal,

                    -- De la tabla 'alumno_ubicacion' (Residencia)
                    au.pais AS pais_residencia, au.estado_ciudad AS ciudad_residencia,
                    au.colonia_localidad AS colonia_residencia, au.calle AS calle_residencia,
                    au.num_interno AS num_int_residencia, au.num_externo AS num_ext_residencia,

                    -- De la tabla 'programa' y 'institucion'
                    p.nombre_programa, p.nivel_educativo, p.rvoe, p.fecha_rvoe, p.dgp,
                    i.nombre AS nombre_institucion,

                    -- De la tabla 'inscripcion_alumno'
                    ia.permanencia, ia.promocion_aplicada, ia.modalidad_titulacion,
                    ia.ciclo_inicio, ia.ciclo_fin, ia.estatus_alumno,
                    ia.modalidad_alumno AS modalidad,

                    -- De la tabla 'antecedente_academico'
                    aa.nivel_educativo_anterior, aa.nombre_institucion AS nombre_institucion_anterior,
                    aa.ciudad_institucion AS ciudad_institucion_anterior,
                    aa.fecha_inicio AS fecha_inicio_anterior, aa.fecha_fin AS fecha_fin_anterior,

                    -- De la tabla 'alumno_laboral'
                    al.nombre_empresa, al.puesto, al.area_departamento,
                    al.telefono AS telefono_trabajo, al.correo_corporativo,

                    -- De la tabla 'alumno_laboral_ubicacion'
                    alu.pais AS pais_trabajo, alu.estado_ciudad AS ciudad_trabajo,
                    alu.colonia AS colonia_trabajo, alu.calle AS calle_trabajo,
                    alu.num_interno AS num_int_trabajo, alu.num_externo AS num_ext_trabajo

                FROM alumno a
                LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
                LEFT JOIN alumno_ubicacion au ON a.matricula = au.matricula
                LEFT JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
                LEFT JOIN programa p ON a.dgp = p.dgp -- Asumiendo que el DPG está en inscripcion_alumno o alumno
                LEFT JOIN institucion i ON p.id_institucion = i.id_institucion
                LEFT JOIN antecedente_academico aa ON a.matricula = aa.matricula
                LEFT JOIN alumno_laboral al ON a.matricula = al.matricula
                LEFT JOIN alumno_laboral_ubicacion alu ON a.matricula = alu.matricula
                WHERE a.matricula = ?";
                
        // Nota: El JOIN con 'programa' usa ia.dgp. Si el DPG está en la tabla 'alumno', cámbialo a 'a.dgp'.
        // Basado en tu esquema, DPG está en la tabla 'alumno', así que la consulta correcta es:
         $sql_final = "SELECT ... FROM alumno a LEFT JOIN programa p ON a.dgp = p.dgp ..."; // Usa a.dgp para el JOIN
         // La consulta completa y corregida está abajo.
    
        $sql_final = "SELECT
            a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, a.genero, a.curp, a.cura, a.fecha_inicio,
            aip.pais_nacimiento, aip.estado_ciudad_nacimiento, aip.nacionalidad, aip.fecha_nacimiento, aip.telefono AS telefono_personal, aip.correo_personal,
            au.pais AS pais_residencia, au.estado_ciudad AS ciudad_residencia, au.colonia_localidad AS colonia_residencia, au.calle AS calle_residencia, au.num_interno AS num_int_residencia, au.num_externo AS num_ext_residencia,
            p.nombre_programa, p.nivel_educativo, p.rvoe, p.fecha_rvoe, p.dgp,
            i.nombre AS nombre_institucion,
            ia.permanencia, ia.promocion_aplicada, ia.modalidad_titulacion, ia.ciclo_inicio, ia.ciclo_fin, ia.estatus_alumno, ia.modalidad_alumno AS modalidad,
            aa.nivel_educativo_anterior, aa.nombre_institucion AS nombre_institucion_anterior, aa.ciudad_institucion AS ciudad_institucion_anterior, aa.fecha_inicio AS fecha_inicio_anterior, aa.fecha_fin AS fecha_fin_anterior,
            al.nombre_empresa, al.puesto, al.area_departamento, al.telefono AS telefono_trabajo, al.correo_corporativo,
            alu.pais AS pais_trabajo, alu.estado_ciudad AS ciudad_trabajo, alu.colonia AS colonia_trabajo, alu.calle AS calle_trabajo, alu.num_interno AS num_int_trabajo, alu.num_externo AS num_ext_trabajo
        FROM alumno a
        LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
        LEFT JOIN alumno_ubicacion au ON a.matricula = au.matricula
        LEFT JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
        LEFT JOIN programa p ON a.dgp = p.dgp
        LEFT JOIN institucion i ON p.id_institucion = i.id_institucion
        LEFT JOIN antecedente_academico aa ON a.matricula = aa.matricula
        LEFT JOIN alumno_laboral al ON a.matricula = al.matricula
        LEFT JOIN alumno_laboral_ubicacion alu ON a.matricula = alu.matricula
        WHERE a.matricula = ?";


        $stmt = $conn->prepare($sql_final);
        if ($stmt) {
            $stmt->bind_param("s", $matricula);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $response['success'] = true;
                $response['expediente'] = $result->fetch_assoc();
            } else {
                throw new Exception("No se encontró el expediente del alumno con la matrícula: " . htmlspecialchars($matricula));
            }
            $stmt->close();
        } else {
            throw new Exception("Error al preparar la consulta del expediente.");
        }

    } catch (Exception $e) {
        http_response_code(404);
        $response['message'] = $e->getMessage();
    }

    $conn->close();
    echo json_encode($response);
}
?>