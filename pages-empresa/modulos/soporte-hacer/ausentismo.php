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

$meses = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

$causas = [
    'covid' => 'COVID-19',
    'gripa' => 'GRIPA / MALESTAR GENERAL',
    'enf_lab' => 'ENFERMEDAD LABORAL',
    'enf_comun' => 'ENFERMEDAD COMUN',
    'acc_lab' => 'ACCIDENTE LABORAL',
    'licencia' => 'Licencia de maternidad y paternidad y matrimonio',
    'suspension' => 'SUSPENSION',
    'permiso' => 'PERMISO REMUNERADO / COMPENSATORIO',
    'calamidad' => 'CALAMIDAD / LICENCIA POR LUTO'
];

$filasDetalle = 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ausentismo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif;}
        body{background:#f2f4f7;padding:20px;color:#111;}
        .contenedor{max-width:1800px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08);}
        .toolbar{
            position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;
            flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2;
        }
        .toolbar h1{font-size:20px;color:#213b67;font-weight:700;}
        .acciones{display:flex;gap:10px;flex-wrap:wrap;}
        .btn{
            border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease;
        }
        .btn:hover{transform:translateY(-1px);opacity:.95;}
        .btn-guardar{background:#198754;color:#fff;}
        .btn-atras{background:#6c757d;color:#fff;}
        .btn-imprimir{background:#0d6efd;color:#fff;}
        .btn-add{background:#213b67;color:#fff;}

        .formulario{padding:18px;}
        table{width:100%;border-collapse:collapse;}
        .encabezado td,.encabezado th,
        .tabla-base td,.tabla-base th,
        .tabla-detalle td,.tabla-detalle th,
        .tabla-resumen td,.tabla-resumen th{
            border:1px solid #6b6b6b;padding:6px;vertical-align:top;
        }
        .encabezado td,.encabezado th{text-align:center;}
        .logo-box{
            width:140px;height:65px;border:2px dashed #c8c8c8;display:flex;align-items:center;justify-content:center;
            margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center;
        }
        .titulo-principal{font-size:16px;font-weight:700;}
        .subtitulo{font-size:14px;}

        .save-msg{
            margin:0 0 15px 0;padding:10px 14px;border-radius:8px;background:#e9f7ef;color:#166534;
            border:1px solid #b7e4c7;font-size:14px;font-weight:700;
        }

        .panel-top{
            margin-top:14px;
            padding:18px;
            background:#f5f5f5;
            border:1px solid #d9d9d9;
        }

        .mini-meses{
            display:grid;
            grid-template-columns:repeat(6, 1fr);
            gap:4px;
            max-width:420px;
            margin-bottom:16px;
        }

        .mini-meses div{
            border:1px solid #8aa8db;
            background:#dbe8f8;
            text-align:center;
            font-size:11px;
            padding:5px 4px;
            font-weight:700;
        }

        .panel-flex{
            display:flex;
            justify-content:space-between;
            gap:20px;
            align-items:flex-start;
            flex-wrap:wrap;
        }

        .titulo-panel{
            font-size:22px;
            color:#5a95d6;
            font-style:italic;
            font-weight:700;
            margin-top:8px;
        }

        .cards{
            display:flex;
            gap:22px;
            flex-wrap:wrap;
            align-items:flex-start;
        }

        .card-kpi{
            min-width:120px;
            background:#fff;
            border-radius:14px;
            padding:12px 14px;
            box-shadow:0 2px 8px rgba(0,0,0,.14);
            text-align:center;
            border:1px solid #d7dde8;
        }

        .card-kpi.blue{
            background:#5d9bd5;
            color:#fff;
        }

        .card-kpi .kpi-title{
            font-size:11px;
            font-weight:700;
            margin-bottom:6px;
            line-height:1.2;
            text-transform:uppercase;
        }

        .card-kpi .kpi-value{
            font-size:28px;
            font-weight:700;
            line-height:1;
        }

        .meta-box{
            margin-top:18px;
        }

        .meta-box th{
            background:#bcd4f6;
            text-align:center;
        }

        .meta-box td{
            text-align:center;
            font-size:13px;
            padding:12px 8px;
        }

        .meta-red{
            color:#c00000;
            font-weight:700;
        }

        .nota{
            margin:12px 0 6px;
            color:#d11f1f;
            font-size:12px;
        }

        .tabla-wrap{overflow:auto;border:1px solid #6b6b6b;}
        .tabla-base{min-width:1300px;font-size:12px;}
        .tabla-base thead th{background:#bcd4f6;text-align:center;}
        .tabla-base input,.tabla-base select{
            width:100%;border:none;outline:none;background:#fff;padding:5px 6px;font-size:12px;text-align:center;
        }
        .readonly{
            background:#f4f6fa !important;
            color:#1f3b68;
            font-weight:700;
        }
        .center{text-align:center;vertical-align:middle !important;}

        .grid-charts{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
            margin-top:18px;
        }

        .chart-card{
            border:1px solid #bfc7d1;
            background:#fff;
            padding:14px;
        }

        .chart-card h3{
            text-align:center;
            margin-bottom:10px;
            font-size:18px;
            color:#4b4b4b;
        }

        .chart-card canvas{
            width:100% !important;
            height:300px !important;
        }

        .topbar{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
            margin:14px 0 8px;
        }

        .tabla-detalle{
            min-width:1900px;
            font-size:11px;
        }

        .tabla-detalle thead th{
            text-align:center;
            background:#8eaadb;
            color:#fff;
            vertical-align:middle;
        }

        .tabla-detalle thead th.red{
            background:#c00000;
            color:#fff;
        }

        .tabla-detalle input,.tabla-detalle select{
            width:100%;
            border:none;
            outline:none;
            background:#fff;
            padding:5px 6px;
            font-size:11px;
            min-height:34px;
        }

        .grid-bottom{
            display:grid;
            grid-template-columns:1.2fr .8fr;
            gap:18px;
            margin-top:18px;
        }

        .bloque h3{
            background:#204d8c;
            color:#fff;
            padding:8px 10px;
            font-size:14px;
            margin-bottom:0;
        }

        .tabla-resumen{
            font-size:12px;
        }

        .tabla-resumen thead th{
            background:#bcd4f6;
            text-align:center;
        }

        .tabla-resumen tfoot td,.tabla-resumen tfoot th{
            background:#f4f6fa;
            font-weight:700;
        }

        .analisis-box{
            border:1px solid #6b6b6b;
            border-top:none;
            min-height:90px;
            padding:10px;
            background:#fff;
            font-size:12px;
            line-height:1.45;
        }

        .analisis-tendencial{
            border:1px solid #6b6b6b;
            background:#fff;
            padding:12px;
            font-size:12px;
            line-height:1.5;
            min-height:130px;
        }

        @media (max-width: 1100px){
            .grid-charts,.grid-bottom{grid-template-columns:1fr;}
        }

        @media print{
            body{background:#fff;padding:0;}
            .toolbar,.topbar{display:none;}
            .contenedor{box-shadow:none;border:none;}
            .formulario{padding:8px;}
            .tabla-wrap{overflow:visible;border:none;}
            input,select{border:none !important;box-shadow:none !important;padding-left:0 !important;padding-right:0 !important;}
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar">
        <h1>Ausentismo</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="formAusentismo">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="formAusentismo" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTION EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">AUSENTISMO</td>
                    <td style="font-weight:700;">RE-SST-27<br>XX/XX/2025</td>
                </tr>
            </table>

            <div class="panel-top">
                <div class="panel-flex">
                    <div>
                        <div class="mini-meses">
                            <?php foreach ($meses as $m): ?>
                                <div><?= $m ?></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="titulo-panel">Ausentismo</div>
                    </div>

                    <div class="cards">
                        <div class="card-kpi blue">
                            <div class="kpi-title">PROMEDIO DE W.</div>
                            <div class="kpi-value" id="kpi_promedio">0</div>
                        </div>
                        <div class="card-kpi blue">
                            <div class="kpi-title">HHT</div>
                            <div class="kpi-value" id="kpi_hht">0</div>
                        </div>
                        <div class="card-kpi blue">
                            <div class="kpi-title">AUSENTISMO LABORAL GLOBAL</div>
                            <div class="kpi-value" id="kpi_alg">0,00%</div>
                        </div>
                        <div class="card-kpi">
                            <div class="kpi-title">DIAS</div>
                            <div class="kpi-value" id="kpi_dias">0</div>
                        </div>
                        <div class="card-kpi blue">
                            <div class="kpi-title">AUSENTISMO X ENFERMEDAD GRAL</div>
                            <div class="kpi-value" id="kpi_eg">0,00%</div>
                        </div>
                        <div class="card-kpi">
                            <div class="kpi-title">DIAS</div>
                            <div class="kpi-value" id="kpi_dias_eg">0</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="meta-box">
                <table>
                    <thead>
                        <tr>
                            <th>OBJETIVO</th>
                            <th>INDICADOR</th>
                            <th>META</th>
                            <th>FRECUENCIA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Controlar las estadísticas de ausentismo</td>
                            <td>Ausentismo Laboral Global = (Total Días Por Ausentismo / D.T) X 100</td>
                            <td class="meta-red">&lt; 20% MENSUAL</td>
                            <td>Trimestral</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="nota">Nota: Ingrese número de trabajadores, total de días trabajados y % de ausentismos del año anterior. Solo diligencie las filas azules. :contentReference[oaicite:1]{index=1}</div>

            <div class="tabla-wrap">
                <table class="tabla-base" id="tablaMensual">
                    <thead>
                        <tr>
                            <th>2025</th>
                            <th>NUMERO DE TRABAJADORES</th>
                            <th>HHT</th>
                            <th>TOTAL DÍAS TRABAJADOS D.T</th>
                            <th>D.P E.G</th>
                            <th>D.P.A</th>
                            <th>Total Horas P.A</th>
                            <th>A.L.G 2025</th>
                            <th>A.L.G 2024</th>
                            <th>A.L x E.G 2025</th>
                            <th>A.L x E.G 2024</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meses as $i => $mes): $n = $i + 1; ?>
                        <tr>
                            <td><strong><?= $mes ?></strong></td>
                            <td><input type="number" step="1" name="trab_<?= $n ?>" value="<?= oldv("trab_$n", $n <= 6 ? ['10','12','12','10','12','15'][$i] ?? '0' : '0') ?>"></td>
                            <td><input type="number" step="1" name="hht_<?= $n ?>" value="<?= oldv("hht_$n", $n <= 6 ? ['2080','2496','2496','2080','2496','3120'][$i] ?? '0' : '0') ?>"></td>
                            <td><input type="number" step="1" name="dt_<?= $n ?>" value="<?= oldv("dt_$n", $n <= 6 ? ['260','312','312','260','312','390'][$i] ?? '0' : '0') ?>"></td>
                            <td><input type="number" step="1" name="dpeg_<?= $n ?>" value="<?= oldv("dpeg_$n", $n <= 6 ? ['27','10','2','0','0','0'][$i] ?? '0' : '0') ?>"></td>
                            <td><input type="number" step="1" name="dpa_<?= $n ?>" value="<?= oldv("dpa_$n", $n <= 6 ? ['27','10','2','12','0','0'][$i] ?? '0' : '0') ?>"></td>
                            <td><input class="readonly" type="number" step="1" name="thpa_<?= $n ?>" readonly></td>
                            <td><input class="readonly" type="text" name="alg_2025_<?= $n ?>" readonly></td>
                            <td><input type="text" name="alg_2024_<?= $n ?>" value="<?= oldv("alg_2024_$n", $n <= 6 ? ['2,00%','1,00%','1,50%','0,5%','0,00%','0,00%'][$i] ?? '0,00%' : '0,00%') ?>"></td>
                            <td><input class="readonly" type="text" name="aleg_2025_<?= $n ?>" readonly></td>
                            <td><input type="text" name="aleg_2024_<?= $n ?>" value="<?= oldv("aleg_2024_$n", $n <= 6 ? ['2,00%','3,00%','4,00%','0,00%','0,00%','0,00%'][$i] ?? '0,00%' : '0,00%') ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>TOTAL</th>
                            <th id="tot_trab">0</th>
                            <th id="tot_hht">0</th>
                            <th>26</th>
                            <th id="tot_dpeg">0</th>
                            <th id="tot_dpa">0</th>
                            <th id="tot_thpa">0</th>
                            <th id="tot_alg">0,00%</th>
                            <th id="prom_alg_2024">0,00%</th>
                            <th id="tot_aleg">0,00%</th>
                            <th id="prom_aleg_2024">0,00%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="grid-charts">
                <div class="chart-card">
                    <h3>AUSENTISMO LABORAL GLOBAL <span style="color:#6d9eeb;">2025 Vs 2024</span></h3>
                    <canvas id="chartALG"></canvas>
                </div>
                <div class="chart-card">
                    <h3>AUSENTISMO LABORAL X ENFERMEDAD GENERAL <span style="color:#6d9eeb;">2025 Vs 2024</span></h3>
                    <canvas id="chartEG"></canvas>
                </div>
            </div>

            <div class="nota">Nota: ingrese el Nombre, cédula, digite la fecha, días de ausentismo según causa. Solo diligencie las celdas con líneas azules. :contentReference[oaicite:2]{index=2}</div>

            <div class="topbar">
                <button type="button" class="btn btn-add" onclick="agregarFilaDetalle()">Agregar fila</button>
            </div>

            <div class="tabla-wrap">
                <table class="tabla-detalle" id="tablaDetalle">
                    <thead>
                        <tr>
                            <th style="width:180px;">NOMBRES Y APELLIDOS</th>
                            <th style="width:140px;">C.C</th>
                            <th style="width:120px;">AREA</th>
                            <th style="width:110px;">FECHA</th>
                            <th style="width:90px;">MES</th>
                            <th style="width:70px;">DIAS</th>
                            <th style="width:80px;">HORAS</th>
                            <th class="red" style="width:90px;">COVID-19</th>
                            <th style="width:120px;">GRIPA / MALESTAR GENERAL</th>
                            <th style="width:110px;">ENFERMEDAD LABORAL</th>
                            <th style="width:110px;">ENFERMEDAD COMUN</th>
                            <th style="width:100px;">ACCIDENTE LABORAL</th>
                            <th style="width:150px;">Licencia de maternidad y paternidad y matrimonio</th>
                            <th style="width:100px;">SUSPENSION</th>
                            <th style="width:130px;">PERMISO REMUNERADO / COMPENSATORIO</th>
                            <th style="width:130px;">CALAMIDAD / LICENCIA POR LUTO</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDetalle">
                        <?php for ($i=1; $i<=$filasDetalle; $i++): ?>
                        <tr>
                            <td><input type="text" name="det_nombre_<?= $i ?>" value="<?= oldv("det_nombre_$i") ?>"></td>
                            <td><input type="text" name="det_cc_<?= $i ?>" value="<?= oldv("det_cc_$i") ?>"></td>
                            <td><input type="text" name="det_area_<?= $i ?>" value="<?= oldv("det_area_$i") ?>"></td>
                            <td><input type="date" name="det_fecha_<?= $i ?>" value="<?= oldv("det_fecha_$i") ?>"></td>
                            <td>
                                <select name="det_mes_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($meses as $m): ?>
                                        <option value="<?= $m ?>" <?= oldv("det_mes_$i") === $m ? 'selected' : '' ?>><?= $m ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" step="1" name="det_dias_<?= $i ?>" value="<?= oldv("det_dias_$i") ?>"></td>
                            <td><input class="readonly" type="number" step="1" name="det_horas_<?= $i ?>" readonly></td>
                            <?php foreach (array_keys($causas) as $ck): ?>
                                <td><input type="number" step="1" name="det_<?= $ck ?>_<?= $i ?>" value="<?= oldv("det_{$ck}_$i") ?>"></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="nota">Nota: todas las celdas se calculan automáticas, no ingresar datos. :contentReference[oaicite:3]{index=3}</div>

            <div class="grid-bottom">
                <div class="bloque">
                    <h3>MOTIVO DEL AUSENTISMO</h3>
                    <table class="tabla-resumen" id="tblMotivos">
                        <thead>
                            <tr>
                                <th>MES</th>
                                <th>COVID-19</th>
                                <th>GRIPA / ENFERME</th>
                                <th>ENFERMEDAD LABORAL</th>
                                <th>ENFERMEDAD COMUN</th>
                                <th>ACCIDENTE LABORAL</th>
                                <th>LICENCIA</th>
                                <th>SUSPENSION</th>
                                <th>PERMISO</th>
                                <th>CALAMIDAD / LICE</th>
                                <th>TOTAL</th>
                                <th>TOTAL A.L x E.G</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>TOTAL</th>
                                <th id="sum_covid">0</th>
                                <th id="sum_gripa">0</th>
                                <th id="sum_enf_lab">0</th>
                                <th id="sum_enf_comun">0</th>
                                <th id="sum_acc_lab">0</th>
                                <th id="sum_licencia">0</th>
                                <th id="sum_suspension">0</th>
                                <th id="sum_permiso">0</th>
                                <th id="sum_calamidad">0</th>
                                <th id="sum_total">0</th>
                                <th id="sum_alxeg">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div>
                    <div class="bloque">
                        <h3>ANALISIS EVENTOS</h3>
                        <div class="analisis-box" id="analisis_eventos"></div>
                    </div>
                    <div class="bloque" style="margin-top:18px;">
                        <h3>ANALISIS TENDENCIAL</h3>
                        <div class="analisis-tendencial" id="analisis_tendencial"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let detalleCount = <?= (int)$filasDetalle ?>;
let chartALG = null;
let chartEG = null;

const MESES = <?= json_encode($meses, JSON_UNESCAPED_UNICODE) ?>;
const CAUSAS = <?= json_encode($causas, JSON_UNESCAPED_UNICODE) ?>;

function n(v){
    return parseFloat(v || 0) || 0;
}
function pct(v, total){
    if (!total) return '0,00%';
    return ((v / total) * 100).toFixed(2).replace('.', ',') + '%';
}
function pctNumber(v, total){
    if (!total) return 0;
    return (v / total) * 100;
}
function parsePct(txt){
    return parseFloat(String(txt || '0').replace('%','').replace(',', '.')) || 0;
}
function setVal(name, value){
    const el = document.querySelector(`[name="${name}"]`);
    if (el) el.value = value;
}
function getVal(name){
    return document.querySelector(`[name="${name}"]`)?.value || '';
}
function monthIndex(m){
    return MESES.indexOf(m);
}

function recalcMensual(){
    let totalTrab = 0, totalHht = 0, totalDpeg = 0, totalDpa = 0, totalThpa = 0;
    let alg2024sum = 0, aleg2024sum = 0, count2024alg = 0, count2024aleg = 0;
    const alg2025Arr = [];
    const alg2024Arr = [];
    const aleg2025Arr = [];
    const aleg2024Arr = [];

    MESES.forEach((mes, i) => {
        const nrow = i + 1;
        const trab = n(getVal(`trab_${nrow}`));
        const hht = n(getVal(`hht_${nrow}`));
        const dt = n(getVal(`dt_${nrow}`));
        const dpeg = n(getVal(`dpeg_${nrow}`));
        const dpa = n(getVal(`dpa_${nrow}`));
        const thpa = dpa * 8;

        const alg2025 = pctNumber(dpa, dt);
        const aleg2025 = pctNumber(dpeg, dt);

        setVal(`thpa_${nrow}`, thpa ? thpa : 0);
        setVal(`alg_2025_${nrow}`, pct(dpa, dt));
        setVal(`aleg_2025_${nrow}`, pct(dpeg, dt));

        totalTrab += trab;
        totalHht += hht;
        totalDpeg += dpeg;
        totalDpa += dpa;
        totalThpa += thpa;

        const alg24 = parsePct(getVal(`alg_2024_${nrow}`));
        const aleg24 = parsePct(getVal(`aleg_2024_${nrow}`));

        alg2024Arr.push(alg24);
        aleg2024Arr.push(aleg24);
        alg2025Arr.push(alg2025);
        aleg2025Arr.push(aleg2025);

        alg2024sum += alg24; count2024alg++;
        aleg2024sum += aleg24; count2024aleg++;
    });

    document.getElementById('tot_trab').textContent = totalTrab;
    document.getElementById('tot_hht').textContent = totalHht;
    document.getElementById('tot_dpeg').textContent = totalDpeg;
    document.getElementById('tot_dpa').textContent = totalDpa;
    document.getElementById('tot_thpa').textContent = totalThpa;
    document.getElementById('tot_alg').textContent = pct(totalDpa, totalHht || 1);
    document.getElementById('tot_aleg').textContent = pct(totalDpeg, totalHht || 1);
    document.getElementById('prom_alg_2024').textContent = (alg2024sum / Math.max(count2024alg,1)).toFixed(2).replace('.', ',') + '%';
    document.getElementById('prom_aleg_2024').textContent = (aleg2024sum / Math.max(count2024aleg,1)).toFixed(2).replace('.', ',') + '%';

    const promedioW = MESES.filter((_, i) => n(getVal(`trab_${i+1}`)) > 0).length
        ? Math.round(totalTrab / MESES.filter((_, i) => n(getVal(`trab_${i+1}`)) > 0).length)
        : 0;

    document.getElementById('kpi_promedio').textContent = promedioW;
    document.getElementById('kpi_hht').textContent = totalHht;
    document.getElementById('kpi_alg').textContent = pct(totalDpa, totalHht || 1);
    document.getElementById('kpi_dias').textContent = totalDpa;
    document.getElementById('kpi_eg').textContent = pct(totalDpeg, totalHht || 1);
    document.getElementById('kpi_dias_eg').textContent = totalDpeg;

    renderCharts(alg2025Arr, alg2024Arr, aleg2025Arr, aleg2024Arr);
}

function recalcDetalle(){
    let resumenPorMes = {};
    MESES.forEach(m => {
        resumenPorMes[m] = {
            covid:0, gripa:0, enf_lab:0, enf_comun:0, acc_lab:0, licencia:0, suspension:0, permiso:0, calamidad:0,
            total:0, alxeg:0
        };
    });

    for (let i = 1; i <= detalleCount; i++) {
        const dias = n(getVal(`det_dias_${i}`));
        const horas = dias * 8;
        setVal(`det_horas_${i}`, horas ? horas : 0);

        const mes = getVal(`det_mes_${i}`);
        if (!mes || !resumenPorMes[mes]) continue;

        let rowTotal = 0;
        Object.keys(CAUSAS).forEach(c => {
            const val = n(getVal(`det_${c}_${i}`));
            resumenPorMes[mes][c] += val;
            rowTotal += val;
        });

        resumenPorMes[mes].total += rowTotal;
        resumenPorMes[mes].alxeg += (n(getVal(`det_covid_${i}`)) + n(getVal(`det_gripa_${i}`)) + n(getVal(`det_enf_lab_${i}`)) + n(getVal(`det_enf_comun_${i}`)));
    }

    const tbody = document.querySelector('#tblMotivos tbody');
    tbody.innerHTML = '';

    const sums = {
        covid:0, gripa:0, enf_lab:0, enf_comun:0, acc_lab:0, licencia:0, suspension:0, permiso:0, calamidad:0, total:0, alxeg:0
    };

    MESES.forEach(m => {
        const r = resumenPorMes[m];
        Object.keys(sums).forEach(k => sums[k] += r[k] || 0);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${m}</strong></td>
            <td class="center">${r.covid}</td>
            <td class="center">${r.gripa}</td>
            <td class="center">${r.enf_lab}</td>
            <td class="center">${r.enf_comun}</td>
            <td class="center">${r.acc_lab}</td>
            <td class="center">${r.licencia}</td>
            <td class="center">${r.suspension}</td>
            <td class="center">${r.permiso}</td>
            <td class="center">${r.calamidad}</td>
            <td class="center">${r.total}</td>
            <td class="center">${r.alxeg}</td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('sum_covid').textContent = sums.covid;
    document.getElementById('sum_gripa').textContent = sums.gripa;
    document.getElementById('sum_enf_lab').textContent = sums.enf_lab;
    document.getElementById('sum_enf_comun').textContent = sums.enf_comun;
    document.getElementById('sum_acc_lab').textContent = sums.acc_lab;
    document.getElementById('sum_licencia').textContent = sums.licencia;
    document.getElementById('sum_suspension').textContent = sums.suspension;
    document.getElementById('sum_permiso').textContent = sums.permiso;
    document.getElementById('sum_calamidad').textContent = sums.calamidad;
    document.getElementById('sum_total').textContent = sums.total;
    document.getElementById('sum_alxeg').textContent = sums.alxeg;

    let principal = 'ninguna';
    let max = 0;
    Object.keys(CAUSAS).forEach(k => {
        if (sums[k] > max) {
            max = sums[k];
            principal = CAUSAS[k];
        }
    });

    document.getElementById('analisis_eventos').textContent =
        sums.total === 0
            ? 'No hay eventos de ausentismo registrados.'
            : `Durante el periodo analizado se registran ${sums.total} días/eventos de ausentismo. La causa principal es ${principal} con ${max} registros, seguida del resto de causas según la consolidación mensual.`;

    const totalHht = n(document.getElementById('tot_hht').textContent);
    const promedio = totalHht ? ((sums.total / totalHht) * 100).toFixed(2).replace('.', ',') : '0,00';
    document.getElementById('analisis_tendencial').textContent =
        sums.total === 0
            ? 'No se observa tendencia por ausencia para el periodo.'
            : `Durante el periodo evaluado se presentó un promedio de ${promedio} días perdidos por cada 100 horas trabajadas. El comportamiento mensual puede monitorearse en las gráficas comparativas y en la tabla de consolidado por motivo.`;
}

function renderCharts(alg2025, alg2024, eg2025, eg2024){
    const labels = MESES;

    if (chartALG) chartALG.destroy();
    if (chartEG) chartEG.destroy();

    chartALG = new Chart(document.getElementById('chartALG'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label:'2025', data: alg2025 },
                { label:'2024', data: alg2024 }
            ]
        },
        options: {
            responsive:true,
            maintainAspectRatio:false,
            scales:{ y:{ beginAtZero:true } },
            plugins:{ legend:{ position:'bottom' } }
        }
    });

    chartEG = new Chart(document.getElementById('chartEG'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label:'2025', data: eg2025 },
                { label:'2024', data: eg2024 }
            ]
        },
        options: {
            responsive:true,
            maintainAspectRatio:false,
            scales:{ y:{ beginAtZero:true } },
            plugins:{ legend:{ position:'bottom' } }
        }
    });
}

function agregarFilaDetalle(){
    detalleCount++;
    const tbody = document.getElementById('tbodyDetalle');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td><input type="text" name="det_nombre_${detalleCount}"></td>
        <td><input type="text" name="det_cc_${detalleCount}"></td>
        <td><input type="text" name="det_area_${detalleCount}"></td>
        <td><input type="date" name="det_fecha_${detalleCount}"></td>
        <td>
            <select name="det_mes_${detalleCount}">
                <option value=""></option>
                ${MESES.map(m => `<option value="${m}">${m}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" step="1" name="det_dias_${detalleCount}"></td>
        <td><input class="readonly" type="number" step="1" name="det_horas_${detalleCount}" readonly></td>
        <td><input type="number" step="1" name="det_covid_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_gripa_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_enf_lab_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_enf_comun_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_acc_lab_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_licencia_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_suspension_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_permiso_${detalleCount}"></td>
        <td><input type="number" step="1" name="det_calamidad_${detalleCount}"></td>
    `;

    tbody.appendChild(tr);
}

function recalcAll(){
    recalcMensual();
    recalcDetalle();
}

document.addEventListener('input', function(e){
    if (e.target.closest('#formAusentismo')) recalcAll();
});

document.addEventListener('change', function(e){
    if (e.target.closest('#formAusentismo')) recalcAll();
});

document.addEventListener('DOMContentLoaded', function(){
    recalcAll();
});
</script>

</body>
</html>