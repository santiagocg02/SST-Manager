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
// Ajusta el ID según la base de datos para este ítem (ej. 55)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 55;

// LOGO
$logoUrl = "";
if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresaId", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// Cargar Datos Previos
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresaId/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? null;

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

function oldv($key, $default = '') {
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] !== '') ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}

// Calcular cantidad de filas dinámicas
$filasMatriz = 0;
if (!empty($datosCampos)) {
    foreach($datosCampos as $k => $v) {
        if (preg_match('/^acpm_(\d+)$/', $k, $matches)) {
            $num = (int)$matches[1];
            if ($num > $filasMatriz) {
                $filasMatriz = $num;
            }
        }
    }
}
// Por lo menos 1 fila al iniciar
if ($filasMatriz === 0) {
    $filasMatriz = 1;
}

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>7.1.3 Matriz de Seguimiento</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --primary:#1a4175; --bg:#dde7f5; }

        body{
          background:#f4f7f9;
          font-family:Segoe UI;
          padding:20px;
        }

        .format-container{
          background:#fff;
          max-width:1400px;
          margin:auto;
          border:1px solid #ccc;
          box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        /* HEADER */
        .toolbar{
          display:flex;
          justify-content:space-between;
          align-items:center;
          padding:10px 20px;
          background:var(--bg);
          border-bottom: 1px solid #c8d3e2;
        }

        .toolbar h1{
          font-size:20px;
          color:var(--primary);
          margin:0;
          font-weight:700;
        }

        /* BOTONES */
        .acciones { display: flex; gap: 10px; }
        .btn{
          padding:8px 15px;
          border:none;
          border-radius:6px;
          color:#fff;
          font-weight:700;
          cursor:pointer;
        }
        .btn:hover{opacity:0.9;}

        .btn-save{ background:#198754; }
        .btn-print{ background:#0d6efd; }
        .btn-back{ background:#6c757d; }
        .btn-add{ background:#0d6efd; }

        /* CONTADORES */
        .box{
          width:45px;
          height:28px;
          text-align:center;
          font-weight:bold;
          font-size:14px;
          border:2px solid;
          vertical-align: middle;
        }

        .abierta{ color:red; border-color:red; }
        .cerrada{ color:#2e7d32; border-color:#2e7d32; }

        /* TABLA PRINCIPAL */
        .table-wrap { padding: 20px; }
        table { width:100%; border-collapse:collapse; }
        th, td {
          border:1px solid #000;
          padding:6px;
          font-size:12px;
          vertical-align:middle;
        }

        th { background:#dde7f5; text-align: center; }

        .encabezado td { text-align: center; }

        /* AJUSTE COLUMNA STATUS */
        #tablaMatriz th:nth-child(8),
        #tablaMatriz td:nth-child(8){
            width:120px;
            text-align:center;
            padding: 2px;
        }

        /* INPUTS */
        input, select, textarea{
          width:100%;
          border:none;
          outline:none;
          background:transparent;
          font-size:12px;
          font-family: inherit;
        }

        textarea { min-height:50px; resize: vertical; white-space: pre-wrap;}

        /* SELECT STATUS PRO */
        .status-selector{
            width:100%;
            height: 100%;
            min-height: 40px;
            text-align:center;
            font-weight:600;
            padding:4px;
            border:none;
        }

        /* COLORES STATUS */
        .status-abierta{
            background:#fdecea !important;
            color:#c62828 !important;
            font-weight:600;
        }

        .status-cerrada{
            background:#e8f5e9 !important;
            color:#2e7d32 !important;
            font-weight:600;
        }

        /* PRINT */
        @media print{
          .no-print{ display:none !important; }
          body{ padding:0; background:#fff;}
          .format-container { border:none; box-shadow:none; max-width: 100%; }
          .table-wrap { padding: 5px; }
          input, select, textarea { border:none !important; }
        }

    </style>
</head>

<body>

<div class="format-container">

    <div class="toolbar no-print">
        <h1>Matriz de Seguimiento</h1>
        <div class="acciones">
            <button class="btn btn-back" onclick="history.back()">Atrás</button>
            <button class="btn btn-save" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-print" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <form id="formMatriz">
        <div class="table-wrap">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:180px;text-align:center;">
                        <?php if($logoUrl): ?>
                            <img src="<?= $logoUrl ?>" style="max-height:60px;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;font-weight:bold; font-size:15px;">
                        SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                    </td>
                    <td style="width:120px;text-align:center; font-weight:bold;">0</td>
                </tr>
                <tr>
                    <td style="text-align:center;font-weight:bold;">
                        MATRIZ DE HALLAZGOS Y ACCIONES PREVENTIVAS Y CORRECTIVAS
                    </td>
                    <td style="text-align:center; font-weight:bold;">AN-SST-29<br>2026</td>
                </tr>
            </table>

            <table style="width:auto; margin-left:auto; margin-top:10px; margin-bottom:10px;">
                <tr>
                    <td style="border:none;font-weight:bold; padding-right:10px;">ABIERTAS</td>
                    <td id="abiertas" class="box abierta">0</td>
                </tr>
                <tr>
                    <td style="border:none;font-weight:bold; padding-right:10px;">CERRADAS</td>
                    <td id="cerradas" class="box cerrada">0</td>
                </tr>
            </table>

            <table id="tablaMatriz">
                <thead>
                    <tr>
                        <th style="width: 5%;">ACPM</th>
                        <th style="width: 15%;">FUENTE</th>
                        <th style="width: 20%;">DESCRIPCIÓN</th>
                        <th style="width: 20%;">ACCIÓN</th>
                        <th style="width: 10%;">RESPONSABLE</th>
                        <th style="width: 7%;">FECHA</th>
                        <th style="width: 15%;">SEGUIMIENTO</th>
                        <th style="width: 8%;">STATUS</th>
                        <th style="width: 10%;">CIERRE</th>
                    </tr>
                </thead>
                <tbody id="tbodyMatriz">
                    <?php for ($i = 1; $i <= $filasMatriz; $i++): ?>
                        <tr>
                            <td><input type="text" style="text-align:center;" name="acpm_<?= $i ?>" value="<?= oldv('acpm_'.$i) ?>"></td>
                            <td>
                                <select name="fuente_<?= $i ?>">
                                    <option value="">-</option>
                                    <option value="CONDICIÓN INSEGURA" <?= oldv('fuente_'.$i) == 'CONDICIÓN INSEGURA' ? 'selected' : '' ?>>CONDICIÓN INSEGURA</option>
                                    <option value="ARL" <?= oldv('fuente_'.$i) == 'ARL' ? 'selected' : '' ?>>ARL</option>
                                    <option value="AUDITORÍA" <?= oldv('fuente_'.$i) == 'AUDITORÍA' ? 'selected' : '' ?>>AUDITORÍA</option>
                                    <option value="REPORTE DE INCIDENTE" <?= oldv('fuente_'.$i) == 'REPORTE DE INCIDENTE' ? 'selected' : '' ?>>REPORTE DE INCIDENTE</option>
                                </select>
                            </td>
                            <td><textarea name="desc_<?= $i ?>"><?= oldv('desc_'.$i) ?></textarea></td>
                            <td><textarea name="accion_<?= $i ?>"><?= oldv('accion_'.$i) ?></textarea></td>
                            <td><input type="text" name="resp_<?= $i ?>" value="<?= oldv('resp_'.$i) ?>"></td>
                            <td><input type="date" name="fecha_<?= $i ?>" value="<?= oldv('fecha_'.$i) ?>"></td>
                            <td><textarea name="seg_<?= $i ?>"><?= oldv('seg_'.$i) ?></textarea></td>
                            <td class="status-cell">
                                <select name="status_<?= $i ?>" class="status-selector status" onchange="estado(this)">
                                    <option value="">-</option>
                                    <option value="ABIERTA" <?= oldv('status_'.$i) == 'ABIERTA' ? 'selected' : '' ?>>ABIERTA</option>
                                    <option value="CERRADA" <?= oldv('status_'.$i) == 'CERRADA' ? 'selected' : '' ?>>CERRADA</option>
                                </select>
                            </td>
                            <td><input type="text" name="cierre_<?= $i ?>" value="<?= oldv('cierre_'.$i) ?>"></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="no-print" style="margin-top:15px; text-align:right;">
                <button type="button" class="btn btn-add" onclick="agregarFila()">
                    <i class="fa fa-plus"></i> Agregar fila
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Contador global de filas
let rowCount = <?= $filasMatriz ?>;

function agregarFila(){
    rowCount++;
    const tbody = document.getElementById("tbodyMatriz");
    const tr = document.createElement("tr");

    tr.innerHTML = `
        <td><input type="text" style="text-align:center;" name="acpm_${rowCount}"></td>
        <td>
            <select name="fuente_${rowCount}">
                <option value="">-</option>
                <option value="CONDICIÓN INSEGURA">CONDICIÓN INSEGURA</option>
                <option value="ARL">ARL</option>
                <option value="AUDITORÍA">AUDITORÍA</option>
                <option value="REPORTE DE INCIDENTE">REPORTE DE INCIDENTE</option>
            </select>
        </td>
        <td><textarea name="desc_${rowCount}"></textarea></td>
        <td><textarea name="accion_${rowCount}"></textarea></td>
        <td><input type="text" name="resp_${rowCount}"></td>
        <td><input type="date" name="fecha_${rowCount}"></td>
        <td><textarea name="seg_${rowCount}"></textarea></td>
        <td class="status-cell">
            <select name="status_${rowCount}" class="status-selector status" onchange="estado(this)">
                <option value="">-</option>
                <option value="ABIERTA">ABIERTA</option>
                <option value="CERRADA">CERRADA</option>
            </select>
        </td>
        <td><input type="text" name="cierre_${rowCount}"></td>
    `;
    
    tbody.appendChild(tr);
}

function estado(el){
    const td = el.parentElement;
    
    td.classList.remove("status-abierta", "status-cerrada");
    el.classList.remove("status-abierta", "status-cerrada");
    
    if(el.value === "ABIERTA") {
        td.classList.add("status-abierta");
        el.classList.add("status-abierta");
    }
    if(el.value === "CERRADA") {
        td.classList.add("status-cerrada");
        el.classList.add("status-cerrada");
    }
    
    contar();
}

function contar(){
    let a=0, c=0;
    document.querySelectorAll(".status").forEach(e => {
        if(e.value === "ABIERTA") a++;
        if(e.value === "CERRADA") c++;
    });
    document.getElementById("abiertas").innerText = a;
    document.getElementById("cerradas").innerText = c;
}

// Inicializar colores y contadores al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".status").forEach(el => estado(el));
});

// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function(e) {
    e.preventDefault(); // Evitar el envío clásico
    
    const btn = this;
    const form = document.getElementById('formMatriz');
    const formData = new FormData(form);
    
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
                id_empresa: <?= $empresaId ?>,
                id_item_sst: <?= $idItem ?>,
                datos: datosJSON
            })
        });

        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'La matriz de seguimiento se ha guardado correctamente.',
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