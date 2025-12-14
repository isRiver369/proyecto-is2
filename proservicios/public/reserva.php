<?php
session_start();
require_once '../src/Servicios/ReservaService.php';
require_once '../config/Database.php';
require_once '../src/Servicios/Seguridad.php';
Seguridad::requerirRol('cliente');

// Seguridad
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos
$db = (new Database())->getConnection();
$reservaService = new ReservaService($db); // <--- Inyección de Dependencias
$stmt = $reservaService->obtenerReservasPorUsuario($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas - ProServicios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">
    
    <?php include 'menu_header.php';  ?>

    <main class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-[#1A4B8C] mb-8">Mis Reservas Activas</h1>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'reserva_creada'): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                ¡Reserva realizada con éxito!
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#F8F9FA]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if ($stmt->rowCount() > 0): ?>
                        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-[#1A4B8C]">
                                    #<?php echo $row['reserva_id']; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <?php echo $row['nombre_servicio']; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $row['fecha_reserva']; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold">
                                    $<?php echo number_format($row['total_pagar'], 2); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $row['estado'] == 'pagada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo ucfirst($row['estado']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if($row['estado'] == 'pendiente' || $row['estado'] == 'confirmada'): ?>
        
                                    <?php if($row['estado'] == 'pendiente'): ?>
                                        <a href="pagos.php?reserva_id=<?php echo $row['reserva_id']; ?>" 
                                        class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-xs font-bold mr-2">
                                        Pagar
                                        </a>
                                    <?php endif; ?>

                                    <a href="cancelar_reserva.php?id=<?php echo $row['reserva_id']; ?>" 
                                    onclick="return confirm('¿Estás seguro que deseas cancelar esta reserva? El cupo quedará libre.');"
                                    class="text-red-600 hover:text-red-900 text-xs font-bold underline">
                                    Cancelar
                                    </a>

                                    <?php elseif($row['estado'] == 'cancelada'): ?>
                                        <span class="text-gray-400 italic text-xs">Cancelada</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Finalizada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No tienes reservas activas todavía. ¡Ve al catálogo!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>