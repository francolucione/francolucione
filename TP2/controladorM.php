    <?php
require_once 'mascota.php';
header('Content-Type: application/json');
    try {
    $data = json_decode(file_get_contents("php://input"), true);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        if (estaLogueado() === true && esAdmin() === false) {
            echo json_encode(Mascota::verMisMascotas($_SESSION['id'])); //ENCAJE SESS ID PARAM FUNC
            exit;
        }

            if (esAdmin() === false) {
            echo json_encode(['error' => 'Acceso restringido.']);
            exit;
        } 

        echo json_encode(Mascota::obtenerMascotas());
        exit;
    }

    if ($method === 'POST') {
        if (estaLogueado() === false) {
        echo json_encode(['error' => 'Para registrar a su mascota, por favor ingrese al sistema.']);
        exit;
        }

        if (!$data || !isset($data['nombre'], $data['especie'])) {
            echo json_encode(['error' => 'Faltan datos obligatorios']);
            exit;
        }
        $nombre = $data['nombre'];
        $especie = $data['especie'];
        $usuario_id = $_SESSION['id'];

        Mascota::crearMascota($nombre, $especie);
        echo json_encode(['mensaje' => 'Mascota registrada correctamente']);
        exit;
    }


    } catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}