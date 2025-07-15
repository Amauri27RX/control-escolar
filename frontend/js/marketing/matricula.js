window.BASE_URL = window.BASE_URL || '/control-escolar';


document.addEventListener('DOMContentLoaded', function() {  
    const primerNombreInput = document.getElementById('nombre');
    const apellidoPaternoInput = document.getElementById('apellido_paterno');
    const apellidoMaternoInput = document.getElementById('apellido_materno');
    const matriculaInput = document.getElementById('matricula');

    function generarPrefijoMatricula() {
        const nombre = primerNombreInput.value.trim();
        const apellidoP = apellidoPaternoInput.value.trim();
        const apellidoM = apellidoMaternoInput.value.trim();

        if (nombre.length > 0 && (apellidoP.length > 0 || apellidoM.length > 0)) {
            const letraNombre = nombre.charAt(0).toUpperCase();
            let letraApellido = '';
            if (apellidoP.length > 0) {
                letraApellido = apellidoP.charAt(0).toUpperCase();
            } else if (apellidoM.length > 0) {
                letraApellido = apellidoM.charAt(0).toUpperCase();
            }
            return letraNombre + letraApellido;
        }
        return '';
    }

    async function obtenerMatriculaCompleta(prefijo) {
        try {
            const response = await fetch(`${BASE_URL}/backend/marketing/api/generar_matricula.php?prefijo=${encodeURIComponent(prefijo)}`);
            if (!response.ok) throw new Error('Error al generar matrícula');
            const data = await response.json();
            console.log('Respuesta del servidor:', data);
            matriculaInput.value = data.matricula || (prefijo + '0001');
        } catch (error) {
            console.error('Error:', error);
            matriculaInput.value = prefijo + '0001'; // fallback
        }
    }

    function actualizarMatriculaEnVivo() {
        const prefijo = generarPrefijoMatricula();
        console.log('Prefijo generado:', prefijo);
        if (prefijo) {
            matriculaInput.value = prefijo + '0001'; // valor provisional mientras llega respuesta
            console.log('Solicitando matrícula para prefijo:', prefijo);
            obtenerMatriculaCompleta(prefijo);
        } else {
            matriculaInput.value = '';
        }
    }

    // Event listeners
    primerNombreInput.addEventListener('input', actualizarMatriculaEnVivo);
    apellidoPaternoInput.addEventListener('input', actualizarMatriculaEnVivo);
    apellidoMaternoInput.addEventListener('input', actualizarMatriculaEnVivo);

    // Ejecutar al cargar página para llenar matrícula si hay datos previos
    actualizarMatriculaEnVivo();
});
