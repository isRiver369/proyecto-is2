<?php
// Archivo: src/Servicios/PagoService.php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/NotificacionService.php';

class PagoService {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. Obtener datos de la reserva para mostrar el total
    public function obtenerReservaParaPago($reserva_id, $usuario_id) {
        $query = "SELECT r.*, s.nombre_servicio 
                  FROM reservas r 
                  INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                  WHERE r.reserva_id = :rid AND r.usuario_id = :uid LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rid", $reserva_id);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Procesar el pago (Transacción completa)
    public function registrarPago($reserva_id, $monto, $metodo) {
        try {
            // Iniciar transacción (todo o nada)
            $this->conn->beginTransaction();

            // A. Insertar en tabla pagos
            $queryPago = "INSERT INTO pagos (reserva_id, monto, metodo_pago, estado_pago) 
                          VALUES (:rid, :monto, :metodo, 'aprobado')";
            $stmt = $this->conn->prepare($queryPago);
            $stmt->execute([
                ':rid' => $reserva_id,
                ':monto' => $monto,
                ':metodo' => $metodo
            ]);

            // B. Actualizar estado de la reserva a 'pagada'
            $queryReserva = "UPDATE reservas SET estado = 'pagada' WHERE reserva_id = :rid";
            $stmtUpdate = $this->conn->prepare($queryReserva);
            $stmtUpdate->execute([':rid' => $reserva_id]);

            // --- Notifiacion ---
            // 1. Obtener email del usuario para notificarle
            $queryUser = "SELECT u.email, u.nombre FROM usuarios u 
                          INNER JOIN reservas r ON u.usuario_id = r.usuario_id 
                          WHERE r.reserva_id = :rid";
            $stmtUser = $this->conn->prepare($queryUser);
            $stmtUser->execute([':rid' => $reserva_id]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $notificador = new NotificacionService();
                $mensaje = "Hola {$user['nombre']}, tu pago de $$monto para la reserva #$reserva_id ha sido confirmado exitosamente.";
                $notificador->enviarEmail($user['email'], "Confirmación de Pago - ProServicios", $mensaje);
            }
            // ------------------------------------
            // Confirmar cambios
            $this->conn->commit();
            return ["success" => true];

        } catch (Exception $e) {
            $this->conn->rollBack(); // Deshacer si algo falla
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
    // 3. Obtener el comprobante de un pago realizado
    public function obtenerComprobante($reserva_id, $usuario_id) {
        $query = "SELECT p.*, r.fecha_reserva, s.nombre_servicio, u.nombre, u.apellido, u.email 
                  FROM pagos p
                  INNER JOIN reservas r ON p.reserva_id = r.reserva_id
                  INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                  INNER JOIN usuarios u ON r.usuario_id = u.usuario_id
                  WHERE r.reserva_id = :rid AND r.usuario_id = :uid 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rid", $reserva_id);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>