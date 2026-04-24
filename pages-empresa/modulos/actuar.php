<?php
session_start();
require_once '../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

// LOGO DINÁMICO
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

<title>SST Manager - Actuar</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="../../assets/css/main-style.css">
<link rel="stylesheet" href="../../assets/css/planear.css">

<style>
#soporteDrawer{ width:min(980px, 92vw); }
#soporteDrawer iframe{ width:100%; height:100%; border:0; }
.logo-box img{ max-width:100%; max-height:100px; object-fit:contain; }
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
            <i class="fa-solid fa-arrows-rotate me-2"></i> ACTUAR
          </h4>
          <div class="sheet-subtitle">Mejora continua del PESV</div>
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
            <div id="scoreBar" class="progress-bar"></div>
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
        Ítems (Actuar) <span class="badge text-bg-primary" id="countBadge">0</span>
      </div>

      <div class="d-flex gap-2">
        <input id="searchInput" class="form-control form-control-sm" placeholder="Buscar...">
        <button id="resetBtn" class="btn btn-sm btn-outline-secondary">Reset</button>
      </div>
    </div>

    <div class="excel-table">
      <table class="table table-sm table-hover mb-0">
        <thead>
          <tr>
            <th class="col-item">ÍTEM</th>
            <th>ACTIVIDAD</th>
            <th class="col-soporte text-center">SOPORTE</th>
            <th class="col-cal text-center">CALIFICACIÓN</th>
          </tr>
        </thead>
        <tbody id="body"></tbody>
      </table>
    </div>

  </div>
</div>

<div class="col-12 col-xl-6">
  <div class="card-soft p-3 bg-white h-100">

    <div class="mini-legend">
      <div class="mini-legend-row"><span class="mini-label">Sí</span><span class="mini-dot ok"></span><span class="mini-val">2</span></div>
      <div class="mini-legend-row"><span class="mini-label">Proceso</span><span class="mini-dot warn"></span><span class="mini-val">1</span></div>
      <div class="mini-legend-row"><span class="mini-label">No</span><span class="mini-dot no"></span><span class="mini-val">0</span></div>
    </div>

    <div class="logo-box mt-4 d-flex align-items-center justify-content-center" style="min-height:120px; border:2px dashed #eee;">
      <?php if ($logoUrl): ?>
        <img src="<?= $logoUrl ?>">
      <?php else: ?>
        <span class="text-muted">TU LOGO AQUÍ</span>
      <?php endif; ?>
    </div>

  </div>
</div>

</div>
</div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="soporteDrawer">
  <div class="offcanvas-body p-0 position-relative">
    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="offcanvas"></button>
    <iframe id="frame"></iframe>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

// 🔥 ITEMS ACTUAR (image_69d735.png)
const items = [
  {item:"7.1.1 →", actividad:"Procedimiento de Mejora continua, acciones preventivas y correctivas", soporte:"7.1.1.php"},
  {item:"7.1.1 →", actividad:"Acciones correctivas - oportunidades de mejora", soporte:"7.1.1-2.php"},
  {item:"7.1.3 → 7.1.4", actividad:"Matriz de seguimiento y cierre de hallazgos", soporte:"7.1.3-4.php"}
];

const body = document.getElementById("body");
const drawer = new bootstrap.Offcanvas(document.getElementById("soporteDrawer"));
const frame = document.getElementById("frame");

function render(data){
  body.innerHTML="";
  document.getElementById("countBadge").innerText=data.length;

  data.forEach((r,i)=>{
    body.innerHTML+=`
    <tr>
      <td class="col-item"><span class="item-chip"><span class="dot"></span>${r.item}</span></td>
      <td>${r.actividad}</td>

      <td class="col-soporte text-center">
        <button class="btn btn-sm btn-outline-primary btn-icon soporte-btn"
                data-file="${r.soporte}">
          <i class="fa-regular fa-file-lines"></i>
        </button>
      </td>

      <td class="col-cal">
        <div class="cal-wrap">

          <label class="cal-option cal-si">
            <input class="form-check-input cal-radio" type="radio" name="cal_${i}" value="2">
            <span>SI</span>
          </label>

          <label class="cal-option cal-proc">
            <input class="form-check-input cal-radio" type="radio" name="cal_${i}" value="1">
            <span>PROCESO</span>
          </label>

          <label class="cal-option cal-no">
            <input class="form-check-input cal-radio" type="radio" name="cal_${i}" value="0">
            <span>NO</span>
          </label>

          <label class="cal-option cal-na">
            <input class="form-check-input cal-radio" type="radio" name="cal_${i}" value="na">
            <span>N/A</span>
          </label>

        </div>
      </td>
    </tr>`;
  });

  calcular();
}

// ABRIR SOPORTE
document.addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-file]");
  if (btn && btn.dataset.file) {
    frame.src = `./soporte-actuar/${btn.dataset.file}`;
    drawer.show();
  }
});

// LIMPIAR IFRAME
document.getElementById("soporteDrawer").addEventListener("hidden.bs.offcanvas", () => {
  frame.src = "";
});

// CALCULO
document.addEventListener("change", calcular);

function calcular(){
  let score=0, max=0;

  document.querySelectorAll("#body tr").forEach(tr=>{
    const val = tr.querySelector(".cal-radio:checked")?.value;

    if(val !== undefined){
      if(val !== "na") max+=2;
      if(val==="2") score+=2;
      else if(val==="1") score+=1;
    }
  });

  let pct = max===0 ? 0 : Math.round((score/max)*100);

  document.getElementById("scorePct").innerText=pct+"%";
  document.getElementById("scorePts").innerText=score;
  document.getElementById("maxPts").innerText=max;
  document.getElementById("scoreBar").style.width=pct+"%";
}

// BUSCADOR
document.getElementById("searchInput").addEventListener("input", e=>{
  let q=e.target.value.toLowerCase();
  render(items.filter(x=>(x.item+x.actividad).toLowerCase().includes(q)));
});

document.getElementById("resetBtn").onclick=()=> {
    document.getElementById("searchInput").value = "";
    render(items);
};

// INIT
render(items);

</script>

</body>
</html>