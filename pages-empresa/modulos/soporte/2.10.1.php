<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 38; 

// --- Lógica de Empresa Optimizada (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
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

// Textos por defecto si no existen datos guardados aún
$bloque1 = [
    "Entrega certificado de afiliación vigente a salud y pensiones",
    "Entrega carta de intención de afiliarse o no a la ARL",
    "Si requiere tener subcontratistas, se exige los mismos documentos a estos"
];

$bloque2 = [
    "Copia de los planillas de pago a EPS, AFP y ARL",
    "Todo el personal de contratistas sin excepción, deben acreditar haber recibido inducción en temas SST de PARQUE LA EMPRESA en caso de subcontratación, deberá hacerla conocer y cumplir a sus Subcontratistas.",
    "Conocer y cumplir la Política de Prevención de Consumo de No Alcohol, Tabaco y Sustancias Psicoactivas",
    "Certificado de vacunaciones aplicables para las zonas endémicas a donde viaja por razón de las actividades a ejecutar con la EMPRESA",
    "Usan ropa adecuada y EPP acordes a la actividad y factores de riesgo presentes",
    "Los EPP utilizados cumplen con las especificaciones técnicas exigidas por la legislación colombiana",
    "Reporta los accidentes de trabajo a su respectiva ARL",
    "El contratista mantiene equipos de emergencias como botiquín extintores, gabinetes contra incendio, entre otros. ¿Libres de obstáculos?",
    "En caso de emergencia acatan las orientaciones dadas por el funcionario de la EMPRESA y la señalización de emergencia"
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RE-SST-16 | Lista de chequeo para verificación de requerimientos generales del SG-SST para persona natural</title>

    <link rel="stylesheet" href="../../../assets/css/toolbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --page-bg:#f3f6fa;
            --paper:#ffffff;
            --line:#9aa7b3;
            --line-soft:#c8d0d8;
            --top:#eef2f6;
            --blue:#8faad1;
            --blue-dark:#375b84;
            --text:#1f2937;
            --muted:#6b7280;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
            --green:#198754;
            --green-hover:#146c43;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            font-family:Arial, Helvetica, sans-serif;
            background:var(--page-bg);
            color:var(--text);
        }

        .page-wrap{
            padding:20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sheet-card{
            background:var(--paper);
            border:1px solid #d7dee6;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 8px 24px rgba(31,41,55,.08);
            margin-bottom: 20px;
        }

        .sheet-scroll{
            width:100%;
            overflow:auto;
            background:#fff;
        }

        .sheet{
            min-width:1180px;
            background:#fff;
        }

        table.form-sheet{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .form-sheet td,
        .form-sheet th{
            border:1px solid var(--line-soft);
            padding:0;
            vertical-align:middle;
        }

        .top-title{
            background:var(--top);
            text-align:center;
            font-weight:800;
            font-size:15px;
            line-height:1.2;
            text-transform:uppercase;
            padding:12px 14px !important;
        }

        .top-subtitle{
            background:var(--top);
            text-align:center;
            font-weight:700;
            font-size:13px;
            line-height:1.25;
            text-transform:uppercase;
            padding:12px 14px !important;
        }

        .top-cell{
            background:var(--top);
            text-align:center;
            font-weight:700;
            font-size:12px;
            padding:8px 10px !important;
            height:34px;
        }

        .logo-box{
            background:var(--top);
            text-align:center;
            color:#b6bcc3;
            font-weight:800;
            height:118px;
        }

        .logo-inner{
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .logo-placeholder{
            border:2px dashed #c9d1d9;
            padding:12px 16px;
            line-height:1.05;
            font-size:15px;
        }

        .info-label{
            background:#fbfcfd;
            font-size:12px;
            font-weight:700;
            padding:10px 12px !important;
            white-space: normal; /* Permite saltos de línea */
    word-break: break-word; /* Rompe palabras largas si es necesario */
    vertical-align: middle;
}
        

        .info-field{
            background:#fff;
        }

        .info-field input{
            width:100%;
            max-width: 100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:10px;
            box-sizing: border-box;
            display: block;
        }
        
        .info-field input:focus { background: #f8fbff; }

        .instruction{
            background:#fff;
            padding:18px 14px !important;
            font-size:13px;
            line-height:1.45;
            font-weight: bold;
        }

        .table-head{
            background:var(--blue);
            font-size:13px;
            font-weight:800;
            text-align:center;
            color:#111827;
            padding:8px 6px !important;
        }

        .table-head-sub{
            background:var(--blue);
            font-size:12px;
            font-weight:800;
            text-align:center;
            color:#111827;
            padding:6px !important;
        }

        .num-cell{
            text-align:center;
            font-weight:800;
            font-size:13px;
            background:#fbfcfd;
            padding:10px 6px !important;
        }

        .req-cell{
            background:#fff;
            padding: 0 !important;
        }

        .req-cell textarea{
            width:100%;
            min-height:54px;
            resize:vertical;
            border:none;
            outline:none;
            background:transparent;
            padding:10px;
            font-size:13px;
            line-height:1.35;
            font-family: Arial, Helvetica, sans-serif;
            display: block;
        }

        .req-cell textarea:focus { background: #f8fbff; }

        .check-cell{
            text-align:center;
            background:#fff;
            padding:8px 4px !important;
        }

        .check-cell input[type="radio"]{
            transform:scale(1.15);
            cursor:pointer;
        }

        .obs-cell{
            background:#fff;
        }

        .obs-cell textarea{
            width:100%;
            min-height:54px;
            resize:vertical;
            border:none;
            outline:none;
            background:transparent;
            padding:10px;
            font-size:13px;
            line-height:1.35;
            font-family: Arial, Helvetica, sans-serif;
            display: block;
        }

        .obs-cell textarea:focus { background: #f8fbff; }

        .signature-label{
            padding:10px !important;
            font-size:13px;
            font-weight:700;
            background:#fff;
        }

        .signature-line{
            height:60px;
            background:#fff;
            position: relative;
        }

        .footer-help{
            padding:12px 16px;
            border-top:1px solid #e3e8ee;
            background:#fafcff;
            font-size:12px;
            color:var(--muted);
        }

        .spacer-row td{
            height:42px;
            background:#fff;
        }

        .w-no{ width:45px; }
        .w-req{ width:535px; }
        .w-check{ width:85px; }
        .w-obs{ width:265px; }

        @media (max-width: 768px){
            .page-wrap{ padding:10px; }
            .sheet-header-title{ font-size:14px; }
            .top-title{ font-size:13px; }
            .top-subtitle{ font-size:12px; }
        }

        /* CONFIGURACIÓN EXCLUSIVA PARA IMPRESIÓN EN TAMAÑO CARTA */
        @media print{
            @page{ 
                size: letter portrait; 
                margin: 8mm; 
            }
            body{ 
                background:#fff !important; 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-wrap{ 
                padding:0 !important; 
                max-width: 100% !important; 
                width: 100% !important;
            }
            .topbar, .sst-toolbar, .footer-help, .print-hide { 
                display:none !important; 
            }
            .sheet-card{ 
                border:none !important; 
                border-radius:0 !important; 
                box-shadow:none !important; 
                margin: 0 !important; 
                padding: 0 !important;
            }
            .sheet-scroll{ 
                overflow:visible !important; 
                width: 100% !important;
            }
            .sheet{ 
                min-width:100% !important; 
                width: 100% !important;
            }
            table.form-sheet {
                width: 100% !important;
                table-layout: fixed !important;
            }
            .info-field input, .obs-cell textarea, .req-cell textarea { 
                background: transparent !important; 
                border:none !important; 
                resize:none !important; 
                overflow: hidden !important;
            }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/toolbar.css">
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="page-wrap">
    <div class="sst-toolbar">
        <h1 class="sst-toolbar-title">LISTA CHEQUEO · RE-SST-15</h1>

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
        <div class="sheet-card">
            <div class="sheet-scroll">
                <div class="sheet">
                    <table class="form-sheet">
                        <colgroup>
    <col style="width:160px"> 
    <col style="width:420px">
    <col style="width:85px">
    <col style="width:85px">
    <col style="width:85px">
    <col style="width:265px">
</colgroup>

                        <tr>
                            <td rowspan="3" colspan="2" class="logo-box">
                                <div class="logo-inner">
                                    <div class="logo-placeholder" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; padding:0;' ?>">
                                        <?php if(!empty($logoEmpresaUrl)): ?>
                                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                        <?php else: ?>
                                            TU LOGO<br>AQUÍ
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td colspan="3" class="top-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                            <td class="top-cell">
                                <input type="text" name="meta_version" value="0" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="top-subtitle">LISTA DE CHEQUEO PARA VERIFICACIÓN DE REQUERIMIENTOS GENERALES DEL SG-SST PARA PERSONA NATURAL</td>
                            <td class="top-cell">
                                <input type="text" name="meta_codigo" value="RE-SST-16" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="top-cell">&nbsp;</td>
                            <td class="top-cell">
                                <input type="date" name="meta_fecha" id="metaFecha" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                            </td>
                        </tr>

                        <tr>
                            <td class="info-label" colspan="1">Fecha:</td>
                            <td class="info-field" colspan="2"><input type="date" name="fecha_evaluacion" id="fechaEvaluacion"></td>
                            <td class="info-label" colspan="1" style="text-align: right;">&nbsp;</td>
                            <td class="info-field" colspan="2"><input type="text" name="codigo_evaluacion" placeholder="Cod. Evaluación"></td>
                        </tr>
                        <tr>
                            <td class="info-label" colspan="1">Nombre del contratista:</td>
                            <td class="info-field" colspan="2"><input type="text" name="contratista" placeholder="Nombre completo..."></td>
                            <td class="info-label" colspan="1" style="text-align: right;">Nit / CC:</td>
                            <td class="info-field" colspan="2"><input type="text" name="nit" placeholder="Documento..."></td>
                        </tr>
                        <tr>
                            <td class="info-label" colspan="1">Nombre del Supervisor:</td>
                            <td class="info-field" colspan="2"><input type="text" name="supervisor" id="nombreSupervisor" placeholder="Nombre del supervisor..."></td>
                            <td class="info-label" colspan="1" style="text-align: right;">CC:</td>
                            <td class="info-field" colspan="2"><input type="text" name="cc_supervisor" placeholder="Cédula supervisor..."></td>
                        </tr>

                        <tr>
                            <td colspan="6" class="instruction">
                                1. Cuando sean seleccionados y con los demás documentos exigidos por la sección de contratación o quien haga sus veces, el contratista debe cumplir con los siguientes requerimientos:
                            </td>
                        </tr>

                        <tr>
                            <th class="table-head w-no" rowspan="2">No</th>
                            <th class="table-head w-req" rowspan="2">REQUERIMIENTO</th>
                            <th class="table-head" colspan="3">CUMPLE</th>
                            <th class="table-head w-obs" rowspan="2">OBSERVACIONES</th>
                        </tr>
                        <tr>
                            <th class="table-head-sub w-check">SI</th>
                            <th class="table-head-sub w-check">NO</th>
                            <th class="table-head-sub w-check">N/A</th>
                        </tr>

                        <!-- BLOQUE 1 EDITABLE -->
                        <?php foreach($bloque1 as $i => $req): $n = $i + 1; ?>
                        <tr>
                            <td class="num-cell w-no"><?= $n ?></td>
                            <td class="req-cell w-req">
                                <textarea name="req_b1_<?= $n ?>" placeholder="Escribe el requerimiento..."><?= e($req) ?></textarea>
                            </td>
                            <td class="check-cell w-check"><input type="radio" name="b1_<?= $n ?>" value="SI"></td>
                            <td class="check-cell w-check"><input type="radio" name="b1_<?= $n ?>" value="NO"></td>
                            <td class="check-cell w-check"><input type="radio" name="b1_<?= $n ?>" value="NA"></td>
                            <td class="obs-cell w-obs"><textarea name="obs_b1_<?= $n ?>" placeholder="Observaciones..."></textarea></td>
                        </tr>
                        <?php endforeach; ?>

                        <tr class="spacer-row">
                            <td colspan="6"></td>
                        </tr>

                        <tr>
                            <th class="table-head w-no" rowspan="2">No</th>
                            <th class="table-head w-req" rowspan="2">REQUERIMIENTO</th>
                            <th class="table-head" colspan="3">CUMPLE</th>
                            <th class="table-head w-obs" rowspan="2">OBSERVACIONES</th>
                        </tr>
                        <tr>
                            <th class="table-head-sub w-check">SI</th>
                            <th class="table-head-sub w-check">NO</th>
                            <th class="table-head-sub w-check">N/A</th>
                        </tr>

                        <!-- BLOQUE 2 EDITABLE -->
                        <?php foreach($bloque2 as $i => $req): $n = $i + 1; ?>
                        <tr>
                            <td class="num-cell w-no"><?= $n ?></td>
                            <td class="req-cell w-req">
                                <textarea name="req_b2_<?= $n ?>" placeholder="Escribe el requerimiento..."><?= e($req) ?></textarea>
                            </td>
                            <td class="check-cell w-check"><input type="radio" name="b2_<?= $n ?>" value="SI"></td>
                            <td class="check-cell w-check"><input type="radio" name="b2_<?= $n ?>" value="NO"></td>
                            <td class="check-cell w-check"><input type="radio" name="b2_<?= $n ?>" value="NA"></td>
                            <td class="obs-cell w-obs"><textarea name="obs_b2_<?= $n ?>" placeholder="Observaciones..."></textarea></td>
                        </tr>
                        <?php endforeach; ?>

                        <tr>
                            <td colspan="2" class="signature-label" style="text-align: right; vertical-align: bottom;">Firma del Supervisor:</td>
                            <td colspan="4" class="signature-line">
                                <div style="border-bottom: 1px solid #111; width: 80%; margin: 30px auto 5px auto;"></div>
                                <div style="text-align: center; font-size: 11px; font-weight: normal; color: #555;" id="firmaText">Firma</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="footer-help print-hide">
                Puedes modificar los textos de los requerimientos, marcar SI / NO / N/A y agregar observaciones. Recuerda presionar "Guardar".
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

        const fEval = document.getElementById("fechaEvaluacion");
        if (fEval && !fEval.value) fEval.value = `${y}-${m}-${dd}`;
    }
    setHoy();

    document.getElementById('nombreSupervisor').addEventListener('input', function() {
        document.getElementById('firmaText').textContent = this.value || "Firma";
    });

    document.addEventListener('DOMContentLoaded', function () {
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (Array.isArray(value)) {
                    // arrays processing
                } else {
                    let campo = document.querySelector(`[name="${key}"]`);
                    if (!campo) {
                        let radio = document.querySelector(`input[name="${key}"][value="${value}"]`);
                        if(radio) radio.checked = true;
                    } else if (campo.type === 'radio' || campo.type === 'checkbox') {
                        // Handled
                    } else {
                        campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                    }
                }
            }
            
            let sup = document.getElementById('nombreSupervisor').value;
            if (sup) document.getElementById('firmaText').textContent = sup;
        }
    });

    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
        const formData = new FormData(form);
        const datosJSON = {};

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
                    text: 'Lista de chequeo guardada correctamente',
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