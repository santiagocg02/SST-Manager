<?php
session_start();

// Validar que el usuario existe y es Master
// Si no cumple, lo manda al login
if (!isset($_SESSION["usuario"]) || ($_SESSION["rol"] ?? '') !== "Master") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Menú administrador</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/menu-admin.css">
</head>

<body class="page-menu-admin">

  <div class="admin-frame">

    <div class="admin-topbar">
      MENU ADMINISTRADOR
    </div>

    <div class="admin-header d-flex justify-content-between align-items-center pe-4">
      
      <div class="admin-title">SSTManager</div>

      <div class="d-flex align-items-center gap-3">
          <span class="text-white small d-none d-md-block">
             Hola, <strong><?= htmlspecialchars($_SESSION["usuario"] ?? 'Admin') ?></strong>
          </span>

          <a href="logout.php" class="btn btn-sm btn-outline-light">
             Cerrar Sesión
          </a>
      </div>

    </div>

    <div class="admin-body">

      <aside class="admin-sidebar">
        <div class="accordion admin-accordion" id="adminMenu">

          <div class="accordion-item">
            <h2 class="accordion-header" id="headingAdmin">
              <button class="accordion-button collapsed admin-accordion-btn"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapseAdmin"
                      aria-expanded="false"
                      aria-controls="collapseAdmin">
                Administración
              </button>
            </h2>

            <div id="collapseAdmin"
                 class="accordion-collapse collapse"
                 aria-labelledby="headingAdmin"
                 data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <a href="pages/tipo-empresa.php" target="contentFrame" class="admin-subitem">Tipos de Empresa</a>
                <a href="pages/item1072.php" target="contentFrame" class="admin-subitem">Item 1072</a>
                <a href="pages/guia-ruc.php" target="contentFrame" class="admin-subitem">Item Guía RUC</a>
                <a href="pages/formulario.php" target="contentFrame" class="admin-subitem">Formularios</a>
                <a href="pages/calificacion.php" target="contentFrame" class="admin-subitem">Calificación</a>
              </div>
            </div>
          </div>

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
                <a href="pages/seguridad/modulo.php" target="contentFrame" class="admin-subitem">Módulos</a>
                <a href="#" class="admin-subitem">Planes</a>
                <a href="#" class="admin-subitem">Servicios</a>
                <a href="#" class="admin-subitem">Usuarios</a>
              </div>
            </div>
          </div>

        </div>
      </aside>

      <main class="admin-content">
        <iframe id="contentFrame" name="contentFrame"
                src="pages/bienvenida.php"
                class="admin-iframe"></iframe>
      </main>

    </div><footer class="admin-footer">
      <span>© 2026 SSTManager · Tu aliado estratégico en SST</span>
    </footer>

  </div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>