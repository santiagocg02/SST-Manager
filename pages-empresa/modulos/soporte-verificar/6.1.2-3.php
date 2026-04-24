<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// ID para Plan de Auditoría (Ajustar según tu base de datos)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 51; 

// --- Lógica de Logo ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// --- Carga de Datos ---
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? null;
if (is_string($camposCrudos)) $datosCampos = json_decode($camposCrudos, true) ?: [];
elseif (is_array($camposCrudos)) $datosCampos = $camposCrudos;

$filas = 10; // Cantidad base de filas de agenda
if (!empty($datosCampos)) {
    foreach ($datosCampos as $key => $v) {
        if (preg_match('/^item_(\d+)$/', $key, $matches)) {
            $num = (int)$matches[1];
            if ($num > $filas) $filas = $num;
        }
    }
}

function oldv($key, $default = '') {
    global $datosCampos;
    return isset($datosCampos[$key]) ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plan de Auditoría - Estilo Corregido</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *{ box-sizing:border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
        body{ background:#f2f4f7; padding:10px; }
        .contenedor{ max-width:1100px; background:#fff; border:1px solid #000; margin:0 auto; padding: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        
        /* Toolbar limpia sin color de fondo azul */
        .toolbar{ display:flex; justify-content:space-between; align-items:center; padding:10px 5px; border-bottom: 1px solid #ccc; margin-bottom: 15px; }
        .titulo-form{ font-size: 16px; font-weight: bold; color: #333; }
        
        .acciones{ display:flex; gap: 8px; }
        .btn{ border:none; padding:8px 16px; border-radius:4px; font-weight:bold; cursor:pointer; font-size: 13px; }
        .btn-guardar{ background:#198754; color:#fff; }
        .btn-guardar:hover{ background:#157347; }
        .btn-imprimir{ background:#0d6efd; color:#fff; }
        .btn-imprimir:hover{ background:#0b5ed7; }
        .btn-add{ background:#213b67; color:#fff; margin-top: 10px; }

        table{ width:100%; border-collapse:collapse; table-layout: fixed; margin-bottom: -1px; }
        th, td{ border:1px solid #000; padding:6px; font-size:12px; vertical-align: middle; }
        
        /* Color azul claro INSTITUCIONAL para cabeceras de agenda */
        .bg-blue-light { background: #dde7f5; font-weight: bold; text-align: center; }
        
        input[type="text"], textarea { 
            width: 100%; border: none; outline: none; background: transparent; 
            font-size: 12px; display: block; padding: 2px;
        }
        textarea { resize: vertical; min-height: 25px; font-family: inherit; line-height: 1.4; }

        .label-bold { font-weight: bold; background: #f2f2f2; width: 220px; text-align: left; }
        
        /* Anchos específicos agenda */
        .col-fecha { width: 90px; }
        .col-hora { width: 110px; }
        .col-auditado { width: 200px; }
        .col-auditor { width: 160px; }
        
        /* Texto verde para Auditado */
        .text-verde { color: #28a745; font-weight: bold; }

        @media print { 
            .print-hide { display: none !important; } 
            body { padding: 0; background: #fff; }
            .contenedor { border: none; box-shadow: none; max-width: 100%; padding: 0; }
            textarea { resize: none; overflow: hidden; }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h2 class="titulo-form">FORMATO: PLAN DE AUDITORÍA</h2>
        <div class="acciones">
            <button class="btn btn-guardar" id="btnGuardar">Guardar Plan</button>
            <button class="btn btn-imprimir" onclick="window.print()">Imprimir PDF</button>
        </div>
    </div>

    <form id="formPlanAuditoria">
        <table>
            <tr>
                <td rowspan="2" style="width:200px; text-align: center; padding: 5px;">
                    <?php if(!empty($logoEmpresaUrl)): ?>
                        <img src="<?= $logoEmpresaUrl ?>" style="max-height:55px;">
                    <?php else: ?>
                        <div style="color: #999; font-weight: bold; font-size: 10px; border: 1px dashed #ccc; padding: 10px;">LOGO</div>
                    <?php endif; ?>
                </td>
                <td style="font-weight:bold; font-size:13px; text-align: center; text-transform: uppercase;">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td style="width:120px; text-align: center;">0</td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align: center; text-transform: uppercase;">PLAN DE AUDITORIA</td>
                <td style="text-align: center; font-size: 11px;">RE-SST-19<br>XX/XX/2025</td>
            </tr>
        </table>

        <table style="margin-top: -1px;">
            <tr>
                <td colspan="2"><textarea name="objetivo" placeholder="OBJETIVO: Escriba aquí el objetivo..."><?= oldv('objetivo') ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2"><textarea name="alcance" placeholder="ALCANCE: Escriba aquí el alcance..."><?= oldv('alcance') ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2"><textarea name="referencia" placeholder="DOCUMENTOS DE REFERENCIA: Escriba aquí las referencias..."><?= oldv('referencia') ?></textarea></td>
            </tr>
            <tr>
                <td class="label-bold">AUDITOR LÍDER :</td>
                <td><input type="text" name="auditor_lider" value="<?= oldv('auditor_lider') ?>"></td>
            </tr>
            <tr>
                <td class="label-bold">AUDITOR (es) ACOMPAÑANTE (s) :</td>
                <td><input type="text" name="auditor_acompa" value="<?= oldv('auditor_acompa', 'Por definir') ?>"></td>
            </tr>
            <tr>
                <td class="label-bold">FECHA:</td>
                <td><input type="text" name="fecha_general" value="<?= oldv('fecha_general') ?>"></td>
            </tr>
            <tr>
                <td class="label-bold">LUGAR:</td>
                <td><input type="text" name="lugar_general" value="<?= oldv('lugar_general') ?>"></td>
            </tr>
            <tr>
                <td class="label-bold">HORA REUNIÓN APERTURA:</td>
                <td><input type="text" name="hora_apertura" value="<?= oldv('hora_apertura') ?>"></td>
            </tr>
            <tr>
                <td class="label-bold">HORA REUNIÓN DE CIERRE:</td>
                <td><input type="text" name="hora_cierre" value="<?= oldv('hora_cierre') ?>"></td>
            </tr>
        </table>

        <table style="margin-top: 10px;" id="tablaAgenda">
            <thead>
                <tr class="bg-blue-light">
                    <th class="col-fecha">FECHA</th>
                    <th class="col-hora">HORA</th>
                    <th>ITEM</th>
                    <th class="col-auditado">AUDITADO</th>
                    <th class="col-auditor">AUDITOR</th>
                </tr>
            </thead>
            <tbody id="tbodyAgenda">
                <?php for ($i = 1; $i <= $filas; $i++): ?>
                <tr>
                    <td><input type="text" name="fec_<?= $i ?>" value="<?= oldv("fec_$i") ?>" style="text-align: center;"></td>
                    <td><input type="text" name="hor_<?= $i ?>" value="<?= oldv("hor_$i") ?>" style="text-align: center;"></td>
                    <td><textarea name="item_<?= $i ?>"><?= oldv("item_$i") ?></textarea></td>
                    <td><textarea name="auditado_<?= $i ?>" class="text-verde"><?= oldv("auditado_$i") ?></textarea></td>
                    <td><input type="text" name="auditor_col_<?= $i ?>" value="<?= oldv("auditor_col_$i") ?>"></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        
        <div class="print-hide">
            <button type="button" class="btn btn-add" onclick="agregarFilaAgenda()">+ AGREGAR ITEM A LA AGENDA</button>
        </div>
    </form>
</div>

<script>
let contadorAgenda = <?= $filas ?>;

function agregarFilaAgenda() {
    contadorAgenda++;
    const tbody = document.getElementById('tbodyAgenda');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="fec_${contadorAgenda}" style="text-align: center;"></td>
        <td><input type="text" name="hor_${contadorAgenda}" style="text-align: center;"></td>
        <td><textarea name="item_${contadorAgenda}"></textarea></td>
        <td><textarea name="auditado_${contadorAgenda}" class="text-verde"></textarea></td>
        <td><input type="text" name="auditor_col_${contadorAgenda}"></td>
    `;
    tbody.appendChild(tr);
}

// Lógica de guardado vía API Fetch
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formPlanAuditoria');
    const formData = new FormData(form);
    const datosJSON = Object.fromEntries(formData.entries());

    btn.innerText = 'Guardando...';
    btn.disabled = true;

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

        const res = await response.json();
        if (res.ok) {
            Swal.fire('¡Guardado!', 'El plan de auditoría se ha actualizado correctamente.', 'success');
        } else {
            Swal.fire('Error', res.error || 'No se pudo completar la operación.', 'error');
        }
    } catch (e) {
        Swal.fire('Error de Conexión', 'No se pudo contactar al servidor.', 'error');
    } finally {
        btn.innerText = 'Guardar Plan';
        btn.disabled = false;
    }
});
</script>

</body>
</html>