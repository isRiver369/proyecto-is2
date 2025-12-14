<?php
// public/editar_servicio.php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$mensaje = "";
$error = "";
$servicio = null;

// 1. VERIFICAR ID (Si no hay ID, lo sacamos)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $servicio = $dashboard->obtenerServicioPorId($id);

    if (!$servicio) {
        header("Location: admin_servicios.php");
        exit();
    }
}

// 2. PROCESAR EL GUARDADO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // El ID viene oculto en el formulario
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    if (empty($nombre) || empty($precio)) {
        $error = "Nombre y precio son obligatorios.";
    } else {
        if ($dashboard->actualizarServicio($id, $nombre, $descripcion, $precio)) {
            // Éxito: volvemos a la lista
            header("Location: admin_servicios.php?mensaje=editado");
            exit();
        } else {
            $error = "Error al actualizar la base de datos.";
        }
    }
    // Si hubo error, recargamos los datos para no perderlos en la vista
    $servicio = ['servicio_id' => $id, 'nombre_servicio' => $nombre, 'precio' => $precio, 'descripcion' => $descripcion];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Servicio - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
      .form-container {
          background: white; padding: 2rem; border-radius: 8px;
          border: 1px solid var(--border); max-width: 600px; margin: 0 auto;
      }
      .form-group { margin-bottom: 1.5rem; }
      .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
      .form-group input, .form-group textarea {
          width: 100%; padding: 0.75rem; border: 1px solid #ccc;
          border-radius: 4px; box-sizing: border-box;
      }
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
      <li><a href="admin_servicios.php" class="active">Servicios</a></li>
      <li><a href="admin_reportes.php">Reportes</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <h2 class="section-title">Editar Servicio #<?php echo $servicio['servicio_id']; ?></h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="editar_servicio.php?id=<?php echo $servicio['servicio_id']; ?>">
            
            <input type="hidden" name="id" value="<?php echo $servicio['servicio_id']; ?>">

            <div class="form-group">
                <label>Nombre del Servicio</label>
                <input type="text" name="nombre" 
                       value="<?php echo htmlspecialchars($servicio['nombre_servicio']); ?>" required>
            </div>

            <div class="form-group">
                <label>Precio ($)</label>
                <input type="number" step="0.01" name="precio" 
                       value="<?php echo $servicio['precio']; ?>" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($servicio['descripcion']); ?></textarea>
            </div>

            <div style="text-align: right;">
                <a href="admin_servicios.php" class="btn btn-warning" style="margin-right: 10px;">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>

        </form>
    </div>
  </main>
</body>
</html>