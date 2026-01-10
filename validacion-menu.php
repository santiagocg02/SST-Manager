<?php
session_start();

// Validar sesión
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Validación de menú</title>

  <!-- Bootstrap -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

  <div class="min-vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow login-card">

      <div class="card-body text-center">

        <!-- TÍTULO -->
        <h3 class="login-title mb-2">SSTManager</h3>

        <!-- SUBTÍTULO PRINCIPAL -->
        <p class="vm-subtitle mb-4">
          Su rol es de administrador, seleccione el menú que quiere ver.
        </p>

        <!-- BOTONES GRANDES -->
        <div class="row justify-content-center mb-4">

          <!-- Menú Administración -->
          <div class="col-12 col-md-5 mb-3">
            <a href="menu-admin.php" class="btn btn-menu w-100">
              Menu administración
            </a>
          </div>

          <!-- Menú Empresa -->
          <div class="col-12 col-md-5 mb-3">
            <a href="user-admin.php" class="btn btn-menu-alt w-100">
              Menu Empresa
            </a>
          </div>
        </div>

        <!-- Botón SALIR -->
        <div class="d-flex justify-content-center">
          <a href="index.php" class="btn btn-salir">
            SALIR
          </a>
        </div>

      </div>

      <!-- Franja inferior -->
      <div class="login-footer">
        Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
