<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../../index.php");
  exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos (Ej: 6 para "1.1.6")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 6; 

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
        
        // Ajusta estos nombres de campos si en tu base de datos se llaman diferente
        $nombreRL = $empData['representante_legal'] ?? $empData['nombre_representante'] ?? '';
        $firmaRL = $empData['firma_representante'] ?? $empData['firma_rl'] ?? '';
        $nombreSST = $empData['responsable_sst'] ?? $empData['nombre_sst'] ?? '';
        $firmaSST = $empData['firma_sst'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS A LA API
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
} else {
    $errorCarga = "No se detectaron campos. " . json_encode($resFormulario);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>1.1.6 | Conformación del COPASST</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root{
      --blue:#1f5fa8;
      --blue-dark:#184d88;
      --line:#111;
      --soft:#eef3fb;
      --gray:#f4f6fa;
      --head:#d9e4f2;
      --text:#1b1b1b;
      --toolbar-bg:#d9dde2;
      --toolbar-border:#c8cdd3;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      background:#e9edf3;
      font-family:Arial, Helvetica, sans-serif;
      color:var(--text);
    }

    .wrap{
      max-width:1100px;
      margin:16px auto;
      padding:0 10px 30px;
    }

    /* ===== BARRA SUPERIOR UNIFICADA ===== */
    .format-toolbar{
  background:#d9dde2;
  border:1px solid #c8cdd3;
  padding:10px 16px;
  margin-bottom:14px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:18px;
  flex-wrap:nowrap;
}

.format-toolbar-title{
  flex:0 0 auto;
  font-size:18px;
  font-weight:900;
  color:#0f2f5c;
  line-height:1.2;
  white-space:nowrap;
}

.format-toolbar-actions{
  flex:1;
  display:flex;
  justify-content:flex-end;
  align-items:center;
  gap:12px;
  flex-wrap:nowrap;
}

.btn-ui{
  min-width:100px;
  height:26px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border:1px solid var(--blue);
  background:var(--blue);
  color:#fff;
  padding:8px 16px;
  font-size:13px;
  font-weight:700;
  text-decoration:none;
  cursor:pointer;
  transition:.2s ease;
  border-radius:0;
  white-space:nowrap;
}

.btn-ui:hover{
  background:var(--blue-dark);
  border-color:var(--blue-dark);
  color:#fff;
}

.btn-ui.secondary{
  background:#fff;
  color:var(--blue);
}

.btn-ui.secondary:hover{
  background:var(--soft);
  color:var(--blue-dark);
  border-color:var(--blue-dark);
}
    .sheet{
      background:#fff; border:2px solid var(--blue); box-shadow:0 8px 18px rgba(0,0,0,.08);
      padding:14px; margin-bottom:18px;
    }

    .page-break{ page-break-after:always; }

    table.format{ width:100%; border-collapse:collapse; table-layout:fixed; font-size:12px; margin-bottom:10px; }
    .format td, .format th{ border:1px solid var(--line); padding:6px 8px; vertical-align:middle; }
    .title{ font-weight:900; text-align:center; font-size:13px; letter-spacing:.2px; }
    .subtitle{ font-weight:900; text-align:center; font-size:12px; }
    .code-box{ text-align:center; font-weight:900; font-size:12px; background:#fafafa; }
    .badge-mod{ display:inline-block; border:1px solid var(--blue); padding:4px 10px; background:var(--soft); font-weight:800; font-size:11px; border-radius:0; }
    
    .logo-box{ border:1px dashed #666; height:68px; display:flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#666; background:#fafafa; padding: 4px;}
    
    .sec-h{ background:var(--head); border:1px solid #9fb2c9; color:#10233c; font-weight:900; text-transform:uppercase; padding:9px 12px; font-size:14px; letter-spacing:.2px; margin:12px 0 10px; }
    .p{ margin-bottom:10px; font-size:12px; line-height:1.6; }
    .in{ width:100%; min-height:34px; border:1px solid #8f8f8f; background:#fff; padding:6px 8px; outline:none; border-radius:0; box-shadow:none; font-size:12px; }
    textarea.in{ resize:vertical; min-height:90px; }
    .in:focus{ border-color:var(--blue); }
    .in.center{ text-align:center; }
    .in.right{ text-align:right; }
    .in.inline{ display:inline-block; width:auto; min-width:140px; vertical-align:middle; }
    .small{ font-size:11px; }
    .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:900; }
    .muted{ color:#555; }

    table.formtbl{ width:100%; border-collapse:collapse; table-layout:fixed; font-size:12px; background:#fff; margin-top:8px; }
    .formtbl th, .formtbl td{ border:1px solid #202020; padding:6px 7px; vertical-align:middle; }
    .formtbl th{ background:#edf2f8; text-align:center; font-weight:900; color:#14253d; font-size:12px; }
    .table-tools{ display:flex; justify-content:flex-end; margin:8px 0 0; }
    .table-tools .btn-ui{ padding:6px 10px; font-size:11px; min-width:120px; height:34px; }

    .sign-line{ margin-top:16px; display:flex; gap:20px; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; }
    .sig{ width:48%; min-width:260px; }
    .sig .line{ border-top:1px solid #111; margin-top:34px; height:1px; }
    .sig .lbl{ text-align:center; font-size:12px; margin-top:6px; font-weight:700; }
    .activity-box{ height:80px; border:1px dashed #777; background:#fafafa; }

@media (max-width: 699px){
  .format{
    font-size:11px;
  }

  .sig{
    width:100%;
  }

  .format-toolbar{
    flex-wrap:wrap;
    align-items:flex-start;
  }

  .format-toolbar-title{
    width:100%;
    white-space:normal;
  }

  .format-toolbar-actions{
    width:100%;
    justify-content:flex-start;
    flex-wrap:wrap;
  }

  .btn-ui{
    min-width:180px;
  }
}

    @media print{
      body{ background:#fff; }

      .format-toolbar,
      .table-tools{
        display:none !important;
      }

      .wrap{
        max-width:100%;
        margin:0;
        padding:0;
      }

      .sheet{
        box-shadow:none;
        border:2px solid #000;
        margin-bottom:0;
      }

      .page-break{
        page-break-after:always;
      }

      .in,
      input,
      textarea,
      select{
        border:none !important;
        background:transparent !important;
        box-shadow:none !important;
        outline:none !important;
        padding:0 !important;
        margin:0 !important;
        min-height:auto !important;
        height:auto !important;
        color:#000 !important;
        -webkit-appearance:none !important;
        -moz-appearance:none !important;
        appearance:none !important;
      }

      textarea.in,
      textarea{
        resize:none !important;
        overflow:visible !important;
      }

      .formtbl td .in,
      .format td .in{
        width:100% !important;
        display:block;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>
<div class="wrap">

  <div class="format-toolbar">
  <div class="format-toolbar-title">Conformación del COPASST</div>

  <div class="format-toolbar-actions">
    <button type="button" class="btn-ui secondary" onclick="volverPlanear()">← Atrás</button>
    <button type="button" class="btn-ui secondary" onclick="abrirOtraPestana()">Abrir pestaña</button>
    <button type="button" class="btn-ui secondary" onclick="recargarFormato()">Recargar</button>
    <button type="button" class="btn-ui btn-success" id="btnGuardar">Guardar</button>
    <button type="button" class="btn-ui" onclick="window.print()">Imprimir</button>
  </div>
</div>

<form id="form-sst-dinamico">
    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
          <td class="code-box">0</td>
          <td class="code-box">ANEXO 1</td>
        </tr>
        <tr>
          <td class="subtitle">CONFORMACIÓN COPASST</td>
          <td class="code-box"><input name="anx1_fecha" class="in center" placeholder="AAAA-MM-DD"></td>
          <td class="center"><span class="badge-mod">PLANEAR</span></td>
        </tr>
      </table>

      <div class="sec-h">Convocatoria elecciones del comité paritario de seguridad y salud en el trabajo</div>

      <div class="p">
        Ciudad:
        <input name="anx1_ciudad" class="in inline" placeholder="Ciudad">
        <span class="ms-3">Fecha:
          <input name="anx1_fecha_conv" class="in inline center" placeholder="AAAA-MM-DD">
        </span>
      </div>

      <div class="p">
        El Gerente / Representante Legal de
        <input name="anx1_empresa" class="in inline" style="min-width:320px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
        convoca a todos los trabajadores para elegir sus representantes al Comité Paritario de Seguridad y Salud en el Trabajo, principales y suplentes.
      </div>

      <div class="p">
        La elección se llevará a cabo en las instalaciones de
        <input name="anx1_lugar" class="in inline" style="min-width:300px" placeholder="Lugar / empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
        , el día
        <input name="anx1_fecha_elec" class="in inline center" placeholder="AAAA-MM-DD">
        a las
        <input name="anx1_hora_elec" class="in inline center" placeholder="HH:MM">.
      </div>

      <div class="p bold">Contamos con su participación activa.</div>

      <div class="sign-line">
        <div class="sig">
          <?php if(!empty($firmaRL)): ?>
              <div style="text-align: center;"><img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 50px; object-fit: contain;"></div>
          <?php endif; ?>
          <div class="line" <?= !empty($firmaRL) ? 'style="margin-top: 5px;"' : '' ?>></div>
          <div class="lbl">
            <input name="anx1_gerente_nombre" class="in center" style="border:none;" placeholder="Nombre Gerente / Representante Legal" value="<?= htmlspecialchars($nombreRL) ?>">
          </div>
        </div>
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Firma</div>
        </div>
      </div>
    </div>

    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">INSCRIPCIÓN CANDIDATOS AL COMITÉ PARITARIO DE SST</td>
          <td class="code-box">ANEXO 2</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">CONFORMACIÓN COPASST</td>
          <td class="code-box">Responsable</td>
          <td class="code-box"><input name="anx2_resp" class="in center" placeholder="Nombre" value="<?= htmlspecialchars($nombreSST) ?>"></td>
        </tr>
      </table>

      <div class="sec-h">Inscripción candidatos al comité paritario de seguridad y salud en el trabajo</div>

      <div class="p">
        Periodo:
        <input name="anx2_periodo" class="in inline center" placeholder="AAAA-MM-DD">
      </div>

      <table class="formtbl dynamic-table">
        <colgroup>
          <col style="width:70px">
          <col>
          <col style="width:160px">
          <col style="width:160px">
          <col style="width:160px">
        </colgroup>
        <thead>
          <tr>
            <th>No.</th>
            <th>Nombres y Apellidos</th>
            <th>Cédula</th>
            <th>Cargo</th>
            <th>Área</th>
          </tr>
        </thead>
        <tbody>
          <?php for($r=1;$r<=10;$r++): ?>
            <tr>
              <td class="center row-number"><?= $r ?></td>
              <td><input name="anx2_candidato_nom[]" class="in"></td>
              <td><input name="anx2_candidato_cc[]" class="in center"></td>
              <td><input name="anx2_candidato_cargo[]" class="in"></td>
              <td><input name="anx2_candidato_area[]" class="in"></td>
            </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="p mt-2">
        Fecha de cierre:
        <input name="anx2_cierre_fecha" class="in inline center" placeholder="AAAA-MM-DD">
        <span class="ms-3">Responsable:
          <input name="anx2_cierre_resp" class="in inline" placeholder="Nombre responsable" value="<?= htmlspecialchars($nombreSST) ?>">
        </span>
      </div>

      <div class="sec-h">Anexo 3 / Tarjetón de votación</div>

      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:220px">
          <col style="width:220px">
        </colgroup>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Área</th>
          </tr>
        </thead>
        <tbody>
          <?php for($r=1;$r<=10;$r++): ?>
          <tr>
            <td><input name="anx3_cand_nom[]" class="in"></td>
            <td><input name="anx3_cand_cargo[]" class="in"></td>
            <td><input name="anx3_cand_area[]" class="in"></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="small muted mt-2">
        Este tarjetón es para diligenciamiento o referencia del proceso de votación.
      </div>
    </div>

    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">ACTA APERTURA ELECCIONES CANDIDATOS COPASST</td>
          <td class="code-box">ANEXO 4</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">
            PERIODO:
            <input name="anx4_per_ini" class="in inline center" placeholder="AAAA-MM" style="width:60px;">
            –
            <input name="anx4_per_fin" class="in inline center" placeholder="AAAA-MM" style="width:60px;">
          </td>
          <td class="code-box">Empresa</td>
          <td class="code-box"><input name="anx4_empresa" class="in center" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>"></td>
        </tr>
      </table>

      <div class="p">
        Siendo las
        <input name="anx4_hora_aper" class="in inline center" placeholder="HH:MM">
        del día
        <input name="anx4_fecha_aper" class="in inline center" placeholder="DD-MM-AAAA">,
        se da apertura al proceso de votación para la elección de los representantes al Comité Paritario de Seguridad y Salud en el Trabajo.
      </div>

      <div class="p">
        La votación se llevará a cabo por medio virtual y el cierre será el
        <input name="anx4_fecha_cierre" class="in inline center" placeholder="DD-MM-AAAA">
        a las
        <input name="anx4_hora_cierre" class="in inline center" placeholder="HH:MM">.
      </div>

      <div class="sec-h">Veedores del proceso</div>
      <div class="p">1) <input name="anx4_veedor1" class="in" placeholder="Nombre completo, área y cédula"></div>
      <div class="p">2) <input name="anx4_veedor2" class="in" placeholder="Nombre completo, área y cédula"></div>

      <div class="sign-line">
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">
            <input name="anx4_jurado1" class="in center" style="border:none;" placeholder="Firma jurado votación — Nombre">
          </div>
        </div>
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">
            <input name="anx4_jurado2" class="in center" style="border:none;" placeholder="Firma jurado votación — Nombre">
          </div>
        </div>
      </div>
    </div>

    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">REGISTRO ASISTENCIA VOTACIÓN – COPASST</td>
          <td class="code-box">ANEXO 5</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">PERIODO: <input name="anx5_per" class="in inline center" placeholder="AAAA-MM-DD"></td>
          <td class="code-box">Fecha</td>
          <td class="code-box"><input name="anx5_fecha" class="in center" placeholder="AAAA-MM-DD"></td>
        </tr>
      </table>

      <div class="p">
        Empresa:
        <input name="anx5_empresa" class="in inline" style="min-width:420px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
      </div>

      <table class="formtbl dynamic-table">
        <colgroup>
          <col style="width:70px">
          <col>
          <col style="width:160px">
          <col style="width:180px">
          <col style="width:220px">
        </colgroup>
        <thead>
          <tr>
            <th>No.</th>
            <th>Nombre y Apellidos</th>
            <th>Cédula</th>
            <th>Área</th>
            <th>Firma</th>
          </tr>
        </thead>
        <tbody>
          <?php for($r=1;$r<=12;$r++): ?>
          <tr>
            <td class="center row-number"><?= $r ?></td>
            <td><input name="anx5_nom[]" class="in"></td>
            <td><input name="anx5_cc[]" class="in center"></td>
            <td><input name="anx5_area[]" class="in"></td>
            <td><input name="anx5_firma[]" class="in"></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>
    </div>

    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">ACTA CIERRE VOTACIONES AL COMITÉ DE SST</td>
          <td class="code-box">ANEXO 6</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">PERIODO: <input name="anx6_per" class="in inline center" placeholder="AAAA-MM-DD"></td>
          <td class="code-box">Fecha</td>
          <td class="code-box"><input name="anx6_fecha" class="in center" placeholder="AAAA-MM-DD"></td>
        </tr>
      </table>

      <div class="p">
        Siendo las
        <input name="anx6_hora" class="in inline center" placeholder="HH:MM">,
        del día
        <input name="anx6_dia" class="in inline center" placeholder="DD-MM-AAAA">,
        la empresa
        <input name="anx6_empresa" class="in inline" style="min-width:300px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
        da por terminado el proceso de votación para elección de representantes del trabajador al Comité Paritario de SST.
      </div>

      <div class="p bold">Como veedores de votación, se encargaron:</div>
      <div class="p">1) <input name="anx6_veedor1" class="in" placeholder="Nombre completo"></div>
      <div class="p">2) <input name="anx6_veedor2" class="in" placeholder="Nombre completo"></div>

      <div class="sec-h">Resultados obtenidos</div>
      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:160px">
        </colgroup>
        <thead>
          <tr>
            <th>Candidato</th>
            <th>Votos</th>
          </tr>
        </thead>
        <tbody>
          <?php for($r=1;$r<=10;$r++): ?>
          <tr>
            <td><input name="anx6_cand[]" class="in"></td>
            <td><input name="anx6_votos[]" class="in center" placeholder="0"></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="sec-h">Elegidos</div>
      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:220px">
        </colgroup>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Rol</th>
          </tr>
        </thead>
        <tbody>
          <tr><td><input name="anx6_eleg_nom[]" class="in"></td><td><input name="anx6_eleg_rol[]" class="in center" placeholder="Principal"></td></tr>
          <tr><td><input name="anx6_eleg_nom[]" class="in"></td><td><input name="anx6_eleg_rol[]" class="in center" placeholder="Suplente"></td></tr>
          <tr><td><input name="anx6_eleg_nom[]" class="in"></td><td><input name="anx6_eleg_rol[]" class="in center" placeholder="Principal"></td></tr>
          <tr><td><input name="anx6_eleg_nom[]" class="in"></td><td><input name="anx6_eleg_rol[]" class="in center" placeholder="Suplente"></td></tr>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="sign-line">
        <div class="sig">
          <div class="line"></div>
          <div class="lbl"><input name="anx6_jurado1" class="in center" style="border:none;" placeholder="Nombre jurado"></div>
        </div>
        <div class="sig">
          <div class="line"></div>
          <div class="lbl"><input name="anx6_jurado2" class="in center" style="border:none;" placeholder="Nombre jurado"></div>
        </div>
      </div>
      <div class="sign-line">
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Firma</div>
        </div>
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Firma</div>
        </div>
      </div>
    </div>

    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">ACTA DE CONSTITUCIÓN DEL COPASST</td>
          <td class="code-box">ANEXO 7</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">Constitución del Comité Paritario de SST</td>
          <td class="code-box">Fecha</td>
          <td class="code-box"><input name="anx7_fecha_doc" class="in center" placeholder="DD-MM-AAAA"></td>
        </tr>
      </table>

      <div class="p">
        El día
        <input name="anx7_fecha_elec" class="in inline center" placeholder="DD-MM-AAAA">,
        se eligió el Comité Paritario de Seguridad y Salud en el Trabajo (COPASST) o Vigía SST de la empresa
        <input name="anx7_empresa" class="in inline" style="min-width:300px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">,
        dando cumplimiento a la Resolución 2013 de 1986 y al Decreto 1295 de 1994.
      </div>

      <div class="p">
        La modalidad utilizada para su elección fue:
        <span class="bold">Votación</span>
      </div>

      <div class="sec-h">Representantes por parte del trabajador</div>
      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:220px">
          <col style="width:220px">
        </colgroup>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Área / Cargo</th>
          </tr>
        </thead>
        <tbody>
          <tr><td><input name="anx7_rep_trab_nom[]" class="in"></td><td><input name="anx7_rep_trab_rol[]" class="in center" placeholder="Principal"></td><td><input name="anx7_rep_trab_area[]" class="in"></td></tr>
          <tr><td><input name="anx7_rep_trab_nom[]" class="in"></td><td><input name="anx7_rep_trab_rol[]" class="in center" placeholder="Suplente"></td><td><input name="anx7_rep_trab_area[]" class="in"></td></tr>
          <tr><td><input name="anx7_rep_trab_nom[]" class="in"></td><td><input name="anx7_rep_trab_rol[]" class="in center" placeholder="Principal"></td><td><input name="anx7_rep_trab_area[]" class="in"></td></tr>
          <tr><td><input name="anx7_rep_trab_nom[]" class="in"></td><td><input name="anx7_rep_trab_rol[]" class="in center" placeholder="Suplente"></td><td><input name="anx7_rep_trab_area[]" class="in"></td></tr>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="sec-h">Representantes por parte de la empresa</div>
      <div class="p">
        El representante legal / gerente designa a las siguientes personas:
      </div>

      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:220px">
          <col style="width:220px">
        </colgroup>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Área / Cargo</th>
          </tr>
        </thead>
        <tbody>
          <tr><td><input name="anx7_rep_emp_nom[]" class="in"></td><td><input name="anx7_rep_emp_rol[]" class="in center" placeholder="Principal"></td><td><input name="anx7_rep_emp_area[]" class="in"></td></tr>
          <tr><td><input name="anx7_rep_emp_nom[]" class="in"></td><td><input name="anx7_rep_emp_rol[]" class="in center" placeholder="Suplente"></td><td><input name="anx7_rep_emp_area[]" class="in"></td></tr>
          <tr><td><input name="anx7_rep_emp_nom[]" class="in"></td><td><input name="anx7_rep_emp_rol[]" class="in center" placeholder="Principal"></td><td><input name="anx7_rep_emp_area[]" class="in"></td></tr>
          <tr><td><input name="anx7_rep_emp_nom[]" class="in"></td><td><input name="anx7_rep_emp_rol[]" class="in center" placeholder="Suplente"></td><td><input name="anx7_rep_emp_area[]" class="in"></td></tr>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="p mt-3">
        Como presidente del Comité quedó designado:
        <input name="anx7_pte" class="in inline" style="min-width:320px" placeholder="Nombre">
      </div>

      <div class="sign-line">
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Firma del Presidente</div>
        </div>
        <div class="sig">
          <?php if(!empty($firmaRL)): ?>
              <div style="text-align: center;"><img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 50px; object-fit: contain;"></div>
          <?php endif; ?>
          <div class="line" <?= !empty($firmaRL) ? 'style="margin-top: 5px;"' : '' ?>></div>
          <div class="lbl">Representante Legal <br><span style="font-weight:normal;"><?= htmlspecialchars($nombreRL) ?></span></div>
        </div>
      </div>
    </div>

    <div class="sheet page-break">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">ACTA DE REUNIÓN MENSUAL COPASST</td>
          <td class="code-box">ANEXO 8</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">FORMATO ACTA DE REUNIÓN MENSUAL COMITÉ PARITARIO SST</td>
          <td class="code-box">Fecha</td>
          <td class="code-box"><input name="anx8_fecha" class="in center" placeholder="DD-MM-AAAA"></td>
        </tr>
      </table>

      <div class="p bold">
        Nombre empresa:
        <input name="anx8_empresa" class="in inline" style="min-width:420px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
      </div>

      <div class="row g-2">
        <div class="col-md-6">
          <div class="p">Hora de inicio: <input name="anx8_hora_ini" class="in inline center" placeholder="HH:MM"></div>
        </div>
        <div class="col-md-6">
          <div class="p">Hora de finalización: <input name="anx8_hora_fin" class="in inline center" placeholder="HH:MM"></div>
        </div>
      </div>

      <div class="p">
        Acta de reunión No.:
        <input name="anx8_acta_no" class="in inline center" placeholder="_____">
      </div>

      <div class="sec-h">Asistentes e invitados</div>
      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:260px">
        </colgroup>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Firma</th>
          </tr>
        </thead>
        <tbody>
          <?php for($r=1;$r<=8;$r++): ?>
          <tr>
            <td><input name="anx8_asis_nom[]" class="in"></td>
            <td><input name="anx8_asis_firma[]" class="in"></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="sec-h">Orden del día</div>
      <div class="p">1) <input name="anx8_orden1" class="in" placeholder=""></div>
      <div class="p">2) <input name="anx8_orden2" class="in" placeholder=""></div>
      <div class="p">3) <input name="anx8_orden3" class="in" placeholder=""></div>
      <div class="p">4) <input name="anx8_orden4" class="in" placeholder=""></div>

      <div class="sec-h">Desarrollo de la reunión</div>
      <textarea name="anx8_desarrollo" class="in" rows="6" placeholder="Escriba aquí el desarrollo..."></textarea>

      <div class="sec-h">Definición de tareas</div>
      <div class="small muted mb-2">Convenciones del estado: A = Abierta · C = Cerrada · P = Proceso</div>

      <table class="formtbl dynamic-table">
        <colgroup>
          <col>
          <col style="width:140px">
          <col style="width:140px">
          <col style="width:130px">
        </colgroup>
        <thead>
          <tr>
            <th>Tarea / Actividad</th>
            <th>Responsable</th>
            <th>Fecha</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php for($r=1;$r<=8;$r++): ?>
          <tr>
            <td><input name="anx8_tarea_act[]" class="in"></td>
            <td><input name="anx8_tarea_resp[]" class="in center"></td>
            <td><input name="anx8_tarea_fecha[]" class="in center" placeholder="DD-MM-AAAA"></td>
            <td><input name="anx8_tarea_est[]" class="in center" placeholder="A/C/P"></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
      <div class="table-tools">
        <button type="button" class="btn-ui secondary add-row-btn">+ Agregar fila</button>
      </div>

      <div class="p mt-3">
        Fecha próxima reunión:
        <input name="anx8_prox_fecha" class="in inline center" placeholder="DD-MM-AAAA">
        <span class="ms-3">Hora próxima reunión:
          <input name="anx8_prox_hora" class="in inline center" placeholder="HH:MM">
        </span>
      </div>

      <div class="sign-line">
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Firma del Presidente</div>
        </div>
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Firma del Secretario</div>
        </div>
      </div>
    </div>

    <div class="sheet">
      <table class="format">
        <colgroup>
          <col style="width:230px">
          <col>
          <col style="width:150px">
          <col style="width:160px">
        </colgroup>
        <tr>
          <td rowspan="2">
              <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '1px dashed #666' : 'none' ?>; background: <?= empty($logoEmpresaUrl) ? '#fafafa' : 'transparent' ?>; padding: 0;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">TU LOGO<br>AQUÍ</div>
                <?php endif; ?>
              </div>
          </td>
          <td class="title">ACTA DE NOMBRAMIENTO DEL VIGÍA EN SST</td>
          <td class="code-box">FORMATO</td>
          <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
        </tr>
        <tr>
          <td class="subtitle">Designación Vigía SST</td>
          <td class="code-box">Fecha</td>
          <td class="code-box"><input name="vig_fecha_doc" class="in center" placeholder="DD/MM/AA"></td>
        </tr>
      </table>

      <div class="p">
        Nombre de la empresa:
        <input name="vig_empresa" class="in inline" style="min-width:320px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
        <span class="ms-3">NIT:
          <input name="vig_nit" class="in inline center" placeholder="">
        </span>
      </div>

      <div class="p">
        En cumplimiento a lo establecido en la Resolución 2013 de 1986, el representante legal
        <input name="vig_gerente" class="in inline" style="min-width:260px" placeholder="Nombre del Gerente / RL" value="<?= htmlspecialchars($nombreRL) ?>">
        de la empresa
        <input name="vig_empresa2" class="in inline" style="min-width:260px" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
        designa como Vigía en SST al Señor(a):
      </div>

      <div class="p">
        Nombre:
        <input name="vig_nom" class="in inline" style="min-width:320px" placeholder="Nombre del vigía">
        <span class="ms-3">Cargo:
          <input name="vig_cargo" class="in inline" style="min-width:240px" placeholder="">
        </span>
      </div>

      <div class="p bold">Y como suplente al Señor(a):</div>

      <div class="p">
        Nombre:
        <input name="vig_sup_nom" class="in inline" style="min-width:320px" placeholder="Nombre del suplente">
        <span class="ms-3">Cargo:
          <input name="vig_sup_cargo" class="in inline" style="min-width:240px" placeholder="">
        </span>
      </div>

      <div class="p">
        Por un periodo de dos (2) años comprendido entre:
        Inicio
        <input name="vig_per_ini" class="in inline center" placeholder="DD-MM-AAAA">
        y Finalización
        <input name="vig_per_fin" class="in inline center" placeholder="DD-MM-AAAA">,
        de conformidad con el Decreto 1295 de 1994.
      </div>

      <div class="p">
        La presente se firma el
        <input name="vig_fecha_firma" class="in inline center" placeholder="DD/MM/AA">.
      </div>

      <div class="sign-line">
        <div class="sig">
          <?php if(!empty($firmaRL)): ?>
              <div style="text-align: center;"><img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 50px; object-fit: contain;"></div>
          <?php endif; ?>
          <div class="line" <?= !empty($firmaRL) ? 'style="margin-top: 5px;"' : '' ?>></div>
          <div class="lbl">Representante Legal <br><span style="font-weight:normal;"><?= htmlspecialchars($nombreRL) ?></span></div>
        </div>
        <div class="sig">
          <div class="line"></div>
          <div class="lbl">Vigía SST</div>
        </div>
      </div>

      <div class="sec-h">Evaluación de conocimientos (post-capacitación)</div>

      <div class="p bold">¿Qué es el Comité Paritario de Seguridad y Salud en el Trabajo?</div>
      <textarea name="vig_eval_q1" class="in" rows="3"></textarea>

      <div class="p bold mt-3">Escriba FALSO (F) ó VERDADERO (V) según corresponda:</div>

      <table class="formtbl">
        <colgroup>
          <col>
          <col style="width:120px">
        </colgroup>
        <thead>
          <tr>
            <th>Enunciado</th>
            <th>F / V</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>El COPASST debe estar conformado por igual número de representantes del empleador y de los trabajadores.</td>
            <td class="center"><input name="vig_eval_vf1" class="in center" placeholder="F/V"></td>
          </tr>
          <tr>
            <td>El COPASST debe velar por el desarrollo del SGSST de la empresa.</td>
            <td class="center"><input name="vig_eval_vf2" class="in center" placeholder="F/V"></td>
          </tr>
          <tr>
            <td>El presidente lo elige el comité en votación y el secretario lo elige el Representante Legal.</td>
            <td class="center"><input name="vig_eval_vf3" class="in center" placeholder="F/V"></td>
          </tr>
          <tr>
            <td>El COPASST debe solicitar periódicamente informes sobre accidentalidad y enfermedades laborales.</td>
            <td class="center"><input name="vig_eval_vf4" class="in center" placeholder="F/V"></td>
          </tr>
        </tbody>
      </table>

      <div class="p bold mt-3">Relacione con una línea:</div>
      <div class="p muted">(Espacio para actividad / relación)</div>
      <div class="activity-box"></div>

      <div class="p mt-3">
        Firma del trabajador:
        <input name="vig_firma_trab" class="in inline" style="min-width:340px" placeholder="">
      </div>

      <div class="text-center mt-3 small muted">Gracias por su participación</div>
    </div>

  </form>
</div>

<script>
  function volverPlanear(){
    try{
      if(window.parent && window.parent !== window){
        window.parent.location.href = '../planear.php';
      }else{
        window.location.href = '../planear.php';
      }
    }catch(e){
      window.location.href = '../planear.php';
    }
  }

  function recargarFormato(){
    if(confirm('¿Deseas recargar y limpiar el formato? Se perderán los datos no guardados.')){
      window.location.reload();
    }
  }

  // --- LÓGICA DE TABLAS DINÁMICAS ---
  function addRowToTable(table) {
    if(!table || !table.classList.contains('dynamic-table')) return;
    const tbody = table.querySelector('tbody');
    const firstRow = tbody.querySelector('tr');
    if(!firstRow) return;

    const newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input, textarea').forEach(function(el){ el.value = ''; });
    tbody.appendChild(newRow);

    const numberedRows = tbody.querySelectorAll('.row-number');
    numberedRows.forEach(function(cell, index){ cell.textContent = index + 1; });
  }

  document.querySelectorAll('.table-tools .add-row-btn').forEach(function(button){
    button.addEventListener('click', function(){
      addRowToTable(this.closest('.table-tools').previousElementSibling);
    });
  });

  // --- INYECCIÓN DE DATOS DESDE PHP ---
  document.addEventListener('DOMContentLoaded', function () {
    let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
    if (typeof datosGuardados === 'string') {
        try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
    }

    <?php if(isset($errorCarga)) echo "console.warn('Advertencia API:', " . json_encode($errorCarga) . ");"; ?>

    if (datosGuardados && Object.keys(datosGuardados).length > 0) {
        for (const [key, value] of Object.entries(datosGuardados)) {
            if (Array.isArray(value)) {
                let campos = document.querySelectorAll(`[name="${key}[]"]`);
                
                // Si la BD tiene más filas de las que hay en el HTML, generamos las que faltan
                if (campos.length > 0 && campos.length < value.length) {
                    const table = campos[0].closest('.dynamic-table');
                    while(document.querySelectorAll(`[name="${key}[]"]`).length < value.length) {
                        addRowToTable(table);
                    }
                    campos = document.querySelectorAll(`[name="${key}[]"]`);
                }

                value.forEach((val, i) => {
                    if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                });
            } else {
                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
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

    // Convertir FormData a JSON, agrupando los arrays (name="campo[]")
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
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Guardando...';
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
                confirmButtonColor: '#1f5fa8'
            });
        } else {
            Swal.fire({
                title: 'Error al guardar',
                text: result.error || "No se pudo completar la operación.",
                icon: 'error',
                confirmButtonColor: '#184d88'
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo contactar al servidor para guardar.',
            icon: 'error',
            confirmButtonColor: '#184d88'
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