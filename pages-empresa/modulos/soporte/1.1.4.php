<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
// Ajusta esta ruta dependiendo de la ubicación de este archivo
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);

// --- Lógica de Empresa y Logo ---
$logoEmpresaUrl = "";

if ($empresa > 0) {
    // Solicitamos a la API exclusivamente la empresa logueada pasando el ID
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);

    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? ''; // <--- Extraemos el logo
    }
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
      --sst-border:#111;
      --sst-primary:#9fb4d9;
      --sst-primary-soft:#dbe7f7;
      --sst-bg:#eef3f9;
      --sst-paper:#ffffff;
      --sst-text:#111;
      --sst-muted:#5f6b7a;
      --sst-toolbar:#dde7f5;
      --sst-toolbar-border:#c8d3e2;
    }

    *{
      box-sizing:border-box;
    }

    html, body{
      margin:0;
      padding:0;
      font-family:Arial, Helvetica, sans-serif;
      background:var(--sst-bg);
      color:var(--sst-text);
      font-size:12px;
    }

     .sst-toolbar{
      position:sticky;
      top:0;
      z-index:100;
      background:var(--sst-toolbar);
      border-bottom:1px solid var(--sst-toolbar-border);
      padding:12px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    .sst-toolbar-title{
      margin:0;
      font-size:15px;
      font-weight:800;
      color:#213b67;
    }

    .sst-toolbar-actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
    }


    .sst-page{
      padding:20px;
    }

    .sst-paper{
      width:min(1600px, 100%);
      margin:0 auto;
      background:var(--sst-paper);
      border:1px solid #d7dee8;
      box-shadow:0 10px 25px rgba(0,0,0,.08);
      padding:12px 14px 18px;
    }

    .tiny{
      font-size:10px;
      color:var(--sst-muted);
      font-weight:700;
    }

    .btn-rect{
      border-radius:0 !important;
    }

    /* Header formato */
    .format-head{
      border:1px solid var(--sst-border);
      border-bottom:none;
      background:#fff;
    }

    .format-head .grid{
      display:grid;
      grid-template-columns:190px 1fr 220px;
      align-items:stretch;
    }

    .logo-box{
      border-right:1px solid var(--sst-border);
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
      border:1px dashed #b5b5b5;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#808080;
      font-weight:800;
      letter-spacing:.4px;
      text-align:center;
    }

    .title-box{
      padding:10px 12px;
      border-right:1px solid var(--sst-border);
      text-align:center;
      background:#fff;
    }

    .title-box .top{
      font-weight:800;
      font-size:11px;
      text-transform:uppercase;
      line-height:1.35;
    }

    .title-box .mid{
      margin-top:4px;
      font-weight:900;
      font-size:12px;
      text-transform:uppercase;
      line-height:1.35;
    }

    .meta-box{
      display:grid;
      grid-template-columns:1fr 1fr;
      grid-auto-rows:minmax(28px, auto);
    }

    .meta-box div{
      border-left:1px solid var(--sst-border);
      border-bottom:1px solid var(--sst-border);
      padding:6px 8px;
      display:flex;
      justify-content:space-between;
      gap:10px;
      align-items:center;
      font-size:11px;
      background:#fff;
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
      border:1px solid var(--sst-border);
      border-top:none;
      padding:8px 10px;
      background:#fff;
    }

    /* Info row */
    .info-row{
      display:grid;
      grid-template-columns:320px 1fr 320px;
      gap:10px;
      margin:10px 0;
      align-items:start;
    }

    .info-card{
      border:1px solid #d7dbe3;
      padding:10px;
      background:#fff;
      border-radius:0 !important;
    }

    .info-card .small{
      font-size:11px;
      color:var(--sst-muted);
      font-weight:700;
      margin-bottom:6px;
    }

    .info-list{
      display:grid;
      grid-template-columns:1fr auto;
      gap:6px 10px;
      font-size:11px;
    }

    .info-list .k{
      color:#111;
      font-weight:700;
    }

    .info-list .v{
      font-weight:800;
    }

    .period{
      display:flex;
      gap:10px;
      align-items:center;
      justify-content:center;
      height:100%;
      border:1px solid #d7dbe3;
      background:var(--sst-primary-soft);
      padding:10px;
      border-radius:0 !important;
    }

    .period .pill{
      background:#fff;
      border:1px solid #d7dbe3;
      padding:8px 10px;
      min-width:260px;
      text-align:center;
      border-radius:0 !important;
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
      padding:8px 10px;
      font-weight:800;
      font-size:12px;
      background:#fff;
      border-radius:0 !important;
    }

    /* Table */
    .table-wrap{
      border:1px solid #cfd6e4;
      overflow:auto;
      background:#fff;
      border-radius:0 !important;
    }

    table{
      border-collapse:separate;
      border-spacing:0;
      width:1800px;
      font-size:11px;
      table-layout:fixed;
    }

    thead th{
      position:sticky;
      top:0;
      z-index:2;
      background:var(--sst-primary);
      color:#111;
      text-align:center;
      font-weight:900;
      padding:8px 6px;
      border-right:1px solid rgba(0,0,0,.18);
      border-bottom:1px solid var(--sst-border);
      white-space:normal;
      word-break:break-word;
      overflow-wrap:anywhere;
      line-height:1.2;
    }

    thead .group{
      background:var(--sst-primary-soft);
      font-size:10px;
      letter-spacing:.3px;
      text-transform:uppercase;
      color:#111;
    }

    tbody td{
      border-right:1px solid #e8edf6;
      border-bottom:1px solid #eef2f8;
      padding:6px 6px;
      vertical-align:middle;
      background:#fff;
      white-space:normal;
      word-break:break-word;
      overflow-wrap:anywhere;
    }

    tbody tr:nth-child(even) td{
      background:#fbfcff;
    }

    tbody td:first-child{
      position:sticky;
      left:0;
      z-index:1;
      background:inherit;
    }

    thead th:first-child{
      position:sticky;
      left:0;
      z-index:3;
    }

    .cell-input{
      width:100%;
      border:1px solid #d7dbe3;
      padding:6px 8px;
      font-size:11px;
      font-weight:700;
      background:#fff;
      border-radius:0 !important;
      min-width:0;
      max-width:100%;
      white-space:normal;
      overflow-wrap:anywhere;
      outline:none;
    }

    .cell-input.money{
      text-align:right;
    }

    .cell-input:focus{
      border-color:#7d96bf;
      box-shadow:none;
    }

    /* Totals row */
    tfoot td{
      position:sticky;
      bottom:0;
      z-index:2;
      background:var(--sst-primary-soft);
      border-top:1px solid #cfd6e4;
      font-weight:900;
      padding:8px 6px;
      white-space:normal;
      word-break:break-word;
      overflow-wrap:anywhere;
    }

    tfoot td:first-child{
      position:sticky;
      left:0;
      z-index:3;
      background:var(--sst-primary-soft);
    }

    tbody td:first-child,
    thead th:first-child,
    tfoot td:first-child{
      min-width:260px;
      width:260px;
}

    @media (max-width: 991px){
      .sst-page{
        padding:12px;
      }

      .sst-paper{
        padding:12px;
      }

      .info-row{
        grid-template-columns:1fr;
      }

      .format-head .grid{
        grid-template-columns:1fr;
      }

      .logo-box,
      .title-box{
        border-right:none;
        border-bottom:1px solid var(--sst-border);
      }

      .meta-box{
        grid-template-columns:1fr 1fr;
      }
    }

    @media print{
      html, body{
        background:#fff !important;
      }

      .sst-toolbar{
        display:none !important;
      }

      .sst-page{
        padding:0 !important;
      }

      .sst-paper{
        max-width:none;
        width:100%;
        padding:0;
        border:none;
        box-shadow:none;
      }

      .table-wrap{
        border:none;
      }

      table{
        width:100%;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>

<div class="sst-toolbar">
  <h1 class="sst-toolbar-title">Seguimiento mensual pago de aportes y parafiscales</h1>

  <div class="sst-toolbar-actions">
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
    </div>
  </div>

<div class="sst-page">
  <div class="sst-paper">

    <div class="format-head">
      <div class="grid">
        <div class="logo-box">
          <?php if(!empty($logoEmpresaUrl)): ?>
              <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
          <?php else: ?>
              <div class="logo-placeholder">TU LOGO AQUÍ</div>
          <?php endif; ?>
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
        <div class="tiny mt-2">
          Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong>
        </div>
      </div>
    </div>

    <div class="table-wrap">
      <table id="tabla">
        <thead>
          <tr>
            <th rowspan="2" style="min-width:260px; width:260px;">Trabajador (Nombre)</th>

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
</div>

<script>
  const money = (n) => {
    const x = Number(n || 0);
    return x.toLocaleString("es-CO", {
      style:"currency",
      currency:"COP",
      maximumFractionDigits:0
    });
  };

  const num = (v) => {
    const x = String(v ?? "")
      .replaceAll(".","")
      .replaceAll(",","")
      .replace(/[^\d-]/g,"");
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

  function rowTemplate(){
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

        ${Array.from({length:12}).map(() => `
          <td class="text-center">
            <input type="text" class="cell-input" style="width:42px;text-align:center;" maxlength="1" placeholder="X">
          </td>
        `).join("")}
      </tr>
    `;
  }

  for(let i=0; i<10; i++){
    tbody.insertAdjacentHTML("beforeend", rowTemplate());
  }

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

  setHoy();
  calcTotals();

  window.addEventListener("message", () => {});
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>