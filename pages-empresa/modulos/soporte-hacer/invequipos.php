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

function oldv($key, $default = '') {
    global $datosCampos;
    return isset($datosCampos[$key]) ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario Equipos Emergencia | SST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --sst-toolbar: #dde7f5;
            --sst-toolbar-border: #c8d3e2;
            --line: #000;
            --primary-blue: #1a4175;
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
            width: 1400px; margin: 20px auto; background: #fff;
            border: 1px solid #ccc; padding: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .head-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .head-table td { border: 1px solid var(--line); padding: 5px; text-align: center; }
        
        .inv-table { width: 100%; border-collapse: collapse; }
        .inv-table th, .inv-table td { border: 1px solid var(--line); padding: 4px; text-align: center; }
        
        .bg-blue-header { background: #5b9bd5; color: #fff; font-weight: bold; }
        .bg-grey-header { background: #aeaaaa; font-weight: bold; }
        .bg-light-grey { background: #d9d9d9; font-weight: bold; }

        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 10px; text-align: center; }
        .btn-add { background: #213b67; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-bottom: 10px; font-weight: bold; }

        @media print {
            .sst-toolbar, .no-print, .btn-add, .btn-remove { display: none !important; }
            body { background: #fff; padding: 0; }
            .sheet { border: none; box-shadow: none; width: 100%; margin: 0; padding: 5px; }
            input[type="date"] { border:none; }
        }
    </style>
</head>
<body>

<div class="sst-toolbar no-print">
    <h1 class="sst-toolbar-title">Inventario General de Equipos de Emergencias</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">Volver</button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="sheet">
    <form id="formInvEquipos">
        <table class="head-table">
            <tr>
                <td rowspan="2" style="width: 20%;">
                    <?php if ($logoEmpresaUrl): ?> <img src="<?= $logoEmpresaUrl ?>" style="max-height: 50px;"> <?php endif; ?>
                </td>
                <td colspan="2" style="font-weight: bold; font-size: 14px;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td style="width: 15%; font-weight: bold;">0</td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; font-size: 13px;">INVENTARIO GENERAL DE EQUIPOS Y ELEMENTOS DE EMERGENCIAS</td>
                <td style="font-size: 11px;">RE-SST-34<br>XX/XX/2025</td>
            </tr>
        </table>

        <div style="margin: 10px 0;">
            <strong>Fecha de actualización:</strong> 
            <input type="date" name="fecha_actualizacion" value="<?= oldv('fecha_actualizacion', date('Y-m-d')) ?>" style="border:none; border-bottom:1px solid #000; outline:none;">
        </div>

        <button type="button" class="btn-add no-print" onclick="addRow()"><i class="fa-solid fa-plus"></i> Agregar Elemento</button>

        <table class="inv-table" id="tablaEquipos">
            <thead>
                <tr>
                    <th colspan="3" class="bg-blue-header">VEHICULO / PUESTO DE CONTROL</th>
                    <th colspan="3" class="bg-grey-header">DATOS DEL ELEMENTO</th>
                    <th colspan="3" class="bg-light-grey">EQUIPOS O ELEMENTO UTILIZADO:<br><small>Marcar X</small></th>
                    <th colspan="3" class="bg-light-grey">ESTADO</th>
                    <th rowspan="2" class="bg-light-grey">OBSERVACIONES</th>
                    <th rowspan="2" class="no-print" style="width: 40px; border:none; background:transparent;"></th>
                </tr>
                <tr>
                    <th style="width: 100px;">Ubicación</th>
                    <th style="width: 80px;">Ciudad</th>
                    <th style="width: 120px;">Dirección</th>
                    <th>Equipo o elemento</th>
                    <th style="width: 60px;">Cantidad</th>
                    <th>Ubicación Exacta</th>
                    <th style="width: 50px;">Primeros Auxilios</th>
                    <th style="width: 50px;">Contra incendios</th>
                    <th style="width: 50px;">Evacuación</th>
                    <th style="width: 45px;">Bueno</th>
                    <th style="width: 45px;">Regular</th>
                    <th style="width: 45px;">En mal estado</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </form>
</div>

<script>
function addRow(data = {}) {
    const tbody = document.querySelector("#tablaEquipos tbody");
    const tr = document.createElement("tr");
    
    tr.innerHTML = `
        <td><input type="text" name="ubicacion[]" class="editable" value="${data.ubicacion || ''}"></td>
        <td><input type="text" name="ciudad[]" class="editable" value="${data.ciudad || ''}"></td>
        <td><input type="text" name="direccion[]" class="editable" value="${data.direccion || ''}"></td>
        <td><input type="text" name="equipo[]" class="editable" value="${data.equipo || ''}"></td>
        <td><input type="number" name="cantidad[]" class="editable" value="${data.cantidad || ''}"></td>
        <td><input type="text" name="ubic_exacta[]" class="editable" value="${data.ubic_exacta || ''}"></td>
        <td><input type="text" name="pa[]" class="editable" value="${data.pa || ''}" maxlength="1"></td>
        <td><input type="text" name="ci[]" class="editable" value="${data.ci || ''}" maxlength="1"></td>
        <td><input type="text" name="evac[]" class="editable" value="${data.evac || ''}" maxlength="1"></td>
        <td><input type="text" name="est_b[]" class="editable" value="${data.est_b || ''}" maxlength="1"></td>
        <td><input type="text" name="est_r[]" class="editable" value="${data.est_r || ''}" maxlength="1"></td>
        <td><input type="text" name="est_m[]" class="editable" value="${data.est_m || ''}" maxlength="1"></td>
        <td><textarea name="obs[]" class="editable" rows="1">${data.obs || ''}</textarea></td>
        <td class="no-print"><button type="button" class="btn btn-link text-danger btn-remove p-0" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

// Cargar datos existentes o fila vacía
document.addEventListener('DOMContentLoaded', () => {
    const datos = <?= json_encode($datosCampos) ?>;
    if (datos && datos.equipo && datos.equipo.length > 0) {
        datos.equipo.forEach((_, i) => {
            let item = {};
            for (let key in datos) { if(Array.isArray(datos[key])) item[key] = datos[key][i]; }
            addRow(item);
        });
    } else {
        addRow();
    }
});

// Lógica de Guardado
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formInvEquipos');
    const formData = new FormData(form);
    const datosJSON = {};

    for (const [key, value] of formData.entries()) {
        if (key.endsWith('[]')) {
            const cleanKey = key.replace('[]', '');
            if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
            datosJSON[cleanKey].push(value);
        } else {
            datosJSON[key] = value;
        }
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
        if (res.ok) Swal.fire('Guardado', 'Inventario actualizado correctamente', 'success');
        else Swal.fire('Error', 'No se pudo completar la operación', 'error');
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