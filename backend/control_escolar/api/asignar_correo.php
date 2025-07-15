<?php
// backend/php/asignar_correo.php
require_once 'db_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Datos incompletos.'];

if (isset($_POST['matricula']) && isset($_POST['correo_institucional'])) {
    $matricula = trim($_POST['matricula']);
    $correo_institucional = trim($_POST['correo_institucional']);

    if (empty($matricula) || empty($correo_institucional) || !filter_var($correo_institucional, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Matrícula o formato de correo inválido.';
        echo json_encode($response);
        exit();
    }

    // Primero, verificar que el correo no esté ya asignado a OTRO alumno
    $sql_check = "SELECT matricula FROM alumno WHERE correo_institucional = ?";
    $stmt_check = $conn->prepare($sql_check);
    if($stmt_check){
        $stmt_check->bind_param("s", $correo_institucional);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            $row_check = $result_check->fetch_assoc();
            if ($row_check['matricula'] !== $matricula) {
                $response['message'] = 'Este correo ya está asignado a otra matrícula.';
                echo json_encode($response);
                $stmt_check->close();
                $conn->close();
                exit();
            }
        }
        $stmt_check->close();
    } else {
        $response['message'] = 'Error preparando verificación de duplicados: ' . $conn->error;
        http_response_code(500);
        echo json_encode($response);
        exit();
    }


    // Actualizar el correo del alumno
    $sql_update = "UPDATE alumno SET correo_institucional = ? WHERE matricula = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update) {
        $stmt_update->bind_param("ss", $correo_institucional, $matricula);
        if ($stmt_update->execute()) {
            if ($stmt_update->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Correo institucional asignado exitosamente a ' . $matricula;
            } else {
                // Podría ser que el correo ya era el mismo, o la matrícula no existió
                $response['message'] = 'No se realizaron cambios. El correo podría ser el mismo o la matrícula es incorrecta.';
                 // Para diferenciar, podrías hacer un SELECT previo para ver el correo actual.
            }
        } else {
            $response['message'] = 'Error al asignar el correo: ' . $stmt_update->error;
            // Verificar si es por UNIQUE constraint si ya lo tenía otro.
            if ($conn->errno == 1062) { // Código de error para entrada duplicada (UNIQUE constraint)
                 $response['message'] = 'El correo electrónico ya existe para otro alumno.';
            }
            http_response_code(500);
        }
        $stmt_update->close();
    } else {
        $response['message'] = 'Error al preparar la asignación: ' . $conn->error;
        http_response_code(500);
    }
}

$conn->close();
echo json_encode($response);
?>