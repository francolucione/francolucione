<?php
require_once 'usuario.php';
require_once 'middleware.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        if (esAdmin() === false) {
            echo json_encode(['error' => 'Acceso restringido.']);
            exit;
        } 
        echo json_encode(Usuario::obtenerUsuarios());
        exit;
    }

    if ($method === 'POST') {
        if (!$data || !isset($data['nombre'], $data['email'], $data['clave'])) {
            echo json_encode(['error' => 'Faltan datos obligatorios']);
            exit;
        }
        $nombre = $data['nombre'];
        $email = $data['email'];
        $clave = password_hash($data['clave'], PASSWORD_DEFAULT);
        $rol = 'comun';
        Usuario::crearUsuario($nombre, $email, $clave, $rol);
        echo json_encode(['mensaje' => 'Usuario creado correctamente']);
        exit;
    }

    if ($method === 'PUT') {
        if (esAdmin() === false) {
            echo json_encode(['error' => 'Acceso restringido.']);
            exit;
        } 
        if (!$data || !isset($data['id'], $data['valorEditado'], $data['nuevoValor'])) {
            echo json_encode(['error' => 'Faltan datos para editar']);
            exit;
        }
        Usuario::editarUsuario((int)$data['id'], $data['valorEditado'], $data['nuevoValor']);
        echo json_encode(['mensaje' => 'Usuario actualizado']);
        exit;
    }

    if ($method === 'DELETE') {
            if (esAdmin() === false) {
            echo json_encode(['error' => 'Acceso restringido.']);
            exit;
        } 
        if (!$data || !isset($data['id'])) {
            echo json_encode(['error' => 'Falta el ID para eliminar']);
            exit;
        }
        Usuario::borrarUsuario((int)$data['id']);
        echo json_encode(['mensaje' => 'Usuario eliminado']);
        exit;
    }

    echo json_encode(['error' => 'Método HTTP no permitido']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}




