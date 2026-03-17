<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

function e($v){
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>2.9.1 - Procedimiento para Compras en SST</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --blue:#1f5fa8;
      --blue-soft:#eef4ff;
      --line:#111;
      --text:#111827;
      --muted:#667085;
      --bg:#f3f6fb;
    }

    body{
      margin:0;
      font-family: Arial, Helvetica, sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    .page-wrap{
      max-width:1100px;
      margin:24px auto 60px;
      padding:0 12px;
    }

    .actions-bar{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      margin-bottom:16px;
    }

    .btn-sst,
    .btn-sst-outline{
      display:inline-block;
      padding:10px 16px;
      border-radius:10px;
      font-weight:600;
      text-decoration:none;
      transition:.2s ease;
    }

    .btn-sst{
      background:var(--blue);
      color:#fff;
      border:none;
    }

    .btn-sst-outline{
      background:#fff;
      color:var(--blue);
      border:1px solid var(--blue);
    }

    .sheet{
      background:#fff;
      border:1px solid #d9e2ef;
      border-radius:14px;
      overflow:hidden;
      box-shadow:0 8px 24px rgba(15, 23, 42, .08);
    }

    .sheet-header{
      padding:18px 20px 10px;
      border-bottom:2px solid var(--blue);
      background:linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
    }

    .top-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .top-table td{
      border:1px solid var(--line);
      padding:8px;
      vertical-align:middle;
      font-size:13px;
    }

    .logo-box{
      height:90px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:700;
      color:#666;
      background:#fafafa;
    }

    .title-main{
      margin:0;
      text-align:center;
      font-size:18px;
      line-height:1.35;
      font-weight:700;
      text-transform:uppercase;
    }

    .doc-name{
      font-weight:700;
      text-align:center;
      font-size:14px;
      text-transform:uppercase;
    }

    .meta-input,
    .line-input,
    .form-control{
      border:1px solid #cfd8e3;
      border-radius:8px;
      font-size:14px;
      box-shadow:none !important;
    }

    .meta-input,
    .line-input{
      width:100%;
      padding:8px 10px;
      outline:none;
      background:#fff;
    }

    .meta-input:focus,
    .line-input:focus,
    .form-control:focus{
      border-color:#6ea8fe;
      box-shadow:0 0 0 .15rem rgba(13,110,253,.12) !important;
    }

    .doc-body{
      padding:22px 20px 28px;
    }

    .cover-card{
      border:1px solid #dbe5f0;
      border-radius:14px;
      padding:24px 18px;
      margin-bottom:22px;
      background:#fcfdff;
    }

    .cover-logo{
      width:180px;
      height:120px;
      margin:0 auto 18px;
      border:1px dashed #aab7c7;
      border-radius:12px;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#667085;
      font-weight:700;
      background:#fff;
    }

    .cover-title{
      text-align:center;
      font-size:20px;
      font-weight:700;
      text-transform:uppercase;
      line-height:1.4;
      margin-bottom:18px;
    }

    .section-card{
      border:1px solid #d9e2ef;
      border-radius:14px;
      overflow:hidden;
      background:#fff;
      margin-bottom:18px;
    }

    .section-title{
      background:var(--blue);
      color:#fff;
      padding:10px 14px;
      font-weight:700;
      text-transform:uppercase;
      font-size:14px;
      letter-spacing:.3px;
    }

    .section-body{
      padding:14px;
    }

    .muted-label{
      display:block;
      margin-bottom:4px;
      font-size:12px;
      color:var(--muted);
      font-weight:600;
    }

    .info-box{
      border:1px solid #e2e8f0;
      border-radius:12px;
      padding:14px;
      background:#fbfdff;
      margin-bottom:14px;
    }

    .info-box:last-child{
      margin-bottom:0;
    }

    .info-box h6{
      margin:0 0 10px;
      font-size:14px;
      color:var(--blue);
      font-weight:700;
      text-transform:uppercase;
    }

    .table-clean{
      width:100%;
      border-collapse:collapse;
    }

    .table-clean th,
    .table-clean td{
      border:1px solid #111;
      padding:10px;
      vertical-align:top;
      font-size:14px;
    }

    .table-clean th{
      background:var(--blue-soft);
      text-align:center;
    }

    .glossary-item{
      border:1px solid #e2e8f0;
      border-radius:12px;
      background:#fbfdff;
      padding:12px;
      margin-bottom:10px;
    }

    .glossary-item strong{
      display:block;
      color:var(--blue);
      margin-bottom:5px;
    }

    textarea.form-control{
      resize:vertical;
      min-height:90px;
    }

    @media print{
      body{
        background:#fff;
      }
      .page-wrap{
        max-width:100%;
        margin:0;
        padding:0;
      }
      .actions-bar{
        display:none !important;
      }
      .sheet{
        border:none;
        box-shadow:none;
        border-radius:0;
      }
    }
  </style>
</head>
<body>

<div class="page-wrap">
  <div class="actions-bar">
    <a href="../planear.php" class="btn-sst-outline">← Volver</a>
    <button type="button" class="btn-sst" onclick="window.print()">Imprimir / Guardar PDF</button>
  </div>

  <div class="sheet">

    <div class="sheet-header">
      <table class="top-table">
        <tr>
          <td rowspan="3" style="width:18%;">
            <div class="logo-box">LOGO</div>
          </td>
          <td rowspan="3" style="width:52%;">
            <h1 class="title-main">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</h1>
          </td>
          <td style="width:15%; font-weight:700;">Versión</td>
          <td style="width:15%;"><input type="text" class="meta-input" value="0"></td>
        </tr>
        <tr>
          <td style="font-weight:700;">Código</td>
          <td><input type="text" class="meta-input" value="AN-XX-SST-23"></td>
        </tr>
        <tr>
          <td style="font-weight:700;">Fecha</td>
          <td><input type="text" class="meta-input" placeholder="XX/XX/20XX"></td>
        </tr>
        <tr>
          <td colspan="4" class="doc-name">PROCEDIMIENTO PARA COMPRAS EN SST</td>
        </tr>
      </table>
    </div>

    <div class="doc-body">

      <div class="cover-card">
        <div class="cover-logo">LOGO</div>

        <div class="cover-title">
          PROCEDIMIENTO PARA COMPRAS EN SST
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="muted-label">Versión</label>
            <input type="text" class="line-input" value="0">
          </div>
          <div class="col-md-6">
            <label class="muted-label">Fecha</label>
            <input type="text" class="line-input" placeholder="DD/MM/AAAA">
          </div>
          <div class="col-12">
            <label class="muted-label">Nombre de la empresa</label>
            <input type="text" class="line-input" placeholder="NOMBRE DE LA EMPRESA">
          </div>
        </div>
      </div>

      <div class="section-card">
        <div class="section-title">Objetivo</div>
        <div class="section-body">
          <textarea class="form-control" rows="4">Constatar que la adquisición de nuevos productos y servicios de interés en el Sistema de Gestión de Seguridad y Salud en el Trabajo, no representen un riesgo para la población trabajadora.</textarea>
        </div>
      </div>

      <div class="section-card">
        <div class="section-title">Alcance</div>
        <div class="section-body">
          <textarea class="form-control" rows="4">El alcance de este procedimiento tiene cobertura en todos los procesos que requieran la adquisición de productos, bienes y servicios.</textarea>
        </div>
      </div>

      <div class="section-card">
        <div class="section-title">Aspectos generales</div>
        <div class="section-body">
          <div class="info-box">
            <textarea class="form-control" rows="7">A través de este procedimiento se establecen los requisitos en materia de la adquisición de productos, bienes y servicios y los requerimientos en Seguridad y Salud en la adquisición de estos.

Comunicar a los proveedores los requisitos en materia de seguridad y Salud establecidos en la empresa.

Se identifican los peligros y valoran los riesgos que conlleva la adquisición de nuevos bienes y servicios y su impacto en los grupos de interés y establecer las medidas preventivas y correctivas frente a la implementación de nuevos productos, bienes y servicios.</textarea>
          </div>
        </div>
      </div>

      <div class="section-card">
        <div class="section-title">Glosario</div>
        <div class="section-body">

          <div class="glossary-item">
            <strong>Compras</strong>
            <textarea class="form-control" rows="2">Proceso de adquisición de bienes y servicios.</textarea>
          </div>

          <div class="glossary-item">
            <strong>Alta gerencia</strong>
            <textarea class="form-control" rows="2">Persona o grupo de personas que gestionan y determinan lineamientos organizacionales.</textarea>
          </div>

          <div class="glossary-item">
            <strong>Identificación de peligros y valoración de riesgos</strong>
            <textarea class="form-control" rows="2">Método para identificar un peligro y establecer las características de este.</textarea>
          </div>

          <div class="glossary-item">
            <strong>Peligro</strong>
            <textarea class="form-control" rows="2">Fuente, situación o acto con potencial de causar daño en la salud de los trabajadores, en los equipos o en las instalaciones.</textarea>
          </div>

          <div class="glossary-item mb-0">
            <strong>Seguridad y Salud en el Trabajo</strong>
            <textarea class="form-control" rows="3">Es la disciplina que trata de la prevención de las lesiones y las enfermedades causadas por las condiciones del trabajo, y de la protección y promoción de la salud de los trabajadores.</textarea>
          </div>

        </div>
      </div>

      <div class="section-card">
        <div class="section-title">Procedimiento</div>
        <div class="section-body">
          <div class="table-responsive">
            <table class="table-clean">
              <thead>
                <tr>
                  <th style="width:80px;">N°</th>
                  <th>Actividad</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-center">1</td>
                  <td>
                    <textarea class="form-control" rows="3">El área de SST debe establecer previamente los requerimientos en seguridad y salud en el trabajo para la adquisición de un nuevo bien o servicio.</textarea>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">2</td>
                  <td>
                    <textarea class="form-control" rows="3">Realizar la solicitud para compra o alquiler, mediante recurso escrito, formato o correo electrónico.</textarea>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">3</td>
                  <td>
                    <textarea class="form-control" rows="3">Se solicita con gerencia quien aprueba o no la compra de un nuevo bien o servicio.</textarea>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">4</td>
                  <td>
                    <textarea class="form-control" rows="3">Gerencia aprueba y es quien autoriza la orden de pedido en la nueva compra y se envía al proveedor.</textarea>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">5</td>
                  <td>
                    <textarea class="form-control" rows="4">Posterior a la adquisición se debe tener custodia de la información documentada de la nueva adquisición, factura, fichas técnicas, hojas de seguridad y/o tarjetas de emergencia, si aplica.</textarea>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">6</td>
                  <td>
                    <textarea class="form-control" rows="3">Se debe registrar la adquisición en la matriz de adquisición de productos y en la gestión del cambio de ser necesario.</textarea>
                  </td>
                </tr>
                <tr>
                  <td class="text-center">7</td>
                  <td>
                    <textarea class="form-control" rows="4">Revisar el impacto que ocasiona el nuevo bien y servicio en las partes interesadas y valorarlo mediante la identificación de peligros y valoración de los riesgos.</textarea>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="section-card mb-0">
        <div class="section-title">Documentos de referencia</div>
        <div class="section-body">
          <div class="info-box">
            <textarea class="form-control" rows="8">Formato de solicitud de compra.
Orden de pedido.
Factura.
Ficha técnica, hoja de seguridad, tarjeta de emergencia.
Matriz de adquisiciones.
Matriz de identificación de peligros y valoración de riesgos.</textarea>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

</body>
</html>