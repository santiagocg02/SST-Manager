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
    <title>2.6.1-2 - Informe de Rendición de Cuentas</title>
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

        .policy-placeholder{
            min-height:120px;
            border:1px dashed #b8c2cc;
            background:#fafcff;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            color:#5c6670;
            font-weight:700;
            padding:20px;
        }

        .graph-placeholder{
            min-height:180px;
            border:1px dashed #b8c2cc;
            background:#fafcff;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            color:#5c6670;
            font-weight:700;
            padding:20px;
            margin-bottom:12px;
        }

        .two-col{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
        }

        .three-col{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:12px;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
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

        .small{ font-size:11px; }
        .center{ text-align:center; }

        @media print{
            body{ background:#fff; }
            .toolbar{ display:none !important; }
            .sheet{ box-shadow:none; margin-bottom:0; border:2px solid #000; }
        }

        @media (max-width: 768px){
            .two-col,
            .three-col,
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
        <div class="cover">
            <div class="cover-logo">LOGO EMPRESA</div>
            <div class="cover-title">REVISIÓN POR LA DIRECCIÓN Y RENDICIÓN DE CUENTAS</div>
            <div class="cover-text">Versión 0</div>
            <div class="cover-text">IN-SST-03</div>
            <div class="cover-text">FECHA: <input type="text" class="edit-inline center" value="XX/XX/2023"></div>
            <div class="cover-text" style="margin-top:20px;">INFORME DE RENDICIÓN DE CUENTAS</div>
            <div class="cover-text">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
            <div class="cover-text"><input type="text" class="edit-inline center" value="EMPRESA"></div>
            <div class="cover-text" style="margin-top:20px;">DECRETO 1072 DE 2015</div>
            <div class="cover-text">RESOLUCIÓN 0312 DEL 2019</div>
            <div class="cover-text" style="margin-top:20px;">PERIODO: <input type="text" class="edit-inline center" value="ENERO – DICIEMBRE 2023"></div>
            <div class="cover-text">FECHA DE REALIZACIÓN: <input type="text" class="edit-inline center" value="23 de Diciembre de 2023"></div>
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
                <td><strong>Versión:</strong> 0</td>
                <td><strong>Código:</strong><br>IN-SST-03</td>
            </tr>
            <tr>
                <td class="subtitle">INFORME DE RENDICIÓN DE CUENTAS</td>
                <td colspan="2"><strong>Fecha:</strong> XX/XX/2023</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Periodo:</strong> Enero - Diciembre 2023</td>
            </tr>
        </table>

        <div class="sec-h">Introducción</div>
        <div class="box text-just">
            <textarea class="edit" rows="8">Las Revisiones Gerenciales son convocadas por el Gerente General de la Empresa o su designado, una vez al año o antes de encontrarse la necesidad. Los aspectos a tener en cuenta como marco para el análisis de las revisiones son:

- La política, los objetivos y metas del SGSST.
- Resultados de indicadores.
- Estrategias implementadas para el cumplimiento de los objetivos y metas.
- Cumplimiento del plan de trabajo.
- Ejecución del presupuesto y suficiencia de los recursos.
- El análisis estadístico del sistema (accidentalidad, incidentalidad, inspecciones, entre otras) y la notificación de accidentes.
- Estado de acciones derivadas de hallazgos al sistema.
- Resultados de implementaciones de acciones preventivas y correctivas.
- El resultado de las auditorías internas y externas.
- Los cambios que puedan afectar el SGSST.
- Requerimientos del COPASST.
- Participación de los trabajadores.
- Requisitos legales de SST.

El presente informe consolida toda la gestión que el Departamento de SST ha ejecutado con relación al Sistema de Gestión de Seguridad y Salud en el Trabajo durante el año evaluado.</textarea>
        </div>

        <div class="sec-h">Política del SG SST</div>
        <div class="policy-placeholder">COLOCAR LA POLÍTICA FIRMADA POR EL REPRESENTANTE LEGAL</div>

        <div class="sec-h">Política de no alcohol, tabaco y sustancias psicoactivas</div>
        <div class="policy-placeholder">COLOCAR LA POLÍTICA FIRMADA POR EL REPRESENTANTE LEGAL</div>

        <div class="sec-h">Ejecución del presupuesto y suficiencia de los recursos</div>
        <div class="box">
            <textarea class="edit" rows="5" placeholder="Coloca el presupuesto de la herramienta SG-SST con el análisis realizado"></textarea>
        </div>

        <div class="sec-h">Cumplimiento del plan de trabajo</div>
        <table class="formtbl">
            <tbody>
                <tr>
                    <th style="width:180px;">Objetivo</th>
                    <td><input class="edit" type="text" value="Planear y controlar la ejecución de las actividades del Sistema de Gestión de Seguridad y Salud en el Trabajo."></td>
                </tr>
                <tr>
                    <th>Meta</th>
                    <td><input class="edit" type="text" value="Cumplir con el 80% de las actividades propuestas"></td>
                </tr>
            </tbody>
        </table>
        <div class="graph-placeholder">INSERTAR GRÁFICA DE CUMPLIMIENTO DE PLAN DE TRABAJO</div>

        <div class="sec-h">Programa de Vigilancia Epidemiológica para la Prevención de Lesiones Osteomusculares</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>OBJETIVO</th>
                    <th style="width:180px;">INDICADOR</th>
                    <th>FÓRMULA DEL INDICADOR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5">Prevenir la aparición de desórdenes músculo esqueléticos a través de la identificación, evaluación e intervención de las condiciones no ergonómicas encontradas en los puestos de trabajo.</td>
                    <td>Prevalencia</td>
                    <td>No. de Casos Nuevos + No. de Casos Antiguos / No. de trabajadores expuestos x 100000</td>
                </tr>
                <tr>
                    <td>Incidencia</td>
                    <td>No. de Casos nuevos / Número de trabajadores expuestos x 100000</td>
                </tr>
                <tr>
                    <td>Cumplimiento</td>
                    <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                </tr>
                <tr>
                    <td>Cobertura</td>
                    <td># de trabajadores que participan / # de trabajadores programados x 100%</td>
                </tr>
                <tr>
                    <td>Eficacia</td>
                    <td>No. de recomendaciones cerradas / Total de recomendaciones x 100%</td>
                </tr>
            </tbody>
        </table>

        <div class="box">
            <strong>Prevalencia e incidencia</strong>
            <textarea class="edit" rows="3">Se mantienen en 0%, ningún caso antiguo ni nuevo calificado como enfermedad laboral con relación a patologías osteomusculares.</textarea>
        </div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - CUMPLIMIENTO PVE OSTEOMUSCULAR</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de cumplimiento según PVE osteomuscular"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - COBERTURA PVE OSTEOMUSCULAR</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de cobertura según PVE osteomuscular"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - EFICACIA PVE OSTEOMUSCULAR</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de eficacia según PVE osteomuscular"></textarea></div>

        <div class="sec-h">Programa de Vigilancia Epidemiológica para la Prevención del Riesgo Psicosocial</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>OBJETIVO</th>
                    <th style="width:180px;">INDICADOR</th>
                    <th>FÓRMULA DEL INDICADOR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5">Implementar un Sistema de Vigilancia Epidemiológica de acuerdo con los parámetros establecidos en la legislación colombiana vigente para preservar, mantener y mejorar la salud individual y colectiva de los trabajadores.</td>
                    <td>Prevalencia</td>
                    <td>No. de Casos Nuevos + No. de Casos Antiguos / No. de trabajadores expuestos x 100000</td>
                </tr>
                <tr>
                    <td>Incidencia</td>
                    <td>No. de Casos nuevos / Número de trabajadores expuestos x 100000</td>
                </tr>
                <tr>
                    <td>Cumplimiento</td>
                    <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                </tr>
                <tr>
                    <td>Cobertura</td>
                    <td># de trabajadores que participan / # de trabajadores programados x 100%</td>
                </tr>
                <tr>
                    <td>Eficacia</td>
                    <td>No. de recomendaciones cerradas / Total de recomendaciones x 100%</td>
                </tr>
            </tbody>
        </table>

        <div class="box">
            <strong>Prevalencia e incidencia</strong>
            <textarea class="edit" rows="3">Se mantienen en 0%, ningún caso antiguo ni nuevo calificado como enfermedad laboral con relación a patologías de riesgo psicosocial.</textarea>
        </div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - CUMPLIMIENTO PVE PSICOSOCIAL</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de cumplimiento según PVE psicosocial"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - COBERTURA PVE PSICOSOCIAL</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de cobertura según PVE psicosocial"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - EFICACIA PVE PSICOSOCIAL</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de eficacia según PVE psicosocial"></textarea></div>

        <div class="sec-h">Programa de Capacitaciones</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>OBJETIVO</th>
                    <th style="width:180px;">INDICADOR</th>
                    <th>FÓRMULA DEL INDICADOR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3">Prevenir la ocurrencia de accidentes y enfermedades laborales por medio de capacitaciones.</td>
                    <td>Cumplimiento</td>
                    <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                </tr>
                <tr>
                    <td>Cobertura</td>
                    <td>No. de trabajadores que participan / No. de trabajadores programados x 100%</td>
                </tr>
                <tr>
                    <td>Eficacia</td>
                    <td>Reducción de la accidentalidad en los trabajadores</td>
                </tr>
            </tbody>
        </table>
        <div class="graph-placeholder">INSERTAR GRÁFICA - CUMPLIMIENTO CAPACITACIONES</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis del cumplimiento del programa de capacitaciones"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - COBERTURA CAPACITACIONES</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de la cobertura del programa de capacitaciones"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - EFICACIA CAPACITACIONES</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de la eficacia del programa de capacitaciones"></textarea></div>

        <div class="sec-h">Programa de Inspecciones</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>OBJETIVO</th>
                    <th style="width:180px;">INDICADOR</th>
                    <th>FÓRMULA DEL INDICADOR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="2">Identificar de manera proactiva condiciones inseguras en las actividades realizadas por los funcionarios, con el fin de corregirlas y minimizar la probabilidad de ocurrencia de lesiones, daños o interrupciones del trabajo.</td>
                    <td>Cumplimiento</td>
                    <td>No. de Actividades Ejecutadas / No. de Actividades Programadas x 100%</td>
                </tr>
                <tr>
                    <td>Eficacia</td>
                    <td>No. de planes de acción desarrollados / No. de planes de acción propuestos</td>
                </tr>
            </tbody>
        </table>
        <div class="graph-placeholder">INSERTAR GRÁFICA - CUMPLIMIENTO INSPECCIONES</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis del cumplimiento del programa de inspecciones"></textarea></div>
        <div class="graph-placeholder">INSERTAR GRÁFICA - EFICACIA INSPECCIONES</div>
        <div class="box"><textarea class="edit" rows="3" placeholder="Realizar análisis de la eficacia del programa de inspecciones"></textarea></div>

        <div class="sec-h">Indicadores de accidentalidad</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>MES</th>
                    <th style="width:120px;">No. Casos</th>
                    <th>TIPO DE LESIÓN</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Marzo</td><td class="center">1</td><td>CONTUSIÓN</td></tr>
                <tr><td>Mayo</td><td class="center">2</td><td>CONTUSIÓN – TORCEDURA</td></tr>
                <tr><td>Junio</td><td class="center">1</td><td>CAÍDA A UN MISMO NIVEL</td></tr>
                <tr><td>Julio</td><td class="center">1</td><td>CAÍDA A UN MISMO NIVEL</td></tr>
                <tr><td>Diciembre</td><td class="center">1</td><td>OTRO TIPO DE CONTACTO</td></tr>
                <tr><th>TOTAL</th><th class="center">6</th><th></th></tr>
            </tbody>
        </table>

        <div class="box">
            <textarea class="edit" rows="5">Durante el periodo enero – diciembre se presentaron accidentes de trabajo. Realizar aquí el análisis de accidentalidad, comparativo con periodos anteriores, mecanismos de lesión y severidad.</textarea>
        </div>

        <table class="formtbl">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th style="width:180px;">Meta</th>
                    <th>Fórmula</th>
                    <th>Análisis</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Frecuencia de accidentalidad</td>
                    <td>&lt; 3%</td>
                    <td>Número de accidentes de trabajo en el último periodo / Total de trabajadores x 100</td>
                    <td><textarea class="edit" rows="3"></textarea></td>
                </tr>
                <tr>
                    <td>Severidad de accidentalidad</td>
                    <td>&lt; 15</td>
                    <td>(Número de días de incapacidad por accidente de trabajo en el mes + número de días cargados en el mes / Número de trabajadores en el mes) * 100</td>
                    <td><textarea class="edit" rows="3"></textarea></td>
                </tr>
                <tr>
                    <td>Tasa de incidencia</td>
                    <td>&lt; 2%</td>
                    <td>AT * 100 / Número de trabajadores</td>
                    <td><textarea class="edit" rows="3"></textarea></td>
                </tr>
                <tr>
                    <td>Índice de mortalidad</td>
                    <td>0</td>
                    <td>Número de eventos mortales / total de accidentes presentados en el periodo * 100</td>
                    <td><textarea class="edit" rows="3">Durante el periodo no se presentan accidentes mortales en la organización.</textarea></td>
                </tr>
                <tr>
                    <td>Ausentismo por causa médica</td>
                    <td>&lt; 2%</td>
                    <td>(Número de días de ausencia por incapacidad laboral o común en el mes / Número de días de trabajo programados en el mes) * 100</td>
                    <td><textarea class="edit" rows="3"></textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Ausentismo general</div>
        <table class="formtbl">
            <tbody>
                <tr>
                    <th style="width:180px;">Objetivo</th>
                    <td>Controlar las estadísticas de ausentismo</td>
                    <th style="width:140px;">Meta</th>
                    <td>&lt;= 4%</td>
                </tr>
                <tr>
                    <th>Indicador</th>
                    <td>Ausentismo Laboral Global = (Total Días por Ausentismo / D.T) x 100</td>
                    <th>Frecuencia</th>
                    <td>Mensual / Anual</td>
                </tr>
            </tbody>
        </table>

        <table class="formtbl">
            <thead>
                <tr>
                    <th>MES</th>
                    <th># DE TRABAJADORES</th>
                    <th>TOTAL DÍAS TRABAJADOS</th>
                    <th>DÍAS PERDIDOS</th>
                    <th>HORAS PERDIDAS</th>
                    <th>% AUSENTISMO</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $meses = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
                foreach($meses as $mes):
                ?>
                <tr>
                    <td><?php echo $mes; ?></td>
                    <td><input class="edit center" type="text"></td>
                    <td><input class="edit center" type="text"></td>
                    <td><input class="edit center" type="text"></td>
                    <td><input class="edit center" type="text"></td>
                    <td><input class="edit center" type="text"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="graph-placeholder">INSERTAR GRÁFICA DE AUSENTISMO</div>
        <div class="graph-placeholder">INSERTAR GRÁFICA MOTIVO DEL AUSENTISMO</div>
        <div class="box"><textarea class="edit" rows="5">La tasa general de ausentismo debe analizarse aquí con sus principales causas y tendencias.</textarea></div>

        <div class="sec-h">Resolución 312 de 2019 - Eficacia del SG SST</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>Criterios de eficacia</th>
                    <th style="width:180px;">Puntaje obtenido en evidencia</th>
                    <th style="width:180px;">Puntaje obtenido en implementación</th>
                    <th style="width:120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>RECURSOS</td><td class="center">5,5</td><td class="center">10</td><td class="center">55%</td></tr>
                <tr><td>GESTIÓN INTEGRAL DEL SG SST</td><td class="center">8,0</td><td class="center">15</td><td class="center">53%</td></tr>
                <tr><td>GESTIÓN DE LA SALUD</td><td class="center">17,0</td><td class="center">20</td><td class="center">85%</td></tr>
                <tr><td>GESTIÓN DE PELIGROS Y RIESGOS</td><td class="center">25,0</td><td class="center">30</td><td class="center">83%</td></tr>
                <tr><td>GESTIÓN DE AMENAZAS</td><td class="center">5,0</td><td class="center">10</td><td class="center">50%</td></tr>
                <tr><td>VERIFICACIÓN DEL SG-SST</td><td class="center">1,3</td><td class="center">5</td><td class="center">25%</td></tr>
                <tr><td>MEJORAMIENTO</td><td class="center">2,5</td><td class="center">10</td><td class="center">25%</td></tr>
                <tr><th>Puntaje de eficacia del SG SST</th><th class="center">64,3</th><th class="center">100</th><th class="center">64,3%</th></tr>
            </tbody>
        </table>
        <div class="graph-placeholder">INSERTAR GRÁFICA DE CUMPLIMIENTO DEL SG SST</div>

        <div class="sec-h">COPASST</div>
        <div class="box">
            <textarea class="edit" rows="4" placeholder="Ingrese la tabla de seguimiento del COPASST y realice un análisis del cumplimiento de actividades"></textarea>
        </div>

        <div class="sec-h">Otras actividades de seguridad y salud en el trabajo</div>
        <div class="box">
            <textarea class="edit" rows="4"></textarea>
        </div>

        <div class="sec-h">Requisitos legales SG SST</div>
        <table class="formtbl">
            <tbody>
                <tr>
                    <th style="width:180px;">Objetivo</th>
                    <td>Calcular el porcentaje de cumplimiento de los requisitos legales de seguridad y salud en el trabajo aplicables anualmente.</td>
                </tr>
                <tr>
                    <th>Indicador</th>
                    <td>(No. de requisitos aplicables que se cumplen / No. de requisitos aplicables) x 100</td>
                </tr>
                <tr>
                    <th>Meta</th>
                    <td>90%</td>
                </tr>
                <tr>
                    <th>Frecuencia</th>
                    <td>Mensual / Anual</td>
                </tr>
            </tbody>
        </table>
        <div class="box">
            <textarea class="edit" rows="5">El cumplimiento de los requisitos legales durante el periodo evaluado fue de ____%, de ____ requisitos aplicables se dio cumplimiento a ____ requisitos. Pendiente de ejecución: ________________________________.</textarea>
        </div>

        <div class="sec-h">Participación de los trabajadores</div>
        <div class="box">
            <textarea class="edit" rows="10">La empresa brinda a sus empleados diversas vías de participación para establecer una comunicación permanente y efectiva que permita conocer las necesidades, dudas e inquietudes que ayuden a mejorar la gestión del Sistema de Seguridad y Salud en el Trabajo.

Comunicación interna:
Comités formales:
Capacitaciones y talleres:
Participación en la identificación de peligros:</textarea>
        </div>

        <div class="sec-h">Resultado de la auditoría interna al SG SST</div>
        <div class="graph-placeholder">INSERTAR GRÁFICA DE CUMPLIMIENTO</div>
        <div class="box">
            <textarea class="edit" rows="5">El Sistema de Gestión de Seguridad y Salud en el Trabajo presenta avances en la implementación de los requisitos establecidos en el Decreto 1072 de 2015 y Resolución 0312 de 2019. Realizar aquí el análisis respectivo según la auditoría realizada.</textarea>
        </div>

        <div class="sec-h">Seguimiento y mejora continua: acciones correctivas y preventivas</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:60px;">No.</th>
                    <th>Descripción de la no conformidad</th>
                    <th style="width:160px;">Tipo de acción</th>
                    <th>Plan de acción</th>
                    <th style="width:160px;">Estado de la acción</th>
                </tr>
            </thead>
            <tbody>
                <?php for($i=1; $i<=6; $i++): ?>
                <tr>
                    <td class="center"><?php echo $i; ?></td>
                    <td><textarea class="edit" rows="3"></textarea></td>
                    <td><input class="edit" type="text" value="<?php echo $i === 1 ? 'CORRECTIVA' : ''; ?>"></td>
                    <td><textarea class="edit" rows="3"></textarea></td>
                    <td><input class="edit" type="text" value="<?php echo $i === 1 ? 'ABIERTA' : ''; ?>"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="sec-h">Gestión de contratistas</div>
        <div class="box">
            <textarea class="edit" rows="4" placeholder="Describir las actividades relacionadas con la gestión de contratistas de la organización"></textarea>
        </div>

        <div class="sec-h">Estrategias implementadas para el cumplimiento de objetivos y metas del SG-SST</div>
        <div class="box">
            <textarea class="edit" rows="10">Ingresar las estrategias y las mejoras a realizar para el siguiente periodo.

Ejemplo:
Cada año se realiza el diseño de un plan de trabajo de acuerdo con las necesidades de los funcionarios en temas de Seguridad y Salud en el Trabajo.
Desarrollo de una matriz con contenido acerca de la normatividad y regulaciones en torno a la Seguridad y Salud en el Trabajo.
Con el fin de prevenir y controlar la accidentalidad se lleva a cabo un registro de todos los accidentes reportados.
En los casos de enfermedad laboral, se generan actividades de prevención y promoción en el marco de los sistemas de vigilancia epidemiológica.</textarea>
        </div>

        <div class="sign-grid">
            <div class="sign">
                REVISADO Y APROBADO POR:<br><br>
                REPRESENTANTE LEGAL
            </div>
            <div class="sign">
                REVISADO Y APROBADO POR:<br><br>
                REPRESENTANTE DEL SGSST
            </div>
        </div>

        <div class="box small" style="margin-top:18px;">
            NOTA: Recuerde que este es un modelo que ayudará a su quehacer diario; es importante aplicar su conocimiento como profesional SST y agregar los ítems que usted crea necesarios.
        </div>
    </div>
</div>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>