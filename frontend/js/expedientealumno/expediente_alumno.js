  document.addEventListener('DOMContentLoaded', function() {
            const modalNuevaObservacion = new bootstrap.Modal(document.getElementById('modalNuevaObservacion'));
        const btnGuardarObservacion = document.querySelector('#modalNuevaObservacion .btn-primary'); // <-- ASEGÚRATE DE QUE ESTA LÍNEA EXISTA
        const observacionTextoInput = document.getElementById('observacionTexto');
        const observacionTipoSelect = document.getElementById('observacionTipo');
        let matriculaActual = null;
            // --- Helper para escapar HTML ---
            function escapeHTML(str) {
                if (str === null || str === undefined) return '';
                return String(str).replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[match]));
            }

            // --- Función principal para cargar el expediente ---
            async function cargarExpediente(matricula) {
                matriculaActual = matricula; // Guardar la matrícula para usarla en otras funciones
                if (!matricula) {
                    document.body.innerHTML = '<div class="alert alert-danger">No se especificó la matrícula del alumno.</div>';
                    return;
                }
                
                try {
                    //const response = await fetch(`backend/php/obtener_expediente.php?matricula=${matricula}`);
                     const response = await fetch(`/control-escolar/backend/expedientealumno/api/obtener_expediente.php?matricula=${matricula}`);

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
                    document.querySelector('.expediente-container').innerHTML = `<div class="alert alert-danger m-3">Error: ${error.message}</div>`;
                }
            }

            // --- Funciones para poblar cada sección ---
            function poblarExpediente(expediente) {
                poblarHeader(expediente.info_general);
                poblarInfoGeneral(expediente.info_general);
                poblarFinanzas(expediente.situacion_financiera);
                poblarComprobantes(expediente.comprobantes_fiscales);
                poblarCalificaciones(expediente.calificaciones);
                poblarObservaciones(expediente.observaciones);
            }

            function poblarHeader(info) {
                const nombreCompleto = `${info.nombre || ''} ${info.apellido_paterno || ''} ${info.apellido_materno || ''}`.trim();
                document.getElementById('alumnoNombre').textContent = nombreCompleto;
                document.title = `Expediente del Alumno - ${nombreCompleto}`; // Actualizar título de la página

                const carreraInfo = `${info.nombre_programa || 'N/D'} (${info.ciclo_inicio || 'N/D'} - ${info.ciclo_fin || 'N/D'})`;
                document.getElementById('alumnoMatriculaCarrera').textContent = `Matrícula: ${info.matricula} | ${carreraInfo}`;
                
                const contactoInfo = `Email: ${info.correo_institucional || info.correo_personal || 'N/D'} | Cel: ${info.telefono_personal || 'N/D'}`;
                document.getElementById('alumnoEmailCelular').textContent = contactoInfo;
            }

            function poblarInfoGeneral(info) {
                document.getElementById('matricula').textContent = escapeHTML(info.matricula);
                document.getElementById('nombreCompleto').textContent = `${info.nombre || ''} ${info.apellido_paterno || ''} ${info.apellido_materno || ''}`.trim();
                const estatusSpan = document.getElementById('estatusAlumno');
                estatusSpan.textContent = escapeHTML(info.estatus_alumno);
                // Cambiar color del badge según estatus
                estatusSpan.className = 'badge'; // Reset class
                switch (info.estatus_alumno) {
                    case 'Activo': estatusSpan.classList.add('bg-success'); break;
                    case 'Baja': case 'Suspendido': estatusSpan.classList.add('bg-danger'); break;
                    case 'Irregular': estatusSpan.classList.add('bg-warning'); break;
                    case 'Egresado': estatusSpan.classList.add('bg-secondary'); break;
                    default: estatusSpan.classList.add('bg-light', 'text-dark');
                }
                
                
                document.getElementById('curp').textContent = escapeHTML(info.curp);
                document.getElementById('fechaNacimiento').textContent = escapeHTML(info.fecha_nacimiento);
                document.getElementById('genero').textContent = (info.genero === 'M') ? 'Masculino' : ((info.genero === 'F') ? 'Femenino' : 'Otro');
                document.getElementById('lugarNacimiento').textContent = escapeHTML(info.lugar_nacimiento);
                document.getElementById('nacionalidad').textContent = escapeHTML(info.nacionalidad);
                document.getElementById('institucion').textContent = escapeHTML(info.nombre_institucion);
                document.getElementById('programa').textContent = escapeHTML(info.nombre_programa);
                document.getElementById('nivelAcademico').textContent = escapeHTML(info.nivel_educativo);
                document.getElementById('generacion').textContent = `${info.ciclo_inicio || 'N/D'} - ${info.ciclo_fin || 'N/D'}`;
                document.getElementById('modalidad').textContent = escapeHTML(info.modalidad);
                document.getElementById('fechaInscripcion').textContent = escapeHTML(info.fecha_inscripcion);
                document.getElementById('telefonoPersonal').textContent = escapeHTML(info.telefono_personal);
                document.getElementById('correoPersonal').textContent = escapeHTML(info.correo_personal);
                document.getElementById('correoInstitucional').textContent = escapeHTML(info.correo_institucional);

                // Asignar link al botón de editar
                const btnEditar = document.querySelector('#info-general .btn-outline-primary');
                if(btnEditar) {
                    btnEditar.onclick = () => {
                        window.location.href = `../control_escolar/inscripcion.php`;
                    };
                }
                const btnExpCompleto = document.getElementById('btnExpComp');
                console.log("entre a poblarinfogeneral");
                if(btnExpCompleto) {
                    console.log("Entre a la condicion");
                        // En lugar de añadir un listener, simplemente convertimos el botón en un enlace
                        // o le asignamos la acción onclick. Usar onclick es más directo aquí.
                        btnExpCompleto.onclick = () => {
                            console.log("entre al boton");
                            // Redirigir a la página del expediente completo con la matrícula correcta
                         window.location.href = `expedientecompleto.php?matricula=${info.matricula}`;
                        };
                        // También puedes eliminar el botón si esta página YA ES el expediente completo,
                        // para evitar que el usuario se redirija a sí mismo.
                        // btnExpCompleto.remove(); // Descomenta si esta página reemplaza a la otra
                    }
            }

                function poblarFinanzas(data) {
        if (!data) return;

        const tablaPendientesBody = document.querySelector('#situacion-financiera #pagosPendientesBody'); // Añade id="pagosPendientesBody" a tu <tbody>
        const tablaRealizadosBody = document.querySelector('#situacion-financiera #pagosRealizadosBody'); // Añade id="pagosRealizadosBody" a tu <tbody>
        const totalPagadoSpan = document.getElementById('totalPagado'); // Añade id="totalPagado" a tu <span>

        if (tablaPendientesBody) {
            tablaPendientesBody.innerHTML = ''; // Limpiar
            if (data.pagos_pendientes && data.pagos_pendientes.length > 0) {
                data.pagos_pendientes.forEach(pago => {
                    const tr = document.createElement('tr');
                    const dias_vencido = parseInt(pago.dias_vencido);
                    let estatusHTML = '';
                    let filaClass = '';

                    if (pago.status === 'Vencido' || dias_vencido > 0) {
                        filaClass = 'table-danger';
                        estatusHTML = `<span class="badge bg-danger-subtle text-danger-emphasis rounded-pill status-badge"><i class="fas fa-exclamation-circle me-1"></i>Vencido</span>`;
                    } else if (pago.status === 'Abonado') { // Asumiendo que este estado puede existir
                        filaClass = 'table-info';
                        estatusHTML = `<span class="badge bg-info-subtle text-info-emphasis rounded-pill status-badge"><i class="fas fa-adjust me-1"></i>Abonado</span>`;
                    } else { // Pendiente, pero no vencido
                        estatusHTML = `<span class="badge bg-warning-subtle text-warning-emphasis rounded-pill status-badge"><i class="fas fa-clock me-1"></i>Pendiente</span>`;
                    }
                    
                    tr.className = filaClass;

                    tr.innerHTML = `
                        <td>${escapeHTML(pago.id_pago)}</td>
                        <td>${escapeHTML(pago.concepto)}</td>
                        <td>${escapeHTML(pago.fecha_vencimiento)}</td>
                        <td class="text-center ${dias_vencido > 0 ? 'dias-vencido-alerta' : ''}">${dias_vencido > 0 ? dias_vencido : 0}</td>
                        <td class="text-end">$${parseFloat(pago.saldo).toFixed(2)}</td>
                        <td class="text-center">${estatusHTML}</td>
                        <td class="text-center">
                            <a href="../../backend/finanzas/plan_pagos_alumno.php?matricula=${matriculaActual}" class="btn btn-sm btn-primary" title="Realizar Pago">
                                <i class="fas fa-dollar-sign me-1"></i> Ir a Pagar
                            </a>
                        </td>
                    `;
                    tablaPendientesBody.appendChild(tr);
                });
            } else {
                tablaPendientesBody.innerHTML = `<tr><td colspan="7" class="text-center">No hay pagos pendientes.</td></tr>`;
            }
        }

        if (tablaRealizadosBody) {
            tablaRealizadosBody.innerHTML = ''; // Limpiar
            let totalPagado = 0;
            if (data.pagos_realizados && data.pagos_realizados.length > 0) {
                data.pagos_realizados.forEach(pago => {
                    totalPagado += parseFloat(pago.monto);
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${escapeHTML(pago.id_pago)}</td>
                        <td>${escapeHTML(pago.concepto)}</td>
                        <td>${escapeHTML(pago.fecha_pago)}</td>
                        <td>$${parseFloat(pago.monto).toFixed(2)}</td>
                        <td class="status-pagado">Pagado</td>
                    `;
                    tablaRealizadosBody.appendChild(tr);
                });
            } else {
                tablaRealizadosBody.innerHTML = `<tr><td colspan="5" class="text-center">No se han realizado pagos.</td></tr>`;
            }

            if(totalPagadoSpan) {
                totalPagadoSpan.textContent = `$${totalPagado.toFixed(2)}`;
            }
        }
    }
            function poblarComprobantes(data) {
                // Poblaría la tabla de comprobantes
                console.log("Datos de comprobantes (simulados):", data);
            }
       function poblarCalificaciones(data) {
    const tablaCalificacionesBody = document.querySelector('#calificaciones tbody');
    if (!tablaCalificacionesBody) return;

    tablaCalificacionesBody.innerHTML = ''; // Limpiar
    if (data && data.length > 0) {
        // Actualizar los encabezados de la tabla en el HTML si es necesario
        // Ejemplo: <th>Materia</th> <th>Ciclo</th> <th>Docente</th> <th>Calificación</th>
        data.forEach(materia => {
            const tr = document.createElement('tr');
            const calificacion = parseFloat(materia.calificacion);
            tr.innerHTML = `
                <td>${escapeHTML(materia.nombre_materia)}</td>
                <td>${escapeHTML(materia.ciclo)}</td>
                <td>${escapeHTML(materia.nombre_docente || 'No asignado')}</td>
                <td class="text-center ${calificacion < 70 ? 'fw-bold text-danger' : ''}">${calificacion.toFixed(2)}</td>
            `;
            tablaCalificacionesBody.appendChild(tr);
        });
    } else {
        tablaCalificacionesBody.innerHTML = `<tr><td colspan="4" class="text-center">No hay calificaciones registradas.</td></tr>`;
    }
}
                // --- ESTA FUNCIÓN SE ACTUALIZA PARA SER DINÁMICA ---
    function poblarObservaciones(observaciones) {
        const listaObservacionesDiv = document.getElementById('listaObservaciones');
        if (!listaObservacionesDiv) return;

        listaObservacionesDiv.innerHTML = ''; // Limpiar contenido estático

        if (observaciones && observaciones.length > 0) {
            observaciones.forEach(obs => {
                const card = document.createElement('div');
                card.className = 'card mb-2';
                
                // Formatear la fecha
                const fecha = new Date(obs.fecha_creacion);
                const fechaFormateada = fecha.toLocaleString('es-MX', { dateStyle: 'long', timeStyle: 'short' });

                card.innerHTML = `
                    <div class="card-body">
                        <p class="card-text">${escapeHTML(obs.observacion)}</p>
                        <footer class="blockquote-footer">
                            Registrado por: <cite title="Autor">${escapeHTML(obs.autor || 'Sistema')}</cite> 
                            - ${fechaFormateada} 
                            <span class="badge bg-info text-dark ms-2">${escapeHTML(obs.tipo)}</span>
                        </footer>
                    </div>
                `;
                listaObservacionesDiv.appendChild(card);
            });
        } else {
            listaObservacionesDiv.innerHTML = '<div class="alert alert-light text-center">No hay observaciones registradas para este alumno.</div>';
        }
    }

    // --- Lógica de Eventos para el Modal de Observaciones ---
    if (btnGuardarObservacion) {
        btnGuardarObservacion.addEventListener('click', async function() {
            const texto = observacionTextoInput.value.trim();
            const tipo = observacionTipoSelect.value;
            
            if (!texto) {
                alert("La observación no puede estar vacía.");
                return;
            }
            if (!matriculaActual) {
                alert("Error: No se ha identificado la matrícula del alumno.");
                return;
            }

            this.disabled = true; // Deshabilitar botón para prevenir doble envío
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

            const formData = new FormData();
            formData.append('matricula', matriculaActual);
            formData.append('observacionTexto', texto);
            formData.append('tipoObservacion', tipo);

            try {
                const response = await fetch('/control-escolar/backend/expedientealumno/api/guardar_observacion.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    modalNuevaObservacion.hide(); // Ocultar modal
                    observacionTextoInput.value = ''; // Limpiar el textarea
                    cargarExpediente(matriculaActual); // Recargar todo el expediente para ver la nueva observación
                    // Opcional: mostrar un mensaje de éxito
                } else {
                    throw new Error(result.message || "Error desconocido al guardar.");
                }

            } catch (error) {
                console.error("Error al guardar observación:", error);
                alert(`No se pudo guardar la observación: ${error.message}`);
            } finally {
                this.disabled = false; // Rehabilitar botón
                this.innerHTML = 'Guardar Observación';
            }
        });
    }


            // --- Lógica de Inicio ---
            // Obtener matrícula de la URL (ej. expediente_alumno.html?matricula=A000123)
            const urlParams = new URLSearchParams(window.location.search);
            const matriculaFromURL = urlParams.get('matricula');

            cargarExpediente(matriculaFromURL);
            
            // Script para activar la primera pestaña o la que esté en el hash de la URL
            var firstTabEl = document.querySelector('#expedienteTabs li:first-child button');
            if(firstTabEl) new bootstrap.Tab(firstTabEl);
        });