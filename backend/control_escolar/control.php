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
    <title>Soporte</title>
    <link rel="stylesheet" href="../../frontend/css/menu.css">
    <link rel="stylesheet" href="../../frontend/css/notificacion.css">
    <link rel="stylesheet" href="../../frontend/css/control_escolar/control.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>
<body>
    <header>
            <h1>¡Bienvenido!</h1>
            <h2>Control Escolar | <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
            <div class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-count">0</span> <!-- Contador de notificaciones -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <!-- Aquí irán las notificaciones -->
                    <ul id="notificationList">
                        <!-- Las notificaciones se agregarán aquí dinámicamente -->
                    </ul>
                    <div class="no-notifications" id="noNotifications">No tienes notificaciones nuevas.</div>
                </div>
            </div>
    </header>

    <button id="menu-toggle" class="menu-toggle">
            <i class="fas fa-bars"></i>
    </button>

    <div class="layout-container">
        <!-- Menú lateral -->
        <aside class="sidebar collapsed" id="sidebar">
            <nav>
                <ul>
                    <li><a href="control.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
                    <li><a href="inscripcion.php"><i class="fas fa-user"></i> <span>Inscribir alumno</span></a></li> 
                    <!-- <li><a href= "../../frontend/html/lista_alumnos.html" ><i class="fas fa-file-alt"></i> <span>Búsqueda alumnos</span></a></li> -->
                    <!-- <li><a href="#"><i class="fas fa-chart-line"></i> <span>Cadenas</span></a></li> -->
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Cerrar sesión</span></a></li>
                </ul>
            </nav>
        </aside>
    
  <main>
    <div class="container-fluid mt-1">
        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="h5 mb-0">Alumnos Registrados</h2>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <a href="../../frontend/html/inscripcion.html" class="btn btn-success"> 
                            <i class="fas fa-user-plus me-2"></i>Inscribir Nuevo Alumno
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-3 align-items-center"> 
                    <div class="col-lg-3 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="filtroBusquedaTexto" class="form-control" placeholder="Buscar por Nombre, Matrícula, CURP...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6"> 
                        <select id="filtroInstitucion" class="form-select">
                            <option selected value="">Todas las Instituciones</option> 
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4"> 
                        <select id="filtroPrograma" class="form-select">
                            <option selected value="">Todos los Programas</option> 
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4"> 
                        <select id="filtroGeneracion" class="form-select">
                            <option selected value="">Todas las Generaciones</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 d-flex justify-content-start justify-content-md-end"> 
                        <button id="btnAplicarFiltros" class="btn btn-outline-primary w-100 w-md-auto"> 
                            <i class="fas fa-filter me-2"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>

                <div id="mensajeLista" class="mb-3"></div> 

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Matrícula <i class="fas fa-sort ms-1 text-muted"></i></th>
                                <th scope="col">Nombre Completo <i class="fas fa-sort ms-1 text-muted"></i></th>
                                <th scope="col">Estatus</th>
                                <th scope="col">CURP</th>
                                <th scope="col">Programa <i class="fas fa-sort ms-1 text-muted"></i></th>
                                <th scope="col">Fecha Ing. <i class="fas fa-sort ms-1 text-muted"></i></th>
                                <th scope="col">Generación</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Correo Institucional</th>
                                <th scope="col">Correo Personal</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaAlumnosBody">
                            
                            <tr>
                                <td colspan="11" class="text-center">Cargando alumnos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Paginación de alumnos" class="mt-4 d-flex justify-content-center">
                    <ul class="pagination" id="paginacionContenedor" style="display:none;"> 
                        {/* <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">Siguiente</a></li> */}
                    </ul>
                </nav>
            </div>
            <div class="card-footer text-muted text-center py-3">
                &copy; <span id="currentYear"></span> Universidad UNAC - Todos los derechos reservados.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
    <script src="../../frontend/js/control_escolar/script_lista_alumnos.js"></script> 



  </main>
  <script src="../../frontend/js/menu_toggle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../../frontend/js/alerta_logout.js"></script> 
</body>
</html>
