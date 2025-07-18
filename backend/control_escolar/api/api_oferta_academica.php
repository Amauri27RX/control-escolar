<?php
// backend/php/api_oferta_academica.php
require_once '../../db_config.php';

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida.'];

try {
    if ($action === 'get_initial_data') {
        $data = [];
        // Obtener ciclos
        $data['ciclos'] = $conn->query("SELECT id_ciclo, codigo FROM ciclo_escolar ORDER BY fecha_inicio DESC")->fetch_all(MYSQLI_ASSOC);
        // Obtener programas
        $data['instituciones'] = $conn->query("SELECT id_institucion, nombre FROM institucion ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
        // Obtener docentes
        $data['docentes'] = $conn->query("SELECT id_docente, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM docente ORDER BY apellidos, nombres")->fetch_all(MYSQLI_ASSOC);
        
        $response = ['success' => true, 'data' => $data];
    }
    
    // --- INICIO DE LA NUEVA SECCIÓN ---
    elseif ($action === 'get_programas_por_institucion') {
        $id_institucion = $_GET['id_institucion'] ?? '';
        
        $sql = "SELECT dgp, nombre_programa FROM programa";
        // Si se proporciona un id_institucion, se filtra. Si no, se devuelven todos.
        if (!empty($id_institucion)) {
            $sql .= " WHERE id_institucion = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $id_institucion);
        } else {
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

        if (empty($id_ciclo) || empty($dgp)) {
            throw new Exception("Ciclo y programa son requeridos.");
        }

        // Obtiene las materias del programa y su oferta actual para el ciclo seleccionado
        $sql = "SELECT 
                    m.clave_materia, 
                    m.nombre, 
                    o.id_oferta, 
                    o.id_docente, 
                    o.cupo, 
                    o.aula
                FROM materia m
                LEFT JOIN oferta_materia o ON m.clave_materia = o.clave_materia AND o.id_ciclo = ?
                WHERE m.programa_dgp = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_ciclo, $dgp);
        $stmt->execute();
        $result = $stmt->get_result();
        $oferta = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        $response = ['success' => true, 'oferta' => $oferta];
    } 
    elseif ($action === 'save_oferta') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id_ciclo = $input['id_ciclo'] ?? 0;
        $clave_materia = $input['clave_materia'] ?? '';
        $id_docente = !empty($input['id_docente']) ? $input['id_docente'] : null;
        $cupo = $input['cupo'] ?? 30;
        $aula = $input['aula'] ?? '';

        if (empty($id_ciclo) || empty($clave_materia)) {
            throw new Exception("Datos incompletos para guardar la oferta.");
        }
        
        // Usamos INSERT ... ON DUPLICATE KEY UPDATE para crear o actualizar la oferta
        // Para esto, necesitamos una llave UNIQUE en (clave_materia, id_ciclo)
        // Primero, nos aseguramos que la llave exista:
        //$conn->query("ALTER TABLE oferta_materia ADD UNIQUE KEY `idx_unica_oferta` (`clave_materia`, `id_ciclo`)");

        $sql = "INSERT INTO oferta_materia (clave_materia, id_ciclo, id_docente, cupo, aula)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE id_docente = VALUES(id_docente), cupo = VALUES(cupo), aula = VALUES(aula)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiss", $clave_materia, $id_ciclo, $id_docente, $cupo, $aula);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Oferta guardada exitosamente.'];
        } else {
            throw new Exception("Error al guardar la oferta: " . $stmt->error);
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