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

  <style>
    #soporteDrawer { width: min(980px, 92vw); }
    #soporteDrawer .offcanvas-body { height: calc(100vh - 64px); }
    #soporteDrawer iframe { width:100%; height:100%; border:0; background:#fff; }
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
              <h4 class="sheet-title"><i class="fa-solid fa-users-gear me-2"></i> HACER</h4>
              <div class="sheet-subtitle">Implementación y ejecución del SG SST</div>
            </div>

            <div class="score-box">
              <div class="muted-small mb-1">Cumplimiento fase</div>
              <div class="d-flex align-items-end justify-content-between">
                <div class="h5 mb-0"><span id="scorePct">0%</span></div>
                <div class="muted-small"><span id="scorePts">0</span> / <span id="maxPts">0</span> pts</div>
              </div>
              <div class="progress progress-pill mt-2"><div id="scoreBar" class="progress-bar" role="progressbar" style="width:0%"></div></div>
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

          <div class="note">
            * Botón <strong>SOPORTE</strong> listo para enlazar cada formato nuevo. Solo crea el archivo en
            <code>pages-empresa/modulos/soporte/hacer/</code> con el nombre sugerido.
          </div>
        </div>
      </div>

      <div class="col-12 col-xl-4">
        <div class="card-soft p-3 bg-white h-100">
          <div class="d-flex justify-content-end">
            <div class="mini-legend">
              <div class="mini-legend-row"><span class="mini-label">Sí</span><span class="mini-dot ok"></span><span class="mini-val">2</span></div>
              <div class="mini-legend-row"><span class="mini-label">En proceso</span><span class="mini-dot warn"></span><span class="mini-val">1</span></div>
              <div class="mini-legend-row"><span class="mini-label">No</span><span class="mini-dot no"></span><span class="mini-val">0</span></div>
            </div>
          </div>

          <div class="panel-block mt-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
              <div class="muted-small">Al colocar el logo en esta hoja saldrá en la mayoría de las hojas.</div>
              <button class="btn btn-sm btn-outline-primary" type="button"><i class="fa-solid fa-upload me-1"></i> Subir logo</button>
            </div>
            <div class="logo-box mt-3"><div class="logo-text">TU LOGO<br>AQUÍ</div></div>
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

