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
// Ajusta el ID de este ítem según tu BD (ej: 51 para Investigación Accidentes)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 51; 

// --- Lógica de Empresa (Logo y Datos) ---
$logoEmpresaUrl = "";
$nombreEmpresaDefault = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        
        if (!empty($empData['nombre_empresa'])) $nombreEmpresaDefault = $empData['nombre_empresa'];
        if (!empty($empData['logo_url'])) $logoEmpresaUrl = $empData['logo_url'];
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? $resFormulario['campos'] ?? null;

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

// 3. FUNCIONES PARA LEER DATOS
function oldv($key, $default = '') {
    global $datosCampos;
    if (isset($datosCampos[$key]) && $datosCampos[$key] !== '') {
        return htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars((string)$default, ENT_QUOTES, 'UTF-8');
}

function isChecked($key) {
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] == '1') ? 'checked' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.2.1-2 - Investigación de Accidentes / Incidentes</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f2f4f7;
            padding:20px;
            color:#111;
        }

        .contenedor{
            max-width:1500px;
            margin:0 auto;
            background:#fff;
            border:1px solid #bfc7d1;
            box-shadow:0 4px 18px rgba(0,0,0,.08);
        }

        .toolbar{
            position:sticky;
            top:0;
            z-index:100;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:12px;
            padding:14px 18px;
            background:#dde7f5;
            border-bottom:1px solid #c8d3e2;
        }

        .toolbar h1{
            font-size:20px;
            color:#213b67;
            font-weight:700;
        }

        .acciones{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn{
            border:none;
            padding:10px 18px;
            border-radius:8px;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            transition:.2s ease;
        }

        .btn:hover{
            transform:translateY(-1px);
            opacity:.95;
        }

        .btn-guardar{ background:#198754; color:#fff; }
        .btn-atras{ background:#6c757d; color:#fff; }
        .btn-imprimir{ background:#0d6efd; color:#fff; }

        .formulario{
            padding:18px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .encabezado td, .encabezado th,
        .tabla-base td, .tabla-base th,
        .tabla-causas td, .tabla-causas th,
        .tabla-plan td, .tabla-plan th,
        .tabla-investigadores td, .tabla-investigadores th,
        .tabla-capacitaciones td, .tabla-capacitaciones th{
            border:1px solid #6b6b6b;
            padding:8px;
            vertical-align:top;
        }

        .encabezado td, .encabezado th{
            text-align:center;
        }

        .logo-box{
            width:140px;
            height:65px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            color:#999;
            font-weight:bold;
            font-size:14px;
            text-align:center;
        }

        .titulo-principal{
            font-size:16px;
            font-weight:700;
        }

        .subtitulo{
            font-size:14px;
        }

        .seccion{
            margin-top:18px;
        }

        .seccion h3{
            font-size:17px;
            margin-bottom:10px;
            color:#213b67;
            border-bottom:2px solid #d9e2f2;
            padding-bottom:6px;
        }

        .tabla-base input[type="text"],
        .tabla-base input[type="date"],
        .tabla-base input[type="time"],
        .tabla-base input[type="number"],
        .tabla-base textarea,
        .tabla-plan input[type="text"],
        .tabla-plan input[type="date"],
        .tabla-plan textarea,
        .tabla-investigadores input[type="text"],
        .tabla-capacitaciones input[type="text"],
        .tabla-capacitaciones input[type="date"],
        .tabla-causas textarea{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:4px;
            font-size:13px;
        }

        textarea{
            resize:none;
            overflow:hidden;
            min-height:70px;
            line-height:1.4;
            white-space:pre-wrap;
            word-break:break-word;
        }

        .desc-box textarea{
            min-height:130px;
        }

        .foto-box{
            min-height:150px;
            border:2px dashed #c9d2e3;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            color:#7a8699;
            background:#fafcff;
            padding:12px;
        }

        .grid-2{
            display:grid;
            grid-template-columns:1.2fr 1fr;
            gap:14px;
        }

        .grid-3{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:14px;
        }

        .checks-inline{
            display:flex;
            gap:16px;
            flex-wrap:wrap;
            align-items:center;
        }

        .checks-inline label{
            display:flex;
            gap:6px;
            align-items:center;
            font-size:13px;
        }

        .lista-checks{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:6px 18px;
        }

        .lista-checks label{
            display:flex;
            gap:8px;
            align-items:flex-start;
            font-size:13px;
            line-height:1.3;
        }

        .tabla-causas thead th,
        .tabla-plan thead th,
        .tabla-investigadores thead th,
        .tabla-capacitaciones thead th{
            background:#f4f6fa;
            text-align:center;
        }

        .mini{
            width:60px;
            text-align:center;
        }

        .nota{
            margin-top:18px;
            padding:12px;
            border:1px solid #d9d9d9;
            background:#fafafa;
            font-size:13px;
            line-height:1.5;
            font-weight:700;
        }

        @media (max-width: 1100px){
            .grid-2,
            .grid-3,
            .lista-checks{
                grid-template-columns:1fr;
            }
        }

        @media print{
            body{
                background:#fff;
                padding:0;
            }

            .toolbar, .print-hide{
                display:none !important;
            }

            .contenedor{
                box-shadow:none;
                border:none;
            }

            .formulario{
                padding:8px;
            }

            input, textarea{
                border:none !important;
                padding-left:0 !important;
                padding-right:0 !important;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>3.2.1-2 - Investigación de Accidentes / Incidentes</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
    </div>

    <div class="formulario">
        <form id="form3212">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">INVESTIGACIÓN DE ACCIDENTES / INCIDENTES</td>
                    <td style="font-weight:700;">RE-SST-10<br><?= date('d/m/Y') ?></td>
                </tr>
            </table>

            <div class="seccion">
                <h3>Datos generales del evento</h3>
                <table class="tabla-base">
                    <tr>
                        <td style="width:18%;"><strong>Fecha del evento</strong><br><input type="date" name="fecha_evento" value="<?= oldv('fecha_evento') ?>"></td>
                        <td style="width:12%;"><strong>Hora</strong><br><input type="time" name="hora_evento" value="<?= oldv('hora_evento') ?>"></td>
                        <td style="width:18%;"><strong>Departamento</strong><br><input type="text" name="departamento" value="<?= oldv('departamento') ?>"></td>
                        <td style="width:18%;"><strong>Municipio</strong><br><input type="text" name="municipio" value="<?= oldv('municipio') ?>"></td>
                        <td style="width:17%;"><strong>Planta</strong><br><input type="text" name="planta" value="<?= oldv('planta') ?>"></td>
                        <td style="width:17%;"><strong>Lugar</strong><br><input type="text" name="lugar" value="<?= oldv('lugar') ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Tipo de evento</strong>
                            <div class="checks-inline" style="margin-top:8px;">
                                <label><input type="checkbox" name="accidente" value="1" <?= isChecked('accidente') ?>> Accidente</label>
                                <label><input type="checkbox" name="incidente" value="1" <?= isChecked('incidente') ?>> Incidente</label>
                            </div>
                        </td>
                        <td colspan="2">
                            <strong>Zona</strong>
                            <div class="checks-inline" style="margin-top:8px;">
                                <label><input type="checkbox" name="urbana" value="1" <?= isChecked('urbana') ?>> Urbana</label>
                                <label><input type="checkbox" name="rural" value="1" <?= isChecked('rural') ?>> Rural</label>
                            </div>
                        </td>
                        <td colspan="2"><strong>Fecha investigación</strong><br><input type="date" name="fecha_investigacion" value="<?= oldv('fecha_investigacion') ?>"></td>
                    </tr>
                </table>
            </div>

            <div class="seccion">
                <h3>Antecedentes del trabajador</h3>
                <table class="tabla-base">
                    <tr>
                        <td style="width:25%;"><strong>Nombre completo</strong><br><input type="text" name="nombre_completo" value="<?= oldv('nombre_completo') ?>"></td>
                        <td style="width:15%;"><strong>Cédula</strong><br><input type="text" name="cedula" value="<?= oldv('cedula') ?>"></td>
                        <td style="width:10%;"><strong>Edad</strong><br><input type="number" name="edad" value="<?= oldv('edad') ?>"></td>
                        <td style="width:20%;"><strong>Cargo</strong><br><input type="text" name="cargo" value="<?= oldv('cargo') ?>"></td>
                        <td style="width:15%;"><strong>Fecha ingreso</strong><br><input type="date" name="fecha_ingreso" value="<?= oldv('fecha_ingreso') ?>"></td>
                        <td style="width:15%;"><strong>Días incapacidad</strong><br><input type="number" name="dias_incapacidad" value="<?= oldv('dias_incapacidad') ?>"></td>
                    </tr>
                    <tr>
                        <td><strong>Teléfono / Ext</strong><br><input type="text" name="telefono" value="<?= oldv('telefono') ?>"></td>
                        <td><strong>Celular</strong><br><input type="text" name="celular" value="<?= oldv('celular') ?>"></td>
                        <td colspan="2"><strong>Tiempo laborado antes del accidente</strong><br><input type="text" name="tiempo_laborado" value="<?= oldv('tiempo_laborado') ?>"></td>
                        <td colspan="2">
                            <strong>Tipo de vinculación</strong>
                            <div class="checks-inline" style="margin-top:8px;">
                                <label><input type="checkbox" name="tv_mision" value="1" <?= isChecked('tv_mision') ?>> Misión</label>
                                <label><input type="checkbox" name="tv_independiente" value="1" <?= isChecked('tv_independiente') ?>> Independiente</label>
                                <label><input type="checkbox" name="tv_estudiante" value="1" <?= isChecked('tv_estudiante') ?>> Estudiante o aprendiz</label>
                                <label><input type="checkbox" name="tv_contratista" value="1" <?= isChecked('tv_contratista') ?>> Contratista</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong>Inducción</strong>
                            <div class="checks-inline" style="margin-top:8px;">
                                <label><input type="checkbox" name="induccion_si" value="1" <?= isChecked('induccion_si') ?>> Sí</label>
                                <label><input type="checkbox" name="induccion_no" value="1" <?= isChecked('induccion_no') ?>> No</label>
                            </div>
                        </td>
                        <td colspan="3"><strong>Consecuencia / Lesión</strong><br><input type="text" name="lesion" value="<?= oldv('lesion') ?>"></td>
                    </tr>
                </table>
            </div>

            <div class="seccion">
                <h3>Equipo investigador</h3>
                <table class="tabla-base">
                    <tr>
                        <td><input type="text" name="investigador_1" value="<?= oldv('investigador_1', 'Jefe inmediato del trabajador accidentado o del área donde ocurrió el accidente.') ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="investigador_2" value="<?= oldv('investigador_2', 'Un representante del COPASST o Vigía de Seguridad y Salud en el Trabajo.') ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="investigador_3" value="<?= oldv('investigador_3', 'Encargado del desarrollo del SG SST.') ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="investigador_4" value="<?= oldv('investigador_4', 'Cuando se estime necesario representante de la ARL.') ?>"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="investigador_5" value="<?= oldv('investigador_5', 'Cuando el accidente se considere grave o produzca la muerte en la investigación deberá participar un profesional con licencia en Salud Ocupacional propio o contratado, así como el personal de la empresa encargado del diseño de normas, procesos y/o mantenimiento.') ?>"></td>
                    </tr>
                </table>
            </div>

            <div class="seccion grid-2">
                <div>
                    <h3>Descripción del accidente</h3>
                    <div class="desc-box">
                        <table class="tabla-base">
                            <tr>
                                <td>
                                    <textarea name="descripcion_accidente"><?= oldv('descripcion_accidente') ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div>
                    <h3>Registro fotográfico</h3>
                    <table class="tabla-base">
                        <tr>
                            <td>
                                <div class="foto-box">
                                    REGISTRO FOTOGRÁFICO<br>
                                    Puedes luego reemplazar este bloque por carga de imagen real.
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="seccion">
                <h3>Tipo de contacto</h3>
                <table class="tabla-base">
                    <tr>
                        <td>
                            <div class="lista-checks">
                                <?php
                                $contactos = [
                                    'Atrapado entre o debajo (Chancado, amputado)',
                                    'Golpeada contra (chocar contra algo)',
                                    'Golpeado por (impactado por objeto en movimiento)',
                                    'Caída a un nivel más bajo',
                                    'Caída en el mismo nivel',
                                    'Resbalar, caer, tropezar',
                                    'Atrapado (puntos de pellizco y mordida)',
                                    'Cogido (enganchado, colgado)',
                                    'Sobreesfuerzo / sobrecarga',
                                    'Falla del equipo, herramienta, maquinaria o instalación',
                                    'Contacto con electricidad, calor, frío, radiación, tóxicos, ruido',
                                    'Otros tipos de contacto'
                                ];
                                foreach ($contactos as $i => $texto):
                                    $name = 'contacto_' . $i;
                                ?>
                                    <label>
                                        <input type="checkbox" name="<?= $name ?>" value="1" <?= isChecked($name) ?>>
                                        <span><?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top:10px;">
                                <strong>Otro / detalle:</strong>
                                <input type="text" name="contacto_otro" value="<?= oldv('contacto_otro') ?>" style="width:100%; border:none; border-bottom:1px solid #8c8c8c; margin-top:6px; padding:6px 4px; background:transparent;">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="seccion">
                <h3>Capacitaciones / entrenamientos recibidos</h3>
                <table class="tabla-capacitaciones">
                    <thead>
                        <tr>
                            <th>ACTIVIDAD</th>
                            <th style="width:180px;">FECHA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i=1; $i<=5; $i++): ?>
                            <tr>
                                <td><input type="text" name="cap_actividad_<?= $i ?>" value="<?= oldv("cap_actividad_$i") ?>"></td>
                                <td><input type="date" name="cap_fecha_<?= $i ?>" value="<?= oldv("cap_fecha_$i") ?>"></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="seccion">
                <h3>Causas inmediatas directas</h3>
                <table class="tabla-causas">
                    <thead>
                        <tr>
                            <th>Prácticas / Actos inseguros</th>
                            <th>Condiciones inseguras</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea name="actos_inseguros"><?= oldv('actos_inseguros', "Manejo de equipo sin autorización\nOmisión de advertir\nUsar equipo defectuoso\nNo usar EPP correctos\nCarga incorrecta\nOmisión de asegurar\nOperar a velocidad indebida\nDesactivar dispositivos de seguridad\nDar servicio a equipo en funcionamiento\nJugueteo, bromas\nBajo influencia del alcohol\nColocación incorrecta\nLevantar incorrectamente\nPosición indebida\nUso inapropiado del equipo\nOtras") ?></textarea>
                            </td>
                            <td>
                                <textarea name="condiciones_inseguras"><?= oldv('condiciones_inseguras', "Protección y barreras inadecuadas o inexistentes\nEPP inadecuado, deteriorado o impropio\nPeligro de explosión o incendio\nDesorden o aseo deficiente\nExposición al ruido o vibraciones\nHerramienta, equipo o material defectuoso\nCongestión en el trabajo o acción restringida\nSistema de advertencia o señalización inexistente\nIluminación inadecuada\nVentilación inadecuada\nCondiciones ambientales peligrosas\nExposición a radiación ionizante / no ionizante\nExposición a temperaturas extremas\nExposición a sustancias químicas peligrosas\nHecho vandálico / delincuencial\nOtras") ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="seccion">
                <h3>Causas básicas raíz</h3>
                <table class="tabla-causas">
                    <thead>
                        <tr>
                            <th>Factores personales</th>
                            <th>Factores del trabajo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea name="factores_personales"><?= oldv('factores_personales', "1. Capacidades físicas / fisiológicas inadecuadas\n2. Capacidad mental / psicológica inadecuada\n3. Tensión física o fisiológica\n4. Tensión mental o psicológica\n5. Falta de conocimiento\n5.3 Entrenamiento inicial inadecuado\n6. Falta de habilidad\n7. Motivación inadecuada") ?></textarea>
                            </td>
                            <td>
                                <textarea name="factores_trabajo"><?= oldv('factores_trabajo', "8. Falta de liderazgo y/o supervisión\n8.4 Políticas, procedimientos, prácticas o pautas inadecuadas\n9. Ingeniería inadecuada\n10. Adquisiciones inadecuadas\n11. Mantenimiento inadecuado\n12. Herramientas, vehículos inadecuados\n13. Estándares de trabajo inadecuados\n14. Desgaste excesivo\n15. Abuso o mal uso") ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="seccion">
                <h3>Plan de acción</h3>
                <table class="tabla-plan">
                    <thead>
                        <tr>
                            <th style="width:230px;">CAUSA</th>
                            <th>ACCIÓN A TOMAR</th>
                            <th style="width:170px;">RESPONSABLE</th>
                            <th style="width:150px;">FECHA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $causas = [
                            '1. Acto inseguro',
                            '2. Condición insegura',
                            '3. Factores personales',
                            '4. Factores del trabajo'
                        ];
                        foreach ($causas as $i => $causa):
                            $n = $i + 1;
                        ?>
                            <tr>
                                <td><input type="text" name="plan_causa_<?= $n ?>" value="<?= oldv("plan_causa_$n", $causa) ?>"></td>
                                <td><textarea name="plan_accion_<?= $n ?>"><?= oldv("plan_accion_$n") ?></textarea></td>
                                <td><input type="text" name="plan_responsable_<?= $n ?>" value="<?= oldv("plan_responsable_$n") ?>"></td>
                                <td><input type="date" name="plan_fecha_<?= $n ?>" value="<?= oldv("plan_fecha_$n") ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:8px; font-size:12px;">
                    Controles: E: Eliminación &nbsp;&nbsp; S: Sustitución &nbsp;&nbsp; CI: Control de ingeniería &nbsp;&nbsp; CA: Control administrativo &nbsp;&nbsp; EPP: Elementos de protección personal
                </div>
            </div>

            <div class="seccion">
                <h3>Datos de los investigadores</h3>
                <table class="tabla-investigadores">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>No. de documento</th>
                            <th>Licencia en S.O</th>
                            <th>Firma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i=1; $i<=4; $i++): ?>
                            <tr>
                                <td><input type="text" name="inv_nombre_<?= $i ?>" value="<?= oldv("inv_nombre_$i") ?>"></td>
                                <td><input type="text" name="inv_cargo_<?= $i ?>" value="<?= oldv("inv_cargo_$i") ?>"></td>
                                <td><input type="text" name="inv_doc_<?= $i ?>" value="<?= oldv("inv_doc_$i") ?>"></td>
                                <td><input type="text" name="inv_lic_<?= $i ?>" value="<?= oldv("inv_lic_$i") ?>"></td>
                                <td><input type="text" name="inv_firma_<?= $i ?>" value="<?= oldv("inv_firma_$i") ?>"></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="nota print-hide">
                Adjuntar copia del FURAT para la investigación del accidente. Este formato fue estructurado para incluir antecedentes del trabajador, equipo investigador, tipo de contacto, causas inmediatas, causas básicas, plan de acción y seguimiento de la investigación.
            </div>
        </form>
    </div>
</div>

<script>
function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea').forEach(textarea => {
        autoResizeTextarea(textarea);
        textarea.addEventListener('input', function () {
            autoResizeTextarea(this);
        });
    });
});

// Guardado del formulario vía Fetch
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form3212');
    const formData = new FormData(form);
    
    // Capturar checkboxes no marcados (por defecto FormData los ignora)
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => {
        if (!cb.checked) {
            formData.append(cb.name, '0');
        }
    });

    // Construir el objeto JSON
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
                text: 'La investigación ha sido guardada correctamente.',
                icon: 'success',
                confirmButtonColor: '#198754'
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