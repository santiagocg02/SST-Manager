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
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 30; 

// --- Lógica de Empresa Optimizada ---
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
    <title>2.6.1-2 - Informe de Rendición de Cuentas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --blue:#1f5fa8;
            --line:#111;
            --head:#d9e4f2;
            --soft:#eef3fb;
            --bg:#eef2f7;
            --text:#1b1b1b;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .wrap{
            max-width:1100px;
            margin:16px auto;
            padding:0 10px;
        }

        .sheet{
            background:#fff;
            border:2px solid var(--blue);
            box-shadow:0 10px 20px rgba(0,0,0,.08);
            padding:14px;
            margin-bottom:16px;
        }

        .page-break{
            page-break-after:always;
        }

        table.format{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:12px;
            margin-bottom:12px;
        }

        .format td,.format th{
            border:1px solid var(--line);
            padding:6px 8px;
            vertical-align:middle;
        }

        .sec-h-edit{
            background:#d9e1ea;
            border:1px solid #b8c2cc;
            margin-top:14px;
            margin-bottom:10px;
            padding: 5px 10px;
        }

        .sec-h-input{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            color:#10233c;
            font-weight:900;
            text-transform:uppercase;
            font-size:15px;
            letter-spacing:.2px;
        }

        .cover{
            min-height:860px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:30px 20px;
        }

        .cover-title-input{
            font-size:26px;
            font-weight:900;
            text-transform:uppercase;
            line-height:1.25;
            max-width:760px;
            margin-bottom:24px;
            border:none;
            outline:none;
            background:transparent;
            text-align:center;
            width:100%;
        }

        .cover-logo{
            width:180px;
            height:130px;
            border:2px dashed rgba(0,0,0,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            color:rgba(0,0,0,.35);
            font-weight:800;
            margin-bottom:24px;
            padding: 5px;
        }

        .cover-text{
            font-size:16px;
            font-weight:700;
            margin-bottom:10px;
            width: 100%;
        }

        .box{
            border:1px solid #1f1f1f;
            padding:12px;
            margin-bottom:12px;
        }

        table.formtbl{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:12px;
            margin-bottom:12px;
            background:#fff;
        }

        .formtbl th,
        .formtbl td{
            border:1px solid #2a2a2a;
            padding:7px 8px;
            vertical-align:middle;
        }

        .edit,
        .edit-inline,
        .th-input{
            width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:0;
            color:#111;
        }

        .th-input{
            font-weight:900;
            color:#14253d;
            text-align:center;
        }

        .th-bg{
            background:#f1f3f6;
            text-align:center;
        }

        .edit-inline{
            display:inline-block;
            width:auto;
            min-width:140px;
            max-width: 100%;
        }

        textarea.edit{
            resize:vertical;
            min-height:60px;
            line-height:1.55;
        }

        .policy-placeholder{
            min-height:120px;
            border:1px dashed #b8c2cc;
            background:#fafcff;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            color:#5c6670;
            font-weight:700;
            padding:20px;
        }

        .graph-placeholder{
            min-height:100px;
            border:1px dashed #b8c2cc;
            background:#fafcff;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            color:#5c6670;
            font-weight:700;
            padding:20px;
            margin-bottom:12px;
        }

        .center{ text-align:center; }

        @media print{
            body{ background:#fff; }
            .sst-toolbar, .btn-row-action{ display:none !important; }
            .sheet{ box-shadow:none; margin-bottom:0; border:2px solid #000; }
        }

        @media (max-width: 768px){
            .cover-title-input{ font-size:20px; }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/toolbar.css">
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
<div class="wrap">

    <div class="sst-toolbar">
        <h1 class="sst-toolbar-title">RENDICIÓN DE CUENTAS (EDITABLE TOTAL)</h1>
        <div class="sst-toolbar-actions">
            <a href="#" class="btn btn-secondary btn-sm">Volver</a>
            <button type="button" class="btn btn-success btn-sm">
                <i class="fa-solid fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <!-- PORTADA -->
        <div class="sheet page-break">
            <div class="cover">
                <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 120px; object-fit: contain;">
                    <?php else: ?>
                        LOGO EMPRESA
                    <?php endif; ?>
                </div>
                <div class="mb-3 w-100">
                    <input type="text" name="cover_t1" class="cover-title-input" value="REVISIÓN POR LA DIRECCIÓN Y RENDICIÓN DE CUENTAS">
                </div>
                <div class="cover-text"><input type="text" name="cover_v" class="edit inline center font-weight-bold" value="Versión 0" style="font-weight:bold; font-size:16px;"></div>
                <div class="cover-text"><input type="text" name="cover_cod" class="edit inline center font-weight-bold" value="IN-SST-03" style="font-weight:bold; font-size:16px;"></div>
                <div class="cover-text">FECHA: <input type="text" name="cover_fecha" class="edit-inline center" placeholder="XX/XX/XXXX"></div>
                
                <div class="w-100 style="margin-top:20px;"">
                    <input type="text" name="cover_t2" class="cover-title-input" style="font-size:20px;" value="INFORME DE RENDICIÓN DE CUENTAS">
                </div>
                <div class="w-100">
                    <input type="text" name="cover_t3" class="cover-title-input" style="font-size:18px;" value="SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUT EN EL TRABAJO">
                </div>
                
                <div class="cover-text"><input type="text" name="cover_empresa" class="edit-inline center" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>" style="font-size:18px; font-weight:bold;"></div>
                
                <div class="cover-text" style="margin-top:20px;"><input type="text" name="cover_dec" class="edit inline center" value="DECRETO 1072 DE 2015" style="font-weight:bold;"></div>
                <div class="cover-text"><input type="text" name="cover_res" class="edit inline center" value="RESOLUCIÓN 0312 DEL 2019" style="font-weight:bold;"></div>
                
                <div class="cover-text" style="margin-top:20px;">PERIODO: <input type="text" name="cover_periodo" class="edit-inline center" placeholder="ENERO – DICIEMBRE 202X"></div>
                <div class="cover-text">FECHA DE REALIZACIÓN: <input type="text" name="cover_fecha_realizacion" class="edit-inline center" placeholder="Día de Mes de Año"></div>
            </div>
        </div>

        <!-- CUERPO DEL INFORME -->
        <div class="sheet">
            <!-- ENCABEZADO DE FORMATO -->
            <table class="format">
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
                    <td><input type="text" name="h_title1" class="edit center style-title" style="font-weight:900; text-align:center;" value="SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO"></td>
                    <td><strong>Versión:</strong> <input type="text" name="h_ver" class="edit-inline" value="0" style="width:30px;"></td>
                    <td><strong>Código:</strong><br><input type="text" name="h_code" class="edit" value="IN-SST-03"></td>
                </tr>
                <tr>
                    <td><input type="text" name="h_subtitle" class="edit center" style="font-weight:900; text-align:center;" value="INFORME DE RENDICIÓN DE CUENTAS"></td>
                    <td colspan="2"><strong>Fecha:</strong> <input type="date" name="meta_fecha_2" id="metaFecha2" style="border:none; font-size:11px; outline:none; background:transparent; width:70px;"></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Periodo:</strong> <input name="meta_periodo" class="edit-inline" type="text" value="Enero - Diciembre 202X"></td>
                </tr>
            </table>

            <!-- SECCIÓN INTRODUCCIÓN -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_intro" class="sec-h-input" value="Introducción">
            </div>
            <div class="box text-just">
                <textarea name="txt_intro" class="edit" rows="14">Las Revisiones Gerenciales son convocadas por el Gerente General de la Empresa o su designado, una vez al año o antes de encontrarse la necesidad. Los aspectos a tener en cuenta como marco para el análisis de las revisiones son:

- La política, los objetivos y metas del SGSST.
- Resultados de indicadores.
- Estrategias implementadas para el cumplimiento de los objetivos y metas.
- Cumplimiento del plan de trabajo.
- Ejecución del presupuesto y suficiencia de los recursos.
- El análisis estadístico del sistema (accidentalidad, incidentalidad, inspecciones, entre otras) y la notificación de accidentes.
- Estado de acciones derivadas de hallazgos al sistema.
- Resultados de implementaciones de acciones preventivas y correctivas.
- El resultado de las auditorías internas y externas.
- Los cambios que puedan afectar el SGSST.
- Requerimientos del COPASST.
- Participación de los trabajadores.
- Requisitos legales de SST.

El presente informe consolida toda la gestión que el Departamento de SST ha ejecutado con relación al Sistema de Gestión de Seguridad y Salud en el Trabajo durante el año evaluado.</textarea>
            </div>

            <!-- SECCIÓN POLÍTICA SST -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_pol1" class="sec-h-input" value="Política del SG SST">
            </div>
            <div class="policy-placeholder">
                <textarea name="txt_politica_sst" class="edit center" rows="3" placeholder="COLOCAR O DESCRIBIR LA POLÍTICA FIRMADA POR EL REPRESENTANTE LEGAL O DEJAR COMO ESPACIO PARA ANEXO"></textarea>
            </div>

            <!-- SECCIÓN POLÍTICA ALCOHOL -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_pol2" class="sec-h-input" value="Política de no alcohol, tabaco y sustancias psychoactivas">
            </div>
            <div class="policy-placeholder">
                <textarea name="txt_politica_alcohol" class="edit center" rows="3" placeholder="COLOCAR O DESCRIBIR LA POLÍTICA FIRMADA POR EL REPRESENTANTE LEGAL O DEJAR COMO ESPACIO PARA ANEXO"></textarea>
            </div>

            <!-- SECCIÓN PRESUPUESTO -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_pres" class="sec-h-input" value="Ejecución del presupuesto y suficiencia de los recursos">
            </div>
            <div class="box">
                <textarea name="txt_presupuesto" class="edit" rows="4" placeholder="Coloca el presupuesto de la herramienta SG-SST con el análisis realizado"></textarea>
            </div>

            <!-- SECCIÓN PLAN DE TRABAJO -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_plan" class="sec-h-input" value="Cumplimiento del plan de trabajo">
            </div>
            <table class="formtbl">
                <tbody>
                    <tr>
                        <th class="th-bg" style="width:180px;"><input type="text" name="lbl_pt_obj" class="th-input" value="Objetivo"></th>
                        <td><input name="pt_obj" class="edit" type="text" value="Planear y controlar la ejecución de las actividades del Sistema de Gestión de Seguridad y Salud en el Trabajo."></td>
                    </tr>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_pt_meta" class="th-input" value="Meta"></th>
                        <td><input name="pt_meta" class="edit" type="text" value="Cumplir con el 80% de las actividades propuestas"></td>
                    </tr>
                </tbody>
            </table>
            <div class="graph-placeholder"><textarea name="nota_grafica_1" class="edit center" placeholder="(Espacio reservado para anexar Gráfica de Cumplimiento del Plan de Trabajo)"></textarea></div>

            <!-- SECCIÓN OSTEOMUSCULAR -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_osteo" class="sec-h-input" value="Programa de Vigilancia Epidemiológica para la Prevención de Lesiones Osteomusculares">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_ost_h1" class="th-input" value="OBJETIVO"></th>
                        <th class="th-bg" style="width:180px;"><input type="text" name="lbl_ost_h2" class="th-input" value="INDICADOR"></th>
                        <th class="th-bg"><input type="text" name="lbl_ost_h3" class="th-input" value="FÓRMULA DEL INDICADOR"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5"><textarea name="pve_osteo_obj_txt" class="edit" rows="6">Prevenir la aparición de desórdenes músculo esqueléticos a través de la identificación, evaluación e intervención de las condiciones no ergonómicas encontradas en los puestos de trabajo.</textarea></td>
                        <td><input type="text" name="pve_ost_ind1" class="edit" value="Prevalencia"></td>
                        <td><input type="text" name="pve_ost_for1" class="edit" value="No. de Casos Nuevos + No. de Casos Antiguos / No. de trabajadores expuestos x 100000"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_ost_ind2" class="edit" value="Incidencia"></td>
                        <td><input type="text" name="pve_ost_for2" class="edit" value="No. de Casos nuevos / Número de trabajadores expuestos x 100000"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_ost_ind3" class="edit" value="Cumplimiento"></td>
                        <td><input type="text" name="pve_ost_for3" class="edit" value="No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_ost_ind4" class="edit" value="Cobertura"></td>
                        <td><input type="text" name="pve_ost_for4" class="edit" value="# de trabajadores que participan / # de trabajadores programados x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_ost_ind5" class="edit" value="Eficacia"></td>
                        <td><input type="text" name="pve_ost_for5" class="edit" value="No. de recomendaciones cerradas / Total de recomendaciones x 100%"></td>
                    </tr>
                </tbody>
            </table>

            <div class="box">
                <input type="text" name="lbl_ost_prev" style="font-weight:bold; border:none; background:transparent; width:100%; outline:none;" value="Prevalencia e incidencia">
                <textarea name="pve_osteo_prev" class="edit" rows="2">Se mantienen en 0%, ningún caso antiguo ni nuevo calificado como enfermedad laboral con relación a patologías osteomusculares.</textarea>
            </div>
            <div class="graph-placeholder"><textarea name="nota_grafica_2" class="edit center" placeholder="(Gráfica - Cumplimiento PVE Osteomuscular)"></textarea></div>
            <div class="box"><textarea name="pve_osteo_cumpl" class="edit" rows="2" placeholder="Realizar análisis de cumplimiento según PVE osteomuscular"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_3" class="edit center" placeholder="(Gráfica - Cobertura PVE Osteomuscular)"></textarea></div>
            <div class="box"><textarea name="pve_osteo_cob" class="edit" rows="2" placeholder="Realizar análisis de cobertura según PVE osteomuscular"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_4" class="edit center" placeholder="(Gráfica - Eficacia PVE Osteomuscular)"></textarea></div>
            <div class="box"><textarea name="pve_osteo_efic" class="edit" rows="2" placeholder="Realizar análisis de eficacia según PVE osteomuscular"></textarea></div>

            <!-- SECCIÓN PSICOSOCIAL -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_psico" class="sec-h-input" value="Programa de Vigilancia Epidemiológica para la Prevención del Riesgo Psicosocial">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_psi_h1" class="th-input" value="OBJETIVO"></th>
                        <th class="th-bg" style="width:180px;"><input type="text" name="lbl_psi_h2" class="th-input" value="INDICADOR"></th>
                        <th class="th-bg"><input type="text" name="lbl_psi_h3" class="th-input" value="FÓRMULA DEL INDICADOR"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5"><textarea name="pve_psico_obj_txt" class="edit" rows="6">Implementar un Sistema de Vigilancia Epidemiológica de acuerdo con los parámetros establecidos en la legislación colombiana vigente para preservar, mantener y mejorar la salud individual y colectiva de los trabajadores.</textarea></td>
                        <td><input type="text" name="pve_psi_ind1" class="edit" value="Prevalencia"></td>
                        <td><input type="text" name="pve_psi_for1" class="edit" value="No. de Casos Nuevos + No. de Casos Antiguos / No. de trabajadores expuestos x 100000"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_psi_ind2" class="edit" value="Incidencia"></td>
                        <td><input type="text" name="pve_psi_for2" class="edit" value="No. de Casos nuevos / Número de trabajadores expuestos x 100000"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_psi_ind3" class="edit" value="Cumplimiento"></td>
                        <td><input type="text" name="pve_psi_for3" class="edit" value="No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_psi_ind4" class="edit" value="Cobertura"></td>
                        <td><input type="text" name="pve_psi_for4" class="edit" value="# de trabajadores que participan / # de trabajadores programados x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="pve_psi_ind5" class="edit" value="Eficacia"></td>
                        <td><input type="text" name="pve_psi_for5" class="edit" value="No. de recomendaciones cerradas / Total de recomendaciones x 100%"></td>
                    </tr>
                </tbody>
            </table>

            <div class="box">
                <input type="text" name="lbl_psi_prev" style="font-weight:bold; border:none; background:transparent; width:100%; outline:none;" value="Prevalencia e incidencia">
                <textarea name="pve_psico_prev" class="edit" rows="2">Se mantienen en 0%, ningún caso antiguo ni nuevo calificado como enfermedad laboral con relación a patologías de riesgo psicosocial.</textarea>
            </div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_5" class="edit center" placeholder="(Gráfica - Cumplimiento PVE Psicosocial)"></textarea></div>
            <div class="box"><textarea name="pve_psico_cumpl" class="edit" rows="2" placeholder="Realizar análisis de cumplimiento según PVE psicosocial"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_6" class="edit center" placeholder="(Gráfica - Cobertura PVE Psicosocial)"></textarea></div>
            <div class="box"><textarea name="pve_psico_cob" class="edit" rows="2" placeholder="Realizar análisis de cobertura según PVE psicosocial"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_7" class="edit center" placeholder="(Gráfica - Eficacia PVE Psicosocial)"></textarea></div>
            <div class="box"><textarea name="pve_psico_efic" class="edit" rows="2" placeholder="Realizar análisis de eficacia según PVE psicosocial"></textarea></div>

            <!-- SECCIÓN CAPACITACIONES -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_cap" class="sec-h-input" value="Programa de Capacitaciones">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_cap_h1" class="th-input" value="OBJETIVO"></th>
                        <th class="th-bg" style="width:180px;"><input type="text" name="lbl_cap_h2" class="th-input" value="INDICADOR"></th>
                        <th class="th-bg"><input type="text" name="lbl_cap_h3" class="th-input" value="FÓRMULA DEL INDICADOR"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="3"><textarea name="cap_obj_txt" class="edit" rows="4">Prevenir la ocurrencia de accidentes y enfermedades laborales por medio de capacitaciones.</textarea></td>
                        <td><input type="text" name="cap_ind1" class="edit" value="Cumplimiento"></td>
                        <td><input type="text" name="cap_for1" class="edit" value="No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="cap_ind2" class="edit" value="Cobertura"></td>
                        <td><input type="text" name="cap_for2" class="edit" value="No. de trabajadores que participan / No. de trabajadores programados x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="cap_ind3" class="edit" value="Eficacia"></td>
                        <td><input type="text" name="cap_for3" class="edit" value="Reducción de la accidentalidad en los trabajadores"></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_8" class="edit center" placeholder="(Gráfica - Cumplimiento Capacitaciones)"></textarea></div>
            <div class="box"><textarea name="cap_cumpl" class="edit" rows="2" placeholder="Realizar análisis del cumplimiento del programa de capacitaciones"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_9" class="edit center" placeholder="(Gráfica - Cobertura Capacitaciones)"></textarea></div>
            <div class="box"><textarea name="cap_cob" class="edit" rows="2" placeholder="Realizar análisis de la cobertura del programa de capacitaciones"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_10" class="edit center" placeholder="(Gráfica - Eficacia Capacitaciones)"></textarea></div>
            <div class="box"><textarea name="cap_efic" class="edit" rows="2" placeholder="Realizar análisis de la eficacia del programa de capacitaciones"></textarea></div>

            <!-- SECCIÓN INSPECCIONES -->
            <div class="sec-h-edit">
                <input type="text" name="h_sec_insp" class="sec-h-input" value="Programa de Inspecciones">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_ins_h1" class="th-input" value="OBJETIVO"></th>
                        <th class="th-bg" style="width:180px;"><input type="text" name="lbl_ins_h2" class="th-input" value="INDICADOR"></th>
                        <th class="th-bg"><input type="text" name="lbl_ins_h3" class="th-input" value="FÓRMULA DEL INDICADOR"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2"><textarea name="insp_obj_txt" class="edit" rows="4">Identificar de manera proactiva condiciones inseguras en las actividades realizadas por los funcionarios, con el fin de corregirlas y minimizar la probabilidad de ocurrencia de lesiones, daños o interrupciones del trabajo.</textarea></td>
                        <td><input type="text" name="insp_ind1" class="edit" value="Cumplimiento"></td>
                        <td><input type="text" name="insp_for1" class="edit" value="No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="insp_ind2" class="edit" value="Eficacia"></td>
                        <td><input type="text" name="insp_for2" class="edit" value="No. de planes de acción desarrollados / No. de planes de acción propuestos"></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_11" class="edit center" placeholder="(Gráfica - Cumplimiento Inspecciones)"></textarea></div>
            <div class="box"><textarea name="insp_cumpl" class="edit" rows="2" placeholder="Realizar análisis del cumplimiento del programa de inspecciones"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_12" class="edit center" placeholder="(Gráfica - Eficacia Inspecciones)"></textarea></div>
            <div class="box"><textarea name="insp_efic" class="edit" rows="2" placeholder="Realizar análisis de la eficacia del programa de inspecciones"></textarea></div>

            <!-- TABLA DINÁMICA: INDICADORES DE ACCIDENTALIDAD -->
            <div class="sec-h-edit d-flex justify-content-between align-items-center">
                <input type="text" name="h_sec_acc" class="sec-h-input" value="Indicadores de accidentalidad" style="width:auto; flex-grow:1;">
                <button type="button" class="btn btn-primary btn-sm btn-row-action" onclick="addFilaAccidentalidad()">
                    <i class="fa-solid fa-plus"></i> Agregar Fila
                </button>
            </div>
            <table class="formtbl" id="tabla-accidentalidad">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_acc_th1" class="th-input" value="MES / PERIODO"></th>
                        <th class="th-bg" style="width:120px;"><input type="text" name="lbl_acc_th2" class="th-input" value="No. Casos"></th>
                        <th class="th-bg"><input type="text" name="lbl_acc_th3" class="th-input" value="TIPO DE LESIÓN"></th>
                        <th style="width:50px;" class="btn-row-action th-bg"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="acc_mes[]" class="edit" type="text" placeholder="Ej: Marzo"></td>
                        <td><input name="acc_casos[]" class="edit center" type="number" min="0"></td>
                        <td><input name="acc_lesion[]" class="edit" type="text" placeholder="Ej: CONTUSIÓN"></td>
                        <td class="text-center align-middle btn-row-action">
                            <button type="button" class="btn btn-danger btn-sm p-1" onclick="deleteFila(this)" style="line-height:1;">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_acc_tot" class="th-input" value="TOTAL" style="text-align:left;"></th>
                        <th class="center"><input name="acc_total_casos" class="edit center" style="font-weight:bold;" type="text" placeholder="0"></th>
                        <th></th>
                        <th class="btn-row-action"></th>
                    </tr>
                </tfoot>
            </table>

            <div class="box">
                <textarea name="txt_acc_analisis" class="edit" rows="3">Durante el periodo enero – diciembre se presentaron accidentes de trabajo. Realizar aquí el análisis de accidentalidad, comparativo con periodos anteriores, mecanismos de lesión y severidad.</textarea>
            </div>

            <!-- TABLA DE FÓRMULAS DE ACCIDENTALIDAD -->
            <table class="formtbl">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_f_h1" class="th-input" value="Nombre"></th>
                        <th class="th-bg" style="width:120px;"><input type="text" name="lbl_f_h2" class="th-input" value="Meta"></th>
                        <th class="th-bg"><input type="text" name="lbl_f_h3" class="th-input" value="Fórmula"></th>
                        <th class="th-bg"><input type="text" name="lbl_f_h4" class="th-input" value="Análisis"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="fn_name1" class="edit" value="Frecuencia de accidentalidad" style="font-weight:bold;"></td>
                        <td><input type="text" name="fn_meta1" class="edit center" value="< 3%"></td>
                        <td><textarea name="fn_for1" class="edit" rows="2">Número de accidentes de trabajo en el último periodo / Total de trabajadores x 100</textarea></td>
                        <td><textarea name="ind_acc_frec" class="edit" rows="2"></textarea></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="fn_name2" class="edit" value="Severidad de accidentalidad" style="font-weight:bold;"></td>
                        <td><input type="text" name="fn_meta2" class="edit center" value="< 15"></td>
                        <td><textarea name="fn_for2" class="edit" rows="2">(Número de días de incapacidad por accidente de trabajo en el mes + número de días cargados en el mes / Número de trabajadores en el mes) * 100</textarea></td>
                        <td><textarea name="ind_acc_sev" class="edit" rows="2"></textarea></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="fn_name3" class="edit" value="Tasa de incidencia" style="font-weight:bold;"></td>
                        <td><input type="text" name="fn_meta3" class="edit center" value="< 2%"></td>
                        <td><textarea name="fn_for3" class="edit" rows="2">AT * 100 / Número de trabajadores</textarea></td>
                        <td><textarea name="ind_acc_inc" class="edit" rows="2"></textarea></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="fn_name4" class="edit" value="Índice de mortalidad" style="font-weight:bold;"></td>
                        <td><input type="text" name="fn_meta4" class="edit center" value="0"></td>
                        <td><textarea name="fn_for4" class="edit" rows="2">Número de eventos mortales / total de accidentes presentados en el periodo * 100</textarea></td>
                        <td><textarea name="ind_acc_mort" class="edit" rows="2">Durante el periodo no se presentan accidentes mortales en la organización.</textarea></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="fn_name5" class="edit" value="Ausentismo por causa médica" style="font-weight:bold;"></td>
                        <td><input type="text" name="fn_meta5" class="edit center" value="< 2%"></td>
                        <td><textarea name="fn_for5" class="edit" rows="2">(Número de días de ausencia por incapacidad laboral o común en el mes / Número de días de trabajo programados en el mes) * 100</textarea></td>
                        <td><textarea name="ind_acc_aus" class="edit" rows="2"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <!-- TABLA DINÁMICA DE AUSENTISMO GENERAL -->
            <div class="sec-h-edit d-flex justify-content-between align-items-center">
                <input type="text" name="h_sec_aus" class="sec-h-input" value="Ausentismo general" style="width:auto; flex-grow:1;">
                <button type="button" class="btn btn-primary btn-sm btn-row-action" onclick="addFilaAusentismo()">
                    <i class="fa-solid fa-plus"></i> Agregar Fila
                </button>
            </div>
            <table class="formtbl" id="tabla-ausentismo">
                <thead>
                    <tr>
                        <th class="th-bg"><input type="text" name="lbl_aus_th1" class="th-input" value="Objetivo"></th>
                        <th class="th-bg" style="width:150px;"><input type="text" name="lbl_aus_th2" class="th-input" value="Meta"></th>
                        <th class="th-bg"><input type="text" name="lbl_aus_th3" class="th-input" value="Indicador / Análisis"></th>
                        <th style="width:50px;" class="btn-row-action th-bg"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="aus_obj[]" class="edit" type="text" value="Controlar las estadísticas de ausentismo"></td>
                        <td><input name="aus_meta[]" class="edit center" type="text" value="<= 4%"></td>
                        <td><textarea name="aus_indicador[]" class="edit" rows="2" placeholder="Análisis del indicador de ausentismo..."></textarea></td>
                        <td class="text-center align-middle btn-row-action">
                            <button type="button" class="btn btn-danger btn-sm p-1" onclick="deleteFila(this)" style="line-height:1;">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
    function addFilaAccidentalidad() {
        const tbody = document.querySelector("#tabla-accidentalidad tbody");
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td><input name="acc_mes[]" class="edit" type="text" placeholder="Ej: Marzo"></td>
            <td><input name="acc_casos[]" class="edit center" type="number" min="0"></td>
            <td><input name="acc_lesion[]" class="edit" type="text" placeholder="Ej: CONTUSIÓN"></td>
            <td class="text-center align-middle btn-row-action">
                <button type="button" class="btn btn-danger btn-sm p-1" onclick="deleteFila(this)" style="line-height:1;">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(nuevaFila);
    }

    function addFilaAusentismo() {
        const tbody = document.querySelector("#tabla-ausentismo tbody");
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td><input name="aus_obj[]" class="edit" type="text" placeholder="Objetivo..."></td>
            <td><input name="aus_meta[]" class="edit center" type="text" placeholder="Meta..."></td>
            <td><textarea name="aus_indicador[]" class="edit" rows="2" placeholder="Análisis..."></textarea></td>
            <td class="text-center align-middle btn-row-action">
                <button type="button" class="btn btn-danger btn-sm p-1" onclick="deleteFila(this)" style="line-height:1;">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(nuevaFila);
    }

    function deleteFila(button) {
        const fila = button.closest("tr");
        const tbody = fila.parentNode;
        if (tbody.querySelectorAll("tr").length > 1) {
            fila.remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe permanecer al menos una fila en la tabla.',
                confirmButtonColor: '#1b4fbd'
            });
        }
    }
</script>
</body>
</html>