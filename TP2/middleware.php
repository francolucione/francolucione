<?php
session_start();

function estaLogueado() {
    return isset($_SESSION['id']);
}

function esAdmin() {
    return (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
}
