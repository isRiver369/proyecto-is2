<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$proveedores = $dashboard->obtenerProveedoresDetallados();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Proveedores - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <header class="admin-header">
    <h1>ProServicios Admin</h1>
    <div class="header-actions"><a href="logout.php">Cerrar Sesión</a></div>
  </header>

  <aside class="sidebar">
    <h2>Menú</h2>
    <ul>
      <li><a href="admin.php">Dashboard</a></li>
      <li><a href="admin_usuarios.php">Usuarios</a></li>
      <li><a href="admin_servicios.php">Servicios</a></li>
      <li><a href="admin_categorias.php">Categorías</a></li>
      <li><a href="admin_proveedores.php" class="active">Proveedores</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
      <li><a href="admin_configuracion.php">Configuración</a></li>
    </ul>   
  </aside>

  <main class="main-content">
    <h2 class="section-title">Socios y Proveedores</h2>

    <div class="table-container">
      <div class="table-header">
        <h3>Listado de Profesionales</h3>
        <a href="admin_usuarios.php" class="btn btn-primary" style="font-size: 0.8rem;">Gestionar Roles</a>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Proveedor / Bio</th>
            <th>Contacto</th>
            <th>Servicios Activos</th>
            <th>Portafolio</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($proveedores) > 0): ?>
              <?php foreach ($proveedores as $p): ?>
              <tr>
                <td><?php echo $p['usuario_id']; ?></td>
                
                <td style="max-width: 250px;">
                    <div style="font-weight: bold; font-size: 1rem;">
                        <?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?>
                    </div>
                    <?php if (!empty($p['bio'])): ?>
                        <div style="font-size: 0.85rem; color: #666; margin-top: 4px; font-style: italic;">
                            "<?php echo substr(htmlspecialchars($p['bio']), 0, 80) . '...'; ?>"
                        </div>
                    <?php else: ?>
                        <div style="font-size: 0.8rem; color: #ccc;">Sin biografía</div>
                    <?php endif; ?>
                </td>
                
                <td>
                    <div style="font-size: 0.9rem;"><?php echo htmlspecialchars($p['email']); ?></div>
                    <?php if (!empty($p['telefono'])): ?>
                        <div style="font-size: 0.9rem; margin-top: 3px;">📱 <?php echo htmlspecialchars($p['telefono']); ?></div>
                    <?php endif; ?>
                </td>
                
                <td style="text-align: center;">
                    <?php if ($p['cantidad_servicios'] > 0): ?>
                        <span class="status-badge status-success" style="font-size: 1rem; padding: 5px 12px;">
                            <?php echo $p['cantidad_servicios']; ?>
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-warning">Inactivo</span>
                    <?php endif; ?>
                </td>
                
                <td>
                    <?php if (!empty($p['portafolio_url'])): ?>
                        <a href="<?php echo htmlspecialchars($p['portafolio_url']); ?>" target="_blank" class="btn btn-info" style="font-size: 0.8rem;">
                            Ver Web
                        </a>
                    <?php else: ?>
                        <span style="color: #999; font-size: 0.8rem;">No registrado</span>
                    <?php endif; ?>
                </td>

                <td class="action-buttons">
                    <a href="cambiar_rol.php?id=<?php echo $p['usuario_id']; ?>&rol=cliente" 
                       class="btn btn-danger"
                       style="font-size: 0.8rem;"
                       onclick="return confirm('¿Estás seguro de quitarle el rol de Proveedor? Sus servicios dejarán de ser visibles.');">
                       ⬇ Revocar
                    </a>
                </td>
              </tr>
              <?php endforeach; ?>
          <?php else: ?>
              <tr><td colspan="6" style="text-align:center; padding: 2rem;">No hay proveedores registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</body>
</html>