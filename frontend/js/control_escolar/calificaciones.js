document.addEventListener('DOMContentLoaded', () => {

    // --- DATOS INICIALES (Agregados programName y endDate) ---
    const mockStudents = [
        {
            id: "AAABka43Tv0",
            name: "Sergio Enrique Madrigal Grimaldo",
            program: "Doctorado",
            programName: "Doctorado en Educación",
            status: "Activo",
            enrollmentDate: "2024-05-28",
            endDate: "En curso",
            matricula: "BU498",
            gender: "Hombre",
            birthDate: "1985-06-15",
            isGraduated: false,
            grades: {
                "Filosofía de la Ciencia": { grade: 10, teacher: "Dr. Armando Villanueva", date: "Enero 2024" },
                "Metodología de la Investigación": { grade: 10, teacher: "Dr. Armando Villanueva", date: "Febrero 2024" },
                "Estadística aplicada a la Educación": { grade: 9, teacher: "Dra. Sofía Reyes", date: "Marzo 2024" },
                "Seminario de Tesis Doctoral I": { grade: 10, teacher: "Dr. Juan Carlos Pérez", date: "Abril 2024" },
                "Seminario de Tesis Doctoral II": { grade: 10, teacher: "Dr. Juan Carlos Pérez", date: "Mayo 2025" }
            },
            notes: ["Alumnos inscritos a asignatura", "Realizó el pago de recursamiento para que se contemple su inscripción al curso", "Tiene 52.27 de calificación en Moodle"]
        },
        {
            id: "AAABhsnT8DM",
            name: "Veronica Yesmin Hernández Martínez",
            program: "Maestría",
            programName: "Maestría en Didáctica del Inglés",
            status: "Activo",
            enrollmentDate: "2025-01-14",
            endDate: "En curso",
            matricula: "DM333",
            gender: "Mujer",
            birthDate: "1990-03-22",
            isGraduated: false,
            grades: {
                "Teorías del Aprendizaje": { grade: 9, teacher: "Mtra. Laura Campos", date: "Enero 2025" },
                "Planeación Didáctica": { grade: 10, teacher: "Mtra. Laura Campos", date: "Febrero 2025" },
                "Didáctica General": { grade: 9, teacher: "Dr. Miguel Fernández", date: "Marzo 2025" }
            },
            notes: ["@calidad@unac.edu.mx realizar captura de calificaciones", "NP - Sin calificación", "Verificar calificación en actas"]
        },
        {
            id: "AAABR_c0RQw",
            name: "Rosalina Rodríguez Santa Cruz",
            program: "Maestría",
            programName: "Maestría en Administración Hospitalaria",
            status: "Egresado",
            enrollmentDate: "2022-07-17",
            endDate: "2024-07-16",
            matricula: "RR133",
            gender: "Mujer",
            birthDate: "1975-09-02",
            isGraduated: true,
            grades: {
                "Teorías del Aprendizaje": { grade: 10, teacher: "Mtra. Laura Campos", date: "Agosto 2022" },
                "Planeación Didáctica": { grade: 10, teacher: "Mtra. Laura Campos", date: "Septiembre 2022" },
                "Didáctica General": { grade: 10, teacher: "Dr. Miguel Fernández", date: "Octubre 2022" },
                "Metodología de la Investigación": { grade: 10, teacher: "Dr. Armando Villanueva", date: "Noviembre 2022" }
            },
            notes: ["Clase realizada. Fecha Entrega limite de Calificaciones: 17/12/2024", "Fecha Final (Cierre): 20/12/2024"]
        }
    ];

    const subjectsByProgram = {
        "Doctorado": ["Filosofía de la Ciencia", "Metodología de la Investigación", "Estadística aplicada a la Educación", "Seminario de Tesis Doctoral I", "Seminario de Tesis Doctoral II", "Economía de la Educación", "Gestión Educativa", "Evaluación Educativa", "Gerencia y Supervisión Escolar", "Administración de la Educación", "Sociología de la Educación", "Calidad Educativa", "Dirección Educativa", "Política y Legislación Educativa", "Formación Docente", "Desarrollo de Tesis Doctoral"],
        "Maestría": ["Teorías del Aprendizaje", "Planeación Didáctica", "Didáctica General", "Metodología de la Investigación", "Psicología del Aprendizaje", "Evaluación del Aprendizaje", "Fonetica y Fonología del Inglés", "Lingüística Aplicada a la Enseñanza del Inglés", "Morfosintaxis y Gramática del Inglés", "Desarrollo de Habilidades en el Idioma Inglés", "Literatura Inglesa", "Didáctica del Inglés"]
    };

    // --- ESTADO DE LA APLICACIÓN ---
    let students = JSON.parse(JSON.stringify(mockStudents));
    let selectedStudent = null;
    let filter = { program: "", status: "", search: "" };

    // --- ELEMENTOS DEL DOM ---
    const studentListContainer = document.getElementById('student-list-container');
    const studentDetailContainer = document.getElementById('student-detail-container');

    // --- FUNCIONES DE RENDERIZADO ---
    const render = () => {
        renderStudentList();
        renderStudentDetail();
    }

    const renderStudentList = () => {
        const filteredStudents = students.filter(student => {
            const matchesProgram = !filter.program || student.program === filter.program;
            const matchesStatus = !filter.status || student.status === filter.status;
            const matchesSearch = !filter.search ||
                student.name.toLowerCase().includes(filter.search.toLowerCase()) ||
                student.matricula.toLowerCase().includes(filter.search.toLowerCase());
            return matchesProgram && matchesStatus && matchesSearch;
        });

        const statusClass = (status) => {
            if (status === 'Activo') return 'bg-green-100 text-green-800';
            if (status === 'Egresado') return 'bg-gray-100 text-gray-800';
            return 'bg-yellow-100 text-yellow-800';
        };
        const programClass = (program) => (program === 'Doctorado' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800');

        studentListContainer.innerHTML = `
            <div class="space-y-6">
                <h2 class="text-2xl font-bold mb-4">Gestión de Estudiantes</h2>
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1"><label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label><input type="text" id="search" class="w-full p-2 border border-gray-300 rounded-md" value="${filter.search}" placeholder="Nombre o matrícula..."></div>
                    <div class="flex-1"><label for="program" class="block text-sm font-medium text-gray-700 mb-1">Programa</label><select id="program" class="w-full p-2 border border-gray-300 rounded-md"><option value="">Todos</option><option value="Doctorado" ${filter.program === 'Doctorado' ? 'selected' : ''}>Doctorado</option><option value="Maestría" ${filter.program === 'Maestría' ? 'selected' : ''}>Maestría</option></select></div>
                    <div class="flex-1"><label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label><select id="status" class="w-full p-2 border border-gray-300 rounded-md"><option value="">Todos</option><option value="Activo" ${filter.status === 'Activo' ? 'selected' : ''}>Activo</option><option value="Egresado" ${filter.status === 'Egresado' ? 'selected' : ''}>Egresado</option></select></div>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matrícula</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programa</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th></tr></thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${filteredStudents.map(student => `<tr data-id="${student.id}" class="hover:bg-gray-50 cursor-pointer transition-colors"><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">${student.name}</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-500">${student.matricula}</div></td><td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${programClass(student.program)}">${student.program}</span></td><td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass(student.status)}">${student.status}</span></td></tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>`;
    }

    const renderStudentDetail = () => {
        if (!selectedStudent) {
            studentDetailContainer.innerHTML = `<div class="bg-white shadow rounded-lg p-6"><p class="text-gray-500 italic">Seleccione un estudiante para ver detalles</p></div>`;
            return;
        }

        const programSubjects = subjectsByProgram[selectedStudent.program];
        const totalSubjects = programSubjects.length;
        const completedSubjects = Object.keys(selectedStudent.grades).filter(subject => typeof selectedStudent.grades[subject].grade === 'number').length;
        const completionPercentage = totalSubjects > 0 ? Math.round((completedSubjects / totalSubjects) * 100) : 0;

        // Formatear fechas para que se vean mejor
        const enrollmentDateFormatted = new Date(selectedStudent.enrollmentDate).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });

        studentDetailContainer.innerHTML = `
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-2xl font-bold">${selectedStudent.name}</h2>
                    <p class="text-gray-600">Matrícula: ${selectedStudent.matricula}</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Información Académica</h3>
                            <dl class="grid grid-cols-2 gap-x-4 gap-y-2">
                                <dt class="text-sm font-medium text-gray-500">Programa:</dt>
                                <dd class="text-sm text-gray-900">${selectedStudent.programName}</dd>
                                
                                <dt class="text-sm font-medium text-gray-500">Estatus:</dt>
                                <dd class="text-sm text-gray-900">${selectedStudent.status}</dd>

                                <dt class="text-sm font-medium text-gray-500">Fecha de Ingreso:</dt>
                                <dd class="text-sm text-gray-900">${enrollmentDateFormatted}</dd>
                                
                                <dt class="text-sm font-medium text-gray-500">Fecha de Término:</dt>
                                <dd class="text-sm text-gray-900">${selectedStudent.endDate}</dd>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Progreso Académico</h3>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${completionPercentage}%"></div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Asignaturas completadas: ${completedSubjects} de ${totalSubjects} (${completionPercentage}%)</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">Calificaciones</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignatura</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periodo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calificación</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${programSubjects.map(subject => {
                                    const gradeInfo = selectedStudent.grades[subject];
                                    const grade = gradeInfo?.grade ?? "NP";
                                    const teacher = gradeInfo?.teacher ?? "N/A";
                                    const date = gradeInfo?.date ?? "N/A";
                                    return `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${subject}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${teacher}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <input type="text" class="w-20 border border-gray-300 rounded-md p-1 text-center" value="${grade}" data-subject="${subject}">
                                        </td>
                                    </tr>
                                `}).join('')}
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8"><h3 class="text-lg font-semibold mb-4">Notas y Comentarios</h3><ul id="notes-list" class="divide-y divide-gray-200 mb-4">${selectedStudent.notes.map(note => `<li class="py-2 text-sm text-gray-600">${note}</li>`).join('')}</ul><form id="add-note-form" class="flex"><input type="text" name="note" class="flex-1 p-2 border border-gray-300 rounded-l-md" placeholder="Agregar nueva nota..."><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">Agregar</button></form></div>
                </div>
            </div>`;
    }

    // --- MANEJADORES DE EVENTOS ---
    const handleFilterChange = (e) => { filter[e.target.id] = e.target.value; renderStudentList(); };
    const handleRowClick = (e) => {
        const row = e.target.closest('tr');
        if (row?.dataset.id) { selectedStudent = students.find(s => s.id === row.dataset.id); render(); }
    };
    
    const handleGradeChange = (e) => {
        const input = e.target;
        if (input.tagName === 'INPUT' && input.dataset.subject && selectedStudent) {
            const subject = input.dataset.subject;
            let value = input.value;
            const numericValue = parseFloat(value);
            if (!isNaN(numericValue) && value.trim() !== "") value = numericValue;
            if (!selectedStudent.grades[subject]) {
                selectedStudent.grades[subject] = { grade: null, teacher: 'N/A', date: 'N/A' };
            }
            selectedStudent.grades[subject].grade = value;
        }
    };
    
    const handleAddNote = (e) => {
        e.preventDefault();
        const noteInput = e.target.elements.note;
        if (noteInput.value.trim() && selectedStudent) {
            selectedStudent.notes.push(noteInput.value.trim());
            noteInput.value = '';
            renderStudentDetail();
        }
    };

    // --- ASIGNACIÓN DE EVENTOS (Delegación) ---
    studentListContainer.addEventListener('input', e => { if (['search', 'program', 'status'].includes(e.target.id)) handleFilterChange(e); });
    studentListContainer.addEventListener('click', handleRowClick);
    studentDetailContainer.addEventListener('change', handleGradeChange);
    studentDetailContainer.addEventListener('submit', e => { if(e.target.id === 'add-note-form') handleAddNote(e); });
    
    // --- INICIALIZACIÓN ---
    render();
});