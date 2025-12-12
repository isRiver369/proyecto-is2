<?php
// public/admin_servicios.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$servicios = $dashboard->obtenerTodosLosServicios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestionar Servicios - ProServicios</title>
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
      <li><a href="admin.php">Dashboard</a></li>
      <li><a href="admin_usuarios.php">Usuarios</a></li>
      <li><a href="admin_servicios.php" class="active">Servicios</a></li>
      <li><a href="admin_categorias.php">Categorías</a></li>
      <li><a href="admin_proveedores.php">Proveedores</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
      <li><a href="admin_configuracion.php">Configuración</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Gestión de Servicios</h2>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'editado'): ?>
      <div class="alert alert-success">El servicio se actualizó correctamente.</div>
    <?php endif; ?>

    <div class="table-container">
      <div class="table-header">
        <h3>Catálogo de Servicios</h3>
        <a href="crear_servicio.php" class="btn btn-primary">+ Nuevo Servicio</a>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre del Servicio</th>
            <th>Proveedor</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($servicios as $s): ?>
          <tr>
            <td><?php echo $s['servicio_id']; ?></td>
            
            <td>
                <strong><?php echo htmlspecialchars($s['nombre_servicio']); ?></strong><br>
                <small style="color: #666;"><?php echo substr($s['descripcion'], 0, 50) . '...'; ?></small>
            </td>
            
            <td>
                <?php echo htmlspecialchars($s['p_nombre'] . ' ' . $s['p_apellido']); ?>
            </td>
            
            <td style="font-weight: bold; color: var(--primary);">
                $<?php echo number_format($s['precio'], 2); ?>
            </td>
            
            <td>
                <?php if ($s['disponible']): ?>
                    <span class="status-badge status-success">Activo</span>
                <?php else: ?>
                    <span class="status-badge status-danger">Inactivo</span>
                <?php endif; ?>
            </td>
            
            <td class="action-buttons">
                <?php if ($s['disponible']): ?>
                    <a href="cambiar_estado_servicio.php?id=<?php echo $s['servicio_id']; ?>" 
                       class="btn btn-warning" 
                       style="font-size: 0.8rem;"
                       onclick="return confirm('¿Pausar este servicio? No aparecerá en el catálogo.');">
                       Pausar
                    </a>
                <?php else: ?>
                    <a href="cambiar_estado_servicio.php?id=<?php echo $s['servicio_id']; ?>" 
                       class="btn btn-success" 
                       style="font-size: 0.8rem;">
                       Activar
                    </a>
                <?php endif; ?>
                
                <a href="editar_servicio.php?id=<?php echo $s['servicio_id']; ?>" 
                  class="btn btn-primary" 
                  style="font-size: 0.8rem;">
                  Editar
                </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>