// Configuración base
const API_BASE = '../../backend/finanzas/api/';
const matricula = document.currentScript.getAttribute('data-matricula') || '';

// Función para formatear moneda
const formatCurrency = (value) => {
    value = parseFloat(value) || 0;
    return '$' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

// Función para mostrar notificaciones al usuario
const showNotification = (message, type = 'info') => {
    const colors = {
        success: 'notification-success',
        error: 'notification-error',
        warning: 'notification-warning',
        info: 'notification-info'
    };
    
    const notification = $(`<div class="w3-panel ${colors[type]} w3-display-topright w3-animate-zoom notification" style="position:fixed;top:20px;right:20px;width:300px;z-index:1000">
        <span onclick="this.parentElement.style.display='none'" class="w3-button w3-hover-none w3-display-topright" style="text-decoration:none">&times;</span>
        <p>${message}</p>
    </div>`);
    
    $('body').append(notification);
    setTimeout(() => notification.fadeOut(500, () => notification.remove()), 5000);
};

// Función para actualizar duración basada en nivel educativo
function actualizarDuracionPorNivel() {
    const nivel = $('#nivel_educativo').val();
    const duracionInput = $('#duracion_meses');
    const doctoradoOpciones = $('#opciones_doctorado');
    const selectDoctorado = $('#duracion_doctorado');

    switch(nivel) {
        case 'Licenciatura':
            duracionInput.val(36);
            doctoradoOpciones.hide();
            break;
        case 'Maestria':
            duracionInput.val(16);
            doctoradoOpciones.hide();
            break;
        case 'Especialidad':
            duracionInput.val(12);
            doctoradoOpciones.hide();
            break;
        case 'Doctorado':
            duracionInput.val('');
            doctoradoOpciones.show();
            break;
        default:
            duracionInput.val('');
            doctoradoOpciones.hide();
    }

    selectDoctorado.val('');
}

$('#nivel_educativo').on('change', actualizarDuracionPorNivel);
$('#duracion_doctorado').on('change', function() {
    const duracion = $(this).val();
    $('#duracion_meses').val(duracion);
});


// Función para mostrar campos dinámicos de descuento
function toggleDiscountFields() {
    document.getElementById("section_inscripcion").style.display = 
        document.getElementById("chkInscripcion").checked ? "block" : "none";

    document.getElementById("section_colegiatura").style.display = 
        document.getElementById("chkColegiatura").checked ? "block" : "none";

    document.getElementById("section_reinscripcion").style.display = 
        document.getElementById("chkReinscripcion").checked ? "block" : "none";
}


// Objeto principal de la aplicación
const PlanPagosApp = {
    dataTable: null,
    
    init: function() {
        this.loadAlumnoInfo();
        this.loadPagos();
        this.bindEvents();
    },

    loadAlumnoInfo: function() {
        if (!matricula) {
            showNotification('Error: Matrícula del alumno no proporcionada.', 'error');
            return;
        }

        $.ajax({
            url: API_BASE + 'obtener_info_alumno.php',
            method: 'GET',
            data: { matricula: matricula },
            dataType: 'json',
            success: (response) => {
                if (response.success && response.data) {
                    $('#matricula').val(response.data.matricula);
                    $('#nivel_educativo').val(response.data.nivel_educativo);
                    $('#nombre_programa').val(response.data.programa);

                    // Asigna fecha técnica
                    document.getElementById('fecha_inicio').value = response.data.fecha_inicio;

                    // Convierte a fecha legible
                    const fecha = new Date(response.data.fecha_inicio + 'T00:00:00'); // Añadimos hora para evitar errores de zona
                    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
                    const fechaFormateada = fecha.toLocaleDateString('es-MX', opciones);

                    // Muestra en campo visual
                    document.getElementById('fecha_inicio_legible').value = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);

                    if (response.data.nivel_educativo === 'Doctorado') {
                        $('#opciones_doctorado').show();
                        if (response.data.duracion_meses === 24 || response.data.duracion_meses === 36) {
                            $('#duracion_doctorado').val(response.data.duracion_meses);
                        }
                    } else {
                        $('#opciones_doctorado').hide();
                    }

                    $('#duracion_meses').val(response.data.duracion_meses);
                } else {
                    showNotification(`Error al cargar información del alumno: ${response.message || 'Datos no encontrados.'}`, 'error');
                }
            },
            error: (xhr, status, error) => {
                showNotification('Error al cargar información del alumno.', 'error');
                console.error('Error al cargar info del alumno:', status, error, xhr.responseText);
            }
        });
    },

    loadPagos: function() {
        if (this.dataTable) {
            this.dataTable.destroy();
            $('#tabla-pagos tbody').empty();
        }

        $.ajax({
            url: API_BASE + 'obtener_pagos.php',
            method: 'GET',
            data: { matricula: matricula },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    const pagos = response.data;
                    const tbody = $('#tabla-pagos tbody');
                    tbody.empty();
                    
                    if (pagos.length === 0) {
                        tbody.append('<tr><td colspan="7" class="w3-center">No hay pagos registrados para este alumno.</td></tr>');
                    } else {
                        pagos.forEach(pago => {
                            const fechaPagoDisplay = pago.fecha_pago ? pago.fecha_pago : 'N/A';
                            const montoPagadoDisplay = pago.monto_pagado ? formatCurrency(pago.monto_pagado) : 'N/A';
                            let estadoClase = '';
                            if (pago.estado_pago === 'Pagado') {
                                estadoClase = 'status-badge paid-badge';
                            } else if (pago.estado_pago === 'Vencido') {
                                estadoClase = 'status-badge overdue-badge';
                            } else {
                                estadoClase = 'status-badge pending-badge';
                            }

                            tbody.append(`
                                <tr>
                                    <td>${pago.concepto}</td>
                                    <td>${formatCurrency(pago.monto_regular)}</td>
                                    <td>${pago.fecha_vencimiento}</td>
                                    <td class="${estadoClase}">${pago.estado_pago}</td>
                                    <td>${fechaPagoDisplay}</td>
                                    <td>${montoPagadoDisplay}</td>
                                    <td>
                                        ${pago.estado_pago !== 'Pagado' ? 
                                        `<button class="w3-button w3-blue w3-round-large w3-small pay-btn" 
                                                 data-id="${pago.id_pago}" 
                                                 data-monto="${pago.monto_regular}"
                                                 data-concepto="${pago.concepto}"> 
                                            <i class="fas fa-cash-register"></i> Pagar
                                        </button>` : 'Pagado'}
                                    </td>
                                </tr>
                            `);
                        });
                    }
                    
                    this.dataTable = $('#tabla-pagos').DataTable({
                        "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                        },

                        "paging": true,
                        "lengthChange": false,
                        "searching": false,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        "destroy": true
                    });
                    this.bindPaymentButtons();
                } else {
                    showNotification(`Error al cargar pagos: ${response.message || 'Error desconocido del servidor.'}`, 'error');
                }
            },
            error: (xhr, status, error) => {
                let errorMsg = 'Error en la solicitud para cargar los pagos.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg = `Error de red o respuesta no JSON: ${status} - ${error}. Respuesta: ${xhr.responseText.substring(0, 200)}...`;
                }
                showNotification(errorMsg, 'error');
                console.error('Error al cargar pagos:', status, error, xhr.responseText);
            }
        });
        this.loadResumenFinanciero();
    },

    loadResumenFinanciero: function() {
        $.ajax({
            url: API_BASE + 'resumen_pagos.php',
            method: 'GET',
            data: { matricula: matricula },
            dataType: 'json',
            success: (response) => {
                if (response && !response.error) {
                    $('#total-pagar').text(formatCurrency(response.total_pagar));
                    $('#total-pagado').text(formatCurrency(response.total_pagado));
                    $('#saldo-pendiente').text(formatCurrency(response.saldo_pendiente));
                    
                    const porcentajePagado = (response.total_pagar > 0) ? 
                        ((response.total_pagado / response.total_pagar) * 100).toFixed(2) : 0;
                    $('.summary-card.paid .card-subtitle').text(`${porcentajePagado}% del total pagado`);
                } else {
                    console.error('Error en resumen de pagos:', response.error);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al cargar el resumen financiero:', status, error, xhr.responseText);
            }
        });
    },

    bindEvents: function() { 
        // Botón Generar Plan
        $('#btn-generar-plan').on('click', () => {
            document.getElementById('modalGenerarPlan').style.display = 'block';
            $('#fecha_inicio').val('');
            $('#descuento').val(0);
        });

        // Confirmar Generar Plan
        $('#btn-confirmar-generar').on('click', () => this.generarPlanPagos());

        // Botón Exportar PDF
        $('#btn-exportar-pdf').on('click', (event) => {
            event.preventDefault();
            const $btn = $(event.currentTarget);
            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');
            
            window.open(`${API_BASE}generar_pdf_plan_pagos.php?matricula=${matricula}`, '_blank');
            
            $btn.prop('disabled', false).html(originalText);
        });

        // Checkbox para descuentos dinámicos
        $('#chkInscripcion, #chkColegiatura, #chkReinscripcion').on('change', toggleDiscountFields);

        // Botón Certificado No Adeudo
        $('#btn-certificado-no-adeudo').on('click', (event) => this.generarCertificado(event));
        
    },

    bindPaymentButtons: function() {
        $('.pay-btn').off('click').on('click', function() {
            const idPago = $(this).data('id');
            const montoRegular = $(this).data('monto');
            const concepto = $(this).data('concepto');

            $('#pago-id').val(idPago);
            $('#pago-monto').val(montoRegular);
            $('#pago-concepto').val(concepto);
            $('#pago-monto-pagado').val(montoRegular);
            $('#pago-fecha').val(new Date().toISOString().slice(0,10));

            $('#modalPago').css('display', 'block');
        });

        $('#btn-guardar-pago').off('click').on('click', () => this.registrarPago());

        $('#modalPago .w3-button.w3-display-topright').off('click').on('click', function() {
            $('#modalPago').css('display', 'none');
            $('#formPago')[0].reset();
        });
        
        $('#modalPago .action-button.w3-grey').off('click').on('click', function() {
            $('#modalPago').css('display', 'none');
            $('#formPago')[0].reset();
        });
    },

    generarPlanPagos: function() {
        const btn = $('#btn-confirmar-generar');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        const datosPlan = {
            matricula: $('#matricula').val(),
            nivel_educativo: $('#nivel_educativo').val(),
            programa: $('#nombre_programa').val(),
            duracion_meses: $('#duracion_meses').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            ...(document.getElementById('chkInscripcion')?.checked && {
                descuento_inscripcion: $('#descuento_inscripcion').val()
            }),
            ...(document.getElementById('chkColegiatura')?.checked && {
                descuento_colegiatura: $('#descuento_colegiatura').val()
            }),
            ...(document.getElementById('chkReinscripcion')?.checked && {
                descuento_reinscripcion: $('#descuento_reinscripcion').val()
            })
        };


        // Validaciones
        if (!datosPlan.nivel_educativo || datosPlan.nivel_educativo === '') {
            showNotification('El nivel educativo es obligatorio.', 'warning');
            btn.prop('disabled', false).html(originalText);
            return;
        }

        $.ajax({
            url: API_BASE + 'generar_plan.php',
            method: 'POST',
            contentType: 'application/json',    // ⇦ *nuevo*
            data: JSON.stringify(datosPlan),    // ⇦ *nuevo*
            processData: false,                 // ⇦ *nuevo*
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    showNotification('Plan de pagos generado exitosamente.', 'success');
                    $('#modalGenerarPlan').hide();
                    this.loadPagos();
                    this.loadResumenFinanciero();
                } else {
                    showNotification(`Error al generar plan: ${response.message}`, 'error');
                }
            },
            error: (xhr) => {
                const mensaje = xhr.responseJSON?.message ||
                                'Error al comunicarse con el servidor.';
                showNotification(mensaje, 'error');
            },
            complete: () => {
                btn.prop('disabled', false).html(originalText);
            }
        });
    },

    registrarPago: function() {
        const btn = $('#btn-guardar-pago');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');

        const datosPago = {
            id_pago: $('#pago-id').val(),
            monto_pagado: $('#pago-monto-pagado').val(),
            fecha_pago: $('#pago-fecha').val(),
            metodo_pago: $('#pago-metodo').val(),
            referencia_pago: $('#pago-referencia').val()
        };

        // Validaciones
        if (!datosPago.monto_pagado || isNaN(datosPago.monto_pagado) || parseFloat(datosPago.monto_pagado) <= 0) {
            showNotification('Ingresa un monto pagado válido y mayor que cero.', 'warning');
            btn.prop('disabled', false).html(originalText);
            return;
        }
        if (!datosPago.fecha_pago) {
            showNotification('La fecha de pago es obligatoria.', 'warning');
            btn.prop('disabled', false).html(originalText);
            return;
        }
        if (!datosPago.metodo_pago || datosPago.metodo_pago === '') {
            showNotification('El método de pago es obligatorio.', 'warning');
            btn.prop('disabled', false).html(originalText);
            return;
        }

        $.ajax({
            url: API_BASE + 'registrar_pago.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(datosPago),
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    showNotification('Pago registrado exitosamente.', 'success');
                    document.getElementById('modalPago').style.display = 'none';
                    $('#formPago')[0].reset();
                    this.loadPagos();
                    this.loadResumenFinanciero();
                } else {
                    showNotification(`Error al registrar pago: ${response.message}`, 'error');
                }
            },
            error: (xhr) => {
                let errorMsg = 'Error al comunicarse con el servidor para registrar el pago.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    console.error('Error detallado (registrar_pago):', xhr.responseText);
                }
                showNotification(errorMsg, 'error');
            },
            complete: () => {
                btn.prop('disabled', false).html(originalText);
            }
        });
    },

    generarCertificado: function(event) {
        event.preventDefault();
        const $btn = $(event.currentTarget);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');

        $.ajax({
            url: API_BASE + 'verificar_adeudos.php',
            method: 'GET',
            data: { matricula: matricula },
            dataType: 'json',
            success: (response) => {
                if (response.success && response.tiene_adeudos !== undefined) {
                    if (response.tiene_adeudos) {
                        showNotification('El alumno tiene pagos pendientes. No se puede generar el certificado.', 'warning');
                    } else {
                        showNotification('Generando certificado de no adeudo...', 'success');
                        window.open(`${API_BASE}generar_certificado_no_adeudo.php?matricula=${matricula}`, '_blank');
                    }
                } else if (response.message) {
                     showNotification(response.message, 'error');
                } else {
                    showNotification('Respuesta inesperada al verificar adeudos.', 'error');
                }
            },
            error: (xhr) => {
                let errorMsg = 'Error al verificar adeudos';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    console.error('Error detallado (verificar_adeudos):', xhr.responseText);
                }
                showNotification(errorMsg, 'error');
            },
            complete: () => {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
};

// Inicializar la aplicación cuando el DOM esté listo
$(document).ready(() => {
    PlanPagosApp.init();
});

$(document).ready(function() {
    // Al dar clic en el botón Nota, muestra el modal y carga la nota actual
    $('#btn-nota').on('click', function() {
        $.get('../../backend/finanzas/api/obtener_nota_pago.php', { matricula: $('#matricula').val() }, function(resp) {
            $('#nota-texto').val(resp.nota || '');
            $('#modalNota').show();
        }, 'json').fail(function() {
            $('#nota-texto').val('');
            $('#modalNota').show();
        });
    });

    // Guardar nota al dar clic en guardar
    $('#btn-guardar-nota').on('click', function() {
        var texto = $('#nota-texto').val();
        $.ajax({
            url: '../../backend/finanzas/api/guardar_nota_pago.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ matricula: $('#matricula').val(), nota: texto }),
            dataType: 'json',
            success: function(resp) {
                console.log(resp);
                if(resp.success) {
                    $('#modalNota').hide();
                    $('#nota-texto-mostrada').text(texto);
                    $('#nota-visual').show();
                } else {
                    alert('Error al guardar la nota');
                }
            }
        });
    });

    // Mostrar la nota guardada al cargar la página
    $.get('../../backend/finanzas/api/obtener_nota_pago.php', { matricula: $('#matricula').val() }, function(resp) {
        if(resp.nota) {
            $('#nota-texto-mostrada').text(resp.nota);
            $('#nota-visual').show();
        } else {
            $('#nota-visual').hide();
        }
    }, 'json');
});
