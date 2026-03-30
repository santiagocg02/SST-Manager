<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../../index.php");
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
      --sst-border:#111;
      --sst-primary:#9fb4d9;
      --sst-primary-soft:#dbe7f7;
      --sst-bg:#eef3f9;
      --sst-paper:#ffffff;
      --sst-text:#111;
      --sst-muted:#5f6b7a;
      --sst-toolbar:#dde7f5;
      --sst-toolbar-border:#c8d3e2;
      --sst-orange:#f28c28;
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
      width:216mm;
      min-height:279mm;
      margin:0 auto;
      background:var(--sst-paper);
      border:1px solid #d7dee8;
      box-shadow:0 10px 25px rgba(0,0,0,.08);
      padding:8mm;
      box-sizing:border-box;
    }

    .sst-table-wrap{
      width:100%;
      overflow-x:auto;
    }

    .sst-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .sst-table td,
    .sst-table th{
      border:1px solid var(--sst-border);
      padding:6px 8px;
      vertical-align:middle;
      font-size:12px;
      word-wrap:break-word;
      height:auto;
    }

    .sst-title{
      background:var(--sst-primary);
      text-align:center;
      font-weight:800;
      text-transform:uppercase;
    }

    .sst-subtitle{
      background:var(--sst-primary-soft);
      text-align:center;
      font-weight:800;
      text-transform:uppercase;
    }

    .months{
      background:var(--sst-primary);
      text-align:center;
      font-weight:800;
    }

    .section-row td{
      background:var(--sst-primary);
      font-weight:800;
    }

    .total-row td{
      background:var(--sst-primary-soft);
      font-weight:800;
    }

    .center{
      text-align:center;
    }

    .right{
      text-align:right;
    }

    .bold{
      font-weight:800;
    }

    .small{
      font-size:12px;
    }

    .muted{
      color:var(--sst-muted);
    }

    .orange{
      color:var(--sst-orange);
      font-weight:800;
    }

    .sst-input,
    .sst-select{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      padding:2px 4px;
      font-family:Arial, Helvetica, sans-serif;
      color:#111;
    }

    .sst-input.center{
      text-align:center;
    }

    .sst-input.right{
      text-align:right;
    }

    .sst-input-line{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      padding:2px 0;
      border-bottom:1px solid #666;
      font-family:Arial, Helvetica, sans-serif;
      color:#111;
    }

    .logo-box{
      height:72px;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
      font-weight:800;
      color:#808080;
      border:2px dashed #b5b5b5;
      text-align:center;
      line-height:1.2;
    }

    .header-main{
      text-align:center;
      font-weight:800;
      font-size:14px;
      line-height:1.4;
      text-transform:uppercase;
    }

    .meta-box{
      display:flex;
      flex-direction:column;
      gap:8px;
      font-size:12px;
      height:100%;
      justify-content:center;
    }

    .meta-box .meta-item{
      text-align:right;
      font-weight:800;
    }

    .note-box{
      margin:8px 0 12px;
      font-size:12px;
      color:var(--sst-orange);
      font-style:italic;
      font-weight:700;
    }

    .signature-label{
      margin-top:8px;
      border-top:1px dashed #1b4fbd;
      padding-top:6px;
      font-weight:800;
      font-size:12px;
    }

    .block-title{
      background:var(--sst-primary);
      text-align:center;
      font-weight:800;
      text-transform:uppercase;
      border:1px solid var(--sst-border);
      padding:6px 8px;
      margin-top:14px;
      font-size:12px;
    }

    .chart-box{
      border:1px solid var(--sst-border);
      background:#eef3fb;
      padding:10px;
      height:260px;
      display:flex;
      flex-direction:column;
      gap:8px;
    }

    .chart-title{
      font-weight:800;
      text-align:center;
      font-size:12px;
      text-transform:uppercase;
    }

    .chart-canvas-wrap{
      flex:1;
      min-height:0;
    }

    .chart-canvas-wrap canvas{
      width:100% !important;
      height:100% !important;
    }

    .semester{
      border:1px solid var(--sst-border);
      padding:10px;
      font-size:12px;
      min-height:120px;
    }

    .semester .t{
      font-weight:800;
      text-align:center;
      margin-bottom:6px;
      text-transform:uppercase;
    }

    .semester textarea{
      width:100%;
      min-height:72px;
      border:none;
      outline:none;
      resize:vertical;
      background:transparent;
      font-size:12px;
      font-family:Arial, Helvetica, sans-serif;
    }

    .add-row-cell{
      padding:0 !important;
      background:#f8fbff;
    }

    .add-row-btn-inline{
      width:100%;
      border:none;
      background:transparent;
      color:#1b4fbd;
      font-weight:800;
      font-size:12px;
      padding:8px 10px;
      text-align:left;
      cursor:pointer;
    }

    .add-row-btn-inline:hover{
      background:#eaf2ff;
    }

    .add-row-btn-inline .plus{
      display:inline-flex;
      width:20px;
      height:20px;
      align-items:center;
      justify-content:center;
      border:1px solid #1b4fbd;
      border-radius:50%;
      margin-right:8px;
      font-size:14px;
      line-height:1;
    }

    @page{
      size:Letter;
      margin:8mm;
    }

    @media print{
      html, body{
        background:#fff !important;
      }

      .sst-toolbar,
      .add-row-trigger{
        display:none !important;
      }

      .sst-page{
        padding:0 !important;
        margin:0 !important;
      }

      .sst-paper{
        width:100% !important;
        min-height:auto !important;
        margin:0 !important;
        border:none !important;
        box-shadow:none !important;
        padding:0 !important;
      }

      .sst-table-wrap{
        overflow:visible !important;
      }

      .sst-input,
      .sst-select,
      .sst-input-line,
      .semester textarea{
        color:#000 !important;
      }
    }

    @media (max-width: 991px){
      .sst-page{
        padding:12px;
      }

      .sst-paper{
        width:100%;
        min-height:auto;
        padding:12px;
      }

      .sst-toolbar{
        padding:12px;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <div class="sst-toolbar">
    <h1 class="sst-toolbar-title">Consolidado General Presupuesto</h1>

    <div class="sst-toolbar-actions">
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
    </div>
  </div>

  <div class="sst-page">
    <div class="sst-paper">

      <table class="sst-table mb-2">
        <colgroup>
          <col style="width:18%;">
          <col style="width:64%;">
          <col style="width:18%;">
        </colgroup>
        <tr>
          <td rowspan="2">
            <div class="logo-box">
              <div>TU LOGO</div>
              <div>AQUÍ</div>
            </div>
          </td>

          <td>
            <div class="header-main">
              SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO
            </div>
          </td>

          <td rowspan="2">
            <div class="meta-box">
              <div class="meta-item">0</div>
              <div class="meta-item">AN-SST-03</div>
              <div class="meta-item">
                <input class="sst-input-line" type="text" value="XX/XX/2025">
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="header-main">CONSOLIDADO GENERAL PRESUPUESTO</div>
          </td>
        </tr>
      </table>

      <div class="note-box">
        Nota: diligencie los campos en naranja, los costos, gastos mensuales y el análisis tendencial.
      </div>

      <div class="sst-table-wrap">
        <table class="sst-table">
          <colgroup>
            <col style="width:26%">
            <col style="width:10%">
            <col style="width:10%">
            <col style="width:4%">
            <col style="width:10%">
            <col style="width:4%">
            <col span="12" style="width:3%">
          </colgroup>

          <thead>
            <tr class="sst-title">
              <th rowspan="2">ACTIVIDADES</th>
              <th rowspan="2">PRESUPUESTO PROYECTADO</th>
              <th rowspan="2">PRESUPUESTO EJECUTADO</th>
              <th rowspan="2">%</th>
              <th rowspan="2">PRESUPUESTO POR EJECUTAR</th>
              <th rowspan="2">%</th>
              <th colspan="12">20XX</th>
            </tr>
            <tr class="months">
              <th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th>
              <th>JUL</th><th>AGO</th><th>SEPT</th><th>OCT</th><th>NOV</th><th>DIC</th>
            </tr>
          </thead>

          <tbody id="budgetBody">
            <tr class="section-row">
              <td>Medicina Preventiva, del Trabajo y Otros</td>
              <td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>

            <tr data-row-type="editable" data-section="medicina">
              <td><input class="sst-input" type="text" value="Exámenes médicos (Emo, Paraclínicos y la…)"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr data-row-type="editable" data-section="medicina">
              <td><input class="sst-input" type="text" value="Vacunación"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr data-row-type="editable" data-section="medicina">
              <td><input class="sst-input" type="text" value="Compra medicamentos para el botiquín"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr class="add-row-trigger" data-section="medicina">
              <td colspan="18" class="add-row-cell">
                <button type="button" class="add-row-btn-inline" onclick="addRowToSection('medicina', this)">
                  <span class="plus">+</span> Agregar fila en Medicina Preventiva
                </button>
              </td>
            </tr>

            <tr class="section-row">
              <td>Higiene Industrial y manejo ambiental</td>
              <td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>

            <tr data-row-type="editable" data-section="higiene">
              <td><input class="sst-input" type="text" value="Mediciones de Higiene"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr data-row-type="editable" data-section="higiene">
              <td><input class="sst-input" type="text" value="Punto Ecológico"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr data-row-type="editable" data-section="higiene">
              <td><input class="sst-input" type="text" value="Tableros informativos"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr class="add-row-trigger" data-section="higiene">
              <td colspan="18" class="add-row-cell">
                <button type="button" class="add-row-btn-inline" onclick="addRowToSection('higiene', this)">
                  <span class="plus">+</span> Agregar fila en Higiene Industrial
                </button>
              </td>
            </tr>

            <tr class="section-row">
              <td>Seguridad Industrial</td>
              <td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>

            <tr data-row-type="editable" data-section="seguridad">
              <td><input class="sst-input" type="text" value="Compra Dotación Personal y EPP"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr data-row-type="editable" data-section="seguridad">
              <td><input class="sst-input" type="text" value="Compra y mantenimiento de extintores"></td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
              <td><input class="sst-input center" type="text"></td>
            </tr>

            <tr class="add-row-trigger" data-section="seguridad">
              <td colspan="18" class="add-row-cell">
                <button type="button" class="add-row-btn-inline" onclick="addRowToSection('seguridad', this)">
                  <span class="plus">+</span> Agregar fila en Seguridad Industrial
                </button>
              </td>
            </tr>
          </tbody>

          <tfoot>
            <tr class="total-row">
              <td>Total</td>
              <td><input class="sst-input right orange" type="text" placeholder="0"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td><input class="sst-input right" type="text" placeholder="0"></td>
              <td><input class="sst-input center" type="text" placeholder="0%"></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
              <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="signature-label">FIRMA DEL REPRESENTANTE LEGAL</div>

      <div class="block-title">ANALISIS TENDENCIAL</div>

      <div class="sst-table-wrap">
        <table class="sst-table">
          <colgroup>
            <col style="width:22%">
            <col span="12" style="width:6.5%">
          </colgroup>

          <thead>
            <tr class="months">
              <th class="center">MES</th>
              <th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th>
              <th>JUL</th><th>AGO</th><th>SEPT</th><th>OCT</th><th>NOV</th><th>DIC</th>
            </tr>
          </thead>

          <tbody id="analysisBody">
            <tr>
              <td class="bold center">TOTAL</td>
              <td><input class="sst-input center orange js-month" data-month="ENE" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="FEB" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="MAR" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="ABR" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="MAY" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="JUN" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="JUL" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="AGO" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="SEPT" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="OCT" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="NOV" type="text" placeholder="0"></td>
              <td><input class="sst-input center orange js-month" data-month="DIC" type="text" placeholder="0"></td>
            </tr>
            <tr>
              <td class="bold center">%</td>
              <td><input class="sst-input center orange js-pct" data-month="ENE" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="FEB" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="MAR" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="ABR" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="MAY" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="JUN" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="JUL" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="AGO" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="SEPT" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="OCT" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="NOV" type="text" placeholder="0%"></td>
              <td><input class="sst-input center orange js-pct" data-month="DIC" type="text" placeholder="0%"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="block-title">ANALISIS TENDENCIAL</div>

      <div class="row g-3 mt-1">
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
            <textarea placeholder="Escriba el análisis del primer semestre..."></textarea>
          </div>

          <div class="semester">
            <div class="t">II SEMESTRE</div>
            <textarea placeholder="Escriba el análisis del segundo semestre..."></textarea>
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

    function getMonthInputs(){
      return Array.from(document.querySelectorAll(".js-month"));
    }

    function getPctInputs(){
      return Array.from(document.querySelectorAll(".js-pct"));
    }

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
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (v) => Number(v).toLocaleString("es-CO")
            }
          }
        }
      }
    });

    function updateChartAndPercents(){
      const monthInputs = getMonthInputs();
      const pctInputs = getPctInputs();

      const totalRow = monthInputs.filter(inp => inp.closest("tr")?.children[0]?.textContent.trim() === "TOTAL");

      const values = monthOrder.map(m => {
        const inp = totalRow.find(x => x.dataset.month === m);
        return parseMoney(inp?.value);
      });

      const totalYear = values.reduce((a,b)=>a+b,0);

      monthOrder.forEach((m, idx) => {
        const pct = totalYear === 0 ? 0 : (values[idx] / totalYear) * 100;
        const pctInp = pctInputs.find(x => x.dataset.month === m && x.closest("tr")?.children[0]?.textContent.trim() === "%");
        if(pctInp){
          pctInp.value = totalYear === 0 ? "0%" : formatPct(pct);
        }
      });

      chart.data.datasets[0].data = values;
      chart.update();
    }

    function createSectionRow(section){
      const tr = document.createElement("tr");
      tr.setAttribute("data-row-type", "editable");
      tr.setAttribute("data-section", section);
      tr.innerHTML = `
        <td><input class="sst-input" type="text" placeholder="Nueva actividad"></td>
        <td><input class="sst-input right orange" type="text" placeholder="0"></td>
        <td><input class="sst-input right" type="text" placeholder="0"></td>
        <td><input class="sst-input center" type="text" placeholder="0%"></td>
        <td><input class="sst-input right" type="text" placeholder="0"></td>
        <td><input class="sst-input center" type="text" placeholder="0%"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
        <td><input class="sst-input center" type="text"></td>
      `;
      return tr;
    }

    function addRowToSection(section, btn){
      const triggerRow = btn.closest("tr");
      const newRow = createSectionRow(section);
      triggerRow.parentNode.insertBefore(newRow, triggerRow);
    }

    document.querySelectorAll(".js-month").forEach(inp => {
      inp.addEventListener("input", updateChartAndPercents);
      inp.addEventListener("change", updateChartAndPercents);
    });

    updateChartAndPercents();
  </script>

</body>
</html>