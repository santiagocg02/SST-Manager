<?php
session_start();
require_once __DIR__ . '/../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token   = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem  = isset($_GET['item']) ? (int)$_GET['item'] : 0;

// LOGO DINÁMICO
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// CARGA DE DATOS EXISTENTES
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? null;

if (is_string($camposCrudos)) { $datosCampos = json_decode($camposCrudos, true) ?: []; }
elseif (is_array($camposCrudos)) { $datosCampos = $camposCrudos; }

function oldv($key, $default = '') {
    global $datosCampos;
    return isset($datosCampos[$key]) ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PVE Ergonomía | SST Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --sst-toolbar: #dde7f5;
            --sst-toolbar-border: #c8d3e2;
            --line: #000;
            --primary-blue: #213b67;
            --pve-red: #c00000; /* Color distintivo del PVE Biomecánico según referencia */
        }

        /* RESET RESPONSIVO */
        html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; background: #f4f7fa; font-family: Arial, sans-serif; font-size: 11px; }

        .sst-toolbar {
            position: sticky; top: 0; z-index: 1000;
            background: var(--sst-toolbar);
            border-bottom: 1px solid var(--sst-toolbar-border);
            padding: 10px 15px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px;
        }
        .sst-toolbar-title { margin: 0; font-size: 14px; font-weight: 800; color: var(--primary-blue); text-transform: uppercase; }

        .sheet {
            width: 95%; max-width: 1100px; margin: 20px auto; background: #fff;
            border: 1px solid #ccc; padding: 25px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .table-responsive-container { width: 100%; overflow-x: auto; margin-bottom: 15px; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 850px; }
        th, td { border: 1px solid var(--line); padding: 5px; vertical-align: middle; text-align: center; }
        
        .bg-red-header { background: var(--pve-red); color: white; font-weight: bold; text-transform: uppercase; }
        .bg-soft-red { background: #feebeb; font-weight: bold; }
        
        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 11px; }
        .month-col { width: 30px; font-size: 9px; }

        .chart-box { border: 1px solid #ccc; padding: 15px; margin-top: 15px; background: #fff; }
        .chart-container { width: 100%; height: 250px; }

        @media print {
            .sst-toolbar, .no-print, .btn-add { display: none !important; }
            body { background: #fff; padding: 0; }
            .sheet { border: none; box-shadow: none; width: 100%; max-width: 100%; margin: 0; padding: 0; }
            .table-responsive-container { overflow: visible; }
        }
    </style>
</head>
<body>

<div class="sst-toolbar no-print">
    <h1 class="sst-toolbar-title">PVE - Riesgo Biomecánico y Ergonómico</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">Volver</button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="sheet">
    <form id="formErgonomia">
        <div class="table-responsive-container">
            <table>
                <tr>
                    <td rowspan="2" style="width: 200px;">
                        <?php if ($logoEmpresaUrl): ?> <img src="<?= $logoEmpresaUrl ?>" style="max-height: 50px; max-width: 100%;"> <?php endif; ?>
                    </td>
                    <td class="bg-soft-red">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width: 150px; font-weight: bold;">Versión: 01</td>
                </tr>
                <tr>
                    <td class="bg-soft-red">PROGRAMA DE VIGILANCIA EPIDEMIOLÓGICA BIOMECÁNICO</td>
                    <td style="font-size: 11px;">RE-SST-22<br><?= date('d/m/Y') ?></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive-container">
            <table>
                <thead>
                    <tr class="bg-red-header">
                        <th style="width: 50px;">PHVA</th>
                        <th>ACTIVIDADES DEL PVE</th>
                        <th>RESPONSABLE</th>
                        <?php 
                        $meses = ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
                        foreach($meses as $m) echo "<th class='month-col'>$m</th>";
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $actividades = [
                        'P' => ['Identificación de peligros biomecánicos', 'Encuestas de morbilidad sentida'],
                        'H' => ['Capacitación en Higiene Postural', 'Pausas Activas Dirigidas', 'Ajuste de puestos de trabajo'],
                        'V' => ['Seguimiento a casos sospechosos', 'Análisis de ausentismo'],
                        'A' => ['Recomendaciones médico-laborales']
                    ];
                    foreach($actividades as $ciclo => $acts): 
                        foreach($acts as $index => $act):
                    ?>
                    <tr>
                        <?php if($index == 0): ?> <td rowspan="<?= count($acts) ?>" style="font-weight:bold; background:#f9f9f9;"><?= $ciclo ?></td> <?php endif; ?>
                        <td style="text-align: left; padding-left: 10px;"><?= $act ?></td>
                        <td><input type="text" name="resp_<?= $ciclo ?>_<?= $index ?>" class="editable" value="<?= oldv("resp_{$ciclo}_{$index}", 'SST / ARL') ?>"></td>
                        <?php for($m=1; $m<=12; $m++): ?>
                        <td><input type="checkbox" name="m_<?= $ciclo ?>_<?= $index ?>_<?= $m ?>" <?= oldv("m_{$ciclo}_{$index}_{$m}") == 'on' ? 'checked' : '' ?>></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <div class="chart-box">
                    <div class="bg-red-header p-1 mb-2 text-center" style="font-size: 12px;">Cumplimiento de Actividades</div>
                    <div class="chart-container"><canvas id="chartCumplimiento"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-box">
                    <div class="bg-red-header p-1 mb-2 text-center" style="font-size: 12px;">Cobertura de Trabajadores</div>
                    <div class="chart-container"><canvas id="chartCobertura"></canvas></div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="bg-soft-red p-2 border"><strong>OBSERVACIONES Y ANÁLISIS DE RESULTADOS:</strong></div>
            <textarea name="analisis" class="editable border p-2" style="min-height: 100px; width: 100%;"><?= oldv('analisis') ?></textarea>
        </div>
    </form>
</div>

<script>
// Configuración de Gráficos (Basado en tendencias de image_19003b.jpg)
const setupChart = (id, label, color, data) => {
    new Chart(document.getElementById(id), {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: label,
                data: data,
                borderColor: color,
                backgroundColor: color + '22',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
};

setupChart('chartCumplimiento', '% Cumplimiento', '#c00000', [80, 85, 90, 70, 95, 100, 90, 85, 80, 95, 100, 100]);
setupChart('chartCobertura', '% Cobertura', '#213b67', [60, 70, 75, 80, 85, 90, 95, 98, 100, 100, 100, 100]);

// Guardado Dinámico
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = Object.fromEntries(new FormData(document.getElementById('formErgonomia')).entries());
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
        const response = await fetch("../../../public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresa ?>, id_item_sst: <?= $idItem ?>, datos: formData })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('Éxito', 'PVE Biomecánico guardado correctamente', 'success');
    } catch (e) { Swal.fire('Error', 'Fallo de conexión', 'error'); } 
    finally { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar'; }
});
</script>
</body>
</html>