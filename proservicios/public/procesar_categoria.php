<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');
$dashboard = new AdminDashboard();

// CASO 1: CREAR (Viene por POST desde el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_categoria']);
    
    if (!empty($nombre)) {
        if($dashboard->crearCategoria($nombre)){
             header("Location: admin_categorias.php?mensaje=creado");
        } else {
             header("Location: admin_categorias.php?error=db");
        }
    } else {
        header("Location: admin_categorias.php?error=vacio");
    }
    exit();
}

// CASO 2: ELIMINAR (Viene por GET desde el botón rojo)
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $dashboard->eliminarCategoria($id);
    header("Location: admin_categorias.php?mensaje=eliminado");
    exit();
}

// Si entran directo sin datos, volver al panel
header("Location: admin_categorias.php");