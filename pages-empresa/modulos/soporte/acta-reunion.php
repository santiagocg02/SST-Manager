<?php
session_start();

// ===============================
// CONEXIÓN
// ===============================

require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

function e($v){
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$api      = new ConexionAPI();
$token    = $_SESSION["token"] ?? "";
$empresa  = (int)($_SESSION["id_empresa"] ?? 0);
$idItem   = isset($_GET['item']) ? (int)$_GET['item'] : 43;

// ===============================
// LOGO EMPRESA
// ===============================

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

// ===============================
// CARGAR FORMULARIO
// ===============================

$resFormulario = $api->solicitar(
    "formularios-dinamicos/empresa/$empresa/item/$idItem",
    "GET",
    null,
    $token
);

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

?>

<!doctype html>
<html lang="es">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>RE-SST-01 | Acta de Reunión</title>

<link rel="stylesheet" href="../../../assets/css/toolbar.css">
<link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

:root{
    --line:#111;
    --blue:#9fb4d9;
    --bg:#eef3f9;
    --paper:#fff;
    --text:#111;
}

*{
    box-sizing:border-box;
}

body{
    margin:0;
    background:var(--bg);
    font-family:Arial, Helvetica, sans-serif;
    color:var(--text);
}

/* ========================= */
/* TOOLBAR */
/* ========================= */

.topbar{
    position:sticky;
    top:0;
    z-index:100;
    background:#dde7f5;
    border-bottom:1px solid #c8d3e2;
    padding:10px 18px;
}

/* ========================= */
/* DOCUMENTO */
/* ========================= */

.page-wrap{
    padding:24px;
}

.paper{
    max-width:1050px;
    margin:0 auto 40px;
    background:#fff;
    border:1px solid #d7dee8;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    border-radius:8px;
    padding:24px;
}

.doc-table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.doc-table td,
.doc-table th{
    border:1px solid var(--line);
    padding:6px 8px;
    vertical-align:middle;
}

/* ========================= */
/* ENCABEZADO */
/* ========================= */

.logo-cell{
    width:18%;
    text-align:center;
    background:#fafafa;
    height:84px;
}

.logo-box{
    height:100px;
    margin:0 auto;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    color:#9a9a9a;
    font-weight:800;
}

.logo-box .small{
    font-size:12px;
}

.logo-box .big{
    font-size:15px;
    margin-top:4px;
}

.title-cell{
    width:62%;
    padding:0 !important;
}

.title-main,
.title-sub{
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
}

.title-main{
    min-height:47px;
    border-bottom:1px solid var(--line);
    font-size:16px;
    font-weight:800;
    text-transform:uppercase;
    padding:8px 10px;
    color:#213b67;
}

.title-sub{
    min-height:37px;
    font-size:14px;
    font-weight:800;
    text-transform:uppercase;
    padding:8px 10px;
}

.meta-cell{
    width:20%;
    padding:0 !important;
}

.meta-box{
    display:flex;
    flex-direction:column;
    min-height:84px;
}

.meta-box input{
    flex:1;
    border-bottom:1px solid var(--line);
    font-weight:700;
    font-size:14px;
    text-align:center;
    padding:6px;
    width:100%;
    border-top:none;
    border-left:none;
    border-right:none;
    outline:none;
    background:transparent;
}

.meta-box input:last-child{
    border-bottom:none;
}

/* ========================= */
/* TABLAS */
/* ========================= */

.section-title{
    background:var(--blue);
    text-align:center;
    font-weight:800;
    font-size:13px;
    text-transform:uppercase;
}

.subhead{
    text-align:center;
    font-weight:700;
    background:#f5f7fa;
}

.center{
    text-align:center;
    font-weight:bold;
    background:#fafafa;
}

.label-cell{
    font-weight:600;
    width:15%;
    white-space:nowrap;
}

.val-cell{
    width:35%;
}

.h-34 td{
    height:34px;
}

.input-cell,
.textarea-cell{
    width:100%;
    border:none;
    outline:none;
    background:transparent;
    font-size:14px;
    padding:6px;
    font-family:inherit;
}

.textarea-cell{
    resize:vertical;
    min-height:70px;
}

#participantes-body input{
    min-height:38px;
}

