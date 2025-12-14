<?php
// 1. INICIAR SESIÓN
session_start();
require_once '../config/Database.php';
require_once '../src/Servicios/ReservaService.php';

// 2. SEGURIDAD: Verificar Login
if (!isset($_SESSION['usuario_id'])) {
    // Guardamos la intención para redirigir después del login (opcional pero recomendado)
    $_SESSION['redirect_after_login'] = 'catalogo.php'; 
    header("Location: login.php?error=debes_iniciar_sesion");
    exit();
}

// 3. PROCESAR SI HAY POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['servicio_id'])) {
    
    $usuario_id = $_SESSION['usuario_id'];
    $servicio_id = $_POST['servicio_id'];
    $precio = $_POST['precio'];
    $db = (new Database())->getConnection();
    // Validación de fecha
    $fecha_inicio = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
    
    // Capturamos el horario
    $horario_elegido = isset($_POST['horario_elegido']) ? $_POST['horario_elegido'] : 'General';

    // 4. INSTANCIAR SERVICIO
    // Nota: El ReservaService que te pasé antes crea su propia conexión en el constructor,
    // así que no hace falta pasarle $db aquí.
    $reservaService = new ReservaService($db);
    
    try {
        // 5. LLAMAR A LA FUNCIÓN (CORREGIDO EL ORDEN DE PARÁMETROS)
        // Orden correcto: Usuario, Servicio, Fecha, Horario, Precio
        $resultado = $reservaService->crearReserva(
            $usuario_id, 
            $servicio_id, 
            $fecha_inicio, 
            $horario_elegido, // <--- El horario va AQUÍ (4to lugar)
            $precio           // <--- El precio va AQUÍ (5to lugar)
        );

        // 6. MANEJAR LA RESPUESTA (LÓGICA NUEVA)

        // Caso A: No hay cupo
        if ($resultado === "sin_cupo") {
            header("Location: catalogo.php?error=sin_cupo");
            exit();
        } 

        // Caso B: Éxito (nos devolvió el ID de la reserva)
        elseif ($resultado) {
            // REDIRECCIÓN CORRECTA: Vamos a PAGOS con el ID de la reserva
            header("Location: pagos.php?reserva_id=" . $resultado);
            exit();
        } 

        // Caso C: Fallo genérico
        else {
            header("Location: catalogo.php?error=fallo_reserva");
            exit();
        }
    
    } catch (Exception $e) {
        // Si hay error de sistema (BD caída, etc)
        error_log("Error al procesar reserva: " . $e->getMessage());
        header("Location: catalogo.php?error=error_sistema");
        exit();
    }

} else {
    // Si intentan entrar directo sin POST
    header("Location: catalogo.php");
    exit();
}
?>