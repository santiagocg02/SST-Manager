<?php
require_once __DIR__ . '/includes/auth.php';

$usuario = $_SESSION["usuario"];
$rol     = "Administrador";

$mensaje = "";
$empresaSeleccionada = "";

// Ejemplo de empresas (luego BD)
$empresas = [
    ""            => "Seleccione empresa",
    "empresa1"    => "Empresa Alfa S.A.S.",
    "empresa2"    => "Empresa Beta Ltda.",
    "empresa3"    => "Empresa Gamma S.A."
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["accion"]) && $_POST["accion"] === "salir") {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    if (isset($_POST["accion"]) && $_POST["accion"] === "iniciar") {
        $empresaSeleccionada = $_POST["empresa"] ?? "";

        if ($empresaSeleccionada === "") {
            $mensaje = "Por favor, selecciona una empresa activa.";
        } else {
            $mensaje = "Se cargará la información de la empresa seleccionada.";
            // Aquí luego puedes redirigir a otro panel
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>User Admin - SSTManager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-user-admin">
  <div class="admin-container">

    <div class="admin-header">
      <h1>SSTManager</h1>
      <div class="roles">
        <span>Usuario</span>
        <span class="rol-activo">Administrador</span>
      </div>
      <p>
        Su rol es de administrador. Ha seleccionado <strong>Menú empresa</strong>.  
        Por favor, seleccione la empresa a la que va a ingresar.
      </p>
    </div>

    <?php if (!empty($mensaje)): ?>
      <div class="alert">
        <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="admin-layout">
        <!-- Lado izquierdo: botón "Menu Empresa" -->
        <div class="admin-left">
          <button type="button" class="btn-menu-empresa">
            Menu Empresa
          </button>
        </div>

        <!-- Lado derecho: select de empresa -->
        <div class="admin-right">
          <label for="empresa" class="label-empresa">Seleccione empresa</label>
          <select id="empresa" name="empresa" class="select-empresa">
            <?php foreach ($empresas as $value => $label): ?>
              <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"
                <?php echo ($value === $empresaSeleccionada) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <div class="admin-text-info">
            Se cargará la información de la empresa seleccionada.  
            <strong>Recuerda que solo se muestran empresas activas.</strong>
          </div>
        </div>
      </div>

      <!-- Botones Iniciar / Salir -->
      <div class="admin-actions">
        <button type="submit" name="accion" value="iniciar" class="btn btn-primary">
          Iniciar
        </button>
        <button type="submit" name="accion" value="salir" class="btn btn-secondary">
          SALIR
        </button>
      </div>
    </form>

    <div class="admin-footer">
      Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
    </div>

  </div>
</body>
</html>
