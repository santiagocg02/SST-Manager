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
// Ajusta el ID de este ítem según tu base de datos (Ej: 39 para Gestión del Cambio)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 39; 

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
    <title>AN-XX-SST-09 | Procedimiento de Gestión del Cambio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --line:#111;
            --blue:#cfdcf3;
            --blue-dark:#9fb6dc;
            --soft:#f5f7fb;
            --text:#1a1a1a;
            --muted:#666;
            --page-bg:#eef2f7;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
            --green:#198754;
            --green-hover:#146c43;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--page-bg);
            font-family: Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .page-wrap{
            padding:20px;
            max-width: 1100px;
            margin: 0 auto 60px;
        }

        .toolbar{
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

        .paper{
            width:100%;
            background:#fff;
            border:1px solid #d8dee8;
            box-shadow:0 10px 30px rgba(0,0,0,.07);
            padding:24px;
            border-radius: 8px;
        }

        .doc-header{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-bottom:20px;
        }

        .doc-header td{
            border:1px solid var(--line);
            padding:8px 10px;
            vertical-align:middle;
        }

        .logo-cell{
            width:22%;
            text-align:center;
            font-weight:700;
            color:#5b5b5b;
            background:#fafafa;
        }

        .main-title{
            width:66%;
            text-align:center;
            font-weight:700;
            font-size:15px;
            line-height:1.35;
        }

        .meta-cell{
            width:12%;
            text-align:center;
            font-weight:700;
            padding:0 !important;
        }

        .meta-box{
            display:flex;
            flex-direction:column;
            height:100%;
            min-height:84px;
        }

        .meta-box div, .meta-box input{
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:6px 4px;
            border-bottom:1px solid var(--line);
            width: 100%;
            border-top: none;
            border-left: none;
            border-right: none;
            outline: none;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            background: transparent;
        }

        .meta-box input:last-child, .meta-box div:last-child{
            border-bottom:none;
        }

        .doc-title{
            text-align:center;
            font-size:24px;
            font-weight:800;
            letter-spacing:.3px;
            margin:14px 0 6px;
            text-transform:uppercase;
        }

        .doc-version{
            text-align:center;
            color:var(--muted);
            font-size:13px;
            margin-bottom:18px;
        }

        .company-box{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-bottom:24px;
        }

        .company-box td{
            border:1px solid var(--line);
            padding:10px 12px;
            height:44px;
        }

        .label{
            width:18%;
            background:var(--blue);
            font-weight:700;
        }

        .field{
            background:#fff;
        }

        .input-line{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:14px;
        }

        .section{
            margin-bottom:20px;
        }

        .section-title{
            background:var(--blue);
            border:1px solid var(--line);
            padding:10px 12px;
            font-weight:800;
            text-transform:uppercase;
            font-size:14px;
            letter-spacing:.2px;
        }

        .section-body{
            border:1px solid var(--line);
            border-top:none;
            padding:14px 16px;
            font-size:14px;
            line-height:1.65;
            text-align:justify;
            background: #fff;
        }

        .section-body textarea {
            width: 100%;
            min-height: 80px;
            border: none;
            outline: none;
            resize: vertical;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.65;
            background: transparent;
        }

        .section-body textarea:focus {
            background: #f8fbff;
        }

        .sub-title{
            font-weight:800;
            margin:16px 0 8px;
            font-size:14px;
            text-transform:uppercase;
            color:#223a66;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:18px;
            margin-top:34px;
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

        .footer-note{
            margin-top:28px;
            text-align:center;
            color:#666;
            font-size:12px;
        }

        @media print{
            body{ background:#fff; margin:0; }
            .page-wrap{ padding:0; max-width: 100%; }
            .toolbar, .print-hide { display:none !important; }
            .paper{ max-width:100%; box-shadow:none; border:none; margin:0; padding:0; }
            @page{ size: letter; margin: 12mm; }
            .section-body textarea { background: transparent !important; }
        }

        @media (max-width: 768px){
            .page-wrap{ padding:12px; }
            .paper{ padding:14px; }
            .doc-title{ font-size:18px; }
            .main-title{ font-size:13px; }
            .section-body{ font-size:13px; }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="page-wrap">
    
    <div class="toolbar print-hide">
        <div class="topbar-left" style="display:flex; gap:8px;">
            <button class="btn-ui secondary" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-ui secondary" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-ui success" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-ui" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
        <div class="topbar-right text-end">
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">GESTIÓN DEL CAMBIO</span><br>
            <span style="font-size: 11px; color: #6b7280; font-weight: 700;">Usuario: <?= e($_SESSION["usuario"] ?? "Usuario") ?></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="paper">

            <table class="doc-header">
                <tr>
                    <td class="logo-cell" style="<?= empty($logoEmpresaUrl) ? '' : 'border:1px solid #111; padding:0; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain; display: block; margin: auto;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </td>
                    <td class="main-title">
                        SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                    </td>
                    <td class="meta-cell">
                        <div class="meta-box">
                            <input type="text" name="meta_version" value="0" title="Versión">
                            <input type="text" name="meta_codigo" value="AN-XX-SST-09" title="Código">
                            <input type="date" name="meta_fecha" id="metaFecha" title="Fecha">
                        </div>
                    </td>
                </tr>
            </table>

            <div class="doc-title">PROCEDIMIENTO DE GESTIÓN DEL CAMBIO</div>
            <div class="doc-version">
                Versión <input type="text" name="doc_version_txt" value="0" style="width: 30px; border:none; text-align:center; font-weight:bold; color:inherit; background:transparent;">
            </div>

            <table class="company-box">
                <tr>
                    <td class="label">NOMBRE DE LA EMPRESA</td>
                    <td class="field">
                        <input type="text" name="nombre_empresa" class="input-line" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
                    </td>
                    <td class="label">FECHA</td>
                    <td class="field">
                        <input type="date" name="fecha_doc" id="fechaDoc" class="input-line">
                    </td>
                </tr>
            </table>

            <section class="section">
                <div class="section-title">Objetivo</div>
                <div class="section-body">
                    <textarea name="txt_objetivo" rows="2">Manejar y controlar de forma oportuna cualquier cambio que pueda afectar los procesos de la compañía tanto en sus peligros y riesgos.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Alcance</div>
                <div class="section-body">
                    <textarea name="txt_alcance" rows="3">Este procedimiento aplica para todos los cambios de operación, sistemas de gestión, actividades, materiales, equipos, procedimientos, servicios y productos, que afecten la Seguridad y Salud en el Trabajo, de tal manera que sean identificados y valorados, determinando las acciones y controles a implementar antes de que se ejecuten los cambios.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Responsabilidad</div>
                <div class="section-body">
                    <textarea name="txt_responsabilidad" rows="9">- Gerente: Aprueba los cambios y la documentación pertinente.
- Encargado del SG-SST: Redacta los documentos involucrados, los formatos y los revisa; así mismo, consolida la información de los cambios requeridos.
- Jefes: Solicitan los cambios, los establecen, documentan e implementan una vez aprobados.

Nota:
Para cambios relacionados con infraestructura, el responsable de reportarlos será el encargado de infraestructura física.
Para cambios relacionados con tecnología, el responsable de reportarlos será el encargado de tecnología.
Para cambios relacionados con contratación, el responsable de reportarlos será el encargado de recursos humanos.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Definiciones</div>
                <div class="section-body">
                    <textarea name="txt_definiciones" rows="14">- Lugar de Trabajo: Cualquier espacio físico en el que se realizan actividades relacionadas con el trabajo, bajo el control de la organización.
- Personal: Conjunto de individuos que trabajan en el mismo lugar de trabajo, organismo o empresa.
- Sistema de Gestión SG-SST: Parte del sistema de gestión de una organización, empleada para desarrollar e implementar su política de seguridad y salud en el trabajo y gestionar sus riesgos.
- Modificaciones: Cambiar o variar en sus caracteres no esenciales una actividad o un proceso. Introducción de cambios que se consideren necesarios tanto en el fondo como en la forma de un documento.
- Actividades: Conjunto de tareas o acciones que se hacen con un fin determinado o son propias de una persona, una profesión o una entidad.
- Materiales y Equipos: Conjunto de máquinas, herramientas u objetos de cualquier clase, necesarios para el desempeño de un servicio o el ejercicio de una profesión.
- Contratistas: Persona o empresa que presta servicios a la compañía.
- Proveedor: Productor, distribuidor, minorista o vendedor de un producto, o prestador de un servicio o información. Puede ser interno o externo a la organización.
- Proceso: Conjunto de actividades mutuamente relacionadas o que interactúan, las cuales transforman elementos de entrada en resultados.
- Proyecto: Proceso único consistente en un conjunto de actividades coordinadas y controladas con fechas de inicio y finalización.
- Procedimiento: Forma especificada para llevar a cabo una actividad o un proceso.
- Documento: Información y sus medios de soporte.</textarea>
                </div>
            </section>

            <section class="section">
                <div class="section-title">Procedimiento</div>
                <div class="section-body">

                    <div class="sub-title">5.1 Identificación</div>
                    <textarea name="txt_identificacion" rows="9">Se identificarán con base en criterios como generación de nuevos procesos, identificación de nuevos requisitos legales, nuevos contratistas, proveedores, productos, maquinaria, tecnología, insumos, estructura organizacional, nuevos proyectos y licitaciones. Los responsables de esta identificación serán, entre otros, gerencias y directores de área.

Tipos de cambios:
- Infraestructura: Cambios en instalaciones, adecuaciones, modificaciones estructurales.
- Requisitos legales o contractuales: Generados a partir de la identificación y seguimiento legal.
- Cambios de personal, contratistas y proveedores: Nuevos terceros cuyo desempeño afecte el sistema.
- Creación de nuevos procesos: Modificación o ampliación del alcance de los servicios.
- Sistema de Gestión: Consecuencia de la revisión del sistema o resultados obtenidos.</textarea>

                    <div class="sub-title">Manejo</div>
                    <textarea name="txt_manejo" rows="7">Cuando se genere un cambio en cualquiera de las estructuras, equipos o procesos, los responsables de identificarlo deben diligenciar el formato de planificación de cambios, el cual deberá ser enviado vía correo o entregado físicamente al encargado del SG-SST para su estudio y aprobación conjunta con la Gerencia.

Una vez sea aceptado y aprobado, se ejecutará el proceso de gestión del cambio. Ninguna gestión de cambio se puede realizar si no ha sido aprobada.

Los cambios se realizarán mediante la identificación, evaluación y control de los peligros y la minimización de los impactos que se generen, aplicando la jerarquía de controles (eliminación, sustitución, ingeniería, administrativos, EPP).</textarea>

                    <div class="sub-title">Aplicación de controles</div>
                    <textarea name="txt_controles" rows="6">- Eliminación: Modificar un diseño, operación o equipo para eliminar el peligro.
- Sustitución: Reemplazar por un material menos peligroso o reducir la energía del sistema.
- Controles de ingeniería: Instalar sistemas de ventilación, protección para máquinas, enclavamientos, etc.
- Señalización, advertencias y/o controles administrativos: Alarmas, procedimientos de seguridad, inspecciones.
- Equipos de protección personal: Gafas de seguridad, protección auditiva, arneses, guantes, entre otros.</textarea>

                    <div class="sub-title">Proceso general</div>
                    <textarea name="txt_proceso" rows="8">De acuerdo con el tipo de cambio correspondiente a las operaciones de la compañía, el responsable de la identificación debe seguir los siguientes parámetros:

- Cambios en instalaciones: Utilice el formato de planificación de cambios y la matriz de identificación de peligros.
- Cambios en proveedores, contratistas o equipos especiales: Envíe la propuesta o borrador para revisión y aprobación por la Gerencia y el encargado del SG-SST.
- Cambios en la utilización de sustancias y materiales peligrosos: Utilice el formato de planificación de cambios.
- Cambios en personal: A todo el personal nuevo debe realizarse inducción al SG-SST y evaluación de su entendimiento.</textarea>

                    <div class="sub-title">Revisión y aprobación</div>
                    <textarea name="txt_revision" rows="4">De acuerdo con el cambio que se establezca, deberá realizarse la evaluación sobre si los controles existentes son suficientes o si se requieren controles adicionales, utilizando el documento de matriz de peligros.

Finalmente, deberán divulgarse los nuevos documentos y/o procedimientos a las partes interesadas correspondientes.</textarea>
                </div>
            </section>

            <div class="sign-grid print-hide">
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

            <div class="footer-note print-hide">
                Documento interno del Sistema de Gestión de Seguridad y Salud en el Trabajo. Haz clic en "Guardar Cambios" para almacenar este procedimiento.
            </div>

        </div>
    </form>
</div>

<script>
    // Poner fecha de hoy por defecto si están vacías
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        
        const fmeta = document.getElementById("metaFecha");
        if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;

        const fDoc = document.getElementById("fechaDoc");
        if (fDoc && !fDoc.value) fDoc.value = `${y}-${m}-${dd}`;
    }
    setHoy();

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
    // Ejecutamos al inicio
    setTimeout(autoResizeTextareas, 100);

    // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
    document.addEventListener('DOMContentLoaded', function () {
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                let campo = document.querySelector(`[name="${key}"]`);
                if (campo) {
                    campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                }
            }
            setTimeout(autoResizeTextareas, 200); // Reajustar tras poblar
        }
    });

    // --- LÓGICA DE GUARDADO ---
    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
        const formData = new FormData(form);
        const datosJSON = {};

        for (const [key, value] of formData.entries()) {
            datosJSON[key] = value;
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

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>