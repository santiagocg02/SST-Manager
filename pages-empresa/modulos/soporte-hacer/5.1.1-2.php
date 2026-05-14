<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];

$empresaId = $_SESSION["id_empresa"] ?? 0;



if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("empresas/$empresaId", "GET", null, $token);
}
?>

<!doctype html>
<html lang="es">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Programas de Gestión</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="../../assets/css/main-style.css">
<link rel="stylesheet" href="../../assets/css/planear.css">

<style>

#soporteDrawer{
    width:min(980px, 92vw);
}

#soporteDrawer iframe{
    width:100%;
    height:100%;
    border:0;
}


.table-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.toolbar-left{
    font-weight:700;
    color:#1a4175;
    font-size:18px;
}

.excel-table{
    border:1px solid #dce3ea;
    border-radius:12px;
    overflow:hidden;
}

.table thead{
    background:#eef3fb;
}

.table thead th{
    font-size:12px;
    color:#1a4175;
    font-weight:700;
    letter-spacing:.5px;
}

.table tbody td{
    vertical-align:middle;
    font-size:14px;
    padding:18px 14px;
}

.item-chip{
    display:flex;
    align-items:center;
    gap:8px;
    font-weight:700;
    color:#0b2f75;
}

.dot{
    width:7px;
    height:7px;
    background:#2f6df6;
    border-radius:50%;
}

.col-item{
    width:140px;
}

.col-soporte{
    width:120px;
}

.btn-icon{
    width:42px;
    height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.planear-hero{
    background:#004176;
    color:#fff;
}

.sheet-title{
    font-weight:700;
    font-size:24px;
}

.sheet-subtitle{
    opacity:.9;
    font-size:14px;
}

</style>

</head>

<body>

<div class="planear-page-scroll">
<div class="page-wrap">

<div class="row g-3 mb-3">

<div class="col-12">

<div class="planear-hero card-soft p-4">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

<div>
    <h4 class="sheet-title">
        <i class="fa-solid fa-layer-group me-2"></i>
       PLAN DE EMERGENCIAS
    </h4>

    <div class="sheet-subtitle">
        Soportes documentales SG-SST
    </div>
</div>

</div>

</div>

</div>

</div>

<div class="row">

<div class="col-12">

<div class="card-soft p-3 bg-white">

<div class="table-toolbar">

<div class="toolbar-left">
    Programas Disponibles
</div>

<div>
    <span class="badge text-bg-primary fs-6" id="countBadge">0</span>
</div>

</div>

<div class="excel-table">

<table class="table table-hover mb-0">

<thead>
<tr>
    <th class="col-item">NÚMERO</th>
    <th>NOMBRE</th>
    <th class="col-soporte text-center">SOPORTE</th>
</tr>
</thead>

<tbody id="body"></tbody>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- DRAWER -->

<div class="offcanvas offcanvas-end"
tabindex="-1"
id="soporteDrawer">

<div class="offcanvas-body p-0 position-relative">

<button type="button"
class="btn-close position-absolute top-0 end-0 m-3 z-3"
data-bs-dismiss="offcanvas">
</button>

<iframe id="frame"></iframe>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

// ITEMS
const items = [

{
numero:"1",
nombre:"Programa de emergencias",
soporte:"7.1.1.php"
},

{
numero:"2",
nombre:"Formato inscripción de brigadas",
soporte:"formbrigada.php"
},

{
numero:"3",
nombre:"Estructura brigadas",
soporte:"estbrigada.php"
},

{
numero:"4",
nombre:"MEDEVAC",
soporte:"medevac.php"
},

{
numero:"5",
nombre:"Registro MEDEVAC",
soporte:"regmedevac.php"
},

{
numero:"6",
nombre:"Inventario de equipos y elementos de primeros auxilios",
soporte:"invequipos.php"
},

{
numero:"7",
nombre:"Entrenamientos realizados",
soporte:"entrealizados.php"
},

{
numero:"8",
nombre:"Lista de chequeo simulacro",
soporte:"cheqsimulacro.php"
},

];

const body = document.getElementById("body");

const drawer = new bootstrap.Offcanvas(
document.getElementById("soporteDrawer")
);

const frame = document.getElementById("frame");

// RENDER
function render(data){

    body.innerHTML = "";

    document.getElementById("countBadge").innerText = data.length;

    data.forEach((r)=>{

        body.innerHTML += `
        <tr>

            <td class="col-item">

                <span class="item-chip">
                    <span class="dot"></span>
                    ${r.numero}
                </span>

            </td>

            <td>
                ${r.nombre}
            </td>

            <td class="text-center">

                <button
                    class="btn btn-outline-primary btn-icon"
                    data-file="${r.soporte}"
                >
                    <i class="fa-regular fa-file-lines"></i>
                </button>

            </td>

        </tr>
        `;
    });
}

// ABRIR SOPORTE
document.addEventListener("click", (e)=>{

    const btn = e.target.closest("button[data-file]");

    if(btn){

      frame.src = `./${btn.dataset.file}`;

        drawer.show();
    }
});

// LIMPIAR
document.getElementById("soporteDrawer")
.addEventListener("hidden.bs.offcanvas", ()=>{

    frame.src = "";

});

// INIT
render(items);

</script>

</body>
</html>