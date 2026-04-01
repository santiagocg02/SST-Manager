<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../index.php');
    exit;
}

function oldv($key, $default = '')
{
    return isset($_POST[$key]) ? htmlspecialchars((string)$_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

$data2024 = [
    ['mes'=>'ENE','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'FEB','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'MAR','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'ABR','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'MAY','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'JUN','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'JUL','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'AGO','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'SEP','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'OCT','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'NOV','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'DIC','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
];

$data2025 = [
    ['mes'=>'ENE','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'FEB','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'MAR','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'ABR','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'MAY','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'JUN','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'JUL','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'AGO','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'SEP','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'OCT','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'NOV','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
    ['mes'=>'DIC','historico'=>0,'casos_nuevos'=>0,'sin_inc'=>0,'con_inc'=>0,'total_at'=>0,'trab'=>0,'mortales'=>0,'dias_incap'=>0,'dias_cargado'=>0,'total_dias_incap'=>0,'total_dias_trab'=>0,'horas_extras'=>0,'total_hht'=>0,'freq'=>0,'sev'=>0,'prop_mortal'=>0,'prev'=>0,'incid'=>0],
];

$campos = [
    'historico' => 'HISTORICO DE E.L',
    'casos_nuevos' => 'CASOS NUEVOS DE EL',
    'sin_inc' => 'No. AT SIN INC.',
    'con_inc' => 'No. AT CON INC.',
    'total_at' => 'TOTAL AT',
    'trab' => 'No. TRABAJ.',
    'mortales' => 'ACCIDENTES MORTALES',
    'dias_incap' => 'DÍAS INCAPACIDAD',
    'dias_cargado' => 'DÍAS CARGADO',
    'total_dias_incap' => 'TOTAL DÍAS INCAPACIDAD',
    'total_dias_trab' => 'TOTAL DÍAS TRABAJADO*',
    'horas_extras' => 'TOTAL HORAS EXTRAS',
    'total_hht' => 'TOTAL HHT',
    'freq' => 'FRECUENCIA DE ACCIDENTALIDAD',
    'sev' => 'SEVERIDAD DE ACCIDENTALIDAD',
    'prop_mortal' => 'PROPORCION DE A.T MORTALE',
    'prev' => 'TASAS DE PREVALENCIA E.L',
    'incid' => 'TASAS DE INCIDENCIA E.L',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accidentalidad</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,Helvetica,sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1900px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:20px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}
        .formulario{padding:18px}
        table{width:100%;border-collapse:collapse}
        .encabezado td,.encabezado th,.tabla-main td,.tabla-main th{border:1px solid #6b6b6b;padding:5px;vertical-align:middle}
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{width:140px;height:65px;border:2px dashed #c8c8c8;display:flex;align-items:center;justify-content:center;margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center}
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px}
        .hero{background:#f5f5f5;border:1px solid #ddd;padding:16px;margin-top:12px}
        .hero-top{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;align-items:flex-start}
        .left-meta{display:flex;gap:30px;align-items:flex-start;flex-wrap:wrap}
        .inline-field{display:flex;align-items:center;gap:8px;font-size:14px;font-weight:700}
        .inline-field input{border:1px solid #bbb;padding:6px 8px;background:#fff;width:140px;font-weight:700}
        .dias-box{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:700}
        .dias-box .valor{border:2px solid #9ccc65;background:#f7fff2;padding:8px 18px;font-size:34px;font-weight:700;color:#7cb342}
        .link-top{color:#2f64c8;text-decoration:underline;font-size:14px;margin-top:6px}
        .titulo-page{font-size:24px;color:#5a95d6;font-style:italic;font-weight:700;margin-top:12px}
        .cards{display:flex;gap:18px;flex-wrap:wrap;align-items:flex-start}
        .card-kpi{min-width:130px;background:#5d9bd5;color:#fff;border-radius:16px;padding:12px 16px;box-shadow:0 2px 8px rgba(0,0,0,.14);text-align:center}
        .card-kpi.gray{background:#f3f3f3;color:#333}
        .card-kpi .kpi-title{font-size:12px;font-weight:700;line-height:1.15;text-transform:uppercase;margin-bottom:8px}
        .card-kpi .kpi-value{font-size:40px;font-weight:700;line-height:1}
        .red{color:#ff2b2b}
        .grid-tables{display:grid;grid-template-columns:1fr;gap:18px;margin-top:18px}
        .tabla-wrap{overflow:auto;border:1px solid #6b6b6b}
        .tabla-main{min-width:1900px;font-size:11px}
        .tabla-main thead th{text-align:center;line-height:1.15}
        .tabla-main.green thead th{background:#d9ead3}
        .tabla-main.blue thead th{background:#cfe2f3}
        .tabla-main tbody td,.tabla-main tfoot td{text-align:center}
        .tabla-main input{
            width:100%;
            border:none;
            outline:none;
            background:#fff;
            padding:4px 3px;
            font-size:11px;
            text-align:center;
        }
        .tabla-main tfoot td{
            font-weight:700;
            background:#f4f6fa;
        }
        .grid-charts{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:24px}
        .chart-card{border:1px solid #d0d0d0;background:#fff}
        .chart-head{padding:10px;text-align:center;font-size:14px;font-weight:700;color:#666;min-height:60px}
        .chart-body{padding:8px 10px 0}
        .chart-body canvas{width:100% !important;height:260px !important}
        .chart-analysis{border-top:1px dashed #999;min-height:110px;padding:10px;font-size:12px;line-height:1.45}
        .mini-years{display:grid;grid-template-columns:repeat(6,1fr);gap:2px;max-width:360px}
        .mini-years div{border:1px solid #8aa8db;background:#dbe8f8;text-align:center;font-size:11px;padding:4px;font-weight:700}
        .save-msg{margin:0 0 15px 0;padding:10px 14px;border-radius:8px;background:#e9f7ef;color:#166534;border:1px solid #b7e4c7;font-size:14px;font-weight:700}
        .note{margin-top:8px;font-size:12px;color:#666}
        @media (max-width: 1400px){.grid-charts{grid-template-columns:repeat(2,1fr)}}
        @media (max-width: 900px){.grid-charts{grid-template-columns:1fr}.hero-top{flex-direction:column}}
        @media print{
            body{background:#fff;padding:0}
            .toolbar{display:none}
            .contenedor{box-shadow:none;border:none}
            .formulario{padding:8px}
            .tabla-main input{border:none !important}
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>Accidentalidad</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="formAccidentalidad">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="formAccidentalidad" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:15%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:70%;">SISTEMA DE GESTION EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:15%;font-weight:700;">0<br>RE-SST-33<br>XX/XX/2025</td>
                </tr>
                <tr>
                    <td class="subtitulo">ACCIDENTALIDAD</td>
                    <td style="font-weight:700;">5</td>
                </tr>
            </table>

            <div class="hero">
                <div class="hero-top">
                    <div>
                        <div class="left-meta">
                            <div class="inline-field">
                                <span>Fecha</span>
                                <input type="date" id="fechaActual" name="fecha_actual" value="<?= oldv('fecha_actual', '') ?>">
                            </div>
                            <div class="inline-field">
                                <span>Último accidente</span>
                                <input type="date" id="ultimoAccidente" name="ultimo_accidente" value="<?= oldv('ultimo_accidente', '') ?>" style="border:2px solid #ff6b6b;color:#d32f2f;">
                            </div>
                            <div class="dias-box">
                                <span>Días sin accidentes</span>
                                <div class="valor" id="diasSinAccidentesBox">0</div>
                            </div>
                        </div>

                        <div class="titulo-page">Accidentalidad</div>

                        <div style="margin-top:10px;">
                            <div class="mini-years">
                                <div>2024</div><div>ENE</div><div>FEB</div><div>MAR</div><div>ABR</div><div>MAY</div>
                                <div>JUN</div><div>JUL</div><div>AGO</div><div>SEP</div><div>OCT</div><div>NOV</div>
                            </div>
                            <div class="mini-years" style="margin-top:6px;">
                                <div>2025</div><div>ENE</div><div>FEB</div><div>MAR</div><div>ABR</div><div>MAY</div>
                                <div>JUN</div><div>JUL</div><div>AGO</div><div>SEP</div><div>OCT</div><div>NOV</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="link-top">CARACTERIZACIÓN DE LA ACCIDENTALIDAD</div>
                        <div class="cards" style="margin-top:22px;">
                            <div class="card-kpi">
                                <div class="kpi-title">HHT</div>
                                <div class="kpi-value" id="kpiHHT">0</div>
                            </div>
                            <div class="card-kpi gray">
                                <div class="kpi-title">MORTALES</div>
                                <div class="kpi-value red" id="kpiMortales">0</div>
                            </div>
                            <div class="card-kpi">
                                <div class="kpi-title">ACCIDENTES. L</div>
                                <div class="kpi-value" id="kpiAcc">0</div>
                            </div>
                            <div class="card-kpi">
                                <div class="kpi-title">DIAS INCAPACIDAD</div>
                                <div class="kpi-value" id="kpiDias">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="note">Edita los valores en las tablas y las gráficas/KPI se actualizarán automáticamente.</div>
            </div>

            <div class="grid-tables">
                <?php
                $years = [
                    '2024' => ['data' => $data2024, 'class' => 'green'],
                    '2025' => ['data' => $data2025, 'class' => 'blue'],
                ];
                foreach ($years as $year => $cfg):
                ?>
                <div class="tabla-wrap">
                    <table class="tabla-main <?= $cfg['class'] ?>" id="tabla<?= $year ?>">
                        <thead>
                            <tr>
                                <th><?= $year ?></th>
                                <?php foreach ($campos as $label): ?>
                                    <th><?= $label ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cfg['data'] as $i => $row): ?>
                            <tr data-year="<?= $year ?>" data-index="<?= $i ?>">
                                <td><strong><?= $row['mes'] ?></strong></td>
                                <?php foreach ($campos as $key => $label): ?>
                                    <td>
                                        <input
                                            type="number"
                                            step="any"
                                            class="cell-input"
                                            data-year="<?= $year ?>"
                                            data-index="<?= $i ?>"
                                            data-field="<?= $key ?>"
                                            value="<?= oldv("{$year}_{$i}_{$key}", (string)$row[$key]) ?>"
                                        >
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot id="tfoot<?= $year ?>">
                            <tr>
                                <td>TOTAL</td>
                                <?php foreach ($campos as $key => $label): ?>
                                    <td id="total_<?= $year ?>_<?= $key ?>">0</td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="grid-charts">
                <div class="chart-card">
                    <div class="chart-head">ACCIDENTES DE TRABAJO 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartAT"></canvas></div>
                    <div class="chart-analysis" id="analysisAT"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-head">DIAS DE AUSENTISMO X ACCIDENTES 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartDias"></canvas></div>
                    <div class="chart-analysis" id="analysisDias"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-head">TASA DE INCIDENCIA 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartIncidencia"></canvas></div>
                    <div class="chart-analysis" id="analysisIncidencia"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-head">TASA DE PREVALENCIA 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartPrevalencia"></canvas></div>
                    <div class="chart-analysis" id="analysisPrevalencia"></div>
                </div>

                <div class="chart-card">
                    <div class="chart-head">PROPORCIÓN DE ACCIDENTES MORTALES 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartMortales"></canvas></div>
                    <div class="chart-analysis" id="analysisMortales"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-head">SEVERIDAD DE LA ACCIDENTALIDAD 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartSeveridad"></canvas></div>
                    <div class="chart-analysis" id="analysisSeveridad"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-head">FRECUENCIA DE LA ACCIDENTALIDAD 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartFrecuencia"></canvas></div>
                    <div class="chart-analysis" id="analysisFrecuencia"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-head">NÚMERO DE TRABAJADORES 2024 VS 2025</div>
                    <div class="chart-body"><canvas id="chartTrab"></canvas></div>
                    <div class="chart-analysis" id="analysisTrab"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const meses = <?= json_encode($meses) ?>;
const fields = <?= json_encode(array_keys($campos)) ?>;
const numericFields = fields;
const charts = {};

function toNumber(value) {
    const n = parseFloat(value);
    return isNaN(n) ? 0 : n;
}

function getYearData(year) {
    const rows = [];
    document.querySelectorAll(`#tabla${year} tbody tr`).forEach((tr) => {
        const row = { mes: tr.querySelector('td strong').textContent.trim() };
        numericFields.forEach((field) => {
            const input = tr.querySelector(`input[data-field="${field}"]`);
            row[field] = toNumber(input ? input.value : 0);
        });
        rows.push(row);
    });
    return rows;
}

function formatNumber(value, decimals = 0) {
    return Number(value).toLocaleString('es-CO', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function updateTotals(year, rows) {
    const totals = {};
    numericFields.forEach(field => totals[field] = 0);

    rows.forEach(row => {
        numericFields.forEach(field => {
            totals[field] += toNumber(row[field]);
        });
    });

    numericFields.forEach(field => {
        const cell = document.getElementById(`total_${year}_${field}`);
        if (!cell) return;
        cell.textContent = formatNumber(totals[field], ['freq','sev','prop_mortal','prev','incid','trab'].includes(field) ? 1 : 0);
    });

    return totals;
}

function updateKPIs(rows2025) {
    const totalHHT = rows2025.reduce((a, b) => a + b.total_hht, 0);
    const totalMortales = rows2025.reduce((a, b) => a + b.mortales, 0);
    const totalAcc = rows2025.reduce((a, b) => a + b.total_at, 0);
    const totalDias = rows2025.reduce((a, b) => a + b.dias_incap, 0);

    document.getElementById('kpiHHT').textContent = formatNumber(totalHHT, 0);
    document.getElementById('kpiMortales').textContent = formatNumber(totalMortales, 0);
    document.getElementById('kpiAcc').textContent = formatNumber(totalAcc, 0);
    document.getElementById('kpiDias').textContent = formatNumber(totalDias, 0);
}

function updateDaysWithoutAccidents() {
    const fechaActual = document.getElementById('fechaActual').value;
    const ultimoAccidente = document.getElementById('ultimoAccidente').value;

    if (!fechaActual || !ultimoAccidente) {
        document.getElementById('diasSinAccidentesBox').textContent = '0';
        return;
    }

    const fa = new Date(fechaActual + 'T00:00:00');
    const fu = new Date(ultimoAccidente + 'T00:00:00');
    const diff = Math.floor((fa - fu) / (1000 * 60 * 60 * 24));

    document.getElementById('diasSinAccidentesBox').textContent = diff > 0 ? diff : '0';
}

function analysisText(title, arr2024, arr2025) {
    const sum2024 = arr2024.reduce((a, b) => a + b, 0);
    const sum2025 = arr2025.reduce((a, b) => a + b, 0);

    if (sum2024 === 0 && sum2025 === 0) {
        return `Análisis tendencial: no hay registros para ${title.toLowerCase()}.`;
    }

    const max2024 = Math.max(...arr2024);
    const max2025 = Math.max(...arr2025);
    const idx2024 = arr2024.indexOf(max2024);
    const idx2025 = arr2025.indexOf(max2025);

    return `Análisis tendencial: en ${title.toLowerCase()}, 2024 acumula ${formatNumber(sum2024, 1)} y su pico está en ${meses[idx2024]} con ${formatNumber(max2024, 1)}. En 2025 acumula ${formatNumber(sum2025, 1)} y su mayor valor está en ${meses[idx2025]} con ${formatNumber(max2025, 1)}.`;
}

function createOrUpdateChart(id, type, dataset1Label, dataset1, dataset2Label, dataset2) {
    const ctx = document.getElementById(id);

    if (charts[id]) {
        charts[id].data.labels = meses;
        charts[id].data.datasets[0].label = dataset1Label;
        charts[id].data.datasets[0].data = dataset1;
        charts[id].data.datasets[1].label = dataset2Label;
        charts[id].data.datasets[1].data = dataset2;
        charts[id].update();
        return;
    }

    charts[id] = new Chart(ctx, {
        type: type,
        data: {
            labels: meses,
            datasets: [
                { label: dataset1Label, data: dataset1, tension: 0.25 },
                { label: dataset2Label, data: dataset2, tension: 0.25 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

function renderAllCharts(rows2024, rows2025) {
    const at2024 = rows2024.map(r => r.total_at);
    const at2025 = rows2025.map(r => r.total_at);
    createOrUpdateChart('chartAT', 'bar', '2024', at2024, '2025', at2025);
    document.getElementById('analysisAT').textContent = analysisText('Accidentes de trabajo', at2024, at2025);

    const dias2024 = rows2024.map(r => r.dias_incap);
    const dias2025 = rows2025.map(r => r.dias_incap);
    createOrUpdateChart('chartDias', 'line', '2024', dias2024, '2025', dias2025);
    document.getElementById('analysisDias').textContent = analysisText('Días de ausentismo x accidentes', dias2024, dias2025);

    const incid2024 = rows2024.map(r => r.incid);
    const incid2025 = rows2025.map(r => r.incid);
    createOrUpdateChart('chartIncidencia', 'line', '2024', incid2024, '2025', incid2025);
    document.getElementById('analysisIncidencia').textContent = analysisText('Tasa de incidencia', incid2024, incid2025);

    const prev2024 = rows2024.map(r => r.prev);
    const prev2025 = rows2025.map(r => r.prev);
    createOrUpdateChart('chartPrevalencia', 'line', '2024', prev2024, '2025', prev2025);
    document.getElementById('analysisPrevalencia').textContent = analysisText('Tasa de prevalencia', prev2024, prev2025);

    const mort2024 = rows2024.map(r => r.prop_mortal);
    const mort2025 = rows2025.map(r => r.prop_mortal);
    createOrUpdateChart('chartMortales', 'line', '2024', mort2024, '2025', mort2025);
    document.getElementById('analysisMortales').textContent = analysisText('Proporción de accidentes mortales', mort2024, mort2025);

    const sev2024 = rows2024.map(r => r.sev);
    const sev2025 = rows2025.map(r => r.sev);
    createOrUpdateChart('chartSeveridad', 'line', '2024', sev2024, '2025', sev2025);
    document.getElementById('analysisSeveridad').textContent = analysisText('Severidad de la accidentalidad', sev2024, sev2025);

    const freq2024 = rows2024.map(r => r.freq);
    const freq2025 = rows2025.map(r => r.freq);
    createOrUpdateChart('chartFrecuencia', 'line', '2024', freq2024, '2025', freq2025);
    document.getElementById('analysisFrecuencia').textContent = analysisText('Frecuencia de la accidentalidad', freq2024, freq2025);

    const trab2024 = rows2024.map(r => r.trab);
    const trab2025 = rows2025.map(r => r.trab);
    createOrUpdateChart('chartTrab', 'bar', '2024', trab2024, '2025', trab2025);
    document.getElementById('analysisTrab').textContent = analysisText('Número de trabajadores', trab2024, trab2025);
}

function recalcDashboard() {
    const rows2024 = getYearData('2024');
    const rows2025 = getYearData('2025');

    updateTotals('2024', rows2024);
    updateTotals('2025', rows2025);
    updateKPIs(rows2025);
    updateDaysWithoutAccidents();
    renderAllCharts(rows2024, rows2025);
}

document.addEventListener('input', function(e) {
    if (e.target.matches('.cell-input, #fechaActual, #ultimoAccidente')) {
        recalcDashboard();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    recalcDashboard();
});
</script>
</body>
</html>