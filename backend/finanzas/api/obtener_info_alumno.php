<?php
header('Content-Type: application/json');
require_once '../../conexion.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $matricula = $_GET['matricula'] ?? '';

    if (empty($matricula)) {
        $response['message'] = 'Matrícula no proporcionada';
        echo json_encode($response);
        exit();
    }

    try {
        // Consulta modificada para incluir JOIN con alumno_info_personal
        $query = "SELECT 
                    a.matricula, 
                    a.nombre, 
                    a.apellido_paterno,
                    a.apellido_materno,
                    aip.telefono,
                    a.correo_institucional,
                    p.nombre_programa as programa,
                    p.nivel_educativo,
                    p.duracion_meses,
                    a.fecha_inicio
                  FROM alumno a
                  JOIN programa p ON a.dgp = p.dgp
                  LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
                  WHERE a.matricula = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $alumno = $result->fetch_assoc();
            
            $response['success'] = true;
            $response['data'] = [
                'matricula' => $alumno['matricula'],
                'nombre' => $alumno['nombre'],
                'apellido_paterno' => $alumno['apellido_paterno'],
                'apellido_materno' => $alumno['apellido_materno'],
                'telefono' => $alumno['telefono'],
                'correo_institucional' => $alumno['correo_institucional'],
                'programa' => $alumno['programa'],
                'nivel_educativo' => $alumno['nivel_educativo'],
                'duracion_meses' => $alumno['duracion_meses'],
                'fecha_inicio' => $alumno['fecha_inicio'] ? date('Y-m-d', strtotime($alumno['fecha_inicio'])) : null


            ];
        } else {
            $response['message'] = 'Alumno no encontrado';
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Error en el servidor: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
?>