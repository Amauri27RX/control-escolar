// script_carrera.js
document.addEventListener('DOMContentLoaded', function() {
    // --- Referencias a Elementos del DOM ---
    const kpiTotalAlumnos = document.getElementById('kpiTotalAlumnos');
    const kpiIngresos = document.getElementById('kpiIngresos');
    const kpiTotalVencido = document.getElementById('kpiTotalVencido');
    const kpiProximosVencer = document.getElementById('kpiProximosVencer');
    
    const filtroInstitucionSelect = document.getElementById('filtroInstitucion');
    const filtroNivelSelect = document.getElementById('filtroNivel');
    const filtroPeriodoSelect = document.getElementById('filtroPeriodo');
    const filtroFechaInicioInput = document.getElementById('filtroFechaInicio');
    const filtroFechaFinInput = document.getElementById('filtroFechaFin');
    const formFiltros = document.getElementById('formFiltros');
    
    const periodoMostradoSpan = document.getElementById('periodoMostrado');
    const tablaReporteBody = document.getElementById('tablaReporteBody');

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

    // --- Cargar Opciones para Filtros ---
    function cargarFiltros() {
        // Cargar Instituciones
        fetch('/control-escolar/backend/control_escolar/api/obtener_datos_programa.php?action=get_instituciones')
            .then(res => res.json())
            .then(data => {
                if(data.success && filtroInstitucionSelect) {
                    data.instituciones.forEach(inst => {
                        filtroInstitucionSelect.add(new Option(inst.nombre, inst.id_institucion));
                    });
                }
            }).catch(err => console.error("Error cargando instituciones:", err));
        
        // Cargar Niveles (necesitaría un endpoint o puedes hardcodearlos)
        const niveles = ["Licenciatura", "Maestria", "Doctorado", "Especialidad"];
        if (filtroNivelSelect) {
            niveles.forEach(nivel => {
                filtroNivelSelect.add(new Option(nivel, nivel));
            });
        }
    }

    // --- Función Principal para Cargar y Mostrar el Reporte ---
    async function cargarReporte() {
        if (!tablaReporteBody) return;
        tablaReporteBody.innerHTML = `<tr><td colspan="7" class="text-center">Cargando reporte...</td></tr>`;

        const formData = new FormData(formFiltros);
        const params = new URLSearchParams(formData);

        try {
            const response = await fetch(`/control-escolar/backend/finanzas/api/obtener_reporte_carreras.php?${params.toString()}`);
            if (!response.ok) throw new Error(`Error del servidor: ${response.status}`);
            const result = await response.json();

            if (result.success && result.data) {
                poblarKPIs(result.data.kpis);
                poblarTabla(result.data.tabla_carreras);
                const textoPeriodoSeleccionado = filtroPeriodoSelect.options[filtroPeriodoSelect.selectedIndex].text;
                if(periodoMostradoSpan) periodoMostradoSpan.textContent = `Periodo: ${textoPeriodoSeleccionado}`;
            } else {
                throw new Error(result.message || "No se pudo cargar el reporte.");
            }
        } catch (error) {
            console.error("Error al cargar reporte:", error);
            tablaReporteBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${escapeHTML(error.message)}</td></tr>`;
        }
    }

    function poblarKPIs(kpis) {
        if (!kpis) return;
        if(kpiTotalAlumnos) kpiTotalAlumnos.textContent = kpis.total_alumnos_activos || 0;
        if(kpiIngresos) kpiIngresos.textContent = formatCurrency(kpis.ingresos_periodo);
        if(kpiTotalVencido) kpiTotalVencido.textContent = formatCurrency(kpis.total_vencido);
        if(kpiProximosVencer) kpiProximosVencer.textContent = kpis.proximos_a_vencer || 0;
    }

    function poblarTabla(carreras) {
        tablaReporteBody.innerHTML = ''; // Limpiar
        if (!carreras || carreras.length === 0) {
            tablaReporteBody.innerHTML = `<tr><td colspan="7" class="text-center">No hay datos que coincidan con los filtros seleccionados.</td></tr>`;
            return;
        }

        carreras.forEach(carrera => {
            const inscritos = parseInt(carrera.inscritos);
            const alCorriente = parseInt(carrera.al_corriente);
            const porcentaje = inscritos > 0 ? ((alCorriente / inscritos) * 100).toFixed(1) : 0;
            const progressBarClass = porcentaje >= 80 ? 'bg-success' : (porcentaje >= 60 ? 'bg-warning' : 'bg-danger');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${escapeHTML(carrera.nombre_programa)}</strong></td>
                <td class="text-center fs-5 fw-bold">${inscritos}</td>
                <td class="text-center text-success fw-bold">${alCorriente}</td>
                <td class="text-center"><span class="badge bg-warning text-dark rounded-pill">${escapeHTML(carrera.proximos_a_vencer)}</span></td>
                <td class="text-center"><span class="badge bg-danger rounded-pill">${escapeHTML(carrera.vencidos)}</span></td>
                <td>
                    <div class="progress" role="progressbar" aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar ${progressBarClass}" style="width: ${porcentaje}%">${porcentaje}%</div>
                    </div>
                </td>
                <td class="text-center">
                    <a href="alumnoscarrera.php?dgp=${escapeHTML(carrera.dgp)}" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                    <i class="fas fa-eye"></i>
                    </a>
                </td>
            `;
            tablaReporteBody.appendChild(tr);
        });
    }

    // --- Lógica de Eventos ---
    if (filtroPeriodoSelect) {
        filtroPeriodoSelect.addEventListener('change', function() {
            const personalizado = this.value === 'personalizado';
            if (filtroFechaInicioInput) filtroFechaInicioInput.disabled = !personalizado;
            if (filtroFechaFinInput) filtroFechaFinInput.disabled = !personalizado;
        });
    }
    
    if (formFiltros) {
        formFiltros.addEventListener('submit', function(e) {
            e.preventDefault(); // Evitar que el formulario se envíe de la forma tradicional
            cargarReporte();
        });
    }

    // --- Carga Inicial ---
    cargarFiltros();
    cargarReporte();
});