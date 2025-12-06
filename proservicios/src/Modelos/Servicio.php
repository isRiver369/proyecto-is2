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

    // Método avanzado con Filtros Dinámicos
    public function obtenerTodos($busqueda = "", $horario = "", $precio_rango = "", $disponibilidad = "") {
        
        // 1. Consulta Base con Cálculo de Cupos
        $query = "SELECT s.*, 
                  (s.cupo_maximo - (
                      SELECT COUNT(*) 
                      FROM reservas r 
                      WHERE r.servicio_id = s.servicio_id 
                      AND r.estado != 'cancelada'
                  )) as cupos_restantes
                  FROM " . $this->table_name . " s
                  WHERE 1=1"; // Truco para concatenar 'AND' fácilmente

        // 2. Filtros WHERE (Datos estáticos)
        
        // Búsqueda por texto
        if (!empty($busqueda)) {
            $query .= " AND (s.nombre_servicio LIKE :busqueda OR s.descripcion LIKE :busqueda)";
        }

        // Filtro por Horario (Busca coincidencias parciales, ej: 'Mañana' dentro de 'Mañana,Tarde')
        if (!empty($horario)) {
            $query .= " AND s.horario LIKE :horario";
        }

        // Filtro por Precio (Rango 'min-max')
        if (!empty($precio_rango)) {
            $rango = explode('-', $precio_rango);
            if (count($rango) == 2) {
                $query .= " AND s.precio >= :p_min AND s.precio <= :p_max";
            }
        }

        // 3. Filtros HAVING (Para columnas calculadas como 'cupos_restantes')
        
        if ($disponibilidad === 'abierto') {
            // Mostrar solo si tiene cupos Y está marcado como disponible
            $query .= " HAVING cupos_restantes > 0 AND s.disponible = 1";
        } elseif ($disponibilidad === 'cerrado') {
            // Mostrar si NO tiene cupos O está marcado como no disponible
            $query .= " HAVING (cupos_restantes <= 0 OR s.disponible = 0)";
        }

        $query .= " ORDER BY s.nombre_servicio ASC";
        
        // 4. Preparar y Bindear
        $stmt = $this->conn->prepare($query);

        if (!empty($busqueda)) {
            $termino = "%" . $busqueda . "%";
            $stmt->bindParam(":busqueda", $termino);
        }
        if (!empty($horario)) {
            $termHorario = "%" . $horario . "%";
            $stmt->bindParam(":horario", $termHorario);
        }
        if (!empty($precio_rango) && count($rango) == 2) {
            $stmt->bindParam(":p_min", $rango[0]);
            $stmt->bindParam(":p_max", $rango[1]);
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
        // 1. Validar si el servicio tiene reservas activas
        $queryCheck = "SELECT COUNT(*) FROM reservas WHERE servicio_id = :sid";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(":sid", $servicio_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->fetchColumn() > 0) {
            return "reservado"; // No eliminar si hay gente inscrita
        }

        // 2. Si está limpio, eliminar
        $query = "DELETE FROM " . $this->table_name . " WHERE servicio_id = :sid AND proveedor_id = :pid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":pid", $proveedor_id);

        return $stmt->execute() ? "ok" : "error";
    }
}
?>