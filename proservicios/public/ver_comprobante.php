<?php
// public/ver_comprobante.php
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/PagoService.php';

Seguridad::requerirRol('cliente');

if (!isset($_GET['reserva_id'])) {
    header("Location: reserva.php");
    exit();
}

$pagoService = new PagoService();
$datos = $pagoService->obtenerComprobante($_GET['reserva_id'], $_SESSION['usuario_id']);

if (!$datos) {
    die("Comprobante no encontrado o acceso denegado.");
}
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
<body class="bg-gray-100 min-h-screen py-10">

    <div class="max-w-2xl mx-auto px-4">
        
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="reserva.php" class="text-gray-600 hover:text-gray-900 font-bold flex items-center">
                ← Volver a Mis Reservas
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
                    <p class="text-sm text-gray-500 font-bold mt-1">PAGO APROBADO</p>
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
                    <p class="font-bold text-gray-800 text-lg"><?php echo $datos['nombre'] . ' ' . $datos['apellido']; ?></p>
                    <p class="text-sm text-gray-600"><?php echo $datos['email']; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-400 uppercase">N° de Transacción</p>
                    <p class="font-bold text-gray-800 text-lg">#<?php echo str_pad($datos['pago_id'], 6, '0', STR_PAD_LEFT); ?></p>
                    <p class="text-xs font-bold text-gray-400 uppercase mt-2">Fecha de Pago</p>
                    <p class="font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($datos['fecha_reserva'])); // Usamos fecha reserva como ref ?></p>
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
                                <?php echo $datos['nombre_servicio']; ?>
                                <span class="block text-xs font-normal text-gray-500">Reserva ID: #<?php echo $datos['reserva_id']; ?></span>
                            </td>
                            <td class="py-4 text-right text-gray-600"><?php echo ucfirst($datos['metodo_pago']); ?></td>
                            <td class="py-4 text-right font-bold text-gray-800">$<?php echo number_format($datos['monto'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <div class="w-1/2">
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($datos['monto'] / 1.15, 2); ?></span>
                    </div>
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>IVA (15%)</span>
                        <span>$<?php echo number_format($datos['monto'] - ($datos['monto'] / 1.15), 2); ?></span>
                    </div>
                    <div class="flex justify-between border-t border-gray-300 pt-2 mt-2">
                        <span class="font-bold text-xl text-[#1A4B8C]">Total Pagado</span>
                        <span class="font-bold text-xl text-[#1A4B8C]">$<?php echo number_format($datos['monto'], 2); ?></span>
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
            
            html2canvas(elemento, { scale: 2 }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgWidth = 210; // Ancho A4
                const imgHeight = canvas.height * imgWidth / canvas.width;
                
                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
                pdf.save("Comprobante_Reserva_<?php echo $datos['reserva_id']; ?>.pdf");
            });
        }
    </script>

</body>
</html>