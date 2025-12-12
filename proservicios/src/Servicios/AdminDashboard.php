<?php
// Archivo: src/Servicios/AdminDashboard.php
require_once __DIR__ . '/../../config/Database.php';

class AdminDashboard {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    // Obtener los contadores de las tarjetas (KPIs)
    public function obtenerEstadisticas() {
        $stats = [];

        // 1. Total Usuarios
        $sql = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->conn->query($sql);
        $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Ingresos Totales (Suma de pagos aprobados)
        // Nota: Si quieres solo "Hoy", añade WHERE DATE(fecha_pago) = CURDATE()
        $sql = "SELECT SUM(monto) as total FROM pagos WHERE estado_pago = 'aprobado'";
        $stmt = $this->conn->query($sql);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['ingresos'] = $total ? $total : 0; // Si es null, poner 0

        // 3. Reservas Pendientes
        $sql = "SELECT COUNT(*) as total FROM reservas WHERE estado = 'pendiente'";
        $stmt = $this->conn->query($sql);
        $stats['pendientes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 4. Servicios Activos (disponible = 1)
        $sql = "SELECT COUNT(*) as total FROM servicios WHERE disponible = 1";
        $stmt = $this->conn->query($sql);
        $stats['servicios_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }
    // Obtener las últimas 5 reservas con datos cruzados (JOINs)
    public function obtenerUltimasReservas() {
        $sql = "SELECT 
                    r.reserva_id, 
                    r.fecha_reserva, 
                    r.estado, 
                    u.nombre AS nombre_cliente, 
                    u.apellido AS apellido_cliente,
                    s.nombre_servicio
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.usuario_id
                JOIN servicios s ON r.servicio_id = s.servicio_id
                ORDER BY r.reserva_id DESC 
                LIMIT 5";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Función para eliminar una reserva por su ID
    public function eliminarReserva($id) {
        try {
            // OPCIONAL: Si tu base de datos no tiene "ON DELETE CASCADE",
            // primero deberíamos borrar los pagos asociados para evitar errores.
            $sqlPagos = "DELETE FROM pagos WHERE reserva_id = :id";
            $stmtPagos = $this->conn->prepare($sqlPagos);
            $stmtPagos->bindParam(':id', $id);
            $stmtPagos->execute();

            // Ahora sí, borramos la reserva
            $sql = "DELETE FROM reservas WHERE reserva_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute(); // Devuelve true si funcionó
        } catch (PDOException $e) {
            error_log("Error al eliminar reserva: " . $e->getMessage());
            return false;
        }
    }
    // Obtener lista completa de usuarios
    public function obtenerTodosLosUsuarios() {
        try {
            $sql = "SELECT usuario_id, nombre, apellido, email, rol, fecha_registro 
                    FROM usuarios 
                    ORDER BY fecha_registro DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    // Cambiar el rol de un usuario
    public function cambiarRolUsuario($id, $nuevoRol) {
        try {
            // Validamos que el rol sea uno de los permitidos para evitar errores
            $rolesPermitidos = ['cliente', 'proveedor', 'administrador'];
            if (!in_array($nuevoRol, $rolesPermitidos)) return false;

            $sql = "UPDATE usuarios SET rol = :rol WHERE usuario_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':rol', $nuevoRol);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // 1. Obtener lista de todos los servicios con el nombre del proveedor
    public function obtenerTodosLosServicios() {
        try {
            // Usamos LEFT JOIN para traer datos aunque el proveedor se haya borrado
            $sql = "SELECT s.*, u.nombre as p_nombre, u.apellido as p_apellido 
                    FROM servicios s 
                    LEFT JOIN usuarios u ON s.proveedor_id = u.usuario_id 
                    ORDER BY s.servicio_id DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    // 2. Interruptor para Activar/Desactivar un servicio
    public function toggleEstadoServicio($id) {
        try {
            // Este truco SQL (NOT disponible) invierte el valor: si es 1 lo hace 0, y viceversa.
            $sql = "UPDATE servicios SET disponible = NOT disponible WHERE servicio_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // Obtener lista solo de proveedores (para el formulario de crear servicio)
    public function obtenerProveedores() {
        try {
            $sql = "SELECT usuario_id, nombre, apellido FROM usuarios WHERE rol = 'proveedor' OR rol = 'administrador'";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    // Crear un nuevo servicio en la BD
    public function crearServicio($proveedor_id, $nombre, $desc, $precio) {
        try {
            $sql = "INSERT INTO servicios (proveedor_id, nombre_servicio, descripcion, precio, disponible, cupo_maximo) 
                    VALUES (:pid, :nom, :desc, :precio, 1, 10)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pid', $proveedor_id);
            $stmt->bindParam(':nom', $nombre);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':precio', $precio);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error crear servicio: " . $e->getMessage());
            return false;
        }
    }
    // Obtener ingresos agrupados por mes (CORREGIDO)
    public function obtenerIngresosPorMes() {
        try {
            // Unimos PAGOS con RESERVAS para obtener la fecha
            $sql = "SELECT DATE_FORMAT(r.fecha_reserva, '%Y-%m') as mes, SUM(p.monto) as total 
                    FROM pagos p
                    JOIN reservas r ON p.reserva_id = r.reserva_id
                    WHERE p.estado_pago = 'aprobado'
                    GROUP BY mes
                    ORDER BY mes ASC
                    LIMIT 6";
            
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error SQL: " . $e->getMessage());
            return [];
        }
    }
    // Obtener los 5 servicios con más reservas
    public function obtenerTopServicios() {
        try {
            $sql = "SELECT s.nombre_servicio, COUNT(r.reserva_id) as cantidad
                    FROM reservas r
                    JOIN servicios s ON r.servicio_id = s.servicio_id
                    GROUP BY s.servicio_id, s.nombre_servicio
                    ORDER BY cantidad DESC
                    LIMIT 5";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    // 1. Obtener un solo servicio por ID (Para llenar el formulario de edición)
    public function obtenerServicioPorId($id) {
        $sql = "SELECT * FROM servicios WHERE servicio_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Actualizar un servicio existente
    public function actualizarServicio($id, $nombre, $desc, $precio) {
        try {
            $sql = "UPDATE servicios 
                    SET nombre_servicio = :nom, 
                        descripcion = :desc, 
                        precio = :precio 
                    WHERE servicio_id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nom', $nombre);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // Obtener una reserva específica por ID (Para editarla)
    public function obtenerReservaPorId($id) {
        $sql = "SELECT * FROM reservas WHERE reserva_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Guardar cambios de la reserva
    public function actualizarReserva($id, $fecha, $estado, $total) {
        try {
            $sql = "UPDATE reservas 
                    SET fecha_reserva = :fecha, 
                        estado = :estado, 
                        total_pagar = :total 
                    WHERE reserva_id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // --- MÓDULO CATEGORÍAS ---
    // Obtener todas las categorías
    public function obtenerCategorias() {
        $sql = "SELECT * FROM categorias ORDER BY categoria_id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Crear nueva categoría
    public function crearCategoria($nombre) {
        try {
            $sql = "INSERT INTO categorias (nombre_categoria) VALUES (:nombre)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // Eliminar categoría
    public function eliminarCategoria($id) {
        try {
            $sql = "DELETE FROM categorias WHERE categoria_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    // --- MÓDULO PROVEEDORES ---
    // Obtener lista de proveedores con conteo de sus servicios
    public function obtenerProveedoresDetallados() {
        try {
            // Usamos LEFT JOIN para contar servicios. Si no tienen, saldrá 0.
            $sql = "SELECT u.*, COUNT(s.servicio_id) as cantidad_servicios
                    FROM usuarios u
                    LEFT JOIN servicios s ON u.usuario_id = s.proveedor_id
                    WHERE u.rol = 'proveedor'
                    GROUP BY u.usuario_id
                    ORDER BY cantidad_servicios DESC"; // Los más activos primero
            
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    // --- MÓDULO CONFIGURACIÓN ---
    // 1. Obtener la configuración actual
    public function obtenerConfiguracion() {
        // Siempre traemos la fila con ID 1
        $sql = "SELECT * FROM configuracion WHERE id = 1";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 2. Guardar cambios globales
    public function actualizarConfiguracion($nombre, $email, $impuesto, $moneda, $mantenimiento) {
        try {
            $sql = "UPDATE configuracion 
                    SET nombre_sitio = :nom, 
                        email_admin = :email, 
                        tasa_impuesto = :tax,
                        moneda = :mon,
                        modo_mantenimiento = :mant
                    WHERE id = 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nom', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':tax', $impuesto);
            $stmt->bindParam(':mon', $moneda);
            $stmt->bindParam(':mant', $mantenimiento);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>