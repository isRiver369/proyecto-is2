<?php
require_once __DIR__ . '/../../config/Database.php';

class ReservaService {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. CREAR RESERVA (Con transacción y resta de cupos)
    public function crearReserva($usuario_id, $servicio_id, $fecha, $horario, $precio) {
        try {
            $this->conn->beginTransaction();

            // Verificar disponibilidad y bloquear fila
            $queryCheck = "SELECT cupos_restantes, disponible FROM servicios WHERE servicio_id = :sid FOR UPDATE";
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->bindParam(":sid", $servicio_id);
            $stmtCheck->execute();
            $servicio = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$servicio) {
                $this->conn->rollBack();
                throw new Exception("El servicio no existe.");
            }

            if ($servicio['cupos_restantes'] <= 0 || $servicio['disponible'] == 0) {
                $this->conn->rollBack();
                return "sin_cupo"; 
            }

            // Restar cupo
            $queryUpdate = "UPDATE servicios SET cupos_restantes = cupos_restantes - 1 WHERE servicio_id = :sid";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(":sid", $servicio_id);
            $stmtUpdate->execute();

            // Insertar reserva
            $queryInsert = "INSERT INTO reservas (usuario_id, servicio_id, fecha_reserva, horario_elegido, total_pagar, estado) 
                            VALUES (:uid, :sid, :fecha, :hor, :total, 'pendiente')";
            
            $stmtInsert = $this->conn->prepare($queryInsert);
            $stmtInsert->bindParam(":uid", $usuario_id);
            $stmtInsert->bindParam(":sid", $servicio_id);
            $stmtInsert->bindParam(":fecha", $fecha);
            $stmtInsert->bindParam(":hor", $horario);
            $stmtInsert->bindParam(":total", $precio);
            
            if ($stmtInsert->execute()) {
                $reserva_id = $this->conn->lastInsertId();
                $this->conn->commit();
                return $reserva_id;
            } else {
                $this->conn->rollBack();
                return false;
            }

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    // 2. OBTENER RESERVA INDIVIDUAL (Para Pagos)
    public function obtenerReservaPorId($reserva_id) {
        $sql = "SELECT r.*, s.nombre_servicio, s.descripcion 
                FROM reservas r
                JOIN servicios s ON r.servicio_id = s.servicio_id
                WHERE r.reserva_id = :rid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':rid', $reserva_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Obtener reservas de todos los servicios que pertenezcan a un proveedor
    public function obtenerReservasPorProveedor($proveedor_id) {
        $query = "SELECT 
                    r.reserva_id,
                    r.fecha_reserva,
                    r.estado,
                    r.total_pagar,
                    r.horario_elegido,
                    CONCAT(u.nombre, ' ', u.apellido) AS cliente,
                    s.nombre_servicio AS servicio_nombre
                FROM reservas r
                INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                INNER JOIN usuarios u ON r.usuario_id = u.usuario_id
                WHERE s.proveedor_id = :pid
                AND r.estado = 'pagada'
                ORDER BY r.fecha_reserva DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pid", $proveedor_id);
        $stmt->execute();
        
        return $stmt;
    }

    // 4. OBTENER LISTA DE RESERVAS POR USUARIO (Esta es la que faltaba)
    public function obtenerReservasPorUsuario($usuario_id) {
        // Hacemos JOIN con servicios para mostrar el nombre del curso
        // Hacemos LEFT JOIN con pagos para ver si ya pagó
        $sql = "SELECT r.*, s.nombre_servicio, p.estado_pago
                FROM reservas r
                JOIN servicios s ON r.servicio_id = s.servicio_id
                LEFT JOIN pagos p ON r.reserva_id = p.reserva_id
                WHERE r.usuario_id = :uid
                ORDER BY r.reserva_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':uid', $usuario_id);
        $stmt->execute();
        return $stmt; // Retornamos el statement para recorrerlo con while o fetchAll
    }
}
?>