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
// Ajusta el ID de este ítem según tu base de datos (Ej: 41 para Manual SG-SST)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 41; 

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
    <title>MN-SST-02 | Manual SG-SST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --line:#111;
            --blue:#d8e4f6;
            --blue2:#b9cdea;
            --soft:#f4f7fb;
            --text:#1c1c1c;
            --muted:#666;
            --title:#213b67;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
            --green:#198754;
            --green-hover:#146c43;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:#edf2f8;
            color:var(--text);
            font-family:Arial, Helvetica, sans-serif;
        }

        .toolbar{
            position:sticky;
            top:0;
            z-index:100;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:10px 18px;
            background:#dfe8f5;
            border-bottom:1px solid #cdd8e7;
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

        .page-wrap{
            padding:24px;
            max-width: 1150px;
            margin: 0 auto 60px;
        }

        .paper{
            background:#fff;
            border:1px solid #d7dee9;
            box-shadow:0 12px 35px rgba(0,0,0,.08);
            padding:34px;
            border-radius: 8px;
        }

        .doc-header{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-bottom:22px;
        }

        .doc-header td{
            border:1px solid var(--line);
            padding:8px 10px;
            vertical-align:middle;
        }

        .logo-box{
            width:22%;
            text-align:center;
            font-weight:700;
            color:#666;
            background:#fafafa;
            min-height:88px;
        }

        .header-main{
            width:66%;
            text-align:center;
            font-weight:800;
            font-size:15px;
            line-height:1.35;
        }

        .header-side{
            width:12%;
            padding:0 !important;
        }

        .side-grid{
            display:flex;
            flex-direction:column;
            min-height:88px;
        }

        .side-grid div, .side-grid input{
            flex:1;
            display:flex;
            justify-content:center;
            align-items:center;
            border-bottom:1px solid var(--line);
            font-weight:700;
            text-align:center;
            padding:6px;
            width: 100%;
            border-top:none; border-left:none; border-right:none;
            background: transparent; outline: none; font-size: 13px;
        }
        .side-grid input:focus { background: #f8fbff; }
        .side-grid input:last-child, .side-grid div:last-child{ border-bottom:none; }

        .doc-title{
            text-align:center;
            font-size:30px;
            font-weight:900;
            letter-spacing:.4px;
            margin:16px 0 6px;
            text-transform:uppercase;
        }

        .doc-subtitle{
            text-align:center;
            color:var(--muted);
            font-size:14px;
            margin-bottom:18px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }

        .intro-block{
            text-align:center;
            margin:18px 0 24px;
        }
        .intro-block .empresa{
            font-size:20px;
            font-weight:800;
            color:var(--title);
            margin-bottom:6px;
        }
        .intro-block .fecha{
            color:#555;
            font-size:15px;
        }

        .table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-bottom:20px;
        }

        .table td, .table th{
            border:1px solid var(--line);
            padding:9px 10px;
            font-size:14px;
            vertical-align:middle;
        }

        .table th{
            background:var(--blue);
            text-transform:uppercase;
            font-weight:800;
            text-align:center;
        }

        .label{
            background:var(--blue);
            font-weight:700;
            width:22%;
        }

        .input-line{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:14px;
            font-family: inherit;
        }
        .input-line:focus { background: #f8fbff; }

        textarea.input-line {
            resize: vertical;
            min-height: 24px;
            line-height: 1.4;
        }

        .signature-box{
            height:60px;
            position: relative;
            background: #fff;
        }

        .section{
            margin-bottom:20px;
        }

        .section-title{
            background:var(--blue);
            border:1px solid var(--line);
            padding:10px 12px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.2px;
            color:#142b4d;
        }

        .section-body{
            border:1px solid var(--line);
            border-top:none;
            padding:15px 16px;
            font-size:14px;
            line-height:1.65;
            text-align:justify;
            background: #fff;
        }

        .section-body textarea {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            resize: vertical;
            min-height: 60px;
            font-family: inherit;
            line-height: 1.6;
        }
        .section-body textarea:focus { background: #f8fbff; }

        .subsection{ margin-top:16px; }

        .subsection-title{
            font-size:15px;
            font-weight:800;
            color:var(--title);
            text-transform:uppercase;
            margin:0 0 10px;
        }

        .mini-title{
            font-size:14px;
            font-weight:800;
            color:#2d4a7f;
            margin:14px 0 8px;
            text-transform:uppercase;
        }

        .toc{
            border:1px solid var(--line);
            margin-bottom:20px;
            background: #fff;
        }

        .toc-title{
            background:var(--blue);
            padding:10px 12px;
            font-weight:900;
            text-transform:uppercase;
            border-bottom:1px solid var(--line);
        }

        .toc-body{
            padding:12px 16px;
            columns:2;
            column-gap:34px;
            font-size:14px;
            line-height:1.7;
        }

        .toc-body div{ break-inside:avoid; }
        .roles-table td:first-child{ width:26%; font-weight:700; background:#f8fbff; }

        .annex-list{
            columns:2;
            column-gap:30px;
            padding-left:18px;
            margin:0;
        }
        .annex-list li{ margin-bottom:8px; break-inside:avoid; }

        .footer-note{
            margin-top:26px;
            font-size:12px;
            color:#666;
            text-align:center;
        }

        @media print{
            body{ background:#fff; }
            .toolbar, .print-hide{ display:none !important; }
            .page-wrap{ padding:0; max-width: 100%; }
            .paper{ border:none; box-shadow:none; padding:0; }
            @page{ size:letter; margin:12mm; }
            textarea, input { background: transparent !important; }
            .toc-body { columns: 2; }
        }

        @media (max-width: 768px){
            .page-wrap{ padding:12px; }
            .paper{ padding:14px; }
            .doc-title{ font-size:21px; }
            .toc-body{ columns:1; }
            .annex-list{ columns:1; }
        }
    </style>
</head>
<body>

<div class="toolbar print-hide">
    <div style="display:flex; gap:8px;">
        <button class="btn-ui secondary" type="button" onclick="history.back()">← Atrás</button>
        <button class="btn-ui secondary" type="button" onclick="window.location.reload()">Recargar</button>
        <button class="btn-ui success" type="button" id="btnGuardar">Guardar Cambios</button>
        <button class="btn-ui" type="button" onclick="window.print()">Imprimir PDF</button>
    </div>
    <div class="text-end">
        <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">MANUAL SG-SST</span><br>
        <span style="font-size: 11px; color: #6b7280; font-weight: 700;">Usuario: <?= e($_SESSION["usuario"] ?? "Usuario") ?></span>
    </div>
</div>

<div class="page-wrap">
    <form id="form-sst-dinamico">
        <div class="paper">

            <table class="doc-header">
                <tr>
                    <td class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:1px solid #111; padding:0; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain; display: block; margin: auto;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </td>
                    <td class="header-main">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td class="header-side">
                        <div class="side-grid">
                            <input type="text" name="meta_version" value="0" title="Versión">
                            <input type="text" name="meta_codigo" value="MN-SST-02" title="Código">
                            <input type="date" name="meta_fecha" id="metaFecha" title="Fecha">
                        </div>
                    </td>
                </tr>
            </table>

            <div class="doc-title">MANUAL SG-SST</div>
            <div class="doc-subtitle">
                Versión <input type="text" name="doc_version_txt" value="0" style="width: 30px; border:none; text-align:center; font-weight:bold; color:inherit; background:transparent; font-size:14px;">
            </div>

            <div class="intro-block">
                <div class="empresa">
                    <input class="input-line text-center fw-bold" name="portada_empresa" type="text" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
                </div>
                <div class="fecha">
                    <input class="input-line text-center" name="portada_fecha" id="portadaFecha" type="text" placeholder="Mes de Año">
                </div>
            </div>

            <table class="table">
                <tr>
                    <th colspan="2">Control de documentos</th>
                </tr>
                <tr>
                    <td class="label">Elaborado por (Responsable SST)</td>
                    <td><input type="text" name="doc_elaboro" class="input-line" value="<?= htmlspecialchars($nombreSST) ?>"></td>
                </tr>
                <tr>
                    <td class="label">Aprobado por (Representante L.)</td>
                    <td><input type="text" name="doc_aprobo" class="input-line" value="<?= htmlspecialchars($nombreRL) ?>"></td>
                </tr>
                <tr>
                    <td class="label">Firma Elaborador</td>
                    <td class="signature-box">
                        <?php if(!empty($firmaSST)): ?>
                            <img src="<?= $firmaSST ?>" alt="Firma Elaborador" style="max-height: 50px; position:absolute; bottom:5px; left:10px;">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Firma Aprobador</td>
                    <td class="signature-box">
                        <?php if(!empty($firmaRL)): ?>
                            <img src="<?= $firmaRL ?>" alt="Firma Aprobador" style="max-height: 50px; position:absolute; bottom:5px; left:10px;">
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <table class="table">
                <tr>
                    <th colspan="3">Control de cambios</th>
                </tr>
                <tr>
                    <th style="width:15%;">Revisión</th>
                    <th style="width:25%;">Fecha</th>
                    <th style="width:60%;">Descripción del cambio</th>
                </tr>
                <tr>
                    <td><input type="text" name="cc_rev[]" class="input-line text-center" value="0"></td>
                    <td><input type="text" name="cc_fecha[]" class="input-line text-center" placeholder="Mes Año"></td>
                    <td><textarea name="cc_desc[]" class="input-line" rows="2">Creación del Sistema de Gestión de la Seguridad y Salud en el Trabajo</textarea></td>
                </tr>
            </table>

            <div class="toc print-hide">
                <div class="toc-title">Contenido</div>
                <div class="toc-body" style="color: #444;">
                    <div>Introducción</div>
                    <div>Definiciones y abreviaturas</div>
                    <div>Esquema SG-SST</div>
                    <div>1. Política</div>
                    <div>2. Organización</div>
                    <div>2.1 Información general de la empresa</div>
                    <div>2.1.1 Misión</div>
                    <div>2.1.2 Visión</div>
                    <div>2.1.3 Información sociodemográfica</div>
                    <div>2.1.4 Horarios de trabajo</div>
                    <div>2.2 Descripción de servicios/productos</div>
                    <div>2.2.1 Maquinaria, herramientas y equipos</div>
                    <div>2.3 Estructura organizacional</div>
                    <div>2.4 Roles y responsabilidades</div>
                    <div>2.5 Aspectos jurídicos y laborales</div>
                    <div>2.6 Definición de recursos</div>
                    <div>2.7 Comunicación</div>
                    <div>2.8 Competencia laboral en SST</div>
                    <div>2.9 Documentación y control de documentos</div>
                    <div>3. Planificación</div>
                    <div>3.1 Objetivos y metas</div>
                    <div>3.2 Requisitos legales</div>
                    <div>3.3 Identificación de peligros y valoración de riesgos</div>
                    <div>3.4 Programas de gestión</div>
                    <div>4. Aplicación</div>
                    <div>4.1 Gestión del cambio</div>
                    <div>4.2 Emergencias</div>
                    <div>4.3 Control de proveedores y subcontratistas</div>
                    <div>5. Verificación</div>
                    <div>6. Auditoría</div>
                    <div>7. Mejoramiento</div>
                    <div>8. Control de cambios</div>
                    <div>Lista de anexos</div>
                </div>
            </div>

            <section class="section">
                <div class="section-title">Introducción</div>
                <div class="section-body">
                    <textarea name="txt_introduccion" rows="8">LA EMPRESA, en cumplimiento de la Ley 1562 de 2012, Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normatividad vigente, ha estructurado el Sistema de Gestión de la Seguridad y Salud en el Trabajo, con el propósito de organizar la acción conjunta entre empleadores y trabajadores para la aplicación de medidas de Seguridad y Salud en el Trabajo a través del mejoramiento continuo de las condiciones y el medio ambiente laboral y el control eficaz de los peligros y riesgos en el lugar de trabajo.

Para su efecto, LA EMPRESA aborda la prevención de lesiones y enfermedades laborales, la promoción y protección de la salud de los trabajadores, a través de un método lógico por etapas basado en el ciclo PHVA: Planificar, Hacer, Verificar y Actuar.

El desarrollo articulado de política, organización, planificación, aplicación, evaluación, auditoría y acciones de mejora permite cumplir los propósitos del SG-SST y adaptarlo al tamaño y características de la empresa.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Definiciones y abreviaturas</div>
                <div class="section-body">
                    <textarea name="txt_definiciones" rows="10">- Seguridad y salud en el trabajo: disciplina orientada a prevenir lesiones y enfermedades laborales y a promover la salud de los trabajadores.
- Accidente de trabajo: suceso repentino que sobreviene por causa o con ocasión del trabajo y produce lesión, invalidez o muerte.
- Enfermedad laboral: resultado de la exposición a factores de riesgo inherentes a la actividad laboral o al medio donde el trabajador desarrolla sus funciones.
- Identificación del peligro: proceso para reconocer si existe un peligro y definir sus características.
- Riesgo: combinación de la probabilidad de ocurrencia de un evento peligroso y la severidad de la lesión o enfermedad que puede causar.
- Valoración de los riesgos: proceso de evaluar el riesgo teniendo en cuenta la suficiencia de los controles existentes.
- SG-SST: Sistema de Gestión de la Seguridad y Salud en el Trabajo.
- SST: Seguridad y Salud en el Trabajo.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Esquema SG-SST</div>
                <div class="section-body text-center">
                    <p class="mb-3" style="font-weight: 800; font-size: 15px; color: #213b67;">Política → Organización → Planificación → Aplicación → Verificación → Auditoría → Mejoramiento</p>
                    <textarea name="txt_esquema" class="text-center" rows="2">Este es el esquema general definido en el manual para el desarrollo del sistema y la mejora continua institucional.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">1. Política</div>
                <div class="section-body">
                    <textarea name="txt_politica" rows="3">La alta dirección, con la participación del COPASST o Vigía, ha definido una política de SST que es comunicada y divulgada a través de inducción, reinducción, formación, capacitación y material publicitario. Además, se publicará en las instalaciones administrativas y será revisada periódicamente en las reuniones de revisión por la dirección.

Ver anexo: PO-SST-01 Política de Seguridad y Salud en el Trabajo.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">2. Organización</div>
                <div class="section-body">

                    <div class="subsection mt-0">
                        <div class="subsection-title">2.1 Información general de la empresa</div>
                        <table class="table mb-4">
                            <tr>
                                <td class="label">Razón social</td>
                                <td><input class="input-line" type="text" name="info_razon" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>"></td>
                                <td class="label">NIT</td>
                                <td><input class="input-line" type="text" name="info_nit"></td>
                            </tr>
                            <tr>
                                <td class="label">Dirección</td>
                                <td><input class="input-line" type="text" name="info_dir"></td>
                                <td class="label">Teléfono</td>
                                <td><input class="input-line" type="text" name="info_tel"></td>
                            </tr>
                            <tr>
                                <td class="label">Representante legal</td>
                                <td><input class="input-line" type="text" name="info_rl" value="<?= htmlspecialchars($nombreRL) ?>"></td>
                                <td class="label">ARL</td>
                                <td><input class="input-line" type="text" name="info_arl"></td>
                            </tr>
                            <tr>
                                <td class="label">Actividad económica</td>
                                <td><input class="input-line" type="text" name="info_act"></td>
                                <td class="label">Clase y grado de riesgo</td>
                                <td><input class="input-line" type="text" name="info_riesgo"></td>
                            </tr>
                            <tr>
                                <td class="label">Centro(s) de trabajo</td>
                                <td colspan="3"><input class="input-line" type="text" name="info_centro" value="Sede Principal"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.1.1 Misión</div>
                        <textarea name="txt_mision" class="input-line border rounded p-2 mb-3 bg-light" rows="3" placeholder="Escriba la misión de la empresa"></textarea>

                        <div class="subsection-title">2.1.2 Visión</div>
                        <textarea name="txt_vision" class="input-line border rounded p-2 mb-3 bg-light" rows="3" placeholder="Escriba la visión de la empresa"></textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.1.3 Información sociodemográfica de la población trabajadora</div>
                        <table class="table">
                            <tr>
                                <th>Colaboradores</th>
                                <th>Hombres</th>
                                <th>Mujeres</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <td>Administración</td>
                                <td><input class="input-line text-center calc-val" type="number" name="soc_admin_h"></td>
                                <td><input class="input-line text-center calc-val" type="number" name="soc_admin_m"></td>
                                <td><input class="input-line text-center fw-bold" type="number" name="soc_admin_t" readonly></td>
                            </tr>
                            <tr>
                                <td>Operaciones</td>
                                <td><input class="input-line text-center calc-val" type="number" name="soc_oper_h"></td>
                                <td><input class="input-line text-center calc-val" type="number" name="soc_oper_m"></td>
                                <td><input class="input-line text-center fw-bold" type="number" name="soc_oper_t" readonly></td>
                            </tr>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td><input class="input-line text-center fw-bold" type="number" name="soc_tot_h" readonly></td>
                                <td><input class="input-line text-center fw-bold" type="number" name="soc_tot_m" readonly></td>
                                <td><input class="input-line text-center fw-bold text-primary" type="number" name="soc_tot_t" readonly></td>
                            </tr>
                        </table>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.1.4 Horarios de trabajo</div>
                        <textarea name="txt_horarios" rows="4">Lunes a Viernes: 8:00 am a 12:00 m y 2:00 pm a 6:00 pm.
Sábados: 8:00 am a 12:00 pm.

Nota: Los horarios pueden variar esporádicamente de acuerdo con la carga laboral existente y la naturaleza del cargo.</textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.2 Descripción de servicios/productos</div>
                        <textarea name="txt_servicios" class="input-line border rounded p-2 mb-3 bg-light" rows="3" placeholder="Describa los servicios o productos de la empresa..."></textarea>

                        <div class="subsection-title">2.2.1 Maquinaria, herramientas y equipos</div>
                        <textarea name="txt_maquinaria" class="input-line border rounded p-2 mb-3 bg-light" rows="3" placeholder="Describa maquinaria, herramientas y equipos que se utilizan..."></textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.3 Estructura organizacional</div>
                        <textarea name="txt_estructura" class="input-line border rounded p-2 mb-3 bg-light" rows="3" placeholder="Describa la estructura o haga referencia al anexo del organigrama institucional..."></textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.4 Roles y responsabilidades</div>
                        <textarea name="txt_roles" rows="12">1. Gerente y/o representante legal: Suministrar recursos, asignar responsabilidades, garantizar participación, supervisión, capacitación y evaluación anual del SG-SST.
2. Jefes de área: Participar en la actualización de peligros y riesgos, planes de acción, investigación de incidentes e inspecciones.
3. Responsable del SG-SST: Planificar, organizar, dirigir, desarrollar y aplicar el sistema, hacer seguimiento e informar a la alta dirección.
4. Trabajadores: Cuidar su salud, cumplir normas de seguridad, participar en la prevención y reportar condiciones o incidentes.
5. COPASST / Vigía: Proponer actividades, analizar causas, visitar instalaciones, recibir sugerencias y apoyar la coordinación entre directivas y trabajadores.
6. Comité de convivencia laboral: Recibir quejas, escuchar a las partes, promover diálogo, hacer seguimiento y emitir recomendaciones.</textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.5 Aspectos jurídicos y laborales</div>
                        <textarea name="txt_juridicos" rows="4">- Reglamento Interno de Trabajo.
- Reglamento de Higiene y Seguridad Industrial.
- Comité Paritario de Seguridad y Salud en el Trabajo (o Vigía).
- Comité de convivencia laboral.</textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.6 Definición de recursos</div>
                        <textarea name="txt_recursos" rows="2">La empresa define y asigna recursos físicos, financieros y humanos para el diseño, desarrollo, supervisión y evaluación de las medidas de prevención y control, así como para el cumplimiento de las funciones del COPASST/Vigía y responsables de SST.</textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.7 Comunicación</div>
                        <textarea name="txt_comunicacion" rows="3">La empresa establece mecanismos de comunicación, participación y consulta con empleados y partes interesadas externas sobre aspectos relevantes del SG-SST, incluyendo correos, teléfonos, comunicaciones físicas, inducción, capacitación y representación a través del COPASST/Vigía.</textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.8 Competencia laboral en SST</div>
                        <textarea name="txt_competencia" rows="10">2.8.1 Inducción en SST
- Aspectos generales y legales en SST
- Política de SST
- Política de no alcohol, drogas ni tabaquismo
- Reglamento de higiene y seguridad industrial
- Plan de emergencia y peligros asociados a la labor
- Procedimientos seguros y reporte de AT/EL

2.8.2 Programa de capacitación y entrenamiento
La empresa implementa un programa de capacitación y entrenamiento en SST revisado periódicamente con participación del COPASST/Vigía para analizar cumplimiento, cobertura y eficacia.</textarea>
                    </div>

                    <div class="subsection">
                        <div class="subsection-title">2.9 Documentación y control de documentos</div>
                        <textarea name="txt_documentacion" rows="2">La empresa cuenta con un procedimiento (PR-SST-01) para el control, administración y conservación de documentos y registros del sistema, asegurando que estén disponibles, legibles e identificables.</textarea>
                    </div>
                </div>
            </section>

            <section class="section">
                <div class="section-title">3. Planificación</div>
                <div class="section-body">
                    <textarea name="txt_planificacion" rows="10">3.1 Objetivos y metas
Se establece un plan de trabajo con objetivos, metas e indicadores para hacer seguimiento al cumplimiento del sistema y definir acciones de mejora cuando sea necesario.

3.2 Requisitos legales
La empresa define un procedimiento para identificar requisitos legales y de otra índole aplicables, manteniendo actualizada la matriz de requisitos legales.

3.3 Identificación de peligros y valoración de riesgos
La empresa cuenta con una matriz documentada para la identificación continua de peligros, evaluación y control de riesgos con base en la jerarquía de controles: eliminación, sustitución, ingeniería, administrativos y EPP.

3.4 Programas de gestión
Incluye medicina preventiva, higiene y seguridad industrial, programa de EPP, orden y aseo, inspecciones y plan de trabajo anual.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">4. Aplicación</div>
                <div class="section-body">
                    <textarea name="txt_aplicacion" rows="9">4.1 Gestión del cambio
La empresa evaluará el impacto sobre la seguridad y salud que puedan generar cambios internos o externos (infraestructura, equipos, personal), adoptando medidas de prevención y control antes de su implementación.

4.2 Prevención, preparación y respuesta ante emergencias
Se contemplan análisis de amenazas y vulnerabilidad, PON, recursos, conformación de brigadas (o personal entrenado), inspección de equipos de emergencia y simulacros.

4.3 Control de proveedores y subcontratistas
Se verifica afiliación al sistema de seguridad social integral, se comunica información de riesgos y emergencias, y se hace seguimiento al cumplimiento normativo de proveedores y contratistas acorde a la ley.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">5. Verificación</div>
                <div class="section-body">
                    <textarea name="txt_verificacion" rows="9">5.1 Supervisión y medición de los resultados
La empresa supervisa, mide y recopila información sobre el desempeño del SG-SST mediante indicadores de estructura, proceso y resultado (accidentalidad, enfermedad laboral, ausentismo).
- Supervisión proactiva: Inspecciones, evaluación de controles, vigilancia de ambientes de trabajo y salud.
- Supervisión reactiva: Identificación, notificación e investigación de incidentes, accidentes y enfermedades.

5.2 Investigación de incidentes, AT y EL
Busca identificar deficiencias en el sistema, identificar las causas básicas e inmediatas, comunicar conclusiones e implementar planes de acción para alimentar los procesos de mejora continua.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">6. Auditoría</div>
                <div class="section-body">
                    <textarea name="txt_auditoria" rows="6">6.1 Auditorías internas
La empresa cuenta con un programa de auditoría interna anual, enfocado en evaluar la idoneidad, conveniencia y eficacia del SG-SST, incluyendo fortalezas, no conformidades y oportunidades de mejora.

6.2 Revisión por la dirección
La alta dirección evalúa el SG-SST como mínimo una vez al año, revisando cumplimiento del plan, eficacia de estrategias, necesidad de cambios, suficiencia de recursos y nuevas prioridades estratégicas.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">7. Mejoramiento</div>
                <div class="section-body">
                    <textarea name="txt_mejoramiento" rows="8">7.1 Mejora continua
La empresa garantiza recursos y disposiciones para perfeccionar el SG-SST, tomando como fuentes los cambios normativos, resultados de auditorías, investigaciones, identificación de peligros y recomendaciones del COPASST/Vigía.

7.2 Acciones preventivas y correctivas
Se documentan acciones para identificar causas fundamentales de no conformidades detectadas, planificar medidas y verificar su eficacia en el tiempo.

7.3 Disposiciones finales
La gerencia está comprometida con el cumplimiento del SG-SST y con las exigencias del Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normatividad aplicable.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Lista de anexos</div>
                <div class="section-body">
                    <textarea name="txt_anexos" rows="16">- Manual de Control de Documentos
- Política de seguridad y salud en el trabajo
- Programa de inducción, capacitación y entrenamiento
- Programa de EPP y Dotación
- Programa de orden, aseo y limpieza
- Programa de inspecciones
- Perfiles de cargo y profesiograma
- Plan de trabajo anual y cronograma de capacitaciones
- Plan de emergencias y análisis de vulnerabilidad
- Reglamento interno de trabajo y de higiene
- Listado maestro de documentos y registros
- Matriz de requisitos legales y Matriz IPVR (Peligros)
- Registro de indicadores y ausentismo
- Procedimientos de gestión de cambio, proveedores y auditorías
- Conformación de COPASST / Vigía, COCOLA y Brigadas
- Formatos de investigación y reportes de AT/EL</textarea>
                </div>
            </section>

            <div class="footer-note print-hide">
                Documento maestro del Sistema de Gestión de la Seguridad y Salud en el Trabajo. Haz clic en "Guardar Cambios" para almacenar.
            </div>

        </div>
    </form>
</div>

<script>
    // Poner fecha de hoy por defecto en la cabecera si está vacía
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        
        const fmeta = document.getElementById("metaFecha");
        if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;

        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        const pFecha = document.getElementById("portadaFecha");
        if(pFecha && !pFecha.value) pFecha.value = `${meses[d.getMonth()]} de ${y}`;
    }
    setHoy();

    // Lógica para recalcular la tabla sociodemográfica
    function calcularSociodemografica() {
        let ah = parseInt(document.querySelector('[name="soc_admin_h"]').value) || 0;
        let am = parseInt(document.querySelector('[name="soc_admin_m"]').value) || 0;
        let oh = parseInt(document.querySelector('[name="soc_oper_h"]').value) || 0;
        let om = parseInt(document.querySelector('[name="soc_oper_m"]').value) || 0;

        document.querySelector('[name="soc_admin_t"]').value = ah + am;
        document.querySelector('[name="soc_oper_t"]').value = oh + om;
        
        let toth = ah + oh;
        let totm = am + om;
        
        document.querySelector('[name="soc_tot_h"]').value = toth;
        document.querySelector('[name="soc_tot_m"]').value = totm;
        document.querySelector('[name="soc_tot_t"]').value = toth + totm;
    }

    document.querySelectorAll('.calc-val').forEach(input => {
        input.addEventListener('input', calcularSociodemografica);
    });

    // Auto-ajustar altura de textareas
    function autoResizeTextareas() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(ta => {
            ta.style.height = 'auto';
            ta.style.height = (ta.scrollHeight) + 'px';
            ta.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    }
    setTimeout(autoResizeTextareas, 100);

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
                    let campo = document.querySelector(`[name="${key}"]`);
                    if (campo) {
                        campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                    }
                }
            }
            calcularSociodemografica();
            setTimeout(autoResizeTextareas, 200);
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
                    text: 'Manual guardado correctamente',
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