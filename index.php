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
  <meta charset="UTF-8">
  <title>SSTManager - Login</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="assets/css/buttons.css">
  <link rel="stylesheet" href="assets/css/responsive.css">

  <!-- (Opcional pero recomendado) para asegurar footer abajo sin scroll raro -->
  <style>
    html, body { height: 100%; }
    body { margin: 0; }
    .login-layout{
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .login-main{
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 16px;
    }
    /* el footer que ya tienes en login.css */
    /* .login-footer { ... } */
  </style>
</head>

<body class="bg-light">
  <div class="login-layout">

    <!-- CONTENIDO -->
    <main class="login-main">
      <div class="login-card" style="width: 100%; max-width: 760px;">
        <div class="card-body">

          <!-- Título -->
          <h3 class="text-center mb-4 login-title">SSTManager</h3>

          <!-- Mensaje de error -->
          <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger py-2" role="alert">
              <?= htmlspecialchars($mensaje) ?>
            </div>
          <?php endif; ?>

          <!-- Formulario -->
          <form action="" method="POST">

            <!-- Usuario -->
            <div class="mb-3">
              <label for="usuario" class="form-label">Usuario</label>
              <input
                type="text"
                class="form-control"
                id="usuario"
                name="usuario"
                placeholder="Ingresa tu usuario"
                required>
            </div>

            <!-- Contraseña -->
            <div class="mb-3">
              <label for="contrasena" class="form-label">Contraseña</label>
              <input
                type="password"
                class="form-control"
                id="contrasena"
                name="contrasena"
                placeholder="Ingresa tu contraseña"
                required>
            </div>

            <!-- Botones -->
            <div class="row my-3">
              <div class="col-6">
                <button type="submit" class="btn btn-iniciar w-100">Iniciar</button>
              </div>
              <div class="col-6">
                <a href="index.php" class="btn btn-cancelar w-100">Cancelar</a>
              </div>
            </div>

            <!-- Recuperar contraseña -->
            <div class="text-center mt-2">
              <a href="recuperar_password.php" class="recuperar-link text-decoration-none">
                Recuperar contraseña
              </a>
            </div>

          </form>
        </div>
      </div>
    </main>

    <!-- FOOTER ABAJO DEL TODO -->
    <footer class="login-footer">
      Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
    </footer>

  </div>

  <!-- Bootstrap JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
