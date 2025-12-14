<?php
session_start();
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/PagoService.php';

// Seguridad: Solo clientes logueados
Seguridad::requerirRol('cliente');

// Obtener datos
$pagoService = new PagoService();
$historial = $pagoService->obtenerHistorialPorUsuario($_SESSION['usuario_id']);
$num_pagos = $historial->rowCount();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pagos - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

    <?php include 'menu_header.php'; ?>

    <main class="max-w-6xl mx-auto px-4 py-10">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-[#1A4B8C]">Historial de Transacciones</h1>
            
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <?php if ($num_pagos > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                                <th class="py-3 px-6 text-left">ID Pago</th>
                                <th class="py-3 px-6 text-left">Servicio Adquirido</th>
                                <th class="py-3 px-6 text-center">Fecha Reserva</th>
                                <th class="py-3 px-6 text-center">Método</th>
                                <th class="py-3 px-6 text-center">Monto</th>
                                <th class="py-3 px-6 text-center">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <?php while ($pago = $historial->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="py-3 px-6 text-left whitespace-nowrap font-bold">
                                        #<?php echo str_pad($pago['pago_id'], 5, '0', STR_PAD_LEFT); ?>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="font-medium text-[#1A4B8C]"><?php echo htmlspecialchars($pago['nombre_servicio']); ?></span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <?php echo date('d/m/Y', strtotime($pago['fecha_reserva'])); ?>
                                    </td>
                                    <td class="py-3 px-6 text-center capitalize">
                                        <?php 
                                        $icono = ($pago['metodo_pago'] == 'tarjeta') ? '💳' : '🏦';
                                        echo $icono . ' ' . $pago['metodo_pago']; 
                                        ?>
                                    </td>
                                    <td class="py-3 px-6 text-center font-bold text-gray-800">
                                        $<?php echo number_format($pago['monto'], 2); ?>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <?php if ($pago['estado_pago'] == 'aprobado'): ?>
                                            <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">Aprobado</span>
                                        <?php elseif ($pago['estado_pago'] == 'pendiente'): ?>
                                            <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold">Revisión</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold">Rechazado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="ver_comprobante.php?reserva_id=<?php echo $pago['reserva_id']; ?>" 
                                           class="text-[#1A4B8C] hover:underline font-semibold text-xs flex items-center justify-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            Ver Recibo
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-10 text-center">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No hay pagos registrados</h3>
                    <p class="mt-1 text-gray-500">Tus transacciones aparecerán aquí una vez que adquieras un servicio.</p>
                    <a href="catalogo.php" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-[#1A4B8C] hover:bg-opacity-90">
                        Ir al Catálogo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>