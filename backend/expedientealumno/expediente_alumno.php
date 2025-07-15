<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente del Alumno - [Nombre del Alumno]</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5; /* Un gris claro similar al de las imágenes */
        }
        .expediente-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1), 0 8px 16px rgba(0,0,0,.1);
            margin-top: 20px;
            padding: 0; /* Eliminamos padding para que el card-header ocupe todo el ancho */
        }
        .expediente-header {
            background-color: #f7f7f7; /* Un fondo ligeramente distinto para el encabezado del expediente */
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
        }
        .expediente-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
            border: 3px solid #dee2e6;
        }
        .expediente-header h2 {
            margin-bottom: 5px;
            font-size: 1.75rem;
            color: #333;
        }
        .expediente-header p {
            margin-bottom: 0;
            color: #555;
        }
        .nav-tabs .nav-link {
            color: #007bff; /* Azul para los links de las pestañas */
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .tab-content {
            padding: 20px;
        }
        .section-title {
            font-size: 1.25rem;
            color: #007bff; /* Azul para los títulos de sección */
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #007bff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }
        .info-item {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        .info-item strong {
            display: block;
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 3px;
        }
        .table-actions button, .table-actions a {
            margin-right: 5px;
        }
        .table-header-custom {
            background-color: #007bff; /* Azul similar al de las imágenes */
            color: white;
        }
        .table-header-custom th {
            font-weight: 500;
        }
        .status-abonado {
            color: green;
            font-weight: bold;
        }
        .status-sinabono, .status-vencido {
            color: red;
            font-weight: bold;
        }
        .status-timbrado {
            color: darkblue;
            font-weight: bold;
        }
        .status-pagado {
             color: green;
            font-weight: bold;
        }
        .dias-vencido-alerta {
            color: red;
            font-weight: bold;
        }
        .dias-vencido-alerta .fa-triangle-exclamation {
            margin-left: 4px;
        }
                /* Estilos para lista de documentos */
        .document-actions .btn {
            margin-left: 5px;
        }
    </style>
</head>
<body>

    <div class="container expediente-container">
        
        <div class="expediente-header">
            <img src="perfil.png" alt="Foto del Alumno"> <div>
                <h2 id="alumnoNombre">Cargando...</h2>
                <p id="alumnoMatriculaCarrera">Cargando...</p>
                <p id="alumnoEmailCelular">Cargando...</p>
            </div>
        </div>

        <ul class="nav nav-tabs nav-fill px-3 pt-2" id="expedienteTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-general-tab" data-bs-toggle="tab" data-bs-target="#info-general" type="button" role="tab" aria-controls="info-general" aria-selected="true"><i class="fas fa-user-circle me-2"></i>Información General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" aria-controls="documentos" aria-selected="false"><i class="fas fa-folder-open me-2"></i>Documentos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="situacion-financiera-tab" data-bs-toggle="tab" data-bs-target="#situacion-financiera" type="button" role="tab" aria-controls="situacion-financiera" aria-selected="false"><i class="fas fa-dollar-sign me-2"></i>Situación Financiera</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comprobantes-tab" data-bs-toggle="tab" data-bs-target="#comprobantes" type="button" role="tab" aria-controls="comprobantes" aria-selected="false"><i class="fas fa-receipt me-2"></i>Comprobantes Fiscales</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="calificaciones-tab" data-bs-toggle="tab" data-bs-target="#calificaciones" type="button" role="tab" aria-controls="calificaciones" aria-selected="false"><i class="fas fa-graduation-cap me-2"></i>Calificaciones</button>
            </li>
             <li class="nav-item" role="presentation">
                <button class="nav-link" id="observaciones-tab" data-bs-toggle="tab" data-bs-target="#observaciones" type="button" role="tab" aria-controls="observaciones" aria-selected="false"><i class="fas fa-comment-dots me-2"></i>Observaciones</button>
            </li>
        </ul>

        <div class="tab-content" id="expedienteTabContent">
            <div class="tab-pane fade show active" id="info-general" role="tabpanel" aria-labelledby="info-general-tab">
            <button class="btn btn-primary" type="button" id="btnExpComp"><i class="fas fa-user-edit me-2 mb-1"></i> Ver Expediente Completo</button>
    <h3 class="section-title">Identificación y Estatus</h3>
    <div class="info-grid">
        <div class="info-item"><strong>Matrícula:</strong> <span id="matricula">cargando...</span></div>
        <div class="info-item"><strong>Nombre Completo:</strong> <span id="nombreCompleto">cargando...</span></div>
        <div class="info-item"><strong>Estatus:</strong> <span id="estatusAlumno" class="badge bg-success">cargando...</span></div>
        <div class="info-item"><strong>CURP:</strong> <span id="curp">cargando...</span></div>
        <div class="info-item"><strong>Fecha de Nacimiento:</strong> <span id="fechaNacimiento">cargando...</span></div>
        <div class="info-item"><strong>Género:</strong> <span id="genero">cargando...</span></div>
        <div class="info-item"><strong>Lugar de Nacimiento:</strong> <span id="lugarNacimiento">cargando...</span></div>
        <div class="info-item"><strong>Nacionalidad:</strong> <span id="nacionalidad">cargando...</span></div>
    </div>

    <h3 class="section-title mt-4">Información Académica</h3>
    <div class="info-grid">
        <div class="info-item"><strong>Institución:</strong> <span id="institucion">cargando...</span></div>
        <div class="info-item"><strong>Programa:</strong> <span id="programa">cargando...</span></div>
        <div class="info-item"><strong>Nivel:</strong> <span id="nivelAcademico">cargando...</span></div>
        <div class="info-item"><strong>Generación:</strong> <span id="generacion">cargando...</span></div>
        <div class="info-item"><strong>Modalidad:</strong> <span id="modalidad">cargando...</span></div>
        <div class="info-item"><strong>Fecha de Inscripción:</strong> <span id="fechaInscripcion">cargando...</span></div>
    </div>

    <h3 class="section-title mt-4">Contacto</h3>
    <div class="info-grid">
        <div class="info-item"><strong>Teléfono Personal:</strong> <span id="telefonoPersonal">cargando...</span></div>
        <div class="info-item"><strong>Correo Personal:</strong> <span id="correoPersonal">cargando...</span></div>
        <div class="info-item"><strong>Correo Institucional:</strong> <span id="correoInstitucional">cargando...</span></div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i> Editar Información</button>
    </div>
</div>

    <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                <h3 class="section-title">Recepción de Documentos</h3>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Progreso de Entrega</span>
                        <strong>4 de 6 Documentos</strong>
                    </div>
                    <div class="progress" style="height: 20px;">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 66.66%;" aria-valuenow="4" aria-valuemin="0" aria-valuemax="6"></div>
                    </div>
                </div>

                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div class="me-auto my-1">
                            <i class="fas fa-id-card me-2 text-primary"></i>
                            <strong>Acta de nacimiento</strong>
                            <small class="d-block text-muted">Última actualización: 15/08/2023</small>
                        </div>
                        <div class="d-flex align-items-center my-1 document-actions">
                            <span class="badge bg-success me-3">Entregado y Verificado</span>
                            <a href="#" class="btn btn-sm btn-outline-secondary" title="Ver Documento"><i class="fas fa-eye"></i> Ver</a>
                            <button class="btn btn-sm btn-outline-primary" title="Reemplazar"><i class="fas fa-upload"></i> Reemplazar</button>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div class="me-auto my-1">
                            <i class="fas fa-id-card me-2 text-primary"></i>
                            <strong>CURP (documento)</strong>
                            <small class="d-block text-muted">Última actualización: 15/08/2023</small>
                        </div>
                        <div class="d-flex align-items-center my-1 document-actions">
                            <span class="badge bg-warning text-dark me-3">Pendiente de Verificación</span>
                            <a href="#" class="btn btn-sm btn-outline-secondary" title="Ver Documento"><i class="fas fa-eye"></i> Ver</a>
                            <button class="btn btn-sm btn-outline-success" title="Marcar como Verificado"><i class="fas fa-check-circle"></i> Verificar</button>
                            <button class="btn btn-sm btn-outline-danger" title="Rechazar Documento"><i class="fas fa-times-circle"></i> Rechazar</button>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap bg-light">
                        <div class="me-auto my-1">
                             <i class="fas fa-file-alt me-2 text-muted"></i>
                            <strong>Certificado de estudios</strong>
                            <small class="d-block text-muted">Aún no se ha entregado</small>
                        </div>
                        <div class="d-flex align-items-center my-1 document-actions">
                             <span class="badge bg-danger me-3">Pendiente</span>
                            <button class="btn btn-sm btn-primary" title="Subir Documento"><i class="fas fa-upload"></i> Subir</button>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div class="me-auto my-1">
                            <i class="fas fa-file-invoice me-2 text-primary"></i>
                            <strong>Título universitario</strong>
                             <small class="d-block text-muted">Última actualización: 18/08/2023</small>
                        </div>
                        <div class="d-flex align-items-center my-1 document-actions">
                            <span class="badge bg-success me-3">Entregado y Verificado</span>
                            <a href="#" class="btn btn-sm btn-outline-secondary" title="Ver Documento"><i class="fas fa-eye"></i> Ver</a>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap bg-light">
                        <div class="me-auto my-1">
                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                            <strong>Comprobante de domicilio</strong>
                            <small class="d-block text-muted">Aún no se ha entregado</small>
                        </div>
                        <div class="d-flex align-items-center my-1 document-actions">
                             <span class="badge bg-danger me-3">Pendiente</span>
                            <button class="btn btn-sm btn-primary" title="Subir Documento"><i class="fas fa-upload"></i> Subir</button>
                        </div>
                    </li>
                     <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div class="me-auto my-1">
                           <i class="fas fa-file-signature me-2 text-danger"></i>
                            <strong>Carta OTEM</strong>
                            <small class="d-block text-danger">Rechazado: El documento no es legible.</small>
                        </div>
                        <div class="d-flex align-items-center my-1 document-actions">
                             <span class="badge bg-danger me-3">Rechazado</span>
                            <button class="btn btn-sm btn-primary" title="Subir Nuevo Documento"><i class="fas fa-upload"></i> Subir de nuevo</button>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="tab-pane fade" id="situacion-financiera" role="tabpanel" aria-labelledby="situacion-financiera-tab">
                <h3 class="section-title">Pagos Pendientes</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-header-custom">
                            <tr>
                                <th>#</th>
                                <th>Concepto</th>
                                <th>Pagar antes de</th>
                                <th>Días Vencido</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="pagosPendientesBody">
                            </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-start mb-3">
                    <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-excel me-1"></i> Exportar Excel</button>
                    <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-pdf me-1"></i> Exportar PDF</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i> Imprimir</button>
                </div>

                <hr class="my-4">

                <h3 class="section-title">Pagos Realizados</h3>
                 <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-header-custom">
                            <tr>
                                <th>#</th>
                                <th>Concepto</th>
                                <th>Fecha Pago</th>
                                <th>Importe</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                            <tbody id="pagosRealizadosBody">
                            </tbody>
                        <tfoot id="pagosRealizadosFooter">
                            <tr>
                                <td colspan="3"></td>
                                <td class="fw-bold">Total Pagado:</td>
                                <td class="fw-bold status-pagado">$0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-start">
                     <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-excel me-1"></i> Exportar Excel</button>
                    <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-pdf me-1"></i> Exportar PDF</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i> Imprimir</button>
                </div>
            </div>

            <div class="tab-pane fade" id="comprobantes" role="tabpanel" aria-labelledby="comprobantes-tab">
                <h3 class="section-title">Comprobantes Fiscales Emitidos</h3>
                 <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-header-custom">
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Importe</th>
                                <th>Folio Fiscal (UUID)</th>
                                <th>Fecha Emisión</th>
                                <th>Status</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>19938Digital</td>
                                <td>28/05/2025</td>
                                <td>$500.00</td>
                                <td>f614cfee-eceb-4645-b139-ae7cc364e5a1</td>
                                <td>28/05/2025 18:10:23</td>
                                <td class="status-timbrado">Timbrado</td>
                                <td class="table-actions">
                                    <a href="#" class="btn btn-sm btn-danger" title="Descargar PDF"><i class="fas fa-file-pdf"></i> PDF</a>
                                    <a href="#" class="btn btn-sm btn-secondary" title="Descargar XML"><i class="fas fa-file-code"></i> XML</a>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-start">
                     <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-excel me-1"></i> Exportar Excel</button>
                    <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-pdf me-1"></i> Exportar PDF</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i> Imprimir</button>
                </div>
            </div>

            <div class="tab-pane fade" id="calificaciones" role="tabpanel" aria-labelledby="calificaciones-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title mb-0">Historial de Calificaciones</h3>
                    <button class="btn btn-primary"><i class="fas fa-print me-2"></i>Generar Boleta</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-header-custom">
                            <tr>
                                <th>Materia</th>
                                <th>Ciclo</th>
                                <th>Docente</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Administración de la producción</td><td class="text-center">10</td></tr>
                            <tr><td>2</td><td>Administración De Recursos Humanos</td><td class="text-center">9</td></tr>
                            <tr><td>3</td><td>Administración Estratégica</td><td class="text-center fw-bold text-danger">0</td></tr>
                            </tbody>
                    </table>
                </div>
                 <div class="d-flex justify-content-start mt-3">
                     <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-excel me-1"></i> Exportar Excel</button>
                    <button class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-file-pdf me-1"></i> Exportar PDF</button>
                </div>
            </div>

            <div class="tab-pane fade" id="observaciones" role="tabpanel" aria-labelledby="observaciones-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title mb-0">Observaciones y Seguimiento</h3>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaObservacion"><i class="fas fa-plus-circle me-2"></i>Nueva Observación</button>
                </div>
                <div id="listaObservaciones">
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="card-text">El alumno solicitó información sobre becas de excelencia.</p>
                            <footer class="blockquote-footer">Registrado por: <cite title="Source Title">Admin Escuela</cite> - 01/06/2025 10:30 AM</footer>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-body">
                            <p class="card-text">Se contactó para seguimiento de pago pendiente de Colegiatura Agosto.</p>
                             <footer class="blockquote-footer">Registrado por: <cite title="Source Title">Finanzas</cite> - 03/06/2025 15:00 PM</footer>
                        </div>
                    </div>
                    </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalNuevaObservacion" tabindex="-1" aria-labelledby="modalNuevaObservacionLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalNuevaObservacionLabel">Agregar Nueva Observación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="observacionTexto" class="form-label">Observación:</label>
                <textarea class="form-control" id="observacionTexto" rows="4"></textarea>
              </div>
               <div class="mb-3">
                <label for="observacionTipo" class="form-label">Tipo:</label>
                <select class="form-select" id="observacionTipo">
                    <option selected>General</option>
                    <option>Académica</option>
                    <option>Financiera</option>
                    <option>Comportamiento</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary">Guardar Observación</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   <script src="../../frontend/js/expedientealumno/expediente_alumno.js"></script>

</body>
</html>