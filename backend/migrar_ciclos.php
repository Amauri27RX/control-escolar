<?php
// migrar_ciclos.php
require_once 'db_config.php';

echo "Iniciando migración de ciclos escolares...<br>";

// Obtener todos los periodos únicos de la tabla legacy
$sql_periodos = "SELECT DISTINCT cuatrimestre FROM materias_alumno_legacy WHERE cuatrimestre IS NOT NULL AND cuatrimestre != ''";
$result = $conn->query($sql_periodos);
if (!$result) {
    die("Error obteniendo los cuatrimestres: " . $conn->error);
}

$stmt = $conn->prepare("INSERT INTO ciclo_escolar (codigo, fecha_inicio, fecha_fin, activo) VALUES (?, ?, ?, 0) ON DUPLICATE KEY UPDATE codigo=codigo");

while ($row = $result->fetch_assoc()) {
    $cuatrimestre_str = $row['cuatrimestre'];

    // Lógica para convertir "YYYY-Q" a fechas
    // Ejemplo: 2024-1 -> Ene-Abr, 2024-2 -> May-Ago, 2024-3 -> Sep-Dic
    // Puedes ajustar esta lógica si tus periodos son diferentes.
    list($year, $period) = explode('-', $cuatrimestre_str);
    $period = intval($period);

    $start_month = 1;
    if ($period === 2) $start_month = 5;
    if ($period === 3) $start_month = 9;

    $fecha_inicio = date("$year-$start_month-01");
    $fecha_fin = date("Y-m-t", strtotime($fecha_inicio . " +3 months")); // Fin del 4to mes

    $stmt->bind_param("sss", $cuatrimestre_str, $fecha_inicio, $fecha_fin);
    if ($stmt->execute()) {
        echo "Ciclo '{$cuatrimestre_str}' creado/verificado.<br>";
    } else {
        echo "Error creando ciclo '{$cuatrimestre_str}': " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
echo "Migración de ciclos finalizada.";
?>