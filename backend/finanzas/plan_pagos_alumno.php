<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Pagos del Alumno</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../frontend/css/finanzas/plan_pagos_alumnos.css">
</head>
<body class="w3-light-grey">
    <div class="dashboard-container">
        <div class="header-section">
            <h1 class="w3-xxxlarge"><i class="fas fa-file-invoice-dollar"></i> Plan de Pagos del Alumno</h1>
        </div>
        
       <?php
require_once '../conexion.php';

$matricula = $_GET['matricula'] ?? '';

// Consulta modificada para incluir JOIN con alumno_info_personal
$query = "SELECT 
            a.nombre, 
            a.apellido_paterno, 
            a.apellido_materno, 
            p.nombre_programa, 
            p.nivel_educativo, 
            a.correo_institucional,
            aip.telefono
          FROM alumno a
          JOIN programa p ON a.dgp = p.dgp
          LEFT JOIN alumno_info_personal aip ON a.matricula = aip.matricula
          WHERE a.matricula = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();
$alumno = $result->fetch_assoc();
$stmt->close();

if (!$alumno) {
    die("Alumno no encontrado. Verifica la matrícula proporcionada.");
}
?>
        
        <div class="student-info-card">
            <div class="w3-row-padding"> <div class="w3-col m4 s12 w3-margin-bottom"> <p><strong><i class="fas fa-user w3-text-indigo"></i> Nombre:</strong> <?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido_paterno'] . ' ' . $alumno['apellido_materno']) ?></p><p><strong><i class="fas fa-phone w3-text-indigo"></i> Teléfono:</strong> 
                <?= htmlspecialchars($alumno['telefono'] ?? 'No especificado') ?>
            </p>
                </div>
                <div class="w3-col m4 s12 w3-margin-bottom">
                    <p><strong><i class="fas fa-id-card w3-text-indigo"></i> Matrícula:</strong> <?= htmlspecialchars($matricula) ?></p> <p><strong><i class="fas fa-envelope w3-text-indigo"></i> Correo Institucional:</strong> 
                <?= htmlspecialchars($alumno['correo_institucional'] ?? 'No especificado') ?>
            </p>
                </div>
                <div class="w3-col m4 s12 w3-margin-bottom">
                    <p><strong><i class="fas fa-graduation-cap w3-text-indigo"></i> Programa:</strong> <?= htmlspecialchars($alumno['nombre_programa']) ?> (<?= htmlspecialchars($alumno['nivel_educativo']) ?>)</p>
                </div>
            </div>
        </div>
        
        <div class="w3-row-padding w3-margin-bottom">
            <div class="w3-col l4 m6 s12 w3-margin-bottom"> <div class="summary-card total">
                    <div class="card-title"><i class="fas fa-money-bill-wave"></i> Total a Pagar</div>
                    <div id="total-pagar" class="card-value w3-text-indigo">$0.00</div>
                    <div class="card-subtitle">Costo total del programa</div>
                </div>
            </div>
            <div class="w3-col l4 m6 s12 w3-margin-bottom">
                <div class="summary-card paid">
                    <div class="card-title"><i class="fas fa-check-circle"></i> Pagado</div>
                    <div id="total-pagado" class="card-value w3-text-green">$0.00</div>
                    <div class="card-subtitle">0% del total pagado</div>
                </div>
            </div>
            <div class="w3-col l4 m12 s12 w3-margin-bottom"> <div class="summary-card pending">
                    <div class="card-title"><i class="fas fa-exclamation-circle"></i> Saldo Pendiente</div>
                    <div id="saldo-pendiente" class="card-value w3-text-red">$0.00</div>
                    <div class="card-subtitle">Por pagar</div>
                </div>
            </div>
        </div>
        
        
        <div class="action-buttons w3-margin-bottom w3-right-align">
            <button id="btn-generar-plan" class="action-button btn-primary" onclick="document.getElementById('modalGenerarPlan').style.display='block'">
                <i class="fas fa-plus-circle"></i> Generar Plan de Pagos
            </button>
                <button id="btn-nota" class="action-button btn-warning">
                <i class="fas fa-sticky-note"></i> Nota
            </button>
            <button id="btn-exportar-pdf" class="action-button btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </button>
            <button id="btn-certificado-no-adeudo" class="action-button btn-success">
                <i class="fas fa-file-certificate"></i> Certificado de No Adeudo
            </button>
        </div>
        
        <div id="content-to-pdf" class="payment-table-container">
            <div class="table-header">
                <h3 class="w3-text-indigo"><i class="fas fa-list"></i> Detalle de Pagos</h3>
            </div>
            <table id="tabla-pagos" class="w3-table-all w3-hoverable" style="width:100%">
                <thead>
                    <tr class="w3-indigo">
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                        <th>Fecha Pago</th>
                        <th>Monto Pagado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>

    <div id="modalPago" class="w3-modal">
        <div class="w3-modal-content w3-card-4" style="max-width:600px;border-radius:10px">
            <div class="modal-header">
                <span onclick="document.getElementById('modalPago').style.display='none'" 
                class="w3-button w3-display-topright">&times;</span>
                <h4><i class="fas fa-money-check-alt"></i> Registrar Pago</h4>
            </div>
            <div class="w3-container w3-padding-24">
                <form id="formPago" class="w3-container">
                    <input type="hidden" id="pago-id">
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-tag"></i> Concepto</b></label>
                        <input class="w3-input w3-border" type="text" id="pago-concepto" readonly>
                    </div>
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-dollar-sign"></i> Monto a Pagar</b></label>
                        <input class="w3-input w3-border" type="text" id="pago-monto" readonly>
                    </div>
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-money-bill-wave"></i> Monto Pagado</b> <span class="w3-text-red">*</span></label>
                        <input class="w3-input w3-border" type="number" id="pago-monto-pagado" step="0.01" min="0" required>
                    </div>
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-calendar-day"></i> Fecha de Pago</b> <span class="w3-text-red">*</span></label>
                        <input class="w3-input w3-border" type="date" id="pago-fecha" required>
                    </div>
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-credit-card"></i> Método de Pago</b> <span class="w3-text-red">*</span></label>
                        <select class="w3-select w3-border" id="pago-metodo" required>
                            <option value="">Seleccionar...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta de Crédito/Débito">Tarjeta de Crédito/Débito</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Depósito Bancario">Depósito Bancario</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-receipt"></i> Referencia/Comprobante</b></label>
                        <input class="w3-input w3-border" type="text" id="pago-referencia" placeholder="Número de transacción, folio, etc.">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="action-button w3-grey" onclick="document.getElementById('modalPago').style.display='none'">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="action-button btn-primary" id="btn-guardar-pago">
                    <i class="fas fa-save"></i> Registrar Pago
                </button>
            </div>
        </div>
    </div>

    <div id="nota-visual" class="w3-panel w3-pale-yellow w3-leftbar w3-border-yellow" style="display:none; margin-top:12px;">
    <strong>Nota:</strong>
    <span id="nota-texto-mostrada"></span>
