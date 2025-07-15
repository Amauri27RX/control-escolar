document.addEventListener('DOMContentLoaded', function() {
    const tablaAlumnosBody = document.getElementById('tablaAlumnosBody');
    const mensajeListaDiv = document.getElementById('mensajeLista');
    
    const filtroBusquedaTexto = document.getElementById('filtroBusquedaTexto');
    const filtroInstitucionSelect = document.getElementById('filtroInstitucion');
    const filtroProgramaSelect = document.getElementById('filtroPrograma');
    const filtroGeneracionSelect = document.getElementById('filtroGeneracion');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');

    function mostrarMensaje(mensaje, tipo = 'info') { // Default a 'info' para mensajes no críticos
        if (mensajeListaDiv) {
            mensajeListaDiv.innerHTML = `<div class="alert alert-${tipo}" role="alert">${escapeHTML(mensaje)}</div>`;
            if (tipo !== 'danger') { // Los errores de peligro pueden quedarse más tiempo
                 setTimeout(() => { if(mensajeListaDiv) mensajeListaDiv.innerHTML = ''; }, 5000);
            }
        }
    }

    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[match]));
    }

    function cargarOpcionesFiltroInstituciones() {
        if (!filtroInstitucionSelect) return;
        fetch('/control-escolar/backend/control_escolar/api/obtener_datos_programa.php?action=get_instituciones')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.instituciones) {
                    data.instituciones.forEach(institucion => {
                        const option = document.createElement('option');
                        option.value = institucion.id_institucion;
                        option.textContent = institucion.nombre;
                        filtroInstitucionSelect.appendChild(option);
                    });
                } else {
                    console.error('Error al cargar instituciones para filtro:', data.message);
                }
            }).catch(error => console.error('Fetch error para filtro de instituciones:', error));
    }

    function cargarOpcionesFiltroProgramas() {
        if (!filtroProgramaSelect) return;
        fetch('/control-escolar/backend/control_escolar/api/obtener_datos_programa.php?action=get_todos_los_programas')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.programas) {
                    data.programas.forEach(programa => {
                        const option = document.createElement('option');
                        option.value = programa.dgp; // Usar DGP como valor
                        option.textContent = programa.nombre_programa;
                        filtroProgramaSelect.appendChild(option);
                    });
                } else {
                    console.error('Error al cargar programas para filtro:', data.message);
                }
            }).catch(error => console.error('Fetch error para filtro de programas:', error));
    }
    
    function cargarOpcionesFiltroGeneraciones() {
        if (!filtroGeneracionSelect) return;
        fetch('/control-escolar/backend/control_escolar/api/obtener_datos_programa.php?action=get_todas_las_generaciones')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.generaciones) {
                    data.generaciones.forEach(gen => {
                        const option = document.createElement('option');
                        option.value = gen.ciclo_inicio; // El PHP devuelve objetos {ciclo_inicio: 'valor'}
                        option.textContent = gen.ciclo_inicio;
                        filtroGeneracionSelect.appendChild(option);
                    });
                } else {
                    console.error('Error al cargar generaciones para filtro:', data.message);
                }
            }).catch(error => console.error('Fetch error para filtro de generaciones:', error));
    }

    function cargarListaAlumnos() {
        if (!tablaAlumnosBody) return;

        const institucion = filtroInstitucionSelect ? filtroInstitucionSelect.value : '';
        const programaDGP = filtroProgramaSelect ? filtroProgramaSelect.value : '';
        const generacion = filtroGeneracionSelect ? filtroGeneracionSelect.value : '';
        const busqueda = filtroBusquedaTexto ? filtroBusquedaTexto.value.trim() : '';

        let queryParams = new URLSearchParams();
        if (institucion) queryParams.append('institucion', institucion);
        if (programaDGP) queryParams.append('programa', programaDGP); // En PHP se espera 'programa' para el DGP
        if (generacion) queryParams.append('generacion', generacion);
        if (busqueda) queryParams.append('busqueda', busqueda);

        const fetchUrl = `/control-escolar/backend/control_escolar/api/obtener_lista_alumnos.php?${queryParams.toString()}`;
        console.log(`Fetching alumnos desde: ${fetchUrl}`);
        tablaAlumnosBody.innerHTML = `<tr><td colspan="11" class="text-center">Cargando alumnos...</td></tr>`; // 11 columnas incluyendo Acciones
        if (mensajeListaDiv) mensajeListaDiv.innerHTML = ''; 


        fetch(fetchUrl)
            .then(response => {
                if (!response.ok) throw new Error(`Error HTTP ${response.status} - ${response.statusText}`);
                return response.json();
            })
            .then(result => {
                tablaAlumnosBody.innerHTML = ''; 
                if (result.success && result.alumnos) {
                    if (result.alumnos.length > 0) {
                        result.alumnos.forEach(alumno => {
                            const tr = document.createElement('tr');
                            const nombreCompleto = `${escapeHTML(alumno.apellido_paterno)} ${escapeHTML(alumno.apellido_materno)} ${escapeHTML(alumno.nombre)}`.trim();
                            const generacionCompleta = `${escapeHTML(alumno.ciclo_inicio || 'N/D')} - ${escapeHTML(alumno.ciclo_fin || 'N/D')}`;
                            
                            tr.innerHTML = `
                                <td>${escapeHTML(alumno.matricula)}</td>
                                <td>${nombreCompleto}</td>
                                <td>${escapeHTML(alumno.estatus_alumno || 'N/D')}</td>
                                <td>${escapeHTML(alumno.curp || 'N/D')}</td>
                                <td>${escapeHTML(alumno.nombre_programa || 'N/D')}</td>
                                <td>${escapeHTML(alumno.fecha_inicio || 'N/D')}</td>
                                <td>${generacionCompleta}</td>
                                <td>${escapeHTML(alumno.telefono_personal || 'N/D')}</td>
                                <td>${escapeHTML(alumno.correo_institucional || 'N/D')}</td>
                                <td>${escapeHTML(alumno.correo_personal || 'N/D')}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"> 
                                            <li><a class="dropdown-item" href="../../backend/expedientealumno/expediente_alumno.php?matricula=${escapeHTML(alumno.matricula)}"><i class="fas fa-eye me-2"></i>Ver Detalles</a></li>
                                            <li><a class="dropdown-item" href="../../backend/control_escolar/inscripcion.php?matricula=${escapeHTML(alumno.matricula)}"><i class="fas fa-edit me-2"></i>Editar</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="verHistorial('${escapeHTML(alumno.matricula)}')"><i class="fas fa-history me-2"></i>Historial</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="darDeBajaAlumno('${escapeHTML(alumno.matricula)}')"><i class="fas fa-trash-alt me-2"></i>Dar de Baja</a></li>
                                        </ul>
                                    </div>
                                </td>
                            `;
                            tablaAlumnosBody.appendChild(tr);
                        });
                    } else {
                        mostrarMensaje(result.message || 'No hay alumnos para mostrar con los criterios seleccionados.', 'info');
                        const tr = document.createElement('tr');
                        const td = document.createElement('td');
                        td.colSpan = 11; // Ajustado a 10 columnas de datos + 1 de acciones
                        td.textContent = result.message || 'No hay alumnos que coincidan con los filtros.';
                        td.classList.add('text-center');
                        tr.appendChild(td);
                        tablaAlumnosBody.appendChild(tr);
                    }
                } else {
                    throw new Error(result.message || 'No se pudieron cargar los alumnos.');
                }
            })
            .catch(error => {
                console.error('Error al cargar la lista de alumnos:', error);
                tablaAlumnosBody.innerHTML = ''; 
                mostrarMensaje(`Error al cargar datos: ${error.message}`);
                 const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 11; // Ajustado
                td.textContent = 'Error al cargar la lista de alumnos.';
                td.classList.add('text-center', 'text-danger');
                tr.appendChild(td);
                tablaAlumnosBody.appendChild(tr);
            });
    }
    
    // Placeholder para funciones de acciones (deberás implementarlas)
    window.verDetallesAlumno = function(matricula) { alert(`Ver detalles del alumno: ${matricula}. (Funcionalidad no implementada)`); };
    window.verHistorial = function(matricula) { alert(`Ver historial del alumno: ${matricula}. (Funcionalidad no implementada)`); };
    window.darDeBajaAlumno = function(matricula) { 
        if(confirm(`¿Está seguro de que desea dar de baja al alumno con matrícula ${matricula}? Esta acción es simulada.`)){
            // Aquí iría la lógica real para dar de baja, por ejemplo, un fetch a un script PHP
            console.log(`Simulación: Alumno ${matricula} dado de baja.`);
            mostrarMensaje(`Alumno ${matricula} marcado para baja (simulación).`, 'warning');
            // Opcionalmente, recargar la lista: cargarListaAlumnos();
        }
    };

    // Event listener para el botón de aplicar filtros
    if (btnAplicarFiltros) {
        btnAplicarFiltros.addEventListener('click', cargarListaAlumnos);
    }
    
    // Event listener para búsqueda por texto al presionar Enter
    if (filtroBusquedaTexto) {
        filtroBusquedaTexto.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                cargarListaAlumnos();
            }
        });
    }
    
    // Carga inicial de datos y opciones de filtros
    cargarOpcionesFiltroInstituciones();
    cargarOpcionesFiltroProgramas(); 
    cargarOpcionesFiltroGeneraciones(); 
    cargarListaAlumnos(); // Cargar todos los alumnos al inicio
});