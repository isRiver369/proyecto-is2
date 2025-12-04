<?php
class Servicio {
    private $conn;
    private $table_name = "servicios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // LEER: Obtener servicios de un proveedor específico
    public function obtenerPorProveedor($proveedor_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE proveedor_id = ? ORDER BY servicio_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $proveedor_id);
        $stmt->execute();
        return $stmt;
    }

    // LEER: Obtener todos Para el catalogo
    public function obtenerTodos($busqueda = "", $horario ="", $precio = "", $disponibilidad = "") {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

        // Filtro de búsqueda por texto
        if (!empty($busqueda)) {
            $query .= " AND (nombre_servicio LIKE :b OR descripcion LIKE :b)";
        }

        // Filtro por horario
        if (!empty($horario)) {
            $query .= " AND horario LIKE :h";
        }

        // Filtro por rangos de precio
        if (!empty($precio)) {
            list($min, $max) = explode("-", $precio);
            $query .= " AND precio BETWEEN :min AND :max";
        }

        // Filtro por disponibilidad/cupos (1 y 0 campo 'disponible' ¿se mantiene así?)
        if ($disponibilidad === "abierto") {
            $query .= " AND disponible = 1";
        } elseif ($disponibilidad === "cerrado") {
            $query .= " AND disponible = 0";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($busqueda)) {
            $termino = "%".$busqueda."%";
            $stmt->bindParam(":b", $termino);
        }

        if (!empty($horario)) {
            $h = "%".$horario."%";
            $stmt->bindParam(":h", $h);
        }

        if (!empty($precio)) {
            $stmt->bindParam(":min", $min);
            $stmt->bindParam(":max", $max);
        }

        $stmt->execute();
        return $stmt;
    }

    // CREAR: Nuevo servicio
    public function crear($proveedor_id, $nombre, $precio, $desc, $horario, $politicas, $cupos, $fecha_inicio, $fecha_fin) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (proveedor_id, nombre_servicio, precio, descripcion, horario, politicas, cupo_maximo, fecha_inicio, fecha_fin, disponible)
                  VALUES (:pid, :nom, :pre, :desc, :hor, :pol, :cup, :fechini, :fechfin, 1)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pid", $proveedor_id);
        $stmt->bindParam(":nom", $nombre);
        $stmt->bindParam(":pre", $precio);
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":hor", $horario);
        $stmt->bindParam(":pol", $politicas);
        $stmt->bindParam(":cup", $cupos);
        $stmt->bindParam(":fechini", $fecha_inicio);
        $stmt->bindParam(":fechfin", $fecha_fin);

        return $stmt->execute();
    }

    // ELIMINAR
    public function eliminar($servicio_id, $proveedor_id) {

        // 1. Validar si el servicio tiene reservas
        $queryCheck = "SELECT COUNT(*) FROM reservas WHERE servicio_id = :sid";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(":sid", $servicio_id);
        $stmtCheck->execute();
        $revisarReservas = $stmtCheck->fetchColumn();

        if ($revisarReservas > 0) {
            return "reservado";
        }

        // 2. Si no tiene reservas, el servicio se puede eliminar 
        $query = "DELETE FROM " . $this->table_name . " WHERE servicio_id = :sid AND proveedor_id = :pid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":pid", $proveedor_id);

        if ($stmt->execute()) {
            return "ok";
        }

        return "error";
    }
}
?>