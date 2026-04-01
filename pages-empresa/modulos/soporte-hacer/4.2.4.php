<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../index.php');
    exit;
}

function post($key, $default = '')
{
    return isset($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

$datos = [
    'version' => post('version', '0'),
    'codigo' => post('codigo', 'PR-SST-02'),
    'fecha_documento' => post('fecha_documento', 'XX/XX/2025'),
    'empresa' => post('empresa', ''),
    'objetivo' => post('objetivo', 'Prevenir la ocurrencia de accidentes y enfermedades laborales asociados a las actividades de la compañía por medio de las inspecciones.'),
    'alcance' => post('alcance', 'Este programa aplica para todas las áreas de trabajo de la empresa.'),
    'recursos' => post('recursos', 'Económicos, técnicos, humanos e infraestructura.'),
    'documentos' => post('documentos', 'Legislación aplicable, plan de trabajo, matriz de identificación de peligros y procedimientos internos.'),
];

$actividades = isset($_POST['actividades']) && is_array($_POST['actividades']) ? $_POST['actividades'] : [
    [
        'fase' => 'PLANEAR',
        'actividad' => 'Establecer objetivos y metas',
        'responsable' => 'SST',
        'alcance' => 'General',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'PLANEAR',
        'actividad' => 'Establecer indicadores de gestión',
        'responsable' => 'SST',
        'alcance' => 'General',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'PLANEAR',
        'actividad' => 'Establecer los mecanismos para controlar el riesgo',
        'responsable' => 'SST',
        'alcance' => 'General',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'HACER',
        'actividad' => 'Inspección a vehículos',
        'responsable' => 'SST',
        'alcance' => 'Operativo',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'HACER',
        'actividad' => 'Inspección a herramientas',
        'responsable' => 'SST',
        'alcance' => 'Operativo',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'HACER',
        'actividad' => 'Inspección a botiquín',
        'responsable' => 'SST',
        'alcance' => 'Operativo',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'HACER',
        'actividad' => 'Inspecciones locativas',
        'responsable' => 'SST',
        'alcance' => 'Operativo',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'HACER',
        'actividad' => 'Inspecciones de extintores',
        'responsable' => 'SST',
        'alcance' => 'Operativo',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'HACER',
        'actividad' => 'Inspección gerencial',
        'responsable' => 'SST',
        'alcance' => 'Gerencial',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'VERIFICAR',
        'actividad' => 'Seguimiento a indicadores',
        'responsable' => 'SST',
        'alcance' => 'General',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'VERIFICAR',
        'actividad' => 'Seguimiento a las acciones tomadas frente a los hallazgos',
        'responsable' => 'SST',
        'alcance' => 'General',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
    [
        'fase' => 'ACTUAR',
        'actividad' => 'Implementación de acciones correctivas y preventivas',
        'responsable' => 'SST',
        'alcance' => 'General',
        'recursos' => '',
        'documentos' => '',
        'meses' => ['0','0','0','0','0','0','0','0','0','0','0','0']
    ],
];

$indicador1 = isset($_POST['indicador1']) && is_array($_POST['indicador1']) ? $_POST['indicador1'] : [
    'nombre' => 'Cumplimiento',
    'interpretacion' => 'Cumplimiento de actividades en el programa',
    'factor_mide' => 'Actividades ejecutadas dentro del cronograma',
    'periodicidad' => 'Semestral',
    'fuente' => 'Plan de trabajo',
    'responsable' => 'Encargado del SG-SST',
    'deben_conocer' => 'Coordinador HSEQ',
    'meta' => '0',
    'numerador_nombre' => 'No. de actividades realizadas',
    'denominador_nombre' => 'No. de actividades programadas',
    'numerador' => ['0','0','0','0','0','0','0','0','0','0','0','0'],
    'denominador' => ['0','0','0','0','0','0','0','0','0','0','0','0'],
    'plan1' => '',
    'plazo1' => '',
    'responsable1' => '',
    'accion1' => '',
    'plan2' => '',
    'plazo2' => '',
    'responsable2' => '',
    'accion2' => '',
];

$indicador2 = isset($_POST['indicador2']) && is_array($_POST['indicador2']) ? $_POST['indicador2'] : [
    'nombre' => 'Eficacia',
    'interpretacion' => 'Eficacia de los planes de acción propuestos',
    'factor_mide' => 'Condiciones peligrosas intervenidas frente a condiciones reportadas',
    'periodicidad' => 'Semestral',
    'fuente' => 'Plan de acción',
    'responsable' => 'Encargado del SG-SST',
    'deben_conocer' => 'Coordinador HSEQ',
    'meta' => '0',
    'numerador_nombre' => 'No. de condiciones peligrosas intervenidas',
    'denominador_nombre' => 'No. de condiciones peligrosas reportadas',
    'numerador' => ['0','0','0','0','0','0','0','0','0','0','0','0'],
    'denominador' => ['0','0','0','0','0','0','0','0','0','0','0','0'],
    'plan1' => '',
    'plazo1' => '',
    'responsable1' => '',
    'accion1' => '',
    'plan2' => '',
    'plazo2' => '',
    'responsable2' => '',
    'accion2' => '',
];

function toNum($value)
{
    return is_numeric($value) ? (float)$value : 0;
}

function calcIndicadorMes($num, $den)
{
    $n = toNum($num);
    $d = toNum($den);
    return $d > 0 ? round(($n / $d) * 100, 2) : 0;
}

function fmtPct($n)
{
    return number_format((float)$n, 0, ',', '.') . '%';
}

$valores1 = [];
$valores2 = [];
for ($i = 0; $i < 12; $i++) {
    $valores1[] = calcIndicadorMes($indicador1['numerador'][$i] ?? 0, $indicador1['denominador'][$i] ?? 0);
    $valores2[] = calcIndicadorMes($indicador2['numerador'][$i] ?? 0, $indicador2['denominador'][$i] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.2.4 Programa de Inspecciones</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1680px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:20px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}
        .contenido{padding:18px}
        .save-msg{
            margin:0 0 15px 0;padding:10px 14px;border-radius:8px;background:#e9f7ef;color:#166534;
            border:1px solid #b7e4c7;font-size:14px;font-weight:700;
        }

        table{width:100%;border-collapse:collapse;table-layout:fixed}
        .encabezado td,.encabezado th,
        .tabla-datos td,.tabla-datos th,
        .tabla-cronograma td,.tabla-cronograma th,
        .tabla-ficha td,.tabla-ficha th,
        .tabla-analisis td,.tabla-analisis th{
            border:1px solid #6b6b6b;
            padding:5px;
            vertical-align:middle;
            word-break:break-word;
            overflow-wrap:anywhere;
        }
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{width:140px;height:65px;border:2px dashed #c8c8c8;display:flex;align-items:center;justify-content:center;margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center}
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px;font-weight:700}

        .seccion-title{
            margin:18px 0 8px;
            font-size:13px;
            color:#213b67;
            font-style:italic;
            font-weight:700;
        }

        .tabla-datos td:first-child{
            width:18%;
            font-weight:700;
            background:#f8fafc;
        }

        input[type="text"], input[type="number"], textarea, select{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px 4px;
            font-size:12px;
            line-height:1.25;
        }

        textarea{
            resize:vertical;
            min-height:58px;
            white-space:pre-wrap;
        }

        .tabla-cronograma-wrap{
            overflow-x:auto;
            width:100%;
        }

        .tabla-cronograma{
            min-width:1650px;
            width:1650px;
        }

        .tabla-cronograma thead th{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            font-size:11px;
            line-height:1.2;
        }

        .fase-cell{
            background:#eaf1fb;
            font-weight:700;
            text-align:center;
            color:#213b67;
        }

        .mes-cell{
            text-align:center;
        }

        .mes-input{
            text-align:center;
            font-weight:700;
        }

        .tabla-ficha thead th,
        .tabla-analisis thead th{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            font-size:11px;
        }

        .bloque-chart{
            border:1px solid #6b6b6b;
            padding:12px;
            background:#fff;
        }

        .chart-box{
            height:300px;
        }

        .grid-2{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
        }

        .mini-title{
            text-align:center;
            font-weight:700;
            color:#213b67;
            margin-bottom:8px;
        }

        .tfoot-soft{
            background:#f8fafc;
            font-weight:700;
        }

        @media (max-width: 1100px){
            .grid-2{grid-template-columns:1fr}
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar{display:none}
            .contenedor{box-shadow:none;border:none}
            .contenido{padding:6px}
            .tabla-cronograma-wrap{overflow:visible}
            .tabla-cronograma{min-width:auto;width:100%}
            input, textarea, select{border:none !important;box-shadow:none !important}
            .chart-box{height:220px}
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>4.2.4 Programa de Inspecciones</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form424">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form424" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:64%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:18%;font-weight:700;"><?php echo $datos['version']; ?></td>
                </tr>
                <tr>
                    <td class="subtitulo">PROGRAMA DE INSPECCIONES</td>
                    <td style="font-weight:700;"><?php echo $datos['codigo']; ?><br><?php echo $datos['fecha_documento']; ?></td>
                </tr>
            </table>

            <div class="seccion-title">1. Información general del programa</div>
            <table class="tabla-datos">
                <tr>
                    <td>EMPRESA / INSTITUCIÓN</td>
                    <td><input type="text" name="empresa" value="<?php echo $datos['empresa']; ?>"></td>
                </tr>
                <tr>
                    <td>OBJETIVO</td>
                    <td><textarea name="objetivo"><?php echo $datos['objetivo']; ?></textarea></td>
                </tr>
                <tr>
                    <td>ALCANCE</td>
                    <td><textarea name="alcance"><?php echo $datos['alcance']; ?></textarea></td>
                </tr>
                <tr>
                    <td>RECURSOS NECESARIOS</td>
                    <td><textarea name="recursos"><?php echo $datos['recursos']; ?></textarea></td>
                </tr>
                <tr>
                    <td>DOCUMENTOS DE REFERENCIA</td>
                    <td><textarea name="documentos"><?php echo $datos['documentos']; ?></textarea></td>
                </tr>
            </table>

            <div class="seccion-title">2. Cronograma de actividades 2025</div>
            <div class="tabla-cronograma-wrap">
                <table class="tabla-cronograma">
                    <thead>
                        <tr>
                            <th style="width:8%;">FASE</th>
                            <th style="width:20%;">ACTIVIDADES</th>
                            <th style="width:8%;">RESPONSABLE</th>
                            <th style="width:8%;">ALCANCE</th>
                            <th style="width:8%;">RECURSOS</th>
                            <th style="width:8%;">DOCUMENTOS</th>
                            <?php foreach ($meses as $mes): ?>
                                <th style="width:4.25%;"><?php echo $mes; ?></th>
                            <?php endforeach; ?>
                            <th style="width:7%;">CONSOLIDADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actividades as $i => $fila): ?>
                            <?php
                            $programadas = 0;
                            for ($m = 0; $m < 12; $m++) {
                                $programadas += toNum($fila['meses'][$m] ?? 0);
                            }
                            ?>
                            <tr>
                                <td class="fase-cell"><input type="text" name="actividades[<?php echo $i; ?>][fase]" value="<?php echo htmlspecialchars($fila['fase'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><textarea name="actividades[<?php echo $i; ?>][actividad]"><?php echo htmlspecialchars($fila['actividad'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="text" name="actividades[<?php echo $i; ?>][responsable]" value="<?php echo htmlspecialchars($fila['responsable'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="actividades[<?php echo $i; ?>][alcance]" value="<?php echo htmlspecialchars($fila['alcance'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="actividades[<?php echo $i; ?>][recursos]" value="<?php echo htmlspecialchars($fila['recursos'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="actividades[<?php echo $i; ?>][documentos]" value="<?php echo htmlspecialchars($fila['documentos'], ENT_QUOTES, 'UTF-8'); ?>"></td>

                                <?php for ($m = 0; $m < 12; $m++): ?>
                                    <td class="mes-cell">
                                        <input class="mes-input" type="number" step="any" name="actividades[<?php echo $i; ?>][meses][<?php echo $m; ?>]" value="<?php echo htmlspecialchars($fila['meses'][$m] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </td>
                                <?php endfor; ?>

                                <td class="tfoot-soft" style="text-align:center;"><?php echo $programadas; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="seccion-title">3. Indicador 1 - Cumplimiento</div>
            <table class="tabla-ficha">
                <thead>
                    <tr>
                        <th colspan="2">FICHA TÉCNICA INDICADOR 1</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td style="width:28%;font-weight:700;background:#f8fafc;">NOMBRE</td><td><input type="text" name="indicador1[nombre]" value="<?php echo htmlspecialchars($indicador1['nombre'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">INTERPRETACIÓN</td><td><textarea name="indicador1[interpretacion]"><?php echo htmlspecialchars($indicador1['interpretacion'], ENT_QUOTES, 'UTF-8'); ?></textarea></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">FACTOR QUE MIDE</td><td><textarea name="indicador1[factor_mide]"><?php echo htmlspecialchars($indicador1['factor_mide'], ENT_QUOTES, 'UTF-8'); ?></textarea></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">PERIODICIDAD DEL REPORTE</td><td><input type="text" name="indicador1[periodicidad]" value="<?php echo htmlspecialchars($indicador1['periodicidad'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">FUENTE DE LA INFORMACIÓN</td><td><input type="text" name="indicador1[fuente]" value="<?php echo htmlspecialchars($indicador1['fuente'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">RESPONSABLE</td><td><input type="text" name="indicador1[responsable]" value="<?php echo htmlspecialchars($indicador1['responsable'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">PERSONAS QUE DEBEN CONOCER</td><td><input type="text" name="indicador1[deben_conocer]" value="<?php echo htmlspecialchars($indicador1['deben_conocer'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                </tbody>
            </table>

            <div class="seccion-title">4. Valores del periodo - Indicador 1</div>
            <table class="tabla-analisis">
                <thead>
                    <tr>
                        <th>PERIODO</th>
                        <?php foreach ($meses as $mes): ?><th><?php echo $mes; ?></th><?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tfoot-soft"><?php echo htmlspecialchars($indicador1['numerador_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <?php foreach ($indicador1['numerador'] as $m => $v): ?>
                            <td><input type="number" step="any" name="indicador1[numerador][<?php echo $m; ?>]" value="<?php echo htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td class="tfoot-soft"><?php echo htmlspecialchars($indicador1['denominador_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <?php foreach ($indicador1['denominador'] as $m => $v): ?>
                            <td><input type="number" step="any" name="indicador1[denominador][<?php echo $m; ?>]" value="<?php echo htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td class="tfoot-soft">META</td>
                        <?php for ($m = 0; $m < 12; $m++): ?>
                            <td><input type="number" step="any" name="indicador1_meta_mes_<?php echo $m; ?>" value="0"></td>
                        <?php endfor; ?>
                    </tr>
                    <tr>
                        <td class="tfoot-soft">VALOR DEL INDICADOR %</td>
                        <?php foreach ($valores1 as $v): ?>
                            <td style="text-align:center;font-weight:700;"><?php echo fmtPct($v); ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>

            <div class="grid-2" style="margin-top:14px;">
                <div class="bloque-chart">
                    <div class="mini-title">ANÁLISIS TENDENCIAL PRIMER SEMESTRE</div>
                    <div class="chart-box"><canvas id="chartIndicador1A"></canvas></div>
                    <table class="tabla-analisis" style="margin-top:10px;">
                        <thead>
                            <tr><th>Plan de Acción</th><th>Plazo</th><th>Responsable</th><th>Acción correctiva?</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><textarea name="indicador1[plan1]"><?php echo htmlspecialchars($indicador1['plan1'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="text" name="indicador1[plazo1]" value="<?php echo htmlspecialchars($indicador1['plazo1'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador1[responsable1]" value="<?php echo htmlspecialchars($indicador1['responsable1'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador1[accion1]" value="<?php echo htmlspecialchars($indicador1['accion1'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bloque-chart">
                    <div class="mini-title">ANÁLISIS TENDENCIAL SEGUNDO SEMESTRE</div>
                    <div class="chart-box"><canvas id="chartIndicador1B"></canvas></div>
                    <table class="tabla-analisis" style="margin-top:10px;">
                        <thead>
                            <tr><th>Plan de Acción</th><th>Plazo</th><th>Responsable</th><th>Acción correctiva?</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><textarea name="indicador1[plan2]"><?php echo htmlspecialchars($indicador1['plan2'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="text" name="indicador1[plazo2]" value="<?php echo htmlspecialchars($indicador1['plazo2'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador1[responsable2]" value="<?php echo htmlspecialchars($indicador1['responsable2'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador1[accion2]" value="<?php echo htmlspecialchars($indicador1['accion2'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="seccion-title">5. Indicador 2 - Eficacia</div>
            <table class="tabla-ficha">
                <thead>
                    <tr>
                        <th colspan="2">FICHA TÉCNICA INDICADOR 2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td style="width:28%;font-weight:700;background:#f8fafc;">NOMBRE</td><td><input type="text" name="indicador2[nombre]" value="<?php echo htmlspecialchars($indicador2['nombre'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">INTERPRETACIÓN</td><td><textarea name="indicador2[interpretacion]"><?php echo htmlspecialchars($indicador2['interpretacion'], ENT_QUOTES, 'UTF-8'); ?></textarea></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">FACTOR QUE MIDE</td><td><textarea name="indicador2[factor_mide]"><?php echo htmlspecialchars($indicador2['factor_mide'], ENT_QUOTES, 'UTF-8'); ?></textarea></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">PERIODICIDAD DEL REPORTE</td><td><input type="text" name="indicador2[periodicidad]" value="<?php echo htmlspecialchars($indicador2['periodicidad'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">FUENTE DE LA INFORMACIÓN</td><td><input type="text" name="indicador2[fuente]" value="<?php echo htmlspecialchars($indicador2['fuente'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">RESPONSABLE</td><td><input type="text" name="indicador2[responsable]" value="<?php echo htmlspecialchars($indicador2['responsable'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                    <tr><td style="font-weight:700;background:#f8fafc;">PERSONAS QUE DEBEN CONOCER</td><td><input type="text" name="indicador2[deben_conocer]" value="<?php echo htmlspecialchars($indicador2['deben_conocer'], ENT_QUOTES, 'UTF-8'); ?>"></td></tr>
                </tbody>
            </table>

            <div class="seccion-title">6. Valores del periodo - Indicador 2</div>
            <table class="tabla-analisis">
                <thead>
                    <tr>
                        <th>PERIODO</th>
                        <?php foreach ($meses as $mes): ?><th><?php echo $mes; ?></th><?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tfoot-soft"><?php echo htmlspecialchars($indicador2['numerador_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <?php foreach ($indicador2['numerador'] as $m => $v): ?>
                            <td><input type="number" step="any" name="indicador2[numerador][<?php echo $m; ?>]" value="<?php echo htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td class="tfoot-soft"><?php echo htmlspecialchars($indicador2['denominador_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <?php foreach ($indicador2['denominador'] as $m => $v): ?>
                            <td><input type="number" step="any" name="indicador2[denominador][<?php echo $m; ?>]" value="<?php echo htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td class="tfoot-soft">META</td>
                        <?php for ($m = 0; $m < 12; $m++): ?>
                            <td><input type="number" step="any" name="indicador2_meta_mes_<?php echo $m; ?>" value="0"></td>
                        <?php endfor; ?>
                    </tr>
                    <tr>
                        <td class="tfoot-soft">VALOR DEL INDICADOR %</td>
                        <?php foreach ($valores2 as $v): ?>
                            <td style="text-align:center;font-weight:700;"><?php echo fmtPct($v); ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>

            <div class="grid-2" style="margin-top:14px;">
                <div class="bloque-chart">
                    <div class="mini-title">ANÁLISIS TENDENCIAL PRIMER SEMESTRE</div>
                    <div class="chart-box"><canvas id="chartIndicador2A"></canvas></div>
                    <table class="tabla-analisis" style="margin-top:10px;">
                        <thead>
                            <tr><th>Plan de Acción</th><th>Plazo</th><th>Responsable</th><th>Acción correctiva?</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><textarea name="indicador2[plan1]"><?php echo htmlspecialchars($indicador2['plan1'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="text" name="indicador2[plazo1]" value="<?php echo htmlspecialchars($indicador2['plazo1'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador2[responsable1]" value="<?php echo htmlspecialchars($indicador2['responsable1'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador2[accion1]" value="<?php echo htmlspecialchars($indicador2['accion1'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bloque-chart">
                    <div class="mini-title">ANÁLISIS TENDENCIAL SEGUNDO SEMESTRE</div>
                    <div class="chart-box"><canvas id="chartIndicador2B"></canvas></div>
                    <table class="tabla-analisis" style="margin-top:10px;">
                        <thead>
                            <tr><th>Plan de Acción</th><th>Plazo</th><th>Responsable</th><th>Acción correctiva?</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><textarea name="indicador2[plan2]"><?php echo htmlspecialchars($indicador2['plan2'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="text" name="indicador2[plazo2]" value="<?php echo htmlspecialchars($indicador2['plazo2'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador2[responsable2]" value="<?php echo htmlspecialchars($indicador2['responsable2'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" name="indicador2[accion2]" value="<?php echo htmlspecialchars($indicador2['accion2'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function createChart(canvasId, labels, data, meta){
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Valor del indicador %',
                    data: data
                },
                {
                    type: 'line',
                    label: 'Meta',
                    data: meta,
                    tension: 0.25
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 100
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function(){
    createChart(
        'chartIndicador1A',
        <?php echo json_encode(array_slice($meses, 0, 6)); ?>,
        <?php echo json_encode(array_slice($valores1, 0, 6)); ?>,
        [0,0,0,0,0,0]
    );

    createChart(
        'chartIndicador1B',
        <?php echo json_encode(array_slice($meses, 6, 6)); ?>,
        <?php echo json_encode(array_slice($valores1, 6, 6)); ?>,
        [0,0,0,0,0,0]
    );

    createChart(
        'chartIndicador2A',
        <?php echo json_encode(array_slice($meses, 0, 6)); ?>,
        <?php echo json_encode(array_slice($valores2, 0, 6)); ?>,
        [0,0,0,0,0,0]
    );

    createChart(
        'chartIndicador2B',
        <?php echo json_encode(array_slice($meses, 6, 6)); ?>,
        <?php echo json_encode(array_slice($valores2, 6, 6)); ?>,
        [0,0,0,0,0,0]
    );
});
</script>
</body>
</html>