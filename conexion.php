<?php
// datos para conectarme a la bd mysql
$servername = "localhost";
$username = "root";
$password = NULL;
$dbname = "sistemavotacion";

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>