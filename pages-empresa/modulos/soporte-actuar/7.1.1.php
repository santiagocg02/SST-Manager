<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN A LA API
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token   = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos para este procedimiento (ej. 59)
$idItem  = isset($_GET['item']) ? (int)$_GET['item'] : 59;

// --- Lógica de Empresa (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE
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
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

// Función para leer datos desde la API
function oldv($key, $default = '') {
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] !== '') ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}

// Valores por defecto
$def_objetivo = "Establecer la identificación de las causas raíz de No Conformidades Reales y definir el mecanismo\npara el control y aseguramiento de las mismas, garantizando la mejora continua del SG-SST.";
$def_alcance = "Aplica a todas las áreas de la organización donde se identifiquen desviaciones, hallazgos\no incidentes que requieran acciones correctivas o preventivas.";
$def_definiciones = "- Acción Correctiva: Acción para eliminar la causa de una no conformidad.\n- Acción Preventiva: Acción para evitar una no conformidad potencial.";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procedimiento 7.1.1</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1100px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:18px;color:#1a4175;font-weight:700;margin:0;}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;color:#fff;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-atras{background:#6c757d;}
        .btn-guardar{background:#198754;}
        .btn-imprimir{background:#0d6efd;}
        
        .contenido{padding:30px 40px;}

        /* ====== HEADER ====== */
        .header-main{width:100%;border-collapse:collapse;margin-bottom:20px;}
        .header-main td{border:1px solid #000;padding:8px;text-align:center;vertical-align:middle;}
        
        .logo-box{display:flex;align-items:center;justify-content:center;height:60px;}
        .logo-box img{max-height:60px;object-fit:contain;}
        
        .title-main{font-size:15px;font-weight:700;margin-bottom:4px;}
        .subtitle-main{font-size:13px;font-weight:700;color:#1a4175;}
        
        .info-header{font-size:12px;text-align:left !important;padding-left:10px !important;}
        .info-header input{width:100%; border:none; outline:none; background:transparent; font-size:12px; font-weight:normal;}

        /* BARRAS */
        .section-bar{
            background:#1a4175;
            color:#fff;
            padding:6px 12px;
            font-size:13px;
            font-weight:600;
            margin-top:25px;
            margin-bottom:10px;
            text-transform:uppercase;
        }

        /* INPUTS Y TEXTAREAS */
        input[type="text"], input[type="date"], textarea {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 13px;
            font-family: inherit;
            line-height: 1.5;
            padding: 4px;
        }
        
        .textarea-box {
            border: 1px solid #ccc;
            padding: 5px;
            background: #fafafa;
        }

        textarea {
            resize: vertical;
            min-height: 60px;
            white-space: pre-wrap;
        }

        /* ====== TABLAS ====== */
        .table-sst{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }
        .table-sst th, .table-sst td{
            border:1px solid #000;
            padding:8px;
            font-size:12px;
            vertical-align: middle;
        }
        .table-sst th{
            background:#dde7f5;
            text-align:center;
            font-weight:600;
        }

        /* ====== FIRMAS ====== */
        .firmas{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:30px;
            margin-top:60px;
            text-align:center;
        }
        .firma-line{
            border-top:1px solid #000;
            margin-top:40px;
            padding-top:8px;
            font-size:12px;
            font-weight:600;
        }
        .firma-line input {
            text-align: center;
            font-weight: normal;
        }

        @media (max-width: 980px){
            .toolbar{position:static}
            body{padding:10px}
            .firmas{grid-template-columns:1fr; gap:10px;}
            .firma-line{margin-top:20px;}
        }

        @page{ size: portrait; margin: 15mm; }

        @media print{
            .toolbar, .print-hide{ display:none !important; }
            body{ padding:0; background:#fff; }
            .contenedor{ border:none; box-shadow:none; max-width:100%; margin:0;}
            .contenido{ padding:0; }
            .textarea-box { border: none; background: transparent; padding: 0;}
            input, textarea{ border:none !important; box-shadow:none !important; background:transparent !important; resize:none;}
        }

    </style>
</head>

<body>

<div class="contenedor">

    <div class="toolbar print-hide">
        <h1>Procedimiento de Acciones Preventivas y Correctivas</h1>
        <div class="acciones">
            <button onclick="history.back()" class="btn btn-atras">Atrás</button>
            <button class="btn btn-guardar" id="btnGuardar">Guardar Procedimiento</button>
            <button onclick="window.print()" class="btn btn-imprimir">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="formProcedimiento">
            
            <table class="header-main">
                <tr>
                    <td rowspan="3" style="width:22%;">
                        <div class="logo-box">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa">
                            <?php else: ?>
                                <span style="color:#999; font-size:12px; font-weight:bold;">LOGO EMPRESA</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td rowspan="3" style="width:53%;">
                        <div class="title-main">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
                        <div class="subtitle-main">PROCEDIMIENTO DE ACCIONES PREVENTIVAS Y CORRECTIVAS</div>
                    </td>
                    <td class="info-header" style="width:25%;">
                        <strong>Código:</strong> 
                        <input type="text" name="codigo" value="<?= oldv('codigo', 'SST-PRO-07-1') ?>" style="display:inline; width:60%;">
                    </td>
                </tr>
                <tr>
                    <td class="info-header">
                        <strong>Versión:</strong> 
                        <input type="text" name="version" value="<?= oldv('version', '01') ?>" style="display:inline; width:60%;">
                    </td>
                </tr>
                <tr>
                    <td class="info-header">
                        <strong>Fecha:</strong> 
                        <input type="date" name="fecha_doc" value="<?= oldv('fecha_doc', date('Y-m-d')) ?>" style="display:inline; width:60%;">
                    </td>
                </tr>
            </table>

            <div class="section-bar">1. OBJETIVO</div>
            <div class="textarea-box">
                <textarea name="objetivo"><?= oldv('objetivo', $def_objetivo) ?></textarea>
            </div>

            <div class="section-bar">2. ALCANCE</div>
            <div class="textarea-box">
                <textarea name="alcance"><?= oldv('alcance', $def_alcance) ?></textarea>
            </div>

            <div class="section-bar">3. DEFINICIONES</div>
            <div class="textarea-box">
                <textarea name="definiciones" style="min-height: 80px;"><?= oldv('definiciones', $def_definiciones) ?></textarea>
            </div>

            <div class="section-bar">4. DESCRIPCIÓN DE ACTIVIDADES</div>
            <table class="table-sst">
                <thead>
                    <tr>
                        <th style="width:25%">ACTIVIDAD</th>
                        <th style="width:50%">DESCRIPCIÓN</th>
                        <th style="width:25%">RESPONSABLE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1; $i<=3; $i++): 
                        $defAct = ($i==1) ? 'Detección' : (($i==2) ? 'Análisis' : 'Plan de acción');
                        $defDesc = ($i==1) ? 'Identificación de desviaciones por auditoría o reporte.' : (($i==2) ? 'Investigación de causas (5 porqués, Ishikawa).' : 'Definición de tareas y tiempos de corrección.');
                        $defResp = ($i==1) ? 'Todo el personal' : (($i==2) ? 'Responsable SST' : 'Jefe de área');
                    ?>
                    <tr>
                        <td><input type="text" name="actividad_<?= $i ?>" value="<?= oldv("actividad_$i", $defAct) ?>" style="font-weight:bold;"></td>
                        <td><textarea name="desc_act_<?= $i ?>" style="min-height:40px;"><?= oldv("desc_act_$i", $defDesc) ?></textarea></td>
                        <td><input type="text" name="resp_act_<?= $i ?>" value="<?= oldv("resp_act_$i", $defResp) ?>" style="text-align:center;"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="section-bar">5. CONTROL DE CAMBIOS</div>
            <table class="table-sst">
                <thead>
                    <tr>
                        <th style="width:15%">VERSIÓN</th>
                        <th style="width:65%">MOTIVO</th>
                        <th style="width:20%">FECHA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1; $i<=2; $i++): ?>
                    <tr>
                        <td><input type="text" name="ctrl_version_<?= $i ?>" value="<?= oldv("ctrl_version_$i", ($i==1?'01':'')) ?>" style="text-align:center;"></td>
                        <td><input type="text" name="ctrl_motivo_<?= $i ?>" value="<?= oldv("ctrl_motivo_$i", ($i==1?'Creación inicial':'')) ?>"></td>
                        <td><input type="date" name="ctrl_fecha_<?= $i ?>" value="<?= oldv("ctrl_fecha_$i") ?>"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="firmas">
                <div>
                    <input type="text" name="firma_elaborado" value="<?= oldv('firma_elaborado') ?>" placeholder="Nombre de quien elabora">
                    <div class="firma-line">ELABORADO POR<br>Responsable SST</div>
                </div>
                <div>
                    <input type="text" name="firma_revisado" value="<?= oldv('firma_revisado') ?>" placeholder="Nombre de quien revisa">
                    <div class="firma-line">REVISADO POR<br>COPASST</div>
                </div>
                <div>
                    <input type="text" name="firma_aprobado" value="<?= oldv('firma_aprobado') ?>" placeholder="Nombre de quien aprueba">
                    <div class="firma-line">APROBADO POR<br>Representante Legal</div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formProcedimiento');
    const formData = new FormData(form);
    
    // Convertimos el formulario en un objeto JSON limpio
    const datosJSON = Object.fromEntries(formData.entries());

    const originalText = btn.innerHTML;
    btn.innerHTML = 'Guardando...';
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
                text: 'El Procedimiento de Acciones Preventivas y Correctivas ha sido guardado correctamente.',
                icon: 'success',
                confirmButtonColor: '#198754'
            });
        } else {
            Swal.fire({
                title: 'Error al guardar',
                text: result.error || "No se pudo completar la operación.",
                icon: 'error',
                confirmButtonColor: '#1b4fbd'
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo contactar al servidor para guardar.',
            icon: 'error',
            confirmButtonColor: '#1b4fbd'
        });
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
});
</script>

</body>
</html>