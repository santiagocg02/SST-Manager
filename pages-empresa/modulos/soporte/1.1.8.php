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
  <title>1.1.8 | Comité de Convivencia (COCOLAB)</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#2f62b6;
      --blue2:#1f4f9a;
      --soft:#f6f8fb;
      --muted:#6b7280;
    }
    body{ background:#fff; color:#111; font-size:12px; }
    .sheet{ max-width: 1100px; margin:0 auto; padding:12px 14px 18px; }

    /* Toolbar */
    .toolbar{
      display:flex; align-items:center; justify-content:space-between;
      gap:10px; margin-bottom:10px;
    }
    .btn-lite{
      border:1px solid #d7dbe3;
      background:#fff;
      padding:6px 10px;
      border-radius:10px;
      font-weight:800;
      font-size:12px;
    }
    .btn-primary-lite{
      border:1px solid #bcd2ff;
      background:#eef4ff;
      color:#1241a6;
      padding:6px 10px;
      border-radius:10px;
      font-weight:900;
      font-size:12px;
    }
    .tiny{ font-size:10px; color:var(--muted); font-weight:700; }
    

    /* Header (estilo formato) */
    .format-head{ border:1px solid var(--line); border-bottom:none; }
    .format-head .grid{
      display:grid;
      grid-template-columns: 190px 1fr 220px;
      align-items:stretch;
    }
    .logo-box{
      border-right:1px solid var(--line);
      padding:10px;
      display:flex; align-items:center; justify-content:center;
      min-height:82px;
    }
    .logo-placeholder{
      width:100%;
      height:58px;
      border:1px dashed #b9c0ce;
      display:flex; align-items:center; justify-content:center;
      color:#9aa3b2;
      font-weight:900;
      letter-spacing:.4px;
    }
    .title-box{
      padding:10px 12px;
      border-right:1px solid var(--line);
      text-align:center;
    }
    .title-box .top{
      font-weight:900; font-size:11px; text-transform:uppercase;
    }
    .title-box .mid{
      margin-top:4px;
      font-weight:900; font-size:12px; text-transform:uppercase;
    }
    .meta-box{
      display:grid;
      grid-template-columns: 1fr 1fr;
      grid-auto-rows:minmax(28px, auto);
    }
    .meta-box div{
      border-left:1px solid var(--line);
      border-bottom:1px solid var(--line);
      padding:6px 8px;
      display:flex; justify-content:space-between; gap:10px; align-items:center;
      font-size:11px;
    }
    .meta-box .lbl{ font-weight:900; text-transform:uppercase; font-size:10px; }
    .meta-box .val{ font-weight:900; }

    .format-sub{
      border:1px solid var(--line);
      border-top:none;
      padding:8px 10px;
      background:#fff;
    }

    /* Secciones / anexos */
    .annex{
      border:1px solid var(--line);
      margin-top:12px;
      background:#fff;
      page-break-inside:avoid;
    }
    .annex-head{
      padding:10px 12px;
      border-bottom:1px solid var(--line);
      background:#fff;
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:12px;
    }
    .annex-title{
      font-weight:900;
      font-size:13px;
      text-transform:uppercase;
      color:#0f2f6d;
      letter-spacing:.3px;
    }
    .annex-sub{ font-size:11px; color:var(--muted); font-weight:700; }
    .annex-body{ padding:12px; }

    .field-row{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:12px;
      margin-bottom:10px;
    }
    .field{
      border:1px solid #d7dbe3;
      border-radius:12px;
      padding:10px 10px 8px;
      background:#fff;
    }
    .field label{
      display:block;
      font-size:10px;
      font-weight:900;
      text-transform:uppercase;
      color:#475569;
      margin-bottom:6px;
    }
    .field input, .field textarea, .field select{
      width:100%;
      border:1px solid #cfd6e4;
      border-radius:10px;
      padding:8px 10px;
      font-size:12px;
      outline:none;
    }
    textarea{ min-height:110px; resize:vertical; }

    table{
      width:100%;
      border-collapse:collapse;
      font-size:12px;
    }
    th, td{
      border:1px solid var(--line);
      padding:8px 8px;
      vertical-align:middle;
    }
    th{
      background:#d9e8f7;
      font-weight:900;
      text-align:center;
    }
    .right{ text-align:right; }
    .center{ text-align:center; }
    .muted{ color:var(--muted); font-weight:700; }

    .sign-row{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:16px;
      margin-top:14px;
      align-items:end;
    }
    .sign{
      border-top:1px solid var(--line);
      padding-top:6px;
      font-weight:800;
      text-align:center;
    }

    @media print{
      .print-hide{ display:none !important; }
      .sheet{ max-width:none; padding:0; }
      .annex{ break-inside: avoid; }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>
<div class="sheet">

  <!-- Toolbar -->
  <div class="toolbar print-hide">
    <div class="d-flex gap-2">
      <button class="btn-lite" type="button" onclick="history.back()">← Atrás</button>
      <button class="btn-primary-lite" type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
    <div class="tiny">
      Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="fechaHoy"></span>
    </div>
  </div>

  <!-- Header -->
  <div class="format-head">
    <div class="grid">
      <div class="logo-box">
        <div class="logo-placeholder">TU LOGO AQUÍ</div>
      </div>

      <div class="title-box">
        <div class="top">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
        <div class="mid">CONFORMACIÓN COCOLAB</div>
      </div>

      <div class="meta-box">
        <div><span class="lbl">Versión</span><span class="val">0</span></div>
        <div><span class="lbl">Código</span><span class="val">RE-XX-SST-0X</span></div>
        <div><span class="lbl">Fecha</span><span class="val"><input id="metaFecha" type="date" class="form-control form-control-sm" style="font-weight:900;"></span></div>
        <div><span class="lbl">Anexo</span><span class="val">1–8</span></div>
      </div>
    </div>
  </div>
  <div class="format-sub">
    <span class="tiny"><strong>Nota:</strong> Este formato contiene los anexos para el proceso de elección y organización del Comité de Convivencia Laboral (COCOLAB).</span>
  </div>

  <!-- ANEXO 1 -->
  <section class="annex" id="anexo1">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 1 · Carta de invitación</div>
        <div class="annex-sub">Asunto: Postulación de candidatos para elección de representantes al Comité de Convivencia</div>
      </div>
      <div class="annex-sub muted">Ciudad / Fecha</div>
    </div>
    <div class="annex-body">
      <div class="field-row">
        <div class="field">
          <label>Ciudad</label>
          <input type="text" placeholder="Ciudad">
        </div>
        <div class="field">
          <label>Fecha</label>
          <input type="date">
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>Nombre de la empresa</label>
          <input type="text" placeholder="Empresa">
        </div>
        <div class="field">
          <label>Representante legal / gerente</label>
          <input type="text" placeholder="Nombre completo">
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>N° principales</label>
          <input type="number" min="0" placeholder="Ej: 2">
        </div>
        <div class="field">
          <label>N° suplentes</label>
          <input type="number" min="0" placeholder="Ej: 2">
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>Fecha de votación</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Horario (desde / hasta)</label>
          <input type="text" placeholder="HH:MM a.m. – HH:MM a.m.">
        </div>
      </div>

      <div class="field">
        <label>Texto de la invitación</label>
        <textarea>
La representante legal convoca a todos los trabajadores para elegir sus representantes al Comité de Convivencia Laboral, según lo establecido en la Resolución 1356 de 2012 y la Resolución 652 de 2012.

Te invitamos a que participes de un comité que vela por la salud y el bienestar de los empleados.

(Completa con los datos del proceso de votación.)
        </textarea>
      </div>

      <div class="sign-row">
        <div class="sign">Nombre Gerente / Representante Legal</div>
        <div class="sign">Firma</div>
      </div>
    </div>
  </section>

  <!-- ANEXO 2 -->
  <section class="annex" id="anexo2">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 2 · Hoja de inscripción de candidatos</div>
        <div class="annex-sub">Periodo: AAAA-MM – AAAA-MM</div>
      </div>
      <div class="annex-sub muted">Registra los candidatos</div>
    </div>
    <div class="annex-body">
      <div class="field-row">
        <div class="field">
          <label>Periodo (desde)</label>
          <input type="month">
        </div>
        <div class="field">
          <label>Periodo (hasta)</label>
          <input type="month">
        </div>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th>CEDULA</th>
              <th>CARGO</th>
              <th>ÁREA</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<14;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="field-row mt-3">
        <div class="field">
          <label>Responsable</label>
          <input type="text" placeholder="Nombre del responsable">
        </div>
        <div class="field">
          <label>Fecha de cierre</label>
          <input type="date">
        </div>
      </div>
    </div>
  </section>

  <!-- ANEXO 3 -->
  <section class="annex" id="anexo3">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 3 · Registro de votantes (COCOLAB)</div>
        <div class="annex-sub">Periodo: AAAA-MM – AAAA-MM</div>
      </div>
      <div class="annex-sub muted">Nombre · Cédula · Cargo · Firma</div>
    </div>
    <div class="annex-body">
      <div class="field-row">
        <div class="field">
          <label>Periodo (desde)</label>
          <input type="month">
        </div>
        <div class="field">
          <label>Periodo (hasta)</label>
          <input type="month">
        </div>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th>CEDULA</th>
              <th>CARGO</th>
              <th>FIRMA</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<18;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text" placeholder="Firma / Observación"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- ANEXO 4 -->
  <section class="annex" id="anexo4">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 4 · Acta de apertura de elecciones</div>
        <div class="annex-sub">Acta de apertura del proceso de votación</div>
      </div>
    </div>
    <div class="annex-body">
      <div class="field-row">
        <div class="field">
          <label>Periodo (desde)</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Periodo (hasta)</label>
          <input type="date">
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>Empresa</label>
          <input type="text" placeholder="Nombre empresa">
        </div>
        <div class="field">
          <label>Hora de apertura</label>
          <input type="time">
        </div>
      </div>

      <div class="field">
        <label>Texto del acta</label>
        <textarea>Siendo las HH:MM del día DD-MM-AAAA, se dio apertura al proceso de votación para la elección de los candidatos al COMITÉ DE CONVIVENCIA, para el periodo comprendido entre las fechas indicadas.</textarea>
      </div>

      <div class="field">
        <label>Jurado de votación (nombres completos, área y cédula)</label>
        <textarea placeholder="Jurado 1: ...&#10;Jurado 2: ..."></textarea>
      </div>

      <div class="sign-row">
        <div class="sign">Firma jurado votación · Nombre</div>
        <div class="sign">Firma jurado votación · Nombre</div>
      </div>
    </div>
  </section>

  <!-- ANEXO 5 -->
  <section class="annex" id="anexo5">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 5 · Acta de cierre de votaciones</div>
        <div class="annex-sub">Escrutinio y resultados para elección de integrantes del comité</div>
      </div>
    </div>
    <div class="annex-body">
      <div class="field-row">
        <div class="field">
          <label>Empresa</label>
          <input type="text" placeholder="Nombre empresa">
        </div>
        <div class="field">
          <label>Hora de cierre</label>
          <input type="time">
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>Fecha de cierre</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Periodo (desde / hasta)</label>
          <input type="text" placeholder="DD-MM-AAAA – DD-MM-AAAA">
        </div>
      </div>

      <div class="field">
        <label>Jurado de votación encargado</label>
        <textarea placeholder="Jurado 1: ...&#10;Jurado 2: ..."></textarea>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>CANDIDATO</th>
              <th class="center" style="width:140px;">NÚMERO VOTOS</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<10;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="number" min="0"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
          <tfoot>
            <tr>
              <td class="right fw-bold">TOTAL VOTOS</td>
              <td><input class="form-control form-control-sm" type="number" min="0"></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="annex-sub mt-3 mb-2">Efectuado el escrutinio se obtuvieron los siguientes resultados (Principal / Suplente):</div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th>ÁREA</th>
              <th>CARGO</th>
              <th class="center" style="width:130px;">CATEGORÍA</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><input class="form-control form-control-sm" type="text"></td>
              <td><input class="form-control form-control-sm" type="text"></td>
              <td><input class="form-control form-control-sm" type="text"></td>
              <td class="center fw-bold">PRINCIPAL</td>
            </tr>
            <tr>
              <td><input class="form-control form-control-sm" type="text"></td>
              <td><input class="form-control form-control-sm" type="text"></td>
              <td><input class="form-control form-control-sm" type="text"></td>
              <td class="center fw-bold">SUPLENTE</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="sign-row">
        <div class="sign">Nombre Jurado · Firma</div>
        <div class="sign">Nombre Jurado · Firma</div>
      </div>
    </div>
  </section>

  <!-- ANEXO 6 -->
  <section class="annex" id="anexo6">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 6 · Registro de asistencia votación (COCOLAB)</div>
        <div class="annex-sub">Periodo: AAAA-MM-DD</div>
      </div>
    </div>
    <div class="annex-body">
      <div class="field-row">
        <div class="field">
          <label>Fecha</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Empresa</label>
          <input type="text" placeholder="Nombre empresa">
        </div>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th style="width:60px;">No.</th>
              <th>NOMBRE Y APELLIDO</th>
              <th style="width:180px;">CÉDULA</th>
              <th style="width:200px;">CARGO</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=1;$i<=18;$i++): ?>
              <tr>
                <td class="center fw-bold"><?= $i ?></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- ANEXO 7 -->
  <section class="annex" id="anexo7">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 7 · Constitución y organización del comité</div>
        <div class="annex-sub">Basado en Ley 1010 de 2006 y Resolución 652/2012 (mod. 1356/2012)</div>
      </div>
    </div>
    <div class="annex-body">
      <div class="field">
        <label>Empresa</label>
        <input type="text" placeholder="Nombre empresa">
      </div>

      <div class="field-row mt-2">
        <div class="field">
          <label>Fecha de elección</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Modalidad utilizada</label>
          <select>
            <option>Votación</option>
            <option>Designación</option>
            <option>Otra</option>
          </select>
        </div>
      </div>

      <div class="annex-sub mt-3 mb-2">Resultados por parte del TRABAJADOR:</div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th class="center" style="width:120px;">PRINCIPAL</th>
              <th class="center" style="width:120px;">SUPLENTE</th>
              <th class="center" style="width:180px;">FIRMA</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<4;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td class="center"><input type="checkbox" class="form-check-input"></td>
                <td class="center"><input type="checkbox" class="form-check-input"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="annex-sub mt-4 mb-2">Representantes por parte de la EMPRESA (designados por el empleador):</div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th class="center" style="width:120px;">PRINCIPAL</th>
              <th class="center" style="width:120px;">SUPLENTE</th>
              <th class="center" style="width:180px;">FIRMA</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<4;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td class="center"><input type="checkbox" class="form-check-input"></td>
                <td class="center"><input type="checkbox" class="form-check-input"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="field mt-3">
        <label>Presidente del comité (designado)</label>
        <input type="text" placeholder="Nombre del presidente">
      </div>

      <div class="sign-row">
        <div class="sign">Firma Representante Legal</div>
        <div class="sign">Nombre Representante Legal</div>
      </div>
    </div>
  </section>

  <!-- ANEXO 8 -->
  <section class="annex" id="anexo8">
    <div class="annex-head">
      <div>
        <div class="annex-title">Anexo 8 · Acta de comité de convivencia laboral (COCOLAB)</div>
        <div class="annex-sub">Formato acta de reunión</div>
      </div>
    </div>
    <div class="annex-body">
      <div class="field">
        <label>Nombre empresa</label>
        <input type="text" placeholder="Nombre empresa">
      </div>

      <div class="field-row mt-2">
        <div class="field">
          <label>Fecha</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Acta No.</label>
          <input type="text" placeholder="___">
        </div>
      </div>

      <div class="field-row">
        <div class="field">
          <label>Hora de inicio</label>
          <input type="time">
        </div>
        <div class="field">
          <label>Hora de finalización</label>
          <input type="time">
        </div>
      </div>

      <div class="annex-sub mb-2">Asistentes e invitados:</div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>NOMBRE</th>
              <th style="width:220px;">FIRMA</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<8;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="field mt-3">
        <label>Orden del día</label>
        <textarea placeholder="1) ...&#10;2) ...&#10;3) ..."></textarea>
      </div>

      <div class="field">
        <label>Desarrollo de la reunión</label>
        <textarea></textarea>
      </div>

      <div class="annex-sub mt-3 mb-2">Definición de tareas (Convenciones: A. Abierta – C. Cerrada – P. Proceso):</div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ACTIVIDAD</th>
              <th style="width:190px;">RESPONSABLE</th>
              <th style="width:160px;">FECHA DE EJECUCIÓN</th>
              <th style="width:150px;">ESTADO</th>
              <th>OBSERVACIONES</th>
            </tr>
          </thead>
          <tbody>
            <?php for($i=0;$i<10;$i++): ?>
              <tr>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="text"></td>
                <td><input class="form-control form-control-sm" type="date"></td>
                <td>
                  <select class="form-select form-select-sm">
                    <option value="A">A</option>
                    <option value="C">C</option>
                    <option value="P">P</option>
                  </select>
                </td>
                <td><input class="form-control form-control-sm" type="text"></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="field-row mt-3">
        <div class="field">
          <label>Fecha próxima reunión</label>
          <input type="date">
        </div>
        <div class="field">
          <label>Hora próxima reunión</label>
          <input type="time">
        </div>
      </div>

      <div class="sign-row">
        <div class="sign">FIRMA DEL PRESIDENTE</div>
        <div class="sign">FIRMA DEL SECRETARIO</div>
      </div>
    </div>
  </section>

</div>

<script>
  function setHoy(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");
    document.getElementById("fechaHoy").textContent = `${y}/${m}/${dd}`;
    const meta = document.getElementById("metaFecha");
    if(meta && !meta.value) meta.value = `${y}-${m}-${dd}`;
  }
  setHoy();
</script>

</body>
</html>