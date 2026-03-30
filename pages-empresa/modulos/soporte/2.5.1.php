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
    <title>2.5.1 - Manual de Control de Documentos y Cambios</title>
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

        .format td, .format th{
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

        .cover .cover-title{
            font-size:28px;
            font-weight:900;
            text-transform:uppercase;
            line-height:1.25;
            max-width:720px;
            margin-bottom:24px;
        }

        .cover .cover-logo{
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

        .cover .cover-text{
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
            line-height:1.6;
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
            min-height:74px;
            line-height:1.5;
        }

        .center{ text-align:center; }
        .small{ font-size:11px; }

        .list-box{
            margin:0;
            padding-left:18px;
        }

        .list-box li{
            margin-bottom:8px;
            line-height:1.55;
            text-align:justify;
        }

        .flow-note{
            background:#f8fbff;
            border:1px solid #d7e2ef;
            padding:10px 12px;
            margin-bottom:10px;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:12px;
            margin-top:12px;
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
            .cover .cover-title{
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
                <td><strong>Versión:</strong> 0</td>
                <td><strong>Fecha:</strong><br>XX/XX/20XX</td>
            </tr>
            <tr>
                <td class="subtitle">MANUAL DE CONTROL DE DOCUMENTOS Y CAMBIOS</td>
                <td class="title" colspan="2">MA-XX-SST-01</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
            </tr>
        </table>

        <div class="cover">
            <div class="cover-title">MANUAL DE CONTROL DE DOCUMENTOS Y CONTROL DE CAMBIOS</div>
            <div class="cover-logo">LOGO</div>
            <div class="cover-text">Versión 0</div>
            <div class="cover-text"><input type="text" class="edit-inline center" value="NOMBRE EMPRESA"></div>
            <div class="cover-text"><input type="text" class="edit-inline center" value="FECHA"></div>
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
                <td><strong>Fecha:</strong><br>XX/XX/20XX</td>
            </tr>
            <tr>
                <td class="subtitle">MANUAL DE CONTROL DE DOCUMENTOS Y CAMBIOS</td>
                <td class="title" colspan="2">MA-XX-SST-01</td>
            </tr>
            <tr>
                <td colspan="3"><strong>Proceso:</strong> Gestión de Seguridad y Salud en el Trabajo</td>
            </tr>
        </table>

        <div class="sec-h">Introducción</div>
        <div class="box text-just">
            <textarea class="edit" rows="5">El presente manual sirve de guía en LA EMPRESA, para el manejo y control documental de todos sus procesos estratégicos, con el fin de tener un manejo más claro, ágil y eficiente. Además, como herramienta para desarrollar la codificación, numeración y el cumplimiento de normas ICONTEC.

La cual implementada de forma adecuada evidenciara el éxito del objetivo planteado.</textarea>
        </div>

        <div class="sec-h">Objetivo</div>
        <div class="box text-just">
            <textarea class="edit" rows="3">Con este manual se pretende estandarizar los procesos LA EMPRESA, para dar agilidad a estos, de una manera entendible y fácil de utilizar.</textarea>
        </div>

        <div class="sec-h">Campo de aplicación</div>
        <div class="box text-just">
            <textarea class="edit" rows="3">Aplica a todos los colaboradores de LA EMPRESA, que generen documentos que representen la empresa o den apoyo al Sistema de Gestión de Seguridad y Salud en el trabajo, entre otros.</textarea>
        </div>

        <div class="sec-h">Normas generales para la elaboración de la documentación y correspondencia</div>
        <div class="box text-just">
            <p>Para elaboración es importante tener en cuenta algunas técnicas sobre la forma de elaborar los documentos con el fin de normalizar su producción. El ICONTEC nos da herramientas que facilitan la gestión de la documentación.</p>

            <p><strong>Márgenes:</strong> Se deben conservar las siguientes márgenes en el documento:</p>

            <table class="formtbl">
                <tbody>
                    <tr><td style="width:180px;"><strong>Superior</strong></td><td><input class="edit" type="text" value="3 cm"></td></tr>
                    <tr><td><strong>Izquierdo</strong></td><td><input class="edit" type="text" value="4 cm"></td></tr>
                    <tr><td><strong>Derecho</strong></td><td><input class="edit" type="text" value="2 cm"></td></tr>
                    <tr><td><strong>Inferior</strong></td><td><input class="edit" type="text" value="3 cm"></td></tr>
                </tbody>
            </table>

            <textarea class="edit" rows="3">En caso de ser impreso por ambas caras todas las márgenes deben ser de 3 cm. Los títulos de cada capítulo deben estar en hojas independientes a 3 cm del borde superior.</textarea>
        </div>

        <div class="sec-h">Portada</div>
        <div class="box text-just">
            <p>La portada de los documentos que se utiliza para el sistema de gestión de la seguridad y salud en el trabajo, manuales, programas, procedimientos e instructivos es la siguiente:</p>
            <ul class="list-box">
                <li>Título del documento: se escribe centrado horizontalmente, en letra Arial 20, en negrilla y en mayúscula sostenida.</li>
                <li>Logotipo de la empresa: centrado en la parte media, de 4 cm de alto por 12 cm de ancho.</li>
                <li>Número de la versión: se escribe la palabra “Versión” seguida del número correspondiente.</li>
                <li>Nombre completo de la empresa centrado horizontalmente al final de la página en letra Arial 12, en negrilla y mayúscula sostenida.</li>
                <li>Fecha: se indica el mes y el año en que la versión del documento ha sido aprobado.</li>
            </ul>
        </div>

        <div class="sec-h">Encabezado del documento</div>
        <div class="box text-just">
            <p>En todas las hojas, a excepción de la portada, debe aparecer el cuadro que identifica el documento y contiene la siguiente información:</p>
            <ol class="list-box">
                <li>Escudo o logo de la empresa.</li>
                <li>Nombre del sistema en mayúscula sostenida, negrita y con letra Arial 12.</li>
                <li>Título del documento en letra Arial 12, sin negrilla y en mayúscula sostenida.</li>
                <li>Versión del documento.</li>
                <li>Código del documento.</li>
                <li>Fecha de emisión y/o actualización del documento.</li>
            </ol>
        </div>

        <div class="sec-h">Lineamientos de redacción</div>
        <table class="formtbl">
            <tbody>
                <tr>
                    <td style="width:180px;"><strong>Redacción</strong></td>
                    <td><textarea class="edit" rows="2">Se deben seguir las reglas ortográficas de la lengua española. La redacción debe ser en tercera persona; se debe justificar toda la documentación y correspondencia.</textarea></td>
                </tr>
                <tr>
                    <td><strong>Tipo de letra</strong></td>
                    <td><textarea class="edit" rows="2">Se recomienda el uso de la fuente Arial con un tamaño 12 u 11, para el cuerpo del documento; los títulos y subtítulos irán en negrilla conservando el mismo tamaño de letra.</textarea></td>
                </tr>
                <tr>
                    <td><strong>Numeración</strong></td>
                    <td><textarea class="edit" rows="2">La numeración de las páginas debe hacerse de forma consecutiva con números arábigos a excepción de la cubierta y la portada, que no se enumeran.</textarea></td>
                </tr>
                <tr>
                    <td><strong>Contenido</strong></td>
                    <td><textarea class="edit" rows="2">Permitir al lector encontrar una parte específica del documento de una forma rápida, utilizando siempre al inicio de cada documento o correspondencia el código de la dependencia.</textarea></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Codificación de procesos</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>Nombres del nivel</th>
                    <th style="width:180px;">Código</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input class="edit" type="text" value="SEGURIDAD Y SALUD EN EL TRABAJO"></td>
                    <td class="center"><input class="edit center" type="text" value="SST"></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Tipo de documentación</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>Nombre del documento</th>
                    <th style="width:180px;">Tipo documento</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Manuales</td><td class="center">MA</td></tr>
                <tr><td>Política</td><td class="center">PO</td></tr>
                <tr><td>Instructivos</td><td class="center">IN</td></tr>
                <tr><td>Programa</td><td class="center">PR</td></tr>
                <tr><td>Manual de funciones</td><td class="center">MF</td></tr>
                <tr><td>Planes</td><td class="center">PL</td></tr>
                <tr><td>Registros</td><td class="center">RE</td></tr>
                <tr><td>Anexos</td><td class="center">AN</td></tr>
                <tr><td>Actas</td><td class="center">AC</td></tr>
                <tr><td>Informe</td><td class="center">IN</td></tr>
            </tbody>
        </table>

        <div class="sec-h">Codificación de la empresa</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:180px;">Código</th>
                    <th>Iniciales de la empresa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center"><input class="edit center" type="text" value="XX"></td>
                    <td><input class="edit" type="text" value="INICIALES DE LA EMPRESA"></td>
                </tr>
            </tbody>
        </table>

        <div class="box text-just">
            <p>Las tablas anteriores se van a codificar de la siguiente manera:</p>
            <p><strong>Tipo de Documento + (XX) Identificación de la empresa + Nombre de Proceso + Consecutivo</strong></p>
            <p><strong>Ejemplo:</strong> MA-XX-SST-01</p>
        </div>

        <div class="sec-h">Ejemplo de codificación del proceso</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:80px;">N°</th>
                    <th>Nombre del proceso</th>
                    <th style="width:220px;">Código</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td><input class="edit" type="text" value="Manual de control de documentos"></td>
                    <td class="center"><input class="edit center" type="text" value="MA-XX-SST-01"></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Procedimiento para control documental</div>

        <div class="flow-note"><strong>Nota:</strong> aquí te lo dejé en tabla editable y limpia, sin cajas internas marcadas, para que se vea más profesional que en el Word original.</div>

        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th style="width:180px;">Entrada</th>
                    <th style="width:180px;">Actividad</th>
                    <th style="width:180px;">Salida</th>
                    <th>Descripción (Cómo)</th>
                    <th style="width:220px;">Responsable / Registro</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td>Inicio</td>
                    <td>Inicio</td>
                    <td>Inicio</td>
                    <td><textarea class="edit" rows="2"></textarea></td>
                    <td><input class="edit" type="text"></td>
                </tr>
                <tr>
                    <td class="center">2</td>
                    <td>Información para elaboración del documento</td>
                    <td>Elaborar documento</td>
                    <td>Elaborar documento</td>
                    <td><textarea class="edit" rows="4">El coordinador de SST se encarga de controlar la elaboración de documentos (manuales, procedimientos, instructivos, planes, registros) del Sistema de Gestión en Seguridad y Salud en el Trabajo. Obtiene la información por medio de observación directa y entrevistas con el personal que realiza las actividades que se documentarán.</textarea></td>
                    <td><textarea class="edit" rows="3">Responsable: Coordinador de SST
Participa: Persona(s) que utilizarán el documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">3</td>
                    <td></td>
                    <td>¿Hay cambios?</td>
                    <td>SI / NO</td>
                    <td><textarea class="edit" rows="3">Si existen cambios en el documento, el coordinador de SST los incorpora, hasta conseguir con quienes participan de la elaboración el consenso acerca de los cambios y obtener el documento previo a ser aprobado.</textarea></td>
                    <td><textarea class="edit" rows="3">Responsable: Coordinador de SST
Participa: Persona(s) que utilizarán el documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">4</td>
                    <td></td>
                    <td>Aprobar edición</td>
                    <td>Aprobar edición</td>
                    <td><textarea class="edit" rows="3">El coordinador de SST revisa inmediatamente y envía al gerente para que apruebe la edición preliminar del documento y la presentación del medio impreso y el archivo en medio magnético.</textarea></td>
                    <td><textarea class="edit" rows="3">Responsable: Coordinador de SST
Participa: Gerente</textarea></td>
                </tr>
                <tr>
                    <td class="center">5</td>
                    <td></td>
                    <td>Codificar y actualizar revisión</td>
                    <td>Codificar y actualizar revisión</td>
                    <td><textarea class="edit" rows="3">Si no existe una versión anterior al documento o es uno nuevo se identifica con la Revisión 0, y se codifica según el anexo 1. Si existe una revisión anterior, se sigue el número consecutivo correspondiente.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Coordinador de SST</textarea></td>
                </tr>
                <tr>
                    <td class="center">6</td>
                    <td></td>
                    <td>Editar</td>
                    <td>Editar</td>
                    <td><textarea class="edit" rows="5">El Coordinador de SST realiza la presentación definitiva del documento, con las firmas de quien elabora, revisa y aprueba en la primera hoja del documento. Al entrar en vigencia una nueva revisión, debe retirar el documento anterior y convertirlo en documento obsoleto. Las fechas de cada documento serán registradas en la primera hoja de cada documento.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Coordinador de SST</textarea></td>
                </tr>
                <tr>
                    <td class="center">7</td>
                    <td></td>
                    <td>Incluir en el listado maestro</td>
                    <td>Listado maestro de documentos</td>
                    <td><textarea class="edit" rows="5">El listado de documentos vigentes, su código y las áreas en que se utilizan se controlan a través del registro Listado maestro de documentos. Cuando algún documento se revisa y es editada una nueva revisión o se distribuye entre personas o áreas diferentes, el coordinador de SST emite un nuevo Listado maestro de documentos.</textarea></td>
                    <td><textarea class="edit" rows="3">Responsable: Coordinador de SST
Registro: AN-XX-SST-05 Listado maestro de documentos</textarea></td>
                </tr>
                <tr>
                    <td class="center">8</td>
                    <td>Documento definitivo (copias)</td>
                    <td>Notificar y distribuir</td>
                    <td>Notificar y distribuir</td>
                    <td><textarea class="edit" rows="5">El Coordinador de SST notifica y entrega a las personas copia del documento impreso que van a utilizar en su lugar de trabajo y se encarga, además, de capacitar a todo el personal que requiere tener documentos controlados en su lugar de trabajo. La notificación puede realizarse a través del correo interno.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Coordinador de SST</textarea></td>
                </tr>
                <tr>
                    <td class="center">9</td>
                    <td>Documentos externos</td>
                    <td>Identificar documentos externos</td>
                    <td>Identificar documentos externos</td>
                    <td><textarea class="edit" rows="4">Se identifican los documentos externos recibidos o que se encuentren en la empresa: manuales, normas técnicas, catálogos de productos, que requieran ser identificados y controlados como parte del sistema de gestión, y se incluyen en el Listado Maestro de Documentos Externos.</textarea></td>
                    <td><textarea class="edit" rows="3">Responsable: Coordinador de SST
Participa: Gerente, jefes de área</textarea></td>
                </tr>
                <tr>
                    <td class="center">10</td>
                    <td></td>
                    <td>Mantener</td>
                    <td>Mantener</td>
                    <td><textarea class="edit" rows="3">Los responsables y/o usuarios de los documentos deben procurar mantener la integridad física de los asignados y el cumplimiento de las directrices allí establecidas.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Persona(s) que utilizan el documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">11</td>
                    <td></td>
                    <td>¿Se necesita actualizar?</td>
                    <td>SI / NO</td>
                    <td><textarea class="edit" rows="4">Si existen cambios en las actividades, procesos, nuevas líneas de productos o servicios, cambios en las funciones de los cargos existentes o nuevos cargos, se hace necesario revisar los documentos actuales para evaluar la necesidad de modificar su contenido, así como los registros involucrados.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Persona(s) que utilizan el documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">12</td>
                    <td></td>
                    <td>Revisar</td>
                    <td>Revisar</td>
                    <td><textarea class="edit" rows="4">La revisión puede ser sugerida por quienes realizan el documento o quienes lo elaboran, aprueban o quienes lo usan. Se deben justificar los cambios propuestos; en caso de aprobar el cambio, al final del documento aparece un resumen de los cambios realizados como evidencia.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Persona(s) que utilizan el documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">13</td>
                    <td></td>
                    <td>Reelaborar</td>
                    <td>Reelaborar</td>
                    <td><textarea class="edit" rows="4">Los cambios son realizados mediante la misma ruta original, y se aprueban como la primera vez. Se toman en cuenta los enlaces con otros documentos relacionados por pertenecer a una misma área, proceso o actividad.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Coordinador de SST / Persona(s) que utilizan el documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">14</td>
                    <td></td>
                    <td>¿Es documento obsoleto?</td>
                    <td>SI / NO</td>
                    <td><textarea class="edit" rows="3">Al entrar en vigencia una nueva revisión, se debe retirar el documento anterior y convertirlo en documento obsoleto.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Coordinador de SST / Responsable del documento</textarea></td>
                </tr>
                <tr>
                    <td class="center">15</td>
                    <td></td>
                    <td>Destruir</td>
                    <td>Destruir</td>
                    <td><textarea class="edit" rows="3">Se eliminan los documentos obsoletos que estén en medio físico y electrónico, se destruyen para evitar su consulta y utilización indebida.</textarea></td>
                    <td><textarea class="edit" rows="2">Responsable: Coordinador de SST y/o jefe de procesos</textarea></td>
                </tr>
                <tr>
                    <td class="center">16</td>
                    <td></td>
                    <td>Fin</td>
                    <td>Fin</td>
                    <td><textarea class="edit" rows="2"></textarea></td>
                    <td><input class="edit" type="text"></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Definiciones y terminología</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th style="width:220px;">Término</th>
                    <th>Definición</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>Aprobación</strong></td><td>Autorización de los documentos del sistema por parte de la gerencia para su utilización, antes de proceder a su aplicación.</td></tr>
                <tr><td><strong>Código</strong></td><td>Asignación de letras y números a los documentos, mediante el cual se obtiene mayor control.</td></tr>
                <tr><td><strong>Documento</strong></td><td>Información y su medio de soporte.</td></tr>
                <tr><td><strong>Documento vigente</strong></td><td>Es aquel documento reproducido del original aprobado, que cuando sufre modificaciones es remplazado para evitar el uso de documentos obsoletos.</td></tr>
                <tr><td><strong>Procedimiento</strong></td><td>Forma especificada para llevar a cabo una actividad o proceso.</td></tr>
                <tr><td><strong>Registro</strong></td><td>Documento que presenta resultados obtenidos o proporciona evidencia de actividades desempeñadas.</td></tr>
            </tbody>
        </table>

        <div class="sec-h">Registros</div>
        <table class="formtbl">
            <thead>
                <tr>
                    <th>Título</th>
                    <th style="width:260px;">Departamento, área o persona que debe retener el documento</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input class="edit" type="text" value="Listado maestro de documentos"></td>
                    <td class="center"><input class="edit center" type="text" value="SST"></td>
                </tr>
            </tbody>
        </table>

        <div class="sec-h">Conservación de documentos</div>
        <div class="box">
            <ul class="list-box">
                <li>Información sobre la legislación en SST aplicable.</li>
                <li>Registros de las formaciones proporcionadas a todos los empleados, incluida la inducción y la reinducción.</li>
                <li>Registros de entrenamientos, simulacros y simulaciones ejecutados en desarrollo del plan de prevención, preparación y respuesta ante emergencias.</li>
                <li>Registros de las inspecciones realizadas.</li>
                <li>Resultados de mediciones y monitoreo a los ambientes de trabajo.</li>
                <li>Registros de no conformidades, incidentes, accidentes, enfermedades laborales y la investigación y análisis de estos eventos.</li>
                <li>Registros de los análisis de seguridad realizados a las tareas críticas no rutinarias o trabajos de alto riesgo.</li>
                <li>Registros de la identificación de peligros, evaluación de riesgos y medidas de prevención y control definidas.</li>
                <li>Registro de entrega de elementos de protección personal.</li>
                <li>Resultados de perfiles epidemiológicos de salud y conceptos de exámenes de ingreso, periódicos y de retiro.</li>
                <li>Resultados de exámenes complementarios como audiometrías, espirometrías, radiografías de tórax y otros que apliquen.</li>
                <li>Registros relacionados con la evaluación del desempeño de la SST.</li>
                <li>Registros de las revisiones por la alta dirección.</li>
                <li>Informes de auditorías internas o externas del SG-SST.</li>
            </ul>
        </div>

        <div class="sign-grid">
            <div class="sign">ELABORÓ</div>
            <div class="sign">REVISÓ</div>
            <div class="sign">APROBÓ</div>
        </div>
    </div>
</div>
</body>
</html>