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
    <title>Programa de Mantenimiento | SST</title>
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
            --phva-blue: #4472c4;
        }

        /* RESET RESPONSIVO SIN SCROLL HORIZONTAL */
        html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; background: #f4f7fa; font-family: Arial, sans-serif; font-size: 10px; }

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
            width: 95%; max-width: 1200px; margin: 20px auto; background: #fff;
            border: 1px solid #ccc; padding: 15px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .table-responsive-container { width: 100%; overflow-x: auto; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th, td { border: 1px solid var(--line); padding: 4px; vertical-align: middle; text-align: center; }
        
        .bg-blue-header { background: var(--phva-blue); color: white; font-weight: bold; }
        .bg-soft-blue { background: #d9e1f2; font-weight: bold; }
        
        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 10px; }
        .col-phva { width: 80px; background: #eee; font-weight: bold; }
        .month-col { width: 30px; font-size: 8px; }

        .chart-container { width: 100%; height: 250px; margin-top: 15px; }

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
    <h1 class="sst-toolbar-title">Programa de Mantenimiento Preventivo / Correctivo</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">Volver</button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="sheet">
    <form id="formMantenimiento">
        <div class="table-responsive-container">
            <table>
                <tr>
                    <td rowspan="2" style="width: 150px;">
                        <?php if ($logoEmpresaUrl): ?> <img src="<?= $logoEmpresaUrl ?>" style="max-height: 45px; max-width: 100%;"> <?php endif; ?>
                    </td>
                    <td class="bg-soft-blue">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width: 100px; font-weight: bold;">Versión: 01</td>
                </tr>
                <tr>
                    <td class="bg-soft-blue">PROGRAMA DE MANTENIMIENTO ANUAL</td>
                    <td>FR-SST-06<br><?= date('d/m/Y') ?></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive-container">
            <table>
                <thead>
                    <tr class="bg-blue-header">
                        <th style="width: 40px;">Fase</th>
                        <th>Actividades de Mantenimiento</th>
                        <th>Responsable</th>
                        <?php 
                        $meses = ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
                        foreach($meses as $m) echo "<th class='month-col'>$m</th>";
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $fases = [
                        'PLANEAR' => ['Definir equipos críticos', 'Elaborar cronograma'],
                        'HACER' => ['Mantenimiento Extintores', 'Mantenimiento Botiquines', 'Revisión Vehículos'],
                        'VERIFICAR' => ['Inspección de cumplimiento'],
                        'ACTUAR' => ['Acciones de mejora']
                    ];
                    foreach($fases as $fase => $acts): 
                        foreach($acts as $i => $act):
                    ?>
                    <tr>
                        <?php if($i == 0): ?> <td rowspan="<?= count($acts) ?>" class="col-phva"><?= $fase ?></td> <?php endif; ?>
                        <td style="text-align: left; padding-left: 10px;"><?= $act ?></td>
                        <td><input type="text" name="resp_<?= $fase ?>_<?= $i ?>" class="editable" value="<?= oldv("resp_{$fase}_{$i}", 'SST') ?>"></td>
                        <?php for($m=1; $m<=12; $m++): ?>
                        <td><input type="checkbox" name="m_<?= $fase ?>_<?= $i ?>_<?= $m ?>" <?= oldv("m_{$fase}_{$i}_{$m}") == 'on' ? 'checked' : '' ?>></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="bg-soft-blue p-2 text-center border">INDICADOR 1: CUMPLIMIENTO</div>
                <div class="chart-container"><canvas id="chartCumplimiento"></canvas></div>
            </div>
            <div class="col-md-6">
                <div class="bg-soft-blue p-2 text-center border">INDICADOR 2: EFICACIA</div>
                <div class="chart-container"><canvas id="chartEficacia"></canvas></div>
            </div>
        </div>

        <div class="mt-3">
            <strong>Observaciones Generales:</strong>
            <textarea name="obs" class="editable border p-2" style="height: 60px;"><?= oldv('obs') ?></textarea>
        </div>
    </form>
</div>

<script>
// Lógica de Gráficos (Basado en Página 1 y 2 de image_195d9a.jpg)
const ctxCumplimiento = document.getElementById('chartCumplimiento').getContext('2d');
new Chart(ctxCumplimiento, {
    type: 'line',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: '% Cumplimiento',
            data: [90, 95, 80, 85, 90, 100],
            borderColor: '#4472c4',
            backgroundColor: 'rgba(68, 114, 196, 0.2)',
            fill: true
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const ctxEficacia = document.getElementById('chartEficacia').getContext('2d');
new Chart(ctxEficacia, {
    type: 'bar',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: '% Eficacia',
            data: [85, 80, 75, 90, 95, 90],
            backgroundColor: '#ed7d31'
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Lógica de Guardado Unificada
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = Object.fromEntries(new FormData(document.getElementById('formMantenimiento')).entries());
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
        const response = await fetch("../../../public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresa ?>, id_item_sst: <?= $idItem ?>, datos: formData })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('Éxito', 'Programa de mantenimiento actualizado', 'success');
    } catch (e) { Swal.fire('Error', 'Fallo de conexión', 'error'); } 
    finally { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar'; }
});
</script>
</body>
</html>