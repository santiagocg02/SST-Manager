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

$filas = 12;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.4-2 - Matriz Seguimiento Exámenes Médicos</title>
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
        .btn-add{ background:#213b67; color:#fff; }

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

        .save-msg{
            margin:0 0 15px 0;
            padding:10px 14px;
            border-radius:8px;
            background:#e9f7ef;
            color:#166534;
            border:1px solid #b7e4c7;
            font-size:14px;
            font-weight:700;
        }

        .topbar{
            margin:16px 0 10px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
        }

        .tabla-wrap{
            overflow:auto;
            border:1px solid #6b6b6b;
            margin-top:8px;
        }

        .matriz{
            min-width:1900px;
            font-size:12px;
        }

        .matriz th,
        .matriz td{
            border:1px solid #6b6b6b;
            padding:4px;
            vertical-align:top;
        }

        .matriz thead th{
            background:#f4f6fa;
            text-align:center;
            position:sticky;
            top:0;
            z-index:2;
        }

        .matriz input[type="text"],
        .matriz input[type="date"],
        .matriz textarea,
        .matriz select{
            width:100%;
            border:none;
            outline:none;
            padding:4px;
            font-size:12px;
            background:transparent;
        }

        .matriz textarea{
            resize:none;
            overflow:hidden;
            min-height:56px;
            line-height:1.35;
            white-space:pre-wrap;
            word-break:break-word;
        }

        .matriz .tiny{
            width:42px;
            min-width:42px;
            text-align:center;
        }

        .matriz .w-num{ min-width:50px; width:50px; text-align:center; }
        .matriz .w-fecha{ min-width:120px; width:120px; }
        .matriz .w-tipo{ min-width:130px; width:130px; }
        .matriz .w-id{ min-width:140px; width:140px; }
        .matriz .w-nombre{ min-width:220px; width:220px; }
        .matriz .w-check{ min-width:40px; width:40px; text-align:center; vertical-align:middle; }
        .matriz .w-texto{ min-width:200px; width:200px; }
        .matriz .w-texto-l{ min-width:240px; width:240px; }

        .check-cell{
            text-align:center;
            vertical-align:middle !important;
        }

        .check-cell input{
            transform:scale(1.08);
            cursor:pointer;
        }

        .ley{
            margin-top:16px;
            padding:12px;
            border:1px solid #d9d9d9;
            background:#fafafa;
            font-size:12px;
            line-height:1.45;
            text-align:justify;
        }

        @media print{
            body{
                background:#fff;
                padding:0;
            }

            .toolbar,
            .topbar{
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

            .matriz{
                min-width:unset;
                width:100%;
                font-size:10px;
            }

            .matriz input,
            .matriz textarea,
            .matriz select{
                border:none !important;
                padding:2px !important;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar">
        <h1>3.1.4-2 - Matriz Seguimiento Exámenes Médicos</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form3142">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form3142" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">MATRIZ SEGUIMIENTO EXÁMENES MÉDICOS</td>
                    <td style="font-weight:700;">AN-SST-30<br>XX/XX/2025</td>
                </tr>
            </table>

            <div class="topbar">
                <button type="button" class="btn btn-add" onclick="agregarFila()">Agregar fila</button>
            </div>

            <div class="tabla-wrap">
                <table class="matriz" id="tablaMatriz">
                    <thead>
                        <tr>
                            <th class="w-num">No.</th>
                            <th class="w-fecha">FECHA EXAMEN</th>
                            <th class="w-tipo">TIPO DE EXAMEN</th>
                            <th class="w-id">No. DE IDENTIFICACIÓN</th>
                            <th class="w-nombre">NOMBRES Y APELLIDOS</th>
                            <th class="w-check">I</th>
                            <th class="w-check">P</th>
                            <th class="w-check">E</th>
                            <th class="w-texto">RECOMENDACIONES PERSONALES</th>
                            <th class="w-texto">RECOMENDACIONES SST</th>
                            <th class="w-texto">RECOMENDACIONES MÉDICAS</th>
                            <th class="w-texto">CARTA PVE</th>
                            <th class="w-texto">PLAN DE ACCIÓN</th>
                            <th class="w-texto-l">SEGUIMIENTO</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMatriz">
                        <?php for ($i = 1; $i <= $filas; $i++): ?>
                            <tr>
                                <td class="w-num">
                                    <input type="text" name="num_<?= $i ?>" value="<?= oldv("num_$i", (string)$i) ?>" class="tiny">
                                </td>
                                <td class="w-fecha">
                                    <input type="date" name="fecha_<?= $i ?>" value="<?= oldv("fecha_$i") ?>">
                                </td>
                                <td class="w-tipo">
                                    <select name="tipo_<?= $i ?>">
                                        <?php
                                        $tipo = oldv("tipo_$i");
                                        $tipos = ['','Ingreso','Periódico','Egreso','Cambio de ocupación','Reintegro','Post incapacidad'];
                                        foreach ($tipos as $op):
                                        ?>
                                            <option value="<?= htmlspecialchars($op, ENT_QUOTES, 'UTF-8') ?>" <?= $tipo === $op ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($op ?: 'Seleccione', ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="w-id">
                                    <input type="text" name="id_<?= $i ?>" value="<?= oldv("id_$i") ?>">
                                </td>
                                <td class="w-nombre">
                                    <input type="text" name="nombre_<?= $i ?>" value="<?= oldv("nombre_$i") ?>">
                                </td>
                                <td class="check-cell w-check">
                                    <input type="checkbox" name="i_<?= $i ?>" value="1" <?= isset($_POST["i_$i"]) ? 'checked' : '' ?>>
                                </td>
                                <td class="check-cell w-check">
                                    <input type="checkbox" name="p_<?= $i ?>" value="1" <?= isset($_POST["p_$i"]) ? 'checked' : '' ?>>
                                </td>
                                <td class="check-cell w-check">
                                    <input type="checkbox" name="e_<?= $i ?>" value="1" <?= isset($_POST["e_$i"]) ? 'checked' : '' ?>>
                                </td>
                                <td class="w-texto">
                                    <textarea name="rec_personal_<?= $i ?>"><?= oldv("rec_personal_$i") ?></textarea>
                                </td>
                                <td class="w-texto">
                                    <textarea name="rec_sst_<?= $i ?>"><?= oldv("rec_sst_$i") ?></textarea>
                                </td>
                                <td class="w-texto">
                                    <textarea name="rec_med_<?= $i ?>"><?= oldv("rec_med_$i") ?></textarea>
                                </td>
                                <td class="w-texto">
                                    <textarea name="carta_pve_<?= $i ?>"><?= oldv("carta_pve_$i") ?></textarea>
                                </td>
                                <td class="w-texto">
                                    <textarea name="plan_<?= $i ?>"><?= oldv("plan_$i") ?></textarea>
                                </td>
                                <td class="w-texto-l">
                                    <textarea name="seguimiento_<?= $i ?>"><?= oldv("seguimiento_$i") ?></textarea>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="ley">
                En cumplimiento de lo previsto por la Ley N° 1581 de 2012 y su Decreto Reglamentario N° 1377 de 2013, los cuales tienen por objeto desarrollar el derecho constitucional que tienen todas las personas a conocer, actualizar y rectificar las informaciones que se hayan recogido sobre ellas en bases de datos o archivos, se informa que la empresa respeta la confidencialidad y seguridad de la información y que los datos personales suministrados al sistema serán administrados por la empresa, garantizando su tratamiento conforme a las disposiciones que regulan la protección de datos personales. :contentReference[oaicite:1]{index=1}
            </div>
        </form>
    </div>
</div>

<script>
let contadorFilas = <?= (int)$filas ?>;

function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = (el.scrollHeight) + 'px';
}

function activarAutoResize(scope = document) {
    scope.querySelectorAll('textarea').forEach(textarea => {
        autoResizeTextarea(textarea);
        textarea.addEventListener('input', function () {
            autoResizeTextarea(this);
        });
    });
}

function agregarFila() {
    contadorFilas++;
    const tbody = document.getElementById('tbodyMatriz');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td class="w-num">
            <input type="text" name="num_${contadorFilas}" value="${contadorFilas}" class="tiny">
        </td>
        <td class="w-fecha">
            <input type="date" name="fecha_${contadorFilas}">
        </td>
        <td class="w-tipo">
            <select name="tipo_${contadorFilas}">
                <option value="">Seleccione</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Periódico">Periódico</option>
                <option value="Egreso">Egreso</option>
                <option value="Cambio de ocupación">Cambio de ocupación</option>
                <option value="Reintegro">Reintegro</option>
                <option value="Post incapacidad">Post incapacidad</option>
            </select>
        </td>
        <td class="w-id">
            <input type="text" name="id_${contadorFilas}">
        </td>
        <td class="w-nombre">
            <input type="text" name="nombre_${contadorFilas}">
        </td>
        <td class="check-cell w-check">
            <input type="checkbox" name="i_${contadorFilas}" value="1">
        </td>
        <td class="check-cell w-check">
            <input type="checkbox" name="p_${contadorFilas}" value="1">
        </td>
        <td class="check-cell w-check">
            <input type="checkbox" name="e_${contadorFilas}" value="1">
        </td>
        <td class="w-texto">
            <textarea name="rec_personal_${contadorFilas}"></textarea>
        </td>
        <td class="w-texto">
            <textarea name="rec_sst_${contadorFilas}"></textarea>
        </td>
        <td class="w-texto">
            <textarea name="rec_med_${contadorFilas}"></textarea>
        </td>
        <td class="w-texto">
            <textarea name="carta_pve_${contadorFilas}"></textarea>
        </td>
        <td class="w-texto">
            <textarea name="plan_${contadorFilas}"></textarea>
        </td>
        <td class="w-texto-l">
            <textarea name="seguimiento_${contadorFilas}"></textarea>
        </td>
    `;

    tbody.appendChild(tr);
    activarAutoResize(tr);
}

document.addEventListener('DOMContentLoaded', function () {
    activarAutoResize();
});
</script>

</body>
</html>