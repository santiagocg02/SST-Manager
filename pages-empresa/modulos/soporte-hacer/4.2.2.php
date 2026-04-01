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

$datos = [
    'version' => post('version', '0'),
    'codigo' => post('codigo', 'RE-SST-26'),
    'fecha_documento' => post('fecha_documento', 'XX/XX/2025'),
    'fecha' => post('fecha', ''),
    'consecutivo' => post('consecutivo', ''),
    'nombre_reporta' => post('nombre_reporta', ''),
    'descripcion_reporte' => post('descripcion_reporte', ''),
    'otro_acto' => post('otro_acto', ''),
    'otro_condicion' => post('otro_condicion', ''),
    'diagnostico' => post('diagnostico', ''),
    'fecha_recibido_sst' => post('fecha_recibido_sst', ''),
    'recibido_por' => post('recibido_por', ''),
    'fecha_traslado' => post('fecha_traslado', ''),
    'fecha_max_solucion' => post('fecha_max_solucion', ''),
    'responsable' => post('responsable', ''),
    'grupo' => post('grupo', ''),
    'acciones_propuestas' => post('acciones_propuestas', ''),
    'fecha_cierre' => post('fecha_cierre', ''),
    'visto_bueno_sst' => post('visto_bueno_sst', ''),
];

$tipos_reportante = [
    'funcionario' => post('funcionario'),
    'contratista' => post('contratista'),
    'visitante' => post('visitante'),
    'otro_tipo' => post('otro_tipo'),
    'otro_tipo_texto' => post('otro_tipo_texto'),
];

$tipo_reporte = [
    'actos_inseguros' => post('actos_inseguros'),
    'autoreporte_salud' => post('autoreporte_salud'),
    'condiciones_inseguras' => post('condiciones_inseguras'),
];

$actos = [
    'No uso o uso inapropiado de Elementos de protección personal',
    'Realizar labores de mantenimiento sin señalizar debidamente',
    'Realizar labores de aseo y limpieza sin señalizar debidamente',
    'Hacer bromas o juegos pesados',
    'Agarrar o manipular objetos de forma insegura o errada',
    'Usar las manos en lugar de las herramientas',
    'Falta de atención a las condiciones del entorno',
    'Adoptar posiciones inseguras',
    'Errores en la conducción de vehículos',
    'Almacenar, apilar, mezclar inadecuadamente equipos y/o herramientas',
    'Realizar actividades en altura sin usar los elementos adecuados',
    'Subirse sobre los escritorios y/o sillas',
    'Utilizar mal herramientas o equipos',
    'Usar equipos, herramientas y/o materiales inseguros o en mal estado',
];

$condiciones = [
    'Áreas sin señalización de emergencias',
    'Ruido excesivo',
    'Espacios inadecuados de circulación',
    'Ventilación general inadecuada',
    'Iluminación deficiente o excesiva',
    'Áreas de almacenamiento inadecuadas',
    'Materiales ubicados y/o almacenados inapropiadamente',
    'Áreas de trabajo obstaculizadas',
    'Áreas en inadecuadas condiciones de orden y aseo',
    'Inapropiada clasificación de residuos sólidos',
    'Fugas o pérdidas de agua',
    'Luces, equipos prendidos innecesariamente',
    'Mobiliario en mal estado',
    'Techos en mal estado o con posibilidad de desplome',
];

$sistemas_salud_izq = [
    'nervioso' => 'Nervioso',
    'cardiovascular' => 'Cardiovascular',
    'osteomuscular' => 'Osteomuscular',
    'digestivo' => 'Digestivo',
];

$sistemas_salud_der = [
    'tegumentario' => 'Tegumentario',
    'sensorial' => 'Sensorial',
    'respiratorio' => 'Respiratorio',
    'psicosocial' => 'Psicosocial',
];

$riesgos_asociados_izq = [
    'riesgo_fisicos' => 'Físicos',
    'riesgo_quimicos' => 'Químicos',
    'riesgo_biologicos' => 'Biológicos',
    'riesgo_biomecanicos' => 'Biomecánicos',
];

