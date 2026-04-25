<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = isset($_SESSION["token"]) ? $_SESSION["token"] : "";
$empresa = isset($_SESSION["id_empresa"]) ? (int)$_SESSION["id_empresa"] : 0;

// Asignación segura del ID
$idItem = 2;
if (isset($_GET['item'])) {
    $idItem = (int)$_GET['item'];
}

// 2. SOLICITAMOS LOS DATOS A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);

// DATOS DE LA EMPRESA (LOGO E INFORMACIÓN)
$resEmpresaInfo = $api->solicitar("empresas/$empresa", "GET", null, $token);
$dataEmp = isset($resEmpresaInfo['data']) ? $resEmpresaInfo['data'] : [];

// Extracción de datos ultra-segura
$logoEmpresaUrl = isset($dataEmp['logo_url']) ? $dataEmp['logo_url'] : '';
$nombreEmpresa  = isset($dataEmp['nombre']) ? $dataEmp['nombre'] : (isset($dataEmp['razon_social']) ? $dataEmp['razon_social'] : 'NOMBRE DE LA EMPRESA');
$nitEmpresa     = isset($dataEmp['nit']) ? $dataEmp['nit'] : '000.000.000';
$dirEmpresa     = isset($dataEmp['direccion']) ? $dataEmp['direccion'] : 'Dirección no registrada';
$telEmpresa     = isset($dataEmp['telefono']) ? $dataEmp['telefono'] : 'Teléfono no registrado';
$emailEmpresa   = isset($dataEmp['correo']) ? $dataEmp['correo'] : (isset($dataEmp['email']) ? $dataEmp['email'] : 'correo@empresa.com');
$ciudadEmpresa  = isset($dataEmp['ciudad']) ? $dataEmp['ciudad'] : 'Cali, Colombia';

// Armamos el pie de página dinámico
$piePaginaDefault = "$dirEmpresa - Teléfono $telEmpresa\ne-mail: $emailEmpresa\n$ciudadEmpresa";

// CORRECCIÓN DE LA FECHA EN ESPAÑOL
$meses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
$mesActual = $meses[date('n') - 1]; 
$fechaTexto = date('d') . " de " . $mesActual . " de " . date('Y');

// Extracción segura de la ciudad
$partesCiudad = explode(',', $ciudadEmpresa);
$ciudadBase = isset($partesCiudad[0]) ? $partesCiudad[0] : 'Cali';
$ciudadYFecha = trim($ciudadBase) . ", " . $fechaTexto;

// PROCESAMIENTO DE CAMPOS GUARDADOS
$datosCampos = [];
$camposCrudos = null;

