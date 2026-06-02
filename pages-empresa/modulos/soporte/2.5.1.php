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
// Ajusta el ID de este ítem según tu base de datos (Ej: 27 para "2.5.1")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 27; 

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
    <title>2.5.1 - Manual de Control de Documentos y Cambios</title>
    <link rel="stylesheet" href="../../../assets/css/toolbar.css">
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

        .sst-toolbar{
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

        .cover .cover-title-input{
            font-size:28px;
            font-weight:900;
            text-transform:uppercase;
            line-height:1.25;
            max-width:720px;
            margin-bottom:24px;
            border:none;
            outline:none;
            background:transparent;
            text-align:center;
            width:100%;
            resize:none;
        }

        .cover .cover-logo{
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

        .cover .cover-text{
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
            line-height:1.6;
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
            max-width:100%;
        }

        textarea.edit{
            resize:none;
            overflow:hidden;
            min-height:36px;
            line-height:1.6;
            width:100%;
            text-align:justify;
            font-family:Arial, Helvetica, sans-serif;
        }

        .center{ text-align:center; }
        .small{ font-size:11px; }

        .list-box-edit{
            margin:0;
            padding:0;
            list-style:none;
        }

        .list-box-edit li{
            display:flex;
            align-items:flex-start;
            gap:8px;
            margin-bottom:8px;
        }

        .list-box-edit .bullet{
            font-weight:700;
            font-size:14px;
            line-height:1.6;
            user-select:none;
        }

        .flow-note{
            background:#f8fbff;
            border:1px solid #d7e2ef;
            padding:10px 12px;
            margin-bottom:10px;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:40px;
            margin-top:36px;
            padding: 0 10px;
        }

        .sign{
            border-top:1px solid #111;
            padding-top:8px;
            text-align:center;
            min-height:75px;
            font-size:12px;
            font-weight:700;
            position: relative;
        }

        .trans-input{
            border:none; outline:none; background:transparent; font-size:12px; font-family:Arial, sans-serif; width:100%; padding:0; margin:0; color:#111;
        }
        .trans-input.bold{ font-weight:900; }
        .trans-input.center{ text-align:center; }

        @media print{
            body{ background:#fff; }
            .sst-toolbar{ display:none !important; }
            .sheet{ box-shadow:none; margin-bottom:0; border:2px solid #000; }
            .trans-input, textarea.edit, .cover-title-input{ color:#000 !important; }
        }

        @media (max-width: 768px){
            .sign-grid{
                grid-template-columns:1fr;
                gap: 40px;
            }
            .cover .cover-title-input{
                font-size:22px;
            }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
<div class="wrap">

    <div class="sst-toolbar">
        <h1 class="sst-toolbar-title">MANUAL CONTROL DOCUMENTOS</h1>
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
        <div class="sheet page-break">
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
                    <td class="title">
                        <input type="text" name="meta_sistema_titulo_1" class="trans-input bold center" value="SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO" style="font-size:13px;">
                    </td>
                    <td><strong>Versión:</strong> <input type="text" name="meta_version_1" class="trans-input bold" value="0" style="width:30px; display:inline-block;"></td>
                    <td><strong>Fecha:</strong><br><input type="date" name="meta_fecha_1" id="metaFecha1" style="border:none; font-size:10px; font-weight:900; outline:none; background:transparent; width:100%;"></td>
                </tr>
                <tr>
                    <td class="subtitle">
                        <input type="text" name="meta_doc_titulo_1" class="trans-input bold center" value="MANUAL DE CONTROL DE DOCUMENTOS Y CAMBIOS" style="font-size:12px;">
                    </td>
                    <td class="title" colspan="2">
                        <input type="text" name="meta_codigo_1" class="trans-input bold center" value="MA-XX-SST-01">
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Proceso:</strong> <input type="text" name="meta_proceso_1" class="trans-input" value="Gestión de Seguridad y Salud en el Trabajo" style="width:75%; display:inline-block;"></td>
                </tr>
            </table>

            <div class="cover">
                <textarea name="cover_titulo_principal" class="cover-title-input" rows="2" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">MANUAL DE CONTROL DE DOCUMENTOS Y CONTROL DE CAMBIOS</textarea>
                <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 120px; object-fit: contain;">
                    <?php else: ?>
                        LOGO
                    <?php endif; ?>
                </div>
                <div class="cover-text">
                    <span>Versión </span><input type="text" name="cover_version_num" class="trans-input bold inline" value="0" style="width:40px;">
                </div>
                <div class="cover-text">
                    <input type="text" name="cover_empresa" class="edit-inline center bold" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>" placeholder="NOMBRE EMPRESA">
                </div>
                <div class="cover-text">
                    <input type="text" name="cover_fecha" id="coverFecha" class="edit-inline center" placeholder="FECHA ACTUAL">
                </div>
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
                    <td class="title">
                        <input type="text" name="meta_sistema_titulo_2" class="trans-input bold center" value="SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO" style="font-size:13px;">
                    </td>
                    <td><strong>Versión:</strong> <input type="text" name="meta_version_2" class="trans-input bold" value="0" style="width:30px; display:inline-block;"></td>
                    <td><strong>Fecha:</strong><br><input type="date" name="meta_fecha_2" id="metaFecha2" style="border:none; font-size:10px; font-weight:900; outline:none; background:transparent; width:100%;"></td>
                </tr>
                <tr>
                    <td class="subtitle">
                        <input type="text" name="meta_doc_titulo_2" class="trans-input bold center" value="MANUAL DE CONTROL DE DOCUMENTOS Y CAMBIOS" style="font-size:12px;">
                    </td>
                    <td class="title" colspan="2">
                        <input type="text" name="meta_codigo_2" class="trans-input bold center" value="MA-XX-SST-01">
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Proceso:</strong> <input type="text" name="meta_proceso_2" class="trans-input" value="Gestión de Seguridad y Salud en el Trabajo" style="width:75%; display:inline-block;"></td>
                </tr>
            </table>

            <div class="sec-h">
                <input type="text" name="seccion_h_1" class="trans-input bold" value="Introducción" style="color:#10233c; font-size:15px;">
            </div>
            <div class="box text-just">
                <textarea name="txt_intro" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">El presente manual sirve de guía en LA EMPRESA, para el manejo y control documental de todos sus procesos estratégicos, con el fin de tener un manejo más claro, ágil y eficiente. Además, como herramienta para desarrollar la codificación, numeración y el cumplimiento de normas ICONTEC.&#10;&#10;La cual implementada de forma adecuada evidenciara el éxito del objetivo planteado.</textarea>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_2" class="trans-input bold" value="Objetivo" style="color:#10233c; font-size:15px;">
            </div>
            <div class="box text-just">
                <textarea name="txt_objetivo" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Con este manual se pretende estandarizar los procesos LA EMPRESA, para dar agilidad a estos, de una manera entendible y fácil de utilizar.</textarea>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_3" class="trans-input bold" value="Campo de aplicación" style="color:#10233c; font-size:15px;">
            </div>
            <div class="box text-just">
                <textarea name="txt_campo" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Aplica a todos los colaboradores de LA EMPRESA, que generen documentos que representen la empresa o den apoyo al Sistema de Gestión de Seguridad y Salud en el trabajo, entre otros.</textarea>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_4" class="trans-input bold" value="Normas generales para la elaboración de la documentación y correspondencia" style="color:#10233c; font-size:15px;">
            </div>
            <div class="box text-just">
                <textarea name="txt_normas_intro" class="edit mb-2" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Para elaboración es importante tener en cuenta algunas técnicas sobre la forma de elaborar los documentos con el fin de normalizar su producción. El ICONTEC nos da herramientas que facilitan la gestión de la documentación.</textarea>
                <p style="margin-bottom:6px;"><strong>Márgenes:</strong> <input type="text" name="label_margenes" class="trans-input bold inline" value="Se deben conservar las siguientes márgenes en el documento:" style="width:80%;"></p>
                <table class="formtbl">
                    <tbody>
                        <tr><td style="width:180px;"><strong>Superior</strong></td><td><input name="margen_sup" class="trans-input" type="text" value="3 cm"></td></tr>
                        <tr><td><strong>Izquierdo</strong></td><td><input name="margen_izq" class="trans-input" type="text" value="4 cm"></td></tr>
                        <tr><td><strong>Derecho</strong></td><td><input name="margen_der" class="trans-input" type="text" value="2 cm"></td></tr>
                        <tr><td><strong>Inferior</strong></td><td><input name="margen_inf" class="trans-input" type="text" value="3 cm"></td></tr>
                    </tbody>
                </table>
                <textarea name="txt_margen_nota" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">En caso de ser impreso por ambas caras todas las márgenes deben ser de 3 cm. Los títulos de cada capítulo deben estar en hojas independientes a 3 cm del borde superior.</textarea>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_5" class="trans-input bold" value="Portada" style="color:#10233c; font-size:15px;">
            </div>
            <div class="box text-just">
                <textarea name="txt_portada_intro" class="edit mb-2" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">La portada de los documentos que se utiliza para el sistema de gestión de la seguridad y salud en el trabajo, manuales, programas, procedimientos e instructivos es la siguiente:</textarea>
                <ul class="list-box-edit">
                    <li><span class="bullet">•</span><textarea name="txt_portada_li_1" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Título del documento: se escribe centrado horizontalmente, en letra Arial 20, en negrilla y en mayúscula sostenida.</textarea></li>
                    <li><span class="bullet">•</span><textarea name="txt_portada_li_2" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Logotipo de la empresa: centrado en la parte media, de 4 cm de alto por 12 cm de ancho.</textarea></li>
                    <li><span class="bullet">•</span><textarea name="txt_portada_li_3" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Número de la versión: se escribe la palabra “Versión” seguida del número correspondiente.</textarea></li>
                    <li><span class="bullet">•</span><textarea name="txt_portada_li_4" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Nombre completo de la empresa centrado horizontalmente al final de la página en letra Arial 12, en negrilla y mayúscula sostenida.</textarea></li>
                    <li><span class="bullet">•</span><textarea name="txt_portada_li_5" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Fecha: se indica el mes y el año en que la versión del documento ha sido aprobado.</textarea></li>
                </ul>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_6" class="trans-input bold" value="Encabezado del documento" style="color:#10233c; font-size:15px;">
            </div>
            <div class="box text-just">
                <textarea name="txt_encabezado_intro" class="edit mb-2" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">En todas las hojas, a excepción de la portada, debe aparecer el cuadro que identifica el documento y contiene la siguiente información:</textarea>
                <ul class="list-box-edit">
                    <li><span class="bullet">1.</span><textarea name="txt_encabezado_li_1" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Escudo o logo de la empresa.</textarea></li>
                    <li><span class="bullet">2.</span><textarea name="txt_encabezado_li_2" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Nombre del sistema en mayúscula sostenida, negrita y con letra Arial 12.</textarea></li>
                    <li><span class="bullet">3.</span><textarea name="txt_encabezado_li_3" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Título del documento en letra Arial 12, sin negrilla y en mayúscula sostenida.</textarea></li>
                    <li><span class="bullet">4.</span><textarea name="txt_encabezado_li_4" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Versión del documento.</textarea></li>
                    <li><span class="bullet">5.</span><textarea name="txt_encabezado_li_5" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Código del documento.</textarea></li>
                    <li><span class="bullet">6.</span><textarea name="txt_encabezado_li_6" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Fecha de emisión y/o actualización del documento.</textarea></li>
                </ul>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_7" class="trans-input bold" value="Lineamientos de redacción" style="color:#10233c; font-size:15px;">
            </div>
            <table class="formtbl">
                <tbody>
                    <tr>
                        <td style="width:180px;"><strong>Redacción</strong></td>
                        <td><textarea name="lin_redaccion" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Se deben seguir las reglas ortográficas de la lengua española. La redacción debe ser en tercera persona; se debe justificar toda la documentación y correspondencia.</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Tipo de letra</strong></td>
                        <td><textarea name="lin_tipo_letra" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Se recomienda el uso de la fuente Arial con un tamaño 12 u 11, para el cuerpo del documento; los títulos y subtítulos irán en negrilla conservando el mismo tamaño de letra.</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Numeración</strong></td>
                        <td><textarea name="lin_numeracion" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">La numeración de las páginas debe hacerse de forma consecutiva con números arábigos a excepción de la cubierta y la portada, que no se enumeran.</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Contenido</strong></td>
                        <td><textarea name="lin_contenido" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">Permitir al lector encontrar una parte específica del documento de una forma rápida, utilizando siempre al inicio de cada documento o correspondencia el código de la dependencia.</textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">
                <input type="text" name="seccion_h_8" class="trans-input bold" value="Codificación de procesos" style="color:#10233c; font-size:15px;">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Nombres del nivel</th>
                        <th style="width:180px;">Código</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="cod_nivel_nombre" class="trans-input" type="text" value="SEGURIDAD Y SALUD EN EL TRABAJO"></td>
                        <td class="center"><input name="cod_nivel_codigo" class="trans-input center bold" type="text" value="SST"></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">
                <input type="text" name="seccion_h_9" class="trans-input bold" value="Tipo de documentación" style="color:#10233c; font-size:15px;">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Nombre del documento</th>
                        <th style="width:180px;">Tipo documento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><input type="text" name="tipo_doc_n_1" class="trans-input" value="Manuales"></td><td class="center"><input type="text" name="tipo_doc_c_1" class="trans-input center bold" value="MA"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_2" class="trans-input" value="Política"></td><td class="center"><input type="text" name="tipo_doc_c_2" class="trans-input center bold" value="PO"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_3" class="trans-input" value="Instructivos"></td><td class="center"><input type="text" name="tipo_doc_c_3" class="trans-input center bold" value="IN"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_4" class="trans-input" value="Programa"></td><td class="center"><input type="text" name="tipo_doc_c_4" class="trans-input center bold" value="PR"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_5" class="trans-input" value="Manual de funciones"></td><td class="center"><input type="text" name="tipo_doc_c_5" class="trans-input center bold" value="MF"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_6" class="trans-input" value="Planes"></td><td class="center"><input type="text" name="tipo_doc_c_6" class="trans-input center bold" value="PL"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_7" class="trans-input" value="Registros"></td><td class="center"><input type="text" name="tipo_doc_c_7" class="trans-input center bold" value="RE"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_8" class="trans-input" value="Anexos"></td><td class="center"><input type="text" name="tipo_doc_c_8" class="trans-input center bold" value="AN"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_9" class="trans-input" value="Actas"></td><td class="center"><input type="text" name="tipo_doc_c_9" class="trans-input center bold" value="AC"></td></tr>
                    <tr><td><input type="text" name="tipo_doc_n_10" class="trans-input" value="Informe"></td><td class="center"><input type="text" name="tipo_doc_c_10" class="trans-input center bold" value="IN"></td></tr>
                </tbody>
            </table>

            <div class="sec-h">
                <input type="text" name="seccion_h_10" class="trans-input bold" value="Codificación de la empresa" style="color:#10233c; font-size:15px;">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:180px;">Código</th>
                        <th>Iniciales de la empresa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center"><input name="cod_empresa_sigla" class="edit center bold" type="text" value="XX"></td>
                        <td><input name="cod_empresa_nombre" class="trans-input" type="text" value="INICIALES DE LA EMPRESA"></td>
                    </tr>
                </tbody>
            </table>

            <div class="box text-just">
                <p>Las tablas anteriores se van a codificar de la siguiente manera:</p>
                <p><strong>Tipo de Documento + (XX) Identificación de la empresa + Nombre de Proceso + Consecutivo</strong></p>
                <p><strong>Ejemplo:</strong> MA-XX-SST-01</p>
            </div>

            <div class="sec-h">
                <input type="text" name="seccion_h_11" class="trans-input bold" value="Ejemplo de codificación del proceso" style="color:#10233c; font-size:15px;">
            </div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:80px;">N°</th>
                        <th>Nombre del proceso</th>
                        <th style="width:220px;">Código</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td><input name="ej_proceso" class="trans-input" type="text" value="Manual de control de documentos"></td>
                        <td class="center"><input name="ej_codigo" class="trans-input center bold" type="text" value="MA-XX-SST-01"></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">
                <input type="text" name="seccion_h_12" class="trans-input bold" value="Procedimiento para control documental" style="color:#10233c; font-size:15px;">
            </div>
            <div class="flow-note"><strong>Nota:</strong> Diligencie la actividad y los responsables según los procedimientos de su empresa.</div>

            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:40px;">ID</th>
                        <th style="width:140px;">Entrada</th>
                        <th style="width:140px;">Actividad</th>
                        <th style="width:140px;">Salida</th>
                        <th>Descripción (Cómo)</th>
                        <th style="width:200px;">Responsable / Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $flujos_estaticos = [
                        ['id' => '1', 'en' => 'Inicio', 'ac' => 'Inicio', 'sa' => 'Inicio', 'desc' => '', 'resp' => ''],
                        ['id' => '2', 'en' => 'Información para elaboración del documento', 'ac' => 'Elaborar documento', 'sa' => 'Elaborar documento', 'desc' => 'El coordinador de SST se encarga de controlar la elaboración de documentos (manuales, procedimientos, instructivos, planes, registros) del Sistema de Gestión en Seguridad y Salud en el Trabajo. Obtiene la información por medio de observación directa y entrevistas con el personal que realiza las actividades que se documentarán.', 'resp' => "Responsable: Coordinador de SST\nParticipa: Persona(s) que utilizarán el documento"],
                        ['id' => '3', 'en' => '', 'ac' => '¿Hay cambios?', 'sa' => 'SI / NO', 'desc' => 'Si existen cambios en el documento, el coordinador de SST los incorpora, hasta conseguir con quienes participan de la elaboración el consenso acerca de los cambios y obtener el documento previo a ser aprobado.', 'resp' => "Responsable: Coordinador de SST\nParticipa: Persona(s) que utilizarán el documento"],
                        ['id' => '4', 'en' => '', 'ac' => 'Aprobar edición', 'sa' => 'Aprobar edición', 'desc' => 'El coordinador de SST revisa inmediatamente y envía al gerente para que apruebe la edición preliminar del documento y la presentación del medio impreso y el archivo en medio magnético.', 'resp' => "Responsable: Coordinador de SST\nParticipa: Gerente"],
                        ['id' => '5', 'en' => '', 'ac' => 'Codificar y actualizar revisión', 'sa' => 'Codificar y actualizar revisión', 'desc' => 'Si no existe una versión anterior al documento o es uno nuevo se identifica con la Revisión 0, y se codifica según el anexo 1. Si existe una revisión anterior, se sigue el número consecutivo correspondiente.', 'resp' => "Responsable: Coordinador de SST"],
                        ['id' => '6', 'en' => '', 'ac' => 'Editar', 'sa' => 'Editar', 'desc' => 'El Coordinador de SST realiza la presentación definitiva del documento, con las firmas de quien elabora, revisa y aprueba en la primera hoja del documento. Al entrar en vigencia una nueva revisión, debe retirar el documento anterior y convertirlo en documento obsoleto. Las fechas de cada documento serán registradas en la primera hoja de cada documento.', 'resp' => "Responsable: Coordinador de SST"],
                        ['id' => '7', 'en' => '', 'ac' => 'Incluir en el listado maestro', 'sa' => 'Listado maestro de documentos', 'desc' => 'El listado de documentos vigentes, su código y las áreas en que se utilizan se controlan a través del registro Listado maestro de documentos. Cuando algún documento se revisa y es editada una nueva revisión o se distribuye entre personas o áreas diferentes, el coordinador de SST emite un nuevo Listado maestro de documentos.', 'resp' => "Responsable: Coordinador de SST\nRegistro: AN-XX-SST-05 Listado maestro de documentos"],
                        ['id' => '8', 'en' => 'Documento definitivo (copias)', 'ac' => 'Notificar y distribuir', 'sa' => 'Notificar y distribuir', 'desc' => 'El Coordinador de SST notifica y entrega a las personas copia del documento impreso que van a utilizar en su lugar de trabajo y se encarga, además, de capacitar a todo el personal que requiere tener documentos controlados en su lugar de trabajo. La notificación puede realizarse a través del correo interno.', 'resp' => "Responsable: Coordinador de SST"],
                        ['id' => '9', 'en' => 'Documentos externos', 'ac' => 'Identificar documentos externos', 'sa' => 'Identificar documentos externos', 'desc' => 'Se identifican los documentos externos recibidos o que se encuentren en la empresa: manuales, normas técnicas, catálogos de productos, que requieran ser identificados y controlados como parte del sistema de gestión, y se incluyen en el Listado Maestro de Documentos Externos.', 'resp' => "Responsable: Coordinador de SST\nParticipa: Gerente, jefes de área"]
                    ];
                    foreach($flujos_estaticos as $i => $fl): 
                        $idx = $fl['id'];
                    ?>
                    <tr>
                        <td class="center"><?= $idx ?></td>
                        <td><input type="text" name="flujo_entrada_<?= $idx ?>" class="trans-input" value="<?= htmlspecialchars($fl['en']) ?>"></td>
                        <td><input type="text" name="flujo_actividad_<?= $idx ?>" class="trans-input" value="<?= htmlspecialchars($fl['ac']) ?>"></td>
                        <td><input type="text" name="flujo_salida_<?= $idx ?>" class="trans-input" value="<?= htmlspecialchars($fl['sa']) ?>"></td>
                        <td><textarea name="flujo_desc_<?= $idx ?>" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"><?= htmlspecialchars($fl['desc']) ?></textarea></td>
                        <td><textarea name="flujo_resp_<?= $idx ?>" class="edit" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"><?= htmlspecialchars($fl['resp']) ?></textarea></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="sign-grid">
                <div class="sign">
                    <div style="height: 45px; display:flex; align-items:flex-end; justify-content:center; margin-bottom:5px;">
                        <?php if(!empty($firmaSST)): ?><img src="<?= $firmaSST ?>" alt="Firma SST" style="max-height:45px; object-fit:contain;"><?php endif; ?>
                    </div>
                    <div class="firma-line" style="border-top:1px solid #111; width:80%; margin:0 auto 4px;"></div>
                    <input type="text" name="firma_nombre_sst" class="trans-input center bold" value="<?= htmlspecialchars($nombreSST ?: 'Coordinador SST') ?>"><br>
                    <span style="font-weight:normal; font-size:11px;">Elaboró / Revisó</span>
                </div>
                <div class="sign">
                    <div style="height: 45px; display:flex; align-items:flex-end; justify-content:center; margin-bottom:5px;">
                        <?php if(!empty($firmaRL)): ?><img src="<?= $firmaRL ?>" alt="Firma RL" style="max-height:45px; object-fit:contain;"><?php endif; ?>
                    </div>
                    <div class="firma-line" style="border-top:1px solid #111; width:80%; margin:0 auto 4px;"></div>
                    <input type="text" name="firma_nombre_rl" class="trans-input center bold" value="<?= htmlspecialchars($nombreRL ?: 'Gerente General') ?>"><br>
                    <span style="font-weight:normal; font-size:11px;">Aprobó</span>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Poner fecha por defecto en los selectores
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        
        const f1 = document.getElementById("metaFecha1");
        const f2 = document.getElementById("metaFecha2");
        if(f1 && !f1.value) f1.value = `${y}-${m}-${dd}`;
        if(f2 && !f2.value) f2.value = `${y}-${m}-${dd}`;

        const cFecha = document.getElementById("coverFecha");
        if(cFecha && !cFecha.value) cFecha.value = `${y}/${m}/${dd}`;

        // --- LÓGICA DE CARGADO DESDE LA API ---
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) { console.error(e); }
        }

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) {
                    campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                }
            }
        }

        // Auto-ajustar dinámicamente las dimensiones verticales de los textareas
        document.querySelectorAll('textarea.edit, .cover-title-input').forEach(textarea => {
            textarea.style.height = '';
            textarea.style.height = textarea.scrollHeight + 'px';
        });
    });

    // --- LÓGICA DE GUARDADO ASÍNCRONO ---
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