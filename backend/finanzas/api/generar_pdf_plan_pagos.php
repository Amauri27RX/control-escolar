<?php
// Define la ruta a la carpeta 'font'
define('FPDF_FONTPATH', __DIR__ . '/../../../font/');

// Incluir librerías
require('../../fpdf.php'); 
require_once '../../conexion.php'; 

// Definir objeto para mapeo de meses
$meses = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];

// Función para formatear fecha
function formatFechaPDF($fechaStr, $meses) {
    if (!$fechaStr || $fechaStr === '0000-00-00') {
        return '-';
    }
    $partes = explode('-', $fechaStr);
    if (count($partes) === 3) {
        $año = $partes[0];
        $mesNum = $partes[1];
        $dia = $partes[2];
        return "$dia de {$meses[$mesNum]} de $año";
    }
    return $fechaStr;
}

// Validar matrícula
$matricula = isset($_GET['matricula']) ? $_GET['matricula'] : '';
if (empty($matricula)) {
    die('Error: Matrícula no especificada.');
}

// Obtener información del alumno
$query_alumno = "SELECT a.nombre, a.apellido_paterno, a.apellido_materno, p.nombre_programa, p.nivel_educativo
                 FROM alumno a
                 JOIN programa p ON a.dgp = p.dgp
                 WHERE a.matricula = ?";
$stmt_alumno = $conn->prepare($query_alumno);
$stmt_alumno->bind_param("s", $matricula);
$stmt_alumno->execute();
$result_alumno = $stmt_alumno->get_result();
$alumno = $result_alumno->fetch_assoc();

if (!$alumno) {
    die('Error: Alumno no encontrado.');
}

// Obtener pagos del alumno
$query_pagos = "SELECT concepto, monto_regular, fecha_vencimiento, estado_pago, fecha_pago, monto_pagado
                FROM planpagos
                WHERE matricula = ? ORDER BY fecha_vencimiento ASC";
$stmt_pagos = $conn->prepare($query_pagos);
$stmt_pagos->bind_param("s", $matricula);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();
$pagos = [];
while ($row = $result_pagos->fetch_assoc()) {
    $pagos[] = $row;
}

// Calcular totales
$total_pagar = 0;
$total_pagado = 0;
foreach ($pagos as $pago) {
    $total_pagar += $pago['monto_regular'];
    $total_pagado += $pago['monto_pagado'];
}
$saldo_pendiente = $total_pagar - $total_pagado;

// ---- Generación del PDF mejorado ----
class PDF extends FPDF
{
    // Variables para encabezado
    private $institucion = "INSTITUCIÓN EDUCATIVA";
    private $sistema = "SISTEMA DE CONTROL ESCOLAR";
    
    // Cabecera mejorada
    function Header()
    {
        // Logo institucional (asumiendo que existe)
        if (file_exists('logo.png')) {
            $this->Image('logo.png', 10, 8, 30);
        }
        
        // Título principal
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(30, 50, 120); // Azul oscuro
        $this->SetY(10);
        $this->Cell(0, 8, utf8_decode($this->institucion), 0, 1, 'C');
        
        // Subtítulo
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(60, 100, 180); // Azul medio
        $this->Cell(0, 8, utf8_decode($this->sistema), 0, 1, 'C');
        
        // Título del documento
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(30, 50, 120); // Azul oscuro
        $this->SetY(35);
        $this->Cell(0, 10, utf8_decode('PLAN DE PAGOS DEL ESTUDIANTE'), 0, 1, 'C');
        
        // Línea decorativa
        $this->SetDrawColor(30, 50, 120);
        $this->SetLineWidth(0.8);
        $this->Line(10, 45, 200, 45);
        $this->Ln(12);
    }

    // Pie de página mejorado
    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        
        // Línea superior
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(3);
        
        // Información de contacto
        $this->Cell(0, 6, utf8_decode('Instituto Educativo - Av. Principal #123, Ciudad'), 0, 1, 'C');
        $this->Cell(0, 6, utf8_decode('Teléfono: (123) 456-7890 | Email: contacto@instituto.edu'), 0, 1, 'C');
        
        // Número de página
        $this->SetY(-10);
        $this->Cell(0, 6, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Sección de información del alumno con estilo
    function InformacionAlumno($alumno, $matricula)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 50);
        $this->SetFillColor(230, 240, 255); // Fondo azul claro
        $this->Cell(0, 8, utf8_decode('INFORMACIÓN DEL ESTUDIANTE'), 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->SetFillColor(245, 249, 255); // Fondo más claro
        
        // Nombre
        $this->Cell(40, 8, utf8_decode('Nombre:'), 0, 0, 'L');
        $this->SetFont('', 'B');
        $this->Cell(0, 8, utf8_decode($alumno['nombre'] . ' ' . $alumno['apellido_paterno'] . ' ' . $alumno['apellido_materno']), 0, 1, 'L');
        
        // Matrícula
        $this->SetFont('', '');
        $this->Cell(40, 8, utf8_decode('Matrícula:'), 0, 0, 'L');
        $this->SetFont('', 'B');
        $this->Cell(0, 8, utf8_decode($matricula), 0, 1, 'L');
        
        // Programa
        $this->SetFont('', '');
        $this->Cell(40, 8, utf8_decode('Programa:'), 0, 0, 'L');
        $this->SetFont('', 'B');
        $this->Cell(0, 8, utf8_decode($alumno['nombre_programa']), 0, 1, 'L');
        
        // Nivel educativo
        $this->SetFont('', '');
        $this->Cell(40, 8, utf8_decode('Nivel:'), 0, 0, 'L');
        $this->SetFont('', 'B');
        $this->Cell(0, 8, utf8_decode($alumno['nivel_educativo']), 0, 1, 'L');
        
        $this->Ln(8);
    }
    
