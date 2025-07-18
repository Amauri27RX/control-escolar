<?php
// backend/php/api_oferta_academica.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida.'];

try {
    if ($action === 'get_initial_data') {
        $data = [];
        $data['ciclos'] = $conn->query("SELECT id_ciclo, codigo FROM ciclo_escolar ORDER BY fecha_inicio DESC")->fetch_all(MYSQLI_ASSOC);
        $data['instituciones'] = $conn->query("SELECT id_institucion, nombre FROM institucion ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
        $data['docentes'] = $conn->query("SELECT id_docente, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM docente ORDER BY apellidos, nombres")->fetch_all(MYSQLI_ASSOC);
        $response = ['success' => true, 'data' => $data];
    } 
    elseif ($action === 'get_programas_por_institucion') {
        $id_institucion = $_GET['id_institucion'] ?? '';
        
        $sql = "SELECT dgp, nombre_programa FROM programa";
        if (!empty($id_institucion)) {
            $sql .= " WHERE id_institucion = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $id_institucion);
        } else {
            $sql .= " ORDER BY nombre_programa ASC";
            $stmt = $conn->prepare($sql);
        }
        
        $stmt->execute();
        $programas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $response = ['success' => true, 'programas' => $programas];
    }
    elseif ($action === 'get_oferta') {
        $id_ciclo = $_GET['id_ciclo'] ?? 0;
        $dgp = $_GET['dgp'] ?? '';
        if (empty($id_ciclo) || empty($dgp)) throw new Exception("Ciclo y programa son requeridos.");

        $sql = "SELECT 
                    m.clave_materia, m.nombre, o.id_oferta, o.id_docente, o.cupo, o.aula
                FROM materia m
                LEFT JOIN oferta_materia o ON m.clave_materia = o.clave_materia AND m.programa_dgp = o.programa_dgp AND o.id_ciclo = ?
                WHERE m.programa_dgp = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_ciclo, $dgp);
        $stmt->execute();
        $oferta = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $response = ['success' => true, 'oferta' => $oferta];
    } 
    elseif ($action === 'save_oferta') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id_ciclo = $input['id_ciclo'] ?? 0;
        $dgp = $input['dgp'] ?? '';
        $clave_materia = $input['clave_materia'] ?? '';
        $id_docente = !empty($input['id_docente']) ? $input['id_docente'] : null;
        $cupo = !empty($input['cupo']) ? $input['cupo'] : 30;
        $aula = $input['aula'] ?? '';

        if (empty($id_ciclo) || empty($dgp) || empty($clave_materia)) throw new Exception("Datos incompletos para guardar la oferta.");
        
        // La llave UNIQUE en (clave_materia, programa_dgp, id_ciclo) permite usar esta consulta de forma segura.
        $sql = "INSERT INTO oferta_materia (clave_materia, programa_dgp, id_ciclo, id_docente, cupo, aula)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE id_docente = VALUES(id_docente), cupo = VALUES(cupo), aula = VALUES(aula)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiiss", $clave_materia, $dgp, $id_ciclo, $id_docente, $cupo, $aula);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Oferta guardada exitosamente.'];
        } else {
            throw new Exception("Error al guardar la oferta: " . $stmt->error);
        }
        $stmt->close();
    }
    elseif ($action === 'clonar_oferta') {
        $id_ciclo_destino = $_GET['id_ciclo_destino'] ?? 0;
        $id_ciclo_origen = $_GET['id_ciclo_origen'] ?? 0;
        $dgp = $_GET['dgp'] ?? '';

        if (empty($id_ciclo_destino) || empty($id_ciclo_origen) || empty($dgp)) {
            throw new Exception("Faltan datos para clonar la oferta.");
        }

        $sql = "INSERT INTO oferta_materia (clave_materia, programa_dgp, id_ciclo, id_docente, cupo, aula)
                SELECT clave_materia, programa_dgp, ?, id_docente, cupo, aula
                FROM oferta_materia
                WHERE id_ciclo = ? AND programa_dgp = ?
                ON DUPLICATE KEY UPDATE id_docente = VALUES(id_docente)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $id_ciclo_destino, $id_ciclo_origen, $dgp);
        
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => $stmt->affected_rows . ' ofertas han sido clonadas al nuevo ciclo.'];
        } else {
            throw new Exception("Error al clonar la oferta: " . $stmt->error);
        }
        $stmt->close();
    }

} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>