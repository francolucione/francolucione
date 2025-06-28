<?php
function conectarBD() {
    $host = 'localhost';
    $usuario = 'root';
    $clave = '';
    $nombreBD = 'veterinaria';

    $conexion = new mysqli($host, $usuario, $clave, $nombreBD);

    if ($conexion->connect_error) {
        die('Error al conectar a la base de datos: ' . $conexion->connect_error);
    }

    return $conexion;
}
