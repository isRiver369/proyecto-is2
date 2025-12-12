<?php
// public/admin_usuarios.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$usuarios = $dashboard->obtenerTodosLosUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios - ProServicios</title>
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
      <li><a href="admin_usuarios.php" class="active">Usuarios</a></li>
      <li><a href="admin_servicios.php">Servicios</a></li>
      <li><a href="admin_categorias.php">Categorías</a></li>
      <li><a href="admin_proveedores.php">Proveedores</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
      <li><a href="admin_configuracion.php">Configuración</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Gestión de Usuarios</h2>

    <div class="table-container">
      <div class="table-header">
        <h3>Lista Completa</h3>
        <a href="#" class="btn btn-primary">+ Nuevo Usuario</a>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre Completo</th>
            <th>Email</th>
            <th>Rol Actual</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><?php echo $u['usuario_id']; ?></td>
            <td>
                <strong><?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?></strong><br>
                <small style="color: #888;">Registro: <?php echo date('d/m/Y', strtotime($u['fecha_registro'])); ?></small>
            </td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td>
                <span class="role-badge role-<?php echo $u['rol']; ?>">
                    <?php echo ucfirst($u['rol']); ?>
                </span>
            </td>
            <td class="action-buttons">
                <?php if ($u['rol'] !== 'administrador'): ?>
                    
                    <?php if ($u['rol'] === 'cliente'): ?>
                        <a href="cambiar_rol.php?id=<?php echo $u['usuario_id']; ?>&rol=proveedor" 
                           class="btn btn-info"
                           onclick="return confirm('¿Hacer PROVEEDOR a este usuario?');">
                           ⬆ Ascender
                        </a>
                    <?php else: ?>
                        <a href="cambiar_rol.php?id=<?php echo $u['usuario_id']; ?>&rol=cliente" 
                           class="btn btn-warning"
                           onclick="return confirm('¿Bajar a CLIENTE a este usuario?');">
                           ⬇ Degradar
                        </a>
                    <?php endif; ?>

                <?php else: ?>
                    <span style="color: #999; font-style: italic;">Sin acciones</span>
                <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>