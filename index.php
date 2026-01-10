<?php
session_start();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario    = trim($_POST["usuario"] ?? "");
    $contrasena = trim($_POST["contrasena"] ?? ""); // <-- debe llamarse igual que el name del input

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
  <meta charset="UTF-8">
  <title>SSTManager - Login</title>

  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet">

  <!-- Tu CSS externo -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

  <div class="min-vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow login-card">
      <div class="card-body">
        
        <!-- Título -->
        <h3 class="text-center mb-4 login-title">SSTManager</h3>

        <!-- Mensaje de error (si lo hay) -->
        <?php if (!empty($mensaje)): ?>
          <div class="alert alert-danger py-2" role="alert">
            <?= htmlspecialchars($mensaje) ?>
          </div>
        <?php endif; ?>

        <!-- Formulario -->
        <!-- action vacío = envía a este mismo archivo -->
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
              required
            >
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
              required
            >
          </div>

          <!-- Botones centrados -->
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

      <!-- Franja inferior -->
      <div class="login-footer">
        Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
  </script>

</body>
</html>
