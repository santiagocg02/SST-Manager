<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN COMO EN EL MENÚ
// Ajusta esta ruta dependiendo de la ubicación de este archivo
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../../index.php");
  exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 1; // Ajustado a 1 según tus pruebas

// 2. SOLICITAMOS LOS DATOS DEL FORMULARIO A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);

$datosCampos = [];
$camposCrudos = null;

// Buscamos la ruta correcta del JSON según lo devuelva la API
if (isset($resFormulario['data']['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['data']['campos'];
} elseif (isset($resFormulario['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['campos'];
} elseif (isset($resFormulario['campos'])) {
    $camposCrudos = $resFormulario['campos'];
}

// Convertimos a array si viene como texto
if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true);
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
} else {
    $errorCarga = "No se detectaron campos válidos. Respuesta: " . json_encode($resFormulario);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AN-SST-33 | Perfil Representante Alta Dirección</title>

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
    .sst-table td, .sst-table th{ border:1px solid var(--sst-border); padding:6px 8px; vertical-align:top; font-size:12px; word-wrap:break-word; height:auto; }
    .sst-title{ background:var(--sst-primary); text-align:center; font-weight:800; text-transform:uppercase; }
    .sst-subtitle{ background:var(--sst-primary-soft); text-align:center; font-weight:800; text-transform:uppercase; }
    .center{ text-align:center; }
    .bold{ font-weight:800; }
    .small{ font-size:12px; }
    .sst-input, .sst-select{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 4px; font-family:Arial, Helvetica, sans-serif; color:#111; }
    .sst-textarea, .editable-block, .editable-list{ width:100%; border:none; outline:none; background:transparent; font-size:12px; line-height:1.45; padding:0; resize:none; overflow:hidden; height:auto; min-height:unset; font-family:Arial, Helvetica, sans-serif; color:#111; display:block; }
    .sst-input-line{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 0; border-bottom:1px solid #666; font-family:Arial, Helvetica, sans-serif; color:#111; }
    .logo-box{ height:72px; display:flex; align-items:center; justify-content:center; flex-direction:column; font-weight:800; color:#808080; border:2px dashed #b5b5b5; text-align:center; line-height:1.2; }
    .header-main{ text-align:center; font-weight:800; font-size:14px; line-height:1.4; text-transform:uppercase; }
    .meta-box{ display:flex; flex-direction:column; gap:8px; font-size:12px; height:100%; justify-content:center; }
    .meta-box .meta-item{ text-align:right; font-weight:800; }
    .signature-space{ height:24px; }
    .signature-line{ border-top:1px solid #000; width:60%; margin:0 auto; }
    a.video-link{ color:#0d6efd; word-break:break-all; text-decoration:none; }
    a.video-link:hover{ text-decoration:underline; }
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
      <h1 class="sst-toolbar-title">Perfil Representante de la Alta Dirección para el SG-SST</h1>

      <div class="sst-toolbar-actions">
        <a href="../planear.php" class="btn btn-secondary btn-sm">Volver</a>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar">
            <i class="fa-solid fa-save"></i> Guardar
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
            <td style="width:18%;">
              <div class="logo-box">
                <div>TU LOGO</div><div>AQUÍ</div>
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
                  <input name="fecha_documento" class="sst-input-line" type="text" value="XX/XX/2025">
                </div>
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="5" class="small">
              <span class="bold">Ver Video:</span>
              <a class="video-link" href="https://www.youtube.com/watch?v=rxf47zQ2FqM&t=4s" target="_blank">https://www.youtube.com/watch?v=rxf47zQ2FqM&t=4s</a>
            </td>
          </tr>

          <tr><td colspan="5" class="sst-title">I. Identificación del Cargo</td></tr>

          <tr class="sst-subtitle">
            <td colspan="2">Nombre del cargo</td>
            <td>Cargo jefe inmediato</td>
            <td>Área</td>
            <td>Tiene personal a cargo</td>
          </tr>

          <tr>
            <td colspan="2"><textarea name="cargo" class="sst-textarea">REPRESENTANTE DE LA ALTA DIRECCIÓN PARA EL SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</textarea></td>
            <td><textarea name="jefe_inmediato" class="sst-textarea">GERENTE</textarea></td>
            <td><textarea name="area" class="sst-textarea">SEGURIDAD Y SALUD EN EL TRABAJO</textarea></td>
            <td class="center">
              <select name="personal_cargo" class="sst-select">
                <option selected>SI</option>
                <option>NO</option>
              </select>
            </td>
          </tr>

          <tr>
            <td colspan="5" class="center bold">
              Actualización <input name="horas_actualizacion" class="sst-input-line" style="display:inline-block; width:120px; text-align:center;" type="text" value="20 horas">
            </td>
          </tr>

          <tr class="sst-subtitle">
            <td>Educación</td>
            <td>Formación / conocimientos</td>
            <td>Experiencia</td>
            <td colspan="2">Entrenamiento</td>
          </tr>

          <tr>
            <td><textarea name="educacion" class="sst-textarea">PROFESIONAL / TÉCNICO / TECNÓLOGO CON LICENCIA EN SST VIGENTE</textarea></td>
            <td><textarea name="formacion" class="sst-textarea">50 horas curso básico en SG-SST (Decreto 1072)</textarea></td>
            <td><textarea name="experiencia" class="sst-textarea">2 años en diseño e implementación de sistemas de gestión de SST</textarea></td>
            <td colspan="2"><textarea name="entrenamiento" class="sst-textarea">COORDINADOR DE TRABAJO EN ALTURAS&#10;BRIGADISTA&#10;CURSO VIRTUAL DE 50 HORAS</textarea></td>
          </tr>

          <tr>
            <td colspan="2" class="sst-title">III. Habilidades</td>
            <td colspan="3" class="sst-title">IV. Comportamientos Asociados</td>
          </tr>

          <tr>
            <td colspan="2"><textarea name="hab_liderazgo" class="editable-block">Liderazgo: Habilidad de poder liderar personal o situaciones.</textarea></td>
            <td colspan="3"><textarea name="comp_liderazgo" class="editable-block">a) Representa al equipo positivamente frente a las organizaciones internas y externas.&#10;b) Resuelve problemas de relaciones interpersonales que afectan el desempeño de su área o de la organización.</textarea></td>
          </tr>
          <tr>
            <td colspan="2"><textarea name="hab_proactividad" class="editable-block">Proactividad: Habilidad para tener la capacidad de auto motivación para lograr los objetivos establecidos en su trabajo.</textarea></td>
            <td colspan="3"><textarea name="comp_proactividad" class="editable-block">a) Muestra una visión optimista de la vida y asume los retos con entusiasmo.&#10;b) Aprende de buena manera lo que otros quieren enseñarle.&#10;c) Mantiene actitud positiva frente a situaciones frustrantes.</textarea></td>
          </tr>
          <tr>
            <td colspan="2"><textarea name="hab_decisiones" class="editable-block">Toma de decisiones: Habilidad de saber elegir entre varias alternativas de manera efectiva y rápida.</textarea></td>
            <td colspan="3"><textarea name="comp_decisiones" class="editable-block">a) Asume la responsabilidad de sus acciones, sean exitosas o no.&#10;b) Toma decisiones de bajo riesgo sin consultar al jefe.&#10;c) Resuelve problemas y da respuesta a situations que surgen, tomando decisiones con agilidad y rapidez.</textarea></td>
          </tr>
          <tr>
            <td colspan="2"><textarea name="hab_calidad" class="editable-block">Preocupación por el orden y la calidad: Alto compromiso por desarrollar actividades ordenadas, con precisión y siguiendo estándares.</textarea></td>
            <td colspan="3"><textarea name="comp_calidad" class="editable-block">a) Aplica proceso de calidad que le corresponden a su cargo.&#10;b) Demuestra un alto nivel de compromiso por realizar sus actividades de manera ordenada.&#10;c) Busca optimizar el rendimiento de las herramientas que se le proporciona.</textarea></td>
          </tr>
          <tr>
            <td colspan="2"><textarea name="hab_compromiso" class="editable-block">Compromiso organizacional: Realiza tareas oportunamente, se adapta al cambio, cumple normas y reglamentos.</textarea></td>
            <td colspan="3"><textarea name="comp_compromiso" class="editable-block">a) Entregar trabajos en el tiempo asignado y con la calidad requerida.&#10;b) Administrar de manera adecuada el tiempo para cumplir objetivos y tareas propuestas.</textarea></td>
          </tr>

          <tr><td colspan="5" class="sst-title">IV. Objeto del Cargo</td></tr>
          <tr><td colspan="5"><textarea name="objeto_cargo" class="sst-textarea">Se encargará de toda la gestión de mejora continua del Sistema de Gestión de la Seguridad y Salud en el Trabajo.</textarea></td></tr>

          <tr><td colspan="5" class="sst-title">V. Funciones y Responsabilidades</td></tr>
          <tr>
            <td colspan="5">
              <input name="titulo_funciones" class="sst-input bold mb-2" type="text" value="1. Diseñar y mantener el sistema de gestión de seguridad y salud en el trabajo (SG-SST)">
              <div class="bold small mb-1">Responsabilidades</div>
              <textarea name="funciones_lista" class="editable-list">• Planear, organizar, dirigir, desarrollar y aplicar el SG-SST; como mínimo una vez al año realizar su evaluación.
• Asegurar que los requisitos del SG-SST se establezcan, implementen y mantengan (Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normas asociadas).
• Verificar el cumplimiento y desempeño del SG-SST.
• Informar a la alta gerencia sobre el funcionamiento y los resultados del SG-SST.
• Promover la participación de todos los miembros de la empresa en la implementación del SG-SST.
• Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del SG-SST.
• Programar auditorías internas necesarias para el mantenimiento del SG-SST.</textarea>
            </td>
          </tr>

          <tr><td colspan="5" class="sst-title">VI. Autoridad</td></tr>
          <tr><td colspan="5"><textarea name="autoridad" class="sst-textarea">Tiene autoridad para representar a la alta dirección en todos los temas del SG-SST.</textarea></td></tr>

          <tr><td colspan="5" class="sst-title">VII. Responsabilidades en SST</td></tr>
          <tr><td colspan="5"><textarea name="responsabilidad_sst" class="sst-textarea">Promover el cumplimiento de los requisitos legales en SST.</textarea></td></tr>

          <tr>
            <td colspan="5" class="center bold" style="padding:18px;">
              <input class="sst-input center bold" type="text" value="FIRMA RECIBIDO Y ENTERADO" readonly>
              <div class="signature-space"></div>
              <div class="signature-line"></div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 1. Auto Ajuste de Textareas
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

      // 2. INYECCIÓN DE DATOS DESDE PHP
      let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
      
      // Si llega como string desde la BD, lo convertimos
      if (typeof datosGuardados === 'string') {
          try { datosGuardados = JSON.parse(datosGuardados); } 
          catch(e) { console.error("No se pudo parsear el JSON de datosGuardados"); }
      }
      
      // DEPURACIÓN PARA VER EN LA CONSOLA (F12)
      console.log("Datos cargados del Backend:", datosGuardados);
      <?php if(isset($errorCarga)) echo "console.warn('Advertencia API:', " . json_encode($errorCarga) . ");"; ?>
      
      if (datosGuardados && Object.keys(datosGuardados).length > 0) {
          // Recorremos el JSON y asignamos el valor a cada input/textarea según su 'name'
          for (const [key, value] of Object.entries(datosGuardados)) {
              const campo = document.querySelector(`[name="${key}"]`);
              if (campo) {
                  // Reemplazamos los saltos de línea codificados por saltos reales para textareas
                  let textoLimpio = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                  campo.value = textoLimpio;
                  if(campo.tagName === 'TEXTAREA') autoResize(campo);
              }
          }
      }
    });

    // 3. LÓGICA DE GUARDADO MEDIANTE FETCH CON SWEETALERT2
    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
        const formData = new FormData(form);
        const datosJSON = Object.fromEntries(formData.entries());

        // Cambiamos estado del botón
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
                    id_empresa: <?= $empresa ?>,
                    id_item_sst: <?= $idItem ?>,
                    datos: datosJSON
                })
            });

            const result = await response.json();

            if (result.ok) {
                // Alerta de éxito con los colores corporativos
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Configuración guardada correctamente para esta empresa',
                    icon: 'success',
                    confirmButtonColor: '#1fa339'
                });
            } else {
                // Alerta de error de la API
                Swal.fire({
                    title: 'Error al guardar',
                    text: result.error || "No se pudo completar la operación.",
                    icon: 'error',
                    confirmButtonColor: '#004176'
                });
            }
        } catch (error) {
            console.error(error);
            // Alerta de error de conexión de red
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