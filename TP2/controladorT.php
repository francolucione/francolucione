    <?php
require_once 'middleware.php';    
require_once 'turno.php';
header('Content-Type: application/json');
    try {
    $data = json_decode(file_get_contents("php://input"), true);
    $method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
        if (esAdmin() === false) {
        echo json_encode(['error' => 'Acceso restringido.']);
        exit;
                                    } 
        echo json_encode(Turno::generarTurnosDisponibles());
        exit;
    }

if ($method === 'GET' && isset($_GET['misTurnos'])) {
    if (estaLogueado() === false) {
        echo json_encode(['error' => 'Debe iniciar sesión.']);
        exit;
    }
    $turnos = Turno::misTurnos();
    if (count($turnos) > 0) {
        echo json_encode([
            'mensaje' => 'Sus turnos reservados son los siguientes:',
            'turnos' => $turnos
        ]);
    } else {
        echo json_encode(['error' => 'Usted aún no ha reservado un turno']);
    }
    exit;
}

    if ($method === 'GET') {
        if (esAdmin() === false && estaLogueado() === true) {
            echo json_encode(['mensaje' => 'Los turnos disponibles son los siguientes. Porfavor, conserve el ID del que desea reservar: ']);
            echo json_encode(Turno::obtenerTurnosDisponibles());
            exit;
        }
        if (esAdmin() === false && estaLogueado() === false) {
            echo json_encode(['error' => 'Ingrese para ver sus turnos.']);
        }
        if (esAdmin() === true){
        echo json_encode(Turno::obtenerTurnos());
        exit;}
    }

if ($method === 'PUT' && isset($_GET['editar'])) {
    if ((esAdmin()) === false) {
        echo json_encode(['error' => 'Solo los administradores pueden editar turnos']);
        exit;
    }

    if (!$data || !isset($data['id'], $data['estado'], $data['comentario'])) {
        echo json_encode(['error' => 'Faltan datos obligatorios']);
        exit;
    }

    $id = $data['id'];
    $estado = $data['estado'];
    $comentario = $data['comentario'];

    Turno::editarTurno($id, $estado, $comentario);
    exit;
}

if ($method === 'PUT' && isset($_GET['inhabilitar'])) {
    if (esAdmin() === false) {
        echo json_encode(['error' => 'Acceso restringido a administradores.']);
        exit;
    }

    if (!$data || !isset($data['fecha'])) {
        echo json_encode(['error' => 'Debe proporcionar una fecha.']);
        exit;
    }

    $fecha = $data['fecha'];
    Turno::desactivarDias($fecha);
    exit;
}


if ($method === 'PUT' && isset($_GET['cancelar'])) {
    if (estaLogueado() === false) {
        echo json_encode(['error' => 'Debe iniciar sesión para cancelar un turno.']);
        exit;
    }

    if (!$data || !isset($data['turno_id'])) {
        echo json_encode(['error' => 'Falta el ID del turno.']);
        exit;
    }

    $turno_id = $data['turno_id'];
    Turno::cancelarTurno($turno_id);
    exit;
}
    

if ($method === 'PUT') {
    if (!estaLogueado()) {
        echo json_encode(['error' => 'Debe iniciar sesión para reservar un turno.']);
        exit;
    }

    if (!$data || !isset($data['turno_id'], $data['mascota_id'])) {
        echo json_encode(['error' => 'Faltan datos obligatorios']);
        exit;
    }

    $turno_id = $data['turno_id'];
    $mascota_id = $data['mascota_id'];

    Turno::solicitarTurno($turno_id, $mascota_id);
    exit;
}

    } catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}