</div>


<div id="modalNota" class="w3-modal">
  <div class="w3-modal-content" style="max-width:400px">
    <header class="modal-header">
      <span onclick="document.getElementById('modalNota').style.display='none'" class="w3-button w3-display-topright">&times;</span>
      <h4><i class="fas fa-sticky-note"></i> Nota</h4>
    </header>
    <div class="w3-container w3-padding-24">
      <textarea id="nota-texto" rows="5" class="w3-input" placeholder="Escribe tu nota..."></textarea>
    </div>
    <footer class="modal-footer">
      <button id="btn-guardar-nota" class="action-button btn-primary">Guardar Nota</button>
    </footer>
  </div>
</div> 

<div id="modalGenerarPlan" class="w3-modal">
        <div class="w3-modal-content w3-card-4" style="max-width:600px;border-radius:10px">
            <div class="modal-header">
                <span onclick="document.getElementById('modalGenerarPlan').style.display='none'" 
                class="w3-button w3-display-topright">&times;</span>
                <h4><i class="fas fa-file-invoice-dollar"></i> Generar Nuevo Plan de Pagos</h4>
            </div>
            <div class="w3-container w3-padding-24">
                <form id="formGenerarPlan" class="w3-container">
                    <input type="hidden" id="matricula" value="<?= htmlspecialchars($matricula) ?>">
                    
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-graduation-cap"></i> Nivel Educativo</b></label>
                        <select class="w3-select w3-border" id="nivel_educativo" required>
                            <option value="">Seleccionar...</option>
                            <option value="Especialidad">Especialidad</option>
                            <option value="Licenciatura">Licenciatura</option>
                            <option value="Maestria">Maestria</option>
                            <option value="Doctorado">Doctorado</option>
                        </select>
                    </div>

                    <div class="w3-section" id="opciones_doctorado" style="display: none;">
                        <label class="w3-text-indigo"><b><i class="fas fa-clock"></i> Opción de duración para Doctorado</b></label>
                        <select class="w3-select w3-border" id="duracion_doctorado">
                            <option value="">Seleccionar duración...</option>
                            <option value="24">24 meses (con Maestría)</option>
                            <option value="36">36 meses (con Licenciatura)</option>
                        </select>
                    </div>
                    
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-calendar-alt"></i> Duración (meses)</b></label>
                        <input class="w3-input w3-border" type="text" id="duracion_meses" readonly>
                    </div>
                    
                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-book"></i> Programa</b></label>
                        <input class="w3-input w3-border" type="text" id="nombre_programa" value="<?= htmlspecialchars($alumno['nombre_programa'] ?? '') ?>" readonly>
                    </div>
                    
                    <div class="w3-section">
                        <input class="w3-input w3-border" type="hidden" id="fecha_inicio" name="fecha_inicio">
                    </div>

                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-calendar-check"></i> Fecha de Inicio </b></label>
                        <input class="w3-input w3-border" type="text" id="fecha_inicio_legible" readonly>
                    </div>

                    <div class="w3-section">
                        <label class="w3-text-indigo"><b><i class="fas fa-tags"></i> Concepto(s) donde se va aplicar el descuento</b></label>

                        <div class="w3-padding-small">
                            <label class="w3-block">
                                <input type="checkbox" id="chkInscripcion" onclick="toggleDiscountFields()">
                                <span class="w3-text-black"> Inscripción</span>
                            </label>
                            <label class="w3-block">
                                <input type="checkbox" id="chkColegiatura" onclick="toggleDiscountFields()">
                                <span class="w3-text-black"> Colegiaturas</span>
                            </label>
                            <label class="w3-block">
                                <input type="checkbox" id="chkReinscripcion" onclick="toggleDiscountFields()">
                                <span class="w3-text-black"> Reinscripción</span>
                            </label>
                        </div>
                    </div>

                    <!-- Secciones ocultas por defecto -->
                    <div id="section_inscripcion" class="w3-section" style="display: none;">
                        <label class="w3-text-indigo"><b><i class="fas fa-percentage"></i> Descuento Inscripción (%)</b></label>
                        <input type="number" id="descuento_inscripcion" name="descuento_inscripcion" class="w3-input w3-border" min="0" max="100" value="0">
                    </div>

                    <div id="section_colegiatura" class="w3-section" style="display: none;">
                        <label class="w3-text-indigo"><b><i class="fas fa-percentage"></i> Descuento Colegiatura (%)</b></label>
                        <input type="number" id="descuento_colegiatura" name="descuento_colegiatura" class="w3-input w3-border" min="0" max="100" value="0">
                    </div>

                    <div id="section_reinscripcion" class="w3-section" style="display: none;">
                        <label class="w3-text-indigo"><b><i class="fas fa-percentage"></i> Descuento Reinscripción (%)</b></label>
                        <input type="number" id="descuento_reinscripcion" name="descuento_reinscripcion" class="w3-input w3-border" min="0" max="100" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="action-button w3-grey" onclick="document.getElementById('modalGenerarPlan').style.display='none'">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="action-button btn-primary" id="btn-confirmar-generar">
                    <i class="fas fa-check"></i> Generar Plan
                </button>
            </div>
        </div>
    </div>


</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="../../frontend/js/finanzas/plan_pagos_alumnos.js" data-matricula="<?= htmlspecialchars($matricula) ?>"></script>
</html>