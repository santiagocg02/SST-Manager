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
// Ajusta el ID de este ítem según tu base de datos (Ej: 43 para Acta de Reunión)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 43; 

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
    <title>RE-SST-01 | Acta de Reunión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --line:#111;
            --blue:#9fb4d9;
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
        .btn-ui.small { padding: 4px 8px; font-size: 11px; }

        .page-wrap{ padding:24px; }

        .paper{
            max-width:1050px;
            margin:0 auto 40px;
            background:var(--paper);
            border:1px solid #d7dee8;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
            overflow:hidden;
            border-radius:8px;
            padding:24px;
        }

        .doc-table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .doc-table td,
        .doc-table th{
            border:1px solid var(--line);
            padding:6px 8px;
            vertical-align:middle;
        }

        .logo-cell{
            width:18%;
            text-align:center;
            background:#fafafa;
            height:84px;
        }

        .logo-box{
            height:100px;
            margin:0 auto;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            color:#9a9a9a;
            font-weight:800;
            line-height:1;
        }

        .logo-box .small{ font-size:12px; }
        .logo-box .big{ font-size:15px; margin-top:4px; }

        .title-cell{ width:62%; padding:0 !important; }

        .title-main,
        .title-sub{
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
        }

        .title-main{
            min-height:47px;
            border-bottom:1px solid var(--line);
            font-size:16px;
            font-weight:800;
            text-transform:uppercase;
            padding:8px 10px;
            color: #213b67;
        }

        .title-sub{
            min-height:37px;
            font-size:14px;
            font-weight:800;
            text-transform:uppercase;
            padding:8px 10px;
        }

        .meta-cell{ width:20%; padding:0 !important; }

        .meta-box{
            display:flex;
            flex-direction:column;
            min-height:84px;
        }

        .meta-box input{
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            border-bottom:1px solid var(--line);
            font-weight:700;
            font-size:14px;
            text-align:center;
            padding:6px;
            width: 100%;
            border-top: none; border-left: none; border-right: none;
            outline: none; background: transparent;
        }
        .meta-box input:focus { background: #f8fbff; }
        .meta-box input:last-child{ border-bottom:none; }

        .section-title{
            background:var(--blue);
            text-align:center;
            font-weight:800;
            font-size:13px;
            text-transform:uppercase;
            color: #1a1a1a;
        }

        .subhead{
            text-align:center;
            font-weight:600;
            background:#f5f7fa;
        }

        .num-col{ width:6%; text-align:center; }
        .name-col{ width:42%; }
        .cargo-col{ width:22%; }
        .firma-col{ width:25%; }
        .del-col{ width:5%; text-align:center; }

        .concl-num{ width:6%; text-align:center; }
        .concl-task{ width:44%; }
        .concl-resp{ width:20%; }
        .concl-date{ width:25%; }

        .input-cell,
        .textarea-cell{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:14px;
            padding:2px 4px;
            font-family: inherit;
        }

        .input-cell:focus, .textarea-cell:focus { background: #f8fbff; }

        .textarea-cell{
            resize:vertical;
            height:100%;
            min-height:38px;
        }

        .center{ text-align:center; font-weight: bold; background: #fafafa; }

        .h-34 td{ height:34px; }
        .h-38 td{ height:38px; }
        .h-40 td{ height:40px; }
        .h-42 td{ height:42px; }

        .discussion-row td{ height:41px; }
        .conclusion-row td{ height:68px; }

        .label-cell{ font-weight:600; background:#fff; white-space: nowrap; width: 15%; }
        .val-cell { background: #fff; width: 35%; }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11px;
        }
        .btn-delete:hover { background: #bb2d3b; }

        .actions-row { background: #f1f4f9; text-align: right; border:none !important; }
        .actions-row td { border: none; padding: 6px; }

        @media print{
            body{ background:#fff; }
            .topbar, .print-hide { display:none !important; }
            .page-wrap{ padding:0; }
            .paper{ max-width:100%; margin:0; border:none; box-shadow:none; padding: 0;}
            @page{ size:letter portrait; margin:10mm; }
            .del-col { display: none; }
            input, textarea { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }

        @media (max-width: 900px){
            .page-wrap{ padding:12px; }
            .paper{ overflow-x:auto; }
            .doc-table{ min-width:800px; }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="topbar print-hide">
    <div style="display:flex; gap:8px;">
        <button class="btn-ui secondary" type="button" onclick="history.back()">← Atrás</button>
        <button class="btn-ui secondary" type="button" onclick="window.location.reload()">Recargar</button>
        <button class="btn-ui success" type="button" id="btnGuardar">Guardar Cambios</button>
        <button class="btn-ui" type="button" onclick="window.print()">Imprimir PDF</button>
    </div>
    <div class="text-end">
        <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">ACTA REUNIÓN</span><br>
        <span style="font-size: 11px; color: #6b7280; font-weight: 700;">Usuario: <?= e($_SESSION["usuario"] ?? "Usuario") ?></span>
    </div>
</div>

<div class="page-wrap">
    <form id="form-sst-dinamico">
        <div class="paper">
            <table class="doc-table">

                <tr>
                    <td class="logo-cell" style="<?= empty($logoEmpresaUrl) ? 'padding:0; background:transparent;' : 'padding:0; background:transparent;' ?>">
                        <div class="logo-box" style="border: <?= empty($logoEmpresaUrl) ? '2px dashed #c7c7c7' : 'none' ?>;">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                            <?php else: ?>
                                <div class="small">TU LOGO</div>
                                <div class="big">AQUÍ</div>
                            <?php endif; ?>
                        </div>
                    </td>

                    <td class="title-cell">
                        <div class="title-main">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</div>
                        <div class="title-sub">ACTA DE REUNIÓN</div>
                    </td>

                    <td class="meta-cell">
                        <div class="meta-box">
                            <input type="text" name="meta_version" value="0" title="Versión">
                            <input type="text" name="meta_codigo" value="RE-SST-01" title="Código">
                            <input type="date" name="meta_fecha" id="metaFecha" title="Fecha">
                        </div>
                    </td>
                </tr>

                <tr class="h-38">
                    <td colspan="3" style="border-left:none; border-right:none; border-bottom:none;"></td>
                </tr>

                <tr class="h-34">
                    <td class="label-cell">Comité o Grupo:</td>
                    <td class="val-cell"><input type="text" name="comite_grupo" class="input-cell"></td>
                    <td class="label-cell" style="width:15%;">Acta No:</td>
                    <td class="val-cell" style="width:35%;"><input type="text" name="acta_no" class="input-cell"></td>
                </tr>

                <tr class="h-34">
                    <td class="label-cell">Citada por:</td>
                    <td class="val-cell"><input type="text" name="citada_por" class="input-cell"></td>
                    <td class="label-cell">Fecha:</td>
                    <td class="val-cell"><input type="date" name="fecha_acta" id="fechaActa" class="input-cell"></td>
                </tr>

                <tr class="h-34">
                    <td class="label-cell">Coordinador:</td>
                    <td class="val-cell"><input type="text" name="coordinador" class="input-cell"></td>
                    <td class="label-cell">Horario:</td>
                    <td class="val-cell" style="white-space: nowrap;">
                        <input type="time" name="hora_inicio" class="input-cell" style="width:40%; display:inline-block;" title="Inicio">
                        - 
                        <input type="time" name="hora_fin" class="input-cell" style="width:40%; display:inline-block;" title="Fin">
                    </td>
                </tr>

            </table>

            <table class="doc-table" style="margin-top: 20px;">
                <tr>
                    <td colspan="5" class="section-title">PARTICIPANTES</td>
                </tr>
                <tr>
                    <td class="subhead num-col">No.</td>
                    <td class="subhead name-col">Nombre y Apellidos</td>
                    <td class="subhead cargo-col">Cargo</td>
                    <td class="subhead firma-col">Firma</td>
                    <td class="subhead del-col print-hide"></td>
                </tr>
                <tbody id="participantes-body">
                    <tr class="h-34 data-row-part">
                        <td class="center seq-num-part">1</td>
                        <td><input type="text" name="part_nombre[]" class="input-cell"></td>
                        <td><input type="text" name="part_cargo[]" class="input-cell"></td>
                        <td><input type="text" name="part_firma[]" class="input-cell"></td>
                        <td class="print-hide text-center"><button type="button" class="btn-delete" onclick="eliminarFila(this, 'part')">X</button></td>
                    </tr>
                </tbody>
                <tr class="actions-row print-hide">
                    <td colspan="5"><button type="button" class="btn-ui secondary small" onclick="agregarFila('participantes-body', 'data-row-part', 'seq-num-part')">+ Agregar Participante</button></td>
                </tr>
            </table>

            <table class="doc-table" style="margin-top: 20px;">
                <tr>
                    <td colspan="3" class="section-title">PUNTOS DE DISCUSIÓN</td>
                </tr>
                <tbody id="puntos-body">
                    <tr class="discussion-row data-row-punto">
                        <td class="center seq-num-punto" style="width:6%;">1</td>
                        <td style="width:89%;"><input type="text" name="punto_desc[]" class="input-cell"></td>
                        <td class="print-hide text-center del-col"><button type="button" class="btn-delete" onclick="eliminarFila(this, 'punto')">X</button></td>
                    </tr>
                </tbody>
                <tr class="actions-row print-hide">
                    <td colspan="3"><button type="button" class="btn-ui secondary small" onclick="agregarFila('puntos-body', 'data-row-punto', 'seq-num-punto')">+ Agregar Punto</button></td>
                </tr>
            </table>

            <table class="doc-table" style="margin-top: 20px;">
                <tr>
                    <td colspan="5" class="section-title">CONCLUSIONES Y TAREAS</td>
                </tr>
                <tr>
                    <td class="subhead concl-num">No.</td>
                    <td class="subhead concl-task">Tarea / Conclusión</td>
                    <td class="subhead concl-resp">Responsable</td>
                    <td class="subhead concl-date">Fecha Cumplimiento</td>
                    <td class="subhead del-col print-hide"></td>
                </tr>
                <tbody id="conclusiones-body">
                    <tr class="conclusion-row data-row-concl">
                        <td class="center seq-num-concl">1</td>
                        <td><textarea name="concl_tarea[]" class="textarea-cell"></textarea></td>
                        <td><input type="text" name="concl_resp[]" class="input-cell"></td>
                        <td><input type="date" name="concl_fecha[]" class="input-cell"></td>
                        <td class="print-hide text-center"><button type="button" class="btn-delete" onclick="eliminarFila(this, 'concl')">X</button></td>
                    </tr>
                </tbody>
                <tr class="actions-row print-hide">
                    <td colspan="5"><button type="button" class="btn-ui secondary small" onclick="agregarFila('conclusiones-body', 'data-row-concl', 'seq-num-concl')">+ Agregar Tarea</button></td>
                </tr>
            </table>

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

        const fActa = document.getElementById("fechaActa");
        if (fActa && !fActa.value) fActa.value = `${y}-${m}-${dd}`;
    }
    setHoy();

    // Auto-ajustar altura de textareas
    function autoResizeTextareas() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(ta => {
            ta.style.height = 'auto';
            ta.style.height = (ta.scrollHeight) + 'px';
            ta.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    }
    setTimeout(autoResizeTextareas, 100);

    // Funciones dinámicas para Filas
    function actualizarNumeracion(bodyId, seqClass) {
        const celdas = document.getElementById(bodyId).querySelectorAll('.' + seqClass);
        celdas.forEach((celda, index) => {
            celda.textContent = index + 1;
        });
    }

    function agregarFila(bodyId, rowClass, seqClass){
        const tbody = document.getElementById(bodyId);
        const plantilla = tbody.querySelector('.' + rowClass);
        
        if(plantilla){
            const nuevaFila = plantilla.cloneNode(true);
            const inputs = nuevaFila.querySelectorAll('input, textarea');
            inputs.forEach(input => input.value = ''); // Limpiar
            tbody.appendChild(nuevaFila);
            actualizarNumeracion(bodyId, seqClass);
            autoResizeTextareas(); // Reasignar eventos al nuevo textarea
        }
    }

    function eliminarFila(btn, tipo){
        const row = btn.closest('tr');
        const tbody = row.parentElement;
        const bodyId = tbody.id;
        
        let rowClass, seqClass;
        if(tipo === 'part') { rowClass = 'data-row-part'; seqClass = 'seq-num-part'; }
        else if(tipo === 'punto') { rowClass = 'data-row-punto'; seqClass = 'seq-num-punto'; }
        else if(tipo === 'concl') { rowClass = 'data-row-concl'; seqClass = 'seq-num-concl'; }

        const filas = tbody.querySelectorAll('.' + rowClass);
        
        if (filas.length > 1) {
            row.remove();
            actualizarNumeracion(bodyId, seqClass);
        } else {
            // Si es la última fila, limpiar
            const inputs = row.querySelectorAll('input, textarea');
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
            
            // 1. Cargar datos simples
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (!Array.isArray(value)) {
                    let campo = document.querySelector(`[name="${key}"]`);
                    if (campo) campo.value = value;
                }
            }

            // 2. Comprobar y generar filas necesarias para Participantes
            let nPart = (datosGuardados['part_nombre'] && Array.isArray(datosGuardados['part_nombre'])) ? datosGuardados['part_nombre'].length : 1;
            for (let i = 1; i < nPart; i++) agregarFila('participantes-body', 'data-row-part', 'seq-num-part');

            // 3. Comprobar y generar filas necesarias para Puntos
            let nPuntos = (datosGuardados['punto_desc'] && Array.isArray(datosGuardados['punto_desc'])) ? datosGuardados['punto_desc'].length : 1;
            for (let i = 1; i < nPuntos; i++) agregarFila('puntos-body', 'data-row-punto', 'seq-num-punto');

            // 4. Comprobar y generar filas necesarias para Conclusiones
            let nConcl = (datosGuardados['concl_tarea'] && Array.isArray(datosGuardados['concl_tarea'])) ? datosGuardados['concl_tarea'].length : 1;
            for (let i = 1; i < nConcl; i++) agregarFila('conclusiones-body', 'data-row-concl', 'seq-num-concl');

            // 5. Poblar las filas con los arrays
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (Array.isArray(value)) {
                    let campos = document.querySelectorAll(`[name="${key}[]"]`);
                    value.forEach((val, i) => {
                        if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                    });
                }
            }

            setTimeout(autoResizeTextareas, 200);
        } else {
            // Si está vacío, cargar unas filas base por estética
            for (let i = 1; i < 3; i++) agregarFila('participantes-body', 'data-row-part', 'seq-num-part');
            for (let i = 1; i < 3; i++) agregarFila('puntos-body', 'data-row-punto', 'seq-num-punto');
            for (let i = 1; i < 3; i++) agregarFila('conclusiones-body', 'data-row-concl', 'seq-num-concl');
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
                    text: 'Acta guardada correctamente',
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