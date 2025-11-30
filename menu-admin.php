<?php
require_once __DIR__ . '/includes/auth.php';
$rol = "Administrador";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú Administrador - SSTManager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-menu-admin">

  <div class="window">

    <!-- Barra pequeña superior -->
    <div class="top-bar">
      MENU ADMINISTRADOR
    </div>

    <!-- Encabezado principal -->
    <header class="main-header">
      <div class="brand">SSTManager</div>
      <div class="badge-admin">ADMIN</div>
    </header>

    <!-- Layout principal -->
    <div class="layout">

      <!-- Menú lateral -->
      <aside class="sidebar">

        <div class="sidebar-section">
          <button class="sidebar-btn">
            <span>Administración</span>
            <span class="sidebar-arrow">▼</span>
          </button>
        </div>

        <div class="sidebar-section">
          <button class="sidebar-btn">
            <span>Empresa</span>
            <span class="sidebar-arrow">▼</span>
          </button>
        </div>

        <div class="sidebar-section">
          <button class="sidebar-btn">
            <span>Seguridad</span>
            <span class="sidebar-arrow">▼</span>
          </button>
        </div>

        <div style="flex:1;border-top:1px solid #c8ccd2;"></div>

      </aside>

      <!-- Contenido principal (vacío por ahora) -->
      <main class="content">
        <!-- Aquí luego cargarás formularios, listados, etc. -->
      </main>

    </div>

    <!-- Pie de página -->
    <footer class="footer">
      Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
    </footer>

  </div>

</body>
</html>
