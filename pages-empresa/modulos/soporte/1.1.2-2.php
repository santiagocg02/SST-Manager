<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AN-SST-03 | Consolidado General Presupuesto</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    :root{
      --line:#111;
      --blue:#8ea6c9;
      --blue2:#d9e1f2;
      --blue3:#b8c7df;
      --note:#f28c28;
    }

    body{ background:#f3f6fa; }
    .wrap{ max-width: 1350px; margin: 18px auto; padding: 0 10px; }

    .toolbar{
      display:flex; justify-content:space-between; align-items:center; gap:10px;
      margin-bottom:10px;
    }

    .sheet{
      background:#fff;
      border:2px solid var(--line);
      box-shadow: 0 10px 22px rgba(0,0,0,.08);
      padding: 10px;
    }

    table.excel{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      font-size:12px;
    }
    table.excel th, table.excel td{
      border:1px solid var(--line);
      padding:6px 6px;
      vertical-align:middle;
    }

    .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:900; }

    .h-blue{ background:var(--blue); font-weight:900; }
    .h-blue2{ background:var(--blue2); font-weight:900; }
    .months{ background:var(--blue3); font-weight:900; text-align:center; }

    .logo-box{
      border:2px dashed rgba(0,0,0,.35);
      height:70px;
      display:flex; align-items:center; justify-content:center;
      font-weight:900; color:rgba(0,0,0,.35);
      text-align:center;
    }

    .title{
      text-align:center;
      font-weight:900;
      font-size:16px;
      padding:10px 6px;
    }
    .subtitle{
      text-align:center;
      font-weight:900;
      font-size:14px;
      padding:6px 6px 10px;
    }

    .note{
      color: var(--note);
      font-style: italic;
      font-size: 11px;
      margin: 6px 0 10px;
    }

    .cell-input{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
    }
    .cell-input.orange{ color: var(--note); font-weight:900; }
    .cell-input.center{ text-align:center; }
    .cell-input.right{ text-align:right; }

    /* ✅ Secciones en celdas reales */
    .section-row td{
      background: var(--blue);
      font-weight:900;
    }

    .total td{
      background: var(--blue2);
      font-weight:900;
    }

    .signature{
      margin-top: 8px;
      border-top: 2px dashed #1b4fbd;
      padding-top: 6px;
      font-weight:900;
      font-size: 12px;
    }

    .block-title{
      background: var(--blue);
      font-weight:900;
      text-align:center;
      padding:6px;
      border:1px solid var(--line);
      margin-top:16px;
    }

    .chart-box{
      border:1px solid var(--line);
      background:#eef3fb;
      padding:10px;
      height: 260px;
      display:flex;
      flex-direction:column;
      gap:8px;
    }
    .chart-title{
      font-weight:900;
      text-align:center;
      font-size: 12px;
    }
    .chart-canvas-wrap{ flex:1; min-height:0; }
    .chart-canvas-wrap canvas{ width:100% !important; height:100% !important; }

    .semester{
      border:1px solid var(--line);
      padding:10px;
      font-size:12px;
      height: 120px;
    }
    .semester .t{
      font-weight:900;
      text-align:center;
      margin-bottom: 6px;
    }

    .table-wrap{ overflow:auto; }

    @media print{
      body{ background:#fff; }
      .toolbar{ display:none !important; }
      .wrap{ max-width:none; margin:0; padding:0; }
      .sheet{ box-shadow:none; border:2px solid #000; }
      .table-wrap{ overflow:visible !important; }
    }
  </style>
</head>

<body>
<div class="wrap">

  <div class="toolbar">
    <div class="d-flex gap-2 flex-wrap">
      <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Volver</a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir / PDF</button>
    </div>
    <div class="small text-muted fw-semibold">Formato: AN-SST-03</div>
  </div>

  <div class="sheet">

    <!-- CABECERA -->
    <table class="excel mb-2">
      <colgroup>
        <col style="width:16%">
        <col style="width:64%">
        <col style="width:20%">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
        <td class="center bold">0</td>
      </tr>
      <tr>
        <td class="subtitle">CONSOLIDADO GENERAL PRESUPUESTO</td>
        <td class="center bold">
          AN-SST-03<br>
          <input class="cell-input center" placeholder="XX/XX/2025">
        </td>
      </tr>
    </table>

    <div class="note">Nota: Diligencie los campos en naranja, los costos, gastos mensuales y el análisis tendencial</div>

    <div class="table-wrap">
      <table class="excel">
        <colgroup>
          <col style="width:26%">
          <col style="width:10%">
          <col style="width:10%">
          <col style="width:4%">
          <col style="width:10%">
          <col style="width:4%">
          <col span="12" style="width:3%">
        </colgroup>

        <!-- ✅ Encabezado: los 6 de la izquierda con rowspan=2 (ELIMINA los cuadros amarillos) -->
        <tr class="h-blue center">
          <th rowspan="2">ACTIVIDADES</th>
          <th rowspan="2">PRESUPUESTO<br>PROYECTADO</th>
          <th rowspan="2">PRESUPUESTO<br>EJECUTADO</th>
          <th rowspan="2">%</th>
          <th rowspan="2">PRESUPUESTO<br>POR EJECUTAR</th>
          <th rowspan="2">%</th>
          <th colspan="12">20XX</th>
        </tr>

        <!-- ✅ Fila de meses SOLO meses (sin celdas sobrantes) -->
        <tr class="months">
          <th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th>
          <th>JUL</th><th>AGO</th><th>SEPT</th><th>OCT</th><th>NOV</th><th>DIC</th>
        </tr>

        <!-- ✅ SECCIÓN con celdas reales (18 celdas) -->
        <tr class="section-row">
          <td>Medicina Preventiva, del Trabajo y Otros</td>
          <td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>Exámenes médicos (Emo, Paraclínicos y la…)</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input orange center" placeholder="0"></td>
          <td><input class="cell-input orange center" placeholder="0"></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
        </tr>

        <tr>
          <td>Vacunación</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input orange center" placeholder="0"></td>
          <td><input class="cell-input orange center" placeholder="0"></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
        </tr>

        <tr>
          <td>Compra medicamentos para el botiquín</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
          <td><input class="cell-input center" placeholder=""></td>
        </tr>

        <!-- filas vacías (18 celdas reales) -->
        <tr>
          <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr>
          <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <!-- ✅ SECCIÓN con celdas reales -->
        <tr class="section-row">
          <td>Higiene Industrial y manejo ambiental</td>
          <td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>Mediciones de Higiene</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>Punto Ecológico</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>Tableros informativos</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <!-- ✅ SECCIÓN con celdas reales -->
        <tr class="section-row">
          <td>Seguridad Industrial</td>
          <td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>Compra Dotación Personal y EPP</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr>
          <td>Compra y mantenimiento de extintores</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        <tr class="total">
          <td>Total</td>
          <td><input class="cell-input orange right" placeholder="0"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td><input class="cell-input right" placeholder="0"></td>
          <td><input class="cell-input center" placeholder="0%"></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
          <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

      </table>
    </div>

    <div class="signature">FIRMA DEL REPRESENTANTE LEGAL</div>

    <!-- ANALISIS TENDENCIAL -->
    <div class="block-title">ANALISIS TENDENCIAL</div>

    <table class="excel">
      <colgroup>
        <col style="width:22%">
        <col span="12" style="width:6.5%">
      </colgroup>
      <tr class="months">
        <th class="center">MES</th>
        <th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th>
        <th>JUL</th><th>AGO</th><th>SEPT</th><th>OCT</th><th>NOV</th><th>DIC</th>
      </tr>
      <tr>
        <td class="bold center">TOTAL</td>
        <td><input class="cell-input orange center js-month" data-month="ENE" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="FEB" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="MAR" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="ABR" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="MAY" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="JUN" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="JUL" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="AGO" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="SEPT" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="OCT" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="NOV" placeholder="0"></td>
        <td><input class="cell-input orange center js-month" data-month="DIC" placeholder="0"></td>
      </tr>
      <tr>
        <td class="bold center">%</td>
        <td><input class="cell-input orange center js-pct" data-month="ENE" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="FEB" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="MAR" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="ABR" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="MAY" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="JUN" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="JUL" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="AGO" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="SEPT" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="OCT" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="NOV" placeholder="0%"></td>
        <td><input class="cell-input orange center js-pct" data-month="DIC" placeholder="0%"></td>
      </tr>
    </table>

    <div class="block-title">ANALISIS TENDENCIAL</div>

    <div class="row g-3 mt-2">
      <div class="col-12 col-lg-8">
        <div class="chart-box">
          <div class="chart-title">PRESUPUESTO EJECUTADO 20XX</div>
          <div class="chart-canvas-wrap">
            <canvas id="budgetChart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="semester mb-3">
          <div class="t">I SEMESTRE</div>
          <textarea class="form-control" rows="4" placeholder="Escriba el análisis del primer semestre..."></textarea>
        </div>
        <div class="semester">
          <div class="t">II SEMESTRE</div>
          <textarea class="form-control" rows="4" placeholder="Escriba el análisis del segundo semestre..."></textarea>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  const monthOrder = ["ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SEPT","OCT","NOV","DIC"];

  function parseMoney(val){
    const raw = String(val ?? "").trim();
    if(!raw) return 0;
    const cleaned = raw.replace(/[^\d]/g, "");
    return cleaned ? Number(cleaned) : 0;
  }

  function formatPct(n){
    return `${Math.round(n)}%`;
  }

  const monthInputs = Array.from(document.querySelectorAll(".js-month"));
  const pctInputs   = Array.from(document.querySelectorAll(".js-pct"));

  const ctx = document.getElementById("budgetChart");

  const chart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: monthOrder,
      datasets: [{
        label: "Total mensual",
        data: monthOrder.map(() => 0),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { callback: (v) => Number(v).toLocaleString("es-CO") }
        }
      }
    }
  });

  function updateChartAndPercents(){
    const values = monthOrder.map(m => {
      const inp = monthInputs.find(x => x.dataset.month === m);
      return parseMoney(inp?.value);
    });

    const totalYear = values.reduce((a,b)=>a+b,0);

    monthOrder.forEach((m, idx) => {
      const pct = totalYear === 0 ? 0 : (values[idx] / totalYear) * 100;
      const pctInp = pctInputs.find(x => x.dataset.month === m);
      if(pctInp) pctInp.value = totalYear === 0 ? "0%" : formatPct(pct);
    });

    chart.data.datasets[0].data = values;
    chart.update();
  }

  monthInputs.forEach(inp => {
    inp.addEventListener("input", updateChartAndPercents);
    inp.addEventListener("change", updateChartAndPercents);
  });

  updateChartAndPercents();
</script>

</body>
</html>