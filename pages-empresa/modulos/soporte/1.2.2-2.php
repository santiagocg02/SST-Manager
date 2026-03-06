<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>1.2.2 | Inducción del SG SST</title>

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
    }
    .btn{
      border:1px solid #cfd6e4;
      background:#fff;
      padding:7px 10px;
      border-radius:10px;
      font-weight:800;
      cursor:pointer;
      font-size:12px;
    }
    .btn.primary{
      border-color:#bcd2ff;
      background:#eef4ff;
      color:#1241a6;
    }
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
  </style>
</head>

<body>
<div class="sheet">

  <!-- toolbar -->
  <div class="toolbar">
    <div style="display:flex; gap:8px;">
      <button class="btn" type="button" onclick="history.back()">← Atrás</button>
      <button class="btn primary" type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
    <div class="tiny">Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span></div>
  </div>

  <!-- ======= PAGE 1 HEADER ======= -->
  <div class="head">
    <div class="logo">
      <div class="ph">TU LOGO AQUÍ</div>
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
        <span><input id="fecha1" type="date"></span>
      </div>
    </div>
  </div>

  <!-- ======= PAGE 1 TABLE (REORGANIZADA COMO TU IMAGEN) ======= -->
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

        <!-- FILA 1 -->
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
              <textarea class="editable" rows="3">Espacios que permiten la concentración y comodidad del personal, en lo posible el manejo de ayudas audiovisuales.</textarea>
            </div>
          </td>
          <td class="col-resp">
            <div class="cell-pad"><input class="editable" value="Encargado de SST"></div>
          </td>
          <td class="col-reg">
            <div class="cell-pad"><input class="editable" value="Presentación Inducción"></div>
          </td>
        </tr>

        <!-- FILA 2 -->
        <tr class="dash-row">
          <td class="col-actividad act-cell">
            <div class="act-wrap">
              <div class="arrow-down"></div>
              <div class="box">Enviar programación<br>a las áreas<br>responsables de<br>facilitar la inducción</div>
            </div>
          </td>
          <td class="col-desc">
            <div class="cell-pad">
              <textarea class="editable" rows="3">Llamadas al personal seleccionado para ingresar a la compañía, dando a conocer fechas y hora de la inducción.</textarea>
            </div>
          </td>
          <td class="col-resp">
            <div class="cell-pad"><input class="editable" value="Encargado de SST"></div>
          </td>
          <td class="col-reg">
            <div class="cell-pad"><input class="editable" value="Cronograma de Inducción"></div>
          </td>
        </tr>

        <!-- FILA 3 -->
        <tr class="dash-row">
          <td class="col-actividad act-cell">
            <div class="act-wrap">
              <div class="arrow-down"></div>
              <div class="box">Realizar la<br>inducción SST</div>
            </div>
          </td>
          <td class="col-desc">
            <div class="cell-pad">
              <textarea class="editable" rows="3">Designar a las áreas involucradas en la inducción, las fechas y tiempos (cronograma de inducción).</textarea>
            </div>
          </td>
          <td class="col-resp">
            <div class="cell-pad"><input class="editable" value="Encargado de SST"></div>
          </td>
          <td class="col-reg">
            <div class="cell-pad"><input class="editable" value="Cronograma de Inducción"></div>
          </td>
        </tr>

        <!-- FILA 4 -->
        <tr class="dash-row">
          <td class="col-actividad act-cell">
            <div class="act-wrap">
              <div class="arrow-down"></div>
              <div class="box">Evaluación de<br>Inducción SST</div>
            </div>
          </td>
          <td class="col-desc">
            <div class="cell-pad">
              <textarea class="editable" rows="4">Definido y seleccionado el grupo de personas para la inducción esta se desarrollará mediante una metodología que permita impartir conocimiento con la participación y motivación de los asistentes.</textarea>
            </div>
          </td>
          <td class="col-resp">
            <div class="cell-pad"><input class="editable" value="Encargado de SST"></div>
          </td>
          <td class="col-reg">
            <div class="cell-pad"><input class="editable" value="Registro de Inducción"></div>
          </td>
        </tr>

        <!-- FILA 5 (ÚLTIMA, con FIN como en la imagen) -->
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
              <textarea class="editable" rows="4">Se realiza un taller preferiblemente individual a los participantes que permite la evaluación, retroalimentación y constancia de que el trabajador recibió toda la información y se verificó el aprendizaje.

