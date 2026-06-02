<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/ConexionAPI.php';

requireAuthenticatedSession();

$api = new ConexionAPI();
$token = sessionString('token');
$rolSesion = sessionString('rol');
$perfilIdSesion = sessionInt('id_perfil');
$empresa = sessionInt('id_empresa');

// --- Lógica de Permisos y Plan ---
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
if (normalizedRole($rolSesion) !== APP_ROLE_MASTER) {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    $datosFinales = $resPermisos['data'] ?? [];
    foreach ($datosFinales as $perm) {
        if (isset($perm['id_modulo'])) {
            $misPermisos[(int)$perm['id_modulo']] = ['ver' => (int)($perm['ver'] ?? 0)];
        }
    }
}

function puedeVer($idModulo, $rol, $permisos, $modulosPlan) {
    $rolLower = normalizedRole((string)$rol);
    if ($rolLower === APP_ROLE_MASTER) return true;
    $enPlan = in_array((int)$idModulo, $modulosPlan, true);
    if ($rolLower === APP_ROLE_ADMINISTRADOR) return $enPlan;
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .admin-subitem.active { 
        color: #198754 !important; font-weight: 700; border-left: 4px solid #198754; 
        padding-left: 12px !important; background: rgba(25,135,84,.08); 
    }
    .accordion-button.active-parent { background: #198754 !important; color: #fff !important; }
    .admin-sidebar { overflow-y: auto; height: calc(100vh - 100px); }
    /* Corrección de subrayados en enlaces */
    a.admin-accordion-btn, a.admin-subitem { text-decoration: none !important; }
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
        
        <!-- BOTÓN ADICIONADO CONDICIONALMENTE: Solo si es MASTER -->
           <style>
  .btn-volver-custom {
    border: 1.5px solid #28a745; /* Un verde un poco más brillante para que destaque sutilmente */
    color: #28a745;
    background: transparent;
    font-size: 0.75rem; /* Tamaño compacto y profesional */
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border-radius: 4px;
    padding: 4px 12px;
}

.btn-volver-custom:hover {
    background: #28a745;
    color: #ffffff !important;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.4);
    transform: translateY(-1px);
}
</style>
        <?php if (normalizedRole($rolSesion) === APP_ROLE_MASTER): ?>
         <a href="validacion-menu.php" class="btn btn-sm btn-volver-custom text-uppercase fw-bold me-2">
    <i class="fa-solid fa-table-cells me-1"></i> Inicio
  </a>
        <?php endif; ?>

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
          <?php if (puedeVer(17, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
          <a href="pages-empresa/modulos/planear.php" target="contentFrame" class="admin-subitem">Planear </a>
          <?php endif; ?>
           <?php if (puedeVer(18, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
          <a href="pages-empresa/modulos/hacer.php" target="contentFrame" class="admin-subitem">Hacer</a>
          <?php endif; ?>
          <?php if (puedeVer(20, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
          <a href="pages-empresa/modulos/verificar.php" target="contentFrame" class="admin-subitem">Verificar</a>
        <?php endif; ?>
          <?php if (puedeVer(21, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
          <a href="pages-empresa/modulos/actuar.php" target="contentFrame" class="admin-subitem">Actuar</a>
        <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if (puedeVer(22, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapsegh">
          <i class="fa-solid fa-users-gear me-2"></i> Gestión Humana
        </button>
      </h2>
      <div id="collapsegh" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <?php if (puedeVer(23, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
          <a href="pages-empresa/modulos/gestionhumana/Vinculacion.php" target="contentFrame" class="admin-subitem">Ingreso</a>
        <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (puedeVer(24, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
    <div class="accordion-item">
      <h2 class="accordion-header">
        <!-- CORREGIDO: Se cambió el ID de colapso para evitar conflicto con #collapsegh -->
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapseCapacitaciones">
          <i class="fa-solid fa-graduation-cap me-2"></i> Capacitaciones
        </button>
      </h2>
      <div id="collapseCapacitaciones" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <?php if (puedeVer(25, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>
          <a href="pages-empresa/modulos/gestionhumana/Capacitaciones.php" target="contentFrame" class="admin-subitem">Capacitación</a>
        <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- =============== OPCIONES: PESV Y ALTURAS =============== -->
    <?php if (puedeVer(26, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?>   

    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapsePESV">
          <i class="fa-solid fa-car me-2"></i> PESV
        </button>
      </h2>
      <div id="collapsePESV" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <?php if (puedeVer(27, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/modulos/pesv/planear.php" target="contentFrame" class="admin-subitem">Planear</a>
          <?php endif; ?>
          <?php if (puedeVer(28, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/modulos/pesv/hacer.php" target="contentFrame" class="admin-subitem">Hacer</a>
          <?php endif; ?>
          <?php if (puedeVer(29, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/modulos/pesv/verificar.php" target="contentFrame" class="admin-subitem">Verificar</a>
          <?php endif; ?>
          <?php if (puedeVer(30, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/modulos/pesv/actuar.php" target="contentFrame" class="admin-subitem">Actuar</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (puedeVer(31, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button collapsed admin-accordion-btn" data-bs-toggle="collapse" data-bs-target="#collapseAlturas">
          <i class="fa-solid fa-person-arrow-up-from-line me-2"></i> Programa de Alturas
        </button>
      </h2>
      <div id="collapseAlturas" class="accordion-collapse collapse" data-bs-parent="#adminMenu">
        <div class="accordion-body py-2">
          <?php if (puedeVer(32, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/modulos/pda/invalturas.php" target="contentFrame" class="admin-subitem">Inventario de Alturas</a>
          <?php endif; ?>
          <?php if (puedeVer(33, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/modulos/pda/proalturas.php" target="contentFrame" class="admin-subitem">Programa de Alturas</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <!-- =============================================================== -->

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
          <?php if (puedeVer(5, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/seguridad/perfil.php" target="contentFrame" class="admin-subitem">Perfiles</a>
          <?php endif; ?>
          <?php if (puedeVer(3, $rolSesion, $misPermisos, $modulosPermitidosPorPlan)): ?> 
          <a href="pages-empresa/seguridad/Usuarios.php" target="contentFrame" class="admin-subitem">Usuarios</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</aside>
      <main class="admin-content">
        <iframe id="contentFrame" name="contentFrame" src="pages-empresa/bienvenidaes.php" class="admin-iframe"></iframe>
        <iframe id="contentFrame" name="contentFrame" class="admin-iframe"></iframe>
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
    // Pasamos el nombre de la empresa como parámetro para que Bienvenida lo capture
    const nombreE = encodeURIComponent("<?= $nombreEmpresaLogeada ?>");

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
            activLink(this);
            sessionStorage.setItem("lastPageSST", this.getAttribute("href"));
        }
      });
    });

    // Al cargar el menú, siempre ir al Bienvenida por consistencia
    document.addEventListener("DOMContentLoaded", () => {
      const nombreE = encodeURIComponent("<?= $nombreEmpresaLogeada ?>");
      frame.src = `pages-empresa/bienvenidaes.php?nombre=${nombreE}`;
      
      menuLinks.forEach(l => l.classList.remove("active"));
      sessionStorage.removeItem("lastPageSST");
    frame.src = `pages-empresa/bienvenidaes.php?nombre=${nombreE}`;
    
    menuLinks.forEach(l => l.classList.remove("active"));
    sessionStorage.removeItem("lastPageSST");
    });
  </script>
</body>
</html>