if (isset($resFormulario['data']['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['data']['campos'];
} elseif (isset($resFormulario['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['campos'];
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
  <title>F-GH-002 | Certificación Laboral</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root{
      --sst-bg:#eef3f9;
      --sst-paper:#ffffff;
      --sst-text:#111;
      --sst-toolbar:#dde7f5;
      --sst-toolbar-border:#c8d3e2;
    }
    html, body{ margin:0; padding:0; font-family: 'Arial', sans-serif; background:var(--sst-bg); color:var(--sst-text); }
    .sst-toolbar{ position:sticky; top:0; z-index:100; background:var(--sst-toolbar); border-bottom:1px solid var(--sst-toolbar-border); padding:12px 18px; display:flex; justify-content:space-between; align-items:center; }
    .sst-page{ padding:20px; }
    
    /* Documento normal en pantalla */
    .sst-paper{ width:216mm; min-height:279mm; margin:0 auto; background:var(--sst-paper); padding:20mm; box-shadow:0 10px 25px rgba(0,0,0,.08); }
    
    /* Encabezado Limpio sin bordes */
    .clean-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 50px; padding-bottom: 15px; border-bottom: 2px solid #003366; }
    .header-logo-container { flex: 0 0 25%; text-align: left; }
    .header-text-container { flex: 1; text-align: center; }
    .header-company-name { width: 100%; border: none; outline: none; font-weight: 900; font-size: 22px; text-align: center; text-transform: uppercase; color: #000; background: transparent; font-family: inherit; }
    .header-nit { font-weight: bold; font-size: 14px; color: #333; margin-top: 5px;}

    /* Estilos de Certificación Cuerpo */
    .cert-body { font-family: 'Times New Roman', Times, serif; font-size: 16px; line-height: 1.6; }
    .editable-content { width: 100%; border: none; outline: none; background: transparent; font-size: 16px; line-height: 1.6; text-align: justify; resize: none; overflow: hidden; font-family: inherit; }
    .editable-content:hover, .inline-edit:hover, .header-company-name:hover { background-color: #f8f9fa; }
    
    .signature-section { margin-top: 60px; font-family: 'Times New Roman', Times, serif; }
    .footer-info { margin-top: 60px; font-size: 12px; color: #555; text-align: center; border-top: 1px solid #ddd; padding-top: 15px; font-family: 'Arial', sans-serif; }
    
    input.inline-edit { border: none; border-bottom: 1px dashed #ccc; outline: none; font-weight: bold; width: auto; font-family: inherit; font-size: inherit; background: transparent;}
    
    /* ====== AJUSTES DE IMPRESIÓN ====== */
    @media print {
      @page { 
          margin: 0; /* Quita los textos automáticos de URL y fecha del navegador */
      } 
      
      html, body {
          background: white !important;
          margin: 0 !important;
          padding: 0 !important;
      }

      .sst-toolbar { display: none !important; }
      .sst-page { padding: 0 !important; }
      
      /* Quitamos el min-height para que no fuerce una página extra en blanco */
      .sst-paper { 
          box-shadow: none !important; 
          border: none !important; 
          width: 100% !important; 
          min-height: auto !important; /* CLAVE PARA EVITAR SALTO DE PÁGINA */
          margin: 0 !important; 
          padding: 15mm !important; /* Margen interno para que no pegue al borde del papel */
          page-break-after: avoid;
      }
      
      .editable-content:hover, .inline-edit:hover, .header-company-name:hover { background-color: transparent; }
      input.inline-edit { border-bottom: none; }
    }
  </style>
</head>
<body>

<form id="form-sst-dinamico">
    <div class="sst-toolbar">
        <h1 style="font-size:16px; font-weight:800; margin:0;">Generador de Certificación Laboral</h1>
        <div>
            <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
        </div>
    </div>

    <div class="sst-page">
        <div class="sst-paper">
            
            <div class="clean-header">
                <div class="header-logo-container">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" style="max-width: 100%; max-height: 80px;">
                    <?php endif; ?>
                </div>
                <div class="header-text-container">
                    <input name="empresa_nombre" class="header-company-name" value="<?= htmlspecialchars($nombreEmpresa) ?>">
                    <div class="header-nit">
                        NIT: <input name="empresa_nit" class="inline-edit" style="width: 150px; text-align: center;" value="<?= htmlspecialchars($nitEmpresa) ?>">
                    </div>
                </div>
                <div style="flex: 0 0 25%;"></div>
            </div>

            <div class="cert-body">
                <div style="text-align: right; margin-bottom: 40px;">
                    <input name="ciudad_fecha" class="inline-edit" style="width: 350px; text-align: right;" value="<?= htmlspecialchars($ciudadYFecha) ?>">
                </div>

                <div style="text-align: center; margin-bottom: 40px;">
                    <input name="titulo_cert" class="editable-content" style="text-align:center; font-weight:bold; font-size: 18px;" value="A QUIEN PUEDA INTERESAR">
                </div>

                <p style="font-weight: bold; margin-bottom: 20px;">CERTIFICA QUE:</p>
                
                <textarea name="contenido_principal" class="editable-content">El(la) señor(a) XXXXX XXXXX, identificado(a) con cédula de ciudadanía No. XX.XXX.XXX, labora en nuestra empresa desde el XX de XXXXXX de XXXX a la fecha, desempeñando el cargo de XXXXXXXX con un contrato de trabajo a término XXXXXXXX y con un salario básico mensual de $XX.XXX.XXX más XXXXXX.</textarea>

                <p style="margin-top: 30px;">
                    <textarea name="constancia_cierre" class="editable-content">Para constancia de lo anterior, se expide a solicitud del interesado.</textarea>
                </p>
            </div>

            <div class="signature-section">
                <p>Cordialmente,</p>
                <div style="margin-top: 60px;">
                    <input name="firma_nombre" class="editable-content" style="font-weight:bold; width: 350px; border-top: 1px solid #000; padding-top: 5px;" value="REPRESENTANTE LEGAL / RRHH">
                    <br>
                    <input name="firma_cargo" class="editable-content" style="width: 350px;" value="Cargo del Firmante">
                </div>
            </div>

            <div class="footer-info">
                <textarea name="pie_pagina" class="editable-content" style="text-align:center; font-size:12px; line-height: 1.4; color: #555;"><?= htmlspecialchars($piePaginaDefault) ?></textarea>
            </div>

        </div>
    </div>
</form>

<script>
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const textareas = document.querySelectorAll('textarea.editable-content');
        textareas.forEach(el => {
            autoResize(el);
            el.addEventListener('input', () => autoResize(el));
        });

        // Forma 100% segura de procesar el JSON en PHP hacia JS
        let datosGuardados = <?= json_encode(empty($datosCampos) ? new stdClass() : $datosCampos) ?>;
        
        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                const campo = document.querySelector(`[name="${key}"]`);
                if (campo) {
                    campo.value = value;
                    if(campo.tagName === 'TEXTAREA') autoResize(campo);
                }
            }
        }
    });

    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const formData = new FormData(document.getElementById('form-sst-dinamico'));
        const datosJSON = Object.fromEntries(formData.entries());

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

        try {
            const response = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $token ?>'
                },
                body: JSON.stringify({
                    id_empresa: <?= $empresa ?>,
                    id_item_sst: <?= $idItem ?>,
                    datos: datosJSON
                })
            });

            const result = await response.json();
            if (result.ok) {
                Swal.fire('¡Éxito!', 'Certificación guardada.', 'success');
            } else {
                Swal.fire('Error', 'No se pudo guardar.', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error de conexión.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar Cambios';
        }
    });
</script>
</body>
</html>