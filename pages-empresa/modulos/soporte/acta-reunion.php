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
  <title>RE-SST-01 | Acta de Reunión</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#9fb4d9;
      --bg:#eef3f9;
      --paper:#fff;
      --text:#111;
    }

    *{
      box-sizing:border-box;
    }

    body{
      margin:0;
      background:var(--bg);
      font-family:Arial, Helvetica, sans-serif;
      color:var(--text);
    }

    .topbar{
      position:sticky;
      top:0;
      z-index:100;
      background:#dde7f5;
      border-bottom:1px solid #c8d3e2;
      padding:14px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    .topbar h1{
      margin:0;
      font-size:15px;
      font-weight:800;
      color:#213b67;
    }

    .actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }

    .page-wrap{
      padding:24px;
    }

    .paper{
      max-width:1050px;
      margin:0 auto;
      background:var(--paper);
      border:1px solid #d7dee8;
      box-shadow:0 10px 30px rgba(0,0,0,.08);
      overflow:hidden;
    }

    .doc-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .doc-table td,
    .doc-table th{
      border:1px solid var(--line);
      padding:6px 8px;
      vertical-align:middle;
    }

    .logo-cell{
      width:18%;
      text-align:center;
      background:#fafafa;
      height:84px;
    }

    .logo-box{
      width:96px;
      height:64px;
      margin:0 auto;
      border:2px dashed #c7c7c7;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
      color:#9a9a9a;
      font-weight:800;
      line-height:1;
    }

    .logo-box .small{
      font-size:12px;
    }

    .logo-box .big{
      font-size:15px;
      margin-top:4px;
    }

    .title-cell{
      width:62%;
      padding:0 !important;
    }

    .title-main,
    .title-sub{
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
    }

    .title-main{
      min-height:47px;
      border-bottom:1px solid var(--line);
      font-size:16px;
      font-weight:800;
      text-transform:uppercase;
      padding:8px 10px;
    }

    .title-sub{
      min-height:37px;
      font-size:14px;
      font-weight:800;
      text-transform:uppercase;
      padding:8px 10px;
    }

    .meta-cell{
      width:20%;
      padding:0 !important;
    }

    .meta-box{
      display:flex;
      flex-direction:column;
      min-height:84px;
    }

    .meta-box div{
      flex:1;
      display:flex;
      align-items:center;
      justify-content:center;
      border-bottom:1px solid var(--line);
      font-weight:700;
      font-size:14px;
      text-align:center;
      padding:6px;
    }

    .meta-box div:last-child{
      border-bottom:none;
    }

    .section-title{
      background:var(--blue);
      text-align:center;
      font-weight:800;
      font-size:13px;
      text-transform:uppercase;
    }

    .subhead{
      text-align:center;
      font-weight:400;
      background:#fff;
    }

    .num-col{
      width:8%;
      text-align:center;
    }

    .name-col{
      width:42%;
    }

    .cargo-col{
      width:18%;
    }

    .firma-col{
      width:18%;
    }

    .concl-num{
      width:8%;
      text-align:center;
    }

    .concl-task{
      width:42%;
    }

    .concl-resp{
      width:18%;
    }

    .concl-date{
      width:20%;
    }

    .input-cell,
    .textarea-cell{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:14px;
      padding:2px 4px;
    }

    .textarea-cell{
      resize:none;
      height:100%;
      min-height:38px;
    }

    .center{
      text-align:center;
    }

    .h-34 td{
      height:34px;
    }

    .h-38 td{
      height:38px;
    }

    .h-40 td{
      height:40px;
    }

    .h-42 td{
      height:42px;
    }

    .discussion-row td{
      height:41px;
    }

    .conclusion-row td{
      height:68px;
    }

    .label-cell{
      font-weight:400;
      background:#fff;
    }

    .print-page-break{
      page-break-before:always;
    }

    @media print{
      body{
        background:#fff;
      }

      .topbar{
        display:none !important;
      }

      .page-wrap{
        padding:0;
      }

      .paper{
        max-width:100%;
        margin:0;
        border:none;
        box-shadow:none;
      }

      @page{
        size:letter;
        margin:10mm;
      }
    }

    @media (max-width: 900px){
      .page-wrap{
        padding:12px;
      }

      .paper{
        overflow-x:auto;
      }

      .doc-table{
        min-width:980px;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <div class="topbar">
    <h1>Acta de Reunión</h1>

    <div class="actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
      <button type="button" class="btn btn-success btn-sm" onclick="agregarParticipante()">Agregar participante</button>
      <button type="button" class="btn btn-success btn-sm" onclick="agregarPunto()">Agregar punto</button>
      <button type="button" class="btn btn-success btn-sm" onclick="agregarConclusion()">Agregar conclusión</button>
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
    </div>
  </div>

  <div class="page-wrap">
    <div class="paper">
      <table class="doc-table">

        <tr>
          <td class="logo-cell">
            <div class="logo-box">
              <div class="small">TU LOGO</div>
              <div class="big">AQUÍ</div>
            </div>
          </td>

          <td class="title-cell">
            <div class="title-main">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</div>
            <div class="title-sub">ACTA DE REUNION</div>
          </td>

          <td class="meta-cell">
            <div class="meta-box">
              <div>0</div>
              <div>RE-SST-01</div>
              <div>XX/XX/2025</div>
            </div>
          </td>
        </tr>

        <tr class="h-38">
          <td colspan="3"></td>
        </tr>

        <tr class="h-34">
          <td colspan="2" class="label-cell">
            Comité o Grupo:
            <input type="text" class="input-cell">
          </td>
          <td class="label-cell">
            Acta No
            <input type="text" class="input-cell">
          </td>
        </tr>

        <tr class="h-34">
          <td colspan="2" class="label-cell">
            Citada por:
            <input type="text" class="input-cell">
          </td>
          <td class="label-cell">
            Fecha:
            <input type="date" class="input-cell">
          </td>
        </tr>

        <tr class="h-34">
          <td colspan="2" class="label-cell">
            Coordinador:
            <input type="text" class="input-cell">
          </td>
          <td class="label-cell">
            Hora inicio:
            <input type="time" class="input-cell" style="width:120px; display:inline-block;">
            &nbsp;&nbsp;Fin:
            <input type="time" class="input-cell" style="width:120px; display:inline-block;">
          </td>
        </tr>

        <tr class="h-38">
          <td colspan="3"></td>
        </tr>

        <tr>
          <td colspan="3" class="section-title">PARTICIPANTES</td>
        </tr>

        <tr>
          <td colspan="3" style="padding:0;">
            <table class="doc-table" style="border:none;">
              <tr>
                <td class="subhead num-col">No.</td>
                <td class="subhead name-col">Nombre y Apellidos</td>
                <td class="subhead cargo-col">Cargo</td>
                <td class="subhead firma-col">Firma</td>
              </tr>
              <tbody id="participantes-body">
                <tr class="h-34">
                  <td class="center">1</td>
                  <td><input type="text" class="input-cell"></td>
                  <td><input type="text" class="input-cell"></td>
                  <td><input type="text" class="input-cell"></td>
                </tr>
                <tr class="h-34">
                  <td class="center">2</td>
                  <td><input type="text" class="input-cell"></td>
                  <td><input type="text" class="input-cell"></td>
                  <td><input type="text" class="input-cell"></td>
                </tr>
                <tr class="h-34">
                  <td class="center">3</td>
                  <td><input type="text" class="input-cell"></td>
                  <td><input type="text" class="input-cell"></td>
                  <td><input type="text" class="input-cell"></td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>
          <td colspan="3" class="section-title">PUNTOS DE DISCUSION</td>
        </tr>

        <tr>
          <td colspan="3" style="padding:0;">
            <table class="doc-table" style="border:none;">
              <tbody id="puntos-body">
                <?php for($i=1; $i<=9; $i++): ?>
                  <tr class="discussion-row">
                    <td class="center" style="width:8%;"><?php echo $i; ?></td>
                    <td style="width:92%;"><input type="text" class="input-cell"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
        </tr>

        <tr>
          <td colspan="3" class="section-title">CONCLUSIONES</td>
        </tr>

        <tr>
          <td colspan="3" style="padding:0;">
            <table class="doc-table" style="border:none;">
              <tr>
                <td class="subhead concl-num">No</td>
                <td class="subhead concl-task">Tarea</td>
                <td class="subhead concl-resp">Responsable</td>
                <td class="subhead concl-date">Fecha de cumplimiento</td>
              </tr>
              <tbody id="conclusiones-body">
                <?php for($i=1; $i<=7; $i++): ?>
                  <tr class="conclusion-row">
                    <td class="center"><?php echo $i; ?></td>
                    <td><textarea class="textarea-cell"></textarea></td>
                    <td><input type="text" class="input-cell"></td>
                    <td><input type="date" class="input-cell"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </td>
        </tr>

      </table>
    </div>
  </div>

  <script>
    function agregarParticipante(){
      const tbody = document.getElementById('participantes-body');
      const numero = tbody.querySelectorAll('tr').length + 1;
      const tr = document.createElement('tr');
      tr.className = 'h-34';

      tr.innerHTML = `
        <td class="center">${numero}</td>
        <td><input type="text" class="input-cell"></td>
        <td><input type="text" class="input-cell"></td>
        <td><input type="text" class="input-cell"></td>
      `;

      tbody.appendChild(tr);
    }

    function agregarPunto(){
      const tbody = document.getElementById('puntos-body');
      const numero = tbody.querySelectorAll('tr').length + 1;
      const tr = document.createElement('tr');
      tr.className = 'discussion-row';

      tr.innerHTML = `
        <td class="center" style="width:8%;">${numero}</td>
        <td style="width:92%;"><input type="text" class="input-cell"></td>
      `;

      tbody.appendChild(tr);
    }

    function agregarConclusion(){
      const tbody = document.getElementById('conclusiones-body');
      const numero = tbody.querySelectorAll('tr').length + 1;
      const tr = document.createElement('tr');
      tr.className = 'conclusion-row';

      tr.innerHTML = `
        <td class="center">${numero}</td>
        <td><textarea class="textarea-cell"></textarea></td>
        <td><input type="text" class="input-cell"></td>
        <td><input type="date" class="input-cell"></td>
      `;

      tbody.appendChild(tr);
    }
  </script>

</body>
</html>