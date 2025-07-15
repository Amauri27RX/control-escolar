<?php
// Evita caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Inicia la sesión para mostrar mensajes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 'marketing':
            header("Location: marketing.php");
            break;
        case 'control':
            header("Location: control.php");
            break;
        case 'finanzas':
            header("Location: finanzas.php");
            break;
        case 'titulacion':
            header("Location: titulacion.php");
            break;
        case 'academico':
            header("Location: academico.php");
            break;
        case 'soporte':
            header("Location: soporte.php");
            break;
        case 'tesis':
            header("Location: tesis.php");
            break;
        default:
            // Si el rol no es válido, cerrar sesión y volver a login
            session_destroy();
            header("Location: login.php");
            break;
    }
    exit();
}

// Captura el mensaje de error si existe
$mensaje_error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="../frontend/css/estilo-login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
</head>

<body <?php echo isset($_SESSION['rol']) ? 'data-rol="' . $_SESSION['rol'] . '"' : ''; ?>>
  <div class="login-container">
    <a href="../index.html" class="btn-regresar">
      <i class="fas fa-arrow-left"></i> 
    </a>
    <h1>Iniciar Sesión</h1>
    <form action="validar_usuario.php" method="POST" class="login-form">
      <label for="correo">Usuario</label>
      <input type="email" id="correo" name="correo" required>

      <label for="contrasena">Contraseña</label>
      <input type="password" id="contrasena" name="contrasena" required>

      <button type="submit"><i class="fas fa-sign-in-alt"></i> Iniciar sesión</button>
      <!-- Mostrar mensaje de error si existe -->
      <?php if (!empty($mensaje_error)) : ?>
        <p class="error" style="color: red; margin-top: 10px;"><?php echo htmlspecialchars($mensaje_error); ?></p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>

