<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../index.php");
  exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";

// Recibimos el id_empresa por la URL (GET) o tomamos el de la sesión como respaldo.
$empresa = isset($_GET['id_empresa']) ? (int)$_GET['id_empresa'] : (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 1; 

// 2. SOLICITAMOS LOS DATOS DEL FORMULARIO A LA API (Para saber si ya hay algo guardado)
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);

// 3. DATOS DE LA EMPRESA DESDE LA BASE DE DATOS (Para auto-llenar)
$resEmpresaInfo = $api->solicitar("empresas/$empresa", "GET", null, $token);
$infoEmpresa = $resEmpresaInfo['data'] ?? [];

// Extraemos los datos de la empresa para inyectarlos en el HTML
$cli_empresa       = $infoEmpresa['nombre_empresa'] ?? '';
$cli_nit           = $infoEmpresa['numero_documento'] ?? '';
$cli_representante = $infoEmpresa['nombre_rl'] ?? '';
$cli_cc            = $infoEmpresa['documento_rl'] ?? '';
$cli_direccion     = $infoEmpresa['direccion'] ?? '';
$cli_telefono      = $infoEmpresa['telefono'] ?? '';
$cli_firma         = $infoEmpresa['firma_rl'] ?? '';

// Lógica para saber de dónde sacar los campos dinámicos
$datosCampos = [];
$camposCrudos = null;

