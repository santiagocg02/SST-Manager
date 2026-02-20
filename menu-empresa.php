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

// Carga permisos (Master se salta)
if ($rolSesion !== "Master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
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

function puedeVer($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    $id = (int)$idModulo;
    return isset($permisos[$id]) && ((int)$permisos[$id]['ver'] === 1);
}

/**
 * IDs sugeridos (ajústalos a tu tabla de módulos cuando los crees):
 * 12 = Empresa (menú padre)
 * 13 = Información Empresa
 * 14 = Representante Legal
 * 15 = Información de Trabajadores
 * 16 = Descripción de la Organización
 * 17 = Organización del Tiempo de Trabajo
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SSTManager - Menú Empresa</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/menu-admin.css">

  <style>
    .admin-subitem.active{
      color:#198754 !important;
      font-weight:600;
      border-left:4px solid #198754;
      padding-left:12px !important;
      background:rgba(25,135,84,.08);
    }
    .accordion-button.active-parent{
      background:#198754 !important;
      color:#fff !important;
    }
    .admin-subitem:hover{
      background:rgba(25,135,84,.05);
    }
  </style>

  <script>
    console.group("SSTManager - Debug Permisos (Menú Empresa)");
    console.log("Rol de Sesión:", "<?= $rolSesion ?>");
    console.log("Perfil ID:", "<?= $perfilIdSesion ?>");
    console.log("Matriz de Permisos:", <?= json_encode($misPermisos) ?>);
    console.groupEnd();
  </script>
</head>

<body class="page-menu-admin">

  <div class="admin-frame">
    <div class="admin-topbar text-uppercase small">Menu Empresa</div>

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

    <!-- ===================== EMPRESA ===================== -->
    <?php if (puedeVer(12, $rolSesion, $misPermisos)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingEmpresa">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseEmpresa">
          Empresa
        </button>
      </h2>

      <div id="collapseEmpresa" class="accordion-collapse collapse">
        <div class="accordion-body py-2">

          <?php if(puedeVer(13, $rolSesion, $misPermisos)): ?><a href="pages-empresa/empresa/Empresa.php" target="contentFrame" class="admin-subitem">Crear</a><?php endif; ?>
              

        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ===================== MODULOS ===================== -->
    <?php if (puedeVer(20, $rolSesion, $misPermisos)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingModulos">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseModulos">
          Modulos
        </button>
      </h2>

      <div id="collapseModulos" class="accordion-collapse collapse">
        <div class="accordion-body py-2">
          <!-- placeholders (luego los conectamos) -->
          <?php if (puedeVer(21, $rolSesion, $misPermisos)): ?>
            <a href="pages/modulos/bienvenida.php" target="contentFrame" class="admin-subitem">Ver módulos</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ===================== REPORTES ===================== -->
    <?php if (puedeVer(30, $rolSesion, $misPermisos)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingReportes">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseReportes">
          Reportes
        </button>
      </h2>

      <div id="collapseReportes" class="accordion-collapse collapse">
        <div class="accordion-body py-2">
          <!-- placeholders -->
          <?php if (puedeVer(31, $rolSesion, $misPermisos)): ?>
            <a href="pages/reportes/bienvenida.php" target="contentFrame" class="admin-subitem">Dashboard</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ===================== SEGURIDAD ===================== -->
    <?php if (puedeVer(1, $rolSesion, $misPermisos)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingSeguridad">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseSeguridad">
          Seguridad
        </button>
      </h2>

      <div id="collapseSeguridad" class="accordion-collapse collapse">
        <div class="accordion-body py-2">
          <?php if (puedeVer(3, $rolSesion, $misPermisos)): ?><a href="pages-empresa/seguridad/perfil.php" target="contentFrame" class="admin-subitem">Perfiles</a><?php endif; ?>
          <?php if (puedeVer(5, $rolSesion, $misPermisos)): ?><a href="pages-empresa/seguridad/Usuarios.php" target="contentFrame" class="admin-subitem">Usuarios</a><?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</aside>


      <main class="admin-content">
        <iframe id="contentFrame" name="contentFrame"
                src="pages/empresa/bienvenida-empresa.php"
                class="admin-iframe"></iframe>
      </main>
    </div>

    <footer class="admin-footer text-center">
      <span>© 2026 SSTManager · Tu aliado estratégico en SST</span>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ===== MENU ACTIVO + SUBMENU FIJO (CON IFRAME) =====
    const menuLinks = document.querySelectorAll(".admin-subitem");
    const frame = document.getElementById("contentFrame");

    function activarMenu(link) {
      menuLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");

      const href = link.getAttribute("href");
      sessionStorage.setItem("menuEmpresaActivo", href);

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

    menuLinks.forEach(link => {
      link.addEventListener("click", function() {
        activarMenu(this);
      });
    });

    document.addEventListener("DOMContentLoaded", () => {
      const guardado = sessionStorage.getItem("menuEmpresaActivo");

      if (!guardado) {
        frame.src = "pages/empresa/bienvenida-empresa.php";
        return;
      }

      const link = Array.from(menuLinks).find(l => l.getAttribute("href") === guardado);

      if (link) {
        activarMenu(link);
        frame.src = guardado;
      } else {
        frame.src = "pages/empresa/bienvenida-empresa.php";
      }
    });
  </script>

</body>
</html>
