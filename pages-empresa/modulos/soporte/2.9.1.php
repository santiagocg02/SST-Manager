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
// Ajusta el ID de este ítem según tu base de datos (Ej: 35)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 35; 

// --- Lógica de Empresa Optimizada (Logo, Nombres y Firmas) ---
$nombreEmpresaLogeada = "NOMBRE DE LA EMPRESA";
$logoEmpresaUrl = "";
$nombreRL = "";
$firmaRL = "";
$nombreSST = "";
$firmaSST = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $nombreEmpresaLogeada = $empData['nombre_empresa'] ?? 'NOMBRE DE LA EMPRESA';
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        
        // Priorizando campos _rl y _sst
        $nombreRL = $empData['nombre_rl'] ?? $empData['representante_legal'] ?? '';
        $firmaRL = $empData['firma_rl'] ?? $empData['firma_representante'] ?? '';
        $nombreSST = $empData['nombre_sst'] ?? $empData['responsable_sst'] ?? '';
        $firmaSST = $empData['firma_sst'] ?? '';
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
    <title>2.9.1 - Procedimiento para Compras en SST</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --blue:#1f5fa8;
            --blue-soft:#eef4ff;
            --line:#111;
            --text:#111827;
            --muted:#667085;
            --bg:#f3f6fb;
        }

        body{
            margin:0;
            font-family: Arial, Helvetica, sans-serif;
            background:var(--bg);
            color:var(--text);
        }

        .page-wrap{
            max-width:1100px;
            margin:16px auto 60px;
            padding:0 12px;
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

        .sheet{
            background:#fff;
            border:1px solid #d9e2ef;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 8px 24px rgba(15, 23, 42, .08);
            margin-bottom: 20px;
        }

        .sheet-header{
            padding:18px 20px 10px;
            border-bottom:2px solid var(--blue);
            background:linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }

        .top-table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }

        .top-table td{
            border:1px solid var(--line);
            padding:8px;
            vertical-align:middle;
            font-size:13px;
        }

        .logo-box{
            height:90px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            color:#666;
            background:#fafafa;
            padding: 5px;
        }

        .title-main{
            margin:0;
            text-align:center;
            font-size:18px;
            line-height:1.35;
            font-weight:700;
            text-transform:uppercase;
        }

        .doc-name{
            font-weight:700;
            text-align:center;
            font-size:14px;
            text-transform:uppercase;
        }

        .meta-input,
        .line-input,
        .form-control{
            border:1px solid #cfd8e3;
            border-radius:8px;
            font-size:14px;
            box-shadow:none !important;
        }

        .meta-input,
        .line-input{
            width:100%;
            padding:8px 10px;
            outline:none;
            background:#fff;
        }

        .meta-input:focus,
        .line-input:focus,
        .form-control:focus{
            border-color:#6ea8fe;
            box-shadow:0 0 0 .15rem rgba(13,110,253,.12) !important;
        }

        .doc-body{
            padding:22px 20px 28px;
        }

        .cover-card{
            border:1px solid #dbe5f0;
            border-radius:14px;
            padding:24px 18px;
            margin-bottom:22px;
            background:#fcfdff;
        }

        .cover-logo{
            width:180px;
            height:120px;
            margin:0 auto 18px;
            border:1px dashed #aab7c7;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#667085;
            font-weight:700;
            background:#fff;
            padding: 10px;
        }

        .cover-title{
            text-align:center;
            font-size:20px;
            font-weight:700;
            text-transform:uppercase;
            line-height:1.4;
            margin-bottom:18px;
        }

        .section-card{
            border:1px solid #d9e2ef;
            border-radius:14px;
            overflow:hidden;
            background:#fff;
            margin-bottom:18px;
        }

        .section-title{
            background:var(--blue);
            color:#fff;
            padding:10px 14px;
            font-weight:700;
            text-transform:uppercase;
            font-size:14px;
            letter-spacing:.3px;
        }

        .section-body{
            padding:14px;
        }

        .muted-label{
            display:block;
            margin-bottom:4px;
            font-size:12px;
            color:var(--muted);
            font-weight:600;
        }

        .info-box{
            border:1px solid #e2e8f0;
            border-radius:12px;
            padding:14px;
            background:#fbfdff;
            margin-bottom:14px;
        }

        .info-box:last-child{
            margin-bottom:0;
        }

        .table-clean{
            width:100%;
            border-collapse:collapse;
        }

        .table-clean th,
        .table-clean td{
            border:1px solid #111;
            padding:10px;
            vertical-align:top;
            font-size:14px;
        }

        .table-clean th{
            background:var(--blue-soft);
            text-align:center;
        }

        .glossary-item{
            border:1px solid #e2e8f0;
            border-radius:12px;
            background:#fbfdff;
            padding:12px;
            margin-bottom:10px;
        }

        .glossary-item strong{
            display:block;
            color:var(--blue);
            margin-bottom:5px;
        }

        textarea.form-control{
            resize:vertical;
            min-height:90px;
        }

        .sign-grid{
            display:grid;
            grid-template-columns:1fr 1fr 1fr;
            gap:18px;
            margin-top:24px;
        }

        .sign{
            border-top:1px solid #111;
            padding-top:8px;
            text-align:center;
            min-height:65px;
            font-size:12px;
            font-weight:700;
            position: relative;
        }

        @media print{
            body{
                background:#fff;
            }
            .page-wrap{
                max-width:100%;
                margin:0;
                padding:0;
            }
            .toolbar, .print-hide{
                display:none !important;
            }
            .sheet{
                border:none;
                box-shadow:none;
                border-radius:0;
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
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">PROCEDIMIENTO COMPRAS</span><br>
            Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet">

            <div class="sheet-header">
                <table class="top-table">
                    <tr>
                        <td rowspan="3" style="width:18%;">
                            <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                                <?php if(!empty($logoEmpresaUrl)): ?>
                                    <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                                <?php else: ?>
                                    LOGO EMPRESA
                                <?php endif; ?>
                            </div>
                        </td>
                        <td rowspan="3" style="width:52%;">
                            <h1 class="title-main">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</h1>
                        </td>
                        <td style="width:15%; font-weight:700;">Versión</td>
                        <td style="width:15%;">
                            <input type="text" name="meta_version" class="meta-input" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Código</td>
                        <td>
                            <input type="text" name="meta_codigo" class="meta-input" value="AN-XX-SST-23">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Fecha</td>
                        <td>
                            <input type="date" name="meta_fecha" id="metaFecha1" class="meta-input">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="doc-name">PROCEDIMIENTO PARA COMPRAS EN SST</td>
                    </tr>
                </table>
            </div>

            <div class="doc-body">

                <div class="cover-card">
                    <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </div>

                    <div class="cover-title">
                        PROCEDIMIENTO PARA COMPRAS EN SST
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="muted-label">Versión</label>
                            <input type="text" name="cover_version" class="line-input" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="muted-label">Fecha</label>
                            <input type="date" name="cover_fecha" id="metaFecha2" class="line-input">
                        </div>
                        <div class="col-12">
                            <label class="muted-label">Nombre de la empresa</label>
                            <input type="text" name="cover_empresa" class="line-input" value="<?= htmlspecialchars($nombreEmpresaLogeada) ?>" placeholder="NOMBRE DE LA EMPRESA">
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Objetivo</div>
                    <div class="section-body">
                        <textarea name="txt_objetivo" class="form-control" rows="4">Constatar que la adquisición de nuevos productos y servicios de interés en el Sistema de Gestión de Seguridad y Salud en el Trabajo, no representen un riesgo para la población trabajadora.</textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Alcance</div>
                    <div class="section-body">
                        <textarea name="txt_alcance" class="form-control" rows="4">El alcance de este procedimiento tiene cobertura en todos los procesos que requieran la adquisición de productos, bienes y servicios.</textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Aspectos generales</div>
                    <div class="section-body">
                        <div class="info-box">
                            <textarea name="txt_aspectos_generales" class="form-control" rows="7">A través de este procedimiento se establecen los requisitos en materia de la adquisición de productos, bienes y servicios y los requerimientos en Seguridad y Salud en la adquisición de estos.

Comunicar a los proveedores los requisitos en materia de seguridad y Salud establecidos en la empresa.

Se identifican los peligros y valoran los riesgos que conlleva la adquisición de nuevos bienes y servicios y su impacto en los grupos de interés y establecer las medidas preventivas y correctivas frente a la implementación de nuevos productos, bienes y servicios.</textarea>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Glosario</div>
                    <div class="section-body">

                        <div class="glossary-item">
                            <strong>Compras</strong>
                            <textarea name="glos_compras" class="form-control" rows="2">Proceso de adquisición de bienes y servicios.</textarea>
                        </div>

                        <div class="glossary-item">
                            <strong>Alta gerencia</strong>
                            <textarea name="glos_alta_gerencia" class="form-control" rows="2">Persona o grupo de personas que gestionan y determinan lineamientos organizacionales.</textarea>
                        </div>

                        <div class="glossary-item">
                            <strong>Identificación de peligros y valoración de riesgos</strong>
                            <textarea name="glos_identificacion" class="form-control" rows="2">Método para identificar un peligro y establecer las características de este.</textarea>
                        </div>

                        <div class="glossary-item">
                            <strong>Peligro</strong>
                            <textarea name="glos_peligro" class="form-control" rows="2">Fuente, situación o acto con potencial de causar daño en la salud de los trabajadores, en los equipos o en las instalaciones.</textarea>
                        </div>

                        <div class="glossary-item mb-0">
                            <strong>Seguridad y Salud en el Trabajo</strong>
                            <textarea name="glos_sst" class="form-control" rows="3">Es la disciplina que trata de la prevención de las lesiones y las enfermedades causadas por las condiciones del trabajo, y de la protección y promoción de la salud de los trabajadores.</textarea>
                        </div>

                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Procedimiento</div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table-clean">
                                <thead>
                                    <tr>
                                        <th style="width:80px;">N°</th>
                                        <th>Actividad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="3">El área de SST debe establecer previamente los requerimientos en seguridad y salud en el trabajo para la adquisición de un nuevo bien o servicio.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="3">Realizar la solicitud para compra o alquiler, mediante recurso escrito, formato o correo electrónico.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="3">Se solicita con gerencia quien aprueba o no la compra de un nuevo bien o servicio.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="3">Gerencia aprueba y es quien autoriza la orden de pedido en la nueva compra y se envía al proveedor.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">5</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="4">Posterior a la adquisición se debe tener custodia de la información documentada de la nueva adquisición, factura, fichas técnicas, hojas de seguridad y/o tarjetas de emergencia, si aplica.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">6</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="3">Se debe registrar la adquisición en la matriz de adquisición de productos y en la gestión del cambio de ser necesario.</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">7</td>
                                        <td>
                                            <textarea name="proc_actividad[]" class="form-control" rows="4">Revisar el impacto que ocasiona el nuevo bien y servicio en las partes interesadas y valorarlo mediante la identificación de peligros y valoración de los riesgos.</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">Documentos de referencia</div>
                    <div class="section-body">
                        <div class="info-box mb-0">
                            <textarea name="txt_documentos_referencia" class="form-control" rows="8">Formato de solicitud de compra.
Orden de pedido.
Factura.
Ficha técnica, hoja de seguridad, tarjeta de emergencia.
Matriz de adquisiciones.
Matriz de identificación de peligros y valoración de riesgos.</textarea>
                        </div>
                    </div>
                </div>

                <div class="sign-grid">
                    <div class="sign">
                        <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                            <?php if(!empty($firmaSST)): ?>
                                <img src="<?= $firmaSST ?>" alt="Firma Elaborador" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                            <?php endif; ?>
                        </div>
                        ELABORÓ<br>
                        <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreSST) ?></span>
                    </div>
                    
                    <div class="sign">
                        <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                            <?php if(!empty($firmaSST)): ?>
                                <img src="<?= $firmaSST ?>" alt="Firma Revisor" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                            <?php endif; ?>
                        </div>
                        REVISÓ<br>
                        <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreSST) ?></span>
                    </div>

                    <div class="sign">
                        <div style="min-height: 40px; position:relative; margin-bottom:5px;">
                            <?php if(!empty($firmaRL)): ?>
                                <img src="<?= $firmaRL ?>" alt="Firma Aprobador" style="max-height: 40px; position:absolute; bottom:0; left:50%; transform:translateX(-50%);">
                            <?php endif; ?>
                        </div>
                        APROBÓ<br>
                        <span style="font-weight:normal; font-size:10px;"><?= htmlspecialchars($nombreRL) ?></span>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // Poner fecha de hoy por defecto si está vacía
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;

        const fmeta1 = document.getElementById("metaFecha1");
        if (fmeta1 && !fmeta1.value) fmeta1.value = `${y}-${m}-${dd}`;

        const fmeta2 = document.getElementById("metaFecha2");
        if (fmeta2 && !fmeta2.value) fmeta2.value = `${y}-${m}-${dd}`;
    }
    setHoy();

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
                    text: 'Procedimiento guardado correctamente',
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