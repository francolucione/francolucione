<?php
session_start();

include("conexionBD.php");
$conn = conectarBD();

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['email'], $data['clave'])) {
    echo json_encode(['error' => 'Faltan email o clave']);
    exit;
}

$email = $data['email'];
$clave = $data['clave'];

$stmt = $conn->prepare("SELECT id, nombre, clave, rol FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($clave, $usuario['clave'])) {

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        echo json_encode(['mensaje' => 'Login exitoso', 'usuario' => $_SESSION['nombre'], 'rol' => $_SESSION['rol']]);
    } else {
        echo json_encode(['error' => 'Clave incorrecta']);
    }
} else {
    echo json_encode(['error' => 'Usuario no encontrado']);
}

$stmt->close();
$conn->close();
?>
