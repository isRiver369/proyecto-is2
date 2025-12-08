<?php
// public/admin.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$stats = $dashboard->obtenerEstadisticas();
$ultimasReservas = $dashboard->obtenerUltimasReservas();

function getEstadoClass($estado) {
    return match ($estado) {
        'confirmada', 'pagada' => 'status-success',
        'pendiente' => 'status-warning',
        'cancelada' => 'status-danger',
        default => 'status-info'
    };
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <header class="admin-header">
    <h1>ProServicios Admin</h1>
    <div class="header-actions">
      <a href="logout.php">Cerrar Sesión</a>
    </div>
  </header>

  <aside class="sidebar">
    <h2>Menú</h2>
    <ul>
      <li><a href="admin.php" class="active">Dashboard</a></li>
      <li><a href="admin_usuarios.php">Usuarios</a></li>
      <li><a href="admin_servicios.php">Servicios</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Dashboard General</h2>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-success">✅ Reserva eliminada correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">❌ No se pudo eliminar la reserva.</div>
    <?php endif; ?>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Usuarios</div>
        <div class="stat-value"><?php echo $stats['usuarios']; ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Ingresos</div>
        <div class="stat-value">$<?php echo number_format($stats['ingresos'], 2); ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Pendientes</div>
        <div class="stat-value"><?php echo $stats['pendientes']; ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Servicios</div>
        <div class="stat-value"><?php echo $stats['servicios_activos']; ?></div>
      </div>
    </div>

    <div class="table-container">
      <div class="table-header">
        <h3>Últimas Reservas</h3>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th> <th>Cliente</th> <th>Servicio</th> <th>Fecha</th> <th>Estado</th> <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ultimasReservas as $reserva): ?>
          <tr>
            <td>#RES<?php echo str_pad($reserva['reserva_id'], 3, '0', STR_PAD_LEFT); ?></td>
            <td><?php echo htmlspecialchars($reserva['nombre_cliente']); ?></td>
            <td><?php echo htmlspecialchars($reserva['nombre_servicio']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
            <td>
                <span class="status-badge <?php echo getEstadoClass($reserva['estado']); ?>">
                    <?php echo ucfirst($reserva['estado']); ?>
                </span>
            </td>
            <td class="action-buttons">
                <a href="#" class="btn btn-primary">Editar</a>
                <a href="eliminar_reserva.php?id=<?php echo $reserva['reserva_id']; ?>" 
                   class="btn btn-danger"
                   onclick="return confirm('¿Eliminar reserva?');">Eliminar</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>