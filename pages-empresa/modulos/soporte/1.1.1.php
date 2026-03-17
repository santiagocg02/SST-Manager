<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../../index.php");
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AN-SST-33 | Perfil Representante Alta Dirección</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --sst-border:#111;
      --sst-primary:#9fb4d9;
      --sst-primary-soft:#dbe7f7;
      --sst-bg:#eef3f9;
      --sst-paper:#ffffff;
      --sst-text:#111;
      --sst-muted:#5f6b7a;
      --sst-toolbar:#dde7f5;
      --sst-toolbar-border:#c8d3e2;
    }

    *{
      box-sizing:border-box;
    }

    html, body{
      margin:0;
      padding:0;
      font-family:Arial, Helvetica, sans-serif;
      background:var(--sst-bg);
      color:var(--sst-text);
    }

    .sst-toolbar{
      position:sticky;
      top:0;
      z-index:100;
      background:var(--sst-toolbar);
      border-bottom:1px solid var(--sst-toolbar-border);
      padding:12px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    .sst-toolbar-title{
      margin:0;
      font-size:15px;
      font-weight:800;
      color:#213b67;
    }

    .sst-toolbar-actions{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
    }

    .sst-page{
      padding:20px;
    }

    .sst-paper{
      width:216mm;
      min-height:279mm;
      margin:0 auto;
      background:var(--sst-paper);
      border:1px solid #d7dee8;
      box-shadow:0 10px 25px rgba(0,0,0,.08);
      padding:8mm;
      box-sizing:border-box;
    }

    .sst-table{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
    }

    .sst-table td,
    .sst-table th{
      border:1px solid var(--sst-border);
      padding:6px 8px;
      vertical-align:top;
      font-size:12px;
      word-wrap:break-word;
      height:auto;
    }

    .sst-title{
      background:var(--sst-primary);
      text-align:center;
      font-weight:800;
      text-transform:uppercase;
    }

    .sst-subtitle{
      background:var(--sst-primary-soft);
      text-align:center;
      font-weight:800;
      text-transform:uppercase;
    }

    .center{
      text-align:center;
    }

    .right{
      text-align:right;
    }

    .bold{
      font-weight:800;
    }

    .small{
      font-size:12px;
    }

    .muted{
      color:var(--sst-muted);
    }

    .sst-input,
    .sst-select{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      padding:2px 4px;
      font-family:Arial, Helvetica, sans-serif;
      color:#111;
    }

    .sst-textarea,
    .editable-block,
    .editable-list{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      line-height:1.45;
      padding:0;
      resize:none;
      overflow:hidden;
      height:auto;
      min-height:unset;
      font-family:Arial, Helvetica, sans-serif;
      color:#111;
      display:block;
    }

    .sst-input-line{
      width:100%;
      border:none;
      outline:none;
      background:transparent;
      font-size:12px;
      padding:2px 0;
      border-bottom:1px solid #666;
      font-family:Arial, Helvetica, sans-serif;
      color:#111;
    }

    .logo-box{
      height:72px;
      display:flex;
      align-items:center;
      justify-content:center;
      flex-direction:column;
      font-weight:800;
      color:#808080;
      border:2px dashed #b5b5b5;
      text-align:center;
      line-height:1.2;
    }

    .header-main{
      text-align:center;
      font-weight:800;
      font-size:14px;
      line-height:1.4;
      text-transform:uppercase;
    }

    .meta-box{
      display:flex;
      flex-direction:column;
      gap:8px;
      font-size:12px;
      height:100%;
      justify-content:center;
    }

    .meta-box .meta-item{
      text-align:right;
      font-weight:800;
    }

    .signature-space{
      height:24px;
    }

    .signature-line{
      border-top:1px solid #000;
      width:60%;
      margin:0 auto;
    }

    a.video-link{
      color:#0d6efd;
      word-break:break-all;
      text-decoration:none;
    }

    a.video-link:hover{
      text-decoration:underline;
    }

    @page{
      size:Letter;
      margin:8mm;
    }

    @media print{
      html, body{
        background:#fff !important;
      }

      .sst-toolbar{
        display:none !important;
      }

      .sst-page{
        padding:0 !important;
        margin:0 !important;
      }

      .sst-paper{
        width:100% !important;
        min-height:auto !important;
        margin:0 !important;
        border:none !important;
        box-shadow:none !important;
        padding:0 !important;
      }

      .sst-input,
      .sst-select,
      .sst-textarea,
      .sst-input-line,
      .editable-block,
      .editable-list{
        color:#000 !important;
      }

      a.video-link{
        color:#000 !important;
        text-decoration:none !important;
      }
    }

    @media (max-width: 991px){
      .sst-page{
        padding:12px;
      }

      .sst-paper{
        width:100%;
        min-height:auto;
        padding:12px;
      }

      .sst-toolbar{
        padding:12px;
      }
    }
  </style>
</head>
<body>

  <div class="sst-toolbar">
    <h1 class="sst-toolbar-title">Perfil Representante de la Alta Dirección para el SG-SST</h1>

    <div class="sst-toolbar-actions">
      <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
      <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Imprimir</button>
    </div>
  </div>

  <div class="sst-page">
    <div class="sst-paper">

      <table class="sst-table">
        <tr>
          <td style="width:18%;">
            <div class="logo-box">
              <div>TU LOGO</div>
              <div>AQUÍ</div>
            </div>
          </td>

          <td colspan="3">
            <div class="header-main">
              SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO<br>
              PERFIL REPRESENTANTE DE LA ALTA DIRECCIÓN PARA EL SG-SST
            </div>
          </td>

          <td style="width:18%;">
            <div class="meta-box">
              <div class="meta-item">0</div>
              <div class="meta-item">AN-SST-33</div>
              <div class="meta-item">
                <input class="sst-input-line" type="text" value="XX/XX/2025">
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="small">
            <span class="bold">Ver Video:</span>
            <a class="video-link" href="https://www.youtube.com/watch?v=rxf47zQ2FqM&t=4s" target="_blank" rel="noreferrer">
              https://www.youtube.com/watch?v=rxf47zQ2FqM&t=4s
            </a>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">I. Identificación del Cargo</td>
        </tr>

        <tr class="sst-subtitle">
          <td colspan="2">Nombre del cargo</td>
          <td>Cargo jefe inmediato</td>
          <td>Área</td>
          <td>Tiene personal a cargo</td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="sst-textarea">REPRESENTANTE DE LA ALTA DIRECCIÓN PARA EL SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</textarea>
          </td>
          <td>
            <textarea class="sst-textarea">GERENTE</textarea>
          </td>
          <td>
            <textarea class="sst-textarea">SEGURIDAD Y SALUD EN EL TRABAJO</textarea>
          </td>
          <td class="center">
            <select class="sst-select">
              <option selected>SI</option>
              <option>NO</option>
            </select>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="center bold">
            Actualización
            <input class="sst-input-line" style="display:inline-block; width:120px; text-align:center;" type="text" value="20 horas">
          </td>
        </tr>

        <tr class="sst-subtitle">
          <td>Educación</td>
          <td>Formación / conocimientos</td>
          <td>Experiencia</td>
          <td colspan="2">Entrenamiento</td>
        </tr>

        <tr>
          <td>
            <textarea class="sst-textarea">PROFESIONAL / TÉCNICO / TECNÓLOGO CON LICENCIA EN SST VIGENTE</textarea>
          </td>
          <td>
            <textarea class="sst-textarea">50 horas curso básico en SG-SST (Decreto 1072)</textarea>
          </td>
          <td>
            <textarea class="sst-textarea">2 años en diseño e implementación de sistemas de gestión de SST</textarea>
          </td>
          <td colspan="2">
            <textarea class="sst-textarea">COORDINADOR DE TRABAJO EN ALTURAS
BRIGADISTA
CURSO VIRTUAL DE 50 HORAS</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2" class="sst-title">III. Habilidades</td>
          <td colspan="3" class="sst-title">IV. Comportamientos Asociados</td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Liderazgo: Habilidad de poder liderar personal o situaciones.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Representa al equipo positivamente frente a las organizaciones internas y externas.
b) Resuelve problemas de relaciones interpersonales que afectan el desempeño de su área o de la organización.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Proactividad: Habilidad para tener la capacidad de auto motivación para lograr los objetivos establecidos en su trabajo.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Muestra una visión optimista de la vida y asume los retos con entusiasmo.
b) Aprende de buena manera lo que otros quieren enseñarle.
c) Mantiene actitud positiva frente a situaciones frustrantes.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Toma de decisiones: Habilidad de saber elegir entre varias alternativas de manera efectiva y rápida.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Asume la responsabilidad de sus acciones, sean exitosas o no.
b) Toma decisiones de bajo riesgo sin consultar al jefe.
c) Resuelve problemas y da respuesta a situaciones que surgen, tomando decisiones con agilidad y rapidez.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Preocupación por el orden y la calidad: Alto compromiso por desarrollar actividades ordenadas, con precisión y siguiendo estándares.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Aplica proceso de calidad que le corresponden a su cargo.
b) Demuestra un alto nivel de compromiso por realizar sus actividades de manera ordenada.
c) Busca optimizar el rendimiento de las herramientas que se le proporciona.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Compromiso organizacional: Realiza tareas oportunamente, se adapta al cambio, cumple normas y reglamentos.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Entregar trabajos en el tiempo asignado y con la calidad requerida.
b) Administrar de manera adecuada el tiempo para cumplir objetivos y tareas propuestas.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Trabajo en equipo: Comunicación efectiva, compartir información e ideas con respeto y cordialidad.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Expresar o transmitir ideas de manera clara y persuasiva.
b) Eficacia para identificar y solucionar problemas sin generar conflictos.
c) Trato cordial y amable con comunidades afectadas y personal que labora para la organización.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="2">
            <textarea class="editable-block">Orientación al cliente: Sensibilidad hacia necesidades del cliente interno/externo y satisfacción permanente.</textarea>
          </td>
          <td colspan="3">
            <textarea class="editable-block">a) Dar valor agregado a una función interna de la organización.
