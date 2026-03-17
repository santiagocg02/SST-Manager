<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}
?>

<!doctype html>
<html lang="es">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>2.11.1-2 Planificación del Cambio</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f7fb;
}

.documento{

background:white;
padding:20px;
border:1px solid #ccc;

}

.header-table{

width:100%;
border-collapse:collapse;

}

.header-table td{

border:1px solid black;
padding:6px;
text-align:center;
font-weight:600;

}

.tabla-cambios{

width:2000px;
border-collapse:collapse;

}

.tabla-cambios th,
.tabla-cambios td{

border:1px solid black;
padding:4px;
font-size:12px;
text-align:center;

}

.tabla-cambios input,
.tabla-cambios textarea{

width:100%;
border:none;
outline:none;
font-size:12px;

}

.scroll{

overflow-x:auto;

}

textarea{

resize:none;
height:60px;

}

.btn-area{

margin-bottom:15px;

}

</style>

</head>

<body>

<div class="container-fluid mt-3">

<div class="btn-area">

<button onclick="window.print()" class="btn btn-primary">
Imprimir
</button>

<a href="../planear.php" class="btn btn-secondary">
Volver
</a>

<button onclick="agregarFila()" class="btn btn-success">
Agregar fila
</button>

</div>

<div class="documento">

<table class="header-table">

<tr>

<td colspan="12">
SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
</td>

<td>0</td>

</tr>

<tr>

<td colspan="12">
PLANIFICACIÓN DEL CAMBIO
</td>

<td>RE-SST-03</td>

</tr>

<tr>

<td colspan="12"></td>

<td>XX/XX/2025</td>

</tr>

</table>

<div class="scroll mt-3">

<table class="tabla-cambios">

<thead>

<tr>

<th>FECHA</th>

<th>AREA RELACIONADA</th>

<th>SOLICITADO POR</th>

<th>I</th>
<th>RQ</th>
<th>PR</th>
<th>PER</th>
<th>CONTR</th>
<th>PSV</th>
<th>SG</th>
<th>AC</th>
<th>OTRO</th>
<th>CUAL</th>

<th>DESCRIPCION DEL CAMBIO</th>

<th colspan="3">GERENCIA</th>

<th colspan="3">ENCARGADO</th>

<th>RESPONSABLE / CARGO</th>

<th>PLAN DE ACCION</th>

<th>FECHA</th>

</tr>

<tr>

<th></th>
<th></th>
<th></th>

<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>

<th></th>

<th>SI</th>
<th>NO</th>
<th>FECHA</th>

<th>SI</th>
<th>NO</th>
<th>FECHA</th>

<th></th>
<th></th>
<th></th>

</tr>

</thead>

<tbody id="tabla-body">

<tr>

<td><input type="date"></td>

<td><input type="text"></td>

<td><input type="text"></td>

<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="checkbox"></td>

<td><input type="text"></td>

<td><textarea></textarea></td>

<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="date"></td>

<td><input type="checkbox"></td>
<td><input type="checkbox"></td>
<td><input type="date"></td>

<td><input type="text"></td>

<td><textarea></textarea></td>

<td><input type="date"></td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

<script>

function agregarFila(){

let fila = document.querySelector("#tabla-body tr").cloneNode(true)

document.getElementById("tabla-body").appendChild(fila)

}

</script>

</body>
</html>