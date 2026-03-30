<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

$rows = [
  [
    'ciclo' => 'I. PLANEAR',
    'grupo' => 'RECURSOS (10%)',
    'estandar' => 'Recursos financieros, técnicos humanos y de otra índole requeridos para coordinar y desarrollar el Sistema de Gestión de Seguridad y Salud en el Trabajo (SG-SST) (4%)',
    'peso' => '4',
    'items' => [
      ['codigo'=>'1.1.1', 'item'=>'Responsable del Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST', 'valor'=>'0,5', 'total'=>'0,5', 'nc'=>'', 'na'=>'', 'calif'=>'2,5'],
      ['codigo'=>'1.1.2', 'item'=>'Responsabilidades en el Sistema de Gestión de Seguridad y Salud en el Trabajo – SG-SST', 'valor'=>'0,5', 'total'=>'0,5', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'1.1.3', 'item'=>'Asignación de recursos para el Sistema de Gestión de Seguridad y Salud en el Trabajo – SG-SST', 'valor'=>'0,5', 'total'=>'0,5', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'1.1.4', 'item'=>'Afiliación al Sistema General de Riesgos Laborales', 'valor'=>'0,5', 'total'=>'0,5', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'1.1.5', 'item'=>'Identificación de trabajadores de alto riesgo y cotización de pensión especial', 'valor'=>'0,5', 'total'=>'', 'nc'=>'', 'na'=>'0,5', 'calif'=>''],
      ['codigo'=>'1.1.6', 'item'=>'Conformación COPASST', 'valor'=>'0,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'1.1.7', 'item'=>'Capacitación COPASST', 'valor'=>'0,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'1.1.8', 'item'=>'Conformación Comité de Convivencia', 'valor'=>'0,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => 'GESTIÓN INTEGRAL DEL SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO (15%)',
    'estandar' => 'Capacitación en el Sistema de Gestión de la Seguridad y Salud en el Trabajo (6%)',
    'peso' => '6',
    'items' => [
      ['codigo'=>'1.2.1', 'item'=>'Programa Capacitación promoción y prevención PYP', 'valor'=>'2', 'total'=>'2', 'nc'=>'', 'na'=>'', 'calif'=>'6'],
      ['codigo'=>'1.2.2', 'item'=>'Inducción y reinducción en Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST, actividades de Promoción y Prevención PyP', 'valor'=>'2', 'total'=>'2', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'1.2.3', 'item'=>'Responsable del Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST con curso virtual de 50 horas', 'valor'=>'2', 'total'=>'2', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Política de Seguridad y Salud en el Trabajo (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.1.1', 'item'=>'Política del Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST firmada, fechada y comunicada al COPASST', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Objetivos del SG SST (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.2.1', 'item'=>'Objetivos definidos, claros, medibles, cuantificables, con metas, documentados, revisados del SG-SST', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Evaluación inicial del SG-SST (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.3.1', 'item'=>'Evaluación e identificación de prioridades', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Plan Anual de Trabajo (2%)',
    'peso' => '2',
    'items' => [
      ['codigo'=>'2.4.1', 'item'=>'Plan que identifica objetivos, metas, responsabilidad, recursos cronograma y firmado', 'valor'=>'2', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>'7'],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Conservación de la documentación (2%)',
    'peso' => '2',
    'items' => [
      ['codigo'=>'2.5.1', 'item'=>'Archivo o retención documental del Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST', 'valor'=>'2', 'total'=>'2', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Rendición de cuentas (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.6.1', 'item'=>'Rendición sobre el desempeño', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Normatividad nacional vigente y aplicable en materia de SST (2%)',
    'peso' => '2',
    'items' => [
      ['codigo'=>'2.7.1', 'item'=>'Matriz legal', 'valor'=>'2', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Comunicación (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.8.1', 'item'=>'Mecanismos de comunicación, auto reporte en Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Adquisiciones (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.9.1', 'item'=>'Identificación, evaluación, para adquisición de productos y servicios en Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Contratación (2%)',
    'peso' => '2',
    'items' => [
      ['codigo'=>'2.10.1', 'item'=>'Evaluación y selección de proveedores y contratistas', 'valor'=>'2', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Gestión del cambio (1%)',
    'peso' => '1',
    'items' => [
      ['codigo'=>'2.11.1', 'item'=>'Evaluación del impacto de cambios internos y externos en el Sistema de Gestión de Seguridad y Salud en el Trabajo SG-SST', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => 'II. HACER',
    'grupo' => 'GESTIÓN DE LA SALUD (20%)',
    'estandar' => 'Condiciones de salud en el trabajo (9%)',
    'peso' => '9',
    'items' => [
      ['codigo'=>'3.1.1', 'item'=>'Descripción sociodemográfica. Diagnóstico de condiciones de salud', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>'5'],
      ['codigo'=>'3.1.2', 'item'=>'Actividades de Promoción y Prevención en Salud', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.3', 'item'=>'Información al médico de los perfiles de cargo', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.4', 'item'=>'Realización de evaluaciones médicas ocupacionales: peligros / periodicidad / comunicación al trabajador', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.5', 'item'=>'Custodia de historias clínicas', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.6', 'item'=>'Restricciones y recomendaciones médico laborales', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.7', 'item'=>'Estilos de vida y entornos saludables', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.8', 'item'=>'Agua potable, servicios sanitarios y disposición de basuras', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.1.9', 'item'=>'Eliminación adecuada de residuos sólidos, líquidos o gaseosos', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Registro, reporte e investigación de enfermedades laborales, incidentes y accidentes del trabajo (5%)',
    'peso' => '5',
    'items' => [
      ['codigo'=>'3.2.1', 'item'=>'Reporte de los accidentes de trabajo y enfermedad laboral a la ARL, EPS y Dirección Territorial del Ministerio de Trabajo', 'valor'=>'2', 'total'=>'2', 'nc'=>'', 'na'=>'', 'calif'=>'5'],
      ['codigo'=>'3.2.2', 'item'=>'Investigación de incidentes, accidentes y enfermedades laborales', 'valor'=>'2', 'total'=>'2', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.2.3', 'item'=>'Registro y análisis estadístico de accidentes y enfermedades laborales', 'valor'=>'1', 'total'=>'1', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Mecanismos de vigilancia de las condiciones de salud de los trabajadores (6%)',
    'peso' => '6',
    'items' => [
      ['codigo'=>'3.3.1', 'item'=>'Medición de la frecuencia de la accidentalidad', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>'0'],
      ['codigo'=>'3.3.2', 'item'=>'Medición de la severidad de la accidentalidad', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.3.3', 'item'=>'Medición de la mortalidad por accidentes de trabajo', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.3.4', 'item'=>'Medición de la prevalencia de enfermedad laboral', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.3.5', 'item'=>'Medición de la incidencia de enfermedad laboral', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'3.3.6', 'item'=>'Medición del ausentismo por causa médica', 'valor'=>'1', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => 'GESTIÓN DE PELIGROS Y RIESGOS (30%)',
    'estandar' => 'Identificación de peligros, evaluación y valoración de los riesgos (15%)',
    'peso' => '15',
    'items' => [
      ['codigo'=>'4.1.1', 'item'=>'Metodología para la identificación de peligros, evaluación y valoración de los riesgos', 'valor'=>'4', 'total'=>'4', 'nc'=>'', 'na'=>'', 'calif'=>'11'],
      ['codigo'=>'4.1.2', 'item'=>'Identificación de peligros con participación de todos los niveles de la empresa', 'valor'=>'4', 'total'=>'4', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'4.1.3', 'item'=>'Identificación de sustancias catalogadas como carcinógenas o con toxicidad aguda', 'valor'=>'3', 'total'=>'', 'nc'=>'', 'na'=>'3', 'calif'=>''],
      ['codigo'=>'4.1.4', 'item'=>'Realización mediciones ambientales, químicos, físicos y biológicos', 'valor'=>'4', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => '',
    'estandar' => 'Medidas de prevención y control para intervenir los peligros/riesgos (15%)',
    'peso' => '15',
    'items' => [
      ['codigo'=>'4.2.1', 'item'=>'Implementación de medidas de prevención y control de peligros/riesgos identificados', 'valor'=>'2,5', 'total'=>'2,5', 'nc'=>'', 'na'=>'', 'calif'=>'7,5'],
      ['codigo'=>'4.2.2', 'item'=>'Verificación de aplicación de medidas de prevención y control por parte de los trabajadores', 'valor'=>'2,5', 'total'=>'2,5', 'nc'=>'', 'na'=>'', 'calif'=>''],
      ['codigo'=>'4.2.3', 'item'=>'Elaboración de procedimientos, instructivos, fichas, protocolos', 'valor'=>'2,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'4.2.4', 'item'=>'Realización de inspecciones sistemáticas a las instalaciones, maquinaria o equipos con participación del COPASST', 'valor'=>'2,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'4.2.5', 'item'=>'Mantenimiento periódico de instalaciones, equipos, máquinas, herramientas', 'valor'=>'2,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'4.2.6', 'item'=>'Entrega de elementos de protección personal EPP, se verifica uso con contratistas y subcontratistas', 'valor'=>'2,5', 'total'=>'2,5', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => '',
    'grupo' => 'GESTIÓN DE AMENAZAS (10%)',
    'estandar' => 'Plan de prevención, preparación y respuesta ante emergencias (10%)',
    'peso' => '10',
    'items' => [
      ['codigo'=>'5.1.1', 'item'=>'Se cuenta con el plan de prevención y preparación ante emergencias', 'valor'=>'5', 'total'=>'5', 'nc'=>'', 'na'=>'', 'calif'=>'5'],
      ['codigo'=>'5.1.2', 'item'=>'Brigada de prevención conformada, capacitada y dotada', 'valor'=>'5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => 'III. VERIFICAR',
    'grupo' => 'VERIFICACIÓN DEL SG-SST (5%)',
    'estandar' => 'Gestión y resultados del SG-SST. (5%)',
    'peso' => '5',
    'items' => [
      ['codigo'=>'6.1.1', 'item'=>'Definición de indicadores del SG-SST de acuerdo a condiciones de la empresa', 'valor'=>'1,25', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>'0'],
      ['codigo'=>'6.1.2', 'item'=>'La empresa adelanta auditoría por lo menos una vez al año', 'valor'=>'1,25', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'6.1.3', 'item'=>'Revisión anual por la alta dirección, resultados y alcance de la auditoría', 'valor'=>'1,25', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'6.1.4', 'item'=>'Planificar auditoría con el COPASST', 'valor'=>'1,25', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
    ]
  ],
  [
    'ciclo' => 'IV. ACTUAR',
    'grupo' => 'MEJORAMIENTO (10%)',
    'estandar' => 'Acciones preventivas y correctivas con base en los resultados del SG-SST. (10%)',
    'peso' => '10',
    'items' => [
      ['codigo'=>'7.1.1', 'item'=>'Definición de acciones preventivas y correctivas con base en resultados del SG-SST', 'valor'=>'2,5', 'total'=>'2,5', 'nc'=>'', 'na'=>'', 'calif'=>'5'],
      ['codigo'=>'7.1.2', 'item'=>'Acciones de mejora conforme a revisión de la alta dirección', 'valor'=>'2,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'7.1.3', 'item'=>'Acciones de mejora con base en investigaciones de accidentes de trabajo y enfermedades laborales', 'valor'=>'2,5', 'total'=>'', 'nc'=>'0', 'na'=>'', 'calif'=>''],
      ['codigo'=>'7.1.4', 'item'=>'Elaboración plan de mejoramiento e implementación de medidas y acciones correctivas solicitadas por autoridades y ARL', 'valor'=>'2,5', 'total'=>'2,5', 'nc'=>'', 'na'=>'', 'calif'=>''],
    ]
  ],
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>2.3.1 - Diagnóstico Inicial</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --blue:#1f5fa8;
      --line:#111;
      --head:#d9e4f2;
      --sub:#eef4fb;
      --gray:#f4f4f4;
    }
    body{
      margin:0;
      background:#eef2f7;
      font-family:Arial, Helvetica, sans-serif;
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
      box-shadow:0 10px 20px rgba(0,0,0,.08);
      padding:14px;
    }
    table.top{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      font-size:11px;
      margin-bottom:8px;
    }
    .top td,.top th{
      border:1px solid var(--line);
      padding:4px 6px;
      vertical-align:middle;
      text-align:center;
      font-weight:700;
    }
    .logo-box{
      border:2px dashed rgba(0,0,0,.2);
      color:rgba(0,0,0,.3);
      min-height:44px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:10px;
      font-weight:800;
    }
    .subnote{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      margin-bottom:8px;
      font-size:11px;
    }
    .subnote td,.subnote th{
      border:1px solid var(--line);
      padding:5px 6px;
      vertical-align:top;
    }
    .legal{
      font-size:10px;
      line-height:1.35;
    }

    .top-scroll{
      overflow-x:auto;
      overflow-y:hidden;
      height:18px;
      margin-bottom:6px;
    }
    .top-scroll-inner{ height:1px; }

    .tbl-scroll{
      overflow:auto;
      border:1px solid #cfd6df;
      max-height:72vh;
    }

    table.diag{
      border-collapse:collapse;
      min-width:1850px;
      width:max-content;
      font-size:10px;
      table-layout:fixed;
    }
    .diag th,.diag td{
      border:1px solid #8f8f8f;
      padding:3px 4px;
      vertical-align:middle;
    }
    .diag th{
      background:var(--head);
      text-align:center;
      font-weight:900;
    }
    .diag .sec-title{
      background:#fff;
      font-weight:900;
      text-align:center;
      font-size:11px;
    }
    .diag .sec-sub{
      background:#fff;
      font-weight:900;
      text-align:center;
    }
    .diag .cycle{
      writing-mode:vertical-rl;
      transform:rotate(180deg);
      text-align:center;
      font-weight:900;
      background:#fafafa;
      min-width:26px;
    }
    .diag .group{
      writing-mode:vertical-rl;
      transform:rotate(180deg);
      text-align:center;
      font-weight:900;
      background:#fcfcfc;
      min-width:32px;
    }
    .diag .estandar{
      background:#fbfbfb;
    }
    .diag .itemtxt{
      text-align:left;
    }
    .diag .center{ text-align:center; }
    .diag .right{ text-align:right; }
    .diag .totales{
      font-weight:900;
      background:#f8f8f8;
    }

    .w-ciclo{ width:34px; }
    .w-grupo{ width:38px; }
    .w-estandar{ width:230px; }
    .w-item{ width:420px; }
    .w-valor{ width:70px; }
    .w-peso{ width:78px; }
    .w-cal{ width:78px; }

    .cell-input{
      width:100%;
      min-width:0;
      box-sizing:border-box;
      border:none;
      background:transparent;
      outline:none;
      font-size:10px;
      text-align:center;
    }
    .cell-input.left{ text-align:left; }

    .footer-sign{
      width:100%;
      border-collapse:collapse;
      margin-top:16px;
      font-size:11px;
    }
    .footer-sign td{
      border-top:1px solid #111;
      padding-top:8px;
      text-align:center;
      font-weight:700;
    }

    @media print{
      body{ background:#fff; }
      .toolbar, .top-scroll{ display:none !important; }
      .sheet{ box-shadow:none; }
      .tbl-scroll{ max-height:none; overflow:visible; border:none; }
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
    <table class="top">
      <colgroup>
        <col style="width:80px">
        <col>
        <col style="width:120px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td>SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
        <td>0<br>AN-SST-02</td>
      </tr>
      <tr>
        <td>DIAGNOSTICO INICIAL</td>
        <td>XX/XX/2025</td>
      </tr>
    </table>

    <table class="subnote">
      <colgroup>
        <col style="width:140px">
        <col style="width:120px">
        <col>
        <col style="width:240px">
        <col style="width:60px">
        <col style="width:60px">
        <col style="width:80px">
      </colgroup>
      <tr>
        <td><strong>Año a Evaluar</strong></td>
        <td class="center"><strong>2025</strong></td>
        <td></td>
        <td><strong>Fecha de Aplicación de la Autoevaluación:</strong></td>
        <td class="center"><strong>1</strong></td>
        <td class="center"><strong>2</strong></td>
        <td class="center"><strong>2024</strong></td>
      </tr>
      <tr>
        <td colspan="7" class="legal">
          <strong>Artículo 27. Tabla de Valores de los Estándares Mínimos - Resolución No. 0312 del 13 de febrero de 2019 por el cual se definen los Estándares Mínimos del Sistema de Gestión de la Seguridad y Salud en el Trabajo.</strong>
          <br><br>
          – Cuando se cumple con el ítem del estándar la calificación será la máxima del respectivo ítem, de lo contrario su calificación será igual a cero (0).
          <br>
          – En los ítems de la Tabla de Valores que no aplican para las empresas de menos de cincuenta (50) trabajadores clasificadas con riesgo I, II o III, de conformidad con los Estándares Mínimos de SST vigentes, se deberá otorgar el porcentaje máximo de calificación en la columna “No Aplica” frente al ítem correspondiente.
          <br>
          – El presente formulario es documento público. La información aquí consignada debe ser veraz. La inclusión de manifestaciones falsas estará sujeta a las sanciones contempladas en la Ley 599 de 2000, Código Penal Colombiano.
        </td>
      </tr>
    </table>

    <div class="top-scroll" id="topScroll">
      <div class="top-scroll-inner" id="topScrollInner"></div>
    </div>

    <div class="tbl-scroll" id="tableScroll">
      <table class="diag" id="diagTable">
        <thead>
          <tr>
            <th colspan="9" class="sec-title">ESTÁNDARES MÍNIMOS SG-SST</th>
          </tr>
          <tr>
            <th colspan="9" class="sec-sub">TABLA DE VALORES Y CALIFICACIÓN</th>
          </tr>
          <tr>
            <th class="w-ciclo">CICLO</th>
            <th class="w-grupo"></th>
            <th class="w-estandar">ESTÁNDAR</th>
            <th class="w-item">ÍTEM DEL ESTÁNDAR</th>
            <th class="w-valor">Valor del ítem del estándar</th>
            <th class="w-peso">PESO PORCENTUAL</th>
            <th colspan="3">Puntaje posible</th>
            <th class="w-cal">Calificación de la empresa o contratante</th>
          </tr>
          <tr>
            <th colspan="6"></th>
            <th class="w-cal">Cumple totalmente</th>
            <th class="w-cal">No cumple</th>
            <th class="w-cal">No aplica</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($rows as $block): ?>
          <?php
            $itemCount = count($block['items']);
            $first = true;
          ?>
          <?php foreach($block['items'] as $idx => $it): ?>
            <tr>
              <?php if($first && $block['ciclo'] !== ''): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="cycle w-ciclo"><?php echo htmlspecialchars($block['ciclo']); ?></td>
              <?php elseif($first && $block['ciclo'] === ''): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="cycle w-ciclo"></td>
              <?php endif; ?>

              <?php if($first && $block['grupo'] !== ''): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="group w-grupo"><?php echo htmlspecialchars($block['grupo']); ?></td>
              <?php elseif($first && $block['grupo'] === ''): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="group w-grupo"></td>
              <?php endif; ?>

              <?php if($first): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="estandar"><?php echo htmlspecialchars($block['estandar']); ?></td>
              <?php endif; ?>

              <td class="itemtxt">
                <?php echo htmlspecialchars($it['codigo'] . '. ' . $it['item']); ?>
              </td>
              <td class="center"><?php echo htmlspecialchars($it['valor']); ?></td>

              <?php if($first): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="center"><strong><?php echo htmlspecialchars($block['peso']); ?></strong></td>
              <?php endif; ?>

              <td class="center"><?php echo htmlspecialchars($it['total']); ?></td>
              <td class="center"><?php echo htmlspecialchars($it['nc']); ?></td>
              <td class="center"><?php echo htmlspecialchars($it['na']); ?></td>

              <?php if($first): ?>
                <td rowspan="<?php echo $itemCount; ?>" class="center"><strong><?php echo htmlspecialchars($it['calif']); ?></strong></td>
              <?php endif; ?>
            </tr>
            <?php $first = false; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>

        <tr class="totales">
          <td colspan="4" class="right"><strong>TOTALES</strong></td>
          <td class="center"></td>
          <td class="center"><strong>100</strong></td>
          <td class="center"></td>
          <td class="center"></td>
          <td class="center"></td>
          <td class="center"><strong>54</strong></td>
        </tr>
        </tbody>
      </table>
    </div>

    <table class="footer-sign">
      <tr>
        <td style="width:50%;">FIRMA DEL EMPLEADOR O CONTRATANTE</td>
        <td style="width:50%;">RESPONSABLE DE LA EJECUCIÓN DEL SG-SST</td>
      </tr>
    </table>
  </div>
</div>

<script>
  const topScroll = document.getElementById('topScroll');
  const tableScroll = document.getElementById('tableScroll');
  const topScrollInner = document.getElementById('topScrollInner');
  const diagTable = document.getElementById('diagTable');

  function syncTopScrollWidth() {
    topScrollInner.style.width = diagTable.scrollWidth + 'px';
  }

  topScroll.addEventListener('scroll', function () {
    tableScroll.scrollLeft = topScroll.scrollLeft;
  });

  tableScroll.addEventListener('scroll', function () {
    topScroll.scrollLeft = tableScroll.scrollLeft;
  });

  window.addEventListener('load', syncTopScrollWidth);
  window.addEventListener('resize', syncTopScrollWidth);
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>