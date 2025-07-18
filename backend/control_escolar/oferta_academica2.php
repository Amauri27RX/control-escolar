<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Oferta Académica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-header { background-color: #e9ecef; }
    </style>
</head>
<body>
    <div class="container-fluid my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0"><i class="fas fa-calendar-alt me-3"></i>Gestión de Oferta Académica</h1>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-4">
                        <label for="filtroInstitucion" class="form-label fw-bold">Institución:</label>
                        <select id="filtroInstitucion" class="form-select form-select-lg"></select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroPrograma" class="form-label fw-bold">Programa:</label>
                        <select id="filtroPrograma" class="form-select form-select-lg"></select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroCiclo" class="form-label fw-bold">Periodo:</label>
                        <select id="filtroCiclo" class="form-select form-select-lg"></select>
                    </div>
                    </div>
            </div>
            <div class="card-body">
                <div id="mensajeOferta" class="mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Materia</th>
                                <th style="width: 30%;">Docente Asignado</th>
                                <th style="width: 10%;">Cupo</th>
                                <th style="width: 15%;">Aula</th>
                                <th style="width: 10%;" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaOfertaBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Seleccione una institución, programa y ciclo para ver las materias.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/control_escolar/script_oferta_academica2.js"></script> 
</body>
</html>