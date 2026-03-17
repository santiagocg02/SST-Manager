<?php
session_start();
require_once 'includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$rolSesion = $_SESSION["rol"] ?? '';
$perfilIdSesion = (int)($_SESSION["id_perfil"] ?? 0);
$empresa = (int)($_SESSION["id_empresa"] ?? 0);

// --- Lógica de Permisos y Plan (Manteniendo tu estructura original) ---
$nombreEmpresaLogeada = "Sin Empresa";
$idPlanEmpresa = 0;

$resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
$todasLasEmpresas = (isset($resEmpresas['data'])) ? $resEmpresas['data'] : [];

foreach ($todasLasEmpresas as $emp) {
    if ((int)($emp['id_empresa'] ?? 0) === $empresa) {
        $nombreEmpresaLogeada = $emp['nombre_empresa'] ?? 'Sin Empresa';
        $idPlanEmpresa = (int)($emp['id_plan'] ?? 0);
        break;
    }
}

$modulosPermitidosPorPlan = [];
if ($idPlanEmpresa > 0) {
    $resPlan = $api->solicitar("planes/permisos/$idPlanEmpresa", "GET", null, $token);
    $datosPlan = $resPlan['data'] ?? [];
    foreach ($datosPlan as $p) {
        if (is_array($p) && isset($p['id_modulo'])) $modulosPermitidosPorPlan[] = (int)$p['id_modulo'];
        elseif (is_numeric($p)) $modulosPermitidosPorPlan[] = (int)$p;
    }
}

$misPermisos = [];
if (strtolower($rolSesion) !== "master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    $datosFinales = $resPermisos['data'] ?? [];
    foreach ($datosFinales as $perm) {
        if (isset($perm['id_modulo'])) {
            $misPermisos[(int)$perm['id_modulo']] = ['ver' => (int)($perm['ver'] ?? 0)];
        }
    }
}

function puedeVer($idModulo, $rol, $permisos, $modulosPlan) {
    $rolLower = strtolower((string)$rol);
    if ($rolLower === "master") return true;
    $enPlan = in_array((int)$idModulo, $modulosPlan, true);
    if ($rolLower === "administrador") return $enPlan;
    return $enPlan && isset($permisos[$idModulo]) && (int)$permisos[$idModulo]['ver'] === 1;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SSTManager - Menú Empresa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/menu-admin.css">
  <style>
    .admin-subitem.active { 
        color: #198754 !important; font-weight: 700; border-left: 4px solid #198754; 
        padding-left: 12px !important; background: rgba(25,135,84,.08); 
    }
    .accordion-button.active-parent { background: #198754 !important; color: #fff !important; }
    .admin-sidebar { overflow-y: auto; height: calc(100vh - 100px); }
  </style>
</head>

<body class="page-menu-admin">
  <div class="admin-frame">
    
    <div class="admin-header d-flex justify-content-between align-items-center pe-4">
      <div class="admin-title text-uppercase fw-bold">
        SSTManager <span class="fs-6 text-white-50 fw-normal ms-2">Empresa: <?= htmlspecialchars($nombreEmpresaLogeada) ?></span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white small d-none d-md-block">Hola, <strong><?= htmlspecialchars($_SESSION["usuario"]) ?></strong></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light">CERRAR SESIÓN</a>
      </div>
    </div>

    <div class="admin-body">
      <aside class="admin-sidebar">
  <div class="accordion admin-accordion" id="adminMenu">

    <div class="accordion-item">
      <h2 class="accordion-header">
        <a href="pages-empresa/bienvenidaes.php" target="contentFrame" class="accordion-button collapsed admin-accordion-btn no-arrow">
          <i class="fa-solid fa-house me-2"></i> Inicio / Dashboard
        </a>
      </h2>
    </div>

    <?php if (puedeVer(12, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapseEmpresa">
          <i class="fa-solid fa-building me-2"></i> Empresa
        </button>
      </h2>
      <div id="collapseEmpresa" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <?php if (puedeVer(13, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
            <a href="pages-empresa/empresa/Empresa.php" target="contentFrame" class="admin-subitem">Ver Información</a>
          <?php endif; ?>
          <a href="pages-empresa/empresa/evaluacion_empresa.php?id=<?= $empresa ?>" target="contentFrame" class="admin-subitem">Autoevaluación</a>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (puedeVer(16, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapseSST">
          <i class="fa-solid fa-helmet-safety me-2"></i> Gestión SST
        </button>
      </h2>
      <div id="collapseSST" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <a href="pages-empresa/modulos/planear.php" target="contentFrame" class="admin-subitem">Planear (PHVA)</a>
          <a href="pages-empresa/modulos/vigia.php" target="contentFrame" class="admin-subitem">Vigía / COPASST</a>
          <a href="pages-empresa/modulos/plan_trabajo.php" target="contentFrame" class="admin-subitem">Plan de Trabajo</a>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (puedeVer(19, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapseReportes">
          <i class="fa-solid fa-chart-line me-2"></i> Reportes
        </button>
      </h2>
      <div id="collapseReportes" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <a href="pages-empresa/bienvenidaes.php" target="contentFrame" class="admin-subitem">Dashboard General</a>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (puedeVer(1, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapseSeguridad">
          <i class="fa-solid fa-shield-halved me-2"></i> Seguridad
        </button>
      </h2>
      <div id="collapseSeguridad" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <a href="pages-empresa/seguridad/perfil.php" target="contentFrame" class="admin-subitem">Perfiles</a>
          <a href="pages-empresa/seguridad/Usuarios.php" target="contentFrame" class="admin-subitem">Usuarios</a>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</aside>
      <main class="admin-content">
        <iframe id="contentFrame" name="contentFrame" src="pages-empresa/bienvenidaes.php?nombre=${nombreE}" class="admin-iframe"></iframe>
      </main>
    </div>

    <footer class="admin-footer text-center">
      <span>© 2026 SSTManager · Tu aliado estratégico en SST</span>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const menuLinks = document.querySelectorAll(".admin-subitem, .admin-accordion-btn");
    const frame = document.getElementById("contentFrame");

    // Función para marcar el activo y asegurar que el acordeón se mantenga abierto
    function activarLink(link) {
      menuLinks.forEach(l => l.classList.remove("active", "active-parent"));
      link.classList.add("active");

      // Si es un subítem, marcamos el padre
      const collapse = link.closest(".accordion-collapse");
      if (collapse) {
        const btnPadre = collapse.previousElementSibling.querySelector(".accordion-button");
        if (btnPadre) btnPadre.classList.add("active-parent");
      }
    }

    menuLinks.forEach(link => {
      link.addEventListener("click", function() {
        if(this.getAttribute("href")) {
            activarLink(this);
            sessionStorage.setItem("lastPageSST", this.getAttribute("href"));
        }
      });
    });

    // EVITAR AVERÍAS: Al cargar el menú, siempre ir al Bienvenida
    document.addEventListener("DOMContentLoaded", () => {
    // Pasamos el nombre de la empresa como parámetro para que Bienvenida lo capture
    const nombreE = encodeURIComponent("<?= $nombreEmpresaLogeada ?>");
    frame.src = `pages-empresa/bienvenidaes.php?nombre=${nombreE}`;
    
    menuLinks.forEach(l => l.classList.remove("active"));
    sessionStorage.removeItem("lastPageSST");
    });
  </script>
</body>
</html>