.conclusion-row td{
    height:68px;
}

.del-col{
    width:5%;
    text-align:center;
}

/* ========================= */
/* BOTONES */
/* ========================= */

.btn-delete{
    background:#dc3545;
    color:#fff;
    border:none;
    padding:2px 6px;
    border-radius:4px;
    cursor:pointer;
    font-weight:bold;
    font-size:11px;
}

.btn-delete:hover{
    background:#bb2d3b;
}

.actions-row{
    background:#f1f4f9;
    text-align:right;
}

.actions-row td{
    border:none;
    padding:6px;
}

/* ========================= */
/* PRINT */
/* ========================= */

@media print{

    body{
        background:#fff;
    }

    .topbar,
    .print-hide{
        display:none !important;
    }

    .page-wrap{
        padding:0;
    }

    .paper{
        max-width:100%;
        margin:0;
        border:none;
        box-shadow:none;
        padding:0;
    }

    @page{
        size:letter portrait;
        margin:10mm;
    }

    .del-col{
        display:none;
    }
}

</style>

</head>

<body>

<!-- ========================= -->
<!-- TOOLBAR -->
<!-- ========================= -->

<div class="topbar print-hide">

    <div class="sst-toolbar w-100 d-flex justify-content-between align-items-center">

        <h1 class="sst-toolbar-title mb-0">
            ACTA REUNIÓN
        </h1>

        <div class="sst-toolbar-actions d-flex justify-content-end align-items-center gap-2 ms-auto">

            <a href="../planear.php" class="btn btn-secondary btn-sm">
                Volver
            </a>

            <button type="button"
                    id="btnGuardar"
                    class="btn btn-success btn-sm">

                <i class="fa-solid fa-save"></i>
                Guardar

            </button>

            <button type="button"
                    class="btn btn-primary btn-sm"
                    onclick="window.print()">

                <i class="fa-solid fa-print"></i>
                Imprimir

            </button>

        </div>

    </div>

</div>

<!-- ========================= -->
<!-- FORMULARIO -->
<!-- ========================= -->

<div class="page-wrap">

<form id="form-sst-dinamico">

<div class="paper">

<!-- ========================= -->
<!-- ENCABEZADO -->
<!-- ========================= -->

<table class="doc-table">

<tr>

<td class="logo-cell">

    <div class="logo-box"
         style="border: <?= empty($logoEmpresaUrl) ? '2px dashed #c7c7c7' : 'none' ?>;">

        <?php if(!empty($logoEmpresaUrl)): ?>

            <img src="<?= $logoEmpresaUrl ?>"
                 alt="Logo Empresa"
                 style="max-width:100%; max-height:80px; object-fit:contain;">

        <?php else: ?>

            <div class="small">TU LOGO</div>
            <div class="big">AQUÍ</div>

        <?php endif; ?>

    </div>

</td>

<td class="title-cell">

    <div class="title-main">
        SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO
    </div>

    <div class="title-sub">
        ACTA DE REUNIÓN
    </div>

</td>

<td class="meta-cell">

    <div class="meta-box">

        <input type="text"
               name="meta_version"
               value="0">

        <input type="text"
               name="meta_codigo"
               value="RE-SST-01">

        <input type="date"
               name="meta_fecha"
               id="metaFecha">

    </div>

</td>

</tr>

<tr class="h-34">
    <td colspan="4" style="border:none;"></td>
</tr>

<tr class="h-34">

    <td class="label-cell">
        Comité o Grupo:
    </td>

    <td class="val-cell">
        <input type="text"
               name="comite_grupo"
               class="input-cell">
    </td>

    <td class="label-cell">
        Acta No:
    </td>

    <td class="val-cell">
        <input type="text"
               name="acta_no"
               class="input-cell">
    </td>

