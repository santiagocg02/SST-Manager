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
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">

  <!-- Tu CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <div class="container-fluid py-3">
    <div class="admin-frame shadow-lg mx-auto">

      <!-- Barra superior gris -->
      <div class="admin-topbar">
        MENU ADMINISTRADOR
      </div>

      <!-- Header azul con SSTManager y círculo ADMIN -->
      <div class="admin-header">
        <div class="admin-title">
          SSTManager
        </div>
      </div>

      <!-- Cuerpo: sidebar + contenido -->
      <div class="admin-body">

        <!-- SIDEBAR IZQUIERDA -->
        <aside class="admin-sidebar">
          <div class="accordion admin-accordion" id="adminMenu">

            <!-- Administración -->
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
                  <a href="#" class="admin-subitem">Gestión de usuarios</a>
                  <a href="#" class="admin-subitem">Roles y permisos</a>
                  <a href="#" class="admin-subitem">Parámetros del sistema</a>
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
                  <a href="#" class="admin-subitem">Datos de la empresa</a>
                  <a href="#" class="admin-subitem">Centros de trabajo</a>
                  <a href="#" class="admin-subitem">Cargos y áreas</a>
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
                  <a href="#" class="admin-subitem">Copias de seguridad</a>
                  <a href="#" class="admin-subitem">Bitácora del sistema</a>
                  <a href="#" class="admin-subitem">Configuración avanzada</a>
                </div>
              </div>
            </div>

          </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="admin-content">
          <div class="p-4">
            <h5 class="mb-2">Bienvenido al menú administrador</h5>
            <p class="text-muted mb-0">
              Seleccione una opción del menú de la izquierda para comenzar.
            </p>
          </div>
        </main>

      </div>

      <!-- FOOTER -->
      <div class="admin-footer">
        Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
      </div>

    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
