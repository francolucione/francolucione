<?php
require_once 'middleware.php';
try {
    class Mascota {
        private $id;
        private $nombre;
        private $especie;
        private $usuario_id;

        public function __construct($id, $nombre, $especie, $usuario_id) {
            $this->id = $id;
            $this->nombre = $nombre;
            $this->especie = $especie;
            $this->usuario_id = $usuario_id;
        }

    public static function crearMascota ($nombre, $especie) {
        include("conexionBD.php");
            if (estaLogueado() === false) {
            echo json_encode(['error' => 'Por favor ingrese para registrar a su mascota.']);
            exit;
        } 
        $usuario_id = $_SESSION['id'];
        $conn = conectarBD();
        $stmt = $conn->prepare("INSERT INTO mascotas (nombre, especie, usuario_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nombre, $especie, $usuario_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }    

    public static function obtenerMascotas () {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("SELECT id, nombre, especie, usuario_id FROM mascotas");
        $stmt->execute();
        $resultado = $stmt->get_result();
        $mascotas = [];
                while ($fila = $resultado->fetch_assoc()) {
        $mascotas[] = $fila;
    }
        $stmt->close();
        $conn->close();

        if (count($mascotas) < 1) {
            return "No hay mascotas registrados";
        }
        return $mascotas;
    }

    public static function verMisMascotas ($id) {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("SELECT id, nombre, especie FROM mascotas WHERE usuario_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $mascotas = [];
                while ($fila = $resultado->fetch_assoc()) {
        $mascotas[] = $fila;
    }
        $stmt->close();
        $conn->close();

        if (count($mascotas) < 1) {
            return "No hay mascotas registrados";
        }
        return $mascotas;
    }


}

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>