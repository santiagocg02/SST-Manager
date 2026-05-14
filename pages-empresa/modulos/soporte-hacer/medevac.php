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
$nombreSST = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $empData = $resEmpresa['data'][0];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        $nombreSST = $empData['nombre_sst'] ?? $empData['responsable_sst'] ?? '';
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
    <title>MEDEVAC | Plan de Evacuación Médica</title>
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

        body { background: #f2f4f7; font-family: Arial, sans-serif; font-size: 12px; margin: 0; }

        /* TOOLBAR INSTITUCIONAL */
        .sst-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--sst-toolbar);
            border-bottom: 1px solid var(--sst-toolbar-border);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .sst-toolbar-title { margin: 0; font-size: 16px; font-weight: 800; color: var(--primary-blue); text-transform: uppercase; }
        .sst-toolbar-actions { display: flex; gap: 10px; }

        /* HOJA DE FORMATO */
        .sheet {
            width: 1000px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .head-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .head-table td { border: 1px solid var(--line); padding: 8px; text-align: center; }
        .bg-blue-soft { background: #cfe2f7; font-weight: bold; }

        .section-title {
            background: var(--primary-blue);
            color: #fff;
            padding: 8px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            text-transform: uppercase;
        }

        .medevac-table { width: 100%; border-collapse: collapse; }
        .medevac-table th, .medevac-table td { border: 1px solid var(--line); padding: 8px; vertical-align: middle; }
        
        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 12px; }
        textarea.editable { resize: vertical; min-height: 40px; }

        @media print {
            .sst-toolbar, .no-print { display: none !important; }
            body { background: #fff; padding: 0; }
            .sheet { border: none; box-shadow: none; width: 100%; margin: 0; padding: 10px; }
        }
    </style>
</head>
<body>

<div class="sst-toolbar">
    <h1 class="sst-toolbar-title">Plan de Evacuación Médica (MEDEVAC)</h1>
    <div class="sst-toolbar-actions">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i> Atrás
        </button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar">
            <i class="fa-solid fa-save"></i> Guardar
        </button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>
</div>

<div class="sheet">
    <form id="formMedevac">
        <table class="head-table">
            <tr>
                <td rowspan="2" style="width: 200px;">
                    <?php if ($logoEmpresaUrl): ?>
                        <img src="<?= $logoEmpresaUrl ?>" style="max-height: 60px;">
                    <?php else: ?>
                        <span style="color:#999; font-size:10px;">SIN LOGO</span>
                    <?php endif; ?>
                </td>
                <td class="bg-blue-soft">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td style="width: 150px; font-weight:bold;">Versión: 02</td>
            </tr>
            <tr>
                <td class="bg-blue-soft">PROCEDIMIENTO DE RESPUESTA MÉDICA - MEDEVAC</td>
                <td style="font-size:11px;">FR-SST-18<br>Fecha: <?= date('d/m/Y') ?></td>
            </tr>
        </table>

        <div class="section-title">Información del Centro de Trabajo</div>
        <table class="medevac-table">
            <tr>
                <th class="bg-blue-soft" style="width: 200px;">Ubicación / Proyecto:</th>
                <td><input name="ubicacion" class="editable" value="<?= oldv('ubicacion') ?>" placeholder="Nombre de la sede o proyecto"></td>
                <th class="bg-blue-soft" style="width: 150px;">Responsable:</th>
                <td><input name="responsable" class="editable" value="<?= oldv('responsable', $nombreSST) ?>"></td>
            </tr>
        </table>

        <div class="section-title">Centros Asistenciales y de Emergencia</div>
        <table class="medevac-table">
            <tr class="bg-blue-soft">
                <th>Entidad / Centro Médico</th>
                <th>Nivel</th>
                <th>Dirección</th>
                <th>Teléfono de Emergencia</th>
                <th>Tiempo Estimado</th>
            </tr>
            <?php for($i=1; $i<=3; $i++): ?>
            <tr>
                <td><input name="entidad_<?= $i ?>" class="editable" value="<?= oldv('entidad_'.$i) ?>"></td>
                <td><input name="nivel_<?= $i ?>" class="editable" value="<?= oldv('nivel_'.$i) ?>" style="text-align:center;"></td>
                <td><input name="dir_<?= $i ?>" class="editable" value="<?= oldv('dir_'.$i) ?>"></td>
                <td><input name="tel_<?= $i ?>" class="editable" value="<?= oldv('tel_'.$i) ?>" style="text-align:center;"></td>
                <td><input name="tiempo_<?= $i ?>" class="editable" value="<?= oldv('tiempo_'.$i) ?>" style="text-align:center;"></td>
            </tr>
            <?php endfor; ?>
        </table>

        <div class="section-title">Protocolo de Comunicación (En caso de Accidente)</div>
        <table class="medevac-table">
            <tr class="bg-blue-soft" style="text-align:center;">
                <th style="width: 50px;">PASO</th>
                <th>ACCIÓN A REALIZAR</th>
                <th>CONTACTO / CARGO</th>
            </tr>
            <tr>
                <td style="text-align:center;">1</td>
                <td>Asegurar el área y prestar primeros auxilios básicos.</td>
                <td>Primer respondiente / Brigadista</td>
            </tr>
            <tr>
                <td style="text-align:center;">2</td>
                <td>Comunicar al Jefe de SST o Coordinador de Emergencias.</td>
                <td><input name="contacto_paso_2" class="editable" value="<?= oldv('contacto_paso_2', $nombreSST) ?>"></td>
            </tr>
            <tr>
                <td style="text-align:center;">3</td>
                <td>Llamar a la línea de emergencias o ambulancia.</td>
                <td><input name="contacto_paso_3" class="editable" value="<?= oldv('contacto_paso_3', 'Línea 123 / ARL') ?>"></td>
            </tr>
        </table>

        <div class="section-title">Observaciones Especiales</div>
        <div style="border: 1px solid #000; padding: 10px;">
            <textarea name="observaciones" class="editable" style="min-height: 80px;"><?= oldv('observaciones', 'Indicar rutas alternas, convenios específicos con clínicas o tipos de transporte disponibles.') ?></textarea>
        </div>
    </form>
</div>

<script>
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = Object.fromEntries(new FormData(document.getElementById('formMedevac')).entries());
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

    try {
        const response = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ 
                id_empresa: <?= $empresa ?>, 
                id_item_sst: <?= $idItem ?>, 
                datos: formData 
            })
        });
        const res = await response.json();
        if (res.ok) {
            Swal.fire('¡Éxito!', 'Plan Medevac actualizado correctamente', 'success');
        } else {
            Swal.fire('Error', res.error || 'No se pudo guardar', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión con el backend', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar';
    }
});
</script>
</body>
</html>