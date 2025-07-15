<?php
// Evita caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('../verificar_sesion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing - Preinscripción</title>
    <link rel="stylesheet" href="../../frontend/css/marketing/preinscripcion.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="../../frontend/js/marketing/main.js"></script>
    <script src="../../frontend/js/marketing/matricula.js"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <header>
        <h1>Marketing</h1>
        <h2>Preinscripción</h2>
        <a href="marketing.php" class="btn-regresar">
            <i class="fas fa-arrow-left"></i>
        </a>
    </header>
</head>
<body> 
    <div class="form-container">
        <form id="formPreinscripcion"> 

        <!-- Sección DATOS DEL PROGRAMA -->
        <div class="form-section">
                <h3>DATOS DEL PROGRAMA</h3>
                <div class="program-data-grid">
                    <div class="form-group">
                        <label for="escuela">Escuela</label>
                        <select id="institucion" name="institucion">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="nivel_educativo">Nivel educativo</label>
                        <select id="nivel_educativo">
                            <option value="">Seleccionar</option>
                            <option value="licenciatura">Licenciatura</option>
                            <option value="especialidad">Especialidad</option>
                            <option value="maestria">Maestría</option>
                            <option value="doctorado">Doctorado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="programa">Carrera o programa</label>
                        <select id="programa">
                            <option value="">Seleccionar programa</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="modalidad">Modalidad</label>
                        <select id="modalidad">
                            <option value="">Seleccionar</option>
                            <option value="escolarizada">Escolarizada</option>
                            <option value="semiescolarizada">Semiescolarizada</option>
                            <option value="en_linea">En línea</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <div class="date-input">
                            <input type="date" id="fecha_inicio">
                            <span class="date-format"></span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="rvoe_auto">RVOE Automatico</label>
                        <input type="text" id="rvoe_auto" value="RVOE" disabled class="input-filled">
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="fecha_rvoe">Fecha de RVOE</label>
                        <input type="text" id="fecha_rvoe" value="Fecha RVOE" disabled class="input-filled">
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="dgp">DGP</label>
                        <input type="text" id="dgp" value="DGP" disabled class="input-filled">
                    </div>
                </div>
            </div>
            
            <!-- Sección DATOS PERSONALES -->
            <div class="form-section">
                <h3>DATOS PERSONALES</h3>
                <div class="personal-data-grid">
                    <div class="form-group">
                        <label for="nombre">Nombres</label>
                        <input type="text" id="nombre" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno</label>
                        <input type="text" id="apellido_paterno" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno</label>
                        <input type="text" id="apellido_materno" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                    </div>

                    <div class="form-group">
                        <label for="matricula">Matrícula</label>
                        <input type="text" id="matricula" name="matricula" disabled class="input-filled">
                    </div>
                    
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero" required>
                            <option value="">Seleccionar</option>
                            <option value="251">Masculino</option>
                            <option value="250">Femenino</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Sección CARGA DE DOCUMENTOS -->
            <div class="form-section">
            <h3>CARGA DE DOCUMENTOS</h3>
            <div class="documents-grid">

                <div class="document-group">
                <label for="acta_nacimiento">Acta de nacimiento (PDF)</label>
                <div class="drop-zone" data-input="acta_nacimiento">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button>
                    </div>
                </div>
                <input type="file" id="acta_nacimiento" accept=".pdf" style="display: none;">
                </div>

                <div class="document-group">
                <label for="curp">CURP (PDF)</label>
                <div class="drop-zone" data-input="curp">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button>
                    </div>
                </div>
                <input type="file" id="curp" accept=".pdf" style="display: none;">
                </div>

                <div class="document-group">
                <label for="solicitud">Solicitud de inscripción (PDF)</label>
                <div class="drop-zone" data-input="solicitud">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button>
                    </div>
                </div>
                <input type="file" id="solicitud" accept=".pdf" style="display: none;">
                </div>

                <div class="document-group">
                <label for="certificado">Certificado de estudios (PDF)</label>
                <div class="drop-zone" data-input="certificado">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button>
                    </div>
                </div>
                <input type="file" id="certificado" accept=".pdf" style="display: none;">
                </div>


                <div class="document-group" id="otem" style="display: none;">
                    <label for="otem">Carta OTEM (PDF)</label>
                    <div class="drop-zone" data-input="otem">
                        <i class="fas fa-file-upload drop-icon"></i>
                        <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                        <div class="file-info" style="display: none;">
                        <i class="fas fa-file-pdf file-icon"></i>
                        <span class="file-name"></span>
                        <button type="button" class="delete-btn">❌</button>
                        </div>
                    </div>
                    <input type="file" id="otem" accept=".pdf" style="display: none;">
                </div>



                <div class="document-group" id="titulo">
                <label for="titulo">Título (PDF)</label>
                <div class="drop-zone" data-input="titulo">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button>
                    </div>
                </div>
                <input type="file" id="titulo" accept=".pdf" style="display: none;">
                </div>

                <div class="document-group" id="cedula">
                <label for="cedula">Cédula (PDF)</label>
                <div class="drop-zone" data-input="cedula">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button>
                    </div>
                </div>
                <input type="file" id="cedula" accept=".pdf" style="display: none;">
                </div>
        </div>
            <div class="document-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="usa_otem" />
                    Usar CARTA OTEM en lugar de título y cédula
                </label>
            </div>
    </div>
 
        
        <!-- Sección PROMOCIÓN APLICADA -->
        <div class="form-section">
            <h3>PROMOCIÓN APLICADA</h3>
            <div class="payment-grid">
                <div class="form-group">
                    <label for="costo_inscripcion">$ Costo inscripción</label>
                    <input type="number" id="costo_inscripcion" min="0" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="colegiatura">$ Colegiatura</label>
                    <input type="number" id="colegiatura" min="0" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="reinscripcion">$ Reinscripción</label>
                    <input type="number" id="reinscripcion" min="0" step="0.01">
                </div>

                <div class="form-group">
                <label for="comprobante_inscripción">Adjuntar comprobante de pago (PDF)</label>
                <div class="drop-zone" data-input="comprobante_inscripcion">
                    <i class="fas fa-file-upload drop-icon"></i>
                    <span class="drop-text">Arrastra o haz clic para subir PDF</span>
                    <div class="file-info" style="display: none;">
                    <i class="fas fa-file-pdf file-icon"></i>
                    <span class="file-name"></span>
                    <button type="button" class="delete-btn">❌</button> 
                    </div>
                </div>
                <input type="file" id="comprobante_inscripcion" accept=".pdf" style="display: none;">
                </div>
            </div>
        </div>
        
        <!-- Botón principal de guardado (FUERA de todas las secciones anteriores) -->
        <div class="form-section text-center">
                <button type="button" id="guardar-btn" class="action-button btn-primary btn-save-all">Guardar toda la información</button>
    </form>
    <button type="button" class="btn-reset">Borrar todos los campos</button>
        </div>
    </div>
    <script src="../../frontend/js/carga_pdf.js"></script>
    <script src="../../frontend/js/alerta_logout.js"></script> 
    <script src="../../frontend/js/marketing/otem.js"></script>
</body>
</html>