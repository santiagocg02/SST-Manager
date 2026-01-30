<?php
session_start();
require_once 'includes/ConexionAPI.php'; 

if (!isset($_SESSION["usuario"]) || (($_SESSION["rol"] ?? '') !== "Master" && ($_SESSION["rol"] ?? '') !== "Administrador")) {
    header("Location: index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$rolSesion = $_SESSION["rol"] ?? '';
$perfilIdSesion = $_SESSION["id_perfil"] ?? 0;

    $misPermisos = [];

    // Solo el Master se salta la carga porque tiene todo en true por defecto
$misPermisos = [];

if ($rolSesion !== "Master") {
    // 1. Realizamos la petición al endpoint
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    
    // 2. IMPORTANTE: Validamos si la data viene envuelta en una llave 'data'
    // Muchos controladores genéricos devuelven ['status' => 200, 'data' => [...]]
    $datosFinales = isset($resPermisos['data']) ? $resPermisos['data'] : $resPermisos;

    if (is_array($datosFinales)) {
        foreach ($datosFinales as $perm) {
            if (isset($perm['id_modulo'])) {
                $idM = (int)$perm['id_modulo'];
                $misPermisos[$idM] = [
                    'ver'      => (int)($perm['ver'] ?? 0),
                    'crear'    => (int)($perm['crear'] ?? 0),
                    'editar'   => (int)($perm['editar'] ?? 0),
                    'eliminar' => (int)($perm['eliminar'] ?? 0)
                ];
            }
        }
    }
}
    /**
     * Función de visibilidad mejorada
     */
    function puedeVer($idModulo, $rol, $permisos) {
        // Los roles de alto nivel saltan la validación
        if ($rol === "Master") {
            return true; 
        }
        
        $id = (int)$idModulo;
        // Retorna true solo si el módulo existe en el array y tiene ver == 1
       return isset($permisos[$id]) && ($permisos[$id]['ver'] == 1);
    }
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SSTManager - Menú Detallado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/menu-admin.css">
  <script>
    // Imprimimos los datos procesados en PHP hacia la consola del navegador
    console.group("SSTManager - Debug de Permisos");
    console.log("Rol de Sesión:", "<?= $rolSesion ?>");
    console.log("Perfil ID:", "<?= $perfilIdSesion ?>");
    console.log("Matriz de Permisos:", <?= json_encode($misPermisos) ?>);
    console.groupEnd();
</script>
</head>

<body class="page-menu-admin">

  <div class="admin-frame">
    <div class="admin-topbar text-uppercase small">Menu Administrador</div>

    <div class="admin-header d-flex justify-content-between align-items-center pe-4">
      <div class="admin-title text-uppercase fw-bold">SSTManager</div>
      <div class="d-flex align-items-center gap-3">
          <span class="text-white small d-none d-md-block">
              Hola, <strong><?= htmlspecialchars($_SESSION["usuario"] ?? 'Usuario') ?></strong>
          </span>
          <a href="logout.php" class="btn btn-sm btn-outline-light text-uppercase">Cerrar Sesión</a>
      </div>
    </div> 

    <div class="admin-body"> 
      <aside class="admin-sidebar">
        <div class="accordion admin-accordion" id="adminMenu">

          <?php if (puedeVer(6, $rolSesion, $misPermisos)): ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingAdmin">
              <button class="accordion-button collapsed admin-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin">
                Administración
              </button>
            </h2>
            <div id="collapseAdmin" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <?php if(puedeVer(7, $rolSesion, $misPermisos)): ?><a href="pages/tipo-empresa.php" target="contentFrame" class="admin-subitem">Tipos de Empresa</a><?php endif; ?>
                <?php if(puedeVer(8, $rolSesion, $misPermisos)): ?><a href="pages/item1072.php" target="contentFrame" class="admin-subitem">Item 1072</a><?php endif; ?>
                <?php if(puedeVer(9, $rolSesion, $misPermisos)): ?><a href="pages/guia-ruc.php" target="contentFrame" class="admin-subitem">Item Guía RUC</a><?php endif; ?>
                <?php if(puedeVer(10, $rolSesion, $misPermisos)): ?><a href="pages/formulario.php" target="contentFrame" class="admin-subitem">Formularios</a><?php endif; ?>
                <?php if(puedeVer(11, $rolSesion, $misPermisos)): ?><a href="pages/calificacion.php" target="contentFrame" class="admin-subitem">Calificación</a><?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (puedeVer(12, $rolSesion, $misPermisos)): ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingEmpresa">
              <button class="accordion-button collapsed admin-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEmpresa">
                Empresa
              </button>
            </h2>
            <div id="collapseEmpresa" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <?php if(puedeVer(13, $rolSesion, $misPermisos)): ?><a href="#" class="admin-subitem">Crear</a><?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (puedeVer(1, $rolSesion, $misPermisos)): ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingSeguridad">
              <button class="accordion-button collapsed admin-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeguridad">
                Seguridad
              </button>
            </h2>
            <div id="collapseSeguridad" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
              <div class="accordion-body py-2">
                <?php if(puedeVer(2, $rolSesion, $misPermisos)): ?><a href="pages/seguridad/modulo.php" target="contentFrame" class="admin-subitem">Módulos</a><?php endif; ?>
                <?php if(puedeVer(3, $rolSesion, $misPermisos)): ?><a href="pages/seguridad/perfil.php" target="contentFrame" class="admin-subitem">Perfiles</a><?php endif; ?>
                <?php if(puedeVer(4, $rolSesion, $misPermisos)): ?><a href="pages/seguridad/Planes.php" target="contentFrame" class="admin-subitem">Planes</a><?php endif; ?>
                <?php if(puedeVer(5, $rolSesion, $misPermisos)): ?><a href="pages/seguridad/Usuarios.php" target="contentFrame" class="admin-subitem">Usuarios</a><?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </aside>

      <main class="admin-content">
        <iframe id="contentFrame" name="contentFrame" src="pages/bienvenida.php" class="admin-iframe"></iframe>
      </main>
    </div>

    <footer class="admin-footer text-center">
      <span>© 2026 SSTManager · Tu aliado estratégico en SST</span>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>