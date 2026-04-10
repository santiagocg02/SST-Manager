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
// Ajusta el ID de este ítem según tu base de datos (Ej: 12 para "1.2.2")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 12; 

// --- Lógica de Empresa Optimizada (Logo, Nombres y Firmas) ---
$nombreEmpresaLogeada = "NOMBRE DE LA EMPRESA";
$logoEmpresaUrl = "";
$nombreRL = "";
$firmaRL = "";
$nombreSST = "";
$firmaSST = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $nombreEmpresaLogeada = $empData['nombre_empresa'] ?? 'NOMBRE DE LA EMPRESA';
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        
        $nombreRL = $empData['nombre_rl'] ?? $empData['representante_legal'] ?? $empData['nombre_representante'] ?? '';
        $firmaRL = $empData['firma_rl'] ?? $empData['firma_representante'] ?? '';
        $nombreSST = $empData['nombre_sst'] ?? $empData['responsable_sst'] ?? '';
        $firmaSST = $empData['firma_sst'] ?? '';
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

// Texto por defecto para el encargado SST
$defaultSST = !empty($nombreSST) ? $nombreSST : "Encargado de SST";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>1.2.2 | Inducción del SG SST</title>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root{
      --line:#000;
      --blue:#2f62b6;
      --blueSoft:#cfe2f7;
      --dash:#6b7280;
      --font: 12px;
    }

    *{ box-sizing:border-box; }
    body{ margin:0; background:#fff; color:#111; font-family: Arial, Helvetica, sans-serif; font-size:var(--font); }
    .sheet{
      width: 960px;
      margin: 0 auto;
      padding: 14px 14px 24px;
    }

    /* toolbar */
    .toolbar{
      display:flex; justify-content:space-between; align-items:center;
      gap:10px; margin: 0 0 10px;
      background: #d9dde2;
      padding: 10px 16px;
      border: 1px solid #c8cdd3;
    }
    .btn{
      border:1px solid #cfd6e4;
      background:#fff;
      color: #2f62b6;
      padding:7px 12px;
      border-radius:6px;
      font-weight:800;
      cursor:pointer;
      font-size:12px;
    }
    .btn:hover { background: #eef4ff; }
    .btn.primary{
      border-color:#1b4fbd;
      background:#1b4fbd;
      color:#fff;
    }
    .btn.primary:hover { background: #0f3484; }
    .btn.success {
      border: 1px solid #198754;
      background: #198754;
      color: #fff;
    }
    .btn.success:hover { background: #146c43; }

    .tiny{ font-size:10px; color:#6b7280; font-weight:800; }
    @media print{ .toolbar{ display:none !important; } .sheet{ width:auto; padding:0; } }

    /* ====== HEADER PAGE 1 ====== */
    .head{
      border:1px solid var(--line);
      display:grid;
      grid-template-columns: 150px 1fr 150px;
      align-items:stretch;
    }
    .head .logo{
      border-right:1px solid var(--line);
      display:flex; align-items:center; justify-content:center;
      padding:8px;
      min-height:60px;
    }
    .logo .ph{
      width:100%;
      height:46px;
      border:1px dashed #b9c0ce;
      display:flex; align-items:center; justify-content:center;
      color:#9aa3b2;
      font-weight:900;
      letter-spacing:.4px;
      text-transform:uppercase;
      font-size:11px;
    }
    .head .titles{
      border-right:1px solid var(--line);
      display:flex;
      flex-direction:column;
      justify-content:center;
      text-align:center;
      padding:6px 8px;
      gap:4px;
    }
    .titles .t1{
      font-weight:900;
      text-transform:uppercase;
      font-size:11px;
    }
    .titles .t2{
      font-weight:900;
      text-transform:uppercase;
      font-size:12px;
    }

    .head .meta{
      display:grid;
      grid-template-rows: 1fr 1fr 1fr;
    }
    .meta .row{
      border-left:1px solid var(--line);
      border-bottom:1px solid var(--line);
      padding:6px 8px;
      display:flex; justify-content:space-between; align-items:center;
      gap:10px;
      font-size:11px;
    }
    .meta .row:last-child{ border-bottom:none; }
    .meta .lbl{ font-weight:900; text-transform:uppercase; font-size:10px; }
    .meta input{
      border:0; outline:none;
      font-weight:900;
      font-size:11px;
      width:120px;
      text-align:right;
    }

    /* ====== TABLE PAGE 1 ====== */
    .block{ margin-top:10px; border:1px solid var(--line); border-top:none; }
    table{ width:100%; border-collapse:collapse; }
    th, td{ border:1px solid var(--line); padding:0; vertical-align:top; }

    .thead th{
      background:var(--blueSoft);
      font-weight:900;
      text-transform:uppercase;
      color:#0b3a8a;
      text-align:center;
      padding:6px 6px;
      font-size:11px;
    }

    /* columnas */
    .col-actividad{ width: 180px; }
    .col-desc{ width: 450px; }
    .col-resp{ width: 170px; }
    .col-reg{ width: 160px; }

    /* punteado horizontal solo en columnas 2-4 como en la imagen */
    .dash-row td:nth-child(2),
    .dash-row td:nth-child(3),
    .dash-row td:nth-child(4){
      border-bottom:1px dotted #111 !important;
    }
    /* la última fila NO punteada */
    .dash-row.last td:nth-child(2),
    .dash-row.last td:nth-child(3),
    .dash-row.last td:nth-child(4){
      border-bottom:1px solid #111 !important;
    }

    .cell-pad{ padding:10px 10px; }

    /* ===== ACTIVIDAD (flow por fila como el formato de la imagen) ===== */
    .act-cell{
      padding:8px 6px;
      position:relative;
      height:100%;
      min-height:92px;
    }
    .act-wrap{
      height:100%;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      gap:6px;
      position:relative;
    }
    /* línea vertical central */
    .act-wrap:before{
      content:"";
      position:absolute;
      left:50%;
      top:6px;
      bottom:6px;
      width:2px;
      background:#111;
      transform:translateX(-50%);
      z-index:0;
    }
    .oval{
      border:1px solid #111;
      border-radius:999px;
      padding:2px 10px;
      font-weight:900;
      font-size:11px;
      background:#fff;
      color:#0b3a8a;
      z-index:1;
    }
    .box{
      width:120px;
      border:1px solid var(--line);
      background:#fff;
      padding:7px 6px;
      text-align:center;
      font-size:10.5px;
      color:#0b3a8a;
      line-height:1.15;
      z-index:1;
    }
    .arrow-down{
      width:0; height:0;
      border-left:6px solid transparent;
      border-right:6px solid transparent;
      border-top:8px solid #111;
      margin: -2px 0 2px;
      z-index:1;
    }

    /* inputs/textarea estilo “editable” */
    .editable{
      width:100%;
      border:0;
      outline:none;
      font-size:12px;
      font-family: inherit;
      line-height:1.25;
      background:transparent;
    }
    textarea.editable{ resize:vertical; min-height: 62px; }
    .muted{ color:#111; }

    /* ===== PAGE 2 ===== */
    .page-break{ page-break-before:always; margin-top:14px; }

    .head2{
      border:1px solid var(--line);
      display:grid;
      grid-template-columns: 1fr 120px;
    }
    .head2 .left{
      border-right:1px solid var(--line);
      padding:6px 8px;
      text-align:center;
    }
    .head2 .left .t1{
      font-weight:900; text-transform:uppercase; font-size:11px;
    }
    .head2 .left .t2{
      margin-top:3px;
      font-weight:900; text-transform:uppercase; font-size:12px;
    }
    .head2 .right{
      display:grid;
      grid-template-rows: 1fr 1fr;
    }
    .head2 .right .r{
      padding:6px 8px;
      border-bottom:1px solid var(--line);
      display:flex; justify-content:space-between; align-items:center;
      font-size:11px;
    }
    .head2 .right .r:last-child{ border-bottom:none; }
    .lbl{ font-weight:900; text-transform:uppercase; font-size:10px; }

    .form{
      border:1px solid var(--line);
      border-top:none;
      padding:10px 12px 14px;
    }
    .line-row{
      display:grid;
      grid-template-columns: 110px 1fr;
      gap:10px;
      align-items:end;
      margin:8px 0;
    }
    .line-row.wide-label{ grid-template-columns: 260px 1fr; }
    .grid-2{ display:grid; grid-template-columns: 1fr 1fr; gap:14px; align-items:end; }
    .grid-3{ display:grid; grid-template-columns: 1fr 130px 1fr; gap:14px; align-items:end; }

    /* ===== Inputs para PAGE 2 (líneas/cuadros editables) ===== */
    .line-input{
      width:100%;
      border:0;
      outline:0;
      background:transparent;
      border-bottom:1px solid #111;
      height:16px;
      font: inherit;
      font-weight:700;
      padding:0 2px;
    }
    .bigbox-input{
      width:100%;
      border:1px solid #111;
      outline:0;
      background:transparent;
      font: inherit;
      padding:8px;
      min-height:115px;
      resize:vertical;
    }
    .cal-input{
      width:52px;
      height:22px;
      border:1px solid #111;
      outline:0;
      background:transparent;
      text-align:center;
      font-weight:900;
    }

    .qtitle{ font-weight:900; font-size:11px; margin-top:10px; text-transform:uppercase; }
    .lines-4{ display:flex; flex-direction:column; gap:8px; margin-top:8px; }

    .match{
      display:grid;
      grid-template-columns: 1fr 1.2fr;
      gap:14px;
      margin-top:8px;
      align-items:start;
    }
    .match .left .item{
      font-weight:900;
      margin:14px 0;
      font-size:11px;
    }
    .match .right .def{
      border:1px solid #111;
      padding:8px;
      margin:10px 0;
      min-height:46px;
      font-size:10.5px;
    }

    .risk{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:14px;
      margin-top:10px;
    }

    .sign{
      width: 260px;
      margin-left:auto;
      margin-top:20px;
      text-align:center;
      font-size:11px;
    }

    .people{
      width:90px; height:90px;
      margin-left:auto;
      margin-top:8px;
    }
    
    @media print {
      input, textarea, select {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        appearance: none !important;
        padding: 0 !important;
        margin: 0 !important;
        resize: none !important;
      }
      .line-input { border-bottom: 1px solid #000 !important; }
      .bigbox-input, .cal-input { border: 1px solid #000 !important; }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>
<div class="sheet">

  <div class="toolbar print-hide">
    <div style="display:flex; gap:8px;">
      <button class="btn" type="button" onclick="history.back()">← Atrás</button>
      <button class="btn" type="button" onclick="window.location.reload()">Recargar</button>
      <button class="btn success" type="button" id="btnGuardar">Guardar Cambios</button>
      <button class="btn primary" type="button" onclick="window.print()">Imprimir PDF</button>
    </div>
    <div class="tiny text-end">
      <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">INDUCCIÓN SST</span><br>
      Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
    </div>
  </div>

  <form id="form-sst-dinamico">
      <div class="head">
        <div class="logo" style="<?= empty($logoEmpresaUrl) ? '' : 'background:transparent; padding:4px;' ?>">
            <?php if(!empty($logoEmpresaUrl)): ?>
                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 50px; object-fit: contain;">
            <?php else: ?>
                <div class="ph">TU LOGO AQUÍ</div>
            <?php endif; ?>
        </div>
        <div class="titles">
          <div class="t1">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</div>
          <div class="t2">PROCEDIMIENTO DE INDUCCIÓN</div>
        </div>
        <div class="meta">
          <div class="row"><span class="lbl">0</span><span></span></div>
          <div class="row"><span class="lbl">AN-SST-04</span><span></span></div>
          <div class="row">
            <span class="lbl">XX/XX/2025</span>
            <span><input id="fecha1" name="ind_fecha_doc" type="date"></span>
          </div>
        </div>
      </div>

      <div class="block">
        <table>
          <thead class="thead">
            <tr>
              <th class="col-actividad">ACTIVIDAD</th>
              <th class="col-desc">DESCRIPCIÓN</th>
              <th class="col-resp">RESPONSABLE</th>
              <th class="col-reg">REGISTRO</th>
            </tr>
          </thead>
          <tbody>

            <tr class="dash-row">
              <td class="col-actividad act-cell">
                <div class="act-wrap">
                  <div class="oval">INICIO</div>
                  <div class="arrow-down"></div>
                  <div class="box">Preparar la<br>programación</div>
                </div>
              </td>
              <td class="col-desc">
                <div class="cell-pad">
                  <textarea name="ind_desc_1" class="editable" rows="3">Espacios que permiten la concentración y comodidad del personal, en lo posible el manejo de ayudas audiovisuales.</textarea>
                </div>
              </td>
              <td class="col-resp">
                <div class="cell-pad"><input name="ind_resp_1" class="editable" value="<?= htmlspecialchars($defaultSST) ?>"></div>
              </td>
              <td class="col-reg">
                <div class="cell-pad"><input name="ind_reg_1" class="editable" value="Presentación Inducción"></div>
              </td>
            </tr>

            <tr class="dash-row">
              <td class="col-actividad act-cell">
                <div class="act-wrap">
                  <div class="arrow-down"></div>
                  <div class="box">Enviar programación<br>a las áreas<br>responsables de<br>facilitar la inducción</div>
                </div>
              </td>
              <td class="col-desc">
                <div class="cell-pad">
                  <textarea name="ind_desc_2" class="editable" rows="3">Llamadas al personal seleccionado para ingresar a la compañía, dando a conocer fechas y hora de la inducción.</textarea>
                </div>
              </td>
              <td class="col-resp">
                <div class="cell-pad"><input name="ind_resp_2" class="editable" value="<?= htmlspecialchars($defaultSST) ?>"></div>
              </td>
              <td class="col-reg">
                <div class="cell-pad"><input name="ind_reg_2" class="editable" value="Cronograma de Inducción"></div>
              </td>
            </tr>

            <tr class="dash-row">
              <td class="col-actividad act-cell">
                <div class="act-wrap">
                  <div class="arrow-down"></div>
                  <div class="box">Realizar la<br>inducción SST</div>
                </div>
              </td>
              <td class="col-desc">
                <div class="cell-pad">
                  <textarea name="ind_desc_3" class="editable" rows="3">Designar a las áreas involucradas en la inducción, las fechas y tiempos (cronograma de inducción).</textarea>
                </div>
              </td>
              <td class="col-resp">
                <div class="cell-pad"><input name="ind_resp_3" class="editable" value="<?= htmlspecialchars($defaultSST) ?>"></div>
              </td>
              <td class="col-reg">
                <div class="cell-pad"><input name="ind_reg_3" class="editable" value="Cronograma de Inducción"></div>
              </td>
            </tr>

            <tr class="dash-row">
              <td class="col-actividad act-cell">
                <div class="act-wrap">
                  <div class="arrow-down"></div>
                  <div class="box">Evaluación de<br>Inducción SST</div>
                </div>
              </td>
              <td class="col-desc">
                <div class="cell-pad">
                  <textarea name="ind_desc_4" class="editable" rows="4">Definido y seleccionado el grupo de personas para la inducción esta se desarrollará mediante una metodología que permita impartir conocimiento con la participación y motivación de los asistentes.</textarea>
                </div>
              </td>
              <td class="col-resp">
                <div class="cell-pad"><input name="ind_resp_4" class="editable" value="<?= htmlspecialchars($defaultSST) ?>"></div>
              </td>
              <td class="col-reg">
                <div class="cell-pad"><input name="ind_reg_4" class="editable" value="Registro de Inducción"></div>
              </td>
            </tr>

            <tr class="dash-row last">
              <td class="col-actividad act-cell">
                <div class="act-wrap">
                  <div class="arrow-down"></div>
                  <div class="box">Re Inducción SST</div>
                  <div class="arrow-down"></div>
                  <div class="oval">FIN</div>
                </div>
              </td>
              <td class="col-desc">
                <div class="cell-pad">
                  <textarea name="ind_desc_5" class="editable" rows="4">Se realiza un taller preferiblemente individual a los participantes que permite la evaluación, retroalimentación y constancia de que el trabajador recibió toda la información y se verificó el aprendizaje.

Una vez el personal regrese de vacaciones, incapacidades largas (mayores a 15 días), se deberá realizar re inducción en SST.</textarea>
                </div>
              </td>
              <td class="col-resp">
                <div class="cell-pad">
                  <input name="ind_resp_5" class="editable" value="SST">
                </div>
              </td>
              <td class="col-reg">
                <div class="cell-pad">
                  <input name="ind_reg_5_1" class="editable" value="Formato de Evaluación">
                  <div style="height:8px;"></div>
                  <input name="ind_reg_5_2" class="editable" value="Registro de Inducción">
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>

      <div class="page-break"></div>

      <div class="head2">
        <div class="left">
          <div class="t1">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
          <div class="t2">REGISTRO DE EVALUACIÓN DE CAPACITACIÓN</div>
        </div>
        <div class="right">
          <div class="r"><span class="lbl">RE-SST-25</span><span></span></div>
          <div class="r">
            <span class="lbl">XX/XX/2025</span>
            <span><input id="fecha2" name="eval_fecha_doc" type="date" style="border:0; outline:0; font-weight:900; text-align:right;"></span>
          </div>
        </div>
      </div>

      <div class="form">
        <div class="line-row">
          <div class="lbl">TEMA</div>
          <input name="eval_tema" class="line-input" type="text" value="INDUCCIÓN SG-SST">
        </div>

        <div class="line-row wide-label">
          <div class="lbl">NOMBRES Y APELLIDOS DEL FACILITADOR</div>
          <input name="eval_facilitador" class="line-input" type="text" value="<?= htmlspecialchars($nombreSST) ?>">
        </div>

        <div class="grid-3">
          <div class="line-row" style="margin:0;">
            <div class="lbl">FECHA</div>
            <input name="eval_fecha_curso" class="line-input" type="date">
          </div>
          <div></div>
          <div class="line-row" style="margin:0;">
            <div class="lbl">LUGAR</div>
            <input name="eval_lugar" class="line-input" type="text">
          </div>
        </div>

        <div class="line-row wide-label">
          <div class="lbl">NOMBRES Y APELLIDOS DEL ASISTENTE</div>
          <input name="eval_asistente" class="line-input" type="text">
        </div>

        <div class="grid-2">
          <div class="line-row" style="margin:0;">
            <div class="lbl">CARGO</div>
            <input name="eval_cargo" class="line-input" type="text">
          </div>
          <div style="display:flex; justify-content:flex-end; align-items:end; gap:10px;">
            <div class="lbl" style="text-align:right;">CALIFICACIÓN</div>
            <input name="eval_calificacion" class="cal-input" type="text" maxlength="3" inputmode="numeric">
          </div>
        </div>

        <div class="people" aria-hidden="true">
          <svg viewBox="0 0 120 120" width="100%" height="100%">
            <circle cx="60" cy="32" r="12" fill="#2f62b6"/>
            <circle cx="35" cy="38" r="10" fill="#9ca3af"/>
            <circle cx="85" cy="38" r="10" fill="#9ca3af"/>
            <rect x="52" y="46" width="16" height="36" rx="8" fill="#2f62b6"/>
            <rect x="25" y="48" width="16" height="30" rx="8" fill="#9ca3af"/>
            <rect x="79" y="48" width="16" height="30" rx="8" fill="#9ca3af"/>
            <rect x="44" y="60" width="32" height="46" rx="16" fill="#2f62b6" opacity="0.55"/>
          </svg>
        </div>

        <div class="qtitle">1. MENCIONE LAS POLÍTICAS DE LA ORGANIZACIÓN</div>
        <div class="lines-4">
          <input name="eval_q1[]" class="line-input" type="text">
          <input name="eval_q1[]" class="line-input" type="text">
          <input name="eval_q1[]" class="line-input" type="text">
          <input name="eval_q1[]" class="line-input" type="text">
        </div>

        <div class="qtitle">2. DÉ UN EJEMPLO DE UN ACTO Y UNA CONDICIÓN INSEGURA</div>
        <div class="grid-2" style="margin-top:8px;">
          <textarea name="eval_q2_acto" class="bigbox-input" placeholder="ACTO INSEGURO"></textarea>
          <textarea name="eval_q2_cond" class="bigbox-input" placeholder="CONDICIÓN INSEGURA"></textarea>
        </div>

        <div class="qtitle">3. RELACIONE CON UNA LÍNEA:</div>
        <div class="match">
          <div class="left">
            <div class="item">• PELIGRO</div>
            <div class="item">• RIESGO</div>
            <div class="item">• ACCIDENTE</div>
            <div class="item">• INCIDENTE</div>
          </div>
          <div class="right">
            <div class="def">Todo suceso repentino que sobrevenga por causa u ocasión del trabajo, y que produzca en el trabajador una lesión, una perturbación funcional o la muerte.</div>
            <div class="def">Se define como cualquier fuente, situación o acto con un potencial de producir un daño en términos de una lesión o enfermedad.</div>
            <div class="def">Suceso ocurrido en el curso del trabajo o en relación con este, que tuvo el potencial de ser un accidente.</div>
            <div class="def">Es la combinación de la probabilidad y la consecuencia que ocurra un evento.</div>
          </div>
        </div>

        <div class="qtitle">4. ENUNCIE 4 RIESGOS LABORALES</div>
        <div class="risk">
          <div>
            <input name="eval_q4[]" class="line-input" type="text">
            <input name="eval_q4[]" class="line-input" type="text" style="margin-top:12px;">
          </div>
          <div>
            <input name="eval_q4[]" class="line-input" type="text">
            <input name="eval_q4[]" class="line-input" type="text" style="margin-top:12px;">
          </div>
        </div>

        <div class="qtitle">5. MENCIONE LOS TIPOS DE BRIGADA EXISTENTES EN SU EMPRESA</div>
        <div class="lines-4">
          <input name="eval_q5[]" class="line-input" type="text">
          <input name="eval_q5[]" class="line-input" type="text">
          <input name="eval_q5[]" class="line-input" type="text">
        </div>

        <div class="sign">
          <input name="eval_firma_trabajador" class="line-input" type="text">
          <div style="margin-top:6px; font-weight:900;">Firma del trabajador</div>
        </div>
      </div>
  </form>
</div>

<script>
  function setHoy(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");
    document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;
    const f1 = document.getElementById("fecha1");
    const f2 = document.getElementById("fecha2");
    if (f1 && !f1.value) f1.value = `${y}-${m}-${dd}`;
    if (f2 && !f2.value) f2.value = `${y}-${m}-${dd}`;
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
                    if (campos[i]) {
                        campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                    }
                });
            } else {
                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) {
                    campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                }
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
                text: 'Configuración guardada correctamente',
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