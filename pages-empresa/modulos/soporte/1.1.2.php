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
  <title>AC-SST-05 | Carta de Nombramiento</title>

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

    .sst-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .sst-table td,
    .sst-table th{
      border:1px solid var(--sst-border);
      padding:6px 8px;
      vertical-align:top;
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

    .sst-textarea,
    .editable-block,
    .editable-list{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      line-height:1.5;
      padding:0;
      resize:none;
      overflow:hidden;
      height:auto;
      min-height:unset;
      font-family:Arial, Helvetica, sans-serif;
      color:#111;
      display:block;
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

    .firma-wrapper{
      text-align:center;
      padding:18px 8px 10px;
    }

    .firma-line{
      width:70%;
      margin:26px auto 6px;
      border-top:1px solid #000;
    }

    .check-list{
      margin:0;
      padding:0;
      list-style:none;
    }

    .check-list li{
      display:flex;
      gap:10px;
      align-items:flex-start;
      padding:6px 0;
    }

    .check{
      font-weight:800;
      min-width:18px;
      text-align:center;
    }

    .empresa-title{
      font-size:18px;
      font-weight:800;
      text-transform:uppercase;
      margin-bottom:6px;
    }

    @page{
      size:Letter;
      margin:8mm;
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

      .sst-input,
      .sst-select,
      .sst-textarea,
      .sst-input-line,
      .editable-block,
      .editable-list{
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
</head>
<body>

  <div class="sst-toolbar">
    <h1 class="sst-toolbar-title">Carta de Nombramiento, Representante por la Alta Dirección</h1>

    <div class="sst-toolbar-actions">
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
    </div>
  </div>

  <div class="sst-page">
    <div class="sst-paper">

      <table class="sst-table">
        <tr>
          <td style="width:18%;">
            <div class="logo-box">
              <div>TU LOGO</div>
              <div>AQUÍ</div>
            </div>
          </td>

          <td colspan="3">
            <div class="header-main">
              SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO<br>
              CARTA DE NOMBRAMIENTO, REPRESENTANTE POR LA ALTA DIRECCIÓN
            </div>
          </td>

          <td style="width:18%;">
            <div class="meta-box">
              <div class="meta-item">0</div>
              <div class="meta-item">AC-SST-05</div>
              <div class="meta-item">
                <input class="sst-input-line" type="text" value="XX/XX/2025">
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">Identificación de la Empresa</td>
        </tr>

        <tr>
          <td colspan="5" class="center" style="padding:20px 10px;">
            <div class="empresa-title">EMPRESA</div>
            <input class="sst-input-line center" style="max-width:420px; display:inline-block;" type="text" value="Nombre de la empresa">
            <div class="bold" style="margin-top:18px; font-size:14px;">CERTIFICA:</div>
          </td>
        </tr>

        <tr>
          <td colspan="5">
            <ul class="check-list">
              <li>
                <div class="check">✓</div>
                <div style="width:100%;">
                  Que
                  <input class="sst-input-line" style="display:inline-block; width:220px;" type="text" value="NOMBRE COMPLETO">
                  identificado(a) con C.C.
                  <input class="sst-input-line" style="display:inline-block; width:150px;" type="text" value="XXXXXXXXXX">
                  ha sido designado(a) como representante de la Dirección para el Sistema de Gestión de Seguridad y Salud en el Trabajo, y se le han asignado las funciones, responsabilidades y autoridades para:
                </div>
              </li>

              <li>
                <div class="check">✓</div>
                <div>
                  Planear, organizar, dirigir, desarrollar y aplicar el SG-SST, y realizar por lo menos una vez al año su evaluación.
                </div>
              </li>

              <li>
                <div class="check">✓</div>
                <div>
                  Asegurar que los requisitos del SG-SST se establezcan, implementen y mantengan, de acuerdo con lo indicado en el Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normas asociadas.
                </div>
              </li>

              <li>
                <div class="check">✓</div>
                <div>
                  Informar a la alta dirección sobre el funcionamiento y los resultados del SG-SST.
                </div>
              </li>

              <li>
                <div class="check">✓</div>
                <div>
                  Promover la participación de todos los miembros de la empresa en la implementación del SG-SST.
                </div>
              </li>

              <li>
                <div class="check">✓</div>
                <div>
                  Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del SG-SST.
                </div>
              </li>

              <li>
                <div class="check">✓</div>
                <div>
                  Programar las auditorías internas necesarias para el mantenimiento y mejora continua del SG-SST.
                </div>
              </li>
            </ul>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">Firmas de Responsabilidad</td>
        </tr>

        <tr>
          <td colspan="2">
            <div class="firma-wrapper">
              <div class="firma-line"></div>
              <input class="sst-input center bold" type="text" value="Representante Legal">
            </div>
          </td>

          <td></td>

          <td colspan="2">
            <div class="firma-wrapper">
              <div class="firma-line"></div>
              <input class="sst-input center bold" type="text" value="Encargado SST">
            </div>
          </td>
        </tr>
      </table>

    </div>
  </div>

</body>
</html>