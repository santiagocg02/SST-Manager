<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
// OJO: Ajusta la cantidad de "../" si estás en una subcarpeta distinta.
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos (Ej: 21 para "2.1.1")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 21; 

// --- Lógica de Empresa Optimizada (Logo, Nombres, Documentos y Firmas) ---
$nombreEmpresaLogeada = "NOMBRE DE LA EMPRESA";
$logoEmpresaUrl = "";
$nombreRL = "";
$ccRL = "";
$firmaRL = "";
$actividadEmpresa = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $nombreEmpresaLogeada = $empData['nombre_empresa'] ?? 'NOMBRE DE LA EMPRESA';
        $actividadEmpresa = $empData['actividad_economica'] ?? '';
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        
        // Priorizando los campos con terminación _rl
        $nombreRL = $empData['nombre_rl'] ?? $empData['representante_legal'] ?? '';
        $ccRL = $empData['documento_rl'] ?? $empData['cc_rl'] ?? $empData['cedula_rl'] ?? '';
        $firmaRL = $empData['firma_rl'] ?? $empData['firma_representante'] ?? '';
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
  <title>2.1.1 - Políticas de Seguridad y Salud en el Trabajo</title>

  <link rel="stylesheet" href="../../../assets/css/toolbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
      background: #d9dde2;
      padding: 10px 16px;
      border: 1px solid #c8cdd3;
      border-radius: 6px;
    }
    .btn-action{
      border:1px solid #cfd6e4;
      background:#fff;
      color: #2f62b6;
      padding:6px 12px;
      border-radius:6px;
      font-weight:800;
      cursor:pointer;
      font-size:12px;
    }
    .btn-action:hover { background: #eef4ff; }
    .btn-primary-action{
      border-color:#1b4fbd;
      background:#1b4fbd;
      color:#fff;
      padding:6px 12px;
      border-radius:6px;
      font-weight:800;
      cursor:pointer;
      font-size:12px;
    }
    .btn-primary-action:hover { background: #0f3484; }
    .btn-success-action {
      border: 1px solid #198754;
      background: #198754;
      color: #fff;
      padding:6px 12px;
      border-radius:6px;
      font-weight:800;
      cursor:pointer;
      font-size:12px;
    }
    .btn-success-action:hover { background: #146c43; }

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
      border:2px dashed #b5b5b5;
      height:68px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:900;
      color:rgba(0,0,0,.35);
      text-align:center;
      font-size:11px;
      padding:4px;
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
      page-break-inside: avoid;
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
      margin-bottom:10px;
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

    /* Estilo transparente para inputs integrados en celdas o párrafos editables */
    .sst-input-trans{
      border:none; outline:none; background:transparent; font-family:Arial, sans-serif; color:#111; width:100%; padding:0; margin:0;
    }
    .sst-input-trans.bold{ font-weight:900; }
    .sst-input-trans.center{ text-align:center; }
    
    /* Textareas adaptables sin bordes para los bloques informales */
    .sst-textarea-trans{
      width:100%; border:none; outline:none; background:transparent; font-family:Arial, sans-serif;
      color:#1a1a1a; font-size:14px; line-height:1.7; text-align:justify; padding:0; margin:0; resize:none; overflow:hidden;
    }
    .sst-textarea-trans.li-type{ line-height:1.6; }
    .sst-textarea-trans.title-type{ font-weight:900; font-size:18px; text-align:center; text-transform:uppercase; margin-bottom:20px; }

    .small-note{
      font-size:12px;
      color:#5c6670;
      margin-top:10px;
    }

    @media print{
      body{ background:#fff; }
      .toolbar, .sst-toolbar{ display:none !important; }
      .sheet{
        box-shadow:none;
        margin-bottom:0;
        border:2px solid #000;
      }
      .in {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        height: auto !important;
        border-bottom: 1px solid #000 !important;
        border-radius: 0 !important;
      }
      .sst-input-trans, .sst-textarea-trans{ color:#000 !important; }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
  <div class="wrap">

    <div class="sst-toolbar">
      <h1 class="sst-toolbar-title">POLÍTICAS SG-SST</h1>

      <div class="sst-toolbar-actions">
        <a href="#" class="btn btn-secondary btn-sm">Volver</a>

        <button type="button" class="btn btn-success btn-sm" id="btnGuardar">
          <i class="fa-solid fa-save"></i> Guardar
        </button>

        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
          <i class="fa-solid fa-print"></i> Imprimir
        </button>
      </div>
    </div>

    <form id="form-sst-dinamico">
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
                <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 55px; object-fit: contain;">
                    <?php else: ?>
                        LOGO EMPRESA
                    <?php endif; ?>
                </div>
              </td>
              <td class="title">
                <textarea name="meta_sistema_titulo" class="sst-input-trans bold center" style="font-size:13px; resize:none; overflow:hidden; height:32px;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</textarea>
              </td>
              <td><strong>Versión:</strong> <input type="text" name="meta_version" class="sst-input-trans bold" value="0" style="width:30px; display:inline-block;"></td>
              <td><strong>Fecha:</strong><br><input type="date" name="meta_fecha" id="metaFecha" style="width:100%; border:none; font-size:11px; font-weight:900; outline:none; background:transparent;"></td>
            </tr>
            <tr>
              <td class="subtitle">
                <input type="text" name="meta_formato_titulo" class="sst-input-trans bold center" value="POLÍTICAS DE SEGURIDAD Y SALUD EN EL TRABAJO" style="font-size:12px;">
              </td>
              <td class="code-box" colspan="2">
                <input type="text" name="meta_codigo" class="sst-input-trans bold center" value="2.1.1" style="font-size:12px;">
              </td>
            </tr>
            <tr>
              <td colspan="3"><strong>Proceso:</strong> <input type="text" name="meta_proceso" class="sst-input-trans" value="Gestión de Seguridad y Salud en el Trabajo" style="width:80%; display:inline-block;"></td>
            </tr>
          </table>

          <div class="sec-h">
            <input type="text" name="barra_titulo_1" class="sst-input-trans bold" value="Política SG SST" style="color:#10233c; font-size:15px; text-transform:uppercase;">
          </div>
          
          <div class="policy-box">
            <textarea name="pol1_titulo_interno" class="sst-textarea-trans title-type" rows="1" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Política SG SST</textarea>

            <p class="policy-p">
              La empresa
              <input type="text" name="pol1_empresa" class="in inline" placeholder="Nombre de la empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
              dedicada a
              <input type="text" name="pol1_actividad" class="in inline long" placeholder="Actividad económica principal" value="<?= htmlspecialchars($actividadEmpresa) ?>">
            </p>

            <p class="policy-p">
              <textarea name="pol1_parrafo_2" class="sst-textarea-trans" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Declara su compromiso con la legislación vigente en materia de seguridad y salud en el trabajo.</textarea>
            </p>

            <p class="policy-p">
              <textarea name="pol1_parrafo_3" class="sst-textarea-trans" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Considera que todo accidente de trabajo y enfermedad laboral puede prevenirse, por tal motivo, se compromete al mejoramiento de las condiciones de trabajo y la protección de la integridad física, mental y social de sus trabajadores, colaboradores, contratistas, visitors y partes interesadas.</textarea>
            </p>

            <p class="policy-p">
              <textarea name="pol1_parrafo_4" class="sst-textarea-trans" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Garantiza una oportuna identificación, evaluación, control y/o eliminación de los riesgos que pueden afectar la salud y calidad de vida de los trabajadores; así como el mejoramiento continuo en su gestión por la prevención de riesgos laborales.</textarea>
            </p>

            <p class="policy-p">
              <textarea name="pol1_parrafo_5" class="sst-textarea-trans" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Asigna los recursos humanos, físicos y financieros requeridos para el normal funcionamiento del sistema de gestión.</textarea>
            </p>

            <p class="policy-p">
              <textarea name="pol1_parrafo_6" class="sst-textarea-trans" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Para el éxito de la gestión de la seguridad y salud en el trabajo, se requiere del compromiso de la gerencia y participación de todos, reflejando el cumplimiento de las normas y procedimientos establecidos por la legislación colombiana en materia de prevención.</textarea>
            </p>

            <div class="firma-wrap">
              <div class="firma-label"><input type="text" name="pol1_label_firma" class="sst-input-trans" value="Firma del representante legal," style="font-size:14px;"></div>
              
              <?php if(!empty($firmaRL)): ?>
                  <img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 50px; display: block; margin-bottom: 5px;">
              <?php else: ?>
                  <div style="height: 50px;"></div>
              <?php endif; ?>

              <div class="firma-line"></div>

              <div class="mb-2">
                <input type="text" name="pol1_rl_nombre" class="in" placeholder="Nombre del representante legal" value="<?= htmlspecialchars($nombreRL) ?>">
              </div>

              <div class="mb-2">
                <input type="text" name="pol1_rl_cc" class="in" placeholder="C.C. del representante legal" value="<?= htmlspecialchars($ccRL) ?>">
              </div>

              <div class="firma-role"><input type="text" name="pol1_cargo_rl" class="sst-input-trans" value="Representante legal" style="font-size:14px;"></div>

              <div class="firma-date">
                Fecha de emisión:
                <input type="date" name="pol1_fecha_emision" class="in inline" placeholder="Día / mes / año">
              </div>
            </div>
          </div>

          <div class="sec-h">
            <input type="text" name="barra_titulo_2" class="sst-input-trans bold" value="Política de prevención al consumo de alcohol, tabaco y sustancias psicoactivas" style="color:#10233c; font-size:15px; text-transform:uppercase;">
          </div>
          
          <div class="policy-box">
            <textarea name="pol2_titulo_interno" class="sst-textarea-trans title-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Política de prevención al consumo de alcohol, tabaco y sustancias psicoactivas</textarea>

            <p class="policy-p">
              La empresa
              <input type="text" name="pol2_empresa" class="in inline" placeholder="Nombre de la empresa" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">,
              dedicada a
              <input type="text" name="pol2_actividad" class="in inline long" placeholder="Actividad económica principal" value="<?= htmlspecialchars($actividadEmpresa) ?>">.
              Se compromete al desarrollo del sistema de gestión de la seguridad y salud en el trabajo, por lo cual:
            </p>

            <div class="policy-list-container" id="listaPoliticaConsumo" style="margin-top: 15px;">
              
              <div class="d-flex align-items-start mb-2 sst-politica-item">
                <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
                <textarea name="pol2_li_1" class="sst-textarea-trans li-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Acatará la legislación colombiana respecto al consumo de alcohol, tabaco y sustancias psicoactivas en el lugar de trabajo.</textarea>
              </div>

              <div class="d-flex align-items-start mb-2 sst-politica-item">
                <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
                <textarea name="pol2_li_2" class="sst-textarea-trans li-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Está prohibido ingresar, regalar, vender, mantener y consumir bebidas alcohólicas o sustancias psicoactivas en el lugar de trabajo.</textarea>
              </div>

              <div class="d-flex align-items-start mb-2 sst-politica-item">
                <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
                <textarea name="pol2_li_3" class="sst-textarea-trans li-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Está prohibido llegar a trabajar bajo los efectos del alcohol, narcóticos o cualquier otra droga enervante, solo cuando afecte directamente el desempeño laboral del trabajador y/o bienestar común de los funcionarios de la organización.</textarea>
              </div>

              <div class="d-flex align-items-start mb-2 sst-politica-item">
                <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
                <textarea name="pol2_li_4" class="sst-textarea-trans li-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Está prohibido fumar dentro de las instalaciones de la empresa.</textarea>
              </div>

              <div class="d-flex align-items-start mb-2 sst-politica-item">
                <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
                <textarea name="pol2_li_5" class="sst-textarea-trans li-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Promoveremos con nuestros proveedores y contratistas la adopción de políticas frente al consumo de alcohol, tabaco y sustancias psicoactivas congruentes con la nuestra.</textarea>
              </div>

              <div class="d-flex align-items-start mb-2 sst-politica-item">
                <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
                <textarea name="pol2_li_6" class="sst-textarea-trans li-type" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Revisaremos cada año la política y la actualizaremos de ser necesario.</textarea>
              </div>

            </div>

            <div class="mt-2 mb-3 print-hide">
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="agregarPuntoConsumo()">
                <i class="fa-solid fa-plus"></i> Agregar punto a la política
              </button>
            </div>

            <div class="firma-wrap">
              <div class="firma-label"><input type="text" name="pol2_label_firma" class="sst-input-trans" value="Firma del representante legal," style="font-size:14px;"></div>
              
              <?php if(!empty($firmaRL)): ?>
                  <img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height: 50px; display: block; margin-bottom: 5px;">
              <?php else: ?>
                  <div style="height: 50px;"></div>
              <?php endif; ?>

              <div class="firma-line"></div>

              <div class="mb-2">
                <input type="text" name="pol2_rl_nombre" class="in" placeholder="Nombre del representante legal" value="<?= htmlspecialchars($nombreRL) ?>">
              </div>

              <div class="mb-2">
                <input type="text" name="pol2_rl_cc" class="in" placeholder="C.C. del representante legal" value="<?= htmlspecialchars($ccRL) ?>">
              </div>

              <div class="firma-role"><input type="text" name="pol2_cargo_rl" class="sst-input-trans" value="Representante legal" style="font-size:14px;"></div>

              <div class="firma-date">
                Fecha de emisión:
                <input type="date" name="pol2_fecha_emision" class="in inline" placeholder="Día / mes / año">
              </div>
            </div>
          </div>

          <div class="small-note print-hide">
            Formato 2.1.1 listo para diligenciar e imprimir.
          </div>

        </div>
    </form>
  </div>

<script>
  function setHoy(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");
    const fmeta = document.getElementById("metaFecha");
    if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;
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
            const campo = document.querySelector(`[name="${key}"]`);
            if (campo) {
                campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
            }
        }
    }

    // Auto-ajustar alturas de los textareas dinámicos al cargar el DOM
    document.querySelectorAll('.sst-textarea-trans').forEach(textarea => {
        textarea.style.height = '';
        textarea.style.height = textarea.scrollHeight + 'px';
    });
  });

  // --- LÓGICA DE GUARDADO ---
  document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form-sst-dinamico');
    const formData = new FormData(form);
    const datosJSON = {};

    for (const [key, value] of formData.entries()) {
        datosJSON[key] = value;
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
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
                text: 'Políticas guardadas correctamente',
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

  // Función para agregar nuevos puntos alineados con viñetas de manera dinámica
  function agregarPuntoConsumo() {
    const contenedor = document.getElementById('listaPoliticaConsumo');
    // Cuenta cuántos elementos existen para asignar el siguiente name secuencial
    const totalItems = contenedor.querySelectorAll('.sst-politica-item').length;
    const siguienteIndex = totalItems + 1;

    // Crear el nuevo nodo con clases de alineación de Bootstrap (Flexbox)
    const nuevoItem = document.createElement('div');
    nuevoItem.className = 'd-flex align-items-start mb-2 sst-politica-item';
    nuevoItem.innerHTML = `
      <span style="font-size: 14px; margin-right: 10px; line-height: 1.7;">•</span>
      <textarea name="pol2_li_${siguienteIndex}" class="sst-textarea-trans li-type" placeholder="Escribe el nuevo compromiso de la política aquí..." oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'" style="height: 30px;"></textarea>
    `;

    contenedor.appendChild(nuevoItem);
    
    // Hace foco automático en el nuevo campo creado
    nuevoItem.querySelector('textarea').focus();
  }
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>