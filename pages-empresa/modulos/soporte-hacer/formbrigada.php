<?php
session_start();

require_once __DIR__ . '/../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();

$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);

$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 0;

// LOGO
$logoEmpresaUrl = "";

if ($empresa > 0) {

    $resEmpresa = $api->solicitar(
        "index.php?table=empresas&id=$empresa",
        "GET",
        null,
        $token
    );

    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// DATOS
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
}
elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

function oldv($key, $default = '')
{
    global $datosCampos;

    return isset($datosCampos[$key])
        ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8')
        : $default;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Formato Brigadas</title>

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
}

.contenedor{
    max-width:1100px;
    margin:auto;
    background:#fff;
    border:1px solid #cfd6df;
    box-shadow:0 4px 18px rgba(0,0,0,.08);
}

.toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 20px;
    background:#dde7f5;
    border-bottom:1px solid #c8d3e2;
}

.toolbar h1{
    font-size:20px;
    color:#1a4175;
    font-weight:700;
}

.acciones{
    display:flex;
    gap:10px;
}

.btn{
    border:none;
    padding:10px 20px;
    border-radius:8px;
    font-size:14px;
    font-weight:700;
    color:#fff;
    cursor:pointer;
}

.btn-atras{
    background:#6c757d;
}

.btn-guardar{
    background:#198754;
}

.btn-imprimir{
    background:#0d6efd;
}

