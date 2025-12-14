<?php
require_once '../config/Database.php';
class Categoria {
    private $conn;
    private $table_name = "categorias";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerNombrePorId($categoria_id) {
        $query = "SELECT nombre_categoria FROM categorias WHERE categoria_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $categoria_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerCategorias() {
        $query = "SELECT categoria_id, nombre_categoria
                  FROM " . $this->table_name . "
                  ORDER BY nombre_categoria ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}