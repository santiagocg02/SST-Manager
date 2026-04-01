<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['emergencia_form'] = $_POST;
    $success = "Datos guardados correctamente en memoria del formulario.";
}

$data = $_SESSION['emergencia_form'] ?? [];

function old($key, $default = ''){
    global $data;
    return htmlspecialchars($data[$key] ?? $default);
}

$meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

$actividades = [
    ['tipo'=>'seccion','texto'=>'PLANEAR: Planeación de Actividades del programa de Emergencias'],
    ['id'=>'a1','actividad'=>'Revisar cumplimiento del Programa en el año','recurso'=>'0'],
    ['id'=>'a2','actividad'=>'Realizar análisis de necesidades del programa y diseñar el programa','recurso'=>'0'],
    ['id'=>'a3','actividad'=>'Revisar y actualizar objetivo, metas e indicadores de gestión del programa','recurso'=>'0'],
    ['id'=>'a4','actividad'=>'Incluir en el presupuesto el costo de las actividades autorizadas a realizar','recurso'=>'0'],

    ['tipo'=>'seccion','texto'=>'HACER: Implementación del Programa Emergencias'],
    ['id'=>'a5','actividad'=>'Divulgar el programa de Emergencias al personal','recurso'=>'0'],
    ['id'=>'a6','actividad'=>'Verificar el personal que integra las brigadas y divulgar al personal','recurso'=>'0'],
    ['id'=>'a7','actividad'=>'Actualizar la matriz de vulnerabilidad','recurso'=>'0'],
    ['id'=>'a8','actividad'=>'Revisar y actualizar Plan de Emergencias','recurso'=>'0'],
    ['id'=>'a9','actividad'=>'Revisar y actualizar los protocolos de emergencias','recurso'=>'0'],
    ['id'=>'a10','actividad'=>'Socializar plan de emergencias / plan de contingencia','recurso'=>'0'],
    ['id'=>'a11','actividad'=>'Divulgar directorio de emergencias al personal','recurso'=>'0'],
    ['id'=>'a12','actividad'=>'Socializar por charlas 5 minutos protocolos, directorios, equipos, rutas y puntos de encuentro','recurso'=>'0'],
    ['id'=>'a13','actividad'=>'Reunión de brigadas de emergencias','recurso'=>'0'],
    ['id'=>'a14','actividad'=>'Realizar simulacros','recurso'=>'0'],
    ['id'=>'a15','actividad'=>'Simulacros internos','recurso'=>'600000'],

    ['tipo'=>'seccion','texto'=>'CAPACITACIONES'],
    ['id'=>'a16','actividad'=>'Capacitación o taller práctico en primeros auxilios','recurso'=>'62500'],
    ['id'=>'a17','actividad'=>'Capacitación o taller práctico en control de incendios','recurso'=>'62500'],
    ['id'=>'a18','actividad'=>'Capacitación o taller práctico en control de derrames','recurso'=>'62500'],

    ['tipo'=>'seccion','texto'=>'SIMULACRO GRUPOS DE APOYO'],
    ['id'=>'a19','actividad'=>'Divulgación de plan de contingencias a entidades de apoyo','recurso'=>'600000'],
    ['id'=>'a20','actividad'=>'Planeación del simulacro','recurso'=>'0'],
    ['id'=>'a21','actividad'=>'Elaboración del guion del simulacro','recurso'=>'0'],
    ['id'=>'a22','actividad'=>'Envío comunicaciones a partes interesadas','recurso'=>'50000'],
    ['id'=>'a23','actividad'=>'Retroalimentación inmediata en campo sobre observaciones del simulacro','recurso'=>'0'],
    ['id'=>'a24','actividad'=>'Elaboración de informe del simulacro','recurso'=>'0'],

    ['tipo'=>'seccion','texto'=>'EVALUACIÓN DEL PROGRAMA DE GESTIÓN'],
    ['id'=>'a25','actividad'=>'Seguimiento al cumplimiento de los objetivos','recurso'=>'0'],
    ['id'=>'a26','actividad'=>'Análisis tendencial al cumplimiento de los objetivos del programa de gestión','recurso'=>'0'],
    ['id'=>'a27','actividad'=>'Ajustes al Programa de Gestión, según revisión al programa','recurso'=>'0'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programa de Emergencias</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root{
            --soporte-bg:#eef3f9;
            --soporte-card:#ffffff;
            --soporte-border:#1f1f1f;
            --soporte-toolbar:#dde7f5;
            --soporte-toolbar-border:#c8d3e2;
            --soporte-toolbar-title:#213b67;
        }

        html, body{
            background:var(--soporte-bg) !important;
            font-family:Arial, Helvetica, sans-serif;
            font-size:12px;
            color:#111;
        }

        .toolbar{
            position:sticky;
            top:0;
            z-index:1000;
            background:var(--soporte-toolbar);
            border-bottom:1px solid var(--soporte-toolbar-border);
            padding:12px 18px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
        }

        .toolbar h1{
            margin:0;
            font-size:22px;
            font-weight:800;
            color:var(--soporte-toolbar-title);
        }

        .toolbar .btn-group-custom{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .sheet{
            max-width:1600px;
            margin:18px auto;
            background:#fff;
            padding:14px;
            box-shadow:0 0 0 1px #cfd8e3;
        }

        .sst-table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .sst-table th,
        .sst-table td{
            border:1px solid #666;
            padding:3px 4px;
            vertical-align:middle;
            word-break:break-word;
            overflow-wrap:break-word;
        }

        .sst-table th{
            background:#f1f1f1;
            text-align:center;
            font-weight:700;
        }

        .head-center{
            text-align:center;
            font-weight:700;
        }

        .logo-box{
            border:1px dashed #999;
            min-height:50px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:11px;
            color:#777;
        }

        .title-main{
            font-size:13px;
            font-weight:700;
            text-align:center;
        }

        .title-sub{
            font-size:12px;
            font-weight:700;
            text-align:center;
        }

        .section-gray{
            background:#bfbfbf !important;
            font-weight:700;
        }

        .subsection{
            background:#d9d9d9 !important;
            font-weight:700;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select{
            width:100%;
            max-width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            box-shadow:none;
            font-size:12px;
            padding:2px 3px;
        }

        textarea{
            resize:vertical;
            min-height:90px;
        }

        .cell-num{
            text-align:center;
        }

        .cell-num input{
            text-align:center;
        }

        .actividad-col{ width: 360px; }
        .recurso-col{ width: 90px; }
        .mes-col{ width: 44px; }
        .resp-col{ width: 90px; }
        .acum-col{ width: 70px; }

        .chart-card{
            border:1px solid #cfd8e3;
            background:#fff;
            padding:10px;
            margin-top:10px;
        }

        .small-title{
            font-weight:700;
            text-align:center;
            margin-bottom:8px;
            font-size:12px;
        }

        .success-msg{
            background:#d1e7dd;
            color:#0f5132;
            border:1px solid #badbcc;
            padding:10px 12px;
            margin-bottom:12px;
            border-radius:6px;
        }

        .percent-box{
            font-weight:800;
            text-align:center;
            background:#00ff4c !important;
        }

        .analysis-table td{
            min-height:160px;
            vertical-align:top;
        }

        canvas{
            width:100% !important;
            height:320px !important;
        }

        .canvas-sm{
            height:240px !important;
        }

        @media print{
            .toolbar,
            .no-print{
                display:none !important;
            }

            body{
                background:#fff !important;
            }

            .sheet{
                box-shadow:none !important;
                margin:0 !important;
                max-width:100% !important;
                padding:0 !important;
            }

            input, textarea, select{
                border:none !important;
                box-shadow:none !important;
                background:transparent !important;
            }

            @page{
                size:landscape;
                margin:8mm;
            }
        }
    </style>
</head>
<body>

<div class="toolbar no-print">
    <h1>Programa de Emergencias</h1>
    <div class="btn-group-custom">
        <button class="btn btn-success" onclick="document.getElementById('form-emergencia').submit()">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
        <button class="btn btn-secondary" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i> Atrás
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>
</div>

<div class="sheet">
    <?php if (!empty($success)): ?>
        <div class="success-msg no-print"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" id="form-emergencia">
        <table class="sst-table mb-2">
            <colgroup>
                <col style="width:150px;">
                <col>
                <col style="width:120px;">
            </colgroup>
            <tr>
                <td rowspan="2">
                    <div class="logo-box">TU LOGO AQUÍ</div>
                </td>
                <td class="title-main">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td class="head-center">PR-SST-11</td>
            </tr>
            <tr>
                <td class="title-sub">PROGRAMA DE EMERGENCIAS</td>
                <td class="head-center">XX/XX/2025</td>
            </tr>
        </table>

        <table class="sst-table mb-2">
            <tr>
                <th style="width:160px;">OBJETIVO</th>
                <td>
                    <input type="text" name="objetivo" value="<?= old('objetivo','EVALUAR EL CONOCIMIENTO Y ENTENDIMIENTO DEL PLAN DE EMERGENCIAS Y CONTINGENCIAS POR PARTE DEL PERSONAL Y DEMÁS PARTES INTERESADAS DE LA EMPRESA') ?>">
                </td>
            </tr>
            <tr>
                <th>META</th>
                <td>
                    <textarea name="meta">1. REALIZAR MÍNIMO 2 SIMULACROS EN EL AÑO, 1 INTERNO Y 1 CON PARTES INTERESADAS EXTERNAS
2. IMPLEMENTAR MÍNIMO EL 90% DE LAS RECOMENDACIONES CORRESPONDIENTES QUE SE GENEREN EN LOS SIMULACROS REALIZADOS DURANTE EL PERIODO
3. TENER LA PARTICIPACIÓN MÍNIMO DEL 80% DE LAS PERSONAS EN LAS SOCIALIZACIONES DEL PLAN DE EMERGENCIAS</textarea>
                </td>
            </tr>
            <tr>
                <th>INDICADOR</th>
                <td>
                    <textarea name="indicador">Indicador 1: N° de Simulacros Realizados
Indicador 2: N° de oportunidades de mejora implementadas / total de oportunidades de mejora generadas
Indicador 3: N° Personas capacitadas / N° personas programadas</textarea>
                </td>
            </tr>
        </table>

        <table class="sst-table" id="tablaCronograma">
            <colgroup>
                <col class="actividad-col">
                <col class="recurso-col">
                <?php for($i=0;$i<12;$i++): ?>
                    <col class="mes-col">
                    <col class="mes-col">
                <?php endfor; ?>
                <col class="resp-col">
                <col class="acum-col">
                <col class="acum-col">
            </colgroup>

            <tr>
                <th rowspan="2">ACTIVIDADES</th>
                <th rowspan="2">RECURSOS</th>
                <?php
                $nombresMeses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
                foreach($nombresMeses as $m){
                    echo "<th colspan='2'>{$m}</th>";
                }
                ?>
                <th rowspan="2">RESPONSABLE</th>
                <th colspan="2">ACUM ANUAL</th>
            </tr>
            <tr>
                <?php for($i=0;$i<12;$i++): ?>
                    <th>P</th>
                    <th>E</th>
                <?php endfor; ?>
                <th>P</th>
                <th>E</th>
            </tr>

            <?php foreach($actividades as $item): ?>
                <?php if(isset($item['tipo']) && $item['tipo']==='seccion'): ?>
                    <tr>
                        <td colspan="29" class="section-gray"><?= htmlspecialchars($item['texto']) ?></td>
                    </tr>
                <?php else: ?>
                    <tr class="fila-actividad">
                        <td><?= htmlspecialchars($item['actividad']) ?></td>
                        <td class="cell-num">
                            <input type="number" step="0.01" class="recurso-input" name="recurso_<?= $item['id'] ?>" value="<?= old('recurso_'.$item['id'], $item['recurso']) ?>">
                        </td>

                        <?php foreach($meses as $mes): ?>
                            <td class="cell-num">
                                <input type="number" min="0" class="prog mes-p" name="p_<?= $item['id'] ?>_<?= $mes ?>" value="<?= old('p_'.$item['id'].'_'.$mes, '') ?>">
                            </td>
                            <td class="cell-num">
                                <input type="number" min="0" class="eje mes-e" name="e_<?= $item['id'] ?>_<?= $mes ?>" value="<?= old('e_'.$item['id'].'_'.$mes, '') ?>">
                            </td>
                        <?php endforeach; ?>

                        <td>
                            <input type="text" name="resp_<?= $item['id'] ?>" value="<?= old('resp_'.$item['id'], '') ?>">
                        </td>
                        <td class="cell-num total-p">0</td>
                        <td class="cell-num total-e">0</td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>

            <tr>
                <td class="subsection">RECURSOS NECESARIOS</td>
                <td class="cell-num" id="totalRecursos">$ 0</td>
                <?php foreach($nombresMeses as $m): ?>
                    <td colspan="2" class="head-center"><?= $m ?></td>
                <?php endforeach; ?>
                <td class="head-center" id="granTotalP">0</td>
                <td class="percent-box" id="granPorcentaje">0%</td>
            </tr>
        </table>

        <br>

        <table class="sst-table mb-2" id="tablaMonitoreo">
            <tr>
                <th>MONITOREO ACTIVIDADES DEL PROGRAMA</th>
                <?php foreach($nombresMeses as $m): ?>
                    <th><?= $m ?></th>
                <?php endforeach; ?>
                <th>TOTAL</th>
            </tr>
            <tr>
                <th>ACTIVIDADES PROGRAMADAS</th>
                <?php foreach($meses as $mes): ?>
                    <td class="cell-num" id="mon_p_<?= $mes ?>">0</td>
                <?php endforeach; ?>
                <td class="cell-num" id="mon_total_p">0</td>
            </tr>
            <tr>
                <th>ACTIVIDADES EJECUTADAS</th>
                <?php foreach($meses as $mes): ?>
                    <td class="cell-num" id="mon_e_<?= $mes ?>">0</td>
                <?php endforeach; ?>
                <td class="cell-num" id="mon_total_e">0</td>
            </tr>
            <tr>
                <th>PORCENTAJE DE CUMPLIMIENTO</th>
                <?php foreach($meses as $mes): ?>
                    <td class="cell-num" id="mon_pct_<?= $mes ?>">0%</td>
                <?php endforeach; ?>
                <td class="cell-num" id="mon_total_pct">0%</td>
            </tr>
        </table>

        <div class="chart-card">
            <div class="small-title">PORCENTAJE DE CUMPLIMIENTO</div>
            <canvas id="chartCumplimiento"></canvas>
        </div>

        <br>

        <table class="sst-table mb-2" id="tablaIndicador1">
            <tr>
                <th colspan="7">INDICADOR 1: NÚMERO DE SIMULACROS REALIZADOS</th>
            </tr>
            <tr>
                <th rowspan="2">MES</th>
                <th colspan="2">SIMULACROS INTERNOS</th>
                <th colspan="2">SIMULACROS EXTERNOS</th>
                <th colspan="2">TOTAL AÑO</th>
            </tr>
            <tr>
                <th>P</th><th>E</th>
                <th>P</th><th>E</th>
                <th>P</th><th>E</th>
            </tr>
            <?php
            $trimestres = [
                'Primer Trimestre'=>'t1',
                'Segundo Trimestre'=>'t2',
                'Tercer Trimestre'=>'t3',
                'Cuarto Trimestre'=>'t4'
            ];
            foreach($trimestres as $label=>$key):
            ?>
            <tr>
                <td><?= $label ?></td>
                <td><input type="number" min="0" class="i1" data-group="<?= $key ?>" data-col="pi" name="i1_pi_<?= $key ?>" value="<?= old('i1_pi_'.$key,'0') ?>"></td>
                <td><input type="number" min="0" class="i1" data-group="<?= $key ?>" data-col="ei" name="i1_ei_<?= $key ?>" value="<?= old('i1_ei_'.$key,'0') ?>"></td>
                <td><input type="number" min="0" class="i1" data-group="<?= $key ?>" data-col="pe" name="i1_pe_<?= $key ?>" value="<?= old('i1_pe_'.$key,'0') ?>"></td>
                <td><input type="number" min="0" class="i1" data-group="<?= $key ?>" data-col="ee" name="i1_ee_<?= $key ?>" value="<?= old('i1_ee_'.$key,'0') ?>"></td>
                <td class="cell-num i1-total-p" id="i1_tp_<?= $key ?>">0</td>
                <td class="cell-num i1-total-e" id="i1_te_<?= $key ?>">0</td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th>TOTAL</th>
                <th id="i1_total_pi">0</th>
                <th id="i1_total_ei">0</th>
                <th id="i1_total_pe">0</th>
                <th id="i1_total_ee">0</th>
                <th id="i1_total_p">0</th>
                <th id="i1_total_e">0</th>
            </tr>
        </table>

        <div class="chart-card">
            <div class="small-title">SIMULACROS INTERNOS / EXTERNOS</div>
            <canvas id="chartIndicador1" class="canvas-sm"></canvas>
        </div>

        <br>

        <table class="sst-table mb-2" id="tablaIndicador2">
            <tr>
                <th colspan="5">INDICADOR 2: RECOMENDACIONES IMPLEMENTADAS</th>
            </tr>
            <tr>
                <th>SIMULACROS</th>
                <th>No Total de Recomendaciones</th>
                <th>No de recomendaciones implementadas</th>
                <th>%</th>
                <th>META</th>
            </tr>
            <tr>
                <td>1° Semestre</td>
                <td><input type="number" min="0" id="i2_total_1" name="i2_total_1" value="<?= old('i2_total_1','0') ?>"></td>
                <td><input type="number" min="0" id="i2_impl_1" name="i2_impl_1" value="<?= old('i2_impl_1','0') ?>"></td>
                <td class="cell-num" id="i2_pct_1">0%</td>
                <td><input type="number" min="0" id="i2_meta_1" name="i2_meta_1" value="<?= old('i2_meta_1','90') ?>"></td>
            </tr>
            <tr>
                <td>2° Semestre</td>
                <td><input type="number" min="0" id="i2_total_2" name="i2_total_2" value="<?= old('i2_total_2','0') ?>"></td>
                <td><input type="number" min="0" id="i2_impl_2" name="i2_impl_2" value="<?= old('i2_impl_2','0') ?>"></td>
                <td class="cell-num" id="i2_pct_2">0%</td>
                <td><input type="number" min="0" id="i2_meta_2" name="i2_meta_2" value="<?= old('i2_meta_2','90') ?>"></td>
            </tr>
            <tr>
                <th>TOTAL</th>
                <th id="i2_total_sum">0</th>
                <th id="i2_impl_sum">0</th>
                <th id="i2_pct_sum">0%</th>
                <th id="i2_meta_prom">90%</th>
            </tr>
        </table>

        <div class="chart-card">
            <div class="small-title">RECOMENDACIONES IMPLEMENTADAS</div>
            <canvas id="chartIndicador2" class="canvas-sm"></canvas>
        </div>

        <br>

        <table class="sst-table mb-2" id="tablaIndicador3">
            <tr>
                <th colspan="5">INDICADOR 3: PARTICIPACIÓN DEL PERSONAL EN SOCIALIZACIONES DEL PLAN DE EMERGENCIAS</th>
            </tr>
            <tr>
                <th>SIMULACROS</th>
                <th>Programados</th>
                <th>Participantes</th>
                <th>%</th>
                <th>META</th>
            </tr>
            <tr>
                <td>1er Semestre</td>
                <td><input type="number" min="0" id="i3_prog_1" name="i3_prog_1" value="<?= old('i3_prog_1','0') ?>"></td>
                <td><input type="number" min="0" id="i3_part_1" name="i3_part_1" value="<?= old('i3_part_1','0') ?>"></td>
                <td class="cell-num" id="i3_pct_1">0%</td>
                <td><input type="number" min="0" id="i3_meta_1" name="i3_meta_1" value="<?= old('i3_meta_1','90') ?>"></td>
            </tr>
            <tr>
                <td>2do Semestre</td>
                <td><input type="number" min="0" id="i3_prog_2" name="i3_prog_2" value="<?= old('i3_prog_2','0') ?>"></td>
                <td><input type="number" min="0" id="i3_part_2" name="i3_part_2" value="<?= old('i3_part_2','0') ?>"></td>
                <td class="cell-num" id="i3_pct_2">0%</td>
                <td><input type="number" min="0" id="i3_meta_2" name="i3_meta_2" value="<?= old('i3_meta_2','90') ?>"></td>
            </tr>
            <tr>
                <th>TOTAL</th>
                <th id="i3_prog_sum">0</th>
                <th id="i3_part_sum">0</th>
                <th id="i3_pct_sum">0%</th>
                <th id="i3_meta_prom">90%</th>
            </tr>
        </table>

        <div class="chart-card">
            <div class="small-title">PARTICIPACIÓN DEL PERSONAL</div>
            <canvas id="chartIndicador3" class="canvas-sm"></canvas>
        </div>

        <br>

        <table class="sst-table analysis-table">
            <tr>
                <th style="width:43%;">ANÁLISIS DE DATOS</th>
                <th style="width:8%;">FECHA</th>
                <th style="width:10%;">RESPONSABLE</th>
                <th style="width:17%;">PLAN DE ACCIÓN</th>
                <th style="width:14%;">RESULTADO DEL SEGUIMIENTO</th>
                <th style="width:8%;">FECHA</th>
            </tr>
            <tr>
                <td>
                    <textarea name="analisis_datos" style="min-height:260px;"><?= old('analisis_datos',"PRIMER SEMESTRE:
Meta 1 SIMULACROS REALIZADOS:
Meta 2 RECOMENDACIONES ESTABLECIDAS:
Meta 3 DIVULGACIÓN DEL PLAN DE EMERGENCIAS:
CUMPLIMIENTO:

SEGUNDO SEMESTRE:
Meta 1 SIMULACROS REALIZADOS:
Meta 2 RECOMENDACIONES ESTABLECIDAS:
Meta 3 DIVULGACIÓN DEL PLAN DE EMERGENCIAS:
CUMPLIMIENTO:") ?></textarea>
                </td>
                <td><textarea name="fecha_analisis_1" style="min-height:260px;"><?= old('fecha_analisis_1','') ?></textarea></td>
                <td><textarea name="responsable_analisis" style="min-height:260px;"><?= old('responsable_analisis','') ?></textarea></td>
                <td><textarea name="plan_accion" style="min-height:260px;"><?= old('plan_accion','') ?></textarea></td>
                <td><textarea name="resultado_seguimiento" style="min-height:260px;"><?= old('resultado_seguimiento','') ?></textarea></td>
                <td><textarea name="fecha_analisis_2" style="min-height:260px;"><?= old('fecha_analisis_2','') ?></textarea></td>
            </tr>
        </table>
    </form>
</div>

<script>
const meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

function num(v){
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
}

function fmtMoney(value){
    return '$ ' + Number(value).toLocaleString('es-CO', {maximumFractionDigits: 0});
}

function pct(a, b){
    if (b <= 0) return 0;
    return (a / b) * 100;
}

function actualizarCronograma(){
    let granTotalP = 0;
    let granTotalE = 0;
    let totalRecursos = 0;

    const monP = {};
    const monE = {};
    meses.forEach(m => { monP[m] = 0; monE[m] = 0; });

    document.querySelectorAll('#tablaCronograma .fila-actividad').forEach(fila => {
        let totalP = 0;
        let totalE = 0;

        const recurso = num(fila.querySelector('.recurso-input')?.value || 0);
        totalRecursos += recurso;

        meses.forEach(mes => {
            const pInput = fila.querySelector(`input[name*="_${mes}"]${''}`);
        });

        fila.querySelectorAll('.prog').forEach(inp => totalP += num(inp.value));
        fila.querySelectorAll('.eje').forEach(inp => totalE += num(inp.value));

        meses.forEach(mes => {
            const p = num(fila.querySelector(`[name^="p_"][name$="_${mes}"]`)?.value || 0);
            const e = num(fila.querySelector(`[name^="e_"][name$="_${mes}"]`)?.value || 0);
            monP[mes] += p;
            monE[mes] += e;
        });

        fila.querySelector('.total-p').textContent = totalP;
        fila.querySelector('.total-e').textContent = totalE;

        granTotalP += totalP;
        granTotalE += totalE;
    });

    document.getElementById('granTotalP').textContent = granTotalP;
    document.getElementById('totalRecursos').textContent = fmtMoney(totalRecursos);

    const totalPct = pct(granTotalE, granTotalP);
    document.getElementById('granPorcentaje').textContent = totalPct.toFixed(0) + '%';

    let totalMonP = 0;
    let totalMonE = 0;
    const labels = [];
    const dataPct = [];

    meses.forEach(mes => {
        document.getElementById(`mon_p_${mes}`).textContent = monP[mes];
        document.getElementById(`mon_e_${mes}`).textContent = monE[mes];
        const p = pct(monE[mes], monP[mes]);
        document.getElementById(`mon_pct_${mes}`).textContent = p.toFixed(0) + '%';

        totalMonP += monP[mes];
        totalMonE += monE[mes];

        labels.push(mes.toUpperCase());
        dataPct.push(Number(p.toFixed(2)));
    });

    document.getElementById('mon_total_p').textContent = totalMonP;
    document.getElementById('mon_total_e').textContent = totalMonE;
    document.getElementById('mon_total_pct').textContent = pct(totalMonE, totalMonP).toFixed(0) + '%';

    chartCumplimiento.data.labels = labels;
    chartCumplimiento.data.datasets[0].data = dataPct;
    chartCumplimiento.update();
}

function actualizarIndicador1(){
    const grupos = ['t1','t2','t3','t4'];
    let total_pi = 0, total_ei = 0, total_pe = 0, total_ee = 0, total_p = 0, total_e = 0;
    const labels = ['1° TRIMESTRE','2° TRIMESTRE','3° TRIMESTRE','4° TRIMESTRE'];
    const internos = [];
    const externos = [];

    grupos.forEach((g, index) => {
        const pi = num(document.querySelector(`[name="i1_pi_${g}"]`).value);
        const ei = num(document.querySelector(`[name="i1_ei_${g}"]`).value);
        const pe = num(document.querySelector(`[name="i1_pe_${g}"]`).value);
        const ee = num(document.querySelector(`[name="i1_ee_${g}"]`).value);

        const tp = pi + pe;
        const te = ei + ee;

        document.getElementById(`i1_tp_${g}`).textContent = tp;
        document.getElementById(`i1_te_${g}`).textContent = te;

        total_pi += pi; total_ei += ei;
        total_pe += pe; total_ee += ee;
        total_p += tp; total_e += te;

        internos.push(ei);
        externos.push(ee);
    });

    document.getElementById('i1_total_pi').textContent = total_pi;
    document.getElementById('i1_total_ei').textContent = total_ei;
    document.getElementById('i1_total_pe').textContent = total_pe;
    document.getElementById('i1_total_ee').textContent = total_ee;
    document.getElementById('i1_total_p').textContent = total_p;
    document.getElementById('i1_total_e').textContent = total_e;

    chartIndicador1.data.labels = labels;
    chartIndicador1.data.datasets[0].data = internos;
    chartIndicador1.data.datasets[1].data = externos;
    chartIndicador1.update();
}

function actualizarIndicador2(){
    const t1 = num(document.getElementById('i2_total_1').value);
    const i1 = num(document.getElementById('i2_impl_1').value);
    const m1 = num(document.getElementById('i2_meta_1').value);

    const t2 = num(document.getElementById('i2_total_2').value);
    const i2 = num(document.getElementById('i2_impl_2').value);
    const m2 = num(document.getElementById('i2_meta_2').value);

    const p1 = pct(i1, t1);
    const p2 = pct(i2, t2);
    const tt = t1 + t2;
    const ti = i1 + i2;
    const pt = pct(ti, tt);
    const metaProm = (m1 + m2) / 2;

    document.getElementById('i2_pct_1').textContent = p1.toFixed(0) + '%';
    document.getElementById('i2_pct_2').textContent = p2.toFixed(0) + '%';
    document.getElementById('i2_total_sum').textContent = tt;
    document.getElementById('i2_impl_sum').textContent = ti;
    document.getElementById('i2_pct_sum').textContent = pt.toFixed(0) + '%';
    document.getElementById('i2_meta_prom').textContent = metaProm.toFixed(0) + '%';

    chartIndicador2.data.labels = ['1° Semestre', '2° Semestre'];
    chartIndicador2.data.datasets[0].data = [p1, p2];
    chartIndicador2.data.datasets[1].data = [m1, m2];
    chartIndicador2.update();
}

function actualizarIndicador3(){
    const p1v = num(document.getElementById('i3_prog_1').value);
    const a1v = num(document.getElementById('i3_part_1').value);
    const m1 = num(document.getElementById('i3_meta_1').value);

    const p2v = num(document.getElementById('i3_prog_2').value);
    const a2v = num(document.getElementById('i3_part_2').value);
    const m2 = num(document.getElementById('i3_meta_2').value);

    const pct1 = pct(a1v, p1v);
    const pct2 = pct(a2v, p2v);

    const sumProg = p1v + p2v;
    const sumPart = a1v + a2v;
    const sumPct = pct(sumPart, sumProg);
    const metaProm = (m1 + m2) / 2;

    document.getElementById('i3_pct_1').textContent = pct1.toFixed(0) + '%';
    document.getElementById('i3_pct_2').textContent = pct2.toFixed(0) + '%';
    document.getElementById('i3_prog_sum').textContent = sumProg;
    document.getElementById('i3_part_sum').textContent = sumPart;
    document.getElementById('i3_pct_sum').textContent = sumPct.toFixed(0) + '%';
    document.getElementById('i3_meta_prom').textContent = metaProm.toFixed(0) + '%';

    chartIndicador3.data.labels = ['1er Semestre', '2do Semestre'];
    chartIndicador3.data.datasets[0].data = [pct1, pct2];
    chartIndicador3.data.datasets[1].data = [m1, m2];
    chartIndicador3.update();
}

const chartCumplimiento = new Chart(document.getElementById('chartCumplimiento'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Porcentaje de cumplimiento',
            data: []
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

const chartIndicador1 = new Chart(document.getElementById('chartIndicador1'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [
            { label: 'Simulacros internos ejecutados', data: [] },
            { label: 'Simulacros externos ejecutados', data: [] }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

const chartIndicador2 = new Chart(document.getElementById('chartIndicador2'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [
            { label: '% ejecutado', data: [] },
            { label: 'Meta', data: [] }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

const chartIndicador3 = new Chart(document.getElementById('chartIndicador3'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [
            { label: '% participación', data: [] },
            { label: 'Meta', data: [] }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

document.querySelectorAll('input').forEach(el => {
    el.addEventListener('input', () => {
        actualizarCronograma();
        actualizarIndicador1();
        actualizarIndicador2();
        actualizarIndicador3();
    });
});

actualizarCronograma();
actualizarIndicador1();
actualizarIndicador2();
actualizarIndicador3();
</script>

</body>
</html>