<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../index.php");
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SST Manager - Planear</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/main-style.css">
  <link rel="stylesheet" href="../../assets/css/planear.css">
</head>

<body>

<!-- ✅ ESTE CONTENEDOR GARANTIZA EL SCROLL -->
<div class="planear-page-scroll">

  <div class="page-wrap">
    <!-- HEADER TOP (100%) -->
    <div class="row g-3 mb-2">
      <div class="col-12">
        <div class="planear-hero card-soft">
          <div class="hero-inner d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
              <h4 class="sheet-title">
                <i class="fa-solid fa-folder-open me-2"></i> PLANEAR
              </h4>
              <div class="sheet-subtitle">Diseño y Planificación del SG - SST</div>
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

    <!-- 50 / 50 -->
    <div class="row g-3 planear-split">

      <!-- IZQUIERDA -->
      <div class="col-12 col-xl-6">
        <div class="card-soft p-3 bg-white">

          <div class="table-toolbar">
            <div class="toolbar-left">
              Ítems (Planear)
              <span class="badge text-bg-primary" id="countBadge">0</span>
            </div>

            <div class="d-flex gap-2 flex-wrap">
              <input id="searchInput" type="text" class="form-control form-control-sm searchbox"
                     placeholder="Buscar ítem o actividad...">
              <button id="resetBtn" class="btn btn-sm btn-outline-secondary btn-reset">Reset</button>
            </div>
          </div>

          <!-- ✅ tabla sin scroll interno -->
          <div class="excel-table">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0" id="planearTable">
                <thead>
                  <tr>
                    <th class="col-item">ÍTEM</th>
                    <th>ACTIVIDAD</th>
                    <th class="col-soporte text-center">SOPORTE</th>
                    <th class="col-cal text-center">CALIFICACIÓN</th>
                  </tr>
                </thead>
                <tbody id="planearBody"></tbody>
              </table>
            </div>
          </div>

          <div class="note">
            * Tabla tipo Excel (izquierda). Panel y gráficos (derecha).
          </div>

        </div>
      </div>

      <!-- DERECHA -->
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

            <div class="col-12 col-lg-7">
              <div class="panel-block">
                <h6 class="panel-title">RESOLUCIÓN 312 del 2019</h6>
                <div class="panel-list">
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> 21 Estándares</div>
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> 60 Estándares</div>
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> Criterios de Verificación</div>
                  <div class="panel-item"><i class="fa-solid fa-check text-success me-2"></i> Resultado 60 Estándares</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-5">
              <div class="panel-block">
                <h6 class="panel-title">% CUMPLIMIENTO</h6>
                <div class="cumpl-wrap">
                  <div class="cumpl-row">
                    <div class="cumpl-icon"><i class="fa-solid fa-table-cells-large"></i></div>
                    <div class="cumpl-eq">=</div>
                    <div class="cumpl-badge ok">90%</div>
                  </div>
                  <div class="cumpl-row">
                    <div class="cumpl-icon"><i class="fa-solid fa-table-cells"></i></div>
                    <div class="cumpl-eq">=</div>
                    <div class="cumpl-badge warn">67.8%</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="panel-block">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                  <div class="muted-small">Al colocar el logo en esta hoja saldrá en la mayoría de las hojas.</div>
                  <button class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-upload me-1"></i> Subir logo
                  </button>
                </div>
                <div class="logo-box mt-3">
                  <div class="logo-text">TU LOGO<br>AQUÍ</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="panel-block">
                <h6 class="panel-title">AVANCE POR CICLO</h6>
                <div class="chart-placeholder">
                  <div class="muted-small">
                    Aquí irá el gráfico radar y los indicadores.
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

    </div>

  </div>
</div>