</tr>

<tr class="h-34">

    <td class="label-cell">
        Citada por:
    </td>

    <td class="val-cell">
        <input type="text"
               name="citada_por"
               class="input-cell">
    </td>

    <td class="label-cell">
        Fecha:
    </td>

    <td class="val-cell">
        <input type="date"
               name="fecha_acta"
               id="fechaActa"
               class="input-cell">
    </td>

</tr>

<tr class="h-34">

    <td class="label-cell">
        Coordinador:
    </td>

    <td class="val-cell">
        <input type="text"
               name="coordinador"
               class="input-cell">
    </td>

    <td class="label-cell">
        Horario:
    </td>

    <td class="val-cell">

        <input type="time"
               name="hora_inicio"
               class="input-cell"
               style="width:40%; display:inline-block;">

        -

        <input type="time"
               name="hora_fin"
               class="input-cell"
               style="width:40%; display:inline-block;">

    </td>

</tr>

</table>

<!-- ========================= -->
<!-- PARTICIPANTES -->
<!-- ========================= -->

<table class="doc-table" style="margin-top:20px;">

<tr>
    <td colspan="5" class="section-title">
        PARTICIPANTES
    </td>
</tr>

<tr>

    <td class="subhead" style="width:6%;">
        No.
    </td>

    <td class="subhead" style="width:44%;">
        Nombre y Apellidos
    </td>

    <td class="subhead" style="width:22%;">
        Cargo
    </td>

    <td class="subhead" style="width:23%;">
        Firma
    </td>

    <td class="subhead del-col print-hide"></td>

</tr>

<tbody id="participantes-body">

<tr class="h-34 data-row-part">

    <td class="center seq-num-part">
        1
    </td>

    <td>
        <input type="text"
               name="part_nombre[]"
               class="input-cell">
    </td>

    <td>
        <input type="text"
               name="part_cargo[]"
               class="input-cell">
    </td>

    <td>
        <input type="text"
               name="part_firma[]"
               class="input-cell">
    </td>

    <td class="print-hide text-center">

        <button type="button"
                class="btn-delete"
                onclick="eliminarFila(this, 'part')">

            X

        </button>

    </td>

</tr>

</tbody>

<tr class="actions-row print-hide">

<td colspan="5">

<button type="button"
        class="btn btn-outline-primary btn-sm"
        onclick="agregarFila('participantes-body', 'data-row-part', 'seq-num-part')">

    + Agregar Participante

</button>

</td>

</tr>

</table>

<!-- ========================= -->
<!-- PUNTOS -->
<!-- ========================= -->

<table class="doc-table" style="margin-top:20px;">

<tr>
    <td colspan="3" class="section-title">
        PUNTOS DE DISCUSIÓN
    </td>
</tr>

<tbody id="puntos-body">

<tr class="data-row-punto">

    <td class="center seq-num-punto" style="width:6%;">
        1
    </td>

    <td>
        <textarea name="punto_desc[]"
                  class="textarea-cell"></textarea>
    </td>

    <td class="print-hide text-center del-col">

        <button type="button"
                class="btn-delete"
                onclick="eliminarFila(this, 'punto')">

            X

        </button>

    </td>

</tr>

</tbody>

<tr class="actions-row print-hide">

<td colspan="3">

<button type="button"
        class="btn btn-outline-primary btn-sm"
        onclick="agregarFila('puntos-body', 'data-row-punto', 'seq-num-punto')">

    + Agregar Punto

</button>

</td>

</tr>

</table>

<!-- ========================= -->
<!-- CONCLUSIONES -->
<!-- ========================= -->

<table class="doc-table" style="margin-top:20px;">

<tr>
    <td colspan="5" class="section-title">
        CONCLUSIONES
    </td>
</tr>

