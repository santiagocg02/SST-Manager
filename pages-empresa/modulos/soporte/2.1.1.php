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
  <title>2.1.1 - Políticas de Seguridad y Salud en el Trabajo</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --blue:#1f5fa8;
      --line:#111;
      --soft:#eef3fb;
      --gray:#f7f7f7;
      --text:#1a1a1a;
    }

    body{
      background:#e9edf3;
      font-family: Arial, Helvetica, sans-serif;
      color:var(--text);
    }

    .wrap{
      max-width:1100px;
      margin:16px auto;
      padding:0 10px;
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
      box-shadow:0 10px 20px rgba(0,0,0,.08);
      padding:14px;
      margin-bottom:16px;
    }

    table.format{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      font-size:12px;
    }

    .format td, .format th{
      border:1px solid var(--line);
      padding:6px 8px;
      vertical-align:middle;
    }

    .title{
      font-weight:900;
      text-align:center;
      font-size:13px;
    }

    .subtitle{
      font-weight:900;
      text-align:center;
      font-size:12px;
    }

    .code-box{
      text-align:center;
      font-weight:900;
      font-size:12px;
    }

    .logo-box{
      border:2px dashed rgba(0,0,0,.35);
      height:68px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:900;
      color:rgba(0,0,0,.35);
      text-align:center;
      font-size:11px;
    }

    .sec-h{
      background:#d9e1ea;
      border:1px solid #b8c2cc;
      color:#10233c;
      font-weight:900;
      text-transform:uppercase;
      padding:10px 14px;
      font-size:15px;
      letter-spacing:.2px;
      margin-top:14px;
      margin-bottom:10px;
    }

    .policy-box{
      border:1px solid #1f1f1f;
      padding:18px 20px;
      min-height:200px;
      word-break:break-word;
      overflow-wrap:break-word;
    }

    .policy-title{
      text-align:center;
      font-weight:900;
      font-size:18px;
      margin-bottom:20px;
      text-transform:uppercase;
    }

    .policy-p{
      text-align:justify;
      font-size:14px;
      line-height:1.7;
      margin-bottom:14px;
    }

    .policy-list{
      margin:0;
      padding-left:22px;
    }

    .policy-list li{
      margin-bottom:12px;
      text-align:justify;
      line-height:1.6;
      font-size:14px;
    }

    .firma-wrap{
      margin-top:36px;
    }

    .firma-label{
      font-size:14px;
      margin-bottom:30px;
    }

    .firma-line{
      width:320px;
      max-width:100%;
      border-top:1px solid #111;
      margin-bottom:8px;
    }

    .firma-name,
    .firma-cc,
    .firma-role,
    .firma-date{
      font-size:14px;
      margin-bottom:4px;
    }

    .firma-date{
      margin-top:16px;
    }

    .in{
      width:100%;
      height:36px;
      border:1px solid #b8b8b8;
      border-radius:8px;
      background:#fafafa;
      padding:6px 10px;
      outline:none;
      box-sizing:border-box;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
    }

    .in.inline{
      display:inline-block;
      width:auto;
      min-width:160px;
      max-width:100%;
      vertical-align:middle;
    }

    .in.long{
      min-width:260px;
    }

    textarea.in{
      height:auto;
      min-height:80px;
      resize:none;
      white-space:normal;
      overflow:auto;
      text-overflow:initial;
    }

    .small-note{
      font-size:12px;
      color:#5c6670;
      margin-top:10px;
    }

    @media print{
      body{ background:#fff; }
      .toolbar{ display:none !important; }
      .sheet{
        box-shadow:none;
        margin-bottom:0;
        border:2px solid #000;
      }
    }
  </style>
</head>
<body>
  <div class="wrap">

    <div class="toolbar">
      <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Atrás</a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
    </div>

    <div class="sheet">

      <table class="format mb-3">
        <colgroup>
          <col style="width:18%">
          <col style="width:52%">
          <col style="width:15%">
          <col style="width:15%">
        </colgroup>
        <tr>
          <td rowspan="3">
            <div class="logo-box">LOGO EMPRESA</div>
          </td>
          <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
          <td><strong>Versión:</strong> 0</td>
          <td><strong>Fecha:</strong><br>XX/XX/20XX</td>
        </tr>
        <tr>
          <td class="subtitle">POLÍTICAS DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
          <td class="code-box" colspan="2">2.1.1</td>
        </tr>
        <tr>
          <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
        </tr>
      </table>

      <div class="sec-h">Política SG SST</div>
      <div class="policy-box">
        <div class="policy-title">Política SG SST</div>

        <p class="policy-p">
          La empresa
          <input type="text" class="in inline" placeholder="Nombre de la empresa">
          dedicada a
          <input type="text" class="in inline long" placeholder="Actividad económica principal">
        </p>

        <p class="policy-p">
          Declara su compromiso con la legislación vigente en materia de seguridad y salud en el trabajo.
        </p>

        <p class="policy-p">
          Considera que todo accidente de trabajo y enfermedad laboral puede prevenirse, por tal motivo, se compromete al mejoramiento de las condiciones de trabajo y la protección de la integridad física, mental y social de sus trabajadores, colaboradores, contratistas, visitantes y partes interesadas.
        </p>

        <p class="policy-p">
          Garantiza una oportuna identificación, evaluación, control y/o eliminación de los riesgos que pueden afectar la salud y calidad de vida de los trabajadores; así como el mejoramiento continuo en su gestión por la prevención de riesgos laborales.
        </p>

        <p class="policy-p">
          Asigna los recursos humanos, físicos y financieros requeridos para el normal funcionamiento del sistema de gestión.
        </p>

        <p class="policy-p">
          Para el éxito de la gestión de la seguridad y salud en el trabajo, se requiere del compromiso de la gerencia y participación de todos, reflejando el cumplimiento de las normas y procedimientos establecidos por la legislación colombiana en materia de prevención.
        </p>

        <div class="firma-wrap">
          <div class="firma-label">Firma del representante legal,</div>
          <div class="firma-line"></div>

          <div class="mb-2">
            <input type="text" class="in" placeholder="Nombre del representante legal">
          </div>

          <div class="mb-2">
            <input type="text" class="in" placeholder="C.C. del representante legal">
          </div>

          <div class="firma-role">Representante legal</div>

          <div class="firma-date">
            Fecha de emisión:
            <input type="text" class="in inline" placeholder="Día / mes / año">
          </div>
        </div>
      </div>

      <div class="sec-h">Política de prevención al consumo de alcohol, tabaco y sustancias psicoactivas</div>
      <div class="policy-box">
        <div class="policy-title">Política de prevención al consumo de alcohol, tabaco y sustancias psicoactivas</div>

        <p class="policy-p">
          La empresa
          <input type="text" class="in inline" placeholder="Nombre de la empresa">,
          dedicada a
          <input type="text" class="in inline long" placeholder="Actividad económica principal">.
          Se compromete al desarrollo del sistema de gestión de la seguridad y salud en el trabajo, por lo cual:
        </p>

        <ol class="policy-list">
          <li>Acatará la legislación colombiana respecto al consumo de alcohol, tabaco y sustancias psicoactivas en el lugar de trabajo.</li>
          <li>Está prohibido ingresar, regalar, vender, mantener y consumir bebidas alcohólicas o sustancias psicoactivas en el lugar de trabajo.</li>
          <li>Está prohibido llegar a trabajar bajo los efectos del alcohol, narcóticos o cualquier otra droga enervante, solo cuando afecte directamente el desempeño laboral del trabajador y/o bienestar común de los funcionarios de la organización.</li>
          <li>Está prohibido fumar dentro de las instalaciones de la empresa.</li>
          <li>Promoveremos con nuestros proveedores y contratistas la adopción de políticas frente al consumo de alcohol, tabaco y sustancias psicoactivas congruentes con la nuestra.</li>
          <li>Revisaremos cada año la política y la actualizaremos de ser necesario.</li>
        </ol>

        <div class="firma-wrap">
          <div class="firma-label">Firma del representante legal,</div>
          <div class="firma-line"></div>

          <div class="mb-2">
            <input type="text" class="in" placeholder="Nombre del representante legal">
          </div>

          <div class="mb-2">
            <input type="text" class="in" placeholder="C.C. del representante legal">
          </div>

          <div class="firma-role">Representante legal</div>

          <div class="firma-date">
            Fecha de emisión:
            <input type="text" class="in inline" placeholder="Día / mes / año">
          </div>
        </div>
      </div>

      <div class="small-note">
        Formato 2.1.1 listo para diligenciar e imprimir.
      </div>

    </div>
  </div>
</body>
</html>