document.addEventListener('DOMContentLoaded', () => {
    // --- ESTADO DE LA APLICACIÓN ---
    let allStudents = [];
    let selectedStudent = null;
    let filter = { program: "", status: "", search: "" };

    // --- ELEMENTOS DEL DOM ---
    const studentListContainer = document.getElementById('student-list-container');
    const studentDetailContainer = document.getElementById('student-detail-container');
    const studentListBody = document.getElementById('student-list-body');
    // Filtros
    const searchInput = document.getElementById('search');
    const filtroInstitucion = document.getElementById('filtroInstitucion');
    const filtroNivel = document.getElementById('filtroNivel');
    const filtroPrograma = document.getElementById('filtroPrograma');
    const filtroStatus = document.getElementById('filtroStatus');

    // --- FUNCIONES DE API (FETCH) ---
    async function api(action, params = {}) {
        const url = new URL(`/control-escolar/backend/control_escolar/api/api_calificaciones.php`, window.location.origin);
        url.searchParams.append('action', action);
        for (const key in params) {
            if (params[key] !== undefined && params[key] !== null) {
                url.searchParams.append(key, params[key]);
            }
        }
        
        const response = await fetch(url);
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || `Error del servidor: ${response.status}`);
        }
        return result;
    }
    
    async function apiPost(action, body) {
        const url = new URL(`/control-escolar/backend/control_escolar/api/api_calificaciones.php?action=${action}`, window.location.origin);
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || `Error del servidor: ${response.status}`);
        }
        return result;
    }

    // --- FUNCIONES DE RENDERIZADO ---
    function render() {
        renderStudentList();
        renderStudentDetail();
    }

    function renderStudentList() {
        if (!studentListBody) return;
        
        const filteredStudents = allStudents.filter(student => {
            const fullName = `${student.nombre || ''} ${student.apellido_paterno || ''} ${student.apellido_materno || ''}`.toLowerCase();
            const searchLower = filter.search.toLowerCase();
            
            const matchesSearch = !filter.search || fullName.includes(searchLower) || student.matricula.toLowerCase().includes(searchLower);
            const matchesStatus = !filter.status || student.estatus_alumno === filter.status;
            // El filtrado por institución, nivel y programa ya se hace en el backend
            
            return matchesSearch && matchesStatus;
        });

        studentListBody.innerHTML = '';
        if (filteredStudents.length > 0) {
            filteredStudents.forEach(student => {
                const tr = document.createElement('tr');
                tr.dataset.matricula = student.matricula;
                tr.className = 'hover:bg-gray-50 cursor-pointer transition-colors';
                tr.innerHTML = `<td class="px-4 py-3 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">${student.apellido_paterno} ${student.apellido_materno}, ${student.nombre}</div></td>`;
                studentListBody.appendChild(tr);
            });
        } else {
            studentListBody.innerHTML = `<tr><td class="px-4 py-3 text-center text-gray-500">No hay estudiantes que coincidan.</td></tr>`;
        }
    }

