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
// Ajusta el ID de este ítem según tu base de datos para este formato (ej. 54)
$idItem  = isset($_GET['item']) ? (int)$_GET['item'] : 54;

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>7.1.2 Formato ACP - Acción Correctiva / Preventiva</title>
    
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
        
        .contenido{padding:25px;}

        table{width:100%;border-collapse:collapse;table-layout:fixed;margin-bottom:15px;}
        td, th{
            border:1px solid #6b6b6b;
            padding:6px;
            vertical-align:middle;
            word-break:break-word;
            overflow-wrap:anywhere;
            font-size:12px;
        }
        
        .logo-box{
            width:140px;height:65px;display:flex;align-items:center;justify-content:center;
            margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center
        }

        .bg-gray { background-color: #f8fafc; font-weight: bold; text-align: center; }
        .title-section { 
            background-color: #1a4175; 
            color: white; 
            font-weight: bold; 
            text-align: center; 
            text-transform: uppercase; 
            padding: 8px;
            font-size: 13px;
            margin-bottom: 0;
            border: 1px solid #6b6b6b;
            border-bottom: none;
        }

        input[type="text"], input[type="date"], textarea, select { 
            width: 100%; 
            border: none; 
            outline: none; 
            background: transparent; 
            font-size: 12px; 
            font-family: inherit; 
            padding: 4px;
        }
        textarea { resize: vertical; min-height: 45px; white-space: pre-wrap; }
        
        .check-group { display: flex; justify-content: space-around; font-weight: bold; align-items: center; }
        .check-group label { cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .check-group input[type="radio"] { width: 14px; height: 14px; margin: 0; cursor: pointer; }

        @media (max-width: 980px){
            .toolbar{position:static}
            body{padding:10px}
        }

        @page{ size: portrait; margin: 10mm; }

        @media print { 
            .toolbar, .print-hide { display: none !important; } 
            body { background: #fff; padding: 0; }
            .contenedor { border: none; box-shadow: none; max-width: 100%; margin: 0; }
            .contenido { padding: 5px; }
            input, textarea, select { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>7.1.2 Formato ACP - Acción Correctiva / Preventiva</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="formACP">
            <table>
                <tr>
                    <td rowspan="2" style="width: 20%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="width: 60%; font-weight: bold; text-align: center; font-size: 14px; background: #f8fafc;">
                        SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                    </td>
                    <td style="width: 20%; text-align: center; font-weight: bold;">Versión: 01</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: center; text-transform: uppercase;">
                        ACCIÓN CORRECTIVA, PREVENTIVA Y DE MEJORA (ACP)
                    </td>
                    <td style="text-align: center; font-weight:bold;">Fecha: 24/04/2026<br>Cod: SST-FOR-07-2</td>
                </tr>
            </table>

            <div class="title-section">1. IDENTIFICACIÓN DE LA ACCIÓN</div>
            <table>
                <tr>
                    <td class="bg-gray" style="width: 20%;">TIPO DE ACCIÓN:</td>
                    <td colspan="2" style="width: 50%;">
                        <div class="check-group">
                            <label><input type="radio" name="tipo_accion" value="CORRECTIVA" <?= oldv('tipo_accion') == 'CORRECTIVA' ? 'checked' : '' ?>> CORRECTIVA</label>
                            <label><input type="radio" name="tipo_accion" value="PREVENTIVA" <?= oldv('tipo_accion') == 'PREVENTIVA' ? 'checked' : '' ?>> PREVENTIVA</label>
                            <label><input type="radio" name="tipo_accion" value="MEJORA" <?= oldv('tipo_accion') == 'MEJORA' ? 'checked' : '' ?>> DE MEJORA</label>
                        </div>
                    </td>
                    <td class="bg-gray" style="width: 15%;">No. CONSECUTIVO:</td>
                    <td style="width: 15%;"><input type="text" name="consecutivo" value="<?= oldv('consecutivo') ?>" placeholder="001"></td>
                </tr>
                <tr>
                    <td class="bg-gray">FECHA DE REPORTE:</td>
                    <td><input type="date" name="fecha_reporte" value="<?= oldv('fecha_reporte') ?>"></td>
                    <td class="bg-gray">PROCESO / ÁREA:</td>
                    <td colspan="2"><input type="text" name="area" value="<?= oldv('area') ?>"></td>
                </tr>
                <tr>
                    <td class="bg-gray">FUENTE DEL HALLAZGO:</td>
                    <td colspan="4">
                        <input type="text" name="fuente" value="<?= oldv('fuente') ?>" placeholder="Ej: Auditoría, Inspección, Reporte de incidente...">
                    </td>
                </tr>
                <tr>
                    <td class="bg-gray" colspan="5">DESCRIPCIÓN DEL HALLAZGO / NO CONFORMIDAD:</td>
                </tr>
                <tr>
                    <td colspan="5"><textarea name="descripcion_hallazgo" style="min-height: 70px;"><?= oldv('descripcion_hallazgo') ?></textarea></td>
                </tr>
            </table>

            <div class="title-section">2. ANÁLISIS DE CAUSA RAÍZ (METODOLOGÍA DE LOS 5 PORQUÉS)</div>
            <table>
                <tr>
                    <td class="bg-gray" style="width: 20%;">1. ¿POR QUÉ?</td>
                    <td><textarea name="porque_1"><?= oldv('porque_1') ?></textarea></td>
                </tr>
                <tr>
                    <td class="bg-gray">2. ¿POR QUÉ?</td>
                    <td><textarea name="porque_2"><?= oldv('porque_2') ?></textarea></td>
                </tr>
                <tr>
                    <td class="bg-gray">3. ¿POR QUÉ?</td>
                    <td><textarea name="porque_3"><?= oldv('porque_3') ?></textarea></td>
                </tr>
                <tr>
                    <td class="bg-gray">4. ¿POR QUÉ?</td>
                    <td><textarea name="porque_4"><?= oldv('porque_4') ?></textarea></td>
                </tr>
                <tr>
                    <td class="bg-gray">5. ¿POR QUÉ?</td>
                    <td><textarea name="porque_5"><?= oldv('porque_5') ?></textarea></td>
                </tr>
                <tr>
                    <td class="bg-gray">CAUSA RAÍZ FINAL:</td>
                    <td><textarea name="causa_raiz" style="font-weight: bold;"><?= oldv('causa_raiz') ?></textarea></td>
                </tr>
            </table>

            <div class="title-section">3. PLAN DE ACCIÓN (TAREAS A REALIZAR)</div>
            <table>
                <tr class="bg-gray">
                    <td style="width: 50%;">ACTIVIDAD / TAREA</td>
                    <td style="width: 30%;">RESPONSABLE</td>
                    <td style="width: 20%;">FECHA LÍMITE</td>
                </tr>
                <?php for($i=1; $i<=3; $i++): ?>
                <tr>
                    <td><textarea name="actividad_<?= $i ?>"><?= oldv("actividad_$i") ?></textarea></td>
                    <td><input type="text" name="resp_act_<?= $i ?>" value="<?= oldv("resp_act_$i") ?>"></td>
                    <td><input type="date" name="fecha_act_<?= $i ?>" value="<?= oldv("fecha_act_$i") ?>"></td>
                </tr>
                <?php endfor; ?>
            </table>

            <div class="title-section">4. VERIFICACIÓN DE LA EFICACIA</div>
            <table>
                <tr>
                    <td class="bg-gray" style="width: 25%;">¿LA ACCIÓN FUE EFICAZ?</td>
                    <td style="width: 25%; text-align:center;">
                        <select name="eficacia">
                            <option value="">- Seleccione -</option>
                            <option value="SI" <?= oldv('eficacia') == 'SI' ? 'selected' : '' ?>>SÍ (Cierre)</option>
                            <option value="NO" <?= oldv('eficacia') == 'NO' ? 'selected' : '' ?>>NO (Reabrir)</option>
                        </select>
                    </td>
                    <td class="bg-gray" style="width: 25%;">FECHA DE CIERRE:</td>
                    <td style="width: 25%;"><input type="date" name="fecha_cierre" value="<?= oldv('fecha_cierre') ?>"></td>
                </tr>
                <tr>
                    <td class="bg-gray">OBSERVACIONES DE CIERRE:</td>
                    <td colspan="3"><textarea name="obs_cierre" style="min-height: 60px;"><?= oldv('obs_cierre') ?></textarea></td>
                </tr>
            </table>

            <table style="margin-top: 25px; border:none;">
                <tr>
                    <td style="height: 80px; text-align: center; vertical-align: bottom; border:none; width:50%;">
                        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top:4px;">RESPONSABLE DEL REPORTE</div>
                    </td>
                    <td style="text-align: center; vertical-align: bottom; border:none; width:50%;">
                        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top:4px;">RESPONSABLE SST / CIERRE</div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>
// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formACP');
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
                text: 'El formato ACP se ha guardado correctamente.',
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