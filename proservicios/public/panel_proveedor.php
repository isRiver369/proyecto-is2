<?php
session_start();
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
            $_POST['cupos'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin'],
        );
        $_SESSION['mensaje'] = $creado ? "¡Servicio creado con éxito!" : "Error al crear servicio.";
        header("Location: panel_proveedor.php");
        exit;
    }

    // B. ELIMINAR SERVICIO
    if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_servicio') {

        $servicio_id = $_POST['servicio_id'];
        // Verificar si el servicio tiene reservas
        $revisarReservas = $reservaService->contarReservasPorServicio($servicio_id);

        if ($revisarReservas > 0) {
             $_SESSION['error_eliminar'] = "No puedes eliminar un servicio que ya tiene reservas activas.";
            header("Location: panel_proveedor.php");
            exit;
        }
        $eliminado = $servicioModel->eliminar($servicio_id, $usuario_id);

        $_SESSION['mensaje'] = $eliminado ? "Servicio eliminado." : "Error al eliminar.";
        header("Location: panel_proveedor.php");
        exit;
    }

    // C. ACTUALIZAR PERFIL (BIO/PORTAFOLIO)
    if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_perfil') {
        $bio = $_POST['bio'] ?? "";
        $portafolio = $_POST['portafolio'] ?? "";

        $ok = $authService->actualizarPerfilProveedor($usuario_id, $bio, $portafolio);

        $_SESSION['mensaje'] = $ok ? "Perfil actualizado correctamente." 
                                : "Error al actualizar el perfil.";

        header("Location: panel_proveedor.php");
        exit;
    }
}

