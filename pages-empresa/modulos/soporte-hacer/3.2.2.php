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

$filasIniciales = 1;

$meses = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
$diasSemana = ['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'];
$rangosHora = [
    'ENTRE LAS 0 Y 3:00',
    'DE LAS 3:00 A LAS 6:00',
    'DE LAS 6:00 A LAS 9:00',
    'DE LAS 9:00 A LAS 12:00',
    'DE LAS 12:00 A LAS 15:00',
    'DE LAS 15:00 A LAS 18:00',
    'DE LAS 18:00 A LAS 21:00',
    'DE LAS 21:00 A LAS 24:00'
];
$vehiculos = ['Patineta eléctrica','Bicicleta','Motocicleta','Motocarro','Automóvil','Camioneta','Bus','Buseta','Camión','Volqueta','Tractocamión','Otro'];
$partes = [
    'CABEZA','OJO','CUELLO','TRONCO (Incluye espalda, columna vertebral, médula espinal, pelvis)',
    'TÓRAX','ABDOMEN','MIEMBROS SUPERIORES','MANOS','MIEMBROS INFERIORES','PIES',
    'LESIONES MÚLTIPLES','LESIONES GENERALES U OTRAS'
];
$lesiones = [
    'FRACTURA','LUXACIÓN','TORCEDURA, ESGUINCE, DESGARRO MUSCULAR, HERNIA O LACERACIÓN DE MÚSCULO O TENDÓN SIN HERIDA',
    'CONMOCIÓN O TRAUMA INTERNO','AMPUTACIÓN O ENUCLEACIÓN (Exclusión o pérdida del ojo)','HERIDA',
    'TRAUMA SUPERFICIAL (Incluye rasguño, punzón o pinchazo y lesión en ojo por cuerpo extraño)',
    'GOLPE O CONTUSIÓN O APLASTAMIENTO','QUEMADURA','ENVENENAMIENTO O INTOXICACIÓN AGUDA O ALERGIA',
    'EFECTO DEL TIEMPO, DEL CLIMA U OTRO RELACIONADO CON EL AMBIENTE','ASFIXIA',
    'EFECTO NOCIVO DE LA RADIACIÓN','LESIONES MÚLTIPLES'
];
$mecanismos = [
    'CAÍDA DE PERSONAS','CAÍDA DE OBJETOS','PISADAS, CHOQUES O GOLPES','ATRAPAMIENTOS',
    'SOBRE ESFUERZO, ESFUERZO EXCESIVO O FALSO MOVIMIENTO',
    'EXPOSICIÓN O CONTACTO CON TEMPERATURA EXTREMA',
    'EXPOSICIÓN O CONTACTO CON ELECTRICIDAD',
    'EXPOSICIÓN O CONTACTO CON SUSTANCIAS NOCIVAS O RADIACIONES O SALPICADURAS',
    'OTRO'
];
$agentes = [
    'MAQUINAS Y/O EQUIPOS','MEDIOS DE TRANSPORTE','APARATOS','HERRAMIENTAS, IMPLEMENTOS O UTENSILIOS',
    'MATERIALES O SUSTANCIAS','RADIACIONES',
    'AMBIENTE DE TRABAJO (Incluye superficies de tránsito y de trabajo, muebles, tejados, en el exterior, interior o subterráneos.)',
    'ANIMALES (Vivos o productos animales)','OTROS AGENTES NO CLASIFICADOS','AGENTES NO CLASIFICADOR POR FALTA DE DATOS'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.2.2 - Caracterización de la Accidentalidad</title>
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
        .tabla-principal td,.tabla-principal th,
        .tabla-resumen td,.tabla-resumen th{
            border:1px solid #6b6b6b;
            padding:6px;
            vertical-align:top;
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

        .topbar{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
            margin:16px 0 10px;
        }

        .tabla-wrap{
            overflow:auto;
            border:1px solid #6b6b6b;
            margin-top:6px;
        }

        .tabla-principal{
            min-width:2750px;
            font-size:11px;
            table-layout:fixed;
        }

        .tabla-principal thead th{
            background:#bcd4f6;
            text-align:center;
            position:sticky;
            top:0;
            z-index:2;
            vertical-align:middle;
            padding:8px 6px;
            line-height:1.2;
            word-break:break-word;
        }

        .tabla-principal td{
            padding:4px;
            vertical-align:middle;
        }

        .tabla-principal input,
        .tabla-principal select,
        .tabla-principal textarea{
            width:100%;
            border:1px solid transparent;
            outline:none;
            background:#fff;
            padding:6px 7px;
            font-size:11px;
            line-height:1.25;
            border-radius:4px;
            min-height:34px;
        }

        .tabla-principal input:focus,
        .tabla-principal select:focus,
        .tabla-principal textarea:focus{
            border-color:#8fb2ea;
            box-shadow:0 0 0 2px rgba(33, 59, 103, .10);
        }

        .tabla-principal select{
            white-space:normal;
        }

        .tabla-principal textarea{
            resize:none;
            overflow:hidden;
            min-height:52px;
            white-space:pre-wrap;
            word-break:break-word;
        }

        .center{text-align:center;vertical-align:middle !important;}

        .check-cell{
            text-align:center;
            vertical-align:middle !important;
        }

        .check-cell input{
            width:18px !important;
            height:18px !important;
            min-height:auto !important;
            padding:0 !important;
            margin:0 auto;
            display:block;
            border:none !important;
            box-shadow:none !important;
        }

        .w-60{width:60px;min-width:60px;}
        .w-70{width:70px;min-width:70px;}
        .w-80{width:80px;min-width:80px;}
        .w-90{width:90px;min-width:90px;}
        .w-100{width:100px;min-width:100px;}
        .w-110{width:110px;min-width:110px;}
        .w-120{width:120px;min-width:120px;}
        .w-130{width:130px;min-width:130px;}
        .w-140{width:140px;min-width:140px;}
        .w-150{width:150px;min-width:150px;}
        .w-160{width:160px;min-width:160px;}
        .w-180{width:180px;min-width:180px;}
        .w-200{width:200px;min-width:200px;}
        .w-220{width:220px;min-width:220px;}
        .w-240{width:240px;min-width:240px;}
        .w-260{width:260px;min-width:260px;}

        .muted{
            color:#666;
            font-size:12px;
            margin-top:10px;
        }

        .grid-resumen{
            display:grid;
            grid-template-columns:1fr 1fr;
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
            background:#d6eef8;
            text-align:center;
        }

        .tabla-resumen td{
            padding:5px 7px;
        }

        .tabla-resumen tfoot td,
        .tabla-resumen tfoot th{
            font-weight:700;
            background:#f4f6fa;
        }

        .analisis-box{
            border:1px solid #6b6b6b;
            border-top:none;
            min-height:72px;
            padding:10px;
            font-size:12px;
            line-height:1.45;
            background:#fff;
        }

        @media (max-width: 1100px){
            .grid-resumen{grid-template-columns:1fr;}
        }

        @media print{
            body{background:#fff;padding:0;}
            .toolbar,.topbar{display:none;}
            .contenedor{box-shadow:none;border:none;}
            .formulario{padding:8px;}
            .tabla-wrap{overflow:visible;border:none;}
            .tabla-principal{min-width:unset;width:100%;font-size:9px;}
            input,select,textarea{
                border:none !important;
                box-shadow:none !important;
                padding-left:0 !important;
                padding-right:0 !important;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar">
        <h1>3.2.2 - Caracterización de la Accidentalidad</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form322">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form322" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">CARACTERIZACIÓN DE LA ACCIDENTALIDAD</td>
                    <td style="font-weight:700;">AN-SST-32<br>XX/XX/2025</td>
                </tr>
            </table>

            <div class="topbar">
                <button type="button" class="btn btn-add" onclick="agregarFila()">Agregar fila</button>
            </div>

            <div class="tabla-wrap">
                <table class="tabla-principal" id="tablaPrincipal">
                    <thead>
                        <tr>
                            <th class="w-120">NOMBRE</th>
                            <th class="w-70">DÍA</th>
                            <th class="w-100">MES</th>
                            <th class="w-80">AÑO</th>
                            <th class="w-140">HORA (0-23)</th>
                            <th class="w-120">EVENTO</th>
                            <th class="w-120">DÍA DE LA SEMANA</th>
                            <th class="w-120">RUTA</th>
                            <th class="w-140">TIPO DE VEHÍCULO</th>
                            <th class="w-120">CARGO</th>
                            <th class="w-120">PROPIO / TERCERO</th>
                            <th class="w-140">TIEMPO TRANSCURRIDO</th>
                            <th class="w-200">MECANISMO</th>
                            <th class="w-220">PARTE DEL CUERPO AFECTADA</th>
                            <th class="w-260">TIPO DE LESIÓN</th>
                            <th class="w-220">AGENTE</th>
                            <th class="w-90">SIN INCAP.</th>
                            <th class="w-90">CON INCAP.</th>
                            <th class="w-100">No. DÍAS INCAP.</th>
                            <th class="w-90">MORTAL</th>
                            <th class="w-90">No. VÍCTIMAS</th>
                            <th class="w-100">AFECTA A TERCEROS</th>
                            <th class="w-120">COSTOS ESTIMADOS</th>
                            <th class="w-240">ACCIONES REALIZADAS DESPUÉS DEL EVENTO</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPrincipal">
                        <?php for ($i = 1; $i <= $filasIniciales; $i++): ?>
                        <tr>
                            <td><input type="text" name="nombre_<?= $i ?>" value="<?= oldv("nombre_$i") ?>"></td>
                            <td><input type="number" name="dia_<?= $i ?>" value="<?= oldv("dia_$i") ?>"></td>
                            <td>
                                <select name="mes_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($meses as $m): ?>
                                        <option value="<?= $m ?>" <?= oldv("mes_$i") === $m ? 'selected' : '' ?>><?= $m ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="ano_<?= $i ?>" value="<?= oldv("ano_$i") ?>"></td>
                            <td>
                                <select name="hora_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($rangosHora as $h): ?>
                                        <option value="<?= $h ?>" <?= oldv("hora_$i") === $h ? 'selected' : '' ?>><?= $h ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="evento_<?= $i ?>" value="<?= oldv("evento_$i") ?>"></td>
                            <td>
                                <select name="dia_semana_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($diasSemana as $d): ?>
                                        <option value="<?= $d ?>" <?= oldv("dia_semana_$i") === $d ? 'selected' : '' ?>><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="ruta_<?= $i ?>" value="<?= oldv("ruta_$i") ?>"></td>
                            <td>
                                <select name="vehiculo_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($vehiculos as $v): ?>
                                        <option value="<?= $v ?>" <?= oldv("vehiculo_$i") === $v ? 'selected' : '' ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="cargo_<?= $i ?>" value="<?= oldv("cargo_$i") ?>"></td>
                            <td>
                                <select name="propio_tercero_<?= $i ?>">
                                    <option value=""></option>
                                    <option value="PROPIO" <?= oldv("propio_tercero_$i") === 'PROPIO' ? 'selected' : '' ?>>PROPIO</option>
                                    <option value="TERCERO" <?= oldv("propio_tercero_$i") === 'TERCERO' ? 'selected' : '' ?>>TERCERO</option>
                                </select>
                            </td>
                            <td><input type="text" name="tiempo_<?= $i ?>" value="<?= oldv("tiempo_$i") ?>"></td>
                            <td>
                                <select name="mecanismo_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($mecanismos as $x): ?>
                                        <option value="<?= $x ?>" <?= oldv("mecanismo_$i") === $x ? 'selected' : '' ?>><?= $x ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="parte_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($partes as $p): ?>
                                        <option value="<?= $p ?>" <?= oldv("parte_$i") === $p ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="lesion_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($lesiones as $l): ?>
                                        <option value="<?= $l ?>" <?= oldv("lesion_$i") === $l ? 'selected' : '' ?>><?= $l ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="agente_<?= $i ?>">
                                    <option value=""></option>
                                    <?php foreach ($agentes as $a): ?>
                                        <option value="<?= $a ?>" <?= oldv("agente_$i") === $a ? 'selected' : '' ?>><?= $a ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="center check-cell"><input type="checkbox" name="sin_incap_<?= $i ?>" value="1" <?= isset($_POST["sin_incap_$i"]) ? 'checked' : '' ?>></td>
                            <td class="center check-cell"><input type="checkbox" name="con_incap_<?= $i ?>" value="1" <?= isset($_POST["con_incap_$i"]) ? 'checked' : '' ?>></td>
                            <td><input type="number" name="dias_incap_<?= $i ?>" value="<?= oldv("dias_incap_$i") ?>"></td>
                            <td class="center check-cell"><input type="checkbox" name="mortal_<?= $i ?>" value="1" <?= isset($_POST["mortal_$i"]) ? 'checked' : '' ?>></td>
                            <td><input type="number" name="victimas_<?= $i ?>" value="<?= oldv("victimas_$i") ?>"></td>
                            <td class="center check-cell"><input type="checkbox" name="afecta_terceros_<?= $i ?>" value="1" <?= isset($_POST["afecta_terceros_$i"]) ? 'checked' : '' ?>></td>
                            <td><input type="text" name="costos_<?= $i ?>" value="<?= oldv("costos_$i") ?>"></td>
                            <td><textarea name="acciones_<?= $i ?>"><?= oldv("acciones_$i") ?></textarea></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="muted">Las tablas de abajo se calculan automáticamente con base en los datos ingresados en la tabla superior. :contentReference[oaicite:1]{index=1}</div>

            <div class="grid-resumen">

                <div class="bloque">
                    <h3>ACCIDENTALIDAD POR EMPRESA O CONTRATISTA</h3>
                    <table class="tabla-resumen" id="tblEmpresa">
                        <thead><tr><th>A.T. POR EMPRESA / CONTRATISTA</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="empresa_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="empresa_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>DÍAS DE INCAPACIDAD</h3>
                    <table class="tabla-resumen" id="tblDias">
                        <thead><tr><th>TIPO</th><th>DÍAS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="dias_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="dias_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>SINIESTRALIDAD VIAL</h3>
                    <table class="tabla-resumen" id="tblSiniestralidad">
                        <thead><tr><th>CATEGORÍA</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="sin_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="sin_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>TIPO DE VEHÍCULO</h3>
                    <table class="tabla-resumen" id="tblVehiculo">
                        <thead><tr><th>TIPO DE VEHÍCULO</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="veh_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="veh_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>ACCIDENTES POR MES</h3>
                    <table class="tabla-resumen" id="tblMeses">
                        <thead><tr><th>MES</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="mes_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="mes_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>PARTE DEL CUERPO AFECTADA</h3>
                    <table class="tabla-resumen" id="tblParte">
                        <thead><tr><th>PARTE DEL CUERPO AFECTADA</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="parte_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="parte_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>DÍA DEL ACCIDENTE</h3>
                    <table class="tabla-resumen" id="tblDiaSemana">
                        <thead><tr><th>DÍA</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="diasem_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="diasem_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>HORA DEL ACCIDENTE</h3>
                    <table class="tabla-resumen" id="tblHora">
                        <thead><tr><th>HORA DEL ACCIDENTE</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="hora_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="hora_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>LESIÓN O DAÑO APARENTE</h3>
                    <table class="tabla-resumen" id="tblLesion">
                        <thead><tr><th>LESIÓN O DAÑO APARENTE</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="lesion_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="lesion_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>MECANISMO O FORMA DEL ACCIDENTE</h3>
                    <table class="tabla-resumen" id="tblMecanismo">
                        <thead><tr><th>MECANISMO</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="mec_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="mec_analisis"></div>
                </div>

                <div class="bloque">
                    <h3>AGENTE DEL ACCIDENTE</h3>
                    <table class="tabla-resumen" id="tblAgente">
                        <thead><tr><th>AGENTE</th><th>No. CASOS</th><th>%</th></tr></thead>
                        <tbody></tbody>
                        <tfoot><tr><th>TOTAL</th><th id="ag_total">0</th><th>100%</th></tr></tfoot>
                    </table>
                    <div class="analisis-box" id="ag_analisis"></div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
let contadorFilas = <?= (int)$filasIniciales ?>;

const MESES = <?= json_encode($meses, JSON_UNESCAPED_UNICODE) ?>;
const DIAS = <?= json_encode($diasSemana, JSON_UNESCAPED_UNICODE) ?>;
const HORAS = <?= json_encode($rangosHora, JSON_UNESCAPED_UNICODE) ?>;
const VEHICULOS = <?= json_encode($vehiculos, JSON_UNESCAPED_UNICODE) ?>;
const PARTES = <?= json_encode($partes, JSON_UNESCAPED_UNICODE) ?>;
const LESIONES = <?= json_encode($lesiones, JSON_UNESCAPED_UNICODE) ?>;
const MECANISMOS = <?= json_encode($mecanismos, JSON_UNESCAPED_UNICODE) ?>;
const AGENTES = <?= json_encode($agentes, JSON_UNESCAPED_UNICODE) ?>;

function pct(v, total){
    if (!total) return '0,0%';
    return ((v / total) * 100).toFixed(1).replace('.', ',') + '%';
}

function autoResizeTextarea(el){
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

function activarAutoResize(scope=document){
    scope.querySelectorAll('textarea').forEach(t => {
        autoResizeTextarea(t);
        t.addEventListener('input', function(){ autoResizeTextarea(this); });
    });
}

function getRows(){
    return Array.from(document.querySelectorAll('#tbodyPrincipal tr')).map(tr => {
        const q = (sel) => tr.querySelector(sel);
        return {
            nombre: q('[name^="nombre_"]')?.value?.trim() || '',
            dia: q('[name^="dia_"]')?.value?.trim() || '',
            mes: q('[name^="mes_"]')?.value || '',
            ano: q('[name^="ano_"]')?.value?.trim() || '',
            hora: q('[name^="hora_"]')?.value || '',
            evento: q('[name^="evento_"]')?.value?.trim() || '',
            diaSemana: q('[name^="dia_semana_"]')?.value || '',
            ruta: q('[name^="ruta_"]')?.value?.trim() || '',
            vehiculo: q('[name^="vehiculo_"]')?.value || '',
            cargo: q('[name^="cargo_"]')?.value?.trim() || '',
            propioTercero: q('[name^="propio_tercero_"]')?.value || '',
            tiempo: q('[name^="tiempo_"]')?.value?.trim() || '',
            mecanismo: q('[name^="mecanismo_"]')?.value || '',
            parte: q('[name^="parte_"]')?.value || '',
            lesion: q('[name^="lesion_"]')?.value || '',
            agente: q('[name^="agente_"]')?.value || '',
            sinIncap: q('[name^="sin_incap_"]')?.checked || false,
            conIncap: q('[name^="con_incap_"]')?.checked || false,
            diasIncap: parseInt(q('[name^="dias_incap_"]')?.value || 0, 10) || 0,
            mortal: q('[name^="mortal_"]')?.checked || false,
            victimas: parseInt(q('[name^="victimas_"]')?.value || 0, 10) || 0,
            afectaTerceros: q('[name^="afecta_terceros_"]')?.checked || false,
            costos: q('[name^="costos_"]')?.value?.trim() || '',
            acciones: q('[name^="acciones_"]')?.value?.trim() || ''
        };
    }).filter(r => {
        return r.nombre || r.dia || r.mes || r.ano || r.hora || r.evento || r.vehiculo || r.propioTercero || r.mecanismo || r.parte || r.lesion || r.agente || r.sinIncap || r.conIncap || r.diasIncap || r.mortal || r.victimas || r.afectaTerceros;
    });
}

function countBy(rows, key, categories){
    const out = {};
    categories.forEach(c => out[c] = 0);
    rows.forEach(r => {
        const val = r[key];
        if (categories.includes(val)) out[val]++;
    });
    return out;
}

function renderTable(tableId, categories, counts, totalId, analysisId, label){
    const tbody = document.querySelector(`#${tableId} tbody`);
    tbody.innerHTML = '';
    const total = categories.reduce((acc, c) => acc + (counts[c] || 0), 0);
    document.getElementById(totalId).textContent = total;

    let maxCat = '';
    let maxVal = 0;

    categories.forEach(cat => {
        const val = counts[cat] || 0;
        if (val > maxVal) {
            maxVal = val;
            maxCat = cat;
        }
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${cat}</td><td class="center">${val}</td><td class="center">${pct(val, total)}</td>`;
        tbody.appendChild(tr);
    });

    document.getElementById(analysisId).textContent = total === 0
        ? `Análisis: No hay datos registrados para ${label.toLowerCase()}.`
        : `Análisis: Se registran ${total} caso(s). La mayor frecuencia corresponde a "${maxCat}" con ${maxVal} caso(s), equivalente a ${pct(maxVal, total)}.`;
}

function renderEmpresa(rows){
    const counts = { 'PROPIO': 0, 'TERCERO': 0 };
    rows.forEach(r => {
        if (r.propioTercero === 'PROPIO' || r.propioTercero === 'TERCERO') counts[r.propioTercero]++;
    });
    renderTable('tblEmpresa', ['PROPIO','TERCERO'], counts, 'empresa_total', 'empresa_analisis', 'Accidentalidad por empresa o contratista');
}

function renderDias(rows){
    const sin = rows.filter(r => r.sinIncap).length;
    const conDias = rows.reduce((acc, r) => acc + (r.diasIncap || 0), 0);
    const tbody = document.querySelector('#tblDias tbody');
    tbody.innerHTML = '';
    const total = sin + conDias;
    document.getElementById('dias_total').textContent = total;

    [
        ['ACCIDENTE SIN INCAPACIDAD', sin],
        ['DÍAS DE INCAPACIDAD', conDias]
    ].forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${item[0]}</td><td class="center">${item[1]}</td><td class="center">${pct(item[1], total)}</td>`;
        tbody.appendChild(tr);
    });

    document.getElementById('dias_analisis').textContent = total === 0
        ? 'Análisis: No hay días de incapacidad registrados.'
        : `Análisis: Se acumulan ${conDias} día(s) de incapacidad y ${sin} evento(s) sin incapacidad.`;
}

function renderSiniestralidad(rows){
    const sin = rows.filter(r => r.sinIncap).length;
    const con = rows.filter(r => r.conIncap).length;
    const tbody = document.querySelector('#tblSiniestralidad tbody');
    tbody.innerHTML = '';
    const total = sin + con;
    document.getElementById('sin_total').textContent = total;

    [
        ['SINIESTRO VIAL SIN INCAPACIDAD', sin],
        ['SINIESTRO VIAL CON DÍAS DE INCAPACIDAD', con]
    ].forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${item[0]}</td><td class="center">${item[1]}</td><td class="center">${pct(item[1], total)}</td>`;
        tbody.appendChild(tr);
    });

    document.getElementById('sin_analisis').textContent = total === 0
        ? 'Análisis: No hay siniestralidad vial registrada.'
        : `Análisis: Del total de eventos, ${con} presentan incapacidad y ${sin} no presentan incapacidad.`;
}

function recalc(){
    const rows = getRows();

    renderEmpresa(rows);
    renderDias(rows);
    renderSiniestralidad(rows);
    renderTable('tblVehiculo', VEHICULOS, countBy(rows, 'vehiculo', VEHICULOS), 'veh_total', 'veh_analisis', 'Tipo de vehículo');
    renderTable('tblMeses', MESES, countBy(rows, 'mes', MESES), 'mes_total', 'mes_analisis', 'Accidentes por mes');
    renderTable('tblParte', PARTES, countBy(rows, 'parte', PARTES), 'parte_total', 'parte_analisis', 'Parte del cuerpo afectada');
    renderTable('tblDiaSemana', DIAS, countBy(rows, 'diaSemana', DIAS), 'diasem_total', 'diasem_analisis', 'Día del accidente');
    renderTable('tblHora', HORAS, countBy(rows, 'hora', HORAS), 'hora_total', 'hora_analisis', 'Hora del accidente');
    renderTable('tblLesion', LESIONES, countBy(rows, 'lesion', LESIONES), 'lesion_total', 'lesion_analisis', 'Lesión o daño aparente');
    renderTable('tblMecanismo', MECANISMOS, countBy(rows, 'mecanismo', MECANISMOS), 'mec_total', 'mec_analisis', 'Mecanismo o forma del accidente');
    renderTable('tblAgente', AGENTES, countBy(rows, 'agente', AGENTES), 'ag_total', 'ag_analisis', 'Agente del accidente');
}

function createOptions(list){
    return '<option value=""></option>' + list.map(v => `<option value="${v}">${v}</option>`).join('');
}

function agregarFila(){
    contadorFilas++;
    const tbody = document.getElementById('tbodyPrincipal');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td><input type="text" name="nombre_${contadorFilas}"></td>
        <td><input type="number" name="dia_${contadorFilas}"></td>
        <td><select name="mes_${contadorFilas}">${createOptions(MESES)}</select></td>
        <td><input type="number" name="ano_${contadorFilas}"></td>
        <td><select name="hora_${contadorFilas}">${createOptions(HORAS)}</select></td>
        <td><input type="text" name="evento_${contadorFilas}"></td>
        <td><select name="dia_semana_${contadorFilas}">${createOptions(DIAS)}</select></td>
        <td><input type="text" name="ruta_${contadorFilas}"></td>
        <td><select name="vehiculo_${contadorFilas}">${createOptions(VEHICULOS)}</select></td>
        <td><input type="text" name="cargo_${contadorFilas}"></td>
        <td>
            <select name="propio_tercero_${contadorFilas}">
                <option value=""></option>
                <option value="PROPIO">PROPIO</option>
                <option value="TERCERO">TERCERO</option>
            </select>
        </td>
        <td><input type="text" name="tiempo_${contadorFilas}"></td>
        <td><select name="mecanismo_${contadorFilas}">${createOptions(MECANISMOS)}</select></td>
        <td><select name="parte_${contadorFilas}">${createOptions(PARTES)}</select></td>
        <td><select name="lesion_${contadorFilas}">${createOptions(LESIONES)}</select></td>
        <td><select name="agente_${contadorFilas}">${createOptions(AGENTES)}</select></td>
        <td class="center check-cell"><input type="checkbox" name="sin_incap_${contadorFilas}" value="1"></td>
        <td class="center check-cell"><input type="checkbox" name="con_incap_${contadorFilas}" value="1"></td>
        <td><input type="number" name="dias_incap_${contadorFilas}"></td>
        <td class="center check-cell"><input type="checkbox" name="mortal_${contadorFilas}" value="1"></td>
        <td><input type="number" name="victimas_${contadorFilas}"></td>
        <td class="center check-cell"><input type="checkbox" name="afecta_terceros_${contadorFilas}" value="1"></td>
        <td><input type="text" name="costos_${contadorFilas}"></td>
        <td><textarea name="acciones_${contadorFilas}"></textarea></td>
    `;

    tbody.appendChild(tr);
    activarAutoResize(tr);
    recalc();
}

document.addEventListener('input', function(e){
    if (e.target.closest('#tablaPrincipal')) recalc();
});

document.addEventListener('change', function(e){
    if (e.target.closest('#tablaPrincipal')) recalc();
});

document.addEventListener('DOMContentLoaded', function(){
    activarAutoResize();
    recalc();
});
</script>

</body>
</html>