<tr>

    <td class="subhead" style="width:6%;">
        No
    </td>

    <td class="subhead" style="width:42%;">
        Tarea
    </td>

    <td class="subhead" style="width:22%;">
        Responsable
    </td>

    <td class="subhead" style="width:25%;">
        Fecha de cumplimiento
    </td>

    <td class="subhead del-col print-hide"></td>

</tr>

<tbody id="conclusiones-body">

<tr class="conclusion-row data-row-concl">

    <td class="center seq-num-concl">
        1
    </td>

    <td>

        <textarea name="concl_tarea[]"
                  class="textarea-cell"></textarea>

    </td>

    <td>

        <input type="text"
               name="concl_resp[]"
               class="input-cell">

    </td>

    <td>

        <input type="date"
               name="concl_fecha[]"
               class="input-cell">

    </td>

    <td class="print-hide text-center">

        <button type="button"
                class="btn-delete"
                onclick="eliminarFila(this, 'concl')">

            X

        </button>

    </td>

</tr>

</tbody>

<tr class="actions-row print-hide">

<td colspan="5">

<button type="button"
        class="btn btn-outline-primary btn-sm"
        onclick="agregarFila('conclusiones-body', 'data-row-concl', 'seq-num-concl')">

    + Agregar Tarea

</button>

</td>

</tr>

</table>

</div>

</form>

</div>

<script>

// ===============================
// FECHA HOY
// ===============================

function setHoy(){

    const d = new Date();

    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");

    const fmeta = document.getElementById("metaFecha");

    if(fmeta && !fmeta.value){
        fmeta.value = `${y}-${m}-${dd}`;
    }

    const fActa = document.getElementById("fechaActa");

    if(fActa && !fActa.value){
        fActa.value = `${y}-${m}-${dd}`;
    }
}

setHoy();

// ===============================
// AUTO RESIZE
// ===============================

function autoResizeTextareas(){

    const textareas = document.querySelectorAll('textarea');

    textareas.forEach(ta => {

        ta.style.height = 'auto';
        ta.style.height = ta.scrollHeight + 'px';

        ta.addEventListener('input', function(){

            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';

        });

    });
}

setTimeout(autoResizeTextareas, 100);

// ===============================
// NUMERACIÓN
// ===============================

function actualizarNumeracion(bodyId, seqClass){

    const celdas = document
        .getElementById(bodyId)
        .querySelectorAll('.' + seqClass);

    celdas.forEach((celda, index) => {
        celda.textContent = index + 1;
    });
}

// ===============================
// AGREGAR FILA
// ===============================

function agregarFila(bodyId, rowClass, seqClass){

    const tbody = document.getElementById(bodyId);

    const plantilla = tbody.querySelector('.' + rowClass);

    if(plantilla){

        const nuevaFila = plantilla.cloneNode(true);

        const inputs = nuevaFila.querySelectorAll('input, textarea');

        inputs.forEach(input => input.value = '');

        tbody.appendChild(nuevaFila);

        actualizarNumeracion(bodyId, seqClass);

        autoResizeTextareas();
    }
}

// ===============================
// ELIMINAR FILA
// ===============================

function eliminarFila(btn, tipo){

    const row = btn.closest('tr');

    const tbody = row.parentElement;

    const bodyId = tbody.id;

    let rowClass, seqClass;

    if(tipo === 'part'){
        rowClass = 'data-row-part';
        seqClass = 'seq-num-part';
    }

    if(tipo === 'punto'){
        rowClass = 'data-row-punto';
        seqClass = 'seq-num-punto';
    }

    if(tipo === 'concl'){
        rowClass = 'data-row-concl';
        seqClass = 'seq-num-concl';
    }

    const filas = tbody.querySelectorAll('.' + rowClass);

    if(filas.length > 1){

        row.remove();

        actualizarNumeracion(bodyId, seqClass);

    }else{

        const inputs = row.querySelectorAll('input, textarea');

        inputs.forEach(input => input.value = '');
    }
}