.formulario-body{
    padding:25px;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

td, th{
    border:1px solid #000;
    padding:6px;
    vertical-align:top;
    font-size:12px;
}

input,
textarea{
    width:100%;
    border:none;
    outline:none;
    background:transparent;
    font-size:12px;
    font-family:inherit;
}

textarea{
    resize:vertical;
    min-height:60px;
}

.titulo{
    font-weight:bold;
    text-align:center;
    background:#f5f5f5;
}

.subtitulo{
    text-align:center;
    font-weight:bold;
}

.label{
    font-weight:bold;
}

.foto-box{
    width:100%;
    height:230px;
    border:2px solid #2f5fb3;
}

.linea{
    border-bottom:1px solid #000;
    min-height:20px;
}

.center{
    text-align:center;
}

.firma-box{
    height:120px;
}

@media print{

    .print-hide{
        display:none !important;
    }

    .contenedor{
        border:none;
        box-shadow:none;
    }

}

</style>

</head>

<body>

<div class="contenedor">

<div class="toolbar print-hide">

    <h1>
        Formato Inscripción Brigadas de Emergencia
    </h1>

    <div class="acciones">

        <button
            class="btn btn-atras"
            onclick="history.back()">
            Atrás
        </button>

        <button
            class="btn btn-guardar"
            id="btnGuardar">
            Guardar
        </button>

        <button
            class="btn btn-imprimir"
            onclick="window.print()">
            Imprimir
        </button>

    </div>

</div>

<div class="formulario-body">

<form id="formBrigada">

<!-- ENCABEZADO -->

<table>

<tr>

<td rowspan="2"
style="width:220px;text-align:center;">

<?php if($logoEmpresaUrl): ?>

<img src="<?= $logoEmpresaUrl ?>"
style="max-height:70px;">

<?php endif; ?>

</td>

<td class="titulo"
style="font-size:16px;">

SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO

</td>

<td class="center"
style="width:120px;font-weight:bold;">

0

</td>

</tr>

<tr>

<td class="subtitulo"
style="font-size:15px;">

FORMATO INSCRIPCIÓN A BRIGADAS DE EMERGENCIA

</td>

<td class="center">

RE-SST-32<br>
XX/XX/2025

</td>

</tr>

</table>

<!-- DATOS BRIGADISTA -->

<table>

<tr>
<td colspan="4" class="titulo">
DATOS DEL BRIGADISTA
</td>
</tr>

<tr>

<td colspan="3">

<table style="border:none;">

<tr>
<td style="border:none;width:200px;" class="label">
FECHA:
</td>

<td style="border:none;">
<input type="date"
name="fecha"
value="<?= oldv('fecha') ?>">
</td>
</tr>

<tr>
<td style="border:none;" class="label">
NOMBRE Y APELLIDOS:
</td>

<td style="border:none;">
<input type="text"
name="nombre"
value="<?= oldv('nombre') ?>">
</td>
</tr>

<tr>
<td style="border:none;" class="label">
LUGAR Y FECHA DE NACIMIENTO:
</td>

<td style="border:none;">
<input type="text"
name="nacimiento"
value="<?= oldv('nacimiento') ?>">
</td>
</tr>

<tr>

<td style="border:none;" class="label">
CEDULA No:
</td>

<td style="border:none;">
<input type="text"
name="cedula"
value="<?= oldv('cedula') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
GRUPO SANGUÍNEO Y RH:
</td>

<td style="border:none;">
<input type="text"
name="rh"
value="<?= oldv('rh') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
ESTATURA:
</td>

<td style="border:none;">
<input type="text"
name="estatura"
value="<?= oldv('estatura') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
PESO:
</td>

<td style="border:none;">
<input type="text"
name="peso"
value="<?= oldv('peso') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
GÉNERO:
</td>

<td style="border:none;">
<input type="text"
name="genero"
value="<?= oldv('genero') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
EPS:
</td>

<td style="border:none;">
<input type="text"
name="eps"
value="<?= oldv('eps') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
ARL:
</td>

<td style="border:none;">
<input type="text"
name="arl"
value="<?= oldv('arl') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
LIMITACIONES FÍSICAS:
</td>

<td style="border:none;">
<input type="text"
name="limitaciones"
value="<?= oldv('limitaciones') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
USA ANTEOJOS:
</td>

<td style="border:none;">
<input type="text"
name="anteojos"
value="<?= oldv('anteojos') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
TELÉFONO:
</td>

<td style="border:none;">
<input type="text"
name="telefono"
value="<?= oldv('telefono') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
CORREO INSTITUCIONAL:
</td>

<td style="border:none;">
<input type="email"
name="correo"
value="<?= oldv('correo') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
N° CELULAR:
</td>

<td style="border:none;">
<input type="text"
name="celular"
value="<?= oldv('celular') ?>">
</td>

</tr>

<tr>

<td style="border:none;" class="label">
N° TEL OFICINA:
</td>

<td style="border:none;">
<input type="text"
name="tel_oficina"
value="<?= oldv('tel_oficina') ?>">
</td>

</tr>

</table>

</td>

<td style="width:200px;">

<div class="foto-box"></div>

</td>

</tr>

</table>

<!-- INFORMACION GENERAL -->

<table>

<tr>
<td colspan="2" class="titulo">
INFORMACIÓN GENERAL
</td>
</tr>

<tr>

<td class="label"
style="width:300px;">
UBICACIÓN EXACTA DONDE LABORA:
</td>

<td>
<input type="text"
name="ubicacion"
value="<?= oldv('ubicacion') ?>">
</td>

</tr>

<tr>

<td class="label">
CARGO:
</td>

<td>
<input type="text"
name="cargo"
value="<?= oldv('cargo') ?>">
</td>

</tr>

<tr>

<td class="label">
TIPO DE VINCULACIÓN:
</td>

<td>
<input type="text"
name="vinculacion"
value="<?= oldv('vinculacion') ?>">
</td>

</tr>

<tr>

<td class="label">
EN CASO DE EMERGENCIA LLAMAR A:
</td>

<td>
<input type="text"
name="emergencia"
value="<?= oldv('emergencia') ?>">
</td>

</tr>

<tr>

<td class="label">
NOMBRE:
</td>

<td>
<input type="text"
name="nombre_contacto"
value="<?= oldv('nombre_contacto') ?>">
</td>

</tr>

<tr>

<td class="label">
TELÉFONO:
</td>

<td>
<input type="text"
name="telefono_contacto"
value="<?= oldv('telefono_contacto') ?>">
</td>

</tr>

</table>

<!-- EXPERIENCIA -->

<table>

<tr>
<td class="titulo">
GRUPOS DE SOCORRO A LOS QUE HA PERTENECIDO Y/U OTRAS BRIGADAS
</td>
</tr>

<tr>
<td>
<textarea
name="experiencia"><?= oldv('experiencia') ?></textarea>
</td>
</tr>

</table>

<!-- BRIGADA -->

<table>

<tr>
<td colspan="2" class="titulo">
EN QUÉ BRIGADA SE QUIERE ESPECIALIZAR
(Marque con una X)
</td>
</tr>

<tr>
<td>CURSO DE PRIMER RESPONDIENTE</td>
<td><input type="text" name="curso"></td>
</tr>

<tr>
<td>PRIMEROS AUXILIOS</td>
<td><input type="text" name="auxilios"></td>
</tr>

<tr>
<td>EVACUACIÓN</td>
<td><input type="text" name="evacuacion"></td>
</tr>

<tr>
<td>CONTRA INCENDIOS</td>
<td><input type="text" name="incendios"></td>
</tr>

<tr>
<td>LÍDER DE BRIGADA</td>
<td><input type="text" name="lider"></td>
</tr>

</table>

<!-- FIRMAS -->

<table>

<tr>

<td style="height:140px;">

<strong>NOMBRE DEL BRIGADISTA:</strong>

<br><br><br><br>

<input type="text"
name="firma_nombre"
value="<?= oldv('firma_nombre') ?>">

</td>

<td style="width:300px;">

<strong>FIRMA:</strong>

</td>

</tr>

</table>

</form>

</div>

</div>

<script>

document.getElementById('btnGuardar')
.addEventListener('click', async function(){

    const btn = this;

    const formData = new FormData(
        document.getElementById('formBrigada')
    );

    const datosJSON = Object.fromEntries(
        formData.entries()
    );

    btn.innerText = 'Guardando...';
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
                'Éxito',
                'Formato guardado correctamente',
                'success'
            );

        }else{

            Swal.fire(
                'Error',
                res.error || 'No se pudo guardar',
                'error'
            );
        }

    }catch(e){

        Swal.fire(
            'Error',
            'Fallo de conexión con el servidor',
            'error'
        );

    }finally{

        btn.innerText = 'Guardar';
        btn.disabled = false;

    }

});

</script>

</body>
</html>