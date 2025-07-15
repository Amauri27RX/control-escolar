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
    <title>Marketing</title>
    <link rel="stylesheet" href="../../frontend/css/menu.css">
    <link rel="stylesheet" href="../../frontend/css/marketing/notificacion.css">
    <link rel="stylesheet" href="../../frontend/css/marketing/marketing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

</head>

<body>
    <header>
            <h1>¡Bienvenido!</h1>
            <h2>Marketing | <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
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
                    <li><a href="marketing.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
                    <li><a href="preinscripcion.php"><i class="fas fa-user"></i> <span>Inscribir</span></a></li>
                    <li><a href="#"><i class="fas fa-file-alt"></i> <span>Promociones</span></a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> <span>Estadísticas</span></a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Cerrar sesión</span></a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="form-container">
                <section class="dashboard-section">
                    <div class="grid-panel">
                        <div class="panel-box">
                            <h3>Inscripciones por mes</h3>
                            <canvas id="chartInscripciones" class="chartInscripciones"></canvas>
                            <!-- Aquí irá tu gráfica -->
                        </div>
                        <div class="panel-box">
                            <h3>Alumnos por programa</h3>
                            <div class="carousel-container">
                                <!-- Slide  -->
                                <div class="slide active">
                                    <h4>UNAC</h4>
                                    <canvas id="chartUnac" class="chartUnac"></canvas>
                                </div>

                                <!-- Slide 2 -->
                                <div class="slide">
                                    <h4>IUAC</h4>
                                    <canvas id="chartIuac" class="chartIuac"></canvas>
                                </div>

                                <!-- Slide 3 -->
                                <div class="slide">
                                    <h4>IUAC de México</h4>
                                    <canvas id="chartIuacMx" class="chartIuacMx"></canvas>
                                </div>

                                <!-- Slide 4 -->
                                <div class="slide">
                                    <h4>UCCEG</h4>
                                    <canvas id="chartUcceg" class="chartUcceg"></canvas>
                                </div>

                                <div class="carousel-alumno">
                                    <button class="carousel-prev-a"><i class="fas fa-chevron-left"></i></button>
                                    <button class="carousel-next-a"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="panel-box1">
                            <h3>Promoción vigente</h3>
                            <div class="carousel-container">
                                <div class="carousel-slide active">
                                    <h4>Licenciatura</h4>
                                    <ul class="promo-list">
                                        <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $99</li>
                                        <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> 20% de descuento</li>
                                        <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $2,500</li>
                                    </ul>
                                </div>
                                <div class="carousel-slide">
                                    <h4>Maestría</h4>
                                    <ul class="promo-list">
                                        <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                        <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> 25% de descuento</li>
                                        <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $3,500</li>
                                    </ul>
                                </div>
                                <div class="carousel-slide">
                                    <h4>Doctorado</h4>
                                    <ul class="promo-list">
                                        <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                        <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                        <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                    </ul>
                                </div>

                                <div class="carousel-controls">
                                    <button class="carousel-prev"><i class="fas fa-chevron-left"></i></button>
                                    <button class="carousel-next"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            
                        </div>
                        <div class="panel-box1">
                            <h3>Becas disponibles</h3>
                            <div class="carousel-container">
                                <!-- Slide 1 -->
                                    <div class="slide-beca active">
                                        <h4>Beca Docentes</h4>
                                        <div class="beca-container">
                                            <div class="beca-box">
                                                <h5>Maestría</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                            <div class="beca-box">
                                                <h5>Especialidad</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                            <div class="beca-box">
                                                <h5>Doctorado</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Slide 2 -->
                                <div class="slide-beca">
                                    <h4>Beca Salud</h4>
                                        <div class="beca-container">
                                            <div class="beca-box">
                                                <h5>Maestría</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                            <div class="beca-box">
                                                <h5>Especialidad</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                            <div class="beca-box">
                                                <h5>Doctorado</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Slide 3 -->
                                <div class="slide-beca">
                                    <h4>Beca Lealtad</h4>
                                        <div class="beca-container">
                                            <div class="beca-box">
                                                <h5>Maestría</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                            <div class="beca-box">
                                                <h5>Especialidad</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                            <div class="beca-box">
                                                <h5>Doctorado</h5>
                                                <ul>
                                                    <li><i class="fas fa-check-circle"></i><strong> Inscripción:</strong> $150</li>
                                                    <li><i class="fas fa-tag"></i><strong> Colegiaturas:</strong> $1,500</li>
                                                    <li><i class="fas fa-redo"></i><strong> Reinscripciones:</strong> $4,000</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                        
                                <!-- Botones de navegación -->
                                <div class="carousel-beca-controls">
                                    <button class="carousel-prev-b"><i class="fas fa-chevron-left"></i></button>
                                    <button class="carousel-next-b"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>    
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="../../frontend/js/alerta_logout.js"></script> 
    <script src="../../frontend/js/menu_toggle.js"></script>
    <script src="../../frontend/js/marketing/inscripciones_mes.js"></script>
    <script src="../../frontend/js/marketing/alumno_programa.js"></script>
    <script src="../../frontend/js/marketing/becas-carousel.js"></script>
    <script src="../../frontend/js/marketing/promo_carousel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>