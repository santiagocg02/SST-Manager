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

<title>Procedimiento 7.1.1</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

    /* ===== TOP BAR ===== */
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#dbe3ef;
    padding:12px 20px;
    border-bottom:1px solid #b8c7dc;
}

/* TÍTULO */
.title-left{
    font-size:18px;
    font-weight:600;
    color:#1a4175;
}

/* BOTONES DERECHA */
.actions-right{
    display:flex;
    gap:10px;
}

/* BOTONES */
.btn-top{
    border:none;
    padding:8px 16px;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    color:#fff;
    cursor:pointer;
}

/* COLORES EXACTOS */
.btn-back{
    background:#6c757d;
}

.btn-save{
    background:#198754;
}

.btn-print{
    background:#0d6efd;
}

/* HOVER */
.btn-top:hover{
    opacity:0.9;
}

/* ====== BASE ====== */
:root{
  --primary:#1a4175;
  --soft:#dde7f5;
}

body{
  background:#f4f7f9;
  font-family:'Segoe UI', Arial;
  padding:20px;
  color:#222;
}

.format-container{
  background:#fff;
  max-width:1100px;
  margin:auto;
  border:1px solid #cfd8e3;
  box-shadow:0 0 10px rgba(0,0,0,.08);
}

/* ====== HEADER ====== */
.header-main{
  width:100%;
  border-collapse:collapse;
}

.header-main td{
  border:1px solid #000;
  padding:8px;
  text-align:center;
}

.logo-box img{
  max-height:70px;
}

.title-main{
  font-size:14px;
  font-weight:700;
}

.subtitle-main{
  font-size:13px;
  font-weight:700;
  color:var(--primary);
}

.info-header{
  font-size:11px;
  text-align:left !important;
  padding-left:10px !important;
}

/* ====== CONTENIDO ====== */
.content{
  padding:30px 40px;
}

/* BARRAS */
.section-bar{
  background:var(--primary);
  color:#fff;
  padding:6px 12px;
  font-size:13px;
  font-weight:600;
  margin-top:25px;
}

/* TEXTO */
p, li{
  font-size:13px;
  line-height:1.6;
  text-align:justify;
}

/* ====== TABLAS ====== */
.table-sst{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}

.table-sst th, .table-sst td{
  border:1px solid #000;
  padding:8px;
  font-size:12px;
}

.table-sst th{
  background:var(--soft);
  text-align:center;
  font-weight:600;
}

/* ====== FIRMAS ====== */
.firmas{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
  margin-top:60px;
  text-align:center;
}

.firma-line{
  border-top:1px solid #000;
  margin-top:40px;
  padding-top:8px;
  font-size:11px;
  font-weight:600;
}

/* ====== BOTONES ====== */
.toolbar{
  display:flex;
  justify-content:flex-end;
  gap:10px;
  padding:10px 20px;
}

.btn-custom{
  border:none;
  padding:6px 14px;
  border-radius:6px;
  color:#fff;
  font-size:13px;
}

.btn-back{ background:#6c757d; }
.btn-print{ background:#0d6efd; }

/* ====== PRINT ====== */
@media print{
  .no-print{ display:none; }
  body{ padding:0; background:#fff; }
  .format-container{ border:none; box-shadow:none; }
}

</style>
</head>

<body>

<div class="format-container">

<!-- BOTONES -->
<div class="top-bar no-print">

    <div class="title-left">
        Acciones Preventivas y Correctivas
    </div>

    <div class="actions-right">
        <button onclick="history.back()" class="btn-top btn-back">
            Atrás
        </button>

        <button class="btn-top btn-save" id="btnGuardar">
            Guardar Programa
        </button>

        <button onclick="window.print()" class="btn-top btn-print">
            Imprimir
        </button>
    </div>

</div>
<!-- HEADER -->
<table class="header-main">
<tr>
<td rowspan="3" style="width:180px;">
    <div class="logo-box">
        <?php if ($logoUrl): ?>
            <img src="<?= $logoUrl ?>">
        <?php else: ?>
            LOGO
        <?php endif; ?>
    </div>
</td>

<td rowspan="3">
    <div class="title-main">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
    <div class="subtitle-main">PROCEDIMIENTO DE ACCIONES PREVENTIVAS Y CORRECTIVAS</div>
</td>

<td class="info-header"><strong>Código:</strong> SST-PRO-07-1</td>
</tr>

<tr><td class="info-header"><strong>Versión:</strong> 01</td></tr>
<tr><td class="info-header"><strong>Fecha:</strong> 24/04/2026</td></tr>
</table>

<div class="content">

<div class="section-bar">1. OBJETIVO</div>
<p>
Establecer la identificación de las causas raíz de No Conformidades Reales y definir el mecanismo
para el control y aseguramiento de las mismas, garantizando la mejora continua del SG-SST.
</p>

<div class="section-bar">2. ALCANCE</div>
<p>
Aplica a todas las áreas de la organización donde se identifiquen desviaciones, hallazgos
o incidentes que requieran acciones correctivas o preventivas.
</p>

<div class="section-bar">3. DEFINICIONES</div>
<ul>
<li><strong>Acción Correctiva:</strong> Acción para eliminar la causa de una no conformidad.</li>
<li><strong>Acción Preventiva:</strong> Acción para evitar una no conformidad potencial.</li>
</ul>

<div class="section-bar">4. DESCRIPCIÓN DE ACTIVIDADES</div>
<table class="table-sst">
<thead>
<tr>
<th style="width:25%">ACTIVIDAD</th>
<th>DESCRIPCIÓN</th>
<th style="width:25%">RESPONSABLE</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>Detección</strong></td>
<td>Identificación de desviaciones por auditoría o reporte.</td>
<td>Todo el personal</td>
</tr>

<tr>
<td><strong>Análisis</strong></td>
<td>Investigación de causas (5 porqués, Ishikawa).</td>
<td>Responsable SST</td>
</tr>

<tr>
<td><strong>Plan de acción</strong></td>
<td>Definición de tareas y tiempos de corrección.</td>
<td>Jefe de área</td>
</tr>
</tbody>
</table>

<div class="section-bar">5. CONTROL DE CAMBIOS</div>
<table class="table-sst">
<thead>
<tr>
<th>VERSIÓN</th>
<th>MOTIVO</th>
<th>FECHA</th>
</tr>
</thead>
<tbody>
<tr>
<td>01</td>
<td>Creación inicial</td>
<td>24/04/2026</td>
</tr>
</tbody>
</table>

<!-- FIRMAS -->
<div class="firmas">
<div>
<div class="firma-line">ELABORADO POR<br>Responsable SST</div>
</div>

<div>
<div class="firma-line">REVISADO POR<br>COPASST</div>
</div>

<div>
<div class="firma-line">APROBADO POR<br>Representante Legal</div>
</div>
</div>

</div>
</div>

</body>
</html>