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
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 54; 

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

$filasBase = 12; // Cantidad de filas iniciales en la matriz
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>7.1.3 Matriz de Seguimiento y Cierre de Hallazgos</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-blue: #1a4175; --header-bg: #dde7f5; }
        body { background-color: #f4f7f9; font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
        .format-container { background: #fff; max-width: 1300px; margin: auto; border: 1px solid #dee2e6; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        
        /* Toolbar Superior */
        .toolbar { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: var(--header-bg); border-bottom: 1px solid #c8d3e2; }
        .toolbar h1 { font-size: 18px; color: var(--primary-blue); font-weight: bold; margin: 0; }
        .btn-group { display: flex; gap: 10px; }
        .btn-custom { border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; color: white; transition: 0.2s; }
        .btn-save { background: #198754; }
        .btn-print { background: #0d6efd; }
        .btn-back { background: #6c757d; }

        /* Estructura de Tabla Matriz */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: 1px solid #000; padding: 6px; font-size: 11px; vertical-align: middle; }
        .bg-blue-light { background: #dde7f5; font-weight: bold; text-align: center; }
        
        /* Inputs Estilo Excel */
        input, select, textarea { width: 100%; border: none; outline: none; background: transparent; font-family: inherit; font-size: 11px; }
        textarea { resize: vertical; min-height: 35px; }

        /* Contadores de Estatus */
        .counter-box { width: 40px; text-align: center; font-weight: bold; font-size: 14px; }
        .text-red { color: red; }
        .text-green { color: green; }

        /* Colores de Estatus en Fila */
        .status-abierta { background-color: #ffdce0; color: #c62828; font-weight: bold; text-align: center; }
        .status-cerrada { background-color: #e2f1e1; color: #2e7d32; font-weight: bold; text-align: center; }

        @media print { .no-print { display: none !important; } .format-container { box-shadow: none; border: none; } body { padding: 0; } }
    </style>
</head>
<body>

<div class="format-container">
    <div class="toolbar no-print">
        <h1>Matriz de Seguimiento y Cierre de Hallazgos (7.1.3)</h1>
        <div class="btn-group">
            <button class="btn-custom btn-back" onclick="history.back()">Atrás</button>
            <button class="btn-custom btn-save" id="btnGuardar">Guardar Matriz</button>
            <button class="btn-custom btn-print" onclick="window.print()">Imprimir PDF</button>
        </div>
    </div>

    <form id="formMatriz">
        <table>
            <tr>
                <td rowspan="2" style="width: 180px; text-align: center;">
                    <?php if ($logoUrl): ?>
                        <img src="<?= $logoUrl ?>" style="max-height: 60px; max-width: 100%;">
                    <?php else: ?>
                        <div style="font-size: 10px; color: #999;">TU LOGO AQUÍ</div>
                    <?php endif; ?>
                </td>
                <td style="font-weight: bold; text-align: center; font-size: 14px; text-transform: uppercase;">
                    SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO
                </td>
                <td style="width: 120px; text-align: center;">0</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center; text-transform: uppercase;">
                    MATRIZ DE CONDICIONES INSEGURAS / ACCIONES CORRECTIVAS Y/O PREVENTIVAS
                </td>
                <td style="text-align: center;">AN-SST-29<br>24/04/2026</td>
            </tr>
        </table>

        <table style="border-top: none;">
            <tr>
                <td colspan="8" style="border: none;"></td>
                <td class="bg-blue-light" style="width: 80px;">ABIERTAS</td>
                <td class="counter-box text-red" id="countAbiertas">0</td>
            </tr>
            <tr>
                <td colspan="8" style="border: none;"></td>
                <td class="bg-blue-light">CERRADAS</td>
                <td class="counter-box text-green" id="countCerradas">0</td>
            </tr>
        </table>

        <table style="margin-top: 10px;" id="tablaMatriz">
            <thead>
                <tr class="bg-blue-light">
                    <th style="width: 110px;">ACPM / HALLAZGO</th>
                    <th style="width: 140px;">FUENTE</th>
                    <th>DESCRIPCIÓN</th>
                    <th>ACCIÓN A TOMAR</th>
                    <th style="width: 110px;">RESPONSABLE DEL SEGUIMIENTO</th>
                    <th style="width: 100px;">FECHA DE CUMPLIMIENTO</th>
                    <th>SEGUIMIENTO</th>
                    <th style="width: 90px;">STATUS</th>
                    <th style="width: 110px;">RESPONSABLE DEL CIERRE</th>
                </tr>
            </thead>
            <tbody id="tbodyMatriz">
                <?php for($i = 1; $i <= $filasBase; $i++): ?>
                <tr>
                    <td><input type="text" name="acpm_<?= $i ?>" value="<?= oldv("acpm_$i") ?>"></td>
                    <td>
                        <select name="fuente_<?= $i ?>" class="fuente-selector">
                            <option value="">- Seleccione -</option>
                            <option value="CONDICIÓN INSEGURA" <?= oldv("fuente_$i") == 'CONDICIÓN INSEGURA' ? 'selected' : '' ?>>CONDICIÓN INSEGURA</option>
                            <option value="RECOMENDACIONES ARL" <?= oldv("fuente_$i") == 'RECOMENDACIONES ARL' ? 'selected' : '' ?>>RECOMENDACIONES ARL</option>
                            <option value="HALLAZGO AUDITORÍA" <?= oldv("fuente_$i") == 'HALLAZGO AUDITORÍA' ? 'selected' : '' ?>>HALLAZGO AUDITORÍA</option>
                            <option value="ACCIONES REVISIÓN DIRECCIÓN" <?= oldv("fuente_$i") == 'ACCIONES REVISIÓN DIRECCIÓN' ? 'selected' : '' ?>>REVISIÓN DIRECCIÓN</option>
                            <option value="INVESTIGACIÓN ACCIDENTES" <?= oldv("fuente_$i") == 'INVESTIGACIÓN ACCIDENTES' ? 'selected' : '' ?>>INVESTIGACIÓN ACCIDENTES</option>
                            <option value="ACTO INSEGURO" <?= oldv("fuente_$i") == 'ACTO INSEGURO' ? 'selected' : '' ?>>ACTO INSEGURO</option>
                        </select>
                    </td>
                    <td><textarea name="desc_<?= $i ?>"><?= oldv("desc_$i") ?></textarea></td>
                    <td><textarea name="accion_<?= $i ?>"><?= oldv("accion_$i") ?></textarea></td>
                    <td><input type="text" name="resp_seg_<?= $i ?>" value="<?= oldv("resp_seg_$i") ?>"></td>
                    <td><input type="date" name="fec_cumpl_<?= $i ?>" value="<?= oldv("fec_cumpl_$i") ?>"></td>
                    <td><textarea name="seguimiento_<?= $i ?>"><?= oldv("seguimiento_$i") ?></textarea></td>
                    <td>
                        <select name="status_<?= $i ?>" class="status-selector" onchange="actualizarColores(this)">
                            <option value="">-</option>
                            <option value="ABIERTA" <?= oldv("status_$i") == 'ABIERTA' ? 'selected' : '' ?>>ABIERTA</option>
                            <option value="CERRADA" <?= oldv("status_$i") == 'CERRADA' ? 'selected' : '' ?>>CERRADA</option>
                        </select>
                    </td>
                    <td><input type="text" name="resp_cierre_<?= $i ?>" value="<?= oldv("resp_cierre_$i") ?>"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </form>
</div>

<script>
function actualizarColores(select) {
    const td = select.parentElement;
    td.className = ''; // Limpiar clases
    if (select.value === 'ABIERTA') td.classList.add('status-abierta');
    if (select.value === 'CERRADA') td.classList.add('status-cerrada');
    contarEstatus();
}

function contarEstatus() {
    let abiertas = 0;
    let cerradas = 0;
    document.querySelectorAll('.status-selector').forEach(sel => {
        if (sel.value === 'ABIERTA') abiertas++;
        if (sel.value === 'CERRADA') cerradas++;
    });
    document.getElementById('countAbiertas').innerText = abiertas;
    document.getElementById('countCerradas').innerText = cerradas;
}

// Inicializar colores al cargar
document.querySelectorAll('.status-selector').forEach(sel => actualizarColores(sel));

document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formMatriz');
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
            Swal.fire('¡Éxito!', 'La matriz se ha guardado correctamente.', 'success');
        } else {
            Swal.fire('Error', res.error || 'No se pudo completar la operación.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión con el servidor.', 'error');
    } finally {
        btn.innerText = 'Guardar Matriz'; btn.disabled = false;
    }
});
</script>

</body>
</html>