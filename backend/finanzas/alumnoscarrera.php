<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Financiero </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .summary-stat {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-left-width: 5px;
            padding: 1rem;
            border-radius: .375rem;
        }
        .summary-stat .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }
        .summary-stat .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .table-header-custom {
            background-color: #343a40;
            color: white;
            position: sticky;
            top: 0;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4em 0.7em;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid my-4">
        
        <div class="mb-4">
            <a href="carrera.php" class="btn btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-2"></i>Volver al Reporte General</a>
            <h1 class="h2">Detalle Financiero: <span class="text-primary" id="nombreCarreraHeader">Cargando...</span></h1>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resumen de esta Carrera</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h6 class="text-muted">CARTERA VENCIDA</h6>
                        <p class="fs-4 fw-bold text-danger" id="resumenVencido">$0.00</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">POR COBRAR (PRÓXIMOS 10 DÍAS)</h6>
                        <p class="fs-4 fw-bold text-warning" id="resumenPorCobrar">$0.00</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">PROMEDIO DÍAS VENCIMIENTO</h6>
                        <p class="fs-4 fw-bold" id="resumenDiasPromedio">0 días</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="filtroBusqueda" placeholder="Buscar por Nombre o Matrícula...">
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="input-group">
                            <label class="input-group-text" for="filtroStatus"><i class="fas fa-filter"></i></label>
                            <select class="form-select" id="filtroStatus">
                                <option value="" selected>Todos los Estatus</option>
                                <option value="Al Corriente">Al Corriente</option>
                                <option value="Proximo a Vencer">Próximo a Vencer</option>
                                <option value="Vencido">Vencido</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                         <button class="btn btn-outline-success"><i class="fas fa-file-excel me-1"></i> Exportar</button>
                    </div>
                </div>
            </div>
                        <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-header-custom">
                            <tr>
                                <th scope="col">Matrícula</th>
                                <th scope="col">Alumno</th>
                                <th scope="col">Contacto</th> <th scope="col">Concepto de Próximo Pago/Vencido</th>
                                <th scope="col">Fecha Venc.</th>
                                <th scope="col" class="text-end">Monto</th>
                                <th scope="col" class="text-center">Estatus Financiero</th>
                                <th scope="col">Último Pago Realizado</th>
                                <th scope="col" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalleBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
    // --- Referencias a Elementos del DOM ---
    const nombreCarreraHeader = document.getElementById('nombreCarreraHeader');
    const tablaDetalleBody = document.getElementById('tablaDetalleBody');
    
    // KPIs en la parte superior
    const kpiInscritos = document.getElementById('kpiTotalInscritos');
    const kpiCorriente = document.getElementById('kpiAlCorriente');
    const kpiProximos = document.getElementById('kpiProximosVencer');
    const kpiVencidos = document.getElementById('kpiConVencidos');

    // Elementos del nuevo resumen financiero de la carrera
    const resumenVencido = document.getElementById('resumenVencido');
    const resumenPorCobrar = document.getElementById('resumenPorCobrar');
    const resumenDiasPromedio = document.getElementById('resumenDiasPromedio');

    // Filtros
    const filtroBusquedaInput = document.getElementById('filtroBusqueda');
    const filtroStatusSelect = document.getElementById('filtroStatus');

    // --- Funciones de Utilidad ---
    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[match]));
    }
    
    function formatCurrency(value) {
        const num = parseFloat(value);
        if (isNaN(num)) return '$0.00';
        return num.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    }

    // --- Función Principal para Cargar y Mostrar el Reporte ---
    async function cargarDetalleCarrera() {
        if (!tablaDetalleBody) return;

        const urlParams = new URLSearchParams(window.location.search);
        const dgp = urlParams.get('dgp');

        if (!dgp) {
            document.querySelector('.container-fluid').innerHTML = '<div class="alert alert-danger">No se especificó un programa. Por favor, vuelva a la página anterior y seleccione una carrera.</div>';
            return;
        }

        const estatus = filtroStatusSelect.value;
        const busqueda = filtroBusquedaInput.value.trim();
        
        let queryParams = new URLSearchParams({ dgp: dgp });
        if (estatus) queryParams.append('estatus', estatus);
        if (busqueda) queryParams.append('busqueda', busqueda);

        try {
            tablaDetalleBody.innerHTML = `<tr><td colspan="9" class="text-center">Cargando datos...</td></tr>`;
            
            // Asegúrate que esta ruta sea correcta para tu proyecto
            const response = await fetch(`/control-escolar/backend/finanzas/api/obtener_detalle_carrera.php?${queryParams.toString()}`);
            
            if (!response.ok) throw new Error(`Error del servidor: ${response.status}`);
            
            const result = await response.json();

            if (result.success && result.data) {
                const data = result.data;
                
                // Poblar el header y las tarjetas KPI (se actualizan solo en la carga inicial sin filtros)
                if (!estatus && !busqueda) {
                    poblarHeaderYKPIs(data);
                }
                
                // Poblar tabla de alumnos
                poblarTablaAlumnos(data.alumnos);

            } else {
                throw new Error(result.message || 'No se pudo cargar el detalle de la carrera.');
            }
        } catch (error) {
            console.error("Error al cargar detalle de la carrera:", error);
            tablaDetalleBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error: ${escapeHTML(error.message)}</td></tr>`;
        }
    }

    function poblarHeaderYKPIs(data) {
        // Header de la página
        if (nombreCarreraHeader) {
            nombreCarreraHeader.textContent = escapeHTML(data.nombre_programa);
            document.title = `Detalle Financiero - ${escapeHTML(data.nombre_programa)}`;
        }

        // Tarjetas KPI
        if (data.kpis) {
            if(kpiInscritos) kpiInscritos.textContent = data.kpis.total_inscritos || 0;
            if(kpiCorriente) kpiCorriente.textContent = data.kpis.al_corriente || 0;
            if(kpiProximos) kpiProximos.textContent = data.kpis.proximos_a_vencer || 0;
            if(kpiVencidos) kpiVencidos.textContent = data.kpis.vencidos || 0;
        }
        
        // Nuevo resumen financiero específico de la carrera
        if (data.resumen_carrera) {
            if(resumenVencido) resumenVencido.textContent = formatCurrency(data.resumen_carrera.total_vencido);
            if(resumenPorCobrar) resumenPorCobrar.textContent = formatCurrency(data.resumen_carrera.total_por_cobrar);
            if(resumenDiasPromedio) resumenDiasPromedio.textContent = `${Math.round(data.resumen_carrera.promedio_dias_vencido)} días`;
        }
    }

    function poblarTablaAlumnos(alumnos) {
        tablaDetalleBody.innerHTML = '';
        if (!alumnos || alumnos.length === 0) {
            tablaDetalleBody.innerHTML = `<tr><td colspan="9" class="text-center">No hay alumnos que coincidan con los filtros.</td></tr>`;
            return;
        }

        alumnos.forEach(alumno => {
            const tr = document.createElement('tr');
            const nombreCompleto = `${escapeHTML(alumno.apellido_paterno)} ${escapeHTML(alumno.apellido_materno)}, ${escapeHTML(alumno.nombre)}`.trim();
            let estatusBadge = '';

            switch (alumno.estatus_pago_calculado) {
                case 'Vencido':
                    tr.classList.add('table-danger');
                    estatusBadge = `<span class="badge bg-danger-subtle text-danger-emphasis rounded-pill"><i class="fas fa-exclamation-circle me-1"></i>Vencido</span>`;
                    break;
                case 'Proximo a Vencer':
                    estatusBadge = `<span class="badge bg-warning-subtle text-warning-emphasis rounded-pill"><i class="fas fa-clock me-1"></i>Próximo a Vencer</span>`;
                    break;
                default:
                    estatusBadge = `<span class="badge bg-success-subtle text-success-emphasis rounded-pill"><i class="fas fa-check-circle me-1"></i>Al Corriente</span>`;
            }

            const contactoHTML = `
                ${alumno.telefono_personal ? `<div class="mb-1"><i class="fas fa-phone me-2 text-muted"></i>${escapeHTML(alumno.telefono_personal)}</div>` : ''}
                ${alumno.correo_personal ? `<div><i class="fas fa-envelope me-2 text-muted"></i>${escapeHTML(alumno.correo_personal)}</div>` : ''}
            `.trim() || 'N/D';

            const ultimoPagoHTML = alumno.fecha_ultimo_pago 
                ? `${escapeHTML(alumno.fecha_ultimo_pago)} <br><small>(${formatCurrency(alumno.monto_ultimo_pago)})</small>`
                : 'N/A';

            tr.innerHTML = `
                <td>${escapeHTML(alumno.matricula)}</td>
                <td>
                    <strong>${nombreCompleto}</strong>
                    <div class="small text-muted">${escapeHTML(alumno.promocion_aplicada || 'Sin promoción')}</div>
                </td>
                <td>${contactoHTML}</td>
                <td>${escapeHTML(alumno.concepto || 'N/A')}</td>
                <td>${escapeHTML(alumno.fecha_vencimiento || 'N/A')}</td>
                <td class="text-end fw-bold">${alumno.monto_regular ? formatCurrency(alumno.monto_regular) : 'N/A'}</td>
                <td class="text-center">${estatusBadge}</td>
                <td>${ultimoPagoHTML}</td>
                <td class="text-center">
                    <a href="../expedientealumno/expediente_alumno.php?matricula=${escapeHTML(alumno.matricula)}" class="btn btn-sm btn-outline-dark" title="Ver Expediente Completo">
                        <i class="fas fa-address-card"></i>
                    </a>
                </td>
            `;
            tablaDetalleBody.appendChild(tr);
        });
    }

    // --- Lógica de Eventos ---
    if (filtroStatusSelect) {
        filtroStatusSelect.addEventListener('change', cargarDetalleCarrera);
    }
    if (filtroBusquedaInput) {
        let timeoutId;
        filtroBusquedaInput.addEventListener('input', () => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                cargarDetalleCarrera();
            }, 500); // Espera 500ms después de que el usuario deja de escribir
        });
    }

    // --- Carga Inicial ---
    cargarDetalleCarrera();
});
</script>
</body>
</html>