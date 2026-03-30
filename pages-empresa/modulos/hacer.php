<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
  header('Location: ../../index.php');
  exit;
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
              <div class="sheet-subtitle">Implementación y ejecución del SG SST</div>
            </div>

            <div class="score-box">
              <div class="muted-small mb-1">Cumplimiento fase</div>
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
      <div class="col-12 col-xl-8">
        <div class="card-soft p-3 bg-white">

          <div class="table-toolbar">
            <div class="toolbar-left">
              Ítems (Hacer)
              <span class="badge text-bg-primary" id="countBadge">0</span>
            </div>

            <div class="d-flex gap-2 flex-wrap">
              <input id="searchInput" type="text" class="form-control form-control-sm searchbox"
                     placeholder="Buscar ítem o actividad...">
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

        </div>
      </div>

      <div class="col-12 col-xl-4">
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

          <div class="panel-block mt-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
              <div class="muted-small">Al colocar el logo en esta hoja saldrá en la mayoría de las hojas.</div>
              <button class="btn btn-sm btn-outline-primary" type="button">
                <i class="fa-solid fa-upload me-1"></i> Subir logo
              </button>
            </div>
            <div class="logo-box mt-3">
              <div class="logo-text">TU LOGO<br>AQUÍ</div>
            </div>
          </div>

          <div class="panel-block mt-3">
            <h6 class="panel-title">Resumen fase HACER</h6>
            <div class="d-flex justify-content-between"><span>Total actividades</span><strong id="totActividades">0</strong></div>
            <div class="d-flex justify-content-between"><span>Calificadas</span><strong id="totCalificadas">0</strong></div>
            <div class="d-flex justify-content-between"><span>Cumplimiento</span><strong id="totCumplimiento">0%</strong></div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const hacerItems = [
  { item:'3.1.1', actividad:'Encuesta Perfil Sociodemográfico', soporte:'', estado:'si' },
  { item:'3.1.1', actividad:'Tabulación Encuesta Perfil Sociodemográfico', soporte:'', estado:'si' },
  { item:'3.1.3', actividad:'Solicitar a IPS, el Profesiograma de acuerdo a los cargos de la empresa', soporte:'', estado:'si' },
  { item:'3.1.4', actividad:'Procedimiento de Exámenes Médicos Ocupacionales', soporte:'', estado:'na' },
  { item:'3.1.4', actividad:'Matriz seguimiento exámenes médicos', soporte:'', estado:'na' },
  { item:'3.1.5', actividad:'Solicitud carta de Guarda y custodia de las Historias clínicas a la IPS', soporte:'REGISTRO', estado:'si' },
  { item:'3.1.6', actividad:'Carta de las recomendaciones y restricciones de las evaluaciones médicas ocupacionales', soporte:'', estado:'si' },
  { item:'3.2.1', actividad:'Procedimiento de investigación de Accidentes', soporte:'', estado:'na' },
  { item:'3.2.1', actividad:'Investigación de accidentes', soporte:'', estado:'proceso' },
  { item:'3.2.2', actividad:'Caracterización Accidentalidad', soporte:'', estado:'si' },
  { item:'3.2.2', actividad:'Lección Aprendida', soporte:'', estado:'si' },
  { item:'3.2.3', actividad:'Ausentismo', soporte:'', estado:'si' },
  { item:'3.3.6', actividad:'Indicadores de accidentalidad', soporte:'', estado:'si' },
  { item:'3.3.6', actividad:'Ficha de indicadores automatizada', soporte:'', estado:'si' },
  { item:'4.1.1', actividad:'Procedimiento para la identificación de peligros', soporte:'', estado:'si' },
  { item:'4.1.2', actividad:'Matriz de identificación de peligros', soporte:'', estado:'si' },
  { item:'4.1.2', actividad:'Identificación de factores de riesgo en conjunto con trabajadores', soporte:'', estado:'si' },
  { item:'4.1.3', actividad:'Identificación de sustancias catalogadas como carcinógenas o toxicidad aguda', soporte:'REGISTRO', estado:'si' },
  { item:'4.1.4', actividad:'Mediciones ambientales', soporte:'REGISTRO', estado:'si' },
  { item:'4.2.1', actividad:'Aplicación de Baterías para el Riesgo psicosocial', soporte:'REGISTRO', estado:'si' },
  { item:'4.2.1', actividad:'Inspecciones Ergonómicas a Puestos de Trabajo', soporte:'REGISTRO', estado:'si' },
  { item:'4.2.1', actividad:'Programas de gestión', soporte:'', estado:'si' },
  { item:'4.2.2', actividad:'Auto reportes de condiciones de seguridad y salud', soporte:'', estado:'proceso' },
  { item:'4.2.3', actividad:'Procedimientos, instructivos, incs, protocolos / Verificación de hojas de seguridad Sustancias químicas', soporte:'', estado:'si' },
  { item:'4.2.3', actividad:'Informe de señalización', soporte:'ANEXO', estado:'na' },
  { item:'4.2.4', actividad:'Realización de inspecciones a las instalaciones, maquinaria o equipos con la participación del COPASST', soporte:'', estado:'na' },
  { item:'4.2.5', actividad:'Mantenimiento periódico de instalaciones, equipos, máquinas, herramientas.', soporte:'', estado:'si' },
  { item:'4.2.6', actividad:'Matriz de EPP\'S', soporte:'', estado:'si' },
  { item:'4.2.6', actividad:'Entrega de Dotación y EPPS', soporte:'', estado:'si' },
  { item:'5.1.1', actividad:'Plan Operativo de Emergencias (vulnerabilidad, amenazas) en todos los centros de trabajo de la entidad', soporte:'ANEXO', estado:'si' },
  { item:'5.1.1', actividad:'Análisis de vulnerabilidad', soporte:'ANEXO', estado:'si' },
  { item:'5.1.1', actividad:'Anexos plan de emergencias', soporte:'ANEXO', estado:'si' }
];

