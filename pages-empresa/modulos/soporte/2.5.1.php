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

        .cover .cover-title{
            font-size:28px;
            font-weight:900;
            text-transform:uppercase;
            line-height:1.25;
            max-width:720px;
            margin-bottom:24px;
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
            resize:vertical;
            min-height:74px;
            line-height:1.5;
        }

        .center{ text-align:center; }
        .small{ font-size:11px; }

        .list-box{
            margin:0;
            padding-left:18px;
        }

        .list-box li{
            margin-bottom:8px;
            line-height:1.55;
            text-align:justify;
        }

        .flow-note{
            background:#f8fbff;
            border:1px solid #d7e2ef;
            padding:10px 12px;
            margin-bottom:10px;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:12px;
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
            .cover .cover-title{
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
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">MANUAL CONTROL DOCUMENTOS</span><br>
            Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
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
                    <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td><strong>Versión:</strong> 0</td>
                    <td><strong>Fecha:</strong><br><input type="date" name="meta_fecha_1" id="metaFecha1" style="border:none; font-size:10px; font-weight:900; outline:none; background:transparent; width:100%;"></td>
                </tr>
                <tr>
                    <td class="subtitle">MANUAL DE CONTROL DE DOCUMENTOS Y CAMBIOS</td>
                    <td class="title" colspan="2">MA-XX-SST-01</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
                </tr>
            </table>

            <div class="cover">
                <div class="cover-title">MANUAL DE CONTROL DE DOCUMENTOS Y CONTROL DE CAMBIOS</div>
                <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 120px; object-fit: contain;">
                    <?php else: ?>
                        LOGO
                    <?php endif; ?>
                </div>
                <div class="cover-text">Versión 0</div>
                <div class="cover-text"><input type="text" name="cover_empresa" class="edit-inline center" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>" placeholder="NOMBRE EMPRESA"></div>
                <div class="cover-text"><input type="text" name="cover_fecha" class="edit-inline center" placeholder="FECHA ACTUAL"></div>
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
                    <td><strong>Fecha:</strong><br><input type="date" name="meta_fecha_2" id="metaFecha2" style="border:none; font-size:10px; font-weight:900; outline:none; background:transparent; width:100%;"></td>
                </tr>
                <tr>
                    <td class="subtitle">MANUAL DE CONTROL DE DOCUMENTOS Y CAMBIOS</td>
                    <td class="title" colspan="2">MA-XX-SST-01</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
                </tr>
            </table>

            <div class="sec-h">Introducción</div>
            <div class="box text-just">
                <textarea name="txt_intro" class="edit" rows="5">El presente manual sirve de guía en LA EMPRESA, para el manejo y control documental de todos sus procesos estratégicos, con el fin de tener un manejo más claro, ágil y eficiente. Además, como herramienta para desarrollar la codificación, numeración y el cumplimiento de normas ICONTEC.

La cual implementada de forma adecuada evidenciara el éxito del objetivo planteado.</textarea>
            </div>

            <div class="sec-h">Objetivo</div>
            <div class="box text-just">
                <textarea name="txt_objetivo" class="edit" rows="3">Con este manual se pretende estandarizar los procesos LA EMPRESA, para dar agilidad a estos, de una manera entendible y fácil de utilizar.</textarea>
            </div>

            <div class="sec-h">Campo de aplicación</div>
            <div class="box text-just">
                <textarea name="txt_campo" class="edit" rows="3">Aplica a todos los colaboradores de LA EMPRESA, que generen documentos que representen la empresa o den apoyo al Sistema de Gestión de Seguridad y Salud en el trabajo, entre otros.</textarea>
            </div>

            <div class="sec-h">Normas generales para la elaboración de la documentación y correspondencia</div>
            <div class="box text-just">
                <p>Para elaboración es importante tener en cuenta algunas técnicas sobre la forma de elaborar los documentos con el fin de normalizar su producción. El ICONTEC nos da herramientas que facilitan la gestión de la documentación.</p>
                <p><strong>Márgenes:</strong> Se deben conservar las siguientes márgenes en el documento:</p>
                <table class="formtbl">
                    <tbody>
                        <tr><td style="width:180px;"><strong>Superior</strong></td><td><input name="margen_sup" class="edit" type="text" value="3 cm"></td></tr>
                        <tr><td><strong>Izquierdo</strong></td><td><input name="margen_izq" class="edit" type="text" value="4 cm"></td></tr>
                        <tr><td><strong>Derecho</strong></td><td><input name="margen_der" class="edit" type="text" value="2 cm"></td></tr>
                        <tr><td><strong>Inferior</strong></td><td><input name="margen_inf" class="edit" type="text" value="3 cm"></td></tr>
                    </tbody>
                </table>
                <textarea name="txt_margen_nota" class="edit" rows="3">En caso de ser impreso por ambas caras todas las márgenes deben ser de 3 cm. Los títulos de cada capítulo deben estar en hojas independientes a 3 cm del borde superior.</textarea>
            </div>

            <div class="sec-h">Portada</div>
            <div class="box text-just">
                <p>La portada de los documentos que se utiliza para el sistema de gestión de la seguridad y salud en el trabajo, manuales, programas, procedimientos e instructivos es la siguiente:</p>
                <ul class="list-box">
                    <li>Título del documento: se escribe centrado horizontalmente, en letra Arial 20, en negrilla y en mayúscula sostenida.</li>
                    <li>Logotipo de la empresa: centrado en la parte media, de 4 cm de alto por 12 cm de ancho.</li>
                    <li>Número de la versión: se escribe la palabra “Versión” seguida del número correspondiente.</li>
                    <li>Nombre completo de la empresa centrado horizontalmente al final de la página en letra Arial 12, en negrilla y mayúscula sostenida.</li>
                    <li>Fecha: se indica el mes y el año en que la versión del documento ha sido aprobado.</li>
                </ul>
            </div>

            <div class="sec-h">Encabezado del documento</div>
            <div class="box text-just">
                <p>En todas las hojas, a excepción de la portada, debe aparecer el cuadro que identifica el documento y contiene la siguiente información:</p>
                <ol class="list-box">
                    <li>Escudo o logo de la empresa.</li>
                    <li>Nombre del sistema en mayúscula sostenida, negrita y con letra Arial 12.</li>
                    <li>Título del documento en letra Arial 12, sin negrilla y en mayúscula sostenida.</li>
                    <li>Versión del documento.</li>
                    <li>Código del documento.</li>
                    <li>Fecha de emisión y/o actualización del documento.</li>
                </ol>
            </div>

            <div class="sec-h">Lineamientos de redacción</div>
            <table class="formtbl">
                <tbody>
                    <tr>
                        <td style="width:180px;"><strong>Redacción</strong></td>
                        <td><textarea name="lin_redaccion" class="edit" rows="3">Se deben seguir las reglas ortográficas de la lengua española. La redacción debe ser en tercera persona; se debe justificar toda la documentación y correspondencia.</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Tipo de letra</strong></td>
                        <td><textarea name="lin_tipo_letra" class="edit" rows="3">Se recomienda el uso de la fuente Arial con un tamaño 12 u 11, para el cuerpo del documento; los títulos y subtítulos irán en negrilla conservando el mismo tamaño de letra.</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Numeración</strong></td>
                        <td><textarea name="lin_numeracion" class="edit" rows="3">La numeración de las páginas debe hacerse de forma consecutiva con números arábigos a excepción de la cubierta y la portada, que no se enumeran.</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Contenido</strong></td>
                        <td><textarea name="lin_contenido" class="edit" rows="3">Permitir al lector encontrar una parte específica del documento de una forma rápida, utilizando siempre al inicio de cada documento o correspondencia el código de la dependencia.</textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">Codificación de procesos</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Nombres del nivel</th>
                        <th style="width:180px;">Código</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="cod_nivel_nombre" class="edit" type="text" value="SEGURIDAD Y SALUD EN EL TRABAJO"></td>
                        <td class="center"><input name="cod_nivel_codigo" class="edit center" type="text" value="SST"></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">Tipo de documentación</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Nombre del documento</th>
                        <th style="width:180px;">Tipo documento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Manuales</td><td class="center">MA</td></tr>
                    <tr><td>Política</td><td class="center">PO</td></tr>
                    <tr><td>Instructivos</td><td class="center">IN</td></tr>
                    <tr><td>Programa</td><td class="center">PR</td></tr>
                    <tr><td>Manual de funciones</td><td class="center">MF</td></tr>
                    <tr><td>Planes</td><td class="center">PL</td></tr>
                    <tr><td>Registros</td><td class="center">RE</td></tr>
                    <tr><td>Anexos</td><td class="center">AN</td></tr>
                    <tr><td>Actas</td><td class="center">AC</td></tr>
                    <tr><td>Informe</td><td class="center">IN</td></tr>
                </tbody>
            </table>

            <div class="sec-h">Codificación de la empresa</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:180px;">Código</th>
                        <th>Iniciales de la empresa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center"><input name="cod_empresa_sigla" class="edit center" type="text" value="XX"></td>
                        <td><input name="cod_empresa_nombre" class="edit" type="text" value="INICIALES DE LA EMPRESA"></td>
                    </tr>
                </tbody>
            </table>

            <div class="box text-just">
                <p>Las tablas anteriores se van a codificar de la siguiente manera:</p>
                <p><strong>Tipo de Documento + (XX) Identificación de la empresa + Nombre de Proceso + Consecutivo</strong></p>
                <p><strong>Ejemplo:</strong> MA-XX-SST-01</p>
            </div>

            <div class="sec-h">Ejemplo de codificación del proceso</div>
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
                        <td><input name="ej_proceso" class="edit" type="text" value="Manual de control de documentos"></td>
                        <td class="center"><input name="ej_codigo" class="edit center" type="text" value="MA-XX-SST-01"></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">Procedimiento para control documental</div>
            <div class="flow-note"><strong>Nota:</strong> Diligencie la actividad y los responsables según los procedimientos de su empresa.</div>

            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:40px;">ID</th>
                        <th style="width:150px;">Entrada</th>
                        <th style="width:150px;">Actividad</th>
                        <th style="width:150px;">Salida</th>
                        <th>Descripción (Cómo)</th>
                        <th style="width:200px;">Responsable / Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>Inicio</td>
                        <td>Inicio</td>
                        <td>Inicio</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="2"></textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="2"></textarea></td>
                    </tr>
                    <tr>
                        <td class="center">2</td>
                        <td>Información para elaboración del documento</td>
                        <td>Elaborar documento</td>
                        <td>Elaborar documento</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="5">El coordinador de SST se encarga de controlar la elaboración de documentos (manuales, procedimientos, instructivos, planes, registros) del Sistema de Gestión en Seguridad y Salud en el Trabajo. Obtiene la información por medio de observación directa y entrevistas con el personal que realiza las actividades que se documentarán.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="5">Responsable: Coordinador de SST
Participa: Persona(s) que utilizarán el documento</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">3</td>
                        <td></td>
                        <td>¿Hay cambios?</td>
                        <td>SI / NO</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="4">Si existen cambios en el documento, el coordinador de SST los incorpora, hasta conseguir con quienes participan de la elaboración el consenso acerca de los cambios y obtener el documento previo a ser aprobado.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="4">Responsable: Coordinador de SST
Participa: Persona(s) que utilizarán el documento</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">4</td>
                        <td></td>
                        <td>Aprobar edición</td>
                        <td>Aprobar edición</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="4">El coordinador de SST revisa inmediatamente y envía al gerente para que apruebe la edición preliminar del documento y la presentación del medio impreso y el archivo en medio magnético.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="4">Responsable: Coordinador de SST
Participa: Gerente</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">5</td>
                        <td></td>
                        <td>Codificar y actualizar revisión</td>
                        <td>Codificar y actualizar revisión</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="4">Si no existe una versión anterior al documento o es uno nuevo se identifica con la Revisión 0, y se codifica según el anexo 1. Si existe una revisión anterior, se sigue el número consecutivo correspondiente.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="4">Responsable: Coordinador de SST</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">6</td>
                        <td></td>
                        <td>Editar</td>
                        <td>Editar</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="6">El Coordinador de SST realiza la presentación definitiva del documento, con las firmas de quien elabora, revisa y aprueba en la primera hoja del documento. Al entrar en vigencia una nueva revisión, debe retirar el documento anterior y convertirlo en documento obsoleto. Las fechas de cada documento serán registradas en la primera hoja de cada documento.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="6">Responsable: Coordinador de SST</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">7</td>
                        <td></td>
                        <td>Incluir en el listado maestro</td>
                        <td>Listado maestro de documentos</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="6">El listado de documentos vigentes, su código y las áreas en que se utilizan se controlan a través del registro Listado maestro de documentos. Cuando algún documento se revisa y es editada una nueva revisión o se distribuye entre personas o áreas diferentes, el coordinador de SST emite un nuevo Listado maestro de documentos.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="6">Responsable: Coordinador de SST
Registro: AN-XX-SST-05 Listado maestro de documentos</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">8</td>
                        <td>Documento definitivo (copias)</td>
                        <td>Notificar y distribuir</td>
                        <td>Notificar y distribuir</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="5">El Coordinador de SST notifica y entrega a las personas copia del documento impreso que van a utilizar en su lugar de trabajo y se encarga, además, de capacitar a todo el personal que requiere tener documentos controlados en su lugar de trabajo. La notificación puede realizarse a través del correo interno.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="5">Responsable: Coordinador de SST</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">9</td>
                        <td>Documentos externos</td>
                        <td>Identificar documentos externos</td>
                        <td>Identificar documentos externos</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="5">Se identifican los documentos externos recibidos o que se encuentren en la empresa: manuales, normas técnicas, catálogos de productos, que requieran ser identificados y controlados como parte del sistema de gestión, y se incluyen en el Listado Maestro de Documentos Externos.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="5">Responsable: Coordinador de SST
Participa: Gerente, jefes de área</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">10</td>
                        <td></td>
                        <td>Mantener</td>
                        <td>Mantener</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="4">Los responsables y/o usuarios de los documentos deben procurar mantener la integridad física de los asignados y el cumplimiento de las directrices allí establecidas.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="4">Responsable: Persona(s) que utilizan el documento</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">11</td>
                        <td></td>
                        <td>¿Se necesita actualizar?</td>
                        <td>SI / NO</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="5">Si existen cambios en las actividades, procesos, nuevas líneas de productos o servicios, cambios en las funciones de los cargos existentes o nuevos cargos, se hace necesario revisar los documentos actuales para evaluar la necesidad de modificar su contenido, así como los registros involucrados.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="5">Responsable: Persona(s) que utilizan el documento</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">12</td>
                        <td></td>
                        <td>Revisar</td>
                        <td>Revisar</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="5">La revisión puede ser sugerida por quienes realizan el documento o quienes lo elaboran, aprueban o quienes lo usan. Se deben justificar los cambios propuestos; en caso de aprobar el cambio, al final del documento aparece un resumen de los cambios realizados como evidencia.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="5">Responsable: Persona(s) que utilizan el documento</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">13</td>
                        <td></td>
                        <td>Reelaborar</td>
                        <td>Reelaborar</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="4">Los cambios son realizados mediante la misma ruta original, y se aprueban como la primera vez. Se toman en cuenta los enlaces con otros documentos relacionados por pertenecer a una misma área, proceso o actividad.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="4">Responsable: Coord. SST / Persona(s) usuarias</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">14</td>
                        <td></td>
                        <td>¿Es documento obsoleto?</td>
                        <td>SI / NO</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="3">Al entrar en vigencia una nueva revisión, se debe retirar el documento anterior y convertirlo en documento obsoleto.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="3">Responsable: Coordinador de SST / Resp. documento</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">15</td>
                        <td></td>
                        <td>Destruir</td>
                        <td>Destruir</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="3">Se eliminan los documentos obsoletos que estén en medio físico y electrónico, se destruyen para evitar su consulta y utilización indebida.</textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="3">Responsable: Coordinador de SST y/o jefe de procesos</textarea></td>
                    </tr>
                    <tr>
                        <td class="center">16</td>
                        <td></td>
                        <td>Fin</td>
                        <td>Fin</td>
                        <td><textarea name="flujo_desc[]" class="edit" rows="2"></textarea></td>
                        <td><textarea name="flujo_resp[]" class="edit" rows="2"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">Definiciones y terminología</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th style="width:220px;">Término</th>
                        <th>Definición</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><strong>Aprobación</strong></td><td>Autorización de los documentos del sistema por parte de la gerencia para su utilización, antes de proceder a su aplicación.</td></tr>
                    <tr><td><strong>Código</strong></td><td>Asignación de letras y números a los documentos, mediante el cual se obtiene mayor control.</td></tr>
                    <tr><td><strong>Documento</strong></td><td>Información y su medio de soporte.</td></tr>
                    <tr><td><strong>Documento vigente</strong></td><td>Es aquel documento reproducido del original aprobado, que cuando sufre modificaciones es remplazado para evitar el uso de documentos obsoletos.</td></tr>
                    <tr><td><strong>Procedimiento</strong></td><td>Forma especificada para llevar a cabo una actividad o proceso.</td></tr>
                    <tr><td><strong>Registro</strong></td><td>Documento que presenta resultados obtenidos o proporciona evidencia de actividades desempeñadas.</td></tr>
                </tbody>
            </table>

            <div class="sec-h">Registros</div>
            <table class="formtbl">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th style="width:260px;">Departamento, área o persona que debe retener el documento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="reg_titulo" class="edit" type="text" value="Listado maestro de documentos"></td>
                        <td class="center"><input name="reg_area" class="edit center" type="text" value="SST"></td>
                    </tr>
                </tbody>
            </table>

            <div class="sec-h">Conservación de documentos</div>
            <div class="box">
                <ul class="list-box">
                    <li>Información sobre la legislación en SST aplicable.</li>
                    <li>Registros de las formaciones proporcionadas a todos los empleados, incluida la inducción y la reinducción.</li>
                    <li>Registros de entrenamientos, simulacros y simulaciones ejecutados en desarrollo del plan de prevención, preparación y respuesta ante emergencias.</li>
                    <li>Registros de las inspecciones realizadas.</li>
                    <li>Resultados de mediciones y monitoreo a los ambientes de trabajo.</li>
                    <li>Registros de no conformidades, incidentes, accidentes, enfermedades laborales y la investigación y análisis de estos eventos.</li>
                    <li>Registros de los análisis de seguridad realizados a las tareas críticas no rutinarias o trabajos de alto riesgo.</li>
                    <li>Registros de la identificación de peligros, evaluación de riesgos y medidas de prevención y control definidas.</li>
                    <li>Registro de entrega de elementos de protección personal.</li>
                    <li>Resultados de perfiles epidemiológicos de salud y conceptos de exámenes de ingreso, periódicos y de retiro.</li>
                    <li>Resultados de exámenes complementarios como audiometrías, espirometrías, radiografías de tórax y otros que apliquen.</li>
                    <li>Registros relacionados con la evaluación del desempeño de la SST.</li>
                    <li>Registros de las revisiones por la alta dirección.</li>
                    <li>Informes de auditorías internas o externas del SG-SST.</li>
                </ul>
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
                    text: 'Manual de documentos guardado correctamente',
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