<?php
require_once __DIR__ . '/../../config/Database.php';

class PagoService {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. OBTENER DATOS PARA PAGAR (Pantalla de Checkout)
    public function obtenerReservaParaPago($reserva_id, $usuario_id) {
        $sql = "SELECT r.*, s.nombre_servicio, s.descripcion, u.nombre, u.apellido, u.email, u.telefono 
                FROM reservas r
                JOIN servicios s ON r.servicio_id = s.servicio_id
                JOIN usuarios u ON r.usuario_id = u.usuario_id
                WHERE r.reserva_id = :rid AND r.usuario_id = :uid";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':rid', $reserva_id);
        $stmt->bindParam(':uid', $usuario_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. REGISTRAR EL PAGO
    public function registrarPago($reserva_id, $monto, $metodo) {
        try {
            $this->conn->beginTransaction();

            $sqlPago = "INSERT INTO pagos (reserva_id, monto, metodo_pago, estado_pago) VALUES (:rid, :monto, :metodo, 'aprobado')";
            $stmt = $this->conn->prepare($sqlPago);
            $stmt->bindParam(':rid', $reserva_id);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':metodo', $metodo);
            $stmt->execute();

            $sqlReserva = "UPDATE reservas SET estado = 'pagada' WHERE reserva_id = :rid";
            $stmtRes = $this->conn->prepare($sqlReserva);
            $stmtRes->bindParam(':rid', $reserva_id);
            $stmtRes->execute();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Pago registrado'];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // 3. HISTORIAL DE PAGOS (Lista)
    public function obtenerHistorialPorUsuario($usuario_id) {
        $sql = "SELECT p.*, s.nombre_servicio, r.fecha_reserva, r.reserva_id
                FROM pagos p
                JOIN reservas r ON p.reserva_id = r.reserva_id
                JOIN servicios s ON r.servicio_id = s.servicio_id
                WHERE r.usuario_id = :uid
                ORDER BY p.pago_id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':uid', $usuario_id);
        $stmt->execute();
        return $stmt;
    }

    // 4. OBTENER COMPROBANTE INDIVIDUAL 
    public function obtenerComprobante($reserva_id) {
        // Traemos datos del pago, reserva, servicio, cliente y proveedor
        $sql = "SELECT 
                    p.pago_id, p.monto, p.metodo_pago, p.estado_pago,
                    r.fecha_reserva, r.horario_elegido, r.reserva_id,
                    s.nombre_servicio, s.descripcion, s.precio,
                    u.nombre as cliente_nombre, u.apellido as cliente_apellido, u.email as cliente_email,
                    prov.nombre as prov_nombre, prov.apellido as prov_apellido, prov.email as prov_email
                FROM pagos p
                JOIN reservas r ON p.reserva_id = r.reserva_id
                JOIN servicios s ON r.servicio_id = s.servicio_id
                JOIN usuarios u ON r.usuario_id = u.usuario_id
                JOIN usuarios prov ON s.proveedor_id = prov.usuario_id
                WHERE r.reserva_id = :rid";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':rid', $reserva_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>