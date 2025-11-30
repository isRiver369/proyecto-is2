<?php
// Archivo: src/Servicios/AuthService.php
require_once __DIR__ . '/../../config/Database.php';

class AuthService {
    private $conn;
    private $table_name = "usuarios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Registrar un nuevo usuario 
    public function registrar($nombre, $apellido, $email, $password, $telefono) {
        // Validar que nombre y apellido solo contengan letras y espacios
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
            return ["success" => false, "message" => "El nombre solo puede contener letras."];
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellido)) {
            return ["success" => false, "message" => "El apellido solo puede contener letras."];
        }
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "El formato del correo no es válido."];
        }
        // Validar que el teléfono tenga solo números y 10 dígitos
        if (!preg_match("/^[0-9]{10}$/", $telefono)) {
            return ["success" => false, "message" => "El teléfono debe tener 10 dígitos numéricos."];
        }
        // 1. Verificar si el email existe 
        $queryCheck = "SELECT usuario_id FROM " . $this->table_name . " WHERE email = ?";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->execute([$email]);
        
        if($stmtCheck->rowCount() > 0){
            return ["success" => false, "message" => "Este correo ya está registrado."];
        }

        // 2. Insertar usuario 
        // Ya no hacemos explode() porque recibimos $nombre y $apellido puros.
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, apellido, email, password_hash, telefono, rol) 
                  VALUES (:nombre, :apellido, :email, :password, :telefono, 'cliente')";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Asignamos directamente
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellido", $apellido);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":telefono", $telefono);

        if($stmt->execute()) {
            return ["success" => true, "message" => "Usuario registrado con éxito."];
        }

        return ["success" => false, "message" => "Error en la base de datos."];
    }

    // Iniciar Sesión
    public function login($email, $password) {
        $query = "SELECT usuario_id, nombre, apellido, password_hash, rol FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificar si la contraseña coincide con el hash
            if (password_verify($password, $row['password_hash'])) {
                // Iniciar la sesión de PHP
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['usuario_id'] = $row['usuario_id'];
                $_SESSION['nombre_completo'] = $row['nombre'] . " " . $row['apellido'];
                $_SESSION['email'] = $email;
                $_SESSION['rol'] = $row['rol'];
                
                return ["success" => true, "message" => "Bienvenido " . $row['nombre']];
            }
        }
        return ["success" => false, "message" => "Correo o contraseña incorrectos."];
    }
    // 1. Obtener datos actuales del usuario
    public function obtenerDatosUsuario($usuario_id) {
        $query = "SELECT nombre, apellido, email, telefono FROM " . $this->table_name . " WHERE usuario_id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $usuario_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Actualizar datos
    public function actualizarPerfil($usuario_id, $nombre, $apellido, $telefono) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, apellido = :apellido, telefono = :telefono 
                  WHERE usuario_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellido", $apellido);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":id", $usuario_id);

        if ($stmt->execute()) {
            // Actualizar la sesión para que el nombre cambie en el menú inmediatamente
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['nombre_completo'] = $nombre . " " . $apellido;
            return ["success" => true, "message" => "Datos actualizados correctamente."];
        }
        return ["success" => false, "message" => "Error al actualizar los datos."];
    }
    // Cerrar Sesión
    public function logout() {
        session_start();
        session_destroy();
    }
    // Obtener todos los datos de un usuario por su ID
    public function obtenerUsuarioPorId($id) {
        // Seleccionamos también bio y portafolio_url por si es proveedor
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // (Opcional) Método para actualizar perfil de proveedor con Bio
    // Esto servirá para el formulario de "Mi Perfil" en el panel
    public function actualizarPerfilProveedor($id, $bio, $portafolio) {
        $query = "UPDATE " . $this->table_name . " 
                  SET bio = :bio, portafolio_url = :url 
                  WHERE usuario_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":url", $portafolio);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
}
?>