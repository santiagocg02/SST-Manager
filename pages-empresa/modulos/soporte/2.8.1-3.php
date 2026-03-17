<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AN-SST-29 | Matriz de condiciones inseguras / acciones correctivas y/o preventivas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#6f6f6f;
      --line-soft:#b8b8b8;
      --head:#d8dee8;
      --sheet:#ffffff;
      --top:#efefef;
      --green-bg:#cfe8d1;
      --green:#1d6b2f;
      --red-bg:#f4c7cf;
      --red:#b01515;
      --text:#111;
      --blue:#0d6efd;
      --blue-dark:#0b5ed7;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      background:#f3f4f6;
      font-family: Arial, Helvetica, sans-serif;
      color:var(--text);
    }

    .page-wrap{
      padding:18px;
    }

    .toolbar{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:14px;
      flex-wrap:wrap;
    }

    .toolbar-left,
    .toolbar-right{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }

    .btn-ui{
      border:1px solid var(--blue);
      background:var(--blue);
      color:#fff;
      padding:8px 14px;
      border-radius:8px;
      text-decoration:none;
      font-size:14px;
      font-weight:600;
      transition:.2s ease;
      display:inline-flex;
      align-items:center;
      gap:8px;
      cursor:pointer;
    }

    .btn-ui:hover{
      background:var(--blue-dark);
      border-color:var(--blue-dark);
      color:#fff;
    }

    .btn-ui.btn-light-ui{
      background:#fff;
      color:var(--blue);
    }

    .btn-ui.btn-light-ui:hover{
      background:#f5f9ff;
      color:var(--blue-dark);
    }

    .sheet-card{
      background:#fff;
      border:1px solid #d7d7d7;
      box-shadow:0 8px 24px rgba(0,0,0,.06);
      overflow:hidden;
    }

    .sheet-scroll{
      overflow:auto;
      width:100%;
    }

    .sheet{
      min-width:1550px;
      background:var(--sheet);
    }

    table.form-sheet{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      background:#fff;
    }

    .form-sheet td,
    .form-sheet th{
      border:1px solid var(--line-soft);
      padding:0;
      vertical-align:middle;
    }

    .top-gray{
      background:var(--top);
      text-align:center;
      font-size:12px;
      font-weight:700;
      height:34px;
      padding:6px 8px !important;
    }

    .top-title{
      background:var(--top);
      text-align:center;
      font-size:18px;
      font-weight:700;
      letter-spacing:.2px;
      height:48px;
      padding:8px 10px !important;
    }

    .top-subtitle{
      background:var(--top);
      text-align:center;
      font-size:15px;
      font-weight:700;
      height:40px;
      padding:8px 10px !important;
    }

    .logo-box{
      background:var(--top);
      height:122px;
      text-align:center;
      color:#b4b4b4;
      font-size:16px;
      font-weight:700;
      letter-spacing:.5px;
    }

    .logo-inner{
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
      line-height:1.05;
    }

    .logo-dashed{
      border:2px dashed #c9c9c9;
      padding:10px 16px;
    }

    .counter-wrap{
      background:var(--top);
      padding:12px 16px !important;
      height:94px;
    }

    .counter-box{
      display:flex;
      justify-content:flex-end;
      align-items:center;
      gap:12px;
      margin:6px 0;
      font-size:13px;
      font-weight:700;
      font-style:italic;
    }

    .counter-num{
      width:72px;
      height:38px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:28px;
      font-weight:800;
      font-style:normal;
      background:#fff;
    }

    .counter-num.red{
      color:#ff2a2a;
      border:2px solid #ff6d6d;
    }

    .counter-num.green{
      color:#4d7d2e;
      border:2px solid #96bf83;
    }

    .col-head{
      background:var(--head);
      text-align:center;
      font-size:12px;
      font-weight:700;
      text-transform:uppercase;
      line-height:1.15;
      height:62px;
      padding:8px 6px !important;
    }

    .cell{
      height:58px;
      background:#fff;
      position:relative;
    }

    .cell input,
    .cell textarea,
    .cell select{
      width:100%;
      height:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      padding:8px 10px;
      color:#222;
    }

    .cell textarea{
      resize:none;
      padding-top:10px;
    }

    .cell.center input,
    .cell.center select{
      text-align:center;
      font-weight:700;
    }

    .cell.status select{
      text-align:center;
      text-transform:uppercase;
      font-weight:700;
      cursor:pointer;
      padding-left:6px;
      padding-right:6px;
    }

    .status-open{
      background:var(--red-bg) !important;
      color:var(--red) !important;
    }

    .status-closed{
      background:var(--green-bg) !important;
      color:var(--green) !important;
    }

    .muted-small{
      font-size:11px;
      color:#666;
    }

    .footer-note{
      padding:10px 14px;
      background:#fafafa;
      border-top:1px solid #e6e6e6;
      font-size:12px;
      color:#555;
    }

    .w-acpm{ width:230px; }
    .w-fuente{ width:100px; }
    .w-desc{ width:225px; }
    .w-accion{ width:270px; }
    .w-resp-seg{ width:145px; }
    .w-fecha{ width:155px; }
    .w-seguimiento{ width:155px; }
    .w-status{ width:70px; }
    .w-resp-cierre{ width:160px; }

    @media (max-width: 768px){
      .page-wrap{ padding:10px; }
      .top-title{ font-size:15px; }
      .top-subtitle{ font-size:13px; }
      .counter-num{ font-size:22px; width:60px; }
    }

    @media print{
      @page{
        size: landscape;
        margin: 10mm;
      }

      body{
        background:#fff !important;
      }

      .page-wrap{
        padding:0 !important;
      }

      .toolbar,
      .footer-note{
        display:none !important;
      }

      .sheet-card{
        border:none !important;
        box-shadow:none !important;
      }

      .sheet-scroll{
        overflow:visible !important;
      }

      .sheet{
        min-width:100% !important;
      }

      .cell input,
      .cell textarea,
      .cell select{
        font-size:11px !important;
        -webkit-appearance:none;
        appearance:none;
      }
    }
  </style>
