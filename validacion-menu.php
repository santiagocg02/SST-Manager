<?php
require_once __DIR__ . '/includes/auth.php';

$rol = "Administrador";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["accion"])) {

        // SALIR
        if ($_POST["accion"] === "salir") {
            session_destroy();
            header("Location: index.php");
            exit;
        }

        // MENÚ ADMINISTRACIÓN
        if ($_POST["accion"] === "menu_admin") {
            header("Location: menu-admin.php");
            exit;
        }

        // MENÚ EMPRESA
        if ($_POST["accion"] === "menu_empresa") {
            header("Location: user-admin.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Validación Menú - SSTManager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="page-validacion">
  <div class="validacion-container">

    <div class="header">
      <h1>SSTManager</h1>
      <div class="roles">
        <span>Usuario</span>
        <span class="rol-activo">Administrador</span>
      </div>
      <p>
        Su rol es de administrador, seleccione el menú que quiere ver.
      </p>
    </div>

    <form method="POST">

      <div class="menus">
        <button name="accion" value="menu_admin" class="btn-menu btn-admin">
          Menu administración
        </button>

        <button name="accion" value="menu_empresa" class="btn-menu btn-empresa">
          Menu Empresa
        </button>
      </div>

      <div class="salir">
        <button name="accion" value="salir" class="btn-salir">
          SALIR
        </button>
      </div>

    </form>

    <div class="footer">
      Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
    </div>

  </div>
</body>
</html>
