<?php
require_once 'usuario.php';
require_once 'middleware.php';

echo json_encode(['mensaje' => 'Sesión cerrada con éxito', 'usuario' =>$_SESSION['nombre'], 'rol' => $_SESSION['rol']]);

session_unset();
session_destroy();

