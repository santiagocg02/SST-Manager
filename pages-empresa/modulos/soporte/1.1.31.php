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
  <title>AN-SST-26 | Seguimiento mensual pago de aportes y parafiscales</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#2f62b6;
      --blue-soft:#d9e6ff;
      --gray:#f4f6fa;
    }
    body{ background:#e9edf3; }
    .wrap{ max-width: 1800px; margin: 16px auto; padding: 0 10px; }

    .toolbar{
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      margin-bottom: 10px;
    }

    .sheet{
      background:#fff;
      border:2px solid var(--blue);
      box-shadow: 0 10px 20px rgba(0,0,0,.08);
      padding: 10px;
    }

    .excel-wrap{
      overflow:auto;
      border:1px solid rgba(0,0,0,.15);
      background:#fff;
    }

    table.excel{
      border-collapse:collapse;
      table-layout:fixed;
      width: 2400px;
      font-size:11px;
    }

    /* ✅ IMPORTANTE:
       - TD se queda tipo Excel (nowrap)
       - TH SÍ puede partir líneas (wrap)
    */
    .excel td{
      border:1px solid var(--line);
      padding:4px 6px;
      vertical-align:middle;
      white-space:nowrap;  /* Excel */
    }

    .excel th{
      border:1px solid var(--line);
      padding:6px 6px;     /* un poquito más alto para títulos */
      vertical-align:middle;
      white-space:normal;  /* ✅ permite salto */
      line-height:1.1;     /* ✅ evita que se monten */
      word-break:break-word;
      overflow-wrap:anywhere;
      text-align:center;
    }

    .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:900; }

    /* Header */
    .top-title{
      font-size: 12px;
      font-weight: 900;
      text-align:center;
      padding:8px 6px;
    }
    .top-sub{
      font-size: 11px;
      font-weight: 900;
      text-align:center;
      padding:6px 6px 8px;
    }

    .logo-box{
      border:2px dashed rgba(0,0,0,.35);
      height:55px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:900;
      color:rgba(0,0,0,.35);
      text-align:center;
      font-size: 11px;
    }

    .mini-note{
      font-size: 10px;
      color:#d14b00;
      font-style: italic;
      margin: 6px 0;
    }

    /* Section headers */
    .h-blue{
      background: var(--blue);
      color:#fff;
      font-weight:900;
      text-align:center;
    }
    .h-soft{
      background: var(--blue-soft);
      font-weight:900;
      text-align:center;
    }

    /* Input inside cell */
    .cell-input{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:11px;
    }
    .cell-input.money{ text-align:right; }
    .cell-input.center{ text-align:center; }

    /* Sticky first columns */
    .sticky-1{ position:sticky; left:0; background:#fff; z-index:3; }
    .sticky-2{ position:sticky; left:170px; background:#fff; z-index:3; }
    .sticky-head{ z-index:5 !important; }

    /* widths */
    .w-name{ width:170px; }
    .w-sm{ width:62px; }     /* ✅ un poquito más compacta */
    .w-md{ width:105px; }    /* ✅ títulos largos respiran mejor */
    .w-lg{ width:120px; }

    .totals td{
      background: var(--gray);
      font-weight:900;
    }

    @media (max-width: 1200px){
      table.excel{ font-size:10px; }
      .excel th{ font-size:10px; }
      .cell-input{ font-size:10px; }
    }

    @media print{
      body{ background:#fff; }
      .toolbar{ display:none !important; }
      .sheet{ box-shadow:none; }
      .excel-wrap{ overflow:visible; border:none; }
      table.excel{ width:100% !important; }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>
<div class="wrap">

  <div class="toolbar">
    <div class="d-flex gap-2 flex-wrap">
      <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Volver</a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir / PDF</button>
    </div>
    <div class="small text-muted fw-semibold">Formato: AN-SST-26</div>
  </div>

  <div class="sheet">

    <!-- CABECERA -->
    <table class="excel mb-2" style="width:100%; table-layout:fixed;">
      <colgroup>
        <col style="width:220px">
        <col>
        <col style="width:180px">
        <col style="width:140px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="top-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
        <td class="center bold">0</td>
        <td class="center bold">AN-SST-26</td>
      </tr>
      <tr>
        <td class="top-sub">SEGUIMIENTO MENSUAL DE PAGO DE APORTES Y PARAFISCALES</td>
        <td class="center bold"><input class="cell-input center" placeholder="XX/XX/2025"></td>
        <td class="center">
          <span class="badge text-bg-light border">PLANEAR</span>
        </td>
      </tr>
    </table>

    <div class="d-flex flex-wrap gap-3 align-items-start mb-2">
      <div class="border p-2 bg-white" style="min-width:260px">
        <div class="bold" style="font-size:11px;">Salario mínimo legal vigente</div>
        <input class="cell-input money" placeholder="$ 0">
        <div class="bold mt-2" style="font-size:11px;">Subsidio de transporte</div>
        <input class="cell-input money" placeholder="$ 0">
        <div class="bold mt-2" style="font-size:11px;">¿Aplica Ley 1607? (Exonerado de pago de aportes)</div>
        <input class="cell-input" placeholder="No / Sí">
      </div>

      <div class="border p-2 bg-white" style="min-width:260px">
        <div class="bold center" style="font-size:11px;">Periodo de pago:</div>
        <input class="cell-input center" placeholder="01/01/2026 - 15/01/2026">
      </div>

      <div class="ms-auto mini-note">Tabla formulada</div>
    </div>

    <div class="excel-wrap">
      <table class="excel" id="t1">

        <!-- ===== ENCABEZADO POR GRUPOS ===== -->
        <tr class="h-blue">
          <th class="sticky-1 sticky-head w-name" rowspan="2">Nombre</th>
          <th class="sticky-2 sticky-head w-lg" rowspan="2">Salario base<br>mensual</th>

          <th colspan="6">Ingresos</th>
          <th colspan="3">Otras Deducciones</th>
          <th colspan="4">Retenciones Empleado</th>
          <th colspan="4">Seguridad social (Aportes Empresa)</th>
          <th colspan="4">Aportes Parafiscales</th>
          <th colspan="4">Provisión Prestaciones sociales (Empresa)</th>

          <th class="w-lg" rowspan="2">Pago Neto<br>Empleado</th>
          <th class="w-lg" rowspan="2">Costo Total<br>empresa</th>

          <th colspan="12">SEGUIMIENTO DE PAGO DE APORTES</th>
        </tr>

        <tr class="h-soft">
          <!-- Ingresos (6) -->
          <th class="w-sm">Días<br>liquidados</th>
          <th class="w-md">Salario</th>
          <th class="w-md">Subsidio<br>transporte</th>
          <th class="w-md">Horas Extra y<br>Recargos</th>
          <th class="w-md">Recargo<br>Nocturno</th>
          <th class="w-md">Otros<br>ingresos</th>

          <!-- Otras deducciones (3) -->
          <th class="w-md">Otros Ingresos<br>constitutivos de salario</th>
          <th class="w-md">Otros Ingresos No<br>Constitutivos</th>
          <th class="w-md">Otras<br>deducciones</th>

          <!-- Retenciones empleado (4) -->
          <th class="w-md">Base seguridad<br>social</th>
          <th class="w-md">Salud</th>
          <th class="w-md">Pensión</th>
          <th class="w-md">Fondo de<br>Solidaridad</th>

          <!-- Aportes empresa (4) -->
          <th class="w-md">Salud</th>
          <th class="w-md">Pensión</th>
          <th class="w-md">Riesgos</th>
          <th class="w-md">Caja de<br>Compensación</th>

          <!-- Parafiscales (4) -->
          <th class="w-md">ICBF</th>
          <th class="w-md">SENA</th>
          <th class="w-md">Cesantías</th>
          <th class="w-md">Intereses sobre<br>cesantías</th>

          <!-- Provisión prestaciones (4) -->
          <th class="w-md">Prima de<br>Servicios</th>
          <th class="w-md">Vacaciones</th>
          <th class="w-md">Otros</th>
          <th class="w-md">Total provisión</th>

          <!-- Meses 12 -->
          <th class="w-sm">ENE</th><th class="w-sm">FEB</th><th class="w-sm">MAR</th><th class="w-sm">ABR</th>
          <th class="w-sm">MAY</th><th class="w-sm">JUN</th><th class="w-sm">JUL</th><th class="w-sm">AGO</th>
          <th class="w-sm">SEP</th><th class="w-sm">OCT</th><th class="w-sm">NOV</th><th class="w-sm">DIC</th>
        </tr>

        <!-- ===== FILAS DEMO ===== -->
        <?php
          $demo = [
            ["$1 000 000", "30"],
            ["$1 300 000", "15"],
            ["$2 300 000", "30"],
            ["$2 000 000", "30"],
            ["$4 000 000", "30"],
            ["$6 000 000", "30"],
          ];
          $i=1;
          foreach($demo as $row):
        ?>
        <tr>
          <td class="sticky-1 w-name"><input class="cell-input" placeholder="Trabajador <?= $i ?>"></td>
          <td class="sticky-2 w-lg"><input class="cell-input money" placeholder="<?= $row[0] ?>"></td>

          <!-- Ingresos -->
          <td class="center"><input class="cell-input center" placeholder="<?= $row[1] ?>"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Otras deducciones -->
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Retenciones empleado -->
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Aportes empresa -->
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Parafiscales -->
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Provisión -->
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Neto / costo -->
          <td><input class="cell-input money" placeholder="$0"></td>
          <td><input class="cell-input money" placeholder="$0"></td>

          <!-- Meses seguimiento -->
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
          <td class="center"><input class="cell-input center" placeholder="X"></td>
        </tr>
        <?php $i++; endforeach; ?>

        <!-- ===== TOTALES ===== -->
        <tr class="totals">
          <td class="sticky-1 bold">Totales</td>
          <td class="sticky-2"></td>

          <?php for($k=0;$k< (6+3+4+4+4+4+2+12); $k++): ?>
            <td></td>
          <?php endfor; ?>
        </tr>

      </table>
    </div>

    <div class="text-center mt-3 small text-muted">© 2026 SSTManager - Tu aliado estratégico en SST</div>

  </div>
</div>

</body>
</html>