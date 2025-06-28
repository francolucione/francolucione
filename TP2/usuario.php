<?php
try {
    class Usuario {
        private $id;
        private $nombre;
        private $email;
        private $clave;
        private $rol;

        public function __construct($id, $nombre, $email, $clave, $rol) {
            $this->id = $id;
            $this->nombre = $nombre;
            $this->email = $email;
            $this->clave = $clave;
            $this->rol = $rol;
        }

    public static function crearUsuario ($nombre, $email, $clave, $rol) {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, clave, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $clave, $rol);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }    

    public static function obtenerUsuarios () {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("SELECT id, nombre, email, clave, rol FROM usuarios");
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarios = [];
                while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }
        $stmt->close();
        $conn->close();

        if (count($usuarios) < 1) {
            return "No hay usuarios registrados";
        }
        return $usuarios;
    }

    public static function editarUsuario ($id, $valorEditado, $nuevoValor) {
        include("conexionBD.php");
        $conn = conectarBD();
                if ($valorEditado === 'nombre') {
        $stmt = $conn->prepare("UPDATE `usuarios` SET `nombre`= ? WHERE id = ?");
        $stmt->bind_param("si", $nuevoValor, $id);
        }
                if ($valorEditado === 'email') {
        $stmt = $conn->prepare("UPDATE `usuarios` SET `email`= ? WHERE id = ?");
        $stmt->bind_param("si", $nuevoValor, $id);            
                }
                if ($valorEditado === 'clave') {
        $nuevoValor = password_hash($nuevoValor, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE `usuarios` SET `clave`= ? WHERE id = ?");
        $stmt->bind_param("si", $nuevoValor, $id);            
                }

        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    public static function borrarUsuario ($id) {
        include("conexionBD.php");
        $conn = conectarBD();
        $stmt = $conn->prepare("DELETE FROM `usuarios` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>