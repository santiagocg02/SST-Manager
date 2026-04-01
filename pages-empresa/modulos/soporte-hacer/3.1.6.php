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

$recomendacionesDefault = [
    'Requiere uso de corrección visual permanente obligatoria para laborar. (Uso de lentes con filtro UV)',
    'Controles por optometría',
    'Acondicionamiento físico nutricional',
    'Mantener ergonomía de columna',
    'Practicar hábitos de vida saludables',
    'Dieta baja en grasas y harinas',
    'Ejercicio cardiovascular',
    'Realizar pausas activas ocupacionales según cronograma de la empresa',
    'Hacer uso de EPP y dotación suministrados por la compañía'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.6 - Recomendaciones Médicas Laborales</title>
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
            max-width:1100px;
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

        .carta{
            margin-top:18px;
            padding:26px 32px;
            border:1px solid #d7dde8;
            background:#fff;
        }

        .fila-derecha{
            display:flex;
            justify-content:flex-end;
            margin-bottom:20px;
        }

        .input-inline{
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:4px 6px;
            font-size:15px;
        }

        .input-ciudad{ min-width:130px; text-align:center; }
        .input-fecha{ min-width:220px; text-align:center; }

        .bloque{
            margin-bottom:18px;
        }

        .bloque p{
            font-size:15px;
            line-height:1.7;
            margin-bottom:12px;
            text-align:justify;
        }

        .etiqueta{
            font-weight:700;
            display:block;
            margin-bottom:6px;
        }

        .input-linea{
            width:100%;
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:6px 4px;
            font-size:15px;
        }

        .referencia{
            display:grid;
            grid-template-columns:130px 1fr;
            gap:10px;
            align-items:start;
            margin:18px 0;
        }

        .referencia label{
            font-weight:700;
            padding-top:6px;
        }

        .textarea-ref,
        .textarea-parrafo{
            width:100%;
            border:1px solid #b7b7b7;
            border-radius:6px;
            outline:none;
            padding:10px;
            font-size:15px;
            line-height:1.6;
            resize:none;
            overflow:hidden;
            min-height:64px;
            white-space:pre-wrap;
            word-break:break-word;
        }

        .textarea-parrafo{
            min-height:120px;
        }

        .recomendaciones{
            margin:12px 0 18px 0;
        }

        .rec-item{
            display:grid;
            grid-template-columns:34px 1fr;
            gap:10px;
            align-items:start;
            margin-bottom:8px;
        }

        .rec-item span{
            font-size:18px;
            line-height:1.6;
        }

        .rec-item input{
            width:100%;
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:6px 4px;
            font-size:15px;
            line-height:1.5;
        }

        .firma{
            margin-top:34px;
            max-width:380px;
        }

        .linea-firma{
            border-top:1px solid #000;
            margin-bottom:10px;
            width:100%;
        }

        .firma input{
            width:100%;
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:6px 4px;
            font-size:15px;
            margin-bottom:8px;
        }

        .footer-info{
            margin-top:28px;
            text-align:center;
            font-size:13px;
            color:#444;
            border-top:2px solid #2d57ff;
            padding-top:12px;
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

            .carta{
                border:none;
                padding:10px 4px;
            }

            .input-inline,
            .input-linea,
            .rec-item input,
            .firma input,
            .textarea-ref,
            .textarea-parrafo{
                border:none !important;
                padding-left:0;
                padding-right:0;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar">
        <h1>3.1.6 - Recomendaciones Médicas Laborales</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form316">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form316" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">Versión: 2</td>
                </tr>
                <tr>
                    <td class="subtitulo">RECOMENDACIONES MÉDICAS LABORALES</td>
                    <td style="font-weight:700;">AN-PM-SST-01<br>Fecha: 10-06-2023</td>
                </tr>
            </table>

            <div class="carta">

                <div class="fila-derecha">
                    <div>
                        <input class="input-inline input-ciudad" type="text" name="ciudad" value="<?= oldv('ciudad', 'Ciudad') ?>">
                        ,
                        <input class="input-inline input-fecha" type="text" name="fecha_carta" value="<?= oldv('fecha_carta', '30 de Junio de 20XX') ?>">
                    </div>
                </div>

                <div class="bloque">
                    <label class="etiqueta">Señor:</label>
                    <input class="input-linea" type="text" name="senor" value="<?= oldv('senor', 'xxxxxxx') ?>">

                    <input class="input-linea" type="text" name="destinatario" value="<?= oldv('destinatario', 'xxxxxxxxxxxxxxxxx') ?>" style="margin-top:8px;">
                </div>

                <div class="referencia">
                    <label>Referencia:</label>
                    <textarea class="textarea-ref" name="referencia"><?= oldv('referencia', 'SEGUIMIENTO A RECOMENDACIONES MÉDICAS DE EXÁMENES MÉDICOS OCUPACIONALES') ?></textarea>
                </div>

                <div class="bloque">
                    <p>Reciba un cordial saludo,</p>

                    <textarea class="textarea-parrafo" name="parrafo_principal"><?= oldv('parrafo_principal', 'Teniendo en cuenta los exámenes médicos ocupacionales realizados por la IPS a solicitud de NOMBRE DE LA EMPRESA, el día 04 de Julio de 20XX, se generaron las siguientes recomendaciones por parte del médico ocupacional, las cuales usted debe atender y entregar el soporte médico al área de Recursos Humanos.') ?></textarea>

                    <p style="margin-top:14px;">
                        <input class="input-inline" type="text" name="plazo" value="<?= oldv('plazo', 'Estas recomendaciones se deben atender en un plazo máximo de 1 mes.') ?>" style="width:100%;">
                    </p>
                </div>

                <div class="recomendaciones">
                    <?php for ($i = 0; $i < 9; $i++): ?>
                        <div class="rec-item">
                            <span>•</span>
                            <input type="text" name="rec_<?= $i ?>" value="<?= oldv("rec_$i", $recomendacionesDefault[$i] ?? '') ?>">
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="bloque">
                    <p>Atentamente,</p>
                </div>

                <div class="firma">
                    <div class="linea-firma"></div>
                    <input type="text" name="firma_nombre" value="<?= oldv('firma_nombre', 'Nombre') ?>">
                    <input type="text" name="firma_cc" value="<?= oldv('firma_cc', 'CC.') ?>">
                    <input type="text" name="firma_cargo" value="<?= oldv('firma_cargo', 'Representante Legal') ?>">
                    <input type="text" name="firma_fecha" value="<?= oldv('firma_fecha', 'Fecha de emisión:') ?>">
                </div>

                <div class="footer-info">
                    Carrera 41 No 33B – 18 Barzal Alto PBX: (6) 6836680 Meta - Colombia
                </div>
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
</script>

</body>
</html>