<?php
session_start();

require_once '../config/Database.php';
require_once 'proveedor_controlador.php';

// Seguridad
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$controller = new ProveedorControlador(
    (new Database())->getConnection(),
    $_SESSION['usuario_id']
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handle($_POST);
}