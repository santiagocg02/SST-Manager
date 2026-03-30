<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$actividades = [
    ['ciclo'=>'I. PLANEAR', 'grupo'=>'RECURSOS', 'item'=>'Estándar 1.1.1 Responsable del SG-SST', 'meta'=>'Mantener', 'responsable'=>'Alta Dirección', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.2 Responsabilidades en el SG-SST', 'meta'=>'Mantener', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.3 Asignación de recursos', 'meta'=>'Mantener', 'responsable'=>'Gerencia', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.4 Afiliación ARL', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.5 Identificación de trabajadores alto riesgo', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.6 Conformación COPASST', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST / Vigía', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.7 Capacitación COPASST', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.1.8 Comité de Convivencia', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],

    ['ciclo'=>'', 'grupo'=>'GESTIÓN INTEGRAL DEL SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO', 'item'=>'Estándar 1.2.1 Programa de capacitación', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.2.2 Inducción y reinducción', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST / Jefes', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 1.2.3 Curso 50 horas SG-SST', 'meta'=>'Mantener', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.1.1 Política SG-SST', 'meta'=>'Anual', 'responsable'=>'Gerencia', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.2.1 Objetivos SG-SST', 'meta'=>'Anual', 'responsable'=>'Gerencia / Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.3.1 Evaluación inicial', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.4.1 Plan de trabajo anual', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.5.1 Conservación documental', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.6.1 Rendición de cuentas', 'meta'=>'Anual', 'responsable'=>'Gerencia', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.7.1 Matriz legal', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.8.1 Comunicación', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.9.1 Adquisiciones', 'meta'=>'Anual', 'responsable'=>'Gerencia / Compras', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.10.1 Contratación', 'meta'=>'Anual', 'responsable'=>'Gerencia / Talento Humano', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 2.11.1 Gestión del cambio', 'meta'=>'Cuando aplique', 'responsable'=>'Gerencia', 'recursos'=>'X'],

    ['ciclo'=>'II. HACER', 'grupo'=>'GESTIÓN DE LA SALUD', 'item'=>'Estándar 3.1.1 Perfil sociodemográfico', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.2 Actividades de promoción y prevención', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST / ARL', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.3 Información al médico ocupacional', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.4 Evaluaciones médicas ocupacionales', 'meta'=>'Anual', 'responsable'=>'Médico ocupacional', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.5 Custodia historias clínicas', 'meta'=>'Mantener', 'responsable'=>'IPS / Médico', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.6 Restricciones y recomendaciones', 'meta'=>'Cuando aplique', 'responsable'=>'Médico / Responsable SG-SST', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.7 Estilos de vida saludable', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.8 Agua potable y saneamiento básico', 'meta'=>'Mantener', 'responsable'=>'Gerencia', 'recursos'=>'Todos'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 3.1.9 Gestión de residuos', 'meta'=>'Mantener', 'responsable'=>'Gerencia / Responsable SG-SST', 'recursos'=>'Todos'],

    ['ciclo'=>'', 'grupo'=>'GESTIÓN DE PELIGROS Y RIESGOS', 'item'=>'Estándar 4.1.1 Identificación de peligros', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.1.2 Participación de trabajadores en identificación', 'meta'=>'Anual', 'responsable'=>'Todos', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.1.3 Sustancias peligrosas', 'meta'=>'Cuando aplique', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.1.4 Mediciones ambientales', 'meta'=>'Cuando aplique', 'responsable'=>'Responsable SG-SST / Proveedor', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.2.1 Medidas de intervención', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.2.2 Verificación de medidas', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.2.3 Procedimientos / instructivos', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.2.4 Inspecciones', 'meta'=>'Mensual', 'responsable'=>'Responsable SG-SST / COPASST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.2.5 Mantenimiento preventivo', 'meta'=>'Anual', 'responsable'=>'Gerencia / Mantenimiento', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 4.2.6 Entrega de EPP', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],

    ['ciclo'=>'', 'grupo'=>'GESTIÓN DE AMENAZAS', 'item'=>'Estándar 5.1.1 Plan de emergencias', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 5.1.2 Brigada de emergencias', 'meta'=>'Anual', 'responsable'=>'Brigada / Responsable SG-SST', 'recursos'=>'X'],

    ['ciclo'=>'III. VERIFICAR', 'grupo'=>'VERIFICACIÓN DEL SG-SST', 'item'=>'Estándar 6.1.1 Definición de indicadores', 'meta'=>'Trimestral', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 6.1.2 Auditoría interna', 'meta'=>'Anual', 'responsable'=>'Auditor interno', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 6.1.3 Revisión por la dirección', 'meta'=>'Anual', 'responsable'=>'Alta Dirección', 'recursos'=>'X'],

    ['ciclo'=>'IV. ACTUAR', 'grupo'=>'MEJORAMIENTO', 'item'=>'Estándar 7.1.1 Acciones preventivas y correctivas', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 7.1.2 Acciones de mejora', 'meta'=>'Cuando aplique', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 7.1.3 Mejoras por investigación AT/EL', 'meta'=>'Cuando aplique', 'responsable'=>'Responsable SG-SST', 'recursos'=>'X'],
    ['ciclo'=>'', 'grupo'=>'', 'item'=>'Estándar 7.1.4 Plan de mejoramiento', 'meta'=>'Anual', 'responsable'=>'Responsable SG-SST / Gerencia', 'recursos'=>'X'],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2.4.1 - Plan de Trabajo Anual SG-SST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --blue:#2c5d99;
            --blue-soft:#dce6f4;
            --blue-mid:#9db5d6;
            --green-soft:#dff2d8;
            --line:#a9a9a9;
            --text:#1b1b1b;
            --bg:#eef2f7;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .wrap{
            max-width:100%;
            padding:14px;
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
            box-shadow:0 8px 18px rgba(0,0,0,.08);
            padding:10px;
        }

        table{
            border-collapse:collapse;
            width:100%;
        }

        .top-table{
            table-layout:fixed;
            font-size:10px;
            margin-bottom:6px;
        }

        .top-table td,.top-table th{
            border:1px solid var(--line);
            padding:3px 5px;
            vertical-align:middle;
        }

        .logo-box{
            height:42px;
            border:1px dashed #b8b8b8;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:9px;
            color:#a0a0a0;
            font-weight:700;
        }

        .title-main{
            font-size:11px;
            font-weight:800;
            text-align:center;
        }

        .title-sub{
            font-size:10px;
            font-weight:700;
            text-align:center;
        }

        .top-label{
            font-weight:700;
            background:#f8fbff;
        }

        .mini-input{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:10px;
            text-align:left;
        }

        .mini-input.center{ text-align:center; }

        .top-scroll{
            overflow-x:auto;
            overflow-y:hidden;
            height:18px;
            margin:6px 0 5px;
        }

        .top-scroll-inner{
            height:1px;
        }

        .table-wrap{
            overflow:auto;
            max-height:72vh;
            border:1px solid #ccd5e0;
        }

        .plan-table{
            min-width:2500px;
            width:max-content;
            table-layout:fixed;
            font-size:9px;
        }

        .plan-table th,
        .plan-table td{
            border:1px solid #c8c8c8;
            padding:2px 3px;
            vertical-align:middle;
        }

        .plan-table th{
            background:var(--blue-soft);
            text-align:center;
            font-weight:800;
        }

        .th-blue{
            background:var(--blue-mid) !important;
            color:#fff;
        }

        .cycle-col{
            writing-mode:vertical-rl;
            transform:rotate(180deg);
            text-align:center;
            font-weight:800;
            background:#f7f7f7;
            min-width:28px;
        }

        .group-col{
            writing-mode:vertical-rl;
            transform:rotate(180deg);
            text-align:center;
            font-weight:800;
            color:#fff;
            min-width:34px;
        }

        .group-planear{ background:#1f3f77; }
        .group-hacer{ background:#8fb0df; color:#0f2648; }
        .group-riesgos{ background:#c8def6; color:#0f2648; }
        .group-verificar{ background:#133a73; }
        .group-actuar{ background:#8bc53f; color:#1d2d0d; }

        .left{ text-align:left !important; }
        .center{ text-align:center !important; }
        .right{ text-align:right !important; }

        .w-ciclo{ width:28px; }
        .w-estandar{ width:34px; }
        .w-item{ width:280px; }
        .w-meta{ width:55px; }
        .w-responsable{ width:95px; }
        .w-recurso-a{ width:42px; }
        .w-recurso-b{ width:42px; }
        .w-recurso-c{ width:42px; }
        .w-mes{ width:24px; }
        .w-obs{ width:130px; }

        .cell-input{
            width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            font-size:9px;
            padding:0;
        }

        .cell-input.left{ text-align:left; }
        .cell-input.center{ text-align:center; }

        .ep-head{
            background:#d0dbeb !important;
            font-size:8px;
        }

        .ep-cell{
            text-align:center;
            font-weight:700;
            font-size:9px;
        }

        .ep-e{
            background:var(--green-soft);
            color:#47773b;
        }

        .ep-p{
            background:#9fb4d3;
            color:#fff;
        }

        .summary-table{
            margin-top:8px;
            table-layout:fixed;
            font-size:9px;
        }

        .summary-table td,.summary-table th{
            border:1px solid #c8c8c8;
            padding:3px 4px;
            vertical-align:middle;
        }

        .section-head{
            background:var(--blue-soft);
            font-weight:800;
            text-align:center;
        }

        .charts-row{
            display:grid;
            grid-template-columns: 2fr 1fr;
            gap:12px;
            margin-top:10px;
        }

        .chart-box{
            border:1px solid #ccd5e0;
            padding:10px;
            background:#fff;
        }

        .analysis-grid{
            margin-top:10px;
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:0;
            border:1px solid #c8c8c8;
        }

        .analysis-item{
            min-height:72px;
            border-right:1px solid #c8c8c8;
            border-bottom:1px solid #c8c8c8;
        }

        .analysis-item:nth-child(2n){ border-right:none; }
        .analysis-title{
            background:var(--blue-soft);
            font-weight:800;
            text-align:center;
            font-size:9px;
            padding:4px;
            border-bottom:1px solid #c8c8c8;
        }
        .analysis-body{
            padding:6px;
        }

        .bottom-grid{
            margin-top:10px;
            display:grid;
            grid-template-columns: 1.3fr 1fr;
            gap:0;
            border:1px solid #c8c8c8;
        }

        .bottom-box{
            min-height:90px;
            border-right:1px solid #c8c8c8;
        }

        .bottom-box:last-child{ border-right:none; }

        .bottom-title{
            background:var(--blue-soft);
            font-weight:800;
            text-align:center;
            font-size:9px;
            padding:4px;
            border-bottom:1px solid #c8c8c8;
        }

        .bottom-body{
            padding:6px;
            font-size:9px;
            min-height:64px;
        }

        .sign-grid{
            margin-top:14px;
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
        }

        .sign{
            padding-top:26px;
            border-top:1px solid #111;
            text-align:center;
            font-size:10px;
            font-weight:700;
        }

        @media print{
            body{ background:#fff; }
            .toolbar, .top-scroll{ display:none !important; }
            .sheet{ box-shadow:none; }
            .table-wrap{
                max-height:none;
                overflow:visible;
                border:none;
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

    <div class="sheet">

        <table class="top-table">
            <colgroup>
                <col style="width:80px">
                <col>
                <col style="width:80px">
                <col style="width:80px">
            </colgroup>
            <tr>
                <td rowspan="2"><div class="logo-box">TU LOGO AQUÍ</div></td>
                <td class="title-main">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td class="center"><strong>CÓDIGO:</strong></td>
                <td class="center">PL-SST-02</td>
            </tr>
            <tr>
                <td class="title-sub">PLAN DE TRABAJO ANUAL SG-SST</td>
                <td class="center"><strong>VERSIÓN:</strong></td>
                <td class="center">1</td>
            </tr>
            <tr>
                <td colspan="2" class="top-label">
                    <strong>OBJETIVO:</strong>
                    <input class="mini-input" value="Planear y ejecutar las actividades para cumplir y mantener el SG-SST">
                </td>
                <td class="center"><strong>FECHA:</strong></td>
                <td class="center">XX/XX/2025</td>
            </tr>
            <tr>
                <td colspan="2" class="top-label">
                    <strong>ALCANCE:</strong>
                    <input class="mini-input" value="Aplica para las áreas, trabajadores y actividades de la empresa">
                </td>
                <td colspan="2" class="top-label">
                    <strong>PLANEAR</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="top-label">
                    <strong>META:</strong>
                    <input class="mini-input center" value="Dar cumplimiento al 100%">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="top-label">
                    <strong>INDICADORES:</strong>
                    <input class="mini-input" value="Cumplimiento del plan de trabajo">
                </td>
                <td colspan="2" class="top-label">
                    <strong>FÓRMULA:</strong>
                    <input class="mini-input" value="N° de actividades ejecutadas / N° de actividades programadas x 100">
                </td>
            </tr>
        </table>

        <div class="top-scroll" id="topScroll">
            <div class="top-scroll-inner" id="topScrollInner"></div>
        </div>

        <div class="table-wrap" id="tableWrap">
            <table class="plan-table" id="planTable">
                <thead>
                    <tr>
                        <th rowspan="3" class="w-ciclo">CICLO</th>
                        <th rowspan="3" class="w-estandar">ESTÁNDAR</th>
                        <th rowspan="3" class="w-item">ÍTEM DEL ESTÁNDAR</th>
                        <th rowspan="3" class="w-meta">META</th>
                        <th rowspan="3" class="w-responsable">RESPONSABLE</th>
                        <th colspan="3">RECURSOS</th>
                        <th colspan="24">mes-año</th>
                        <th rowspan="3" class="w-obs">OBSERVACIONES</th>
                    </tr>
                    <tr>
                        <th class="w-recurso-a">Humano</th>
                        <th class="w-recurso-b">Físico</th>
                        <th class="w-recurso-c">Económico</th>

                        <th colspan="2">ene-25</th>
                        <th colspan="2">feb-25</th>
                        <th colspan="2">mar-25</th>
                        <th colspan="2">abr-25</th>
                        <th colspan="2">may-25</th>
                        <th colspan="2">jun-25</th>
                        <th colspan="2">jul-25</th>
                        <th colspan="2">ago-25</th>
                        <th colspan="2">sep-25</th>
                        <th colspan="2">oct-25</th>
                        <th colspan="2">nov-25</th>
                        <th colspan="2">dic-25</th>
                    </tr>
                    <tr>
                        <th>X</th>
                        <th>X</th>
                        <th>X</th>

                        <?php for($i=0; $i<12; $i++): ?>
                            <th class="ep-head w-mes">E</th>
                            <th class="ep-head w-mes">P</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($actividades as $idx => $a): ?>
                        <?php
                            $groupClass = '';
                            $grupoTxt = strtoupper($a['grupo']);
                            if (str_contains($grupoTxt, 'RECURSOS') || str_contains($grupoTxt, 'GESTIÓN INTEGRAL')) $groupClass = 'group-planear';
                            elseif (str_contains($grupoTxt, 'GESTIÓN DE LA SALUD')) $groupClass = 'group-hacer';
                            elseif (str_contains($grupoTxt, 'PELIGROS') || str_contains($grupoTxt, 'AMENAZAS')) $groupClass = 'group-riesgos';
                            elseif (str_contains($grupoTxt, 'VERIFICACIÓN')) $groupClass = 'group-verificar';
                            elseif (str_contains($grupoTxt, 'MEJORAMIENTO')) $groupClass = 'group-actuar';
                        ?>
                        <tr>
                            <td class="cycle-col w-ciclo"><?php echo htmlspecialchars($a['ciclo']); ?></td>
                            <td class="group-col <?php echo $groupClass; ?> w-estandar"><?php echo htmlspecialchars($a['grupo']); ?></td>
                            <td class="left w-item"><input class="cell-input left" value="<?php echo htmlspecialchars($a['item']); ?>"></td>
                            <td class="center w-meta"><input class="cell-input center" value="<?php echo htmlspecialchars($a['meta']); ?>"></td>
                            <td class="left w-responsable"><input class="cell-input left" value="<?php echo htmlspecialchars($a['responsable']); ?>"></td>
                            <td class="center w-recurso-a"><input class="cell-input center" value="<?php echo htmlspecialchars($a['recursos']); ?>"></td>
                            <td class="center w-recurso-b"><input class="cell-input center" value=""></td>
                            <td class="center w-recurso-c"><input class="cell-input center" value=""></td>

                            <?php for($m=1; $m<=12; $m++): ?>
                                <td class="ep-cell ep-e w-mes"><input class="cell-input center" value=""></td>
                                <td class="ep-cell ep-p w-mes"><input class="cell-input center" value=""></td>
                            <?php endfor; ?>

                            <td class="left w-obs"><input class="cell-input left" value=""></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <table class="summary-table">
            <colgroup>
                <col style="width:280px">
                <col>
                <col style="width:120px">
                <col style="width:120px">
            </colgroup>
            <tr>
                <th class="section-head">INDICADOR - RESULTADOS POR TRIMESTRE</th>
                <th class="section-head">mes-año</th>
                <th class="section-head">% DE CUMPLIMIENTO</th>
                <th class="section-head">ESTADO</th>
            </tr>
            <tr>
                <td>Cumplimiento de las actividades programadas del Plan de Trabajo</td>
                <td>ene-25 a mar-25</td>
                <td class="center">92%</td>
                <td class="center">ALTO</td>
            </tr>
            <tr>
                <td>Cumplimiento de las actividades programadas del Plan de Trabajo</td>
                <td>abr-25 a jun-25</td>
                <td class="center">95%</td>
                <td class="center">ALTO</td>
            </tr>
            <tr>
                <td>Cumplimiento de las actividades programadas del Plan de Trabajo</td>
                <td>jul-25 a sep-25</td>
                <td class="center">89%</td>
                <td class="center">MEDIO</td>
            </tr>
            <tr>
                <td>Cumplimiento de las actividades programadas del Plan de Trabajo</td>
                <td>oct-25 a dic-25</td>
                <td class="center">97%</td>
                <td class="center">ALTO</td>
            </tr>
            <tr>
                <td colspan="2" class="right"><strong>% DE CUMPLIMIENTO ANUAL</strong></td>
                <td class="center"><strong>93%</strong></td>
                <td class="center"><strong>ALTO</strong></td>
            </tr>
        </table>

        <div class="charts-row">
            <div class="chart-box">
                <div class="section-head mb-2">CUMPLIMIENTO DEL PLAN DE TRABAJO</div>
                <canvas id="barChart" height="110"></canvas>
            </div>
            <div class="chart-box">
                <div class="section-head mb-2">CUMPLIMIENTO</div>
                <canvas id="pieChart" height="180"></canvas>
            </div>
        </div>

        <div class="analysis-grid">
            <div class="analysis-item">
                <div class="analysis-title">ANÁLISIS TRIMESTRE 1</div>
                <div class="analysis-body"><input class="cell-input left" value=""></div>
            </div>
            <div class="analysis-item">
                <div class="analysis-title">ANÁLISIS TRIMESTRE 2</div>
                <div class="analysis-body"><input class="cell-input left" value=""></div>
            </div>
            <div class="analysis-item">
                <div class="analysis-title">ANÁLISIS TRIMESTRE 3</div>
                <div class="analysis-body"><input class="cell-input left" value=""></div>
            </div>
            <div class="analysis-item">
                <div class="analysis-title">ANÁLISIS TRIMESTRE 4</div>
                <div class="analysis-body"><input class="cell-input left" value=""></div>
            </div>
        </div>

        <div class="bottom-grid">
            <div class="bottom-box">
                <div class="bottom-title">RECURSOS NECESARIOS</div>
                <div class="bottom-body">
                    <div><input class="cell-input left" value="Personal responsable del SG-SST"></div>
                    <div><input class="cell-input left" value="Capacitaciones y acompañamiento de la ARL"></div>
                    <div><input class="cell-input left" value="Elementos de protección personal"></div>
                    <div><input class="cell-input left" value="Inspecciones y seguimiento"></div>
                    <div><input class="cell-input left" value="Presupuesto anual SG-SST"></div>
                </div>
            </div>
            <div class="bottom-box">
                <div class="bottom-title">OBSERVACIONES</div>
                <div class="bottom-body">
                    <input class="cell-input left" value="">
                </div>
            </div>
        </div>

        <div class="sign-grid">
            <div class="sign">ELABORADO POR</div>
            <div class="sign">APROBADO POR</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const topScroll = document.getElementById('topScroll');
    const topScrollInner = document.getElementById('topScrollInner');
    const tableWrap = document.getElementById('tableWrap');
    const planTable = document.getElementById('planTable');

    function syncTopScrollWidth() {
        topScrollInner.style.width = planTable.scrollWidth + 'px';
    }

    topScroll.addEventListener('scroll', function () {
        tableWrap.scrollLeft = topScroll.scrollLeft;
    });

    tableWrap.addEventListener('scroll', function () {
        topScroll.scrollLeft = tableWrap.scrollLeft;
    });

    window.addEventListener('load', syncTopScrollWidth);
    window.addEventListener('resize', syncTopScrollWidth);

    const barCtx = document.getElementById('barChart');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['ene-25','feb-25','mar-25','abr-25','may-25','jun-25','jul-25','ago-25','sep-25','oct-25','nov-25','dic-25'],
            datasets: [{
                label: '% Cumplimiento',
                data: [92, 95, 90, 94, 96, 95, 86, 89, 92, 97, 96, 98],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    const pieCtx = document.getElementById('pieChart');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cumplido', 'Pendiente'],
            datasets: [{
                data: [93, 7]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '65%'
        }
    });
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>