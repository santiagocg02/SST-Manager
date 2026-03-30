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
  <title>MN-SST-02 | Manual SG-SST</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --line:#111;
      --blue:#d8e4f6;
      --blue2:#b9cdea;
      --soft:#f4f7fb;
      --text:#1c1c1c;
      --muted:#666;
      --title:#213b67;
    }

    *{ box-sizing:border-box; }

    body{
      margin:0;
      background:#edf2f8;
      color:var(--text);
      font-family:Arial, Helvetica, sans-serif;
    }

    .toolbar{
      position:sticky;
      top:0;
      z-index:100;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:14px 18px;
      background:#dfe8f5;
      border-bottom:1px solid #cdd8e7;
    }

    .toolbar h1{
      margin:0;
      font-size:15px;
      font-weight:800;
      color:var(--title);
    }

    .toolbar .actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
    }

    .page-wrap{
      padding:24px;
    }

    .paper{
      max-width:1100px;
      margin:0 auto;
      background:#fff;
      border:1px solid #d7dee9;
      box-shadow:0 12px 35px rgba(0,0,0,.08);
      padding:24px;
    }

    .doc-header{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      margin-bottom:22px;
    }

    .doc-header td{
      border:1px solid var(--line);
      padding:8px 10px;
      vertical-align:middle;
    }

    .logo-box{
      width:22%;
      text-align:center;
      font-weight:700;
      color:#666;
      background:#fafafa;
      min-height:88px;
    }

    .header-main{
      width:66%;
      text-align:center;
      font-weight:800;
      font-size:15px;
      line-height:1.35;
    }

    .header-side{
      width:12%;
      padding:0 !important;
    }

    .side-grid{
      display:flex;
      flex-direction:column;
      min-height:88px;
    }

    .side-grid div{
      flex:1;
      display:flex;
      justify-content:center;
      align-items:center;
      border-bottom:1px solid var(--line);
      font-weight:700;
      text-align:center;
      padding:6px;
    }

    .side-grid div:last-child{ border-bottom:none; }

    .doc-title{
      text-align:center;
      font-size:30px;
      font-weight:900;
      letter-spacing:.4px;
      margin:16px 0 6px;
      text-transform:uppercase;
    }

    .doc-subtitle{
      text-align:center;
      color:var(--muted);
      font-size:14px;
      margin-bottom:18px;
    }

    .intro-block{
      text-align:center;
      margin:18px 0 24px;
    }

    .intro-block .empresa{
      font-size:20px;
      font-weight:800;
      color:var(--title);
      margin-bottom:6px;
    }

    .intro-block .fecha{
      color:#555;
      font-size:15px;
    }

    .table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      margin-bottom:20px;
    }

    .table td, .table th{
      border:1px solid var(--line);
      padding:9px 10px;
      font-size:14px;
      vertical-align:top;
    }

    .table th{
      background:var(--blue);
      text-transform:uppercase;
      font-weight:800;
      text-align:center;
    }

    .label{
      background:var(--blue);
      font-weight:700;
      width:22%;
    }

    .input-line{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:14px;
    }

    .signature-box{
      height:58px;
    }

    .section{
      margin-bottom:20px;
    }

    .section-title{
      background:var(--blue);
      border:1px solid var(--line);
      padding:10px 12px;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.2px;
      color:#142b4d;
    }

    .section-body{
      border:1px solid var(--line);
      border-top:none;
      padding:15px 16px;
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

    .subsection{
      margin-top:16px;
    }

    .subsection-title{
      font-size:15px;
      font-weight:800;
      color:var(--title);
      text-transform:uppercase;
      margin:0 0 10px;
    }

    .mini-title{
      font-size:14px;
      font-weight:800;
      color:#2d4a7f;
      margin:14px 0 8px;
      text-transform:uppercase;
    }

    ul.clean{
      margin:0;
      padding-left:18px;
    }

    ul.clean li{
      margin-bottom:8px;
    }

    .toc{
      border:1px solid var(--line);
      margin-bottom:20px;
    }

    .toc-title{
      background:var(--blue);
      padding:10px 12px;
      font-weight:900;
      text-transform:uppercase;
      border-bottom:1px solid var(--line);
    }

    .toc-body{
      padding:12px 16px;
      columns:2;
      column-gap:34px;
      font-size:14px;
      line-height:1.7;
    }

    .toc-body div{
      break-inside:avoid;
    }

    .roles-table td:first-child{
      width:26%;
      font-weight:700;
      background:#f8fbff;
    }

    .annex-list{
      columns:2;
      column-gap:30px;
      padding-left:18px;
      margin:0;
    }

    .annex-list li{
      margin-bottom:8px;
      break-inside:avoid;
    }

    .footer-note{
      margin-top:26px;
      font-size:12px;
      color:#666;
      text-align:center;
    }

    @media print{
      body{ background:#fff; }
      .toolbar{ display:none !important; }
      .page-wrap{ padding:0; }
      .paper{
        max-width:100%;
        margin:0;
        border:none;
        box-shadow:none;
        padding:0;
      }
      @page{
        size:letter;
        margin:12mm;
      }
    }

    @media (max-width: 768px){
      .page-wrap{ padding:12px; }
      .paper{ padding:14px; }
      .doc-title{ font-size:21px; }
      .toc-body{ columns:1; }
      .annex-list{ columns:1; }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <div class="toolbar">
    <h1>Manual SG-SST</h1>
    <div class="actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
    </div>
  </div>

  <div class="page-wrap">
    <div class="paper">

      <table class="doc-header">
        <tr>
          <td class="logo-box">LOGO</td>
          <td class="header-main">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</td>
          <td class="header-side">
            <div class="side-grid">
              <div>Versión: 0</div>
              <div>MN-SST-02</div>
              <div>Fecha: 02-01-20XX</div>
            </div>
          </td>
        </tr>
      </table>

      <div class="doc-title">MANUAL SG-SST</div>
      <div class="doc-subtitle">Versión 0</div>

      <div class="intro-block">
        <div class="empresa">
          <input class="input-line text-center fw-bold" type="text" value="La Empresa">
        </div>
        <div class="fecha">
          <input class="input-line text-center" type="text" value="Enero de 20XX">
        </div>
      </div>

      <table class="table">
        <tr>
          <th colspan="2">Control de documentos</th>
        </tr>
        <tr>
          <td class="label">Elaborado por</td>
          <td><input type="text" class="input-line"></td>
        </tr>
        <tr>
          <td class="label">Aprobado por</td>
          <td><input type="text" class="input-line"></td>
        </tr>
        <tr>
          <td class="label">Firma</td>
          <td class="signature-box"></td>
        </tr>
        <tr>
          <td class="label">Firma</td>
          <td class="signature-box"></td>
        </tr>
      </table>

      <table class="table">
        <tr>
          <th colspan="3">Control de cambios</th>
        </tr>
        <tr>
          <th>Revisión</th>
          <th>Fecha</th>
          <th>Descripción del cambio</th>
        </tr>
        <tr>
          <td class="text-center">0</td>
          <td>Enero 2024</td>
          <td>Creación del Sistema de Gestión de la Seguridad y Salud en el Trabajo</td>
        </tr>
      </table>

      <div class="toc">
        <div class="toc-title">Contenido</div>
        <div class="toc-body">
          <div>Introducción</div>
          <div>Definiciones y abreviaturas</div>
          <div>Esquema SG-SST</div>
          <div>1. Política</div>
          <div>2. Organización</div>
          <div>2.1 Información general de la empresa</div>
          <div>2.1.1 Misión</div>
          <div>2.1.2 Visión</div>
          <div>2.1.3 Información sociodemográfica</div>
          <div>2.1.4 Horarios de trabajo</div>
          <div>2.2 Descripción de servicios/productos</div>
          <div>2.2.1 Maquinaria, herramientas y equipos</div>
          <div>2.3 Estructura organizacional</div>
          <div>2.4 Roles y responsabilidades</div>
          <div>2.5 Aspectos jurídicos y laborales</div>
          <div>2.6 Definición de recursos</div>
          <div>2.7 Comunicación</div>
          <div>2.8 Competencia laboral en SST</div>
          <div>2.9 Documentación y control de documentos</div>
          <div>3. Planificación</div>
          <div>3.1 Objetivos y metas</div>
          <div>3.2 Requisitos legales</div>
          <div>3.3 Identificación de peligros y valoración de riesgos</div>
          <div>3.4 Programas de gestión</div>
          <div>4. Aplicación</div>
          <div>4.1 Gestión del cambio</div>
          <div>4.2 Emergencias</div>
          <div>4.3 Control de proveedores y subcontratistas</div>
          <div>5. Verificación</div>
          <div>6. Auditoría</div>
          <div>7. Mejoramiento</div>
          <div>8. Control de cambios</div>
          <div>Lista de anexos</div>
        </div>
      </div>

      <section class="section">
        <div class="section-title">Introducción</div>
        <div class="section-body">
          <p>LA EMPRESA, en cumplimiento de la Ley 1562 de 2012, Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normatividad vigente, ha estructurado el Sistema de Gestión de la Seguridad y Salud en el Trabajo, con el propósito de organizar la acción conjunta entre empleadores y trabajadores para la aplicación de medidas de Seguridad y Salud en el Trabajo a través del mejoramiento continuo de las condiciones y el medio ambiente laboral y el control eficaz de los peligros y riesgos en el lugar de trabajo.</p>
          <p>Para su efecto, LA EMPRESA aborda la prevención de lesiones y enfermedades laborales, la promoción y protección de la salud de los trabajadores, a través de un método lógico por etapas basado en el ciclo PHVA: Planificar, Hacer, Verificar y Actuar.</p>
          <p>El desarrollo articulado de política, organización, planificación, aplicación, evaluación, auditoría y acciones de mejora permite cumplir los propósitos del SG-SST y adaptarlo al tamaño y características de la empresa. :contentReference[oaicite:1]{index=1}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Definiciones y abreviaturas</div>
        <div class="section-body">
          <ul class="clean">
            <li><strong>Seguridad y salud en el trabajo:</strong> disciplina orientada a prevenir lesiones y enfermedades laborales y a promover la salud de los trabajadores.</li>
            <li><strong>Accidente de trabajo:</strong> suceso repentino que sobreviene por causa o con ocasión del trabajo y produce lesión, invalidez o muerte.</li>
            <li><strong>Enfermedad laboral:</strong> resultado de la exposición a factores de riesgo inherentes a la actividad laboral o al medio donde el trabajador desarrolla sus funciones.</li>
            <li><strong>Identificación del peligro:</strong> proceso para reconocer si existe un peligro y definir sus características.</li>
            <li><strong>Riesgo:</strong> combinación de la probabilidad de ocurrencia de un evento peligroso y la severidad de la lesión o enfermedad que puede causar.</li>
            <li><strong>Valoración de los riesgos:</strong> proceso de evaluar el riesgo teniendo en cuenta la suficiencia de los controles existentes.</li>
            <li><strong>SG-SST:</strong> Sistema de Gestión de la Seguridad y Salud en el Trabajo.</li>
            <li><strong>SST:</strong> Seguridad y Salud en el Trabajo. :contentReference[oaicite:2]{index=2}</li>
          </ul>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Esquema SG-SST</div>
        <div class="section-body text-center">
          <p class="mb-3"><strong>Política → Organización → Planificación → Aplicación → Verificación → Auditoría → Mejoramiento</strong></p>
          <p>Este es el esquema general definido en el manual para el desarrollo del sistema. :contentReference[oaicite:3]{index=3}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">1. Política</div>
        <div class="section-body">
          <p>La alta dirección, con la participación del COPASST, ha definido una política de SST que es comunicada y divulgada a través de inducción, reinducción, formación, capacitación y material publicitario. Además, se publicará en las instalaciones administrativas y será revisada periódicamente en las reuniones de revisión por la dirección. :contentReference[oaicite:4]{index=4}</p>
          <p><strong>Ver anexo:</strong> PO-SST-01 Política de Seguridad y Salud en el Trabajo.</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">2. Organización</div>
        <div class="section-body">

          <div class="subsection">
            <div class="subsection-title">2.1 Información general de la empresa</div>
            <table class="table">
              <tr>
                <td class="label">Razón social</td>
                <td><input class="input-line" type="text"></td>
                <td class="label">NIT</td>
                <td><input class="input-line" type="text"></td>
              </tr>
              <tr>
                <td class="label">Dirección</td>
                <td><input class="input-line" type="text"></td>
                <td class="label">Teléfono</td>
                <td><input class="input-line" type="text"></td>
              </tr>
              <tr>
                <td class="label">Representante legal</td>
                <td><input class="input-line" type="text"></td>
                <td class="label">ARL</td>
                <td><input class="input-line" type="text"></td>
              </tr>
              <tr>
                <td class="label">Actividad económica</td>
                <td><input class="input-line" type="text"></td>
                <td class="label">Clase y grado de riesgo</td>
                <td><input class="input-line" type="text"></td>
              </tr>
              <tr>
                <td class="label">Centro(s) de trabajo</td>
                <td colspan="3"><input class="input-line" type="text" value="Villavicencio"></td>
              </tr>
            </table>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.1.1 Misión</div>
            <p><input class="input-line" type="text" placeholder="Escriba la misión de la empresa"></p>

            <div class="subsection-title">2.1.2 Visión</div>
            <p><input class="input-line" type="text" placeholder="Escriba la visión de la empresa"></p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.1.3 Información sociodemográfica de la población trabajadora</div>
            <table class="table">
              <tr>
                <th>Colaboradores</th>
                <th>Hombres</th>
                <th>Mujeres</th>
                <th>Total</th>
              </tr>
              <tr>
                <td>Administración</td>
                <td><input class="input-line text-center" type="number"></td>
                <td><input class="input-line text-center" type="number"></td>
                <td><input class="input-line text-center" type="number"></td>
              </tr>
              <tr>
                <td>Operaciones</td>
                <td><input class="input-line text-center" type="number"></td>
                <td><input class="input-line text-center" type="number"></td>
                <td><input class="input-line text-center" type="number"></td>
              </tr>
              <tr>
                <td><strong>Total</strong></td>
                <td><input class="input-line text-center" type="number"></td>
                <td><input class="input-line text-center" type="number"></td>
                <td><input class="input-line text-center" type="number"></td>
              </tr>
            </table>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.1.4 Horarios de trabajo</div>
            <p>Lunes a Viernes: 8:00 am a 12:00 m y 2:00 pm a 6:00 pm.</p>
            <p>Sábados: 8:00 am a 12:00 pm.</p>
            <p><strong>Nota:</strong> Los horarios pueden variar esporádicamente de acuerdo con la carga laboral existente. :contentReference[oaicite:5]{index=5}</p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.2 Descripción de servicios/productos</div>
            <p><input class="input-line" type="text" placeholder="Describa los servicios o productos"></p>

            <div class="subsection-title">2.2.1 Maquinaria, herramientas y equipos</div>
            <p><input class="input-line" type="text" placeholder="Describa maquinaria, herramientas y equipos"></p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.3 Estructura organizacional</div>
            <p><input class="input-line" type="text" placeholder="Describa o enlace el organigrama"></p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.4 Roles y responsabilidades</div>
            <table class="table roles-table">
              <tr>
                <th>Rol</th>
                <th>Responsabilidad</th>
              </tr>
              <tr>
                <td>Gerente y/o representante legal</td>
                <td>Suministrar recursos, asignar responsabilidades, garantizar participación, supervisión, capacitación y evaluación anual del SG-SST.</td>
              </tr>
              <tr>
                <td>Jefes de área</td>
                <td>Participar en la actualización de peligros y riesgos, planes de acción, investigación de incidentes e inspecciones.</td>
              </tr>
              <tr>
                <td>Responsable del SG-SST</td>
                <td>Planificar, organizar, dirigir, desarrollar y aplicar el sistema, hacer seguimiento e informar a la alta dirección.</td>
              </tr>
              <tr>
                <td>Trabajadores</td>
                <td>Cuidar su salud, cumplir normas de seguridad, participar en la prevención y reportar condiciones o incidentes.</td>
              </tr>
              <tr>
                <td>COPASST / Vigía</td>
                <td>Proponer actividades, analizar causas, visitar instalaciones, recibir sugerencias y apoyar la coordinación entre directivas y trabajadores.</td>
              </tr>
              <tr>
                <td>Comité de convivencia laboral</td>
                <td>Recibir quejas, escuchar a las partes, promover diálogo, hacer seguimiento y emitir recomendaciones. :contentReference[oaicite:6]{index=6}</td>
              </tr>
            </table>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.5 Aspectos jurídicos y laborales</div>
            <ul class="clean">
              <li>Reglamento Interno de Trabajo.</li>
              <li>Reglamento de Higiene y Seguridad Industrial.</li>
              <li>Comité Paritario de Seguridad y Salud en el Trabajo.</li>
              <li>Comité de convivencia laboral. :contentReference[oaicite:7]{index=7}</li>
            </ul>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.6 Definición de recursos</div>
            <p>La empresa define y asigna recursos físicos, financieros y humanos para el diseño, desarrollo, supervisión y evaluación de las medidas de prevención y control, así como para el cumplimiento de las funciones del COPASST y responsables de SST. :contentReference[oaicite:8]{index=8}</p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.7 Comunicación</div>
            <p>La empresa establece mecanismos de comunicación, participación y consulta con empleados y partes interesadas externas sobre aspectos relevantes del SG-SST, incluyendo correos, teléfonos, comunicaciones físicas, inducción, capacitación y representación a través del COPASST. :contentReference[oaicite:9]{index=9}</p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.8 Competencia laboral en SST: inducción, capacitación y entrenamiento</div>

            <div class="mini-title">2.8.1 Inducción en SST</div>
            <ul class="clean">
              <li>Aspectos generales y legales en SST</li>
              <li>Política de SST</li>
              <li>Política de no alcohol, drogas ni tabaquismo</li>
              <li>Reglamento de higiene y seguridad industrial</li>
              <li>Plan de emergencia</li>
              <li>Peligros y riesgos asociados a la labor</li>
              <li>Procedimientos seguros y reporte de accidentes de trabajo</li>
            </ul>

            <div class="mini-title">2.8.2 Programa de capacitación y entrenamiento</div>
            <p>La empresa implementa un programa de capacitación y entrenamiento en SST revisado semestralmente con participación del COPASST para analizar cumplimiento, cobertura y eficacia. :contentReference[oaicite:10]{index=10}</p>
          </div>

          <div class="subsection">
            <div class="subsection-title">2.9 Documentación y control de documentos</div>
            <p>La empresa cuenta con un manual y procedimiento para el control, administración y conservación de documentos y registros. :contentReference[oaicite:11]{index=11}</p>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="section-title">3. Planificación</div>
        <div class="section-body">
          <div class="mini-title">3.1 Objetivos y metas</div>
          <p>Se establece un plan de trabajo con objetivos, metas e indicadores para hacer seguimiento al cumplimiento del sistema y definir acciones de mejora cuando sea necesario.</p>

          <div class="mini-title">3.2 Requisitos legales</div>
          <p>La empresa define un procedimiento para identificar requisitos legales y de otra índole aplicables, manteniendo actualizada la matriz de requisitos legales.</p>

          <div class="mini-title">3.3 Identificación de peligros y valoración de riesgos</div>
          <p>La empresa cuenta con un procedimiento documentado para la identificación continua de peligros, evaluación y control de riesgos con base en la jerarquía de controles: eliminación, sustitución, controles de ingeniería, controles administrativos y EPP. :contentReference[oaicite:12]{index=12}</p>

          <div class="mini-title">3.4 Programas de gestión</div>
          <p>Incluye medicina preventiva, higiene industrial, seguridad industrial, programa de EPP, orden y aseo, inspecciones y plan de trabajo anual. :contentReference[oaicite:13]{index=13}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">4. Aplicación</div>
        <div class="section-body">
          <div class="mini-title">4.1 Gestión del cambio</div>
          <p>La empresa evaluará el impacto sobre la seguridad y salud que puedan generar cambios internos o externos, adoptando medidas de prevención y control antes de su implementación cuando proceda.</p>

          <div class="mini-title">4.2 Prevención, preparación y respuesta ante emergencias</div>
          <p>Se contemplan análisis de amenazas y vulnerabilidad, PON, recursos, brigada integral, entrenamiento, inspección de equipos y procedimientos de simulacros.</p>

          <div class="mini-title">4.3 Control de proveedores y subcontratistas</div>
          <p>Se verifica afiliación al sistema de seguridad social, se comunica información de riesgos y emergencias, y se hace seguimiento al cumplimiento normativo de proveedores y contratistas. :contentReference[oaicite:14]{index=14}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">5. Verificación</div>
        <div class="section-body">
          <div class="mini-title">5.1 Supervisión y medición de los resultados</div>
          <p>La empresa supervisa, mide y recopila información sobre el desempeño del SG-SST mediante indicadores de cumplimiento, cobertura, eficacia, accidentalidad, enfermedad laboral y ausentismo.</p>

          <div class="mini-title">5.1.1 Supervisión proactiva</div>
          <p>Incluye inspecciones, intercambio de información, evaluación de controles, vigilancia de ambientes de trabajo y seguimiento a la salud de los trabajadores.</p>

          <div class="mini-title">5.1.2 Supervisión reactiva</div>
          <p>Incluye identificación, notificación e investigación de incidentes, accidentes, enfermedades laborales, ausentismo y otras pérdidas asociadas a SST.</p>

          <div class="mini-title">5.2 Investigación de incidentes, accidentes y enfermedades relacionadas con el trabajo</div>
          <p>Busca identificar deficiencias, comunicar conclusiones, informar resultados y alimentar los procesos de mejora continua. :contentReference[oaicite:15]{index=15}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">6. Auditoría</div>
        <div class="section-body">
          <div class="mini-title">6.1 Auditorías internas</div>
          <p>La empresa cuenta con procedimiento y formato para auditorías internas anuales, incluyendo fortalezas, no conformidades, observaciones y oportunidades de mejora.</p>

          <div class="mini-title">6.2 Revisión por la dirección</div>
          <p>La alta dirección evalúa el SG-SST como mínimo una vez al año, revisando cumplimiento del plan, eficacia de estrategias, necesidad de cambios, suficiencia de recursos y nuevas prioridades estratégicas. :contentReference[oaicite:16]{index=16}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">7. Mejoramiento</div>
        <div class="section-body">
          <div class="mini-title">7.1 Mejora continua</div>
          <p>La empresa garantiza recursos y disposiciones para perfeccionar el SG-SST, tomando como fuentes los cambios normativos, resultados de auditorías, investigaciones, identificación de peligros y recomendaciones del COPASST.</p>

          <div class="mini-title">7.2 Acciones preventivas y correctivas</div>
          <p>Se documentan acciones para identificar causas fundamentales de no conformidades, planificar medidas y verificar su eficacia.</p>

          <div class="mini-title">7.3 Disposiciones finales</div>
          <p>La gerencia está comprometida con el cumplimiento del SG-SST y con las exigencias de la Ley 1562 de 2012, Decreto 1072 de 2015 y Resolución 0312 de 2019. :contentReference[oaicite:17]{index=17}</p>
        </div>
      </section>

      <section class="section">
        <div class="section-title">Lista de anexos</div>
        <div class="section-body">
          <ul class="annex-list">
            <li>Manual de Control de Documentos</li>
            <li>Manual del SG-SST</li>
            <li>Política de seguridad y salud en el trabajo</li>
            <li>Programa de inducción, capacitación y entrenamiento</li>
            <li>Programa de EPP</li>
            <li>Programa de orden, aseo y limpieza</li>
            <li>Programa de inspecciones</li>
            <li>Perfiles de cargo</li>
            <li>Plan de capacitaciones</li>
            <li>Plan de trabajo</li>
            <li>Plan de emergencias</li>
            <li>Reglamento interno de trabajo</li>
            <li>Reglamento de higiene y seguridad industrial</li>
            <li>Presupuesto</li>
            <li>Listado maestro de documentos</li>
            <li>Matriz de requisitos legales</li>
            <li>Matriz de IPER</li>
            <li>Profesiograma</li>
            <li>Registro de indicadores y ausentismo</li>
            <li>Análisis de vulnerabilidad y amenazas</li>
            <li>Procedimiento gestión de proveedores</li>
            <li>Procedimiento auditoría interna</li>
            <li>Acciones correctivas y preventivas</li>
            <li>Conformación COPASST / Vigía SST</li>
            <li>Conformación COCOLA</li>
            <li>Conformación Brigadas de Emergencia</li>
            <li>Formato de investigación de AT e IT</li>
            <li>Formato entrega de EPP y dotación</li>
            <li>Lista de chequeo personas naturales</li>
            <li>Lista de chequeo personas jurídicas</li>
          </ul>
          <p class="mt-3">El documento también incluye la lista extensa de anexos y documentos asociados del sistema. :contentReference[oaicite:18]{index=18}</p>
        </div>
      </section>

      <div class="footer-note">
        Documento interno del Sistema de Gestión de la Seguridad y Salud en el Trabajo
      </div>

    </div>
  </div>


<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>