<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN A LA API
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu BD (ej: 50 para Procedimiento Investigación)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 50; 

// --- Lógica de Empresa (Logo y Datos desde tu API) ---
$logoEmpresaUrl = "";
$nombreEmpresaDefault = "NOMBRE DE LA EMPRESA"; 

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        
        if (!empty($empData['nombre_empresa'])) $nombreEmpresaDefault = $empData['nombre_empresa'];
        if (!empty($empData['logo_url'])) $logoEmpresaUrl = $empData['logo_url'];
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE (Formatos Dinámicos)
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? $resFormulario['campos'] ?? null;

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

// 3. FUNCIÓN PARA LEER DATOS (Sobreescrita para leer del JSON de la API)
function old_val($key, $default = '') {
    global $datosCampos;
    if (isset($datosCampos[$key]) && $datosCampos[$key] !== '') {
        return htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars((string)$default, ENT_QUOTES, 'UTF-8');
}

// Generar fecha actual por defecto
$meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
$fechaDefault = date('d') . " de " . $meses[date('n')-1] . " de " . date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.2.1 - Procedimiento de Investigación de Accidentes e Incidentes</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f2f4f7;
            padding:20px;
            color:#111;
        }

        .contenedor{
            max-width:1200px;
            margin:0 auto;
            background:#fff;
            border:1px solid #bfc7d1;
            box-shadow:0 4px 18px rgba(0,0,0,.08);
        }

        .toolbar{
            position:sticky;
            top:0;
            z-index:100;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:12px;
            padding:14px 18px;
            background:#dde7f5;
            border-bottom:1px solid #c8d3e2;
        }

        .toolbar h1{
            font-size:19px;
            color:#213b67;
            font-weight:700;
        }

        .acciones{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn{
            border:none;
            padding:10px 18px;
            border-radius:8px;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            transition:.2s ease;
        }

        .btn:hover{
            transform:translateY(-1px);
            opacity:.95;
        }

        .btn-guardar{ background:#198754; color:#fff; }
        .btn-atras{ background:#6c757d; color:#fff; }
        .btn-imprimir{ background:#0d6efd; color:#fff; }

        .formulario{
            padding:18px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .encabezado td, .encabezado th{
            border:1px solid #6b6b6b;
            padding:10px;
            text-align:center;
            vertical-align:middle;
        }

        .logo-box{
            width:140px;
            height:65px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            color:#999;
            font-weight:bold;
            font-size:14px;
            text-align:center;
        }

        .titulo-principal{
            font-size:16px;
            font-weight:700;
        }

        .subtitulo{
            font-size:14px;
        }

        .portada{
            margin-top:20px;
            border:1px solid #cfd7e3;
            padding:40px 30px;
            text-align:center;
        }

        .portada h2{
            font-size:26px;
            margin-bottom:28px;
            color:#1f3b68;
        }

        .portada h3{
            font-size:22px;
            margin-bottom:20px;
        }

        .portada .empresa{
            margin-top:60px;
            font-size:18px;
            font-weight:700;
        }

        .portada .fecha{
            margin-top:10px;
            font-size:16px;
        }

        .input-portada{
            border:none !important;
            border-bottom:1px solid #999 !important;
            text-align:center;
            font-size:18px !important;
            font-weight:700;
            width:320px !important;
            background:transparent;
            outline:none;
            padding:4px 6px;
        }

        .input-portada-fecha{
            border:none !important;
            border-bottom:1px solid #999 !important;
            text-align:center;
            font-size:16px !important;
            width:260px !important;
            background:transparent;
            outline:none;
            padding:4px 6px;
        }
        
        .input-portada:focus, .input-portada-fecha:focus, .campo textarea:focus {
            background: #f8fbff;
        }

        .toc,
        .seccion{
            margin-top:24px;
        }

        .seccion h3,
        .toc h3{
            font-size:18px;
            margin-bottom:12px;
            color:#213b67;
            border-bottom:2px solid #d9e2f2;
            padding-bottom:8px;
        }

        .toc ul{
            list-style:none;
            padding-left:0;
        }

        .toc li{
            padding:6px 0;
            border-bottom:1px dashed #d6d6d6;
            font-size:14px;
        }

        .campo{
            margin-bottom:14px;
        }

        .campo label{
            display:block;
            font-weight:700;
            margin-bottom:6px;
            font-size:14px;
        }

        .campo input,
        .campo textarea{
            width:100%;
            padding:10px;
            border:1px solid #b7b7b7;
            border-radius:6px;
            outline:none;
            font-size:14px;
            line-height:1.5;
            background: transparent;
        }

        .campo input{
            min-height:44px;
        }

        .campo textarea{
            resize:none;
            overflow:hidden;
            min-height:140px;
            white-space:pre-wrap;
            word-break:break-word;
        }

        .campo textarea.alta{
            min-height:260px;
        }

        .campo textarea.media{
            min-height:190px;
        }

        .campo textarea.baja{
            min-height:90px;
        }

        .flujo-box{
            border:1px solid #cfd7e3;
            background:#fafcff;
            padding:18px;
            border-radius:10px;
        }

        .flujo{
            display:grid;
            grid-template-columns:repeat(2, 1fr);
            gap:14px;
        }

        .flujo-item{
            border:1px solid #bfcce0;
            background:#fff;
            border-radius:10px;
            padding:14px;
            font-size:14px;
            line-height:1.4;
            min-height:82px;
        }

        .nota{
            margin-top:18px;
            padding:12px;
            border:1px solid #d9d9d9;
            background:#fafafa;
            font-size:13px;
            line-height:1.5;
            font-weight:700;
        }

        .footer-info{
            margin-top:26px;
            text-align:center;
            font-size:13px;
            color:#444;
            border-top:2px solid #2d57ff;
            padding-top:12px;
        }

        @media (max-width: 900px){
            .flujo{
                grid-template-columns:1fr;
            }
        }

        @media print{
            body{
                background:#fff;
                padding:0;
            }

            .toolbar, .print-hide{
                display:none !important;
            }

            .contenedor{
                box-shadow:none;
                border:none;
            }

            .formulario{
                padding:8px;
            }

            .portada{
                border:none;
            }

            .campo input,
            .campo textarea, .input-portada, .input-portada-fecha{
                border:none !important;
                padding:4px 0;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>3.2.1 - Procedimiento de Investigación de Accidentes e Incidentes</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Procedimiento</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
    </div>

    <div class="formulario">

        <form id="form321">

            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">Versión: 0</td>
                </tr>
                <tr>
                    <td class="subtitulo">PROCEDIMIENTO DE INVESTIGACIÓN DE ACCIDENTES E INCIDENTES</td>
                    <td style="font-weight:700;">AN-XX-SST-19<br>Fecha: <?= date('d/m/Y') ?></td>
                </tr>
            </table>

            <div class="portada">
                <h2>PROCEDIMIENTO DE INVESTIGACIÓN DE ACCIDENTES E INCIDENTES</h2>
                <h3>Versión 0</h3>
                <div class="empresa">
                    <input type="text" class="input-portada" name="empresa" value="<?= old_val('empresa', $nombreEmpresaDefault) ?>">
                </div>
                <div class="fecha">
                    <input type="text" class="input-portada-fecha" name="fecha_portada" value="<?= old_val('fecha_portada', $fechaDefault) ?>">
                </div>
            </div>

            <div class="toc print-hide">
                <h3>Contenido</h3>
                <ul>
                    <li>1. Objetivo</li>
                    <li>2. Alcance</li>
                    <li>3. Responsables</li>
                    <li>4. Glosario</li>
                    <li>5. Procedimiento</li>
                    <li>5.1 Generalidades</li>
                    <li>5.1.1 Accidentes e Incidentes</li>
                    <li>5.2 Actividades</li>
                    <li>5.2.1 Reporte de Accidentes y Casi Accidentes</li>
                    <li>5.2.2 Investigación de Accidentes y Casi Accidentes</li>
                    <li>5.3 Análisis de causalidad</li>
                    <li>5.3.1 Informe de investigación</li>
                    <li>Anexo: Explicación Método TASC</li>
                </ul>
            </div>

            <div class="seccion">
                <h3>1. Objetivo</h3>
                <div class="campo">
                    <textarea class="baja" name="objetivo"><?= old_val('objetivo', 'Establecer los pasos a seguir para el reporte, investigación y análisis de las causas de accidentes y casi accidentes, para definir las acciones a adelantar en la toma de acciones correctivas y preventivas con el propósito de evitar que los problemas y las desviaciones (reales o potenciales) se repitan al interior de la organización.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>2. Alcance</h3>
                <div class="campo">
                    <textarea class="baja" name="alcance"><?= old_val('alcance', 'La información descrita en este procedimiento aplica a la investigación y análisis de accidentes y casi accidentes que se presenten en la organización.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>3. Responsables</h3>
                <div class="campo">
                    <textarea class="baja" name="responsables"><?= old_val('responsables', 'El encargado del SGSST de la organización es el responsable de la implementación y divulgación de este procedimiento.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>4. Glosario</h3>
                <div class="campo">
                    <textarea class="alta" name="glosario"><?= old_val('glosario', "Accidente: evento no deseado que da lugar a muerte, enfermedad, lesión, daño u otra pérdida.\nAccidente de trabajo: es todo suceso repentino que sobrevenga por causa o con ocasión del trabajo y que produzca en el trabajador una lesión orgánica, una perturbación funcional, una invalidez o la muerte.\nIncidente: evento que generó un accidente o que tuvo el potencial de llegar a ser un accidente, sin que ocurra enfermedad, lesión, daño u otra pérdida.\nEnfermedad profesional: es el estado patológico contraído con ocasión del trabajo o exposición del medio ambiente en que el trabajador se encuentra sometido.\nRiesgo de trabajo: probabilidad de ocurrencia de un accidente de trabajo.\nPérdida: gasto innecesario de cualquier recurso.\nAdministración del Control de Pérdidas: es la aplicación de las habilidades administrativas profesionales al control de las pérdidas de los riesgos del negocio.\nAcción Correctiva: toda medida o acción tomada para eliminar la causa de una no conformidad detectada u otra situación indeseable.\nAcción Directa o Inmediata: medida tomada para suprimir las causas inmediatas de un desvío, no así su causa raíz.\nCausa Raíz o del Sistema: razón básica para un problema que, si se hubiese eliminado o corregido, habría impedido que ocurriera.") ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5. Procedimiento</h3>
                <div class="campo">
                    <textarea class="baja" name="procedimiento_intro"><?= old_val('procedimiento_intro', 'Adicional a este procedimiento se deben diligenciar los formatos y procedimientos suministrados por la Administradora de Riesgos Laborales. Para el análisis de causas se utilizará la metodología TASC.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.1 Generalidades</h3>
                <div class="campo">
                    <textarea class="media" name="generalidades"><?= old_val('generalidades', 'Los reportes internos de los eventos ocurridos deben hacerse máximo después de 24 horas hábiles de sucedido este en el formato correspondiente o el suministrado por el cliente. Para accidente de trabajo el reporte a la Administradora de Riesgos Laborales (ARL) se realiza dentro de los dos días hábiles siguientes al evento en el formato suministrado por la misma.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.1.1 Accidentes e Incidentes</h3>
                <div class="campo">
                    <textarea class="alta" name="accidentes_incidentes"><?= old_val('accidentes_incidentes', "• Todos los incidentes y accidentes de trabajo se deben investigar dentro de los quince (15) días siguientes a su ocurrencia del evento, a través del equipo investigador.\n• Remitir a la Administradora de Riesgos Laborales, dentro de los quince (15) días siguientes a la ocurrencia del evento, el informe de investigación del accidente de trabajo mortal y de los accidentes graves.\n• Equipo Investigador: El equipo investigador se conforma de acuerdo a la gravedad del evento:\na) Jefe inmediato o supervisor del trabajador accidentado o del área donde ocurrió el accidente.\nb) Un representante del COPASST.\nc) Encargado del desarrollo del SG SST.\nd) Cuando se estime necesario, representante de la ARL.\ne) Cuando el accidente se considere grave o produzca la muerte, deberá participar un profesional con licencia en salud ocupacional propio o contratado, así como el personal de la empresa encargado del diseño de normas, procesos y/o mantenimiento.") ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.2 Actividades</h3>
                <div class="campo">
                    <textarea class="baja" name="actividades_intro"><?= old_val('actividades_intro', 'Las actividades del presente procedimiento comprenden el reporte, la recopilación de información, la investigación del evento, el análisis de causalidad y la definición de acciones correctivas y preventivas.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.2.1 Reporte de Accidentes y Casi Accidentes</h3>
                <div class="campo">
                    <textarea class="media" name="reporte_accidentes"><?= old_val('reporte_accidentes', 'Si la situación corresponde a un accidente se aplican las acciones iniciales: dar los primeros auxilios, prevenir accidentes secundarios y, si aplica, poner en funcionamiento el Plan de Emergencia, además de reportar el hecho a la Administradora de Riesgos Laborales.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.2.2 Investigación de Accidentes y Casi Accidentes</h3>
                <div class="campo">
                    <textarea class="alta" name="investigacion_accidentes"><?= old_val('investigacion_accidentes', "Recopilación de Información.\n\nRevisión Documentación: Los procedimientos de trabajo, las normas de seguridad, los registros de mantenimiento y de capacitación sobre el riesgo son documentos que dan luces sobre el estado y manejo de los equipos, procesos e instalaciones existentes en el área o zona del incidente.\n\nReconocimiento del Área: El equipo investigador es responsable de realizar una inspección al sitio donde ocurrió el accidente, a menos que las condiciones no lo permitan. En la inspección se debe:\n• Tomar registros (fotografías y/o filmaciones) si las circunstancias y autoridades competentes lo permiten.\n• Verificar que la ubicación de controles, equipos, herramientas y elementos de protección corresponde a lo establecido en los procedimientos de la organización.\n\nEntrevistas:\n• Aclarar al entrevistado que el propósito de la investigación es identificar las causas.\n• Realizar la entrevista en forma individual.\n• Escuchar atentamente al entrevistado.\n• Evitar el uso de grabadoras.\n• Preguntar sobre el trabajo que se estaba realizando y el procedimiento seguido.\n• Indagar si conoce procedimientos escritos o prácticos para la ejecución de la labor.\n• Hacer reconstrucción de los hechos cuando sea posible.") ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.3 Análisis de causalidad</h3>
                <div class="campo">
                    <textarea class="media" name="analisis_causalidad"><?= old_val('analisis_causalidad', 'El grupo investigador determina la(s) causa(s) inmediatas y básica(s) del evento, para ello utilizará la metodología TASC.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5.3.1 Informe de investigación</h3>
                <div class="campo">
                    <textarea class="alta" name="informe_investigacion"><?= old_val('informe_investigacion', "Es responsabilidad del equipo investigador generar el informe de investigación. El informe del evento se registra en el formato correspondiente de la compañía e incluye como mínimo:\n\n• Datos generales del evento: fecha, hora, lugar, fecha del reporte y tipo de evento.\n• Detalles del empleado: nombre, identificación, edad, cargo, antigüedad en el cargo y en la empresa.\n• Descripción del evento: qué trabajo se adelantaba, cómo se hacía y cómo se presentaron los hechos.\n• Descripción de la lesión o pérdidas: humanas, materiales, al proceso, equipos, ambiente o terceros.\n• Antecedentes y observaciones: visita al sitio, entrevistas, análisis de reportes, manuales y procedimientos.\n• Análisis de causalidad: causas básicas e inmediatas usando metodología TASC.\n• Acciones preventivas y correctivas.\n• Equipo investigador: nombre, cargo, identificación y firma de quienes participaron.") ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>Flujograma general del procedimiento</h3>
                <div class="flujo-box">
                    <div class="flujo">
                        <div class="flujo-item">1. Ocurre el accidente, incidente o casi accidente.</div>
                        <div class="flujo-item">2. Se reporta internamente el evento dentro de los tiempos definidos.</div>
                        <div class="flujo-item">3. Se brinda atención inicial y, si aplica, primeros auxilios y activación del plan de emergencias.</div>
                        <div class="flujo-item">4. Se reporta a la ARL cuando corresponda.</div>
                        <div class="flujo-item">5. Se conforma el equipo investigador.</div>
                        <div class="flujo-item">6. Se recopila la información: documentos, inspección del área, entrevistas y evidencias.</div>
                        <div class="flujo-item">7. Se analizan causas inmediatas y básicas mediante TASC.</div>
                        <div class="flujo-item">8. Se genera el informe de investigación.</div>
                        <div class="flujo-item">9. Se definen acciones correctivas y preventivas.</div>
                        <div class="flujo-item">10. Se realiza seguimiento al cumplimiento de las acciones.</div>
                    </div>
                </div>
            </div>

            <div class="seccion">
                <h3>Anexo: Explicación Método TASC</h3>
                <div class="campo">
                    <textarea class="alta" name="metodo_tasc"><?= old_val('metodo_tasc', "Este método, también llamado de “Análisis de la Cadena Causal”, está basado en el modelo causal de pérdidas. Para efectuar el análisis de causalidad, se parte de la pérdida o lesión ocasionada por el accidente que se investiga y se asciende lógica y cronológicamente a través de la cadena causal.\n\nLa secuencia de aplicación de la metodología TASC para un accidente de trabajo es la siguiente:\na) Estipulación de las lesiones producidas por el accidente.\nb) Estipulación de los contactos con energías o sustancias que causaron el accidente.\nc) Determinación de las causas inmediatas o directas (actos y/o condiciones inseguras).\nd) Determinación de las causas básicas o raíz (factores personales y factores del trabajo).\ne) Determinación de las causas relacionadas con la falta de control administrativo o fallas del sistema de gestión de seguridad y salud en el trabajo.") ?></textarea>
                </div>
            </div>

            <div class="nota print-hide">
                Este archivo se estructuró a partir del contenido del documento cargado para el formato 3.2.1, que corresponde al procedimiento de investigación de accidentes e incidentes y al uso de la metodología TASC.
            </div>

            <div class="footer-info">
                Generado por Sistema de Gestión - <?= $nombreEmpresaDefault ?>
            </div>

        </form>
    </div>
</div>

<script>
function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

function autoResizeInput(el) {
    const temp = document.createElement('span');
    const styles = window.getComputedStyle(el);

    temp.style.position = 'absolute';
    temp.style.visibility = 'hidden';
    temp.style.whiteSpace = 'pre';
    temp.style.font = styles.font;
    temp.style.fontSize = styles.fontSize;
    temp.style.fontWeight = styles.fontWeight;
    temp.style.letterSpacing = styles.letterSpacing;
    temp.textContent = el.value || el.placeholder || '';

    document.body.appendChild(temp);
    const newWidth = Math.max(temp.offsetWidth + 20, 120);
    el.style.width = newWidth + 'px';
    document.body.removeChild(temp);
}

document.addEventListener('DOMContentLoaded', function () {
    const textareas = document.querySelectorAll('textarea');
    const autoInputs = document.querySelectorAll('.input-portada, .input-portada-fecha');

    textareas.forEach(textarea => {
        autoResizeTextarea(textarea);
        textarea.addEventListener('input', function () {
            autoResizeTextarea(this);
        });
    });

    autoInputs.forEach(input => {
        autoResizeInput(input);
        input.addEventListener('input', function () {
            autoResizeInput(this);
        });
    });
});

// Guardado del formulario vía Fetch
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form321');
    const formData = new FormData(form);
    
    // Construir el objeto JSON
    const datosJSON = Object.fromEntries(formData.entries());

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
                text: 'El procedimiento ha sido guardado correctamente.',
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