const $body = document.getElementById('hacerBody');
const $search = document.getElementById('searchInput');
const $reset = document.getElementById('resetBtn');
const $count = document.getElementById('countBadge');
const $scorePts = document.getElementById('scorePts');
const $maxPts = document.getElementById('maxPts');
const $scorePct = document.getElementById('scorePct');
const $scoreBar = document.getElementById('scoreBar');
const $totActividades = document.getElementById('totActividades');
const $totCalificadas = document.getElementById('totCalificadas');
const $totCumplimiento = document.getElementById('totCumplimiento');

function escapeHtml(str){
  return String(str ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');
}

function optionCell(name, value, checked){
  return `<label class="cal-option cal-${value === 'proceso' ? 'proc' : value === 'si' ? 'si' : value === 'no' ? 'no' : 'na'}">
    <input class="form-check-input cal-radio" type="radio" name="${name}" value="${value}" ${checked ? 'checked' : ''}>
    <span>${value === 'si' ? 'SI' : value === 'proceso' ? 'PROCESO' : value === 'no' ? 'NO' : 'N/A'}</span>
  </label>`;
}

function renderTable(list){
  $body.innerHTML = '';
  $count.textContent = list.length;

  list.forEach((row, idx) => {
    const name = `cal_hacer_${idx}`;
    const badge = row.soporte ? `<span class="badge text-bg-light border">${escapeHtml(row.soporte)}</span>` : '<i class="fa-regular fa-file-lines text-primary"></i>';

    $body.insertAdjacentHTML('beforeend', `
      <tr>
        <td class="col-item"><span class="item-chip"><span class="dot"></span>${escapeHtml(row.item)}</span></td>
        <td>${escapeHtml(row.actividad)}</td>
        <td class="col-soporte text-center">${badge}</td>
        <td class="col-cal">
          <div class="cal-wrap">
            ${optionCell(name, 'si', row.estado === 'si')}
            ${optionCell(name, 'proceso', row.estado === 'proceso')}
            ${optionCell(name, 'no', row.estado === 'no')}
            ${optionCell(name, 'na', row.estado === 'na')}
          </div>
        </td>
      </tr>
    `);
  });

  recalc();
}

function recalc(){
  const rows = document.querySelectorAll('#hacerBody tr');
  let score = 0;
  let max = 0;
  let cal = 0;

  rows.forEach(tr => {
    const selected = tr.querySelector('.cal-radio:checked')?.value ?? null;
    if (!selected) return;

    cal += 1;
    if (selected !== 'na') {
      max += 2;
      if (selected === 'si') score += 2;
      else if (selected === 'proceso') score += 1;
    }
  });

  const pct = max === 0 ? 0 : Math.round((score / max) * 100);
  $scorePts.textContent = score;
  $maxPts.textContent = max;
  $scorePct.textContent = pct + '%';
  $scoreBar.style.width = pct + '%';

  $totActividades.textContent = rows.length;
  $totCalificadas.textContent = cal;
  $totCumplimiento.textContent = pct + '%';
}

function filterTable(){
  const q = ($search.value || '').toLowerCase().trim();
  const filtered = hacerItems.filter(x =>
    (x.item || '').toLowerCase().includes(q) ||
    (x.actividad || '').toLowerCase().includes(q)
  );
  renderTable(filtered);
}

$search.addEventListener('input', filterTable);
$reset.addEventListener('click', () => {
  $search.value = '';
  renderTable(hacerItems);
});

document.addEventListener('change', (e) => {
  if (e.target.classList.contains('cal-radio')) recalc();
});

renderTable(hacerItems);
</script>

</body>
</html>
