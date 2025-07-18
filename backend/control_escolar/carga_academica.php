<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Carga Académica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <div class="container my-5">
        <h1 class="h2 mb-4"><i class="fas fa-book-reader me-3"></i>Gestión de Carga Académica del Alumno</h1>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">1. Buscar Alumno</h5>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre o matrícula...">
                    <button class="btn btn-primary" id="searchButton"><i class="fas fa-search"></i></button>
                </div>
                <div id="searchResults" class="list-group mt-2"></div>
            </div>
        </div>

        <div id="studentDetailsCard" class="card shadow-sm mt-4" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">2. Asignar Materias</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Matrícula:</strong> <span id="studentMatricula"></span></div>
                    <div class="col-md-6"><strong>Nombre:</strong> <span id="studentName"></span></div>
                    <div class="col-md-3"><strong>Ciclo de Inicio:</strong> <span id="studentCiclo"></span></div>
                </div>
                <hr>
                <h6>Materias del Programa: <strong id="studentProgram"></strong></h6>
                <div id="materiasList" class="list-group">
                    </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-success btn-lg" id="saveButton">
                    <i class="fas fa-save me-2"></i>Guardar Cambios en la Carga Académica
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/control_escolar/script_carga_academica.js"></script>
</body>
</html>