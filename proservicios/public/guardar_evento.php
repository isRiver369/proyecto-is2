<?php
session_start();
require_once '../config/Database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$proveedor_id = $_SESSION['usuario_id'];
$nombre_evento = $_POST['nombre_evento'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$dia = $_POST['dia'] ?? '';
$hora = $_POST['hora'] ?? '';
$evento_id = $_POST['evento_id'] ?? '';  // Evento ID (si estamos editando)

// Validación de datos
if (empty($nombre_evento)) {
    die("El nombre del evento es obligatorio");
}

if (empty($dia)) {
    die("El día del evento es obligatorio");
}

if ($hora === '' || $hora === null) {
    die("La hora del evento es obligatoria");
}

$database = new Database();
$conn = $database->getConnection();

// Si existe el evento_id, hacemos un UPDATE
if (!empty($evento_id)) {
    $query = "UPDATE eventos 
              SET nombre_evento = :nombre_evento, descripcion = :descripcion, dia = :dia, hora = :hora 
              WHERE evento_id = :evento_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":evento_id", $evento_id);
} else {
    // Si no existe el evento_id, hacemos un INSERT
    $query = "INSERT INTO eventos (proveedor_id, nombre_evento, descripcion, dia, hora)
              VALUES (:proveedor_id, :nombre_evento, :descripcion, :dia, :hora)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":proveedor_id", $proveedor_id);
}

$stmt->bindParam(":nombre_evento", $nombre_evento);
$stmt->bindParam(":descripcion", $descripcion);
$stmt->bindParam(":dia", $dia);
$stmt->bindParam(":hora", $hora);
$stmt->execute();

$_SESSION['mensaje'] = "Evento guardado correctamente";
header("Location: panel_proveedor.php#agenda");
exit();
?>