<?php
// public/crear_servicio.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$proveedores = $dashboard->obtenerProveedores();
$mensaje = "";
$error = "";

// PROCESAR FORMULARIO AL ENVIAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $proveedor_id = $_POST['proveedor_id'];
    $descripcion = $_POST['descripcion'];

    if (empty($nombre) || empty($precio) || empty($proveedor_id)) {
        $error = "Por favor completa los campos obligatorios.";
    } else {
        if ($dashboard->crearServicio($proveedor_id, $nombre, $descripcion, $precio)) {
            // Redirigir al listado con mensaje de éxito
            header("Location: admin_servicios.php?mensaje=creado");
            exit();
        } else {
            $error = "Hubo un error al guardar en la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Servicio - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
      /* Estilos específicos para formularios */
      .form-container {
          background: white;
          padding: 2rem;
          border-radius: 8px;
          border: 1px solid var(--border);
          max-width: 600px;
          margin: 0 auto; /* Centrado */
      }
      .form-group { margin-bottom: 1.5rem; }
      .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: var(--text-primary); }
      .form-group input, .form-group select, .form-group textarea {
          width: 100%;
          padding: 0.75rem;
          border: 1px solid #ccc;
          border-radius: 4px;
          font-family: inherit;
          box-sizing: border-box; /* Importante para que no se salga del ancho */
      }
      .form-group textarea { resize: vertical; height: 100px; }
      .form-actions { text-align: right; margin-top: 2rem; }
  </style>
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
      <li><a href="#">Reportes</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Registrar Nuevo Servicio</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="crear_servicio.php">
            
            <div class="form-group">
                <label for="nombre">Nombre del Servicio *</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ej: Clase de Piano Básico" required>
            </div>

            <div class="form-group">
                <label for="proveedor_id">Asignar al Proveedor *</label>
                <select name="proveedor_id" id="proveedor_id" required>
                    <option value="">-- Selecciona un Proveedor --</option>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?php echo $p['usuario_id']; ?>">
                            <?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #666;">Solo aparecen usuarios con rol 'Proveedor'.</small>
            </div>

            <div class="form-group" style="display: flex; gap: 1rem;">
                <div style="flex: 1;">
                    <label for="precio">Precio ($) *</label>
                    <input type="number" step="0.01" name="precio" id="precio" placeholder="0.00" required>
                </div>
                <div style="flex: 1;">
                    <label>Cupo Máximo</label>
                    <input type="number" value="10" disabled style="background: #eee;">
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" placeholder="Detalles del servicio..."></textarea>
            </div>

            <div class="form-actions">
                <a href="admin_servicios.php" class="btn btn-warning" style="margin-right: 10px;">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Servicio</button>
            </div>

        </form>
    </div>
  </main>
</body>
</html>