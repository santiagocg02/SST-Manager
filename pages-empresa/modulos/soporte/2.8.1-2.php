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

<title>2.8.1-2 Reporte de actos y condiciones inseguras</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f2f6fb;
font-family:Arial;
}

.page{
max-width:1200px;
margin:auto;
background:white;
padding:20px;
box-shadow:0 10px 25px rgba(0,0,0,.1);
}

.table-sst{
width:100%;
border-collapse:collapse;
}

.table-sst td,.table-sst th{
border:1px solid #111;
padding:6px;
font-size:13px;
}

.header-blue{
background:#1f5fa8;
color:white;
font-weight:bold;
text-align:center;
}

input[type=text]{
width:100%;
border:none;
outline:none;
}

textarea{
width:100%;
border:none;
outline:none;
resize:none;
}

.checkbox{
width:18px;
height:18px;
}

.logo{
height:80px;
display:flex;
align-items:center;
justify-content:center;
border:1px dashed #aaa;
}

.level-alto{background:#ff3b3b;color:white;font-weight:bold;text-align:center}
.level-medio{background:#ffd700;font-weight:bold;text-align:center}
.level-bajo{background:#2ecc71;color:white;font-weight:bold;text-align:center}

</style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>

<div class="page">

<div class="mb-3">
<a href="../planear.php" class="btn btn-outline-primary">← Volver</a>
<button onclick="window.print()" class="btn btn-primary">Imprimir</button>
</div>

<table class="table-sst">

<tr>
<td rowspan="2" style="width:120px"><div class="logo">LOGO</div></td>
<td colspan="3" style="text-align:center;font-weight:bold">
SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
</td>
<td style="width:80px;text-align:center">0</td>
</tr>

<tr>
<td colspan="3" style="font-weight:bold">
FORMATO PARA EL REPORTE DE ACTOS Y CONDICIONES INSEGURAS Y AUTOREPORTE CONDICIONES EN SALUD
</td>
<td style="text-align:center">RE-SST-26</td>
</tr>

<tr class="header-blue">
<td colspan="5">I. IDENTIFICACIÓN</td>
</tr>

<tr>
<td>Fecha</td>
<td colspan="2"><input type="text"></td>
<td>Consecutivo</td>
<td><input type="text"></td>
</tr>

<tr>
<td>Nombre de quien reporta</td>
<td colspan="4"><input type="text"></td>
</tr>

<tr>
<td>Funcionario <input type="checkbox" class="checkbox"></td>
<td>Contratista <input type="checkbox" class="checkbox"></td>
<td>Visitante <input type="checkbox" class="checkbox"></td>
<td>Otro</td>
<td><input type="text"></td>
</tr>

<tr class="header-blue">
<td colspan="5">II. DESCRIPCIÓN DEL REPORTE</td>
</tr>

<tr>
<td colspan="5">
<textarea rows="4"></textarea>
</td>
</tr>

<tr class="header-blue">
<td colspan="5">III. ACTOS INSEGUROS</td>
</tr>

<tr>
<td colspan="4">No uso o uso inapropiado de elementos de protección personal</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Realizar labores de mantenimiento sin señalizar</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Realizar labores de aseo y limpieza sin señalizar</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Hacer bromas o juegos pesados</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Agarrar o manipular objetos de forma insegura</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Usar las manos en lugar de herramientas</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Errores en conducción de vehículos</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Trabajar en alturas sin elementos adecuados</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Otro</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="5">¿Cuál? <input type="text"></td>
</tr>

<tr class="header-blue">
<td colspan="5">IV. CONDICIONES INSEGURAS</td>
</tr>

<tr>
<td colspan="4">Áreas sin señalización de emergencia</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Ruido excesivo</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Espacios inadecuados de circulación</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Ventilación inadecuada</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Iluminación deficiente</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Materiales almacenados incorrectamente</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Techos en mal estado</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="4">Otro</td>
<td><input type="checkbox" class="checkbox"></td>
</tr>

<tr>
<td colspan="5">¿Cuál? <input type="text"></td>
</tr>

<tr class="header-blue">
<td colspan="5">V. AUTOREPORTE CONDICIONES DE SALUD</td>
</tr>

<tr>
<td colspan="2">Nervioso</td>
<td><input type="checkbox"></td>
<td>Cardiovascular</td>
<td><input type="checkbox"></td>
</tr>

<tr>
<td colspan="2">Osteomuscular</td>
<td><input type="checkbox"></td>
<td>Digestivo</td>
<td><input type="checkbox"></td>
</tr>

<tr>
<td colspan="2">Tegumentario</td>
<td><input type="checkbox"></td>
<td>Respiratorio</td>
<td><input type="checkbox"></td>
</tr>

<tr>
<td colspan="5">
Diagnóstico o sintomatología
<textarea rows="3"></textarea>
</td>
</tr>

<tr class="header-blue">
<td colspan="5">VI. RIESGO ASOCIADO</td>
</tr>

<tr>
<td>Físicos</td>
<td><input type="checkbox"></td>
<td>Psicosociales</td>
<td><input type="checkbox"></td>
<td></td>
</tr>

<tr>
<td>Químicos</td>
<td><input type="checkbox"></td>
<td>Naturales</td>
<td><input type="checkbox"></td>
<td></td>
</tr>

<tr>
<td>Biológicos</td>
<td><input type="checkbox"></td>
<td>Condiciones de seguridad</td>
<td><input type="checkbox"></td>
<td></td>
</tr>

<tr>
<td colspan="5">Nivel de criticidad</td>
</tr>

<tr>
<td class="level-alto">ALTO</td>
<td class="level-medio">MEDIO</td>
<td class="level-bajo">BAJO</td>
<td colspan="2"></td>
</tr>

<tr class="header-blue">
<td colspan="5">VII. ACCIONES PROPUESTAS</td>
</tr>

<tr>
<td colspan="5">
<textarea rows="4"></textarea>
</td>
</tr>

<tr class="header-blue">
<td colspan="5">VIII. SOPORTES DE CIERRE</td>
</tr>

<tr>
<td>Fotografías</td>
<td><input type="checkbox"></td>
<td>Informe</td>
<td><input type="checkbox"></td>
<td>Otros <input type="text"></td>
</tr>

</table>

</div>

</body>
</html>