<?php
// public/cambiar_estado_servicio.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

if (isset($_GET['id'])) {
    $dashboard = new AdminDashboard();
    $dashboard->toggleEstadoServicio($_GET['id']);
}

// Redirigir de vuelta a la lista
header("Location: admin_servicios.php");
exit();