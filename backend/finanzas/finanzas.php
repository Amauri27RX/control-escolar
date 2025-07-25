<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Financiero - UI/UX Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --card-bg: #ffffff;
            --sidebar-bg: #2d3748;
            --sidebar-hover: #4a5568;
            --header-bg: linear-gradient(120deg, #4361ee, #3a0ca3);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            color: #333;
            min-height: 100vh;
        }
        
        /* Layout */
        .app-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            background: var(--sidebar-bg);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: var(--sidebar-hover);
            color: white;
        }
        
        .sidebar-menu a i {
            margin-right: 12px;
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            padding: 20px;
            overflow-y: auto;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 25px;
        }
        
        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #2d3748;
        }
        
        .header-title p {
            color: #718096;
            margin-bottom: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--info);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .kpi-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }
        
        .kpi-card.income::before {
            background: var(--primary);
        }
        
        .kpi-card.overdue::before {
            background: var(--danger);
        }
        
        .kpi-card.upcoming::before {
            background: var(--warning);
        }
        
        .kpi-card.current::before {
            background: var(--success);
        }
        
        .kpi-card .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.2rem;
            opacity: 0.15;
            color: inherit;
        }
        
        .kpi-card .stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #718096;
            margin-bottom: 8px;
        }
        
        .kpi-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .kpi-card .stat-trend {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .trend-up {
            color: #38a169;
        }
        
        .trend-down {
            color: #e53e3e;
        }
        
        /* Dashboard Content Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }
        
        /* Main Card */
        .main-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 0;
        }
        
        .card-header-custom {
            background: white;
            padding: 20px;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header-custom h5 {
            font-weight: 600;
            margin: 0;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-header-custom h5 i {
            color: var(--primary);
        }
        
        .card-header-actions {
            display: flex;
            gap: 10px;
        }
        
        .card-body {
            padding: 0;
        }
        
        /* Table Styling */
        .table-container {
            overflow-x: auto;
        }
        
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-custom thead th {
            background-color: #f7fafc;
            color: #718096;
            font-weight: 600;
            padding: 15px 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table-custom tbody tr {
            transition: background-color 0.2s;
        }
        
        .table-custom tbody tr:hover {
            background-color: #f7fafc;
        }
        
        .table-custom tbody td {
            padding: 15px 20px;
            border-bottom: 1px solid #edf2f7;
            color: #4a5568;
        }
        
        .table-institution-row {
            background-color: #f7fafc;
            font-weight: 600;
            color: #2d3748;
        }
        
        .table-institution-row td {
            padding: 12px 20px;
        }
        
        .table-program-row td:first-child {
            padding-left: 40px !important;
        }
        
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: rgba(72, 187, 120, 0.15);
            color: #38a169;
        }
        
        .badge-warning {
            background-color: rgba(246, 173, 85, 0.15);
            color: #dd6b20;
        }
        
        .badge-danger {
            background-color: rgba(245, 101, 101, 0.15);
            color: #e53e3e;
        }
        
        /* Side Cards */
        .side-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .side-card .list-group-item {
            border: none;
            padding: 15px 20px;
            border-bottom: 1px solid #edf2f7;
            transition: background-color 0.2s;
        }
        
        .side-card .list-group-item:last-child {
            border-bottom: none;
        }
        
        .side-card .list-group-item:hover {
            background-color: #f7fafc;
        }
        
        .alert-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-title {
            font-weight: 600;
            margin-bottom: 3px;
            color: #2d3748;
        }
        
        .alert-subtitle {
            font-size: 0.85rem;
            color: #718096;
        }
        
        .alert-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .shortcut-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #4a5568;
            text-decoration: none;
        }
        
        .shortcut-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1rem;
        }
        
        .shortcut-text {
            font-weight: 500;
        }
        
        /* Footer */
        .app-footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 0.9rem;
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .app-container {
                grid-template-columns: 80px 1fr;
            }
            
            .sidebar-logo span, .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu a {
                justify-content: center;
            }
            
            .sidebar-menu a i {
                margin-right: 0;
                font-size: 1.3rem;
            }
        }
        
        @media (max-width: 768px) {
            .app-container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .header-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-university"></i>
                    <span>Finanzas</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-home"></i><span>Inicio</span></a></li>
                <li><a href="carrera.php"><i class="fas fa-chart-line"></i><span>Reportes</span></a></li>
                <li><a href="lista_alumnos.php"><i class="fas fa-users"></i><span>Estudiantes</span></a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i><span>Pagos</span></a></li>
                <li><a href="#"><i class="fas fa-building"></i><span>Instituciones</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i><span>Configuración</span></a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-title">
                    <h1>Finanzas</h1>
                    <p>Buenos días. Hoy es viernes, 13 de junio de 2025.</p>
                </div>
                <div class="header-actions">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="notifications">
                        <button class="btn btn-light position-relative">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">AM</div>
                        <div>
                            <div class="user-name">Ana Martínez</div>
                            <small class="user-role">Administradora Financiera</small>
                        </div>
                    </div>
                </div>
            </header>

            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card income">
                    <i class="fas fa-hand-holding-dollar card-icon"></i>
                    <div class="stat-label">INGRESOS (MES ACTUAL)</div>
                    <div class="stat-value text-primary">$1,350,000</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>12.5% vs mes anterior</span>
                    </div>
                </div>
                
                <div class="kpi-card overdue">
                    <i class="fas fa-file-invoice-dollar card-icon"></i>
                    <div class="stat-label">CARTERA VENCIDA</div>
                    <div class="stat-value text-danger">$85,400</div>
                    <div class="stat-trend trend-down">
                        <i class="fas fa-arrow-down"></i>
                        <span>8.2% vs mes anterior</span>
                    </div>
                </div>
                
                <div class="kpi-card upcoming">
                    <i class="fas fa-user-clock card-icon"></i>
                    <div class="stat-label">PRÓXIMOS A VENCER (7 DÍAS)</div>
                    <div class="stat-value text-warning">112</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>5.3% vs semana anterior</span>
                    </div>
                </div>
                
                <div class="kpi-card current">
                    <i class="fas fa-chart-pie card-icon"></i>
                    <div class="stat-label">% CARTERA AL CORRIENTE</div>
                    <div class="stat-value text-success">91%</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>3.1% vs mes anterior</span>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Main Card - Financial Summary -->
                <div class="main-card">
                    <div class="card-header-custom">
                        <h5><i class="fas fa-file-invoice-dollar"></i> Resumen Financiero por Programa</h5>
                        <div class="card-header-actions">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-sync"></i> Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th>Institución / Programa</th>
                                        <th class="text-center">Total Alumnos</th>
                                        <th class="text-center">Al Corriente</th>
                                        <th class="text-center">Próximos a Vencer</th>
                                        <th class="text-center">Vencidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-institution-row">
                                        <td colspan="5"><i class="fas fa-university me-2"></i>Instituto Universitario de las Américas y el Caribe</td>
                                    </tr>
                                    <tr class="table-program-row">
                                        <td>Licenciatura en Administración</td>
                                        <td class="text-center">124</td>
                                        <td class="text-center"><span class="badge-status badge-success">110 (89%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-warning">8 (6%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-danger">6 (5%)</span></td>
                                    </tr>
                                    <tr class="table-program-row">
                                        <td>Maestría en Enseñanza de Inglés</td>
                                        <td class="text-center">45</td>
                                        <td class="text-center"><span class="badge-status badge-success">38 (84%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-warning">5 (11%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-danger">2 (5%)</span></td>
                                    </tr>
                                    
                                    <tr class="table-institution-row">
                                        <td colspan="5"><i class="fas fa-university me-2"></i>UNAC Campus Sur</td>
                                    </tr>
                                    <tr class="table-program-row">
                                        <td>Doctorado en Alta Dirección</td>
                                        <td class="text-center">28</td>
                                        <td class="text-center"><span class="badge-status badge-success">27 (96%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-warning">1 (4%)</span></td>
                                        <td class="text-center"><span class="badge-status">0</span></td>
                                    </tr>
                                    
                                    <tr class="table-institution-row">
                                        <td colspan="5"><i class="fas fa-university me-2"></i>Instituto Técnico Asociado</td>
                                    </tr>
                                    <tr class="table-program-row">
                                        <td>Licenciatura en Derecho</td>
                                        <td class="text-center">88</td>
                                        <td class="text-center"><span class="badge-status badge-success">50 (57%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-warning">15 (17%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-danger">23 (26%)</span></td>
                                    </tr>
                                    
                                    <tr class="table-institution-row">
                                        <td colspan="5"><i class="fas fa-university me-2"></i>Centro de Estudios Superiores</td>
                                    </tr>
                                    <tr class="table-program-row">
                                        <td>Ingeniería Industrial</td>
                                        <td class="text-center">72</td>
                                        <td class="text-center"><span class="badge-status badge-success">65 (90%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-warning">5 (7%)</span></td>
                                        <td class="text-center"><span class="badge-status badge-danger">2 (3%)</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center py-3">
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-file-alt me-2"></i>Ver Reporte Financiero Completo
                        </a>
                    </div>
                </div>
                
                <!-- Side Cards -->
                <div>
                    <!-- Alerts Card -->
                    <div class="side-card">
                        <div class="card-header-custom">
                            <h5><i class="fas fa-bell text-danger"></i> Alertas de Pagos Críticos</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="alert-item">
                                    <div class="alert-content">
                                        <div class="alert-title">Juan Carlos Fernández H.</div>
                                        <div class="alert-subtitle">Lic. en Administración</div>
                                    </div>
                                    <span class="alert-badge bg-danger text-white">32 días vencido</span>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="alert-item">
                                    <div class="alert-content">
                                        <div class="alert-title">Ana Patricia Ramírez S.</div>
                                        <div class="alert-subtitle">Lic. en Derecho</div>
                                    </div>
                                    <span class="alert-badge bg-danger text-white">28 días vencido</span>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="alert-item">
                                    <div class="alert-content">
                                        <div class="alert-title">Ricardo Gómez L.</div>
                                        <div class="alert-subtitle">Lic. en Derecho</div>
                                    </div>
                                    <span class="alert-badge bg-danger text-white">25 días vencido</span>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="alert-item">
                                    <div class="alert-content">
                                        <div class="alert-title">María José Vargas R.</div>
                                        <div class="alert-subtitle">Ing. Industrial</div>
                                    </div>
                                    <span class="alert-badge bg-danger text-white">18 días vencido</span>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="alert-item">
                                    <div class="alert-content">
                                        <div class="alert-title">Carlos Andrés Mendoza</div>
                                        <div class="alert-subtitle">Maestría en Inglés</div>
                                    </div>
                                    <span class="alert-badge bg-warning text-dark">Vence hoy</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Shortcuts Card -->
                    <div class="side-card">
                        <div class="card-header-custom">
                            <h5><i class="fas fa-rocket text-primary"></i> Accesos Directos</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="shortcut-item">
                                    <div class="shortcut-icon">
                                        <i class="fas fa-search-dollar"></i>
                                    </div>
                                    <div class="shortcut-text">Buscar Transacción</div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="shortcut-item">
                                    <div class="shortcut-icon">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div class="shortcut-text">Generar Reporte de Ingresos</div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="shortcut-item">
                                    <div class="shortcut-icon">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <div class="shortcut-text">Configurar Planes de Pago</div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="shortcut-item">
                                    <div class="shortcut-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="shortcut-text">Inscribir Nuevo Alumno</div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="shortcut-item">
                                    <div class="shortcut-icon">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="shortcut-text">Ver Estadísticas</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="app-footer">
                <p>© 2025</p>
            </footer>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/finanzas/datosalumnos.js">
    </script>
</body>
</html>