<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos (Ej: 23 para "2.2.1")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 23; 

// --- Lógica de Empresa Optimizada (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = null;

if (isset($resFormulario['data']['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['data']['campos'];
} elseif (isset($resFormulario['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['campos'];
} elseif (isset($resFormulario['campos'])) {
    $camposCrudos = $resFormulario['campos'];
}

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true);
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>2.2.1 - Matriz de Indicadores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  :root{
    --blue:#1f5fa8;
    --line:#111;
    --head:#dfe7f3;
    --sub:#edf3fb;
    --bg:#eef2f7;
  }

  body{
    background:var(--bg);
    font-family:Arial, Helvetica, sans-serif;
    margin:0;
  }

  .wrap{
    max-width:100%;
    margin:auto;
    padding:14px;
  }

  .toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    margin-bottom:12px;
    flex-wrap:wrap;
    background: #d9dde2;
    padding: 10px 16px;
    border: 1px solid #c8cdd3;
    border-radius: 6px;
  }
  .btn-action{
    border:1px solid #cfd6e4;
    background:#fff;
    color: #2f62b6;
    padding:6px 12px;
    border-radius:6px;
    font-weight:800;
    cursor:pointer;
    font-size:12px;
  }
  .btn-action:hover { background: #eef4ff; }
  .btn-primary-action{
    border-color:#1b4fbd;
    background:#1b4fbd;
    color:#fff;
    padding:6px 12px;
    border-radius:6px;
    font-weight:800;
    cursor:pointer;
    font-size:12px;
  }
  .btn-primary-action:hover { background: #0f3484; }
  .btn-success-action {
    border: 1px solid #198754;
    background: #198754;
    color: #fff;
    padding:6px 12px;
    border-radius:6px;
    font-weight:800;
    cursor:pointer;
    font-size:12px;
  }
  .btn-success-action:hover { background: #146c43; }
  .tiny{ font-size:11px; color:#6b7280; font-weight:700; }

  .sheet{
    background:#fff;
    border:2px solid var(--blue);
    padding:14px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
  }

  .format-top{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    margin-bottom:12px;
    font-size:11px;
  }

  .format-top td,
  .format-top th{
    border:1px solid var(--line);
    padding:4px 6px;
    text-align:center;
    vertical-align:middle;
  }

  .logo-box{
    border:2px dashed rgba(0,0,0,.25);
    height:46px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    color:rgba(0,0,0,.35);
    font-size:10px;
    padding: 2px;
  }

  .top-scroll{
    overflow-x:auto;
    overflow-y:hidden;
    margin-bottom:6px;
    height:18px;
  }

  .top-scroll-inner{
    height:1px;
  }

  .tbl-scroll{
    overflow-x:auto;
    overflow-y:auto;
    border:1px solid #cfd6df;
  }

  table.matrix{
    border-collapse:collapse;
    min-width:2450px;
    width:max-content;
    font-size:10px;
    table-layout:fixed;
  }

  .matrix th,
  .matrix td{
    border:1px solid #8d8d8d;
    padding:3px 4px;
    text-align:center;
    vertical-align:middle;
  }

  .matrix th{
    background:var(--head);
    font-weight:900;
  }

  .matrix .sub-month{
    background:#7fa2d6;
    color:#fff;
    font-size:9px;
    font-weight:700;
    padding:2px 3px;
  }

  .matrix .month{
    background:#f5f8fd;
    font-weight:900;
  }

  .left{
    text-align:left !important;
  }

  .workers-label{
    background:#fff !important;
    font-weight:700 !important;
  }

  .w-indicador{ width:180px; }
  .w-item{ width:40px; }
  .w-meta{ width:52px; }
  .w-proceso{ width:88px; }
  .w-responsable{ width:78px; }
  .w-objetivo{ width:130px; }
  .w-formula{ width:165px; }
  .w-month{ width:78px; }
  .w-periodicidad{ width:85px; }
  .w-fuente{ width:95px; }
  .w-persona{ width:100px; }
  .w-analisis{ width:150px; }

  .cell-input{
    width:100%;
    min-width:0;
    border:none;
    background:transparent;
    font-size:10px;
    text-align:center;
    outline:none;
    box-sizing:border-box;
  }

  .cell-input.left{
    text-align:left;
  }

  .cell-input::placeholder{
    color:#999;
  }

  .workers-input{
    width:100%;
    border:none;
    background:transparent;
    text-align:center;
    font-weight:700;
    outline:none;
    font-size:10px;
  }

  @media print{
    .toolbar{ display:none !important; }
    body{ background:#fff; }
    .sheet{ box-shadow:none; border:2px solid #000; }
    .top-scroll{ display:none; }
    .tbl-scroll{ overflow:visible; border:none; }
    table.matrix{ min-width: 100%; width: 100%; page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
  }
</style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="wrap">

  <div class="toolbar print-hide">
    <div style="display:flex; gap:8px;">
      <button class="btn-action" type="button" onclick="history.back()">← Atrás</button>
      <button class="btn-action" type="button" onclick="window.location.reload()">Recargar</button>
      <button class="btn-success-action" type="button" id="btnGuardar">Guardar Cambios</button>
      <button class="btn-primary-action" type="button" onclick="window.print()">Imprimir PDF</button>
    </div>
    <div class="tiny text-end">
      <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">MATRIZ INDICADORES</span><br>
      Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
    </div>
  </div>

  <form id="form-sst-dinamico">
      <div class="sheet">

        <table class="format-top">
          <colgroup>
            <col style="width:100px">
            <col>
            <col style="width:120px">
            <col style="width:90px">
          </colgroup>
          <tr>
            <td rowspan="2">
                <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 40px; object-fit: contain;">
                    <?php else: ?>
                        TU LOGO AQUÍ
                    <?php endif; ?>
                </div>
            </td>
            <td><strong>SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</strong></td>
            <td><strong>0</strong></td>
            <td rowspan="2"><strong>PLANEAR</strong></td>
          </tr>
          <tr>
            <td><strong>MATRIZ DE INDICADORES</strong></td>
            <td><strong>AN-SST-06<br><input type="date" name="meta_fecha" id="metaFecha" style="border:none; font-size:10px; font-weight:900; outline:none; background:transparent;"></strong></td>
          </tr>
        </table>

        <div class="top-scroll" id="topScroll">
          <div class="top-scroll-inner" id="topScrollInner"></div>
        </div>

        <div class="tbl-scroll" id="tableScroll">
          <table class="matrix" id="matrixTable">
            <thead>
              <tr>
                <th rowspan="3" class="w-indicador">INDICADOR</th>
                <th rowspan="3" class="w-item">ITEM</th>
                <th rowspan="3" class="w-meta">META</th>
                <th rowspan="3" class="w-proceso">PROCESO</th>
                <th rowspan="3" class="w-responsable">RESPONSABLE</th>
                <th rowspan="3" class="w-objetivo">OBJETIVO</th>
                <th rowspan="3" class="w-formula">FORMULA NUMERADOR</th>
                <th rowspan="3" class="w-formula">FORMULA DENOMINADOR</th>

                <th colspan="24"> </th>

                <th rowspan="3" class="w-periodicidad">PERIODICIDAD</th>
                <th rowspan="3" class="w-fuente">FUENTE DE LA INFORMACIÓN</th>
                <th rowspan="3" class="w-persona">PERSONA QUE DEBEN CONOCER</th>
                <th rowspan="3" class="w-analisis">ANÁLISIS Y ACCIONES DE MEJORA</th>
              </tr>

              <tr>
                <th colspan="24" class="workers-label">
                  Número de trabajadores
                </th>
              </tr>

              <tr>
                <?php for($m=1;$m<=12;$m++): ?>
                  <th colspan="2" class="w-month">
                    <input type="text" name="ind_trabajadores[]" class="workers-input" placeholder="Ej: 100">
                  </th>
                <?php endfor; ?>
              </tr>

              <tr>
                <th colspan="8" style="background:#fff;"></th>

                <th colspan="2" class="month">ENERO</th>
                <th colspan="2" class="month">FEBRERO</th>
                <th colspan="2" class="month">MARZO</th>
                <th colspan="2" class="month">ABRIL</th>
                <th colspan="2" class="month">MAYO</th>
                <th colspan="2" class="month">JUNIO</th>
                <th colspan="2" class="month">JULIO</th>
                <th colspan="2" class="month">AGOSTO</th>
                <th colspan="2" class="month">SEPTIEMBRE</th>
                <th colspan="2" class="month">OCTUBRE</th>
                <th colspan="2" class="month">NOVIEMBRE</th>
                <th colspan="2" class="month">DICIEMBRE</th>

                <th colspan="4" style="background:#fff;"></th>
              </tr>

              <tr>
                <th class="w-indicador"> </th>
                <th class="w-item"> </th>
                <th class="w-meta"> </th>
                <th class="w-proceso"> </th>
                <th class="w-responsable"> </th>
                <th class="w-objetivo"> </th>
                <th class="w-formula"> </th>
                <th class="w-formula"> </th>

                <?php for($m=1;$m<=12;$m++): ?>
                  <th class="sub-month w-month">EJECUTADO</th>
                  <th class="sub-month w-month">PROGRAMADO</th>
                <?php endfor; ?>

                <th class="w-periodicidad"> </th>
                <th class="w-fuente"> </th>
                <th class="w-persona"> </th>
                <th class="w-analisis"> </th>
              </tr>
            </thead>

            <tbody>
              <?php for($i=1;$i<=22;$i++): ?>
              <tr>
                <td><input name="ind_nombre[]" class="cell-input left" type="text"></td>
                <td><?php echo $i; ?></td>
                <td><input name="ind_meta[]" class="cell-input" type="text"></td>
                <td><input name="ind_proceso[]" class="cell-input left" type="text"></td>
                <td><input name="ind_responsable[]" class="cell-input left" type="text"></td>
                <td><input name="ind_objetivo[]" class="cell-input left" type="text"></td>
                <td><input name="ind_form_num[]" class="cell-input left" type="text"></td>
                <td><input name="ind_form_den[]" class="cell-input left" type="text"></td>

                <?php for($m=1;$m<=12;$m++): ?>
                  <td><input name="ind_ejecutado_<?= $m ?>[]" class="cell-input" type="text"></td>
                  <td><input name="ind_programado_<?= $m ?>[]" class="cell-input" type="text"></td>
                <?php endfor; ?>

                <td><input name="ind_periodicidad[]" class="cell-input left" type="text"></td>
                <td><input name="ind_fuente[]" class="cell-input left" type="text"></td>
                <td><input name="ind_persona[]" class="cell-input left" type="text"></td>
                <td><input name="ind_analisis[]" class="cell-input left" type="text"></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>

      </div>
  </form>
</div>

<script>
  // Lógica para sincronizar scrolls
  const topScroll = document.getElementById('topScroll');
  const tableScroll = document.getElementById('tableScroll');
  const topScrollInner = document.getElementById('topScrollInner');
  const matrixTable = document.getElementById('matrixTable');

  function syncTopScrollWidth() {
    topScrollInner.style.width = matrixTable.scrollWidth + 'px';
  }

  topScroll.addEventListener('scroll', function () {
    tableScroll.scrollLeft = topScroll.scrollLeft;
  });

  tableScroll.addEventListener('scroll', function () {
    topScroll.scrollLeft = tableScroll.scrollLeft;
  });

  window.addEventListener('load', syncTopScrollWidth);
  window.addEventListener('resize', syncTopScrollWidth);

  // Poner fecha de hoy por defecto
  function setHoy(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");
    document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;
    const fmeta = document.getElementById("metaFecha");
    if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;
  }
  setHoy();

  // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
  document.addEventListener('DOMContentLoaded', function () {
    let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
    if (typeof datosGuardados === 'string') {
        try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
    }

    if (datosGuardados && Object.keys(datosGuardados).length > 0) {
        for (const [key, value] of Object.entries(datosGuardados)) {
            if (Array.isArray(value)) {
                let campos = document.querySelectorAll(`[name="${key}[]"]`);
                value.forEach((val, i) => {
                    if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                });
            } else {
                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
            }
        }
    }
  });

  // --- LÓGICA DE GUARDADO ---
  document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form-sst-dinamico');
    const formData = new FormData(form);
    const datosJSON = {};

    for (const [key, value] of formData.entries()) {
        if (key.endsWith('[]')) {
            const cleanKey = key.replace('[]', '');
            if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
            datosJSON[cleanKey].push(value);
        } else {
            datosJSON[key] = value;
        }
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = 'Guardando...';
    btn.disabled = true;

    try {
        const token = "<?= $token ?>";
        const urlAPI = "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar";

        const response = await fetch(urlAPI, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({
                id_empresa: <?= $empresa ?>,
                id_item_sst: <?= $idItem ?>,
                datos: datosJSON
            })
        });

        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Matriz guardada correctamente',
                icon: 'success',
                confirmButtonColor: '#198754'
            });
        } else {
            Swal.fire({
                title: 'Error al guardar',
                text: result.error || "No se pudo completar la operación.",
                icon: 'error',
                confirmButtonColor: '#1b4fbd'
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo contactar al servidor para guardar.',
            icon: 'error',
            confirmButtonColor: '#1b4fbd'
        });
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
  });
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>