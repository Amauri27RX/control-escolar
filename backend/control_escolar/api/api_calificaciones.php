<?php
// backend/php/api_calificaciones.php
require_once '../../db_config.php';

header('Content-Type: application/json');

// Determinar la acción a realizar
$action = $_GET['action'] ?? '';
$matricula = $_GET['matricula'] ?? null;
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($action) {
                case 'get_filters_data':
            $data = [];
            $data['instituciones'] = $conn->query("SELECT id_institucion, nombre FROM institucion ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
            $data['niveles'] = $conn->query("SELECT DISTINCT nivel_educativo FROM programa WHERE nivel_educativo IS NOT NULL ORDER BY nivel_educativo")->fetch_all(MYSQLI_ASSOC);
            // No cargamos programas aquí, se cargarán dinámicamente
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'get_programs':
            $id_institucion = $_GET['institucion'] ?? '';
            $nivel = $_GET['nivel'] ?? '';
            
            $sql = "SELECT dgp, nombre_programa FROM programa WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($id_institucion)) {
                $sql .= " AND id_institucion = ?";
                $params[] = $id_institucion;
                $types .= "s";
            }
            if (!empty($nivel)) {
                $sql .= " AND nivel_educativo = ?";
                $params[] = $nivel;
                $types .= "s";
            }
            $sql .= " ORDER BY nombre_programa";

            $stmt = $conn->prepare($sql);
            if (!empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $programas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'programs' => $programas]);
            break;

        case 'get_all_students':
            $institucion_filtro = $_GET['institucion'] ?? '';
            $nivel_filtro = $_GET['nivel'] ?? '';
            $programa_filtro = $_GET['programa'] ?? '';

            $sql = "SELECT 
                        a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, 
                        p.nivel_educativo, ia.estatus_alumno 
                    FROM alumno a
                    JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
                    JOIN programa p ON a.dgp = p.dgp
                    WHERE 1=1";
            
            $params = [];
            $types = "";

            if (!empty($institucion_filtro)) {
                $sql .= " AND p.id_institucion = ?";
                $params[] = $institucion_filtro;
                $types .= "s";
            }
            if (!empty($nivel_filtro)) {
                $sql .= " AND p.nivel_educativo = ?";
                $params[] = $nivel_filtro;
                $types .= "s";
            }
            if (!empty($programa_filtro)) {
                $sql .= " AND a.dgp = ?";
                $params[] = $programa_filtro;
                $types .= "s";
            }
            $sql .= " ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";

            $stmt = $conn->prepare($sql);
            if (!empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'students' => $students]);
            break;
            
        case 'get_student_details':
            // Obtiene el detalle completo de un alumno
            if (!$matricula) throw new Exception("Matrícula no proporcionada.");
            
            $details = [];

            // Información General y Académica
            $sql_general = "SELECT 
                                a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, a.genero, a.dgp,
                                p.nombre_programa, p.nivel_educativo,
                                ia.fecha_inscripcion, ia.estatus_alumno, ia.ciclo_finalizado AS fecha_termino
                            FROM alumno a
                            JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
                            JOIN programa p ON a.dgp = p.dgp
                            WHERE a.matricula = ?";
            $stmt_general = $conn->prepare($sql_general);
            $stmt_general->bind_param("s", $matricula);
            $stmt_general->execute();
            $details['general'] = $stmt_general->get_result()->fetch_assoc();
            $stmt_general->close();

            if(!$details['general']) throw new Exception("No se encontró al alumno.");

            // Calificaciones del Alumno
            $sql_grades = "SELECT 
                                m.nombre AS nombre_materia, 
                                c.codigo AS ciclo,
                                im.calificacion,
                                CONCAT(d.nombres, ' ', d.apellidos) AS nombre_docente,
                                m.clave_materia,
                                im.id_inscripcion
                           FROM inscripcion_materia im
                           JOIN oferta_materia o ON im.id_oferta = o.id_oferta
                           JOIN materia m ON o.clave_materia = m.clave_materia AND o.programa_dgp = m.programa_dgp
                           JOIN ciclo_escolar c ON o.id_ciclo = c.id_ciclo
                           LEFT JOIN docente d ON o.id_docente = d.id_docente
                           WHERE im.matricula = ?
                           ORDER BY c.fecha_inicio, m.nombre";
            $stmt_grades = $conn->prepare($sql_grades);
            $stmt_grades->bind_param("s", $matricula);
            $stmt_grades->execute();
            $details['grades'] = $stmt_grades->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_grades->close();

            // Observaciones
            $sql_notes = "SELECT id_observacion, observacion, autor, fecha_creacion FROM observaciones_alumno WHERE matricula = ? ORDER BY fecha_creacion DESC";
            $stmt_notes = $conn->prepare($sql_notes);
            $stmt_notes->bind_param("s", $matricula);
            $stmt_notes->execute();
            $details['notes'] = $stmt_notes->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_notes->close();
            
            // Total de materias del plan de estudios
            $sql_plan = "SELECT COUNT(*) as total FROM materia WHERE programa_dgp = ?";
            $stmt_plan = $conn->prepare($sql_plan);
            $stmt_plan->bind_param("s", $details['general']['dgp']);
            $stmt_plan->execute();
            $details['total_materias_plan'] = $stmt_plan->get_result()->fetch_assoc()['total'];
            $stmt_plan->close();

            echo json_encode(['success' => true, 'details' => $details]);
            break;

        case 'save_grade':
            // Guarda o actualiza la calificación
            if (!$input || !isset($input['id_inscripcion'], $input['calificacion'])) {
                throw new Exception("Datos incompletos para guardar calificación.");
            }
            $sql_save = "UPDATE inscripcion_materia SET calificacion = ?, aprobada = ? WHERE id_inscripcion = ?";
            $stmt_save = $conn->prepare($sql_save);
            $calificacion = ($input['calificacion'] === 'NP' || $input['calificacion'] === '') ? null : floatval($input['calificacion']);
            $aprobada = ($calificacion !== null && $calificacion >= 70) ? 1 : 0; // Asumiendo que 70 es aprobatorio
            $stmt_save->bind_param("dii", $calificacion, $aprobada, $input['id_inscripcion']);
            $stmt_save->execute();

            if ($stmt_save->affected_rows > 0) {
                 echo json_encode(['success' => true, 'message' => 'Calificación guardada.']);
            } else {
                 echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
            }
            $stmt_save->close();
            break;

        case 'add_note':
            // Añade una nueva observación
            if (!$input || !isset($input['matricula'], $input['nota'])) {
                throw new Exception("Datos incompletos para añadir nota.");
            }
            // El autor debería venir de la sesión del usuario logueado
            $autor = 'Control Escolar'; 
            $sql_note = "INSERT INTO observaciones_alumno (matricula, observacion, autor, tipo) VALUES (?, ?, ?, 'Académica')";
            $stmt_note = $conn->prepare($sql_note);
            $stmt_note->bind_param("sss", $input['matricula'], $input['nota'], $autor);
            $stmt_note->execute();
            $stmt_note->close();
            echo json_encode(['success' => true, 'message' => 'Nota añadida.']);
            break;

        default:
            throw new Exception("Acción no válida.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>