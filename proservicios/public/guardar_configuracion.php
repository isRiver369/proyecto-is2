<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dashboard = new AdminDashboard();
    
    // Recoger datos
    $nombre = $_POST['nombre_sitio'];
    $email = $_POST['email_admin'];
    $impuesto = $_POST['tasa_impuesto'];
    $moneda = $_POST['moneda'];
    
    // El checkbox si no se marca no envía nada, así que validamos con isset
    // Si está marcado enviamos 1, si no, 0.
    $mantenimiento = isset($_POST['modo_mantenimiento']) ? 1 : 0;

    if ($dashboard->actualizarConfiguracion($nombre, $email, $impuesto, $moneda, $mantenimiento)) {
        header("Location: admin_configuracion.php?mensaje=guardado");
    } else {
        header("Location: admin_configuracion.php?error=db");
    }
    exit();
}

// Si intentan entrar por GET, los devolvemos
header("Location: admin_configuracion.php");