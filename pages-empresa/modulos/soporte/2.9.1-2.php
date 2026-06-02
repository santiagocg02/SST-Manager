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

    <link rel="stylesheet" href="../../../assets/css/toolbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            padding:6px 14px !important;
        }

        /* Contenedor de la celda de índice */
        .item-cell{
            text-align:center;
            font-weight:800;
            font-size:13px;
            background:#fbfcfd;
            padding:10px 6px !important;
            position: relative;
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

        /* --- BOTONES ESTILIZADOS (INTEGRACIÓN ESTÉTICA) --- */
        
        /* Botón Agregar dentro de la fila azul */
        .btn-add-section {
            background: transparent;
            border: none;
            color: #0f172a;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            transition: all 0.2s ease-in-out;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-add-section:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #000;
        }
        .btn-add-section i {
            margin-right: 4px;
        }

        /* Botón eliminar ultra sutil (Aparece solo en Hover) */
        .btn-remove-row {
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--danger);
            font-size: 12px;
            padding: 4px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.15s ease-in-out;
            z-index: 10;
        }
        /* Al hacer hover sobre la celda del número, se revela suavemente la papelera */
        .item-cell:hover .btn-remove-row {
            opacity: 1;
        }
        /* Ocultar el texto correlativo (a, b, c) brevemente si la papelera está encima */
        .item-cell:hover .lbl-index {
            opacity: 0.15;
        }
        .lbl-index {
            transition: opacity 0.15s ease-in-out;
        }

        .w-item{ width:100px; }
        .w-desc{ width:290px; }
        .w-esp{ width:460px; }
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
            .sst-toolbar, .topbar, .sheet-header, .print-hide, .btn-remove-row { display:none !important; }
            .sheet-card{ border:none !important; border-radius:0 !important; box-shadow:none !important; margin: 0; }
            .sheet-scroll{ overflow:visible !important; }
            .sheet{ min-width:100% !important; }
            .editable input, .editable textarea{ font-size:12px !important; background:transparent !important; }
            .item-cell .lbl-index { opacity: 1 !important; }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    <div class="sst-toolbar">
        <h1 class="sst-toolbar-title">COMPRAS SST · RE-SST-18</h1>
        <div class="sst-toolbar-actions">
            <a href="#" class="btn btn-secondary btn-sm">Volver</a>
            <button type="button" id="btnGuardar" class="btn btn-success btn-sm">
                <i class="fa-solid fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet-scroll">
            <div class="sheet">
                <table class="form-sheet" id="tabla-especificaciones">
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

                    <!-- SECCIÓN 1 -->
                    <tr class="section-row" data-prefix="epp">
                        <td colspan="4">
                            <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                <span>Equipos de protección personal</span>
                                <button type="button" class="btn-add-section print-hide" onclick="agregarFila(this, 'epp')">
                                    <i class="fa-solid fa-plus"></i> Agregar ítem
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">a)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Gafas de seguridad"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">En policarbonato, liviana, anti-impacto, filtro UV 99,9%, resistencia a impactos, abrasión y salpicaduras de líquidos irritantes.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">ANSI Z87.1:2010</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">b)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Protector respiratorio"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">Respirador para partículas N95, protección contra polvo y partículas sin presencia de aceite.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">NIOSH bajo la especificación N95 de la norma 42CFR84.</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">c)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Guantes de impacto"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">Guante de alta sensibilidad con una alta resistencia, aplicaciones de peso medio.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">EN 420 Requisitos generales.&#13;&#10;EN 388 Protección contra riesgo mecánico (3143X).</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">d)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Protectores auditivos de inserción"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">Polímero hipoalergénico, premoldeados, con tres falanges que se adaptan a la cavidad auditiva.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">ANSI S3.19-1974</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">e)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Botas de seguridad"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">Dieléctricas, antideslizantes, con puntera, livianas y resistentes a hidrocarburos.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">NTC ISO 20345, Numeral 8.2.3&#13;&#10;ASTM F2413-05, Numeral 5.5.8.1&#13;&#10;NTC ISO 20344:2007</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">f)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Guantes de hilaza con látex"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="2">Resistencia mecánica leve.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="2">EN 420 Requisitos generales.</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">g)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Gafas de seguridad"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">Gafas de protección ante proyección de partículas con protección frontal y lateral en material de policarbonato.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">ANSI Z87.1</textarea></td>
                    </tr>
                    <tr class="row-epp">
                        <td class="item-cell"><span class="lbl-index">h)</span></td>
                        <td class="editable"><input type="text" name="epp_desc[]" value="Guantes de seguridad"></td>
                        <td class="editable"><textarea name="epp_esp[]" rows="3">En poliuretano, diseñadas para procesos industriales y mantenimiento.</textarea></td>
                        <td class="editable"><textarea name="epp_norma[]" rows="3">EN166 CE&#13;&#10;EN 388</textarea></td>
                    </tr>

                    <!-- SECCIÓN 2 -->
                    <tr class="section-row" data-prefix="emg">
                        <td colspan="4">
                            <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                <span>Equipos emergencias</span>
                                <button type="button" class="btn-add-section print-hide" onclick="agregarFila(this, 'emg')">
                                    <i class="fa-solid fa-plus"></i> Agregar ítem
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="row-emg">
                        <td class="item-cell"><span class="lbl-index">a)</span></td>
                        <td class="editable"><input type="text" name="emg_desc[]" value="Collarín"></td>
                        <td class="editable"><textarea name="emg_esp[]" rows="2"></textarea></td>
                        <td class="editable"><textarea name="emg_norma[]" rows="2"></textarea></td>
                    </tr>
                    <tr class="row-emg">
                        <td class="item-cell"><span class="lbl-index">b)</span></td>
                        <td class="editable"><input type="text" name="emg_desc[]" value="Extintor"></td>
                        <td class="editable"><textarea name="emg_esp[]" rows="2">Polvo químico seco BC, agente limpio, gas carbónico CO2.</textarea></td>
                        <td class="editable"><textarea name="emg_norma[]" rows="2">NFPA 10</textarea></td>
                    </tr>
                    <tr class="row-emg">
                        <td class="item-cell"><span class="lbl-index">c)</span></td>
                        <td class="editable"><input type="text" name="emg_desc[]" value="Camilla"></td>
                        <td class="editable"><textarea name="emg_esp[]" rows="3">Camilla rígida de 6.5 kg, de alta resistencia, resistente al agua, con arnés reflectivo y soporte hasta 180 kg.</textarea></td>
                        <td class="editable"><textarea name="emg_norma[]" rows="3">NTC 2885</textarea></td>
                    </tr>

                    <!-- SECCIÓN 3 -->
                    <tr class="section-row" data-prefix="pq">
                        <td colspan="4">
                            <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                <span>Productos químicos</span>
                                <button type="button" class="btn-add-section print-hide" onclick="agregarFila(this, 'pq')">
                                    <i class="fa-solid fa-plus"></i> Agregar ítem
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="row-pq">
                        <td class="item-cell"><span class="lbl-index">a)</span></td>
                        <td class="editable"><input type="text" name="pq_desc[]" value="Hojas de seguridad"></td>
                        <td class="editable"><textarea name="pq_esp[]" rows="2">Merakem - inhibidor de corrosión</textarea></td>
                        <td class="editable"><textarea name="pq_norma[]" rows="2">Documento bajo los criterios de peligro y las regulaciones controladas de los productos (CPR).</textarea></td>
                    </tr>
                    <tr class="row-pq">
                        <td class="item-cell"><span class="lbl-index">b)</span></td>
                        <td class="editable"><input type="text" name="pq_desc[]" value="Hojas de seguridad"></td>
                        <td class="editable"><textarea name="pq_esp[]" rows="2">Ikempol - polímero floculante</textarea></td>
                        <td class="editable"><textarea name="pq_norma[]" rows="2">Documento bajo los criterios de peligro y las regulaciones controladas de los productos (CPR).</textarea></td>
                    </tr>
                    <tr class="row-pq">
                        <td class="item-cell"><span class="lbl-index">c)</span></td>
                        <td class="editable"><input type="text" name="pq_desc[]" value="Fichas técnicas"></td>
                        <td class="editable"><textarea name="pq_esp[]" rows="2"></textarea></td>
                        <td class="editable"><textarea name="pq_norma[]" rows="2"></textarea></td>
                    </tr>

                    <!-- SECCIÓN 4 -->
                    <tr class="section-row" data-prefix="eq">
                        <td colspan="4">
                            <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                <span>Equipos</span>
                                <button type="button" class="btn-add-section print-hide" onclick="agregarFila(this, 'eq')">
                                    <i class="fa-solid fa-plus"></i> Agregar ítem
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="row-eq">
                        <td class="item-cell"><span class="lbl-index">a)</span></td>
                        <td class="editable"><input type="text" name="eq_desc[]" value=""></td>
                        <td class="editable"><textarea name="eq_esp[]" rows="2"></textarea></td>
                        <td class="editable"><textarea name="eq_norma[]" rows="2"></textarea></td>
                    </tr>

                    <!-- SECCIÓN 5 -->
                    <tr class="section-row" data-prefix="maq">
                        <td colspan="4">
                            <div class="d-flex justify-content-between align-items-center w-100 px-1">
                                <span>Maquinaria</span>
                                <button type="button" class="btn-add-section print-hide" onclick="agregarFila(this, 'maq')">
                                    <i class="fa-solid fa-plus"></i> Agregar ítem
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="row-maq">
                        <td class="item-cell"><span class="lbl-index">a)</span></td>
                        <td class="editable"><input type="text" name="maq_desc[]" value=""></td>
                        <td class="editable"><textarea name="maq_esp[]" rows="2"></textarea></td>
                        <td class="editable"><textarea name="maq_norma[]" rows="2"></textarea></td>
                    </tr>

                    <!-- NOTA FIJA FIN DE TABLA -->
                    <tr id="row-nota-fija">
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

    // --- LÓGICA DINÁMICA DE AGREGAR / ELIMINAR FILAS ---
    function obtenerLetraIndex(num) {
        let letra = "";
        while (num >= 0) {
            letra = String.fromCharCode((num % 26) + 97) + letra;
            num = Math.floor(num / 26) - 1;
        }
        return letra + ")";
    }

    function renombrarIndicesSeccion(prefix) {
        const filas = document.querySelectorAll(`.row-${prefix}`);
        filas.forEach((fila, index) => {
            const lbl = fila.querySelector('.lbl-index');
            if (lbl) lbl.textContent = obtenerLetraIndex(index);
        });
    }

    function agregarFila(btn, prefix, valDesc = '', valEsp = '', valNorma = '') {
        const trSeccion = btn.closest('tr');
        
        // Creamos la nueva fila perfectamente estructurada e integrada visualmente
        const nuevaFila = document.createElement('tr');
        nuevaFila.className = `row-${prefix}`;
        
        nuevaFila.innerHTML = `
            <td class="item-cell">
                <button type="button" class="btn-remove-row print-hide" onclick="eliminarFila(this, '${prefix}')" title="Eliminar ítem">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
                <span class="lbl-index">a)</span>
            </td>
            <td class="editable"><input type="text" name="${prefix}_desc[]" value="${valDesc}"></td>
            <td class="editable"><textarea name="${prefix}_esp[]" rows="2">${valEsp}</textarea></td>
            <td class="editable"><textarea name="${prefix}_norma[]" rows="2">${valNorma}</textarea></td>
        `;

        // Ubicamos la fila inmediatamente después de la última del mismo bloque
        const filasExistentes = document.querySelectorAll(`.row-${prefix}`);
        if (filasExistentes.length > 0) {
            const ultimaFila = filasExistentes[filasExistentes.length - 1];
            ultimaFila.parentNode.insertBefore(nuevaFila, ultimaFila.nextSibling);
        } else {
            trSeccion.parentNode.insertBefore(nuevaFila, trSeccion.nextSibling);
        }

        renombrarIndicesSeccion(prefix);
    }

    function eliminarFila(btn, prefix) {
        const fila = btn.closest('tr');
        fila.remove();
        renombrarIndicesSeccion(prefix);
    }

    // --- LÓGICA DE CARGADO DE DATOS DESDE PHP / API ---
    document.addEventListener('DOMContentLoaded', function () {
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        const prefijos = ['epp', 'emg', 'pq', 'eq', 'maq'];

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            
            // 1. Validar si la API tiene más ítems de los renderizados por defecto
            prefijos.forEach(pfx => {
                const arrayDesc = datosGuardados[`${pfx}_desc`] || [];
                const filasActuales = document.querySelectorAll(`.row-${pfx}`);
                
                // Si la API tiene más elementos que el HTML base, generamos las filas necesarias
                if (arrayDesc.length > filasActuales.length) {
                    for (let i = filasActuales.length; i < arrayDesc.length; i++) {
                        const btnSeccion = document.querySelector(`tr[data-prefix="${pfx}"] .print-hide`);
                        if (btnSeccion) agregarFila(btnSeccion, pfx);
                    }
                }
            });

            // 2. Mapear y rellenar los datos de forma exacta en inputs y textareas
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

            if (result.ok || result.status === 'success') {
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
                    confirmButtonColor: '#d62828'
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo contactar al servidor para guardar.',
                icon: 'error',
                confirmButtonColor: '#d62828'
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