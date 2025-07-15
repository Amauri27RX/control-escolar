    document.addEventListener('DOMContentLoaded', function() {
        // --- Función para poner datos en un campo ---
        function setData(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || 'N/A'; // Muestra 'N/A' si el dato es nulo o vacío
            } else {
                console.warn(`Elemento no encontrado para el id: ${id}`);
            }
        }

        // --- Función para manejar el cálculo y muestra de la edad ---
        function setEdad(fechaNacimiento) {
            if (fechaNacimiento) {
                try {
                    const birthDate = new Date(fechaNacimiento);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    setData('edad', age >= 0 ? `${age} años` : 'N/A');
                } catch (e) {
                    setData('edad', 'Error');
                }
            } else {
                setData('edad', 'N/A');
            }
        }

        // --- Función principal para cargar el expediente ---
        async function cargarExpediente(matricula) {
            if (!matricula) {
                document.body.innerHTML = `<div class="alert alert-danger m-3">No se especificó la matrícula del alumno en la URL. Ejemplo: expedientecompleto.html?matricula=SU_MATRICULA</div>`;
                return;
            }
            
            try {
                // Asegúrate que esta ruta sea correcta para tu proyecto
                const response = await fetch(`/control-escolar/backend/expedientealumno/api/obtener_expediente_completo.php?matricula=${matricula}`);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    throw new Error(errorData?.message || `Error del servidor: ${response.status}`);
                }
                const result = await response.json();

                if (result.success && result.expediente) {
                    poblarExpediente(result.expediente);
                } else {
                    throw new Error(result.message || "No se pudo cargar el expediente.");
                }
            } catch (error) {
                console.error("Error al cargar expediente:", error);
                document.querySelector('.accordion').innerHTML = `<div class="alert alert-danger m-3"><strong>Error:</strong> ${error.message}</div>`;
            }
        }

        // --- Función para poblar todos los campos del expediente ---
        function poblarExpediente(data) {
            const nombreCompleto = `${data.nombre || ''} ${data.apellido_paterno || ''} ${data.apellido_materno || ''}`.trim();

            // Header
            setData('nombreAlumnoHeader', `${nombreCompleto} - Matrícula: ${data.matricula}`);
            document.title = `Expediente Completo - ${nombreCompleto}`;
            
            // Sección 1: Datos Personales
            setData('matricula', data.matricula);
            setData('nombre', data.nombre);
            setData('apellido_paterno', data.apellido_paterno);
            setData('apellido_materno', data.apellido_materno);
            setData('genero', data.genero === 'M' ? 'Masculino' : (data.genero === 'F' ? 'Femenino' : 'Otro'));
            setData('fecha_nacimiento', data.fecha_nacimiento);
            setEdad(data.fecha_nacimiento); // Calcular y mostrar edad
            setData('pais_nacimiento', data.pais_nacimiento);
            setData('ciudad_nacimiento', data.estado_ciudad_nacimiento);
            setData('nacionalidad', data.nacionalidad);
            setData('curp', data.curp);
            setData('cura', data.cura);

            // Sección 2: Domicilio y Contacto
            setData('pais_residencia', data.pais_residencia);
            setData('ciudad_residencia', data.ciudad_residencia);
            setData('colonia_residencia', data.colonia_residencia);
            setData('calle_residencia', data.calle_residencia);
            setData('num_ext_residencia', data.num_ext_residencia);
            setData('num_int_residencia', data.num_int_residencia);
            setData('telefono_personal', data.telefono_personal);
            setData('correo_personal', data.correo_personal);

            // Sección 3: Programa al que se Inscribe
            setData('institucion_programa', data.nombre_institucion);
            setData('nivel_educativo_programa', data.nivel_academico);
            setData('carrera_programa', data.nombre_programa);
            setData('modalidad_programa', data.modalidad);
            setData('rvoe_programa', data.rvoe);
            setData('fecha_rvoe_programa', data.fecha_rvoe);
            setData('dgp_programa', data.dgp);
            setData('permanencia_programa', data.permanencia);
            setData('fecha_ingreso_programa', data.fecha_inicio);
            setData('ciclo_escolar_programa', `${data.ciclo_inicio || ''} - ${data.ciclo_fin || ''}`);
            setData('titulacion_programa', data.modalidad_titulacion);
            setData('promocion_programa', data.promocion_aplicada);
            setData('estatus_alumno_programa', data.estatus_alumno);

            // Sección 4: Antecedentes Académicos
            setData('nivel_anterior', data.nivel_educativo_anterior);
            setData('institucion_anterior', data.nombre_institucion_anterior);
            setData('ciudad_anterior', data.ciudad_institucion_anterior);
            setData('fecha_inicio_anterior', data.fecha_inicio_anterior);
            setData('fecha_fin_anterior', data.fecha_fin_anterior);

            // Sección 5: Lugar de Trabajo
            setData('nombre_empresa', data.nombre_empresa);
            setData('puesto_trabajo', data.puesto);
            setData('area_trabajo', data.area_departamento);
            setData('pais_trabajo', data.pais_trabajo);
            setData('ciudad_trabajo', data.ciudad_trabajo);
            setData('colonia_trabajo', data.colonia_trabajo);
            setData('calle_trabajo', data.calle_trabajo);
            setData('num_ext_trabajo', data.num_ext_trabajo);
            setData('num_int_trabajo', data.num_int_trabajo);
            setData('telefono_trabajo', data.telefono_trabajo);
            setData('correo_trabajo', data.correo_corporativo);
        }

        // --- Lógica de Inicio ---
        const urlParams = new URLSearchParams(window.location.search);
        const matriculaFromURL = urlParams.get('matricula');
        cargarExpediente(matriculaFromURL);
    });