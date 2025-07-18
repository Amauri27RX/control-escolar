document.addEventListener('DOMContentLoaded', () => {
    // --- ESTADO DE LA APLICACIÓN ---
    let students = [];
    let selectedStudent = null;
    let filter = { program: "", status: "", search: "" };

    // --- ELEMENTOS DEL DOM ---
    const studentListContainer = document.getElementById('student-list-container');
    const studentDetailContainer = document.getElementById('student-detail-container');

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
        if (!studentListContainer) return;
        const filteredStudents = students.filter(student => {
            const fullName = `${student.nombre || ''} ${student.apellido_paterno || ''} ${student.apellido_materno || ''}`;
            const matchesProgram = !filter.program || student.nivel_educativo === filter.program;
            const matchesStatus = !filter.status || student.estatus_alumno === filter.status;
            const matchesSearch = !filter.search ||
                fullName.toLowerCase().includes(filter.search.toLowerCase()) ||
                student.matricula.toLowerCase().includes(filter.search.toLowerCase());
            return matchesProgram && matchesStatus && matchesSearch;
        });

        const statusClass = (status) => {
            if (status === 'Activo') return 'bg-green-100 text-green-800';
            if (status === 'Egresado') return 'bg-gray-100 text-gray-800';
            return 'bg-yellow-100 text-yellow-800';
        };
        const programClass = (program) => (program === 'Doctorado' ? 'bg-blue-100 text-blue-800' : 'bg-indigo-100 text-indigo-800');

        studentListContainer.innerHTML = `
            <div class="space-y-6">
                <h2 class="text-2xl font-bold mb-4">Gestión de Estudiantes</h2>
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1"><label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label><input type="text" id="search" class="w-full p-2 border border-gray-300 rounded-md" value="${filter.search}" placeholder="Nombre o matrícula..."></div>
                    <div class="flex-1"><label for="program" class="block text-sm font-medium text-gray-700 mb-1">Programa</label><select id="program" class="w-full p-2 border border-gray-300 rounded-md"><option value="">Todos</option><option value="Doctorado" ${filter.program === 'Doctorado' ? 'selected' : ''}>Doctorado</option><option value="Maestria" ${filter.program === 'Maestria' ? 'selected' : ''}>Maestría</option><option value="Licenciatura" ${filter.program === 'Licenciatura' ? 'selected' : ''}>Licenciatura</option><option value="Especialidad" ${filter.program === 'Especialidad' ? 'selected' : ''}>Especialidad</option></select></div>
                    <div class="flex-1"><label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estatus</label><select id="status" class="w-full p-2 border border-gray-300 rounded-md"><option value="">Todos</option><option value="Activo" ${filter.status === 'Activo' ? 'selected' : ''}>Activo</option><option value="Egresado" ${filter.status === 'Egresado' ? 'selected' : ''}>Egresado</option><option value="Baja" ${filter.status === 'Baja' ? 'selected' : ''}>Baja</option></select></div>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matrícula</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programa</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus</th></tr></thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${filteredStudents.map(student => `<tr data-matricula="${student.matricula}" class="hover:bg-gray-50 cursor-pointer transition-colors"><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">${student.apellido_paterno} ${student.apellido_materno}, ${student.nombre}</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-500">${student.matricula}</div></td><td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${programClass(student.nivel_educativo)}">${student.nivel_educativo}</span></td><td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass(student.estatus_alumno)}">${student.estatus_alumno}</span></td></tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>`;
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
                        </div>
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
        studentListContainer.addEventListener('input', e => {
            if (['search', 'program', 'status'].includes(e.target.id)) handleFilterChange(e);
        });
        studentListContainer.addEventListener('click', handleRowClick);
    }
    if(studentDetailContainer) {
        studentDetailContainer.addEventListener('change', handleGradeChange);
        studentDetailContainer.addEventListener('submit', e => {
            if (e.target.id === 'add-note-form') handleAddNote(e);
        });
    }
    
    // --- INICIALIZACIÓN ---
    async function init() {
        try {
            const result = await api('get_all_students');
            students = result.students;
            render();
        } catch (error) {
            console.error("Error inicial al cargar estudiantes:", error);
            if(studentListContainer) studentListContainer.innerHTML = `<div class="text-red-600 p-4">${error.message}</div>`;
        }
    }

    init();
});