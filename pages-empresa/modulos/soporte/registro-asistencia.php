<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);

$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 42;

// LOGO EMPRESA
$logoEmpresaUrl = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);

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

<title>RE-SST-01 | Registro de Asistencia</title>

<link rel="stylesheet" href="../../../assets/css/toolbar.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

:root{
    --line:#111;
    --blue:#9fb4d9;
    --bg:#eef3f9;
}

*{
    box-sizing:border-box;
}

body{
    margin:0;
    background:var(--bg);
    font-family:Arial, Helvetica, sans-serif;
}

.page-wrap{
    padding:24px;
}

.paper{
    max-width:1100px;
    margin:0 auto;
    background:#fff;
    border-radius:10px;
    border:1px solid #d7dee8;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    padding:24px;
}

/* HEADER */

.header-table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    margin-bottom:20px;
}

.header-table td{
    border:1px solid #111;
    padding:0;
}

.logo-cell{
    width:25%;
    text-align:center;
}

.logo-box{
    height:100px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.title-cell{
    width:55%;
}

.title-main{
    min-height:55px;
    border-bottom:1px solid #111;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding:10px;
    font-size:16px;
    font-weight:800;
    color:#213b67;
    text-transform:uppercase;
}

.title-sub{
    min-height:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding:10px;
    font-size:14px;
    font-weight:800;
    text-transform:uppercase;
}

.meta-cell{
    width:20%;
}

.meta-box{
    display:flex;
    flex-direction:column;
    height:100px;
}

.meta-box input{
    flex:1;
    border:none;
    border-bottom:1px solid #111;
    text-align:center;
    font-weight:700;
    outline:none;
    background:transparent;
}

.meta-box input:last-child{
    border-bottom:none;
}

/* INFO */

.info-block{
    margin-bottom:20px;
}

.info-row{
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:12px;
}

.info-label{
    font-size:14px;
    font-weight:800;
    color:#213b67;
}

.line-input{
    border:none;
    border-bottom:1px solid #777;
    background:transparent;
    outline:none;
    padding:4px;
}

.line-input.sm{
    width:150px;
}

.line-input.lg{
    flex:1;
}

/* TABLA */

.attendance-table{
    width:100%;
    border-collapse:collapse;
}

.attendance-table th,
.attendance-table td{
    border:1px solid #555;
    height:38px;
}

.attendance-table th{
    background:var(--blue);
    text-align:center;
    font-size:13px;
    font-weight:800;
    padding:6px;
}

.cell-input{
    width:100%;
    height:100%;
    border:none;
    outline:none;
    background:transparent;
    padding:6px 8px;
}

.num-cell{
    width:60px;
    text-align:center;
    font-weight:700;
    background:#fafafa;
}

.col-del{
    width:60px;
    text-align:center;
}

.btn-delete{
    background:#dc3545;
    border:none;
    color:#fff;
    width:28px;
    height:28px;
    border-radius:6px;
    cursor:pointer;
    font-size:12px;
}

.btn-delete:hover{
    background:#bb2d3b;
}

/* BOTON AGREGAR */

.add-row-wrap{
    margin-top:16px;
    display:flex;
    justify-content:flex-end;
}

.btn-add{
    border:none;
    background:#198754;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    font-size:13px;
    font-weight:700;
    cursor:pointer;
}

.btn-add:hover{
    background:#146c43;
}

@media print{

    .sst-toolbar,
    .print-hide{
        display:none !important;
    }

    body{
        background:#fff;
    }

    .page-wrap{
        padding:0;
    }

    .paper{
        box-shadow:none;
        border:none;
        padding:0;
        max-width:100%;
    }
}

</style>
</head>

<body>

<!-- TOOLBAR -->
<div class="sst-toolbar">

    <h1 class="sst-toolbar-title">
        REGISTRO DE ASISTENCIA
    </h1>

    <div class="sst-toolbar-actions">

        <a href="../planear.php" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>

        <button
            type="button"
            id="btnGuardar"
            class="btn btn-success btn-sm"
        >
            <i class="fa-solid fa-floppy-disk"></i>
            Guardar
        </button>

        <button
            type="button"
            class="btn btn-primary btn-sm"
            onclick="window.print()"
        >
            <i class="fa-solid fa-print"></i>
            Imprimir
        </button>

    </div>

</div>

<div class="page-wrap">

<form id="form-sst-dinamico">

<div class="paper">

    <!-- HEADER -->
    <table class="header-table">

        <tr>

            <td class="logo-cell">

                <div class="logo-box">

                    <?php if(!empty($logoEmpresaUrl)): ?>

                        <img
                            src="<?= $logoEmpresaUrl ?>"
                            alt="Logo"
                            style="max-width:100%; max-height:80px; object-fit:contain;"
                        >

                    <?php else: ?>

                        <span style="color:#999; font-weight:700;">
                            LOGO EMPRESA
                        </span>

                    <?php endif; ?>

                </div>

            </td>

            <td class="title-cell">

                <div class="title-main">
                    SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO
                </div>

                <div class="title-sub">
                    REGISTRO DE ASISTENCIA
                </div>

            </td>

            <td class="meta-cell">

                <div class="meta-box">

                    <input
                        type="text"
                        name="meta_version"
                        value="0"
                    >

                    <input
                        type="text"
                        name="meta_codigo"
                        value="RE-SST-01"
                    >

                    <input
                        type="date"
                        name="meta_fecha"
                        id="metaFecha"
                    >

                </div>

            </td>

        </tr>

    </table>

    <!-- INFO -->
    <div class="info-block">

        <div class="info-row">

            <span class="info-label">FECHA:</span>

            <input
                type="date"
                name="fecha_registro"
                class="line-input sm"
                id="fechaRegistro"
            >

            <span class="info-label ms-3">HORA INICIO:</span>

            <input
                type="time"
                name="hora_inicio"
                class="line-input sm"
            >

            <span class="info-label ms-3">HORA FINAL:</span>

            <input
                type="time"
                name="hora_fin"
                class="line-input sm"
            >

        </div>

        <div class="info-row">

            <span class="info-label">TEMA:</span>

            <input
                type="text"
                name="tema_tratado"
                class="line-input lg"
            >

        </div>

    </div>

    <!-- TABLA -->
    <table class="attendance-table">

        <thead>

            <tr>

                <th style="width:60px;">No.</th>

                <th>NOMBRE COMPLETO</th>

                <th style="width:60px;" class="print-hide"></th>

            </tr>

        </thead>

        <tbody id="attendance-body">

            <tr class="data-row">

                <td class="num-cell seq-num">1</td>

                <td>
                    <input
                        type="text"
                        name="asist_nombre[]"
                        class="cell-input"
                    >
                </td>

                <td class="col-del print-hide">

                    <button
                        type="button"
                        class="btn-delete"
                        onclick="eliminarFila(this)"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </td>

            </tr>

        </tbody>

    </table>

    <!-- BOTON AGREGAR -->
    <div class="add-row-wrap print-hide">

        <button
            type="button"
            class="btn-add"
            onclick="agregarFila()"
        >
            <i class="fa-solid fa-plus"></i>
            Agregar fila
        </button>

    </div>

</div>

</form>

</div>

<script>

// FECHA HOY
function setHoy(){

    const d = new Date();

    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,"0");
    const dd = String(d.getDate()).padStart(2,"0");

    const fecha = `${y}-${m}-${dd}`;

    const meta = document.getElementById("metaFecha");
    const reg = document.getElementById("fechaRegistro");

    if(meta && !meta.value) meta.value = fecha;
    if(reg && !reg.value) reg.value = fecha;
}

