<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$bloque1 = [
    "Certificación expedida por su ARL en donde se evidencie el porcentaje de avance del SG SST, según la última revisión realizada y el nivel de riesgo al cual se encuentra expuesto",
    "Todo el personal de contratistas sin excepción, deben acreditar haber recibido inducción en temas SST de la EMPRESA"
];

$bloque2 = [
    "El Contratista deberá conocer, entender, comunicar y cumplir con la política de SST de la EMPRESA, en caso de subcontratación, deberá hacerla conocer y cumplir a sus Subcontratistas",
    "Conocer y cumplir la Política de Prevención de Consumo de No Alcohol, Tabaco y Sustancias Psicoactivas",
    "Presentar Matriz de Peligros, con la identificación de peligros, valoración de riesgos y determinación de controles para la ejecución del objeto del contrato en el cual debe contemplar a los subcontratistas",
    "Se cuenta con el listado de nombres y números de cédula de trabajadores del contratista y sus subcontratistas",
    "Exámenes de ingreso periódicos y egreso.",
    "El contratista notifica al Supervisor del contrato o de la orden contractual cada vez que se presentan cambios con el personal relacionado",
    "El contratista realiza para sus trabajadores y exige para sus subcontratistas los pagos correspondientes de seguridad social tal y como lo exige la ley.",
    "Todas las personas que realizan actividades para la empresa contratista portan el carné de afiliación a EPS, ARL (si está afiliado), cédula de ciudadanía y carné de identificación de la empresa contratista a la que pertenece",
    "El contratista entrega y controla el uso de ropa adecuada y EPP al personal según la actividad y peligros a que estarán expuestos sus trabajadores y subcontratistas",
    "Los EPP cumplen con las normas técnicas NTC, NIOSH (para equipos de protección respiratoria) y ANSI (para los demás equipos de protección personal) y demás exigidas por la legislación colombiana",
    "El contratista inspecciona y mantiene el inventario suficiente de EPP para reemplazarlos en caso de deterioro o pérdida",
    "En caso de accidente de trabajo el contratista posee un procedimiento para garantizar el traslado y la atención inmediata del accidentado",
    "El contratista realiza y mantiene actualizadas las estadísticas de accidentes que se produzcan en el desarrollo de sus actividades",
    "Las estadísticas de accidente contemplan como mínimo:\n- Número de accidentes ocurridos en el mes.\n- Días de incapacidad por ocurridos en el mes.\n- Tipo de accidente (caídas, golpes, etc).\n- Causas de los accidentes.\n- Medidas correctivas tomadas",
    "Si no se presentan accidentes, el Contratista lo certifica",
    "El contratista realiza investigación de los accidentes y genera acciones para atacar las causas básicas y evitar que estos se repitan",
    "El contratista mantiene equipos de emergencias como extintores, gabinetes contra incendio, botiquín, entre otros. ¿Libres de obstáculos?",
    "En caso de emergencia acatan las orientaciones dadas por el funcionario de seguridad y la señalización de emergencia",
    "El contratista realiza capacitaciones y entrenamientos para evitar accidentes y enfermedades profesionales para sus trabajadores y subcontratistas"
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RE-SST-15 | Lista de chequeo para verificación de requerimientos generales del SG-SST para persona jurídicas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --page-bg:#f3f6fa;
            --paper:#ffffff;
            --line:#b9c4cf;
            --line-dark:#8796a5;
            --head:#eef3f8;
            --blue:#93add4;
            --blue-dark:#2f5f8d;
            --text:#1f2937;
            --muted:#6b7280;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--page-bg);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .page-wrap{
            padding:20px;
        }

        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:16px;
        }

        .topbar-left,
        .topbar-right{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn-ui{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:9px 16px;
            border-radius:10px;
            border:1px solid var(--btn);
            background:var(--btn);
            color:#fff;
            text-decoration:none;
            font-size:14px;
            font-weight:700;
            transition:.2s ease;
            cursor:pointer;
            box-shadow:0 4px 14px rgba(13,110,253,.15);
        }

        .btn-ui:hover{
            background:var(--btn-hover);
            border-color:var(--btn-hover);
            color:#fff;
        }

        .btn-ui.secondary{
            background:#fff;
            color:var(--btn);
        }

        .btn-ui.secondary:hover{
            background:#eef5ff;
            color:var(--btn-hover);
        }

        .badge-format{
            font-size:12px;
            color:var(--muted);
            background:#fff;
            border:1px solid #d8dee6;
            padding:7px 12px;
            border-radius:999px;
            font-weight:700;
        }

        .sheet-card{
            background:var(--paper);
            border:1px solid #d7dee6;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 12px 28px rgba(31,41,55,.08);
        }

        .sheet-header{
            padding:14px 18px;
            background:linear-gradient(135deg, #f8fbff 0%, #eef4fb 100%);
            border-bottom:1px solid #dde6ef;
        }

        .sheet-header-title{
            margin:0;
            font-size:16px;
            font-weight:800;
            color:var(--blue-dark);
        }

        .sheet-header-subtitle{
            margin:4px 0 0;
            font-size:12px;
            color:var(--muted);
        }

        .sheet-scroll{
            width:100%;
            overflow:auto;
            background:#fff;
        }

        .sheet{
            min-width:1180px;
            background:#fff;
        }

        table.form-sheet{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .form-sheet th,
        .form-sheet td{
            border:1px solid var(--line);
            padding:0;
            vertical-align:middle;
        }

        .top-title{
            background:var(--head);
            text-align:center;
            font-weight:800;
            font-size:15px;
            line-height:1.2;
            text-transform:uppercase;
            padding:12px 14px !important;
        }

        .top-subtitle{
            background:var(--head);
            text-align:center;
            font-weight:700;
            font-size:13px;
            line-height:1.25;
            text-transform:uppercase;
            padding:12px 14px !important;
        }

        .top-cell{
            background:var(--head);
            text-align:center;
            font-weight:700;
            font-size:12px;
            padding:8px 10px !important;
            height:34px;
        }

        .logo-box{
            background:var(--head);
            text-align:center;
            color:#b6bcc3;
            font-weight:800;
            height:122px;
        }

        .logo-inner{
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .logo-placeholder{
            border:2px dashed #c9d1d9;
            padding:12px 16px;
            line-height:1.05;
            font-size:15px;
        }

        .info-label{
            background:#fbfcfd;
            font-size:12px;
            font-weight:700;
            padding:7px 10px !important;
        }

        .info-field{
            background:#fff;
        }

        .info-field input{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:13px;
            padding:7px 10px;
        }

        .instruction{
            background:#fff;
            padding:14px !important;
            font-size:13px;
            line-height:1.4;
        }

        .table-head{
            background:var(--blue);
            color:#111827;
            font-weight:800;
            font-size:13px;
            text-align:center;
            padding:8px 6px !important;
        }

        .table-head-sub{
            background:var(--blue);
            color:#111827;
            font-weight:800;
            font-size:12px;
            text-align:center;
            padding:6px !important;
        }

        .num-cell{
            background:#fbfcfd;
            text-align:center;
            font-weight:800;
            font-size:13px;
            padding:10px 6px !important;
        }

        .req-cell{
            background:#fff;
            font-size:13px;
            line-height:1.35;
            padding:10px !important;
            white-space:pre-line;
        }

        .check-cell{
            background:#fff;
            text-align:center;
            padding:8px 4px !important;
        }

        .check-cell input[type="radio"]{
            transform:scale(1.15);
            cursor:pointer;
        }

        .obs-cell{
            background:#fff;
        }

        .obs-cell textarea{
            width:100%;
            min-height:54px;
            border:none;
            outline:none;
            resize:none;
            background:transparent;
            padding:10px;
            font-size:13px;
            line-height:1.35;
        }

        .signature-label{
            padding:10px !important;
            font-size:13px;
            font-weight:700;
            background:#fff;
        }

        .signature-line{
            height:42px;
            background:#fff;
        }

        .footer-help{
            padding:12px 16px;
            border-top:1px solid #e3e8ee;
            background:#fafcff;
            font-size:12px;
            color:var(--muted);
        }

        @media (max-width:768px){
            .page-wrap{ padding:10px; }
            .sheet-header-title{ font-size:14px; }
            .top-title{ font-size:13px; }
            .top-subtitle{ font-size:12px; }
        }

        @media print{
            @page{
                size:portrait;
                margin:10mm;
            }

            body{
                background:#fff !important;
            }

            .page-wrap{
                padding:0 !important;
            }

            .topbar,
            .sheet-header,
            .footer-help{
                display:none !important;
            }

            .sheet-card{
                border:none !important;
                border-radius:0 !important;
                box-shadow:none !important;
            }

            .sheet-scroll{
                overflow:visible !important;
            }

            .sheet{
                min-width:100% !important;
            }
        }
    </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="page-wrap">
    <div class="topbar">
        <div class="topbar-left">
            <a href="../planear.php" class="btn-ui">← Volver a Planear</a>
            <button type="button" class="btn-ui secondary" onclick="window.print()">🖨 Imprimir</button>
        </div>
        <div class="topbar-right">
            <span class="badge-format">Formato 2.10.1-2 · RE-SST-15</span>
        </div>
    </div>

    <div class="sheet-card">
        <div class="sheet-header">
            <h1 class="sheet-header-title">Lista de chequeo para verificación de requerimientos generales del SG-SST para persona jurídicas</h1>
            <p class="sheet-header-subtitle">Formato editable con presentación profesional para el módulo Planear</p>
        </div>

        <div class="sheet-scroll">
            <div class="sheet">
                <table class="form-sheet">
                    <colgroup>
                        <col style="width:170px">
                        <col style="width:430px">
                        <col style="width:85px">
                        <col style="width:85px">
                        <col style="width:85px">
                        <col style="width:290px">
                    </colgroup>

                    <tr>
                        <td rowspan="3" colspan="2" class="logo-box">
                            <div class="logo-inner">
                                <div class="logo-placeholder">TU LOGO<br>AQUÍ</div>
                            </div>
                        </td>
                        <td colspan="3" class="top-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                        <td class="top-cell">0</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="top-subtitle">LISTA DE CHEQUEO PARA VERIFICACION DE REQUERIMIENTOS GENERALES DEL SG-SST PARA PERSONA JURIDICAS</td>
                        <td class="top-cell">RE-SST-15</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="top-cell">&nbsp;</td>
                        <td class="top-cell">XX/XX/2025</td>
                    </tr>

                    <tr>
                        <td class="info-label">Fecha:</td>
                        <td class="info-field" colspan="2"><input type="text" name="fecha"></td>
                        <td class="info-label">&nbsp;</td>
                        <td class="info-label">&nbsp;</td>
                        <td class="info-field"><input type="text"></td>
                    </tr>
                    <tr>
                        <td class="info-label">Nombre del contratista</td>
                        <td class="info-field" colspan="2"><input type="text" name="contratista"></td>
                        <td class="info-label">Nit:</td>
                        <td class="info-field" colspan="2"><input type="text" name="nit"></td>
                    </tr>
                    <tr>
                        <td class="info-label">Nombre del Supervisor</td>
                        <td class="info-field" colspan="2"><input type="text" name="supervisor"></td>
                        <td class="info-label">CC</td>
                        <td class="info-field" colspan="2"><input type="text" name="cc"></td>
                    </tr>

                    <tr>
                        <td colspan="6" class="instruction">1. Documentos que el proponente debe entregar con la propuesta.</td>
                    </tr>

                    <tr>
                        <th class="table-head" rowspan="2">No</th>
                        <th class="table-head" rowspan="2">REQUERIMIENTO</th>
                        <th class="table-head" colspan="3">CUMPLE</th>
                        <th class="table-head" rowspan="2">OBSERVACIONES</th>
                    </tr>
                    <tr>
                        <th class="table-head-sub">SI</th>
                        <th class="table-head-sub">NO</th>
                        <th class="table-head-sub">N/A</th>
                    </tr>

                    <?php foreach($bloque1 as $i => $req): $n = $i + 1; ?>
                    <tr>
                        <td class="num-cell"><?= $n ?></td>
                        <td class="req-cell"><?= e($req) ?></td>
                        <td class="check-cell"><input type="radio" name="b1_<?= $n ?>" value="SI"></td>
                        <td class="check-cell"><input type="radio" name="b1_<?= $n ?>" value="NO"></td>
                        <td class="check-cell"><input type="radio" name="b1_<?= $n ?>" value="NA"></td>
                        <td class="obs-cell"><textarea name="obs_b1_<?= $n ?>"></textarea></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="6" class="instruction">2. Durante la ejecución del contrato u orden contractual, el contratista debe cumplir con los siguientes requerimientos:</td>
                    </tr>

                    <tr>
                        <th class="table-head" rowspan="2">No</th>
                        <th class="table-head" rowspan="2">REQUERIMIENTO</th>
                        <th class="table-head" colspan="3">CUMPLE</th>
                        <th class="table-head" rowspan="2">OBSERVACIONES</th>
                    </tr>
                    <tr>
                        <th class="table-head-sub">SI</th>
                        <th class="table-head-sub">NO</th>
                        <th class="table-head-sub">N/A</th>
                    </tr>

                    <?php foreach($bloque2 as $i => $req): $n = $i + 1; ?>
                    <tr>
                        <td class="num-cell"><?= $n ?></td>
                        <td class="req-cell"><?= e($req) ?></td>
                        <td class="check-cell"><input type="radio" name="b2_<?= $n ?>" value="SI"></td>
                        <td class="check-cell"><input type="radio" name="b2_<?= $n ?>" value="NO"></td>
                        <td class="check-cell"><input type="radio" name="b2_<?= $n ?>" value="NA"></td>
                        <td class="obs-cell"><textarea name="obs_b2_<?= $n ?>"></textarea></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="2" class="signature-label">Firma del Supervisor:</td>
                        <td colspan="4" class="signature-line"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer-help">
            Puedes diligenciar la información, marcar SI / NO / N/A y agregar observaciones antes de imprimir el formato.
        </div>
    </div>
</div>


<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>