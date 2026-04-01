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

$empresa = [
    'razon_social'   => post('razon_social'),
    'nit'            => post('nit'),
    'actividad'      => post('actividad'),
    'departamento'   => post('departamento'),
    'ciudad'         => post('ciudad'),
    'direccion'      => post('direccion'),
    'telefonos'      => post('telefonos'),
    'correo'         => post('correo'),
    'arl'            => post('arl'),
    'trabajadores'   => post('trabajadores'),
    'horario_lv'     => post('horario_lv', 'Lunes a viernes: 8:00 Am a 12:00 M. y de 2:00 pm a 6:00 pm'),
    'horario_sab'    => post('horario_sab', 'Sábados: 8:00 Am a 12:00 M.'),
    'fecha_documento'=> post('fecha_documento', date('Y-m-d')),
    'version'        => post('version', '0'),
    'codigo'         => post('codigo', 'AN-XX-SST-13'),
];

$introduccion = post('introduccion', 'El trabajo es una actividad que el individuo desarrolla para satisfacer sus necesidades básicas y obtener unas condiciones de vida acordes con su dignidad humana y poder realizarse como persona, tanto física como intelectual y socialmente.

Para trabajar con eficiencia, es necesario estar en buenas condiciones de salud; sin embargo, en muchas ocasiones, el trabajo contribuye a deteriorar la salud del individuo, debido a las condiciones inadecuadas en que se realiza.

Por esta razón, la empresa ha elaborado la Matriz de Identificación de Peligros, Valoración y Priorización del Riesgo, teniendo en cuenta que la efectividad de un control depende de un diagnóstico integral completo de la problemática existente.');

$obj_general = post('obj_general', 'Identificar los riesgos presentes en los ambientes de trabajo y en las operaciones desarrolladas por la empresa, que puedan ocasionar accidentes de trabajo y enfermedades laborales.');
$obj_esp_1 = post('obj_esp_1', 'Analizar y evaluar los peligros y sus riesgos mediante la aplicación de la Guía Técnica Colombiana GTC 45 versión 2010.');
$obj_esp_2 = post('obj_esp_2', 'Formular recomendaciones de carácter general, con el fin de orientar la estructuración del Sistema de Gestión de Seguridad y Salud en el Trabajo (SG-SST), en concordancia con las normas legales vigentes.');

$conclusion_1 = post('conclusion_1', 'La valoración de los factores de riesgo de la empresa permite especificar las acciones de prevención frente a las posibilidades de pérdidas humanas y materiales que los procesos exhiben en su flujo de trabajo.');
$conclusion_2 = post('conclusion_2', 'La gestión en seguridad y salud en el trabajo constituye una herramienta gerencial que se fortalece a través de la identificación, valoración, priorización e intervención de los riesgos existentes.');
$conclusion_3 = post('conclusion_3', 'Los riesgos prioritarios a intervenir para preservar la salud y seguridad deben definirse con base en la matriz de peligros, la valoración del riesgo y el plan de acción.');

$riesgos_prioritarios = post('riesgos_prioritarios', "RIESGO BIOMECÁNICO\nRIESGO FÍSICO\nRIESGO PSICOSOCIAL\nRIESGO CONDICIONES DE SEGURIDAD");

$tabla_peligros = isset($_POST['tabla_peligros']) && is_array($_POST['tabla_peligros']) ? $_POST['tabla_peligros'] : [];
if (empty($tabla_peligros)) {
    $tabla_peligros = [
        [
            'proceso' => '',
            'zona' => '',
            'actividad' => '',
            'tarea' => '',
            'rutinaria' => '',
            'peligro' => '',
            'clasificacion' => '',
            'efectos' => '',
            'controles' => '',
            'nd' => '',
            'ne' => '',
            'np' => '',
            'nc' => '',
            'nr' => '',
            'aceptabilidad' => '',
            'intervencion' => ''
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.1.1 Metodología Matriz de Peligros</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1500px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
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
        .encabezado td,.encabezado th,.tabla-datos td,.tabla-datos th,.tabla-info td,.tabla-info th,.tabla-peligros td,.tabla-peligros th{
            border:1px solid #6b6b6b;
            padding:6px;
            vertical-align:middle;
            overflow-wrap:anywhere;
            word-break:break-word;
        }
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{width:140px;height:65px;border:2px dashed #c8c8c8;display:flex;align-items:center;justify-content:center;margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center}
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px;line-height:1.35}
        .save-msg{
            margin:0 0 15px 0;padding:10px 14px;border-radius:8px;background:#e9f7ef;color:#166534;
            border:1px solid #b7e4c7;font-size:14px;font-weight:700;
        }
        .seccion-title{
            margin:18px 0 8px;
            font-size:13px;
            color:#213b67;
            font-style:italic;
            font-weight:700;
        }
        .texto-box,.lista-box{
            width:100%;
            border:1px solid #6b6b6b;
            min-height:110px;
            padding:10px 12px;
            font-size:13px;
            line-height:1.55;
            resize:vertical;
            outline:none;
            overflow-wrap:anywhere;
            word-break:break-word;
        }
        .lista-box{min-height:85px}

        .tabla-datos td:first-child{
            width:28%;
            font-weight:700;
            background:#f8fafc;
        }
        .tabla-info td:first-child{
            width:26%;
            font-weight:700;
            background:#f8fafc;
        }

        .tabla-datos input,.tabla-info input,.tabla-datos textarea,.tabla-info textarea{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:4px 5px;
            font-size:13px;
            line-height:1.4;
            overflow-wrap:anywhere;
            word-break:break-word;
        }
        .tabla-datos textarea,.tabla-info textarea{
            resize:vertical;
            min-height:60px;
            white-space:pre-wrap;
        }

        .tabla-info thead th,.tabla-peligros thead th{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            font-size:11px;
            line-height:1.2;
            padding:7px 5px;
        }
        .tabla-info td,.tabla-peligros td{
            font-size:12px;
            line-height:1.3;
        }

        .tabla-peligros-wrap{
            width:100%;
            overflow-x:hidden;
        }

        .tabla-peligros{
            width:100%;
            table-layout:fixed;
        }

        .tabla-peligros th,
        .tabla-peligros td{
            padding:4px;
        }

        .tabla-peligros input,
        .tabla-peligros textarea{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px 2px;
            font-size:11px;
            line-height:1.25;
            overflow-wrap:anywhere;
            word-break:break-word;
        }

        .tabla-peligros input{
            text-align:center;
        }

        .tabla-peligros textarea{
            resize:vertical;
            min-height:42px;
            white-space:pre-wrap;
        }

        .tabla-peligros th:nth-child(1), .tabla-peligros td:nth-child(1){width:7%}
        .tabla-peligros th:nth-child(2), .tabla-peligros td:nth-child(2){width:6%}
        .tabla-peligros th:nth-child(3), .tabla-peligros td:nth-child(3){width:7%}
        .tabla-peligros th:nth-child(4), .tabla-peligros td:nth-child(4){width:7%}
        .tabla-peligros th:nth-child(5), .tabla-peligros td:nth-child(5){width:5%}
        .tabla-peligros th:nth-child(6), .tabla-peligros td:nth-child(6){width:8%}
        .tabla-peligros th:nth-child(7), .tabla-peligros td:nth-child(7){width:8%}
        .tabla-peligros th:nth-child(8), .tabla-peligros td:nth-child(8){width:10%}
        .tabla-peligros th:nth-child(9), .tabla-peligros td:nth-child(9){width:9%}
        .tabla-peligros th:nth-child(10), .tabla-peligros td:nth-child(10){width:4%}
        .tabla-peligros th:nth-child(11), .tabla-peligros td:nth-child(11){width:4%}
        .tabla-peligros th:nth-child(12), .tabla-peligros td:nth-child(12){width:4%}
        .tabla-peligros th:nth-child(13), .tabla-peligros td:nth-child(13){width:4%}
        .tabla-peligros th:nth-child(14), .tabla-peligros td:nth-child(14){width:4%}
        .tabla-peligros th:nth-child(15), .tabla-peligros td:nth-child(15){width:7%}
        .tabla-peligros th:nth-child(16), .tabla-peligros td:nth-child(16){width:10%}

        .bloque{
            border:1px solid #6b6b6b;
            padding:12px;
            background:#fff;
        }
        .bloque p{
            font-size:13px;
            line-height:1.6;
            margin-bottom:10px;
            text-align:justify;
            overflow-wrap:anywhere;
            word-break:break-word;
        }
        .bloque p:last-child{margin-bottom:0}

        .dos-col{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
        }

        .acciones-tabla{
            margin-top:10px;
            display:flex;
            justify-content:flex-end;
        }
        .btn-agregar{
            background:#0d6efd;
            color:#fff;
        }
        .nota{
            font-size:12px;
            color:#4b5563;
            margin-top:8px;
        }

        @media (max-width: 1200px){
            .tabla-peligros-wrap{
                overflow-x:auto;
            }
            .tabla-peligros{
                min-width:1200px;
            }
        }

        @media (max-width: 980px){
            .dos-col{grid-template-columns:1fr}
            .toolbar{position:static}
            body{padding:10px}
            .contenedor{max-width:100%}
        }

        @page{
            size: landscape;
            margin: 10mm;
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar,.acciones-tabla{display:none}
            .contenedor{box-shadow:none;border:none;max-width:100%}
            .contenido{padding:6px}
            input, textarea, select{
                border:none !important;
                box-shadow:none !important;
            }
            .texto-box,.lista-box{border:1px solid #6b6b6b}
            .tabla-peligros-wrap{
                overflow:visible !important;
            }
            .tabla-peligros{
                min-width:auto !important;
                width:100% !important;
                table-layout:fixed !important;
            }
            .tabla-peligros th,
            .tabla-peligros td{
                font-size:9px !important;
                padding:3px !important;
            }
            .tabla-peligros input,
            .tabla-peligros textarea{
                font-size:9px !important;
                padding:2px 1px !important;
                min-height:30px !important;
            }
            .tabla-info thead th,.tabla-peligros thead th{
                font-size:9px !important;
                line-height:1.15 !important;
            }
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>4.1.1 Metodología para la Matriz de Identificación de Peligros</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form411">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form411" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:64%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:18%;font-weight:700;"><?php echo $empresa['version']; ?></td>
                </tr>
                <tr>
                    <td class="subtitulo">METODOLOGÍA PARA LA MATRIZ DE IDENTIFICACIÓN DE PELIGROS, VALORACIÓN Y PRIORIZACIÓN DEL RIESGO</td>
                    <td style="font-weight:700;">
                        <?php echo $empresa['codigo']; ?><br>
                        <?php echo $empresa['fecha_documento']; ?>
                    </td>
                </tr>
            </table>

            <div class="seccion-title">1. Información general del documento</div>
            <table class="tabla-datos">
                <tr>
                    <td>EMPRESA</td>
                    <td><input type="text" name="razon_social" value="<?php echo $empresa['razon_social']; ?>"></td>
                </tr>
                <tr>
                    <td>VERSIÓN</td>
                    <td><input type="text" name="version" value="<?php echo $empresa['version']; ?>"></td>
                </tr>
                <tr>
                    <td>CÓDIGO</td>
                    <td><input type="text" name="codigo" value="<?php echo $empresa['codigo']; ?>"></td>
                </tr>
                <tr>
                    <td>FECHA</td>
                    <td><input type="date" name="fecha_documento" value="<?php echo $empresa['fecha_documento']; ?>"></td>
                </tr>
            </table>

            <div class="seccion-title">2. Introducción</div>
            <textarea class="texto-box" name="introduccion"><?php echo $introduccion; ?></textarea>

            <div class="seccion-title">3. Objetivos</div>
            <table class="tabla-datos">
                <tr>
                    <td>OBJETIVO GENERAL</td>
                    <td><textarea name="obj_general"><?php echo $obj_general; ?></textarea></td>
                </tr>
                <tr>
                    <td>OBJETIVO ESPECÍFICO 1</td>
                    <td><textarea name="obj_esp_1"><?php echo $obj_esp_1; ?></textarea></td>
                </tr>
                <tr>
                    <td>OBJETIVO ESPECÍFICO 2</td>
                    <td><textarea name="obj_esp_2"><?php echo $obj_esp_2; ?></textarea></td>
                </tr>
            </table>

            <div class="seccion-title">4. Generalidades de la empresa</div>
            <table class="tabla-datos">
                <tr><td>RAZÓN SOCIAL</td><td><input type="text" name="razon_social" value="<?php echo $empresa['razon_social']; ?>"></td></tr>
                <tr><td>NIT</td><td><input type="text" name="nit" value="<?php echo $empresa['nit']; ?>"></td></tr>
                <tr><td>ACTIVIDAD ECONÓMICA (OBJETO SOCIAL)</td><td><textarea name="actividad"><?php echo $empresa['actividad']; ?></textarea></td></tr>
                <tr><td>DEPARTAMENTO</td><td><input type="text" name="departamento" value="<?php echo $empresa['departamento']; ?>"></td></tr>
                <tr><td>CIUDAD</td><td><input type="text" name="ciudad" value="<?php echo $empresa['ciudad']; ?>"></td></tr>
                <tr><td>DIRECCIÓN</td><td><input type="text" name="direccion" value="<?php echo $empresa['direccion']; ?>"></td></tr>
                <tr><td>TELÉFONOS</td><td><input type="text" name="telefonos" value="<?php echo $empresa['telefonos']; ?>"></td></tr>
                <tr><td>CORREO ELECTRÓNICO</td><td><input type="email" name="correo" value="<?php echo $empresa['correo']; ?>"></td></tr>
                <tr><td>ARL</td><td><input type="text" name="arl" value="<?php echo $empresa['arl']; ?>"></td></tr>
                <tr><td>No. DE TRABAJADORES</td><td><input type="number" name="trabajadores" value="<?php echo $empresa['trabajadores']; ?>"></td></tr>
                <tr><td>HORARIO LUNES A VIERNES</td><td><input type="text" name="horario_lv" value="<?php echo $empresa['horario_lv']; ?>"></td></tr>
                <tr><td>HORARIO SÁBADOS</td><td><input type="text" name="horario_sab" value="<?php echo $empresa['horario_sab']; ?>"></td></tr>
            </table>

            <div class="seccion-title">5. Marco teórico</div>
            <div class="bloque">
                <p><strong>ACCIDENTE DE TRABAJO:</strong> Es accidente de trabajo todo suceso repentino que sobrevenga por causa o con ocasión del trabajo y que produzca en el trabajador una lesión orgánica, perturbación funcional o psiquiátrica, invalidez o muerte.</p>
                <p><strong>ACTIVIDAD RUTINARIA:</strong> Actividad que forma parte de un proceso de la organización, ha sido planificada y es estandarizable.</p>
                <p><strong>ACTIVIDAD NO RUTINARIA:</strong> Actividad que no se ha planificado ni estandarizado dentro de un proceso o que la organización determine como no rutinaria por su baja frecuencia de ejecución.</p>
                <p><strong>ANÁLISIS DEL RIESGO:</strong> Proceso para comprender la naturaleza del riesgo y determinar su nivel.</p>
                <p><strong>CONSECUENCIA:</strong> Resultado, en términos de lesión o enfermedad, de la materialización de un riesgo.</p>
                <p><strong>EPP:</strong> Dispositivo que sirve como barrera entre un peligro y alguna parte del cuerpo de una persona.</p>
                <p><strong>ENFERMEDAD LABORAL:</strong> Enfermedad contraída como resultado de la exposición a factores de riesgo inherentes a la actividad laboral o del medio en el que el trabajador se ha visto obligado a trabajar.</p>
                <p><strong>EVALUACIÓN DEL RIESGO:</strong> Proceso para determinar el nivel de riesgo asociado al nivel de probabilidad y al nivel de consecuencia.</p>
                <p><strong>EXPOSICIÓN:</strong> Situación en la cual las personas se encuentran en contacto con los peligros.</p>
                <p><strong>IDENTIFICACIÓN DEL PELIGRO:</strong> Proceso para reconocer si existe un peligro y definir sus características.</p>
            </div>

            <div class="seccion-title">6. Identificación de los peligros y valoración de los riesgos</div>
            <div class="bloque">
                <p>El propósito general de la identificación de los peligros y la valoración de los riesgos en Seguridad y Salud en el Trabajo es entender los peligros que se pueden generar en el desarrollo de las actividades, con el fin de que la organización pueda establecer los controles necesarios hasta asegurar que cualquier riesgo sea aceptable.</p>
                <p>La valoración de los riesgos es la base para la gestión proactiva de SST, liderada por la alta dirección como parte de la gestión integral del riesgo, con la participación y compromiso de todos los niveles de la organización y otras partes interesadas.</p>
                <p>Todos los trabajadores deben identificar y comunicar a su empleador los peligros asociados a su actividad laboral. Los empleadores tienen el deber de evaluar los riesgos derivados de estas actividades.</p>
            </div>

            <div class="seccion-title">6.1 Actividades para identificar los peligros y valorar los riesgos</div>
            <table class="tabla-info">
                <thead>
                    <tr>
                        <th>ACTIVIDAD</th>
                        <th>DESCRIPCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Definir instrumento</td><td>Disponer de una herramienta donde se registre la información para la identificación de peligros y valoración de riesgos.</td></tr>
                    <tr><td>Clasificar procesos</td><td>Preparar una lista de procesos, actividades y tareas; incluir instalaciones, planta, personas y procedimientos.</td></tr>
                    <tr><td>Identificar peligros</td><td>Incluir todos los peligros asociados con cada actividad laboral y considerar quién, cuándo y cómo puede resultar afectado.</td></tr>
                    <tr><td>Identificar controles existentes</td><td>Relacionar los controles implementados por la organización para reducir el riesgo.</td></tr>
                    <tr><td>Evaluar riesgo</td><td>Calificar el riesgo asociado a cada peligro considerando la eficacia de los controles, la probabilidad y la consecuencia.</td></tr>
                    <tr><td>Aceptabilidad</td><td>Definir criterios para determinar si el riesgo es aceptable o no.</td></tr>
                    <tr><td>Plan de acción</td><td>Elaborar el plan de acción para mejorar los controles cuando sea necesario.</td></tr>
                    <tr><td>Seguimiento</td><td>Realizar revisión, actualización y trazabilidad de los controles implementados.</td></tr>
                </tbody>
            </table>

            <div class="seccion-title">6.2 Instrumento para recolectar la información</div>
            <table class="tabla-info">
                <thead>
                    <tr>
                        <th>ÍTEM</th>
                        <th>DESCRIPCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Proceso</td><td>Proceso al cual pertenece la actividad evaluada.</td></tr>
                    <tr><td>Zona / lugar</td><td>Área o dependencia donde se desarrolla la actividad.</td></tr>
                    <tr><td>Actividad / tarea</td><td>Acción específica ejecutada por el trabajador.</td></tr>
                    <tr><td>Rutinaria</td><td>Indicar si la tarea es rutinaria o no rutinaria.</td></tr>
                    <tr><td>Peligro</td><td>Descripción y clasificación del peligro identificado.</td></tr>
                    <tr><td>Efectos posibles</td><td>Consecuencias esperadas en salud o seguridad.</td></tr>
                    <tr><td>Controles existentes</td><td>Fuente, medio e individuo.</td></tr>
                    <tr><td>Evaluación</td><td>ND, NE, NP, NC, NR e interpretación.</td></tr>
                    <tr><td>Valoración</td><td>Aceptabilidad del riesgo y criterios para establecer controles.</td></tr>
                    <tr><td>Intervención</td><td>Eliminación, sustitución, ingeniería, administrativos y EPP.</td></tr>
                </tbody>
            </table>

            <div class="seccion-title">7. Clasificación de la gravedad de los niveles de daño</div>
            <table class="tabla-info">
                <thead>
                    <tr>
                        <th>CATEGORÍA DEL DAÑO</th>
                        <th>DAÑO LEVE</th>
                        <th>DAÑO MEDIO</th>
                        <th>DAÑO EXTREMO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SALUD</td>
                        <td>Molestias e irritación; enfermedad temporal que produce malestar.</td>
                        <td>Enfermedades que causan incapacidad temporal.</td>
                        <td>Enfermedades agudas o crónicas que generan incapacidad permanente, invalidez o muerte.</td>
                    </tr>
                    <tr>
                        <td>SEGURIDAD</td>
                        <td>Lesiones superficiales, heridas de poca profundidad, contusiones.</td>
                        <td>Laceraciones, heridas profundas, quemaduras de primer grado, esguinces graves, fracturas de huesos cortos.</td>
                        <td>Amputaciones, fracturas de huesos largos, trauma craneoencefálico, quemaduras graves, lesiones severas de columna, ojo u oído.</td>
                    </tr>
                </tbody>
            </table>

            <div class="seccion-title">8. Evaluación de los riesgos</div>
            <div class="dos-col">
                <table class="tabla-info">
                    <thead>
                        <tr>
                            <th colspan="3">Determinación del nivel de deficiencia (ND)</th>
                        </tr>
                        <tr>
                            <th>NIVEL</th>
                            <th>ND</th>
                            <th>SIGNIFICADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>MUY ALTO (MA)</td><td>10</td><td>Peligro muy posible o controles nulos/inexistentes.</td></tr>
                        <tr><td>ALTO (A)</td><td>6</td><td>Peligro que puede dar lugar a consecuencias significativas o controles bajos.</td></tr>
                        <tr><td>MEDIO (M)</td><td>2</td><td>Peligros de menor importancia o controles moderados.</td></tr>
                        <tr><td>BAJO (B)</td><td>No aplica valor</td><td>Riesgo controlado o sin anomalía destacable.</td></tr>
                    </tbody>
                </table>

                <table class="tabla-info">
                    <thead>
                        <tr>
                            <th colspan="3">Determinación del nivel de exposición (NE)</th>
                        </tr>
                        <tr>
                            <th>NIVEL</th>
                            <th>NE</th>
                            <th>SIGNIFICADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>CONTINUA (EC)</td><td>4</td><td>Exposición sin interrupción o varias veces con tiempo prolongado.</td></tr>
                        <tr><td>FRECUENTE (EF)</td><td>3</td><td>Exposición varias veces durante la jornada por tiempos cortos.</td></tr>
                        <tr><td>OCASIONAL (EO)</td><td>2</td><td>Exposición alguna vez durante la jornada y por corto tiempo.</td></tr>
                        <tr><td>ESPORÁDICA (EE)</td><td>1</td><td>Exposición eventual.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="seccion-title">9. Nivel de probabilidad, consecuencia, riesgo y aceptabilidad</div>
            <div class="dos-col">
                <table class="tabla-info">
                    <thead>
                        <tr>
                            <th colspan="3">Nivel de consecuencias (NC)</th>
                        </tr>
                        <tr>
                            <th>NIVEL</th>
                            <th>NC</th>
                            <th>DAÑOS PERSONALES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Mortal o catastrófico</td><td>100</td><td>Muerte</td></tr>
                        <tr><td>Muy grave</td><td>60</td><td>Lesiones graves irreparables, invalidez.</td></tr>
                        <tr><td>Grave</td><td>25</td><td>Lesiones con incapacidad laboral temporal.</td></tr>
                        <tr><td>Leve</td><td>10</td><td>Lesiones que no requieren hospitalización.</td></tr>
                    </tbody>
                </table>

                <table class="tabla-info">
                    <thead>
                        <tr>
                            <th colspan="3">Aceptabilidad del riesgo</th>
                        </tr>
                        <tr>
                            <th>NIVEL DE RIESGO</th>
                            <th>CLASIFICACIÓN</th>
                            <th>SIGNIFICADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>I</td><td>INACEPTABLE</td><td>Suspender actividades hasta controlar el riesgo.</td></tr>
                        <tr><td>II</td><td>NO ACEPTABLE / ACEPTABLE CON CONTROL ESPECÍFICO</td><td>Corregir y adoptar medidas de control de inmediato.</td></tr>
                        <tr><td>III</td><td>MEJORABLE</td><td>Mejorar si es posible y justificar intervención.</td></tr>
                        <tr><td>IV</td><td>PERMISIBLE</td><td>Mantener controles y verificar periódicamente.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="seccion-title">10. Medidas de intervención</div>
            <table class="tabla-info">
                <thead>
                    <tr>
                        <th>JERARQUÍA DE CONTROL</th>
                        <th>DEFINICIÓN Y EJEMPLO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Eliminación</td><td>Modificar un diseño para eliminar el peligro.</td></tr>
                    <tr><td>Sustitución</td><td>Sustituir por un material menos peligroso o reducir la energía del sistema.</td></tr>
                    <tr><td>Controles de ingeniería</td><td>Ventilación, protecciones para máquinas, enclavamientos, cerramientos acústicos, etc.</td></tr>
                    <tr><td>Señalización / administrativos</td><td>Alarmas, procedimientos, inspecciones, controles de acceso, capacitación.</td></tr>
                    <tr><td>Equipo de protección personal</td><td>Gafas, protección auditiva, máscaras, arneses, respiradores, guantes.</td></tr>
                </tbody>
            </table>

            <div class="seccion-title">11. Tabla de peligros</div>
            <div class="tabla-peligros-wrap">
                <table class="tabla-peligros" id="tablaPeligros">
                    <thead>
                        <tr>
                            <th>PROCESO</th>
                            <th>ZONA / LUGAR</th>
                            <th>ACTIVIDAD</th>
                            <th>TAREA</th>
                            <th>RUTINARIA</th>
                            <th>PELIGRO</th>
                            <th>CLASIFICACIÓN</th>
                            <th>EFECTOS POSIBLES</th>
                            <th>CONTROLES EXISTENTES</th>
                            <th>ND</th>
                            <th>NE</th>
                            <th>NP</th>
                            <th>NC</th>
                            <th>NR</th>
                            <th>ACEPTABILIDAD</th>
                            <th>MEDIDAS DE INTERVENCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPeligros">
                        <?php foreach ($tabla_peligros as $i => $fila): ?>
                            <tr>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][proceso]"><?php echo htmlspecialchars($fila['proceso'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][zona]"><?php echo htmlspecialchars($fila['zona'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][actividad]"><?php echo htmlspecialchars($fila['actividad'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][tarea]"><?php echo htmlspecialchars($fila['tarea'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="text" name="tabla_peligros[<?php echo $i; ?>][rutinaria]" value="<?php echo htmlspecialchars($fila['rutinaria'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][peligro]"><?php echo htmlspecialchars($fila['peligro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][clasificacion]"><?php echo htmlspecialchars($fila['clasificacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][efectos]"><?php echo htmlspecialchars($fila['efectos'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][controles]"><?php echo htmlspecialchars($fila['controles'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><input type="number" step="any" name="tabla_peligros[<?php echo $i; ?>][nd]" value="<?php echo htmlspecialchars($fila['nd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="campo-nd"></td>
                                <td><input type="number" step="any" name="tabla_peligros[<?php echo $i; ?>][ne]" value="<?php echo htmlspecialchars($fila['ne'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="campo-ne"></td>
                                <td><input type="number" step="any" name="tabla_peligros[<?php echo $i; ?>][np]" value="<?php echo htmlspecialchars($fila['np'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="campo-np"></td>
                                <td><input type="number" step="any" name="tabla_peligros[<?php echo $i; ?>][nc]" value="<?php echo htmlspecialchars($fila['nc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="campo-nc"></td>
                                <td><input type="number" step="any" name="tabla_peligros[<?php echo $i; ?>][nr]" value="<?php echo htmlspecialchars($fila['nr'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="campo-nr"></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][aceptabilidad]"><?php echo htmlspecialchars($fila['aceptabilidad'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td><textarea name="tabla_peligros[<?php echo $i; ?>][intervencion]"><?php echo htmlspecialchars($fila['intervencion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="acciones-tabla">
                <button type="button" class="btn btn-agregar" id="agregarFila">Agregar fila</button>
            </div>
            <div class="nota">La tabla inicia con una fila y puedes agregar todas las que necesites sin afectar el diseño.</div>

            <div class="seccion-title">12. Conclusiones</div>
            <table class="tabla-datos">
                <tr>
                    <td>CONCLUSIÓN 1</td>
                    <td><textarea name="conclusion_1"><?php echo $conclusion_1; ?></textarea></td>
                </tr>
                <tr>
                    <td>CONCLUSIÓN 2</td>
                    <td><textarea name="conclusion_2"><?php echo $conclusion_2; ?></textarea></td>
                </tr>
                <tr>
                    <td>CONCLUSIÓN 3</td>
                    <td><textarea name="conclusion_3"><?php echo $conclusion_3; ?></textarea></td>
                </tr>
                <tr>
                    <td>RIESGOS PRIORITARIOS A INTERVENIR</td>
                    <td><textarea name="riesgos_prioritarios"><?php echo $riesgos_prioritarios; ?></textarea></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>
(function(){
    const tbody = document.getElementById('tbodyPeligros');
    const btnAgregar = document.getElementById('agregarFila');

    function recalcularFila(tr){
        const nd = parseFloat(tr.querySelector('.campo-nd')?.value) || 0;
        const ne = parseFloat(tr.querySelector('.campo-ne')?.value) || 0;
        const nc = parseFloat(tr.querySelector('.campo-nc')?.value) || 0;
        const npInput = tr.querySelector('.campo-np');
        const nrInput = tr.querySelector('.campo-nr');
        const aceptabilidad = tr.querySelector('textarea[name*="[aceptabilidad]"]');

        const np = nd * ne;
        const nr = np * nc;

        if (npInput) npInput.value = np > 0 ? np : '';
        if (nrInput) nrInput.value = nr > 0 ? nr : '';

        if (aceptabilidad) {
            let texto = '';
            if (nr >= 600) {
                texto = 'I - INACEPTABLE';
            } else if (nr >= 150) {
                texto = 'II - NO ACEPTABLE / CONTROL ESPECÍFICO';
            } else if (nr >= 40) {
                texto = 'III - MEJORABLE';
            } else if (nr > 0) {
                texto = 'IV - PERMISIBLE';
            }
            aceptabilidad.value = texto;
        }
    }

    function recalcularTodo(){
        tbody.querySelectorAll('tr').forEach(recalcularFila);
    }

    function crearFila(index){
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><textarea name="tabla_peligros[${index}][proceso]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][zona]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][actividad]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][tarea]"></textarea></td>
            <td><input type="text" name="tabla_peligros[${index}][rutinaria]"></td>
            <td><textarea name="tabla_peligros[${index}][peligro]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][clasificacion]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][efectos]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][controles]"></textarea></td>
            <td><input type="number" step="any" name="tabla_peligros[${index}][nd]" class="campo-nd"></td>
            <td><input type="number" step="any" name="tabla_peligros[${index}][ne]" class="campo-ne"></td>
            <td><input type="number" step="any" name="tabla_peligros[${index}][np]" class="campo-np"></td>
            <td><input type="number" step="any" name="tabla_peligros[${index}][nc]" class="campo-nc"></td>
            <td><input type="number" step="any" name="tabla_peligros[${index}][nr]" class="campo-nr"></td>
            <td><textarea name="tabla_peligros[${index}][aceptabilidad]"></textarea></td>
            <td><textarea name="tabla_peligros[${index}][intervencion]"></textarea></td>
        `;
        return tr;
    }

    btnAgregar.addEventListener('click', function(){
        const index = tbody.querySelectorAll('tr').length;
        tbody.appendChild(crearFila(index));
    });

    document.addEventListener('input', function(e){
        if (e.target.closest('#tbodyPeligros')) {
            const tr = e.target.closest('tr');
            if (tr) recalcularFila(tr);
        }
    });

    recalcularTodo();
})();
</script>
</body>
</html>