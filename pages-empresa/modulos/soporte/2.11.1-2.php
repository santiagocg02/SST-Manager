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
// Ajusta el ID de este ítem según tu base de datos (Ej: 40 para Planificación del Cambio)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 40; 

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
    <title>2.11.1-2 Planificación del Cambio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --page-bg:#f5f7fb;
            --paper:#ffffff;
            --line:#9aa7b3;
            --line-soft:#c8d0d8;
            --head:#eef3f8;
            --blue:#8faad1;
            --blue-dark:#375b84;
            --text:#1f2937;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
            --green:#198754;
            --green-hover:#146c43;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--page-bg);
            font-family: Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .page-wrap{
            padding:20px;
            max-width: 100%;
            margin: 0 auto;
        }

        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:16px;
            background: #d9dde2;
            padding: 10px 16px;
            border: 1px solid #c8cdd3;
            border-radius: 6px;
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

        .sheet-card{
            background:var(--paper);
            border:1px solid #d7dee6;
            border-radius:8px;
            box-shadow:0 8px 24px rgba(31,41,55,.08);
            padding: 20px;
        }

        .header-table{
            width:100%;
            border-collapse:collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .header-table td{
            border:1px solid var(--line);
            padding:10px;
            text-align:center;
            font-weight:800;
            vertical-align: middle;
        }

        .logo-box {
            width: 20%;
        }

        .title-box {
            width: 70%;
            font-size: 16px;
            color: var(--blue-dark);
            text-transform: uppercase;
        }

        .meta-box {
            width: 10%;
            padding: 0 !important;
        }

        .meta-box input {
            width: 100%;
            border: none;
            border-bottom: 1px solid var(--line);
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            background: transparent;
            outline: none;
        }
        .meta-box input:last-child { border-bottom: none; }

        .scroll{
            overflow-x:auto;
            width: 100%;
            padding-bottom: 10px;
        }

        .tabla-cambios{
            width:2400px; /* Ancho forzado para acomodar 24 columnas */
            border-collapse:collapse;
            table-layout: fixed;
        }

        .tabla-cambios th,
        .tabla-cambios td{
            border:1px solid var(--line-soft);
            padding:4px;
            font-size:11px;
            text-align:center;
            vertical-align: middle;
        }

        .tabla-cambios th {
            background: var(--head);
            font-weight: 800;
            color: var(--blue-dark);
        }

        .tabla-cambios th.sub-th {
            background: var(--blue);
            color: #fff;
        }

        .tabla-cambios input[type="text"],
        .tabla-cambios input[type="date"],
        .tabla-cambios select,
        .tabla-cambios textarea{
            width:100%;
            border:none;
            outline:none;
            font-size:11px;
            background: transparent;
            padding: 4px;
            font-family: inherit;
        }

        .tabla-cambios input:focus,
        .tabla-cambios select:focus,
        .tabla-cambios textarea:focus {
            background: #f8fbff;
        }

        .tabla-cambios select {
            text-align: center;
            text-align-last: center;
            cursor: pointer;
        }

        .tabla-cambios textarea{
            resize:vertical;
            height:50px;
        }

        /* Anchos de columna */
        .w-fecha { width: 110px; }
        .w-area { width: 150px; }
        .w-solic { width: 150px; }
        .w-check { width: 45px; } /* I, RQ, PR, etc. */
        .w-otro { width: 100px; }
        .w-desc { width: 250px; }
        .w-sino { width: 50px; }
        .w-plan { width: 250px; }
        .w-resp { width: 150px; }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 10px;
        }
        .btn-delete:hover { background: #bb2d3b; }

        @media print{
            @page{ size: landscape; margin: 10mm; }
            body{ background:#fff !important; }
            .page-wrap{ padding:0 !important; }
            .toolbar, .print-hide { display:none !important; }
            .sheet-card{ border:none !important; box-shadow:none !important; padding: 0; }
            .scroll{ overflow:visible !important; }
            .tabla-cambios{ width: 100% !important; }
            /* En impresión, puede que la tabla se salga, requiere que el usuario ajuste la escala al 50% o menos en su navegador */
        }
    </style>
</head>
<body>

<div class="page-wrap">

    <div class="toolbar print-hide">
        <div style="display:flex; gap:8px;">
            <button class="btn-ui secondary" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-ui secondary" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-ui success" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-ui secondary" type="button" onclick="agregarFila()">+ Agregar fila</button>
            <button class="btn-ui" type="button" onclick="window.print()">Imprimir</button>
        </div>
        <div class="text-end">
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">PLANIFICACIÓN CAMBIO</span><br>
            <span style="font-size: 11px; color: #6b7280; font-weight: 700;">Usuario: <?= e($_SESSION["usuario"] ?? "Usuario") ?></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet-card">

            <table class="header-table">
                <tr>
                    <td rowspan="3" class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:1px solid #111; padding:0; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain; display: block; margin: auto;">
                        <?php else: ?>
                            TU LOGO AQUÍ
                        <?php endif; ?>
                    </td>
                    <td class="title-box">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td class="meta-box"><input type="text" name="meta_version" value="0"></td>
                </tr>
                <tr>
                    <td class="title-box">PLANIFICACIÓN DEL CAMBIO</td>
                    <td class="meta-box"><input type="text" name="meta_codigo" value="RE-SST-03"></td>
                </tr>
                <tr>
                    <td class="title-box" style="font-size: 12px; color: #666; font-weight: normal;">Registro de modificaciones a infraestructura, procesos o personal</td>
                    <td class="meta-box"><input type="date" name="meta_fecha" id="metaFecha"></td>
                </tr>
            </table>

            <div class="scroll mt-3">
                <table class="tabla-cambios">
                    <thead>
                        <tr>
                            <th rowspan="2" class="w-fecha">FECHA</th>
                            <th rowspan="2" class="w-area">ÁREA RELACIONADA</th>
                            <th rowspan="2" class="w-solic">SOLICITADO POR</th>
                            
                            <th colspan="9">TIPO DE CAMBIO (Marque X)</th>
                            
                            <th rowspan="2" class="w-otro">OTRO ¿CUÁL?</th>
                            <th rowspan="2" class="w-desc">DESCRIPCIÓN DEL CAMBIO</th>
                            
                            <th colspan="3">GERENCIA</th>
                            <th colspan="3">ENCARGADO SST</th>
                            
                            <th rowspan="2" class="w-resp">RESPONSABLE / CARGO</th>
                            <th rowspan="2" class="w-plan">PLAN DE ACCIÓN</th>
                            <th rowspan="2" class="w-fecha">FECHA FIN</th>
                            <th rowspan="2" class="w-check print-hide">Acción</th>
                        </tr>
                        <tr>
                            <th class="sub-th w-check" title="Infraestructura">I</th>
                            <th class="sub-th w-check" title="Requisitos Legales">RQ</th>
                            <th class="sub-th w-check" title="Procesos">PR</th>
                            <th class="sub-th w-check" title="Personal">PER</th>
                            <th class="sub-th w-check" title="Contratistas">CTR</th>
                            <th class="sub-th w-check" title="Proveedores de Servicio">PSV</th>
                            <th class="sub-th w-check" title="Sistema de Gestión">SG</th>
                            <th class="sub-th w-check" title="Accidentes">AC</th>
                            <th class="sub-th w-check" title="Otro">OT</th>

                            <th class="sub-th w-sino">SI</th>
                            <th class="sub-th w-sino">NO</th>
                            <th class="sub-th w-fecha">FECHA</th>
                            <th class="sub-th w-sino">SI</th>
                            <th class="sub-th w-sino">NO</th>
                            <th class="sub-th w-fecha">FECHA</th>
                        </tr>
                    </thead>

                    <tbody id="tabla-body">
                        <tr class="data-row">
                            <td><input type="date" name="col_fecha[]"></td>
                            <td><input type="text" name="col_area[]"></td>
                            <td><input type="text" name="col_solicitante[]"></td>
                            
                            <td><select name="chk_i[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_rq[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_pr[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_per[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_ctr[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_psv[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_sg[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_ac[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="chk_ot[]"><option value=""></option><option value="X">X</option></select></td>

                            <td><input type="text" name="col_otro[]"></td>
                            <td><textarea name="col_desc[]"></textarea></td>

                            <td><select name="ger_si[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="ger_no[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><input type="date" name="ger_fecha[]"></td>

                            <td><select name="enc_si[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><select name="enc_no[]"><option value=""></option><option value="X">X</option></select></td>
                            <td><input type="date" name="enc_fecha[]"></td>

                            <td><input type="text" name="col_responsable[]"></td>
                            <td><textarea name="col_plan[]"></textarea></td>
                            <td><input type="date" name="col_fecha_fin[]"></td>

                            <td class="print-hide">
                                <button type="button" class="btn-delete" onclick="eliminarFila(this)">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </form>
</div>

<script>
    // Poner fecha de hoy por defecto en la cabecera
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        const fmeta = document.getElementById("metaFecha");
        if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;
    }
    setHoy();

    // Función para agregar filas dinámicamente
    function agregarFila(){
        const tbody = document.getElementById("tabla-body");
        // Clonar la primera fila o crear una vacía si no hay
        let plantilla = tbody.querySelector(".data-row");
        if(plantilla){
            let nuevaFila = plantilla.cloneNode(true);
            // Limpiar valores
            let inputs = nuevaFila.querySelectorAll("input, textarea, select");
            inputs.forEach(input => {
                input.value = "";
            });
            tbody.appendChild(nuevaFila);
        }
    }

    function eliminarFila(btn){
        const tbody = document.getElementById("tabla-body");
        if(tbody.querySelectorAll(".data-row").length > 1){
            btn.closest("tr").remove();
        } else {
            // Si es la última fila, solo limpiar valores
            let inputs = btn.closest("tr").querySelectorAll("input, textarea, select");
            inputs.forEach(input => input.value = "");
        }
    }

    // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
    document.addEventListener('DOMContentLoaded', function () {
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            
            // 1. Llenar meta tags (campos que no son arrays)
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (!Array.isArray(value)) {
                    let campo = document.querySelector(`[name="${key}"]`);
                    if (campo) campo.value = value;
                }
            }

            // 2. Determinar la cantidad de filas a crear basándose en el primer array (ej. col_fecha)
            let numFilas = 0;
            if(datosGuardados['col_fecha'] && Array.isArray(datosGuardados['col_fecha'])){
                numFilas = datosGuardados['col_fecha'].length;
            }

            // Crear filas adicionales si es necesario
            const tbody = document.getElementById("tabla-body");
            for(let i = 1; i < numFilas; i++){
                let nuevaFila = tbody.querySelector(".data-row").cloneNode(true);
                tbody.appendChild(nuevaFila);
            }

            // 3. Poblar las filas con los arrays
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (Array.isArray(value)) {
                    let campos = document.querySelectorAll(`[name="${key}[]"]`);
                    value.forEach((val, i) => {
                        if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
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
                    text: 'Planificación guardada correctamente',
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