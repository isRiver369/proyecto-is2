<?php
class Servicio {
    private $conn;
    private $table_name = "servicios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // LEER: Obtener servicios de un proveedor
    public function obtenerPorProveedor($proveedor_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE proveedor_id = ? ORDER BY servicio_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $proveedor_id);
        $stmt->execute();
        return $stmt;
    }

    // FILTROS: Ahora usa la columna REAL de 'modalidad'
    public function obtenerTodos($busqueda = "", $horario = "", $precioRango = "", $disponibilidad = "", $modalidad = "") {
        
        $sql = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        // 1. BÚSQUEDA
        if (!empty($busqueda)) {
            $sql .= " AND (nombre_servicio LIKE ? OR descripcion LIKE ?)";
            $busquedaParam = "%{$busqueda}%";
            $params[] = $busquedaParam;
            $params[] = $busquedaParam;
        }

        // 2. HORARIO (Busca texto parcial: 'Mañana', 'Tarde')
        if (!empty($horario)) {
            $sql .= " AND horario LIKE ?";
            $params[] = "%{$horario}%";
        }

        // 3. MODALIDAD (AHORA SÍ ES UNA COLUMNA REAL)
        if (!empty($modalidad)) {
            $sql .= " AND modalidad = ?";
            $params[] = $modalidad;
        }

        // 4. PRECIO
        if (!empty($precioRango)) {
            $rangos = explode('-', $precioRango);
            if (count($rangos) == 2) {
                $sql .= " AND precio BETWEEN ? AND ?";
                $params[] = $rangos[0];
                $params[] = $rangos[1];
            }
        }

        // 5. DISPONIBILIDAD (Usa la columna cupos_restantes)
        if (!empty($disponibilidad)) {
            if ($disponibilidad === 'abierto') {
                $sql .= " AND cupos_restantes > 0 AND disponible = 1";
            } elseif ($disponibilidad === 'cerrado') {
                $sql .= " AND (cupos_restantes <= 0 OR disponible = 0)";
            }
        }

        $sql .= " ORDER BY servicio_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    // CREAR: Actualizado con Modalidad, Categoría y Cupos Restantes
    public function crear($proveedor_id, $categoria_id, $nombre, $precio, $desc, $horario, $politicas, $cupos, $modalidad, $fecha_inicio, $fecha_fin) {
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (proveedor_id, categoria_id, nombre_servicio, precio, descripcion, horario, politicas, cupo_maximo, cupos_restantes, modalidad, fecha_inicio, fecha_fin, disponible)
                  VALUES (:pid, :catid, :nom, :pre, :desc, :hor, :pol, :cup, :cup_rest, :mod, :fechini, :fechfin, 1)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind de parámetros
        $stmt->bindParam(":pid", $proveedor_id);
        $stmt->bindParam(":catid", $categoria_id); // Nuevo
        $stmt->bindParam(":nom", $nombre);
        $stmt->bindParam(":pre", $precio);
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":hor", $horario);
        $stmt->bindParam(":pol", $politicas);
        $stmt->bindParam(":cup", $cupos);
        $stmt->bindParam(":cup_rest", $cupos); // Al inicio, restantes = maximo
        $stmt->bindParam(":mod", $modalidad);  // Nuevo
        $stmt->bindParam(":fechini", $fecha_inicio);
        $stmt->bindParam(":fechfin", $fecha_fin);

        return $stmt->execute();
    }
    
    // ELIMINAR (Sin cambios)
    public function eliminar($servicio_id, $proveedor_id) {
        $queryCheck = "SELECT COUNT(*) FROM reservas WHERE servicio_id = :sid";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(":sid", $servicio_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) return "reservado";

        $query = "DELETE FROM " . $this->table_name . " WHERE servicio_id = :sid AND proveedor_id = :pid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":pid", $proveedor_id);
        return $stmt->execute() ? "ok" : "error";
    }
}
?>