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

    // LEER: Obtener todos (Para el catálogo público)
    public function obtenerTodos($busqueda = "") {
        $query = "SELECT * FROM " . $this->table_name;
        if (!empty($busqueda)) {
            $query .= " WHERE nombre_servicio LIKE :b OR descripcion LIKE :b";
        }
        $stmt = $this->conn->prepare($query);
        if (!empty($busqueda)) {
            $termino = "%" . $busqueda . "%";
            $stmt->bindParam(":b", $termino);
        }
        $stmt->execute();
        return $stmt;
    }

    // CREAR: Nuevo servicio
    public function crear($proveedor_id, $nombre, $precio, $desc, $horario, $politicas, $cupos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (proveedor_id, nombre_servicio, precio, descripcion, horario, politicas, cupo_maximo, disponible)
                  VALUES (:pid, :nom, :pre, :desc, :hor, :pol, :cup, 1)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pid", $proveedor_id);
        $stmt->bindParam(":nom", $nombre);
        $stmt->bindParam(":pre", $precio);
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":hor", $horario);
        $stmt->bindParam(":pol", $politicas);
        $stmt->bindParam(":cup", $cupos);

        return $stmt->execute();
    }

    // ELIMINAR
    public function eliminar($servicio_id, $proveedor_id) {
        // Validamos proveedor_id para asegurar que nadie borre servicios de otro
        $query = "DELETE FROM " . $this->table_name . " WHERE servicio_id = :sid AND proveedor_id = :pid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":pid", $proveedor_id);
        return $stmt->execute();
    }
}
?>