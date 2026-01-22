<?php
session_start();

// 1. Seguridad
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

// 2. Recuperar el rol y limpiarlo
// trim() elimina espacios en blanco al inicio o final
$rol = isset($_SESSION["rol"]) ? trim($_SESSION["rol"]) : '';
$user = isset($_SESSION["usuario"]) ? trim($_SESSION["usuario"]) : '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Menú</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/buttons.css">
  <link rel="stylesheet" href="assets/css/validacion.css">
</head>

<body class="page-validacion">

  <div class="validacion-container">
    <div class="validacion-content">
      <h2>SSTManager</h2>

      <?php if ($rol == 'Master' || $rol == 'master'): ?>
      
          <p>Bienvenido <strong><?php echo $user; ?></strong>. Seleccione el panel a gestionar:</p>
          
          <div class="validacion-actions">
            <a href="menu-admin.php" class="btn btn-menu">Menú Administración</a>
            <a href="menu-empresa.php" class="btn btn-menu-alt">Menú Empresa</a>
          </div>

      <?php else: ?>
      
          <p>Bienvenido. Ingrese a su panel de gestión:  </p>
          
          <div class="validacion-actions">
            <a href="menu-empresa.php" class="btn btn-menu-alt">Ingresar al Sistema</a>
          </div>

      <?php endif; ?>

      <br>
      <a href="logout.php" class="btn btn-salir">CERRAR SESIÓN</a>
    </div>
  </div>

  <footer class="validacion-footer">
    Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
  </footer>

</body>
</html>