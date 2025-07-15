document.addEventListener('DOMContentLoaded', function() {
        // --- Helper para escapar HTML ---
        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return String(str).replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[match]));
        }

        // --- Función principal para cargar datos del dashboard ---
        async function cargarDashboard() {
            try {
                // Asegúrate que esta ruta sea correcta
                const response = await fetch('/control-escolar/backend/finanzas/api/obtener_dashboard_financiero.php');
                if (!response.ok) {
                    throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
                }
                const result = await response.json();
                
                if (result.success && result.data) {
                    poblarKPIs(result.data.kpi);
                    poblarResumenFinanciero(result.data.resumen_financiero);
                    poblarAlertas(result.data.alertas_criticas);
                } else {
                    throw new Error(result.message || "No se pudieron cargar los datos del dashboard.");
                }

            } catch (error) {
                console.error("Error al cargar el dashboard:", error);
                // Mostrar un error general en la página si falla todo
                document.querySelector('.main-content').innerHTML = `<div class="alert alert-danger">Error al cargar el dashboard: ${error.message}</div>`;
            }
        }

        function poblarKPIs(kpiData) {
            if (!kpiData) return;
            // Usaremos querySelector para encontrar los elementos por una estructura más robusta
            document.querySelector('.kpi-card.income .stat-value').textContent = `$${kpiData.ingresos_mes_actual || '0.00'}`;
            document.querySelector('.kpi-card.overdue .stat-value').textContent = `$${kpiData.cartera_vencida || '0.00'}`;
            document.querySelector('.kpi-card.upcoming .stat-value').textContent = kpiData.proximos_a_vencer || '0';
            document.querySelector('.kpi-card.current .stat-value').textContent = `${kpiData.porcentaje_cartera_corriente || 100}%`;
            // Los textos de "tendencia" se pueden dejar estáticos o calcularse también en el backend si se desea
        }

        function poblarResumenFinanciero(resumenData) {
            const tbody = document.querySelector('.table-custom tbody');
            if (!tbody || !resumenData) return;

            tbody.innerHTML = ''; // Limpiar contenido estático

            if (Object.keys(resumenData).length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">No hay datos financieros para mostrar.</td></tr>`;
                return;
            }

            for (const nombreInstitucion in resumenData) {
                const institucionRow = document.createElement('tr');
                institucionRow.className = 'table-institution-row';
                institucionRow.innerHTML = `<td colspan="5"><i class="fas fa-university me-2"></i>${escapeHTML(nombreInstitucion)}</td>`;
                tbody.appendChild(institucionRow);

                const programas = resumenData[nombreInstitucion];
                programas.forEach(programa => {
                    const programaRow = document.createElement('tr');
                    programaRow.className = 'table-program-row';

                    const totalAlumnos = parseInt(programa.total_alumnos);
                    const alCorriente = parseInt(programa.al_corriente);
                    const proximos = parseInt(programa.proximos_a_vencer);
                    const vencidos = parseInt(programa.vencidos);
                    
                    const porcCorriente = totalAlumnos > 0 ? ((alCorriente / totalAlumnos) * 100).toFixed(0) : 0;
                    const porcProximos = totalAlumnos > 0 ? ((proximos / totalAlumnos) * 100).toFixed(0) : 0;
                    const porcVencidos = totalAlumnos > 0 ? ((vencidos / totalAlumnos) * 100).toFixed(0) : 0;

                    programaRow.innerHTML = `
                        <td>${escapeHTML(programa.nombre_programa)}</td>
                        <td class="text-center">${totalAlumnos}</td>
                        <td class="text-center"><span class="badge-status badge-success">${alCorriente} (${porcCorriente}%)</span></td>
                        <td class="text-center"><span class="badge-status badge-warning">${proximos} (${porcProximos}%)</span></td>
                        <td class="text-center"><span class="badge-status badge-danger">${vencidos} (${porcVencidos}%)</span></td>
                    `;
                    tbody.appendChild(programaRow);
                });
            }
        }
        
        function poblarAlertas(alertasData) {
            const listaAlertas = document.querySelector('.side-card .list-group');
            if (!listaAlertas || !alertasData) return;

            listaAlertas.innerHTML = ''; // Limpiar contenido estático

            if (alertasData.length === 0) {
                listaAlertas.innerHTML = '<div class="list-group-item">No hay alertas críticas en este momento. ¡Buen trabajo!</div>';
                return;
            }
            
            alertasData.forEach(alerta => {
                const nombreCompleto = `${alerta.nombre || ''} ${alerta.apellido_paterno || ''} ${alerta.apellido_materno || ''}`.trim();
                const diasVencido = parseInt(alerta.dias_vencido);
                let badgeClass = 'bg-warning text-dark';
                if(diasVencido > 15) badgeClass = 'bg-danger text-white';

                const a = document.createElement('a');
                a.href = '#'; // Aquí podrías poner un enlace al expediente del alumno
                a.className = 'list-group-item list-group-item-action';
                a.innerHTML = `
                    <div class="alert-item">
                        <div class="alert-content">
                            <div class="alert-title">${escapeHTML(nombreCompleto)}</div>
                            <div class="alert-subtitle">${escapeHTML(alerta.nombre_programa)}</div>
                        </div>
                        <span class="alert-badge ${badgeClass}">${diasVencido} día(s) vencido</span>
                    </div>`;
                listaAlertas.appendChild(a);
            });
        }


        // Animación de tarjetas y carga inicial
        const cards = document.querySelectorAll('.kpi-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 200 * index);
        });
        
        // Cargar todos los datos al iniciar
        cargarDashboard();
    });
