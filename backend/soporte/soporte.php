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
    <link rel="stylesheet" href="../../frontend/css/soporte/soporte.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>
<body>
    <header>
            <h1>¡Bienvenido!</h1>
            <h2>Soporte | <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
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
                    <li><a href="soporte.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
                    <li><a href="registro_usuario.php"><i class="fas fa-user"></i> <span>Registrar Usuario</span></a></li>
                    <li><a href="../../frontend/html/asignar_correo.html"><i class="fas fa-file-alt"></i> <span>Correos</span></a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> <span>Cadenas</span></a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Cerrar sesión</span></a></li>
                </ul>
            </nav>
        </aside>

  <main>
  </main>
 
  <script src="../../frontend/js/menu_toggle.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../../frontend/js/alerta_logout.js"></script> 
</body>
</html>
