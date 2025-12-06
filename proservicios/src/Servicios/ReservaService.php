<?php
// Archivo: src/Servicios/ReservaService.php
require_once __DIR__ . '/../../config/Database.php';

class ReservaService {
    private $conn;
    private $table_name = "reservas";

    // APLICANDO SOLID (DIP): Inyección de Dependencias
    // La conexión viene de fuera, haciendo la clase más flexible y testeable.
    public function __construct($dbConn = null) {
        if ($dbConn == null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $dbConn;
        }
    }

    // 1. Crear una nueva reserva con VALIDACIÓN DE CUPO y HORARIO
    public function crearReserva($usuario_id, $servicio_id, $fecha, $total, $horario_elegido) {
        
        // A. VERIFICAR CUPO DISPONIBLE
        $queryCupo = "SELECT cupo_maximo FROM servicios WHERE servicio_id = :sid";
        $stmtCupo = $this->conn->prepare($queryCupo);
        $stmtCupo->execute([':sid' => $servicio_id]);
        $rowServicio = $stmtCupo->fetch(PDO::FETCH_ASSOC);
        
        if (!$rowServicio) {
            return ["success" => false, "message" => "Servicio no encontrado."];
        }
        $cupoMaximo = $rowServicio['cupo_maximo'];

        // Contar reservas activas (excluyendo canceladas)
        $queryCount = "SELECT COUNT(*) as ocupados FROM " . $this->table_name . " 
                       WHERE servicio_id = :sid 
                       AND estado != 'cancelada'";
        
        $stmtCount = $this->conn->prepare($queryCount);
        $stmtCount->execute([':sid' => $servicio_id]);
        $rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC);
        
        if ($rowCount['ocupados'] >= $cupoMaximo) {
            return ["success" => false, "message" => "Lo sentimos, no hay cupo para este curso. ($cupoMaximo/$cupoMaximo ocupados)"];
        }

        // B. SI HAY CUPO, INSERTAMOS LA RESERVA
        // Nota: Asegúrate de que la columna 'horario_elegido' exista en tu BD
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, servicio_id, fecha_reserva, total_pagar, estado, horario_elegido) 
                  VALUES (:uid, :sid, :fecha, :total, 'pendiente', :horario)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":total", $total);
        $stmt->bindParam(":horario", $horario_elegido);

        if($stmt->execute()) {
            return ["success" => true, "message" => "Reserva creada exitosamente."];
        }
        return ["success" => false, "message" => "Error técnico al guardar la reserva."];
    }

    // 2. Leer reservas de un usuario específico
    public function obtenerReservasPorUsuario($usuario_id) {
        $query = "SELECT r.reserva_id, r.fecha_reserva, r.estado, r.total_pagar, r.horario_elegido, s.nombre_servicio 
                  FROM " . $this->table_name . " r
                  INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                  WHERE r.usuario_id = :uid
                  ORDER BY r.reserva_id DESC"; // Ordenar por ID suele ser mejor para ver lo último creado

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->execute();
        
        return $stmt; 
    }

    // 3. Cancelar una reserva
    public function cancelarReserva($reserva_id, $usuario_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'cancelada' 
                  WHERE reserva_id = :rid AND usuario_id = :uid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rid", $reserva_id);
        $stmt->bindParam(":uid", $usuario_id);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return ["success" => true, "message" => "Reserva cancelada correctamente."];
            } else {
                return ["success" => false, "message" => "No se encontró la reserva o no se puede cancelar."];
            }
        }
        return ["success" => false, "message" => "Error de base de datos."];
    }

    // 4. Obtener reservas de todos los servicios que pertenezcan a un proveedor
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
    
    // Contar reservas activas por servicio (Útil para el panel de proveedor o catálogo)
    public function contarReservasPorServicio($servicio_id) {
        $query = "SELECT COUNT(*) FROM reservas WHERE servicio_id = :id AND estado != 'cancelada'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $servicio_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>