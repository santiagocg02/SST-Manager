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
  <title>AC-SST-05 | Carta de Nombramiento</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#1f4fd6;
      --orange:#f28c28;
    }

    body{ background:#f4f6f8; }
    .wrap{ max-width: 950px; margin: 18px auto; }
    .toolbar{
      display:flex; justify-content:space-between; align-items:center; gap:10px;
      margin-bottom: 10px;
    }

    .sheet{
      background:#fff;
      border:2px solid var(--line);
      box-shadow: 0 10px 22px rgba(0,0,0,.08);
    }

    .fmt{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .fmt td{ border:1px solid var(--line); padding:10px; vertical-align:top; }

    .logo-box{
      border:2px dashed rgba(0,0,0,.35);
      height:90px;
      display:flex; align-items:center; justify-content:center;
      font-weight:800; color:rgba(0,0,0,.45);
      text-align:center;
    }

    .head-top{
      font-weight:900;
      text-align:center;
      font-size:18px;
      padding:18px 10px;
    }

    .sub-top{
      font-weight:900;
      text-align:center;
      font-size:16px;
      padding:10px;
    }

    .code-box{
      text-align:center;
      font-weight:800;
      font-size:14px;
    }

    .cell-input{
      width:100%;
      border:1px solid rgba(0,0,0,.25);
      border-radius:6px;
      padding:8px 10px;
      font-size:14px;
      background:#fff;
    }

    .center{ text-align:center; }
    .orange{ color: var(--orange); font-weight:900; }
    .bullets{ margin:0; padding-left: 0; list-style:none; }
    .bullets li{
      display:flex; gap:12px; align-items:flex-start;
      padding:10px 0;
      border-bottom:1px solid rgba(0,0,0,.08);
    }
    .bullets li:last-child{ border-bottom:none; }
    .tick{
      font-size:22px;
      line-height: 1;
      margin-top: 2px;
    }

    .line-sign{
      border-top:1px solid #000;
      width: 70%;
      margin: 45px auto 6px;
    }
    .sign-label{
      text-align:center;
      font-size:14px;
      margin-bottom: 18px;
    }
    .sign-row td{
      padding: 30px 10px;
    }

    /* impresión */
    @media print{
      body{ background:#fff; }
      .toolbar{ display:none !important; }
      .wrap{ max-width:none; margin:0; }
      .sheet{ box-shadow:none; }
      .cell-input{ border:1px solid #000; }
    }
  </style>
</head>

<body>

<div class="wrap">

  <div class="toolbar">
    <div class="d-flex gap-2 flex-wrap">
      <!-- ✅ volver a planear.php -->
      <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Volver </a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
    <div class="small text-muted fw-semibold">Formato: AC-SST-05</div>
  </div>

  <div class="sheet p-2">
    <table class="fmt">
      <tr>
        <td style="width:22%">
          <div class="logo-box">TU LOGO<br>AQUÍ</div>
        </td>

        <td class="head-top" colspan="2">
          SISTEMA DE GESTIÓN<br>
          DE SEGURIDAD Y SALUD EN EL TRABAJO
          <div class="sub-top">
            CARTA DE NOMBRAMIENTO, REPRESENTANTE POR LA ALTA DIRECCIÓN
          </div>
        </td>

        <td style="width:18%">
          <div class="code-box mb-2">0</div>
          <div class="code-box mb-2">AC-SST-05</div>
          <input class="cell-input" type="text" placeholder="XX/XX/2025">
        </td>
      </tr>

      <tr>
        <td colspan="4" class="center" style="padding:36px 10px;">
          <div class="orange" style="font-size:20px;">EMPRESA</div>
          <div class="mt-2">
            <input class="cell-input" style="max-width:520px; margin:0 auto;" type="text" placeholder="Nombre de la empresa">
          </div>

          <div class="mt-4 fw-bold" style="font-size:18px;">CERTIFICA:</div>
        </td>
      </tr>

      <tr>
        <td colspan="4" style="padding: 18px 18px 10px;">
          <ul class="bullets">

            <li>
              <div class="tick">✓</div>
              <div>
                Que <span class="orange">NOMBRE</span>, con C.C
                <input class="cell-input" style="display:inline-block; width:160px; margin:0 6px;" type="text" placeholder="XXX">
                ha sido designada como representante de la Dirección para el sistema de Gestión de seguridad y salud en el trabajo,
                y se le han asignado las funciones, responsabilidades y autoridades para:
              </div>
            </li>

            <li>
              <div class="tick">✓</div>
              <div>
                Planear, organizar, dirigir, desarrollar y aplicar el PESV, y realizar por lo menos una vez al año su evaluación.
              </div>
            </li>

            <li>
              <div class="tick">✓</div>
              <div>
                Asegurar que los requisitos del SG-SST se establezcan, implementen y mantengan, de acuerdo con lo indicado en el Decreto 1072 de 2015,
                Resolución 0312 del 2019 y demás normas asociadas.
              </div>
            </li>

            <li>
              <div class="tick">✓</div>
              <div>
                Informar a la alta Dirección sobre el funcionamiento y los resultados del SG - SST.
              </div>
            </li>

            <li>
              <div class="tick">✓</div>
              <div>
                Promover la participación de todos los miembros de la empresa en la implementación del SG - SST.
              </div>
            </li>

            <li>
              <div class="tick">✓</div>
              <div>
                Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del SG - SST.
              </div>
            </li>

            <li>
              <div class="tick">✓</div>
              <div>
                Programar las auditorías internas necesarias para el mantenimiento y mejora continua del SG - SST.
              </div>
            </li>

          </ul>
        </td>
      </tr>

      <tr class="sign-row">
        <td colspan="2" class="center">
          <div class="line-sign"></div>
          <div class="sign-label">Representante Legal</div>
        </td>
        <td colspan="2" class="center">
          <div class="line-sign"></div>
          <div class="sign-label">Encargado SST</div>
        </td>
      </tr>

    </table>
  </div>
</div>

</body>
</html>