<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

if (isset($_GET['id']) && isset($_GET['rol'])) {
    $dashboard = new AdminDashboard();
    // Ejecutamos el cambio
    $dashboard->cambiarRolUsuario($_GET['id'], $_GET['rol']);
}

// Nos devolvemos a la lista de usuarios
header("Location: admin_usuarios.php");
exit();