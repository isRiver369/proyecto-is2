<?php
// Archivo: public/procesar_reserva.php
session_start();
require_once '../src/Servicios/ReservaService.php';

// 1. Seguridad: ¿Está logueado?
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=debes_iniciar_sesion");
    exit();
}

// 2. Validar que vengan datos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['servicio_id'])) {
    
    $usuario_id = $_SESSION['usuario_id'];
    $servicio_id = $_POST['servicio_id'];
    $precio = $_POST['precio'];
    
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d', strtotime('+1 day'));

    $reservaService = new ReservaService();
    $resultado = $reservaService->crearReserva($usuario_id, $servicio_id, $fecha, $precio);

    if ($resultado['success']) {
        header("Location: reserva.php?msg=reserva_creada");
    } else {
        // Si el mensaje es de cupo, redirigimos con error específico
        if (strpos($resultado['message'], 'cupo') !== false) {
            header("Location: catalogo.php?error=sin_cupo");
        } else {
            header("Location: catalogo.php?error=fallo_reserva");
        }
    }
} else {
    header("Location: catalogo.php");
}
?>