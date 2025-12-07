<?php
require_once '../src/Servicios/Seguridad.php';

// Solo deja pasar si el rol es 'administrador'
Seguridad::requerirRol('administrador');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel Admin - ProServicios</title>
  <style>
    /* ========== VARIABLES DE DISEÑO ========== */
    :root {
      /* Paleta principal */
      --primary: #1A4B8C;
      --primary-dark: #0D2F5A;
      --white: #FFFFFF;
      --bg-light: #F8F9FA;
      --text-secondary: #6C757D;
      --text-primary: #212529;

      /* Estados / Acción */
      --success: #28A745;
      --danger: #DC3545;
      --warning: #FFC107;
      --info: #17A2B8;
      --premium: #6F42C1;

      /* Otros */
      --border: #DEE2E6;
      --shadow: rgba(0, 0, 0, 0.1);
    }

    /* ========== TIPOGRAFÍA Y GENERAL ========== */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: var(--bg-light);
      color: var(--text-primary);
      line-height: 1.6;
    }

    h1, h2, h3, h4 {
      margin: 0 0 1rem 0;
      color: var(--primary);
    }

    h1 {
      font-size: 2rem;
    }

    h2 {
      font-size: 1.5rem;
    }

    a {
      color: var(--primary);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    /* ========== HEADER ========== */
    .admin-header {
      background-color: var(--primary);
      color: var(--white);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px var(--shadow);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .admin-header h1 {
      color: var(--white);
      font-size: 1.4rem;
      font-weight: bold;
    }

    .header-actions a {
      color: var(--white);
      background-color: var(--danger);
      padding: 0.4rem 1rem;
      border-radius: 4px;
      font-weight: bold;
      text-decoration: none;
      margin-left: 1rem;
      transition: background-color 0.2s;
    }

    .header-actions a:hover {
      background-color: #c82333;
      text-decoration: none;
    }

    /* ========== SIDEBAR ========== */
    .sidebar {
      width: 240px;
      background-color: var(--white);
      height: calc(100vh - 64px);
      position: fixed;
      top: 64px;
      left: 0;
      border-right: 1px solid var(--border);
      padding: 1.5rem 0;
      overflow-y: auto;
    }

    .sidebar h2 {
      padding: 0 1.5rem 1rem;
      font-size: 1.1rem;
      color: var(--text-secondary);
      border-bottom: 1px solid var(--border);
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar li {
      margin: 0;
    }

    .sidebar a {
      display: block;
      padding: 0.75rem 1.5rem;
      color: var(--text-primary);
      font-weight: 500;
      transition: color 0.2s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      color: var(--primary);
    }

    /* ========== MAIN CONTENT ========== */
    .main-content {
      margin-left: 240px;
      padding: 2rem;
      min-height: calc(100vh - 64px);
    }

    .section-title {
      margin-bottom: 1.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid var(--primary);
    }

    /* ========== TARJETAS DE RESUMEN ========== */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background-color: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 1.25rem;
      box-shadow: 0 2px 4px var(--shadow);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 8px var(--shadow);
    }

    .stat-value {
      font-size: 1.75rem;
      font-weight: bold;
      color: var(--primary);
      margin: 0.25rem 0;
    }

    .stat-label {
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    /* ========== TABLAS ========== */
    .table-container {
      background-color: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 2rem;
      box-shadow: 0 2px 4px var(--shadow);
    }

    .table-header {
      padding: 1rem 1.5rem;
      background-color: var(--bg-light);
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--border);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }

    th, td {
      padding: 1rem 1.5rem;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }

    th {
      background-color: var(--bg-light);
      color: var(--text-primary);
      font-weight: 600;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: var(--bg-light);
    }

    /* ========== ESTADOS ========== */
    .status-badge {
      padding: 0.25rem 0.6rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: bold;
      color: var(--text-primary);
      display: inline-block;
    }

    .status-success {
      background-color: #d4edda;
      color: var(--success);
    }

    .status-danger {
      background-color: #f8d7da;
      color: var(--danger);
    }

    .status-warning {
      background-color: #fff3cd;
      color: var(--warning);
    }

    .status-info {
      background-color: #d1ecf1;
      color: var(--info);
    }

    .status-premium {
      background-color: #e2d9f3;
      color: var(--premium);
    }

    /* ========== BOTONES ========== */
    .btn {
      display: inline-block;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      text-align: center;
      color: var(--white);
      transition: opacity 0.2s;
    }

    .btn:hover {
      opacity: 0.9;
    }

    .btn-primary { background-color: var(--primary); }
    .btn-success { background-color: var(--success); }
    .btn-danger { background-color: var(--danger); }
    .btn-warning {
      background-color: var(--warning);
      color: var(--text-primary) !important;
    }
    .btn-info { background-color: var(--info); }
    .btn-premium { background-color: var(--premium); }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    /* ========== FOOTER ========== */
    .footer {
      text-align: center;
      padding: 1.5rem;
      background-color: var(--text-secondary);
      color: var(--white);
      font-size: 0.9rem;
      margin-top: 2rem;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
        padding: 1rem 0;
      }
      .sidebar h2,
      .sidebar span {
        display: none;
      }
      .sidebar a {
        text-align: center;
        padding: 0.75rem 0;
        font-size: 1.2rem;
      }
      .main-content {
        margin-left: 70px;
        padding: 1.5rem;
      }
      .admin-header h1 {
        font-size: 1.2rem;
      }
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <header class="admin-header">
    <h1>ProServicios Admin</h1>
    <div class="header-actions">
      <a href="logout.php">Cerrar Sesión</a>
    </div>
  </header>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <h2>Menú</h2>
    <ul>
      <li><a href="#" class="active">Dashboard</a></li>
      <li><a href="#">Usuarios</a></li>
      <li><a href="#">Servicios</a></li>
      <li><a href="#">Categorías</a></li>
      <li><a href="#">Proveedores</a></li>
      <li><a href="#">Reportes</a></li>
      <li><a href="#">Configuración</a></li>
    </ul>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content">
    <h2 class="section-title">Panel de Administrador</h2>

    <!-- Tarjetas de resumen -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Usuarios</div>
        <div class="stat-value">24</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Ingresos Hoy</div>
        <div class="stat-value">$1,240.00</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Reservas Pendientes</div>
        <div class="stat-value">7</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Servicios Activos</div>
        <div class="stat-value">15</div>
      </div>
    </div>

    <!-- Últimas reservas -->
    <div class="table-container">
      <div class="table-header">
        <h3>Últimas Reservas</h3>
        <div class="action-buttons">
          <a href="#" class="btn btn-primary">Ver Todo</a>
          <a href="#" class="btn btn-info">Exportar</a>
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#RES024</td>
            <td>Ana Gómez</td>
            <td>Asesoría Legal</td>
            <td>02/12/2025</td>
            <td><span class="status-badge status-success">Confirmada</span></td>
            <td class="action-buttons">
              <a href="#" class="btn btn-primary">Editar</a>
              <a href="#" class="btn btn-danger">Eliminar</a>
            </td>
          </tr>
          <tr>
            <td>#RES023</td>
            <td>Luis Mora</td>
            <td>Clase de Guitarra</td>
            <td>05/12/2025</td>
            <td><span class="status-badge status-warning">Pendiente</span></td>
            <td class="action-buttons">
              <a href="#" class="btn btn-primary">Editar</a>
              <a href="#" class="btn btn-danger">Eliminar</a>
            </td>
          </tr>
          <tr>
            <td>#RES022</td>
            <td>Carmen Díaz</td>
            <td>Curso de Fotografía</td>
            <td>01/12/2025</td>
            <td><span class="status-badge status-danger">Cancelada</span></td>
            <td class="action-buttons">
              <a href="#" class="btn btn-primary">Editar</a>
              <a href="#" class="btn btn-danger">Eliminar</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Acciones rápidas -->
    <div class="table-container">
      <div class="table-header">
        <h3>Acciones Rápidas</h3>
      </div>
      <div style="padding: 1.5rem; display: flex; flex-wrap: wrap; gap: 1rem;">
        <a href="#" class="btn btn-primary">Crear Usuario</a>
        <a href="#" class="btn btn-success">Generar Reporte</a>
        <a href="#" class="btn btn-warning">Configurar Impuestos</a>
        <a href="#" class="btn btn-premium">Plan Premium</a>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="footer">
    © 2025 ProServicios. Todos los derechos reservados.
  </footer>

</body>
</html>