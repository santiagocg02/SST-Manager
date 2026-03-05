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
      --line:#1f1f1f;
      --blue:#b9cbe7;        /* franja azul clara tipo Excel */
      --blue2:#d6e2f3;       /* azul más suave */
      --text:#0b0b0b;
    }

    body{ background:#f4f6f8; color:var(--text); }
    .sheet-wrap{ max-width: 980px; margin: 20px auto; }
    .sheet-toolbar{
      display:flex; gap:10px; justify-content:space-between; align-items:center;
      margin-bottom: 10px;
    }
    .sheet{
      background:#fff;
      border:2px solid var(--line);
      box-shadow: 0 10px 22px rgba(0,0,0,.08);
    }

    /* tabla estilo formato */
    .fmt{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .fmt td, .fmt th{ border:1px solid var(--line); padding:8px; vertical-align:top; }
    .head-blue{ background:var(--blue); font-weight:800; text-align:center; }
    .sub-blue{ background:var(--blue2); font-weight:800; text-align:center; }
    .small{ font-size:12px; }
    .tight{ padding:6px; }
    .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:800; }
    .muted{ color:rgba(0,0,0,.65); }

    /* campos dentro de celdas */
    .cell-input, .cell-select, .cell-textarea{
      width:100%;
      border:1px solid rgba(0,0,0,.25);
      border-radius:6px;
      padding:7px 9px;
      font-size:14px;
      background:#fff;
    }
    .cell-textarea{ min-height: 72px; resize: vertical; }
    .cell-input.smallish{ font-size:13px; padding:6px 8px; }
    .no-border{ border:none !important; }
    .logo-box{
      border:2px dashed rgba(0,0,0,.35);
      height:72px;
      display:flex; align-items:center; justify-content:center;
      font-weight:800; color:rgba(0,0,0,.45);
    }

    /* listas tipo viñetas dentro de tabla */
    .bullet{
      margin: 0;
      padding-left: 18px;
    }
    .bullet li{ margin: 3px 0; }

    /* impresión */
    @media print{
      body{ background:#fff; }
      .sheet-toolbar{ display:none !important; }
      .sheet-wrap{ max-width:none; margin:0; }
      .sheet{ box-shadow:none; border:2px solid #000; }
      .cell-input, .cell-select, .cell-textarea{
        border:1px solid #000;
      }
    }
  </style>
</head>

<body>

<div class="sheet-wrap">

  <div class="sheet-toolbar">
    <div class="d-flex gap-2 flex-wrap">
      <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Atrás </a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
    <div class="small muted">AN-SST-33 (Frontend)</div>
  </div>

  <div class="sheet p-2">

    <table class="fmt">
      <!-- Encabezado superior -->
      <tr>
        <td style="width:18%">
          <div class="logo-box">TU LOGO<br>AQUÍ</div>
        </td>
        <td class="center bold" colspan="3">
          SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO<br>
          <span class="bold">PERFIL REPRESENTANTE DE LA ALTA DIRECCIÓN PARA EL SG SST</span>
        </td>
        <td style="width:18%">
          <div class="small">
            <div class="right bold">0</div>
            <div class="right bold">AN-SST-33</div>
            <div class="right">
              <input class="cell-input smallish" type="text" placeholder="XX/XX/2025">
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td colspan="5" class="small">
          <span class="bold">Ver Video:</span>
          <a href="https://www.youtube.com/watch?v=rxf47zQ2FqM&t=4s" target="_blank" rel="noreferrer">
            https://www.youtube.com/watch?v=rxf47zQ2FqM&t=4s
          </a>
        </td>
      </tr>

      <!-- I -->
      <tr><td colspan="5" class="head-blue">I. Identificación del Cargo</td></tr>
      <tr class="sub-blue">
        <td colspan="2">NOMBRE DEL CARGO</td>
        <td>CARGO JEFE INMEDIATO</td>
        <td>ÁREA</td>
        <td>TIENE PERSONAL A CARGO</td>
      </tr>
      <tr>
        <td colspan="2">
          <input class="cell-input" type="text"
            value="REPRESENTANTE DE LA ALTA DIRECCIÓN PARA EL SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO">
        </td>
        <td><input class="cell-input" type="text" value="GERENTE"></td>
        <td><input class="cell-input" type="text" value="SEGURIDAD Y SALUD EN EL TRABAJO"></td>
        <td class="center">
          <select class="cell-select">
            <option selected>SI</option>
            <option>NO</option>
          </select>
        </td>
      </tr>

      <tr>
        <td colspan="5" class="center small bold">Actualización
          <input class="cell-input smallish" style="display:inline-block; width:120px" type="text" value="20 horas">
        </td>
      </tr>

      <tr class="sub-blue">
        <td>EDUCACIÓN</td>
        <td>FORMACIÓN / CONOCIMIENTOS</td>
        <td>EXPERIENCIA</td>
        <td colspan="2">ENTRENAMIENTO</td>
      </tr>
      <tr>
        <td><textarea class="cell-textarea">PROFESIONAL / TÉCNICO / TECNÓLOGO CON LICENCIA EN SST VIGENTE</textarea></td>
        <td><textarea class="cell-textarea">50 horas curso básico en SG-SST (Decreto 1072)</textarea></td>
        <td><textarea class="cell-textarea">2 años en diseño e implementación de sistemas de gestión de SST</textarea></td>
        <td colspan="2"><textarea class="cell-textarea">COORDINADOR DE TRABAJO EN ALTURAS
BRIGADISTA
CURSO VIRTUAL DE 50 HORAS</textarea></td>
      </tr>

      <!-- III & IV -->
      <tr>
        <td colspan="2" class="head-blue">III. Habilidades</td>
        <td colspan="3" class="head-blue">IV. Comportamientos Asociados</td>
      </tr>

      <!-- Liderazgo -->
      <tr>
        <td colspan="2">
          <span class="bold">Liderazgo:</span> Habilidad de poder liderar personal o situaciones
        </td>
        <td colspan="3">
          <div class="small">
            a) Representa al equipo positivamente frente a las organizaciones internas y externas.<br>
            b) Resuelve problemas de relaciones interpersonales que afectan el desempeño de su área o de la organización.
          </div>
        </td>
      </tr>

      <!-- Proactividad -->
      <tr>
        <td colspan="2">
          <span class="bold">Proactividad:</span> Habilidad para tener la capacidad de auto motivación para lograr los objetivos establecidos en su trabajo.
        </td>
        <td colspan="3" class="small">
          a) Muestra una visión optimista de la vida y asume los retos con entusiasmo.<br>
          b) Aprende de buena manera lo que otros quieren enseñarle.<br>
          c) Mantiene actitud positiva frente a situaciones frustrantes.
        </td>
      </tr>

      <!-- Toma de decisiones -->
      <tr>
        <td colspan="2">
          <span class="bold">Toma de decisiones:</span> Habilidad de saber elegir entre varias alternativas de manera efectiva y rápida.
        </td>
        <td colspan="3" class="small">
          a) Asume la responsabilidad de sus acciones, sean exitosas o no.<br>
          b) Toma decisiones de bajo riesgo sin consultar al jefe.<br>
          c) Resuelve problemas y da respuesta a situaciones que surgen, tomando decisiones con agilidad y rapidez.
        </td>
      </tr>

      <!-- Orden y calidad -->
      <tr>
        <td colspan="2">
          <span class="bold">Preocupación por el orden y la calidad:</span> Alto compromiso por desarrollar actividades ordenadas, con precisión y siguiendo estándares.
        </td>
        <td colspan="3" class="small">
          a) Aplica proceso de calidad que le corresponden a su cargo.<br>
          b) Demuestra un alto nivel de compromiso por realizar sus actividades de manera ordenada.<br>
          c) Busca optimizar el rendimiento de las herramientas que se le proporciona.
        </td>
      </tr>

      <!-- Compromiso -->
      <tr>
        <td colspan="2">
          <span class="bold">Compromiso Organizacional:</span> Realiza tareas oportunamente, se adapta al cambio, cumple normas y reglamentos.
        </td>
        <td colspan="3" class="small">
          a) Entregar trabajos en el tiempo asignado y con la calidad requerida.<br>
          b) Administrar de manera adecuada el tiempo para cumplir objetivos y tareas propuestas.
        </td>
      </tr>

      <!-- Trabajo en equipo -->
      <tr>
        <td colspan="2">
          <span class="bold">Trabajo en equipo:</span> Comunicación efectiva, compartir información e ideas con respeto y cordialidad.
        </td>
        <td colspan="3" class="small">
          a) Expresar o transmitir ideas de manera clara y persuasiva.<br>
          b) Eficacia para identificar y solucionar problemas sin generar conflictos.<br>
          c) Trato cordial y amable con comunidades afectadas y personal que labora para la organización.
        </td>
      </tr>

      <!-- Orientación -->
      <tr>
        <td colspan="2">
          <span class="bold">Orientación al cliente:</span> Sensibilidad hacia necesidades del cliente interno/externo y satisfacción permanente.
        </td>
        <td colspan="3" class="small">
          a) Dar valor agregado a una función interna de la organización.<br>
          b) Considerar necesidades del cliente al diseñar o prestar servicios.<br>
          c) Establecer relaciones sólidas con clientes para fidelización.
        </td>
      </tr>

      <!-- IV Objeto -->
      <tr><td colspan="5" class="head-blue">IV. Objeto del Cargo</td></tr>
      <tr>
        <td colspan="5">
          <textarea class="cell-textarea">Se encargará de toda la gestión de mejora continua del Sistema de Gestión de la Seguridad y Salud en el Trabajo.</textarea>
        </td>
      </tr>

      <!-- V Funciones -->
      <tr><td colspan="5" class="head-blue">V. Funciones y Responsabilidades</td></tr>
      <tr>
        <td colspan="5">
          <div class="bold mb-2">1. Diseñar y mantener el sistema de gestión de seguridad y salud en el trabajo (SGSST)</div>
          <div class="bold small mb-1">Responsabilidades</div>
          <ul class="bullet small">
            <li>Planear, organizar, dirigir, desarrollar y aplicar el SGSST; como mínimo una vez al año realizar su evaluación.</li>
            <li>Asegurar que los requisitos del SG SST se establezcan, implementen y mantengan (Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normas asociadas).</li>
            <li>Verificar el cumplimiento y desempeño del SGSST.</li>
            <li>Informar a la alta gerencia sobre el funcionamiento y los resultados del SGSST.</li>
            <li>Promover la participación de todos los miembros de la empresa en la implementación del SGSST.</li>
            <li>Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del SGSST.</li>
            <li>Programar auditorías internas necesarias para el mantenimiento del SGSST.</li>
          </ul>
        </td>
      </tr>

      <!-- VI Autoridad -->
      <tr><td colspan="5" class="head-blue">VI. Autoridad</td></tr>
      <tr>
        <td colspan="5">
          <textarea class="cell-textarea">Tiene autoridad para representar a la alta dirección en todos los temas del SGSST.</textarea>
        </td>
      </tr>

      <!-- VII -->
      <tr><td colspan="5" class="head-blue">VII. Responsabilidades en SST</td></tr>
      <tr>
        <td colspan="5">
          <textarea class="cell-textarea">Promover el cumplimiento de los requisitos legales en SST.</textarea>
        </td>
      </tr>

      <!-- Firma -->
      <tr>
        <td colspan="5" class="center bold" style="padding:18px;">
          FIRMA RECIBIDO Y ENTERADO
          <div style="height:24px;"></div>
          <div style="border-top:1px solid #000; width:60%; margin:0 auto;"></div>
        </td>
      </tr>

    </table>

  </div>
</div>

</body>
</html>