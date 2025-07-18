<?php
// backend/php/api_carga_academica.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida.'];

try {
    if ($action === 'search_students') {
        $term = $_GET['term'] ?? '';
        if (strlen($term) < 3) {
            echo json_encode(['success' => true, 'students' => []]);
            exit();
        }
        $likeTerm = "%{$term}%";
        $sql = "SELECT matricula, CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) as nombre_completo 
                FROM alumno 
                WHERE matricula LIKE ? OR CONCAT(nombre, ' ', apellido_paterno) LIKE ?
                LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $likeTerm, $likeTerm);
        $stmt->execute();
        $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $response = ['success' => true, 'students' => $students];
    }
    elseif ($action === 'get_academic_load') {
        $matricula = $_GET['matricula'] ?? '';
        if (empty($matricula)) throw new Exception("Matrícula requerida.");

        $data = [];
        // Obtener info del alumno y su programa
        $sql_info = "SELECT a.matricula, CONCAT(a.nombre, ' ', a.apellido_paterno) as nombre_completo, a.dgp, p.nombre_programa, ia.ciclo_inicio
                     FROM alumno a
                     JOIN programa p ON a.dgp = p.dgp
                     JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
                     WHERE a.matricula = ?";
        $stmt_info = $conn->prepare($sql_info);
        $stmt_info->bind_param("s", $matricula);
        $stmt_info->execute();
        $data['info'] = $stmt_info->get_result()->fetch_assoc();
        $stmt_info->close();

        if (!$data['info']) throw new Exception("Alumno no encontrado.");

        // Obtener todas las materias del plan de estudios del alumno
        $sql_materias = "SELECT clave_materia, nombre FROM materia WHERE programa_dgp = ?";
        $stmt_materias = $conn->prepare($sql_materias);
        $stmt_materias->bind_param("s", $data['info']['dgp']);
        $stmt_materias->execute();
        $data['plan_estudios'] = $stmt_materias->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_materias->close();

        // Obtener las materias en las que el alumno YA está inscrito en su ciclo de inicio
        $sql_inscritas = "SELECT om.clave_materia FROM inscripcion_materia im
                          JOIN oferta_materia om ON im.id_oferta = om.id_oferta
                          JOIN ciclo_escolar ce ON om.id_ciclo = ce.id_ciclo
                          WHERE im.matricula = ? AND ce.codigo = ?";
        $stmt_inscritas = $conn->prepare($sql_inscritas);
        $stmt_inscritas->bind_param("ss", $matricula, $data['info']['ciclo_inicio']);
        $stmt_inscritas->execute();
        $result_inscritas = $stmt_inscritas->get_result()->fetch_all(MYSQLI_ASSOC);
        $data['materias_inscritas'] = array_column($result_inscritas, 'clave_materia');
        $stmt_inscritas->close();

        $response = ['success' => true, 'data' => $data];
    }
    elseif ($action === 'update_academic_load') {
        $input = json_decode(file_get_contents('php://input'), true);
        $matricula = $input['matricula'] ?? '';
        $materias_a_inscribir = $input['materias'] ?? []; // Array de claves de materia
        $ciclo_inicio = $input['ciclo_inicio'] ?? '';

        if (empty($matricula) || empty($ciclo_inicio)) throw new Exception("Matrícula y ciclo son requeridos.");

        $conn->begin_transaction();

        // 1. Obtener id_ciclo
        $stmt_ciclo = $conn->prepare("SELECT id_ciclo FROM ciclo_escolar WHERE codigo = ?");
        $stmt_ciclo->bind_param("s", $ciclo_inicio);
        $stmt_ciclo->execute();
        $id_ciclo = $stmt_ciclo->get_result()->fetch_assoc()['id_ciclo'];
        $stmt_ciclo->close();
        if(!$id_ciclo) throw new Exception("Ciclo '{$ciclo_inicio}' no encontrado.");

        // 2. Borrar inscripciones existentes del alumno en ese ciclo para empezar de cero
        $sql_delete = "DELETE im FROM inscripcion_materia im
                       JOIN oferta_materia om ON im.id_oferta = om.id_oferta
                       WHERE im.matricula = ? AND om.id_ciclo = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("si", $matricula, $id_ciclo);
        $stmt_delete->execute();
        $stmt_delete->close();

        // 3. Inscribir en las nuevas materias seleccionadas
        if (!empty($materias_a_inscribir)) {
            $sql_insert = "INSERT INTO inscripcion_materia (matricula, id_oferta)
                           SELECT ?, o.id_oferta
                           FROM oferta_materia o
                           WHERE o.id_ciclo = ? AND o.clave_materia = ?";
            $stmt_insert = $conn->prepare($sql_insert);

            foreach ($materias_a_inscribir as $clave_materia) {
                $stmt_insert->bind_param("sis", $matricula, $id_ciclo, $clave_materia);
                $stmt_insert->execute();
            }
            $stmt_insert->close();
        }
        
        $conn->commit();
        $response = ['success' => true, 'message' => 'Carga académica actualizada exitosamente.'];
    }
} catch (Exception $e) {
    if($conn->in_transaction) $conn->rollback();
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>