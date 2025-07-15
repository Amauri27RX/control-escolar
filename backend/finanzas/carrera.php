<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financiero - Reporte por Carrera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
<body>
    <div class="container-fluid my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0"><i class="fas fa-list-check me-3"></i>Reporte Financiero por Carrera</h1>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-users fa-3x text-primary opacity-50 me-4"></i>
                        <div>
                            <h6 class="card-title">Total Alumnos Activos</h6>
                            <p class="card-text" id="kpiTotalAlumnos">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-lg-3">
                <div class="card kpi-card shadow-sm h-100" style="border-left-color: #198754;">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-hand-holding-dollar fa-3x text-success opacity-50 me-4"></i>
                        <div>
                            <h6 class="card-title">Ingresos del Periodo</h6>
                            <p class="card-text" id="kpiIngresos">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card kpi-card shadow-sm h-100" style="border-left-color: #dc3545;">
                     <div class="card-body d-flex align-items-center">
                        <i class="fas fa-file-invoice-dollar fa-3x text-danger opacity-50 me-4"></i>
                        <div>
                            <h6 class="card-title">Total Cartera Vencida</h6>
                            <p class="card-text" id="kpiTotalVencido">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card kpi-card shadow-sm h-100" style="border-left-color: #ffc107;">
                     <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user-clock fa-3x text-warning opacity-50 me-4"></i>
                        <div>
                            <h6 class="card-title">Pagos Próximos a Vencer (10 días)</h6>
                            <p class="card-text" id="kpiProximosVencer">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end" id="formFiltros">
                    <div class="col-lg-2 col-md-6">
                        <label for="filtroInstitucion" class="form-label fw-bold">Institución</label>
                        <select id="filtroInstitucion" name="institucion" class="form-select">
                            <option value="" selected>Todas</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="filtroNivel" class="form-label fw-bold">Nivel</label>
                        <select id="filtroNivel" name="nivel" class="form-select">
                            <option value="" selected>Todos</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="filtroPeriodo" class="form-label fw-bold">Periodo</label>
                        <select id="filtroPeriodo" name="periodo" class="form-select">
                            <option value="mes_actual" selected>Mes Actual</option>
                            <option value="mes_pasado">Mes Pasado</option>
                            <option value="trimestre_actual">Trimestre Actual</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label for="filtroFechaInicio" class="form-label">Desde</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="filtroFechaInicio" disabled>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label for="filtroFechaFin" class="form-label">Hasta</label>
                        <input type="date" class="form-control" name="fecha_fin" id="filtroFechaFin" disabled>
                    </div>
                    <div class="col-lg-1 col-md-12">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Resumen por Carrera - <span class="text-primary" id="periodoMostrado">Periodo: Mes Actual</span></h5>
                <div>
                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-file-excel me-1"></i> Exportar</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-header-custom">
                            <tr>
                                <th scope="col">Carrera / Programa</th>
                                <th scope="col" class="text-center">Inscritos</th>
                                <th scope="col" class="text-center">Al Corriente</th>
                                <th scope="col" class="text-center">Próximos a Vencer</th>
                                <th scope="col" class="text-center">Pagos Vencidos</th>
                                <th scope="col" style="width: 15%;">% Al Corriente</th>
                                <th scope="col" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaReporteBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/finanzas/script_carrera.js"></script> </body>
</html>