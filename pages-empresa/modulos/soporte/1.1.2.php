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
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 5; // ID del ítem anclado a esta carta (ej: 5 por AC-SST-05)

// --- Lógica de Permisos y Empresa (Optimizada) ---
$nombreEmpresaLogeada = "NOMBRE DE LA EMPRESA";
$logoEmpresaUrl = "";
$firmaRLUrl = "";

if ($empresa > 0) {
    // Solicitamos a la API exclusivamente la empresa logueada pasando el ID
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);

    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $nombreEmpresaLogeada = $empData['nombre_empresa'] ?? 'NOMBRE DE LA EMPRESA';
        
        // Obtenemos el logo y la firma del Representante Legal
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        $firmaRLUrl = $empData['firma_rl'] ?? '';
    }
}

// --- SOLICITAMOS DATOS DEL PERSONAL SST (Firma Encargado) ---
$resSST = $api->solicitar("personal_sst/empresa/$empresa", "GET", null, $token);
$profesionalSST = $resSST['data'] ?? null;
$firmaSSTUrl = $profesionalSST['firma_sst_url'] ?? '';

// 2. SOLICITAMOS LOS DATOS DEL FORMULARIO A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);

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
} else {
    $errorCarga = "No se detectaron campos válidos. Respuesta: " . json_encode($resFormulario);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AC-SST-05 | Carta de Nombramiento</title>

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

    html, body{
      margin:0; padding:0; font-family:Arial, Helvetica, sans-serif;
      background:var(--sst-bg); color:var(--sst-text);
    }

    .sst-toolbar{
      position:sticky; top:0; z-index:100; background:var(--sst-toolbar);
      border-bottom:1px solid var(--sst-toolbar-border); padding:12px 18px;
      display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;
    }

    .sst-toolbar-title{ margin:0; font-size:15px; font-weight:800; color:#213b67; }
    .sst-toolbar-actions{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .sst-page{ padding:20px; }
    
    .sst-paper{
      width:216mm; min-height:279mm; margin:0 auto; background:var(--sst-paper);
      border:1px solid #d7dee8; box-shadow:0 10px 25px rgba(0,0,0,.08);
      padding:8mm; box-sizing:border-box;
    }

    .sst-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .sst-table td, .sst-table th{
      border:1px solid var(--sst-border); padding:6px 8px; vertical-align:top;
      font-size:12px; word-wrap:break-word; height:auto;
    }

    .sst-title{ background:var(--sst-primary); text-align:center; font-weight:800; text-transform:uppercase; }
    .sst-subtitle{ background:var(--sst-primary-soft); text-align:center; font-weight:800; text-transform:uppercase; }
    .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:800; }
    .small{ font-size:12px; }
    .muted{ color:var(--sst-muted); }

    .sst-input, .sst-select{
      width:100%; border:none; outline:none; background:transparent;
      font-size:12px; padding:2px 4px; font-family:Arial, Helvetica, sans-serif; color:#111;
    }

    .sst-textarea, .editable-block, .editable-list{
      width:100%; border:none; outline:none; background:transparent;
      font-size:12px; line-height:1.5; padding:0; resize:none; overflow:hidden;
      height:auto; min-height:unset; font-family:Arial, Helvetica, sans-serif; color:#111; display:block;
    }

    .sst-input-line{
      width:100%; border:none; outline:none; background:transparent;
      font-size:12px; padding:2px 0; border-bottom:1px solid #666;
      font-family:Arial, Helvetica, sans-serif; color:#111;
    }

    .logo-box{
      height:72px; display:flex; align-items:center; justify-content:center;
      flex-direction:column; font-weight:800; color:#808080;
      border:2px dashed #b5b5b5; text-align:center; line-height:1.2;
    }

    .header-main-textarea {
      width: 100%; border: none; outline: none; background: transparent;
      text-align: center; font-weight: 800; font-size: 13px; line-height: 1.4;
      text-transform: uppercase; font-family: Arial, Helvetica, sans-serif;
      resize: none; overflow: hidden; height: 42px; color: #111;
    }

    .meta-box{
      display:flex; flex-direction:column; gap:4px; font-size:12px; height:100%; justify-content:center;
    }
    .meta-box .meta-item{ text-align:right; font-weight:800; }

    .firma-wrapper{ text-align:center; padding:18px 8px 10px; }
    .firma-line{ width:70%; margin:26px auto 6px; border-top:1px solid #000; }

    .empresa-title-input{ font-size:18px; font-weight:800; text-transform:uppercase; border:none; outline:none; background:transparent; text-align:center; width:100%; }

    /* Estilos de la lista dinámica con puntos viñeta */
    .punto-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 4px 0;
    }
    .punto-bullet {
      font-size: 14px;
      line-height: 1.5;
      color: var(--sst-text);
      user-select: none;
    }
    .btn-eliminar-punto {
      color: #dc3545;
      cursor: pointer;
      opacity: 0.4;
      transition: opacity 0.2s;
      padding-top: 2px;
    }
    .punto-item:hover .btn-eliminar-punto {
      opacity: 1;
    }

    @page{ size:Letter; margin:8mm; }
    @media print{
      html, body{ background:#fff !important; }
      .sst-toolbar{ display:none !important; }
      .sst-page{ padding:0 !important; margin:0 !important; }
      .sst-paper{
        width:100% !important; min-height:auto !important; margin:0 !important;
        border:none !important; box-shadow:none !important; padding:0 !important;
      }
      .sst-input, .sst-select, .sst-textarea, .sst-input-line, .editable-block, .editable-list, .header-main-textarea, .empresa-title-input{ color:#000 !important; }
    }
    @media (max-width: 991px){
      .sst-page{ padding:12px; }
      .sst-paper{ width:100%; min-height:auto; padding:12px; }
      .sst-toolbar{ padding:12px; }
    }
  </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

  <form id="form-sst-dinamico">
    <div class="sst-toolbar">
      <h1 class="sst-toolbar-title">Carta de Nombramiento, Representante por la Alta Dirección</h1>

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
              <div style="padding: 2px;">
                <textarea name="header_titulo_completo" class="header-main-textarea">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO&#10;CARTA DE NOMBRAMIENTO, REPRESENTANTE POR LA ALTA DIRECCIÓN</textarea>
              </div>
            </td>

            <td style="width:18%;">
              <div class="meta-box">
                <div class="meta-item">
                  <input name="documento_version" class="sst-input right" type="text" value="Versión: 0" style="font-weight:800; padding:0;">
                </div>
                <div class="meta-item">
                  <input name="documento_codigo" class="sst-input right" type="text" value="AC-SST-05" style="font-weight:800; padding:0;">
                </div>
                <div class="meta-item">
                  <input name="fecha_documento" class="sst-input-line right" type="text" value="XX/XX/2025">
                </div>
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="5" class="sst-title" style="padding: 0;">
              <input name="seccion_titulo_1" class="sst-input center bold" type="text" value="Identificación de la Empresa" style="text-transform:uppercase; color:#111; font-size:12px; height:28px;">
            </td>
          </tr>

          <tr>
            <td colspan="5" class="center" style="padding:15px 10px;">
              <div style="margin-bottom: 4px;">
                <input name="label_empresa" class="empresa-title-input" type="text" value="EMPRESA">
              </div>
              <input name="nombre_empresa" class="sst-input-line center" style="max-width:420px; display:inline-block;" type="text" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>">
              <div style="margin-top:14px;">
                <input name="label_certifica" class="sst-input center bold" type="text" value="CERTIFICA:" style="font-size:14px;">
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="5" style="padding: 15px 20px;">
              <div style="width:100%; display: flex; flex-wrap: wrap; align-items: center; gap: 4px; margin-bottom: 15px;">
                <span>Que</span>
                <input name="nombre_encargado" class="sst-input-line" style="width:220px;" type="text" value="NOMBRE COMPLETO">
                <span>identificado(a) con C.C.</span>
                <input name="cc_encargado" class="sst-input-line" style="width:130px;" type="text" value="XXXXXXXXXX">
                <span>ha sido designado(a) como representante de la Dirección para el Sistema de Gestión de Seguridad y Salud en el Trabajo, y se le han asignado las funciones, responsabilidades y autoridades para:</span>
              </div>

              <ul id="lista-responsabilidades" style="margin:0; padding:0; list-style:none;">
                </ul>

              <div class="text-start mt-2 d-print-none">
                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-agregar-punto">
                  <i class="fa-solid fa-plus"></i> Añadir punto
                </button>
              </div>
            </td>
          </tr>

          <tr>
            <td colspan="5" class="sst-title" style="padding: 0;">
              <input name="seccion_titulo_2" class="sst-input center bold" type="text" value="Firmas de Responsabilidad" style="text-transform:uppercase; color:#111; font-size:12px; height:28px;">
            </td>
          </tr>

          <tr>
            <td colspan="2">
              <div class="firma-wrapper">
                <div style="height: 80px; display: flex; align-items: flex-end; justify-content: center;">
                    <?php if(!empty($firmaRLUrl)): ?>
                        <img src="<?= $firmaRLUrl ?>" alt="Firma Representante Legal" style="max-height: 80px; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="firma-line" style="margin-top: 5px;"></div>
                <input name="firma_rep_legal" class="sst-input center bold" type="text" value="Representante Legal">
              </div>
            </td>

            <td></td>

            <td colspan="2">
              <div class="firma-wrapper">
                <div style="height: 80px; display: flex; align-items: flex-end; justify-content: center;">
                    <?php if(!empty($firmaSSTUrl)): ?>
                        <img src="<?= $firmaSSTUrl ?>" alt="Firma SST" style="max-height: 80px; object-fit: contain;">
                    <?php endif; ?>
                </div>
                <div class="firma-line" style="margin-top: 5px;"></div>
                <input name="firma_sst_nombre" class="sst-input center bold" type="text" value="<?= htmlspecialchars($profesionalSST['nombre'] ?? 'Encargado SST') ?>">
              </div>
            </td>
          </tr>
        </table>

      </div>
    </div>
  </form>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const contenedorLista = document.getElementById('lista-responsabilidades');
        const btnAgregar = document.getElementById('btn-agregar-punto');

        // Responsabilidades por defecto si el formato no tiene datos previos guardados
        const responsabilidadesPorDefecto = [
            "Planear, organizar, dirigir, desarrollar y aplicar el SG-SST, y realizar por lo menos una vez al año su evaluación.",
            "Asegurar que los requisitos del SG-SST se establezcan, implementen y mantengan, de acuerdo con lo indicado en el Decreto 1072 de 2015, Resolución 0312 de 2019 y demás normas asociadas.",
            "Informar a la alta dirección sobre el funcionamiento y los resultados del SG-SST.",
            "Promover la participación de todos los miembros de la empresa en la implementación del SG-SST.",
            "Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del SG-SST.",
            "Programar las auditorías internas necesarias para el mantenimiento y mejora continua del SG-SST."
        ];

        // Función para renderizar una viñeta rellena o vacía en el HTML
        function agregarFilaPunto(texto = "") {
            const li = document.createElement('li');
            li.className = 'punto-item';
            li.innerHTML = `
                <div class="punto-bullet">•</div>
                <div style="width: 100%;">
                    <textarea name="responsabilidades_lista[]" class="sst-textarea" style="height:auto;" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">${texto}</textarea>
                </div>
                <div class="btn-eliminar-punto d-print-none" title="Eliminar punto">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
            `;

            // Escuchar la eliminación de la fila
            li.querySelector('.btn-eliminar-punto').addEventListener('click', function() {
                li.remove();
            });

            contenedorLista.appendChild(li);

            // Ajustar altura inicial del textarea según el volumen de texto
            const tx = li.querySelector('textarea');
            tx.style.height = tx.scrollHeight + 'px';
        }

        // Evento click del botón para agregar nuevas líneas en blanco
        btnAgregar.addEventListener('click', () => agregarFilaPunto(""));

        // 1. INYECCIÓN DE DATOS DESDE PHP / API
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } 
            catch(e) { console.error("No se pudo parsear el JSON de datosGuardados"); }
        }
        
        <?php if(isset($errorCarga)) echo "console.warn('Advertencia API:', " . json_encode($errorCarga) . ");"; ?>
        
        // Mapear y rellenar los inputs normales del formulario
        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (key === 'responsabilidades_lista') continue; // Ignoramos la lista dinámica aquí

                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) {
                    campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                    if(campo.tagName.toLowerCase() === 'textarea') {
                         campo.style.height = '';
                         campo.style.height = campo.scrollHeight + 'px';
                    }
                }
            }
        }

        // Carga de la lista de responsabilidades (Usa datos de BD o en su defecto los iniciales de SST)
        if (datosGuardados && datosGuardados.responsabilidades_lista && Array.isArray(datosGuardados.responsabilidades_lista)) {
            datosGuardados.responsabilidades_lista.forEach(texto => agregarFilaPunto(texto));
        } else {
            responsabilidadesPorDefecto.forEach(texto => agregarFilaPunto(texto));
        }
    });

    // 2. LÓGICA DE ENVÍO Y GUARDADO EN LA API
    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
        
        // Obtenemos los campos fijos
        const formData = new FormData(form);
        const datosJSON = Object.fromEntries(formData.entries());

        // Capturamos y mapeamos el contenido de los textareas del listado de puntos
        const textareasDinamicos = document.querySelectorAll('textarea[name="responsabilidades_lista[]"]');
        datosJSON.responsabilidades_lista = Array.from(textareasDinamicos).map(tx => tx.value);
        
        // Quitamos la llave temporal que genera el parse nativo de los inputs del formulario
        delete datosJSON['responsabilidades_lista[]'];

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
                Swal.fire({ title: '¡Éxito!', text: 'Configuración guardada correctamente', icon: 'success', confirmButtonColor: '#1fa339' });
            } else {
                Swal.fire({ title: 'Error al guardar', text: result.error || "No se pudo completar la operación.", icon: 'error', confirmButtonColor: '#004176' });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo contactar al servidor para guardar.', icon: 'error', confirmButtonColor: '#004176' });
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
  </script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>