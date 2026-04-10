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
// Ajusta el ID de este ítem según tu base de datos (Ej: 36 para Especificaciones de Compras)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 36; 

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
        
        // Priorizando campos _rl y _sst
        $nombreRL = $empData['nombre_rl'] ?? $empData['representante_legal'] ?? '';
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
    <title>RE-SST-18 | Especificaciones de las compras en SST</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --blue-main:#5f8fbe;
            --blue-soft:#dbe8f5;
            --blue-dark:#2b5d8a;
            --line:#8f9aa5;
            --line-soft:#bcc6cf;
            --header:#eef2f6;
            --paper:#ffffff;
            --page:#f3f6fa;
            --text:#1f2937;
            --muted:#6b7280;
            --danger:#d62828;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
            --green:#198754;
            --green-hover:#146c43;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--page);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .page-wrap{ padding:20px; max-width: 1200px; margin: 0 auto; }

        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:16px;
            background: #d9dde2;
            padding: 10px 16px;
            border: 1px solid #c8cdd3;
            border-radius: 6px;
        }

        .topbar-left,
        .topbar-right{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn-ui{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:6px 12px;
            border-radius:6px;
            border:1px solid var(--btn);
            background:var(--btn);
            color:#fff;
            text-decoration:none;
            font-size:12px;
            font-weight:800;
            transition:.2s ease;
            cursor:pointer;
        }

        .btn-ui:hover{ background:var(--btn-hover); border-color:var(--btn-hover); color:#fff; }

        .btn-ui.secondary{ background:#fff; color:var(--btn); border-color:#cfd6e4; }
        .btn-ui.secondary:hover{ background:#eef5ff; color:var(--btn-hover); }

        .btn-ui.success { background:var(--green); border-color:var(--green); color:#fff; }
        .btn-ui.success:hover { background:var(--green-hover); }

        .badge-format{
            font-size:12px;
            color:#0f2f5c;
            background:transparent;
            font-weight:900;
        }

        .sheet-card{
            background:var(--paper);
            border:1px solid #d7dee6;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 8px 24px rgba(31,41,55,.08);
            margin-bottom: 20px;
        }

        .sheet-header{
            padding:14px 18px;
            background:linear-gradient(135deg, #f8fbff 0%, #eef4fb 100%);
            border-bottom:1px solid #dde6ef;
        }

        .sheet-header-title{
            margin:0;
            font-size:16px;
            font-weight:800;
            color:var(--blue-dark);
        }

        .sheet-header-subtitle{
            margin:4px 0 0;
            font-size:12px;
            color:var(--muted);
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
            background:#fff;
        }

        .form-sheet th,
        .form-sheet td{
            border:1px solid var(--line-soft);
            vertical-align:middle;
            padding:0;
        }

        .top-cell{
            background:var(--header);
            text-align:center;
            font-weight:700;
            font-size:12px;
            padding:8px 10px !important;
            height:34px;
        }

        .top-title{
            background:var(--header);
            text-align:center;
            font-weight:800;
            font-size:15px;
            padding:10px 14px !important;
            line-height:1.2;
            text-transform:uppercase;
        }

        .top-subtitle{
            background:var(--header);
            text-align:center;
            font-weight:700;
            font-size:13px;
            padding:10px 14px !important;
            text-transform:uppercase;
        }

        .logo-box{
            background:var(--header);
            text-align:center;
            color:#b6bcc3;
            font-weight:800;
            height:102px;
        }

        .logo-box-inner{
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .logo-placeholder{
            border:2px dashed #c9d1d9;
            padding:10px 16px;
            line-height:1.05;
            font-size:15px;
        }

        .head-row th{
            background:var(--blue-soft);
            color:#111827;
            font-size:13px;
            font-weight:800;
            text-align:center;
            padding:9px 8px !important;
        }

        .section-row td{
            background:var(--blue-main);
            color:#0f172a;
            font-size:14px;
            font-weight:800;
            text-align:center;
            padding:7px 10px !important;
        }

        .item-cell{
            text-align:center;
            font-weight:800;
            font-size:13px;
            background:#fbfcfd;
            padding:10px 6px !important;
        }

        .editable{
            background:#fff;
            min-height:54px;
            position:relative;
        }

        .editable input,
        .editable textarea{
            width:100%;
            height:100%;
            border:none;
            outline:none;
            background:transparent;
            color:var(--text);
            font-size:13px;
            padding:10px 10px;
            line-height:1.35;
        }

        .editable textarea{
            resize:vertical;
            min-height:54px;
        }

        .editable input:focus,
        .editable textarea:focus{
            background:#f8fbff;
        }

        .note-cell{
            color:var(--danger);
            font-size:12px;
            line-height:1.45;
            padding:12px 14px !important;
            font-weight:700;
            background:#fff;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:18px;
            margin: 24px;
        }

        .sign{
            border-top:1px solid #111;
            padding-top:8px;
            text-align:center;
            min-height:65px;
            font-size:12px;
            font-weight:700;
            position: relative;
        }

        .w-item{ width:90px; }
        .w-desc{ width:290px; }
        .w-esp{ width:470px; }
        .w-norma{ width:330px; }

        @media (max-width: 768px){
            .page-wrap{ padding:10px; }
            .sheet-header-title{ font-size:14px; }
            .top-title{ font-size:13px; }
            .top-subtitle{ font-size:12px; }
        }

        @media print{
            @page{ size:portrait; margin:10mm; }
            body{ background:#fff !important; }
            .page-wrap{ padding:0 !important; max-width: 100%; }
            .topbar, .sheet-header, .print-hide { display:none !important; }
            .sheet-card{ border:none !important; border-radius:0 !important; box-shadow:none !important; margin: 0; }
            .sheet-scroll{ overflow:visible !important; }
            .sheet{ min-width:100% !important; }
            .editable input, .editable textarea{ font-size:12px !important; background:transparent !important; }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    <div class="topbar print-hide">
        <div class="topbar-left">
            <button class="btn-ui secondary" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-ui secondary" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-ui success" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-ui" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
        <div class="topbar-right">
            <span class="badge-format">COMPRAS SST · RE-SST-18</span><br>
            <span style="font-size:11px; color:#6b7280; font-weight:700;">Usuario: <?= e($_SESSION["usuario"] ?? "Usuario") ?></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet-card">
            <div class="sheet-header print-hide">
                <h1 class="sheet-header-title">Especificaciones de las compras en SST</h1>
                <p class="sheet-header-subtitle">Formato editable con presentación profesional</p>
            </div>

            <div class="sheet-scroll">
                <div class="sheet">
                    <table class="form-sheet">
                        <colgroup>
                            <col class="w-item">
                            <col class="w-desc">
                            <col class="w-esp">
                            <col class="w-norma">
                        </colgroup>

                        <tr>
                            <td rowspan="3" class="logo-box">
                                <div class="logo-box-inner">
                                    <div class="logo-placeholder" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; padding:0;' ?>">
                                        <?php if(!empty($logoEmpresaUrl)): ?>
                                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                                        <?php else: ?>
                                            TU LOGO<br>AQUÍ
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td colspan="2" class="top-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                            <td class="top-cell">
                                <input type="text" name="meta_version" value="0" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="top-subtitle">ESPECIFICACIONES DE LAS COMPRAS EN SST</td>
                            <td class="top-cell">
                                <input type="text" name="meta_codigo" value="RE-SST-18" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="top-cell">&nbsp;</td>
                            <td class="top-cell">
                                <input type="date" name="meta_fecha" id="metaFecha" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                            </td>
                        </tr>

                        <tr class="head-row">
                            <th>Ítem</th>
                            <th>Descripción</th>
                            <th>Especificaciones</th>
                            <th>Normas específicas</th>
                        </tr>

                        <tr class="section-row">
                            <td colspan="4">Equipos de protección personal</td>
                        </tr>
                        <tr>
                            <td class="item-cell">a)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Gafas de seguridad"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">En policarbonato, liviana, anti-impacto, filtro UV 99,9%, resistencia a impactos, abrasión y salpicaduras de líquidos irritantes.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">ANSI Z87.1:2010</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">b)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Protector respiratorio"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">Respirador para partículas N95, protección contra polvo y partículas sin presencia de aceite.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">NIOSH bajo la especificación N95 de la norma 42CFR84.</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">c)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Guantes de impacto"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">Guante de alta sensibilidad con una alta resistencia, aplicaciones de peso medio.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">EN 420 Requisitos generales.&#13;&#10;EN 388 Protección contra riesgo mecánico (3143X).</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">d)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Protectores auditivos de inserción"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">Polímero hipoalergénico, premoldeados, con tres falanges que se adaptan a la cavidad auditiva.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">ANSI S3.19-1974</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">e)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Botas de seguridad"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">Dieléctricas, antideslizantes, con puntera, livianas y resistentes a hidrocarburos.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">NTC ISO 20345, Numeral 8.2.3&#13;&#10;ASTM F2413-05, Numeral 5.5.8.1&#13;&#10;NTC ISO 20344:2007</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">f)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Guantes de hilaza con látex"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="2">Resistencia mecánica leve.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="2">EN 420 Requisitos generales.</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">g)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Gafas de seguridad"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">Gafas de protección ante proyección de partículas con protección frontal y lateral en material de policarbonato.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">ANSI Z87.1</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">h)</td>
                            <td class="editable"><input type="text" name="epp_desc[]" value="Guantes de seguridad"></td>
                            <td class="editable"><textarea name="epp_esp[]" rows="3">En poliuretano, diseñadas para procesos industriales y mantenimiento.</textarea></td>
                            <td class="editable"><textarea name="epp_norma[]" rows="3">EN166 CE&#13;&#10;EN 388</textarea></td>
                        </tr>

                        <tr class="section-row">
                            <td colspan="4">Equipos emergencias</td>
                        </tr>
                        <tr>
                            <td class="item-cell">a)</td>
                            <td class="editable"><input type="text" name="emg_desc[]" value="Collarín"></td>
                            <td class="editable"><textarea name="emg_esp[]" rows="2"></textarea></td>
                            <td class="editable"><textarea name="emg_norma[]" rows="2"></textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">b)</td>
                            <td class="editable"><input type="text" name="emg_desc[]" value="Extintor"></td>
                            <td class="editable"><textarea name="emg_esp[]" rows="2">Polvo químico seco BC, agente limpio, gas carbónico CO2.</textarea></td>
                            <td class="editable"><textarea name="emg_norma[]" rows="2">NFPA 10</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">c)</td>
                            <td class="editable"><input type="text" name="emg_desc[]" value="Camilla"></td>
                            <td class="editable"><textarea name="emg_esp[]" rows="3">Camilla rígida de 6.5 kg, de alta resistencia, resistente al agua, con arnés reflectivo y soporte hasta 180 kg.</textarea></td>
                            <td class="editable"><textarea name="emg_norma[]" rows="3">NTC 2885</textarea></td>
                        </tr>

                        <tr class="section-row">
                            <td colspan="4">Productos químicos</td>
                        </tr>
                        <tr>
                            <td class="item-cell">a)</td>
                            <td class="editable"><input type="text" name="pq_desc[]" value="Hojas de seguridad"></td>
                            <td class="editable"><textarea name="pq_esp[]" rows="2">Merakem - inhibidor de corrosión</textarea></td>
                            <td class="editable"><textarea name="pq_norma[]" rows="2">Documento bajo los criterios de peligro y las regulaciones controladas de los productos (CPR).</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">b)</td>
                            <td class="editable"><input type="text" name="pq_desc[]" value="Hojas de seguridad"></td>
                            <td class="editable"><textarea name="pq_esp[]" rows="2">Ikempol - polímero floculante</textarea></td>
                            <td class="editable"><textarea name="pq_norma[]" rows="2">Documento bajo los criterios de peligro y las regulaciones controladas de los productos (CPR).</textarea></td>
                        </tr>
                        <tr>
                            <td class="item-cell">c)</td>
                            <td class="editable"><input type="text" name="pq_desc[]" value="Fichas técnicas"></td>
                            <td class="editable"><textarea name="pq_esp[]" rows="2"></textarea></td>
                            <td class="editable"><textarea name="pq_norma[]" rows="2"></textarea></td>
                        </tr>

                        <tr class="section-row">
                            <td colspan="4">Equipos</td>
                        </tr>
                        <tr>
                            <td class="item-cell">a)</td>
                            <td class="editable"><input type="text" name="eq_desc[]" value=""></td>
                            <td class="editable"><textarea name="eq_esp[]" rows="2"></textarea></td>
                            <td class="editable"><textarea name="eq_norma[]" rows="2"></textarea></td>
                        </tr>

                        <tr class="section-row">
                            <td colspan="4">Maquinaria</td>
                        </tr>
                        <tr>
                            <td class="item-cell">a)</td>
                            <td class="editable"><input type="text" name="maq_desc[]" value=""></td>
                            <td class="editable"><textarea name="maq_esp[]" rows="2"></textarea></td>
                            <td class="editable"><textarea name="maq_norma[]" rows="2"></textarea></td>
                        </tr>

                        <tr>
                            <td colspan="4" class="note-cell">
                                NOTA. En esta matriz se deben incluir todos los requisitos y estándares de seguridad y salud necesarios para máquinas, herramientas, EPP, elementos de emergencia y todos aquellos equipos que se consideren necesarios en la organización al realizar las compras.
                            </td>
                        </tr>
                    </table>
                    
                    <div class="sign-grid">
                        <div class="sign">
                            <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                                <?php if(!empty($firmaSST)): ?>
                                    <img src="<?= $firmaSST ?>" alt="Firma Elaborador" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                                <?php endif; ?>
                            </div>
                            ELABORÓ<br>
                            <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreSST) ?></span>
                        </div>
                        
                        <div class="sign">
                            <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                                <?php if(!empty($firmaSST)): ?>
                                    <img src="<?= $firmaSST ?>" alt="Firma Revisor" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                                <?php endif; ?>
                            </div>
                            REVISÓ<br>
                            <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreSST) ?></span>
                        </div>

                        <div class="sign">
                            <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                                <?php if(!empty($firmaRL)): ?>
                                    <img src="<?= $firmaRL ?>" alt="Firma Aprobador" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                                <?php endif; ?>
                            </div>
                            APROBÓ<br>
                            <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreRL) ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Poner fecha de hoy por defecto si está vacía
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
                if (Array.isArray(value)) {
                    let campos = document.querySelectorAll(`[name="${key}[]"]`);
                    value.forEach((val, i) => {
                        if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                    });
                } else {
                    const campo = document.querySelector(`[name="${key}"]`);
                    if (campo) {
                        campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
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
                    text: 'Especificaciones guardadas correctamente',
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