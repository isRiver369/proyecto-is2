<?php
session_start();
require_once '../config/Database.php';
require_once '../src/Servicios/ReservaService.php';

// 1. Seguridad: Verificar Login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php?error=debes_iniciar_sesion");
    exit();
}

// 2. Procesar la Reserva
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['servicio_id'])) {
    
    $usuario_id = $_SESSION['usuario_id'];
    $servicio_id = $_POST['servicio_id'];
    $precio = $_POST['precio'];
    
    // Validación: Si la fecha viene vacía, usamos la fecha de hoy
    $fecha_inicio = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
    
    // IMPORTANTE: Capturamos el horario elegido del formulario
    $horario_elegido = isset($_POST['horario_elegido']) ? $_POST['horario_elegido'] : 'General';

    // 3. Iniciar Servicio con Inyección de Dependencias (SOLID)
    $database = new Database();
    $db = $database->getConnection();
    $reservaService = new ReservaService($db);
    
    // 4. Llamar a la función con los 5 ARGUMENTOS
    $resultado = $reservaService->crearReserva(
        $usuario_id, 
        $servicio_id, 
        $fecha_inicio, 
        $precio, 
        $horario_elegido // <--- El 5to parámetro que faltaba
    );

    // 5. Redirección según resultado
    if ($resultado['success']) {
        header("Location: reserva.php?msg=reserva_creada");
        exit();
    } else {
        // Si el error es por cupo, redirigir con mensaje específico
        if (strpos($resultado['message'], 'cupo') !== false) {
            header("Location: catalogo.php?error=sin_cupo");
        } else {
            header("Location: catalogo.php?error=fallo_reserva");
        }
        exit();
    }
} else {
    // Si intentan entrar directo sin POST
    header("Location: catalogo.php");
    exit();
}
?>