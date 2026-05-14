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

// LOGO Y DATOS EMPRESA
$logoEmpresaUrl = "";
$nombreEmpresa = "NOMBRE DE LA EMPRESA";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
        $nombreEmpresa = $resEmpresa['data'][0]['nombre_empresa'] ?? $nombreEmpresa;
    }
}

// CARGA DE DATOS
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Chequeo Simulacro | SST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --sst-toolbar: #dde7f5;
            --sst-toolbar-border: #c8d3e2;
            --line: #000;
            --primary-blue: #213b67;
        }

        /* RESET RESPONSIVO */
        html, body { margin: 0; padding: 0; width: 100%; overflow-x: hidden; background: #f4f7fa; font-family: Arial, sans-serif; font-size: 11px; }

        /* TOOLBAR STICKY (Ajustada) */
        .sst-toolbar {
            position: sticky; top: 0; z-index: 1000;
            background: var(--sst-toolbar);
            border-bottom: 1px solid var(--sst-toolbar-border);
            padding: 10px 15px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px;
        }
        .sst-toolbar-title { margin: 0; font-size: 14px; font-weight: 800; color: var(--primary-blue); text-transform: uppercase; }

        /* HOJA FLUIDA */
        .sheet {
            width: 95%; max-width: 1000px; margin: 20px auto; background: #fff;
            border: 1px solid #ccc; padding: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        /* CONTENEDOR DE TABLAS (Evita scroll lateral de la página) */
        .table-responsive-container { width: 100%; overflow-x: auto; margin-bottom: 15px; -webkit-overflow-scrolling: touch; }

        table { width: 100%; border-collapse: collapse; min-width: 650px; }
        th, td { border: 1px solid var(--line); padding: 5px; vertical-align: middle; }
        
        .bg-blue-header { background: #0000FF; color: white; text-align: center; font-weight: bold; text-transform: uppercase; }
        .bg-grey-soft { background: #e9ecef; font-weight: bold; text-align: center; }
        
        .editable { width: 100%; border: none; outline: none; background: transparent; font-size: 11px; }
        .center { text-align: center; }

        @media print {
            .sst-toolbar, .no-print { display: none !important; }
            body { background: #fff; padding: 0; }
            .sheet { border: none; box-shadow: none; width: 100%; max-width: 100%; margin: 0; padding: 0; }
            .table-responsive-container { overflow: visible; }
        }
    </style>
</head>
<body>

<div class="sst-toolbar no-print">
    <h1 class="sst-toolbar-title">Lista de Chequeo de Simulacro</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-secondary btn-sm" onclick="history.back()">Volver</button>
        <button type="button" class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
    </div>
</div>

<div class="sheet">
    <form id="formSimulacro">
        <div class="table-responsive-container">
            <table>
                <tr>
                    <td rowspan="2" style="width: 200px; text-align: center;">
                        <?php if ($logoEmpresaUrl): ?> <img src="<?= $logoEmpresaUrl ?>" style="max-height: 50px; max-width: 100%;"> <?php endif; ?>
                    </td>
                    <td class="center" style="font-weight: bold; font-size: 13px;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width: 120px; text-align: center; font-weight: bold;">Versión: 01</td>
                </tr>
                <tr>
                    <td class="center" style="font-weight: bold; font-size: 12px;">LISTA DE CHEQUEO DE SIMULACRO (RE-SST-31)</td>
                    <td class="center"><?= date('d/m/Y') ?></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive-container">
            <table>
                <tr><td colspan="6" style="background:#eee;"><strong>EMPRESA:</strong> <?= $nombreEmpresa ?></td></tr>
                <tr>
                    <td style="background:#f8f9fa; width:15%;">INICIO:</td>
                    <td><input type="time" name="h_ini" class="editable" value="<?= oldv('h_ini') ?>"></td>
                    <td style="background:#f8f9fa; width:15%;">FIN:</td>
                    <td><input type="time" name="h_fin" class="editable" value="<?= oldv('h_fin') ?>"></td>
                    <td style="background:#f8f9fa; width:15%;">TOTAL:</td>
                    <td><input type="text" name="t_total" class="editable" value="<?= oldv('t_total') ?>" placeholder="min:seg"></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive-container">
            <table>
                <tr class="bg-grey-soft"><th>ITEM A EVALUAR</th><th style="width:50px;">SI</th><th style="width:50px;">NO</th></tr>
                <tr><td>¿La realización del simulacro fue a la hora indicada?</td>
                    <td class="center"><input type="radio" name="p1" value="si" <?= oldv('p1')=='si'?'checked':'' ?>></td>
                    <td class="center"><input type="radio" name="p1" value="no" <?= oldv('p1')=='no'?'checked':'' ?>></td></tr>
                <tr><td>¿El personal desalojó de manera ordenada y segura?</td>
                    <td class="center"><input type="radio" name="p2" value="si" <?= oldv('p2')=='si'?'checked':'' ?>></td>
                    <td class="center"><input type="radio" name="p2" value="no" <?= oldv('p2')=='no'?'checked':'' ?>></td></tr>
            </table>
        </div>

        <div class="bg-blue-header" style="padding:5px; margin-top:10px;">Evaluación de Brigadistas</div>
        
        <div class="table-responsive-container">
            <table>
                <?php 
                $items = ["¿Portan identificación?", "¿Hicieron censo?", "¿Instrucciones claras?", "¿Guiaron adecuadamente?"];
                foreach($items as $i => $item): ?>
                <tr>
                    <td><?= $item ?></td>
                    <td style="width:50px;" class="center"><input type="radio" name="b<?= $i ?>" value="si" <?= oldv('b'.$i)=='si'?'checked':'' ?>></td>
                    <td style="width:50px;" class="center"><input type="radio" name="b<?= $i ?>" value="no" <?= oldv('b'.$i)=='no'?'checked':'' ?>></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="margin-top:10px;">
            <strong>Observaciones / Descripción de la zona al finalizar:</strong>
            <textarea name="obs" class="editable" style="border:1px solid #000; height:80px; margin-top:5px; padding:8px;"><?= oldv('obs') ?></textarea>
        </div>
    </form>
</div>

<script>
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = Object.fromEntries(new FormData(document.getElementById('formSimulacro')).entries());
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    try {
        const response = await fetch("../../../public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresa ?>, id_item_sst: <?= $idItem ?>, datos: formData })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('¡Éxito!', 'Datos guardados', 'success');
    } catch (e) { Swal.fire('Error', 'Fallo de conexión', 'error'); } 
    finally { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar'; }
});
</script>
</body>
</html>