</head>
<body>
  <div class="page-wrap">
    <div class="toolbar">
      <div class="toolbar-left">
        <a href="../planear.php" class="btn-ui">← Volver a Planear</a>
        <button type="button" class="btn-ui btn-light-ui" onclick="window.print()">🖨 Imprimir</button>
      </div>

      <div class="toolbar-right">
        <div class="muted-small">Formato editable - 2.8.1-3</div>
      </div>
    </div>

    <div class="sheet-card">
      <div class="sheet-scroll">
        <div class="sheet">
          <table class="form-sheet">
            <colgroup>
              <col style="width:230px">
              <col style="width:100px">
              <col style="width:225px">
              <col style="width:270px">
              <col style="width:145px">
              <col style="width:155px">
              <col style="width:155px">
              <col style="width:70px">
              <col style="width:160px">
            </colgroup>

            <tr>
              <td class="logo-box" rowspan="3" colspan="2">
                <div class="logo-inner">
                  <div class="logo-dashed">
                    TU LOGO<br>AQUÍ
                  </div>
                </div>
              </td>
              <td class="top-title" colspan="5">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
              <td class="top-gray" colspan="2">0</td>
            </tr>
            <tr>
              <td class="top-gray" colspan="5">&nbsp;</td>
              <td class="top-gray" colspan="2">AN-SST-29</td>
            </tr>
            <tr>
              <td class="top-subtitle" colspan="5">MATRIZ DE CONDICIONES INSEGURAS / ACCIONES CORRECTIVAS Y/O PREVENTIVAS</td>
              <td class="top-gray" colspan="2">XX/XX/2025</td>
            </tr>

            <tr>
              <td class="counter-wrap" colspan="9">
                <div class="counter-box">
                  <span>ABIERTAS</span>
                  <div class="counter-num red" id="abiertasCount">9</div>
                </div>
                <div class="counter-box">
                  <span>CERRADAS</span>
                  <div class="counter-num green" id="cerradasCount">2</div>
                </div>
              </td>
            </tr>

            <tr>
              <th class="col-head w-acpm">ACPM / HALLAZGO</th>
              <th class="col-head w-fuente">FUENTE</th>
              <th class="col-head w-desc">DESCRIPCIÓN</th>
              <th class="col-head w-accion">ACCIÓN A TOMAR</th>
              <th class="col-head w-resp-seg">RESPONSABLE DEL SEGUIMIENTO</th>
              <th class="col-head w-fecha">FECHA DE CUMPLIMIENTO</th>
              <th class="col-head w-seguimiento">SEGUIMIENTO</th>
              <th class="col-head w-status">STATUS</th>
              <th class="col-head w-resp-cierre">RESPONSABLE DEL CIERRE</th>
            </tr>

            <?php
              $filas = [
                ["", "CONDICIÓN INSEGURA", "", "", "", "", "CERRADA", ""],
                ["", "RECOMENDACIONES DE LA ARL", "", "", "", "", "ABIERTA", ""],
                ["", "HALLAZGO", "", "", "", "", "ABIERTA", ""],
                ["", "HALLAZGO", "", "", "", "", "CERRADA", ""],
                ["", "OBSERVACIÓN DE COMPORTAMIENTO", "", "", "", "", "ABIERTA", ""],
                ["", "ACCIONES DE LA REVISIÓN POR LA ALTA DIRECCIÓN", "", "", "", "", "ABIERTA", ""],
                ["", "INVESTIGACIÓN DE ACCIDENTES", "", "", "", "", "ABIERTA", ""],
                ["", "CONDICIÓN INSEGURA", "", "", "", "", "ABIERTA", ""],
                ["", "ACTO INSEGURO", "", "", "", "", "ABIERTA", ""],
                ["", "RECOMENDACIONES DE LA ARL", "", "", "", "", "ABIERTA", ""],
                ["", "RECOMENDACIONES DE LA ARL", "", "", "", "", "ABIERTA", ""],
              ];

              foreach($filas as $fila):
                $status = strtoupper($fila[6]);
                $statusClass = $status === 'CERRADA' ? 'status-closed' : 'status-open';
            ?>
            <tr>
              <td class="cell">
                <textarea name="acpm_hallazgo[]"><?= e($fila[0]) ?></textarea>
              </td>
              <td class="cell center">
                <select name="fuente[]">
                  <?php
                    $fuentes = [
                      "",
                      "CONDICIÓN INSEGURA",
                      "ACTO INSEGURO",
                      "HALLAZGO",
                      "RECOMENDACIONES DE LA ARL",
                      "OBSERVACIÓN DE COMPORTAMIENTO",
                      "ACCIONES DE LA REVISIÓN POR LA ALTA DIRECCIÓN",
                      "INVESTIGACIÓN DE ACCIDENTES"
                    ];
                    foreach($fuentes as $f):
                  ?>
                    <option value="<?= e($f) ?>" <?= $fila[1] === $f ? 'selected' : '' ?>><?= e($f) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td class="cell">
                <textarea name="descripcion[]"><?= e($fila[2]) ?></textarea>
              </td>
              <td class="cell">
                <textarea name="accion_tomar[]"><?= e($fila[3]) ?></textarea>
              </td>
              <td class="cell">
                <input type="text" name="responsable_seguimiento[]" value="<?= e($fila[4]) ?>">
              </td>
              <td class="cell">
                <input type="date" name="fecha_cumplimiento[]" value="<?= e($fila[5]) ?>">
              </td>
              <td class="cell">
                <textarea name="seguimiento[]"></textarea>
              </td>
              <td class="cell status <?= $statusClass ?>">
                <select name="status[]" class="status-select <?= $statusClass ?>">
                  <option value="ABIERTA" <?= $status === 'ABIERTA' ? 'selected' : '' ?>>ABIERTA</option>
                  <option value="CERRADA" <?= $status === 'CERRADA' ? 'selected' : '' ?>>CERRADA</option>
                </select>
              </td>
              <td class="cell">
                <input type="text" name="responsable_cierre[]" value="<?= e($fila[7]) ?>">
              </td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </div>

      <div class="footer-note">
        Puedes editar cada fila directamente. El contador de abiertas y cerradas cambia automáticamente según el campo <strong>STATUS</strong>.
      </div>
    </div>
  </div>

  <script>
    function actualizarEstadoVisual(select){
      const td = select.closest('.status');
      td.classList.remove('status-open', 'status-closed');
      select.classList.remove('status-open', 'status-closed');

      if(select.value === 'CERRADA'){
        td.classList.add('status-closed');
        select.classList.add('status-closed');
      }else{
        td.classList.add('status-open');
        select.classList.add('status-open');
      }
    }

    function actualizarContadores(){
      const selects = document.querySelectorAll('.status-select');
      let abiertas = 0;
      let cerradas = 0;

      selects.forEach(select => {
        if(select.value === 'CERRADA'){
          cerradas++;
        }else{
          abiertas++;
        }
      });

      document.getElementById('abiertasCount').textContent = abiertas;
      document.getElementById('cerradasCount').textContent = cerradas;
    }

    document.querySelectorAll('.status-select').forEach(select => {
      actualizarEstadoVisual(select);

      select.addEventListener('change', function(){
        actualizarEstadoVisual(this);
        actualizarContadores();
      });
    });

    actualizarContadores();
  </script>
</body>
</html>