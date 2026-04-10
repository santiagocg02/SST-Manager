<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos (Ej: 42 para Registro de Asistencia)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 42; 

// --- Lógica de Empresa Optimizada (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE A LA API
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
    $datosCampos = json_decode($camposCrudos, true);
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RE-SST-01 | Registro de Asistencia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --line:#111;
            --blue:#9fb4d9;
            --blue-soft:#dfe8f6;
            --bg:#eef3f9;
            --paper:#fff;
            --text:#111;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
            --green:#198754;
            --green-hover:#146c43;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .topbar{
            position:sticky;
            top:0;
            z-index:100;
            background:#dde7f5;
            border-bottom:1px solid #c8d3e2;
            padding:10px 18px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
        }

        .btn-ui{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:6px 12px;
            border-radius:6px;
            border:1px solid var(--btn);
            background:var(--btn);
            color:#fff;
            text-decoration:none;
            font-size:12px;
            font-weight:800;
            transition:.2s ease;
            cursor:pointer;
        }

        .btn-ui:hover{ background:var(--btn-hover); border-color:var(--btn-hover); color:#fff; }
        .btn-ui.secondary{ background:#fff; color:var(--btn); border-color:#cfd6e4; }
        .btn-ui.secondary:hover{ background:#eef5ff; color:var(--btn-hover); }
        .btn-ui.success { background:var(--green); border-color:var(--green); color:#fff; }
        .btn-ui.success:hover { background:var(--green-hover); }

        .page-wrap{
            padding:24px;
        }

        .paper{
            max-width:1100px;
            margin:0 auto 40px;
            background:var(--paper);
            border:1px solid #d7dee8;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
            overflow:hidden;
            border-radius: 8px;
            padding: 24px;
        }

        .header-table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            margin-bottom: 20px;
        }

        .header-table td{
            border:1px solid var(--line);
            vertical-align:middle;
            padding:0;
        }

        .logo-cell{
            width:25%;
            text-align:center;
            background:#fafafa;
        }

        .logo-box{
            height:100px;
            margin:0 auto;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            color:#9a9a9a;
            font-weight:800;
            line-height:1;
        }

        .title-cell{
            width:55%;
        }

        .title-main,
        .title-sub{
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
            font-weight:800;
            padding:10px 12px;
        }

        .title-main{
            min-height:50px;
            border-bottom:1px solid var(--line);
            font-size:16px;
            text-transform:uppercase;
            color: #213b67;
        }

        .title-sub{
            min-height:30px;
            font-size:14px;
            text-transform:uppercase;
            font-weight:700;
        }

        .meta-cell{
            width:20%;
        }

        .meta-box{
            display:flex;
            flex-direction:column;
            height:100px;
        }

        .meta-box div, .meta-box input{
            flex:1;
            border-bottom:1px solid var(--line);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            padding:4px;
            text-align:center;
            width: 100%;
            border-top: none; border-left: none; border-right: none;
            outline: none; background: transparent; font-size: 13px;
        }
        .meta-box input:last-child { border-bottom: none; }

        .info-block{
            padding:0 0 10px;
            border-bottom:1px solid var(--line);
            margin-bottom: 20px;
        }

        .info-row{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
            padding:8px 14px;
        }

        .info-label{
            font-size:14px;
            font-weight:800;
            white-space:nowrap;
            color: #213b67;
        }

        .line-input{
            border:none;
            border-bottom:1px solid #777;
            background:transparent;
            outline:none;
            min-width:140px;
            padding:2px 4px;
            font-size:14px;
        }

        .line-input:focus { background: #f8fbff; border-bottom: 1px solid var(--btn); }

        .line-input.sm{ width:130px; }
        .line-input.md{ width:220px; }
        .line-input.lg{ flex-grow: 1; max-width:100%; }

        .check-group{
            display:flex;
            align-items:center;
            gap:6px;
            margin-right:18px;
            font-weight: bold;
            font-size: 14px;
        }

        .check-input{
            width:16px;
            height:16px;
            accent-color:#375f9c;
            cursor: pointer;
        }

        .attendance-table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .attendance-table th,
        .attendance-table td{
            border:1px solid #555;
            padding:0;
            height:34px;
            font-size:14px;
            vertical-align: middle;
        }

        .attendance-table thead th{
            background:var(--blue);
            text-align:center;
            font-weight:800;
            padding:6px;
        }

        .col-no{ width:5%; text-align:center; }
        .col-name{ width:35%; }
        .col-id{ width:20%; }
        .col-role{ width:20%; }
        .col-sign{ width:20%; }
        .col-del{ width:4%; text-align: center; }

        .num-cell{
            text-align:center;
            font-size:14px;
            font-weight: bold;
            background: #fafafa;
        }

        .cell-input{
            width:100%;
            height:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:4px 8px;
            font-size:14px;
        }

        .cell-input:focus { background: #f8fbff; }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
        }
        .btn-delete:hover { background: #bb2d3b; }

        .footer-space{
            height:20px;
        }

        @media print{
            body{ background:#fff; }
            .topbar, .print-hide { display:none !important; }
            .page-wrap{ padding:0; }
            .paper{
                max-width:100%;
                border:none;
                box-shadow:none;
                margin:0;
                padding:0;
            }
            .line-input, .cell-input{
                -webkit-print-color-adjust:exact;
                print-color-adjust:exact;
            }
            .col-del { display: none; }
            @page{ size:letter portrait; margin:12mm; }
        }

        @media (max-width: 900px){
            .page-wrap{ padding:12px; }
            .paper{ overflow-x:auto; }
            .header-table, .attendance-table{ min-width:800px; }
        }
    </style>
</head>
<body>

<div class="topbar print-hide">
    <div style="display:flex; gap:8px;">
        <button class="btn-ui secondary" type="button" onclick="history.back()">← Atrás</button>
        <button class="btn-ui secondary" type="button" onclick="window.location.reload()">Recargar</button>
        <button class="btn-ui success" type="button" id="btnGuardar">Guardar Cambios</button>
        <button class="btn-ui secondary" type="button" onclick="agregarFila()">+ Agregar fila</button>
        <button class="btn-ui" type="button" onclick="window.print()">Imprimir PDF</button>
    </div>
    <div class="text-end">
        <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">ASISTENCIA</span><br>
        <span style="font-size: 11px; color: #6b7280; font-weight: 700;">Usuario: <?= e($_SESSION["usuario"] ?? "Usuario") ?></span>
    </div>
</div>

<div class="page-wrap">
    <form id="form-sst-dinamico">
        <div class="paper">

            <table class="header-table">
                <tr>
                    <td class="logo-cell" style="<?= empty($logoEmpresaUrl) ? '' : 'padding:0; background:transparent;' ?>">
                        <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '2px dashed #c9c9c9' : 'none' ?>;">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                            <?php else: ?>
                                <span>TU LOGO</span>
                                <span>AQUÍ</span>
                            <?php endif; ?>
                        </div>
                    </td>

                    <td class="title-cell">
                        <div class="title-main">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</div>
                        <div class="title-sub">REGISTRO DE ASISTENCIA</div>
                    </td>

                    <td class="meta-cell">
                        <div class="meta-box">
                            <input type="text" name="meta_version" value="0" title="Versión">
                            <input type="text" name="meta_codigo" value="RE-SST-01" title="Código">
                            <input type="date" name="meta_fecha" id="metaFecha" title="Fecha de Aprobación">
                        </div>
                    </td>
                </tr>
            </table>

            <div class="info-block">
                <div class="info-row">
                    <span class="info-label">FECHA:</span>
                    <input type="date" name="fecha_registro" class="line-input sm" id="fechaRegistro">

                    <span class="info-label ms-3">HORA DE INICIO:</span>
                    <input type="time" name="hora_inicio" class="line-input sm">

                    <span class="info-label ms-3">HORA FINALIZACIÓN:</span>
                    <input type="time" name="hora_fin" class="line-input sm">
                </div>

                <div class="info-row mt-2">
                    <span class="info-label">TIPO:</span>

                    <label class="check-group ms-2">
                        <input name="tipo_registro" value="CAPACITACION" class="check-input" type="radio"> CAPACITACIÓN
                    </label>

                    <label class="check-group">
                        <input name="tipo_registro" value="REUNION" class="check-input" type="radio"> REUNIÓN
                    </label>
                </div>

                <div class="info-row mt-2">
                    <span class="info-label">TEMA TRATADO:</span>
                    <input type="text" name="tema_tratado" class="line-input lg">
                </div>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-name">NOMBRE COMPLETO</th>
                        <th class="col-id">NÚMERO DE CÉDULA</th>
                        <th class="col-role">CARGO</th>
                        <th class="col-sign">FIRMA</th>
                        <th class="col-del print-hide"></th>
                    </tr>
                </thead>
                <tbody id="attendance-body">
                    <?php for($i=1; $i<=10; $i++): ?>
                    <tr class="data-row">
                        <td class="num-cell seq-num"><?= $i ?></td>
                        <td><input type="text" name="asist_nombre[]" class="cell-input"></td>
                        <td><input type="text" name="asist_cedula[]" class="cell-input"></td>
                        <td><input type="text" name="asist_cargo[]" class="cell-input"></td>
                        <td><input type="text" name="asist_firma[]" class="cell-input"></td>
                        <td class="print-hide text-center"><button type="button" class="btn-delete" onclick="eliminarFila(this)">X</button></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="footer-space"></div>
        </div>
    </form>
</div>

<script>
    // Poner fecha de hoy por defecto
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        
        const fmeta = document.getElementById("metaFecha");
        if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;

        const fReg = document.getElementById("fechaRegistro");
        if (fReg && !fReg.value) fReg.value = `${y}-${m}-${dd}`;
    }
    setHoy();

    // Lógica para añadir y eliminar filas
    function actualizarNumeracion() {
        const celdas = document.querySelectorAll('.seq-num');
        celdas.forEach((celda, index) => {
            celda.textContent = index + 1;
        });
    }

    function agregarFila(){
        const tbody = document.getElementById('attendance-body');
        const plantilla = tbody.querySelector('.data-row');
        
        if(plantilla){
            const nuevaFila = plantilla.cloneNode(true);
            const inputs = nuevaFila.querySelectorAll('input');
            inputs.forEach(input => input.value = ''); // Limpiar valores
            tbody.appendChild(nuevaFila);
            actualizarNumeracion();
        }
    }

    function eliminarFila(btn){
        const tbody = document.getElementById('attendance-body');
        const filas = tbody.querySelectorAll('.data-row');
        
        if (filas.length > 1) {
            btn.closest('tr').remove();
            actualizarNumeracion();
        } else {
            // Si es la última fila, solo borrar contenido
            const inputs = btn.closest('tr').querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }
    }

    // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
    document.addEventListener('DOMContentLoaded', function () {
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            
            // 1. Cargar datos simples (no arrays) y Radios
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (!Array.isArray(value)) {
                    let campo = document.querySelector(`[name="${key}"]`);
                    if (campo) {
                        campo.value = value;
                    } else {
                        // Buscar si es un Radio
                        let radio = document.querySelector(`input[name="${key}"][value="${value}"]`);
                        if(radio) radio.checked = true;
                    }
                }
            }

            // 2. Comprobar si hay que crear más de las 10 filas iniciales
            let numAsistentes = 0;
            if(datosGuardados['asist_nombre'] && Array.isArray(datosGuardados['asist_nombre'])) {
                numAsistentes = datosGuardados['asist_nombre'].length;
            }

            const filasActuales = document.querySelectorAll('.data-row').length;
            if(numAsistentes > filasActuales) {
                for (let i = filasActuales; i < numAsistentes; i++) {
                    agregarFila();
                }
            }

            // 3. Poblar las filas
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (Array.isArray(value)) {
                    let campos = document.querySelectorAll(`[name="${key}[]"]`);
                    value.forEach((val, i) => {
                        if (campos[i]) campos[i].value = val;
                    });
                }
            }
        }
    });

    // --- LÓGICA DE GUARDADO ---
    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
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
                    text: 'Registro de asistencia guardado correctamente',
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

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>