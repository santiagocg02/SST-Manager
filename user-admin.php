<?php
require_once __DIR__ . '/includes/bootstrap.php';

requireRole([APP_ROLE_MASTER]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Menú empresa</title>

  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">

  <!-- Tu CSS -->
   
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/user-admin.css">
  <link rel="stylesheet" href="assets/css/responsive.css">

</head>
<body class="bg-light">

  <div class="min-vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow login-card">
      <div class="card-body">

        <!-- Título -->
        <h3 class="text-center mb-2 login-title">SSTManager</h3>

        <!-- Texto de explicación -->
        <p class="text-center vm-subtitle mb-4">
          Su rol es de administrador. Ha seleccionado Menú empresa.  
          Por favor, seleccione la empresa a la que va a ingresar.
        </p>

        <!-- Contenido -->
        <form action="tu_siguiente_vista.php" method="POST">

          <div class="row justify-content-center align-items-center mb-2">

            <!-- Botón Menu Empresa -->
            <div class="col-12 col-md-4 mb-3 mb-md-0 text-center text-md-end">
              <button type="button" class="btn btn-menu-alt w-100">
                Menu Empresa
              </button>
            </div>

            <!-- Select empresa -->
            <div class="col-12 col-md-6">
              <label for="empresa" class="form-label">Seleccione empresa</label>
              <select id="empresa" name="empresa" class="form-select" required>
                <option value="" selected disabled>Seleccione empresa</option>
                <option value="1">Empresa 1</option>
                <option value="2">Empresa 2</option>
                <option value="3">Empresa 3</option>
              </select>

              <!-- Texto movido debajo del select -->
              <p class="text-muted mt-2" style="font-size: 0.88rem;">
                Se cargará la información de la empresa seleccionada.  
                Recuerde que solo se muestran empresas activas.
              </p>
            </div>

          </div>

          <!-- Botones Iniciar y Salir -->
          <div class="row justify-content-center my-4">

    <div class="col-6 col-md-3">
        <button type="submit" class="btn btn-iniciar w-100">
            INICIAR
        </button>
    </div>

    <div class="col-6 col-md-3">
        <a href="validacion-menu.php" class="btn btn-salir w-100">
            SALIR
        </a>
    </div>

</div>

        </form>

      </div>

      <!-- Footer -->
      <div class="login-footer">
        Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
