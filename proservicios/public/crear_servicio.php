<?php
require_once '../src/Servicios/Seguridad.php';
require_once '../src/Servicios/AuthService.php';
require_once '../config/Database.php';
require_once '../src/Modelos/Servicio.php';

// 1. Verificar Rol (Proveedor o Admin)
Seguridad::requerirRol(['proveedor', 'administrador']);

$database = new Database();
$db = $database->getConnection();
$servicioModel = new Servicio($db);

// 2. Obtener Categorías para el formulario
$queryCats = "SELECT * FROM categorias ORDER BY nombre_categoria ASC";
$stmtCats = $db->prepare($queryCats);
$stmtCats->execute();
$categorias = $stmtCats->fetchAll(PDO::FETCH_ASSOC);

$mensaje = "";
$error = "";

// 3. PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Capturar datos
    $proveedor_id = $_SESSION['usuario_id']; // El usuario logueado es el proveedor
    $nombre = $_POST['nombre_servicio'];
    $categoria_id = $_POST['categoria_id'];
    $descripcion = $_POST['descripcion'];
    $modalidad = $_POST['modalidad'];
    $cupos = $_POST['cupos'];
    $precio = $_POST['precio'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $horario = $_POST['horario']; // String largo: "Mañana (08-10), Tarde (15-17)"
    $politicas = $_POST['politicas'];

    // Validaciones básicas
    if (empty($nombre) || empty($precio) || empty($cupos) || empty($fecha_inicio)) {
        $error = "Por favor complete los campos obligatorios.";
    } else {
        // Guardar
        $resultado = $servicioModel->crear(
            $proveedor_id,
            $categoria_id,
            $nombre,
            $precio,
            $descripcion,
            $horario,
            $politicas,
            $cupos,
            $modalidad,
            $fecha_inicio,
            $fecha_fin
        );

        if ($resultado) {
            // Redirigir al panel correspondiente según el rol
            $redirect = ($_SESSION['rol'] === 'administrador') ? 'admin_servicios.php' : 'panel_proveedor.php';
            header("Location: $redirect?msg=creado");
            exit();
        } else {
            $error = "Hubo un error al guardar el servicio en la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Servicio - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

    <?php include 'menu_header.php'; ?>

    <main class="max-w-4xl mx-auto px-4 py-10">
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-[#1A4B8C] p-6 text-white flex justify-between items-center">
                <h1 class="text-2xl font-bold">Publicar Nuevo Curso / Servicio</h1>
                <a href="panel_proveedor.php" class="text-sm bg-white/20 hover:bg-white/30 px-3 py-1 rounded transition">Cancelar</a>
            </div>

            <div class="p-8">
                
                <?php if($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                        <p class="font-bold">Error</p>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form action="crear_servicio.php" method="POST" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Curso / Servicio *</label>
                            <input type="text" name="nombre_servicio" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]" placeholder="Ej: Curso de Piano Avanzado">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categoría *</label>
                            <select name="categoria_id" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C] bg-white">
                                <option value="" disabled selected>-- Seleccione --</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?php echo $cat['categoria_id']; ?>"><?php echo $cat['nombre_categoria']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Modalidad *</label>
                            <select name="modalidad" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C] bg-white">
                                <option value="Presencial">Presencial</option>
                                <option value="Online">Online</option>
                                <option value="Híbrido">Híbrido</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Descripción Detallada</label>
                        <textarea name="descripcion" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]" placeholder="¿Qué aprenderá el estudiante?"></textarea>
                    </div>

                    <hr class="border-gray-200">

                    <h3 class="text-lg font-bold text-[#1A4B8C]">Logística y Precios</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cupos Totales *</label>
                            <input type="number" name="cupos" min="1" max="100" value="5" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]">
                            <small class="text-gray-500">Se irán restando automáticamente.</small>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Precio ($) *</label>
                            <input type="number" name="precio" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]" placeholder="0.00">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de Inicio *</label>
                            <input type="date" name="fecha_inicio" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Fecha de Finalización *</label>
                            <input type="date" name="fecha_fin" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]">
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <label class="block text-sm font-bold text-[#1A4B8C] mb-2">Horarios Disponibles *</label>
                        <input type="text" name="horario" required 
                               class="w-full border border-blue-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]" 
                               placeholder="Ej: Mañana (09:00 - 11:00), Tarde (16:00 - 18:00)">
                        
                        <p class="text-xs text-gray-600 mt-2">
                            <strong>¡Importante!</strong> Separa los turnos con <strong>comas (,)</strong> e incluye la palabra 
                            <strong>"Mañana"</strong> o <strong>"Tarde"</strong> para que el sistema los ordene correctamente en el catálogo.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Políticas / Requisitos (Opcional)</label>
                        <textarea name="politicas" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-[#1A4B8C]" placeholder="Ej: Traer laptop propia, Ropa cómoda..."></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 transition shadow-lg transform hover:scale-105">
                            Publicar Curso
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</body>
</html>