setHoy();

// NUMERACION
function actualizarNumeracion(){

    document.querySelectorAll('.seq-num').forEach((el,index)=>{

        el.textContent = index + 1;

    });
}

// AGREGAR FILA
function agregarFila(){

    const tbody = document.getElementById('attendance-body');

    const fila = tbody.querySelector('.data-row');

    const nueva = fila.cloneNode(true);

    nueva.querySelectorAll('input').forEach(input=>{

        input.value = '';

    });

    tbody.appendChild(nueva);

    actualizarNumeracion();
}

// ELIMINAR FILA
function eliminarFila(btn){

    const tbody = document.getElementById('attendance-body');

    const filas = tbody.querySelectorAll('.data-row');

    if(filas.length > 1){

        btn.closest('tr').remove();

        actualizarNumeracion();

    }else{

        btn.closest('tr')
            .querySelector('input')
            .value = '';
    }
}

// CARGAR DATOS
document.addEventListener('DOMContentLoaded', ()=>{

    let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;

    if(typeof datosGuardados === 'string'){

        try{
            datosGuardados = JSON.parse(datosGuardados);
        }catch(e){}
    }

    if(datosGuardados && datosGuardados['asist_nombre']){

        const nombres = datosGuardados['asist_nombre'];

        for(let i=1; i<nombres.length; i++){

            agregarFila();
        }

        document
            .querySelectorAll('[name="asist_nombre[]"]')
            .forEach((input,index)=>{

                input.value = nombres[index] || '';

            });
    }
});

// GUARDAR
document.getElementById('btnGuardar')
.addEventListener('click', async function(){

    const btn = this;

    const form = document.getElementById('form-sst-dinamico');

    const formData = new FormData(form);

    const datosJSON = {};

    for(const [key,value] of formData.entries()){

        if(key.endsWith('[]')){

            const cleanKey = key.replace('[]','');

            if(!datosJSON[cleanKey]){

                datosJSON[cleanKey] = [];
            }

            datosJSON[cleanKey].push(value);

        }else{

            datosJSON[key] = value;
        }
    }

    const original = btn.innerHTML;

    btn.innerHTML = 'Guardando...';

    btn.disabled = true;

    try{

        const response = await fetch(
            "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar",
            {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'Authorization':'Bearer <?= $token ?>'
                },
                body:JSON.stringify({
                    id_empresa: <?= $empresa ?>,
                    id_item_sst: <?= $idItem ?>,
                    datos: datosJSON
                })
            }
        );

        const result = await response.json();

        if(result.ok){

            Swal.fire({
                icon:'success',
                title:'Guardado',
                text:'Registro guardado correctamente'
            });

        }else{

            Swal.fire({
                icon:'error',
                title:'Error',
                text: result.error || 'No se pudo guardar'
            });
        }

    }catch(error){

        Swal.fire({
            icon:'error',
            title:'Error',
            text:'Error de conexión'
        });

    }finally{

        btn.innerHTML = original;

        btn.disabled = false;
    }
});

</script>

</body>
</html>