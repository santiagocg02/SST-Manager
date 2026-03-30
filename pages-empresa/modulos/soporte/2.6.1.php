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
    <title>2.6.1 - Procedimiento de Revisión por la Dirección y Rendición de Cuentas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --blue:#1f5fa8;
            --line:#111;
            --head:#d9e4f2;
            --soft:#eef3fb;
            --bg:#eef2f7;
            --text:#1b1b1b;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .wrap{
            max-width:1100px;
            margin:16px auto;
            padding:0 10px;
        }

        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            margin-bottom:12px;
            flex-wrap:wrap;
        }

        .sheet{
            background:#fff;
            border:2px solid var(--blue);
            box-shadow:0 10px 20px rgba(0,0,0,.08);
            padding:14px;
            margin-bottom:16px;
        }

        .page-break{
            page-break-after:always;
        }

        table.format{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:12px;
            margin-bottom:12px;
        }

        .format td,.format th{
            border:1px solid var(--line);
            padding:6px 8px;
            vertical-align:middle;
        }

        .title{
            font-weight:900;
            text-align:center;
            font-size:13px;
        }

        .subtitle{
            font-weight:900;
            text-align:center;
            font-size:12px;
        }

        .logo-box{
            border:2px dashed rgba(0,0,0,.25);
            height:68px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:800;
            color:rgba(0,0,0,.35);
            text-align:center;
            font-size:11px;
        }

        .sec-h{
            background:#d9e1ea;
            border:1px solid #b8c2cc;
            color:#10233c;
            font-weight:900;
            text-transform:uppercase;
            padding:10px 14px;
            font-size:15px;
            letter-spacing:.2px;
            margin-top:14px;
            margin-bottom:10px;
        }

        .cover{
            min-height:860px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:30px 20px;
        }

        .cover-title{
            font-size:28px;
            font-weight:900;
            text-transform:uppercase;
            line-height:1.25;
            max-width:760px;
            margin-bottom:24px;
        }

        .cover-logo{
            width:180px;
            height:130px;
            border:2px dashed rgba(0,0,0,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            color:rgba(0,0,0,.35);
            font-weight:800;
            margin-bottom:24px;
        }

        .cover-text{
            font-size:16px;
            font-weight:700;
            margin-bottom:10px;
        }

        .box{
            border:1px solid #1f1f1f;
            padding:12px;
            margin-bottom:12px;
        }

        .text-just{
            text-align:justify;
            line-height:1.65;
        }

        table.formtbl{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:12px;
            margin-bottom:12px;
            background:#fff;
        }

        .formtbl th,
        .formtbl td{
            border:1px solid #2a2a2a;
            padding:7px 8px;
            vertical-align:top;
        }

        .formtbl th{
            background:#f1f3f6;
            text-align:center;
            font-weight:900;
            color:#14253d;
            font-size:12px;
        }

        .edit,
        .edit-inline{
            width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:0;
            color:#111;
        }

        .edit-inline{
            display:inline-block;
            width:auto;
            min-width:140px;
        }

        textarea.edit{
            resize:vertical;
            min-height:70px;
            line-height:1.55;
        }

        .list-box{
            margin:0;
            padding-left:20px;
        }

        .list-box li{
            margin-bottom:8px;
            line-height:1.55;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:12px;
            margin-top:18px;
        }

        .sign{
            border-top:1px solid #111;
            padding-top:8px;
            text-align:center;
            min-height:56px;
            font-size:12px;
            font-weight:700;
        }

        @media print{
            body{ background:#fff; }
            .toolbar{ display:none !important; }
            .sheet{ box-shadow:none; margin-bottom:0; border:2px solid #000; }
        }

        @media (max-width: 768px){
            .sign-grid{
                grid-template-columns:1fr;
            }
            .cover-title{
                font-size:22px;
            }
        }
    </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
<div class="wrap">

    <div class="toolbar">
        <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Atrás</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
    </div>

    <!-- PORTADA -->
    <div class="sheet page-break">
        <table class="format">
            <colgroup>
                <col style="width:18%">
                <col style="width:52%">
                <col style="width:15%">
                <col style="width:15%">
            </colgroup>
            <tr>
                <td rowspan="3">
                    <div class="logo-box">LOGO EMPRESA</div>
                </td>
                <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td><strong>Versión:</strong> 1</td>
                <td><strong>Fecha:</strong><br>XX/XX/20XX</td>
            </tr>
            <tr>
                <td class="subtitle">PROCEDIMIENTO DE REVISIÓN POR LA DIRECCIÓN Y RENDICIÓN DE CUENTAS</td>
                <td class="title" colspan="2">AN-XX-SST-24</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
            </tr>
        </table>

        <div class="cover">
            <div class="cover-title">PROCEDIMIENTO DE REVISIÓN POR LA DIRECCIÓN</div>
            <div class="cover-logo">LOGO</div>
            <div class="cover-text">Versión 0</div>
            <div class="cover-text"><input type="text" class="edit-inline" value="NOMBRE EMPRESA"></div>
            <div class="cover-text"><input type="text" class="edit-inline" value="FECHA"></div>
        </div>
    </div>

    <!-- DOCUMENTO -->
    <div class="sheet">
        <table class="format">
            <colgroup>
                <col style="width:18%">
                <col style="width:52%">
                <col style="width:15%">
                <col style="width:15%">
            </colgroup>
            <tr>
                <td rowspan="3">
                    <div class="logo-box">LOGO EMPRESA</div>
                </td>
                <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td><strong>Versión:</strong> 1</td>
                <td><strong>Fecha:</strong><br>XX/XX/20XX</td>
            </tr>
            <tr>
                <td class="subtitle">PROCEDIMIENTO DE REVISIÓN POR LA DIRECCIÓN Y RENDICIÓN DE CUENTAS</td>
                <td class="title" colspan="2">AN-XX-SST-24</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
            </tr>
        </table>

        <div class="sec-h">Objetivo</div>
        <div class="box text-just">
            <textarea class="edit" rows="4">Proporcionar evidencia del compromiso con el desarrollo, implementación y mantenimiento del SG SST Sistema de Gestión de Seguridad y Salud en el Trabajo que se tiene establecido en la fundación para asegurar la efectiva aplicación y mejoramiento continuo del mismo.</textarea>
        </div>

        <div class="sec-h">Alcance</div>
        <div class="box text-just">
            <textarea class="edit" rows="3">Este procedimiento es aplicable a los requerimientos definidos del Decreto 1072 de 2015 y Resolución 0312 de 2019.</textarea>
        </div>

        <div class="sec-h">Responsables</div>
        <table class="formtbl">
            <tbody>
                <tr>
                    <th style="width:220px;">Responsable principal</th>
                    <td><input class="edit" type="text" value="El Gerente General o su designado deben ejecutar lo dispuesto en este procedimiento."></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Procedimiento</div>
        <div class="box text-just">
            <textarea class="edit" rows="7">Como mínimo anualmente debe realizarse una revisión gerencial para evaluar el funcionamiento en general del sistema de Gestión en Seguridad y Salud en el Trabajo, los elementos del SGSST que responden a los lineamientos del Decreto 1072 del 2015, auditorías internas, retroalimentaciones del COPASST, estado de las acciones correctivas y preventivas, análisis de accidentalidad, seguimiento a las Revisiones Gerenciales entre otras. SIEMPRE se debe dejar acta de esta reunión.

No obstante, se podrán realizar revisiones extemporáneas a petición del Gerente General.

Las Revisiones Gerenciales son convocadas por el Gerente General de la Empresa o su designado, una vez al año o antes de encontrarse la necesidad.</textarea>
        </div>

        <div class="sec-h">Aspectos a tener en cuenta para el análisis de la revisión</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:70px;">N°</th>
                    <th>Aspecto a revisar</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="center">1</td><td>La política, los objetivos y metas del SGSST.</td></tr>
                <tr><td class="center">2</td><td>Resultados de indicadores.</td></tr>
                <tr><td class="center">3</td><td>Estrategias implementadas para el cumplimiento de los objetivos y metas.</td></tr>
                <tr><td class="center">4</td><td>Cumplimiento del plan de trabajo.</td></tr>
                <tr><td class="center">5</td><td>Ejecución del presupuesto y suficiencia de los recursos.</td></tr>
                <tr><td class="center">6</td><td>El análisis estadístico del sistema (accidentalidad, incidentalidad, inspecciones, entre otras) y la notificación de accidentes.</td></tr>
                <tr><td class="center">7</td><td>Estado de acciones derivadas de hallazgos al sistema (no conformidades, iniciativas, recomendaciones, entre otras).</td></tr>
                <tr><td class="center">8</td><td>Resultados de implementaciones de acciones preventivas y correctivas.</td></tr>
                <tr><td class="center">9</td><td>El resultado de las auditorías internas y externas.</td></tr>
                <tr><td class="center">10</td><td>Los cambios que puedan afectar el SGSST.</td></tr>
                <tr><td class="center">11</td><td>Requerimientos del COPASST.</td></tr>
                <tr><td class="center">12</td><td>Participación de los trabajadores (mecanismos, evidencias).</td></tr>
                <tr><td class="center">13</td><td>Requisitos legales de SST.</td></tr>
                <tr><td class="center">14</td><td>Entre otros descritos en la norma.</td></tr>
            </tbody>
        </table>

        <div class="sec-h">Resultado de la revisión gerencial</div>
        <div class="box text-just">
            <textarea class="edit" rows="5">Como resultado de estas Revisiones Gerenciales, se establece planes de acciones que permitan corregir y hacer seguimiento a las no conformidades relacionadas con las mejoras de la eficiencia del SGSST.</textarea>
        </div>

        <div class="sec-h">Plan de acciones derivado de la revisión</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:60px;">N°</th>
                    <th>Hallazgo / oportunidad de mejora</th>
                    <th style="width:180px;">Acción</th>
                    <th style="width:180px;">Responsable</th>
                    <th style="width:140px;">Fecha</th>
                    <th style="width:140px;">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php for($i=1; $i<=8; $i++): ?>
                <tr>
                    <td class="center"><?php echo $i; ?></td>
                    <td><textarea class="edit" rows="2"></textarea></td>
                    <td><textarea class="edit" rows="2"></textarea></td>
                    <td><input class="edit" type="text"></td>
                    <td><input class="edit" type="text"></td>
                    <td><input class="edit" type="text"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="sec-h">Observaciones</div>
        <div class="box">
            <textarea class="edit" rows="4"></textarea>
        </div>

        <div class="sign-grid">
            <div class="sign">ELABORÓ</div>
            <div class="sign">REVISÓ</div>
            <div class="sign">APROBÓ</div>
        </div>
    </div>
</div>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>