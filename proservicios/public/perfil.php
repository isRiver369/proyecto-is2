<?php
// public/perfil.php
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/AuthService.php';

// 1. Proteger la página
Seguridad::requerirRol('cliente');

$auth = new AuthService();
$usuario_id = $_SESSION['usuario_id'];
$mensaje = "";
$tipoMensaje = "";

// 2. Procesar Formulario (si se envió guardar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $auth->actualizarPerfil(
        $usuario_id,
        $_POST['nombre'],
        $_POST['apellido'],
        $_POST['telefono']
    );

    if ($resultado['success']) {
        $mensaje = $resultado['message'];
        $tipoMensaje = "success";
    } else {
        $mensaje = $resultado['message'];
        $tipoMensaje = "error";
    }
}

// 3. Obtener datos actuales (siempre al final para mostrar lo actualizado)
$datos = $auth->obtenerDatosUsuario($usuario_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Datos - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .proservicios-primary { background-color: #1A4B8C; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <?php include 'menu_header.php'; ?>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-[#1A4B8C] mb-8">Mis Datos Personales</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="p-4 rounded-xl mb-6 text-center font-bold <?php echo $tipoMensaje == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden p-8">
            <form action="perfil.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Nombre</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($datos['nombre']); ?>" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Apellido</label>
                        <input type="text" name="apellido" value="<?php echo htmlspecialchars($datos['apellido']); ?>" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                        <input type="email" value="<?php echo htmlspecialchars($datos['email']); ?>" disabled
                               class="w-full border border-gray-200 bg-gray-100 text-gray-500 rounded-lg px-4 py-3 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">El correo no se puede modificar.</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($datos['telefono']); ?>" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]">
                    </div>
                </div>

                <div class="flex justify-end gap-4 border-t pt-6">
                    <a href="menucliente.php" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-600 font-bold hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-6 py-3 rounded-lg bg-[#1A4B8C] text-white font-bold hover:bg-opacity-90 transition shadow-lg">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>