// ===============================
// CARGAR DATOS
// ===============================

document.addEventListener('DOMContentLoaded', function(){

    let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;

    if(typeof datosGuardados === 'string'){

        try{
            datosGuardados = JSON.parse(datosGuardados);
        }catch(e){}
    }

    if(datosGuardados && Object.keys(datosGuardados).length > 0){

        // DATOS SIMPLES
        for (const [key, value] of Object.entries(datosGuardados)) {

            if(!Array.isArray(value)){

                let campo = document.querySelector(`[name="${key}"]`);

                if(campo){
                    campo.value = value;
                }
            }
        }

        // PARTICIPANTES
        let nPart = (
            datosGuardados['part_nombre']
            && Array.isArray(datosGuardados['part_nombre'])
        )
        ? datosGuardados['part_nombre'].length
        : 1;

        for(let i = 1; i < nPart; i++){

            agregarFila(
                'participantes-body',
                'data-row-part',
                'seq-num-part'
            );
        }

        // PUNTOS
        let nPuntos = (
            datosGuardados['punto_desc']
            && Array.isArray(datosGuardados['punto_desc'])
        )
        ? datosGuardados['punto_desc'].length
        : 1;

        for(let i = 1; i < nPuntos; i++){

            agregarFila(
                'puntos-body',
                'data-row-punto',
                'seq-num-punto'
            );
        }

        // CONCLUSIONES
        let nConcl = (
            datosGuardados['concl_tarea']
            && Array.isArray(datosGuardados['concl_tarea'])
        )
        ? datosGuardados['concl_tarea'].length
        : 1;

        for(let i = 1; i < nConcl; i++){

            agregarFila(
                'conclusiones-body',
                'data-row-concl',
                'seq-num-concl'
            );
        }

        // POBLAR ARRAYS
        for (const [key, value] of Object.entries(datosGuardados)) {

            if(Array.isArray(value)){

                let campos = document.querySelectorAll(`[name="${key}[]"]`);

                value.forEach((val, i) => {

                    if(campos[i]){
                        campos[i].value = val;
                    }

                });
            }
        }

        setTimeout(autoResizeTextareas, 200);
    }
});

// ===============================
// GUARDAR
// ===============================

document.getElementById('btnGuardar').addEventListener('click', async function(){

    const btn = this;

    const form = document.getElementById('form-sst-dinamico');

    const formData = new FormData(form);

    const datosJSON = {};

    for (const [key, value] of formData.entries()) {

        if(key.endsWith('[]')){

            const cleanKey = key.replace('[]', '');

            if(!datosJSON[cleanKey]){
                datosJSON[cleanKey] = [];
            }

            datosJSON[cleanKey].push(value);

        }else{

            datosJSON[key] = value;
        }
    }

    const originalText = btn.innerHTML;

    btn.innerHTML = 'Guardando...';

    btn.disabled = true;

    try{

        const token = "<?= $token ?>";

        const urlAPI =
            "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar";

        const response = await fetch(urlAPI, {

            method:'POST',

            headers:{
                'Content-Type':'application/json',
                'Authorization':'Bearer ' + token
            },

            body:JSON.stringify({
                id_empresa: <?= $empresa ?>,
                id_item_sst: <?= $idItem ?>,
                datos: datosJSON
            })

        });

        const result = await response.json();

        if(result.ok){

            Swal.fire({
                title:'¡Éxito!',
                text:'Acta guardada correctamente',
                icon:'success',
                confirmButtonColor:'#198754'
            });

        }else{

            Swal.fire({
                title:'Error',
                text: result.error || 'No se pudo guardar',
                icon:'error'
            });
        }

    }catch(error){

        console.error(error);

        Swal.fire({
            title:'Error',
            text:'No se pudo conectar con el servidor',
            icon:'error'
        });

    }finally{

        btn.innerHTML = originalText;

        btn.disabled = false;
    }

});

</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>

</body>
</html>