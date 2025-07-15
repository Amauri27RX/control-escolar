const BASE_URL = '/control-escolar';

document.addEventListener('DOMContentLoaded', function() {
    // 1. Obtener referencias a todos los elementos del formulario
    const institucionSelect = document.getElementById('institucion');
    const nivelSelect = document.getElementById('nivel_educativo');
    const programaSelect = document.getElementById('programa');
    const rvoeAutoSelect = document.getElementById('rvoe_auto');
    const modalidadSelect = document.getElementById('modalidad');
    const dgpInput = document.getElementById('dgp');
    const fechaRvoeInput = document.getElementById('fecha_rvoe');
    const resetButton = document.querySelector('.btn-reset');
    const saveButton = document.querySelector('.btn-save-all');
    const matriculaInput = document.getElementById('matricula');

    // 2. Mapeo de niveles educativos
    const nivelMap = {
        'licenciatura': 'Licenciatura',
        'especialidad': 'Especialidad',
        'maestria': 'Maestría',
        'doctorado': 'Doctorado'
    };

    // 3. Cargar opciones de escuelas al inicio
    cargarinstitucion();

    function cargarinstitucion() {
        fetch(`/control-escolar/backend/marketing/api/escuela_options.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                institucionSelect.innerHTML = '<option value="">Seleccionar</option>';
                data.forEach(institucion => {
                    const option = document.createElement('option');
                    option.value = institucion.id;
                    option.textContent = institucion.nombre;
                    institucionSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar institucion:', error);
                institucionSelect.innerHTML = '<option value="">Error al cargar institucion</option>';
            });
    }

    // 4. Función para cargar programas según nivel y escuela
    function cargarProgramas() {
        const nivelKey = nivelSelect.value;
        const nivelValue = nivelMap[nivelKey] || '';
        const institucionValue = institucionSelect.value;

        // Limpiar campos dependientes
        programaSelect.innerHTML = '<option value="">Seleccione un programa</option>';
        rvoeAutoSelect.value = '';
        modalidadSelect.value = '';
        dgpInput.value = '';
        fechaRvoeInput.value = '';

        if (nivelValue && institucionValue) {
            programaSelect.disabled = true;
            programaSelect.innerHTML = '<option value="">Cargando programas...</option>';

            fetch(`/control-escolar/backend/marketing/api/get_programa.php?nivel=${encodeURIComponent(nivelValue)}&institucion=${encodeURIComponent(institucionValue)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    programaSelect.innerHTML = '<option value="">Seleccione un programa</option>';

                    if (data.length === 0) {
                        programaSelect.innerHTML = '<option value="">No hay programas disponibles</option>';
                    } else {
                        data.forEach(programa => {
                            const option = document.createElement('option');
                            option.value = programa.dgp;
                            option.textContent = programa.nombre_programa;
                            option.dataset.rvoe = programa.rvoe || '';
                            option.dataset.modalidad = programa.modalidades || '';
                            option.dataset.fechaRvoe = programa.fecha_rvoe || '';
                            programaSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    alert('No se pudieron cargar los programas. Detalles: ' + error.message);
                    programaSelect.innerHTML = `<option value="">Error ${error.message}</option>`;
                })
                .finally(() => {
                    programaSelect.disabled = false;
                });
        }
    }

    // 5. Event listeners para cambios en escuela y nivel
    institucionSelect.addEventListener('change', cargarProgramas);
    nivelSelect.addEventListener('change', cargarProgramas);

    // 6. Evento para cuando seleccionen un programa específico
    programaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            dgpInput.value = selectedOption.value;
            rvoeAutoSelect.value = selectedOption.dataset.rvoe || '';
            modalidadSelect.value = selectedOption.dataset.modalidad || '';

            if (selectedOption.dataset.fechaRvoe) {
                const fecha = new Date(selectedOption.dataset.fechaRvoe);
                fechaRvoeInput.value = fecha.toISOString().split('T')[0];
            } else {
                fechaRvoeInput.value = '';
            }
        } else {
            dgpInput.value = '';
            rvoeAutoSelect.value = '';
            modalidadSelect.value = '';
            fechaRvoeInput.value = '';
        }
    });

    // 7. Autocompletar datos del alumno al ingresar matrícula
    matriculaInput.addEventListener('change', function() {
        const matricula = this.value.trim();
        
        if (matricula) {
            fetch(`/control-escolar/backend/marketing/api/get_alumno.php?matricula=${encodeURIComponent(matricula)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Autocompletar campos personales
                        document.getElementById('nombre').value = data.alumno.nombre || '';
                        document.getElementById('apellido_paterno').value = data.alumno.apellido_paterno || '';
                        document.getElementById('apellido_materno').value = data.alumno.apellido_materno || '';
                        document.getElementById('genero').value = data.alumno.genero || '';
                        
                        // Si hay DGP, cargar programa asociado
                        if (data.alumno.dgp && data.programa) {
                            dgpInput.value = data.alumno.dgp;
                            // Buscar y seleccionar el programa correspondiente
                            const options = programaSelect.options;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === data.alumno.dgp) {
                                    programaSelect.selectedIndex = i;
                                    programaSelect.dispatchEvent(new Event('change'));
                                    break;
                                }
                            }
                        }
                    } else {
                        alert(data.message || 'Alumno no encontrado');
                    }
                })
                .catch(error => {
                    console.error('Error al buscar alumno:', error);
                    alert('Error al buscar información del alumno');
                });
        }
    });

    // 8. Botón para borrar todos los campos
    resetButton.addEventListener('click', function() {
        if (confirm('¿Estás seguro que deseas borrar todos los campos?')) {
            document.querySelectorAll('input').forEach(input => {
                if (['text', 'number', 'date'].includes(input.type)) input.value = '';
                if (input.type === 'file') {
                    input.value = '';
                    const statusElement = input.nextElementSibling;
                    if (statusElement && statusElement.classList.contains('file-status')) {
                        statusElement.textContent = 'Sin archivo seleccionado';
                    }
                }
                if (input.type === 'checkbox') input.checked = false;
            });
            document.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            programaSelect.innerHTML = '<option value="">Seleccione un programa</option>';
            alert('Todos los campos han sido borrados');
        }
    });

    // 9. Botón para guardar toda la información
    saveButton.addEventListener('click', async function() {
        try {
            // Verificar que todos los elementos requeridos existen
            const requiredElements = {
                nombre: document.getElementById('nombre'),
                apellido_paterno: document.getElementById('apellido_paterno'),
                dgp: document.getElementById('dgp'),
                matricula: document.getElementById('matricula'),
                genero: document.getElementById('genero')
            };

            // Verificar elementos nulos
            for (const [key, element] of Object.entries(requiredElements)) {
                if (!element) {
                    throw new Error(`No se encontró el campo ${key}`);
                }
            }

            // Obtener valores de los campos
            const nombre = requiredElements.nombre.value.trim();
            const apellidoPaterno = requiredElements.apellido_paterno.value.trim();
            const dgp = requiredElements.dgp.value.trim();
            const matricula = requiredElements.matricula.value.trim();
            const genero = requiredElements.genero.value;

            // Validar campos obligatorios
            if (!nombre || !dgp || !matricula || !genero) {
                throw new Error('Por favor complete todos los campos obligatorios:\n\n' +
                    '- Nombre(s)\n' +
                    '- DGP\n' +
                    '- Matrícula\n' +
                    '- Género');
            }

            // Configurar estado de carga
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            // Preparar FormData
            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('apellido_paterno', apellidoPaterno);
            formData.append('apellido_materno', document.getElementById('apellido_materno').value.trim());
            formData.append('dgp', dgp);
            formData.append('matricula', matricula);
            formData.append('genero', genero);
            formData.append('fecha_inicio', document.getElementById('fecha_inicio').value); // Asegúrate de enviar fecha_inicio
            formData.append('modalidad', document.getElementById('modalidad').value); // Enviar modalidad auto-rellenada

            // Agregar archivos
            const fileInputs = [
                'acta_nacimiento', 'curp', 'solicitud', 'certificado', 
                'titulo', 'cedula', 'otem', 'comprobante_inscripcion'
            ];
            
            fileInputs.forEach(id => {
                const input = document.getElementById(id);
                if (input?.files?.[0]) {
                    formData.append(id, input.files[0]);
                }
            });

            // Enviar datos al servidor
            const response = await fetch(`/control-escolar/backend/marketing/api/guardar_alumno.php`, {
                method: 'POST',
                body: formData
            });

            // Procesar respuesta
            const result = await response.json().catch(() => {
                throw new Error('La respuesta del servidor no es válida');
            });
            
            if (!response.ok) {
                throw new Error(result.error || 'Error al guardar los datos');
            }

            if (result.success) {
                alert('Alumno guardado correctamente');
                // Opcional: resetear formulario
                // resetButton.click();
            } else {
                throw new Error(result.error || 'Error desconocido');
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar: ' + error.message);
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = 'Guardar toda la información';
        }
    });

    // 10. Mostrar estado de archivos seleccionados
    document.querySelectorAll('input[type="file"]').forEach(input => {
        const statusElement = document.createElement('small');
        statusElement.className = 'file-status';
        statusElement.textContent = 'Sin archivo seleccionado';
        input.parentNode.appendChild(statusElement);

        input.addEventListener('change', function() {
            statusElement.textContent = this.files[0] 
                ? `Archivo seleccionado: ${this.files[0].name}` 
                : 'Sin archivo seleccionado';
        });
    });
});