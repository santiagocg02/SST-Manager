<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

// Helpers
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AN-SST-26 | Seguimiento mensual pago de aportes y parafiscales</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#2f62b6;
      --blue2:#1f4f9a;
      --soft:#f6f8fb;
      --muted:#6b7280;
    }
    body{
      background:#fff;
      color:#111;
      font-size: 12px;
    }
    .sheet{
      max-width: 1600px;
      margin: 0 auto;
      padding: 12px 14px 18px;
    }

    /* Toolbar */
    .toolbar{
      display:flex;
      gap:10px;
      align-items:center;
      justify-content:space-between;
      margin-bottom:10px;
    }
    .toolbar .left{
      display:flex; gap:8px; align-items:center;
    }
    .btn-lite{
      border:1px solid #d7dbe3;
      background:#fff;
      padding:6px 10px;
      border-radius:10px;
      font-weight:600;
      font-size:12px;
    }
    .btn-primary-lite{
      border:1px solid #bcd2ff;
      background:#eef4ff;
      color:#1241a6;
      padding:6px 10px;
      border-radius:10px;
      font-weight:700;
      font-size:12px;
    }

    /* Header format */
    .format-head{
      border:1px solid var(--line);
      border-bottom:none;
    }
    .format-head .grid{
      display:grid;
      grid-template-columns: 190px 1fr 220px;
      align-items:stretch;
    }
    .logo-box{
      border-right:1px solid var(--line);
      padding:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:82px;
      background:#fff;
    }
    .logo-box .logo-placeholder{
      width:100%;
      height:58px;
      border:1px dashed #b9c0ce;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#9aa3b2;
      font-weight:700;
      letter-spacing:.5px;
    }
    .title-box{
      padding:10px 12px;
      border-right:1px solid var(--line);
      text-align:center;
    }
    .title-box .top{
      font-weight:800;
      font-size:11px;
      text-transform:uppercase;
    }
    .title-box .mid{
      margin-top:4px;
      font-weight:900;
      font-size:12px;
      text-transform:uppercase;
    }
    .meta-box{
      display:grid;
      grid-template-columns: 1fr 1fr;
      grid-auto-rows:minmax(28px, auto);
      border-left:none;
    }
    .meta-box div{
      border-left:1px solid var(--line);
      border-bottom:1px solid var(--line);
      padding:6px 8px;
      display:flex;
      justify-content:space-between;
      gap:10px;
      align-items:center;
      font-size:11px;
    }
    .meta-box div:nth-child(1),
    .meta-box div:nth-child(2){
      border-top:none;
    }
    .meta-box .lbl{
      color:#111;
      font-weight:800;
      text-transform:uppercase;
      font-size:10px;
    }
    .meta-box .val{
      font-weight:800;
    }

    .format-sub{
      border:1px solid var(--line);
      border-top:none;
      padding:8px 10px;
      background:#fff;
    }

    /* Info row */
    .info-row{
      display:grid;
      grid-template-columns: 320px 1fr 320px;
      gap:10px;
      margin:10px 0 10px;
      align-items:start;
    }
    .info-card{
      border:1px solid #d7dbe3;
      border-radius:12px;
      padding:10px;
      background:#fff;
    }
    .info-card .small{
      font-size:11px;
      color:var(--muted);
      font-weight:700;
      margin-bottom:6px;
    }
    .info-list{
      display:grid;
      grid-template-columns: 1fr auto;
      gap:6px 10px;
      font-size:11px;
    }
    .info-list .k{ color:#111; font-weight:700; }
    .info-list .v{ font-weight:800; }
    .period{
      display:flex;
      gap:10px;
      align-items:center;
      justify-content:center;
      height:100%;
      border:1px solid #d7dbe3;
      border-radius:12px;
      background:var(--soft);
      padding:10px;
    }
    .period .pill{
      background:#fff;
      border:1px solid #d7dbe3;
      border-radius:10px;
      padding:8px 10px;
      min-width: 260px;
      text-align:center;
    }
    .period label{
      font-size:10px;
      font-weight:900;
      color:#111;
      text-transform:uppercase;
      display:block;
      margin-bottom:6px;
    }
    .period input{
      width:100%;
      border:1px solid #d7dbe3;
      border-radius:10px;
      padding:8px 10px;
      font-weight:800;
      font-size:12px;
      background:#fff;
    }

    /* Table */
    .table-wrap{
      border:1px solid #cfd6e4;
      border-radius:14px;
      overflow:auto;
      background:#fff;
    }
    table{
      border-collapse:separate;
      border-spacing:0;
      width:1800px; /* grande tipo Excel */
      font-size:11px;
    }
    thead th{
      position:sticky;
      top:0;
      z-index:2;
      background:var(--blue);
      color:#fff;
      text-align:center;
      font-weight:900;
      padding:8px 6px;
      border-right:1px solid rgba(255,255,255,.25);
      border-bottom:1px solid #1a3f7a;
      white-space:nowrap;
    }
    thead .group{
      background:var(--blue2);
      font-size:10px;
      letter-spacing:.3px;
      text-transform:uppercase;
    }
    tbody td{
      border-right:1px solid #e8edf6;
      border-bottom:1px solid #eef2f8;
      padding:6px 6px;
      vertical-align:middle;
      background:#fff;
    }
    tbody tr:nth-child(even) td{ background:#fbfcff; }
    tbody td:first-child{ position:sticky; left:0; z-index:1; background:inherit; }
    thead th:first-child{ position:sticky; left:0; z-index:3; }

    .cell-input{
      width:100%;
      border:1px solid #d7dbe3;
      border-radius:8px;
      padding:6px 8px;
      font-size:11px;
      font-weight:700;
      background:#fff;
    }
    .cell-input.money{ text-align:right; }
    .tiny{
      font-size:10px;
      color:var(--muted);
      font-weight:700;
    }

    /* Totals row */
    tfoot td{
      position:sticky;
      bottom:0;
      z-index:2;
      background:#f3f6ff;
      border-top:1px solid #cfd6e4;
      font-weight:900;
      padding:8px 6px;
    }
    tfoot td:first-child{
      position:sticky;
      left:0;
      z-index:3;
      background:#edf2ff;
    }

    @media print{
      .toolbar{ display:none !important; }
      .sheet{ max-width:none; padding:0; }
      .table-wrap{ border:none; border-radius:0; }
      table{ width:100%; }
    }
  </style>
</head>

<body>
<div class="sheet">

  <!-- Toolbar -->
  <div class="toolbar">
    <div class="left">
      <button class="btn-lite" type="button" onclick="goBack()">
        ← Atrás
      </button>
      <button class="btn-primary-lite" type="button" onclick="window.print()">
        Imprimir / Guardar PDF
      </button>
    </div>
    <div class="tiny">
      Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong>
    </div>
  </div>

  <!-- Header formato -->
  <div class="format-head">
    <div class="grid">
      <div class="logo-box">
        <div class="logo-placeholder">TU LOGO AQUÍ</div>
      </div>

      <div class="title-box">
        <div class="top">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
        <div class="mid">SEGUIMIENTO MENSUAL DE PAGO DE APORTES Y PARAFISCALES</div>
      </div>

      <div class="meta-box">
        <div><span class="lbl">Código</span><span class="val">AN-SST-26</span></div>
        <div><span class="lbl">Proceso</span><span class="val">PLANEAR</span></div>
        <div><span class="lbl">Versión</span><span class="val">0</span></div>
        <div><span class="lbl">Fecha</span><span class="val"><span id="fechaHoy">XXXX/XX/XX</span></span></div>
      </div>
    </div>
  </div>
  <div class="format-sub">
    <span class="tiny"><strong>Nota:</strong> Diligencie la información por trabajador y marque el seguimiento mensual al final.</span>
  </div>

  <!-- Info -->
  <div class="info-row">
    <div class="info-card">
      <div class="small">Parámetros</div>
      <div class="info-list">
        <div class="k">Salario mínimo legal vigente</div>
        <div class="v"><input class="cell-input money" id="smmlv" value="1300000"></div>

        <div class="k">Subsidio de transporte</div>
        <div class="v"><input class="cell-input money" id="subTrans" value="162000"></div>

        <div class="k">¿Aplica Ley 1607? (Exoneración)</div>
        <div class="v">
          <select class="cell-input" id="ley1607">
            <option value="NO" selected>NO</option>
            <option value="SI">SI</option>
          </select>
        </div>
      </div>
    </div>

    <div class="period">
      <div class="pill">
        <label>Periodo de pago</label>
        <div class="d-flex gap-2">
          <input type="date" id="periodoIni">
          <input type="date" id="periodoFin">
        </div>
      </div>
    </div>

    <div class="info-card">
      <div class="small">Instrucciones rápidas</div>
      <ul class="mb-0 ps-3 tiny">
        <li>Digita ingresos y deducciones por trabajador.</li>
        <li>Marca en “Seguimiento” los meses pagados (X).</li>
        <li>Usa “Imprimir / Guardar PDF” para archivar.</li>
      </ul>
    </div>
  </div>

  <!-- Tabla -->
  <div class="table-wrap">
    <table id="tabla">
      <thead>
        <tr>
          <th rowspan="2">Trabajador (Nombre)</th>

          <th class="group" colspan="6">Ingresos</th>
          <th class="group" colspan="3">Deducciones</th>

          <th class="group" colspan="3">Retenciones empleado</th>
          <th class="group" colspan="4">Seguridad social (Aportes empresa)</th>
          <th class="group" colspan="4">Aportes parafiscales</th>
          <th class="group" colspan="3">Provisión prestaciones sociales (Empresa)</th>

          <th rowspan="2">Pago neto<br>empleado</th>
          <th rowspan="2">Costo total<br>empresa</th>

          <th class="group" colspan="12">Seguimiento pago aportes</th>
        </tr>

        <tr>
          <th>Salario base</th>
          <th>Días</th>
          <th>Salario</th>
          <th>Sub. transporte</th>
          <th>Horas extras</th>
          <th>Otros ingresos</th>

          <th>Otras deducciones</th>
          <th>Base seg. social</th>
          <th>Base salud/pensión</th>

          <th>Salud</th>
          <th>Pensión</th>
          <th>Fondo solidaridad</th>

          <th>Salud (8.5%)</th>
          <th>Pensión (12%)</th>
          <th>Riesgos (ARL)</th>
          <th>Caja comp. (4%)</th>

          <th>ICBF (3%)</th>
          <th>SENA (2%)</th>
          <th>Cesantías (8.33%)</th>
          <th>Intereses cesantías (12%)</th>

          <th>Prima (8.33%)</th>
          <th>Vacaciones</th>
          <th>Otras</th>

          <th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th>
          <th>JUL</th><th>AGO</th><th>SEP</th><th>OCT</th><th>NOV</th><th>DIC</th>
        </tr>
      </thead>

      <tbody id="tbody"></tbody>

      <tfoot>
        <tr>
          <td>Totales</td>
          <td id="t_salbase">0</td>
          <td class="tiny">—</td>
          <td id="t_salario">0</td>
          <td id="t_sub">0</td>
          <td id="t_hextras">0</td>
          <td id="t_otros">0</td>

          <td id="t_oded">0</td>
          <td class="tiny">—</td>
          <td class="tiny">—</td>

          <td id="t_rsalud">0</td>
          <td id="t_rpension">0</td>
          <td id="t_rfs">0</td>

          <td id="t_esalud">0</td>
          <td id="t_epension">0</td>
          <td id="t_arl">0</td>
          <td id="t_caja">0</td>

          <td id="t_icbf">0</td>
          <td id="t_sena">0</td>
          <td id="t_ces">0</td>
          <td id="t_intces">0</td>

          <td id="t_prima">0</td>
          <td id="t_vac">0</td>
          <td id="t_otrasprov">0</td>

          <td id="t_neto">0</td>
          <td id="t_costo">0</td>

          <td class="tiny" colspan="12">—</td>
        </tr>
      </tfoot>
    </table>
  </div>

</div>

<script>
  // ===== helpers
  const money = (n) => {
    const x = Number(n || 0);
    return x.toLocaleString("es-CO", {style:"currency", currency:"COP", maximumFractionDigits:0});
  };
  const num = (v) => {
    const x = String(v ?? "").replaceAll(".","").replaceAll(",","").replace(/[^\d-]/g,"");
    return Number(x || 0);
  };
  const setHoy = () => {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");
    document.getElementById("fechaHoy").textContent = `${y}/${m}/${dd}`;
  };

  function goBack(){
    // Si está embebido (drawer/iframe), intentamos cerrar (si el padre expone bootstrap)
    // Si no, historial normal
    try {
      if (window.parent && window.parent !== window) {
        window.parent.postMessage({type:"SST_CLOSE_DRAWER"}, "*");
      } else {
        history.back();
      }
    } catch(e){
      history.back();
    }
  }

  // ===== render filas
  const tbody = document.getElementById("tbody");

  const cols = [
    "nombre",
    "salario_base","dias","salario","sub_trans","horas_extras","otros_ing",
    "otras_ded","base_ss","base_sp",
    "ret_salud","ret_pension","ret_fs",
    "emp_salud","emp_pension","arl","caja",
    "icbf","sena","ces","int_ces",
    "prima","vac","otras_prov",
    "neto","costo"
  ];

  function rowTemplate(i){
    return `
      <tr>
        <td><input class="cell-input" data-col="nombre" placeholder="Nombre trabajador"></td>

        <td><input class="cell-input money" data-col="salario_base" value="0"></td>
        <td><input class="cell-input" data-col="dias" value="30"></td>
        <td><input class="cell-input money" data-col="salario" value="0"></td>
        <td><input class="cell-input money" data-col="sub_trans" value="0"></td>
        <td><input class="cell-input money" data-col="horas_extras" value="0"></td>
        <td><input class="cell-input money" data-col="otros_ing" value="0"></td>

        <td><input class="cell-input money" data-col="otras_ded" value="0"></td>
        <td><input class="cell-input money" data-col="base_ss" value="0"></td>
        <td><input class="cell-input money" data-col="base_sp" value="0"></td>

        <td><input class="cell-input money" data-col="ret_salud" value="0"></td>
        <td><input class="cell-input money" data-col="ret_pension" value="0"></td>
        <td><input class="cell-input money" data-col="ret_fs" value="0"></td>

        <td><input class="cell-input money" data-col="emp_salud" value="0"></td>
        <td><input class="cell-input money" data-col="emp_pension" value="0"></td>
        <td><input class="cell-input money" data-col="arl" value="0"></td>
        <td><input class="cell-input money" data-col="caja" value="0"></td>

        <td><input class="cell-input money" data-col="icbf" value="0"></td>
        <td><input class="cell-input money" data-col="sena" value="0"></td>
        <td><input class="cell-input money" data-col="ces" value="0"></td>
        <td><input class="cell-input money" data-col="int_ces" value="0"></td>

        <td><input class="cell-input money" data-col="prima" value="0"></td>
        <td><input class="cell-input money" data-col="vac" value="0"></td>
        <td><input class="cell-input money" data-col="otras_prov" value="0"></td>

        <td><input class="cell-input money" data-col="neto" value="0"></td>
        <td><input class="cell-input money" data-col="costo" value="0"></td>

        ${Array.from({length:12}).map((_,m)=>`<td class="text-center">
          <input type="text" class="cell-input" style="width:42px;text-align:center;" maxlength="1" placeholder="X">
        </td>`).join("")}
      </tr>
    `;
  }

  // 10 filas por defecto (puedes subir a 15/20)
  for(let i=0;i<10;i++) tbody.insertAdjacentHTML("beforeend", rowTemplate(i));

  // ===== totales
  const totalMap = {
    salario_base:"t_salbase",
    salario:"t_salario",
    sub_trans:"t_sub",
    horas_extras:"t_hextras",
    otros_ing:"t_otros",
    otras_ded:"t_oded",
    ret_salud:"t_rsalud",
    ret_pension:"t_rpension",
    ret_fs:"t_rfs",
    emp_salud:"t_esalud",
    emp_pension:"t_epension",
    arl:"t_arl",
    caja:"t_caja",
    icbf:"t_icbf",
    sena:"t_sena",
    ces:"t_ces",
    int_ces:"t_intces",
    prima:"t_prima",
    vac:"t_vac",
    otras_prov:"t_otrasprov",
    neto:"t_neto",
    costo:"t_costo",
  };

  function calcTotals(){
    const rows = document.querySelectorAll("#tbody tr");
    const sums = {};
    Object.keys(totalMap).forEach(k => sums[k]=0);

    rows.forEach(tr=>{
      Object.keys(totalMap).forEach(col=>{
        const inp = tr.querySelector(`[data-col="${col}"]`);
        if(!inp) return;
        sums[col] += num(inp.value);
      });
    });

    Object.entries(totalMap).forEach(([col, id])=>{
      document.getElementById(id).textContent = money(sums[col]);
    });
  }

  document.addEventListener("input", (e)=>{
    if(e.target && e.target.matches("input[data-col]")) calcTotals();
  });

  // init
  setHoy();
  calcTotals();

  // Opcional: escuchar mensaje del padre para cerrar drawer
  window.addEventListener("message", (ev)=>{
    // (si quieres acciones futuras)
  });
</script>

</body>
</html>