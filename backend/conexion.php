<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "control_escolar";

// Crear conexión
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Asegurar que usamos UTF-8 para caracteres especiales
mysqli_set_charset($conn, "utf8");

// Opcional: Configurar zona horaria si es necesario
date_default_timezone_set('America/Mexico_City');
?> 