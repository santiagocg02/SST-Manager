<?php
session_start();
require_once 'includes/ConexionAPI.php'; // Asegúrate de que la ruta sea correcta

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario    = trim($_POST["usuario"] ?? "");
    $contrasena = trim($_POST["contrasena"] ?? "");

    if (!empty($usuario) && !empty($contrasena)) {
        $api = new ConexionAPI();
        
        $datosLogin = [
            "email" => $usuario, 
            "password" => $contrasena
        ];

        // Petición al backend
        $respuesta = $api->solicitar("index.php?action=login", "POST", $datosLogin);

        if ($respuesta['status'] === 200) {
            // El JSON decodificado está en $respuesta['data']
            $datosAPI = $respuesta['data'];

            // 1. Guardar Token (está en la raíz del JSON)
            $_SESSION["token"] = $datosAPI['token'] ?? null; 
            
            // 2. Guardar Usuario (usamos el del formulario o el nombre que devuelve la API)
            $_SESSION["usuario"] = $datosAPI['user']['datos_personales']['nombre_completo'] ?? $usuario;

            // 3. CORRECCIÓN PRINCIPAL: Ruta exacta al rol
            // Entramos a 'user' -> 'seguridad' -> 'rol_sistema'
            if (isset($datosAPI['user']['seguridad']['rol_sistema'])) {
                $_SESSION["rol"] = $datosAPI['user']['seguridad']['rol_sistema']; 
            } else {
                $_SESSION["rol"] = "user"; // Fallback
            }

            if (isset($datosAPI['user']['seguridad']['perfil']['id'])) {
                $_SESSION["id_perfil"] = $datosAPI['user']['seguridad']['perfil']['id']; 
            } else {
                $_SESSION["id_perfil"] = "user"; // Fallback
            }

            header("Location: validacion-menu.php");
            exit;
            
        } else {
            // Error de la API
            $mensaje = $respuesta['data']['mensaje'] ?? "Credenciales incorrectas.";
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
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
