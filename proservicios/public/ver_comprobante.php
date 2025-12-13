<?php
// public/ver_comprobante.php
session_start(); // Asegurar sesión iniciada
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/PagoService.php';
require_once '../config/Database.php'; // Necesario para leer la config del IVA

Seguridad::requerirRol('cliente');

if (!isset($_GET['reserva_id'])) {
    header("Location: reserva.php");
    exit();
}

// 1. OBTENER DATOS DEL COMPROBANTE
$pagoService = new PagoService();
// Corregido: La función solo pide reserva_id
$datos = $pagoService->obtenerComprobante($_GET['reserva_id']);

if (!$datos) {
    die("Comprobante no encontrado.");
}

// 2. SEGURIDAD: Verificar que el comprobante pertenezca al usuario logueado
// Usamos el email que viene en los datos del comprobante vs el de la sesión
if (isset($_SESSION['usuario_email']) && $datos['cliente_email'] !== $_SESSION['usuario_email']) {
    die("Acceso denegado: Este comprobante no te pertenece.");
}

// 3. OBTENER CONFIGURACIÓN DE IMPUESTO (DINÁMICO)
$database = new Database();
$db = $database->getConnection();
$stmtConfig = $db->prepare("SELECT tasa_impuesto FROM configuracion WHERE id = 1");
$stmtConfig->execute();
$config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

// Cálculo matemático
$tasa_impuesto = $config['tasa_impuesto'] ?? 15.00; // Ej: 15
$factor_divisior = 1 + ($tasa_impuesto / 100); // Ej: 1.15

$total = $datos['monto'];
$subtotal = $total / $factor_divisior;
$iva_monto = $total - $subtotal;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante #<?php echo $datos['pago_id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 font-sans">

    <div class="max-w-2xl mx-auto px-4">
        
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="mis_pagos.php" class="text-gray-600 hover:text-gray-900 font-bold flex items-center transition">
                ← Volver a Historial
            </a>
            <button onclick="descargarPDF()" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-red-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Descargar PDF
            </button>
        </div>

        <div id="comprobante" class="bg-white p-10 rounded-xl shadow-2xl relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-4 bg-[#1A4B8C]"></div>

            <div class="flex justify-between items-start mb-8 mt-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-[#1A4B8C]">COMPROBANTE</h1>
                    <p class="text-sm text-green-600 font-bold mt-1 uppercase border border-green-600 inline-block px-2 rounded">
                        <?php echo $datos['estado_pago']; ?>
                    </p>
                </div>
                <div class="text-right">
                    <h2 class="font-bold text-xl text-gray-800">ProServicios</h2>
                    <p class="text-sm text-gray-500">RUC: 0990000000001</p>
                    <p class="text-sm text-gray-500">Guayaquil, Ecuador</p>
                </div>
            </div>

            <hr class="border-gray-200 mb-8">

            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Facturar a</p>
                    <p class="font-bold text-gray-800 text-lg"><?php echo $datos['cliente_nombre'] . ' ' . $datos['cliente_apellido']; ?></p>
                    <p class="text-sm text-gray-600"><?php echo $datos['cliente_email']; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-400 uppercase">N° de Transacción</p>
                    <p class="font-bold text-gray-800 text-lg">#<?php echo str_pad($datos['pago_id'], 6, '0', STR_PAD_LEFT); ?></p>
                    <p class="text-xs font-bold text-gray-400 uppercase mt-2">Fecha de Pago</p>
                    <p class="font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($datos['fecha_reserva'])); ?></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-8">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-bold text-gray-500 uppercase border-b border-gray-200">
                            <th class="pb-2">Descripción</th>
                            <th class="pb-2 text-right">Método</th>
                            <th class="pb-2 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr>
                            <td class="py-4 font-bold text-gray-700">
                                <?php echo htmlspecialchars($datos['nombre_servicio']); ?>
                                <span class="block text-xs font-normal text-gray-500 mt-1">
                                    Horario: <?php echo $datos['horario_elegido']; ?>
                                </span>
                            </td>
                            <td class="py-4 text-right text-gray-600 capitalize"><?php echo $datos['metodo_pago']; ?></td>
                            <td class="py-4 text-right font-bold text-gray-800">$<?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <div class="w-1/2">
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>IVA (<?php echo $tasa_impuesto; ?>%)</span>
                        <span>$<?php echo number_format($iva_monto, 2); ?></span>
                    </div>
                    <div class="flex justify-between border-t border-gray-300 pt-2 mt-2">
                        <span class="font-bold text-xl text-[#1A4B8C]">Total Pagado</span>
                        <span class="font-bold text-xl text-[#1A4B8C]">$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-10 text-center border-t border-dashed border-gray-300 pt-6">
                <p class="text-xs text-gray-400">Este documento es un comprobante electrónico válido.</p>
                <p class="text-xs text-gray-400">Gracias por confiar en ProServicios.</p>
            </div>
        </div>
    </div>

    <script>
        window.jsPDF = window.jspdf.jsPDF;

        function descargarPDF() {
            const elemento = document.getElementById('comprobante');
            
            // Usamos html2canvas para capturar el diseño exacto
            html2canvas(elemento, { 
                scale: 2, // Mejor resolución
                useCORS: true 
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save("Recibo_ProServicios_<?php echo $datos['pago_id']; ?>.pdf");
            });
        }
    </script>

</body>
</html>