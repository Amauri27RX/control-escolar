<?php
session_start();
include 'conexion.php'; // Asegúrate de tener tu conexión configurada correctamente

$correo = $_POST['correo'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

$correo = trim($correo);
$contrasena = trim($contrasena);

// Validación de usuario
if (empty($correo) || empty($contrasena)) {
    echo "Correo y contraseña son obligatorios.";
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Debes ingresar un correo electrónico válido.";
    header("Location: login.php");
    exit();
}

// Buscar usuario en la base de datos
$sql = "SELECT * FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuarioBD = $resultado->fetch_assoc();

    echo "Contraseña del Formulario: " . htmlspecialchars($contrasena);
    echo "<br>";
    echo "Hash de la Base de Datos: " . htmlspecialchars($usuarioBD['contrasena']);
    echo "<br>";

    if (password_verify($contrasena, $usuarioBD['contrasena'])) {
        $_SESSION['usuario'] = $usuarioBD['correo'];
        $_SESSION['rol'] = $usuarioBD['tipo_usuario'];
        switch ($usuarioBD['tipo_usuario']) {
            case 'marketing':
                header("Location: marketing/marketing.php");
                break;
            case 'control':
                header("Location: control_escolar/control.php");
                break;
            case 'finanzas':
                header("Location: finanzas/finanzas.php");
                break;
            case 'titulacion':
                header("Location: titulacion.php");
                break;
            case 'academico':
                header("Location: academico.php");
                break;
            case 'soporte':
                header("Location: soporte/soporte.php");
                break;
            case 'tesis':
                header("Location: tesis.php");
                break;
            default:
                $_SESSION['error'] = "Tipo de usuario no reconocido.";
                header("Location: login.php");
                break;
        } exit();
    } else {
        $_SESSION['error'] = "Credenciales incorrectas.";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Credenciales incorrectas.";
    header("Location: login.php");
    exit();
}

?>
