<?php
// Archivo: public/eliminar_reserva.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

// 1. Seguridad: Solo admins pueden entrar aquí
Seguridad::requerirRol('administrador');

// 2. Validar que nos enviaron un ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 3. Llamar al servicio para borrar
    $dashboard = new AdminDashboard();
    
    if ($dashboard->eliminarReserva($id)) {
        // Redirigir con éxito
        header("Location: admin.php?mensaje=eliminado");
    } else {
        // Redirigir con error (probablemente por llave foránea o BD caída)
        header("Location: admin.php?error=no_se_pudo_eliminar");
    }
} else {
    // Si intentan entrar sin ID, los devolvemos
    header("Location: admin.php");
}
exit();