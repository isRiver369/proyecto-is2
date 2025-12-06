<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/NotificacionService.php';

class PagoService {
    private $conn;
    private $notificador;

    // APLICACIÓN DE DIP (Dependency Inversion Principle):
    // No creamos las dependencias aquí dentro con 'new'.
    // Las pedimos como parámetros. Esto hace al código flexible y testeable.
    // Usamos null por defecto para mantener compatibilidad si no se pasan.
    public function __construct($dbConn = null, $notificador = null) {
        
        // 1. Inyección de Base de Datos
        if ($dbConn == null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $dbConn;
        }

        // 2. Inyección del Notificador
        if ($notificador == null) {
            $this->notificador = new NotificacionService();
        } else {
            $this->notificador = $notificador;
        }
    }

    // 1. Obtener datos de la reserva
    public function obtenerReservaParaPago($reserva_id, $usuario_id) {
        $query = "SELECT r.*, s.nombre_servicio, s.precio 
                  FROM reservas r 
                  INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                  WHERE r.reserva_id = :rid AND r.usuario_id = :uid LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rid", $reserva_id);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Procesar el pago
    // APLICACIÓN DE SRP (Single Responsibility): Este método solo coordina la transacción.
    public function registrarPago($reserva_id, $monto, $metodo) {
        try {
            $this->conn->beginTransaction();

            // A. Registrar el pago
            $queryPago = "INSERT INTO pagos (reserva_id, monto, metodo_pago, estado_pago) 
                          VALUES (:rid, :monto, :metodo, 'aprobado')";
            $stmt = $this->conn->prepare($queryPago);
            $stmt->execute([
                ':rid' => $reserva_id,
                ':monto' => $monto,
                ':metodo' => $metodo
            ]);

            // B. Actualizar reserva
            $queryReserva = "UPDATE reservas SET estado = 'pagada', pagado = 1 WHERE reserva_id = :rid";
            $stmtUpdate = $this->conn->prepare($queryReserva);
            $stmtUpdate->execute([':rid' => $reserva_id]);

            // C. Notificar (Usando la dependencia inyectada)
            $this->notificarUsuario($reserva_id, $monto);

            $this->conn->commit();
            return ["success" => true];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Método auxiliar privado para manejar la lógica de notificación
    // Ayuda a mantener limpio registrarPago()
    private function notificarUsuario($reserva_id, $monto) {
        $queryUser = "SELECT u.email, u.nombre FROM usuarios u 
                      INNER JOIN reservas r ON u.usuario_id = r.usuario_id 
                      WHERE r.reserva_id = :rid";
        $stmtUser = $this->conn->prepare($queryUser);
        $stmtUser->execute([':rid' => $reserva_id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // USAMOS LA PROPIEDAD INYECTADA, NO 'new'
            $mensaje = "Hola {$user['nombre']}, tu pago de $$monto para la reserva #$reserva_id ha sido confirmado.";
            $this->notificador->enviarEmail($user['email'], "Pago Exitoso - ProServicios", $mensaje);
        }
    }

    // 3. Obtener comprobante
    public function obtenerComprobante($reserva_id, $usuario_id) {
        $query = "SELECT p.pago_id, p.monto, p.metodo_pago, r.fecha_reserva, r.reserva_id,
                         s.nombre_servicio, u.nombre, u.apellido, u.email 
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