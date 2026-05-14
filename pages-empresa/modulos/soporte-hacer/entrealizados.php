<?php
session_start();
require_once __DIR__ . '/../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token   = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem  = isset($_GET['item']) ? (int)$_GET['item'] : 0;

// LOGO DINÁMICO
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// CARGA DE DATOS EXISTENTES
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? null;

if (is_string($camposCrudos)) { $datosCampos = json_decode($camposCrudos, true) ?: []; }
elseif (is_array($camposCrudos)) { $datosCampos = $camposCrudos; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrenamientos Realizados | SST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --sst-toolbar: #dde7f5;
            --sst-toolbar-border: #c8d3e2;
            --line: #000;
            --primary-blue: #1a4175;
            --brigada-green: #70ad47; /* Color verde de tu imagen de referencia */
        }

        body { background: #f2f4f7; font-family: Arial, sans-serif; font-size: 11px; margin: 0; }

        /* TOOLBAR STICKY */
        .sst-toolbar {
            position: sticky; top: 0; z-index: 1000;
            background: var(--sst-toolbar);
            border-bottom: 1px solid var(--sst-toolbar-border);
            padding: 12px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .sst-toolbar-title { margin: 0; font-size: 15px; font-weight: 800; color: var(--primary-blue); text-transform: uppercase; }

        /* HOJA DE FORMATO */
        .sheet {
            width: 1200px; margin: 20px auto; background: #fff;
            border: 1px solid #ccc; padding: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .head-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .head-table td { border: 1px solid var(--line); padding: 5px; text-align: center; }
        
        /* ESTILOS TABLA VERDE (image_261820.png) */
        .training-table { width: 100%; border-collapse: collapse; }
        .training-table th, .training-table td { border: 1px solid var(--line); padding: 6px; text-align: center; }
        .training-table th { background: var(--brigada-green); color: #fff; font-weight: bold; vertical-align: middle; }
        
        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 11px; text-align: center; }
        .btn-add { background: var(--primary-blue); color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-bottom: 10px; font-weight: bold; }

        .pct-cell { font-weight: bold; background: #f9f9f9; width: 50px; }

        @media print {
            .sst-toolbar, .no-print, .btn-add, .btn-remove { display: none !important; }
            body { background: #fff; padding: 0; }
            .sheet { border: none; box-shadow: none; width: 100%; margin: 0; padding: 5px; }
        }
    </style>
</head>
<body>

<div class="sst-toolbar no-print">
    <h1 class="sst-toolbar-title">Seguimiento de Entrenamientos y Capacitaciones</h1>
    <div class="sst-toolbar-actions d-flex gap-2">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">Volver</button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="sheet">
    <form id="formEntrenamientos">
        <table class="head-table">
            <tr>
                <td rowspan="2" style="width: 200px;">
                    <?php if ($logoEmpresaUrl): ?> <img src="<?= $logoEmpresaUrl ?>" style="max-height: 50px;"> <?php endif; ?>
                </td>
                <td style="font-weight: bold; font-size: 14px;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td style="width: 150px; font-weight: bold;">Versión: 01</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-size: 13px;">INDICADORES DE ENTRENAMIENTO DE BRIGADA</td>
                <td style="font-size: 11px;">RE-SST-40<br><?= date('d/m/Y') ?></td>
            </tr>
        </table>

        <button type="button" class="btn-add no-print" onclick="addRow()"><i class="fa-solid fa-plus"></i> Agregar Capacitación</button>

        <table class="training-table" id="tablaEntrenamientos">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 40px;">No.</th>
                    <th rowspan="2">ENTRENAMIENTOS Y CAPACITACIONES</th>
                    <th rowspan="2" style="width: 100px;">FECHA DE REALIZACIÓN</th>
                    <th colspan="3">Cumplimiento</th>
                    <th colspan="3">Eficacia</th>
                    <th colspan="3">Cobertura</th>
                    <th rowspan="2" class="no-print" style="width: 40px; background:white; border:none;"></th>
                </tr>
                <tr>
                    <th style="width: 70px;">Programada</th>
                    <th style="width: 70px;">Ejecutada</th>
                    <th style="width: 50px;">%</th>
                    <th style="width: 70px;">Evaluados</th>
                    <th style="width: 70px;">Eficaces</th>
                    <th style="width: 50px;">%</th>
                    <th style="width: 70px;">Convocados</th>
                    <th style="width: 70px;">Capacitados</th>
                    <th style="width: 50px;">%</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </form>
</div>

<script>
function addRow(data = {}) {
    const tbody = document.querySelector("#tablaEntrenamientos tbody");
    const rowCount = tbody.rows.length + 1;
    const tr = document.createElement("tr");
    
    tr.innerHTML = `
        <td>${rowCount}</td>
        <td><input type="text" name="tema[]" class="editable" value="${data.tema || ''}" style="text-align:left; padding-left:10px;"></td>
        <td><input type="date" name="fecha[]" class="editable" value="${data.fecha || ''}"></td>
        
        <td><input type="number" name="c_prog[]" class="editable calc" value="${data.c_prog || ''}" oninput="updatePct(this)"></td>
        <td><input type="number" name="c_ejec[]" class="editable calc" value="${data.c_ejec || ''}" oninput="updatePct(this)"></td>
        <td class="pct-cell">0%</td>
        
        <td><input type="number" name="e_eval[]" class="editable calc" value="${data.e_eval || ''}" oninput="updatePct(this)"></td>
        <td><input type="number" name="e_efic[]" class="editable calc" value="${data.e_efic || ''}" oninput="updatePct(this)"></td>
        <td class="pct-cell">0%</td>
        
        <td><input type="number" name="b_conv[]" class="editable calc" value="${data.b_conv || ''}" oninput="updatePct(this)"></td>
        <td><input type="number" name="b_capa[]" class="editable calc" value="${data.b_capa || ''}" oninput="updatePct(this)"></td>
        <td class="pct-cell">0%</td>
        
        <td class="no-print"><button type="button" class="btn btn-link text-danger p-0" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    // Calcular porcentajes si hay datos cargados
    const inputs = tr.querySelectorAll('.calc');
    if(inputs.length > 0) updatePct(inputs[0]);
}

function updatePct(el) {
    const tr = el.closest('tr');
    const cells = tr.querySelectorAll('td');
    
    const calculate = (val1, val2, targetCellIndex) => {
        const v1 = parseFloat(val1) || 0;
        const v2 = parseFloat(val2) || 0;
        const pct = v1 > 0 ? Math.round((v2 / v1) * 100) : 0;
        cells[targetCellIndex].innerText = pct + '%';
    };

    // Cumplimiento: Ejecutada (cell 4) / Programada (cell 3) -> Result cell 5
    calculate(tr.querySelector('[name="c_prog[]"]').value, tr.querySelector('[name="c_ejec[]"]').value, 5);
    // Eficacia: Eficaces (cell 7) / Evaluados (cell 6) -> Result cell 8
    calculate(tr.querySelector('[name="e_eval[]"]').value, tr.querySelector('[name="e_efic[]"]').value, 8);
    // Cobertura: Capacitados (cell 10) / Convocados (cell 9) -> Result cell 11
    calculate(tr.querySelector('[name="b_conv[]"]').value, tr.querySelector('[name="b_capa[]"]').value, 11);
}

document.addEventListener('DOMContentLoaded', () => {
    const datos = <?= json_encode($datosCampos) ?>;
    if (datos && datos.tema && datos.tema.length > 0) {
        datos.tema.forEach((_, i) => {
            let item = {};
            for (let key in datos) { if(Array.isArray(datos[key])) item[key] = datos[key][i]; }
            addRow(item);
        });
    } else {
        addRow({tema: 'Conformación de la Brigada'}); // Fila de ejemplo
    }
});

document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formEntrenamientos');
    const formData = new FormData(form);
    const datosJSON = {};

    for (const [key, value] of formData.entries()) {
        const cleanKey = key.replace('[]', '');
        if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
        datosJSON[cleanKey].push(value);
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

    try {
        const response = await fetch("../../../public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresa ?>, id_item_sst: <?= $idItem ?>, datos: datosJSON })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('¡Éxito!', 'Indicadores actualizados correctamente', 'success');
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar';
    }
});
</script>
</body>
</html>