<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

// LOGO DINÁMICO
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
    <title>SST Manager - Procedimiento 7.1.1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary-color: #1a4175; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
        .format-container { background: #fff; border-radius: 4px; box-shadow: 0 0 15px rgba(0,0,0,0.05); max-width: 1000px; margin: auto; padding: 0; border: 1px solid #dee2e6; }
        
        /* ENCABEZADO SEGÚN IMAGEN */
        .header-main { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .header-main td { border: 1px solid #000; padding: 10px; text-align: center; vertical-align: middle; }
        .logo-box img { max-height: 65px; max-width: 100%; object-fit: contain; }
        .title-main { font-weight: bold; font-size: 14px; margin: 0; text-transform: uppercase; }
        .subtitle-main { font-weight: bold; font-size: 13px; color: var(--primary-color); margin: 5px 0 0 0; text-transform: uppercase; }
        .info-header { text-align: left !important; font-size: 11px; width: 220px; padding-left: 15px !important; }

        /* SECCIONES */
        .content-padding { padding: 30px 40px; }
        .section-bar { background-color: var(--primary-color); color: white; padding: 6px 15px; font-weight: bold; margin: 25px 0 12px 0; border-radius: 2px; font-size: 13px; }
        p, li { font-size: 13px; line-height: 1.6; color: #333; text-align: justify; }
        
        /* TABLAS */
        .table-custom { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #000; }
        .table-custom th, .table-custom td { border: 1px solid #000; padding: 10px; font-size: 12px; }
        .table-custom th { background-color: #f2f2f2; font-weight: bold; text-align: center; }

        /* FIRMAS */
        .signature-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 60px; text-align: center; }
        .sig-line { border-top: 1px solid #000; padding-top: 8px; font-size: 11px; font-weight: bold; }

        @media print {
            body { background: white; padding: 0; }
            .format-container { box-shadow: none; border: none; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="format-container">
    <table class="header-main">
        <tr>
            <td rowspan="3" style="width: 180px;">
                <div class="logo-box">
                    <?php if ($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" alt="Logo">
                    <?php else: ?>
                        <span style="font-size: 10px; color: #999;">ESPACIO PARA LOGO</span>
                    <?php endif; ?>
                </div>
            </td>
            <td rowspan="3">
                <h1 class="title-main">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</h1>
                <h2 class="subtitle-main">PROCEDIMIENTO DE ACCIONES PREVENTIVAS Y CORRECTIVAS</h2>
            </td>
            <td class="info-header"><strong>Código:</strong> SST-PRO-07-1</td>
        </tr>
        <tr><td class="info-header"><strong>Versión:</strong> 01</td></tr>
        <tr><td class="info-header"><strong>Fecha:</strong> 24/04/2026</td></tr>
    </table>

    <div class="content-padding">
        <div class="section-bar">1. OBJETIVO</div>
        <p>Establecer la identificación de las causas raíz de No Conformidades Reales y definir el mecanismo para el control y aseguramiento de las mismas según el estándar cuando estas se presenten en la empresa, garantizando la mejora continua del SG-SST.</p>

        <div class="section-bar">2. ALCANCE</div>
        <p>Este procedimiento aplica a todas las áreas y procesos de la organización donde se identifiquen desviaciones, hallazgos de auditoría o incidentes que requieran tratamiento preventivo o correctivo.</p>

        <div class="section-bar">3. DEFINICIONES</div>
        <ul>
            <li><strong>Acción Correctiva:</strong> Acción para eliminar la causa de una no conformidad.</li>
            <li><strong>Acción Preventiva:</strong> Acción para eliminar la causa de una no conformidad potencial.</li>
        </ul>

        <div class="section-bar">4. DESCRIPCIÓN DE ACTIVIDADES</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 25%;">ACTIVIDAD</th>
                    <th>DESCRIPCIÓN</th>
                    <th style="width: 25%;">RESPONSABLE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Detección</strong></td>
                    <td>Identificación de la desviación por cualquier medio (auditoría, reporte, etc.)</td>
                    <td>Todo el personal</td>
                </tr>
                <tr>
                    <td><strong>Análisis</strong></td>
                    <td>Investigación de causas mediante "5 Por qués" o Espina de Pescado.</td>
                    <td>Responsable SST</td>
                </tr>
                <tr>
                    <td><strong>Plan de Acción</strong></td>
                    <td>Definición de tareas, tiempos y recursos para la corrección.</td>
                    <td>Jefe de Área</td>
                </tr>
            </tbody>
        </table>

        <div class="section-bar">5. CONTROL DE CAMBIOS</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>VERSIÓN</th>
                    <th>MOTIVO DEL CAMBIO</th>
                    <th>FECHA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01</td>
                    <td>Creación inicial del documento.</td>
                    <td>24/04/2026</td>
                </tr>
            </tbody>
        </table>

        <div class="signature-grid">
            <div class="sig-box">
                <div class="sig-line">ELABORADO POR:<br>Responsable SST</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">REVISADO POR:<br>Asesor SST / COPASST</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">APROBADO POR:<br>Representante Legal</div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-4 mb-5 no-print">
    <button onclick="window.print()" class="btn btn-primary px-4">
        <i class="fa-solid fa-print me-2"></i> Imprimir Documento
    </button>
</div>

</body>
</html>