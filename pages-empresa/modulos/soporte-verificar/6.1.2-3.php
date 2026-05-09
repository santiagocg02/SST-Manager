<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);

// ID para Plan de Auditoría
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 51;

// --- Lógica de Logo ---
$logoEmpresaUrl = "";

if ($empresa > 0) {

    $resEmpresa = $api->solicitar(
        "index.php?table=empresas&id=$empresa",
        "GET",
        null,
        $token
    );

    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {

        $empData = isset($resEmpresa['data'][0])
            ? $resEmpresa['data'][0]
            : $resEmpresa['data'];

        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// --- Carga de Datos ---
$resFormulario = $api->solicitar(
    "formularios-dinamicos/empresa/$empresa/item/$idItem",
    "GET",
    null,
    $token
);

$datosCampos = [];

$camposCrudos =
    $resFormulario['data']['data']['campos']
    ?? $resFormulario['data']['campos']
    ?? null;

if (is_string($camposCrudos)) {

    $datosCampos = json_decode($camposCrudos, true) ?: [];

} elseif (is_array($camposCrudos)) {

    $datosCampos = $camposCrudos;
}

// FILAS DINÁMICAS
$filas = 1;

if (!empty($datosCampos)) {

    $maxFila = 1;

    foreach ($datosCampos as $key => $v) {

        if (preg_match('/^item_(\d+)$/', $key, $matches)) {

            $num = (int)$matches[1];

            if ($num > $maxFila) {
                $maxFila = $num;
            }
        }
    }

    $filas = $maxFila;
}

function oldv($key, $default = '')
{
    global $datosCampos;

    if (isset($datosCampos[$key])) {

        return htmlspecialchars(
            (string)$datosCampos[$key],
            ENT_QUOTES,
            'UTF-8'
        );
    }

    return $default;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Plan de Auditoría
    </title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family:Arial,sans-serif;
        }

        body{
            background:#f2f4f7;
            padding:20px;
            color:#111;
        }

        .contenedor{
            max-width:1200px;
            margin:0 auto;
            background:#fff;
            border:1px solid #bfc7d1;
            box-shadow:0 4px 18px rgba(0,0,0,.08);
        }

        /* =========================================
           TOOLBAR NUEVO
        ========================================= */

        .topbar{
            position:sticky;
            top:0;
            z-index:100;
            background:#dde7f5;
            border-bottom:1px solid #c8d3e2;
            padding:14px 20px;
        }

        .toolbar-flex{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
        }

        .toolbar-title{
            font-size:20px;
            font-weight:700;
            color:#1a4175;
        }

        .toolbar-actions{
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:10px;
            margin-left:auto;
        }

        .btn-ui{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:10px 18px;
            border-radius:8px;
            border:none;
            text-decoration:none;
            font-size:14px;
            font-weight:700;
            transition:.2s ease;
            cursor:pointer;
            color:#fff;
        }

        .btn-ui:hover{
            transform:translateY(-1px);
            opacity:.95;
        }

        .btn-secondary{
            background:#6c757d;
        }

        .btn-secondary:hover{
            background:#5c636a;
        }

        .btn-success{
            background:#198754;
        }

        .btn-success:hover{
            background:#157347;
        }

        .btn-primary{
            background:#0d6efd;
        }

        .btn-primary:hover{
            background:#0b5ed7;
        }

        /* =========================================
           FORMULARIO
        ========================================= */

        .formulario{
            padding:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-bottom:-1px;
        }

        th,
        td{
            border:1px solid #000;
            padding:6px;
            font-size:12px;
            vertical-align:middle;
        }

        .bg-blue-light{
            background:#dde7f5;
            font-weight:bold;
            text-align:center;
        }

        input[type="text"],
        textarea{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:2px;
            font-family:inherit;
        }

        textarea{
            resize:vertical;
            min-height:25px;
            line-height:1.4;
        }

        .label-bold{
            font-weight:bold;
            background:#f2f2f2;
            width:220px;
            text-align:left;
        }

        .col-fecha{
            width:90px;
        }

        .col-hora{
            width:110px;
        }

        .col-auditado{
            width:200px;
        }

        .col-auditor{
            width:160px;
        }

        .text-verde{
            color:#198754;
            font-weight:bold;
        }

        .btn-add{
            margin-top:12px;
            background:#213b67;
            color:#fff;
            border:none;
            padding:10px 18px;
            border-radius:8px;
            font-weight:700;
            cursor:pointer;
            transition:.2s ease;
        }

        .btn-add:hover{
            background:#172b4d;
        }

        @media print{

            .print-hide{
                display:none !important;
            }

            body{
                padding:0;
                background:#fff;
            }

            .contenedor{
                border:none;
                box-shadow:none;
                max-width:100%;
            }

            textarea{
                resize:none;
                overflow:hidden;
            }
        }

    </style>

</head>

<body>

<div class="contenedor">

    <!-- TOOLBAR -->
    <div class="topbar print-hide">

        <div class="toolbar-flex">

            <h1 class="toolbar-title">
                Plan de Auditoría (RE-SST-19)
            </h1>

            <div class="toolbar-actions">

                <a href="../planear.php"
                   class="btn-ui btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>
                    Volver

                </a>

                <button type="button"
                        id="btnGuardar"
                        class="btn-ui btn-success">

                    <i class="fa-solid fa-save"></i>
                    Guardar

                </button>

                <button type="button"
                        class="btn-ui btn-primary"
                        onclick="window.print()">

                    <i class="fa-solid fa-print"></i>
                    Imprimir

                </button>

            </div>

        </div>

    </div>

    <!-- FORMULARIO -->
    <div class="formulario">

        <form id="formPlanAuditoria">

            <!-- CABECERA -->
            <table>

                <tr>

                    <td rowspan="2"
                        style="width:200px; text-align:center; padding:5px;">

                        <?php if(!empty($logoEmpresaUrl)): ?>

                            <img src="<?= $logoEmpresaUrl ?>"
                                 style="max-height:55px;">

                        <?php else: ?>

                            <div style="color:#999; font-weight:bold; font-size:10px; border:1px dashed #ccc; padding:10px;">
                                LOGO
                            </div>

                        <?php endif; ?>

                    </td>

                    <td style="font-weight:bold; font-size:13px; text-align:center; text-transform:uppercase;">

                        SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO

                    </td>

                    <td style="width:120px; text-align:center;">
                        0
                    </td>

                </tr>

                <tr>

                    <td style="font-weight:bold; text-align:center; text-transform:uppercase;">

                        PLAN DE AUDITORIA

                    </td>

                    <td style="text-align:center; font-size:11px;">

                        RE-SST-19
                        <br>
                        XX/XX/2025

                    </td>

                </tr>

            </table>

            <!-- INFORMACIÓN -->
            <table style="margin-top:-1px;">

                <tr>
                    <td colspan="2">
                        <textarea name="objetivo" placeholder="OBJETIVO"><?= oldv('objetivo') ?></textarea>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <textarea name="alcance" placeholder="ALCANCE"><?= oldv('alcance') ?></textarea>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <textarea name="referencia" placeholder="DOCUMENTOS DE REFERENCIA"><?= oldv('referencia') ?></textarea>
                    </td>
                </tr>

                <tr>
                    <td class="label-bold">AUDITOR LÍDER :</td>
                    <td>
                        <input type="text"
                               name="auditor_lider"
                               value="<?= oldv('auditor_lider') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="label-bold">AUDITOR(ES) ACOMPAÑANTE(S) :</td>
                    <td>
                        <input type="text"
                               name="auditor_acompa"
                               value="<?= oldv('auditor_acompa') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="label-bold">FECHA :</td>
                    <td>
                        <input type="text"
                               name="fecha_general"
                               value="<?= oldv('fecha_general') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="label-bold">LUGAR :</td>
                    <td>
                        <input type="text"
                               name="lugar_general"
                               value="<?= oldv('lugar_general') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="label-bold">HORA REUNIÓN APERTURA :</td>
                    <td>
                        <input type="text"
                               name="hora_apertura"
                               value="<?= oldv('hora_apertura') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="label-bold">HORA REUNIÓN CIERRE :</td>
                    <td>
                        <input type="text"
                               name="hora_cierre"
                               value="<?= oldv('hora_cierre') ?>">
                    </td>
                </tr>

            </table>

            <!-- TABLA -->
            <table style="margin-top:10px;"
                   id="tablaAgenda">

                <thead>

                    <tr class="bg-blue-light">

                        <th class="col-fecha">
                            FECHA
                        </th>

                        <th class="col-hora">
                            HORA
                        </th>

                        <th>
                            ITEM
                        </th>

                        <th class="col-auditado">
                            AUDITADO
                        </th>

                        <th class="col-auditor">
                            AUDITOR
                        </th>

                    </tr>

                </thead>

                <tbody id="tbodyAgenda">

                <?php for ($i = 1; $i <= $filas; $i++): ?>

                    <tr>

                        <td>
                            <input type="text"
                                   name="fec_<?= $i ?>"
                                   value="<?= oldv("fec_$i") ?>"
                                   style="text-align:center;">
                        </td>

                        <td>
                            <input type="text"
                                   name="hor_<?= $i ?>"
                                   value="<?= oldv("hor_$i") ?>"
                                   style="text-align:center;">
                        </td>

                        <td>
                            <textarea name="item_<?= $i ?>"><?= oldv("item_$i") ?></textarea>
                        </td>

                        <td>
                            <textarea name="auditado_<?= $i ?>"
                                      class="text-verde"><?= oldv("auditado_$i") ?></textarea>
                        </td>

                        <td>
                            <input type="text"
                                   name="auditor_col_<?= $i ?>"
                                   value="<?= oldv("auditor_col_$i") ?>">
                        </td>

                    </tr>

                <?php endfor; ?>

                </tbody>

            </table>

            <!-- BOTÓN -->
            <div class="print-hide">

                <button type="button"
                        class="btn-add"
                        onclick="agregarFilaAgenda()">

                    + AGREGAR ITEM A LA AGENDA

                </button>

            </div>

        </form>

    </div>

</div>

<script>

let contadorAgenda = <?= $filas ?>;

function agregarFilaAgenda(){

    contadorAgenda++;

    const tbody = document.getElementById('tbodyAgenda');

    const tr = document.createElement('tr');

    tr.innerHTML = `

        <td>
            <input type="text"
                   name="fec_${contadorAgenda}"
                   style="text-align:center;">
        </td>

        <td>
            <input type="text"
                   name="hor_${contadorAgenda}"
                   style="text-align:center;">
        </td>

        <td>
            <textarea name="item_${contadorAgenda}"></textarea>
        </td>

        <td>
            <textarea name="auditado_${contadorAgenda}"
                      class="text-verde"></textarea>
        </td>

        <td>
            <input type="text"
                   name="auditor_col_${contadorAgenda}">
        </td>

    `;

    tbody.appendChild(tr);
}

// GUARDAR
document.getElementById('btnGuardar')
.addEventListener('click', async function(){

    const btn = this;

    const form = document.getElementById('formPlanAuditoria');

    const formData = new FormData(form);

    const datosJSON = Object.fromEntries(formData.entries());

    btn.innerHTML = 'Guardando...';

    btn.disabled = true;

    try {

        const response = await fetch(

            "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar",

            {
                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'Authorization':'Bearer <?= $token ?>'
                },

                body: JSON.stringify({

                    id_empresa: <?= $empresa ?>,

                    id_item_sst: <?= $idItem ?>,

                    datos: datosJSON
                })
            }
        );

        const res = await response.json();

        if(res.ok){

            Swal.fire(
                '¡Guardado!',
                'El plan de auditoría se actualizó correctamente.',
                'success'
            );

        } else {

            Swal.fire(
                'Error',
                res.error || 'No se pudo guardar.',
                'error'
            );
        }

    } catch(e){

        Swal.fire(
            'Error',
            'No se pudo conectar con el servidor.',
            'error'
        );

    } finally {

        btn.innerHTML = `
            <i class="fa-solid fa-save"></i>
            Guardar
        `;

        btn.disabled = false;
    }
});

</script>

</body>
</html>