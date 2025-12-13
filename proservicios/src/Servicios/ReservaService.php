<?php
require_once __DIR__ . '/../../config/Database.php';

class ReservaService {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. CREAR RESERVA (Resta 1 cupo y usa transacciones)
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

            // RESTAR cupo
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

    // 2. OBTENER RESERVA POR ID (Para pantalla de Pagos)
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

    // 3. LISTAR RESERVAS POR CLIENTE (Para 'Mis Reservas')
    public function obtenerReservasPorUsuario($usuario_id) {
        $sql = "SELECT r.*, s.nombre_servicio, p.estado_pago
                FROM reservas r
                JOIN servicios s ON r.servicio_id = s.servicio_id
                LEFT JOIN pagos p ON r.reserva_id = p.reserva_id
                WHERE r.usuario_id = :uid
                ORDER BY r.reserva_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':uid', $usuario_id);
        $stmt->execute();
        return $stmt;
    }

    // 4. LISTAR RESERVAS POR PROVEEDOR (NUEVA: Para 'Panel Proveedor')
    // Esta función busca todas las reservas de los servicios que pertenecen al proveedor
    public function obtenerReservasPorProveedor($proveedor_id) {
        $sql = "SELECT r.*, s.nombre_servicio, u.nombre as nombre_cliente, u.apellido as apellido_cliente, u.email as email_cliente, p.estado_pago
                FROM reservas r
                JOIN servicios s ON r.servicio_id = s.servicio_id
                JOIN usuarios u ON r.usuario_id = u.usuario_id
                LEFT JOIN pagos p ON r.reserva_id = p.reserva_id
                WHERE s.proveedor_id = :pid
                ORDER BY r.reserva_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':pid', $proveedor_id);
        $stmt->execute();
        return $stmt;
    }

    // 5. CANCELAR RESERVA (Devuelve el cupo)
    public function cancelarReserva($reserva_id, $usuario_id) {
        try {
            $this->conn->beginTransaction();

            $sqlGet = "SELECT servicio_id, estado FROM reservas WHERE reserva_id = :rid AND usuario_id = :uid FOR UPDATE";
            $stmtGet = $this->conn->prepare($sqlGet);
            $stmtGet->bindParam(':rid', $reserva_id);
            $stmtGet->bindParam(':uid', $usuario_id);
            $stmtGet->execute();
            $reserva = $stmtGet->fetch(PDO::FETCH_ASSOC);

            if (!$reserva) {
                $this->conn->rollBack();
                return false;
            }

            if ($reserva['estado'] === 'cancelada') {
                $this->conn->rollBack();
                return true;
            }

            // Devolver Cupo
            $sqlUpdateService = "UPDATE servicios SET cupos_restantes = cupos_restantes + 1 WHERE servicio_id = :sid";
            $stmtUpd = $this->conn->prepare($sqlUpdateService);
            $stmtUpd->bindParam(':sid', $reserva['servicio_id']);
            $stmtUpd->execute();

            // Cancelar Reserva
            $sqlCancel = "UPDATE reservas SET estado = 'cancelada' WHERE reserva_id = :rid";
            $stmtCancel = $this->conn->prepare($sqlCancel);
            $stmtCancel->bindParam(':rid', $reserva_id);
            $stmtCancel->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }
}
?>