// 4. Obtener Datos para la Vista
$misServicios = $servicioModel->obtenerPorProveedor($usuario_id);
$miPerfil = $authService->obtenerUsuarioPorId($usuario_id); // Trae nombre, email. bio, portafolio
$misReservas = $reservaService->obtenerReservasPorProveedor($usuario_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Proveedor - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .active-tab { border-bottom: 2px solid #1A4B8C; color: #1A4B8C; font-weight: bold; }
        input::placeholder,
        textarea::placeholder {
            font-style: italic;
        }
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
        
        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center font-bold">
                <?php 
                    echo $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']); 
                ?>
            </div>
        <?php endif; ?>

        <div class="flex border-b border-gray-200 mb-6">
            <button onclick="switchTab('servicios')" id="tab-servicios" class="active-tab px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Mis Servicios</button>
            <button onclick="switchTab('perfil')" id="tab-perfil" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Mi Perfil (Bio)</button>
            <button onclick="switchTab('reservas')" id="tab-reservas" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Reservas</button>
            <button onclick="switchTab('agenda')" id="tab-agenda" class="px-6 py-3 text-gray-500 hover:text-[#1A4B8C] transition">Agenda</button>
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
                    
                    <!-- Fecha: -->
                    <div class="md:col-span-2">
                        <h4 class="text-lg font-bold text-black mb-2">Fechas:</h4>
                    </div>

                    <!-- Fecha inicio -->
                    <div class="flex flex-col">
                        <label class="text-base font-bold text-black mb-1">Inicio</label>
                        <input 
                            type="date" 
                            name="fecha_inicio" 
                            required 
                            class="border p-2 rounded"
                        >
                    </div>

                    <!-- Fecha fin -->
                    <div class="flex flex-col">
                        <label class="text-base font-bold text-black mb-1">Fin</label>
                        <input 
                            type="date" 
                            name="fecha_fin" 
                            required 
                            class="border p-2 rounded"
                        >
                    </div>

                    <button type="submit" class="bg-green-600 text-white font-bold py-2 rounded md:col-span-2 hover:bg-green-700 transition">
                        Publicar Servicio
                    </button>
                </form>
            </div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Servicios</h2> 
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
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Mi Perfil Profesional</h2>

            <div class="bg-white rounded-xl shadow-lg p-8 grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- COLUMNA IZQUIERDA: FOTO + PORTAFOLIO + CONTACTO -->
                <div class="flex flex-col items-center">

                    <!-- FOTO -->
                    <div class="w-48 h-48 bg-gray-200 rounded-xl flex items-center justify-center text-gray-500 mb-4">
                        Subir foto
                    </div>

                    <!-- ENLACE A PORTAFOLIO -->
                    <div class="w-full mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Enlace a Portafolio (URL)</label>
                        <input type="text" name="portafolio"
                            value="<?= htmlspecialchars($miPerfil['portafolio_url'] ?? '') ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]"
                            placeholder="https://mipagina.com">
                    </div>

                    <!-- CONTACTO -->
                    <div class="w-full mt-[-10px] mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Contacto</label>
                        <input type="text" name="contacto"
                            value="<?= htmlspecialchars($miPerfil['contacto'] ?? '') ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]"
                            placeholder="Número de teléfono/correo/redes sociales">
                    </div>
                </div>

                <!-- CAMPOS DE PERFIL -->
                <div class="lg:col-span-2">

                    <!-- DATOS NO EDITABLES -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Nombre</label>
                            <input type="text" disabled
                                value="<?= htmlspecialchars($miPerfil['nombre']) ?>"
                                class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg px-4 py-3 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Apellido</label>
                            <input type="text" disabled
                                value="<?= htmlspecialchars($miPerfil['apellido']) ?>"
                                class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-lg px-4 py-3 cursor-not-allowed">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                            <input type="email" disabled
                                value="<?= htmlspecialchars($miPerfil['email']) ?>"
                                class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-4 py-3 cursor-not-allowed">
                        </div>
                    </div>

                    <!-- CAMPOS EDITABLES -->
                    <form method="POST">
                        <input type="hidden" name="accion" value="actualizar_perfil">

                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">Biografía / Acerca de mí</label>
                            <textarea name="bio" rows="4"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]"
                                placeholder="Cuenta sobre ti..."><?= htmlspecialchars($miPerfil['bio'] ?? '') ?></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="view-reservas" class="hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Reservas</h2>
            
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

                        <?php while ($r = $misReservas->fetch(PDO::FETCH_ASSOC)): ?>

                        <tr>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <?php echo $r['cliente']; ?>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <?php echo $r['servicio_nombre']; ?>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                <?php echo $r['fecha_reserva']; ?>
                            </td>

                            <td class="px-6 py-4">
                                <?php if ($r['estado'] === 'pagada'): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Pagada</span>
                                <?php elseif ($r['estado'] === 'cancelada'): ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Cancelada</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-gray-200 text-gray-700 text-xs font-semibold rounded-full">
                                        <?php echo ucfirst($r['estado']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="view-agenda" class="hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Agenda de mis servicios</h2>

            <div class="bg-white p-6 rounded-xl shadow border border-gray-200 overflow-x-auto">
                <table class="min-w-max border-collapse">
                    <thead>
                        <tr>
                            <th class="border p-3 bg-gray-100 font-bold text-gray-700 w-32"> </th>

                            <!-- Columnas - horas -->
                            <?php for ($h = 0; $h <= 23; $h++): 
                                    $hora = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
                            ?>
                                <th class="border p-3 bg-gray-100 text-xs font-semibold text-gray-600">
                                    <?= $hora ?>
                                </th>
                            <?php endfor; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                            $dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
                            foreach ($dias as $dia):
                        ?>
                            <tr class="text-center">
                                <!-- Nombre del día de la semana-->
                                <td class="border p-3 bg-gray-50 font-semibold text-gray-700">
                                    <?= $dia ?>
                                </td>

                                <!-- Columnas por hora -->
                                <?php for ($h = 0; $h <= 23; $h++): ?>
                                    <td class="border p-3 text-sm text-gray-600 hover:bg-blue-50 cursor-pointer">
                                        <!-- Espacio para futuros servicios, eventos o proyectos -->
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function switchTab(tabId) {
            // Ocultar contenidos
            ['servicios', 'perfil', 'reservas', 'agenda'].forEach(t => {
                document.getElementById('view-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).classList.remove('active-tab');
            });
            // Mostrar seleccionado
            document.getElementById('view-' + tabId).classList.remove('hidden');
            document.getElementById('tab-' + tabId).classList.add('active-tab');
        }
    </script>

    <!-- Error al intentar eliminar un servicio ya reservado -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_SESSION['error_eliminar'])): ?>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Política infringida',
            text: '<?php echo $_SESSION['error_eliminar']; ?>',
            confirmButtonColor: '#1A4B8C'
        });
        </script>
    <?php unset($_SESSION['error_eliminar']); endif; ?>

</body>
</html>