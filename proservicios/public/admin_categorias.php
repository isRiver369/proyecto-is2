<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$categorias = $dashboard->obtenerCategorias();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Categorías - ProServicios</title>
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
      <li><a href="admin_categorias.php" class="active">Categorías</a></li>
      <li><a href="admin_proveedores.php">Proveedores</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
      <li><a href="admin_configuracion.php">Configuración</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Gestión de Categorías</h2>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'creado'): ?>
        <div class="alert alert-success">✅ Categoría creada correctamente.</div>
    <?php endif; ?>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'eliminado'): ?>
        <div class="alert alert-warning">🗑️ Categoría eliminada.</div>
    <?php endif; ?>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: start;">
        
        <div class="table-container" style="flex: 2; min-width: 300px;">
            <div class="table-header">
                <h3>Categorías Existentes</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Nombre</th>
                        <th style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td><?php echo $c['categoria_id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($c['nombre_categoria']); ?></strong></td>
                        <td class="action-buttons">
                            <a href="procesar_categoria.php?eliminar=<?php echo $c['categoria_id']; ?>" 
                               class="btn btn-danger"
                               style="padding: 5px 10px; font-size: 0.8rem;"
                               onclick="return confirm('¿Borrar esta categoría?');">
                               🗑️ Borrar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="flex: 1; min-width: 250px;">
            <div class="stat-card">
                <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; color: var(--primary);">
                    + Nueva Categoría
                </h3>
                
                <form action="procesar_categoria.php" method="POST" style="margin-top: 15px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #555;">Nombre:</label>
                        <input type="text" name="nombre_categoria" required 
                               placeholder="Ej: Decoración" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        Guardar
                    </button>
                </form>
            </div>
            
            <div class="alert alert-info" style="margin-top: 20px; font-size: 0.85rem;">
                ℹ️ <strong>Tip:</strong> Usa categorías generales (ej: "Hogar", "Eventos") para agrupar mejor los servicios.
            </div>
        </div>

    </div>
  </main>

</body>
</html>