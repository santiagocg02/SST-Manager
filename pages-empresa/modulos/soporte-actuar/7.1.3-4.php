<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 54;

$logoUrl = "";
if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresaId", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>7.1.3 Matriz</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

:root { --primary:#1a4175; --bg:#dde7f5; }

body{
  background:#f4f7f9;
  font-family:Segoe UI;
  padding:20px;
}

.format-container{
  background:#fff;
  max-width:1400px;
  margin:auto;
  border:1px solid #ccc;
  box-shadow:0 0 10px rgba(0,0,0,.1);
}

/* HEADER */
.toolbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:5px;
  background:var(--bg);
}

.toolbar h1{
  font-size:20px;
  color:var(--primary);
}

/* BOTONES */
.btn{
  padding:8px 15px;
  border:none;
  border-radius:6px;
  color:#fff;
  cursor:pointer;
}

.btn-save{ background:#198754; }
.btn-print{ background:#0d6efd; }
.btn-back{ background:#6c757d; }

/* CONTADORES */
.box{
  width:45px;
  height:28px;
  text-align:center;
  font-weight:bold;
  font-size:14px;
  border:2px solid;
}

.abierta{ color:red; border-color:red; }
.cerrada{ color:#2e7d32; border-color:#2e7d32; }

/* TABLA */
table{
  width:100%;
  border-collapse:collapse;
}

th, td{
  border:1px solid #000;
  padding:8px;
  font-size:12px;
  vertical-align:middle;
}

th{
  background:#dde7f5;
}

/* AJUSTE COLUMNA STATUS */
#tablaMatriz th:nth-child(8),
#tablaMatriz td:nth-child(8){
    width:120px;
    text-align:center;
}

/* INPUTS */
input, select, textarea{
  width:100%;
  border:none;
  outline:none;
  background:transparent;
  font-size:12px;
}

textarea{
  min-height:50px;
}

/* SELECT STATUS PRO */
.status-selector{
    width:100%;
    text-align:center;
    font-weight:600;
    padding:4px;
    border:1px solid #ccc;
    border-radius:4px;
}

/* COLORES STATUS */
.status-abierta{
    background:#fdecea !important;
    color:#c62828 !important;
    font-weight:600;
}

.status-cerrada{
    background:#e8f5e9 !important;
    color:#2e7d32 !important;
    font-weight:600;
}

/* PRINT */
@media print{
  .no-print{ display:none; }
  body{ padding:0; }
}

</style>
</head>

<body>

<div class="format-container">

<div class="toolbar no-print">
  <h1>Matriz de Seguimiento</h1>

  <div>
    <button class="btn btn-back" onclick="history.back()">Atrás</button>
    <button class="btn btn-save" id="btnGuardar">Guardar</button>
    <button class="btn btn-print" onclick="window.print()">Imprimir</button>
  </div>
</div>

<form id="formMatriz">

<table>
<tr>
<td rowspan="2" style="width:180px;text-align:center;">
<?php if($logoUrl): ?>
<img src="<?= $logoUrl ?>" style="max-height:60px;">
<?php else: ?>LOGO<?php endif; ?>
</td>

<td style="text-align:center;font-weight:bold;">
SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO
</td>

<td style="width:120px;text-align:center;">0</td>
</tr>

<tr>
<td style="text-align:center;font-weight:bold;">
MATRIZ DE HALLAZGOS Y ACCIONES
</td>

<td style="text-align:center;">AN-SST-29<br>2026</td>
</tr>
</table>

<!-- CONTADORES -->
<table style="width:auto; margin-left:auto; margin-top:10px;">
<tr>
<td style="border:none;font-weight:bold;">ABIERTAS</td>
<td id="abiertas" class="box abierta">0</td>
</tr>
<tr>
<td style="border:none;font-weight:bold;">CERRADAS</td>
<td id="cerradas" class="box cerrada">0</td>
</tr>
</table>

<!-- TABLA -->
<table id="tablaMatriz">
<thead>
<tr>
<th>ACPM</th>
<th>FUENTE</th>
<th>DESCRIPCIÓN</th>
<th>ACCIÓN</th>
<th>RESPONSABLE</th>
<th>FECHA</th>
<th>SEGUIMIENTO</th>
<th>STATUS</th>
<th>CIERRE</th>
</tr>
</thead>

<tbody id="tbodyMatriz">

<tr>
<td><input name="acpm_1"></td>

<td>
<select name="fuente_1">
<option value="">-</option>
<option>CONDICIÓN INSEGURA</option>
<option>ARL</option>
</select>
</td>

<td><textarea name="desc_1"></textarea></td>
<td><textarea name="accion_1"></textarea></td>
<td><input name="resp_1"></td>
<td><input type="date" name="fecha_1"></td>
<td><textarea name="seg_1"></textarea></td>

<td>
<select name="status_1" class="status-selector status" onchange="estado(this)">
<option value="">-</option>
<option value="ABIERTA">ABIERTA</option>
<option value="CERRADA">CERRADA</option>
</select>
</td>

<td><input name="cierre_1"></td>
</tr>

</tbody>
</table>

<div class="no-print" style="padding:10px;">
<button type="button" class="btn btn-save" onclick="agregarFila()">
<i class="fa fa-plus"></i> Agregar fila
</button>
</div>

</form>
</div>

<script>

let i=1;

function agregarFila(){
i++;

let fila=`
<tr>
<td><input name="acpm_${i}"></td>
<td>
<select name="fuente_${i}">
<option>-</option>
<option>CONDICIÓN INSEGURA</option>
<option>ARL</option>
</select>
</td>
<td><textarea name="desc_${i}"></textarea></td>
<td><textarea name="accion_${i}"></textarea></td>
<td><input name="resp_${i}"></td>
<td><input type="date" name="fecha_${i}"></td>
<td><textarea name="seg_${i}"></textarea></td>

<td>
<select name="status_${i}" class="status-selector status" onchange="estado(this)">
<option>-</option>
<option value="ABIERTA">ABIERTA</option>
<option value="CERRADA">CERRADA</option>
</select>
</td>

<td><input name="cierre_${i}"></td>
</tr>
`;

document.getElementById("tbodyMatriz").insertAdjacentHTML("beforeend",fila);
}

function estado(el){
const td = el.parentElement;

td.classList.remove("status-abierta","status-cerrada");

if(el.value === "ABIERTA") td.classList.add("status-abierta");
if(el.value === "CERRADA") td.classList.add("status-cerrada");

contar();
}

function contar(){
let a=0,c=0;

document.querySelectorAll(".status").forEach(e=>{
if(e.value==="ABIERTA") a++;
if(e.value==="CERRADA") c++;
});

document.getElementById("abiertas").innerText=a;
document.getElementById("cerradas").innerText=c;
}

document.getElementById("btnGuardar").onclick=async()=>{
let data=Object.fromEntries(new FormData(formMatriz));

await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar",{
method:"POST",
headers:{
"Content-Type":"application/json",
"Authorization":"Bearer <?= $token ?>"
},
body:JSON.stringify({
id_empresa:<?= $empresaId ?>,
id_item_sst:<?= $idItem ?>,
datos:data
})
});

Swal.fire("Guardado","","success");
};

</script>

</body>
</html>