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
    public function obtenerTodos($busqueda = "", $horario = "", $modalidad = "", $precio_rango = "", $disponibilidad = "", $ubicacion = "") {
        
        // 1. Consulta Base con Cálculo de Cupos
        $query = "SELECT s.*, 
                    c.nombre_categoria,
                    (s.cupo_maximo - (
                        SELECT COUNT(*) 
                        FROM reservas r 
                        WHERE r.servicio_id = s.servicio_id 
                        AND r.estado != 'cancelada'
                    )) as cupos_restantes
                    FROM " . $this->table_name . " s
                    INNER JOIN categorias c ON s.categoria_id = c.categoria_id
                    WHERE 1=1";

        // 2. Filtros WHERE (Datos estáticos)
        
        // Búsqueda por texto
        if (!empty($busqueda)) {
            $query .= " AND (s.nombre_servicio LIKE :busqueda OR s.descripcion LIKE :busqueda)";
        }

        // Filtro por Horario (Busca coincidencias parciales, ej: 'Mañana' dentro de 'Mañana,Tarde')
        if (!empty($horario)) {
            $query .= " AND LOWER(s.horario) LIKE :horario";
        }

        // Filtro por Modalidad (Presencial / Online)
        if (!empty($modalidad)) {
            $query .= " AND s.modalidad = :modalidad";
        }

        // Filtro por Precio (Rango 'min-max')
        if (!empty($precio_rango)) {
            $rango = explode('-', $precio_rango);
            if (count($rango) == 2) {
                $query .= " AND s.precio >= :p_min AND s.precio <= :p_max";
            }
        }

        // Filtro por Ubicación
        if (!empty($ubicacion)) {
            $query .= " AND LOWER(s.ubicacion) LIKE :ubicacion";
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
            $termHorario = "%" . strtolower($horario) . "%";
            $stmt->bindParam(":horario", $termHorario);
        }
        if (!empty($modalidad)) {
            $stmt->bindParam(":modalidad", $modalidad);
        }
        if (!empty($precio_rango) && count($rango) == 2) {
            $stmt->bindParam(":p_min", $rango[0]);
            $stmt->bindParam(":p_max", $rango[1]);
        }
        if (!empty($ubicacion)) {
            $termUbicacion = "%" . strtolower($ubicacion) . "%";
            $stmt->bindParam(":ubicacion", $termUbicacion);
        }

        $stmt->execute();
        return $stmt;
    }

    // CREAR: Nuevo servicio
    public function crear($proveedor_id, $nombre, $precio, $desc, $horario, $politicas, $cupos, $fecha_inicio, $fecha_fin, $descripcion_breve, $categoria_id, $modalidad, $ubicacion) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (proveedor_id, nombre_servicio, precio, descripcion, horario, politicas, cupo_maximo, cupos_restantes, fecha_inicio, fecha_fin, descripcion_breve, categoria_id, modalidad, ubicacion, disponible)
                  VALUES (:pid, :nom, :pre, :desc, :hor, :pol, :cup, :cup, :fechini, :fechfin, :descp_breve, :catid, :mod, :ubi, 1)";
        
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
        $stmt->bindParam(":descp_breve", $descripcion_breve);
        $stmt->bindParam(":catid", $categoria_id);
        $stmt->bindParam(":cat", $categoria);
        $stmt->bindParam(":mod", $modalidad);
        $stmt->bindParam(":ubi", $ubicacion);

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

    //EDITAR
    public function editar($servicio_id, $proveedor_id, $nombre, $precio, $desc, $horario, $politicas, $cupos, $fecha_inicio, $fecha_fin, $descripcion_breve, $categoria_id, $modalidad, $ubicacion) {
        $query = "UPDATE " . $this->table_name . "
                SET nombre_servicio = :nom, precio = :pre, descripcion = :desc, horario = :hor, politicas = :pol, cupo_maximo = :cup, cupos_restantes = :cup, fecha_inicio = :fechini, fecha_fin = :fechfin, descripcion_breve = :desc_breve, categoria_id = :categoria_id, modalidad = :modalidad, ubicacion = :ubicacion
                WHERE servicio_id = :sid
                AND proveedor_id = :pid
                LIMIT 1";

        $stmt = $this->conn->prepare($query);   
        $stmt->bindParam(":nom", $nombre);
        $stmt->bindParam(":pre", $precio);
        $stmt->bindParam(":desc", $desc);
        $stmt->bindParam(":hor", $horario);
        $stmt->bindParam(":pol", $politicas);
        $stmt->bindParam(":cup", $cupos);
        $stmt->bindParam(":fechini", $fecha_inicio);
        $stmt->bindParam(":fechfin", $fecha_fin);
        $stmt->bindParam(":desc_breve", $descripcion_breve);
        $stmt->bindParam(":categoria_id", $categoria_id);
        $stmt->bindParam(":modalidad", $modalidad);
        $stmt->bindParam(":ubicacion", $ubicacion);

        $stmt->bindParam(":sid", $servicio_id);
        $stmt->bindParam(":pid", $proveedor_id);

        return $stmt->execute();
    }
}
?>