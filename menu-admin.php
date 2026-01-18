<?php
session_start();

// Solo admin
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Menú administrador</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Tu CSS -->
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/menu-admin.css">

</head>

<body class="page-menu-admin">

  <!-- SIN container-fluid ni py-3 para que sea full size -->
  <div class="admin-frame">

    <!-- Barra superior gris -->
    <div class="admin-topbar">
      MENU ADMINISTRADOR
    </div>

    <!-- Header azul -->
    <div class="admin-header">
      <div class="admin-title">SSTManager</div>
    </div>

    <!-- Cuerpo: sidebar + contenido -->
    <div class="admin-body">

      <!-- SIDEBAR -->
      <aside class="admin-sidebar">
        <div class="accordion admin-accordion" id="adminMenu">

          <!-- Administracion -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingItem1072">
              <button class="accordion-button collapsed admin-accordion-btn"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapseItem1072"
                      aria-expanded="false"
                      aria-controls="collapseItem1072">
                Administración
              </button>
            </h2>

            <div id="collapseItem1072"
                 class="accordion-collapse collapse"
                 aria-labelledby="headingItem1072"
                 data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <a href="pages/tipo-empresa.php" target="contentFrame" class="admin-subitem">Tipos de Empresa</a>
                <a href="pages/item1072.php" target="contentFrame" class="admin-subitem">Item 1072</a>
                <a href="pages/guia-ruc.php" target="contentFrame" class="admin-subitem">Item Guía RUC</a>
                <a href="pages/formulario.php" target="contentFrame" class="admin-subitem">Formularios</a>
                <a href="pages/calificacion.php" target="contentFrame" class="admin-subitem">Calificacíon</a>
              </div>
            </div>
          </div>

          <!-- Empresa -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingEmpresa">
              <button class="accordion-button collapsed admin-accordion-btn"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapseEmpresa"
                      aria-expanded="false"
                      aria-controls="collapseEmpresa">
                Empresa
              </button>
            </h2>
            <div id="collapseEmpresa"
                 class="accordion-collapse collapse"
                 aria-labelledby="headingEmpresa"
                 data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <a href="#" class="admin-subitem">Crear</a>
              </div>
            </div>
          </div>

          <!-- Seguridad -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeguridad">
              <button class="accordion-button collapsed admin-accordion-btn"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapseSeguridad"
                      aria-expanded="false"
                      aria-controls="collapseSeguridad">
                Seguridad
              </button>
            </h2>
            <div id="collapseSeguridad"
                 class="accordion-collapse collapse"
                 aria-labelledby="headingSeguridad"
                 data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <a href="#" class="admin-subitem">Modulos</a>
                <a href="#" class="admin-subitem">Planes</a>
                <a href="#" class="admin-subitem">Servicios</a>
                <a href="#" class="admin-subitem">Usuarios</a>
              </div>
            </div>
          </div>

        </div>
      </aside>

      <!-- CONTENIDO -->
      <main class="admin-content">
        <iframe id="contentFrame" name="contentFrame"
                src="pages/bienvenida.php"
                class="admin-iframe"></iframe>
      </main>

    </div><!-- /admin-body -->

    <!-- Footer dentro del frame (queda perfecto) -->
    <footer class="admin-footer">
      <span>© 2026 SSTManager · Tu aliado estratégico en SST</span>
    </footer>

  </div><!-- /admin-frame -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
