<?php
session_start();

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/buttons.css">
  <link rel="stylesheet" href="assets/css/validacion.css">
</head>

<body class="page-validacion">

  <div class="validacion-container">
    <div class="validacion-content">
      <h2>SSTManager</h2>
      <p>Su rol es de administrador, seleccione el menú que quiere ver.</p>

      <div class="validacion-actions">
        <a href="menu-admin.php" class="btn btn-menu">Menu administración</a>
        <a href="menu-empresa.php" class="btn btn-menu-alt">Menu Empresa</a>
      </div>

      <a href="logout.php" class="btn btn-salir">SALIR</a>
    </div>
  </div>

  <footer class="validacion-footer">
    Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
  </footer>

</body>


</body>
</html>
