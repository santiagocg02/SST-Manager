<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 54; // ID correspondiente al ítem 7.1.2

// --- Lógica de Logo ---
$logoUrl = "";
if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresaId", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// --- Carga de Datos Dinámicos ---
$resForm = $api->solicitar("formularios-dinamicos/empresa/$empresaId/item/$idItem", "GET", null, $token);
$datos = [];
$camposCrudos = $resForm['data']['data']['campos'] ?? $resForm['data']['campos'] ?? null;
if (is_string($camposCrudos)) $datos = json_decode($camposCrudos, true) ?: [];
elseif (is_array($camposCrudos)) $datos = $camposCrudos;

function oldv($key, $default = '') {
    global $datos;
    return isset($datos[$key]) ? htmlspecialchars((string)$datos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>7.1.2 Formato ACP - Acción Correctiva / Preventiva</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1a4175; --section-bg: #f2f2f2; }
        body { background-color: #f4f7f9; font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
        .format-container { background: #fff; max-width: 900px; margin: auto; border: 1px solid #000; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        
        /* Toolbar */
        .toolbar { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background: #dde7f5; border-bottom: 1px solid #000; }
        
        /* Tablas */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: 1px solid #000; padding: 8px; font-size: 11px; vertical-align: middle; }
        .bg-gray { background-color: var(--section-bg); font-weight: bold; text-align: center; }
        .title-section { background-color: var(--primary-blue); color: white; font-weight: bold; text-align: center; text-transform: uppercase; }

        /* Inputs */
        input[type="text"], input[type="date"], textarea, select { width: 100%; border: none; outline: none; background: transparent; font-size: 11px; font-family: inherit; }
        textarea { resize: none; min-height: 40px; }
        .check-group { display: flex; justify-content: space-around; font-weight: bold; }

        @media print { .no-print { display: none !important; } .format-container { box-shadow: none; } }
    </style>
</head>
<body>

<div class="format-container">
    <div class="toolbar no-print">
        <span class="fw-bold">Formato 7.1.2 - Registro de ACP</span>
        <div>
            <button class="btn btn-sm btn-secondary" onclick="history.back()">Volver</button>
            <button class="btn btn-sm btn-success" id="btnGuardar">Guardar Registro</button>
            <button class="btn btn-sm btn-primary" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <form id="formACP">
        <table>
            <tr>
                <td rowspan="2" style="width: 180px; text-align: center;">
                    <?php if ($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" style="max-height: 60px;">
                    <?php else: ?>
                        <div style="font-size: 9px; color: #999;">LOGO EMPRESA</div>
                    <?php endif; ?>
                </td>
                <td style="font-weight: bold; text-align: center; font-size: 13px;">
                    SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                </td>
                <td style="width: 120px; text-align: center; font-weight: bold;">Versión: 01</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center; text-transform: uppercase;">
                    ACCIÓN CORRECTIVA, PREVENTIVA Y DE MEJORA (ACP)
                </td>
                <td style="text-align: center; font-size: 10px;">Fecha: 24/04/2026<br>Cod: SST-FOR-07-2</td>
            </tr>
        </table>

        <div class="title-section">1. IDENTIFICACIÓN DE LA ACCIÓN</div>
        <table>
            <tr>
                <td class="bg-gray" style="width: 150px;">TIPO DE ACCIÓN:</td>
                <td colspan="2">
                    <div class="check-group">
                        <label><input type="radio" name="tipo_accion" value="CORRECTIVA" <?= oldv('tipo_accion') == 'CORRECTIVA' ? 'checked' : '' ?>> CORRECTIVA</label>
                        <label><input type="radio" name="tipo_accion" value="PREVENTIVA" <?= oldv('tipo_accion') == 'PREVENTIVA' ? 'checked' : '' ?>> PREVENTIVA</label>
                        <label><input type="radio" name="tipo_accion" value="MEJORA" <?= oldv('tipo_accion') == 'MEJORA' ? 'checked' : '' ?>> DE MEJORA</label>
                    </div>
                </td>
                <td class="bg-gray" style="width: 100px;">No. CONSECUTIVO:</td>
                <td><input type="text" name="consecutivo" value="<?= oldv('consecutivo') ?>" placeholder="001"></td>
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
                <td colspan="5"><textarea name="descripcion_hallazgo"><?= oldv('descripcion_hallazgo') ?></textarea></td>
            </tr>
        </table>

        <div class="title-section">2. ANÁLISIS DE CAUSA RAÍZ (METODOLOGÍA DE LOS 5 PORQUÉS)</div>
        <table>
            <tr>
                <td class="bg-gray" style="width: 100px;">1. ¿POR QUÉ?</td>
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
                <td>RESPONSABLE</td>
                <td style="width: 120px;">FECHA LÍMITE</td>
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
                <td class="bg-gray" style="width: 150px;">¿LA ACCIÓN FUE EFICAZ?</td>
                <td style="width: 150px;">
                    <select name="eficacia">
                        <option value="">- Seleccione -</option>
                        <option value="SI" <?= oldv('eficacia') == 'SI' ? 'selected' : '' ?>>SÍ (Cierre)</option>
                        <option value="NO" <?= oldv('eficacia') == 'NO' ? 'selected' : '' ?>>NO (Reabrir)</option>
                    </select>
                </td>
                <td class="bg-gray">FECHA DE CIERRE:</td>
                <td><input type="date" name="fecha_cierre" value="<?= oldv('fecha_cierre') ?>"></td>
            </tr>
            <tr>
                <td class="bg-gray">OBSERVACIONES DE CIERRE:</td>
                <td colspan="3"><textarea name="obs_cierre"><?= oldv('obs_cierre') ?></textarea></td>
            </tr>
        </table>

        <table style="margin-top: 10px;">
            <tr>
                <td style="height: 60px; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">RESPONSABLE DEL REPORTE</div>
                </td>
                <td style="text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto;">RESPONSABLE SST / CIERRE</div>
                </td>
            </tr>
        </table>
    </form>
</div>

<script>
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formACP');
    const formData = new FormData(form);
    const datosJSON = Object.fromEntries(formData.entries());

    btn.innerText = 'Guardando...'; btn.disabled = true;

    try {
        const response = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresaId ?>, id_item_sst: <?= $idItem ?>, datos: datosJSON })
        });

        const res = await response.json();
        if (res.ok) {
            Swal.fire('¡Guardado!', 'El registro ACP ha sido actualizado.', 'success');
        } else {
            Swal.fire('Error', res.error || 'No se pudo guardar.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Fallo de red.', 'error');
    } finally {
        btn.innerText = 'Guardar Registro'; btn.disabled = false;
    }
});
</script>

</body>
</html>