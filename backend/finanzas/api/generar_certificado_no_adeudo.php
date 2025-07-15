<?php
// Define la ruta a la carpeta 'font' (asegúrate que la ruta sea correcta)
define('FPDF_FONTPATH', __DIR__ . '/../../../font/');

// Incluir librerías
require('../../fpdf.php');
require_once '../../conexion.php'; // Asegúrate de que este archivo maneje la conexión a la base de datos de forma segura.

// --- Configuración de codificación para FPDF ---
// Es crucial para que los caracteres especiales se muestren correctamente.
class PDF extends FPDF
{
    // Puedes extender FPDF si necesitas funciones personalizadas, pero para la codificación
    // basta con asegurar que la fuente y la codificación de salida sean correctas.
}

// --- 1. Validación de la matrícula ---
// Obtener la matrícula de forma segura
$matricula = $_GET['matricula'] ?? '';

if (empty($matricula)) {
    die('Error: La matrícula no fue proporcionada.');
}

// --- 2. Primero verificar que no tenga adeudos ---
$query_adeudos = "SELECT COUNT(*) as pendientes FROM planpagos
                  WHERE matricula = ? AND estado_pago = 'Pendiente'";
$stmt = $conn->prepare($query_adeudos);

if (!$stmt) {
    die('Error al preparar la consulta de adeudos: ' . $conn->error);
}

$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['pendientes'] > 0) {
    die('El alumno con matrícula ' . htmlspecialchars($matricula) . ' tiene pagos pendientes. No se puede generar el certificado.');
}

// --- 3. Obtener información del alumno ---
$query_alumno = "SELECT a.nombre, a.apellido_paterno, a.apellido_materno,
                         p.nombre_programa, p.nivel_educativo
                  FROM alumno a
                  JOIN programa p ON a.dgp = p.dgp
                  WHERE a.matricula = ?";
$stmt = $conn->prepare($query_alumno);

if (!$stmt) {
    die('Error al preparar la consulta del alumno: ' . $conn->error);
}

$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();
$alumno = $result->fetch_assoc();
$stmt->close();
$conn->close(); // Cerrar la conexión a la base de datos una vez que ya no se necesita

if (!$alumno) {
    die('Error: No se encontró información para la matrícula ' . htmlspecialchars($matricula) . '.');
}

// --- 4. Crear PDF ---
// Inicializar FPDF (asegúrate de que sea la clase base FPDF si no usas la extensión PDF de arriba)
$pdf = new FPDF('P', 'mm', 'Letter'); // Portrait, milímetros, tamaño carta

// Añadir una fuente que soporte UTF-8 si es necesario (ej: DejaVuSans, o descargar fuentes a la carpeta font de FPDF)
// Si las fuentes Arial, Times, Courier no son suficientes, FPDF permite añadir fuentes TrueType
// Puedes descargar fuentes TrueType y convertirlas a formato FPDF con la utilidad makefont.php
// Ejemplo: $pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
// $pdf->SetFont('DejaVuSans', '', 12);

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Encabezado
// Usar utf8_decode() para textos estáticos si no se usa AddFont con una fuente UTF-8 nativa
// Si la fuente 'Arial' ya es compatible o tu archivo PHP está en UTF-8 y se usa mb_string, puede que no sea necesario
$pdf->Cell(0, 10, utf8_decode('CERTIFICADO DE NO ADEUDO'), 0, 1, 'C');
$pdf->Ln(15);

// Cuerpo del documento
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, utf8_decode('La institución educativa certifica que el(la) alumno(a):'), 0, 'J');
$pdf->Ln(10);

// Nombre del alumno
$pdf->SetFont('Arial', 'B', 14);
$nombre_completo = utf8_decode($alumno['nombre'] . ' ' . $alumno['apellido_paterno'] . ' ' . $alumno['apellido_materno']);
$pdf->Cell(0, 10, $nombre_completo, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode('Con matrícula: ' . $matricula), 0, 1);
$pdf->Cell(0, 8, utf8_decode('Del programa: ' . $alumno['nombre_programa'] . ' (' . $alumno['nivel_educativo'] . ')'), 0, 1);
$pdf->Ln(15);

// Asegurarse de que el locale esté configurado para mostrar el mes en español
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp'); // Para sistemas Linux/macOS
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { // Para sistemas Windows
    setlocale(LC_TIME, 'spanish');
}
// Formatear la fecha
$fecha_hoy = date('d') . ' de ' . strftime('%B') . ' de ' . date('Y');

$pdf->MultiCell(0, 8, utf8_decode('NO TIENE ADEUDOS PENDIENTES con esta institución al día de hoy, ' . $fecha_hoy . '.'), 0, 'J');
$pdf->Ln(20);

// Firma 
$pdf->Cell(0, 8, '__________________________', 0, 1, 'R');
$pdf->Cell(0, 8, utf8_decode('Firma y sello'), 0, 1, 'R');
$pdf->Cell(0, 8, utf8_decode('Departamento de Finanzas'), 0, 1, 'R');

// Generar PDF
$pdf->Output('I', 'certificado_no_adeudo_' . $matricula . '.pdf');
?>