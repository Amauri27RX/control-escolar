<?php
// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
    header("Location: login.php"); // Ajusta si este archivo está en raíz
    exit();
}

// Validar acceso según rol y página actual
$rol = $_SESSION['rol'];
$archivo_actual = basename($_SERVER['PHP_SELF']);

$accesos_por_rol = [
    'marketing'  => ['marketing.php', 'preinscripcion.php'],
    'control'    => ['control.php', 'inscripcion.php'],
    'finanzas'   => ['finanzas.php'],
    'titulacion' => ['titulacion.php'],
    'academico'  => ['academico.php'],
    'soporte'    => ['soporte.php', 'registro_usuario.php'],
    'tesis'      => ['tesis.php']
];

if (!in_array($archivo_actual, $accesos_por_rol[$rol])) {
    // Si intenta entrar a una página que no le corresponde
    header("Location: ../login.php");
    exit();
}
?>
