<?php
// Archivo: public/registro.php
require_once '../src/Servicios/AuthService.php';

$mensaje = "";
$tipoMensaje = "";

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $auth = new AuthService();
    $resultado = $auth->registrar(
        $_POST['nombre'], 
        $_POST['apellido'], 
        $_POST['email'], 
        $_POST['password'], 
        $_POST['phone']
    );

    if ($resultado['success']) {
        // Redirigir al login si fue exitoso
        header("Location: login.php?registro=exitoso");
        exit();
    } else {
        $mensaje = $resultado['message'];
        $tipoMensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="min-h-screen flex items-center justify-center p-6 bg-gray-100">

    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden max-w-3xl w-full p-8 sm:p-12">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-[#1A4B8C]">Crear Cuenta</h1>
            <p class="text-gray-500 mt-2">Regístrate para reservar servicios</p>
        </div>

        <?php if (!empty($mensaje)): ?>
            <div class="p-3 rounded-xl bg-red-100 text-red-700 font-semibold mb-4 text-center">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nombres</label>
                    <input name="nombre" type="text" required 
                           class="w-full px-4 py-3 border rounded-xl focus:border-[#1A4B8C] outline-none" 
                           placeholder="Ej: Juan Carlos"
                           pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+"
                           title="Solo se permiten letras y espacios"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Apellidos</label>
                    <input name="apellido" type="text" required 
                           class="w-full px-4 py-3 border rounded-xl focus:border-[#1A4B8C] outline-none" 
                           placeholder="Ej: Pérez Lozano"
                           pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+"
                           title="Solo se permiten letras y espacios"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                </div>
            </div>

            <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Correo electrónico</label>
                    <input name="email" type="email" required 
                           class="w-full px-4 py-3 border rounded-xl focus:border-[#1A4B8C] outline-none" 
                           placeholder="tucorreo@mail.com"
                           oninput="this.value = this.value.toLowerCase()">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Teléfono</label>
                <input name="phone" type="tel" required 
                    class="w-full px-4 py-3 border rounded-xl focus:border-[#1A4B8C] outline-none" 
                    placeholder="0991234567"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Debe contener 10 dígitos numéricos"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Contraseña</label>
                    <input name="password" type="password" required class="w-full px-4 py-3 border rounded-xl" placeholder="••••••">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Confirmar contraseña</label>
                    <input name="password_confirm" type="password" required class="w-full px-4 py-3 border rounded-xl" placeholder="••••••">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#1A4B8C] text-white py-3 rounded-xl font-bold shadow-lg hover:opacity-90 transition">
                Registrarme
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            ¿Ya tienes cuenta? <a href="login.php" class="text-[#17A2B8] font-bold hover:underline">Inicia sesión</a>
        </p>
    </div>
</body>
</html>