    // Sección de resumen de pagos con estilo
    function ResumenPagos($total_pagar, $total_pagado, $saldo_pendiente)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 50);
        $this->SetFillColor(230, 240, 255); // Fondo azul claro
        $this->Cell(0, 8, utf8_decode('RESUMEN FINANCIERO'), 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        
        // Total a pagar
        $this->Cell(60, 8, utf8_decode('Total a pagar:'), 0, 0, 'R');
        $this->SetFont('', 'B');
        $this->SetTextColor(30, 50, 120); // Azul oscuro
        $this->Cell(40, 8, '$' . number_format($total_pagar, 2), 0, 1, 'L');
        
        // Total pagado
        $this->SetFont('', '');
        $this->SetTextColor(0, 0, 0);
        $this->Cell(60, 8, utf8_decode('Total pagado:'), 0, 0, 'R');
        $this->SetFont('', 'B');
        $this->SetTextColor(0, 100, 0); // Verde
        $this->Cell(40, 8, '$' . number_format($total_pagado, 2), 0, 1, 'L');
        
        // Saldo pendiente
        $this->SetFont('', '');
        $this->SetTextColor(0, 0, 0);
        $this->Cell(60, 8, utf8_decode('Saldo pendiente:'), 0, 0, 'R');
        $this->SetFont('', 'B');
        $this->SetTextColor(180, 0, 0); // Rojo
        $this->Cell(40, 8, '$' . number_format($saldo_pendiente, 2), 0, 1, 'L');
        
        $this->Ln(8);
    }
    
    // Tabla de pagos mejorada con colores según estado
    function TablaPagos($header, $data, $meses)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50, 50, 50);
        $this->SetFillColor(230, 240, 255); // Fondo azul claro
        $this->Cell(0, 8, utf8_decode('DETALLE DE PAGOS'), 1, 1, 'C', true);
        $this->Ln(4);
        
        // Cabecera de tabla
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(240, 245, 255); // Fondo más claro para encabezados
        $this->SetTextColor(30, 50, 120); // Azul oscuro
        $this->SetDrawColor(180, 200, 255); // Borde azul claro
        
        // Anchos de columnas
        $w = array(60, 25, 30, 25, 30, 25);
        
        // Cabeceras
        for($i=0; $i<count($header); $i++) {
            $this->Cell($w[$i], 8, utf8_decode($header[$i]), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Restaurar configuración para datos
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(200, 200, 200); // Gris claro para bordes
        
        $fill = false;
        foreach($data as $row) {
            // Determinar color según estado
            $estado = utf8_decode($row['estado_pago']);
            $fecha_vencimiento = new DateTime($row['fecha_vencimiento']);
            $hoy = new DateTime();
            
            if ($estado === 'Pagado') {
                $this->SetFillColor(220, 255, 220); // Verde claro
            } elseif ($fecha_vencimiento < $hoy && $estado !== 'Pagado') {
                $this->SetFillColor(255, 220, 220); // Rojo claro
                $estado = 'Vencido';
            } else {
                $this->SetFillColor(255, 255, 200); // Amarillo claro
            }
            
            // Fila de datos
            $this->Cell($w[0], 7, utf8_decode($row['concepto']), 'LR', 0, 'L', true);
            $this->Cell($w[1], 7, '$' . number_format($row['monto_regular'], 2), 'LR', 0, 'R', true);
            $this->Cell($w[2], 7, formatFechaPDF($row['fecha_vencimiento'], $meses), 'LR', 0, 'C', true);
            $this->Cell($w[3], 7, $estado, 'LR', 0, 'C', true);
            $this->Cell($w[4], 7, ($row['fecha_pago'] ? formatFechaPDF($row['fecha_pago'], $meses) : '-'), 'LR', 0, 'C', true);
            $this->Cell($w[5], 7, ($row['monto_pagado'] ? '$' . number_format($row['monto_pagado'], 2) : '-'), 'LR', 0, 'R', true);
            $this->Ln();
            
            $fill = !$fill;
        }
        
        // Cierre de la tabla
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(10);
    }
    
    // Sección de notas importantes
    function NotasImportantes()
    {
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(100, 100, 100);
        $this->MultiCell(0, 5, utf8_decode("Notas importantes:\n" .
            "• Los pagos vencidos generan intereses del 5% mensual.\n" .
            "• Para evitar cargos adicionales, realice sus pagos antes de la fecha de vencimiento.\n" .
            "• Presente este documento como referencia al realizar pagos en caja.\n" .
            "• Para cualquier aclaración, contacte al departamento de finanzas."));
    }
}

// Creación del PDF mejorado
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25); // Margen inferior de 25mm

// Información del alumno
$pdf->InformacionAlumno($alumno, $matricula);

// Resumen de pagos
$pdf->ResumenPagos($total_pagar, $total_pagado, $saldo_pendiente);

// Tabla de pagos
$header = array('Concepto', 'Monto', 'Vencimiento', 'Estado', 'Fecha Pago', 'Pagado');
$pdf->TablaPagos($header, $pagos, $meses);

// Notas importantes
$pdf->NotasImportantes();

// Salida del PDF
$nombre_archivo = 'Plan_Pagos_' . $alumno['apellido_paterno'] . '_' . $alumno['nombre'] . '.pdf';
$pdf->Output('I', $nombre_archivo);

// Cerrar conexión
$conn->close();
?>