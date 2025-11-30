<?php
// Archivo: public/login.php
require_once '../src/Servicios/AuthService.php';

$error = "";
$successMsg = "";

// Mensaje si viene de registro
if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') {
    $successMsg = "¡Cuenta creada! Ahora puedes iniciar sesión.";
}

// Procesar Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auth = new AuthService();
    $resultado = $auth->login($_POST['email'], $_POST['password']);

    if ($resultado['success']) {
        // Verificamos el rol guardado en la sesión para decidir a dónde ir
        $rol = $_SESSION['rol'];

        if ($rol === 'proveedor') {
            header("Location: panel_proveedor.php"); 
        } elseif ($rol === 'administrador') {
            header("Location: admin.php");           
        } else {
            header("Location: menucliente.php");    
        }
        exit();
    } else {
        $error = $resultado['message'];
    }
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .bg-brand { background-color: #1A4B8C; }
        .text-brand { color: #1A4B8C; }
        .btn-brand { background-color: #1A4B8C; }
        .btn-brand:hover { background-color: #0D2F5A; }
    </style>
</head>
<body class="h-screen w-full flex overflow-hidden bg-gray-50">

    <div class="hidden lg:flex w-1/2 bg-brand flex-col justify-center items-center relative p-12 text-white text-center">
        
        <h2 class="text-4xl font-extrabold mb-4">Bienvenido de vuelta</h2>
        <p class="text-lg text-blue-100 mb-10 max-w-md">
            Accede a diversos servicios de profesionales verificados.
        </p>

        <div class="w-64 h-64 bg-white rounded-full flex justify-center items-center shadow-2xl mb-16 relative">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="w-80 h-80">
                        <!-- Fondo (Escudo/Contenedor) - Invertido para que resalte sobre azul -->
                        <path d="M100 10 C150 10, 190 50, 190 100 C190 150, 150 190, 100 190 C50 190, 10 150, 10 100 C10 50, 50 10, 100 10 Z" fill="#FFFFFF"/>
                        
                        <!-- Elemento 1: Libro (Cursos) -->
                        <path d="M60 50 Q80 45, 100 50 L100 150 Q80 155, 60 150 L60 50 M100 50 Q120 45, 140 50 L140 150 Q120 155, 100 150" fill="#1A4B8C" />
                        
                        <!-- Elemento 2: Checkmark (Reservas) -->
                        <path d="M80 100 L110 130 L160 70 L145 55 L110 100 L95 85 Z" fill="#17A2B8" stroke="#FFFFFF" stroke-width="8" stroke-linejoin="round" stroke-linecap="round"/>
                    </svg>
        </div>

        <p class="absolute bottom-10 text-sm text-blue-200">
            Tu plataforma de servicios favoritos.
        </p>
    </div>

    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white p-8 overflow-y-auto">
        
        <div class="w-full max-w-md">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-brand mb-2">Iniciar Sesión</h1>
                <p class="text-gray-500">Ingresa tus datos para continuar</p>
            </div>

            <?php if (!empty($successMsg)): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-center text-sm font-semibold">
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-center text-sm font-semibold">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-sm text-gray-700 mb-2">Correo Electrónico</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1A4B8C] focus:ring-1 focus:ring-[#1A4B8C] outline-none transition"
                           placeholder="tucorreo@gmail.com">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">Contraseña</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-[#1A4B8C] focus:ring-1 focus:ring-[#1A4B8C] outline-none transition"
                           placeholder="••••••••">
                </div>

                <div class="flex justify-between items-center text-sm">
                    <label class="flex items-center text-gray-500 cursor-pointer">
                        <input type="checkbox" class="mr-2 rounded border-gray-300 text-brand focus:ring-brand">
                        Recordarme
                    </label>
                    <a href="#" class="text-brand hover:underline font-medium">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="w-full btn-brand text-white py-3 rounded-lg font-bold shadow-lg transition transform hover:scale-[1.02]">
                    Iniciar Sesión
                </button>

            </form>

            <p class="mt-8 text-center text-sm text-gray-500">
                ¿Aún no tienes cuenta? 
                <a href="registro.php" class="text-brand font-bold hover:underline">Regístrate Aquí</a>
            </p>

            <div class="mt-6 border-t pt-6 border-gray-100 text-center">
                <a href="menuvisitante.php" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-brand transition group">
                    <span>Volver a la pagina de Inicio</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

        </div>
    </div>
</body>
</html>