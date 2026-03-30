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
<title>2.2.1 - Matriz de Indicadores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
  }

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
    .sheet{ box-shadow:none; }
    .top-scroll{ display:none; }
  }
</style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="wrap">

  <div class="toolbar">
    <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Atrás</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
  </div>

  <div class="sheet">

    <table class="format-top">
      <colgroup>
        <col style="width:100px">
        <col>
        <col style="width:120px">
        <col style="width:90px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO AQUÍ</div></td>
        <td><strong>SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</strong></td>
        <td><strong>0</strong></td>
        <td rowspan="2"><strong>PLANEAR</strong></td>
      </tr>
      <tr>
        <td><strong>MATRIZ DE INDICADORES</strong></td>
        <td><strong>AN-SST-06<br>XX/XX/2025</strong></td>
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
                <input type="text" class="workers-input" value="100">
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
            <td><input class="cell-input left" type="text"></td>
            <td><?php echo $i; ?></td>
            <td><input class="cell-input" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>

            <?php for($m=1;$m<=12;$m++): ?>
              <td><input class="cell-input" type="text"></td>
              <td><input class="cell-input" type="text"></td>
            <?php endfor; ?>

            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
            <td><input class="cell-input left" type="text"></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
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
</script>

</body>
</html>