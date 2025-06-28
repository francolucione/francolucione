<?php
require_once 'middleware.php';
try {
    class Turno {
        private $id;
        private $fecha;
        private $hora;
        private $usuario_id;
        private $mascota_id;
        private $estado;

        public function __construct($id, $fecha, $hora, $usuario_id, $mascota_id, $estado) {
            $this->id = $id;
            $this->fecha = $fecha;
            $this->hora = $hora;
            $this->usuario_id = $usuario_id;
            $this->mascota_id = $mascota_id;
            $this->estado = $estado;
        }

public static function generarTurnosDisponibles($dias = 15) {
    // Conectamos a la base de datos
    include("conexionBD.php");
    $conn = conectarBD();

    // Guardamos la fecha de hoy como texto (ej: "2025-06-16")
    $hoy = date("Y-m-d");

    // Inicializamos un contador de días hábiles ya generados
    $generados = 0;

    // Convertimos la fecha de hoy a un número de segundos (timestamp)
    $diaActual = strtotime($hoy);

    // Mientras no se hayan generado los turnos de los días hábiles necesarios
    while ($generados < $dias) {
        // Obtenemos el número del día de la semana (1=lunes, 7=domingo)
        $diaSemana = date("N", $diaActual);

        // Si es un día hábil (de lunes a viernes)
        if ($diaSemana < 6) {
            // Convertimos el día actual en formato texto para guardar en la base
            $fecha = date("Y-m-d", $diaActual);

            // Empezamos desde la hora 9 hasta la 17
            $hora = 9;
            while ($hora <= 17) {
                // Escribimos la hora en formato "09:00:00", "10:00:00", etc.
                $horaTexto = $hora . ":00:00";

                // Preparamos la consulta SQL para insertar un turno
                $sql = "INSERT INTO turnos (fecha, hora, usuario_id, mascota_id, estado) 
                        VALUES (?, ?, NULL, NULL, 'disponible')";

                // Preparamos la consulta para ejecutarla con valores variables
                $stmt = $conn->prepare($sql);

                // Le pasamos los valores reales: fecha y hora
                $stmt->bind_param("ss", $fecha, $horaTexto);

                // Ejecutamos la consulta (guarda un turno en la base)
                $stmt->execute();

                // Cerramos la consulta para seguir con la siguiente hora
                $stmt->close();

                // Pasamos a la hora siguiente
                $hora++;
            }

            // Terminamos de generar ese día, sumamos uno al contador
            $generados++;
        }

        // Avanzamos al día siguiente (en segundos)
        $diaActual = strtotime("+1 day", $diaActual);
    }

    // Cerramos la conexión a la base de datos
    $conn->close();

    // Devolvemos un mensaje de éxito
    return ["mensaje" => "Turnos generados correctamente"];
}

    public static function obtenerTurnos () {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("SELECT id, fecha, hora, usuario_id, mascota_id, estado FROM turnos");
        $stmt->execute();
        $resultado = $stmt->get_result();
        $turnos = [];
                while ($fila = $resultado->fetch_assoc()) {
        $turnos[] = $fila;
    }
        $stmt->close();
        $conn->close();

        if (count($turnos) < 1) {
            return "No hay turnos registrados";
        }
        return $turnos;
    }

public static function obtenerTurnosDisponibles () {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("SELECT id, fecha, hora, estado FROM turnos WHERE estado='disponible'");
        $stmt->execute();
        $resultado = $stmt->get_result();
        $turnos = [];
                while ($fila = $resultado->fetch_assoc()) {
        $turnos[] = $fila;
    }
        $stmt->close();
        $conn->close();

        if (count($turnos) < 1) {
            return "No hay turnos disponibles";
        }
        return $turnos;
}

public static function solicitarTurno($turno_id, $mascota_id) {
    include("conexionBD.php");
    $usuario_id = $_SESSION['id'];
        $conn = conectarBD();
        $stmt = $conn->prepare("UPDATE turnos SET usuario_id = ?, mascota_id = ?, estado = 'reservado' WHERE id = ? AND estado = 'disponible'");
        $stmt->bind_param("iii", $usuario_id, $mascota_id, $turno_id);
        $stmt->execute();
            if ($stmt->affected_rows > 0) {
        echo json_encode(['mensaje' => 'Turno reservado correctamente']);
    } else {
        echo json_encode(['error' => 'Turno no disponible o ya reservado']);
    }
    $stmt->close();
    $conn->close();
}

public static function misTurnos () {
    include("conexionBD.php");
    $usuario_id = $_SESSION['id'];
    $conn = conectarBD();
    $stmt = $conn->prepare("SELECT id, fecha, hora, estado, mascota_id  FROM turnos WHERE usuario_id= ? AND estado='reservado'");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $turnos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $turnos[] = $fila;
    }
    $stmt->close();
    $conn->close();

    return $turnos;
}

public static function cancelarTurno($turno_id) {
    include("conexionBD.php");
    $usuario_id = $_SESSION['id'];
        $conn = conectarBD();
        $stmt = $conn->prepare("UPDATE turnos SET estado = 'cancelado' WHERE id = ? AND usuario_id = ? AND estado = 'reservado'");
        $stmt->bind_param("ii", $turno_id, $usuario_id);
        $stmt->execute();
            if ($stmt->affected_rows > 0) {
        echo json_encode(['mensaje' => 'Turno cancelado correctamente']);
    } else {
        echo json_encode(['error' => 'Verifique el ID de su turno']);
    }
    $stmt->close();
    $conn->close();
}

public static function desactivarDias ($fecha) {
    include("conexionBD.php");
    $conn = conectarBD();
    $stmt = $conn->prepare("UPDATE turnos SET estado = 'INHABILITADO' WHERE fecha = ?");
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
                if ($stmt->affected_rows > 0) {
        echo json_encode(['mensaje' => 'Cambios Aplicados']);
    } else {
        echo json_encode(['error' => 'La fecha ingresada no es válida']);
    }
    $stmt->close();
    $conn->close();
}

public static function editarTurno ($id, $estado, $comentario) {
    include("conexionBD.php");
    $conn = conectarBD();
    $stmt = $conn->prepare("UPDATE turnos SET estado = ?, comentarios = ? WHERE id = ?");
    $stmt->bind_param("ssi", $estado, $comentario, $id);
        $stmt->execute();
                if ($stmt->affected_rows > 0) {
        echo json_encode(['mensaje' => 'Cambios Aplicados']);
    } else {
        echo json_encode(['error' => 'Los datos ingresados son erróneos']);
    }
    $stmt->close();
    $conn->close();
}

}

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>