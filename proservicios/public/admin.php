<?php
require_once '../src/Servicios/Seguridad.php';

// Solo deja pasar si el rol es 'administrador'
Seguridad::requerirRol('administrador');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-800 min-h-screen text-white">
    <div class="container mx-auto p-10">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-4xl font-bold text-yellow-500"> Panel de Administrador</h1>
            <a href="logout.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Cerrar Sesión</a>
        </div>

        <div class="bg-gray-700 p-8 rounded-xl shadow-lg">
            <h2 class="text-2xl mb-4">Bienvenido, Admin.</h2>
            <p>Solo los usuarios con rol <strong>'administrador'</strong> pueden ver esta pantalla.</p>
            
            <div class="mt-8 grid grid-cols-3 gap-4">
                <div class="bg-gray-600 p-4 rounded text-center">
                    <span class="block text-3xl font-bold">0</span>
                    <span class="text-sm text-gray-400">Usuarios Reportados</span>
                </div>
                <div class="bg-gray-600 p-4 rounded text-center">
                    <span class="block text-3xl font-bold">$0.00</span>
                    <span class="text-sm text-gray-400">Ingresos Totales</span>
                </div>
                </div>
        </div>
    </div>
</body>
</html>