<?php
session_start();

// 1. CONEXIÓN Y VALIDACIÓN
require_once '../../includes/ConexionAPI.php'; 

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

// 2. OBTENER LOGO DINÁMICO
$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
$logoUrl = "";

if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("empresas/$empresaId", "GET", null, $token);
    $logoUrl = $resEmpresa['data']['logo_url'] ?? ""; 
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SST Manager - Hacer</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/main-style.css">
  <link rel="stylesheet" href="../../assets/css/planear.css">

  <style>
    #soporteDrawer{ width:min(980px, 92vw); }
    #soporteDrawer .offcanvas-body{ height:calc(100vh - 64px); background:#fff; }
    #soporteDrawer iframe{ width:100%; height:100%; border:0; background:#fff; display:block; }
    /* Ajuste para que el logo no se deforme */
    .logo-box img { max-width: 100%; max-height: 100px; object-fit: contain; }
  </style>
</head>

<body>

<div class="planear-page-scroll">
  <div class="page-wrap">

    <div class="row g-3 mb-2">
      <div class="col-12">
        <div class="planear-hero card-soft">
          <div class="hero-inner d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
              <h4 class="sheet-title">
                <i class="fa-solid fa-users-gear me-2"></i> HACER
              </h4>
              <div class="sheet-subtitle">Implementación y ejecución del SG - SST</div>
            </div>

            <div class="score-box">
              <div class="muted-small mb-1">Progreso general</div>
              <div class="d-flex align-items-end justify-content-between">
                <div class="h5 mb-0"><span id="scorePct">0%</span></div>
                <div class="muted-small">
                  <span id="scorePts">0</span> / <span id="maxPts">0</span> pts
                </div>
              </div>
              <div class="progress progress-pill mt-2">
                <div id="scoreBar" class="progress-bar" role="progressbar" style="width:0%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 planear-split">

      <div class="col-12 col-xl-6">
        <div class="card-soft p-3 bg-white">
          <div class="table-toolbar">
            <div class="toolbar-left">
              Ítems (Hacer) <span class="badge text-bg-primary" id="countBadge">0</span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <input id="searchInput" type="text" class="form-control form-control-sm searchbox" placeholder="Buscar ítem o actividad...">
              <button id="resetBtn" class="btn btn-sm btn-outline-secondary btn-reset" type="button">Reset</button>
            </div>
          </div>

          <div class="excel-table">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0" id="hacerTable">
                <thead>
                  <tr>
                    <th class="col-item">ÍTEM</th>
                    <th>ACTIVIDAD</th>
                    <th class="col-soporte text-center">SOPORTE</th>
                    <th class="col-cal text-center">CALIFICACIÓN</th>
                  </tr>
                </thead>
                <tbody id="hacerBody"></tbody>
              </table>
            </div>
          </div>
          <div class="note">* Al dar clic en SOPORTE, el formato se abrirá en un panel lateral derecho.</div>
        </div>
      </div>

      <div class="col-12 col-xl-6">
        <div class="card-soft p-3 bg-white h-100">

          <div class="d-flex justify-content-end">
            <div class="mini-legend">
              <div class="mini-legend-row">
                <span class="mini-label">Sí</span><span class="mini-dot ok"></span><span class="mini-val">2</span>
              </div>
              <div class="mini-legend-row">
                <span class="mini-label">En proceso</span><span class="mini-dot warn"></span><span class="mini-val">1</span>
              </div>
              <div class="mini-legend-row">
                <span class="mini-label">No</span><span class="mini-dot no"></span><span class="mini-val">0</span>
              </div>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-12">
              <div class="panel-block">
                <h6 class="panel-title">FASE DE EJECUCIÓN</h6>
                <div class="panel-list">
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> Gestión de la Salud</div>
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> Gestión de Peligros y Riesgos</div>
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> Gestión de Amenazas</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="panel-block">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                  <div class="muted-small">Logo empresarial configurado.</div>
                  <button class="btn btn-sm btn-outline-primary" type="button">
                    <i class="fa-solid fa-upload me-1"></i> Actualizar logo
                  </button>
                </div>
                
                <div class="logo-box mt-3 d-flex align-items-center justify-content-center" style="min-height: 120px; border: 2px dashed #eee;">
                  <?php if (!empty($logoUrl)): ?>
                      <img src="<?= $logoUrl ?>" alt="Logo Empresa">
                  <?php else: ?>
                      <div class="logo-text text-center text-muted">TU LOGO AQUÍ</div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="panel-block">
                <h6 class="panel-title">ESTADÍSTICAS DE FASE</h6>
                <div class="chart-placeholder">
                  <div class="muted-small">Monitoreo de implementación en tiempo real.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="soporteDrawer">
  <div class="offcanvas-body p-0 position-relative">
    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="offcanvas" style="z-index: 20;"></button>
    <iframe id="soporteFrameDrawer" src="" loading="lazy"></iframe>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const hacerItems = [
    { item:'3.1.1', actividad:'Encuesta Perfil Sociodemográfico', soporte:'3.1.1.php' },
    { item:'3.1.1', actividad:'Tabulación Encuesta Perfil Sociodemográfico', soporte:'3.1.1-2.php' },
    { item:'3.1.3', actividad:'Solicitar a IPS, el Profesiograma', soporte:'3.1.3.php' },
    { item:'3.1.4', actividad:'Procedimiento de Exámenes Médicos Ocupacionales', soporte:'3.1.4.php' },
    { item:'3.1.4', actividad:'Matriz seguimiento exámenes médicos', soporte:'3.1.4-2.php' },
    { item:'3.1.5', actividad:'Solicitud carta de Guarda y custodia H.C.', soporte:'' },
    { item:'3.1.6', actividad:'Carta de recomendaciones y restricciones', soporte:'3.1.6.php' },
    { item:'3.2.1', actividad:'Procedimiento de investigación de Accidentes', soporte:'3.2.1.php' },
    { item:'3.2.1', actividad:'Investigación de accidentes', soporte:'3.2.1-2.php' },
    { item:'3.2.2', actividad:'Caracterización Accidentalidad', soporte:'3.2.2.php' },
    { item:'—', actividad:'Ausentismo', soporte:'ausentismo.php' },
    { item:'—', actividad:'Indicadores de accidentalidad', soporte:'accidentalidad.php' },
    { item:'—', actividad:'Ficha de indicadores automatizada', soporte:'indicador.php' },
    { item:'4.1.1', actividad:'Procedimiento para la identificación de peligros', soporte:'4.1.1.php' },
    { item:'4.1.2', actividad:'Matriz de identificación de peligros', soporte:'4.1.2.php' },
    { item:'4.1.2', actividad:'Identificación de factores de riesgo con trabajadores', soporte:'4.1.2-2.php' },
    { item:'4.2.1', actividad:'Programas de gestión', soporte:'4.2.1.php' },
    { item:'4.2.2', actividad:'Auto reportes de condiciones de seguridad', soporte:'4.2.2.php' },
    { item:'4.2.3', actividad:'Procedimientos, instructivos, fichas, protocolos', soporte:'4.2.3.php' },
    { item:'4.2.4', actividad:'Inspecciones instalaciones y maquinaria (COPASST)', soporte:'4.2.4.php' },
    { item:'4.2.5', actividad:'Mantenimiento periódico de instalaciones y equipos', soporte:'4.2.5.php' },
    { item:'4.2.6', actividad:'Matriz de EPP´S', soporte:'4.2.6.php' },
    { item:'4.2.6', actividad:'Entrega de Dotación y EPPS', soporte:'4.2.6-2.php' },
    { item:'5.1.1', actividad:'Anexos plan de emergencias', soporte:'5.1.1-2.php' },
  ];

  const $body = document.getElementById("hacerBody");
  const $search = document.getElementById("searchInput");
  const $reset  = document.getElementById("resetBtn");
  const $count  = document.getElementById("countBadge");
  const $scorePts = document.getElementById("scorePts");
  const $maxPts   = document.getElementById("maxPts");
  const $scorePct = document.getElementById("scorePct");
  const $scoreBar = document.getElementById("scoreBar");

  const drawerEl = document.getElementById("soporteDrawer");
  const soporteDrawer = new bootstrap.Offcanvas(drawerEl);
  const $drawerFrame = document.getElementById("soporteFrameDrawer");

  function escapeHtml(str){
    return String(str ?? "").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;");
  }

  function renderTable(list) {
    $body.innerHTML = "";
    $count.textContent = list.length;
    list.forEach((row, idx) => {
      const id = `row_${idx}`;
      const file = row.soporte || "";
      const disabled = file ? "" : "disabled";
      const title = file ? "Abrir soporte" : "Pendiente por crear";
      $body.insertAdjacentHTML("beforeend", `
        <tr>
          <td class="col-item"><span class="item-chip"><span class="dot"></span>${escapeHtml(row.item)}</span></td>
          <td>${escapeHtml(row.actividad)}</td>
          <td class="col-soporte text-center">
            <button class="btn btn-sm btn-outline-primary btn-icon soporte-btn" type="button" title="${escapeHtml(title)}" data-file="${escapeHtml(file)}" ${disabled}>
              <i class="fa-regular fa-file-lines"></i>
            </button>
          </td>
          <td class="col-cal">
            <div class="cal-wrap">
              <label class="cal-option cal-si"><input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="2"><span>SI</span></label>
              <label class="cal-option cal-proc"><input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="1"><span>PROCESO</span></label>
              <label class="cal-option cal-no"><input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="0"><span>NO</span></label>
              <label class="cal-option cal-na"><input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="na"><span>N/A</span></label>
            </div>
          </td>
        </tr>
      `);
    });
    recalcScore();
  }

  document.addEventListener("change", (e) => {
    if (e.target.classList.contains("cal-radio")) recalcScore();
  });

  document.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-file]");
    if (btn && btn.dataset.file) {
        // Ruta corregida para la subcarpeta de soportes de Hacer
        $drawerFrame.src = `./soporte-hacer/${btn.dataset.file}`;
        soporteDrawer.show();
    }
  });

  drawerEl.addEventListener("hidden.bs.offcanvas", () => { $drawerFrame.src = ""; });

  function recalcScore() {
    const rows = document.querySelectorAll("#hacerBody tr");
    let score = 0, max = 0;
    rows.forEach(tr => {
      const selected = tr.querySelector(".cal-radio:checked")?.value ?? null;
      if (selected !== null) {
        if (selected !== "na") max += 2;
        if (selected === "2") score += 2;
        else if (selected === "1") score += 1;
      }
    });
    const pct = max === 0 ? 0 : Math.round((score / max) * 100);
    $scorePts.textContent = score; $maxPts.textContent = max;
    $scorePct.textContent = pct + "%"; $scoreBar.style.width = pct + "%";
  }

  $search.addEventListener("input", () => {
    const q = $search.value.toLowerCase().trim();
    renderTable(hacerItems.filter(x => (x.item+x.actividad).toLowerCase().includes(q)));
  });

  $reset.addEventListener("click", () => { $search.value = ""; renderTable(hacerItems); });

  renderTable(hacerItems);
</script>
</body>
</html>