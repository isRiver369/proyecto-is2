<?php
// Archivo: src/Servicios/ReservaService.php
require_once __DIR__ . '/../../config/Database.php';

class ReservaService {
    private $conn;
    private $table_name = "reservas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. Crear una nueva reserva con VALIDACIÓN DE CUPO
    public function crearReserva($usuario_id, $servicio_id, $fecha, $total) {
        
        // A. VERIFICAR CUPO DISPONIBLE
        // Primero obtener el cupo máximo del servicio
        $queryCupo = "SELECT cupo_maximo FROM servicios WHERE servicio_id = :sid";
        $stmtCupo = $this->conn->prepare($queryCupo);
        $stmtCupo->execute([':sid' => $servicio_id]);
        $rowServicio = $stmtCupo->fetch(PDO::FETCH_ASSOC);
        $cupoMaximo = $rowServicio['cupo_maximo'];

        // Segundo, contar cuántas reservas activas hay para esa fecha
        // (Ignorar las canceladas)
        $queryCount = "SELECT COUNT(*) as ocupados FROM " . $this->table_name . " 
                       WHERE servicio_id = :sid 
                       AND fecha_reserva = :fecha 
                       AND estado != 'cancelada'";
        
        $stmtCount = $this->conn->prepare($queryCount);
        $stmtCount->execute([':sid' => $servicio_id, ':fecha' => $fecha]);
        $rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC);
        
        if ($rowCount['ocupados'] >= $cupoMaximo) {
            return ["success" => false, "message" => "Lo sentimos, no hay cupo para esta fecha. ($cupoMaximo/$cupoMaximo ocupados)"];
        }

        // B. SI HAY CUPO, INSERTAMOS LA RESERVA
        $query = "INSERT INTO " . $this->table_name . " 
                  (usuario_id, servicio_id, fecha_reserva, total_pagar, estado) 
                  VALUES (:uid, :sid, :fecha, :total, 'pendiente')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":total", $total);

        if($stmt->execute()) {
            return ["success" => true, "message" => "Reserva creada exitosamente."];
        }
        return ["success" => false, "message" => "Error técnico al guardar la reserva."];
    }

    // 2. Leer reservas de un usuario específico (Usando JOIN para traer el nombre del servicio)
    public function obtenerReservasPorUsuario($usuario_id) {
        $query = "SELECT r.reserva_id, r.fecha_reserva, r.estado, r.total_pagar, s.nombre_servicio 
                  FROM " . $this->table_name . " r
                  INNER JOIN servicios s ON r.servicio_id = s.servicio_id
                  WHERE r.usuario_id = :uid
                  ORDER BY r.fecha_reserva DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $usuario_id);
        $stmt->execute();
        
        return $stmt; // Retornamos el objeto PDOStatement para recorrerlo en la vista
    }
    // 3. Cancelar una reserva (Cambia el estado, liberando el cupo automáticamente)
    public function cancelarReserva($reserva_id, $usuario_id) {
        // Validamos que la reserva pertenezca al usuario antes de tocarla
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'cancelada' 
                  WHERE reserva_id = :rid AND usuario_id = :uid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rid", $reserva_id);
        $stmt->bindParam(":uid", $usuario_id);

        if ($stmt->execute()) {
            // Verificar si realmente se afectó alguna fila (si no, es que no era suya o no existía)
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
    
    public function contarReservasPorServicio($servicio_id) {
        $query = "SELECT COUNT(*) FROM reservas WHERE servicio_id = :id AND estado != 'cancelada'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $servicio_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>