<script>
  const planearItems = [
    { item:"1.1.1", actividad:"Perfil del Responsable del SG SST", soporte:"" },
    { item:"1.1.2", actividad:"Carta del Representante SST", soporte:"" },
    { item:"1.1.2", actividad:"Asignación de responsabilidades SST", soporte:"" },
    { item:"1.1.3", actividad:"Elaboración, Revisión y Aprobación del Presupuesto", soporte:"" },
    { item:"1.1.4", actividad:"Seguimiento Aportes Sociales", soporte:"" },
    { item:"1.1.5", actividad:"Identificar trabajos de alto riesgo decreto 2090 del 2003", soporte:"REGISTRO" },
    { item:"1.1.6", actividad:"Conformación del COPASST", soporte:"" },
    { item:"1.1.7", actividad:"Seguimiento COPASST - COCOLAB", soporte:"" },
    { item:"1.1.8", actividad:"Comité de Convivencia", soporte:"" },
    { item:"1.2.2", actividad:"Inducción del SG SST", soporte:"" },
    { item:"1.2.2", actividad:"Evaluación Inducción", soporte:"" },
    { item:"1.2.3", actividad:"Capacitación 50 Horas SG SST / 20 Horas SG SST", soporte:"REGISTRO" },
    { item:"2.1.1", actividad:"Políticas Organizacionales", soporte:"" },
    { item:"2.2.1", actividad:"Matriz de Objetivos, metas e indicadores", soporte:"" },
    { item:"2.3.1", actividad:"Evaluación Diagnostico Inicial", soporte:"" },
    { item:"2.4.1", actividad:"Plan de trabajo", soporte:"" },
    { item:"2.5.1", actividad:"Manual de control de documentos", soporte:"" },
    { item:"2.5.1", actividad:"Lista maestra de documentos", soporte:"" },
    { item:"2.6.1", actividad:"Procedimiento de rendición de cuentas", soporte:"" },
    { item:"2.6.1", actividad:"Informe de rendición de cuentas", soporte:"" },
    { item:"2.7.1", actividad:"Procedimiento de ident. de requisitos legales", soporte:"" },
    { item:"2.7.1", actividad:"Matriz de requisitos legales en SST", soporte:"" },
    { item:"2.8.1", actividad:"Procedimiento de participación, comunicación y consulta", soporte:"" },
    { item:"2.8.1", actividad:"Formato de reporte de actos y condiciones inseguras", soporte:"" },
    { item:"2.8.1", actividad:"Consolidado de reporte de actos y condiciones inseguras", soporte:"" },
    { item:"2.9.1", actividad:"Procedimiento para Compras en SST", soporte:"" },
    { item:"2.9.1", actividad:"Formato de especificaciones en compras", soporte:"" },
    { item:"2.10.1", actividad:"Lista de chequeo contratista persona natural", soporte:"" },
    { item:"2.10.1", actividad:"Lista de chequeo contratista persona jurídica", soporte:"" },
    { item:"2.11.1", actividad:"Procedimiento de Gestión al Cambio", soporte:"" },
    { item:"2.11.1", actividad:"Matriz de gestión del Cambio", soporte:"" },
    { item:"—", actividad:"Manual del SG SST", soporte:"" },
    { item:"—", actividad:"Registro de Asistencia", soporte:"" },
    { item:"—", actividad:"Acta de Reunión y seguimiento de actividades", soporte:"" },
  ];

  const $body = document.getElementById("planearBody");
  const $search = document.getElementById("searchInput");
  const $reset  = document.getElementById("resetBtn");
  const $count  = document.getElementById("countBadge");

  const $scorePts = document.getElementById("scorePts");
  const $maxPts   = document.getElementById("maxPts");
  const $scorePct = document.getElementById("scorePct");
  const $scoreBar = document.getElementById("scoreBar");

  function escapeHtml(str){
    return String(str ?? "")
      .replaceAll("&","&amp;")
      .replaceAll("<","&lt;")
      .replaceAll(">","&gt;")
      .replaceAll('"',"&quot;")
      .replaceAll("'","&#039;");
  }

  function renderTable(list) {
    $body.innerHTML = "";
    $count.textContent = list.length;

    list.forEach((row, idx) => {
      const id = `row_${idx}`;

      $body.insertAdjacentHTML("beforeend", `
        <tr>
          <td class="col-item">
            <span class="item-chip"><span class="dot"></span>${escapeHtml(row.item)}</span>
          </td>
          <td>${escapeHtml(row.actividad)}</td>

          <td class="col-soporte text-center">
            <button class="btn btn-sm btn-outline-primary btn-icon" type="button" title="Soporte">
              <i class="fa-regular fa-file-lines"></i>
            </button>
          </td>

          <td class="col-cal">
            <div class="cal-wrap">
              <label class="cal-option cal-si">
                <input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="2"><span>SI</span>
              </label>
              <label class="cal-option cal-proc">
                <input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="1"><span>PROCESO</span>
              </label>
              <label class="cal-option cal-no">
                <input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="0"><span>NO</span>
              </label>
              <label class="cal-option cal-na">
                <input class="form-check-input cal-radio" type="radio" name="cal_${id}" value="na"><span>N/A</span>
              </label>
            </div>
          </td>
        </tr>
      `);
    });

    attachEvents();
    recalcScore();
  }

  function attachEvents() {
    document.querySelectorAll(".cal-radio").forEach(r => {
      r.addEventListener("change", recalcScore);
    });
  }

  function recalcScore() {
    const rows = document.querySelectorAll("#planearBody tr");
    let score = 0;
    let max = 0;

    rows.forEach(tr => {
      const selected = tr.querySelector(".cal-radio:checked")?.value ?? null;
      if (selected === null) return;

      if (selected !== "na") max += 2;
      if (selected === "2") score += 2;
      else if (selected === "1") score += 1;
    });

    const pct = max === 0 ? 0 : Math.round((score / max) * 100);
    $scorePts.textContent = score;
    $maxPts.textContent = max;
    $scorePct.textContent = pct + "%";
    $scoreBar.style.width = pct + "%";
  }

  function filterTable() {
    const q = ($search.value || "").toLowerCase().trim();
    const filtered = planearItems.filter(x =>
      (x.item || "").toLowerCase().includes(q) ||
      (x.actividad || "").toLowerCase().includes(q)
    );
    renderTable(filtered);
  }

  $search.addEventListener("input", filterTable);
  $reset.addEventListener("click", () => {
    $search.value = "";
    renderTable(planearItems);
  });

  renderTable(planearItems);
</script>

</body>
</html>
