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
// Ajusta el ID de este ítem según tu base de datos (Ej: 30 para "Rendición de Cuentas")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 30; 

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
    <title>2.6.1-2 - Informe de Rendición de Cuentas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .tiny{ font-size:11px; color:#6b7280; font-weight:700; }

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

        .logo-box{
            border:2px dashed rgba(0,0,0,.25);
            height:68px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:800;
            color:rgba(0,0,0,.35);
            text-align:center;
            font-size:11px;
            padding: 4px;
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

        .cover{
            min-height:860px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:30px 20px;
        }

        .cover-title{
            font-size:28px;
            font-weight:900;
            text-transform:uppercase;
            line-height:1.25;
            max-width:760px;
            margin-bottom:24px;
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

        .text-just{
            text-align:justify;
            line-height:1.65;
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
            vertical-align:top;
        }

        .formtbl th{
            background:#f1f3f6;
            text-align:center;
            font-weight:900;
            color:#14253d;
            font-size:12px;
        }

        .edit,
        .edit-inline{
            width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:0;
            color:#111;
        }

        .edit-inline{
            display:inline-block;
            width:auto;
            min-width:140px;
            max-width: 100%;
        }

        textarea.edit{
            resize:vertical;
            min-height:70px;
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

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
            margin-top:24px;
        }

        .sign{
            border-top:1px solid #111;
            padding-top:8px;
            text-align:center;
            min-height:56px;
            font-size:12px;
            font-weight:700;
            position: relative;
        }

        .small{ font-size:11px; }
        .center{ text-align:center; }

        @media print{
            body{ background:#fff; }
            .toolbar{ display:none !important; }
            .sheet{ box-shadow:none; margin-bottom:0; border:2px solid #000; }
        }

        @media (max-width: 768px){
            .sign-grid{
                grid-template-columns:1fr;
                gap: 40px;
            }
            .cover-title{
                font-size:22px;
            }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
<div class="wrap">

    <div class="toolbar print-hide">
        <div style="display:flex; gap:8px;">
            <button class="btn-action" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-action" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-success-action" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-primary-action" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
        <div class="tiny text-end">
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">RENDICIÓN DE CUENTAS</span><br>
            Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet page-break">
            <div class="cover">
                <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 120px; object-fit: contain;">
                    <?php else: ?>
                        LOGO EMPRESA
                    <?php endif; ?>
                </div>
                <div class="cover-title">REVISIÓN POR LA DIRECCIÓN Y RENDICIÓN DE CUENTAS</div>
                <div class="cover-text">Versión 0</div>
                <div class="cover-text">IN-SST-03</div>
                <div class="cover-text">FECHA: <input type="text" name="cover_fecha" class="edit-inline center" placeholder="XX/XX/XXXX"></div>
                <div class="cover-text" style="margin-top:20px;">INFORME DE RENDICIÓN DE CUENTAS</div>
                <div class="cover-text">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
                <div class="cover-text"><input type="text" name="cover_empresa" class="edit-inline center" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>"></div>
                <div class="cover-text" style="margin-top:20px;">DECRETO 1072 DE 2015</div>
                <div class="cover-text">RESOLUCIÓN 0312 DEL 2019</div>
                <div class="cover-text" style="margin-top:20px;">PERIODO: <input type="text" name="cover_periodo" class="edit-inline center" placeholder="ENERO – DICIEMBRE 202X"></div>
                <div class="cover-text">FECHA DE REALIZACIÓN: <input type="text" name="cover_fecha_realizacion" class="edit-inline center" placeholder="Día de Mes de Año"></div>
            </div>
        </div>

        <div class="sheet">

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
                    <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td><strong>Versión:</strong> 0</td>
                    <td><strong>Código:</strong><br>IN-SST-03</td>
                </tr>
                <tr>
                    <td class="subtitle">INFORME DE RENDICIÓN DE CUENTAS</td>
                    <td colspan="2"><strong>Fecha:</strong> <input type="date" name="meta_fecha_2" id="metaFecha2" style="border:none; font-size:10px; font-weight:900; outline:none; background:transparent; width:100%;"></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Periodo:</strong> <input name="meta_periodo" class="edit-inline" type="text" placeholder="Enero - Diciembre 202X"></td>
                </tr>
            </table>

            <div class="sec-h">Introducción</div>
            <div class="box text-just">
                <textarea name="txt_intro" class="edit" rows="12">Las Revisiones Gerenciales son convocadas por el Gerente General de la Empresa o su designado, una vez al año o antes de encontrarse la necesidad. Los aspectos a tener en cuenta como marco para el análisis de las revisiones son:

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

            <div class="sec-h">Política del SG SST</div>
            <div class="policy-placeholder">
                <textarea name="txt_politica_sst" class="edit center" rows="3" placeholder="COLOCAR O DESCRIBIR LA POLÍTICA FIRMADA POR EL REPRESENTANTE LEGAL O DEJAR COMO ESPACIO PARA ANEXO"></textarea>
            </div>

            <div class="sec-h">Política de no alcohol, tabaco y sustancias psicoactivas</div>
            <div class="policy-placeholder">
                <textarea name="txt_politica_alcohol" class="edit center" rows="3" placeholder="COLOCAR O DESCRIBIR LA POLÍTICA FIRMADA POR EL REPRESENTANTE LEGAL O DEJAR COMO ESPACIO PARA ANEXO"></textarea>
            </div>

            <div class="sec-h">Ejecución del presupuesto y suficiencia de los recursos</div>
            <div class="box">
                <textarea name="txt_presupuesto" class="edit" rows="5" placeholder="Coloca el presupuesto de la herramienta SG-SST con el análisis realizado"></textarea>
            </div>

            <div class="sec-h">Cumplimiento del plan de trabajo</div>
            <table class="formtbl">
                <tbody>
                    <tr>
                        <th style="width:180px;">Objetivo</th>
                        <td><input name="pt_obj" class="edit" type="text" value="Planear y controlar la ejecución de las actividades del Sistema de Gestión de Seguridad y Salud en el Trabajo."></td>
                    </tr>
                    <tr>
                        <th>Meta</th>
                        <td><input name="pt_meta" class="edit" type="text" value="Cumplir con el 80% de las actividades propuestas"></td>
                    </tr>
                </tbody>
            </table>
            <div class="graph-placeholder"><textarea name="nota_grafica_1" class="edit center" placeholder="(Espacio reservado para anexar Gráfica de Cumplimiento del Plan de Trabajo)"></textarea></div>

            <div class="sec-h">Programa de Vigilancia Epidemiológica para la Prevención de Lesiones Osteomusculares</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>OBJETIVO</th>
                        <th style="width:180px;">INDICADOR</th>
                        <th>FÓRMULA DEL INDICADOR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5">Prevenir la aparición de desórdenes músculo esqueléticos a través de la identificación, evaluación e intervención de las condiciones no ergonómicas encontradas en los puestos de trabajo.</td>
                        <td>Prevalencia</td>
                        <td>No. de Casos Nuevos + No. de Casos Antiguos / No. de trabajadores expuestos x 100000</td>
                    </tr>
                    <tr>
                        <td>Incidencia</td>
                        <td>No. de Casos nuevos / Número de trabajadores expuestos x 100000</td>
                    </tr>
                    <tr>
                        <td>Cumplimiento</td>
                        <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                    </tr>
                    <tr>
                        <td>Cobertura</td>
                        <td># de trabajadores que participan / # de trabajadores programados x 100%</td>
                    </tr>
                    <tr>
                        <td>Eficacia</td>
                        <td>No. de recomendaciones cerradas / Total de recomendaciones x 100%</td>
                    </tr>
                </tbody>
            </table>

            <div class="box">
                <strong>Prevalencia e incidencia</strong>
                <textarea name="pve_osteo_prev" class="edit" rows="3">Se mantienen en 0%, ningún caso antiguo ni nuevo calificado como enfermedad laboral con relación a patologías osteomusculares.</textarea>
            </div>
            <div class="graph-placeholder"><textarea name="nota_grafica_2" class="edit center" placeholder="(Gráfica - Cumplimiento PVE Osteomuscular)"></textarea></div>
            <div class="box"><textarea name="pve_osteo_cumpl" class="edit" rows="3" placeholder="Realizar análisis de cumplimiento según PVE osteomuscular"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_3" class="edit center" placeholder="(Gráfica - Cobertura PVE Osteomuscular)"></textarea></div>
            <div class="box"><textarea name="pve_osteo_cob" class="edit" rows="3" placeholder="Realizar análisis de cobertura según PVE osteomuscular"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_4" class="edit center" placeholder="(Gráfica - Eficacia PVE Osteomuscular)"></textarea></div>
            <div class="box"><textarea name="pve_osteo_efic" class="edit" rows="3" placeholder="Realizar análisis de eficacia según PVE osteomuscular"></textarea></div>

            <div class="sec-h">Programa de Vigilancia Epidemiológica para la Prevención del Riesgo Psicosocial</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>OBJETIVO</th>
                        <th style="width:180px;">INDICADOR</th>
                        <th>FÓRMULA DEL INDICADOR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5">Implementar un Sistema de Vigilancia Epidemiológica de acuerdo con los parámetros establecidos en la legislación colombiana vigente para preservar, mantener y mejorar la salud individual y colectiva de los trabajadores.</td>
                        <td>Prevalencia</td>
                        <td>No. de Casos Nuevos + No. de Casos Antiguos / No. de trabajadores expuestos x 100000</td>
                    </tr>
                    <tr>
                        <td>Incidencia</td>
                        <td>No. de Casos nuevos / Número de trabajadores expuestos x 100000</td>
                    </tr>
                    <tr>
                        <td>Cumplimiento</td>
                        <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                    </tr>
                    <tr>
                        <td>Cobertura</td>
                        <td># de trabajadores que participan / # de trabajadores programados x 100%</td>
                    </tr>
                    <tr>
                        <td>Eficacia</td>
                        <td>No. de recomendaciones cerradas / Total de recomendaciones x 100%</td>
                    </tr>
                </tbody>
            </table>

            <div class="box">
                <strong>Prevalencia e incidencia</strong>
                <textarea name="pve_psico_prev" class="edit" rows="3">Se mantienen en 0%, ningún caso antiguo ni nuevo calificado como enfermedad laboral con relación a patologías de riesgo psicosocial.</textarea>
            </div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_5" class="edit center" placeholder="(Gráfica - Cumplimiento PVE Psicosocial)"></textarea></div>
            <div class="box"><textarea name="pve_psico_cumpl" class="edit" rows="3" placeholder="Realizar análisis de cumplimiento según PVE psicosocial"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_6" class="edit center" placeholder="(Gráfica - Cobertura PVE Psicosocial)"></textarea></div>
            <div class="box"><textarea name="pve_psico_cob" class="edit" rows="3" placeholder="Realizar análisis de cobertura según PVE psicosocial"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_7" class="edit center" placeholder="(Gráfica - Eficacia PVE Psicosocial)"></textarea></div>
            <div class="box"><textarea name="pve_psico_efic" class="edit" rows="3" placeholder="Realizar análisis de eficacia según PVE psicosocial"></textarea></div>

            <div class="sec-h">Programa de Capacitaciones</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>OBJETIVO</th>
                        <th style="width:180px;">INDICADOR</th>
                        <th>FÓRMULA DEL INDICADOR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="3">Prevenir la ocurrencia de accidentes y enfermedades laborales por medio de capacitaciones.</td>
                        <td>Cumplimiento</td>
                        <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                    </tr>
                    <tr>
                        <td>Cobertura</td>
                        <td>No. de trabajadores que participan / No. de trabajadores programados x 100%</td>
                    </tr>
                    <tr>
                        <td>Eficacia</td>
                        <td>Reducción de la accidentalidad en los trabajadores</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_8" class="edit center" placeholder="(Gráfica - Cumplimiento Capacitaciones)"></textarea></div>
            <div class="box"><textarea name="cap_cumpl" class="edit" rows="3" placeholder="Realizar análisis del cumplimiento del programa de capacitaciones"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_9" class="edit center" placeholder="(Gráfica - Cobertura Capacitaciones)"></textarea></div>
            <div class="box"><textarea name="cap_cob" class="edit" rows="3" placeholder="Realizar análisis de la cobertura del programa de capacitaciones"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_10" class="edit center" placeholder="(Gráfica - Eficacia Capacitaciones)"></textarea></div>
            <div class="box"><textarea name="cap_efic" class="edit" rows="3" placeholder="Realizar análisis de la eficacia del programa de capacitaciones"></textarea></div>

            <div class="sec-h">Programa de Inspecciones</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>OBJETIVO</th>
                        <th style="width:180px;">INDICADOR</th>
                        <th>FÓRMULA DEL INDICADOR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2">Identificar de manera proactiva condiciones inseguras en las actividades realizadas por los funcionarios, con el fin de corregirlas y minimizar la probabilidad de ocurrencia de lesiones, daños o interrupciones del trabajo.</td>
                        <td>Cumplimiento</td>
                        <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                    </tr>
                    <tr>
                        <td>Eficacia</td>
                        <td>No. de planes de acción desarrollados / No. de planes de acción propuestos</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_11" class="edit center" placeholder="(Gráfica - Cumplimiento Inspecciones)"></textarea></div>
            <div class="box"><textarea name="insp_cumpl" class="edit" rows="3" placeholder="Realizar análisis del cumplimiento del programa de inspecciones"></textarea></div>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_12" class="edit center" placeholder="(Gráfica - Eficacia Inspecciones)"></textarea></div>
            <div class="box"><textarea name="insp_efic" class="edit" rows="3" placeholder="Realizar análisis de la eficacia del programa de inspecciones"></textarea></div>

            <div class="sec-h">Indicadores de accidentalidad</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>MES / PERIODO</th>
                        <th style="width:120px;">No. Casos</th>
                        <th>TIPO DE LESIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=0; $i<6; $i++): ?>
                    <tr>
                        <td><input name="acc_mes[]" class="edit" type="text" placeholder="Ej: Marzo"></td>
                        <td><input name="acc_casos[]" class="edit center" type="number" min="0"></td>
                        <td><input name="acc_lesion[]" class="edit" type="text" placeholder="Ej: CONTUSIÓN"></td>
                    </tr>
                    <?php endfor; ?>
                    <tr>
                        <th>TOTAL</th>
                        <th class="center"><input name="acc_total_casos" class="edit center" style="font-weight:bold;" type="text" placeholder="0"></th>
                        <th></th>
                    </tr>
                </tbody>
            </table>

            <div class="box">
                <textarea name="txt_acc_analisis" class="edit" rows="5">Durante el periodo enero – diciembre se presentaron accidentes de trabajo. Realizar aquí el análisis de accidentalidad, comparativo con periodos anteriores, mecanismos de lesión y severidad.</textarea>
            </div>

            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th style="width:180px;">Meta</th>
                        <th>Fórmula</th>
                        <th>Análisis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Frecuencia de accidentalidad</td>
                        <td>&lt; 3%</td>
                        <td>Número de accidentes de trabajo en el último periodo / Total de trabajadores x 100</td>
                        <td><textarea name="ind_acc_frec" class="edit" rows="3"></textarea></td>
                    </tr>
                    <tr>
                        <td>Severidad de accidentalidad</td>
                        <td>&lt; 15</td>
                        <td>(Número de días de incapacidad por accidente de trabajo en el mes + número de días cargados en el mes / Número de trabajadores en el mes) * 100</td>
                        <td><textarea name="ind_acc_sev" class="edit" rows="3"></textarea></td>
                    </tr>
                    <tr>
                        <td>Tasa de incidencia</td>
                        <td>&lt; 2%</td>
                        <td>AT * 100 / Número de trabajadores</td>
                        <td><textarea name="ind_acc_inc" class="edit" rows="3"></textarea></td>
                    </tr>
                    <tr>
                        <td>Índice de mortalidad</td>
                        <td>0</td>
                        <td>Número de eventos mortales / total de accidentes presentados en el periodo * 100</td>
                        <td><textarea name="ind_acc_mort" class="edit" rows="3">Durante el periodo no se presentan accidentes mortales en la organización.</textarea></td>
                    </tr>
                    <tr>
                        <td>Ausentismo por causa médica</td>
                        <td>&lt; 2%</td>
                        <td>(Número de días de ausencia por incapacidad laboral o común en el mes / Número de días de trabajo programados en el mes) * 100</td>
                        <td><textarea name="ind_acc_aus" class="edit" rows="3"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">Ausentismo general</div>
            <table class="formtbl">
                <tbody>
                    <tr>
                        <th style="width:180px;">Objetivo</th>
                        <td>Controlar las estadísticas de ausentismo</td>
                        <th style="width:140px;">Meta</th>
                        <td>&lt;= 4%</td>
                    </tr>
                    <tr>
                        <th>Indicador</th>
                        <td>Ausentismo Laboral Global = (Total Días por Ausentismo / D.T) x 100</td>
                        <th>Frecuencia</th>
                        <td>Mensual / Anual</td>
                    </tr>
                </tbody>
            </table>

            <table class="formtbl">
                <thead>
                    <tr>
                        <th>MES</th>
                        <th># DE TRABAJADORES</th>
                        <th>TOTAL DÍAS TRABAJADOS</th>
                        <th>DÍAS PERDIDOS</th>
                        <th>HORAS PERDIDAS</th>
                        <th>% AUSENTISMO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $meses = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
                    foreach($meses as $mes):
                    ?>
                    <tr>
                        <td>
                            <?php echo $mes; ?>
                            <input type="hidden" name="aus_mes[]" value="<?php echo $mes; ?>">
                        </td>
                        <td><input name="aus_trabajadores[]" class="edit center" type="text"></td>
                        <td><input name="aus_dias_trab[]" class="edit center" type="text"></td>
                        <td><input name="aus_dias_perd[]" class="edit center" type="text"></td>
                        <td><input name="aus_horas_perd[]" class="edit center" type="text"></td>
                        <td><input name="aus_porc[]" class="edit center" type="text"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="graph-placeholder"><textarea name="nota_grafica_13" class="edit center" placeholder="(Gráfica - Ausentismo)"></textarea></div>
            <div class="graph-placeholder"><textarea name="nota_grafica_14" class="edit center" placeholder="(Gráfica - Motivo del Ausentismo)"></textarea></div>
            <div class="box"><textarea name="txt_aus_analisis" class="edit" rows="5" placeholder="La tasa general de ausentismo debe analizarse aquí con sus principales causas y tendencias."></textarea></div>

            <div class="sec-h">Resolución 312 de 2019 - Eficacia del SG SST</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Criterios de eficacia</th>
                        <th style="width:180px;">Puntaje obtenido en evidencia</th>
                        <th style="width:180px;">Puntaje obtenido en implementación</th>
                        <th style="width:120px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $criteriosEficacia = [
                        'RECURSOS', 'GESTIÓN INTEGRAL DEL SG SST', 'GESTIÓN DE LA SALUD', 
                        'GESTIÓN DE PELIGROS Y RIESGOS', 'GESTIÓN DE AMENAZAS', 
                        'VERIFICACIÓN DEL SG-SST', 'MEJORAMIENTO'
                    ];
                    foreach($criteriosEficacia as $criterio): 
                    ?>
                    <tr>
                        <td>
                            <?php echo $criterio; ?>
                            <input type="hidden" name="ef_criterio[]" value="<?php echo $criterio; ?>">
                        </td>
                        <td><input name="ef_evidencia[]" class="edit center" type="text"></td>
                        <td><input name="ef_implementacion[]" class="edit center" type="text"></td>
                        <td><input name="ef_total[]" class="edit center" type="text"></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th>Puntaje de eficacia del SG SST</th>
                        <th><input name="ef_gran_evidencia" class="edit center" style="font-weight:bold" type="text"></th>
                        <th><input name="ef_gran_implementacion" class="edit center" style="font-weight:bold" type="text"></th>
                        <th><input name="ef_gran_total" class="edit center" style="font-weight:bold" type="text"></th>
                    </tr>
                </tbody>
            </table>
            
            <div class="graph-placeholder"><textarea name="nota_grafica_15" class="edit center" placeholder="(Gráfica - Cumplimiento del SG SST)"></textarea></div>

            <div class="sec-h">COPASST</div>
            <div class="box">
                <textarea name="txt_copasst" class="edit" rows="4" placeholder="Ingrese la tabla o resumen de seguimiento del COPASST y realice un análisis del cumplimiento de actividades"></textarea>
            </div>

            <div class="sec-h">Otras actividades de seguridad y salud en el trabajo</div>
            <div class="box">
                <textarea name="txt_otras_act" class="edit" rows="4"></textarea>
            </div>

            <div class="sec-h">Requisitos legales SG SST</div>
            <table class="formtbl">
                <tbody>
                    <tr>
                        <th style="width:180px;">Objetivo</th>
                        <td>Calcular el porcentaje de cumplimiento de los requisitos legales de seguridad y salud en el trabajo aplicables anualmente.</td>
                    </tr>
                    <tr>
                        <th>Indicador</th>
                        <td>(No. de requisitos aplicables que se cumplen / No. de requisitos aplicables) x 100</td>
                    </tr>
                    <tr>
                        <th>Meta</th>
                        <td>90%</td>
                    </tr>
                    <tr>
                        <th>Frecuencia</th>
                        <td>Mensual / Anual</td>
                    </tr>
                </tbody>
            </table>
            <div class="box">
                <textarea name="txt_req_legales" class="edit" rows="5">El cumplimiento de los requisitos legales durante el periodo evaluado fue de ____%, de ____ requisitos aplicables se dio cumplimiento a ____ requisitos. Pendiente de ejecución: ________________________________.</textarea>
            </div>

            <div class="sec-h">Participación de los trabajadores</div>
            <div class="box">
                <textarea name="txt_participacion" class="edit" rows="10">La empresa brinda a sus empleados diversas vías de participación para establecer una comunicación permanente y efectiva que permita conocer las necesidades, dudas e inquietudes que ayuden a mejorar la gestión del Sistema de Seguridad y Salud en el Trabajo.

Comunicación interna:
Comités formales:
Capacitaciones y talleres:
Participación en la identificación de peligros:</textarea>
            </div>

            <div class="sec-h">Resultado de la auditoría interna al SG SST</div>
            <div class="graph-placeholder"><textarea name="nota_grafica_16" class="edit center" placeholder="(Gráfica - Cumplimiento Auditoría)"></textarea></div>
            <div class="box">
                <textarea name="txt_auditoria" class="edit" rows="5">El Sistema de Gestión de Seguridad y Salud en el Trabajo presenta avances en la implementación de los requisitos establecidos en el Decreto 1072 de 2015 y Resolución 0312 de 2019. Realizar aquí el análisis respectivo según la auditoría realizada.</textarea>
            </div>

            <div class="sec-h">Seguimiento y mejora continua: acciones correctivas y preventivas</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:60px;">No.</th>
                        <th>Descripción de la no conformidad</th>
                        <th style="width:160px;">Tipo de acción</th>
                        <th>Plan de acción</th>
                        <th style="width:160px;">Estado de la acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1; $i<=6; $i++): ?>
                    <tr>
                        <td class="center"><?php echo $i; ?></td>
                        <td><textarea name="mej_desc[]" class="edit" rows="3"></textarea></td>
                        <td><input name="mej_tipo[]" class="edit center" type="text" placeholder="Ej: CORRECTIVA"></td>
                        <td><textarea name="mej_plan[]" class="edit" rows="3"></textarea></td>
                        <td><input name="mej_estado[]" class="edit center" type="text" placeholder="Ej: ABIERTA"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="sec-h">Gestión de contratistas</div>
            <div class="box">
                <textarea name="txt_contratistas" class="edit" rows="4" placeholder="Describir las actividades relacionadas con la gestión de contratistas de la organización"></textarea>
            </div>

            <div class="sec-h">Estrategias implementadas para el cumplimiento de objetivos y metas del SG-SST</div>
            <div class="box">
                <textarea name="txt_estrategias" class="edit" rows="10">Ingresar las estrategias y las mejoras a realizar para el siguiente periodo.

Ejemplo:
- Cada año se realiza el diseño de un plan de trabajo de acuerdo con las necesidades de los funcionarios en temas de Seguridad y Salud en el Trabajo.
- Desarrollo de una matriz con contenido acerca de la normatividad y regulaciones en torno a la Seguridad y Salud en el Trabajo.
- Con el fin de prevenir y controlar la accidentalidad se lleva a cabo un registro de todos los accidentes reportados.
- En los casos de enfermedad laboral, se generan actividades de prevención y promoción en el marco de los sistemas de vigilancia epidemiológica.</textarea>
            </div>

            <div class="sign-grid">
                <div class="sign">
                    <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                        <?php if(!empty($firmaRL)): ?>
                            <img src="<?= $firmaRL ?>" alt="Firma Representante Legal" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                        <?php endif; ?>
                    </div>
                    REVISADO Y APROBADO POR:<br><br>
                    REPRESENTANTE LEGAL<br>
                    <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreRL) ?></span>
                </div>
                
                <div class="sign">
                    <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                        <?php if(!empty($firmaSST)): ?>
                            <img src="<?= $firmaSST ?>" alt="Firma Responsable SST" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                        <?php endif; ?>
                    </div>
                    REVISADO Y APROBADO POR:<br><br>
                    REPRESENTANTE DEL SGSST<br>
                    <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreSST) ?></span>
                </div>
            </div>

            <div class="box small" style="margin-top:18px;">
                NOTA: Recuerde que este es un modelo que ayudará a su quehacer diario; es importante aplicar su conocimiento como profesional SST y agregar los ítems que usted crea necesarios.
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
        document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;

        const fmeta2 = document.getElementById("metaFecha2");
        if (fmeta2 && !fmeta2.value) fmeta2.value = `${y}-${m}-${dd}`;
        
        // Poner en portada también si aplica
        const c_fecha = document.querySelector('input[name="cover_fecha"]');
        if(c_fecha && !c_fecha.value) c_fecha.value = `${dd}/${m}/${y}`;
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
                    text: 'Informe guardado correctamente',
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