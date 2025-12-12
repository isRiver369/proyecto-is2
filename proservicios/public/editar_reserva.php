<?php
require_once __DIR__ . '/../src/Servicios/Seguridad.php';
require_once __DIR__ . '/../src/Servicios/AdminDashboard.php';

Seguridad::requerirRol('administrador');

$dashboard = new AdminDashboard();
$mensaje = "";
$error = "";
$reserva = null;

// 1. OBTENER DATOS
if (isset($_GET['id'])) {
    $reserva = $dashboard->obtenerReservaPorId($_GET['id']);
    if (!$reserva) {
        header("Location: admin.php");
        exit();
    }
}

// 2. GUARDAR CAMBIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $total = $_POST['total'];
    $estado = $_POST['estado'];

    if ($dashboard->actualizarReserva($id, $fecha, $estado, $total)) {
        header("Location: admin.php?mensaje=editado");
        exit();
    } else {
        $error = "Error al actualizar la reserva.";
    }
    // Recargar datos para no perderlos en pantalla
    $reserva = ['reserva_id' => $id, 'fecha_reserva' => $fecha, 'total_pagar' => $total, 'estado' => $estado];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Reserva - ProServicios</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
      .form-container { background: white; padding: 2rem; border-radius: 8px; border: 1px solid var(--border); max-width: 500px; margin: 2rem auto; }
      .form-group { margin-bottom: 1.5rem; }
      .form-group label { display: block; font-weight: bold; margin-bottom: 0.5rem; }
      .form-group input, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
  </style>
</head>
<body>

  <header class="admin-header">
    <h1>ProServicios Admin</h1>
    <div class="header-actions"><a href="logout.php">Cerrar Sesión</a></div>
  </header>

  <main class="main-content" style="margin-left: 0; padding-top: 2rem;"> <div style="text-align: center; margin-bottom: 2rem;">
        <h2>Editar Reserva #<?php echo $reserva['reserva_id']; ?></h2>
        <a href="admin.php">← Volver al Dashboard</a>
    </div>

    <?php if ($error): ?><div class="alert alert-error" style="max-width: 500px; margin: 0 auto 20px;"><?php echo $error; ?></div><?php endif; ?>

    <div class="form-container">
        <form method="POST" action="editar_reserva.php?id=<?php echo $reserva['reserva_id']; ?>">
            <input type="hidden" name="id" value="<?php echo $reserva['reserva_id']; ?>">

            <div class="form-group">
                <label>Fecha de Reserva</label>
                <input type="date" name="fecha" value="<?php echo $reserva['fecha_reserva']; ?>" required>
            </div>

            <div class="form-group">
                <label>Total a Pagar ($)</label>
                <input type="number" step="0.01" name="total" value="<?php echo $reserva['total_pagar']; ?>" required>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="pendiente" <?php echo $reserva['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="confirmada" <?php echo $reserva['estado'] == 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                    <option value="pagada" <?php echo $reserva['estado'] == 'pagada' ? 'selected' : ''; ?>>Pagada</option>
                    <option value="cancelada" <?php echo $reserva['estado'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Guardar Cambios</button>
        </form>
    </div>

  </main>
</body>
</html>