<?php
session_start();
require_once '../config/Database.php';
include_once 'obtener_categorias.php';

// 1. Verificar si es cliente para ingresar
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

// 2. VERIFICAR QUE SE ENVÍA EL ID DEL SERVICIO
if (!isset($_GET['servicio_id']) || empty($_GET['servicio_id'])) {
    header("Location: catalogo.php"); // o catálogo de servicios
    exit();
}

$servicio_id = intval($_GET['servicio_id']);

// 3. CONSULTAR INFO DEL SERVICIO
$database = new Database();
$conn = $database->getConnection(); // Establecer la conexión

$query = "SELECT s.*, u.nombre AS nombre_proveedor, u.apellido AS apellido_proveedor
          FROM servicios s
          INNER JOIN usuarios u ON s.proveedor_id = u.usuario_id
          WHERE s.servicio_id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":id", $servicio_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo "<p style='color:red; text-align:center;'>Servicio no encontrado.</p>";
    exit();
}

$servicio = $stmt->fetch(PDO::FETCH_ASSOC);
$categoriaObj = new Categoria();
$nombreCategoria = $categoriaObj->obtenerNombrePorId($servicio['categoria_id']);
$disponibilidad_texto = ($servicio['disponible'] == 1)
    ? 'Inscripciones abiertas'
    : 'Inscripciones cerradas';
// Modalidad
$modalidad = htmlspecialchars($servicio['modalidad']);

// Horario (texto largo)
$horario = nl2br(htmlspecialchars($servicio['horario']));

// Políticas (texto largo)
$politicas = nl2br(htmlspecialchars($servicio['politicas']));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Servicio - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'menu_header.php'; ?>
    <div class="max-w-4xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">

        <h1 class="text-3xl font-bold mb-6 text-[#1A4B8C]">
            <?= !empty($servicio['nombre_servicio']) ? htmlspecialchars($servicio['nombre_servicio']) : 'Sin definir'; ?>
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- COLUMNA IZQUIERDA -->
            <div>
                <h2 class="text-xl font-semibold mb-2 text-gray-800">
                    Descripción del servicio:
                </h2>
                
                <p class="text-gray-700 mb-4">
                    <?= !empty($servicio['descripcion']) ? nl2br(htmlspecialchars($servicio['descripcion'])) : 'Sin definir'; ?>
                </p>
                <hr class="my-6 border-gray-200">

                <p class="text-gray-600 mb-2">
                    <strong>Categoría:</strong>
                    <?= $nombreCategoria ? htmlspecialchars($nombreCategoria) : 'Sin definir'; ?>
                </p>
                
                <p class="text-gray-600 mb-2">
                    <strong>Modalidad:</strong>
                    <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-md text-sm font-medium">
                        <?= !empty($modalidad) ? $modalidad : 'Sin definir'; ?>
                    </span>
                </p>

                <?php if (!empty($servicio['ubicacion'])): ?>
                    <p class="text-gray-600 mb-2">
                        <strong>Ubicación:</strong>
                        <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded-md text-sm font-medium">
                            <?= htmlspecialchars($servicio['ubicacion']); ?>
                        </span>
                    </p>
                <?php else: ?>
                    <p class="text-gray-600 mb-2">
                        <strong>Ubicación:</strong>
                        <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded-md text-sm font-medium">
                            Sin definir
                        </span>
                    </p>
                <?php endif; ?>

                <div class="text-gray-600 mb-4">
                    <strong>Disponibilidad:</strong>
                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                        <?= ($servicio['disponible'] == 1)
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700'; ?>">
                        <?= !empty($disponibilidad_texto) ? $disponibilidad_texto : 'Sin definir'; ?>
                    </span>
                </div>
                <hr class="my-6 border-gray-200">

                <h2 class="text-xl font-semibold mb-2 text-gray-800">
                    Horarios:
                </h2>

                <p class="text-gray-600 mb-4">
                    <?= !empty($horario) ? $horario : 'Sin definir'; ?>
                </p>

                <h2 class="text-xl font-semibold mb-2 text-gray-800">
                    Políticas:
                </h2>

                <p class="text-gray-600">
                    <?= !empty($politicas) ? $politicas : 'Sin definir'; ?>
                </p>
            </div>

            <!-- COLUMNA DERECHA -->
            <div>
                <!-- Proveedor -->
                <div class="bg-gray-50 p-5 rounded-lg border mb-4">
                    <p class="text-gray-600">
                        <strong>Proveedor:</strong><br>
                        <?= htmlspecialchars($servicio['nombre_proveedor'] . ' ' . $servicio['apellido_proveedor']); ?>
                    </p>
                </div>

                <!-- Fechas y cupo -->
                <div class="bg-gray-50 p-5 rounded-lg border mb-4">
                    <p class="text-gray-600 mb-1">
                        <strong>Fecha de inicio:</strong>
                        <?= !empty($servicio['fecha_inicio']) ? htmlspecialchars($servicio['fecha_inicio']) : 'Sin definir'; ?>
                    </p>

                    <p class="text-gray-600 mb-1">
                        <strong>Fecha de fin:</strong>
                        <?= !empty($servicio['fecha_fin']) ? htmlspecialchars($servicio['fecha_fin']) : 'Sin definir'; ?>
                    </p>

                    <p class="text-gray-600">
                        <strong>Cupo máximo:</strong>
                        <?= !empty($servicio['cupos_restantes']) ? htmlspecialchars($servicio['cupos_restantes']) : 'Sin definir'; ?>
                    </p>
                </div>

                <!-- Precio -->
                <div class="bg-gray-50 p-5 rounded-lg border mb-6 text-center">
                    <p class="text-gray-600 mb-1 font-semibold">
                        Precio
                    </p>
                    <p class="text-2xl font-bold text-[#1A4B8C]">
                        $<?= !empty($servicio['precio']) ? number_format($servicio['precio'], 2) : 'Sin definir'; ?>
                    </p>
                </div>

                <!-- Botón reservar -->
                <form action="procesar_reserva.php" method="POST">
                    <input type="hidden" name="servicio_id" value="<?= $servicio['servicio_id']; ?>">
                    <input type="hidden" name="precio" value="<?= $servicio['precio']; ?>">
                    <input type="hidden" name="fecha" value="<?= $servicio['fecha_inicio']; ?>">
                    <input type="hidden" name="horario_elegido" value="General">

                    <button type="submit"
                        class="w-full py-3 font-bold text-white bg-[#1A4B8C] hover:bg-opacity-90 transition rounded-lg">
                        Agregar a mis reservas
                    </button>
                </form>

                <a href="catalogo.php"
                    class="block text-center mt-3 text-[#1A4B8C] hover:underline">
                    Volver al catálogo
                </a>
            </div>

        </div>

</body>
</html>