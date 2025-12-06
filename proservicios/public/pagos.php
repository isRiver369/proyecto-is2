<?php
// 1. SEGURIDAD Y DEPENDENCIAS
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/PagoService.php';
require_once '../src/Servicios/PaymentStrategies.php'; // <--- NUEVO: Incluimos las estrategias

Seguridad::requerirRol('cliente');

// 2. OBTENER DATOS DE LA RESERVA
if (!isset($_GET['reserva_id'])) {
    header("Location: reserva.php");
    exit();
}

$reserva_id = $_GET['reserva_id'];
// SOLID: Inyección de Dependencias (Composición)
$database = new Database();
$dbConn = $database->getConnection();
$notificador = new NotificacionService();
// Inyectamos las dependencias al nacer
$pagoService = new PagoService($dbConn, $notificador);
$datosReserva = $pagoService->obtenerReservaParaPago($reserva_id, $_SESSION['usuario_id']);

if (!$datosReserva) {
    die("Reserva no encontrada o no te pertenece.");
}

// 3. CÁLCULO DE PRECIOS
$tasa_impuesto = 0.15;
$total = floatval($datosReserva['total_pagar']);
$subtotal = $total / (1 + $tasa_impuesto);
$impuesto = $total - $subtotal;

// 4. PROCESAMIENTO (POST)
$errores = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. Validaciones Comunes (Facturación)
    if (empty($_POST['billing_cedula'])) $errores[] = "La cédula es obligatoria.";
    if (empty($_POST['billing_address'])) $errores[] = "La dirección es obligatoria.";
    
    $metodo = $_POST['payment-method'];

    // B. Validación Específica por Método (APLICANDO OCP)
    try {
        // 1. Pedimos a la fábrica el validador correcto (sin usar if/else aquí)
        $validador = ValidadorFactory::obtenerValidador($metodo);
        
        // 2. Ejecutamos la validación polimórfica
        $erroresMetodo = $validador->validar($_POST, $_FILES);
        
        // 3. Unimos los errores
        $errores = array_merge($errores, $erroresMetodo);

    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }

    // C. Procesar si no hay errores
    if (empty($errores)) {
        $resultado = $pagoService->registrarPago($reserva_id, $total, $metodo);
        if ($resultado['success']) {
            header("Location: ver_comprobante.php?reserva_id=" . $reserva_id);
            exit();
        } else {
            $errores[] = "Error del sistema: " . $resultado['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pago - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --primary: #1A4B8C; --primary-dark: #0D2F5A; --success: #1B7A1B; --bg-light: #F8F9FA; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--bg-light); }
        .progress-container { background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .progress-bar { display: flex; justify-content: space-between; position: relative; }
        .progress-bar::before { content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 2px; background-color: #CED4DA; z-index: 1; }
        .progress-step { flex: 1; text-align: center; position: relative; z-index: 2; }
        .progress-step .step-circle { width: 40px; height: 40px; border-radius: 50%; background-color: #E9ECEF; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: bold; color: #495057; border: 2px solid #CED4DA; transition: all 0.3s ease; }
        .progress-step.active .step-circle { background-color: var(--primary); color: white; border-color: var(--primary); transform: scale(1.1); }
        .progress-step.completed .step-circle { background-color: var(--success); color: white; border-color: var(--success); }
        .step-block { display: none; animation: fadeIn 0.3s ease; }
        .step-block.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .radio-option { border: 2px solid #CED4DA; cursor: pointer; transition: all 0.2s; }
        .radio-option:hover { border-color: var(--primary); background-color: rgba(26, 75, 140, 0.05); }
        .radio-option input:checked + div { border-color: var(--primary); }
        .selected-method { border-color: var(--primary) !important; background-color: rgba(26, 75, 140, 0.1); }
    </style>
</head>
<body class="min-h-screen">

    <?php include 'menu_header.php'; ?>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-[#1A4B8C] mb-8 text-center">Procesar Pago de Reserva</h1>

        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-step active" id="p-step-1">
                    <div class="step-circle">1</div>
                    <div class="text-xs font-bold text-gray-600 mt-2">Facturación</div>
                </div>
                <div class="progress-step" id="p-step-2">
                    <div class="step-circle">2</div>
                    <div class="text-xs font-bold text-gray-600 mt-2">Método de Pago</div>
                </div>
                <div class="progress-step" id="p-step-3">
                    <div class="step-circle">3</div>
                    <div class="text-xs font-bold text-gray-600 mt-2">Confirmar</div>
                </div>
            </div>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
                <p class="font-bold">Error:</p>
                <ul class="list-disc ml-5 text-sm">
                    <?php foreach ($errores as $err) echo "<li>$err</li>"; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-lg">
                <form action="pagos.php?reserva_id=<?php echo $reserva_id; ?>" method="POST" enctype="multipart/form-data" id="paymentForm">
                    
                    <div id="step1" class="step-block active">
                        <h2 class="text-xl font-bold text-[#1A4B8C] mb-6 border-b pb-2">Datos de Facturación</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo</label>
                                <input type="text" value="<?php echo $_SESSION['nombre_completo']; ?>" class="w-full border bg-gray-100 rounded px-3 py-2 text-gray-600" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email" value="<?php echo $_SESSION['email']; ?>" class="w-full border bg-gray-100 rounded px-3 py-2 text-gray-600" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Cédula / RUC *</label>
                                <input type="text" name="billing_cedula" id="cedula" placeholder="0999999999" class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                                <input type="text" name="billing_phone" placeholder="099..." class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Dirección *</label>
                                <input type="text" name="billing_address" id="direccion" placeholder="Calle Principal y Secundaria" class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none">
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="button" onclick="validarPaso1()" class="bg-[#1A4B8C] text-white px-6 py-3 rounded-lg font-bold hover:opacity-90 transition">
                                Siguiente: Método de Pago
                            </button>
                        </div>
                    </div>

                    <div id="step2" class="step-block">
                        <h2 class="text-xl font-bold text-[#1A4B8C] mb-6 border-b pb-2">Selecciona Método</h2>
                        
                        <div class="space-y-4 mb-6">
                            <label class="radio-option flex items-center p-4 rounded-lg" onclick="selectMethod('tarjeta')">
                                <input type="radio" name="payment-method" value="tarjeta" checked class="mr-3 h-5 w-5 accent-[#1A4B8C]">
                                <div>
                                    <div class="font-bold text-gray-800">Tarjeta de Crédito / Débito</div>
                                    <div class="text-xs text-gray-500">Visa, Mastercard (Procesamiento Seguro)</div>
                                </div>
                            </label>
                            
                            <label class="radio-option flex items-center p-4 rounded-lg" onclick="selectMethod('transferencia')">
                                <input type="radio" name="payment-method" value="transferencia" class="mr-3 h-5 w-5 accent-[#1A4B8C]">
                                <div>
                                    <div class="font-bold text-gray-800">Transferencia Bancaria</div>
                                    <div class="text-xs text-gray-500">Adjuntar comprobante para validación</div>
                                </div>
                            </label>
                        </div>

                        <div id="card-details" class="bg-gray-50 p-5 rounded-lg border mb-6">
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Número de Tarjeta</label>
                                <input type="text" name="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expiración</label>
                                    <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">CVV</label>
                                    <input type="text" name="cvv" placeholder="123" maxlength="4" class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Titular</label>
                                <input type="text" name="cardName" placeholder="Como aparece en la tarjeta" class="w-full border rounded px-3 py-2">
                            </div>
                        </div>

                        <div id="transfer-details" class="hidden bg-yellow-50 p-5 rounded-lg border border-yellow-200 mb-6">
                            <p class="text-sm text-yellow-800 font-bold mb-2">Datos para depósito:</p>
                            <ul class="text-sm text-gray-600 list-disc ml-5 mb-4">
                                <li>Banco Pichincha - Cta Cte: 2100456789</li>
                                <li>RUC: 0990000000001 - ProServicios S.A.</li>
                            </ul>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Adjuntar Comprobante:</label>
                            <input type="file" name="voucher" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-[#1A4B8C] file:text-white hover:file:bg-opacity-90">
                        </div>

                        <div class="flex justify-between mt-8">
                            <button type="button" onclick="changeStep(1)" class="text-gray-500 font-bold hover:underline">Atrás</button>
                            <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 shadow-lg transform transition hover:scale-105">
                                Pagar $<?php echo number_format($total, 2); ?>
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-[#1A4B8C] text-white p-6 rounded-xl shadow-lg sticky top-24">
                    <h3 class="text-lg font-bold mb-4 border-b border-white/20 pb-2">Resumen de Orden</h3>
                    
                    <div class="mb-4">
                        <p class="text-xs opacity-70 uppercase">Servicio</p>
                        <p class="font-bold text-lg"><?php echo $datosReserva['nombre_servicio']; ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-xs opacity-70 uppercase">Fecha Reservada</p>
                        <p class="font-bold"><?php echo $datosReserva['fecha_reserva']; ?></p>
                    </div>

                    <div class="space-y-2 text-sm border-t border-white/20 pt-4 mt-4">
                        <div class="flex justify-between opacity-90">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between opacity-90">
                            <span>IVA (15%)</span>
                            <span>$<?php echo number_format($impuesto, 2); ?></span>
                        </div>
                    </div>

                    <div class="flex justify-between text-2xl font-extrabold pt-4 mt-2 border-t border-white/20">
                        <span>Total</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <div class="mt-6 text-center text-xs opacity-70 bg-black/10 p-2 rounded">
                        Transacción Segura SSL
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function changeStep(step) {
            document.querySelectorAll('.step-block').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
            
            const p1 = document.getElementById('p-step-1');
            const p2 = document.getElementById('p-step-2');
            
            if(step === 2) {
                p1.classList.add('completed'); p1.classList.remove('active');
                p2.classList.add('active');
            } else {
                p1.classList.add('active'); p1.classList.remove('completed');
                p2.classList.remove('active');
            }
        }

        function validarPaso1() {
            const cedula = document.getElementById('cedula').value;
            const dir = document.getElementById('direccion').value;
            if(cedula.trim() === '' || dir.trim() === '') {
                alert('Por favor completa la Cédula y la Dirección para continuar.');
                return;
            }
            changeStep(2);
        }

        function selectMethod(method) {
            const card = document.getElementById('card-details');
            const transfer = document.getElementById('transfer-details');
            
            if(method === 'tarjeta') {
                card.classList.remove('hidden');
                transfer.classList.add('hidden');
            } else {
                card.classList.add('hidden');
                transfer.classList.remove('hidden');
            }

            document.querySelectorAll('.radio-option').forEach(el => el.classList.remove('selected-method'));
            event.currentTarget.classList.add('selected-method');
        }
        
        document.querySelector('.radio-option').classList.add('selected-method');
    </script>
</body>
</html>