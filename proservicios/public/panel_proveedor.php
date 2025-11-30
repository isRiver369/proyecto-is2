<?php
require_once '../src/Servicios/Seguridad.php';
require_once '../config/Database.php';
require_once '../src/Modelos/Servicio.php';
require_once '../src/Servicios/AuthService.php'; // Para perfil
require_once '../src/Servicios/ReservaService.php'; // Para ver reservas

// 1. Seguridad
Seguridad::requerirRol('proveedor');
$usuario_id = $_SESSION['usuario_id'];

// 2. Inicializar
$db = (new Database())->getConnection();
$servicioModel = new Servicio($db);
$authService = new AuthService();
$reservaService = new ReservaService(); 

// 3. Procesar Acciones (Crear/Eliminar Servicio)
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. CREAR SERVICIO
    if (isset($_POST['accion']) && $_POST['accion'] == 'crear_servicio') {
        $creado = $servicioModel->crear(
            $usuario_id,
            $_POST['nombre'],
            $_POST['precio'],
            $_POST['descripcion'],
            $_POST['horario'],
            $_POST['politicas'],
            $_POST['cupos']
        );
        $mensaje = $creado ? "¡Servicio creado con éxito!" : "Error al crear servicio.";
    }

    // B. ELIMINAR SERVICIO
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_servicio') {
        $eliminado = $servicioModel->eliminar($_POST['servicio_id'], $usuario_id);
        $mensaje = $eliminado ? "Servicio eliminado." : "Error al eliminar.";
    }

    // C. ACTUALIZAR PERFIL (BIO/PORTAFOLIO)
    if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_perfil') {
        $mensaje = "Perfil actualizado (Simulado - falta SQL update en AuthService).";
    }
}

// 4. Obtener Datos para la Vista
$misServicios = $servicioModel->obtenerPorProveedor($usuario_id);
$miPerfil = $authService->obtenerUsuarioPorId($usuario_id); // Trae nombre, email. bio, portafolio
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Proveedor - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .active-tab { border-bottom: 2px solid #1A4B8C; color: #1A4B8C; font-weight: bold; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-[#1A4B8C] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2 font-bold text-xl">
                <span>ProServicios | Proveedor</span>
            </div>
            <div class="flex gap-4 items-center">
                <span>Hola, <?php echo $_SESSION['nombre_completo']; ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        
        <?php if($mensaje): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center font-bold">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="flex border-b border-gray-200 mb-6">
            <button onclick="switchTab('servicios')" id="tab-servicios" class="active-tab px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Mis Servicios</button>
            <button onclick="switchTab('perfil')" id="tab-perfil" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Mi Perfil (Bio)</button>
            <button onclick="switchTab('reservas')" id="tab-reservas" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Calendario / Reservas</button>
        </div>

        <div id="view-servicios" class="block">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Gestionar Oferta</h2>
                </div>

            <div class="bg-white p-6 rounded-xl shadow mb-8 border border-gray-200">
                <h3 class="text-lg font-bold text-[#1A4B8C] mb-4">+ Crear Nuevo Servicio</h3>
                <form action="panel_proveedor.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="accion" value="crear_servicio">
                    
                    <input type="text" name="nombre" placeholder="Nombre del Servicio (ej: Clase de Yoga)" required class="border p-2 rounded">
                    <input type="number" name="precio" placeholder="Precio ($)" step="0.01" required class="border p-2 rounded">
                    
                    <input type="text" name="horario" placeholder="Horario (ej: Lun-Vie 15:00)" class="border p-2 rounded">
                    <input type="number" name="cupos" placeholder="Cupos Máximos" required class="border p-2 rounded">
                    
                    <textarea name="descripcion" placeholder="Descripción detallada..." class="border p-2 rounded md:col-span-2" rows="2"></textarea>
                    <textarea name="politicas" placeholder="Políticas (ej: Cancelar 24h antes)" class="border p-2 rounded md:col-span-2 text-sm" rows="1"></textarea>
                    
                    <button type="submit" class="bg-green-600 text-white font-bold py-2 rounded md:col-span-2 hover:bg-green-700 transition">
                        Publicar Servicio
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($row = $misServicios->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition p-5 border-l-4 border-[#1A4B8C]">
                        <h4 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($row['nombre_servicio']); ?></h4>
                        <p class="text-gray-500 text-sm mb-2 h-10 overflow-hidden"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        
                        <div class="text-sm bg-gray-50 p-2 rounded mb-3">
                            <p><strong>Precio:</strong> $<?php echo number_format($row['precio'], 2); ?></p>
                            <p><strong>Cupos:</strong> <?php echo $row['cupo_maximo']; ?></p>
                            <p><strong>Horario:</strong> <?php echo $row['horario'] ?: 'No definido'; ?></p>
                        </div>

                        <form action="panel_proveedor.php" method="POST" onsubmit="return confirm('¿Seguro de eliminar este servicio?');">
                            <input type="hidden" name="accion" value="eliminar_servicio">
                            <input type="hidden" name="servicio_id" value="<?php echo $row['servicio_id']; ?>">
                            <button type="submit" class="text-red-500 text-sm font-bold hover:underline">Eliminar Servicio</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div id="view-perfil" class="hidden">
            <div class="bg-white p-8 rounded-xl shadow max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Mi Perfil Profesional</h2>
                <form action="panel_proveedor.php" method="POST" class="space-y-4">
                    <input type="hidden" name="accion" value="actualizar_perfil">
                    
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Biografía / Acerca de mí</label>
                        <textarea name="bio" rows="4" class="w-full border p-3 rounded" placeholder="Cuenta tu experiencia..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Enlace a Portafolio (URL)</label>
                        <input type="url" name="portafolio" class="w-full border p-3 rounded" placeholder="https://mipagina.com">
                    </div>

                    <button type="submit" class="bg-[#1A4B8C] text-white px-6 py-2 rounded font-bold hover:opacity-90">
                        Guardar Perfil
                    </button>
                </form>
            </div>
        </div>

        <div id="view-reservas" class="hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Agenda de Reservas</h2>
            
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Servicio</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        
                        <tr>
                            <td class="px-6 py-4 font-bold">Juan Pérez</td>
                            <td class="px-6 py-4">Clase de Piano</td>
                            <td class="px-6 py-4">2025-11-30</td>
                            <td class="px-6 py-4"><span class="bg-green-100 text-green-800 px-2 rounded-full text-xs">Pagada</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        function switchTab(tabId) {
            // Ocultar contenidos
            ['servicios', 'perfil', 'reservas'].forEach(t => {
                document.getElementById('view-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).classList.remove('active-tab');
            });
            // Mostrar seleccionado
            document.getElementById('view-' + tabId).classList.remove('hidden');
            document.getElementById('tab-' + tabId).classList.add('active-tab');
        }
    </script>
</body>
</html>