if (isset($resFormulario['data']['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['data']['campos'];
} elseif (isset($resFormulario['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['campos'];
} elseif (isset($resFormulario['campos'])) {
    $camposCrudos = $resFormulario['campos'];
}

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true);
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>F-JUR-002 | Contrato Prestación de Servicios SGSST</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    *{ box-sizing:border-box; }
    html, body{ margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; background:var(--sst-bg); color:var(--sst-text); }
    .sst-toolbar{ position:sticky; top:0; z-index:100; background:var(--sst-toolbar); border-bottom:1px solid var(--sst-toolbar-border); padding:12px 18px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
    .sst-toolbar-title{ margin:0; font-size:15px; font-weight:800; color:#213b67; }
    .sst-toolbar-actions{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .sst-page{ padding:20px; }
    .sst-paper{ width:216mm; min-height:279mm; margin:0 auto; background:var(--sst-paper); border:1px solid #d7dee8; box-shadow:0 10px 25px rgba(0,0,0,.08); padding:8mm; box-sizing:border-box; }
    .sst-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .sst-table td, .sst-table th{ border:1px solid var(--sst-border); padding:6px 8px; vertical-align:middle; font-size:12px; word-wrap:break-word; height:auto; }
    .sst-title{ background:var(--sst-primary); text-align:center; font-weight:800; text-transform:uppercase; }
    .sst-subtitle{ background:var(--sst-primary-soft); text-align:center; font-weight:800; text-transform:uppercase; }
    .center{ text-align:center; }
    .bold{ font-weight:800; }
    .small{ font-size:12px; }
    .sst-input, .sst-select{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 4px; font-family:Arial, Helvetica, sans-serif; color:#111; }
    .sst-textarea, .editable-block, .editable-list{ width:100%; border:none; outline:none; background:transparent; font-size:12px; line-height:1.45; padding:0; resize:none; overflow:hidden; height:auto; min-height:unset; font-family:Arial, Helvetica, sans-serif; color:#111; display:block; text-align: justify; }
    .sst-input-line{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 0; border-bottom:1px solid #666; font-family:Arial, Helvetica, sans-serif; color:#111; }
    .header-main{ text-align:center; font-weight:800; font-size:14px; line-height:1.4; text-transform:uppercase; margin:0; padding:10px; }
    .signature-space{ height:24px; }
    .signature-line{ border-top:1px solid #000; width:80%; margin:0 auto; }
    @page{ size:Letter; margin:8mm; }
    @media print{
      html, body{ background:#fff !important; }
      .sst-toolbar{ display:none !important; }
      .sst-page{ padding:0 !important; margin:0 !important; }
      .sst-paper{ width:100% !important; min-height:auto !important; margin:0 !important; border:none !important; box-shadow:none !important; padding:0 !important; }
      .sst-input, .sst-select, .sst-textarea, .sst-input-line, .editable-block, .editable-list{ color:#000 !important; }
    }
  </style>
</head>
<body>

  <form id="form-sst-dinamico">
    <div class="sst-toolbar">
      <h1 class="sst-toolbar-title">Contrato de Prestación de Servicios SG-SST</h1>

      <div class="sst-toolbar-actions">
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar">
            <i class="fa-solid fa-save"></i> Guardar Contrato
        </button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
      </div>
    </div>

    <div class="sst-page">
      <div class="sst-paper">

        <table class="sst-table">
          <tr>
            <td style="width: 20%; text-align: center; font-weight: 800; font-size: 13px;">
              F-JUR-002-2025
            </td>
            <td colspan="3" style="width: 60%;">
              <div class="header-main">
                CONTRATO PRESTACIÓN DE SERVICIOS<br>
                IMPLEMENTACIÓN DEL SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO (SG-SST) E IMPLEMENTACION RECURSOS HUMANOS
              </div>
            </td>
            <td style="width: 20%; text-align: center; font-weight: 800; font-size: 13px;">
              Fecha: <input name="fecha_documento" class="sst-input-line" style="width: 70px; text-align: center; font-weight: bold; margin-left: 5px;" type="text" value="<?= date('d/m/Y') ?>">
            </td>
          </tr>

          <tr><td colspan="5" class="sst-title">DATOS DEL CLIENTE</td></tr>
          <tr>
            <td class="bold">EMPRESA CLIENTE:</td>
            <td colspan="2"><input name="cliente_empresa" class="sst-input" value="<?= htmlspecialchars($cli_empresa) ?>"></td>
            <td class="bold">NIT:</td>
            <td><input name="cliente_nit" class="sst-input" value="<?= htmlspecialchars($cli_nit) ?>"></td>
          </tr>
          <tr>
            <td class="bold">REPRESENTANTE LEGAL:</td>
            <td colspan="2"><input name="cliente_representante" class="sst-input" value="<?= htmlspecialchars($cli_representante) ?>"></td>
            <td class="bold">CÉDULA:</td>
            <td><input name="cliente_cc" class="sst-input" value="<?= htmlspecialchars($cli_cc) ?>"></td>
          </tr>
          <tr>
            <td class="bold">LUG. EXPEDICIÓN:</td>
            <td colspan="2"><input name="cliente_expedicion" class="sst-input" value=""></td>
            <td class="bold">TELÉFONO:</td>
            <td><input name="cliente_telefono" class="sst-input" value="<?= htmlspecialchars($cli_telefono) ?>"></td>
          </tr>
          <tr>
            <td class="bold">DIRECCIÓN:</td>
            <td colspan="2"><input name="cliente_direccion" class="sst-input" value="<?= htmlspecialchars($cli_direccion) ?>"></td>
            <td class="bold">CIUDAD:</td>
            <td><input name="cliente_ciudad" class="sst-input" value=""></td>
          </tr>

          <tr><td colspan="5" class="sst-title">DATOS DEL PROVEEDOR</td></tr>
          <tr>
            <td class="bold">EMPRESA PROVEEDOR:</td>
            <td colspan="2"><input name="proveedor_empresa" class="sst-input" value=""></td>
            <td class="bold">NIT:</td>
            <td><input name="proveedor_nit" class="sst-input" value=""></td>
          </tr>
          <tr>
            <td class="bold">REPRESENTANTE LEGAL:</td>
            <td colspan="2"><input name="proveedor_representante" class="sst-input" value=""></td>
            <td class="bold">CÉDULA:</td>
            <td><input name="proveedor_cc" class="sst-input" value=""></td>
          </tr>
          <tr>
            <td class="bold">DIRECCIÓN:</td>
            <td colspan="2"><input name="proveedor_direccion" class="sst-input" value=""></td>
            <td class="bold">TELÉFONO:</td>
            <td><input name="proveedor_telefono" class="sst-input" value=""></td>
          </tr>
          <tr>
            <td class="bold" colspan="1">CIUDAD:</td>
            <td colspan="4"><input name="proveedor_ciudad" class="sst-input" value=""></td>
          </tr>

          <tr><td colspan="5" class="sst-title">CLÁUSULAS DEL CONTRATO</td></tr>
          
          <tr>
            <td colspan="5" style="padding: 15px;">
              <textarea name="clausula_intro" class="editable-block mb-3">Reunidos de una parte el Representante Legal en nombre de la empresa denominada en adelante el “CLIENTE”, y de otra parte el Representante Legal en nombre de la empresa en adelante el “PROVEEDOR”. El CLIENTE y EL PROVEEDOR, en adelante, podrán ser denominadas, individualmente, “la Parte” y conjuntamente, “las Partes”, reconociéndose mutuamente capacidad jurídica y de obra, suficiente para la celebración del presente Contrato.</textarea>

              <div class="bold mb-1">EXPONEN</div>
              <textarea name="clausula_exponen" class="editable-block mb-4">PRIMERO: Que el CLIENTE, está interesado en darle continuidad y fortalecer el Sistema de Gestión de Seguridad y Salud en el Trabajo; basado en la organización y planificación, con el objetivo no solo de cumplir con un requisito legal, sino también de anticipar, reconocer, controlar y evaluar los riesgos que puedan afectar la Seguridad y la Salud de sus colaboradores e implementar recursos humanos como función crítica y esencial para toda organización como pilar de la eficacia, productividad y el éxito de la empresa.

SEGUNDO: Que la necesidad del CLIENTE, es también velar por la seguridad y la salud de sus empleados, para garantizar la aplicación de las medidas de seguridad y salud en el trabajo, el mejoramiento del comportamiento de los trabajadores, las condiciones y el ambiente laboral y el control eficaz de los riesgos que se puedan presentar en el lugar de trabajo.

TERCERO: Que el PROVEEDOR es una empresa constituida formalmente especializada y certificada en la prestación de los servicios que se exponen en este contrato.

CUARTO: Que las Partes están interesadas en celebrar un contrato de prestación de servicios en virtud del cual el PROVEEDOR preste al CLIENTE los servicios descritos en el punto anterior. Que las partes reunidas en la sede social del CLIENTE, acuerdan celebrar el presente contrato de prestación de servicios, en adelante el "Contrato", de acuerdo con las siguientes CLÁUSULAS:</textarea>

              <div class="bold mb-1">PRIMERA. - OBJETO.</div>
              <textarea name="clausula_1" class="editable-block mb-3">En virtud del contrato el PROVEEDOR se obliga a prestar al CLIENTE el servicio de: DISEÑO E IMPLEMENTACION DEL SISTEMA DE GESTIÓN SEGURIDAD Y SALUD EN EL TRABAJO, (Decreto 1072 de 2015 y Resolución 0312 de 2019), con el fin de priorizar y establecer controles que permitan llevar a cabo el cronograma de las actividades proyectadas. Crear, implementar y gestionar recursos humanos a través de programas y procedimientos eficientes, así como velar por el cumplimiento normativo.</textarea>

              <div class="bold mb-1">SEGUNDA. - ALCANCE DEL SISTEMA.</div>
              <textarea name="clausula_2" class="editable-block mb-3">El PROVEEDOR, se compromete a brindar todo el apoyo para que EL CLIENTE ejecute el SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO Y RECURSOS HUMANOS el cual tendrá una cobertura para todos los trabajadores directos y temporales que laboran en la empresa.</textarea>

              <div class="bold mb-1">TERCERA. - ACTIVIDADES ESPECÍFICAS DEL CONTRATO.</div>
              <textarea name="clausula_3" class="editable-block mb-3">3.1 SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
3.1.1 Reporte de Autoevaluación en la plataforma del ministerio.
3.1.2 Reporte de Autoevaluación plataforma ARL.
3.1.3 Desarrollo Autoevaluación Estándares Mínimos Res 0312 de 2019.
3.1.4 Elaboración de Plan de Mejora (Resultados Autoevaluación EM).
3.1.5 Elaboración Plan de Trabajo del SGSST 2025.
3.1.6 Diseño y socialización de las Políticas del SG-SST.
3.1.7 Diseño y socialización del Reglamento de Higiene y Seguridad Industrial.
3.1.8 Diseño de Matriz de Objetivos y Metas del SGSST.
3.1.9 Diseño del Perfil sociodemográfico poblacional.
3.1.10 Diseño y Socialización de roles y responsabilidades del SGSST.
3.1.11 Inducción y Reinducción General en SG-SST.
3.1.12 Diseño procedimiento elaboración de matriz de peligros y riesgos.
3.1.13 Diseño de la Matriz de Peligros y Riesgos.
3.1.14 Diseño procedimiento elaboración de matriz de requisitos legales.
3.1.15 Diseño de Matriz de Requisitos Legales.
3.1.16 Diseño Procedimientos y Estándares Seguros de Trabajo.
3.1.17 Conformación de Copasst.
3.1.18 Inducción inicial para el Copasst.
3.1.19 Conformación del comité de Convivencia Laboral.
3.1.20 Inducción inicial del comité de Convivencia Laboral.
3.1.21 Conformación de Brigada de Emergencia (Clase I).
3.1.22 Diseño Programa de Pausas Activas.
3.1.23 Registros y seguimiento a la Accidentalidad y Ausentismo.
3.1.24 Diseño Fichas Indicadores SGSST; Estructura, Proceso y Resultado.
3.1.25 Diseño Indicadores Estándares Mínimos.
3.1.26 Diseño Programa de Inspecciones Operacionales.
3.1.27 Diseño Programa y cronograma de Inducción, Capacitación y Entrenamiento.
3.1.28 Diseño procedimiento para los exámenes ocupacionales.
3.1.29 Diseño de Matriz de exámenes ocupacionales.
3.1.30 Diseño de cartas de recomendaciones medicas ocupacionales.
3.1.31 Seguimiento periódico exámenes ocupacionales.
3.1.32 Diseño de Procedimiento de Reintegro Laboral.
3.1.33 Diseño de Manual de Proveedores y Contratistas.
3.1.34 Diseño Matriz de registro de Proveedores y Contratistas.
3.1.35 Socialización SGSST para Proveedores y Contratistas.
3.1.36 Diseño procedimiento Gestión del cambio.
3.1.37 Diseño procedimiento para el reporte de Accidentes e Incidentes de Trabajo.
3.1.38 Procedimiento Investigación de Incidentes y Accidentes de Trabajo.
3.1.39 Investigación Accidentes de Trabajo (Leves).
3.1.40 Diseño Seguimiento a Indicadores SG-SST.
3.1.41 Diseño de Procedimiento de Auditorías Internas.
3.1.42 Acompañamiento en la Revisión por la Dirección.
3.1.43 Diseño procedimiento manejo de Acciones Preventivas, Correctivas y de Mejora.
3.1.44 Informes de seguridad industrial y de emergencias periódicos.
3.1.45 Socialización resultados de las actividades del SG-SST.

3.2 PLAN DE PREPARACION Y RESPUESTA ANTE EMERGENCIAS
3.2.1 Desarrollo de diagnóstico inicial en sede, observación de instalaciones propias y establecimientos vecinos.
3.2.2 Informe Identificación de Amenazas y Análisis de vulnerabilidad de la sede.
3.2.3 Plan de Ayuda mutua.
3.2.4 Capacitación al personal en Primeros Auxilios Básicos.
3.2.5 Capacitación básica al personal en Prevención de incendios (Manejo de Extintores).
3.2.6 Elaboración de Procedimientos operativos normalizados PON's.
3.2.7 Diseño del Plan de preparación y respuesta ante emergencias.
3.2.8 Socialización del Plan de preparación y respuesta ante emergencias.
3.2.9 Conformación y socialización Brigada de Emergencia (primeros respondientes). Roles y Responsabilidades, Marco Legal.
3.2.10 Definición de Rutas de Evacuación, Salida de Emergencia y Punto de Encuentro.
3.2.11 Diseño guion para cada Simulacro.
3.2.12 Desarrollo de Simulacro de evacuación y evaluación del ejercicio.
3.2.13 Recomendaciones y correcciones al Plan de Emergencias - Mejora continua.</textarea>

              <div class="bold mb-1">CUARTA. - PROGRAMA DE CAPACITACIONES.</div>
              <textarea name="clausula_4" class="editable-block mb-3">El PROVEEDOR. Establece un Programa de capacitaciones propias del Sistema y Otras que hacen parte de la Responsabilidad Social Empresarial, las cuales se desarrollan de acuerdo con la prioridad, disposición de espacios y personal, ordenados por el CLIENTE:

- Capacitación inicial a Gerencia, socios y Líderes empresa en Responsabilidades del SG-SST.
- Inducción general del sistema de gestión.
- Socialización política y reglamento de higiene y seguridad industrial, Matriz de riesgos.
- Socialización de Funciones del Copasst, Comité de Convivencia y Brigada.
- Reporte y Manejo de Accidentes de Trabajo y Enfermedad Laboral.
- Riesgo Mecánico y uso de EPP.
- Riesgo Biomecánico / Higiene Postural y Manipulación de Cargas (Fisioterapeuta).
- Socialización Plan de Emergencias y simulacros.
- Taller Primeros auxilios (Paramédico).
- Taller Manejo de extintores, prevención y control del fuego (Bombero).

Nota 1: Los profesionales que imparten los talleres o capaciones no tienen cargo para la empresa.
Nota 2: La capacitación de Emergencia es para todo el personal de una hora por tema, conocimientos básicos sin certificación. Para capacitar la brigada que se toma el 20% de la población, se requiere un mínimo de cuatro Horas que pueden hacer uso de los cronogramas de la Arl. Sin embargo, para efectos de certificación y tiempo, también pueden hacer uso del Bombero quien se desplazaría hasta sus instalaciones para realizar la actividad de acuerdo al tiempo destinado por la empresa.
Nota 3: Los temas se abordan uno por mes de trabajo de acuerdo con disponibilidad de cliente.
Nota 4: La empresa cliente es responsable de la logística de cada capacitación al igual que hacer la difusión de cada capacitación programada. SCE Consultores realiza la invitación, la cual es enviada a la empresa para su difusión con el personal.</textarea>

              <div class="bold mb-1">QUINTA. - CAMPAÑAS Y OTRAS ACTIVIDADES Y GESTIÓN DEL TALENTO HUMANO.</div>
              <textarea name="clausula_5" class="editable-block mb-3">CAMPAÑAS Y OTRAS ACTIVIDADES:
- Seguridad Vial.
- Hábitos saludables de vida y trabajo.
- Pausas Activas.
- Importancia del Orden y aseo.
- La actividad física diaria.
- Uso y cuidados de los EPP.
- Prevención consumo Tabaco Alcohol y droga.
- Higiene de manos.
Nota: Se envían piezas digitales vía correo y/o WhatsApp a la empresa para ser compartidos con el personal.

GESTION DEL TALENTO HUMANO:
- Diagnóstico Inicial: Chequeo de la información.
- Desarrollo de procesos: Diseño de Documentación, formatos, procedimientos con su correspondiente codificación y control documental.
- Inducción del puesto de Trabajo: Se diseña y detalla cada una de las tareas, el paso a paso responsabilidades, objetivos y expectativas, así como la forma en que su puesto contribuye a la empresa.
- Evaluación de desempeño: Diseño de formato para evaluar al colaborador inicialmente a los dos meses y posterior a su periodo de prueba semestral.
- Directrices estratégicas de la empresa: Políticas, Misión, visión, Organigrama, Valores Corporativos.
- Definición de Estructura: Diseño de Perfil y manual de funciones por cargo.
- Procedimiento de ingreso del colaborador: recopilación de la documentación del candidato, ingreso documental, programación examen ocupacional. Validación resultados del candidato.
- Vinculación Seguridad Social: Realizar procedimiento de afiliación a las diferentes administradoras de seguridad social.
- Legalización Cuenta Bancaria: Verificar tramite de creación cuenta bancaria para nomina previo al ingreso.
- Dotación y EPP: Realizar entrega de dotación completa de acuerdo con el cargo que va a desempeñar el trabajador y de acuerdo con normatividad vigente. Abril 30, agosto 30 y diciembre 20 y sus epp de acuerdo con su periodicidad.
- Socialización documentos trabajador: Socializar con el trabajador que ingresa, Reglamento interno de trabajo, Contrato de Trabajo, perfil de cargo, documentos legales, inducción SGSST, procedimiento de firmas y legalización.
- Control Archivo: Preservar y sistematizar archivo del personal, Controlar y llevar al día el archivo físico y digital.
- Tramite de Incapacidades: Radicación, seguimiento y procedimiento de cobro a las diferentes administradoras.
- Ausentismo: Control ausentismo incapacitante y no incapacitante, registros en formato, seguimiento.
- Documentación laboral: Diligenciar y expedir certificaciones laborales, cartas de despido, vacaciones, permisos, comunicados.
- Procedimientos disciplinarios: Diseño Socialización y cargue de llamados de atención, memorandos, procedimientos de descargos a cada líder de área para que realice dicho procedimiento (diligencia de datos, firmas, etc). Realizar seguimiento a los mismos llevar un control y archivo.
- Procedimiento de Quejas: Diseño y socialización de procedimiento con sus formatos para quejas y resolución de conflictos.
- Cartera y Estados de Cuenta: Realizar depuración de cartera de todas las administradoras, enviar respuestas, realizar seguimientos, paz y salvos.
- Seguridad Social: Realizar alistamiento y aplicación de novedades de la planilla de seguridad en el operador aportes en línea de forma periódica, informar fechas de vencimiento de la misma, enviar planilla pre-lista para su verificación y pago, descargar y mantener archivo digital de las planillas canceladas de manera mensual.
- Compras y adquisiciones: Realizar cotizaciones de los diferentes requerimientos en Seguridad y Salud en el Trabajo que necesite la empresa.
- Inventario: llevar un inventario en tiempo real de los EPP, revisión de fichas técnicas, recambios, Seguimiento a la calidad de la dotación.
- Ejecución de tareas y/o actividades: Entrega de comunicados a cada trabajador, logística para salidas, paseos de fin de año, actividades de integración, capacitación, formación.
- Cumplimiento Legal: Creación de contratos, modificaciones, otros si.
- Consultoría y Asesoría legal: De acuerdo con el conocimiento del CST Ley 100 y demás normatividad, se dará permanente acompañamiento en materia de recursos humanos, novedades nuevas resoluciones que afecten el area.
- Gestión Seguridad Social: Dar respuesta constante a requerimientos de los colaboradores: inclusiones de beneficiarios, exclusiones, cambios de entidad administradora, cambios de Ips. Resolución de novedades como la no atención, no entrega de medicamentos, seguimiento a casos especiales con la súper salud Etc.
- Informes Periódicos: Rendir informes de gestión o novedades de manera trimestral.
- Casos médicos: Alinear y dar seguimiento desde recursos humanos a casos médicos, reincorporación laboral, reubicación de puesto, casos de restricciones, recomendaciones temporales etc.
- Comunicaciones: Alimentar y mantener al día el tablero informativo con diferentes comunicados de interés para los colaboradores, fechas de cumpleaños, documentos legales que se deban tener publicados y actualizados.
- Presupuesto: Realizar el presupuesto semestral y anual del sistema de gestión, revisar con contabilidad, llevar al día el formato.</textarea>

              <div class="bold mb-1">SEXTA. - METODOLOGIA PLAN DE TRABAJO.</div>
              <textarea name="clausula_6" class="editable-block mb-3">La Consultoría se dará de la siguiente forma:

1. Diagnóstico Inicial: Se realiza una visita de reconocimiento locativo, donde se identifican condiciones de señalética, emergencias, demarcación entre otros aspectos de seguridad industrial. Se evalúa la gestión documental física y digital revisando procedimientos, manuales, programas, matrices, registros entre otros del SG-SST y de Recursos humanos existentes. Se revisa documentación legal de la empresa, rut, Actividad económica cámara de comercio, centros de trabajo y niveles de riesgo de acuerdo con la nueva normatividad, carpetas de los colaboradores, planillas etc. Se realiza entrevista con Líder del Sistema para contextualizar la empresa. Se genera informe preliminar como punto de partida para darle continuidad al proceso.

2. Plan de Trabajo: Se estructura un plan de trabajo para llevar a cabo durante el semestre. En el plan de trabajo se registran todas las actividades clasificadas por mes de acuerdo con la prioridad encontrada. Se evalúa mes a mes los indicadores de cumplimiento del plan de trabajo.

3. Tareas: Se estructura un acta incorporando las actividades mensuales del plan de trabajo con el objetivo de realizar seguimiento puntual y un mejor control de las tareas asignadas.

4. Capacitación: Se realiza un cronograma de capacitaciones, talleres y actividad lúdica. Se programa capacitación mensual de acuerdo con el tiempo y espacio aprobado por la empresa para todo el personal.

5. Inspecciones: Se ejecuta el programa de inspecciones periódicas en las instalaciones. El equipo consultor estructura un cronograma para liderar las inspecciones generales se realiza inducción de estas al Copasst y Brigada.

6. Informes: Derivado de las inspecciones se genera un informe con recomendaciones para que se estructure y documente un plan de acción. Se realiza un informe de seguimiento de las acciones preventivas correctivas y de mejora. Se realiza un informe trimestral con los indicadores de cumplimiento y se trazan planes de acción para las actividades no ejecutadas.

7. Accidentalidad: Se realiza reportes o acompañamiento e inducción en reporte del accidente, se ejecuta la investigación, seguimiento, plan de acción y se socializa lección aprendida. Aplica para accidentes leves, los graves tienen un valor adicional de la asesoría.

8. Reunión mensual: Se realiza con Líder asignado para la revisión del avance del Sistema en cada una de las fases propuestas para su conocimiento, retroalimentación y mejora, revisar tareas pendientes y gestión de informes.

9. Reunión Trimestral: con la Gerencia y Líder asignado del Sistema de gestión para revisión de avances.

10. Documentación: La documentación del sistema como manuales, programas, procedimientos, formatos, evaluaciones, inspecciones, se manejan bajo archivos digitales y físicos, se entregan de manera periódica posterior a revisión de la empresa, los registros físicos quedarán en custodia del líder asignado de la empresa, los digitales en conjunto por medio de servicio de almacenamiento en nube y disco duro.

11. Visitas presenciales: SCE Consultores de acuerdo con la necesidad evidenciada de la empresa asigna dos visitas semanales coordinadas con el Líder del sistema de gestión asignado por la empresa para la ejecución de las diferentes actividades programadas.

12. Seguimiento virtual: El equipo consultor dispone de canales virtuales previamente concertados y programados para adelantar, procesos disciplinarios, documentación, consultas, reuniones que se requieran, solicitar información de manera virtual permanentemente, fuera de las dos visitas a la semana.

13. Visitas de inspección y vigilancia: El equipo consultor realiza el acompañamiento si se presentan visitas programadas por entidades como Ministerio de Trabajo o ARL, espacios previamente concertados con equipo consultor.

EL PROVEEDOR, realizará un acompañamiento así:
LA EMPRESA, deberá emitir una autorización a los profesionales del equipo Consultor, para ingresar a la empresa a realizar las diferentes actividades que estén programadas dentro del Sistema de Gestión.
EI PROVEEDOR. Tomará evidencias fotográficas y videos de uso exclusivo para el desarrollo de análisis de riesgos, vulnerabilidad y recomendaciones.
LA EMPRESA, deberá suministrar toda la información necesaria al PROVEEDOR para la implementación del Sistema de Gestión de Seguridad y Salud en el Trabajo.</textarea>

              <div class="bold mb-1">SEPTIMA. - PRESENTACION DE RESULTADOS.</div>
              <textarea name="clausula_7" class="editable-block mb-3">EI PROVEEDOR, presentará un plan de trabajo con todas las actividades y dará retroalimentación al líder asignado de manera mensual, identificando los alcances y/o inconvenientes presentados, así como recomendaciones, y en general todos los hallazgos del Sistema de Gestión.
EI PROVEEDOR, presentará informes trimestrales del avance del Sistema de Gestión con la Gerencia, cita acordada previamente.
La documentación como manuales, programas, procedimientos, formatos, evaluaciones, inspecciones, archivos digitales etc, se entregarán de manera periódica posterior a revisión por parte de auditoría y quedarán en custodia del líder asignado de la empresa.</textarea>

              <div class="bold mb-1">OCTAVA. - CONSIDERACIONES ESPECIALES.</div>
              <textarea name="clausula_8" class="editable-block mb-3">EI PROVEEDOR dentro del alcance del presente contrato NO INCLUYE:
- Prestación de servicios fuera del área metropolitana (estos servicios se cobran de manera independiente).
- Validación del profesiograma (este corresponde al médico ocupacional de la IPS ocupacional).
- Diseño de programas de vigilancia epidemiológica (este corresponde al médico ocupacional de la IPS ocupacional).
- Gestión de tareas de alto riesgo (dígase Trabajo en alturas, trabajos en caliente, izaje de cargas, entre otros).
- No están incluidas auditorías internas correspondientes al Sistema de Gestión de SST. (Este lo debe realizar personal externo a los consultores SCE, por lo cual este servicio genera un cobro adicional)
- No se realizan exámenes ocupacionales y paraclínicos.
- No se suministra Señalización en Emergencias y Seguridad Industrial.
- No se realiza Demarcación en piso.
- No se suministran extintores o recargas.
- No se realizan Estudios de Puesto de Trabajo (este es trabajo de un especialista de la salud).
- No se realiza aplicación de Batería Riesgo Psicosocial.
- No se realizan mediciones de higiene industrial.
- Plan estratégico de seguridad vial PESV (costo adicional del plan)
- Investigación de ACCIDENTES DE TRABAJO GRAVES Y MORTALES*
*EI PROVEEDOR cuenta con las competencias y licencia para realizar la investigación de estos accidentes, sin embargo, de acuerdo a la complejidad y compromiso de estos eventos no está dentro del presupuesto de este contrato, por tanto, tendrá un costo adicional que se debe cancelar de manera anticipada.</textarea>

              <div class="bold mb-1">NOVENA. - TÉRMINOS Y CONDICIONES GENERALES Y ESPECÍFICOS DE PRESTACIÓN DE LOS SERVICIOS.</div>
              <textarea name="clausula_9" class="editable-block mb-3">9.1 EL PROVEEDOR, de manera independiente, sin subordinación o dependencia, utilizando sus propios medios, elementos de trabajo, personal a su cargo, prestará los servicios de Continuidad, Fortalecimiento e Implementación del Sistema de Gestión de la Seguridad y Salud en el Trabajo, SG SST.
9.2. EI PROVEEDOR responderá con la calidad del trabajo de manera oportuna, eficiente y con la celeridad pertinente, desarrollada con la diligencia exigible a una empresa experta en la realización de los trabajos objeto del Contrato.
9.3. EI PROVEEDOR se obliga a gestionar y obtener, a su cargo, todas las licencias, permisos y autorizaciones administrativas que pudieren ser necesarias para la realización de los Servicios.
9.4. EI PROVEEDOR guardará confidencialidad sobre la información que le facilite el CLIENTE en o para la ejecución del Contrato o que por su propia naturaleza deba ser tratada como tal. Se excluye de la categoría de información confidencial toda aquella información que sea divulgada por el CLIENTE, aquella que haya de ser revelada de acuerdo con las leyes o con una resolución judicial o acto de autoridad competente. Este deber se mantendrá durante un plazo de un año a contar desde la finalización del servicio.
9.5. EI CLIENTE prestará la documentación requerida al PROVEEDOR y deberá estar disponible previa cita o comunicado para ser recogida, b) Debe existir disposición, es decir otorgar al Equipo Consultor un espacio de tiempo y lugar para las diferentes actividades que se necesiten sin que esto incurra en una pérdida importante de tiempo y de trabajo para el CLIENTE y(o) sus colaboradores.
9.6. EL CLIENTE Contribuirá para que las sugerencias solicitadas sean llevadas a cabo en el menor tiempo posible y o de acuerdo a las posibilidades del mismo.
9.7. EL CLIENTE designará a un(os) funcionario(s) de su organización para cumplir con el rol de LIDER DEL SGSST, para hacer de enlace con el PROVEEDOR. Ellos colaborarán con el PROVEEDOR y su equipo de trabajo para llevar a buen fin todas las obligaciones derivadas de este contrato.
9.8. EL CLIENTE es responsable de reportar de manera oportuna y en la menor brevedad posible todas las novedades que afecten o impacten el SG-SST, tales como ingreso de personal nuevo, exámenes ocupacionales (ingreso, retiro, post-incapacidad, etc.), casos de salud, modificaciones locativas, cambios de cargo, requerimientos de ARL o Ministerio de Trabajo, accidentes e incidentes, enfermedades laborales.
9.9. EL CLIENTE podrá recibir asesoría, informes de gestión, documentación, formatos y en general todo lo que no exija soporte físico inmediato, a través de medios digitales y/o correos electrónicos, y la comunicación se podrá realizar por líneas telefónicas y celulares, de manera virtual entre el CLIENTE y el PROVEEDOR o su equipo de trabajo.
9.10. EL PROVEEDOR se compromete a brindar acompañamiento permanente, prestar asesoría y asistencia técnica, establecer mecanismos de coordinación entre la ARL y la empresa y a realizar la vigilancia delegada del cumplimiento del SG SST de acuerdo a la normatividad vigente (Decreto 1072 de 2015 y Resolución 0312 de 2019).
9.11. EL PROVEEDOR. Realizara el acompañamiento en caso de una visita para revisión del Sistema de Gestión de Seguridad y Salud en el trabajo por parte de entidades tales como Ministerio de trabajo y ARL.</textarea>

              <div class="bold mb-1">DIEZ. - PRECIO Y FACTURACIÓN.</div>
              <textarea name="clausula_10" class="editable-block mb-3">10.1 ALCANCE DEL CONTRATO: El Seguimiento, Control y Ejecución del SG-SST Y Recursos Humanos de la empresa, tendrá una cobertura de todos los trabajadores directos, prestadores de servicio y temporales que laboran en las oficina, Planta, Bodega y puntos de venta.
10.2 DESARROLLO DE LAS ACTIVIDAES:
10.2.1 Dos días a la Semana presencial cada uno de cinco (5) horas.
10.2.2 Seguimiento en Oficina, Planta, bodega y puntos de venta, de acuerdo con el cronograma de trabajo.
10.2.3 Acompañamiento virtual siempre que la empresa lo requiera (La asesoría virtual o por llamadas no tiene límite de tiempo). Solicitud y elaboración de documentos, procesos, envió de información permanente.
10.3 VALOR: El valor del presente contrato se cancelará mes vencido y corresponde a la suma de TRES MILLONES DE PESOS M/CTE ($ 3.000.000) NO INCLUIDO IVA.
10.4 PAGOS: se deben efectuar los cinco primeros días de cada mes (5) de cada mes después de la fecha de vencimiento de la factura, será cobrado un recargo del 1% sobre el valor de la factura. El pago deberá realizarse a través de consignación o transferencia electrónica a la cuenta de SOLUCIONES Y CONSULTORIAS EMPRESARIALES SAS. Banco Caja Social, Cuenta de Ahorros No. 24093643436.
10.5 EL CLIENTE deberá cancelar la factura en las fechas indicadas, independiente del recibo de la misma, debido a que los valores de las facturas son iguales mes a mes.
10.6 Los trabajos realizados fuera de este contrato se facturarán de manera independiente y se pagarán conforme a la cláusula anterior.</textarea>

              <div class="bold mb-1">ONCE. - DURACIÓN Y POLITICAS DEL CONTRATO.</div>
              <textarea name="clausula_11" class="editable-block mb-3">11.1 El presente contrato tendrá duración inicial de (12) meses, contados a partir de la fecha de firma del presente contrato.
11.2 El presente contrato es firmado y aceptado de forma libre y voluntaria de acuerdo a la necesidad de la empresa, de continuar y fortalecer un sistema de Gestión de Seguridad y Salud en el trabajo que corresponde al marco legal colombiano.
11.3 EL CLIENTE y/o el PROVEEDOR pueden hacer uso de medios escritos manifestando inconformidad u omisión de alguno de los ítems del plan de trabajo descrito en este contrato pactado.
11.4 EL CLIENTE y/o el PROVEEDOR pueden hacer cancelación del contrato en cualquier momento sin que esto de lugar a Sanciones por ninguna de las partes justificando el no cumplimiento de las obligaciones contractuales con mínimo treinta (30) días de anticipación por medio escrito.
11.5 Si al término de este contrato existe entre ambas partes la intención de continuar será prorrogado automáticamente, pero si por el contrario finaliza, se debe presentar con mínimo treinta (30) días calendario de anticipación, una carta donde especifique la terminación definitiva del mismo.
11.6 EL PROVEEDOR para prestar los servicios contratados debe mantener vigente su licencia en seguridad y salud en el trabajo o en su defecto renovada, de lo contrario sería una causal de terminación del contrato por parte del CLIENTE.</textarea>

              <div class="bold mb-1">DOCE. - REGIMEN JURÍDICO.</div>
              <textarea name="clausula_12" class="editable-block mb-3">El presente contrato tiene carácter mercantil, no existiendo en ningún caso vínculo laboral alguno entre el CLIENTE y el personal del PROVEEDOR que preste concretamente los Servicios. Ambas partes se someten, para cualquier diferencia que pudiere surgir de la interpretación y cumplimiento del presente contrato, a la jurisdicción arbitral de la cámara de comercio de la ciudad de Cali (V).</textarea>

              <div class="mt-4">
                <textarea name="texto_cierre" class="editable-block">Y en prueba de cuanto antecede, las Partes suscriben el Contrato, en dos ejemplares y a un solo efecto, en el lugar y fecha señalados en el encabezamiento.</textarea>
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="2" class="center bold" style="padding:50px 18px 20px 18px; border-right: none;">
              <?php if(!empty($cli_firma)): ?>
                <img src="<?= htmlspecialchars($cli_firma) ?>" style="max-height: 60px; display:block; margin: 0 auto;">
              <?php endif; ?>
              <div class="signature-line" style="<?= !empty($cli_firma) ? 'margin-top:5px;' : '' ?>"></div>
              <input name="firma_cliente_nombre" class="sst-input center bold mt-2" type="text" value="<?= htmlspecialchars($cli_representante) ?>">
              <input name="firma_cliente_documento" class="sst-input center" type="text" value="<?= htmlspecialchars($cli_cc) ?>">
              <div class="small mt-1">POR EL CLIENTE</div>
            </td>
            <td colspan="3" class="center bold" style="padding:50px 18px 20px 18px; border-left: none;">
              <div class="signature-line"></div>
              <input name="firma_proveedor_nombre" class="sst-input center bold mt-2" type="text" value="">
              <input name="firma_proveedor_documento" class="sst-input center" type="text" value="">
              <div class="small mt-1">POR EL PROVEEDOR</div>
            </td>
          </tr>

        </table>
      </div>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function autoResize(el) {
      el.style.height = 'auto';
      el.style.height = el.scrollHeight + 'px';
    }

    document.addEventListener('DOMContentLoaded', function () {
      const textareas = document.querySelectorAll('.sst-textarea, .editable-block, .editable-list');
      textareas.forEach(function (el) {
        autoResize(el);
        el.addEventListener('input', function () { autoResize(el); });
      });

      // INYECCIÓN DE DATOS DESDE PHP (Sobreescribe los campos si ya había un guardado previo)
      let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
      
      if (typeof datosGuardados === 'string') {
          try { datosGuardados = JSON.parse(datosGuardados); } 
          catch(e) { console.error("No se pudo parsear el JSON de datosGuardados"); }
      }
      
      if (datosGuardados && Object.keys(datosGuardados).length > 0) {
          for (const [key, value] of Object.entries(datosGuardados)) {
              const campo = document.querySelector(`[name="${key}"]`);
              if (campo) {
                  let textoLimpio = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                  campo.value = textoLimpio;
                  if(campo.tagName === 'TEXTAREA') autoResize(campo);
              }
          }
      }
    });

    // LÓGICA DE GUARDADO
    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
        const formData = new FormData(form);
        const datosJSON = Object.fromEntries(formData.entries());

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
        btn.disabled = true;

        try {
            const token = "<?= $token ?>";
            const urlAPI = "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar";

            const response = await fetch(urlAPI, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    id_empresa: <?= $empresa ?>, // Toma el ID que llegó por URL
                    id_item_sst: <?= $idItem ?>,
                    datos: datosJSON
                })
            });

            const result = await response.json();

            if (result.ok) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Contrato guardado correctamente para esta empresa',
                    icon: 'success',
                    confirmButtonColor: '#1fa339'
                });
            } else {
                Swal.fire({
                    title: 'Error al guardar',
                    text: result.error || "No se pudo completar la operación.",
                    icon: 'error',
                    confirmButtonColor: '#004176'
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo contactar al servidor para guardar el formulario.',
                icon: 'error',
                confirmButtonColor: '#004176'
            });
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
  </script>
</body>
</html>