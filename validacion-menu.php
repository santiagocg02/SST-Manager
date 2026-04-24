<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/ConexionAPI.php';

requireAuthenticatedSession();
// NUEVO: Atrapa el formulario y guarda la empresa en la sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_empresa'])) {
    $_SESSION['id_empresa'] = trim($_POST['id_empresa']);
    header("Location: menu-empresa.php"); // Redirige al menú después de guardar
    exit;
}

$api = new ConexionAPI();

// 2. Recuperar datos de sesión
$rol = sessionString('rol');
$user = sessionString('usuario');
$token = sessionString('token');

// 3. Consultar Empresas
$resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
$listaEmpresas = (isset($resEmpresas['status']) && $resEmpresas['status'] == 200) ? ($resEmpresas['data'] ?? []) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Menú</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/buttons.css">
  <link rel="stylesheet" href="assets/css/validacion.css">
</head>

<body class="page-validacion">

  <div class="validacion-container">
    <div class="validacion-content">
      <h2>SSTManager</h2>

      <?php if (in_array(strtolower($rol), ['Master', 'administrador'])): ?>
      
          <p>Bienvenido <strong><?= htmlspecialchars($user) ?></strong>. Seleccione el panel a gestionar:</p>
          
          <!-- Mantiene EXACTO el layout bonito -->
          <div class="row align-items-start justify-content-center mt-4 g-4">

            <!-- Admin -->
            <div class="col-12 col-md-6">
              <a href="menu-admin.php"
                 class="btn btn-success w-100 py-3 fs-5 shadow-sm text-nowrap"
                 style="border-radius: 14px; font-weight: 700;">
                Menú Administración
              </a>
            </div>

            <!-- Empresa (botón azul + select debajo, bien integrado) -->
            <div class="col-12 col-md-6">
              <form action="menu-empresa.php" method="GET" class="d-flex flex-column gap-2 mb-0">

                <button type="submit"
                        class="btn text-white w-100 py-3 fs-5 shadow-sm"
                        style="background-color: #003f7a; border-radius: 14px; font-weight: 700;">
                  Menú Empresa
                </button>

                <div class="mt-1">
                  <label class="form-label fw-bold small text-uppercase text-primary mb-1">
                    Empresa a gestionar
                  </label>

                  <select id="id_empresa" name="id_empresa"
                          class="form-select border-primary shadow-sm py-2"
                          style="border-radius: 12px;"
                          required>
                      <option value="">-- Seleccionar Empresa --</option>
                      <?php foreach ($listaEmpresas as $emp): ?>
                          <option value="<?= $emp['id_empresa'] ?>">
                              <?= htmlspecialchars($emp['nombre_empresa']) ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                </div>

              </form>
            </div>

          </div>

      <?php else: ?>
      
          <p>Bienvenido. Ingrese a su panel de gestión:</p>
          <div class="validacion-actions">
            <a href="menu-empresa.php" class="btn btn-menu-alt">Ingresar al Sistema</a>
          </div>

      <?php endif; ?>

      <br>
      <a href="logout.php" class="btn btn-salir mt-3">CERRAR SESIÓN</a>
    </div>
  </div>

  <footer class="validacion-footer">
    Tu aliado estratégico en la gestión de Seguridad y Salud en el Trabajo.
  </footer>

</body>
</html>