<?php
session_start();
require_once '../src/Servicios/ReservaService.php';

// 1. Seguridad
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Procesar Cancelación
if (isset($_GET['id'])) {
    $reserva_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    require_once '../config/Database.php';
    $db = (new Database())->getConnection();
    $reservaService = new ReservaService($db); // <--- Inyección de Dependencias
    $resultado = $reservaService->cancelarReserva($reserva_id, $usuario_id);

    if ($resultado['success']) {
        header("Location: reserva.php?msg=cancelacion_exitosa");
    } else {
        header("Location: reserva.php?error=fallo_cancelar");
    }
} else {
    header("Location: reserva.php");
}
?>