$riesgos_asociados_der = [
    'riesgo_psicosociales' => 'Psicosociales',
    'riesgo_naturales' => 'Riesgos naturales',
    'riesgo_seguridad' => 'Condiciones de Seguridad',
    'riesgo_salud' => 'Condiciones de Salud',
];

function checked($value)
{
    return !empty($value) ? 'checked' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.2.2 Reporte de Actos y Condiciones Inseguras</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1450px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
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
        .encabezado td,.encabezado th,.tabla-base td,.tabla-base th{
            border:1px solid #444;
            padding:4px 5px;
            vertical-align:middle;
            overflow-wrap:anywhere;
            word-break:break-word;
        }
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{
            width:140px;height:65px;border:2px dashed #c8c8c8;display:flex;align-items:center;justify-content:center;
            margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center
        }
        .titulo-principal{font-size:15px;font-weight:700}
        .subtitulo{font-size:14px;font-weight:700;line-height:1.25}

        .section-title{
            background:#0f73bd;
            color:#fff;
            text-align:center;
            font-weight:700;
            font-size:15px;
            padding:6px 8px;
            border:1px solid #444;
            border-top:none;
        }

        .texto-ayuda{
            font-size:12px;
            line-height:1.35;
        }

        .tabla-base td,
        .tabla-base th{
            font-size:12px;
            line-height:1.2;
        }

        input[type="text"], input[type="date"], textarea{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px 4px;
            font-size:12px;
            line-height:1.2;
        }

        textarea{
            resize:vertical;
            min-height:34px;
            white-space:pre-wrap;
            word-break:break-word;
            overflow-wrap:anywhere;
        }

        .input-box{
            height:30px;
            border:1px solid #444;
            background:#fff;
        }

        .mini-box{
            width:18px;
            height:18px;
            accent-color:#0d6efd;
            cursor:pointer;
        }

        .tipo-grid{
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:14px;
            margin-top:8px;
            align-items:center;
        }

        .tipo-item{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:12px;
        }

        .tipo-item .box-text{
            width:54px;
        }

        .descripcion-lg{
            min-height:120px;
        }

        .tabla-listado{
        table-layout:fixed;
    }

    .tabla-listado td:first-child{
        width:94%;
    }

    .tabla-listado td:last-child{
        width:6%;
        min-width:44px;
        text-align:center;
        vertical-align:middle;
        padding:0;
    }

    .tabla-listado td:last-child .box-center{
        width:100%;
        min-height:28px;
        display:flex;
        align-items:center;
        justify-content:center;
    }

        .tabla-listado .linea-otro{
            padding-top:2px;
            padding-bottom:2px;
        }

        .diag-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            margin-top:8px;
        }

        .diag-col .fila{
            display:grid;
            grid-template-columns:1fr 70px;
            gap:8px;
            align-items:center;
            margin-bottom:4px;
        }

        .bool-row{
            display:grid;
            grid-template-columns:1fr 70px 70px;
            gap:0;
            align-items:center;
        }

        .bool-row .opt{
            border:1px solid #444;
            text-align:center;
            padding:4px;
            font-size:12px;
        }

        .bool-row .opt input{
            transform:scale(1.05);
        }

        .box-center{
            display:flex;
            align-items:center;
            justify-content:center;
            width:100%;
            height:100%;
        }

        .sst-label{
            background:#e9ecef;
            text-align:center;
            font-weight:700;
        }

        .riesgo-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
        }

        .riesgo-col .fila{
            display:grid;
            grid-template-columns:1fr 70px;
            gap:8px;
            align-items:center;
            margin-bottom:4px;
        }

        .criticidad td{
            text-align:center;
            font-weight:700;
        }

        .crit-alto{background:#ff1f1f;color:#111}
        .crit-medio{background:#fff200;color:#111}
        .crit-bajo{background:#00c853;color:#111}

        .acciones-box{
            min-height:90px;
        }

        .soportes-grid{
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:16px;
            align-items:center;
        }

        .soporte-item{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:12px;
        }

        .firma-row{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:0;
        }

        .firma-row > div{
            border-right:1px solid #444;
        }

        .firma-row > div:last-child{
            border-right:none;
        }

        .muted{
            color:#555;
        }

        @media (max-width: 980px){
            .toolbar{position:static}
            .tipo-grid,.diag-grid,.riesgo-grid,.soportes-grid,.firma-row{grid-template-columns:1fr}
            body{padding:10px}
        }

        @page{
            size: portrait;
            margin: 8mm;
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar{display:none}
            .contenedor{box-shadow:none;border:none;max-width:100%}
            .contenido{padding:5px}
            .encabezado td,.tabla-base td,.tabla-base th,.section-title{
                font-size:10px !important;
                padding:2px 3px !important;
            }
            textarea,input[type="text"],input[type="date"]{
                font-size:10px !important;
                padding:2px !important;
            }
            .descripcion-lg{min-height:90px !important}
            .acciones-box{min-height:70px !important}
            .mini-box{width:14px;height:14px}
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>4.2.2 Reporte de Actos y Condiciones Inseguras</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form422">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form422" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:58%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:22%;font-weight:700;"><?php echo $datos['version']; ?></td>
                </tr>
                <tr>
                    <td class="subtitulo">FORMATO PARA EL REPORTE DE ACTOS Y CONDICIONES INSEGURAS Y AUTOREPORTE CONDICIONES EN SALUD</td>
                    <td style="font-weight:700;">
                        <?php echo $datos['codigo']; ?><br>
                        <?php echo $datos['fecha_documento']; ?>
                    </td>
                </tr>
            </table>

            <div class="section-title">I. IDENTIFICACIÓN</div>
            <table class="tabla-base">
                <tr>
                    <td style="width:13%;font-weight:700;">Fecha</td>
                    <td style="width:22%;"><input type="date" name="fecha" value="<?php echo $datos['fecha']; ?>"></td>
                    <td style="width:38%;"></td>
                    <td style="width:15%;font-weight:700;text-align:center;">Consecutivo</td>
                    <td style="width:12%;"><input type="text" name="consecutivo" value="<?php echo $datos['consecutivo']; ?>"></td>
                </tr>
                <tr>
                    <td style="font-weight:700;">Nombre de quien reporta</td>
                    <td colspan="4"><input type="text" name="nombre_reporta" value="<?php echo $datos['nombre_reporta']; ?>"></td>
                </tr>
            </table>

            <table class="tabla-base" style="border-top:none;">
                <tr>
                    <td>
                        <div class="tipo-grid">
                            <label class="tipo-item">
                                <span>Funcionario</span>
                                <input class="mini-box" type="checkbox" name="funcionario" value="1" <?php echo checked($tipos_reportante['funcionario']); ?>>
                            </label>
                            <label class="tipo-item">
                                <span>Contratista</span>
                                <input class="mini-box" type="checkbox" name="contratista" value="1" <?php echo checked($tipos_reportante['contratista']); ?>>
                            </label>
                            <label class="tipo-item">
                                <span>Visitante</span>
                                <input class="mini-box" type="checkbox" name="visitante" value="1" <?php echo checked($tipos_reportante['visitante']); ?>>
                            </label>
                            <div class="tipo-item">
                                <label style="display:flex;align-items:center;gap:8px;">
                                    <span>Otro</span>
                                    <input class="mini-box" type="checkbox" name="otro_tipo" value="1" <?php echo checked($tipos_reportante['otro_tipo']); ?>>
                                </label>
                                <input class="box-text" type="text" name="otro_tipo_texto" value="<?php echo $tipos_reportante['otro_tipo_texto']; ?>">
                            </div>
                        </div>

                        <div style="margin-top:8px;text-align:center;font-weight:700;">Indique con una X qué desea reportar:</div>

                        <table class="tabla-base" style="margin-top:6px;">
                            <tr>
                                <td class="texto-ayuda" style="width:94%;">
                                    <strong>Actos Inseguros:</strong> Es la violación de un procedimiento o norma de trabajo por parte del trabajador que puede conllevar a la ocurrencia de un incidente, accidente de trabajo o afectación ambiental.
                                </td>
                                <td class="box-center">
                                    <input class="mini-box" type="checkbox" name="actos_inseguros" value="1" <?php echo checked($tipo_reporte['actos_inseguros']); ?>>
                                </td>
                            </tr>
                            <tr>
                                <td class="texto-ayuda">
                                    <strong>Autoreporte de Condiciones en Salud:</strong> Proceso mediante el cual funcionario o contratista reporta por escrito al empleador o contratante las condiciones adversas en salud que identifica en su lugar de trabajo.
                                </td>
                                <td class="box-center">
                                    <input class="mini-box" type="checkbox" name="autoreporte_salud" value="1" <?php echo checked($tipo_reporte['autoreporte_salud']); ?>>
                                </td>
                            </tr>
                            <tr>
                                <td class="texto-ayuda">
                                    <strong>Condiciones Inseguras:</strong> Toda circunstancia física que presente una desviación de lo estándar o establecido y que facilite la ocurrencia de accidentes.
                                </td>
                                <td class="box-center">
                                    <input class="mini-box" type="checkbox" name="condiciones_inseguras" value="1" <?php echo checked($tipo_reporte['condiciones_inseguras']); ?>>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="section-title">II. DESCRIPCIÓN DEL REPORTE</div>
            <table class="tabla-base">
                <tr>
                    <td class="muted">Por favor indique con claridad qué, cómo, dónde (sitio, piso y ala) y cuándo se presentó el evento</td>
                </tr>
                <tr>
                    <td><textarea class="descripcion-lg" name="descripcion_reporte"><?php echo $datos['descripcion_reporte']; ?></textarea></td>
                </tr>
            </table>

            <div class="section-title">III. ACTOS INSEGUROS</div>
            <table class="tabla-base tabla-listado">
                <tr>
                    <td colspan="2">Marque con una X si el acto y/o la condición insegura observada se encuentran relacionadas en el siguiente listado, de lo contrario relaciónelas al final del mismo.</td>
                </tr>
                <?php foreach ($actos as $i => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="box-center">
                            <input class="mini-box" type="checkbox" name="acto_<?php echo $i; ?>" value="1" <?php echo checked(post("acto_$i")); ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Otro</td>
                    <td class="box-center"><input class="mini-box" type="checkbox" name="acto_otro_check" value="1" <?php echo checked(post('acto_otro_check')); ?>></td>
                </tr>
                <tr>
                    <td class="linea-otro">Cuál? <input type="text" name="otro_acto" value="<?php echo $datos['otro_acto']; ?>"></td>
                    <td></td>
                </tr>
            </table>

            <div class="section-title">IV. CONDICIONES INSEGURAS</div>
            <table class="tabla-base tabla-listado">
                <?php foreach ($condiciones as $i => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="box-center">
                            <input class="mini-box" type="checkbox" name="condicion_<?php echo $i; ?>" value="1" <?php echo checked(post("condicion_$i")); ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Otro</td>
                    <td class="box-center"><input class="mini-box" type="checkbox" name="condicion_otro_check" value="1" <?php echo checked(post('condicion_otro_check')); ?>></td>
                </tr>
                <tr>
                    <td class="linea-otro">Cuál? <input type="text" name="otro_condicion" value="<?php echo $datos['otro_condicion']; ?>"></td>
                    <td></td>
                </tr>
            </table>

            <div class="section-title">V. AUTOREPORTE CONDICIONES DE SALUD</div>
            <table class="tabla-base">
                <tr>
                    <td colspan="2" style="text-align:center;">Cuál sistema se encuentra afectado por su sintomatología?</td>
                </tr>
                <tr>
                    <td style="width:50%;">
                        <div class="diag-col">
                            <?php foreach ($sistemas_salud_izq as $key => $label): ?>
                                <div class="fila">
                                    <span><?php echo $label; ?></span>
                                    <div class="box-center"><input class="mini-box" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo checked(post($key)); ?>></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td style="width:50%;">
                        <div class="diag-col">
                            <?php foreach ($sistemas_salud_der as $key => $label): ?>
                                <div class="fila">
                                    <span><?php echo $label; ?></span>
                                    <div class="box-center"><input class="mini-box" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo checked(post($key)); ?>></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="bool-row">
                            <div>Presenta una sintomatología específica (Diagnóstico emitido por médico)</div>
                            <label class="opt">SI <input class="mini-box" type="checkbox" name="diag_si" value="1" <?php echo checked(post('diag_si')); ?>></label>
                            <label class="opt">NO <input class="mini-box" type="checkbox" name="diag_no" value="1" <?php echo checked(post('diag_no')); ?>></label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="bool-row">
                            <div>Cree que su sintomatología puede afectar sus actividades laborales diarias</div>
                            <label class="opt">SI <input class="mini-box" type="checkbox" name="afecta_si" value="1" <?php echo checked(post('afecta_si')); ?>></label>
                            <label class="opt">NO <input class="mini-box" type="checkbox" name="afecta_no" value="1" <?php echo checked(post('afecta_no')); ?>></label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">Especifique el diagnóstico o describa la sintomatología.</td>
                </tr>
                <tr>
                    <td colspan="2"><textarea name="diagnostico" class="descripcion-lg"><?php echo $datos['diagnostico']; ?></textarea></td>
                </tr>
            </table>

            <table class="tabla-base" style="border-top:none;">
                <tr>
                    <td colspan="4" class="sst-label">Espacio exclusivo para ser diligenciado por Seguridad y Salud en el Trabajo</td>
                </tr>
                <tr>
                    <td style="width:25%;font-weight:700;">Fecha de recibido en SST</td>
                    <td style="width:25%;"><input type="date" name="fecha_recibido_sst" value="<?php echo $datos['fecha_recibido_sst']; ?>"></td>
                    <td style="width:20%;font-weight:700;">Recibido por</td>
                    <td style="width:30%;"><input type="text" name="recibido_por" value="<?php echo $datos['recibido_por']; ?>"></td>
                </tr>
            </table>

            <div class="section-title">VI. RIESGO ASOCIADO</div>
            <table class="tabla-base">
                <tr>
                    <td colspan="3">
                        <div class="riesgo-grid">
                            <div class="riesgo-col">
                                <?php foreach ($riesgos_asociados_izq as $key => $label): ?>
                                    <div class="fila">
                                        <span><?php echo $label; ?></span>
                                        <div class="box-center"><input class="mini-box" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo checked(post($key)); ?>></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="riesgo-col">
                                <?php foreach ($riesgos_asociados_der as $key => $label): ?>
                                    <div class="fila">
                                        <span><?php echo $label; ?></span>
                                        <div class="box-center"><input class="mini-box" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo checked(post($key)); ?>></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="criticidad">
                    <td style="width:26%;text-align:left;font-weight:700;background:#f8fafc;">Nivel de criticidad</td>
                    <td class="crit-alto">ALTO</td>
                    <td class="crit-medio">MEDIO</td>
                    <td class="crit-bajo">BAJO</td>
                </tr>
                <tr>
                    <td style="font-weight:700;background:#f8fafc;">Plazo de intervención</td>
                    <td style="text-align:center;">Prioritario<br>5 días hábiles</td>
                    <td style="text-align:center;">Urgente<br>15 días hábiles</td>
                    <td style="text-align:center;">Poco urgente<br>30 días hábiles</td>
                </tr>
            </table>

            <div class="section-title">VII. ACCIONES PROPUESTAS PARA EL TRATAMIENTO DEL EVENTO REPORTADO</div>
            <table class="tabla-base">
                <tr>
                    <td style="width:20%;font-weight:700;">Fecha de traslado</td>
                    <td style="width:30%;"><input type="date" name="fecha_traslado" value="<?php echo $datos['fecha_traslado']; ?>"></td>
                    <td style="width:20%;font-weight:700;">Fecha máxima de solución</td>
                    <td style="width:30%;"><input type="date" name="fecha_max_solucion" value="<?php echo $datos['fecha_max_solucion']; ?>"></td>
                </tr>
                <tr>
                    <td style="font-weight:700;">Responsable</td>
                    <td><input type="text" name="responsable" value="<?php echo $datos['responsable']; ?>"></td>
                    <td style="font-weight:700;">Grupo</td>
                    <td><input type="text" name="grupo" value="<?php echo $datos['grupo']; ?>"></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;font-weight:700;background:#f8fafc;">ACCIONES PROPUESTAS</td>
                </tr>
                <tr>
                    <td colspan="4"><textarea class="acciones-box" name="acciones_propuestas"><?php echo $datos['acciones_propuestas']; ?></textarea></td>
                </tr>
                <tr>
                    <td colspan="2">Las acciones propuestas requieren modificación de la matriz de identificación de peligros, valoración de riesgos y determinación de controles.</td>
                    <td class="box-center">SI <input class="mini-box" type="checkbox" name="modifica_matriz_si" value="1" <?php echo checked(post('modifica_matriz_si')); ?>></td>
                    <td class="box-center">NO <input class="mini-box" type="checkbox" name="modifica_matriz_no" value="1" <?php echo checked(post('modifica_matriz_no')); ?>></td>
                </tr>
                <tr>
                    <td>Las acciones fueron eficaces</td>
                    <td class="box-center">SI <input class="mini-box" type="checkbox" name="acciones_eficaces_si" value="1" <?php echo checked(post('acciones_eficaces_si')); ?>></td>
                    <td class="box-center">NO <input class="mini-box" type="checkbox" name="acciones_eficaces_no" value="1" <?php echo checked(post('acciones_eficaces_no')); ?>></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">Se requiere reprogramar acciones de intervención</td>
                    <td class="box-center">SI <input class="mini-box" type="checkbox" name="reprogramar_si" value="1" <?php echo checked(post('reprogramar_si')); ?>></td>
                    <td class="box-center">NO <input class="mini-box" type="checkbox" name="reprogramar_no" value="1" <?php echo checked(post('reprogramar_no')); ?>></td>
                </tr>
            </table>

            <div class="section-title">VIII. SOPORTES DE CIERRE</div>
            <table class="tabla-base">
                <tr>
                    <td colspan="2">
                        <div class="soportes-grid">
                            <label class="soporte-item">Fotografías <input class="mini-box" type="checkbox" name="soporte_fotos" value="1" <?php echo checked(post('soporte_fotos')); ?>></label>
                            <label class="soporte-item">Informe <input class="mini-box" type="checkbox" name="soporte_informe" value="1" <?php echo checked(post('soporte_informe')); ?>></label>
                            <label class="soporte-item">Recibidos a satisfacción <input class="mini-box" type="checkbox" name="soporte_satisfaccion" value="1" <?php echo checked(post('soporte_satisfaccion')); ?>></label>
                            <label class="soporte-item">Otros <input class="mini-box" type="checkbox" name="soporte_otros" value="1" <?php echo checked(post('soporte_otros')); ?>></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;font-weight:700;">Fecha del cierre</td>
                    <td style="width:50%;font-weight:700;">Visto bueno SST</td>
                </tr>
                <tr>
                    <td><input type="date" name="fecha_cierre" value="<?php echo $datos['fecha_cierre']; ?>"></td>
                    <td><input type="text" name="visto_bueno_sst" value="<?php echo $datos['visto_bueno_sst']; ?>"></td>
                </tr>
            </table>
        </form>
    </div>
</div>
</body>
</html>