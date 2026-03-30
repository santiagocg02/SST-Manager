<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}

function e($v){
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AN-XX-SST-09 | Procedimiento de Gestión del Cambio</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#cfdcf3;
      --blue-dark:#9fb6dc;
      --soft:#f5f7fb;
      --text:#1a1a1a;
      --muted:#666;
    }

    *{
      box-sizing:border-box;
    }

    body{
      margin:0;
      background:#eef2f7;
      font-family: Arial, Helvetica, sans-serif;
      color:var(--text);
    }

    .topbar{
      width:100%;
      padding:14px 18px;
      background:#dde6f5;
      border-bottom:1px solid #c9d4e7;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      position:sticky;
      top:0;
      z-index:50;
    }

    .topbar-title{
      font-size:15px;
      font-weight:700;
      color:#24406f;
      margin:0;
    }

    .topbar-actions{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
    }

    .doc-wrap{
      padding:24px;
    }

    .paper{
      width:100%;
      max-width:1100px;
      margin:0 auto;
      background:#fff;
      border:1px solid #d8dee8;
      box-shadow:0 10px 30px rgba(0,0,0,.07);
      padding:24px;
    }

    .doc-header{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      margin-bottom:20px;
    }

    .doc-header td{
      border:1px solid var(--line);
      padding:8px 10px;
      vertical-align:middle;
    }

    .logo-cell{
      width:22%;
      text-align:center;
      font-weight:700;
      color:#5b5b5b;
      background:#fafafa;
    }

    .main-title{
      width:66%;
      text-align:center;
      font-weight:700;
      font-size:15px;
      line-height:1.35;
    }

    .meta-cell{
      width:12%;
      text-align:center;
      font-weight:700;
      padding:0 !important;
    }

    .meta-box{
      display:flex;
      flex-direction:column;
      height:100%;
      min-height:84px;
    }

    .meta-box div{
      flex:1;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:8px 6px;
      border-bottom:1px solid var(--line);
    }

    .meta-box div:last-child{
      border-bottom:none;
    }

    .doc-title{
      text-align:center;
      font-size:24px;
      font-weight:800;
      letter-spacing:.3px;
      margin:14px 0 6px;
      text-transform:uppercase;
    }

    .doc-version{
      text-align:center;
      color:var(--muted);
      font-size:13px;
      margin-bottom:18px;
    }

    .company-box{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      margin-bottom:24px;
    }

    .company-box td{
      border:1px solid var(--line);
      padding:10px 12px;
      height:44px;
    }

    .label{
      width:18%;
      background:var(--blue);
      font-weight:700;
    }

    .field{
      background:#fff;
    }

    .input-line{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:14px;
    }

    .section{
      margin-bottom:20px;
    }

    .section-title{
      background:var(--blue);
      border:1px solid var(--line);
      padding:10px 12px;
      font-weight:800;
      text-transform:uppercase;
      font-size:14px;
      letter-spacing:.2px;
    }

    .section-body{
      border:1px solid var(--line);
      border-top:none;
      padding:14px 16px;
      font-size:14px;
      line-height:1.65;
      text-align:justify;
    }

    .section-body p{
      margin:0 0 12px;
    }

    .section-body p:last-child{
      margin-bottom:0;
    }

    .sub-title{
      font-weight:800;
      margin:16px 0 8px;
      font-size:14px;
      text-transform:uppercase;
      color:#223a66;
    }

    .def-list,
    .resp-list,
    .control-list{
      margin:0;
      padding-left:18px;
    }

    .def-list li,
    .resp-list li,
    .control-list li{
      margin-bottom:10px;
    }

    .note-box{
      margin-top:12px;
      padding:10px 12px;
      border:1px dashed #6d84ab;
      background:#f6f9ff;
      font-size:13.5px;
    }

    .footer-note{
      margin-top:28px;
      text-align:center;
      color:#666;
      font-size:12px;
    }

    @media print{
      body{
        background:#fff;
      }

      .topbar{
        display:none !important;
      }

      .doc-wrap{
        padding:0;
      }

      .paper{
        max-width:100%;
        box-shadow:none;
        border:none;
        margin:0;
        padding:0;
      }

      @page{
        size: letter;
        margin: 12mm;
      }
    }

    @media (max-width: 768px){
      .doc-wrap{
        padding:12px;
      }

      .paper{
        padding:14px;
      }

      .doc-title{
        font-size:18px;
      }

      .main-title{
        font-size:13px;
      }

      .section-body{
        font-size:13px;
      }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <div class="topbar">
    <h1 class="topbar-title">Formato 2.11.1 · Procedimiento de Gestión del Cambio</h1>

    <div class="topbar-actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
    </div>
  </div>

  <div class="doc-wrap">
    <div class="paper">

      <table class="doc-header">
        <tr>
          <td class="logo-cell">
            LOGO
          </td>
          <td class="main-title">
            SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
          </td>
          <td class="meta-cell">
            <div class="meta-box">
              <div>0</div>
              <div>AN-XX-SST-09</div>
              <div>XX/XX/20XX</div>
            </div>
          </td>
        </tr>
      </table>

      <div class="doc-title">PROCEDIMIENTO DE GESTIÓN DEL CAMBIO</div>
      <div class="doc-version">Versión 0</div>

      <table class="company-box">
        <tr>
          <td class="label">NOMBRE DE LA EMPRESA</td>
          <td class="field">
            <input type="text" class="input-line" value="">
          </td>
          <td class="label">FECHA</td>
          <td class="field">
            <input type="date" class="input-line" value="">
          </td>
        </tr>
      </table>

      <section class="section">
        <div class="section-title">Objetivo</div>
        <div class="section-body">
          <p>
            Manejar y controlar de forma oportuna cualquier cambio que pueda afectar los procesos de la compañía tanto en sus peligros y riesgos.
          </p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Alcance</div>
        <div class="section-body">
          <p>
            Este procedimiento aplica para todos los cambios de operación, sistemas de gestión, actividades, materiales, equipos, procedimientos, servicios y productos, que afecten la Seguridad y Salud en el Trabajo, de tal manera que sean identificados y valorados, determinando las acciones y controles a implementar antes de que se ejecuten los cambios.
          </p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Responsabilidad</div>
        <div class="section-body">
          <ul class="resp-list">
            <li><strong>Gerente:</strong> Aprueba los cambios y la documentación pertinente.</li>
            <li><strong>Encargado del SG-SST:</strong> Redacta los documentos involucrados, los formatos y los revisa; así mismo, consolida la información de los cambios requeridos.</li>
            <li><strong>Jefes:</strong> Solicitan los cambios, los establecen, documentan e implementan una vez aprobados.</li>
          </ul>

          <div class="note-box">
            <strong>Nota:</strong><br>
            Para cambios relacionados con infraestructura, el responsable de reportarlos será el encargado de infraestructura física.<br>
            Para cambios relacionados con tecnología, el responsable de reportarlos será el encargado de tecnología.<br>
            Para cambios relacionados con contratación, el responsable de reportarlos será el encargado de recursos humanos.
          </div>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Definiciones</div>
        <div class="section-body">
          <ul class="def-list">
            <li><strong>Lugar de Trabajo:</strong> Cualquier espacio físico en el que se realizan actividades relacionadas con el trabajo, bajo el control de la organización.</li>
            <li><strong>Personal:</strong> Conjunto de individuos que trabajan en el mismo lugar de trabajo, organismo o empresa.</li>
            <li><strong>Sistema de Gestión SG-SST:</strong> Parte del sistema de gestión de una organización, empleada para desarrollar e implementar su política de seguridad y salud en el trabajo y gestionar sus riesgos.</li>
            <li><strong>Modificaciones:</strong> Cambiar o variar en sus caracteres no esenciales una actividad o un proceso. Introducción de cambios que se consideren necesarios tanto en el fondo como en la forma de un documento.</li>
            <li><strong>Actividades:</strong> Conjunto de tareas o acciones que se hacen con un fin determinado o son propias de una persona, una profesión o una entidad.</li>
            <li><strong>Materiales y Equipos:</strong> Conjunto de máquinas, herramientas u objetos de cualquier clase, necesarios para el desempeño de un servicio o el ejercicio de una profesión.</li>
            <li><strong>Contratistas:</strong> Persona o empresa que presta servicios a la compañía.</li>
            <li><strong>Proveedor:</strong> Productor, distribuidor, minorista o vendedor de un producto, o prestador de un servicio o información. Puede ser interno o externo a la organización.</li>
            <li><strong>Proceso:</strong> Conjunto de actividades mutuamente relacionadas o que interactúan, las cuales transforman elementos de entrada en resultados.</li>
            <li><strong>Proyecto:</strong> Proceso único consistente en un conjunto de actividades coordinadas y controladas con fechas de inicio y finalización, ejecutadas para lograr un objetivo conforme con requisitos específicos, incluyendo limitaciones de tiempo, costo y recursos.</li>
            <li><strong>Procedimiento:</strong> Forma especificada para llevar a cabo una actividad o un proceso.</li>
            <li><strong>Documento:</strong> Información y sus medios de soporte.</li>
          </ul>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Procedimiento</div>
        <div class="section-body">

          <div class="sub-title">5.1 Identificación</div>
          <p>
            Se identificarán con base en criterios como generación de nuevos procesos, identificación de nuevos requisitos legales, nuevos contratistas, proveedores, productos, maquinaria, tecnología, insumos, estructura organizacional, nuevos proyectos y licitaciones. Los responsables de esta identificación serán, entre otros, gerencias y directores de área.
          </p>

          <p><strong>Descripción de los tipos de cambios:</strong></p>
          <ul class="def-list">
            <li><strong>Infraestructura:</strong> Cambios en instalaciones, adecuaciones de oficinas, modificaciones estructurales y zonas de almacenamiento de materiales.</li>
            <li><strong>Requisitos legales o contractuales:</strong> Se generan a partir de la identificación, seguimiento y verificación del cumplimiento de requisitos legales y/o contractuales suscritos por la organización.</li>
            <li><strong>Cambios de personal, contratistas y proveedores:</strong> Cuando se presentan cambios significativos en cargos o nuevos terceros cuyo desempeño afecte notoriamente el sistema.</li>
            <li><strong>Creación de nuevos procesos, cambios en actividades, materiales e insumos:</strong> Cuando por cambios en la actividad económica o ampliación del alcance de los servicios se requiera implementar nuevos procesos o modificar los existentes.</li>
            <li><strong>Sistema de Gestión:</strong> Cuando como consecuencia de la revisión del sistema, seguimiento al desempeño o resultados obtenidos, se requieran cambios a nivel general.</li>
          </ul>

          <div class="sub-title">Manejo</div>
          <p>
            Cuando se genere un cambio en cualquiera de las estructuras, equipos o procesos, los responsables de identificarlo deben diligenciar el formato de planificación de cambios, el cual deberá ser enviado vía correo o entregado físicamente al encargado del SG-SST para su estudio y aprobación conjunta con la Gerencia.
          </p>

          <p>
            Una vez sea aceptado y aprobado, se ejecutará el proceso de gestión del cambio. Ninguna gestión de cambio se puede realizar si no ha sido aprobada y revisada por la Gerencia y el encargado del SG-SST.
          </p>

          <p>
            Los cambios se realizarán mediante la identificación, evaluación y control de los peligros y la minimización de los impactos que se generen. Se aplicará la jerarquía de controles: eliminación, sustitución, controles de ingeniería, controles administrativos y equipos de protección personal. Estos controles deberán ser cargados en la matriz de planificación del cambio por el encargado del SG-SST, quien será responsable de consolidar la información y realizar el seguimiento respectivo.
          </p>

          <div class="sub-title">Aplicación de controles</div>
          <ul class="control-list">
            <li><strong>Eliminación:</strong> Modificar un diseño, operación o equipo para eliminar el peligro.</li>
            <li><strong>Sustitución:</strong> Reemplazar por un material menos peligroso o reducir la energía del sistema.</li>
            <li><strong>Controles de ingeniería:</strong> Instalar sistemas de ventilación, protección para máquinas, enclavamientos, cerramientos acústicos, entre otros.</li>
            <li><strong>Señalización, advertencias y/o controles administrativos:</strong> Instalación de alarmas, procedimientos de seguridad, inspecciones de equipos y controles de acceso.</li>
            <li><strong>Equipos de protección personal:</strong> Gafas de seguridad, protección auditiva, máscaras faciales, arneses, respiradores, guantes, entre otros.</li>
          </ul>

          <div class="sub-title">Proceso general</div>
          <p>
            De acuerdo con el tipo de cambio correspondiente a las operaciones de la compañía, el responsable de la identificación debe seguir los siguientes parámetros:
          </p>

          <ul class="def-list">
            <li>
              <strong>Cambios en instalaciones:</strong> Construcción de locaciones, oficinas, modificaciones estructurales o zonas de almacenamiento de materiales peligrosos. Utilice el formato de planificación de cambios y la matriz de identificación de peligros correspondiente.
            </li>
            <li>
              <strong>Cambios en proveedores, contratistas o equipos especiales:</strong> Utilice el formato de planificación de cambios y envíe la propuesta con los cambios sugeridos o el borrador de los procedimientos nuevos a implementar para revisión y aprobación por la Gerencia y el encargado del SG-SST.
            </li>
            <li>
              <strong>Cambios en la utilización de sustancias y materiales peligrosos:</strong> Como ACPM, gasolina, aceites de motor y otros. Utilice el formato de planificación de cambios.
            </li>
            <li>
              <strong>Cambios en personal:</strong> A todo el personal nuevo debe realizarse inducción al SG-SST, incluyendo las operaciones propias a ejecutar y la evaluación de su entendimiento.
            </li>
          </ul>

          <div class="sub-title">Revisión y aprobación</div>
          <p>
            De acuerdo con el cambio que se establezca, deberá realizarse la evaluación sobre si los controles existentes son suficientes o si se requieren controles adicionales. Para ello, utilice el documento de identificación de peligros, valoración de riesgos y determinación de controles.
          </p>

          <p>
            Finalmente, deberán divulgarse los nuevos documentos y/o procedimientos a las partes interesadas correspondientes.
          </p>
        </div>
      </section>

      <div class="footer-note">
        Documento interno del Sistema de Gestión de Seguridad y Salud en el Trabajo
      </div>

    </div>
  </div>

</body>
</html>