<?php
session_start();

require_once __DIR__ . '/../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();

$token   = $_SESSION["token"] ?? "";
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Estructura Brigada</title>

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
    max-width:1600px;
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
    padding:30px;
    overflow:auto;
}

.organigrama{
    position:relative;
    min-width:1400px;
    min-height:1200px;
}

.linea-h{
    position:absolute;
    background:#356dc4;
    height:6px;
}

.linea-v{
    position:absolute;
    background:#356dc4;
    width:6px;
}

.flecha{
    width:0;
    height:0;
    border-left:14px solid transparent;
    border-right:14px solid transparent;
    border-top:20px solid #356dc4;
    position:absolute;
}

.box{
    position:absolute;
    width:220px;
    text-align:center;
}

.cargo{
    border:1px solid #93a77f;
    background:#fff;
    padding:10px;
    font-weight:700;
    font-size:18px;
    min-height:60px;
}

.nombre{
    border:1px solid #93a77f;
    background:#a9d18e;
    padding:14px;
    margin-top:10px;
}

.nombre input{
    width:100%;
    border:none;
    background:transparent;
    text-align:center;
    font-weight:bold;
}

.foto{
    height:170px;
    border:1px solid #93a77f;
    margin-top:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#aaa;
    font-size:20px;
    background:#fafafa;
}

.grupo-apoyo{
    position:absolute;
    right:40px;
    top:200px;
    width:230px;
}

.grupo-apoyo .contenido{
    border:1px solid #93a77f;
    background:#a9d18e;
    padding:15px;
    line-height:1.6;
    font-weight:700;
    text-align:center;
}

.logo{
    position:absolute;
    top:0;
    left:0;
}

.logo img{
    max-height:80px;
}

@media print{

    .print-hide{
        display:none !important;
    }

    body{
        background:#fff;
        padding:0;
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

<!-- TOOLBAR -->

<div class="toolbar print-hide">

    <h1>
        Estructura Organizacional Brigada de Emergencia
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

<!-- BODY -->

<div class="formulario-body">

<form id="formEstructura">

<div class="organigrama">

<!-- LOGO -->

<div class="logo">
<?php if($logoEmpresaUrl): ?>
    <img src="<?= $logoEmpresaUrl ?>">
<?php endif; ?>
</div>

<!-- LINEAS SUPERIORES -->

<div class="linea-h"
style="top:40px;left:280px;width:800px;">
</div>

<div class="linea-v"
style="top:40px;left:280px;height:250px;">
</div>

<div class="linea-v"
style="top:40px;left:1080px;height:250px;">
</div>

<div class="linea-h"
style="top:290px;left:280px;width:800px;">
</div>

<div class="linea-v"
style="top:140px;left:680px;height:220px;">
</div>

<div class="flecha"
style="top:250px;left:268px;">
</div>

<div class="flecha"
style="top:250px;left:1068px;">
</div>

<div class="flecha"
style="top:340px;left:668px;">
</div>

<!-- COORDINADOR -->

<div class="box"
style="top:0;left:570px;">

<div class="cargo">
Coordinador de Emergencias
</div>

<div class="nombre">
<input type="text"
name="coordinador"
value="<?= oldv('coordinador') ?>"
placeholder="Nombre y Apellido">
</div>

</div>

<!-- SEGURIDAD -->

<div class="box"
style="top:250px;left:170px;">

<div class="cargo">
Seguridad Física
</div>

<div class="nombre">
<input type="text"
name="seguridad"
value="<?= oldv('seguridad') ?>"
placeholder="Nombre y Apellido">
</div>

</div>

<!-- LOGISTICA -->

<div class="box"
style="top:230px;left:970px;">

<div class="cargo">
Enlace - Logística
</div>

<div class="nombre">
<input type="text"
name="logistica"
value="<?= oldv('logistica') ?>"
placeholder="Nombre y Apellido">
</div>

</div>

<!-- JEFE BRIGADA -->

<div class="box"
style="top:420px;left:570px;">

<div class="foto">
FOTO
</div>

<div class="cargo">
Jefe de Brigada
</div>

<div class="nombre">
<input type="text"
name="jefe"
value="<?= oldv('jefe') ?>"
placeholder="Nombre y Apellido">
</div>

</div>

<!-- LINEAS INFERIORES -->

<div class="linea-v"
style="top:760px;left:680px;height:90px;">
</div>

<div class="flecha"
style="top:830px;left:668px;">
</div>

<!-- AUXILIOS -->

<div class="box"
style="top:900px;left:170px;">

<div class="cargo"
style="font-size:16px;">
Líder de Primeros Auxilios
</div>

<div class="nombre">
<input type="text"
name="auxilios"
value="<?= oldv('auxilios') ?>"
placeholder="Nombre y Apellido">
</div>

<div class="foto">
FOTO
</div>

</div>

<!-- INCENDIOS -->

<div class="box"
style="top:900px;left:570px;">

<div class="cargo"
style="font-size:16px;">
Líder de Control de Incendios
</div>

<div class="nombre">
<input type="text"
name="incendios"
value="<?= oldv('incendios') ?>"
placeholder="Nombre y Apellido">
</div>

<div class="foto">
FOTO
</div>

</div>

<!-- EVACUACION -->

<div class="box"
style="top:900px;left:970px;">

<div class="cargo"
style="font-size:16px;">
Líder de Evacuación y Rescate
</div>

<div class="nombre">
<input type="text"
name="evacuacion"
value="<?= oldv('evacuacion') ?>"
placeholder="Nombre y Apellido">
</div>

<div class="foto">
FOTO
</div>

</div>

<!-- APOYO EXTERNO -->

<div class="grupo-apoyo">

<div class="cargo">
Grupos de Apoyo Externo
</div>

<div class="contenido">

<textarea
name="apoyo_externo"
style="width:100%;
height:260px;
border:none;
background:transparent;
resize:none;
text-align:center;
font-weight:bold;"><?= oldv('apoyo_externo',
'Policía Metropolitana
DIJIN
Secretaría Distrital de Salud
Fiscalía
CTI
Secretaría de Tránsito
IDIGER
Gas Natural
Hospitales
Cruz Roja
Defensa Civil
Bomberos') ?></textarea>

</div>

</div>

</div>

</form>

</div>

</div>

<script>

document.getElementById('btnGuardar')
.addEventListener('click', async function(){

    const btn = this;

    const formData = new FormData(
        document.getElementById('formEstructura')
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
                'Estructura guardada correctamente',
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