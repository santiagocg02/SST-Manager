<?php
session_start();
require_once 'includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$rolSesion = $_SESSION["rol"] ?? '';
$perfilIdSesion = $_SESSION["id_perfil"] ?? 0;
$empresa = $_SESSION["id_empresa"] ?? 0;
$nombreEmpresaLogeada = "Sin Empresa";
$idPlanEmpresa = 0;

if (in_array(strtolower($rolSesion), ['master', 'administrador']) && !empty($_REQUEST["id_empresa"])) {
    $_SESSION["id_empresa"] = (int)$_REQUEST["id_empresa"];
}

$resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
$todasLasEmpresas = (isset($resEmpresas['status']) && $resEmpresas['status'] == 200) ? ($resEmpresas['data'] ?? []) : [];

// 3. Buscar la empresa específica y extraer solo su nombre
foreach ($todasLasEmpresas as $emp) {
    if (isset($emp['id_empresa']) && $emp['id_empresa'] == $empresa) {
        $nombreEmpresaLogeada = $emp['nombre_empresa'];
        $idPlanEmpresa = (int)($emp['id_plan'] ?? 0);
        break; // Detenemos el ciclo porque ya encontramos el nombre
    }
}

$misPermisos = [];

$modulosPermitidosPorPlan = [];

// A. Validar qué módulos permite el plan de la EMPRESA (Aplica para TODOS los usuarios)
if ($idPlanEmpresa > 0) {
    $resPlan = $api->solicitar("planes/permisos/$idPlanEmpresa", "GET", null, $token);
    $datosPlan = $resPlan['data'] ?? $resPlan;
    
    if (is_array($datosPlan)) {
        foreach ($datosPlan as $p) {
            // Guardamos los IDs de módulos que el plan permite a la empresa
            $modulosPermitidosPorPlan[] = (int)$p['id_modulo'];
        }
    }
}

// B. Carga de permisos tradicionales (Perfiles de usuario)
if ($rolSesion !== "Master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    $datosFinales = $resPermisos['data'] ?? $resPermisos;

    if (is_array($datosFinales)) {
        foreach ($datosFinales as $perm) {
            if (isset($perm['id_modulo'])) {
                $idM = (int)$perm['id_modulo'];
                $misPermisos[$idM] = [
                    'ver' => (int)($perm['ver'] ?? 0)
                ];
            }
        }
    }
}

// 3. FUNCIÓN DE VALIDACIÓN MEJORADA
function puedeVer($idModulo, $rol, $permisos, $modulosPlan) {
    $id = (int)$idModulo;
    $rolLower = strtolower($rol);

    // Si es Master, ve todo a menos que el plan lo limite (opcional)
    if ($rolLower === "master") return true; 

    // Si es Administrador, debe estar en el plan
    if ($rolLower === "administrador") {
        return in_array($id, $modulosPlan);
    }

    // Para otros roles (Usuario, SST, etc.), debe estar en el plan Y tener permiso en su perfil
    return in_array($id, $modulosPlan) && isset($permisos[$id]) && (int)$permisos[$id]['ver'] === 1;
}
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
  .admin-subitem.active {
    color: #198754 !important;
    font-weight: 600;
    border-left: 4px solid #198754;
    padding-left: 12px !important;
    background: rgba(25,135,84,.08);
  }
  .accordion-button.active-parent {
    background: #198754 !important;
    color: #fff !important;
  }
  .admin-subitem:hover {
    background: rgba(25,135,84,.05);
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

    <div class="admin-header d-flex justify-content-between align-items-center pe-4">
      <div class="admin-title text-uppercase fw-bold">SSTManager <span class="fs-6  text-white fw-normal ms-2 text-secondary">Empresa:<?= htmlspecialchars($nombreEmpresaLogeada) ?></span></div>
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
    <?php if (puedeVer(12, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingEmpresa">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseEmpresa">
          Empresa
        </button>
      </h2>

      <div id="collapseEmpresa" class="accordion-collapse collapse">
        <div class="accordion-body py-2">

          <?php if(puedeVer(13, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?><a href="pages-empresa/empresa/Empresa.php" target="contentFrame" class="admin-subitem">Ver</a><?php endif; ?>
              

        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ===================== MODULOS ===================== -->
    <?php if (puedeVer(20, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingModulos">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseModulos">
          Modulos
        </button>
      </h2>

      <div id="collapseModulos" class="accordion-collapse collapse">
        <div class="accordion-body py-2">
          <?php if (puedeVer(21, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
            <a href="pages-empresa/modulos/planear.php" target="contentFrame" class="admin-subitem">Planear</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ===================== REPORTES ===================== -->
    <?php if (puedeVer(30, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
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
          <?php if (puedeVer(31, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
            <a href="pages/reportes/bienvenida.php" target="contentFrame" class="admin-subitem">Dashboard</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ===================== SEGURIDAD ===================== -->
    <?php if (puedeVer(1, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingSeguridad">
        <button class="accordion-button collapsed admin-accordion-btn" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseSeguridad">
          Seguridad
        </button>
      </h2>

      <div id="collapseSeguridad" class="accordion-collapse collapse">
        <div class="accordion-body py-2">
          <?php if (puedeVer(3, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?><a href="pages-empresa/seguridad/perfil.php" target="contentFrame" class="admin-subitem">Perfiles</a><?php endif; ?>
          <?php if (puedeVer(5, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?><a href="pages-empresa/seguridad/Usuarios.php" target="contentFrame" class="admin-subitem">Usuarios</a><?php endif; ?>
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
