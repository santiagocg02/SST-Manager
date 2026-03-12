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
    <title>1.1.2-2 - Manual de Funciones y Competencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --blue:#1f5fa8;
            --line:#111;
            --soft:#eef3fb;
            --head:#d9e4f2;
            --bg:#eef2f7;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:#1b1b1b;
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
            min-height:880px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            padding:30px 20px;
        }

        .cover .cover-mini{
            font-size:13px;
            font-weight:700;
            margin-bottom:14px;
        }

        .cover .cover-title{
            font-size:28px;
            font-weight:900;
            line-height:1.25;
            text-transform:uppercase;
            max-width:700px;
            margin-bottom:24px;
        }

        .cover .cover-logo{
            width:180px;
            height:140px;
            border:2px dashed rgba(0,0,0,.25);
            display:flex;
            align-items:center;
            justify-content:center;
            color:rgba(0,0,0,.35);
            font-weight:800;
            margin-bottom:30px;
        }

        .cover .cover-company,
        .cover .cover-date,
        .cover .cover-version{
            font-size:16px;
            font-weight:700;
            margin-bottom:10px;
        }

        .box{
            border:1px solid #1f1f1f;
            padding:12px;
            margin-bottom:12px;
        }

        .in{
            width:100%;
            min-width:0;
            height:34px;
            border:1px solid #b8b8b8;
            border-radius:8px;
            background:#fafafa;
            padding:6px 10px;
            outline:none;
            box-sizing:border-box;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        textarea.in{
            height:auto;
            min-height:84px;
            resize:vertical;
            white-space:normal;
            overflow:auto;
            text-overflow:initial;
        }

        .in.inline{
            display:inline-block;
            width:auto;
            min-width:180px;
            max-width:100%;
            vertical-align:middle;
        }

        .in.short{ min-width:120px; }
        .in.medium{ min-width:220px; }
        .in.long{ min-width:320px; }

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

        .text-just{
            text-align:justify;
            line-height:1.6;
        }

        .small{
            font-size:11px;
        }

        .list-box{
            margin:0;
            padding-left:18px;
        }

        .list-box li{
            margin-bottom:8px;
            text-align:justify;
            line-height:1.5;
        }

        .profile-title{
            background:#eef3fb;
            border:1px solid #c9d6ea;
            padding:10px 12px;
            font-weight:900;
            text-transform:uppercase;
            margin-bottom:10px;
        }

        .grid-2{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
        }

        .signature-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:12px;
            margin-top:12px;
        }

        .sign-box{
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
            .grid-2,
            .signature-grid{
                grid-template-columns:1fr;
            }
            .cover .cover-title{
                font-size:22px;
            }
        }
    </style>
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
                <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO SG SST - PESV</td>
                <td><strong>Versión:</strong> 0</td>
                <td><strong>Fecha:</strong><br>XX-XX-XXXX</td>
            </tr>
            <tr>
                <td class="subtitle">MANUAL DE FUNCIONES Y COMPETENCIAS</td>
                <td class="title" colspan="2">MA-XX-SST-03</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
            </tr>
        </table>

        <div class="cover">
            <div class="cover-mini">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO SG SST - PESV</div>
            <div class="cover-title">MANUAL DE FUNCIONES Y COMPETENCIAS</div>
            <div class="cover-logo">LOGO</div>
            <div class="cover-version">Versión 0</div>
            <div class="cover-company">
                <input type="text" class="in center" style="max-width:420px;margin:0 auto;" placeholder="NOMBRE DE LA EMPRESA">
            </div>
            <div class="cover-date">
                <input type="text" class="in center" style="max-width:280px;margin:0 auto;" placeholder="FECHA">
            </div>
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
                <td class="title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO SG SST - PESV</td>
                <td><strong>Versión:</strong> 0</td>
                <td><strong>Fecha:</strong><br>XX-XX-XXXX</td>
            </tr>
            <tr>
                <td class="subtitle">MANUAL DE FUNCIONES Y COMPETENCIAS</td>
                <td class="title" colspan="2">MA-XX-SST-03</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
            </tr>
        </table>

        <div class="sec-h">Control de documentos</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>Elaboró por</th>
                    <th>Revisado por</th>
                    <th>Aprobado por</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input class="in" type="text" placeholder="Nombre y cargo"></td>
                    <td><input class="in" type="text" placeholder="Nombre y cargo"></td>
                    <td><input class="in" type="text" placeholder="Nombre y cargo"></td>
                </tr>
                <tr>
                    <td><input class="in" type="text" placeholder="Firma / fecha"></td>
                    <td><input class="in" type="text" placeholder="Firma / fecha"></td>
                    <td><input class="in" type="text" placeholder="Firma / fecha"></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Control de cambios</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:90px;">Revisión</th>
                    <th style="width:140px;">Fecha</th>
                    <th>Descripción del cambio</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">0</td>
                    <td><input class="in" type="text" placeholder="XX/XX/XXXX"></td>
                    <td><input class="in" type="text" value="Creación del Manual de Funciones y competencias"></td>
                </tr>
                <tr>
                    <td><input class="in center" type="text"></td>
                    <td><input class="in" type="text"></td>
                    <td><input class="in" type="text"></td>
                </tr>
                <tr>
                    <td><input class="in center" type="text"></td>
                    <td><input class="in" type="text"></td>
                    <td><input class="in" type="text"></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Contenido</div>
        <table class="formtbl">
            <tbody>
                <tr><td>1. OBJETIVO</td><td class="center" style="width:90px;">4</td></tr>
                <tr><td>2. ALCANCE</td><td class="center">4</td></tr>
                <tr><td>3. DEFINICIONES</td><td class="center">4</td></tr>
                <tr><td>4. CONDICIONES GENERALES</td><td class="center">6</td></tr>
                <tr><td>5. CONTENIDO</td><td class="center">7</td></tr>
                <tr><td>5.1 Funciones y competencias del cargo</td><td class="center">7</td></tr>
            </tbody>
        </table>

        <div class="sec-h">1. Objetivo</div>
        <div class="box text-just">
            Establecer las funciones y competencias laborales del personal encargado de desempeñar los cargos que llevan a cabo los procesos de la empresa.
        </div>

        <div class="sec-h">2. Alcance</div>
        <div class="box text-just">
            Este manual aplica para todos los niveles de la estructura organizacional de la empresa.
        </div>

        <div class="sec-h">3. Definiciones</div>
        <table class="formtbl">
            <tbody>
                <tr>
                    <td style="width:180px;"><strong>Cargo</strong></td>
                    <td>Es la agrupación de todas aquellas actividades o tareas realizadas por un solo trabajador que ocupe un lugar específico dentro del organigrama de la empresa.</td>
                </tr>
                <tr>
                    <td><strong>Funciones</strong></td>
                    <td>Son las tareas que el trabajador debe realizar o ejecutar.</td>
                </tr>
                <tr>
                    <td><strong>Responsabilidad</strong></td>
                    <td>Es asumir las consecuencias de los resultados de las tareas desarrolladas por sí mismo o por personas a cargo.</td>
                </tr>
                <tr>
                    <td><strong>Autoridad</strong></td>
                    <td>Equivale a rendir cuentas, poder de decisión y a los cargos más altos de la pirámide organizacional.</td>
                </tr>
                <tr>
                    <td><strong>Competencia</strong></td>
                    <td>Capacidad para aplicar conocimientos y habilidades con el fin de lograr los resultados previstos.</td>
                </tr>
                <tr>
                    <td><strong>Educación</strong></td>
                    <td>Adquisición de conocimientos académicos mediante estudio formal.</td>
                </tr>
                <tr>
                    <td><strong>Experiencia</strong></td>
                    <td>Conocimiento, habilidades y destrezas desarrolladas o adquiridas mediante el ejercicio de una profesión, arte u oficio.</td>
                </tr>
                <tr>
                    <td><strong>Experiencia laboral</strong></td>
                    <td>Es la adquirida con el ejercicio de cualquier empleo, ocupación, arte u oficio.</td>
                </tr>
                <tr>
                    <td><strong>Experiencia específica</strong></td>
                    <td>Es la obtenida en la realización de actividades del cargo específico de la vacante ofertada.</td>
                </tr>
                <tr>
                    <td><strong>Experiencia relacionada</strong></td>
                    <td>Es la adquirida en el ejercicio de empleos o actividades que tengan funciones similares a las del cargo a proveer.</td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">4. Condiciones generales</div>
        <div class="box">
            <ul class="list-box">
                <li>Las funciones definidas en el presente manual deberán ser cumplidas por todo el personal de la organización con criterios de eficiencia y eficacia en el logro de la misión, visión, objetivos estratégicos, políticas y funciones que la ley, estatutos y reglamentos internos le señalen a la empresa.</li>
                <li>El jefe del proceso de Gestión del Talento Humano tiene la autoridad para homologar los requisitos establecidos en las competencias laborales de cada cargo, pudiendo homologar educación por experiencia y viceversa.</li>
                <li>La convalidación o eliminación de requisitos para seleccionar a un aspirante solo podrá ser realizada por la gerencia o la junta de socios.</li>
                <li>El jefe del proceso de Gestión del Talento Humano es responsable de hacer cumplir lo establecido en este manual y socializarlo a cada trabajador.</li>
                <li>Los jefes inmediatos deben responder por la orientación del trabajador para el cumplimiento de las funciones que le correspondan.</li>
            </ul>
        </div>

        <div class="sec-h">5. Funciones y competencias del cargo</div>

        <div class="profile-title">Perfil 1 - Encargado del SG SST - PESV</div>

        <table class="formtbl">
            <tbody>
                <tr>
                    <th style="width:220px;">Nivel</th>
                    <td><input class="in" type="text" value="DIRECTIVO (A)"></td>
                </tr>
                <tr>
                    <th>Denominación del empleo</th>
                    <td><input class="in" type="text" value="ENCARGADO DEL SG SST - PESV"></td>
                </tr>
                <tr>
                    <th>Dependencia</th>
                    <td><input class="in" type="text" value="PROFESIONAL"></td>
                </tr>
                <tr>
                    <th>Propósito principal</th>
                    <td><textarea class="in" rows="3" placeholder="Describa el propósito principal del cargo"></textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Funciones específicas</div>
        <table class="formtbl">
            <tbody>
                <?php for($i=1; $i<=8; $i++): ?>
                <tr>
                    <td style="width:50px;" class="center"><strong><?php echo $i; ?></strong></td>
                    <td><textarea class="in" rows="2" placeholder="Función específica <?php echo $i; ?>"></textarea></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="sec-h">Responsabilidades frente al Sistema de Seguridad y Salud en el Trabajo</div>
        <table class="formtbl">
            <tbody>
                <tr><td>1</td><td>Cumplir con la normatividad establecida por la legislación en seguridad y salud en el trabajo vigente.</td></tr>
                <tr><td>2</td><td>Informar al jefe inmediato o miembros del COPASST sobre las condiciones y/o acciones inseguras en los lugares de trabajo y presentar sugerencias de corrección.</td></tr>
                <tr><td>3</td><td>Participar activamente en las charlas y cursos de capacitación en seguridad y salud en el trabajo.</td></tr>
                <tr><td>4</td><td>Participar activamente en los grupos de seguridad y salud en el trabajo que se conformen en la empresa como COPASST, Comité de convivencia laboral y brigadas de emergencia.</td></tr>
                <tr><td>5</td><td>Hacer adecuado uso de las instalaciones, máquinas, equipos, herramientas y elementos de protección personal asignados para desarrollar su labor.</td></tr>
                <tr><td>6</td><td>Acatar y atender las recomendaciones en seguridad y salud en el trabajo del jefe inmediato.</td></tr>
                <tr><td>7</td><td>Aplicar los procedimientos y programas establecidos en materia de seguridad y salud en el trabajo.</td></tr>
                <tr><td>8</td><td>Reportar inmediatamente a su jefe inmediato los accidentes de trabajo que se presenten y participar de la investigación de los mismos.</td></tr>
                <tr><td>9</td><td>Realizar las actividades haciendo un buen uso de los recursos naturales y físicos.</td></tr>
                <tr><td>10</td><td>Suministrar información clara, veraz y completa sobre su estado de salud.</td></tr>
                <tr><td>11</td><td>Realizar inspecciones gerenciales.</td></tr>
                <tr><td>12</td><td>Garantizar el buen funcionamiento del sistema de gestión de SST.</td></tr>
                <tr><td>13</td><td>Realizar revisión por la gerencia del sistema en general.</td></tr>
                <tr><td>14</td><td>Definir y aprobar las políticas, reglamentos, manuales y procedimientos definidos al interior de la organización para el sistema de gestión SST.</td></tr>
                <tr><td>15</td><td>Conocer, entender y cumplir las políticas organizacionales.</td></tr>
                <tr><td>16</td><td>Otras responsabilidades derivadas del sistema de gestión SST.</td></tr>
            </tbody>
        </table>

        <div class="sec-h">Responsabilidades frente al Plan Estratégico de Seguridad Vial</div>
        <table class="formtbl">
            <tbody>
                <tr><td>1</td><td>Planear, organizar, dirigir, desarrollar y aplicar el PESV, y como mínimo una vez al año realizar su evaluación.</td></tr>
                <tr><td>2</td><td>Verificar el cumplimiento y desempeño del PESV.</td></tr>
                <tr><td>3</td><td>Informar a la alta gerencia sobre el funcionamiento y los resultados del PESV.</td></tr>
                <tr><td>4</td><td>Promover la participación de todos los miembros de la empresa en la implementación del PESV.</td></tr>
                <tr><td>5</td><td>Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del PESV.</td></tr>
                <tr><td>6</td><td>Programar las auditorías internas necesarias para el mantenimiento del PESV.</td></tr>
                <tr><td>7</td><td>Reportar siniestros viales en desplazamientos laborales.</td></tr>
                <tr><td>8</td><td>Participar en las capacitaciones de seguridad vial.</td></tr>
                <tr><td>9</td><td>Cumplir con los lineamientos de seguridad vial en la organización.</td></tr>
                <tr><td>10</td><td>Reportar de manera oportuna y veraz el estado de sus condiciones de salud.</td></tr>
            </tbody>
        </table>

        <div class="sec-h">Competencias transversales</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:220px;">Categoría</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Conducción de trabajo</strong></td>
                    <td>Capacidad para dirigir grupos de colaboradores de alto desempeño, distribuir tareas y delegar autoridad, generando oportunidades de aprendizaje y crecimiento.</td>
                </tr>
                <tr>
                    <td><strong>Liderazgo</strong></td>
                    <td>Capacidad para definir y comunicar la visión organizacional y generar entusiasmo, compromiso y orientación al logro de objetivos.</td>
                </tr>
                <tr>
                    <td><strong>Visión estratégica</strong></td>
                    <td>Capacidad para anticiparse y comprender los cambios del entorno, y establecer su impacto a corto, mediano y largo plazo en la organización.</td>
                </tr>
                <tr>
                    <td><strong>Manejo de crisis</strong></td>
                    <td><textarea class="in" rows="2" placeholder="Descripción de la competencia"></textarea></td>
                </tr>
                <tr>
                    <td><strong>Responsabilidad social</strong></td>
                    <td>Capacidad para identificarse con las políticas organizacionales en materia de responsabilidad social y colaborar con la sociedad en aquello relacionado con sus tareas.</td>
                </tr>
                <tr>
                    <td><strong>Habilidades sociales</strong></td>
                    <td>Capacidad para establecer y mantener relaciones sociales con las demás personas.</td>
                </tr>
                <tr>
                    <td><strong>Toma de decisiones</strong></td>
                    <td>Capacidad para tomar decisiones constantemente y de manera rápida, con prontitud y seguridad.</td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Autoridad</div>
        <div class="box text-just">
            Tiene autoridad para representar a la alta dirección en todos los temas del PESV.
        </div>

        <div class="profile-title">Perfil 2 - Editable</div>

        <table class="formtbl">
            <tbody>
                <tr>
                    <th style="width:220px;">Nivel</th>
                    <td><input class="in" type="text" value="DIRECTIVO (A)"></td>
                </tr>
                <tr>
                    <th>Denominación del empleo</th>
                    <td><input class="in" type="text" value="PERFIL 2"></td>
                </tr>
                <tr>
                    <th>Dependencia</th>
                    <td><input class="in" type="text" value="PROFESIONAL"></td>
                </tr>
                <tr>
                    <th>Propósito principal</th>
                    <td><textarea class="in" rows="3" placeholder="Describa el propósito principal del cargo"></textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Funciones específicas - Perfil 2</div>
        <table class="formtbl">
            <tbody>
                <?php for($i=1; $i<=8; $i++): ?>
                <tr>
                    <td style="width:50px;" class="center"><strong><?php echo $i; ?></strong></td>
                    <td><textarea class="in" rows="2" placeholder="Función específica perfil 2 - <?php echo $i; ?>"></textarea></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="sec-h">Responsabilidades SG-SST - Perfil 2</div>
        <table class="formtbl">
            <tbody>
                <?php for($i=1; $i<=10; $i++): ?>
                <tr>
                    <td style="width:50px;" class="center"><?php echo $i; ?></td>
                    <td><textarea class="in" rows="2" placeholder="Responsabilidad SG-SST perfil 2"></textarea></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="sec-h">Responsabilidades PESV - Perfil 2</div>
        <table class="formtbl">
            <tbody>
                <?php for($i=1; $i<=8; $i++): ?>
                <tr>
                    <td style="width:50px;" class="center"><?php echo $i; ?></td>
                    <td><textarea class="in" rows="2" placeholder="Responsabilidad PESV perfil 2"></textarea></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="sec-h">Aprobación</div>
        <div class="signature-grid">
            <div class="sign-box">ELABORÓ</div>
            <div class="sign-box">REVISÓ</div>
            <div class="sign-box">APROBÓ</div>
        </div>
    </div>
</div>
</body>
</html>