b) Considerar necesidades del cliente al diseñar o prestar servicios.
c) Establecer relaciones sólidas con clientes para fidelización.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">IV. Objeto del Cargo</td>
        </tr>

        <tr>
          <td colspan="5">
            <textarea class="sst-textarea">Se encargará de toda la gestión de mejora continua del Sistema de Gestión de la Seguridad y Salud en el Trabajo.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">V. Funciones y Responsabilidades</td>
        </tr>

        <tr>
          <td colspan="5">
            <input class="sst-input bold mb-2" type="text" value="1. Diseñar y mantener el sistema de gestión de seguridad y salud en el trabajo (SG-SST)">
            <input class="sst-input bold small mb-1" type="text" value="Responsabilidades">
            <textarea class="editable-list">• Planear, organizar, dirigir, desarrollar y aplicar el SG-SST; como mínimo una vez al año realizar su evaluación.
• Asegurar que los requisitos del SG-SST se establezcan, implementen y mantengan (Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normas asociadas).
• Verificar el cumplimiento y desempeño del SG-SST.
• Informar a la alta gerencia sobre el funcionamiento y los resultados del SG-SST.
• Promover la participación de todos los miembros de la empresa en la implementación del SG-SST.
• Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del SG-SST.
• Programar auditorías internas necesarias para el mantenimiento del SG-SST.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">VI. Autoridad</td>
        </tr>

        <tr>
          <td colspan="5">
            <textarea class="sst-textarea">Tiene autoridad para representar a la alta dirección en todos los temas del SG-SST.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="sst-title">VII. Responsabilidades en SST</td>
        </tr>

        <tr>
          <td colspan="5">
            <textarea class="sst-textarea">Promover el cumplimiento de los requisitos legales en SST.</textarea>
          </td>
        </tr>

        <tr>
          <td colspan="5" class="center bold" style="padding:18px;">
            <input class="sst-input center bold" type="text" value="FIRMA RECIBIDO Y ENTERADO">
            <div class="signature-space"></div>
            <div class="signature-line"></div>
          </td>
        </tr>
      </table>

    </div>
  </div>

  <script>
    function autoResize(el) {
      el.style.height = 'auto';
      el.style.height = el.scrollHeight + 'px';
    }

    document.addEventListener('DOMContentLoaded', function () {
      const textareas = document.querySelectorAll('.sst-textarea, .editable-block, .editable-list');

      textareas.forEach(function (el) {
        autoResize(el);

        el.addEventListener('input', function () {
          autoResize(el);
        });
      });
    });
  </script>

</body>
</html>