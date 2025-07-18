document.addEventListener('DOMContentLoaded', function() {
    // --- Elementos del DOM ---
    const filtroCiclo = document.getElementById('filtroCiclo');
    const filtroInstitucion = document.getElementById('filtroInstitucion');
    const filtroPrograma = document.getElementById('filtroPrograma');
    const tablaOfertaBody = document.getElementById('tablaOfertaBody');
    const mensajeOferta = document.getElementById('mensajeOferta');
    let listaDocentes = [];

    // --- Funciones ---
    function mostrarMensaje(texto, tipo = 'info') {
        mensajeOferta.innerHTML = `<div class="alert alert-${tipo}" role="alert">${texto}</div>`;
        setTimeout(() => { mensajeOferta.innerHTML = '' }, 4000);
    }

    async function cargarDatosIniciales() {
        try {
            const response = await fetch('/control-escolar/backend/control_escolar/api/api_oferta_academica2.php?action=get_initial_data');
            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            // Poblar select de ciclos e instituciones
            filtroCiclo.add(new Option("Seleccione un Ciclo...", ""));
            result.data.ciclos.forEach(ciclo => filtroCiclo.add(new Option(ciclo.codigo, ciclo.id_ciclo)));
            
            filtroInstitucion.add(new Option("Seleccione una Institución...", ""));
            result.data.instituciones.forEach(inst => filtroInstitucion.add(new Option(inst.nombre, inst.id_institucion)));

            listaDocentes = result.data.docentes;
            
            // Cargar programas iniciales (todos)
            cargarProgramas();

        } catch (error) {
            mostrarMensaje(`Error al cargar datos iniciales: ${error.message}`, 'danger');
        }
    }


        async function cargarProgramas() {
        const idInstitucion = filtroInstitucion.value;
        
        filtroPrograma.innerHTML = '<option value="">Cargando...</option>';
        filtroPrograma.disabled = true;

        try {
            const response = await fetch(`/control-escolar/backend/control_escolar/api/api_oferta_academica2.php?action=get_programas_por_institucion&id_institucion=${idInstitucion}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            filtroPrograma.innerHTML = '<option value="">Seleccione un Programa...</option>';
            result.programas.forEach(prog => filtroPrograma.add(new Option(prog.nombre_programa, prog.dgp)));
            
        } catch (error) {
            mostrarMensaje(`Error al cargar programas: ${error.message}`, 'danger');
        } finally {
            filtroPrograma.disabled = false;
            cargarOferta(); // Llama a cargar la tabla después de actualizar los programas
        }
    }

    async function cargarOferta() {
        const id_ciclo = filtroCiclo.value;
        const dgp = filtroPrograma.value;

        if (!id_ciclo || !dgp) {
            tablaOfertaBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-5">Seleccione un ciclo y un programa para continuar.</td></tr>';
            return;
        }

        tablaOfertaBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-5">Cargando...</td></tr>';
        
        try {
            const response = await fetch(`/control-escolar/backend/control_escolar/api/api_oferta_academica2.php?action=get_oferta&id_ciclo=${id_ciclo}&dgp=${dgp}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            renderTablaOferta(result.oferta);
        } catch (error) {
            mostrarMensaje(`Error al cargar la oferta: ${error.message}`, 'danger');
        }
    }

    function renderTablaOferta(ofertaData) {
        tablaOfertaBody.innerHTML = '';
        if (ofertaData.length === 0) {
            tablaOfertaBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-5">Este programa no tiene materias en su catálogo.</td></tr>';
            return;
        }

        ofertaData.forEach(materia => {
            const tr = document.createElement('tr');
            let docentesOptions = '<option value="">-- Sin Asignar --</option>';
            listaDocentes.forEach(docente => {
                const isSelected = materia.id_docente == docente.id_docente ? 'selected' : '';
                docentesOptions += `<option value="${docente.id_docente}" ${isSelected}>${docente.nombre_completo}</option>`;
            });

            tr.dataset.claveMateria = materia.clave_materia;
            tr.innerHTML = `
                <td>
                    <strong>${materia.nombre}</strong>
                    <small class="d-block text-muted">${materia.clave_materia}</small>
                </td>
                <td><select class="form-select select-docente">${docentesOptions}</select></td>
                <td><input type="number" class="form-control input-cupo" value="${materia.cupo || 30}"></td>
                <td><input type="text" class="form-control input-aula" value="${materia.aula || ''}"></td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm btn-save-oferta" title="Guardar cambios de esta fila">
                        <i class="fas fa-save"></i>
                    </button>
                </td>
            `;
            tablaOfertaBody.appendChild(tr);
        });
    }

    async function handleGuardarOferta(e) {
        const btn = e.target.closest('.btn-save-oferta');
        if (!btn) return;

        const fila = btn.closest('tr');
        const data = {
            id_ciclo: filtroCiclo.value,
            dgp: filtroPrograma.value,
            clave_materia: fila.dataset.claveMateria,
            id_docente: fila.querySelector('.select-docente').value,
            cupo: fila.querySelector('.input-cupo').value,
            aula: fila.querySelector('.input-aula').value,
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const response = await fetch('/control-escolar/backend/control_escolar/api/api_oferta_academica2.php?action=save_oferta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            
            mostrarMensaje(result.message, 'success');
        } catch (error) {
            mostrarMensaje(`Error al guardar: ${error.message}`, 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i>';
        }
    }

    // --- Event Listeners ---
    filtroCiclo.addEventListener('change', cargarOferta);
    filtroInstitucion.addEventListener('change', cargarProgramas);
    filtroPrograma.addEventListener('change', cargarOferta);
    tablaOfertaBody.addEventListener('click', handleGuardarOferta);

    // --- Carga Inicial ---
    cargarDatosIniciales();
});