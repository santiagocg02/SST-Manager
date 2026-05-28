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
// Ajusta el ID de este ítem según tu base de datos para este formato (ej. 53)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 53;

// --- Lógica de Empresa (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE
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

// Función para leer datos desde la API
function oldv($key, $default = '')
{
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] !== '') ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

$datos = [
    'version'         => oldv('version', '0'),
    'codigo'          => oldv('codigo', 'PR-SST-02'),
    'fecha_documento' => oldv('fecha_documento', date('Y-m-d')),
    'empresa'         => oldv('empresa', ''),
    'objetivo'        => oldv('objetivo', 'Prevenir la ocurrencia de accidentes y enfermedades laborales asociados a las actividades de la compañía por medio de las inspecciones.'),
    'alcance'         => oldv('alcance', 'Este programa aplica para todas las áreas de trabajo de la empresa.'),
    'recursos'        => oldv('recursos', 'Económicos, técnicos, humanos e infraestructura.'),
    'documentos'      => oldv('documentos', 'Legislación aplicable, plan de trabajo, matriz de identificación de peligros y procedimientos internos.'),
];

// Reconstrucción del arreglo de actividades desde los datos guardados o por defecto
$defaultActividades = [
    ['fase' => 'PLANEAR', 'actividad' => 'Establecer objetivos y metas', 'responsable' => 'SST', 'alcance' => 'General'],
    ['fase' => 'PLANEAR', 'actividad' => 'Establecer indicadores de gestión', 'responsable' => 'SST', 'alcance' => 'General'],
    ['fase' => 'PLANEAR', 'actividad' => 'Establecer los mecanismos para controlar el riesgo', 'responsable' => 'SST', 'alcance' => 'General'],
    ['fase' => 'HACER', 'actividad' => 'Inspección a vehículos', 'responsable' => 'SST', 'alcance' => 'Operativo'],
    ['fase' => 'HACER', 'actividad' => 'Inspección a herramientas', 'responsable' => 'SST', 'alcance' => 'Operativo'],
    ['fase' => 'HACER', 'actividad' => 'Inspección a botiquín', 'responsable' => 'SST', 'alcance' => 'Operativo'],
    ['fase' => 'HACER', 'actividad' => 'Inspecciones locativas', 'responsable' => 'SST', 'alcance' => 'Operativo'],
    ['fase' => 'HACER', 'actividad' => 'Inspecciones de extintores', 'responsable' => 'SST', 'alcance' => 'Operativo'],
    ['fase' => 'HACER', 'actividad' => 'Inspección gerencial', 'responsable' => 'SST', 'alcance' => 'Gerencial'],
    ['fase' => 'VERIFICAR', 'actividad' => 'Seguimiento a indicadores', 'responsable' => 'SST', 'alcance' => 'General'],
    ['fase' => 'VERIFICAR', 'actividad' => 'Seguimiento a las acciones tomadas frente a los hallazgos', 'responsable' => 'SST', 'alcance' => 'General'],
    ['fase' => 'ACTUAR', 'actividad' => 'Implementación de acciones correctivas y preventivas', 'responsable' => 'SST', 'alcance' => 'General'],
];

$actividades = [];
for ($i = 0; $i < 12; $i++) {
    $actividades[$i] = [
        'fase'        => oldv("actividades[$i][fase]", $defaultActividades[$i]['fase']),
        'actividad'   => oldv("actividades[$i][actividad]", $defaultActividades[$i]['actividad']),
        'responsable' => oldv("actividades[$i][responsable]", $defaultActividades[$i]['responsable']),
        'alcance'     => oldv("actividades[$i][alcance]", $defaultActividades[$i]['alcance']),
        'recursos'    => oldv("actividades[$i][recursos]", ''),
        'documentos'  => oldv("actividades[$i][documentos]", ''),
        'meses'       => []
    ];
    for ($m = 0; $m < 12; $m++) {
        $actividades[$i]['meses'][$m] = oldv("actividades[$i][meses][$m]", '0');
    }
}

// Configuración de Indicador 1
$indicador1 = [
    'nombre'             => oldv('indicador1[nombre]', 'Cumplimiento'),
    'interpretacion'     => oldv('indicador1[interpretacion]', 'Cumplimiento de actividades en el programa'),
    'factor_mide'        => oldv('indicador1[factor_mide]', 'Actividades ejecutadas dentro del cronograma'),
    'periodicidad'       => oldv('indicador1[periodicidad]', 'Semestral'),
    'fuente'             => oldv('indicador1[fuente]', 'Plan de trabajo'),
    'responsable'        => oldv('indicador1[responsable]', 'Encargado del SG-SST'),
    'deben_conocer'      => oldv('indicador1[deben_conocer]', 'Coordinador HSEQ'),
    'numerador_nombre'   => oldv('indicador1[numerador_nombre]', 'No. de actividades realizadas'),
    'denominador_nombre' => oldv('indicador1[denominador_nombre]', 'No. de actividades programadas'),
    'plan1'              => oldv('indicador1[plan1]', ''),
    'plazo1'             => oldv('indicador1[plazo1]', ''),
    'responsable1'       => oldv('indicador1[responsable1]', ''),
    'accion1'            => oldv('indicador1[accion1]', ''),
    'plan2'              => oldv('indicador1[plan2]', ''),
    'plazo2'             => oldv('indicador1[plazo2]', ''),
    'responsable2'       => oldv('indicador1[responsable2]', ''),
    'accion2'            => oldv('indicador1[accion2]', ''),
    'numerador'          => [],
    'denominador'        => []
];
for ($m = 0; $m < 12; $m++) {
    $indicador1['numerador'][$m] = oldv("indicador1[numerador][$m]", '0');
    $indicador1['denominador'][$m] = oldv("indicador1[denominador][$m]", '0');
}