function renderStudentDetail() {
    if (!selectedStudent) {
        studentDetailContainer.innerHTML = `<div class="bg-white shadow rounded-lg p-6"><p class="text-gray-500 italic">Seleccione un estudiante para ver detalles y calificaciones.</p></div>`;
        return;
    }

    const totalSubjects = selectedStudent.total_materias_plan || 0;
    const completedSubjects = selectedStudent.grades.filter(g => g.calificacion !== null).length;
    const completionPercentage = totalSubjects > 0 ? Math.round((completedSubjects / totalSubjects) * 100) : 0;
    const enrollmentDateFormatted = new Date(selectedStudent.general.fecha_inscripcion).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
    const endDateFormatted = selectedStudent.general.fecha_termino ? new Date(selectedStudent.general.fecha_termino).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) : 'En curso';

    // --- INICIO DE LA LÓGICA MEJORADA ---
    let progressAlertHTML = '';

    // Condición para el 100% (Proceso de Titulación)
    if (completionPercentage === 100) {
        progressAlertHTML = `
            <div class="mt-2 p-3 bg-green-100 border-l-4 border-green-500 text-green-700">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-graduation-cap fa-lg me-3"></i></div>
                    <div>
                        <p class="font-bold">¡Programa Completado!</p>
                        <p class="text-sm">Este alumno ha completado el <strong>100%</strong> de su plan de estudios. Es momento de iniciar el <strong>proceso de titulación</strong>.</p>
                    </div>
                </div>
            </div>
        `;
    } 
    // Condición para el 50% (Servicio Constitucional)
    else if (completionPercentage >= 50) {
        progressAlertHTML = `
            <div class="mt-2 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-info-circle fa-lg me-3"></i></div>
                    <div>
                        <p class="font-bold">Recordatorio Importante</p>
                        <p class="text-sm">Este alumno ha completado el ${completionPercentage}% de su programa. Es momento de iniciar el trámite para su <strong>Servicio Constitucional</strong>.</p>
                    </div>
                </div>
            </div>
        `;
    }
    // --- FIN DE LA LÓGICA MEJORADA ---

    studentDetailContainer.innerHTML = `
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold">${selectedStudent.general.nombre} ${selectedStudent.general.apellido_paterno} ${selectedStudent.general.apellido_materno}</h2>
                <p class="text-gray-600">Matrícula: ${selectedStudent.general.matricula}</p>
            </div>
            <div class="p-6">
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Información Académica</h3>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <dt class="text-sm font-medium text-gray-500">Programa:</dt><dd class="text-sm text-gray-900">${selectedStudent.general.nombre_programa}</dd>
                            <dt class="text-sm font-medium text-gray-500">Estatus:</dt><dd class="text-sm text-gray-900">${selectedStudent.general.estatus_alumno}</dd>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Ingreso:</dt><dd class="text-sm text-gray-900">${enrollmentDateFormatted}</dd>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Término:</dt><dd class="text-sm text-gray-900">${endDateFormatted}</dd>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Progreso Académico</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-blue-600 h-2.5 rounded-full" style="width: ${completionPercentage}%"></div></div>
                        <p class="text-sm text-gray-500 mt-2">Asignaturas con calificación: ${completedSubjects} de ${totalSubjects} (${completionPercentage}%)</p>
                        ${progressAlertHTML} </div>
                </div>
                <h3 class="text-lg font-semibold mb-4">Calificaciones</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignatura</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calificación</th></tr></thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${selectedStudent.grades.map(gradeInfo => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${gradeInfo.nombre_materia}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${gradeInfo.nombre_docente || 'N/A'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${gradeInfo.ciclo}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <input type="text" class="w-20 border border-gray-300 rounded-md p-1 text-center" value="${gradeInfo.calificacion ?? 'NP'}" data-id-inscripcion="${gradeInfo.id_inscripcion}">
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Notas y Comentarios</h3>
                    <ul id="notes-list" class="divide-y divide-gray-200 mb-4">${selectedStudent.notes.map(note => `<li class="py-2 text-sm text-gray-600">${note.observacion}<br><small class="text-gray-400"> - ${note.autor}, ${new Date(note.fecha_creacion).toLocaleString('es-MX')}</small></li>`).join('')}</ul>
                    <form id="add-note-form" class="flex"><input type="text" name="note" class="flex-1 p-2 border border-gray-300 rounded-l-md" placeholder="Agregar nueva nota..."><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">Agregar</button></form>
                </div>
            </div>
        </div>`;
}
    // --- MANEJADORES DE EVENTOS ---
    async function loadFilterOptions() {
        try {
            const result = await api('get_filters_data');
            const { instituciones, niveles } = result.data;
            
            instituciones.forEach(inst => filtroInstitucion.add(new Option(inst.nombre, inst.id_institucion)));
            niveles.forEach(nivel => filtroNivel.add(new Option(nivel.nivel_educativo, nivel.nivel_educativo)));
            
        } catch (error) {
            console.error("Error cargando opciones de filtros:", error);
        }
    }

        async function loadProgramOptions() {
        const idInstitucion = filtroInstitucion.value;
        const nivel = filtroNivel.value;
        
        filtroPrograma.innerHTML = '<option value="">Cargando...</option>';
        filtroPrograma.disabled = true;

        try {
            const result = await api('get_programs', { institucion: idInstitucion, nivel: nivel });
            filtroPrograma.innerHTML = '<option value="">Todos los Programas</option>';
            result.programs.forEach(prog => filtroPrograma.add(new Option(prog.nombre_programa, prog.dgp)));
        } catch(error) {
            console.error("Error cargando programas:", error);
            filtroPrograma.innerHTML = '<option value="">Error al cargar</option>';
        } finally {
            filtroPrograma.disabled = false;
        }
    }

    async function applyServerFiltersAndFetchStudents() {
        filter.institucion = filtroInstitucion.value;
        filter.nivel = filtroNivel.value;
        filter.programa = filtroPrograma.value;
        
        studentListBody.innerHTML = `<tr><td class="px-4 py-3 text-center text-gray-500">Cargando estudiantes...</td></tr>`;

        try {
            const result = await api('get_all_students', filter);
            allStudents = result.students;
            renderStudentList(); // Renderizar con los nuevos datos, aplicando filtros de cliente (search, status)
        } catch (error) {
            console.error("Error al aplicar filtros del servidor:", error);
            studentListBody.innerHTML = `<tr><td class="px-4 py-3 text-center text-danger">${error.message}</td></tr>`;
        }
    }




    const handleFilterChange = (e) => {
        filter[e.target.id] = e.target.value;
        renderStudentList();
    };

    const handleRowClick = async (e) => {
        const row = e.target.closest('tr');
        if (row?.dataset.matricula) {
            try {
                const result = await api('get_student_details', { matricula: row.dataset.matricula });
                selectedStudent = result.details;
                render();
            } catch (error) {
                console.error("Error al obtener detalles del estudiante:", error);
                alert(error.message);
            }
        }
    };
    
    const handleGradeChange = async (e) => {
        const input = e.target;
        if (input.tagName === 'INPUT' && input.dataset.idInscripcion && selectedStudent) {
            const idInscripcion = input.dataset.idInscripcion;
            const calificacion = input.value.trim().toUpperCase();
            input.disabled = true;
            try {
                await apiPost('save_grade', {
                    id_inscripcion: idInscripcion,
                    calificacion: calificacion
                });
                // Actualizar el estado local para el cálculo del progreso
                const gradeToUpdate = selectedStudent.grades.find(g => g.id_inscripcion == idInscripcion);
                if(gradeToUpdate) gradeToUpdate.calificacion = (calificacion === 'NP' ? null : parseFloat(calificacion));
                renderStudentDetail();
            } catch (error) {
                console.error("Error al guardar calificación:", error);
                alert(error.message);
            } finally {
                input.disabled = false;
            }
        }
    };
    
    const handleAddNote = async (e) => {
        e.preventDefault();
        const noteInput = e.target.elements.note;
        const nota = noteInput.value.trim();
        if (nota && selectedStudent) {
            try {
                await apiPost('add_note', {
                    matricula: selectedStudent.general.matricula,
                    nota: nota
                });
                noteInput.value = '';
                // Recargar solo las notas para actualizar la vista
                const result = await api('get_student_details', { matricula: selectedStudent.general.matricula });
                selectedStudent.notes = result.details.notes;
                renderStudentDetail();
            } catch(error) {
                console.error("Error al añadir nota:", error);
                alert(error.message);
            }
        }
    };

    // --- ASIGNACIÓN DE EVENTOS (Delegación) ---
    if (studentListContainer) {
        // Filtros del servidor
        filtroInstitucion.addEventListener('change', () => { loadProgramOptions().then(applyServerFiltersAndFetchStudents); });
        filtroNivel.addEventListener('change', () => { loadProgramOptions().then(applyServerFiltersAndFetchStudents); });
        filtroPrograma.addEventListener('change', applyServerFiltersAndFetchStudents);
        
        // Filtros del cliente
        searchInput.addEventListener('input', () => { filter.search = searchInput.value; renderStudentList(); });
        filtroStatus.addEventListener('input', () => { filter.status = filtroStatus.value; renderStudentList(); });
        
        studentListBody.addEventListener('click', handleRowClick);
    }
    
    // --- INICIALIZACIÓN ---
    async function init() {
        await loadFilterOptions();
        await loadProgramOptions();
        await applyServerFiltersAndFetchStudents();
    }

    init();
});