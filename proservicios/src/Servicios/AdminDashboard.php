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
    // Obtener ingresos agrupados por mes (Últimos 6 meses)
    public function obtenerIngresosPorMes() {
        try {
            // DATE_FORMAT(fecha, '%Y-%m') agrupa por "2025-12", "2026-01", etc.
            $sql = "SELECT DATE_FORMAT(p.fecha_pago, '%Y-%m') as mes, SUM(p.monto) as total 
                    FROM pagos p
                    WHERE p.estado_pago = 'aprobado'
                    GROUP BY mes
                    ORDER BY mes ASC
                    LIMIT 6";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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
}
?>