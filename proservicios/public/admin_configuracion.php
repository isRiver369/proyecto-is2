<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
require_once '../src/Servicios/Seguridad.php';
require_once '../config/Database.php';

// Seguridad: Solo admin
Seguridad::requerirRol('administrador');

$database = new Database();
$db = $database->getConnection();

$mensaje = "";
$error = "";

// 2. PROCESAR GUARDADO (Lógica PHP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $impuesto = $_POST['impuesto'];
    $moneda = $_POST['moneda'];
    $nombre_sitio = $_POST['nombre_sitio'];
    $email_admin = $_POST['email_admin'];

    // Validar que sea numérico
    if (is_numeric($impuesto)) {
        // Actualizamos la fila ID = 1
        $query = "UPDATE configuracion SET tasa_impuesto = :tasa, moneda = :moneda, nombre_sitio = :nom, email_admin = :mail WHERE id = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':tasa', $impuesto);
        $stmt->bindParam(':moneda', $moneda);
        $stmt->bindParam(':nom', $nombre_sitio);
        $stmt->bindParam(':mail', $email_admin);

        if ($stmt->execute()) {
            $mensaje = "¡Configuración guardada correctamente!";
        } else {
            $error = "Error al guardar en la base de datos.";
        }
    } else {
        $error = "El impuesto debe ser un número.";
    }
}

// 3. OBTENER DATOS ACTUALES
$queryGet = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
$stmtGet = $db->prepare($queryGet);
$stmtGet->execute();
$config = $stmtGet->fetch(PDO::FETCH_ASSOC);

// Valores por defecto
$tasa_actual = $config['tasa_impuesto'] ?? 15.00;
$moneda_actual = $config['moneda'] ?? '$';
$sitio_actual = $config['nombre_sitio'] ?? 'ProServicios';
$email_actual = $config['email_admin'] ?? 'admin@proservicios.com';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Configuración - ProServicios Admin</title>
  <link rel="stylesheet" href="css/style.css">
  
  <style>
      .form-group { margin-bottom: 15px; }
      .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
      .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
      .row { display: flex; gap: 20px; flex-wrap: wrap; }
      .col { flex: 1; min-width: 250px; }
      .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
      .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
      .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
      <li><a href="admin_dashboard.php">Dashboard</a></li>
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

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="stat-card" style="padding: 2rem;">
        
        <form action="admin_configuracion.php" method="POST">
            
            <h3 style="margin-top:0; color: var(--primary); border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                Información General
            </h3>

            <div class="row">
                <div class="col form-group">
                    <label>Nombre del Sitio:</label>
                    <input type="text" name="nombre_sitio" value="<?php echo htmlspecialchars($sitio_actual); ?>" class="form-control">
                </div>
                <div class="col form-group">
                    <label>Email Administrador:</label>
                    <input type="email" name="email_admin" value="<?php echo htmlspecialchars($email_actual); ?>" class="form-control">
                </div>
            </div>

            <h3 style="color: var(--primary); border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; margin-top: 20px;">
                Finanzas e Impuestos
            </h3>

            <div class="row">
                <div class="col form-group">
                    <label>Impuesto (IVA/VAT) %:</label>
                    <input type="number" step="0.01" name="impuesto" value="<?php echo htmlspecialchars($tasa_actual); ?>" class="form-control" style="font-weight: bold;">
                    <small style="color: #666;">Ejemplo: 15.00 para 15%</small>
                </div>
                <div class="col form-group">
                    <label>Símbolo de Moneda:</label>
                    <select name="moneda" class="form-control">
                        <option value="$" <?php echo ($moneda_actual == '$') ? 'selected' : ''; ?>>$ (Dólar)</option>
                        <option value="€" <?php echo ($moneda_actual == '€') ? 'selected' : ''; ?>>€ (Euro)</option>
                        <option value="S/" <?php echo ($moneda_actual == 'S/') ? 'selected' : ''; ?>>S/ (Sol)</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn btn-success" style="padding: 12px 30px; font-size: 1rem;">
                    Guardar Configuración
                </button>
            </div>

        </form>

    </div>
  </main>

</body>
</html>