<div class="offcanvas offcanvas-end" tabindex="-1" id="soporteDrawer" aria-labelledby="soporteDrawerLabel">
  <div class="offcanvas-header">
    <div>
      <h6 class="offcanvas-title mb-0" id="soporteDrawerLabel">SOPORTE / FORMATO (HACER)</h6>
      <div class="text-muted small" id="soporteDrawerSub">Selecciona un ítem para abrir o crear su formato.</div>
    </div>

    <div class="d-flex gap-2 align-items-center">
      <a class="btn btn-sm btn-outline-secondary" href="#" target="_blank" id="btnOpenNewTab" style="display:none;">Abrir pestaña</a>
      <button class="btn btn-sm btn-outline-primary" type="button" id="btnReloadDrawer">Recargar</button>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
  </div>
  <div class="offcanvas-body p-0">
    <iframe id="soporteFrameDrawer" src="" title="Soporte Hacer" loading="lazy"></iframe>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const hacerItems = [
  { item:'3.1.1', actividad:'Encuesta Perfil Sociodemográfico', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.1.1-encuesta-perfil.php' },
  { item:'3.1.1', actividad:'Tabulación Encuesta Perfil Sociodemográfico', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.1.1-tabulacion-perfil.php' },
  { item:'3.1.3', actividad:'Solicitar a IPS, el Profesiograma de acuerdo a los cargos de la empresa', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.1.3-profesiograma.php' },
  { item:'3.1.4', actividad:'Procedimiento de Exámenes Médicos Ocupacionales', tipoSoporte:'FORMATO', estado:'na', soporteFile:'hacer/3.1.4-procedimiento-examenes.php' },
  { item:'3.1.4', actividad:'Matriz seguimiento exámenes médicos', tipoSoporte:'FORMATO', estado:'na', soporteFile:'hacer/3.1.4-matriz-seguimiento-examenes.php' },
  { item:'3.1.5', actividad:'Solicitud carta de Guarda y custodia de las Historias clínicas a la IPS', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/3.1.5-carta-guarda-historias.php' },
  { item:'3.1.6', actividad:'Carta de las recomendaciones y restricciones de las evaluaciones médicas ocupacionales', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.1.6-carta-recomendaciones.php' },
  { item:'3.2.1', actividad:'Procedimiento de investigación de Accidentes', tipoSoporte:'FORMATO', estado:'na', soporteFile:'hacer/3.2.1-procedimiento-investigacion.php' },
  { item:'3.2.1', actividad:'Investigación de accidentes', tipoSoporte:'REGISTRO', estado:'proceso', soporteFile:'hacer/3.2.1-investigacion-accidentes.php' },
  { item:'3.2.2', actividad:'Caracterización Accidentalidad', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.2.2-caracterizacion-accidentalidad.php' },
  { item:'3.2.2', actividad:'Lección Aprendida', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.2.2-leccion-aprendida.php' },
  { item:'3.2.3', actividad:'Ausentismo', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/3.2.3-ausentismo.php' },
  { item:'3.3.6', actividad:'Indicadores de accidentalidad', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.3.6-indicadores-accidentalidad.php' },
  { item:'3.3.6', actividad:'Ficha de indicadores automatizada', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/3.3.6-ficha-indicadores.php' },
  { item:'4.1.1', actividad:'Procedimiento para la identificación de peligros', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.1.1-procedimiento-identificacion-peligros.php' },
  { item:'4.1.2', actividad:'Matriz de identificación de peligros', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.1.2-matriz-identificacion-peligros.php' },
  { item:'4.1.2', actividad:'Identificación de factores de riesgo en conjunto con trabajadores', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.1.2-identificacion-factores-riesgo.php' },
  { item:'4.1.3', actividad:'Identificación de sustancias catalogadas como carcinógenas o toxicidad aguda', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/4.1.3-sustancias-carcinogenas.php' },
  { item:'4.1.4', actividad:'Mediciones ambientales', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/4.1.4-mediciones-ambientales.php' },
  { item:'4.2.1', actividad:'Aplicación de Baterías para el Riesgo psicosocial', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/4.2.1-baterias-riesgo-psicosocial.php' },
  { item:'4.2.1', actividad:'Inspecciones Ergonómicas a Puestos de Trabajo', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/4.2.1-inspecciones-ergonomicas.php' },
  { item:'4.2.1', actividad:'Programas de gestión', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.2.1-programas-gestion.php' },
  { item:'4.2.2', actividad:'Auto reportes de condiciones de seguridad y salud', tipoSoporte:'REGISTRO', estado:'proceso', soporteFile:'hacer/4.2.2-auto-reportes.php' },
  { item:'4.2.3', actividad:'Procedimientos, instructivos, incs, protocolos / Verificación de hojas de seguridad Sustancias químicas', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.2.3-verificacion-hojas-seguridad.php' },
  { item:'4.2.3', actividad:'Informe de señalización', tipoSoporte:'ANEXO', estado:'na', soporteFile:'hacer/4.2.3-informe-senalizacion.php' },
  { item:'4.2.4', actividad:'Realización de inspecciones a las instalaciones, maquinaria o equipos con la participación del COPASST', tipoSoporte:'ANEXO', estado:'na', soporteFile:'hacer/4.2.4-inspecciones-copasst.php' },
  { item:'4.2.5', actividad:'Mantenimiento periódico de instalaciones, equipos, máquinas, herramientas.', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.2.5-mantenimiento-periodico.php' },
  { item:'4.2.6', actividad:'Matriz de EPP\'S', tipoSoporte:'FORMATO', estado:'si', soporteFile:'hacer/4.2.6-matriz-epps.php' },
  { item:'4.2.6', actividad:'Entrega de Dotación y EPPS', tipoSoporte:'REGISTRO', estado:'si', soporteFile:'hacer/4.2.6-entrega-dotacion-epps.php' },
  { item:'5.1.1', actividad:'Plan Operativo de Emergencias (vulnerabilidad, amenazas) en todos los centros de trabajo de la entidad', tipoSoporte:'ANEXO', estado:'si', soporteFile:'hacer/5.1.1-plan-operativo-emergencias.php' },
  { item:'5.1.1', actividad:'Análisis de vulnerabilidad', tipoSoporte:'ANEXO', estado:'si', soporteFile:'hacer/5.1.1-analisis-vulnerabilidad.php' },
  { item:'5.1.1', actividad:'Anexos plan de emergencias', tipoSoporte:'ANEXO', estado:'si', soporteFile:'hacer/5.1.1-anexos-plan-emergencias.php' }
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

const drawerEl = document.getElementById('soporteDrawer');
const soporteDrawer = new bootstrap.Offcanvas(drawerEl);
const $drawerFrame = document.getElementById('soporteFrameDrawer');
const $drawerSub = document.getElementById('soporteDrawerSub');
const $btnReloadD = document.getElementById('btnReloadDrawer');
const $btnNewTab = document.getElementById('btnOpenNewTab');

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

function renderSupportButton(row) {
  const file = row.soporteFile || '';
  const type = row.tipoSoporte || 'FORMATO';
  return `
    <button
      class="btn btn-sm btn-outline-primary btn-icon soporte-btn"
      type="button"
      title="Abrir / enlazar soporte"
      data-file="${escapeHtml(file)}"
      data-item="${escapeHtml(row.item)}"
      data-actividad="${escapeHtml(row.actividad)}"
    >
      <i class="fa-solid fa-link"></i>
      <span class="ms-1">${escapeHtml(type)}</span>
    </button>
  `;
}

function renderTable(list){
  $body.innerHTML = '';
  $count.textContent = list.length;

  list.forEach((row, idx) => {
    const name = `cal_hacer_${idx}`;
    $body.insertAdjacentHTML('beforeend', `
      <tr>
        <td class="col-item"><span class="item-chip"><span class="dot"></span>${escapeHtml(row.item)}</span></td>
        <td>${escapeHtml(row.actividad)}</td>
        <td class="col-soporte text-center">${renderSupportButton(row)}</td>
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

function openSoporteDrawer(file, row){
  const url = `./soporte/${file}`;
  $drawerFrame.src = url;
  $drawerSub.textContent = `${row.item} · ${row.actividad} · Archivo: ${file}`;
  $btnNewTab.style.display = 'inline-flex';
  $btnNewTab.href = url;
  soporteDrawer.show();
}

$btnReloadD.addEventListener('click', () => {
  if ($drawerFrame.src) $drawerFrame.src = $drawerFrame.src;
});

drawerEl.addEventListener('hidden.bs.offcanvas', () => {
  $drawerFrame.src = '';
  $drawerSub.textContent = 'Selecciona un ítem para abrir o crear su formato.';
  $btnNewTab.style.display = 'none';
  $btnNewTab.href = '#';
});

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

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.soporte-btn');
  if (!btn) return;

  const file = btn.dataset.file || '';
  const item = btn.dataset.item || '';
  const actividad = btn.dataset.actividad || '';
  openSoporteDrawer(file, { item, actividad });
});

renderTable(hacerItems);
</script>

</body>
</html>