Una vez el personal regrese de vacaciones, incapacidades largas (mayores a 15 días), se deberá realizar re inducción en SST.</textarea>
            </div>
          </td>
          <td class="col-resp">
            <div class="cell-pad">
              <input class="editable" value="SST">
            </div>
          </td>
          <td class="col-reg">
            <div class="cell-pad">
              <input class="editable" value="Formato de Evaluación">
              <div style="height:8px;"></div>
              <input class="editable" value="Registro de Inducción">
            </div>
          </td>
        </tr>

      </tbody>
    </table>
  </div>

  <!-- ===================== PAGE 2 ===================== -->
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
        <span><input id="fecha2" type="date" style="border:0; outline:0; font-weight:900; text-align:right;"></span>
      </div>
    </div>
  </div>

  <div class="form">
    <div class="line-row">
      <div class="lbl">TEMA</div>
      <input class="line-input" type="text">
    </div>

    <div class="line-row wide-label">
      <div class="lbl">NOMBRES Y APELLIDOS DEL FACILITADOR</div>
      <input class="line-input" type="text">
    </div>

    <div class="grid-3">
      <div class="line-row" style="margin:0;">
        <div class="lbl">FECHA</div>
        <input class="line-input" type="date">
      </div>
      <div></div>
      <div class="line-row" style="margin:0;">
        <div class="lbl">LUGAR</div>
        <input class="line-input" type="text">
      </div>
    </div>

    <div class="line-row wide-label">
      <div class="lbl">NOMBRES Y APELLIDOS DEL ASISTENTE</div>
      <input class="line-input" type="text">
    </div>

    <div class="grid-2">
      <div class="line-row" style="margin:0;">
        <div class="lbl">CARGO</div>
        <input class="line-input" type="text">
      </div>
      <div style="display:flex; justify-content:flex-end; align-items:end; gap:10px;">
        <div class="lbl" style="text-align:right;">CALIFICACIÓN</div>
        <input class="cal-input" type="text" maxlength="3" inputmode="numeric">
      </div>
    </div>

    <!-- icon -->
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
      <input class="line-input" type="text">
      <input class="line-input" type="text">
      <input class="line-input" type="text">
      <input class="line-input" type="text">
    </div>

    <div class="qtitle">2. DÉ UN EJEMPLO DE UN ACTO Y UNA CONDICIÓN INSEGURA</div>
    <div class="grid-2" style="margin-top:8px;">
      <textarea class="bigbox-input" placeholder="ACTO INSEGURO"></textarea>
      <textarea class="bigbox-input" placeholder="CONDICIÓN INSEGURA"></textarea>
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
        <input class="line-input" type="text">
        <input class="line-input" type="text" style="margin-top:12px;">
      </div>
      <div>
        <input class="line-input" type="text">
        <input class="line-input" type="text" style="margin-top:12px;">
      </div>
    </div>

    <div class="qtitle">5. MENCIONE LOS TIPOS DE BRIGADA EXISTENTES EN SU EMPRESA</div>
    <div class="lines-4">
      <input class="line-input" type="text">
      <input class="line-input" type="text">
      <input class="line-input" type="text">
    </div>

    <div class="sign">
      <input class="line-input" type="text">
      <div style="margin-top:6px; font-weight:900;">Firma del trabajador</div>
    </div>
  </div>

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
</script>
</body>
</html>