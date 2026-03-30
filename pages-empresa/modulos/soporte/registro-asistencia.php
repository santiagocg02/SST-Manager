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
  <title>RE-SST-01 | Registro de Asistencia</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#9fb4d9;
      --blue-soft:#dfe8f6;
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
      max-width:1100px;
      margin:0 auto;
      background:var(--paper);
      border:1px solid #d7dee8;
      box-shadow:0 10px 30px rgba(0,0,0,.08);
      overflow:hidden;
    }

    .header-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .header-table td{
      border:1px solid var(--line);
      vertical-align:middle;
      padding:0;
    }

    .logo-cell{
      width:28%;
      height:106px;
      text-align:center;
      background:#fafafa;
    }

    .logo-box{
      width:102px;
      height:64px;
      margin:0 auto;
      border:2px dashed #c9c9c9;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      color:#9a9a9a;
      font-weight:800;
      line-height:1;
    }

    .logo-box span:first-child{
      font-size:15px;
    }

    .logo-box span:last-child{
      font-size:12px;
      margin-top:4px;
    }

    .title-cell{
      width:52%;
    }

    .title-main,
    .title-sub{
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
      font-weight:800;
      padding:10px 12px;
    }

    .title-main{
      min-height:70px;
      border-bottom:1px solid var(--line);
      font-size:16px;
      text-transform:uppercase;
    }

    .title-sub{
      min-height:35px;
      font-size:14px;
      text-transform:uppercase;
      font-weight:400;
    }

    .meta-cell{
      width:20%;
    }

    .meta-box{
      display:flex;
      flex-direction:column;
      min-height:106px;
    }

    .meta-box div{
      flex:1;
      border-bottom:1px solid var(--line);
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:700;
      padding:8px;
      text-align:center;
    }

    .meta-box div:last-child{
      border-bottom:none;
    }

    .content{
      padding:14px 0 0;
    }

    .info-block{
      padding:0 0 10px;
      border-bottom:1px solid var(--line);
    }

    .info-row{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      padding:8px 14px;
    }

    .info-label{
      font-size:16px;
      font-weight:400;
      white-space:nowrap;
    }

    .line-input{
      border:none;
      border-bottom:1px solid #444;
      background:transparent;
      outline:none;
      min-width:140px;
      padding:2px 4px;
      font-size:15px;
    }

    .line-input.sm{ width:130px; }
    .line-input.md{ width:220px; }
    .line-input.lg{ width:520px; max-width:100%; }

    .check-group{
      display:flex;
      align-items:center;
      gap:10px;
      margin-right:18px;
    }

    .check-group label{
      font-size:16px;
      margin:0;
    }

    .check-input{
      width:18px;
      height:18px;
      accent-color:#375f9c;
    }

    .attendance-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .attendance-table th,
    .attendance-table td{
      border:1px dotted #555;
      padding:0;
      height:28px;
      font-size:14px;
    }

    .attendance-table thead th{
      background:var(--blue);
      border:1px solid var(--line);
      text-align:center;
      font-weight:800;
      height:26px;
      padding:4px 6px;
    }

    .col-no{ width:8%; text-align:center; }
    .col-name{ width:37%; }
    .col-id{ width:19%; }
    .col-role{ width:16%; }
    .col-sign{ width:20%; }

    .num-cell{
      text-align:center;
      font-size:15px;
    }

    .cell-input{
      width:100%;
      height:100%;
      border:none;
      outline:none;
      background:transparent;
      padding:4px 8px;
      font-size:14px;
    }

    .footer-space{
      height:10px;
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
        border:none;
        box-shadow:none;
        margin:0;
      }

      .line-input,
      .cell-input{
        -webkit-print-color-adjust:exact;
        print-color-adjust:exact;
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

      .header-table,
      .attendance-table{
        min-width:980px;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <div class="topbar">
    <h1>Registro de Asistencia</h1>

    <div class="actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
      <button type="button" class="btn btn-success btn-sm" onclick="agregarFila()">Agregar fila</button>
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
    </div>
  </div>

  <div class="page-wrap">
    <div class="paper">

      <table class="header-table">
        <tr>
          <td class="logo-cell">
            <div class="logo-box">
              <span>TU LOGO</span>
              <span>AQUÍ</span>
            </div>
          </td>

          <td class="title-cell">
            <div class="title-main">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</div>
            <div class="title-sub">REGISTRO DE ASISTENCIA</div>
          </td>

          <td class="meta-cell">
            <div class="meta-box">
              <div>0</div>
              <div>RE-SST-01</div>
              <div>XX/XX/2025</div>
            </div>
          </td>
        </tr>
      </table>

      <div class="content">
        <div class="info-block">
          <div class="info-row">
            <span class="info-label">FECHA:</span>
            <input type="date" class="line-input sm">

            <span class="info-label">HORA DE INICIO</span>
            <input type="time" class="line-input sm">

            <span class="info-label">HORA FINALIZACIÓN</span>
            <input type="time" class="line-input sm">
          </div>

          <div class="info-row">
            <span class="info-label">TIPO</span>

            <div class="check-group">
              <label for="capacitacion">CAPACITACIÓN</label>
              <input id="capacitacion" class="check-input" type="checkbox">
            </div>

            <div class="check-group">
              <label for="reunion">REUNIÓN</label>
              <input id="reunion" class="check-input" type="checkbox">
            </div>
          </div>

          <div class="info-row">
            <span class="info-label">TEMA DE CAPACITACIÓN</span>
            <input type="text" class="line-input lg">
          </div>
        </div>

        <table class="attendance-table">
          <thead>
            <tr>
              <th class="col-no">No.</th>
              <th class="col-name">NOMBRE</th>
              <th class="col-id">NUMERO DE CEDULA</th>
              <th class="col-role">CARGO</th>
              <th class="col-sign">FIRMA</th>
            </tr>
          </thead>
          <tbody id="attendance-body">
            <?php for($i=1; $i<=10; $i++): ?>
              <tr>
                <td class="num-cell"><?php echo $i; ?></td>
                <td><input type="text" class="cell-input"></td>
                <td><input type="text" class="cell-input"></td>
                <td><input type="text" class="cell-input"></td>
                <td><input type="text" class="cell-input"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>

        <div class="footer-space"></div>
      </div>
    </div>
  </div>

  <script>
    function agregarFila(){
      const tbody = document.getElementById('attendance-body');
      const filas = tbody.querySelectorAll('tr').length;
      const tr = document.createElement('tr');

      tr.innerHTML = `
        <td class="num-cell">${filas + 1}</td>
        <td><input type="text" class="cell-input"></td>
        <td><input type="text" class="cell-input"></td>
        <td><input type="text" class="cell-input"></td>
        <td><input type="text" class="cell-input"></td>
      `;

      tbody.appendChild(tr);
    }
  </script>

</body>
</html>