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
if ($rolSesion !== "Master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);

    // A veces viene envuelto en 'data'
    $datosFinales = isset($resPermisos['data']) ? $resPermisos['data'] : $resPermisos;

    if (is_array($datosFinales)) {
        foreach ($datosFinales as $perm) {
            if (isset($perm['id_modulo'])) {
                $idM = (int)$perm['id_modulo'];
                $misPermisos[$idM] = [
                    'ver' => (int)($perm['ver'] ?? 0),
                    'crear' => (int)($perm['crear'] ?? 0),
                    'editar' => (int)($perm['editar'] ?? 0),
                    'eliminar' => (int)($perm['eliminar'] ?? 0)
                ];
            }
        }
    }
}

/**
 * Visibilidad por permisos
 */
function puedeVer($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    $id = (int)$idModulo;
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

  <style>
    /* ITEM ACTIVO (subrayado/indicador verde) */
    .admin-subitem.active{
      color:#198754 !important;
      font-weight:600;
      border-left:4px solid #198754;
      padding-left:12px !important;
      background:rgba(25,135,84,.08);
    }

    /* Botón padre activo */
    .accordion-button.active-parent{
      background:#198754 !important;
      color:#fff !important;
    }

    .admin-subitem:hover{
      background:rgba(25,135,84,.05);
    }
  </style>

  <script>
    console.group("SSTManager - Debug de Permisos");
    console.log("Rol de Sesión:", "<?= $rolSesion ?>");
    console.log("Perfil ID:", "<?= $perfilIdSesion ?>");
    console.log("Matriz de Permisos:", <?= json_encode($misPermisos) ?>);
    console.groupEnd();
  </script>
</head>

<body class="page-menu-admin">

  <div class="admin-frame">
    

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

            <!-- IMPORTANTE: sin data-bs-parent para que NO se cierre -->
            <div id="collapseAdmin" class="accordion-collapse collapse">
              <div class="accordion-body py-2">
                <?php if(puedeVer(7, $rolSesion, $misPermisos)): ?><a href="pages/administracion/tipo-empresa.php" target="contentFrame" class="admin-subitem">Tipos de Empresa</a><?php endif; ?>
                <?php if(puedeVer(8, $rolSesion, $misPermisos)): ?><a href="pages/administracion/item1072.php" target="contentFrame" class="admin-subitem">Item 1072</a><?php endif; ?>
                <?php if(puedeVer(9, $rolSesion, $misPermisos)): ?><a href="pages/administracion/guia-ruc.php" target="contentFrame" class="admin-subitem">Item Guía RUC</a><?php endif; ?>
                <?php if(puedeVer(11, $rolSesion, $misPermisos)): ?><a href="pages/administracion/calificacion.php" target="contentFrame" class="admin-subitem">Calificación</a><?php endif; ?>
                <?php if(puedeVer(10, $rolSesion, $misPermisos)): ?><a href="pages/administracion/formulario.php" target="contentFrame" class="admin-subitem">Formularios</a><?php endif; ?>
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

            <div id="collapseEmpresa" class="accordion-collapse collapse">
              <div class="accordion-body py-2">
                <?php if(puedeVer(13, $rolSesion, $misPermisos)): ?><a href="pages/empresa/Empresa.php" target="contentFrame" class="admin-subitem">Crear</a><?php endif; ?>
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

            <div id="collapseSeguridad" class="accordion-collapse collapse">
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
        <!-- BIENVENIDA POR DEFECTO -->
        <iframe id="contentFrame" name="contentFrame" src="administracion/pages/bienvenida.php" class="admin-iframe"></iframe>
      </main>
    </div>

    <footer class="admin-footer text-center">
      <span>© 2026 SSTManager · Tu aliado estratégico en SST</span>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ===== MENU ACTIVO + SUBMENU FIJO (CON IFRAME) =====
    // IMPORTANTE: Usamos sessionStorage para que al abrir una NUEVA pestaña vuelva a bienvenida

    // Limpia el valor viejo si alguna vez usaste localStorage (una sola vez)
    // localStorage.removeItem("menuActivo");

    const menuLinks = document.querySelectorAll(".admin-subitem");
    const frame = document.getElementById("contentFrame");

    function activarMenu(link) {
      menuLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");

      const href = link.getAttribute("href");
      sessionStorage.setItem("menuActivo", href);

      // abrir y mantener abierto el submenu padre
      const collapse = link.closest(".accordion-collapse");
      if (collapse) {
        collapse.classList.add("show");

        const btn = collapse.previousElementSibling.querySelector(".accordion-button");
        if (btn) {
          btn.classList.remove("collapsed");
          btn.classList.add("active-parent");
          btn.setAttribute("aria-expanded", "true");
        }
      }
    }

    // Click: marca activo (el iframe lo cambia el target="contentFrame")
    menuLinks.forEach(link => {
      link.addEventListener("click", function() {
        activarMenu(this);
      });
    });

    // Al cargar: si no hay nada guardado, mostramos bienvenida (administracion/pages/bienvenida.php)
    document.addEventListener("DOMContentLoaded", () => {
      const guardado = sessionStorage.getItem("menuActivo");

      if (!guardado) {
        frame.src = "pages/bienvenida.php";
        return;
      }

      const link = Array.from(menuLinks).find(l => l.getAttribute("href") === guardado);

      if (link) {
        activarMenu(link);
        frame.src = guardado;
      } else {
        // por si el link guardado ya no existe por permisos
        frame.src = "pages/bienvenida.php";
      }
    });
  </script>

</body>
</html>
