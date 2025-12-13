<?php
session_start();
require_once '../config/Database.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// Validar parámetro
if (!isset($_GET['evento_id']) || $_GET['evento_id'] === '') {
    http_response_code(400);
    echo json_encode(["error" => "ID de evento requerido"]);
    exit;
}

$evento_id = $_GET['evento_id'];
$proveedor_id = $_SESSION['usuario_id'];

$database = new Database();
$conn = $database->getConnection();

// Obtener solo el evento del proveedor logueado
$query = "SELECT evento_id, nombre_evento, descripcion, dia, hora
          FROM eventos
          WHERE evento_id = :evento_id
          AND proveedor_id = :proveedor_id
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bindParam(":evento_id", $evento_id, PDO::PARAM_INT);
$stmt->bindParam(":proveedor_id", $proveedor_id, PDO::PARAM_INT);
$stmt->execute();

$evento = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el evento
if (!$evento) {
    http_response_code(404);
    echo json_encode(["error" => "Evento no encontrado"]);
    exit;
}

// Respuesta JSON
header('Content-Type: application/json');
echo json_encode($evento);