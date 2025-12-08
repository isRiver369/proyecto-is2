<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$ingresosMes = $dashboard->obtenerIngresosPorMes();
$topServicios = $dashboard->obtenerTopServicios();

// Preparamos los datos para que JavaScript los entienda (Convertir a JSON)
$labelsMes = json_encode(array_column($ingresosMes, 'mes'));
$dataMes = json_encode(array_column($ingresosMes, 'total'));

$labelsServicios = json_encode(array_column($topServicios, 'nombre_servicio'));
$dataServicios = json_encode(array_column($topServicios, 'cantidad'));
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      <li><a href="admin.php">Dashboard</a></li>
      <li><a href="admin_usuarios.php">Usuarios</a></li>
      <li><a href="admin_servicios.php">Servicios</a></li>
      <li><a href="admin_reportes.php" class="active">Reportes</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Reportes y Analíticas</h2>

    <div class="stats-grid" style="grid-template-columns: 2fr 1fr;">
      
      <div class="stat-card">
        <h3>💰 Evolución de Ingresos</h3>
        <canvas id="chartIngresos"></canvas>
      </div>

      <div class="stat-card">
        <h3>🔥 Top Servicios</h3>
        <div style="height: 300px; display: flex; justify-content: center;">
             <canvas id="chartServicios"></canvas>
        </div>
      </div>

    </div>

    <div class="table-container">
        <div class="table-header">
            <h3>Detalle de Ingresos por Mes</h3>
            <button class="btn btn-primary" onclick="window.print()">🖨 Imprimir Reporte</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Ingresos Totales</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingresosMes as $dato): ?>
                <tr>
                    <td><?php echo $dato['mes']; ?></td>
                    <td><strong>$<?php echo number_format($dato['total'], 2); ?></strong></td>
                    <td><span class="status-badge status-success">Procesado</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

  </main>

  <script>
    // 1. Configuración Gráfico de Barras (Ingresos)
    const ctx1 = document.getElementById('chartIngresos');
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: <?php echo $labelsMes; ?>, // PHP imprime aquí ["2025-11", "2025-12"]
        datasets: [{
          label: 'Ingresos ($)',
          data: <?php echo $dataMes; ?>, // PHP imprime aquí [150.00, 300.00]
          backgroundColor: '#1A4B8C',
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });

    // 2. Configuración Gráfico de Dona (Servicios)
    const ctx2 = document.getElementById('chartServicios');
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: <?php echo $labelsServicios; ?>,
        datasets: [{
          data: <?php echo $dataServicios; ?>,
          backgroundColor: [
            '#1A4B8C', '#17A2B8', '#28A745', '#FFC107', '#DC3545'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>

</body>
</html>