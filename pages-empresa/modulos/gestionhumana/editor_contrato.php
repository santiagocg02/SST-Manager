<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
// Ajusta esta ruta dependiendo de la ubicación de este archivo
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../../index.php");
  exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 1; 

// 2. SOLICITAMOS LOS DATOS DEL FORMULARIO A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);

// DATOS DE LA EMPRESA (LOGO)
$resEmpresaInfo = $api->solicitar("empresas/$empresa", "GET", null, $token);
$logoEmpresaUrl = $resEmpresaInfo['data']['logo_url'] ?? '';

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
  <title>F-JUR-001 | Contrato a Término Indefinido</title>

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
    .sst-textarea, .editable-block, .editable-list{ width:100%; border:none; outline:none; background:transparent; font-size:12px; line-height:1.45; padding:0; resize:none; overflow:hidden; height:auto; min-height:unset; font-family:Arial, Helvetica, sans-serif; color:#111; display:block; text-align: justify; }
    .sst-input-line{ width:100%; border:none; outline:none; background:transparent; font-size:12px; padding:2px 0; border-bottom:1px solid #666; font-family:Arial, Helvetica, sans-serif; color:#111; }
    .logo-box{ height:72px; display:flex; align-items:center; justify-content:center; flex-direction:column; font-weight:800; color:#808080; border:2px dashed #b5b5b5; text-align:center; line-height:1.2; }
    .header-main{ text-align:center; font-weight:800; font-size:14px; line-height:1.4; text-transform:uppercase; }
    .meta-box{ display:flex; flex-direction:column; gap:8px; font-size:12px; height:100%; justify-content:center; }
    .meta-box .meta-item{ text-align:right; font-weight:800; }
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
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <form id="form-sst-dinamico">
    <div class="sst-toolbar">
      <h1 class="sst-toolbar-title">Contrato de Trabajo a Término Indefinido</h1>

      <div class="sst-toolbar-actions">
        <a href="../../../pages-empresa/modulos/gestionhumana/Crearvinculacion.php" class="btn btn-secondary btn-sm">Volver</a>
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
            <td style="width:20%;">
              <div class="logo-box" style="border: none;">
                <?php if(!empty($logoEmpresaUrl)): ?>
                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 72px; object-fit: contain;">
                <?php else: ?>
                    <div style="width:100%; border:2px dashed #b5b5b5; padding: 10px; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                        <div>TU LOGO</div><div>AQUÍ</div>
                    </div>
                <?php endif; ?>
              </div>
            </td>
            <td colspan="3">
              <div class="header-main">
                CONTRATO DE TRABAJO INDEFINIDO<br>
              </div>
            </td>
            <td style="width:20%;">
              <div class="meta-box">
                <div class="meta-item">F-JUR-001-2025</div>
                <div class="meta-item">
                  Fecha: <input name="fecha_documento" class="sst-input-line" style="width: 70px; display: inline-block;" type="text" value="xxxxx">
                </div>
              </div>
            </td>
          </tr>

          <tr><td colspan="5" class="sst-title">DATOS DE LAS PARTES Y CONDICIONES</td></tr>
          
          <tr>
            <td colspan="1" class="bold">EMPLEADOR:</td>
            <td colspan="4"><input name="empleador_nombre" class="sst-input" value="xxxxx"></td>
          </tr>
          <tr>
            <td colspan="1" class="bold">TRABAJADOR:</td>
            <td colspan="4"><input name="trabajador_nombre" class="sst-input" value="xxxxx"></td>
          </tr>
          
          <tr>
            <td class="bold">CORREO:</td>
            <td colspan="2"><input name="trabajador_correo" class="sst-input" value="xxxxx"></td>
            <td class="bold">TELÉFONO:</td>
            <td><input name="trabajador_telefono" class="sst-input" value="xxxxx"></td>
          </tr>
          
          <tr>
            <td class="bold">CARGO:</td>
            <td colspan="2"><input name="cargo_contrato" class="sst-input" value="xxxxx"></td>
            <td class="bold">SALARIO:</td>
            <td><input name="salario_contrato" class="sst-input" value="xxxxx"></td>
          </tr>

          <tr>
            <td class="bold">PERIODOS DE PAGO:</td>
            <td><input name="periodo_pago" class="sst-input" value="xxxxx"></td>
            <td class="bold">AUX. TRANSPORTE:</td>
            <td colspan="2"><input name="aux_transporte" class="sst-input" value="xxxxx"></td>
          </tr>

          <tr>
            <td class="bold">JORNADA:</td>
            <td><input name="jornada" class="sst-input" value="xxxxx"></td>
            <td class="bold">AUX. NO PRESTACIONAL:</td>
            <td colspan="2"><input name="aux_no_prestacional" class="sst-input" value="xxxxx"></td>
          </tr>

          <tr>
            <td class="bold" colspan="2">FECHA DE INICIO:</td>
            <td><input name="fecha_inicio" class="sst-input" value="xxxxx"></td>
            <td class="bold">LUGAR:</td>
            <td><input name="lugar_trabajo" class="sst-input" value="xxxxx"></td>
          </tr>

          <tr><td colspan="5" class="sst-title">CLÁUSULAS DEL CONTRATO</td></tr>
          
          <tr>
            <td colspan="5" style="padding: 15px;">
              <textarea name="clausula_intro" class="editable-block mb-3">Entre EL EMPLEADOR, y EL TRABAJADOR, identificados como aparece al pie de sus firmas, se ha convenido celebrar el presente contrato de trabajo regido, además de las disposiciones legales, por las siguientes cláusulas.</textarea>

              <div class="bold mb-1">PRIMERA: OBLIGACIONES DEL TRABAJADOR.</div>
              <textarea name="clausula_1" class="editable-block mb-3">A partir de la fecha de iniciación de labores EL TRABAJADOR ingresa al servicio del EMPLEADOR, comprometiéndose:
a) A poner al servicio del EMPLEADOR toda su capacidad de trabajo en el desempeño de las funciones propias del oficio contratado y en las anexas y complementarias, de conformidad con las ordenes e instrucciones que le imparta el EMPLEADOR o por las personas en quien este delegue.
b) A cumplir sus funciones de trabajo de manera cuidadosa y diligente en el lugar, tiempo y condiciones que EL EMPLEADOR le señale y de acuerdo con los horarios que se le fijen conforme a las necesidades del servicio.
c) A observar rigurosamente la disciplina interna establecida por EL EMPLEADOR o por las personas autorizadas por este.
d) A guardar estricta reserva de todo lo que llegue a su conocimiento por razón de oficio y cuya divulgación pudiera causar perjuicio AL EMPLEADOR o a las personas o entidades en cuyos establecimientos trabaje.
e) A no atender durante las horas de trabajo asuntos u ocupaciones distintas a los que EL EMPLEADOR o las personas autorizadas por este le encomienden.
f) A cuidar y manejar con esmero y atención las herramientas de trabajo asignados.
g) A acatar como TRABAJADOR, las funciones y responsabilidades del perfil del cargo, los reglamentos de la empresa, tales como el Reglamento Interno de Trabajo, El Código de Ética, Sistema de Seguridad y Salud en el Trabajo, Políticas y Sistemas que adopte como EMPLEADOR.
h) A Cumplir lo dispuesto en la Cláusula Decima, sobre INVENCIONES, PROPIEDAD INTELECTUAL DISEÑOS Y DESARROLLO DE PRODUCTOS Y SERVICIOS.
i) A aceptar los traslados de lugar de trabajo que disponga EL EMPLEADOR.
j) A suministrar la información clara, veraz y completa sobre su estado de salud mediante el diligenciamiento de los formatos que disponga su empleador.
k) A cumplir con las obligaciones legales y reglamentarias establecidas en la ley.</textarea>

              <div class="bold mb-1">SEGUNDA: DURACIÓN DEL CONTRATO.</div>
              <textarea name="clausula_2" class="editable-block mb-3">El término del presente contrato será indefinido.</textarea>

              <div class="bold mb-1">TERCERA: SALARIO.</div>
              <textarea name="clausula_3" class="editable-block mb-3">EL EMPLEADOR pagará al TRABAJADOR por la prestación de sus servicios el salario indicado, pagadero en las oportunidades que su empleador le indique. Dentro de este pago se encuentra incluida la remuneración de los descansos remuneratorios y festivos de que tratan los Capítulos I, II y III del Título VII del Código Sustantivo del Trabajo. En cumplimiento de lo previsto en el artículo 127 del C.S.T. modificado por el artículo 14 de la Ley 50 de 1990. El trabajador tendrá derecho al pago de vacaciones y prima de servicios en los términos del Contrato de Trabajo, conforme lo disponen los artículos 306 y 189 del CST. Los pagos que el EMPLEADOR haga al TRABAJADOR por conceptos como comisiones, bonificaciones, incentivos, auxilios habituales u ocasionales acordados contractualmente u otorgados en forma extralegal no constituyen salario, conforme a la Ley 50 de 1990 artículo 15.</textarea>

              <div class="bold mb-1">CUARTA: TRABAJO EXTRAORDINARIO.</div>
              <textarea name="clausula_4" class="editable-block mb-3">Todo trabajo suplementario superior a la jornada laboral dispuesta en la ley 2101 de 2021, en día de descanso obligatorio o festivo, en los que legalmente debe concederse descanso. EL EMPLEADOR en consecuencia no reconocerá ningún trabajo suplementario o en días de descanso legalmente obligatorio que no haya sido autorizado por el jefe Inmediato para lo cual EL EMPLEADOR llevará registro de las mismas.</textarea>

              <div class="bold mb-1">QUINTA: JORNADA ORDINARIA.</div>
              <textarea name="clausula_5" class="editable-block mb-3">Será de tiempo completo y/o la máxima legal permitida de (44) horas a la semana, que podrán ser distribuidas, de común acuerdo, entre empleador y trabajador, en 5 o 6 días a la semana, garantizando siempre el día de descanso. Asimismo, las partes podrán acordar que la jornada semanal se realice mediante jornadas diarias flexibles de trabajo, distribuidas en máximo seis días a la semana con un día de descanso obligatorio, que podrá coincidir con el día domingo. Para efectos del cumplimiento de la labor arriba señalada, las partes acuerdan como jornada ordinaria la dispuesta en el Reglamento Interno de Trabajo.</textarea>

              <div class="bold mb-1">SEXTA: PERIODO DE PRUEBA.</div>
              <textarea name="clausula_6" class="editable-block mb-3">El término máximo de duración del periodo de prueba es de dos (2) meses, en los términos establecidos en el artículo 78 del C.S.T., subrogado por el artículo 7 de la L50/90. Durante este período tanto EL EMPLEADOR como EL TRABAJADOR podrán terminar este contrato, en cualquier momento, en forma unilateral.</textarea>

              <div class="bold mb-1">SÉPTIMA: TERMINACIÓN DE CONTRATO.</div>
              <textarea name="clausula_7" class="editable-block mb-3">Son justas causas para poner término a este contrato unilateralmente, las enumeradas en el artículo 7o. del Decreto 2351 de 1.965 y además, por parte de EL EMPLEADOR, las siguientes faltas que para el efecto se califican como graves:
