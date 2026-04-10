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
// Ajusta el ID de este ítem según tu base de datos (Ej: 31 para "Requisitos Legales")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 31; 

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
    <title>2.7.1 - Procedimiento de Identificación, Evaluación y Seguimiento a Requisitos Legales</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --blue:#1f5fa8;
            --blue-soft:#eaf2ff;
            --line:#111;
            --gray:#666;
            --light:#f8fafc;
        }

        body{
            background:#f3f6fb;
            font-family: Arial, Helvetica, sans-serif;
            color:#111;
            margin:0;
        }

        .page-wrap{
            max-width: 1100px;
            margin: 16px auto 60px;
            padding: 0 12px;
        }

        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            margin-bottom:16px;
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
            border:1px solid #d9e2ef;
            box-shadow:0 8px 24px rgba(15, 23, 42, .08);
            border-radius: 14px;
            overflow:hidden;
            margin-bottom: 20px;
        }

        .sheet-header{
            padding: 18px 20px 10px;
            border-bottom: 2px solid var(--blue);
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }

        .top-table{
            width:100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .top-table td,
        .top-table th{
            border:1px solid var(--line);
            padding:8px;
            vertical-align: middle;
            font-size: 13px;
        }

        .logo-box{
            height:90px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            color:#666;
            background:#fafafa;
            padding: 5px;
        }

        .title-main{
            font-size:18px;
            font-weight:700;
            text-align:center;
            text-transform:uppercase;
            line-height:1.35;
            margin:0;
        }

        .code-box{
            font-weight:700;
            text-align:center;
            font-size:14px;
        }

        .meta-input,
        .line-input,
        .inline-input,
        textarea.form-control,
        select.form-select{
            border:1px solid #cfd8e3;
            border-radius:8px;
            font-size:14px;
        }

        .meta-input,
        .line-input,
        .inline-input{
            width:100%;
            padding:8px 10px;
            outline:none;
            background:#fff;
        }

        .meta-input:focus,
        .line-input:focus,
        .inline-input:focus,
        textarea.form-control:focus,
        select.form-select:focus{
            border-color:#6ea8fe;
            box-shadow:0 0 0 .15rem rgba(13,110,253,.12);
        }

        .doc-body{
            padding: 22px 20px 28px;
        }

        .doc-cover{
            border:1px solid #dbe5f0;
            border-radius:14px;
            padding:24px 18px;
            margin-bottom:22px;
            background:#fcfdff;
        }

        .cover-logo{
            width:180px;
            height:120px;
            border:1px dashed #aab7c7;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:0 auto 18px;
            color:#667085;
            font-weight:700;
            background:#fff;
            padding: 10px;
        }

        .cover-title{
            text-align:center;
            font-weight:700;
            font-size:20px;
            text-transform:uppercase;
            line-height:1.4;
            margin-bottom:18px;
        }

        .cover-grid{
            max-width:700px;
            margin:0 auto;
        }

        .section-card{
            border:1px solid #d9e2ef;
            border-radius:14px;
            margin-bottom:18px;
            overflow:hidden;
            background:#fff;
        }

        .section-title{
            background:var(--blue);
            color:#fff;
            font-weight:700;
            padding:10px 14px;
            text-transform:uppercase;
            letter-spacing:.3px;
            font-size:14px;
        }

        .section-body{
            padding:14px;
        }

        .section-body p{
            margin-bottom:10px;
            font-size:14px;
            line-height:1.6;
            text-align:justify;
        }

        .table-clean{
            width:100%;
            border-collapse: collapse;
        }

        .table-clean th,
        .table-clean td{
            border:1px solid #111;
            padding:10px;
            vertical-align: top;
            font-size:14px;
        }

        .table-clean th{
            background:#eef4ff;
            text-align:center;
        }

        .glossary-list{
            display:grid;
            grid-template-columns: 1fr;
            gap:10px;
        }

        .glossary-item{
            border:1px solid #e2e8f0;
            border-radius:12px;
            padding:12px;
            background:#fbfdff;
        }

        .glossary-item strong{
            color:var(--blue);
            display:block;
            margin-bottom:4px;
        }

        .step-block{
            border:1px solid #dbe4ef;
            border-radius:12px;
            padding:14px;
            margin-bottom:14px;
            background:#fff;
        }

        .step-block h6{
            font-weight:700;
            color:var(--blue);
            margin-bottom:10px;
            text-transform:uppercase;
            font-size:14px;
        }

        .sub-list{
            margin:0;
            padding-left:18px;
        }

        .sub-list li{
            margin-bottom:6px;
            font-size:14px;
            line-height:1.5;
        }

        .convenciones{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap:10px;
        }

        .tag-box{
            border-radius:10px;
            padding:10px 12px;
            font-weight:700;
            font-size:13px;
            text-transform:uppercase;
            border:1px solid #dbe5f0;
            background:#f8fbff;
            text-align:center;
        }

        .registro-box{
            border:1px dashed #9db1c7;
            border-radius:12px;
            padding:14px;
            background:#f9fcff;
            font-size:14px;
            line-height:1.6;
        }

        .muted-label{
            font-size:12px;
            color:#667085;
            margin-bottom:4px;
            display:block;
            font-weight:600;
        }

        textarea{
            resize:vertical;
            min-height:90px;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:18px;
            margin-top:24px;
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

        @media print{
            body{
                background:#fff;
            }
            .page-wrap{
                max-width:100%;
                margin:0;
                padding:0;
            }
            .toolbar, .print-hide{ display:none !important; }
            .sheet{
                border:none;
                box-shadow:none;
                border-radius:0;
            }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    
    <div class="toolbar print-hide">
        <div style="display:flex; gap:8px;">
            <button class="btn-action" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-action" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-success-action" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-primary-action" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
        <div class="tiny text-end">
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">PROCEDIMIENTO REQ. LEGALES</span><br>
            Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet">
            <div class="sheet-header">
                <table class="top-table">
                    <tr>
                        <td rowspan="3" style="width:18%;">
                            <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                                <?php if(!empty($logoEmpresaUrl)): ?>
                                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                                <?php else: ?>
                                    LOGO EMPRESA
                                <?php endif; ?>
                            </div>
                        </td>
                        <td rowspan="3" style="width:52%;">
                            <h1 class="title-main">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</h1>
                        </td>
                        <td style="width:15%; font-weight:700;">Versión</td>
                        <td style="width:15%;">
                            <input type="text" name="meta_version" class="meta-input" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Código</td>
                        <td>
                            <input type="text" name="meta_codigo" class="meta-input" value="AN-XX-SST-20">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Fecha</td>
                        <td>
                            <input type="date" name="meta_fecha" id="metaFecha1" class="meta-input">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="code-box">
                            PROCEDIMIENTO DE IDENTIFICACIÓN, EVALUACIÓN Y SEGUIMIENTO A REQUISITOS LEGALES
                        </td>
                    </tr>
                </table>
            </div>

            <div class="doc-body">
                <div class="doc-cover">
                    <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </div>

                    <div class="cover-title">
                        PROCEDIMIENTO DE IDENTIFICACIÓN, EVALUACIÓN Y SEGUIMIENTO A REQUISITOS LEGALES
                    </div>

                    <div class="cover-grid row g-3">
                        <div class="col-md-6">
                            <label class="muted-label">Versión</label>
                            <input type="text" name="cover_version" class="line-input" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="muted-label">Fecha</label>
                            <input type="date" name="cover_fecha" id="metaFecha2" class="line-input">
                        </div>
                        <div class="col-12">
                            <label class="muted-label">Nombre de la empresa</label>
                            <input type="text" name="cover_empresa" class="line-input" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>" placeholder="NOMBRE DE LA EMPRESA">
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Objetivo</div>
                    <div class="section-body">
                        <textarea name="txt_objetivo" class="form-control" rows="4">Identificar, tener acceso a los requisitos legales y otros que en materia de Seguridad y Salud en el Trabajo (SST), verificando el cumplimiento de aquellos que aplican a las actividades y servicios desarrollados por la compañía. Así mismo, fijar los lineamientos para mantener actualizada la información y coordinar las comunicaciones relacionadas, con el fin de asegurar el cumplimiento de los requisitos legales y de otra índole en Seguridad y Salud en el Trabajo.</textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Alcance</div>
                    <div class="section-body">
                        <textarea name="txt_alcance" class="form-control" rows="3">El alcance de este documento aplica a la identificación, actualización, verificación y comunicación de los requisitos legales y otros aplicables en Seguridad y Salud en el Trabajo.</textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Responsabilidades</div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th style="width:30%;">Responsable</th>
                                        <th>Responsabilidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="resp_cargo[]" class="line-input" value="Encargado del SG-SST">
                                        </td>
                                        <td>
                                            <textarea name="resp_desc[]" class="form-control" rows="4">Identificar, evaluar cumplimiento y mantener actualizados los requisitos legales aplicables en materia de SST.
Alimentar la matriz de requisitos legales y otros.
Realizar la evaluación del cumplimiento legal.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="resp_cargo[]" class="line-input" value="Encargado del SG-SST, Asesor Externo">
                                        </td>
                                        <td>
                                            <textarea name="resp_desc[]" class="form-control" rows="2">Evaluar el cumplimiento legal.</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Glosario</div>
                    <div class="section-body">
                        <div class="glossary-list">
                            <div class="glossary-item">
                                <strong>Artículo</strong>
                                <textarea name="glos_articulo" class="form-control" rows="2">Cada una de las partes más o menos independientes en que se divide un escrito jurídico, como una ley o reglamento.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Circular</strong>
                                <textarea name="glos_circular" class="form-control" rows="2">Escrito dirigido a varias personas para comunicar algo.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Código</strong>
                                <textarea name="glos_codigo" class="form-control" rows="2">Colección sistemática de leyes.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Decreto</strong>
                                <textarea name="glos_decreto" class="form-control" rows="2">Acto administrativo expedido por funcionarios en ejercicio de funciones administrativas. Por lo general son expedidos por el Presidente, Gobernadores y Alcaldes, entre otros.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Decreto - Ley</strong>
                                <textarea name="glos_decreto_ley" class="form-control" rows="2">Acto expedido por el Presidente de la República que tiene la misma fuerza que una ley, pero que por mandato de la Constitución en algunos casos particulares, se asimilan a leyes expedidas por el Congreso.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>EPS</strong>
                                <textarea name="glos_eps" class="form-control" rows="2">Entidad Promotora de Salud.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>ICONTEC</strong>
                                <textarea name="glos_icontec" class="form-control" rows="2">Instituto colombiano de normas técnicas.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Jurisprudencia</strong>
                                <textarea name="glos_jurisprudencia" class="form-control" rows="2">Decisiones de carácter general y definitivo tomadas por los órganos jurisdiccionales del país.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Legislación</strong>
                                <textarea name="glos_legislacion" class="form-control" rows="2">Conjunto de leyes por la que se rige una materia.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Ley</strong>
                                <textarea name="glos_ley" class="form-control" rows="2">Regla, norma, disposición emanada del poder legislativo.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Normatividad</strong>
                                <textarea name="glos_normatividad" class="form-control" rows="2">Es el marco regulatorio nacional que existe en el ordenamiento jurídico y que regula los distintos comportamientos y acciones de toda persona natural o jurídica.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Otros compromisos</strong>
                                <textarea name="glos_otros" class="form-control" rows="2">Requisitos adicionales a las obligaciones legales.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Requisito legal</strong>
                                <textarea name="glos_req_legal" class="form-control" rows="2">Condición(es) que establece la ley para el ejercicio del(los) derecho(s) de la organización.</textarea>
                            </div>
                            <div class="glossary-item">
                                <strong>Resolución</strong>
                                <textarea name="glos_resolucion" class="form-control" rows="2">Acto administrativo por el cual las diferentes entidades de la Administración Pública adoptan decisiones en el ejercicio de sus funciones.</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Procedimiento</div>
                    <div class="section-body">

                        <div class="step-block">
                            <h6>1. Identificación de los requisitos legales</h6>
                            <textarea name="paso1_desc" class="form-control mb-3" rows="3">El encargado del SG-SST identifica los requisitos legales y de otra índole aplicables en SST.</textarea>

                            <label class="muted-label">Fuentes de información para actualización y consulta</label>
                            <div class="table-responsive">
                                <table class="table-clean">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th>Entidad / Fuente</th>
                                            <th>Enlace / Referencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td><input type="text" name="fuente_entidad[]" class="line-input" value="Ministerio del Trabajo"></td>
                                            <td><input type="text" name="fuente_enlace[]" class="line-input" value="www.mintrabajo.com.co"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td><input type="text" name="fuente_entidad[]" class="line-input" value="Ministerio de Transporte"></td>
                                            <td><input type="text" name="fuente_enlace[]" class="line-input" value="www.mintransporte.gov.co"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td><input type="text" name="fuente_entidad[]" class="line-input" value="ICONTEC"></td>
                                            <td><input type="text" name="fuente_enlace[]" class="line-input" value="www.icontec.org"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td><input type="text" name="fuente_entidad[]" class="line-input" value="Consejo Colombiano de Seguridad"></td>
                                            <td><input type="text" name="fuente_enlace[]" class="line-input" value="www.laseguridad.ws"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td><input type="text" name="fuente_entidad[]" class="line-input" value="Legis"></td>
                                            <td><input type="text" name="fuente_enlace[]" class="line-input" value="www.legis.com"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">6</td>
                                            <td><input type="text" name="fuente_entidad[]" class="line-input" value=""></td>
                                            <td><input type="text" name="fuente_enlace[]" class="line-input" value=""></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <label class="muted-label">Observación</label>
                                <textarea name="paso1_obs" class="form-control" rows="3">Los requisitos de SST y de otra índole que apliquen se registran en la matriz de identificación de requisitos legales.</textarea>
                            </div>
                        </div>

                        <div class="step-block">
                            <h6>2. Actualización</h6>
                            <textarea name="paso2_desc" class="form-control" rows="5">El encargado del SG-SST consulta periódicamente en las fuentes descritas anteriormente la información actualizada sobre las normas jurídicas en SST, aplicables a las actividades de la organización. La actualización se realizará por lo menos cada 6 meses o si se conoce antes un requisito a tener en cuenta.

Se deben consultar cuando se tengan cambios en la matriz de identificación de peligros y valoración de riesgos, entre otros, y se hace una revisión de los requisitos legales aplicables.

Cuando se hayan presentado actualizaciones en la identificación de peligros que lleven al cumplimiento de nuevos requisitos legales.</textarea>

                            <div class="mt-3">
                                <label class="muted-label">Convenciones</label>
                                <div class="convenciones">
                                    <div class="tag-box">Normativa adicionada</div>
                                    <div class="tag-box">Normativa derogada</div>
                                    <div class="tag-box">Normativa modificada</div>
                                    <div class="tag-box">Normativa compilada</div>
                                </div>
                            </div>
                        </div>

                        <div class="step-block">
                            <h6>3. Análisis de la información y aplicabilidad en la empresa</h6>
                            <textarea name="paso3_desc" class="form-control" rows="3">El encargado del SG-SST analiza si el requisito que se identifica aplica a la compañía y genera las acciones para dar cumplimiento.</textarea>
                        </div>

                        <div class="step-block">
                            <h6>4. Verificación del cumplimiento de los requisitos</h6>
                            <textarea name="paso4_desc" class="form-control" rows="4">El encargado del SG-SST con el apoyo del asesor jurídico de la organización y/o externo verificará el cumplimiento de los requisitos legales con frecuencia semestral, dejando evidencia en la matriz de identificación y evaluación de requisitos legales y otros en SST.

En caso de evidenciar desviaciones en el cumplimiento de los requisitos legales o de otra índole, se generarán las acciones necesarias para llegar a su cumplimiento y se informará al responsable de su implementación.</textarea>
                        </div>

                        <div class="step-block mb-0">
                            <h6>5. Comunicación de los requisitos legales</h6>
                            <textarea name="paso5_desc" class="form-control" rows="3">Una vez identificados los requisitos (nuevo requisito o falta de cumplimiento), el encargado del SG-SST divulga a las áreas involucradas las acciones para el cumplimiento del requisito a través de cualquiera de los siguientes medios: correo electrónico, reuniones, cartelera, capacitaciones y boletines.</textarea>
                        </div>

                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Registros</div>
                    <div class="section-body">
                        <div class="registro-box">
                            <textarea name="txt_registros" class="form-control" rows="2">Matriz de identificación y evaluación de requisitos legales de Seguridad y Salud en el Trabajo y otra índole.</textarea>
                        </div>
                    </div>
                </div>

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
    // Poner fecha de hoy por defecto si está vacía
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;

        const fmeta1 = document.getElementById("metaFecha1");
        if (fmeta1 && !fmeta1.value) fmeta1.value = `${y}-${m}-${dd}`;

        const fmeta2 = document.getElementById("metaFecha2");
        if (fmeta2 && !fmeta2.value) fmeta2.value = `${y}-${m}-${dd}`;
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
                    text: 'Procedimiento guardado correctamente',
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

</body>
</html>