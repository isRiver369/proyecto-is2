<?php
// 1. SEGURIDAD Y DEPENDENCIAS
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/PagoService.php';
require_once '../src/Servicios/PaymentStrategies.php'; 
require_once '../src/Servicios/AuthService.php';

Seguridad::requerirRol('cliente');

// Verificamos que esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// OBTENEMOS LOS DATOS GUARDADOS
$auth = new AuthService();
$datosUsuario = $auth->obtenerUsuarioPorId($_SESSION['usuario_id']);

// Preparamos las variables (si no tiene teléfono guardado, queda vacío)
$telefonoPre = isset($datosUsuario['telefono']) ? $datosUsuario['telefono'] : '';
$direccionPre = isset($datosUsuario['direccion']) ? $datosUsuario['direccion'] : ''; // Si tuvieras dirección guardada

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

// 3. CÁLCULO DE PRECIOS (DINÁMICO)
require_once '../src/Servicios/AdminDashboard.php'; // Importamos el dashboard
$adminDashboard = new AdminDashboard();
$config = $adminDashboard->obtenerConfiguracion();

// Verificamos si existe la tasa en la BD, si no, usamos 15% por defecto
if ($config && isset($config['tasa_impuesto'])) {
    $tasa_impuesto = floatval($config['tasa_impuesto']) / 100; // Ej: 12 convertimos a 0.12
} else {
    $tasa_impuesto = 0.15; 
}

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
                                <input type="text" name="nombre" 
                                       value="<?php echo htmlspecialchars($datosUsuario['nombre'] . ' ' . $datosUsuario['apellido']); ?>" 
                                       readonly 
                                       class="w-full border bg-gray-100 rounded px-3 py-2 text-gray-500 focus:outline-none cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email" name="email"
                                       value="<?php echo htmlspecialchars($datosUsuario['email']); ?>" 
                                       readonly 
                                       class="w-full border bg-gray-100 rounded px-3 py-2 text-gray-500 focus:outline-none cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Cédula / RUC *</label>
                                <input type="text" 
                                    name="billing_cedula" 
                                    id="cedula" 
                                    placeholder="Ingrese 10 o 13 dígitos" 
                                    maxlength="13" 
                                    class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none transition-colors"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                                <input type="text" name="telefono" 
                                       placeholder="099..."
                                       value="<?php echo htmlspecialchars($telefonoPre); ?>" 
                                       class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none transition-colors">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Dirección *</label>
                                <input type="text" name="billing_address" id="direccion" 
                                       placeholder="Calle Principal y Secundaria" 
                                       class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none transition-colors">
                            </div>

                        </div>
                        <div class="mt-8 flex items-center justify-between">
                            <a href="menucliente.php" class="group flex items-center gap-2 text-gray-500 hover:text-red-600 font-bold transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:-translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Cancelar y Salir
                            </a>
                            <button type="button" onclick="validarPaso1()" class="bg-[#1A4B8C] text-white px-6 py-3 rounded-lg font-bold hover:opacity-90 transition shadow-md flex items-center gap-2">
                                Siguiente: Método de Pago
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
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
                                <input type="text" 
                                    name="cardNumber" 
                                    id="cardNumber" 
                                    placeholder="0000 0000 0000 0000" 
                                    maxlength="19" 
                                    class="w-full border rounded px-3 py-2 transition-colors focus:border-[#1A4B8C] outline-none">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expiración</label>
                                    <input type="text" 
                                        name="expiry" 
                                        id="cardExpiry" 
                                        placeholder="MM/YY" 
                                        maxlength="5" 
                                        class="w-full border rounded px-3 py-2 transition-colors focus:border-[#1A4B8C] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">CVV</label>
                                    <input type="text" 
                                        name="cvv" 
                                        id="cardCvv" 
                                        placeholder="123" 
                                        maxlength="4" 
                                        class="w-full border rounded px-3 py-2 transition-colors focus:border-[#1A4B8C] outline-none">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Titular</label>
                                <input type="text" name="cardName" placeholder="Como aparece en la tarjeta" class="w-full border rounded px-3 py-2 focus:border-[#1A4B8C] outline-none">
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
                            <button type="button" onclick="irAConfirmar()" class="bg-[#1A4B8C] text-white px-8 py-3 rounded-lg font-bold hover:opacity-90 shadow-lg">
                                Revisar Orden
                            </button>
                        </div>
                    </div>
                    <div id="step3" class="step-block">
                        <h2 class="text-xl font-bold text-[#1A4B8C] mb-6 border-b pb-2">Confirmar Pedido</h2>
                        
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 space-y-4 mb-6">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Cliente:</span>
                                <span class="font-bold text-gray-800" id="resumen-nombre"></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Enviar factura a:</span>
                                <span class="font-bold text-gray-800 text-right" id="resumen-correo"></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Cédula/RUC:</span>
                                <span class="font-bold text-gray-800" id="resumen-cedula"></span>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 uppercase font-bold mb-2">Método de Pago Seleccionado</p>
                                <div class="flex items-center bg-white p-3 rounded border border-blue-100">
                                    <span id="resumen-metodo-icon" class="text-2xl mr-3">💳</span>
                                    <div>
                                        <p class="font-bold text-[#1A4B8C]" id="resumen-metodo-titulo">Tarjeta de Crédito</p>
                                        <p class="text-sm text-gray-500" id="resumen-metodo-detalle">**** **** **** 0000</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start mb-6 bg-yellow-50 p-3 rounded text-sm text-yellow-800">
                            <input type="checkbox" required class="mt-1 mr-2" id="check-terms">
                            <label for="check-terms">
                                Confirmo que los datos son correctos y acepto realizar el pago por el monto total de 
                                <strong class="text-black">$<?php echo number_format($total, 2); ?></strong>.
                            </label>
                        </div>

                        <div class="flex justify-between mt-8">
                            <button type="button" onclick="changeStep(2)" class="text-gray-500 font-bold hover:underline">Atrás</button>
                            
                            <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 shadow-lg transform transition hover:scale-105 flex items-center gap-2">
                                <span></span> Confirmar y Pagar
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
                            <span>IVA (<?php echo $tasa_impuesto * 100; ?>%)</span>
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
            // 1. Mostrar el bloque de contenido correcto (Esto ya funcionaba bien)
            document.querySelectorAll('.step-block').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
            
            // 2. Actualizar la barra de progreso (Círculos)
            const p1 = document.getElementById('p-step-1');
            const p2 = document.getElementById('p-step-2');
            const p3 = document.getElementById('p-step-3'); 

            // Limpiamos estados para evitar conflictos al ir y volver (atrás/adelante)
            // Quitamos 'active' y 'completed' de todos primero
            [p1, p2, p3].forEach(p => p.classList.remove('active', 'completed'));

            if(step === 1) {
                // Paso 1: Solo el primero está activo
                p1.classList.add('active');
            } 
            else if(step === 2) {
                // Paso 2: El 1 completado, el 2 activo
                p1.classList.add('completed');
                p2.classList.add('active');
            } 
            else if(step === 3) {
                // Paso 3: El 1 y 2 completados, el 3 activo
                p1.classList.add('completed');
                p2.classList.add('completed');
                p3.classList.add('active');
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

        // Reemplaza tu función validarPaso1 existente por esta:
        function validarPaso1() {
            const cedula = document.getElementById('cedula').value;
            const dir = document.getElementById('direccion').value;
            
            // 1. Validar que no estén vacíos
            if(cedula.trim() === '' || dir.trim() === '') {
                alert('Por favor completa la Cédula y la Dirección para continuar.');
                return;
            }

            // 2. NUEVO: Validar longitud exacta (10 para Cédula, 13 para RUC)
            if (cedula.length !== 10 && cedula.length !== 13) {
                alert('Error: La Cédula debe tener 10 dígitos o el RUC 13 dígitos.');
                // Opcional: poner el borde rojo para indicar error
                document.getElementById('cedula').style.borderColor = 'red';
                return;
            }

            // Si todo está bien, quitamos el rojo (por si acaso) y avanzamos
            document.getElementById('cedula').style.borderColor = '#e5e7eb'; // Color gris original
            changeStep(2);
        }

        // --- LÓGICA DE FORMATO DE TARJETA ---
        document.addEventListener('DOMContentLoaded', function() {
        
        const inputCard = document.getElementById('cardNumber');
        const inputExpiry = document.getElementById('cardExpiry');
        const inputCvv = document.getElementById('cardCvv');

        // 1. Formato Tarjeta: "0000 0000 0000 0000"
        if(inputCard) {
            inputCard.addEventListener('input', function(e) {
                // Eliminamos todo lo que no sea número
                let value = e.target.value.replace(/\D/g, '');
                
                // Limitamos a 16 dígitos reales
                value = value.substring(0, 16);
                
                // Agregamos espacio cada 4 dígitos
                // La expresión regular /.{1,4}/g busca grupos de 4 caracteres
                let sections = value.match(/.{1,4}/g);
                
                if (sections) {
                    e.target.value = sections.join(' ');
                } else {
                    e.target.value = value;
                }
            });
        }

        // 2. Formato Expiración: "MM/YY"
        if(inputExpiry) {
            inputExpiry.addEventListener('input', function(e) {
                // Eliminamos todo lo que no sea número
                let value = e.target.value.replace(/\D/g, '');
                
                // Limitamos a 4 dígitos (MMYY)
                value = value.substring(0, 4);

                // Si ya escribió más de 2 números, ponemos la barra
                if (value.length >= 3) {
                    e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
                } else {
                    e.target.value = value;
                }
            });
            
            // Validar que el mes no sea mayor a 12 (Opcional, mejora UX)
            inputExpiry.addEventListener('blur', function(e) {
               let value = e.target.value.replace(/\D/g, '');
               if(value.length >= 2) {
                   let mes = parseInt(value.substring(0, 2));
                   if(mes > 12 || mes === 0) {
                       alert('Mes inválido');
                       e.target.value = '';
                   }
               }
            });
        }

        // 3. Formato CVV: Solo números
        if(inputCvv) {
            inputCvv.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
        }
    });
    // --- FIN LÓGICA DE FORMATO DE TARJETA ---
    function irAConfirmar() {
        // 1. Validar que haya llenado el paso 2 (Tarjeta o Transferencia)
        const metodo = document.querySelector('input[name="payment-method"]:checked').value;
        
        if (metodo === 'tarjeta') {
            const cardNum = document.getElementById('cardNumber').value;
            const expiry = document.getElementById('cardExpiry').value;
            const cvv = document.getElementById('cardCvv').value;
            
            if (cardNum.length < 15 || expiry.length < 4 || cvv.length < 3) {
                alert("Por favor completa los datos de la tarjeta correctamente.");
                return;
            }
            
            // Llenar resumen visual
            document.getElementById('resumen-metodo-titulo').innerText = "Tarjeta de Crédito / Débito";
            // Mostrar solo los últimos 4 dígitos
            const last4 = cardNum.slice(-4);
            document.getElementById('resumen-metodo-detalle').innerText = `Terminada en **** ${last4} | Exp: ${expiry}`;
            document.getElementById('resumen-metodo-icon').innerText = "💳";
        } 
        else {
            // Es transferencia
            const archivo = document.querySelector('input[name="voucher"]').value;
            if (archivo === '') {
                alert("Por favor adjunta la foto del comprobante.");
                return;
            }
            document.getElementById('resumen-metodo-titulo').innerText = "Transferencia Bancaria";
            document.getElementById('resumen-metodo-detalle').innerText = "Comprobante adjuntado para validación.";
            document.getElementById('resumen-metodo-icon').innerText = "🏦";
        }

        // 2. Copiar datos del Paso 1 al Resumen
        // Nota: 'nombre' es el name del input en paso 1
        const nombre = document.querySelector('input[name="nombre"]').value; 
        const email = document.querySelector('input[name="email"]').value;
        const cedula = document.getElementById('cedula').value;

        document.getElementById('resumen-nombre').innerText = nombre;
        document.getElementById('resumen-correo').innerText = email;
        document.getElementById('resumen-cedula').innerText = cedula;

        // 3. Avanzar al paso 3
        changeStep(3);
    }


    </script>
    
</body>
</html>