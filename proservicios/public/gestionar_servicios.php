<?php
session_start();

require_once '../config/Database.php';
require_once '../src/Modelos/Servicio.php';
require_once '../src/Servicios/ReservaService.php';
require_once '../src/Servicios/AuthService.php';

// Asegurar que el proveedor está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Conectar a la BD
$db = (new Database())->getConnection();

// Crear instancias de los modelos
$servicioModel = new Servicio($db);
$reservaService = new ReservaService($db);
$authService = new AuthService($db);

// 1. Procesar Acciones (Crear/Eliminar Servicio)
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. CREAR SERVICIO
    if (isset($_POST['accion']) && $_POST['accion'] == 'crear_servicio') {
        $creado = $servicioModel->crear(
            $usuario_id,
            $_POST['nombre'],
            $_POST['precio'],
            $_POST['descripcion'],
            $_POST['horario'],
            $_POST['politicas'],
            $_POST['cupos'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin'],
            $_POST['descripcion_breve'],
            $_POST['categoria_id'],
            $_POST['modalidad'],
            $_POST['ubicacion']
        );
        $_SESSION['mensaje'] = $creado ? "¡Servicio creado con éxito!" : "Error al crear servicio.";
        header("Location: panel_proveedor.php");
        exit;
    }

    // B. ELIMINAR SERVICIO
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_servicio') {

        $servicio_id = $_POST['servicio_id'];
        // Verificar si el servicio tiene reservas
        $revisarReservas = $reservaService->contarReservasPorServicio($servicio_id);

        if ($revisarReservas > 0) {
             $_SESSION['error_eliminar'] = "No puedes eliminar un servicio que ya tiene reservas activas.";
            header("Location: panel_proveedor.php");
            exit;
        }
        $eliminado = $servicioModel->eliminar($servicio_id, $usuario_id);

        $_SESSION['mensaje'] = $eliminado ? "Servicio eliminado." : "Error al eliminar.";
        header("Location: panel_proveedor.php");
        exit;
    }

    // C. EDITAR SERVICIO
    if (isset($_POST['accion']) && $_POST['accion'] == 'editar_servicio') {

        $actualizado = $servicioModel->editar(
            $_POST['servicio_id'],
            $usuario_id,
            $_POST['nombre'],
            $_POST['precio'],
            $_POST['descripcion'],
            $_POST['horario'],
            $_POST['politicas'],
            $_POST['cupos'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin'],
            $_POST['descripcion_breve'],
            $_POST['categoria_id'],
            $_POST['modalidad'],
            $_POST['ubicacion']
        );

        $_SESSION['mensaje'] = $actualizado ? "Servicio actualizado con éxito." :
                                            "Error al actualizar el servicio.";

        header("Location: panel_proveedor.php");
        exit;
    }

    // D. ACTUALIZAR PERFIL (BIO/PORTAFOLIO/CONTACTO)
    if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_perfil') {
        // Leer los names que existen en el formulario
        $bio = $_POST['bio'] ?? "";
        $portafolio = $_POST['portafolio_url'] ?? "";    // name="portafolio_url" en el form
        $contacto = $_POST['contacto'] ?? "";

        // Llamar al servicio de autenticación / perfil
        $ok = $authService->actualizarPerfilProveedor($usuario_id, $bio, $portafolio, $contacto);

        $_SESSION['mensaje'] = $ok ? "Perfil actualizado correctamente." 
                                : "Error al actualizar el perfil.";

        header("Location: panel_proveedor.php");
        exit;
    }
}
?>