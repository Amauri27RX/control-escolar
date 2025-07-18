document.addEventListener('DOMContentLoaded', function() {
    // --- Elementos del DOM ---
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');
    const studentDetailsCard = document.getElementById('studentDetailsCard');
    const saveButton = document.getElementById('saveButton');
    const materiasList = document.getElementById('materiasList');
    
    let currentStudentData = null;

    // --- Funciones ---
    async function searchStudents() {
        const term = searchInput.value.trim();
        if (term.length < 3) {
            searchResults.innerHTML = '';
            return;
        }
        
        const response = await fetch(`/control-escolar/backend/control_escolar/api/api_carga_academica.php?action=search_students&term=${term}`);
        const result = await response.json();
        
        searchResults.innerHTML = '';
        if (result.success && result.students.length > 0) {
            result.students.forEach(student => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = `${student.nombre_completo} (${student.matricula})`;
                item.dataset.matricula = student.matricula;
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    loadStudentDetails(student.matricula);
                });
                searchResults.appendChild(item);
            });
        } else {
            searchResults.innerHTML = '<div class="list-group-item">No se encontraron alumnos.</div>';
        }
    }

    async function loadStudentDetails(matricula) {
        searchResults.innerHTML = '';
        searchInput.value = '';
        studentDetailsCard.style.display = 'block';
        materiasList.innerHTML = '<div class="list-group-item">Cargando...</div>';

        try {
            const response = await fetch(`/control-escolar/backend/control_escolar/api/api_carga_academica.php?action=get_academic_load&matricula=${matricula}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            currentStudentData = result.data;
            document.getElementById('studentMatricula').textContent = currentStudentData.info.matricula;
            document.getElementById('studentName').textContent = currentStudentData.info.nombre_completo;
            document.getElementById('studentProgram').textContent = currentStudentData.info.nombre_programa;
            document.getElementById('studentCiclo').textContent = currentStudentData.info.ciclo_inicio;

            renderMateriasList();

        } catch (error) {
            alert(`Error al cargar datos del alumno: ${error.message}`);
        }
    }

    function renderMateriasList() {
        materiasList.innerHTML = '';
        if (currentStudentData.plan_estudios.length === 0) {
            materiasList.innerHTML = '<div class="list-group-item">Este programa no tiene materias en su plan de estudios.</div>';
            return;
        }

        currentStudentData.plan_estudios.forEach(materia => {
            const isChecked = currentStudentData.materias_inscritas.includes(materia.clave_materia);
            const item = document.createElement('div');
            item.className = 'list-group-item';
            item.innerHTML = `
                <div class="form-check form-switch fs-5">
                    <input class="form-check-input" type="checkbox" role="switch" id="materia-${materia.clave_materia}" value="${materia.clave_materia}" ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label" for="materia-${materia.clave_materia}">
                        ${materia.nombre} <small class="text-muted">(${materia.clave_materia})</small>
                    </label>
                </div>
            `;
            materiasList.appendChild(item);
        });
    }

    async function saveChanges() {
        if (!currentStudentData) {
            alert("Primero debe seleccionar un alumno.");
            return;
        }

        const selectedMaterias = [];
        document.querySelectorAll('#materiasList input[type="checkbox"]:checked').forEach(checkbox => {
            selectedMaterias.push(checkbox.value);
        });

        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        try {
            const response = await fetch('/control-escolar/backend/control_escolar/api/api_carga_academica.php?action=update_academic_load', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    matricula: currentStudentData.info.matricula,
                    ciclo_inicio: currentStudentData.info.ciclo_inicio,
                    materias: selectedMaterias
                })
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            alert(result.message);
            studentDetailsCard.style.display = 'none'; // Ocultar para buscar a otro alumno

        } catch (error) {
            alert(`Error al guardar los cambios: ${error.message}`);
        } finally {
            saveButton.disabled = false;
            saveButton.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Cambios en la Carga Académica';
        }
    }

    // --- Event Listeners ---
    searchButton.addEventListener('click', searchStudents);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') searchStudents();
    });
    saveButton.addEventListener('click', saveChanges);
});