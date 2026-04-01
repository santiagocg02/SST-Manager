<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../index.php');
    exit;
}

function old($key, $default = '')
{
    return isset($_POST[$key]) ? htmlspecialchars((string)$_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$filasDefault = [
    [
        'cargo' => 'Cargo de Gerente',
        'riesgo' => "• Biomecánico (posturas inadecuadas, manejo de computador)\n• Iluminación artificial\n• Psicosocial (organización, contenido, complejidad y responsabilidad del cargo)\n• Ruido proveniente del ambiente de trabajo en las diferentes áreas",
        'pve' => "1. Cardiovascular\n2. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','electro','hemat','lipid','glice','psico']
    ],
    [
        'cargo' => 'Cargo de Operaciones',
        'riesgo' => "• Biomecánico (posturas inadecuadas, manejo de computador)\n• Iluminación artificial\n• Psicosocial (organización, contenido, complejidad y responsabilidad del cargo)\n• Ruido proveniente del ambiente de trabajo en las diferentes áreas",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','hemat','glice']
    ],
    [
        'cargo' => 'Cargo de Conductor',
        'riesgo' => "• Físicos (ruido, radiaciones no ionizantes)\n• Psicosocial (trabajos bajo presión, condiciones de la tarea)\n• Biomecánicos (posturas en la conducción de vehículos livianos)\n• Condiciones de seguridad (accidentes de tránsito, orden público, atracos, asaltos, locativos, trabajo en alturas)\n• Biológicos (bacterias, virus, mordeduras, picaduras)",
        'pve' => "1. Cardiovascular\n2. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','espiro','alturas','electro','psico_crc','hemat','lipid','glice','alcohol','psico']
    ],
    [
        'cargo' => 'Cargo de Seguridad Física',
        'riesgo' => "• Físicos (ruido, radiaciones no ionizantes, iluminación artificial)\n• Psicosocial (trabajos bajo presión, condiciones de la tarea)\n• Biomecánicos (posturas en la conducción de vehículos livianos, uso de escritorio y video terminal)\n• Condiciones de seguridad (accidentes de tránsito, orden público, atracos, asaltos, locativos, trabajo en alturas)\n• Biológicos (bacterias, virus, mordeduras, picaduras)",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','alturas','psico_crc','hemat','glice','psico']
    ],
    [
        'cargo' => 'Cargo de Liquidaciones y Facturación',
        'riesgo' => "• Biomecánico (posturas inadecuadas, manejo de computador)\n• Iluminación artificial\n• Psicosocial (organización, contenido, complejidad y responsabilidad del cargo)\n• Ruido proveniente del ambiente de trabajo en las diferentes áreas",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','hemat','glice']
    ],
    [
        'cargo' => 'Cargo de Mantenimiento',
        'riesgo' => "• Físicos (ruido, radiaciones no ionizantes, iluminación artificial)\n• Psicosocial (trabajos bajo presión, condiciones de la tarea)\n• Biomecánicos (posturas en el uso de videoterminales, uso de escritorio)\n• Condiciones de seguridad (accidentes de tránsito, orden público, atracos, asaltos, locativos, trabajo en alturas)\n• Biológicos (bacterias, virus, mordeduras, picaduras)",
        'pve' => "1. Desórdenes musculoesqueléticos\n2. Ruido",
        'examenes' => ['fisico','opto','audio','alturas','hemat','glice','psico']
    ],
    [
        'cargo' => 'Cargo de Talento Humano',
        'riesgo' => "• Biomecánico (posturas inadecuadas, manejo de computador)\n• Iluminación artificial\n• Psicosocial (organización, contenido, complejidad y responsabilidad del cargo)\n• Ruido proveniente del ambiente de trabajo en las diferentes áreas",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','hemat','glice']
    ],
    [
        'cargo' => 'Cargo de Servicios Generales',
        'riesgo' => "• Biomecánico (posturas inadecuadas, manejo de computador)\n• Iluminación artificial\n• Psicosocial (organización, contenido, complejidad y responsabilidad del cargo)\n• Ruido proveniente del ambiente de trabajo en las diferentes áreas\n• Químico (vapores)",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','hemat','glice','tetano','frotis_unas','frotis_faringeo']
    ],
    [
        'cargo' => 'Cargo HSEQ',
        'riesgo' => "• Físicos (ruido, radiaciones no ionizantes)\n• Psicosocial (trabajos bajo presión, condiciones de la tarea)\n• Biomecánicos (posturas en la conducción de vehículos livianos, uso de escritorio y video terminal)\n• Condiciones de seguridad (accidentes de tránsito, orden público, atracos, asaltos, locativos, trabajo en alturas)\n• Biológicos (bacterias, virus, mordeduras, picaduras)",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','alturas','hemat','glice','psico']
    ],
    [
        'cargo' => 'Cargo Aprendiz Sena',
        'riesgo' => "• Físicos (ruido, radiaciones no ionizantes)\n• Psicosocial (trabajos bajo presión, condiciones de la tarea)\n• Biomecánicos (posturas en la conducción de vehículos livianos, uso de escritorio y video terminal)\n• Condiciones de seguridad (accidentes de tránsito, orden público, atracos, asaltos, locativos, trabajo en alturas)\n• Biológicos (bacterias, virus, mordeduras, picaduras)",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','hemat','glice','psico']
    ],
    [
        'cargo' => 'Director Contable',
        'riesgo' => "• Biomecánico (posturas inadecuadas, manejo de computador)\n• Iluminación artificial\n• Psicosocial (organización, contenido, complejidad y responsabilidad del cargo)\n• Ruido proveniente del ambiente de trabajo en las diferentes áreas",
        'pve' => "1. Desórdenes musculoesqueléticos",
        'examenes' => ['fisico','opto','audio','hemat','glice']
    ],
];

$examCols = [
    'fisico' => 'Físico con énfasis osteomuscular',
    'opto' => 'Optometría',
    'audio' => 'Audiometría',
    'espiro' => 'Espirometría',
    'alturas' => 'Certificación trabajo en alturas',
    'electro' => 'Electrocardiograma',
    'psico_crc' => 'Psicosensométrico CRC',
    'hemat' => 'Cuadro hemático',
    'lipid' => 'Perfil lipídico',
    'glice' => 'Glicemia',
    'alcohol' => 'Alcohol y drogas',
    'psico' => 'Psicotécnico',
    'fiebre' => 'Vacuna fiebre amarilla',
    'tetano' => 'Vacuna tétano',
    'frotis_unas' => 'Frotis de uñas',
    'frotis_faringeo' => 'Frotis faríngeo',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.3 - Profesiograma</title>
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
            max-width:1700px;
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
        .encabezado td, .encabezado th{
            border:1px solid #6b6b6b;
            padding:10px;
            text-align:center;
            vertical-align:middle;
        }
        .logo-box{
            width:140px;
            height:65px;
            border:2px dashed #c8c8c8;
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

        .alerta-ok{
            margin:14px 0;
            padding:10px 14px;
            border:1px solid #b7e4c7;
            background:#e9f7ef;
            color:#166534;
            border-radius:8px;
            font-size:14px;
            font-weight:700;
        }

        .tabla-wrap{
            margin-top:16px;
            overflow:auto;
            border:1px solid #6b6b6b;
        }

        .pro-table{
            min-width:1650px;
            font-size:12px;
        }

        .pro-table th,
        .pro-table td{
            border:1px solid #6b6b6b;
            padding:6px;
            vertical-align:top;
        }

        .pro-table thead th{
            text-align:center;
            background:#f4f6fa;
            position:sticky;
            top:0;
            z-index:2;
        }

        .col-cargo{ width:180px; }
        .col-riesgo{ width:360px; }
        .col-pve{ width:190px; }
        .col-exam{ width:70px; text-align:center; vertical-align:middle !important; }

        textarea{
    width:100%;
    min-height:110px;
    resize:none;
    overflow:hidden;
    border:none;
    outline:none;
    font-size:12px;
    line-height:1.4;
    white-space:pre-wrap;
    word-break:break-word;
}
        .cargo-input{
            min-height:60px;
        }

        .check-cell{
            text-align:center;
            vertical-align:middle !important;
        }

        .check-cell input{
            transform:scale(1.1);
            cursor:pointer;
        }

        .firmas{
            margin-top:18px;
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:18px;
        }

        .firma-box label{
            display:block;
            font-weight:700;
            margin-bottom:6px;
            font-size:14px;
        }

        .firma-box input,
        .firma-box textarea{
            width:100%;
            border:1px solid #9ea9b8;
            border-radius:6px;
            padding:10px;
            font-size:14px;
            min-height:auto;
        }

        .nota{
            margin-top:16px;
            padding:12px;
            border:1px solid #d9d9d9;
            background:#fafafa;
            font-size:13px;
            line-height:1.5;
            font-weight:700;
        }

        @media (max-width: 1100px){
            .firmas{
                grid-template-columns:1fr;
            }
        }

        @media print{
            body{
                background:#fff;
                padding:0;
            }
            .toolbar{
                display:none;
            }
            .contenedor{
                box-shadow:none;
                border:none;
            }
            .formulario{
                padding:8px;
            }
            .tabla-wrap{
                overflow:visible;
                border:none;
            }
            .pro-table{
                min-width:unset;
                width:100%;
                font-size:10px;
            }
            textarea,
            input{
                border:none !important;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar">
        <h1>3.1.3 - Profesiograma</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="formProfesiograma">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alerta-ok">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="formProfesiograma" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">PROFESIOGRAMA</td>
                    <td style="font-weight:700;">AN-SST-11<br>XX/XX/2025</td>
                </tr>
            </table>

            <div class="tabla-wrap">
                <table class="pro-table">
                    <thead>
                        <tr>
                            <th class="col-cargo">CARGO</th>
                            <th class="col-riesgo">FACTOR DE RIESGO DE EXPOSICIÓN ASOCIADO</th>
                            <th class="col-pve">PVE</th>
                            <?php foreach ($examCols as $label): ?>
                                <th class="col-exam"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filasDefault as $i => $fila): ?>
                            <tr>
                                <td class="col-cargo">
                                    <textarea class="cargo-input" name="cargo_<?= $i ?>"><?= old("cargo_$i", $fila['cargo']) ?></textarea>
                                </td>
                                <td class="col-riesgo">
                                    <textarea name="riesgo_<?= $i ?>"><?= old("riesgo_$i", $fila['riesgo']) ?></textarea>
                                </td>
                                <td class="col-pve">
                                    <textarea name="pve_<?= $i ?>"><?= old("pve_$i", $fila['pve']) ?></textarea>
                                </td>
                                <?php foreach ($examCols as $key => $label): ?>
                                    <?php
                                    $checkedDefault = in_array($key, $fila['examenes'], true);
                                    $checked = isset($_POST["chk_{$i}_{$key}"]) ? true : $checkedDefault;
                                    ?>
                                    <td class="check-cell col-exam">
                                        <input type="checkbox" name="chk_<?= $i ?>_<?= $key ?>" value="1" <?= $checked ? 'checked' : '' ?>>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="firmas">
                <div class="firma-box">
                    <label for="nombre_responsable">Nombre</label>
                    <input type="text" id="nombre_responsable" name="nombre_responsable" value="<?= old('nombre_responsable', '') ?>">
                </div>
                <div class="firma-box">
                    <label for="revision_hseq">Revisión HSEQ</label>
                    <input type="text" id="revision_hseq" name="revision_hseq" value="<?= old('revision_hseq', '') ?>">
                </div>
                <div class="firma-box">
                    <label for="licencia_so">Licencia en Salud Ocupacional</label>
                    <input type="text" id="licencia_so" name="licencia_so" value="<?= old('licencia_so', '') ?>">
                </div>
            </div>

            <div class="firmas" style="margin-top:12px;">
                <div class="firma-box">
                    <label for="firma_nombre">Firma Nombre</label>
                    <input type="text" id="firma_nombre" name="firma_nombre" value="<?= old('firma_nombre', '') ?>">
                </div>
                <div class="firma-box" style="grid-column: span 2;">
                    <label for="observaciones">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" style="min-height:70px; border:1px solid #9ea9b8; border-radius:6px; padding:10px;"><?= old('observaciones', '') ?></textarea>
                </div>
            </div>

            <div class="nota">
                NOTA: ESTE ES UN MODELO DE PROFESIOGRAMA, USTED DEBE ADECUAR UNO SEGÚN LOS PERFILES DE LOS TRABAJADORES Y ESTE DOCUMENTO DEBE SER VALIDADO Y FIRMADO POR UN MÉDICO ESPECIALISTA EN SALUD OCUPACIONAL.
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
    const textareas = document.querySelectorAll('textarea');

    textareas.forEach(textarea => {
        autoResizeTextarea(textarea);

        textarea.addEventListener('input', function () {
            autoResizeTextarea(this);
        });
    });
});
</script>

</body>
</html>