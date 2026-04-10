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
// Ajusta el ID de este ítem según tu base de datos (Ej: 34)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 34; 

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
    <title>AN-SST-29 | Matriz de condiciones inseguras / acciones correctivas y/o preventivas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --line:#6f6f6f;
            --line-soft:#b8b8b8;
            --head:#d8dee8;
            --sheet:#ffffff;
            --top:#efefef;
            --green-bg:#cfe8d1;
            --green:#1d6b2f;
            --red-bg:#f4c7cf;
            --red:#b01515;
            --text:#111;
            --blue:#0d6efd;
            --blue-dark:#0b5ed7;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:#f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .page-wrap{
            padding:18px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            margin-bottom:16px;
            flex-wrap:wrap;
            background: #d9dde2;
            padding: 10px 16px;
            border: 1px solid #c8cdd3;
            border-radius: 6px;
        }
        .btn-action{
            border:1px solid #cfd6e4;
            background:#fff;
            color: #2f62b6;
            padding:6px 12px;
            border-radius:6px;
            font-weight:800;
            cursor:pointer;
            font-size:12px;
        }
        .btn-action:hover { background: #eef4ff; }
        .btn-primary-action{
            border-color:#1b4fbd;
            background:#1b4fbd;
            color:#fff;
            padding:6px 12px;
            border-radius:6px;
            font-weight:800;
            cursor:pointer;
            font-size:12px;
        }
        .btn-primary-action:hover { background: #0f3484; }
        .btn-success-action {
            border: 1px solid #198754;
            background: #198754;
            color: #fff;
            padding:6px 12px;
            border-radius:6px;
            font-weight:800;
            cursor:pointer;
            font-size:12px;
        }
        .btn-success-action:hover { background: #146c43; }
        .tiny{ font-size:11px; color:#6b7280; font-weight:700; }

        .sheet-card{
            background:#fff;
            border:1px solid #d7d7d7;
            box-shadow:0 8px 24px rgba(0,0,0,.06);
            overflow:hidden;
            border-radius: 8px;
        }

        .sheet-scroll{
            overflow:auto;
            width:100%;
        }

        .sheet{
            min-width:1550px;
            background:var(--sheet);
        }

        table.form-sheet{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            background:#fff;
        }

        .form-sheet td,
        .form-sheet th{
            border:1px solid var(--line-soft);
            padding:0;
            vertical-align:middle;
        }

        .top-gray{
            background:var(--top);
            text-align:center;
            font-size:12px;
            font-weight:700;
            height:34px;
            padding:6px 8px !important;
        }

        .top-title{
            background:var(--top);
            text-align:center;
            font-size:18px;
            font-weight:700;
            letter-spacing:.2px;
            height:48px;
            padding:8px 10px !important;
        }

        .top-subtitle{
            background:var(--top);
            text-align:center;
            font-size:15px;
            font-weight:700;
            height:40px;
            padding:8px 10px !important;
        }

        .logo-box{
            background:var(--top);
            height:122px;
            text-align:center;
            color:#b4b4b4;
            font-size:16px;
            font-weight:700;
            letter-spacing:.5px;
            padding: 8px;
        }

        .logo-inner{
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            line-height:1.05;
        }

        .logo-dashed{
            border:2px dashed #c9c9c9;
            padding:10px 16px;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .counter-wrap{
            background:var(--top);
            padding:12px 16px !important;
            height:94px;
        }

        .counter-box{
            display:flex;
            justify-content:flex-end;
            align-items:center;
            gap:12px;
            margin:6px 0;
            font-size:13px;
            font-weight:700;
            font-style:italic;
        }

        .counter-num{
            width:72px;
            height:38px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
            font-weight:800;
            font-style:normal;
            background:#fff;
        }

        .counter-num.red{
            color:#ff2a2a;
            border:2px solid #ff6d6d;
        }

        .counter-num.green{
            color:#4d7d2e;
            border:2px solid #96bf83;
        }

        .col-head{
            background:var(--head);
            text-align:center;
            font-size:12px;
            font-weight:700;
            text-transform:uppercase;
            line-height:1.15;
            height:62px;
            padding:8px 6px !important;
        }

        .cell{
            height:58px;
            background:#fff;
            position:relative;
        }

        .cell input,
        .cell textarea,
        .cell select{
            width:100%;
            height:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:8px 10px;
            color:#222;
        }

        .cell textarea{
            resize:none;
            padding-top:10px;
        }

        .cell.center input,
        .cell.center select{
            text-align:center;
            font-weight:700;
        }

        .cell.status select{
            text-align:center;
            text-transform:uppercase;
            font-weight:700;
            cursor:pointer;
            padding-left:6px;
            padding-right:6px;
        }

        .status-open{
            background:var(--red-bg) !important;
            color:var(--red) !important;
        }

        .status-closed{
            background:var(--green-bg) !important;
            color:var(--green) !important;
        }

        .footer-note{
            padding:10px 14px;
            background:#fafafa;
            border-top:1px solid #e6e6e6;
            font-size:12px;
            color:#555;
        }

        .w-acpm{ width:230px; }
        .w-fuente{ width:150px; }
        .w-desc{ width:225px; }
        .w-accion{ width:270px; }
        .w-resp-seg{ width:145px; }
        .w-fecha{ width:125px; }
        .w-seguimiento{ width:155px; }
        .w-status{ width:90px; }
        .w-resp-cierre{ width:160px; }

        @media (max-width: 768px){
            .page-wrap{ padding:10px; }
            .top-title{ font-size:15px; }
            .top-subtitle{ font-size:13px; }
            .counter-num{ font-size:22px; width:60px; }
        }

        @media print{
            @page{ size: landscape; margin: 10mm; }
            body{ background:#fff !important; }
            .page-wrap{ padding:0 !important; max-width: 100%; }
            .toolbar, .print-hide, .footer-note{ display:none !important; }
            .sheet-card{ border:none !important; box-shadow:none !important; }
            .sheet-scroll{ overflow:visible !important; }
            .sheet{ min-width:100% !important; }
            .cell input, .cell textarea, .cell select{
                font-size:11px !important;
                -webkit-appearance:none;
                appearance:none;
            }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
    <div class="page-wrap">
        <div class="toolbar print-hide">
            <div style="display:flex; gap:8px;">
                <button class="btn-action" type="button" onclick="history.back()">← Atrás</button>
                <button class="btn-action" type="button" onclick="window.location.reload()">Recargar</button>
                <button class="btn-success-action" type="button" id="btnGuardar">Guardar Cambios</button>
                <button class="btn-primary-action" type="button" onclick="window.print()">Imprimir PDF</button>
            </div>
            <div class="tiny text-end">
                <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">MATRIZ CONDICIONES INSEGURAS</span><br>
                Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
            </div>
        </div>

        <div class="sheet-card">
            <form id="form-sst-dinamico">
                <div class="sheet-scroll">
                    <div class="sheet">
                        <table class="form-sheet">
                            <colgroup>
                                <col style="width:230px">
                                <col style="width:150px">
                                <col style="width:225px">
                                <col style="width:270px">
                                <col style="width:145px">
                                <col style="width:125px">
                                <col style="width:155px">
                                <col style="width:90px">
                                <col style="width:160px">
                            </colgroup>

                            <tr>
                                <td class="logo-box" rowspan="3" colspan="2">
                                    <div class="logo-inner">
                                        <div class="logo-dashed" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; padding:0;' ?>">
                                            <?php if(!empty($logoEmpresaUrl)): ?>
                                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                            <?php else: ?>
                                                TU LOGO<br>AQUÍ
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="top-title" colspan="5">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                                <td class="top-gray" colspan="2">
                                    <input type="text" name="meta_version" class="center" value="0" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                                </td>
                            </tr>
                            <tr>
                                <td class="top-gray" colspan="5">&nbsp;</td>
                                <td class="top-gray" colspan="2">AN-SST-29</td>
                            </tr>
                            <tr>
                                <td class="top-subtitle" colspan="5">MATRIZ DE CONDICIONES INSEGURAS / ACCIONES CORRECTIVAS Y/O PREVENTIVAS</td>
                                <td class="top-gray" colspan="2">
                                    <input type="date" name="meta_fecha" id="metaFecha" style="width:100%; border:none; background:transparent; font-weight:bold; text-align:center;">
                                </td>
                            </tr>

                            <tr>
                                <td class="counter-wrap" colspan="9">
                                    <div class="counter-box">
                                        <span>ABIERTAS</span>
                                        <div class="counter-num red" id="abiertasCount">0</div>
                                    </div>
                                    <div class="counter-box">
                                        <span>CERRADAS</span>
                                        <div class="counter-num green" id="cerradasCount">0</div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th class="col-head w-acpm">ACPM / HALLAZGO</th>
                                <th class="col-head w-fuente">FUENTE</th>
                                <th class="col-head w-desc">DESCRIPCIÓN</th>
                                <th class="col-head w-accion">ACCIÓN A TOMAR</th>
                                <th class="col-head w-resp-seg">RESPONSABLE DEL SEGUIMIENTO</th>
                                <th class="col-head w-fecha">FECHA DE CUMPLIMIENTO</th>
                                <th class="col-head w-seguimiento">SEGUIMIENTO</th>
                                <th class="col-head w-status">STATUS</th>
                                <th class="col-head w-resp-cierre">RESPONSABLE DEL CIERRE</th>
                            </tr>

                            <?php for($i=0; $i<12; $i++): ?>
                            <tr>
                                <td class="cell">
                                    <textarea name="acpm_hallazgo[]"></textarea>
                                </td>
                                <td class="cell center">
                                    <select name="fuente[]">
                                        <option value=""></option>
                                        <option value="CONDICIÓN INSEGURA">CONDICIÓN INSEGURA</option>
                                        <option value="ACTO INSEGURO">ACTO INSEGURO</option>
                                        <option value="HALLAZGO">HALLAZGO</option>
                                        <option value="RECOMENDACIONES DE LA ARL">RECOMENDACIONES DE LA ARL</option>
                                        <option value="OBSERVACIÓN DE COMPORTAMIENTO">OBSERVACIÓN DE COMPORTAMIENTO</option>
                                        <option value="ACCIONES DE LA REVISIÓN POR LA ALTA DIRECCIÓN">ACCIONES DE LA REVISIÓN POR LA ALTA DIRECCIÓN</option>
                                        <option value="INVESTIGACIÓN DE ACCIDENTES">INVESTIGACIÓN DE ACCIDENTES</option>
                                    </select>
                                </td>
                                <td class="cell">
                                    <textarea name="descripcion[]"></textarea>
                                </td>
                                <td class="cell">
                                    <textarea name="accion_tomar[]"></textarea>
                                </td>
                                <td class="cell">
                                    <input type="text" name="responsable_seguimiento[]">
                                </td>
                                <td class="cell">
                                    <input type="date" name="fecha_cumplimiento[]">
                                </td>
                                <td class="cell">
                                    <textarea name="seguimiento[]"></textarea>
                                </td>
                                <td class="cell status status-open">
                                    <select name="status[]" class="status-select status-open">
                                        <option value="ABIERTA" selected>ABIERTA</option>
                                        <option value="CERRADA">CERRADA</option>
                                    </select>
                                </td>
                                <td class="cell">
                                    <input type="text" name="responsable_cierre[]">
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                </div>

                <div class="footer-note print-hide">
                    Puedes editar cada fila directamente. El contador de abiertas y cerradas cambia automáticamente según el campo <strong>STATUS</strong>.
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones originales para contadores y visualización
        function actualizarEstadoVisual(select){
            const td = select.closest('.status');
            td.classList.remove('status-open', 'status-closed');
            select.classList.remove('status-open', 'status-closed');

            if(select.value === 'CERRADA'){
                td.classList.add('status-closed');
                select.classList.add('status-closed');
            }else{
                td.classList.add('status-open');
                select.classList.add('status-open');
            }
        }

        function actualizarContadores(){
            const selects = document.querySelectorAll('.status-select');
            let abiertas = 0;
            let cerradas = 0;

            selects.forEach(select => {
                if(select.value === 'CERRADA'){
                    cerradas++;
                }else{
                    // Si hay algo escrito en ACPM/Hallazgo u otro campo clave y está abierta, se cuenta. 
                    // Para simplificar y mantener compatibilidad, contamos todos los dropdowns 'ABIERTA'.
                    // Pero para ser limpios, solo contaremos las filas que tengan algún "Hallazgo" o "Fuente" registrado:
                    const row = select.closest('tr');
                    const hallazgo = row.querySelector('[name="acpm_hallazgo[]"]').value.trim();
                    const fuente = row.querySelector('[name="fuente[]"]').value.trim();
                    
                    // Si la fila tiene uso, cuenta. Si está vacía completamente, la ignoramos de las métricas.
                    if (hallazgo !== '' || fuente !== '') {
                        abiertas++;
                    }
                }
            });

            document.getElementById('abiertasCount').textContent = abiertas;
            document.getElementById('cerradasCount').textContent = cerradas;
        }

        // Poner fecha de hoy por defecto si está vacía
        function setHoy(){
            const d = new Date();
            const y = d.getFullYear();
            const m = String(d.getMonth()+1).padStart(2,"0");
            const dd = String(d.getDate()).padStart(2,"0");
            document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;

            const fmeta = document.getElementById("metaFecha");
            if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;
        }
        setHoy();

        // Escuchadores de eventos para los cambios manuales
        document.querySelectorAll('.status-select').forEach(select => {
            actualizarEstadoVisual(select);
            select.addEventListener('change', function(){
                actualizarEstadoVisual(this);
                actualizarContadores();
            });
        });
        
        // Escuchadores adicionales para actualizar contadores si se escribe en una fila "abierta"
        document.querySelectorAll('[name="acpm_hallazgo[]"], [name="fuente[]"]').forEach(input => {
            input.addEventListener('input', actualizarContadores);
            input.addEventListener('change', actualizarContadores);
        });

        // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
        document.addEventListener('DOMContentLoaded', function () {
            let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
            if (typeof datosGuardados === 'string') {
                try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
            }

            if (datosGuardados && Object.keys(datosGuardados).length > 0) {
                for (const [key, value] of Object.entries(datosGuardados)) {
                    if (Array.isArray(value)) {
                        let campos = document.querySelectorAll(`[name="${key}[]"]`);
                        value.forEach((val, i) => {
                            if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                        });
                    } else {
                        const campo = document.querySelector(`[name="${key}"]`);
                        if (campo) {
                            campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                        }
                    }
                }
                
                // Actualizar los colores y contadores después de poblar los datos
                document.querySelectorAll('.status-select').forEach(select => {
                    actualizarEstadoVisual(select);
                });
                actualizarContadores();
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
                        text: 'Matriz guardada correctamente',
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