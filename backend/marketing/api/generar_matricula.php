<?php
header('Content-Type: application/json');
include("../../conexion.php");

try {
    if (!$conn) {
        throw new Exception("No se pudo establecer la conexión");
    }

    if (!isset($_GET['prefijo'])) {
        throw new Exception("No se recibió prefijo");
    }

    $prefijo = strtoupper(trim($_GET['prefijo']));

    if (!preg_match('/^[A-Z]{2}$/', $prefijo)) {
        throw new Exception("Prefijo inválido. Debe contener exactamente 2 letras mayúsculas.");
    }

    $letra_ap = $prefijo[0];
    $letra_am = $prefijo[1];

    function generarMatricula($conn, $letra_ap, $letra_am) {
        $prefijo = $letra_ap . $letra_am;
        $intentos = 0;

        do {
            $numero_aleatorio = rand(1000, 9999);
            $matricula = $prefijo . $numero_aleatorio;

            $sql = "SELECT COUNT(*) FROM alumno WHERE matricula = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la verificación: " . $conn->error);
            }

            $stmt->bind_param("s", $matricula);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la verificación: " . $stmt->error);
            }

            $existe = 0; // Inicializa la variable
            $stmt->bind_result($existe);
            $stmt->fetch();
            $stmt->close();

            $intentos++;
            if ($intentos > 10) {
                throw new Exception("No se pudo generar una matrícula única después de varios intentos.");
            }

        } while ($existe > 0);

        return $matricula;
    }

    $matricula = generarMatricula($conn, $letra_ap, $letra_am);
    echo json_encode(['matricula' => $matricula]);

    $conn->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
