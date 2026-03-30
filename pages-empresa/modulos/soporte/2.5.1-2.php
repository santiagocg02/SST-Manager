<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$documentos = [
    ['tipo'=>'Políticas', 'nombre'=>'Políticas del SG SST', 'version'=>'0', 'codigo'=>'PO-SST-01', 'fecha'=>'1/1/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Planes', 'nombre'=>'Plan de trabajo', 'version'=>'0', 'codigo'=>'PL-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Planes', 'nombre'=>'Plan de Evacuación Medica MEDEVAC', 'version'=>'0', 'codigo'=>'PL-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Programas', 'nombre'=>'Programa de capacitaciones', 'version'=>'0', 'codigo'=>'PR-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Inspecciones', 'version'=>'0', 'codigo'=>'PR-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Gestión de Residuos', 'version'=>'0', 'codigo'=>'PR-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia Epidemiológica PVE Ergonomía', 'version'=>'0', 'codigo'=>'PR-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Mantenimiento', 'version'=>'0', 'codigo'=>'PR-SST-06', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Prevención de Consumo de alcohol, cigarrillo y sustancias psicoactivas', 'version'=>'0', 'codigo'=>'PR-SST-07', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia Epidemiológica PVE Auditivo', 'version'=>'0', 'codigo'=>'PR-SST-08', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia Epidemiológica PVE Psicosocial', 'version'=>'0', 'codigo'=>'PR-SST-09', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Riesgo Mecánico', 'version'=>'0', 'codigo'=>'PR-SST-10', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Emergencias', 'version'=>'0', 'codigo'=>'PR-SST-11', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia epidemiológica de fomento de estilos de vida y trabajo saludable', 'version'=>'0', 'codigo'=>'PR-SST-12', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de prevención de riesgo vial', 'version'=>'0', 'codigo'=>'PR-SST-13', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de auditorías', 'version'=>'0', 'codigo'=>'PR-SST-14', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Manuales', 'nombre'=>'Manual de control de Documentos', 'version'=>'0', 'codigo'=>'MA-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Manuales', 'nombre'=>'Manual SG-SST', 'version'=>'0', 'codigo'=>'MA-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Registros', 'nombre'=>'Registro de Asistencia', 'version'=>'0', 'codigo'=>'RE-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Seguimiento a capacitaciones', 'version'=>'0', 'codigo'=>'RE-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de planificación del cambio', 'version'=>'0', 'codigo'=>'RE-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Ambiental', 'version'=>'0', 'codigo'=>'RE-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de identificación de acciones de mejora y correctivas', 'version'=>'0', 'codigo'=>'RE-SST-05', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inspección de uso y mantenimiento dotación y elementos de protección personal', 'version'=>'0', 'codigo'=>'RE-SST-06', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Gerencial', 'version'=>'0', 'codigo'=>'RE-SST-07', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Extintor', 'version'=>'0', 'codigo'=>'RE-SST-08', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Botiquín', 'version'=>'0', 'codigo'=>'RE-SST-09', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato Investigación de Accidente e Incidente', 'version'=>'0', 'codigo'=>'RE-SST-10', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formatos de Inspecciones - Locativo', 'version'=>'0', 'codigo'=>'RE-SST-11', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formatos de Inspecciones - Sustancias Químicas', 'version'=>'0', 'codigo'=>'RE-SST-12', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inspección de uso y mantenimiento dotación y elementos de protección personal', 'version'=>'0', 'codigo'=>'RE-SST-13', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inspección de herramientas genéricas', 'version'=>'0', 'codigo'=>'RE-SST-14', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de Chequeo Personas Jurídicas', 'version'=>'0', 'codigo'=>'RE-SST-15', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de Chequeo Personas Naturales', 'version'=>'0', 'codigo'=>'RE-SST-16', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Entrega de dotación y EPP', 'version'=>'0', 'codigo'=>'RE-SST-17', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato para la adquisición de compras', 'version'=>'0', 'codigo'=>'RE-SST-18', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato Plan de auditoría', 'version'=>'0', 'codigo'=>'RE-SST-19', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato acta de reunión de COPASST', 'version'=>'0', 'codigo'=>'RE-SST-21', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lecciones aprendidas', 'version'=>'0', 'codigo'=>'RE-SST-22', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Observación de comportamiento', 'version'=>'0', 'codigo'=>'RE-SST-23', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de verificación de transporte de productos químicos', 'version'=>'0', 'codigo'=>'RE-SST-24', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Evaluación Inducción', 'version'=>'0', 'codigo'=>'RE-SST-25', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Tarjeta de reporte de actos y condiciones inseguras', 'version'=>'0', 'codigo'=>'RE-SST-26', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Estadísticas de ausentismo', 'version'=>'0', 'codigo'=>'RE-SST-27', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Ficha técnica del indicador', 'version'=>'0', 'codigo'=>'RE-SST-28', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'MEDEVAC', 'version'=>'0', 'codigo'=>'RE-SST-29', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de chequeo auditoría', 'version'=>'0', 'codigo'=>'RE-SST-30', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de chequeo simulacro', 'version'=>'0', 'codigo'=>'RE-SST-31', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de inscripción de brigadas', 'version'=>'0', 'codigo'=>'RE-SST-32', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Estadísticas de Accidentalidad', 'version'=>'0', 'codigo'=>'RE-SST-33', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Registro de MEDEVAC', 'version'=>'0', 'codigo'=>'RE-SST-34', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inventario general de equipos y elementos de emergencia', 'version'=>'0', 'codigo'=>'RE-SST-35', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Anexos', 'nombre'=>'Diagnóstico inicial', 'version'=>'0', 'codigo'=>'AN-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Presupuesto', 'version'=>'0', 'codigo'=>'AN-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de inducción', 'version'=>'0', 'codigo'=>'AN-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Listado Maestro de documentos', 'version'=>'0', 'codigo'=>'AN-SST-05', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de Objetivos e indicadores', 'version'=>'0', 'codigo'=>'AN-SST-06', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de identificación de requisitos legales', 'version'=>'0', 'codigo'=>'AN-SST-07', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz para la identificación de peligros y la valoración de Riesgos', 'version'=>'0', 'codigo'=>'AN-SST-08', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento Gestión de cambio', 'version'=>'0', 'codigo'=>'AN-SST-09', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para realizar EMO', 'version'=>'0', 'codigo'=>'AN-SST-10', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Profesiograma', 'version'=>'0', 'codigo'=>'AN-SST-11', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de auditoría interna', 'version'=>'0', 'codigo'=>'AN-SST-12', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para la Identificación de Peligros', 'version'=>'0', 'codigo'=>'AN-SST-13', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Encuesta para perfil sociodemográfico', 'version'=>'0', 'codigo'=>'AN-SST-14', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Análisis Encuesta para perfil sociodemográfico', 'version'=>'0', 'codigo'=>'AN-SST-15', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de Investigación de Accidentes', 'version'=>'0', 'codigo'=>'AN-SST-19', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de Idn_Ev_Seg RQ Legales', 'version'=>'0', 'codigo'=>'AN-SST-20', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de EPP por cargos', 'version'=>'0', 'codigo'=>'AN-SST-21', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para la gestión del cambio', 'version'=>'0', 'codigo'=>'AN-SST-22', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de participación, consulta y comunicación', 'version'=>'0', 'codigo'=>'AN-SST-23', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de revisión por la dirección y rendición de cuentas', 'version'=>'0', 'codigo'=>'AN-SST-24', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Seguimiento de pago de aportes al Sistema de Seguridad Social', 'version'=>'0', 'codigo'=>'AN-SST-26', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Perfiles de cargo', 'version'=>'0', 'codigo'=>'AN-SST-28', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz Actos y condiciones inseguras/ acciones correctivas y/o preventivas', 'version'=>'0', 'codigo'=>'AN-SST-29', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de Seguimiento de Exámenes Médicos', 'version'=>'0', 'codigo'=>'AN-SST-30', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para compras en SST', 'version'=>'0', 'codigo'=>'AN-SST-31', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Caracterización de la accidentalidad', 'version'=>'0', 'codigo'=>'AN-SST-32', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Perfil del representante de la alta dirección para el SG SST', 'version'=>'0', 'codigo'=>'AN-SST-33', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de acciones correctivas y preventivas', 'version'=>'0', 'codigo'=>'AN-SST-35', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Actas', 'nombre'=>'Conformación COPASST', 'version'=>'0', 'codigo'=>'AC-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Conformación COCOLAB', 'version'=>'0', 'codigo'=>'AC-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Conformación de brigadas', 'version'=>'0', 'codigo'=>'AC-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Acta de reunión y seguimiento de actividades', 'version'=>'0', 'codigo'=>'AC-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Carta de nombramiento representante SG- SST Alta gerencia', 'version'=>'0', 'codigo'=>'AC-SST-05', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Informes', 'nombre'=>'Informe de auditoría', 'version'=>'0', 'codigo'=>'IN-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Informes', 'nombre'=>'Informe de Señalización', 'version'=>'0', 'codigo'=>'IN-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Informes', 'nombre'=>'Informe de Rendición de cuentas', 'version'=>'0', 'codigo'=>'IN-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
];

$grupos = [];
foreach ($documentos as $doc) {
    $grupos[$doc['tipo']][] = $doc;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2.5.1-2 - Listado Maestro de Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --blue:#1f5fa8;
            --head:#89a6d6;
            --line:#8f8f8f;
            --bg:#eef2f7;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:#111;
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
            padding:10px;
        }

        .top-scroll{
            overflow-x:auto;
            overflow-y:hidden;
            height:18px;
            margin-bottom:6px;
        }

        .top-scroll-inner{
            height:1px;
        }

        .table-wrap{
            overflow:auto;
            max-height:78vh;
            border:1px solid #ccd5e0;
        }

        table.master{
            width:max-content;
            min-width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:12px;
            background:#fff;
        }

        .master td,.master th{
            border:1px solid var(--line);
            padding:4px 6px;
            vertical-align:middle;
        }

        .master thead th{
            background:var(--head);
            color:#111;
            font-weight:900;
            text-align:center;
        }

        .head-box{
            background:#fff !important;
            font-weight:800;
            text-align:center;
        }

        .logo-cell{
            width:150px;
        }

        .logo-box{
            height:72px;
            border:2px dashed rgba(0,0,0,.22);
            display:flex;
            align-items:center;
            justify-content:center;
            color:rgba(0,0,0,.35);
            font-size:11px;
            font-weight:800;
            text-align:center;
        }

        .title-main{
            font-weight:900;
            text-align:center;
            font-size:13px;
        }

        .title-sub{
            font-weight:900;
            text-align:center;
            font-size:12px;
        }

        .type-cell{
            font-weight:800;
            text-align:center;
            background:#fbfbfb;
        }

        .edit{
            width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:0;
            color:#111;
        }

        .edit.center{
            text-align:center;
        }

        .w-tipo{ width:160px; }
        .w-nombre{ width:430px; }
        .w-version{ width:60px; }
        .w-codigo{ width:110px; }
        .w-fecha{ width:150px; }
        .w-ubicacion{ width:220px; }

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

        <div class="top-scroll" id="topScroll">
            <div class="top-scroll-inner" id="topScrollInner"></div>
        </div>

        <div class="table-wrap" id="tableWrap">
            <table class="master" id="masterTable">
                <colgroup>
                    <col class="w-tipo">
                    <col class="w-nombre">
                    <col class="w-version">
                    <col class="w-codigo">
                    <col class="w-fecha">
                    <col class="w-ubicacion">
                </colgroup>

                <thead>
                    <tr>
                        <th rowspan="2" class="logo-cell head-box">
                            <div class="logo-box">LOGO<br>EMPRESA</div>
                        </th>
                        <th colspan="4" class="head-box title-main">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</th>
                        <th class="head-box">0</th>
                    </tr>
                    <tr>
                        <th colspan="4" class="head-box title-sub">LISTADO MAESTRO DE DOCUMENTOS</th>
                        <th class="head-box">AN-SST-05</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="head-box"></th>
                        <th class="head-box">XX/XX/2025</th>
                    </tr>
                    <tr>
                        <th>TIPO DE DOCUMENTO</th>
                        <th>NOMBRE DEL DOCUMENTO</th>
                        <th>VERSIÓN</th>
                        <th>CÓDIGO</th>
                        <th>FECHA</th>
                        <th>UBICACIÓN</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($grupos as $tipo => $items): ?>
                        <?php $rowspan = count($items); ?>
                        <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <?php if ($index === 0): ?>
                                    <td rowspan="<?php echo $rowspan; ?>" class="type-cell">
                                        <?php echo htmlspecialchars($tipo); ?>
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <input class="edit" type="text" value="<?php echo htmlspecialchars($item['nombre']); ?>">
                                </td>
                                <td>
                                    <input class="edit center" type="text" value="<?php echo htmlspecialchars($item['version']); ?>">
                                </td>
                                <td>
                                    <input class="edit center" type="text" value="<?php echo htmlspecialchars($item['codigo']); ?>">
                                </td>
                                <td>
                                    <input class="edit center" type="text" value="<?php echo htmlspecialchars($item['fecha']); ?>">
                                </td>
                                <td>
                                    <input class="edit" type="text" value="<?php echo htmlspecialchars($item['ubicacion']); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                    <?php for($i=0; $i<6; $i++): ?>
                        <tr>
                            <td class="type-cell"><input class="edit center" type="text" value=""></td>
                            <td><input class="edit" type="text" value=""></td>
                            <td><input class="edit center" type="text" value=""></td>
                            <td><input class="edit center" type="text" value=""></td>
                            <td><input class="edit center" type="text" value=""></td>
                            <td><input class="edit" type="text" value=""></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const topScroll = document.getElementById('topScroll');
    const topScrollInner = document.getElementById('topScrollInner');
    const tableWrap = document.getElementById('tableWrap');
    const masterTable = document.getElementById('masterTable');

    function syncTopScrollWidth() {
        topScrollInner.style.width = masterTable.scrollWidth + 'px';
    }

    topScroll.addEventListener('scroll', function () {
        tableWrap.scrollLeft = topScroll.scrollLeft;
    });

    tableWrap.addEventListener('scroll', function () {
        topScroll.scrollLeft = tableWrap.scrollLeft;
    });

    window.addEventListener('load', syncTopScrollWidth);
    window.addEventListener('resize', syncTopScrollWidth);
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>