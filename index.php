<?php
session_start();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario    = trim($_POST["usuario"] ?? "");
    $contrasena = trim($_POST["contrasena"] ?? "");

    // Credenciales de ejemplo
    $usuario_valido    = "admin";
    $contrasena_valida = "1234";

    if ($usuario === $usuario_valido && $contrasena === $contrasena_valida) {
        $_SESSION["usuario"] = $usuario;
        $_SESSION["rol"]     = "admin";

        // Ir a validación de menú
        header("Location: validacion-menu.php");
        exit;
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login SSTManager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="page-login">
  <div class="login-container">

    <div class="login-header">
      <h1>SSTManager</h1>
    </div>

    <?php if (!empty($mensaje)): ?>
      <div class="alert">
        <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="usuario">Usuario</label>
        <input
          type="text"
          id="usuario"
          name="usuario"
          placeholder="Ingresa tu usuario"
          required
        />
      </div>

      <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input
          type="password"
          id="contrasena"
          name="contrasena"
          placeholder="Ingresa tu contraseña"
          required
        />
      </div>

      <div class="login-actions">
        <button type="submit" class="btn btn-primary">Iniciar</button>
        <button type="reset" class="btn btn-secondary">Cancelar</button>
      </div>

      <div class="recover-link">
        <a href="#">Recuperar contraseña</a>
      </div>
    </form>

    <div class="login-footer">
      Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
    </div>

  </div>
</body>
</html>
