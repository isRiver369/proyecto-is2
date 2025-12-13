<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$config = $dashboard->obtenerConfiguracion();

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Configuración - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
      .config-section {
          background: white;
          padding: 25px;
          border-radius: 8px;
          border: 1px solid var(--border);
          margin-bottom: 20px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      }
      .config-title {
          font-size: 1.1rem;
          font-weight: bold;
          color: var(--primary);
          margin-bottom: 15px;
          padding-bottom: 10px;
          border-bottom: 1px solid #eee;
      }
      .form-grid {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 20px;
      }
      .form-group label {
          display: block;
          margin-bottom: 8px;
          font-weight: bold;
          color: #555;
      }
      .form-group input, .form-group select {
          width: 100%;
          padding: 10px;
          border: 1px solid #ccc;
          border-radius: 4px;
          box-sizing: border-box;
      }
      /* Switch Toggle para mantenimiento */
      .toggle-switch {
          position: relative;
          display: inline-block;
          width: 50px;
          height: 24px;
      }
      .toggle-switch input { opacity: 0; width: 0; height: 0; }
      .slider {
          position: absolute; cursor: pointer;
          top: 0; left: 0; right: 0; bottom: 0;
          background-color: #ccc; transition: .4s; border-radius: 34px;
      }
      .slider:before {
          position: absolute; content: "";
          height: 16px; width: 16px; left: 4px; bottom: 4px;
          background-color: white; transition: .4s; border-radius: 50%;
      }
      input:checked + .slider { background-color: var(--danger); }
      input:checked + .slider:before { transform: translateX(26px); }
  </style>
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
      <li><a href="admin_proveedores.php">Proveedores</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
      <li><a href="admin_configuracion.php" class="active">Configuración</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Configuración del Sistema</h2>

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'guardado'): ?>
        <div class="alert alert-success">Configuración actualizada correctamente.</div>
    <?php endif; ?>

    <form action="guardar_configuracion.php" method="POST">
        
        <div class="config-section">
            <div class="config-title">Información General</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nombre del Sitio</label>
                    <input type="text" name="nombre_sitio" value="<?php echo htmlspecialchars($config['nombre_sitio']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Correo de Administración</label>
                    <input type="email" name="email_admin" value="<?php echo htmlspecialchars($config['email_admin']); ?>" required>
                    <small style="color: #888;">Recibirá notificaciones de sistema aquí.</small>
                </div>
            </div>
        </div>

        <div class="config-section">
            <div class="config-title">Finanzas e Impuestos</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Impuesto (IVA/VAT) %</label>
                    <input type="number" step="0.01" name="tasa_impuesto" value="<?php echo $config['tasa_impuesto']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Símbolo de Moneda</label>
                    <select name="moneda">
                        <option value="$" <?php echo $config['moneda'] == '$' ? 'selected' : ''; ?>>$ (Dólar)</option>
                        <option value="€" <?php echo $config['moneda'] == '€' ? 'selected' : ''; ?>>€ (Euro)</option>
                        <option value="S/." <?php echo $config['moneda'] == 'S/.' ? 'selected' : ''; ?>>S/. (Sol)</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 1rem;">
                Guardar Configuración
            </button>
        </div>

    </form>
  </main>

</body>
</html>