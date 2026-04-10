<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
// OJO: Si te sigue saliendo pantalla blanca de error (como en la Imagen 1), 
// cámbialo por: require_once '../../includes/ConexionAPI.php'; (quitándole un ../)
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos (Ej: 8 para "1.1.8")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 8; 

// --- Lógica de Empresa Optimizada (Logo, Nombres y Firmas) ---
$nombreEmpresaLogeada = "NOMBRE DE LA EMPRESA";
$logoEmpresaUrl = "";
$nombreRL = "";
$firmaRL = "";
$nombreSST = "";
$firmaSST = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $nombreEmpresaLogeada = $empData['nombre_empresa'] ?? 'NOMBRE DE LA EMPRESA';
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        
        // --- AJUSTE: Priorizamos los campos nombre_rl y firma_rl ---
        $nombreRL = $empData['nombre_rl'] ?? $empData['representante_legal'] ?? $empData['nombre_representante'] ?? '';
        $firmaRL = $empData['firma_rl'] ?? $empData['firma_representante'] ?? '';
        
        $nombreSST = $empData['nombre_sst'] ?? $empData['responsable_sst'] ?? '';
        $firmaSST = $empData['firma_sst'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = null;

if (isset($resFormulario['data']['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['data']['campos'];
} elseif (isset($resFormulario['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['campos'];
} elseif (isset($resFormulario['campos'])) {
    $camposCrudos = $resFormulario['campos'];
}

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true);
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>1.1.8 | Comité de Convivencia (COCOLAB)</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
      background: #d9dde2;
      padding: 10px 16px;
      border: 1px solid #c8cdd3;
    }
    .btn-lite{
      border:1px solid #d7dbe3;
      background:#fff;
      color: #2f62b6;
      padding:6px 12px;
      font-weight:800;
      font-size:12px;
      cursor: pointer;
    }
    .btn-lite:hover { background: #eef4ff; }
    .btn-primary-lite{
      border:1px solid #1b4fbd;
      background:#1b4fbd;
      color:#fff;
      padding:6px 12px;
      font-weight:900;
      font-size:12px;
      cursor: pointer;
    }
    .btn-primary-lite:hover { background: #0f3484; }
    .btn-success-lite {
      border: 1px solid #198754;
      background: #198754;
      color: #fff;
      padding:6px 12px;
      font-weight:900;
      font-size:12px;
      cursor: pointer;
    }
    .btn-success-lite:hover { background: #146c43; }
    
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
      padding:4px;
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
      .sheet{ max-width:none; padding:0; border:none; }
      .annex{ break-inside: avoid; }
      
      input, textarea, select {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        appearance: none !important;
        padding: 0 !important;
        margin: 0 !important;
        resize: none !important;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>
<div class="sheet">

  <div class="toolbar print-hide">
    <div class="d-flex gap-2">
      <button class="btn-lite" type="button" onclick="history.back()">← Atrás</button>
      <button class="btn-lite" type="button" onclick="window.location.reload()">Recargar</button>
      <button class="btn-success-lite" type="button" id="btnGuardar">Guardar Cambios</button>
      <button class="btn-primary-lite" type="button" onclick="window.print()">Imprimir PDF</button>
    </div>
    <div class="tiny text-end">
      <span style="font-size: 15px; font-weight: 900; color: #0f2f5c;">COCOLAB</span><br>
      Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="fechaHoy"></span>
    </div>
  </div>

  <form id="form-sst-dinamico">
      <div class="format-head">
        <div class="grid">
          <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border-right:1px solid var(--line); background:transparent; padding:4px;' ?>">
            <?php if(!empty($logoEmpresaUrl)): ?>
                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 65px; object-fit: contain;">
            <?php else: ?>
                <div class="logo-placeholder">TU LOGO AQUÍ</div>
            <?php endif; ?>
          </div>

          <div class="title-box">
            <div class="top">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
            <div class="mid">CONFORMACIÓN COCOLAB</div>
          </div>

          <div class="meta-box">
            <div><span class="lbl">Versión</span><span class="val">0</span></div>
            <div><span class="lbl">Código</span><span class="val">RE-XX-SST-0X</span></div>
            <div><span class="lbl">Fecha</span><span class="val"><input id="metaFecha" name="meta_fecha" type="date" class="form-control form-control-sm" style="font-weight:900; border:none; padding:0; height:auto; background:transparent;"></span></div>
            <div><span class="lbl">Anexo</span><span class="val">1–8</span></div>
          </div>
        </div>
      </div>
      <div class="format-sub">
        <span class="tiny"><strong>Nota:</strong> Este formato contiene los anexos para el proceso de elección y organización del Comité de Convivencia Laboral (COCOLAB).</span>
      </div>

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
              <input type="text" name="anx1_ciudad" placeholder="Ciudad">
            </div>
            <div class="field">
              <label>Fecha</label>
              <input type="date" name="anx1_fecha">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Nombre de la empresa</label>
              <input type="text" name="anx1_empresa" placeholder="Empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
            </div>
            <div class="field">
              <label>Representante legal / gerente</label>
              <input type="text" name="anx1_rep_legal" placeholder="Nombre completo" value="<?= htmlspecialchars($nombreRL) ?>">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>N° principales</label>
              <input type="number" name="anx1_n_prin" min="0" placeholder="Ej: 2">
            </div>
            <div class="field">
              <label>N° suplentes</label>
              <input type="number" name="anx1_n_sup" min="0" placeholder="Ej: 2">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Fecha de votación</label>
              <input type="date" name="anx1_fecha_votacion">
            </div>
            <div class="field">
              <label>Horario (desde / hasta)</label>
              <input type="text" name="anx1_horario" placeholder="HH:MM a.m. – HH:MM a.m.">
            </div>
          </div>

          <div class="field">
            <label>Texto de la invitación</label>
            <textarea name="anx1_texto">La representante legal convoca a todos los trabajadores para elegir sus representantes al Comité de Convivencia Laboral, según lo establecido en la Resolución 1356 de 2012 y la Resolución 652 de 2012.

Te invitamos a que participes de un comité que vela por la salud y el bienestar de los empleados.</textarea>
          </div>

          <div class="sign-row">
            <div class="sign">
                <?php if(!empty($firmaRL)): ?>
                    <img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 45px; display: block; margin: 0 auto 5px;">
                <?php endif; ?>
                Nombre Gerente / Representante Legal <br>
                <span style="font-weight:normal; font-size:11px;"><?= htmlspecialchars($nombreRL) ?></span>
            </div>
            <div class="sign">Firma</div>
          </div>
        </div>
      </section>

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
              <input type="month" name="anx2_per_desde">
            </div>
            <div class="field">
              <label>Periodo (hasta)</label>
              <input type="month" name="anx2_per_hasta">
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
                <?php for($i=0;$i<10;$i++): ?>
                  <tr>
                    <td><input name="anx2_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx2_cc[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx2_cargo[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx2_area[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>

          <div class="field-row mt-3">
            <div class="field">
              <label>Responsable</label>
              <input type="text" name="anx2_resp" placeholder="Nombre del responsable" value="<?= htmlspecialchars($nombreSST) ?>">
            </div>
            <div class="field">
              <label>Fecha de cierre</label>
              <input type="date" name="anx2_fecha_cierre">
            </div>
          </div>
        </div>
      </section>

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
              <input type="month" name="anx3_per_desde">
            </div>
            <div class="field">
              <label>Periodo (hasta)</label>
              <input type="month" name="anx3_per_hasta">
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
                <?php for($i=0;$i<15;$i++): ?>
                  <tr>
                    <td><input name="anx3_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx3_cc[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx3_cargo[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx3_firma[]" class="form-control form-control-sm" type="text" placeholder="Firma / Observación" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

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
              <input type="date" name="anx4_per_desde">
            </div>
            <div class="field">
              <label>Periodo (hasta)</label>
              <input type="date" name="anx4_per_hasta">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Empresa</label>
              <input type="text" name="anx4_empresa" placeholder="Nombre empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
            </div>
            <div class="field">
              <label>Hora de apertura</label>
              <input type="time" name="anx4_hora_aper">
            </div>
          </div>

          <div class="field">
            <label>Texto del acta</label>
            <textarea name="anx4_texto">Siendo las HH:MM del día DD-MM-AAAA, se dio apertura al proceso de votación para la elección de los candidatos al COMITÉ DE CONVIVENCIA LABORAL, para el periodo comprendido entre las fechas indicadas.</textarea>
          </div>

          <div class="field">
            <label>Jurado de votación (nombres completos, área y cédula)</label>
            <textarea name="anx4_jurados" placeholder="Jurado 1: ...&#10;Jurado 2: ..."></textarea>
          </div>

          <div class="sign-row">
            <div class="sign">Firma jurado votación · Nombre</div>
            <div class="sign">Firma jurado votación · Nombre</div>
          </div>
        </div>
      </section>

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
              <input type="text" name="anx5_empresa" placeholder="Nombre empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
            </div>
            <div class="field">
              <label>Hora de cierre</label>
              <input type="time" name="anx5_hora_cierre">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Fecha de cierre</label>
              <input type="date" name="anx5_fecha_cierre">
            </div>
            <div class="field">
              <label>Periodo (desde / hasta)</label>
              <input type="text" name="anx5_periodo" placeholder="DD-MM-AAAA – DD-MM-AAAA">
            </div>
          </div>

          <div class="field">
            <label>Jurado de votación encargado</label>
            <textarea name="anx5_jurados" placeholder="Jurado 1: ...&#10;Jurado 2: ..."></textarea>
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
                <?php for($i=0;$i<8;$i++): ?>
                  <tr>
                    <td><input name="anx5_cand[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx5_votos[]" class="form-control form-control-sm center js-voto" type="number" min="0" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td class="right fw-bold">TOTAL VOTOS</td>
                  <td><input name="anx5_total_votos" id="anx5_total_votos" class="form-control form-control-sm center fw-bold" type="text" readonly style="border:none; background:transparent;"></td>
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
                  <td><input name="anx5_res_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  <td><input name="anx5_res_area[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  <td><input name="anx5_res_cargo[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  <td class="center fw-bold">PRINCIPAL</td>
                </tr>
                <tr>
                  <td><input name="anx5_res_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  <td><input name="anx5_res_area[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  <td><input name="anx5_res_cargo[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
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
              <input type="date" name="anx6_fecha">
            </div>
            <div class="field">
              <label>Empresa</label>
              <input type="text" name="anx6_empresa" placeholder="Nombre empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
            </div>
          </div>

          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th style="width:40px;">No.</th>
                  <th>NOMBRE Y APELLIDO</th>
                  <th style="width:160px;">CÉDULA</th>
                  <th style="width:180px;">CARGO</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=1;$i<=15;$i++): ?>
                  <tr>
                    <td class="center fw-bold"><?= $i ?></td>
                    <td><input name="anx6_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx6_cc[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx6_cargo[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

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
            <input type="text" name="anx7_empresa" placeholder="Nombre empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
          </div>

          <div class="field-row mt-2">
            <div class="field">
              <label>Fecha de elección</label>
              <input type="date" name="anx7_fecha_elec">
            </div>
            <div class="field">
              <label>Modalidad utilizada</label>
              <select name="anx7_modalidad">
                <option value="Votación">Votación</option>
                <option value="Designación">Designación</option>
                <option value="Otra">Otra</option>
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
                    <td><input name="anx7_trab_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td class="center"><input name="anx7_trab_prin[]" value="<?= $i ?>" type="checkbox" class="form-check-input"></td>
                    <td class="center"><input name="anx7_trab_sup[]" value="<?= $i ?>" type="checkbox" class="form-check-input"></td>
                    <td><input name="anx7_trab_firma[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
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
                    <td><input name="anx7_emp_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td class="center"><input name="anx7_emp_prin[]" value="<?= $i ?>" type="checkbox" class="form-check-input"></td>
                    <td class="center"><input name="anx7_emp_sup[]" value="<?= $i ?>" type="checkbox" class="form-check-input"></td>
                    <td><input name="anx7_emp_firma[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>

          <div class="field mt-3">
            <label>Presidente del comité (designado)</label>
            <input type="text" name="anx7_presidente" placeholder="Nombre del presidente">
          </div>

          <div class="sign-row mt-4">
            <div class="sign">
                <?php if(!empty($firmaRL)): ?>
                    <img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 45px; display: block; margin: 0 auto 5px;">
                <?php endif; ?>
                Firma Representante Legal
            </div>
            <div class="sign">
                Nombre Representante Legal <br>
                <span style="font-weight:normal; font-size:11px;"><?= htmlspecialchars($nombreRL) ?></span>
            </div>
          </div>
        </div>
      </section>

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
            <input type="text" name="anx8_empresa" placeholder="Nombre empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
          </div>

          <div class="field-row mt-2">
            <div class="field">
              <label>Fecha</label>
              <input type="date" name="anx8_fecha">
            </div>
            <div class="field">
              <label>Acta No.</label>
              <input type="text" name="anx8_acta_no" placeholder="___">
            </div>
          </div>

          <div class="field-row">
            <div class="field">
              <label>Hora de inicio</label>
              <input type="time" name="anx8_hora_ini">
            </div>
            <div class="field">
              <label>Hora de finalización</label>
              <input type="time" name="anx8_hora_fin">
            </div>
          </div>

          <div class="annex-sub mb-2 mt-3">Asistentes e invitados:</div>
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>NOMBRE</th>
                  <th style="width:220px;">FIRMA</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=0;$i<6;$i++): ?>
                  <tr>
                    <td><input name="anx8_asis_nom[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx8_asis_firma[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>

          <div class="field mt-3">
            <label>Orden del día</label>
            <textarea name="anx8_orden_dia" placeholder="1) ...&#10;2) ...&#10;3) ..."></textarea>
          </div>

          <div class="field">
            <label>Desarrollo de la reunión</label>
            <textarea name="anx8_desarrollo"></textarea>
          </div>

          <div class="annex-sub mt-3 mb-2">Definición de tareas (Convenciones: A. Abierta – C. Cerrada – P. Proceso):</div>
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>ACTIVIDAD</th>
                  <th style="width:170px;">RESPONSABLE</th>
                  <th style="width:140px;">FECHA DE EJECUCIÓN</th>
                  <th style="width:90px;">ESTADO</th>
                  <th>OBSERVACIONES</th>
                </tr>
              </thead>
              <tbody>
                <?php for($i=0;$i<6;$i++): ?>
                  <tr>
                    <td><input name="anx8_tarea_act[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx8_tarea_resp[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                    <td><input name="anx8_tarea_fecha[]" class="form-control form-control-sm" type="date" style="border:none;"></td>
                    <td>
                      <select name="anx8_tarea_est[]" class="form-select form-select-sm" style="border:none;">
                        <option value=""></option>
                        <option value="A">A</option>
                        <option value="C">C</option>
                        <option value="P">P</option>
                      </select>
                    </td>
                    <td><input name="anx8_tarea_obs[]" class="form-control form-control-sm" type="text" style="border:none;"></td>
                  </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>

          <div class="field-row mt-3">
            <div class="field">
              <label>Fecha próxima reunión</label>
              <input type="date" name="anx8_prox_fecha">
            </div>
            <div class="field">
              <label>Hora próxima reunión</label>
              <input type="time" name="anx8_prox_hora">
            </div>
          </div>

          <div class="sign-row">
            <div class="sign">FIRMA DEL PRESIDENTE</div>
            <div class="sign">FIRMA DEL SECRETARIO</div>
          </div>
        </div>
      </section>
  </form>
</div>

<script>
  // Script para totalizar los votos automáticamente en el Anexo 5
  document.querySelectorAll('.js-voto').forEach(input => {
      input.addEventListener('input', () => {
          let total = 0;
          document.querySelectorAll('.js-voto').forEach(v => {
              total += parseInt(v.value || 0);
          });
          document.getElementById('anx5_total_votos').value = total;
      });
  });

  // Fecha de hoy para el header
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

  // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
  document.addEventListener('DOMContentLoaded', function () {
    let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
    if (typeof datosGuardados === 'string') {
        try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
    }

    if (datosGuardados && Object.keys(datosGuardados).length > 0) {
        for (const [key, value] of Object.entries(datosGuardados)) {
            if (Array.isArray(value)) {
                let campos = document.querySelectorAll(`[name="${key}[]"]`);
                value.forEach((val, i) => {
                    if (campos[i]) {
                        if (campos[i].type === 'checkbox') {
                            campos[i].checked = (val === "on" || val === true || val === campos[i].value);
                        } else {
                            campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                        }
                    }
                });
            } else {
                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) {
                    if (campo.type === 'checkbox') {
                        campo.checked = (value === "on" || value === true || value === campo.value);
                    } else {
                        campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                    }
                }
            }
        }
    }
  });

  // --- LÓGICA DE GUARDADO ---
  document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form-sst-dinamico');
    const formData = new FormData(form);
    const datosJSON = {};

    // Iterar para manejar arrays (los 'name[]') y checkboxes
    document.querySelectorAll('#form-sst-dinamico input[type="checkbox"]').forEach(cb => {
        if (!cb.checked) {
            // Asegurarnos de guardar estado falso para checkboxes no marcados
            const cleanKey = cb.name.replace('[]', '');
            if (cb.name.endsWith('[]')) {
                if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
                datosJSON[cleanKey].push(""); 
            } else {
                datosJSON[cb.name] = "";
            }
        }
    });

    for (const [key, value] of formData.entries()) {
        if (key.endsWith('[]')) {
            const cleanKey = key.replace('[]', '');
            if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
            datosJSON[cleanKey].push(value);
        } else {
            datosJSON[key] = value;
        }
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = 'Guardando...';
    btn.disabled = true;

    try {
        const token = "<?= $token ?>";
        const urlAPI = "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar";

        const response = await fetch(urlAPI, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({
                id_empresa: <?= $empresa ?>,
                id_item_sst: <?= $idItem ?>,
                datos: datosJSON
            })
        });

        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Configuración guardada correctamente',
                icon: 'success',
                confirmButtonColor: '#198754'
            });
        } else {
            Swal.fire({
                title: 'Error al guardar',
                text: result.error || "No se pudo completar la operación.",
                icon: 'error',
                confirmButtonColor: '#1b4fbd'
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo contactar al servidor para guardar.',
            icon: 'error',
            confirmButtonColor: '#1b4fbd'
        });
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
  });
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>