a) Violación por parte del TRABAJADOR de cualquiera de sus obligaciones legales, contractuales o reglamentarias de conformidad con lo dispuesto por el Reglamento Interno de Trabajo, El Contrato de Trabajo y el Código de Ética.
b) Las demás previstas como justas causas para dar por terminado el Contrato en el Código Sustantivo de Trabajo, y en la Ley.</textarea>

              <div class="bold mb-1">OCTAVA: PAGO QUE NO CONSTITUYE SALARIO.</div>
              <textarea name="clausula_8" class="editable-block mb-3">Las partes acuerdan que cualquier auxilio o beneficio en dinero o en especie de carácter extralegal que EL EMPLEADOR reconozca o pague al TRABAJADOR no constituye salario para ningún efecto, cuando se conceda para la realización de las labores de su cargo, no para su ingreso personal.</textarea>

              <div class="bold mb-1">NOVENA: AUTORIZACIÓN DE DEDUCCIÓN.</div>
              <textarea name="clausula_9" class="editable-block mb-3">EL TRABAJADOR autoriza AL EMPLEADOR para deducir de los salarios, prestaciones sociales, vacaciones o indemnizaciones causadas a su favor, el valor que se cause por la pérdida o daño de vehículo, maquinaria, herramientas y elementos que sean facilitados por EL EMPLEADOR, los valores que surjan por faltantes de dinero o prestamos realizados al TRABAJADOR, los dineros que se deban descontar por orden judicial o acuerdos por cuota alimentaria.</textarea>

              <div class="bold mb-1">DÉCIMA: INVENCIONES, PROPIEDAD INTELECTUAL.</div>
              <textarea name="clausula_10" class="editable-block mb-3">Las invenciones o descubrimientos realizados por el TRABAJADOR, mientras preste sus servicios al EMPLEADOR, pertenecerán al EMPLEADOR, de conformidad con lo dispuesto en el artículo 8 de la Decisión 85 del Acuerdo de Cartagena. En consecuencia, tendrá el EMPLEADOR el derecho de hacer patentar a su nombre o a nombre de terceros esos inventos o mejoras.</textarea>

              <div class="bold mb-1">DÉCIMA PRIMERA: LUGAR DE PRESTACIÓN DEL SERVICIO Y CAMBIO DE OFICIO.</div>
              <textarea name="clausula_11" class="editable-block mb-3">Las partes podrán convenir que el trabajo se preste en lugar distinto del inicialmente contratado, según las necesidades del servicio, siempre que tales traslados no desmejoren las condiciones laborales o de remuneración del TRABAJADOR. EL TRABAJADOR se obliga a aceptar los cambios de oficio que decida el EMPLEADOR dentro de su poder subordinante.</textarea>

              <div class="bold mb-1">DÉCIMA SEGUNDA: CODIGO DE ETICA.</div>
              <textarea name="clausula_12" class="editable-block mb-3">Yo como trabajador, declaro que he revisado, entendido y aceptado el contenido del Código de Ética como parte integral del contrato de trabajo y me comprometo a cumplir con las normas de mi comportamiento ético y equitativo aquí establecidas.</textarea>

              <div class="bold mb-1">DÉCIMA TERCERA: PROTECCION DE DATOS PERSONALES.</div>
              <textarea name="clausula_13" class="editable-block mb-3">Las partes se obligan a realizar el tratamiento de los datos personales y/o las bases de datos personales a los que puedan tener acceso en virtud de las obligaciones del contrato, conforme con la normatividad vigente en Colombia sobre la Protección de Datos Personales, Ley Estatutaria 1581 de 2012 y Decreto 1377 de 2013.</textarea>

              <div class="bold mb-1">DÉCIMA CUARTA: AUTORIZACIÓN DE NOTIFICACION.</div>
              <textarea name="clausula_14" class="editable-block mb-3">EL TRABAJADOR autoriza AL EMPLEADOR para ser notificado de cualquier información o requerimiento contractual o disciplinario durante los días y/o horas hábiles a su residencia, correo electrónico, WhatsApp o plataforma de mensajería instantánea.</textarea>

              <div class="bold mb-1">DÉCIMA QUINTA: VALIDEZ.</div>
              <textarea name="clausula_15" class="editable-block mb-3">Este Contrato ha sido redactado estrictamente de acuerdo a la ley y a la jurisprudencia y será interpretado de buena fe y en consonancia con el CST. En consecuencias las partes manifiestan que reconocen validez a las estipulaciones convenidas en el presente contrato de trabajo, que es el único vigente entre ellas reemplazando y desconociendo cualquier otra verbal o escrita.</textarea>

              <div class="mt-4">
                <textarea name="texto_cierre" class="editable-block">El presente CONTRATO se firma en xxxxx a los xxxxx días del mes de xxxxx del año xxxxx.</textarea>
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="2" class="center bold" style="padding:50px 18px 20px 18px; border-right: none;">
              <div class="signature-line"></div>
              <input name="firma_empleador_nombre" class="sst-input center bold mt-2" type="text" value="xxxxx">
              <input name="firma_empleador_nit" class="sst-input center" type="text" value="xxxxx">
            </td>
            <td colspan="3" class="center bold" style="padding:50px 18px 20px 18px; border-left: none;">
              <div class="signature-line"></div>
              <input name="firma_trabajador_nombre" class="sst-input center bold mt-2" type="text" value="xxxxx">
              <input name="firma_trabajador_cc" class="sst-input center" type="text" value="xxxxx">
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

    // 3. LÓGICA DE GUARDADO MEDIANTE FETCH CON SWEETALERT2
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
                    id_empresa: <?= $empresa ?>,
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

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>