<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
// Ajusta esta ruta dependiendo de la ubicación de este archivo
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 2; // ID del ítem anclado a este manual

// --- Lógica de Permisos y Plan (Optimizada) ---
$nombreEmpresaLogeada = "Sin Empresa";
$idPlanEmpresa = 0;

if ($empresa > 0) {
    // Solicitamos a la API exclusivamente la empresa logueada pasando el ID
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);

    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $nombreEmpresaLogeada = $empData['nombre_empresa'] ?? 'Sin Empresa';
        $idPlanEmpresa = (int)($empData['id_plan'] ?? 0);
    }
}

// 2. SOLICITAMOS LOS DATOS DEL FORMULARIO A LA API
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
} else {
    $errorCarga = "No se detectaron campos válidos. Respuesta: " . json_encode($resFormulario);
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>1.1.2-2 - Manual de Funciones y Competencias</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --sst-border:#111;
            --sst-primary:#9fb4d9;
            --sst-primary-soft:#dbe7f7;
            --sst-bg:#eef3f9;
            --sst-paper:#ffffff;
            --sst-text:#111;
            --sst-muted:#5f6b7a;
            --sst-toolbar:#dde7f5;
            --sst-toolbar-border:#c8d3e2;
        }

        *{ box-sizing:border-box; }

        html, body{ margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; background:var(--sst-bg); color:var(--sst-text); }
        .sst-toolbar{ position:sticky; top:0; z-index:100; background:var(--sst-toolbar); border-bottom:1px solid var(--sst-toolbar-border); padding:12px 18px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
        .sst-toolbar-title{ margin:0; font-size:15px; font-weight:800; color:#213b67; }
        .sst-toolbar-actions{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .sst-page{ padding:20px; }
        .sst-paper{ width:216mm; min-height:279mm; margin:0 auto 16px auto; background:var(--sst-paper); border:1px solid #d7dee8; box-shadow:0 10px 25px rgba(0,0,0,.08); padding:8mm; box-sizing:border-box; }
        .page-break{ page-break-after:always; }
        .sst-table{ width:100%; border-collapse:collapse; table-layout:fixed; font-size:12px; margin-bottom:12px; }
        .sst-table td, .sst-table th{ border:1px solid var(--sst-border); padding:6px 8px; vertical-align:top; font-size:12px; word-wrap:break-word; height:auto; }
        .doc-title{ text-align:center; font-weight:800; font-size:13px; text-transform:uppercase; line-height:1.35; }
        .doc-subtitle{ text-align:center; font-weight:800; font-size:12px; text-transform:uppercase; line-height:1.35; }
        .logo-box{ height:68px; border:2px dashed #b5b5b5; display:flex; align-items:center; justify-content:center; text-align:center; font-weight:800; color:#808080; font-size:11px; line-height:1.2; }
        .section-title{ background:var(--sst-primary); border:1px solid var(--sst-border); color:#10233c; font-weight:800; text-transform:uppercase; padding:10px 14px; font-size:14px; margin-top:14px; margin-bottom:10px; }
        .section-header{ display:flex; justify-content:space-between; align-items:center; gap:10px; margin-top:14px; margin-bottom:10px; }
        .section-header .section-title{ margin:0; flex:1; }
        .cover{ min-height:860px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding:24px 20px; }
        .cover-mini{ font-size:13px; font-weight:700; margin-bottom:14px; text-transform:uppercase; width:100%; }
        .cover-title{ font-size:28px; font-weight:900; line-height:1.25; text-transform:uppercase; max-width:720px; margin-bottom:24px; width:100%; }
        .cover-logo{ width:180px; height:140px; border:2px dashed #b5b5b5; display:flex; align-items:center; justify-content:center; color:#808080; font-weight:800; margin-bottom:30px; }
        .cover-version, .cover-company, .cover-date{ font-size:16px; font-weight:700; margin-bottom:10px; width:100%; }
        .box{ border:1px solid var(--sst-border); padding:12px; margin-bottom:12px; }
        .text-just{ text-align:justify; line-height:1.6; }
        .small{ font-size:11px; }
        .center{ text-align:center; }
        .bold{ font-weight:800; }
        .profile-title{ background:var(--sst-primary-soft); border:1px solid #c9d6ea; padding:10px 12px; font-weight:800; text-transform:uppercase; margin-bottom:10px; }
        .signature-grid{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-top:12px; }
        .sign-box{ border-top:1px solid #111; padding-top:8px; text-align:center; min-height:56px; font-size:12px; font-weight:700; }
        .sst-input, .sst-select{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 4px; font-family:Arial, Helvetica, sans-serif; color:#111; }
        .sst-textarea{ width:100%; border:none; outline:none; background:transparent; font-size:12px; line-height:1.45; padding:0; resize:none; overflow:hidden; height:auto; min-height:unset; font-family:Arial, Helvetica, sans-serif; color:#111; display:block; }
        .sst-input-line{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 0; border-bottom:1px solid #666; font-family:Arial, Helvetica, sans-serif; color:#111; }
        .sst-input-cover{ width:100%; max-width:420px; margin:0 auto; border:none; border-bottom:1px solid #666; outline:none; background:transparent; text-align:center; font-size:16px; padding:6px 8px; font-weight:700; }
        .sst-input-cover.small{ max-width:280px; }
        .list-box{ margin:0; padding-left:18px; }
        .list-box li{ margin-bottom:8px; text-align:justify; line-height:1.5; }
        .add-btn{ white-space:nowrap; }

        @page{ size:Letter; margin:8mm; }
        @media print{
            html, body{ background:#fff !important; }
            .sst-toolbar, .no-print{ display:none !important; }
            .sst-page{ padding:0 !important; margin:0 !important; }
            .sst-paper{ width:100% !important; min-height:auto !important; margin:0 !important; border:none !important; box-shadow:none !important; padding:0 !important; }
            .sst-input, .sst-select, .sst-textarea, .sst-input-line, .sst-input-cover{ color:#000 !important; }
        }
        @media (max-width: 991px){
            .sst-page{ padding:12px; }
            .sst-paper{ width:100%; min-height:auto; padding:12px; }
            .signature-grid{ grid-template-columns:1fr; }
            .cover-title{ font-size:22px; }
            .section-header{ flex-direction:column; align-items:stretch; }
        }
    </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<form id="form-sst-dinamico">
    <div class="sst-toolbar">
        <h1 class="sst-toolbar-title">1.1.2-2 · Manual de Funciones y Competencias</h1>

        <div class="sst-toolbar-actions">
            <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
            <button type="button" class="btn btn-success btn-sm" id="btnGuardar">
                <i class="fa-solid fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="sst-page">

        <div class="sst-paper page-break">
            <table class="sst-table">
                <colgroup>
                    <col style="width:18%">
                    <col style="width:52%">
                    <col style="width:15%">
                    <col style="width:15%">
                </colgroup>
                <tr>
                    <td rowspan="3">
                        <div class="logo-box">LOGO EMPRESA</div>
                    </td>
                    <td><textarea name="doc_title_1" class="sst-textarea doc-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO SG-SST - PESV</textarea></td>
                    <td><strong>Versión:</strong> <textarea name="doc_version_1" class="sst-textarea center">0</textarea></td>
                    <td><strong>Fecha:</strong><br><textarea name="doc_fecha_1" class="sst-textarea center">XX-XX-XXXX</textarea></td>
                </tr>
                <tr>
                    <td><textarea name="doc_subtitle_1" class="sst-textarea doc-subtitle">MANUAL DE FUNCIONES Y COMPETENCIAS</textarea></td>
                    <td colspan="2"><textarea name="doc_codigo_1" class="sst-textarea doc-title center">MA-XX-SST-03</textarea></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Proceso:</strong> <textarea name="doc_proceso_1" class="sst-textarea">Gestión de Seguridad y Salud en el Trabajo</textarea></td>
                </tr>
            </table>

            <div class="cover">
                <div class="cover-mini">
                    <textarea name="cover_mini" class="sst-textarea center bold">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO SG-SST - PESV</textarea>
                </div>
                <div class="cover-title">
                    <textarea name="cover_title" class="sst-textarea center bold" style="font-size:28px; text-transform:uppercase;">MANUAL DE FUNCIONES Y COMPETENCIAS</textarea>
                </div>
                <div class="cover-logo">LOGO</div>
                <div class="cover-version">
                    <textarea name="cover_version" class="sst-textarea center bold">Versión 0</textarea>
                </div>
                <div class="cover-company">
                    <input name="cover_company" type="text" class="sst-input-cover" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
                </div>
                <div class="cover-date">
                    <input name="cover_date" type="text" class="sst-input-cover small" value="FECHA">
                </div>
            </div>
        </div>

        <div class="sst-paper">

            <table class="sst-table">
                <colgroup>
                    <col style="width:18%">
                    <col style="width:52%">
                    <col style="width:15%">
                    <col style="width:15%">
                </colgroup>
                <tr>
                    <td rowspan="3">
                        <div class="logo-box">LOGO EMPRESA</div>
                    </td>
                    <td><textarea name="doc_title_2" class="sst-textarea doc-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO SG-SST - PESV</textarea></td>
                    <td><strong>Versión:</strong> <textarea name="doc_version_2" class="sst-textarea center">0</textarea></td>
                    <td><strong>Fecha:</strong><br><textarea name="doc_fecha_2" class="sst-textarea center">XX-XX-XXXX</textarea></td>
                </tr>
                <tr>
                    <td><textarea name="doc_subtitle_2" class="sst-textarea doc-subtitle">MANUAL DE FUNCIONES Y COMPETENCIAS</textarea></td>
                    <td colspan="2"><textarea name="doc_codigo_2" class="sst-textarea doc-title center">MA-XX-SST-03</textarea></td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Proceso:</strong> <textarea name="doc_proceso_2" class="sst-textarea">Gestión de Seguridad y Salud en el Trabajo</textarea></td>
                </tr>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Control de documentos</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addControlDocumentos()">Agregar fila</button>
            </div>
            <table class="sst-table">
                <thead>
                    <tr>
                        <th>Elaboró por</th>
                        <th>Revisado por</th>
                        <th>Aprobado por</th>
                    </tr>
                </thead>
                <tbody id="control-documentos-body" data-add-func="addControlDocumentos">
                    <tr>
                        <td><textarea name="ctrl_doc_elaboro[]" class="sst-textarea" placeholder="Nombre y cargo"></textarea></td>
                        <td><textarea name="ctrl_doc_reviso[]" class="sst-textarea" placeholder="Nombre y cargo"></textarea></td>
                        <td><textarea name="ctrl_doc_aprobo[]" class="sst-textarea" placeholder="Nombre y cargo"></textarea></td>
                    </tr>
                    <tr>
                        <td><textarea name="ctrl_doc_elaboro[]" class="sst-textarea" placeholder="Firma / fecha"></textarea></td>
                        <td><textarea name="ctrl_doc_reviso[]" class="sst-textarea" placeholder="Firma / fecha"></textarea></td>
                        <td><textarea name="ctrl_doc_aprobo[]" class="sst-textarea" placeholder="Firma / fecha"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Control de cambios</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addControlCambios()">Agregar fila</button>
            </div>
            <table class="sst-table">
                <thead>
                    <tr>
                        <th style="width:90px;">Revisión</th>
                        <th style="width:140px;">Fecha</th>
                        <th>Descripción del cambio</th>
                    </tr>
                </thead>
                <tbody id="control-cambios-body" data-add-func="addControlCambios">
                    <tr>
                        <td><textarea name="ctrl_cam_rev[]" class="sst-textarea center">0</textarea></td>
                        <td><textarea name="ctrl_cam_fecha[]" class="sst-textarea">XX/XX/XXXX</textarea></td>
                        <td><textarea name="ctrl_cam_desc[]" class="sst-textarea">Creación del Manual de Funciones y competencias</textarea></td>
                    </tr>
                    <tr>
                        <td><textarea name="ctrl_cam_rev[]" class="sst-textarea center"></textarea></td>
                        <td><textarea name="ctrl_cam_fecha[]" class="sst-textarea"></textarea></td>
                        <td><textarea name="ctrl_cam_desc[]" class="sst-textarea"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Contenido</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addContenido()">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="contenido-body" data-add-func="addContenido">
                    <tr><td><textarea name="cont_tema[]" class="sst-textarea">1. OBJETIVO</textarea></td><td style="width:90px;"><textarea name="cont_pag[]" class="sst-textarea center">4</textarea></td></tr>
                    <tr><td><textarea name="cont_tema[]" class="sst-textarea">2. ALCANCE</textarea></td><td><textarea name="cont_pag[]" class="sst-textarea center">4</textarea></td></tr>
                    <tr><td><textarea name="cont_tema[]" class="sst-textarea">3. DEFINICIONES</textarea></td><td><textarea name="cont_pag[]" class="sst-textarea center">4</textarea></td></tr>
                    <tr><td><textarea name="cont_tema[]" class="sst-textarea">4. CONDICIONES GENERALES</textarea></td><td><textarea name="cont_pag[]" class="sst-textarea center">6</textarea></td></tr>
                    <tr><td><textarea name="cont_tema[]" class="sst-textarea">5. CONTENIDO</textarea></td><td><textarea name="cont_pag[]" class="sst-textarea center">7</textarea></td></tr>
                    <tr><td><textarea name="cont_tema[]" class="sst-textarea">5.1 Funciones y competencias del cargo</textarea></td><td><textarea name="cont_pag[]" class="sst-textarea center">7</textarea></td></tr>
                </tbody>
            </table>

            <div class="section-title">1. Objetivo</div>
            <div class="box text-just">
                <textarea name="objetivo" class="sst-textarea">Establecer las funciones y competencias laborales del personal encargado de desempeñar los cargos que llevan a cabo los procesos de la empresa.</textarea>
            </div>

            <div class="section-title">2. Alcance</div>
            <div class="box text-just">
                <textarea name="alcance" class="sst-textarea">Este manual aplica para todos los niveles de la estructura organizacional de la empresa.</textarea>
            </div>

            <div class="section-header no-print">
                <div class="section-title">3. Definiciones</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addDefinicion()">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="definiciones-body" data-add-func="addDefinicion">
                    <tr><td style="width:180px;"><textarea name="def_concepto[]" class="sst-textarea bold">Cargo</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Es la agrupación de todas aquellas actividades o tareas realizadas por un solo trabajador que ocupe un lugar específico dentro del organigrama de la empresa.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Funciones</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Son las tareas que el trabajador debe realizar o ejecutar.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Responsabilidad</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Es asumir las consecuencias de los resultados de las tareas desarrolladas por sí mismo o por personas a cargo.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Autoridad</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Equivale a rendir cuentas, poder de decisión y a los cargos más altos de la pirámide organizacional.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Competencia</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Capacidad para aplicar conocimientos y habilidades con el fin de lograr los resultados previstos.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Educación</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Adquisición de conocimientos académicos mediante estudio formal.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Experiencia</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Conocimiento, habilidades y destrezas desarrolladas o adquiridas mediante el ejercicio de una profesión, arte u oficio.</textarea></td></tr>
                    <tr><td><textarea name="def_concepto[]" class="sst-textarea bold">Experiencia laboral</textarea></td><td><textarea name="def_desc[]" class="sst-textarea">Es la adquirida con el ejercicio de cualquier empleo, ocupación, arte u oficio.</textarea></td></tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">4. Condiciones generales</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addCondicionGeneral()">Agregar ítem</button>
            </div>
            <div class="box">
                <ul class="list-box" id="condiciones-generales-list" data-add-func="addCondicionGeneral">
                    <li><textarea name="condicion_gen[]" class="sst-textarea">Las funciones definidas en el presente manual deberán ser cumplidas por todo el personal de la organización con criterios de eficiencia y eficacia en el logro de la misión, visión, objetivos estratégicos, políticas y funciones que la ley, estatutos y reglamentos internos le señalen a la empresa.</textarea></li>
                    <li><textarea name="condicion_gen[]" class="sst-textarea">El jefe del proceso de Gestión del Talento Humano tiene la autoridad para homologar los requisitos establecidos en las competencias laborales de cada cargo, pudiendo homologar educación por experiencia y viceversa.</textarea></li>
                    <li><textarea name="condicion_gen[]" class="sst-textarea">La convalidación o eliminación de requisitos para seleccionar a un aspirante solo podrá ser realizada por la gerencia o la junta de socios.</textarea></li>
                    <li><textarea name="condicion_gen[]" class="sst-textarea">El jefe del proceso de Gestión del Talento Humano es responsable de hacer cumplir lo establecido en este manual y socializarlo a cada trabajador.</textarea></li>
                    <li><textarea name="condicion_gen[]" class="sst-textarea">Los jefes inmediatos deben responder por la orientación del trabajador para el cumplimiento de las funciones que le correspondan.</textarea></li>
                </ul>
            </div>

            <div class="section-title">5. Funciones y competencias del cargo</div>

            <div class="profile-title"><textarea name="p1_titulo" class="sst-textarea bold center">Perfil 1 - Encargado del SG-SST - PESV</textarea></div>

            <table class="sst-table">
                <tbody>
                    <tr>
                        <th style="width:220px;">Nivel</th>
                        <td><textarea name="p1_nivel" class="sst-textarea">DIRECTIVO (A)</textarea></td>
                    </tr>
                    <tr>
                        <th>Denominación del empleo</th>
                        <td><textarea name="p1_denominacion" class="sst-textarea">ENCARGADO DEL SG-SST - PESV</textarea></td>
                    </tr>
                    <tr>
                        <th>Dependencia</th>
                        <td><textarea name="p1_dependencia" class="sst-textarea">PROFESIONAL</textarea></td>
                    </tr>
                    <tr>
                        <th>Propósito principal</th>
                        <td><textarea name="p1_proposito" class="sst-textarea" placeholder="Describa el propósito principal del cargo"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Funciones específicas</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addTwoColRow('funciones-especificas-body')">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="funciones-especificas-body" data-add-func="addTwoColRow">
                    <?php for($i=1; $i<=8; $i++): ?>
                    <tr>
                        <td style="width:50px;" class="center"><strong><?php echo $i; ?></strong></td>
                        <td><textarea name="p1_func[]" class="sst-textarea" placeholder="Función específica <?php echo $i; ?>"></textarea></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Responsabilidades frente al SG-SST</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addTwoColRow('responsabilidades-sgsst-body')">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="responsabilidades-sgsst-body" data-add-func="addTwoColRow">
                    <tr><td style="width:50px;">1</td><td><textarea name="p1_sgsst[]" class="sst-textarea">Cumplir con la normatividad establecida por la legislación en seguridad y salud en el trabajo vigente.</textarea></td></tr>
                    <tr><td>2</td><td><textarea name="p1_sgsst[]" class="sst-textarea">Informar al jefe inmediato o miembros del COPASST sobre las condiciones y/o acciones inseguras en los lugares de trabajo.</textarea></td></tr>
                    <tr><td>3</td><td><textarea name="p1_sgsst[]" class="sst-textarea">Participar activamente en las charlas y cursos de capacitación en seguridad y salud en el trabajo.</textarea></td></tr>
                    <tr><td>4</td><td><textarea name="p1_sgsst[]" class="sst-textarea">Participar activamente en los grupos de seguridad y salud en el trabajo que se conformen en la empresa.</textarea></td></tr>
                    <tr><td>5</td><td><textarea name="p1_sgsst[]" class="sst-textarea">Hacer adecuado uso de las instalaciones, máquinas, equipos, herramientas y elementos de protección personal.</textarea></td></tr>
                    <tr><td>6</td><td><textarea name="p1_sgsst[]" class="sst-textarea">Acatar y atender las recomendaciones en seguridad y salud en el trabajo del jefe inmediato.</textarea></td></tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Responsabilidades frente al PESV</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addTwoColRow('responsabilidades-pesv-body')">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="responsabilidades-pesv-body" data-add-func="addTwoColRow">
                    <tr><td style="width:50px;">1</td><td><textarea name="p1_pesv[]" class="sst-textarea">Planear, organizar, dirigir, desarrollar y aplicar el PESV, y como mínimo una vez al año realizar su evaluación.</textarea></td></tr>
                    <tr><td>2</td><td><textarea name="p1_pesv[]" class="sst-textarea">Verificar el cumplimiento y desempeño del PESV.</textarea></td></tr>
                    <tr><td>3</td><td><textarea name="p1_pesv[]" class="sst-textarea">Informar a la alta gerencia sobre el funcionamiento y los resultados del PESV.</textarea></td></tr>
                    <tr><td>4</td><td><textarea name="p1_pesv[]" class="sst-textarea">Promover la participación de todos los miembros de la empresa en la implementación del PESV.</textarea></td></tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Competencias transversales</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addCompetencia()">Agregar fila</button>
            </div>
            <table class="sst-table">
                <thead>
                    <tr>
                        <th style="width:220px;">Categoría</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody id="competencias-body" data-add-func="addCompetencia">
                    <tr><td><textarea name="comp_cat[]" class="sst-textarea bold">Conducción de trabajo</textarea></td><td><textarea name="comp_desc[]" class="sst-textarea">Capacidad para dirigir grupos de colaboradores de alto desempeño, distribuir tareas y delegar autoridad.</textarea></td></tr>
                    <tr><td><textarea name="comp_cat[]" class="sst-textarea bold">Liderazgo</textarea></td><td><textarea name="comp_desc[]" class="sst-textarea">Capacidad para definir y comunicar la visión organizacional y generar entusiasmo, compromiso y orientación.</textarea></td></tr>
                    <tr><td><textarea name="comp_cat[]" class="sst-textarea bold">Visión estratégica</textarea></td><td><textarea name="comp_desc[]" class="sst-textarea">Capacidad para anticiparse y comprender los cambios del entorno, y establecer su impacto a corto, mediano y largo plazo.</textarea></td></tr>
                    <tr><td><textarea name="comp_cat[]" class="sst-textarea bold">Manejo de crisis</textarea></td><td><textarea name="comp_desc[]" class="sst-textarea" placeholder="Descripción de la competencia"></textarea></td></tr>
                </tbody>
            </table>

            <div class="section-title">Autoridad</div>
            <div class="box text-just">
                <textarea name="autoridad_final" class="sst-textarea">Tiene autoridad para representar a la alta dirección en todos los temas del PESV.</textarea>
            </div>

            <div class="profile-title"><textarea name="p2_titulo" class="sst-textarea bold center">Perfil 2 - Editable</textarea></div>

            <table class="sst-table">
                <tbody>
                    <tr>
                        <th style="width:220px;">Nivel</th>
                        <td><textarea name="p2_nivel" class="sst-textarea">DIRECTIVO (A)</textarea></td>
                    </tr>
                    <tr>
                        <th>Denominación del empleo</th>
                        <td><textarea name="p2_denominacion" class="sst-textarea">PERFIL 2</textarea></td>
                    </tr>
                    <tr>
                        <th>Dependencia</th>
                        <td><textarea name="p2_dependencia" class="sst-textarea">PROFESIONAL</textarea></td>
                    </tr>
                    <tr>
                        <th>Propósito principal</th>
                        <td><textarea name="p2_proposito" class="sst-textarea" placeholder="Describa el propósito principal del cargo"></textarea></td>
                    </tr>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Funciones específicas - Perfil 2</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addTwoColRow('funciones-perfil2-body')">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="funciones-perfil2-body" data-add-func="addTwoColRow">
                    <?php for($i=1; $i<=5; $i++): ?>
                    <tr>
                        <td style="width:50px;" class="center"><strong><?php echo $i; ?></strong></td>
                        <td><textarea name="p2_func[]" class="sst-textarea" placeholder="Función específica perfil 2 - <?php echo $i; ?>"></textarea></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Responsabilidades SG-SST - Perfil 2</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addTwoColRow('sgsst-perfil2-body')">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="sgsst-perfil2-body" data-add-func="addTwoColRow">
                    <?php for($i=1; $i<=5; $i++): ?>
                    <tr>
                        <td style="width:50px;" class="center"><?php echo $i; ?></td>
                        <td><textarea name="p2_sgsst[]" class="sst-textarea" placeholder="Responsabilidad SG-SST perfil 2"></textarea></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="section-header no-print">
                <div class="section-title">Responsabilidades PESV - Perfil 2</div>
                <button type="button" class="btn btn-success btn-sm add-btn" onclick="addTwoColRow('pesv-perfil2-body')">Agregar fila</button>
            </div>
            <table class="sst-table">
                <tbody id="pesv-perfil2-body" data-add-func="addTwoColRow">
                    <?php for($i=1; $i<=5; $i++): ?>
                    <tr>
                        <td style="width:50px;" class="center"><?php echo $i; ?></td>
                        <td><textarea name="p2_pesv[]" class="sst-textarea" placeholder="Responsabilidad PESV perfil 2"></textarea></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="section-title">Aprobación</div>
            <div class="signature-grid">
                <div class="sign-box"><textarea name="firma_elaboro" class="sst-textarea center bold">ELABORÓ</textarea></div>
                <div class="sign-box"><textarea name="firma_reviso" class="sst-textarea center bold">REVISÓ</textarea></div>
                <div class="sign-box"><textarea name="firma_aprobo" class="sst-textarea center bold">APROBÓ</textarea></div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. UTILIDADES DE REDIMENSIÓN
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    function bindAutoResize(root = document) {
        const textareas = root.querySelectorAll('.sst-textarea');
        textareas.forEach(function (el) {
            autoResize(el);
            if (!el.dataset.bound) {
                el.addEventListener('input', function () { autoResize(el); });
                el.dataset.bound = '1';
            }
        });
    }

    // 2. FUNCIONES DE AGREGAR FILAS DINÁMICAS
    function renumberTwoColRows(tbodyId) {
        const rows = document.querySelectorAll('#' + tbodyId + ' tr');
        rows.forEach((row, index) => {
            const firstCell = row.children[0];
            if (firstCell) firstCell.textContent = index + 1;
        });
    }

    function addTwoColRow(tbodyId) {
        let inputName = "dinamico[]";
        if(tbodyId === 'funciones-especificas-body') inputName = "p1_func[]";
        if(tbodyId === 'responsabilidades-sgsst-body') inputName = "p1_sgsst[]";
        if(tbodyId === 'responsabilidades-pesv-body') inputName = "p1_pesv[]";
        if(tbodyId === 'funciones-perfil2-body') inputName = "p2_func[]";
        if(tbodyId === 'sgsst-perfil2-body') inputName = "p2_sgsst[]";
        if(tbodyId === 'pesv-perfil2-body') inputName = "p2_pesv[]";

        const tbody = document.getElementById(tbodyId);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="width:50px;" class="center"></td>
            <td><textarea name="${inputName}" class="sst-textarea"></textarea></td>
        `;
        tbody.appendChild(tr);
        renumberTwoColRows(tbodyId);
        bindAutoResize(tr);
    }

    function addControlDocumentos() {
        const tbody = document.getElementById('control-documentos-body');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><textarea name="ctrl_doc_elaboro[]" class="sst-textarea" placeholder="Nombre, cargo o firma"></textarea></td>
            <td><textarea name="ctrl_doc_reviso[]" class="sst-textarea" placeholder="Nombre, cargo o firma"></textarea></td>
            <td><textarea name="ctrl_doc_aprobo[]" class="sst-textarea" placeholder="Nombre, cargo o firma"></textarea></td>
        `;
        tbody.appendChild(tr);
        bindAutoResize(tr);
    }

    function addControlCambios() {
        const tbody = document.getElementById('control-cambios-body');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><textarea name="ctrl_cam_rev[]" class="sst-textarea center"></textarea></td>
            <td><textarea name="ctrl_cam_fecha[]" class="sst-textarea"></textarea></td>
            <td><textarea name="ctrl_cam_desc[]" class="sst-textarea"></textarea></td>
        `;
        tbody.appendChild(tr);
        bindAutoResize(tr);
    }

    function addContenido() {
        const tbody = document.getElementById('contenido-body');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><textarea name="cont_tema[]" class="sst-textarea"></textarea></td>
            <td style="width:90px;"><textarea name="cont_pag[]" class="sst-textarea center"></textarea></td>
        `;
        tbody.appendChild(tr);
        bindAutoResize(tr);
    }

    function addDefinicion() {
        const tbody = document.getElementById('definiciones-body');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="width:180px;"><textarea name="def_concepto[]" class="sst-textarea bold"></textarea></td>
            <td><textarea name="def_desc[]" class="sst-textarea"></textarea></td>
        `;
        tbody.appendChild(tr);
        bindAutoResize(tr);
    }

    function addCondicionGeneral() {
        const ul = document.getElementById('condiciones-generales-list');
        const li = document.createElement('li');
        li.innerHTML = `<textarea name="condicion_gen[]" class="sst-textarea"></textarea>`;
        ul.appendChild(li);
        bindAutoResize(li);
    }

    function addCompetencia() {
        const tbody = document.getElementById('competencias-body');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><textarea name="comp_cat[]" class="sst-textarea bold"></textarea></td>
            <td><textarea name="comp_desc[]" class="sst-textarea"></textarea></td>
        `;
        tbody.appendChild(tr);
        bindAutoResize(tr);
    }

    // 3. INYECCIÓN INTELIGENTE DE DATOS
    document.addEventListener('DOMContentLoaded', function () {
        bindAutoResize();
        ['funciones-especificas-body', 'responsabilidades-sgsst-body', 'responsabilidades-pesv-body', 'funciones-perfil2-body', 'sgsst-perfil2-body', 'pesv-perfil2-body'].forEach(renumberTwoColRows);

        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        <?php if(isset($errorCarga)) echo "console.warn('Advertencia API:', " . json_encode($errorCarga) . ");"; ?>

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                
                if (Array.isArray(value)) {
                    let campos = document.querySelectorAll(`[name="${key}[]"]`);
                    
                    if (campos.length > 0 && campos.length < value.length) {
                        const tbody = campos[0].closest('tbody') || campos[0].closest('ul');
                        const funcName = tbody ? tbody.dataset.addFunc : null;
                        
                        while(document.querySelectorAll(`[name="${key}[]"]`).length < value.length && funcName && window[funcName]) {
                            window[funcName](tbody.id);
                        }
                        campos = document.querySelectorAll(`[name="${key}[]"]`);
                    }

                    value.forEach((val, i) => {
                        if (campos[i]) {
                            campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                            if(campos[i].tagName === 'TEXTAREA') autoResize(campos[i]);
                        }
                    });

                } else {
                    const campo = document.querySelector(`[name="${key}"]`);
                    if (campo) {
                        campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                        if(campo.tagName === 'TEXTAREA') autoResize(campo);
                    }
                }
            }
        }
    });

    // 4. GUARDAR DATOS
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

            if (result.ok) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Configuración guardada correctamente',
                    icon: 'success',
                    confirmButtonColor: '#1fa339'
                });
            } else {
                Swal.fire({
                    title: 'Error al guardar',
                    text: result.error || "No se pudo completar la operación.",
                    icon: 'error',
                    confirmButtonColor: '#004176'
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo contactar al servidor.',
                icon: 'error',
                confirmButtonColor: '#004176'
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