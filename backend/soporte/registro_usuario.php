<?php
// Evita caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('../verificar_sesion.php');

include '../conexion.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $tipo_usuario = $_POST['tipo_usuario'];

    if (empty($correo) || empty($contrasena)) {
        $mensaje = "Correo y contraseña son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Debes ingresar un correo electrónico válido.";
    } else {
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (correo, contrasena, tipo_usuario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $correo, $contrasena_hash, $tipo_usuario);

        if ($stmt->execute()) {
            $mensaje = "✅ Usuario registrado exitosamente.";
        } else {
            $mensaje = "❌ Error al registrar usuario: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <link rel="stylesheet" href="../../frontend/css/estilo-registro-usuario.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
</head>


<body>
  <header>
    <h1>Soporte</h1>
    <h2>Registrar usuario</h2>
    <a href="soporte.php" class="btn-regresar"> 
        <i class="fas fa-arrow-left"></i>
    </a>
    </header>

    <div class="form-container">
      <h3>Registro con correo institucional</h3>
      <form method="POST" action="" class="login-form">
        <label for="correo">Usuario</label>
        <input type="email" name="correo" id="correo" required>

        <label for="contrasena">Contraseña</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <label for="tipo_usuario">Tipo de usuario</label>
        <select name="tipo_usuario" id="tipo_usuario" required>
          <option value="marketing">Marketing</option> 
          <option value="control">Control</option>
          <option value="finanzas">Finanzas</option>
          <option value="titulacion">Titulación</option>
          <option value="soporte">Soporte</option>
          <option value="academico">Académico</option>
          <option value="tesis">Tesis</option>
          <option value="alumnos">Alumnos</option>
          <option value="maestros">Maestros</option>
        </select>

        <button type="submit">Registrar usuario</button>
      </form>

      <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
      <?php endif; ?>
    </div>
    <script src="../../frontend/js/alerta_logout.js"></script> 
</body>
</html>