// Configuración de Indicador 2
$indicador2 = [
    'nombre'             => oldv('indicador2[nombre]', 'Eficacia'),
    'interpretacion'     => oldv('indicador2[interpretacion]', 'Eficacia de los planes de acción propuestos'),
    'factor_mide'        => oldv('indicador2[factor_mide]', 'Condiciones peligrosas intervenidas frente a condiciones reportadas'),
    'periodicidad'       => oldv('indicador2[periodicidad]', 'Semestral'),
    'fuente'             => oldv('indicador2[fuente]', 'Plan de acción'),
    'responsable'        => oldv('indicador2[responsable]', 'Encargado del SG-SST'),
    'deben_conocer'      => oldv('indicador2[deben_conocer]', 'Coordinador HSEQ'),
    'numerador_nombre'   => oldv('indicador2[numerador_nombre]', 'No. de condiciones peligrosas intervenidas'),
    'denominador_nombre' => oldv('indicador2[denominador_nombre]', 'No. de condiciones peligrosas reportadas'),
    'plan1'              => oldv('indicador2[plan1]', ''),
    'plazo1'             => oldv('indicador2[plazo1]', ''),
    'responsable1'       => oldv('indicador2[responsable1]', ''),
    'accion1'            => oldv('indicador2[accion1]', ''),
    'plan2'              => oldv('indicador2[plan2]', ''),
    'plazo2'             => oldv('indicador2[plazo2]', ''),
    'responsable2'       => oldv('indicador2[responsable2]', ''),
    'accion2'            => oldv('indicador2[accion2]', ''),
    'numerador'          => [],
    'denominador'        => []
];
for ($m = 0; $m < 12; $m++) {
    $indicador2['numerador'][$m] = oldv("indicador2[numerador][$m]", '0');
    $indicador2['denominador'][$m] = oldv("indicador2[denominador][$m]", '0');
}

function toNum($value) {
    return is_numeric($value) ? (float)$value : 0;
}

function calcIndicadorMes($num, $den) {
    $n = toNum($num);
    $d = toNum($den);
    return $d > 0 ? round(($n / $d) * 100, 2) : 0;
}

function fmtPct($n) {
    return number_format((float)$n, 0, ',', '.') . '%';
}

$valores1 = [];
$valores2 = [];
$metas1_A = []; $metas1_B = [];
$metas2_A = []; $metas2_B = [];

for ($i = 0; $i < 12; $i++) {
    $valores1[] = calcIndicadorMes($indicador1['numerador'][$i] ?? 0, $indicador1['denominador'][$i] ?? 0);
    $valores2[] = calcIndicadorMes($indicador2['numerador'][$i] ?? 0, $indicador2['denominador'][$i] ?? 0);
    
    // Preparar metas para gráficos
    $m1 = toNum(oldv("indicador1_meta_mes_$i", '0'));
    $m2 = toNum(oldv("indicador2_meta_mes_$i", '0'));
    if ($i < 6) {
        $metas1_A[] = $m1;
        $metas2_A[] = $m2;
    } else {
        $metas1_B[] = $m1;
        $metas2_B[] = $m2;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.2.4 Programa de Inspecciones</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        .logo-box{width:140px;height:65px;display:flex;align-items:center;justify-content:center;margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center}
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
            .toolbar, .print-hide{display:none !important}
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
    <div class="toolbar print-hide">
        <h1>4.2.4 Programa de Inspecciones</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="form424">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
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

            <div class="seccion-title">2. Cronograma de actividades</div>
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
                            <td><input type="number" step="any" name="indicador1_meta_mes_<?php echo $m; ?>" value="<?php echo oldv('indicador1_meta_mes_'.$m, '0'); ?>"></td>
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
                            <td><input type="number" step="any" name="indicador2_meta_mes_<?php echo $m; ?>" value="<?php echo oldv('indicador2_meta_mes_'.$m, '0'); ?>"></td>
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
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Meta %',
                    data: meta,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.25,
                    fill: false
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
        <?php echo json_encode($metas1_A); ?>
    );

    createChart(
        'chartIndicador1B',
        <?php echo json_encode(array_slice($meses, 6, 6)); ?>,
        <?php echo json_encode(array_slice($valores1, 6, 6)); ?>,
        <?php echo json_encode($metas1_B); ?>
    );

    createChart(
        'chartIndicador2A',
        <?php echo json_encode(array_slice($meses, 0, 6)); ?>,
        <?php echo json_encode(array_slice($valores2, 0, 6)); ?>,
        <?php echo json_encode($metas2_A); ?>
    );

    createChart(
        'chartIndicador2B',
        <?php echo json_encode(array_slice($meses, 6, 6)); ?>,
        <?php echo json_encode(array_slice($valores2, 6, 6)); ?>,
        <?php echo json_encode($metas2_B); ?>
    );
});

// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form424');
    const formData = new FormData(form);
    
    // Convertimos el formulario en un objeto JSON limpio
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
                text: 'El Programa de Inspecciones se ha guardado correctamente.',
                icon: 'success',
                confirmButtonColor: '#198754'
            }).then(() => {
                // Opcional: Recargar la página para que se actualicen las gráficas y %
                window.location.reload();
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