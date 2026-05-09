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

// Ajusta el ID de este ítem
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 50;

// --- Lógica Empresa ---
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

// DATOS GUARDADOS
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
    ?? $resFormulario['campos']
    ?? null;

if (is_string($camposCrudos)) {

    $datosCampos = json_decode($camposCrudos, true) ?: [];

} elseif (is_array($camposCrudos)) {

    $datosCampos = $camposCrudos;
}

// FILAS
$filas = 10;

if (!empty($datosCampos)) {

    $maxFila = 10;

    foreach ($datosCampos as $key => $value) {

        if (preg_match('/^auditoria_(\d+)$/', $key, $matches)) {

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

    if (isset($datosCampos[$key]) && $datosCampos[$key] !== '') {

        return htmlspecialchars(
            (string)$datosCampos[$key],
            ENT_QUOTES,
            'UTF-8'
        );
    }

    return isset($_POST[$key])
        ? htmlspecialchars((string)$_POST[$key], ENT_QUOTES, 'UTF-8')
        : $default;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        PR-SST-14 - Programa Anual de Auditorías
    </title>

    <link rel="stylesheet" href="../../../assets/css/toolbar.css">

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
            max-width:1800px;
            margin:0 auto;
            background:#fff;
            border:1px solid #bfc7d1;
        }

       /* =========================================
   TOOLBAR SST
========================================= */

.sst-toolbar{
    position:sticky;
    top:0;
    z-index:100;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
    padding:14px 20px;
    background:#dde7f5;
    border-bottom:1px solid #c8d3e2;
}

.sst-toolbar-title{
    font-size:20px;
    font-weight:700;
    color:#213b67;
    margin:0;
}

.sst-toolbar-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:10px;
    margin-left:auto;
}

/* =========================================
   BOTONES TOOLBAR
========================================= */

.btn-toolbar{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:10px 20px;
    border:none;
    border-radius:8px;
    font-size:14px;
    font-weight:700;
    text-decoration:none;
    cursor:pointer;
    transition:.2s ease;
    color:#fff;
}

.btn-toolbar:hover{
    transform:translateY(-1px);
    opacity:.92;
    color:#fff;
}

/* VOLVER */

.btn-secondary{
    background:#6c757d;
}

/* GUARDAR */

.btn-success{
    background:#198754;
}

/* IMPRIMIR */

.btn-primary{
    background:#0d6efd;
}

/* RESPONSIVE */

@media(max-width:900px){

    .sst-toolbar{
        flex-direction:column;
        align-items:flex-start;
    }

    .sst-toolbar-actions{
        width:100%;
        justify-content:flex-end;
        flex-wrap:wrap;
    }
}

        /* =========================================
           FORMULARIO
        ========================================= */

        .formulario{
            padding:18px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            word-break:break-word;
        }

        th,
        td{
            border:1px solid #000;
            padding:4px;
            font-size:10px;
            text-align:center;
            vertical-align:middle;
            overflow:hidden;
        }

        .bg-gris{
            background:#f2f2f2;
        }

        .text-left{
            text-align:left;
            padding-left:8px;
        }

        input[type="text"],
        textarea{
            width:100%;
            border:none;
            outline:none;
            font-size:10px;
            background:transparent;
            display:block;
            margin:0;
            padding:2px;
            text-align:center;
        }

        textarea{
            resize:vertical;
            min-height:40px;
            text-align:left;
        }

        .mes-col{
            width:45px;
        }

        .pe-header{
            font-size:9px;
            font-weight:bold;
            background:#eee;
            width:22px;
        }

        .celda-pe{
            width:22px;
            padding:0;
        }

        .celda-pe input{
            padding:1px;
            font-size:9px;
        }

        .btn-add{
            background:#213b67;
            color:#fff;
            border:none;
            padding:10px 18px;
            border-radius:6px;
            cursor:pointer;
            font-weight:700;
            margin-top:12px;
        }

        .btn-add:hover{
            background:#172b4d;
        }

        @media print {

            .print-hide{
                display:none !important;
            }

            body{
                padding:0;
                background:#fff;
            }

            .contenedor{
                border:none;
            }

            table{
                table-layout:fixed;
            }

            input::placeholder{
                color:transparent;
            }
        }

    </style>

</head>

<body>

<div class="contenedor">

    <!-- =========================================
     TOOLBAR CORREGIDO
========================================= -->

<div class="sst-toolbar print-hide">

    <h1 class="sst-toolbar-title">
        Programa Anual de Auditorías (PR-SST-14)
    </h1>

    <div class="sst-toolbar-actions">

        <a href="../planear.php" class="btn-toolbar btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>

        <button type="button"
                id="btnGuardar"
                class="btn-toolbar btn-success">

            <i class="fa-solid fa-save"></i>
            Guardar

        </button>

        <button type="button"
                class="btn-toolbar btn-primary"
                onclick="window.print()">

            <i class="fa-solid fa-print"></i>
            Imprimir

        </button>

    </div>

</div>


    <!-- FORMULARIO -->
    <div class="formulario">

        <form id="formAuditoria">

            <!-- CABECERA -->
            <table>

                <tr>

                    <td rowspan="2" style="width:20%;">

                        <div style="height:60px; display:flex; align-items:center; justify-content:center;">

                            <?php if(!empty($logoEmpresaUrl)): ?>

                                <img src="<?= $logoEmpresaUrl ?>"
                                     style="max-height:55px;">

                            <?php else: ?>

                                <strong>TU LOGO</strong>

                            <?php endif; ?>

                        </div>

                    </td>

                    <td style="width:60%; font-weight:bold; font-size:14px;">
                        SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                    </td>

                    <td style="width:20%;">
                        0
                    </td>

                </tr>

                <tr>

                    <td style="font-weight:bold;">
                        PROGRAMA ANUAL DE AUDITORIAS
                    </td>

                    <td>
                        PR-SST-14
                        <br>
                        XX/XX/2025
                    </td>

                </tr>

            </table>

            <!-- DATOS -->
            <table style="margin-top:-1px;">

                <tr>

                    <td class="text-left"
                        style="width:15%; font-weight:bold;">

                        Objetivo del Programa:

                    </td>

                    <td colspan="5">

                        <input type="text"
                               name="objetivo_prog"
                               value="<?= oldv('objetivo_prog') ?>">

                    </td>

                </tr>

                <tr>

                    <td class="text-left"
                        style="font-weight:bold;">

                        Alcance del Programa:

                    </td>

                    <td colspan="5">

                        <input type="text"
                               name="alcance_prog"
                               value="<?= oldv('alcance_prog') ?>">

                    </td>

                </tr>

                <tr>

                    <td class="text-left"
                        style="font-weight:bold;">

                        Criterios Requisitos legales:

                    </td>

                    <td colspan="5">

                        <input type="text"
                               name="criterios_leg"
                               value="<?= oldv('criterios_leg') ?>">

                    </td>

                </tr>

            </table>

            <!-- BOTON AGREGAR -->
            <div class="print-hide">

                <button type="button"
                        class="btn-add"
                        onclick="agregarFila()">

                    + Agregar Auditoría

                </button>

            </div>

            <!-- TABLA -->
            <div style="overflow-x:auto;">

                <table style="margin-top:10px; min-width:1200px;"
                       id="tablaPrincipal">

                    <thead>

                    <tr>

                        <th rowspan="3" style="width:150px;">
                            AUDITORIA
                        </th>

                        <th rowspan="3">
                            RESPONSABLES
                        </th>

                        <th rowspan="3">
                            COSTOS
                        </th>

                        <th rowspan="3">
                            RECURSOS
                        </th>

                        <th colspan="5">
                            PROCESOS / ÁREAS / CONTRATISTAS
                        </th>

                        <?php

                        $meses = [
                            'Enero',
                            'Febrero',
                            'Marzo',
                            'Abril',
                            'Mayo',
                            'Junio',
                            'Julio',
                            'Agosto',
                            'Septiembre',
                            'Octubre',
                            'Noviembre',
                            'Diciembre'
                        ];

                        foreach($meses as $m){

                            echo "<th colspan='2' class='mes-col'>$m</th>";
                        }

                        ?>

                    </tr>

                    <tr>

                        <th rowspan="2" style="font-size:9px;">
                            Estratégico
                        </th>

                        <th rowspan="2" style="font-size:9px;">
                            Misional
                        </th>

                        <th colspan="2" style="font-size:9px;">
                            Apoyo
                        </th>

                        <th rowspan="2" style="font-size:9px;">
                            Operaciones
                        </th>

                        <?php for($i=0; $i<12; $i++): ?>

                            <th class="pe-header">P</th>
                            <th class="pe-header">E</th>

                        <?php endfor; ?>

                    </tr>

                    <tr>

                        <th style="font-size:8px;">Adm</th>
                        <th style="font-size:8px;">Garantía</th>

                    </tr>

                    </thead>

                    <tbody id="tbodyAuditoria">

                    <?php for ($i = 1; $i <= $filas; $i++): ?>

                        <tr>

                            <td>
                                <textarea name="auditoria_<?= $i ?>">
<?= oldv("auditoria_$i") ?>
                                </textarea>
                            </td>

                            <td>
                                <input type="text"
                                       name="resp_<?= $i ?>"
                                       value="<?= oldv("resp_$i") ?>">
                            </td>

                            <td>
                                <input type="text"
                                       name="costo_<?= $i ?>"
                                       value="<?= oldv("costo_$i") ?>">
                            </td>

                            <td>
                                <input type="text"
                                       name="recurso_<?= $i ?>"
                                       value="<?= oldv("recurso_$i") ?>">
                            </td>

                            <td>
                                <input type="checkbox"
                                       name="est_<?= $i ?>"
                                       value="1"
                                    <?= oldv("est_$i") == '1' ? 'checked' : '' ?>>
                            </td>

                            <td>
                                <input type="checkbox"
                                       name="mis_<?= $i ?>"
                                       value="1"
                                    <?= oldv("mis_$i") == '1' ? 'checked' : '' ?>>
                            </td>

                            <td>
                                <input type="checkbox"
                                       name="apo_a_<?= $i ?>"
                                       value="1"
                                    <?= oldv("apo_a_$i") == '1' ? 'checked' : '' ?>>
                            </td>

                            <td>
                                <input type="checkbox"
                                       name="apo_g_<?= $i ?>"
                                       value="1"
                                    <?= oldv("apo_g_$i") == '1' ? 'checked' : '' ?>>
                            </td>

                            <td>
                                <input type="checkbox"
                                       name="ope_<?= $i ?>"
                                       value="1"
                                    <?= oldv("ope_$i") == '1' ? 'checked' : '' ?>>
                            </td>

                            <?php for($m=1; $m<=12; $m++): ?>

                                <td class="celda-pe">

                                    <input type="text"
                                           name="p_<?= $m ?>_<?= $i ?>"
                                           value="<?= oldv("p_{$m}_{$i}") ?>">

                                </td>

                                <td class="celda-pe">

                                    <input type="text"
                                           name="e_<?= $m ?>_<?= $i ?>"
                                           value="<?= oldv("e_{$m}_{$i}") ?>">

                                </td>

                            <?php endfor; ?>

                        </tr>

                    <?php endfor; ?>

                    </tbody>

                    <tfoot>

                    <tr class="bg-gris">

                        <td colspan="9"
                            style="text-align:right; font-weight:bold;">

                            CUMPLIMIENTO

                        </td>

                        <?php for($m=1; $m<=12; $m++): ?>

                            <td colspan="2">

                                <input type="text"
                                       name="cumpl_<?= $m ?>"
                                       value="<?= oldv("cumpl_$m") ?>"
                                       placeholder="0%">

                            </td>

                        <?php endfor; ?>

                    </tr>

                    <tr class="bg-gris">

                        <td colspan="9"
                            style="text-align:right; font-weight:bold;">

                            EFICACIA

                        </td>

                        <?php for($m=1; $m<=12; $m++): ?>

                            <td colspan="2">

                                <input type="text"
                                       name="efic_<?= $m ?>"
                                       value="<?= oldv("efic_$m") ?>"
                                       placeholder="0%">

                            </td>

                        <?php endfor; ?>

                    </tr>

                    </tfoot>

                </table>

            </div>

        </form>

    </div>

</div>

<script>

let contador = <?= $filas ?>;

function agregarFila(){

    contador++;

    const tbody = document.getElementById('tbodyAuditoria');

    const tr = document.createElement('tr');

    let mesesHtml = '';

    for(let m=1; m<=12; m++){

        mesesHtml += `
            <td class="celda-pe">
                <input type="text" name="p_${m}_${contador}">
            </td>

            <td class="celda-pe">
                <input type="text" name="e_${m}_${contador}">
            </td>
        `;
    }

    tr.innerHTML = `

        <td>
            <textarea name="auditoria_${contador}"></textarea>
        </td>

        <td>
            <input type="text" name="resp_${contador}">
        </td>

        <td>
            <input type="text" name="costo_${contador}">
        </td>

        <td>
            <input type="text" name="recurso_${contador}">
        </td>

        <td>
            <input type="checkbox" name="est_${contador}" value="1">
        </td>

        <td>
            <input type="checkbox" name="mis_${contador}" value="1">
        </td>

        <td>
            <input type="checkbox" name="apo_a_${contador}" value="1">
        </td>

        <td>
            <input type="checkbox" name="apo_g_${contador}" value="1">
        </td>

        <td>
            <input type="checkbox" name="ope_${contador}" value="1">
        </td>

        ${mesesHtml}
    `;

    tbody.appendChild(tr);
}

// GUARDAR
document.getElementById('btnGuardar')
.addEventListener('click', async function(){

    const btn = this;

    const form = document.getElementById('formAuditoria');

    const formData = new FormData(form);

    const datosJSON = {};

    formData.forEach((value, key) => {

        datosJSON[key] = value;

    });

    btn.innerHTML = 'Guardando...';

    btn.disabled = true;

    try {

        const response = await fetch(

            "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar",

            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $token ?>'
                },

                body: JSON.stringify({

                    id_empresa: <?= $empresa ?>,

                    id_item_sst: <?= $idItem ?>,

                    datos: datosJSON
                })
            }
        );

        const result = await response.json();

        if (result.ok) {

            Swal.fire(
                '¡Guardado!',
                'El programa de auditorías se actualizó.',
                'success'
            );

        } else {

            Swal.fire(
                'Error',
                'No se pudo guardar la información.',
                'error'
            );
        }

    } catch (error) {

        Swal.fire(
            'Error',
            'No se pudo conectar con la API.',
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