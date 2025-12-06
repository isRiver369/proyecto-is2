<?php
// Archivo: src/Servicios/AuthService.php
require_once __DIR__ . '/../../config/Database.php';

class AuthService {
    private $conn;
    private $table_name = "usuarios";

    // APLICANDO DIP (Inyección de Dependencias)
    // Permitimos pasar la conexión desde fuera. Si no se pasa, creamos una por defecto.
    public function __construct($dbConn = null) {
        if ($dbConn == null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $dbConn;
        }
    }

    // Registrar un nuevo usuario 
    public function registrar($nombre, $apellido, $email, $password, $telefono) {
        // Validaciones Regex (Igual que tenías, están perfectas)
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) return ["success" => false, "message" => "El nombre solo puede contener letras."];
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellido)) return ["success" => false, "message" => "El apellido solo puede contener letras."];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ["success" => false, "message" => "El formato del correo no es válido."];
        if (!preg_match("/^[0-9]{10}$/", $telefono)) return ["success" => false, "message" => "El teléfono debe tener 10 dígitos numéricos."];

        // 1. Verificar si el email existe 
        $queryCheck = "SELECT usuario_id FROM " . $this->table_name . " WHERE email = ?";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->execute([$email]);
        
        if($stmtCheck->rowCount() > 0){
            return ["success" => false, "message" => "Este correo ya está registrado."];
        }

        // 2. Insertar usuario 
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, apellido, email, password_hash, telefono, rol) 
                  VALUES (:nombre, :apellido, :email, :password, :telefono, 'cliente')";

        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
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
        $query = "SELECT usuario_id, nombre, apellido, password_hash, rol, email FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['password_hash'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // SEGURIDAD: Prevenir fijación de sesión
                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $row['usuario_id'];
                $_SESSION['nombre_completo'] = $row['nombre'] . " " . $row['apellido'];
                $_SESSION['email'] = $row['email']; // Útil para otros procesos
                $_SESSION['rol'] = $row['rol'];
                
                return ["success" => true, "message" => "Bienvenido " . $row['nombre']];
            }
        }
        return ["success" => false, "message" => "Correo o contraseña incorrectos."];
    }

    // Obtener todos los datos de un usuario por su ID
    // (Reemplaza a obtenerDatosUsuario para evitar duplicidad)
    public function obtenerUsuarioPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar datos básicos (Cliente)
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
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['nombre_completo'] = $nombre . " " . $apellido;
            return ["success" => true, "message" => "Datos actualizados correctamente."];
        }
        return ["success" => false, "message" => "Error al actualizar los datos."];
    }

    // Actualizar perfil extendido (Proveedor)
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

    // Cerrar Sesión
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
    }
}
?>