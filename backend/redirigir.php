<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: login.php");
    exit();
}

$tipoSolicitado = $_GET['tipo'] ?? '';

if ($_SESSION['tipo_usuario'] === $tipoSolicitado) {
    // Redirigir al archivo correspondiente (ajusta nombres si son distintos)
    header("Location: ../../backend/php/{$tipoSolicitado}.php");
    exit();
} else {
    $_SESSION['mensaje_denegado'] = "❌ Acceso denegado. No tienes permisos para ingresar a esta sección.";
    header("Location: login.php");
    exit();
}
