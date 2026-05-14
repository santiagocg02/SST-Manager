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

// LOGO Y DATOS DE EMPRESA
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
    <title>SST Manager | Registro MEDEVAC</title>
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
            width: 1300px; margin: 20px auto; background: #fff;
            border: 1px solid #ccc; padding: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .head-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .head-table td { border: 1px solid var(--line); padding: 5px; text-align: center; }
        .bg-grey { background: #d9d9d9; font-weight: bold; }

        .reg-table { width: 100%; border-collapse: collapse; }
        .reg-table th, .reg-table td { border: 1px solid var(--line); padding: 4px; text-align: center; }
        .reg-table th { background: #bfbfbf; vertical-align: middle; }
        
        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 10px; text-align: center; }
        
        .btn-add { background: #213b67; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-bottom: 10px; }

        @media print {
            .sst-toolbar, .no-print, .btn-add, .btn-remove { display: none !important; }
            body { background: #fff; padding: 0; }
            .sheet { border: none; box-shadow: none; width: 100%; margin: 0; padding: 5px; }
        }
    </style>
</head>
<body>

<div class="sst-toolbar no-print">
    <h1 class="sst-toolbar-title">Registro Datos del Personal - MEDEVAC</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">Volver</button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="sheet">
    <form id="formRegMedevac">
        <table class="head-table">
            <tr>
                <td rowspan="2" style="width: 250px;">
                    <?php if ($logoEmpresaUrl): ?> <img src="<?= $logoEmpresaUrl ?>" style="max-height: 50px;"> <?php endif; ?>
                </td>
                <td class="bg-grey" style="font-size: 14px;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td style="width: 150px; font-weight: bold;">Versión: 01</td>
            </tr>
            <tr>
                <td class="bg-grey" style="font-size: 14px;">MEDEVAC - DATOS DEL PERSONAL</td>
                <td style="font-size: 11px;">RE-SST-34<br>XX/XX/2025</td>
            </tr>
        </table>

        <button type="button" class="btn-add no-print" onclick="addRow()"><i class="fa-solid fa-plus"></i> Agregar Fila</button>

        <table class="reg-table" id="tablaPersonal">
            <thead>
                <tr>
                    <th style="width: 30px;">Item</th>
                    <th style="width: 70px;">Fecha</th>
                    <th>Nombre y Apellido</th>
                    <th>Identificación</th>
                    <th>Empresa</th>
                    <th>N° Telefónico</th>
                    <th style="width: 40px;">RH</th>
                    <th>EPS</th>
                    <th>ARL</th>
                    <th>Alergias / Restricciones</th>
                    <th>En caso Emergencia avisar a:</th>
                    <th>N° Telefónico</th>
                    <th style="width: 50px;">Hora Entrada</th>
                    <th style="width: 50px;">Hora Salida</th>
                    <th class="no-print" style="width: 40px;"></th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </form>
</div>

<script>
let rowCount = 0;

function addRow(data = {}) {
    rowCount++;
    const tbody = document.querySelector("#tablaPersonal tbody");
    const tr = document.createElement("tr");
    
    tr.innerHTML = `
        <td>${rowCount}</td>
        <td><input type="date" name="fecha[]" class="editable" value="${data.fecha || ''}"></td>
        <td><input type="text" name="nombre[]" class="editable" value="${data.nombre || ''}"></td>
        <td><input type="text" name="id[]" class="editable" value="${data.id || ''}"></td>
        <td><input type="text" name="empresa[]" class="editable" value="${data.empresa || ''}"></td>
        <td><input type="text" name="tel_p[]" class="editable" value="${data.tel_p || ''}"></td>
        <td><input type="text" name="rh[]" class="editable" value="${data.rh || ''}"></td>
        <td><input type="text" name="eps[]" class="editable" value="${data.eps || ''}"></td>
        <td><input type="text" name="arl[]" class="editable" value="${data.arl || ''}"></td>
        <td><input type="text" name="alergias[]" class="editable" value="${data.alergias || ''}"></td>
        <td><input type="text" name="emergencia_nombre[]" class="editable" value="${data.emergencia_nombre || ''}"></td>
        <td><input type="text" name="emergencia_tel[]" class="editable" value="${data.emergencia_tel || ''}"></td>
        <td><input type="time" name="h_entrada[]" class="editable" value="${data.h_entrada || ''}"></td>
        <td><input type="time" name="h_salida[]" class="editable" value="${data.h_salida || ''}"></td>
        <td class="no-print"><button type="button" class="btn btn-link text-danger btn-remove" onclick="this.closest('tr').remove()"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

// Carga inicial (una fila vacía o datos del backend)
document.addEventListener('DOMContentLoaded', () => {
    const datos = <?= json_encode($datosCampos) ?>;
    if (datos && datos.fecha && datos.fecha.length > 0) {
        datos.fecha.forEach((_, i) => {
            let item = {};
            for (let key in datos) { item[key] = datos[key][i]; }
            addRow(item);
        });
    } else {
        addRow(); // Fila por defecto
    }
});

// Guardar
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formRegMedevac');
    const formData = new FormData(form);
    const datosJSON = {};

    for (const [key, value] of formData.entries()) {
        const cleanKey = key.replace('[]', '');
        if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
        datosJSON[cleanKey].push(value);
    }

    btn.disabled = true;
    try {
        const response = await fetch("../../../public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresa ?>, id_item_sst: <?= $idItem ?>, datos: datosJSON })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('Guardado', 'El registro se actualizó correctamente', 'success');
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión', 'error');
    } finally { btn.disabled = false; }
});
</script>
</body>
</html>