document.addEventListener('DOMContentLoaded', function () {
    const formInscripcion = document.getElementById('formInscripcion');
    const btnRevisarDatos = document.getElementById('btnRevisarDatos');
    const modalConfirmacionElement = document.getElementById('modalConfirmacionInscripcion');
    const modalConfirmacion = modalConfirmacionElement ? new bootstrap.Modal(modalConfirmacionElement) : null;
    const datosParaConfirmarEnModalDiv = document.getElementById('datosParaConfirmarEnModal');
    const btnEnviarFinal = document.getElementById('btnEnviarFinal');
    const mensajeRespuestaServidorDiv = document.getElementById('mensajeRespuestaServidor');

    // --- Elementos para la funcionalidad dinámica ---
    const inputMatricula = document.getElementById('matricula');
    const selectInstitucion = document.getElementById('institucion');
    const selectNivelEducativo = document.getElementById('nivel_educativo');
    const selectCarreraPrograma = document.getElementById('carrera_programa');
    const inputRVOE = document.getElementById('rvoe');
    const inputFechaRVOE = document.getElementById('fecha_rvoe');
    const inputDGP = document.getElementById('dgp');

    let isEditMode = false;
    let currentMatriculaForEdit = null;
    let programasDataStore = {};

    const formStructure = [
        {
            title: "Programa a Inscribirse",
            fields: [
                { id: "institucion", label: "Institución", name: "institucion", type: "select" },
                { id: "nivel_educativo", label: "Nivel Educativo", name: "nivel_educativo", type: "select" },
                { id: "carrera_programa", label: "Carrera/Programa", name: "carrera_programa", type: "select" },
                { id: "modalidad", label: "Modalidad", name: "modalidad", type: "select" },
                { id: "rvoe", label: "RVOE", name: "rvoe", type: "text" },
                { id: "fecha_rvoe", label: "Fecha de RVOE", name: "fecha_rvoe", type: "date" },
                { id: "dgp", label: "DGP", name: "dgp", type: "text" },
                { id: "permanencia", label: "Permanencia", name: "permanencia", type: "text" },
                { id: "promocion", label: "Promoción aplicada", name: "promocion", type: "text" },
                { id: "fecha_ingreso", label: "Fecha de Ingreso", name: "fecha_ingreso", type: "date" },
                { id: "ciclo_inicio", label: "Ciclo Escolar (Inicio)", name: "ciclo_inicio", type: "select" },
                { id: "ciclo_fin", label: "Ciclo Escolar (Fin)", name: "ciclo_fin", type: "select" },
                { id: "modalidad_titulacion", label: "Modalidad de Titulación", name: "modalidad_titulacion", type: "text" },
                { id: "estatus_alumno", label: "Estatus del Alumno", name: "estatus_alumno", type: "select" }
            ]
        },
        {
            title: "Antecedentes de estudios",
            fields: [
                { id: "nivel_anterior", label: "Nivel educativo anterior", name: "nivel_anterior", type: "text" },
                { id: "nombre_institucion", label: "Nombre de la Institución", name: "nombre_institucion", type: "text" },
                { id: "ubicacion_institucion", label: "Ciudad de la Institución", name: "ubicacion_institucion", type: "text" },
                { id: "fecha_inicio_antecedentes", label: "Fecha de Inicio (Antecedentes)", name: "fecha_inicio_antecedentes", type: "date" },
                { id: "fecha_finalizacion", label: "Fecha de Finalización", name: "fecha_finalizacion", type: "date" }
            ]
        },
        {
            title: "Datos Personales",
            fields: [
                { id: "matricula", label: "Matrícula", name: "matricula", type: "text" },
                { id: "nombre", label: "Nombre(s)", name: "nombre", type: "text" },
                { id: "apellido_paterno", label: "Apellido paterno", name: "apellido_paterno", type: "text" },
                { id: "apellido_materno", label: "Apellido materno", name: "apellido_materno", type: "text" },
                { id: "genero", label: "Género", name: "genero", type: "select" }, // Cambiado a select
                { id: "fecha_nacimiento", label: "Fecha de Nacimiento", name: "fecha_nacimiento", type: "date" },
                { id: "edad", label: "Edad", name: "edad", type: "number" },
                { id: "pais_nacimiento", label: "País de nacimiento", name: "pais_nacimiento", type: "text" },
                { id: "ciudad_nacimiento", label: "Ciudad o Estado de nacimiento", name: "ciudad_nacimiento", type: "text" },
                { id: "nacionalidad", label: "Nacionalidad", name: "nacionalidad", type: "text" },
                { id: "curp", label: "CURP", name: "curp", type: "text" },
                { id: "cura", label: "CURA", name: "cura", type: "text" }
            ]
        },
        {
            title: "Ubicación de residencia e información de contacto",
            fields: [
                { id: "pais_residencia", label: "País", name: "pais_residencia", type: "text" },
                { id: "ciudad_residencia", label: "Ciudad o Estado", name: "ciudad_residencia", type: "text" },
                { id: "colonia_residencia", label: "Colonia o localidad", name: "colonia_residencia", type: "text" },
                { id: "calle_residencia", label: "Calle", name: "calle_residencia", type: "text" },
                { id: "num_int_residencia", label: "Núm. Int. (Opcional)", name: "num_int_residencia", type: "text" },
                { id: "num_ext_residencia", label: "Núm. Ext.", name: "num_ext_residencia", type: "text" },
                { id: "telefono_personal", label: "Teléfono fijo/móvil", name: "telefono_personal", type: "tel" },
                { id: "correo_personal", label: "Correo electrónico", name: "correo_personal", type: "email" }
            ]
        },
        {
            title: "Información del lugar de trabajo y el cargo",
            fields: [
                { id: "nombre_empresa", label: "Nombre de la Empresa/Institución", name: "nombre_empresa", type: "text" },
                { id: "puesto_trabajo", label: "Puesto o Cargo", name: "puesto_trabajo", type: "text" },
                { id: "area_trabajo", label: "Área o Departamento", name: "area_trabajo", type: "text" },
                { id: "pais_trabajo", label: "Ubicación (País)", name: "pais_trabajo", type: "text" },
                { id: "ciudad_trabajo", label: "Ciudad o Estado", name: "ciudad_trabajo", type: "text" },
                { id: "colonia_trabajo", label: "Colonia o Localidad", name: "colonia_trabajo", type: "text" },
                { id: "calle_trabajo", label: "Calle", name: "calle_trabajo", type: "text" },
                { id: "num_int_trabajo", label: "Núm. Int. (Opcional)", name: "num_int_trabajo", type: "text" },
                { id: "num_ext_trabajo", label: "Núm. Ext.", name: "num_ext_trabajo", type: "text" },
                { id: "telefono_trabajo", label: "Teléfono", name: "telefono_trabajo", type: "tel" },
                { id: "correo_trabajo", label: "Correo Electrónico Corporativo", name: "correo_trabajo", type: "email" }
            ]
        },
        {
            title: "Documentos Requeridos",
            fields: [
                { id: "acta_nacimiento_doc", label: "Acta de Nacimiento", name: "acta_nacimiento_doc", type: "text" },
                { id: "curp_doc", label: "CURP (Documento)", name: "curp_doc", type: "text" },
                { id: "certificado_estudios_doc", label: "Certificado de Estudios", name: "certificado_estudios_doc", type: "text" },
                { id: "titulo_universitario_doc", label: "Título Universitario (Documento)", name: "titulo_universitario_doc", type: "text" },
                { id: "comprobante_domicilio_doc", label: "Comprobante de Domicilio", name: "comprobante_domicilio_doc", type: "text" },
                { id: "carta_otem_doc", label: "Carta OTEM", name: "carta_otem_doc", type: "text" }
            ]
        }
    ];

    function cargarInstituciones() {
        if (!selectInstitucion) return;
        fetch('/control-escolar/backend/control_escolar/api/obtener_datos_programa.php?action=get_instituciones')
            .then(response => response.json())
            .then(data => {
                selectInstitucion.innerHTML = '<option selected disabled value="">Seleccione una institución</option>';
                if (data.success && data.instituciones) {
                    data.instituciones.forEach(institucion => {
                        const option = document.createElement('option');
                        option.value = institucion.id_institucion;
                        option.textContent = institucion.nombre;
                        selectInstitucion.appendChild(option);
                    });
                } else {
                    console.error('Error al cargar instituciones:', data.message);
                    selectInstitucion.innerHTML = '<option selected disabled value="">Error al cargar</option>';
                }
            })
            .catch(error => {
                console.error('Fetch error para instituciones:', error);
                selectInstitucion.innerHTML = '<option selected disabled value="">Error de conexión</option>';
            });
    }
    if (selectInstitucion) cargarInstituciones();

    function actualizarListaProgramas() {
        const nivelSeleccionado = selectNivelEducativo ? selectNivelEducativo.value : '';
        const institucionSeleccionada = selectInstitucion ? selectInstitucion.value : '';
        console.log('actualizarListaProgramas - Nivel:', nivelSeleccionado, 'Institucion:', institucionSeleccionada);

        if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Cargando programas...</option>';
        if (inputRVOE) inputRVOE.value = '';
        if (inputFechaRVOE) inputFechaRVOE.value = '';
        if (inputDGP) inputDGP.value = '';
        programasDataStore = {};

        if (nivelSeleccionado && institucionSeleccionada) {
            console.log('HACIENDO FETCH para programas...');
            fetch(`/control-escolar/backend/control_escolar/api/obtener_datos_programa.php?action=get_programas_por_nivel&nivel=${encodeURIComponent(nivelSeleccionado)}&institucion=${encodeURIComponent(institucionSeleccionada)}`)
                .then(response => {
                    if (!response.ok) throw new Error(`Error HTTP ${response.status} al obtener programas.`);
                    return response.json();
                })
                .then(programasResult => {
                    if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Seleccione una carrera</option>';
                    if (programasResult.success && programasResult.programas.length > 0) {
                        programasResult.programas.forEach(programa => {
                            programasDataStore[programa.dgp] = programa;
                            const option = document.createElement('option');
                            option.value = programa.dgp;
                            option.textContent = programa.nombre_programa;
                            if (selectCarreraPrograma) selectCarreraPrograma.appendChild(option);
                        });
                        if (selectCarreraPrograma && selectCarreraPrograma.dataset.pendingValue) {
                            const pendingDGP = selectCarreraPrograma.dataset.pendingValue;
                            if (Array.from(selectCarreraPrograma.options).some(opt => opt.value === pendingDGP)) {
                                selectCarreraPrograma.value = pendingDGP;
                            } else {
                                console.warn(`El DGP pendiente '${pendingDGP}' no se encontró en las opciones de carrera.`);
                            }
                            delete selectCarreraPrograma.dataset.pendingValue;
                            selectCarreraPrograma.dispatchEvent(new Event('change'));
                        }
                    } else if (programasResult.success && programasResult.programas.length === 0) {
                        if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">No hay programas para esta selección</option>';
                    } else {
                        console.error('Error al obtener programas:', programasResult.message);
                        if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Error al cargar programas</option>';
                    }
                })
                .catch(error => {
                    console.error('Error en fetch para programas:', error);
                    if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Error de conexión</option>';
                });
        } else {
            console.log('NO SE HACE FETCH de programas - falta nivel o institución.');
            if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Seleccione Institución y Nivel Educativo</option>';
        }
    }

    if (selectNivelEducativo) selectNivelEducativo.addEventListener('change', actualizarListaProgramas);
    if (selectInstitucion) selectInstitucion.addEventListener('change', actualizarListaProgramas);

    if (selectCarreraPrograma) {
        selectCarreraPrograma.addEventListener('change', function() {
            const dgpSeleccionado = this.value;
            if (dgpSeleccionado && programasDataStore[dgpSeleccionado]) {
                const programaInfo = programasDataStore[dgpSeleccionado];
                if (inputRVOE) inputRVOE.value = programaInfo.rvoe || '';
                if (inputFechaRVOE) inputFechaRVOE.value = programaInfo.fecha_rvoe || '';
                if (inputDGP) inputDGP.value = programaInfo.dgp || '';
            } else {
                if (inputRVOE) inputRVOE.value = '';
                if (inputFechaRVOE) inputFechaRVOE.value = '';
                if (inputDGP) inputDGP.value = '';
            }
        });
    }
    
    if (inputMatricula) {
        inputMatricula.addEventListener('blur', function () {
            const matriculaValue = this.value.trim();
            if (matriculaValue) {
                fetch(`/control-escolar/backend/control_escolar/api/obtener_datos_alumno.php?matricula=${encodeURIComponent(matriculaValue)}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`Error HTTP ${response.status} al buscar alumno.`);
                        return response.json();
                    })
                    .then(result => {
                        if (result.success && result.data) {
                            populateForm(result.data);
                            isEditMode = true;
                            currentMatriculaForEdit = matriculaValue;
                            // if (inputMatricula) inputMatricula.disabled = true; // Opcional
                            mostrarMensajeEnFormulario('Datos del alumno cargados para edición.', 'info', mensajeRespuestaServidorDiv);
                        } else {
                            isEditMode = false;
                            currentMatriculaForEdit = null;
                            // if (inputMatricula) inputMatricula.disabled = false;
                            // Considerar limpiar el formulario o solo los campos relevantes
                            // formInscripcion.reset(); // Esto limpia todo
                            if (result.message) {
                                 mostrarMensajeEnFormulario(result.message + ' Puede proceder a un nuevo registro.', 'warning', mensajeRespuestaServidorDiv);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener datos del alumno:', error);
                        mostrarMensajeEnFormulario(`Error al buscar alumno: ${error.message}`, 'danger', mensajeRespuestaServidorDiv);
                        isEditMode = false; // Asegurar que no esté en modo edición si falla la búsqueda
                        currentMatriculaForEdit = null;
                        // if (inputMatricula) inputMatricula.disabled = false;
                    });
            }
        });

        inputMatricula.addEventListener('input', function() { // Resetear modo edición si se borra la matrícula
            if (this.value.trim() === '') {
                isEditMode = false;
                currentMatriculaForEdit = null;
                if (inputMatricula) inputMatricula.disabled = false;
                // Aquí podrías llamar a una función que resetee los campos del programa también
                // formInscripcion.reset(); // O una limpieza más selectiva
            }
        });
    }

    function populateForm(data) {
        formStructure.forEach(section => {
            section.fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element) {
                    console.warn(`populateForm: Elemento no encontrado: ${field.id}`);
                    return;
                }

                if (field.id === 'edad') {
                    if (data.fecha_nacimiento) {
                        try {
                            const birthDate = new Date(data.fecha_nacimiento);
                            if (isNaN(birthDate.getTime())) throw new Error("Fecha de nacimiento inválida");
                            const today = new Date();
                            let age = today.getFullYear() - birthDate.getFullYear();
                            const m = today.getMonth() - birthDate.getMonth();
                            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
                            element.value = age >= 0 ? age : '';
                        } catch (e) {
                            console.error("Error al calcular edad:", e, "Fecha Nac.:", data.fecha_nacimiento);
                            element.value = '';
                        }
                    } else {
                        element.value = '';
                    }
                } else if (data.hasOwnProperty(field.id)) {
                    if (element.tagName === 'SELECT') {
                        if (field.id !== 'institucion' && field.id !== 'nivel_educativo' && field.id !== 'carrera_programa') {
                            element.value = data[field.id] || '';
                        }
                        // Para 'genero', que ahora es un select, data[field.id] (ej. data.genero) contendrá 'M', 'F', 'O'
                        // y element.value = 'M' (por ejemplo) seleccionará la opción correcta.
                        // El caso especial que convertía a "Masculino" ya no es necesario aquí.
                    } else { // Inputs de texto, date, number (excepto 'edad' ya manejado)
                        element.value = data[field.id] || '';
                    }
                } else {
                     // Si el dato no viene de PHP y no es 'edad' (que se calcula o limpia)
                     // y no es un select dependiente principal
                    if (field.id !== 'institucion' && field.id !== 'nivel_educativo' && field.id !== 'carrera_programa' && field.id !== 'edad') {
                        // element.value = ''; // Opcional: Limpiar estos campos
                    }
                }
            });
        });

        let initialInstitucion = data.id_institucion_programa || "";
        let initialNivel = data.nivel_educativo || "";
        let initialDGP = data.dgp || "";

        if (selectInstitucion) selectInstitucion.value = initialInstitucion;
        if (selectNivelEducativo) selectNivelEducativo.value = initialNivel;
        
        if (inputRVOE && data.rvoe) inputRVOE.value = data.rvoe;
        if (inputFechaRVOE && data.fecha_rvoe) inputFechaRVOE.value = data.fecha_rvoe;
        if (inputDGP && data.dgp) inputDGP.value = data.dgp; // Input de texto DGP

        if (initialNivel && initialInstitucion) {
            if (selectCarreraPrograma) selectCarreraPrograma.dataset.pendingValue = initialDGP;
            actualizarListaProgramas();
        } else {
            if (selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Seleccione Institución y Nivel Educativo</option>';
        }
    }

    if (btnRevisarDatos) {
        btnRevisarDatos.addEventListener('click', function () {
            // ... (lógica del modal como la tenías, parece correcta) ...
            // Solo asegúrate que la validación y recolección de datos esté bien
            let modalHtmlContent = '';
            formDataForSubmission = new FormData(formInscripcion); // Usar FormData(form) es más simple

            // Si deshabilitaste la matrícula en modo edición, FormData no la incluirá.
            // Añádela manualmente si es necesario.
            if (isEditMode && currentMatriculaForEdit && inputMatricula && inputMatricula.disabled) {
                formDataForSubmission.set('matricula', currentMatriculaForEdit); // Usar .set para evitar duplicados si ya existe
            }
            // O, si el input de matrícula está habilitado, FormData lo tomará.

            let allValid = true;
            if (selectInstitucion && !selectInstitucion.value) {
                 mostrarMensajeEnFormulario('Por favor, seleccione una Institución.', 'warning', mensajeRespuestaServidorDiv);
                 allValid = false;
            }
            // Añade más validaciones si es necesario...
            if (!allValid) return;

            modalHtmlContent += "<h4>Revisa tus datos:</h4>";
            formStructure.forEach(section => {
                modalHtmlContent += `<h5 class="mt-3 text-primary">${section.title}</h5>`;
                modalHtmlContent += '<ul class="list-group list-group-flush mb-3">';
                section.fields.forEach(field => {
                    const element = document.getElementById(field.id);
                    let displayValue = "<em>No completado</em>";
                    const valueFromFormData = formDataForSubmission.get(field.name);

                    if (element) {
                        if (element.tagName === 'SELECT') {
                            if (element.value && element.options[element.selectedIndex] && !element.options[element.selectedIndex].disabled) {
                                displayValue = element.options[element.selectedIndex].text;
                            } else {
                                displayValue = "<em>No seleccionado</em>";
                            }
                        } else if (element.type === 'file') {
                            if (element.files && element.files.length > 0) {
                                displayValue = element.files[0].name;
                            } else {
                                displayValue = "<em>No seleccionado</em>";
                            }
                        } else {
                            displayValue = element.value || "<em>No completado</em>";
                        }
                    } else if (valueFromFormData) { // Para matrícula si está deshabilitada pero en FormData
                        displayValue = valueFromFormData;
                    }
                    modalHtmlContent += `<li class="list-group-item"><strong class="me-2">${field.label}:</strong> ${displayValue}</li>`;
                });
                modalHtmlContent += '</ul>';
            });

            if(datosParaConfirmarEnModalDiv) datosParaConfirmarEnModalDiv.innerHTML = modalHtmlContent;
            if(modalConfirmacion) modalConfirmacion.show();
        });
    }

    if (btnEnviarFinal) {
        btnEnviarFinal.addEventListener('click', function() {
            if (formDataForSubmission) {
                let targetUrl = isEditMode ? '/control-escolar/backend/control_escolar/api/actualizar_datos_alumno.php' : '/control-escolar/backend/control_escolar/api/procesar_nueva_inscripcion.php';
                
                // Si la matrícula fue deshabilitada y no se añadió explícitamente a formDataForSubmission antes:
                if (isEditMode && currentMatriculaForEdit && inputMatricula && inputMatricula.disabled && !formDataForSubmission.has('matricula')) {
                    formDataForSubmission.append('matricula', currentMatriculaForEdit);
                }

                fetch(targetUrl, {
                    method: 'POST',
                    body: formDataForSubmission
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                           throw new Error(`Error del servidor: ${response.status} ${response.statusText}. Detalles: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarMensajeEnFormulario(data.message || (isEditMode ? '¡Actualización exitosa!' : '¡Inscripción exitosa!'), 'success', mensajeRespuestaServidorDiv);
                        if(formInscripcion) formInscripcion.reset();
                        isEditMode = false;
                        currentMatriculaForEdit = null;
                        if (inputMatricula) inputMatricula.disabled = false;

                        if(selectInstitucion) selectInstitucion.value = "";
                        if(selectNivelEducativo) selectNivelEducativo.value = "";
                        if(selectCarreraPrograma) selectCarreraPrograma.innerHTML = '<option selected disabled value="">Seleccione una carrera</option>';
                        if(inputRVOE) inputRVOE.value = "";
                        if(inputFechaRVOE) inputFechaRVOE.value = "";
                        if(inputDGP) inputDGP.value = "";
                        
                        const estatusAlumnoSelect = document.getElementById('estatus_alumno');
                        if(estatusAlumnoSelect) estatusAlumnoSelect.value = "Activo";
                        const modalidadSelect = document.getElementById('modalidad');
                        if(modalidadSelect) modalidadSelect.value = "";
                        const cicloInicioSelect = document.getElementById('ciclo_inicio');
                        if(cicloInicioSelect) cicloInicioSelect.value = "";
                        const cicloFinSelect = document.getElementById('ciclo_fin');
                        if(cicloFinSelect) cicloFinSelect.value = "";
                    } else {
                        mostrarMensajeEnFormulario(data.message || 'Ocurrió un error.', 'danger', mensajeRespuestaServidorDiv);
                    }
                })
                .catch(error => {
                    console.error('Error en fetch para enviar:', error);
                    mostrarMensajeEnFormulario(`Error de conexión o procesamiento: ${error.message}`, 'danger', mensajeRespuestaServidorDiv);
                })
                .finally(() => {
                    if(modalConfirmacion) modalConfirmacion.hide();
                    formDataForSubmission = null;
                });
            } else {
                mostrarMensajeEnFormulario('No hay datos para enviar. Revisa el formulario.', 'warning', mensajeRespuestaServidorDiv);
                if(modalConfirmacion) modalConfirmacion.hide();
            }
        });
    }
    
    function mostrarMensajeEnFormulario(mensaje, tipo, elementoContenedor) {
        if (elementoContenedor) {
            elementoContenedor.innerHTML = `<div class="alert alert-${tipo}" role="alert">${mensaje}</div>`;
            setTimeout(() => {
                elementoContenedor.innerHTML = '';
            }, 7000);
        }
    }
});