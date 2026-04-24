<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 52; 

// --- Lógica de Logo ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// --- Carga de Datos ---
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? null;
if (is_string($camposCrudos)) $datosCampos = json_decode($camposCrudos, true) ?: [];
elseif (is_array($camposCrudos)) $datosCampos = $camposCrudos;

function oldv($key, $default = '') {
    global $datosCampos;
    return isset($datosCampos[$key]) ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}

// Estructura de la Auditoría según Estándares Mínimos
$secciones = [
    "1. RECURSOS (10%)" => [
        "1.1.1" => "Responsable del Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST",
        "1.1.2" => "Responsabilidades en el Sistema de Gestión de Seguridad y Salud en el Trabajo – SG-SST",
        "1.1.3" => "Asignación de recursos para el Sistema de Gestión en Seguridad y Salud en el Trabajo",
        "1.1.4" => "Afiliación al Sistema General de Riesgos Laborales",
        "1.1.5" => "Pago de aportes al Sistema General de Riesgos Laborales",
        "1.1.6" => "Conformación y funcionamiento del COPASST",
        "1.1.7" => "Conformación y funcionamiento del Comité de Convivencia Laboral",
        "1.1.8" => "Programa de Capacitación anual"
    ],
    "2. GESTIÓN DE LA SALUD (15%)" => [
        "2.1.1" => "Descripción socio-demográfica y Diagnóstico de condiciones de salud",
        "2.2.1" => "Actividades de Promoción y Prevención en Salud",
        "2.3.1" => "Evaluaciones Médicas Ocupacionales",
        "2.4.1" => "Custodia de Historias Clínicas",
        "2.5.1" => "Restricciones y Recomendaciones Médico Laborales"
    ],
    "3. GESTIÓN DE PELIGROS Y RIESGOS (30%)" => [
        "3.1.1" => "Metodología para identificación de peligros, evaluación y valoración de riesgos",
        "3.1.2" => "Identificación de peligros con participación de todos los niveles de la empresa",
        "3.1.3" => "Identificación de sustancias catalogadas como carcinógenas o con toxicidad aguda",
        "3.2.1" => "Ejecución de medidas de prevención y control de peligros y riesgos"
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>6.1.2-4 Lista de Chequeo Auditoría</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *{ box-sizing:border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
        body{ background:#f2f4f7; padding:20px; }
        
        .contenedor{ max-width:1300px; background:#fff; border:1px solid #bfc7d1; margin:0 auto; box-shadow:0 4px 18px rgba(0,0,0,.08); }
        
        /* HEADER REFINADO */
        .toolbar{ 
            display:flex; 
            justify-content:space-between; 
            align-items:center; 
            padding:14px 20px; 
            background:#dde7f5; 
            border-bottom:1px solid #c8d3e2; 
        }
        .toolbar h1{ font-size:20px; color:#1a4175; font-weight:700; }
        
        .acciones{ display:flex; gap:10px; }
        .btn{ border:none; padding:10px 22px; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; color:#fff; transition:.2s ease; }
        .btn-atras{ background:#6c757d; }
        .btn-guardar{ background:#198754; }
        .btn-imprimir{ background:#0d6efd; }
        .btn:hover{ opacity: 0.9; transform: translateY(-1px); }

        .formulario-body{ padding:25px; }

        /* TABLA IDENTIFICACIÓN */
        table.encabezado{ width:100%; border-collapse:collapse; margin-bottom: 20px; }
        table.encabezado td{ border:1px solid #000; padding:10px; text-align:center; font-size: 12px; }
        .label-gris{ background:#f2f2f2; font-weight:bold; text-align:left !important; width:180px; }

        /* TABLA DE EVALUACIÓN */
        table.lista{ width:100%; border-collapse:collapse; }
        table.lista th, table.lista td{ border:1px solid #000; padding:8px; font-size:11px; }
        .bg-blue-header{ background: #dde7f5; color: #000; text-align: center; font-weight: bold; text-transform: uppercase; }
        .bg-gris-sub{ background: #f2f2f2; font-weight: bold; }

        select, textarea, input[type="text"]{ width:100%; border:none; outline:none; background:transparent; font-family: inherit; }
        textarea{ resize: vertical; min-height: 40px; font-size: 10px; }
        select { cursor: pointer; font-weight: bold; text-align: center; }

        /* RESULTADOS */
        .resumen-box { 
            margin-top: 25px; border: 2px solid #213b67; padding: 20px; background: #fff;
            display: flex; justify-content: space-around; align-items: center; border-radius: 10px;
        }
        .score-val { font-size: 36px; font-weight: bold; color: #198754; }

        @media print{ .print-hide{ display:none !important; } .contenedor{ border:none; box-shadow:none; } }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>Lista de Chequeo de Auditoría (RE-SST-20)</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Auditoría</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario-body">
        <form id="formListaChequeo">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:220px;">
                        <img src="<?= $logoEmpresaUrl ?>" style="max-height:55px;">
                    </td>
                    <td style="font-weight:bold; font-size:15px;">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:120px;">0</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">LISTA DE CHEQUEO DE AUDITORIA</td>
                    <td>RE-SST-20<br>24/04/2026</td>
                </tr>
            </table>

            <table class="encabezado">
                <tr>
                    <td class="label-gris">PROCESO AUDITADO:</td>
                    <td><input type="text" name="proceso_auditado" value="<?= oldv('proceso_auditado') ?>"></td>
                    <td class="label-gris">FECHA:</td>
                    <td><input type="text" name="fecha_auditoria" value="<?= oldv('fecha_auditoria', date('d/m/Y')) ?>"></td>
                </tr>
            </table>

            <table class="lista">
                <thead>
                    <tr class="bg-blue-header">
                        <th style="width:80px;">ESTÁNDAR</th>
                        <th>CRITERIO DE AUDITORÍA / REQUISITO</th>
                        <th style="width:120px;">CUMPLE</th>
                        <th>OBSERVACIONES / HALLAZGOS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($secciones as $titulo => $items): ?>
                        <tr class="bg-gris-sub">
                            <td colspan="4" style="text-align:left; padding-left:15px; font-size: 12px;"><?= $titulo ?></td>
                        </tr>
                        <?php foreach($items as $cod => $crit): 
                            $idName = str_replace('.', '_', $cod); ?>
                        <tr>
                            <td style="text-align:center; font-weight:bold;"><?= $cod ?></td>
                            <td style="text-align:justify; padding: 10px; line-height: 1.4;"><?= $crit ?></td>
                            <td>
                                <select name="val_<?= $idName ?>" class="calc-val">
                                    <option value="">-</option>
                                    <option value="C" <?= oldv("val_$idName") == 'C' ? 'selected' : '' ?>>CUMPLE</option>
                                    <option value="NC" <?= oldv("val_$idName") == 'NC' ? 'selected' : '' ?>>NO CUMPLE</option>
                                    <option value="NA" <?= oldv("val_$idName") == 'NA' ? 'selected' : '' ?>>N/A</option>
                                </select>
                            </td>
                            <td><textarea name="obs_<?= $idName ?>"><?= oldv("obs_$idName") ?></textarea></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="resumen-box">
                <div style="text-align:center;">
                    <p style="font-weight:bold; color: #213b67;">CALIFICACIÓN TOTAL</p>
                    <div class="score-val" id="txtCumplimiento">0%</div>
                </div>
                <div>
                    <table style="border:none;">
                        <tr><td style="border:none; text-align:left;">CUMPLE:</td><td style="border:none;"><span id="resC" style="font-weight:bold;">0</span></td></tr>
                        <tr><td style="border:none; text-align:left;">NO CUMPLE:</td><td style="border:none;"><span id="resNC" style="font-weight:bold; color:red;">0</span></td></tr>
                        <tr><td style="border:none; text-align:left;">N/A:</td><td style="border:none;"><span id="resNA" style="font-weight:bold;">0</span></td></tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function calcularAuditoria() {
    let cumple = 0, noCumple = 0, na = 0;
    const selects = document.querySelectorAll('.calc-val');
    
    selects.forEach(s => {
        if(s.value === 'C') cumple++;
        else if(s.value === 'NC') noCumple++;
        else if(s.value === 'NA') na++;
    });

    const totalEvaluado = cumple + noCumple;
    const porcentaje = totalEvaluado > 0 ? Math.round((cumple / totalEvaluado) * 100) : 0;

    document.getElementById('resC').innerText = cumple;
    document.getElementById('resNC').innerText = noCumple;
    document.getElementById('resNA').innerText = na;
    document.getElementById('txtCumplimiento').innerText = porcentaje + "%";
}

document.querySelectorAll('.calc-val').forEach(s => s.addEventListener('change', calcularAuditoria));

document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = new FormData(document.getElementById('formListaChequeo'));
    const datosJSON = Object.fromEntries(formData.entries());

    btn.innerText = 'Guardando...'; btn.disabled = true;
    try {
        const response = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresa ?>, id_item_sst: <?= $idItem ?>, datos: datosJSON })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('Éxito', 'Auditoría guardada correctamente', 'success');
    } catch (e) { Swal.fire('Error', 'Fallo al conectar con el servidor', 'error'); } 
    finally { btn.innerText = 'Guardar Auditoría'; btn.disabled = false; }
});

window.onload = calcularAuditoria;
</script>
</body>
</html>