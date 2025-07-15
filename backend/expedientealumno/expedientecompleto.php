<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente Completo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.2rem;
            font-size: 0.9rem;
        }
        .info-data {
            font-size: 1rem;
            padding: 0.375rem 0.75rem;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            min-height: 38px;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
            <div>
                <h1 class="h3 mb-0">Expediente Completo del Alumno</h1>
                <p class="mb-0 text-muted" id="nombreAlumnoHeader">cargando...</p>
            </div>
            <div>
                <button class="btn btn-primary"><i class="fas fa-edit me-2"></i>Editar Datos</button>
                <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i>Imprimir Ficha</button>
            </div>
        </div>

        <div class="accordion shadow-sm" id="accordionExpedienteCompleto">

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <i class="fas fa-user-alt me-3"></i> 1. Datos Personales
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-4"><p class="info-label">Matrícula:</p><div class="info-data" id="matricula">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Nombre(s):</p><div class="info-data" id="nombre">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Apellido Paterno:</p><div class="info-data" id="apellido_paterno">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Apellido Materno:</p><div class="info-data" id="apellido_materno">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Género:</p><div class="info-data" id="genero">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Fecha de Nacimiento:</p><div class="info-data" id="fecha_nacimiento">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Edad:</p><div class="info-data" id="edad">31 años</div></div>
                            <div class="col-md-4"><p class="info-label">País de Nacimiento:</p><div class="info-data" id="pais_nacimiento">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Ciudad o Estado de Nacimiento:</p><div class="info-data" id="ciudad_nacimiento">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Nacionalidad:</p><div class="info-data" id="nacionalidad">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">CURP:</p><div class="info-data" id="curp">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">CURA (si aplica):</p><div class="info-data" id="cura">cargando...</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <i class="fas fa-map-marker-alt me-3"></i> 2. Domicilio y Contacto
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                    <div class="accordion-body">
                        <h5>Residencia</h5>
                        <div class="row g-3">
                            <div class="col-md-6"><p class="info-label">País:</p><div class="info-data" id="pais_residencia">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Ciudad o Estado:</p><div class="info-data" id="ciudad_residencia">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Colonia o Localidad:</p><div class="info-data" id="colonia_residencia">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Calle:</p><div class="info-data" id="calle_residencia">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Núm. Exterior:</p><div class="info-data" id="num_ext_residencia">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Núm. Interior (opcional):</p><div class="info-data" id="num_int_residencia">cargando...</div></div>
                        </div>
                        <hr class="my-4">
                        <h5>Contacto</h5>
                         <div class="row g-3">
                            <div class="col-md-6"><p class="info-label">Teléfono Personal:</p><div class="info-data" id="telefono_personal">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Correo Electrónico Personal:</p><div class="info-data" id="correo_personal">cargando...</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <i class="fas fa-graduation-cap me-3"></i> 3. Programa al que se Inscribe
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                    <div class="accordion-body">
                         <div class="row g-3">
                            <div class="col-md-12"><p class="info-label">Institución:</p><div class="info-data" id="institucion_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Nivel Educativo:</p><div class="info-data" id="nivel_educativo_programa">cargando...</div></div>
                            <div class="col-md-8"><p class="info-label">Carrera/Programa:</p><div class="info-data" id="carrera_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Modalidad:</p><div class="info-data" id="modalidad_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">RVOE:</p><div class="info-data" id="rvoe_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Fecha de RVOE:</p><div class="info-data" id="fecha_rvoe_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Clave DGP:</p><div class="info-data" id="dgp_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Permanencia:</p><div class="info-data" id="permanencia_programa">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Fecha de Ingreso:</p><div class="info-data" id="fecha_ingreso_programa">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Ciclo Escolar:</p><div class="info-data" id="ciclo_escolar_programa">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Modalidad de Titulación:</p><div class="info-data" id="titulacion_programa">cargando...</div></div>
                             <div class="col-md-6"><p class="info-label">Promoción Aplicada:</p><div class="info-data" id="promocion_programa">cargando...</div></div>
                             <div class="col-md-6"><p class="info-label">Estatus del Alumno:</p><div class="info-data fw-bold text-success" id="estatus_alumno_programa">cargando...</div></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <i class="fas fa-book-reader me-3"></i> 4. Antecedentes Académicos
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                    <div class="accordion-body">
                         <div class="row g-3">
                            <div class="col-md-6"><p class="info-label">Nivel Educativo Anterior:</p><div class="info-data" id="nivel_anterior">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Nombre de la Institución:</p><div class="info-data" id="institucion_anterior">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Ciudad o Estado de la Institución:</p><div class="info-data" id="ciudad_anterior">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Fecha de Inicio:</p><div class="info-data" id="fecha_inicio_anterior">cargando...</div></div>
                            <div class="col-md-4"><p class="info-label">Fecha de Finalización:</p><div class="info-data" id="fecha_fin_anterior">cargando...</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        <i class="fas fa-briefcase me-3"></i> 5. Lugar de Trabajo (Opcional)
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                    <div class="accordion-body">
                         <div class="row g-3">
                            <div class="col-md-12"><p class="info-label">Nombre de la empresa o institución:</p><div class="info-data" id="nombre_empresa">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Puesto o cargo:</p><div class="info-data" id="puesto_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Área o departamento:</p><div class="info-data" id="area_trabajo">cargando...</div></div>
                            
                            <div class="col-12"><hr class="my-2"></div>

                            <div class="col-md-6"><p class="info-label">País:</p><div class="info-data" id="pais_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Ciudad o estado:</p><div class="info-data" id="ciudad_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Colonia o localidad:</p><div class="info-data" id="colonia_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Calle:</p><div class="info-data" id="calle_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Núm. Exterior:</p><div class="info-data" id="num_ext_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Núm. Interior (opcional):</p><div class="info-data" id="num_int_trabajo">cargando...</div></div>
                            
                            <div class="col-12"><hr class="my-2"></div>
                            
                            <div class="col-md-6"><p class="info-label">Teléfono:</p><div class="info-data" id="telefono_trabajo">cargando...</div></div>
                            <div class="col-md-6"><p class="info-label">Correo electrónico corporativo:</p><div class="info-data" id="correo_trabajo">cargando...</div></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/expedientealumno/expedientecompleto.js"></script>
</body>
</html>