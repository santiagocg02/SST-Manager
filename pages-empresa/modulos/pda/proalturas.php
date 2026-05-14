<?php
session_start();

require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

// LOGO DINÁMICO
$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
$logoUrl = "";

if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("empresas/$empresaId", "GET", null, $token);
    $logoUrl = $resEmpresa['data']['logo_url'] ?? "";
}

$anioActual = date("Y");
?>

<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SSTMANAGER - PGTSA COMPLETO</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root{
    --primary:#0d4d8b;
    --primary-hover:#0a3b69;
    --bg:#f4f7fb;
    --border:#dbe4f0;
    --success:#198754;
    --danger:#dc3545;
    
    /* Colores del Excel */
    --header-teal: #008080;
    --header-orange: #e67e22;
    --header-light-orange: #f39c12;
    --header-green: #27ae60;
    --table-bg-gray: #f2f2f2;
}

*{ margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
body{ background:var(--bg); }
.main-page{ padding:20px; }

/* CONTENEDOR PRINCIPAL TIPO DOCUMENTO */
.doc-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 30px;
    margin: 0 auto;
    max-width: 1400px;
}

/* ENCABEZADO DEL DOCUMENTO */
.doc-header {
    display: grid;
    grid-template-columns: 200px 1fr 200px;
    border: 2px solid #000;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
    font-size: 14px;
}
.doc-header > div { border-right: 2px solid #000; padding: 10px; display: flex; align-items: center; justify-content: center; }
.doc-header > div:last-child { border-right: none; }
.doc-header-logo img { max-width: 100%; max-height: 80px; object-fit: contain; }
.doc-header-info { display: flex; flex-direction: column; text-align: left; font-size: 12px; gap: 4px; }

/* SECCIONES TABLAS */
.section-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
.section-table th, .section-table td { border: 1px solid #000; padding: 6px; vertical-align: top; }
.section-title { color: #fff; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 12px; }

.bg-teal { background-color: var(--header-teal) !important; color: white; }
.bg-orange { background-color: var(--header-orange) !important; color: white; }
.bg-light-orange { background-color: var(--header-light-orange) !important; color: white; }
.bg-green { background-color: var(--header-green) !important; color: white; }
.bg-gray { background-color: var(--table-bg-gray) !important; font-weight: bold;}

.editable-div {
    width: 100%; min-height: 20px; outline: none; background: transparent; border: none;
}
.editable-div:focus { background: #fdfdfd; box-shadow: inset 0 0 0 1px var(--primary); }

/* TABLA CRONOGRAMA ESPECÍFICA */
.table-crono { width: 100%; border-collapse: collapse; font-size: 10px; text-align: center; }
.table-crono th, .table-crono td { border: 1px solid #000; padding: 2px; vertical-align: middle; }
.table-crono th { background-color: var(--header-green); color: white; font-size: 11px; }
.col-fase { width: 30px; font-weight: bold; font-size: 14px; }
.fase-p { background: #d9edf7; color: #31708f; }
.fase-h { background: #dff0d8; color: #3c763d; }
.fase-v { background: #fcf8e3; color: #8a6d3b; }
.fase-a { background: #f2dede; color: #a94442; }

.col-actividad { min-width: 280px; text-align: left; padding-left: 5px !important; }
.col-resp { min-width: 100px; }
.col-mes { width: 35px; }

.pe-box { display: flex; flex-direction: column; align-items: center; gap: 1px; }
.pe-row { display: flex; align-items: center; gap: 2px; font-size: 8px; font-weight: bold; }
.chk-p, .chk-e { width: 11px; height: 11px; cursor: pointer; margin: 0;}
.chk-p { accent-color: #0d4d8b; }
.chk-e { accent-color: #198754; }

/* BOTONES FLOTANTES / CONTROLES */
.controls-bar {
    position: sticky; top: 0; background: rgba(255,255,255,0.95); padding: 15px;
    border-bottom: 1px solid var(--border); margin-bottom: 20px; z-index: 100;
    display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.btn-sys {
    border: none; border-radius: 8px; padding: 8px 16px; font-weight: bold; font-size: 13px; color: #fff; cursor: pointer;
}
.btn-save { background: var(--primary); }
.btn-print { background: #6c757d; }
.btn-add { background: var(--header-green); padding: 4px 8px; font-size: 11px; margin-bottom: 5px; }

/* GRÁFICOS CONTAINER */
.charts-container { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 30px; }
.chart-box { background: #fff; border: 1px solid #000; padding: 15px; }

@media print {
    @page { size: landscape; margin: 10mm; }
    body { background: #fff; }
    .controls-bar, .no-print { display: none !important; }
    .doc-container { box-shadow: none; padding: 0; width: 100%; max-width: none; }
    .editable-div { border: none !important; }
    /* Ajustes para que quepa en una hoja */
    .section-table { font-size: 9px; margin-bottom: 10px; }
    .table-crono { font-size: 8px; }
    .table-crono th { font-size: 9px; }
    .chart-box { padding: 10px; }
}
</style>
</head>
<body>

<div class="main-page">

    <div class="doc-container">
        <div class="controls-bar no-print">
        <div class="d-flex align-items-center gap-3">
            <h4 class="m-0 fw-bold" style="color:var(--primary);">SSTManager</h4>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-sys btn-save" onclick="alert('Guardado en base de datos')"><i class="fa-solid fa-save me-2"></i>Guardar PGTSA</button>
            <button class="btn-sys btn-print" onclick="window.print()"><i class="fa-solid fa-print me-2"></i>Imprimir / PDF</button>
        </div>
    </div>
        <div class="doc-header">
            <div class="doc-header-logo">
                <?php if ($logoUrl): ?>
                    <img src="<?= $logoUrl ?>" alt="Logo">
                <?php else: ?>
                    <span style="color:#b0b7c3;">LOGOTIPO EMPRESA</span>
                <?php endif; ?>
            </div>
            <div style="font-size: 16px; color: var(--primary);">
                PROGRAMA DE GESTIÓN DE TRABAJO SEGURO EN ALTURAS (PGTSA)
            </div>
            <div class="doc-header-info">
                <div><strong>Código:</strong> <span contenteditable="true">SST-PG-01</span></div>
                <div><strong>Versión:</strong> <span contenteditable="true">02</span></div>
                <div><strong>Fecha:</strong> <span contenteditable="true"><?= date("d/m/Y") ?></span></div>
                <div><strong>Página:</strong> 1 de 1</div>
            </div>
        </div>

        <table class="section-table">
            <tr><th colspan="2" class="section-title bg-teal">1. INFORMACIÓN DEL PROGRAMA</th></tr>
            <tr>
                <td style="width:15%;" class="bg-gray text-center align-middle">OBJETIVO</td>
                <td><div class="editable-div" contenteditable="true">Establecer los lineamientos, procedimientos y controles necesarios para prevenir y proteger a los trabajadores de los riesgos de caída, garantizando un ambiente de trabajo seguro en todas las tareas que se realicen a 2.0 metros o más sobre un nivel inferior, cumpliendo con la normatividad legal vigente.</div></td>
            </tr>
            <tr>
                <td class="bg-gray text-center align-middle">ALCANCE</td>
                <td><div class="editable-div" contenteditable="true">Aplica a todos los trabajadores directos, contratistas, subcontratistas y visitantes que realicen, supervisen o autoricen trabajos en alturas dentro de las instalaciones de la empresa o en proyectos externos bajo su responsabilidad.</div></td>
            </tr>
            <tr>
                <td class="bg-gray text-center align-middle">MARCO NORMATIVO</td>
                <td><div class="editable-div" contenteditable="true">Resolución 4272 de 2021 (Requisitos mínimos de seguridad para desarrollo de trabajo en alturas), Decreto 1072 de 2015, Resolución 0312 de 2019, NTC 1642.</div></td>
            </tr>
        </table>

        <table class="section-table">
            <tr><th colspan="3" class="section-title bg-orange">2. ROLES Y RESPONSABILIDADES EN EL PGTSA</th></tr>
            <tr class="bg-gray text-center">
                <th style="width:20%;">ROL</th>
                <th style="width:50%;">RESPONSABILIDAD PRINCIPAL</th>
                <th style="width:30%;">PERFIL SUGERIDO / REQUISITO</th>
            </tr>
            <tr>
                <td class="fw-bold">Empleador / Alta Dirección</td>
                <td><div class="editable-div" contenteditable="true">Suministrar los recursos financieros, técnicos y humanos. Garantizar el programa de capacitación, dotación de EPP y equipos certificados.</div></td>
                <td><div class="editable-div" contenteditable="true">Gerente / Representante Legal</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Administrador del Programa</td>
                <td><div class="editable-div" contenteditable="true">Diseñar, administrar y asegurar el cumplimiento del PGTSA. Consolidar indicadores.</div></td>
                <td><div class="editable-div" contenteditable="true">Profesional SST con licencia y Curso 50h/20h SST.</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Coordinador de Trabajo en Alturas</td>
                <td><div class="editable-div" contenteditable="true">Identificar peligros en el sitio, aplicar controles, avalar permisos de trabajo, inspeccionar equipos antes de cada uso.</div></td>
                <td><div class="editable-div" contenteditable="true">Certificado de Coordinador de Alturas (80h).</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Persona Calificada</td>
                <td><div class="editable-div" contenteditable="true">Calcular, diseñar, verificar e inspeccionar anualmente puntos de anclaje y líneas de vida.</div></td>
                <td><div class="editable-div" contenteditable="true">Ingeniero con experiencia certificada / Tarjeta Prof.</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Trabajador Autorizado</td>
                <td><div class="editable-div" contenteditable="true">Cumplir los procedimientos, inspeccionar su EPP antes de uso, reportar condiciones inseguras, participar en capacitaciones.</div></td>
                <td><div class="editable-div" contenteditable="true">Certificado Nivel Trabajador Autorizado (32h).</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Ayudante de Seguridad</td>
                <td><div class="editable-div" contenteditable="true">Apoyar al coordinador, advertir riesgos, restringir áreas de peligro, no realiza el trabajo en altura directamente.</div></td>
                <td><div class="editable-div" contenteditable="true">Curso básico o avanzado en alturas.</div></td>
            </tr>
        </table>

        <table class="section-table">
            <tr><th colspan="4" class="section-title bg-light-orange">3. REQUISITOS DE CAPACITACIÓN, ENTRENAMIENTO Y COMPETENCIA</th></tr>
            <tr class="bg-gray text-center">
                <th colspan="4">Personas objeto de la capacitación y entrenamiento. Se deben capacitar y entrenar en trabajo en alturas los siguientes roles</th>
            </tr>
            <tr class="bg-gray text-center">
                <th style="width:25%;">ROL</th>
                <th style="width:45%;">PERSONAL OBJETO</th>
                <th style="width:15%;">DURACIÓN</th>
                
            </tr>
            <tr>
                <td class="fw-bold">Jefes de area para trabajos en alturas</td>
                <td><div class="editable-div" contenteditable="true">Leer y escribir.</div></td>
                <td class="text-center"><div class="editable-div" contenteditable="true">Mínimo 8 horas</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Trabajador autorizado</td>
                <td><div class="editable-div" contenteditable="true">Para realizar la capacitación de trabajador autorizado, el aspirante deberá presentar certificado médico de aptitud médica para trabajo en alturas.</div></td>
                <td class="text-center"><div class="editable-div" contenteditable="true">Mínimo 32 horas</div></td>
            </tr>
            <tr>
                <td class="fw-bold">Coordinador de trabajo en alturas</td>
                <td><div class="editable-div" contenteditable="true">Certificado nivel trabajador autorizado, o certificado de competencia laboral para trabajo en altura y experiencia mínima de un (1) año en actividades afines.</div></td>
                <td class="text-center"><div class="editable-div" contenteditable="true">Mínimo 80 horas</div></td>
            </tr>
        </table>

        <div style="margin-bottom: 20px;">
            <table class="section-table m-0" style="width: 100%;">
                <tr><th colspan="5" class="section-title bg-teal">4. MEDIDAS DE PREVENCIÓN Y PROTECCIÓN CONTRA CAÍDAS (INDICADORES)</th></tr>
                
                <tr>
                    <td style="width: 25%;" class="bg-teal text-end fw-bold align-middle">
                        INDICADOR COBERTURA %
                    </td>
                    <td style="width: 40%; background-color: #a9cce3;" class="align-middle">
                        <div class="editable-div" contenteditable="true">(Trabajadores expuestos al Riesgo de Trabajo en Alturas / trabajadores Trabajadores Capacitados para realizar Trabajo en Alturas) * 100</div>
                    </td>
                    <td style="width: 5%;" class="bg-teal fw-bold text-center align-middle">
                        META
                    </td>
                    <td style="width: 5%;" class="fw-bold text-center align-middle">
                        <div class="editable-div" contenteditable="true">100%</div>
                    </td>
                    <td style="width: 25%;" class="align-middle">
                        <div class="editable-div" contenteditable="true">Ejecutar las actividades enmarcadas dentro del programa</div>
                    </td>
                </tr>

                <tr>
                    <td class="bg-teal text-end fw-bold align-middle">
                        INDICADOR DE CUMPLIMIENTO %
                    </td>
                    <td style="background-color: #a9cce3;" class="align-middle">
                        <div class="editable-div" contenteditable="true">(N° de actividades ejecutadas / No de actividades programadas) * 100</div>
                    </td>
                    <td class="bg-teal fw-bold text-center align-middle">
                        META
                    </td>
                    <td class="fw-bold text-center align-middle">
                        <div class="editable-div" contenteditable="true">100%</div>
                    </td>
                    <td class="align-middle">
                        <div class="editable-div" contenteditable="true">Lograr una cobertura de todo el personal de los cargos críticos</div>
                    </td>
                </tr>

                <tr>
                    <td class="bg-teal text-end fw-bold align-middle">
                        INDICADOR DE EFICACIA
                    </td>
                    <td style="background-color: #a9cce3;" class="align-middle">
                        <div class="editable-div" contenteditable="true">(N° de eventos por AT en alturas / N° de personas expuestas al riesgo de alturas) * 100</div>
                    </td>
                    <td class="bg-teal fw-bold text-center align-middle">
                        META
                    </td>
                    <td class="fw-bold text-center align-middle">
                        <div class="editable-div" contenteditable="true">0%</div>
                    </td>
                    <td class="align-middle">
                        <div class="editable-div" contenteditable="true">Mantener el indicador en cero accidentes</div>
                    </td>
                </tr>
            </table>
        </div>

         <!-- INICIO TABLA RECURSOS, RESPONSABLES E IMPACTO -->
        <div style="margin-bottom: 20px;">
            <table class="section-table m-0" style="width: 100%;">
                <tr style="background-color: #bfbfbf; color: #000;" class="text-center fw-bold">
                    <th style="width: 35%;">RECURSOS</th>
                    <th style="width: 45%;">RESPONSABLES</th>
                    <th style="width: 20%;">IMPACTO A CONTROLAR</th>
                </tr>
                <tr>
                    <td>
                        <div class="editable-div" contenteditable="true" style="text-align: justify; line-height: 1.4;">
                            Para el desarrollo del Programa de Gestión del Riesgo de caída en alturas se requieren los siguientes recursos:<br>
                            Talento Humano: Personal a cargo el desarrollo del sistema gestión de la empresa, asesores de la ARL, asesorias externas profesionales.<br>
                            Recurso tecnicos y tecnológicos: Equipos de computo, video beam.<br>
                            Recursos Financieros: Propios de la empresa destinados desde el presupuesto del SG-SST a las diferentes actividades, los cuales están representados en los rubros destinados, al pago a profesionales, contratistas, tecnico, tecnólogos de seguridad y salud en el trabajo, adicianal a ello se cuenta con las actividades concertadas y aporbadas por Gerencia para realizar actividades de prevención en campañas y capacitaciones de Talento Humano.
                        </div>
                    </td>
                    <td>
                        <div class="editable-div" contenteditable="true" style="text-align: justify; line-height: 1.4;">
                            <strong>Gerencia</strong><br>
                            Destinar los recursos necesarios para la implementación y desarrollo del presente programa.<br>
                            <strong>Responsable del SG-SSTA</strong><br>
                            Direccionar la implementación del programa de riesgo para trabajo en alturas<br>
                            Recibir y tramitar los reportes de actos y condiciones subestándar relacionados con factores comportamentales y de condiciones para el desarrollo del trabajo en alturas.<br>
                            Realizar al 100% la investigación de accidentes e incidentes de la operación de PPPCCA y hacer seguimiento a los planes de acción resultantes.<br>
                            <strong>Supervisores</strong><br>
                            Implementar las medidas establecidas en el programa de gestión para la prevención deL riesgo.<br>
                            Implementar las acciones correctivas y preventivas que permitan prevenir accidentes.<br>
                            Incentivar en los trabajadores la identificación actos y condiciones subestándar relacionadas con riesgo para PPPCCA, a través de las herramientas establecidas por la empresa.<br>
                            <strong>Trabajadores en General que pertenecen al programa</strong><br>
                            Participar activamente en el desarrollo de las actividades del programa para la prevención de riesgo PPPCCA.<br>
                            Reportar oportunamente actos y condiciones subestándar a través del formato de Autoreportes.<br>
                            Asistir a capacitación de Reentrenamiento o coordinador según corresponda.
                        </div>
                    </td>
                    <td class="text-center align-middle fw-bold">
                        <div class="editable-div" contenteditable="true">
                            ACCIDENTES DE TRABAJO EN ALTURAS
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <!-- FIN TABLA RECURSOS, RESPONSABLES E IMPACTO -->          

        <div class="d-flex justify-content-between align-items-center mb-1 mt-3">
            <div class="section-title bg-green p-2" style="width: 100%; border: 1px solid #000;">6. CRONOGRAMA DE ACTIVIDADES Y SEGUIMIENTO</div>
        </div>
        
        <table class="table-crono" id="tablaCronograma">
            <thead>
                <tr>
                    <th rowspan="2" colspan="2" class="col-actividad">ACTIVIDAD DEL PROGRAMA</th>
                    <th rowspan="2" class="col-resp">RESPONSABLE</th>
                    <th colspan="12">CRONOGRAMA <span id="lblAnio">2026</span></th>
                    <th rowspan="2" style="width:30px;">%</th>
                    <th rowspan="2" style="width:150px;">EVIDENCIA</th>
                    <th rowspan="2" class="no-print" style="width:20px;"><button class="btn-sys btn-add" onclick="agregarFila()"><i class="fa-solid fa-plus"></i></button></th>
                </tr>
                <tr>
                    <th>E</th><th>F</th><th>M</th><th>A</th><th>M</th><th>J</th><th>J</th><th>A</th><th>S</th><th>O</th><th>N</th><th>D</th>
                </tr>
            </thead>
            <tbody id="tbodyCronograma">
            </tbody>
        </table> <!-- CORREGIDO: Se cierra la tabla aquí antes del panel global -->

        <!-- INICIO PANEL DE CUMPLIMIENTO GLOBAL (NUEVO) -->
        <div id="resumenFooter" style="margin-top: 15px; font-family: 'Segoe UI', sans-serif;">
            
            <!-- Fila superior de totales -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 5px;">
                <table style="border-collapse: collapse; text-align: center; font-weight: bold; font-size: 14px;">
                    <tr>
                        <td style="border: 1px solid #000; width: 45px; background: #fff;" id="sumP">0</td>
                        <td style="border: 1px solid #000; width: 45px; background: #fff;" id="sumE">0</td>
                        <td style="border: 1px solid #000; width: 55px; background: #fff;" id="sumPct">0%</td>
                        <td style="border: 1px solid #000; width: 350px; font-size: 12px;" class="bg-teal text-white">PORCENTAJE DE CUMPLIMIENTO GLOBAL</td>
                    </tr>
                </table>
            </div>

            <!-- Tabla de meses + cuadro gigante % -->
            <div style="display: flex; width: 100%;">
                <table style="border-collapse: collapse; text-align: center; font-size: 11px; flex-grow: 1;">
                    <!-- Total Programado -->
                    <tr>
                        <td style="border: 1px solid #000; background: #00b0f0; color: #000; font-weight: bold; width: 25%; text-align: right; padding-right: 10px;">TOTAL PROGRAMADO MES</td>
                        <td id="p_mes_0" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_1" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_2" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_3" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_4" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_5" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_6" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_7" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_8" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_9" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_10" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="p_mes_11" style="border: 1px solid #000; background: #fff;">0</td>
                    </tr>
                    <!-- Total Ejecutado -->
                    <tr>
                        <td style="border: 1px solid #000; background: #92d050; color: #000; font-weight: bold; text-align: right; padding-right: 10px;">TOTAL EJECUTADO MES</td>
                        <td id="e_mes_0" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_1" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_2" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_3" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_4" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_5" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_6" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_7" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_8" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_9" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_10" style="border: 1px solid #000; background: #fff;">0</td>
                        <td id="e_mes_11" style="border: 1px solid #000; background: #fff;">0</td>
                    </tr>
                    <!-- Meses Encabezados -->
                    <tr class="bg-teal text-white fw-bold">
                        <td style="border: 1px solid #000; background: #d9d9d9;"></td>
                        <td style="border: 1px solid #000;">ENE</td><td style="border: 1px solid #000;">FEB</td><td style="border: 1px solid #000;">MAR</td>
                        <td style="border: 1px solid #000;">ABR</td><td style="border: 1px solid #000;">MAY</td><td style="border: 1px solid #000;">JUN</td>
                        <td style="border: 1px solid #000;">JUL</td><td style="border: 1px solid #000;">AGO</td><td style="border: 1px solid #000;">SEP</td>
                        <td style="border: 1px solid #000;">OCT</td><td style="border: 1px solid #000;">NOV</td><td style="border: 1px solid #000;">DIC</td>
                    </tr>
                    <!-- Meta Propuesta -->
                    <tr>
                        <td style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 10px; background: #fff;">META PROPUESTA</td>
                        <td id="meta_mes_0" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_1" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_2" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_3" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_4" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_5" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_6" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_7" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_8" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_9" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_10" style="border: 1px solid #000; background: #fff;">100%</td>
                        <td id="meta_mes_11" style="border: 1px solid #000; background: #fff;">100%</td>
                    </tr>
                    <!-- Cumplimiento -->
                    <tr>
                        <td style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 10px; background: #fff;">CUMPLIMIENTO</td>
                        <td id="pct_mes_0" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_1" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_2" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_3" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_4" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_5" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_6" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_7" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_8" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_9" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_10" style="border: 1px solid #000; background: #fff;">0%</td>
                        <td id="pct_mes_11" style="border: 1px solid #000; background: #fff;">0%</td>
                    </tr>
                </table>
                <!-- Cuadro Gigante 99% -->
                <div style="border: 1px solid #000; width: 350px; border-left: none; display: flex; align-items: center; justify-content: center; font-size: 42px; font-weight: bold; background: #fff;" id="bigPct">
                    0%
                </div>
            </div>

            <!-- Tabla Inferior de Indicadores -->
            <table style="border-collapse: collapse; text-align: center; font-size: 11px; width: 100%; margin-top: 10px; background: #fff;">
               <tr>
                   <td colspan="4" style="border: none;"></td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 10%;">META</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 15%;">PORCENTAJE RESULTADO</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 25%;">OBJETIVO</td>
               </tr>
               <tr>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 20%;">ACTIVIDADES</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 15%;">ESTADO ACTUAL DE AVANCE</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 15%;">% DE CUMPLIMIENTO GLOBAL</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000; width: 15%;">INDICADOR COBERTURA %</td>
                   <td style="border: 1px solid #000; font-weight: bold;">90%</td>
                   <td style="border: 1px solid #000; font-weight: bold; font-size: 14px;"><div class="editable-div" contenteditable="true">93%</div></td>
                   <td style="border: 1px solid #000; text-align: left; padding: 4px;">Ejecutar las actividades enmarcadas dentro del programa</td>
               </tr>
               <tr>
                   <td style="border: 1px solid #000; background: #9bc2e6; font-weight: bold; font-size: 12px;">PROGRAMADAS</td>
                   <td style="border: 1px solid #000; font-weight: bold; font-size: 18px;" id="indProg">0</td>
                   <td rowspan="2" style="border: 1px solid #000; font-weight: bold; font-size: 32px;" id="indBigPct">0%</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000;">INDICADOR DE CUMPLIMIENTO %</td>
                   <td style="border: 1px solid #000; font-weight: bold;">90%</td>
                   <td style="border: 1px solid #000; font-weight: bold; font-size: 14px;" id="indPctCump">0%</td>
                   <td style="border: 1px solid #000; text-align: left; padding: 4px;">Lograr una cobertura de todo el personal que realice tareas de alto riesgo</td>
               </tr>
               <tr>
                   <td style="border: 1px solid #000; background: #d9d9d9; font-weight: bold; font-size: 12px;">EJECUTADAS</td>
                   <td style="border: 1px solid #000; font-weight: bold; font-size: 18px;" id="indEjec">0</td>
                   <td class="bg-teal text-white fw-bold" style="border: 1px solid #000;">INDICADOR DE EFICACIA</td>
                   <td style="border: 1px solid #000; font-weight: bold;">90%</td>
                   <td style="border: 1px solid #000; font-weight: bold; font-size: 14px;"><div class="editable-div" contenteditable="true">100%</div></td>
                   <td style="border: 1px solid #000; text-align: left; padding: 4px;">Mantener el indicador en cero accidentes</td>
               </tr>
            </table>
        </div>
        <!-- FIN PANEL DE CUMPLIMIENTO GLOBAL -->

        <div class="charts-container no-print">
            <div class="chart-box">
                <h5 style="font-size:13px; font-weight:800; text-align:center;">CUMPLIMIENTO POR MES (Planificado vs Ejecutado)</h5>
                <canvas id="chartMensual" height="100"></canvas>
            </div>
            <div class="chart-box" style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
                <h5 style="font-size:13px; font-weight:800; text-align:center;">CUMPLIMIENTO TOTAL DEL PGTSA</h5>
                <div style="position: relative; width: 140px; height: 140px;">
                    <canvas id="chartTotal"></canvas>
                    <div id="pctTotalText" style="position: absolute; top:50%; left:50%; transform:translate(-50%, -30%); font-size:22px; font-weight:900; color:var(--header-green);">0%</div>
                </div>
            </div>
        </div>

        <!-- INICIO ANÁLISIS DE RESULTADOS Y PLAN DE ACCIÓN (ACTUALIZADO) -->
        <div style="margin-top: 30px; padding-bottom: 60px;"> <!-- <-- Aquí agregamos padding inferior -->
            
            <!-- Tabla Superior: Análisis y Tendencia -->
            <table class="section-table" style="width: 100%; margin-bottom: 0; border-bottom: none;">
                <tr>
                    <th colspan="7" class="bg-teal text-center fw-bold py-2">ANÁLISIS DE RESULTADOS</th>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 15px; background-color: #fff;">
                        <div class="editable-div" contenteditable="true" style="white-space: pre-line; line-height: 1.5;">Resultados: Se evidencia que el programa esta en proceso de documentación, sin embargo contamos con una serie de actividades que nos arrojan los siguientes resultados:
Cobertura: 93%
Cumplimiento: 99%
Eficacia: 100%
Se evidencia una eficacia del 100% del programa con cero accidentes laborales en alturas.</div>
                    </td>
                </tr>
                <tr>
                    <th colspan="7" class="bg-teal text-center fw-bold py-2">ANÁLISIS TENDENCIAL</th>
                </tr>
                <tr class="bg-teal text-white fw-bold text-center align-middle">
                    <td style="width: 6%; border: 1px solid #000;">AÑO</td>
                    <td style="width: 6%; border: 1px solid #000;">AL</td>
                    <td style="width: 36%; border: 1px solid #000;">ACCIDENTALIDAD ÚLTIMOS CINCO AÑOS POR EXPRESIÓN DEL RIESGO DE CAÍDA DE ALTURAS</td>
                    <td style="width: 6%; border: 1px solid #000;">AÑO</td>
                    <td style="width: 6%; border: 1px solid #000;">AL</td>
                    <td style="width: 36%; border: 1px solid #000;">ACCIDENTALIDAD AÑO ACTUAL</td>
                    <td class="no-print" style="width: 4%; border: 1px solid #000;">
                        <button class="btn-sys btn-add" onclick="agregarFilaTendencia()"><i class="fa-solid fa-plus"></i></button>
                    </td>
                </tr>
                <tbody id="tbodyTendencia">
                    <tr class="text-center fw-bold align-middle" style="background-color: #fff;">
                        <td><div class="editable-div text-center" contenteditable="true">2025</div></td>
                        <td><div class="editable-div text-center" contenteditable="true">0</div></td>
                        <td class="text-start px-2"><div class="editable-div" contenteditable="true">No se cuenta con accidentes por caída de alturas</div></td>
                        <td><div class="editable-div text-center" contenteditable="true">2025</div></td>
                        <td><div class="editable-div text-center" contenteditable="true">0</div></td>
                        <td class="text-center px-2"><div class="editable-div" contenteditable="true">No se evidencian accidentes por caídas de alturas</div></td>
                        <td class="no-print"><button class="btn-sys" style="background:var(--danger); padding:2px 6px;" onclick="eliminarFilaSencilla(this)"><i class="fa-solid fa-xmark"></i></button></td>
                    </tr>
                </tbody>
            </table>

            <!-- Tabla Inferior: Plan de Acción -->
            <!-- Se quitó la clase m-0 y se agregó un margin-bottom para evitar que quede pegada al borde -->
            <table class="section-table" style="width: 100%; border-top: none; margin-bottom: 40px;">
                <thead>
                    <tr class="bg-teal text-white fw-bold text-center">
                        <td style="width: 48%; border: 1px solid #000; border-top: none; padding: 8px;">PLAN DE ACCIÓN</td>
                        <td style="width: 24%; border: 1px solid #000; border-top: none; padding: 8px;">RESPONSABLE</td>
                        <td style="width: 24%; border: 1px solid #000; border-top: none; padding: 8px;">FECHA</td>
                        <td class="no-print" style="width: 4%; border: 1px solid #000; border-top: none; padding: 8px;">
                            <button class="btn-sys btn-add" onclick="agregarFilaPlan()"><i class="fa-solid fa-plus"></i></button>
                        </td>
                    </tr>
                </thead>
                <tbody id="tbodyPlanAccion">
                    <tr style="background-color: #fff;">
                        <td style="padding: 8px;"><div class="editable-div" contenteditable="true">Incentivar al personal, para que realicen el curso de jefes de area de trabajo en alturas</div></td>
                        <td style="padding: 8px;"><div class="editable-div text-center" contenteditable="true">Responsable SG-SST</div></td>
                        <td style="padding: 8px;"><div class="editable-div text-center" contenteditable="true">DD/MM/AAAA</div></td>
                        <td class="no-print text-center align-middle"><button class="btn-sys" style="background:var(--danger); padding:2px 6px;" onclick="eliminarFilaSencilla(this)"><i class="fa-solid fa-xmark"></i></button></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <!-- FIN ANÁLISIS DE RESULTADOS Y PLAN DE ACCIÓN -->            
    </div>
</div>



<script>
    let ctxMensual = document.getElementById('chartMensual').getContext('2d');
    let ctxTotal = document.getElementById('chartTotal').getContext('2d');

    let chartMensual = new Chart(ctxMensual, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [
                { label: 'Planificado (P)', backgroundColor: '#0d4d8b', data: Array(12).fill(0) },
                { label: 'Ejecutado (E)', backgroundColor: '#27ae60', data: Array(12).fill(0) }
            ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { position: 'bottom' } } }
    });

    let chartTotal = new Chart(ctxTotal, {
        type: 'doughnut',
        data: { labels: ['Ejecutado', 'Faltante'], datasets: [{ backgroundColor: ['#27ae60', '#e9ecef'], data: [0, 100], borderWidth: 0 }] },
        options: { cutout: '75%', responsive: true, plugins: { legend: { display: false } } }
    });

   // Actividades de PHVA extraídas del documento
    const actividadesBase = [
        // PLANEAR
        { fase: "P", cls: "fase-p", act: "Actualizar Matriz de Identificación y control del personal autorizado para realizar trabajo seguro en alturas (PPPCCA)", resp: "Coordinador del SGSST", evi: "Programa de PPPCCA hoja de: Matriz de peligros y riesgos" },
        
        // HACER
        { fase: "H", cls: "fase-h", act: "Toma de exámenes de ingreso para trabajo en alturas", resp: "Coordinador del SGSST", evi: "Resultados digital o en físico de los exámenes de ingreso para trabajo en alturas" },
        { fase: "H", cls: "fase-h", act: "Capacitación de Reentrenamiento PPPCCA para trabajador autorizado y ayudante de alturas.", resp: "Sena", evi: "Certificado Sena, - Ver programa de capacitación anual SGSST" },
        { fase: "H", cls: "fase-h", act: "Capacitación de jefes de área", resp: "Sena", evi: "Certificado Sena, - Ver programa de capacitación anual SGSST" },
        { fase: "H", cls: "fase-h", act: "Identificar los peligros y valorar los riesgos asociados a las tareas en alturas y establecer los controles.", resp: "Coordinador del SGSST", evi: "Ver matriz de peligros y riesgos" },
        { fase: "H", cls: "fase-h", act: "Inventariar las actividades de trabajos en alturas, con su definición de tareas rutinarias y no rutinarias.", resp: "Coordinador del SGSST", evi: "Ver matriz de peligros y riesgos" },
        { fase: "H", cls: "fase-h", act: "Documentar en caso de no haber los Procedimientos de trabajo relacionados con las tareas en alturas (En caso de haber realizar el listado de los mismos).", resp: "Coordinador del SGSST", evi: "Ver ATS" },
        { fase: "H", cls: "fase-h", act: "Determinar las medidas de prevención para trabajos en alturas", resp: "Coordinador de alturas / Responsable del trabajo", evi: "Esta actividad involucra varios elementos" },
        { fase: "H", cls: "fase-h", act: "Determinar los Sistemas de acceso para trabajos en alturas", resp: "Coordinador de alturas / Responsable del trabajo", evi: "Acorde al tipo de trabajo (Escalera, Andamio, elevadores, plataforma entre otros)" },
        { fase: "H", cls: "fase-h", act: "Determinar las Medidas de protección para trabajos en alturas", resp: "Coordinador de alturas / Responsable del trabajo", evi: "Árnes, Eslinga, pretal, espolines, cinta de posicionamiento, mosquetones." },
        { fase: "H", cls: "fase-h", act: "Realizar y / o actualizar en caso de tenerse el procedimientos en caso de emergencias", resp: "Coordinador del SGSST", evi: "Procedimiento de rescate en alturas, Kit de rescate en alturas" },
        { fase: "H", cls: "fase-h", act: "Realizar simulacro de emergencias (Art. 26)", resp: "Coordinador del SGSST", evi: "Informe de simulacro" },
        { fase: "H", cls: "fase-h", act: "Documentar y / o actualizar si se requieren las hojas de vida de los equipos para tareas en alturas (Art. 16)", resp: "Almacenista", evi: "10. Se debe tener una hoja de vida de los equipos elevadores de personas, escaleras, y andamios en los cuales sus partes cuentan con un solo diseño, donde estén consignados como mínimo los datos de: marca, serial, fecha de fabricación, tiempo de vida útil, historial de uso, registros de inspección, registros de mantenimiento, ficha técnica, certificación del fabricante y observaciones." },
        { fase: "H", cls: "fase-h", act: "Realizar inspecciones técnicas a los equipos para tareas en alturas (Art. 16)", resp: "Coordinador del SGSST", evi: "3. Los sistemas elevadores de personas, también deben ser inspeccionados mínimo una vez al año por una persona avalada por el fabricante o una persona calificada conforme a las recomendaciones del fabricante o las normas nacionales o internacionales vigentes. 4. Si existen no conformidades, el sistema debe retirarse de servicio y enviarse a mantenimiento." },
        { fase: "H", cls: "fase-h", act: "Realizar mantenimiento a los sistemas de acceso para tareas en alturas (Art. 16)", resp: "Mantenimiento", evi: "11. El mantenimiento de los sistemas de acceso, deberá ser realizado de acuerdo con las especificaciones del fabricante y registrados en la hoja de vida del equipo." },
        { fase: "H", cls: "fase-h", act: "Verificar que los permisos de PPPCCA que correspondan al personal Autorizado y correspondiente firma del Coordinador.", resp: "Coordinador del SGSST", evi: "Lista de chequeo Pre y Post de trabajo en Alturas y permiso de trabajo en alturas." },
        
        // VERIFICAR
        { fase: "V", cls: "fase-v", act: "Determinar y evaluar los indicadores de gestión específicos alineados al Decreto 1072 de 2015", resp: "Coordinador del SGSST", evi: "Revisión de cumplimiento de actividades" },
        
        // ACTUAR
        { fase: "A", cls: "fase-a", act: "Plan de acción", resp: "Coordinador del SGSST - Gerencia", evi: "Plan de acción (Tomar las acciones de mejora resultantes de las inspecciones, reportes de los trabajadores y / o contratistas, accidentes, casi accidentes en alturas en caso que hayan surgido al igual que del análisis de los resultados de los indicadores del programa)" }
    ];

    function renderCeldasMeses(filaId) {
        let tds = "";
        for(let i=0; i<12; i++){
            tds += `<td class="col-mes">
                        <div class="pe-box">
                            <div class="pe-row"><span style="color:#0d4d8b">P</span> <input type="checkbox" class="chk-p" data-mes="${i}" onchange="calcularTodo()"></div>
                            <div class="pe-row"><span style="color:#198754">E</span> <input type="checkbox" class="chk-e" data-mes="${i}" onchange="calcularTodo()"></div>
                        </div>
                    </td>`;
        }
        return tds;
    }

    function cargarFilasIniciales() {
        const tbody = document.getElementById("tbodyCronograma");
        tbody.innerHTML = ""; 
        
        actividadesBase.forEach((item, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="col-fase ${item.cls}">${item.fase}</td>
                <td class="col-actividad"><div class="editable-div text-start fw-bold" contenteditable="true">${item.act}</div></td>
                <td><div class="editable-div" contenteditable="true">${item.resp}</div></td>
                ${renderCeldasMeses(index)}
                <td class="fw-bold pct-fila bg-gray">0%</td>
                <td class="text-start" style="font-size: 9px;"><div class="editable-div" contenteditable="true">${item.evi}</div></td>
                <td class="no-print"><button class="btn-sys" style="background:var(--danger); padding:2px 6px;" onclick="eliminarFila(this)"><i class="fa-solid fa-xmark"></i></button></td>
            `;
            tbody.appendChild(tr);
        });
        calcularTodo();
    }

    function agregarFila() {
        const tbody = document.getElementById("tbodyCronograma");
        const tr = document.createElement("tr");
        const idx = tbody.children.length;
        tr.innerHTML = `
            <td class="col-fase"><div class="editable-div" contenteditable="true">-</div></td>
            <td class="col-actividad"><div class="editable-div text-start fw-bold" contenteditable="true">Nueva actividad...</div></td>
            <td><div class="editable-div" contenteditable="true">Responsable...</div></td>
            ${renderCeldasMeses(idx)}
            <td class="fw-bold pct-fila bg-gray">0%</td>
            <td class="text-start" style="font-size: 9px;"><div class="editable-div" contenteditable="true">Evidencia...</div></td>
            <td class="no-print"><button class="btn-sys" style="background:var(--danger); padding:2px 6px;" onclick="eliminarFila(this)"><i class="fa-solid fa-xmark"></i></button></td>
        `;
        tbody.appendChild(tr);
        calcularTodo();
    }

    function eliminarFila(btn) {
        if (document.querySelectorAll("#tbodyCronograma tr").length > 1) {
            btn.closest("tr").remove();
            calcularTodo();
        }
    }

    // CORRECCIÓN: Lógica completa para conectar los checkboxes con TODOS los paneles y gráficos.
    function calcularTodo() {
        const filas = document.querySelectorAll("#tbodyCronograma tr");
        let arrP = Array(12).fill(0);
        let arrE = Array(12).fill(0);
        let totalP = 0; let totalE = 0;

        filas.forEach(fila => {
            let filaP = 0; let filaE = 0;
            const checksP = fila.querySelectorAll('.chk-p');
            const checksE = fila.querySelectorAll('.chk-e');

            for(let i=0; i<12; i++) {
                if(checksP[i].checked) { arrP[i]++; filaP++; totalP++; }
                if(checksE[i].checked) { arrE[i]++; filaE++; totalE++; }
            }

            let pctFila = filaP === 0 ? 0 : Math.round((filaE / filaP) * 100);
            if(pctFila > 100) pctFila = 100;
            fila.querySelector('.pct-fila').innerText = pctFila + "%";
        });

        // 1. Actualizar Gráficos Chart.js
        chartMensual.data.datasets[0].data = arrP;
        chartMensual.data.datasets[1].data = arrE;
        chartMensual.update();

        let pctGlobal = totalP === 0 ? 0 : Math.round((totalE / totalP) * 100);
        if(pctGlobal > 100) pctGlobal = 100;
        
        chartTotal.data.datasets[0].data = [pctGlobal, 100 - pctGlobal];
        chartTotal.update();
        document.getElementById('pctTotalText').innerText = pctGlobal + "%";

        // 2. Actualizar Tabla de Resumen Superior
        document.getElementById('sumP').innerText = totalP;
        document.getElementById('sumE').innerText = totalE;
        document.getElementById('sumPct').innerText = pctGlobal + "%";

        // 3. Actualizar la tabla de Meses (Programado y Ejecutado por mes)
        for(let i=0; i<12; i++) {
            document.getElementById('p_mes_' + i).innerText = arrP[i];
            document.getElementById('e_mes_' + i).innerText = arrE[i];
            
            let pctMes = arrP[i] === 0 ? 0 : Math.round((arrE[i] / arrP[i]) * 100);
            if(pctMes > 100) pctMes = 100;
            document.getElementById('pct_mes_' + i).innerText = pctMes + "%";
        }

        // 4. Actualizar Cuadro Gigante y Tabla Inferior
        document.getElementById('bigPct').innerText = pctGlobal + "%";
        
        document.getElementById('indProg').innerText = totalP;
        document.getElementById('indEjec').innerText = totalE;
        document.getElementById('indPctCump').innerText = pctGlobal + "%";
        document.getElementById('indBigPct').innerText = pctGlobal + "%";
    }

    document.getElementById("selectAnio").addEventListener("change", function() {
        document.getElementById("lblAnio").innerText = this.value;
        cargarFilasIniciales(); 
    });

    document.addEventListener("DOMContentLoaded", () => {
        cargarFilasIniciales();
    });

    // NUEVAS FUNCIONES PARA TABLAS TENDENCIAL Y PLAN DE ACCIÓN
    function agregarFilaTendencia() {
        const tbody = document.getElementById("tbodyTendencia");
        const tr = document.createElement("tr");
        tr.className = "text-center fw-bold align-middle";
        tr.style.backgroundColor = "#fff";
        tr.innerHTML = `
            <td><div class="editable-div text-center" contenteditable="true">Año</div></td>
            <td><div class="editable-div text-center" contenteditable="true">0</div></td>
            <td class="text-start px-2"><div class="editable-div" contenteditable="true">Detalle accidente histórico...</div></td>
            <td><div class="editable-div text-center" contenteditable="true">Año</div></td>
            <td><div class="editable-div text-center" contenteditable="true">0</div></td>
            <td class="text-center px-2"><div class="editable-div" contenteditable="true">Detalle accidente actual...</div></td>
            <td class="no-print"><button class="btn-sys" style="background:var(--danger); padding:2px 6px;" onclick="eliminarFilaSencilla(this)"><i class="fa-solid fa-xmark"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function agregarFilaPlan() {
        const tbody = document.getElementById("tbodyPlanAccion");
        const tr = document.createElement("tr");
        tr.style.backgroundColor = "#fff";
        tr.innerHTML = `
            <td style="padding: 4px 8px;"><div class="editable-div" contenteditable="true">Nueva acción...</div></td>
            <td><div class="editable-div text-center" contenteditable="true">Responsable...</div></td>
            <td><div class="editable-div text-center" contenteditable="true">DD/MM/AAAA</div></td>
            <td class="no-print text-center"><button class="btn-sys" style="background:var(--danger); padding:2px 6px;" onclick="eliminarFilaSencilla(this)"><i class="fa-solid fa-xmark"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function eliminarFilaSencilla(btn) {
        const tbody = btn.closest("tbody");
        // Evita eliminar la última fila para que la tabla no quede vacía
        if (tbody.children.length > 1) {
            btn.closest("tr").remove();
        }
    }


</script>

</body>
</html>