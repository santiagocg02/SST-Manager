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
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 31; 

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

// Función auxiliar para repoblar inputs/textareas con datos de la API o valores por defecto
function getValue($key, $default, $datosCampos) {
    return isset($datosCampos[$key]) ? $datosCampos[$key] : $default;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2.7.1 - PROCEDIMIENTO DE IDENTIFICACIÓN, EVALUACIÓN Y SEGUIMIENTO A REQUISITOS LEGALES</title>

    <link rel="stylesheet" href="../../../assets/css/toolbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --blue:#1f5fa8;
            --blue-soft:#eaf2ff;
            --line:#111;
            --gray:#666;
            --light:#f8fafc;
        }

        body{
            background:#f3f6fb;
            font-family: Arial, Helvetica, sans-serif;
            color:#111;
            margin:0;
        }

        .page-wrap{
            max-width: 1100px;
            margin: 16px auto 60px;
            padding: 0 12px;
        }

        .sst-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .sst-toolbar-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: #333;
            text-transform: uppercase;
        }
        .sst-toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .sheet{
            background:#fff;
            border:1px solid #d9e2ef;
            box-shadow:0 8px 24px rgba(15, 23, 42, .08);
            border-radius: 14px;
            overflow:hidden;
            margin-bottom: 20px;
        }

        .sheet-header{
            padding: 18px 20px 10px;
            border-bottom: 2px solid var(--blue);
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }

        .top-table{
            width:100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .top-table td,
        .top-table th{
            border:1px solid var(--line);
            padding:8px;
            vertical-align: middle;
            font-size: 13px;
            text-transform: uppercase;
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
            text-transform: uppercase;
        }

        .title-main-input{
            font-size:16px;
            font-weight:700;
            text-align:center;
            text-transform:uppercase;
            border: none;
            background: transparent;
            width: 100%;
            resize: none;
            outline: none;
        }

        .code-box-input{
            font-weight:700;
            text-align:center;
            font-size:14px;
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            text-transform: uppercase;
        }

        .meta-input,
        .line-input,
        .inline-input,
        textarea.form-control,
        select.form-select{
            border:1px solid #cfd8e3;
            border-radius:8px;
            font-size:14px;
            text-transform: uppercase;
        }

        .meta-input,
        .line-input,
        .inline-input{
            width:100%;
            padding:8px 10px;
            outline:none;
            background:#fff;
        }

        .meta-input:focus,
        .line-input:focus,
        .inline-input:focus,
        textarea.form-control:focus,
        select.form-select:focus{
            border-color:#6ea8fe;
            box-shadow:0 0 0 .15rem rgba(13,110,253,.12);
        }

        .doc-body{
            padding: 22px 20px 28px;
        }

        .doc-cover{
            border:1px solid #dbe5f0;
            border-radius:14px;
            padding:24px 18px;
            margin-bottom:22px;
            background:#fcfdff;
        }

        .cover-logo{
            width:180px;
            height:120px;
            border:1px dashed #aab7c7;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:0 auto 18px;
            color:#667085;
            font-weight:700;
            background:#fff;
            padding: 10px;
            text-transform: uppercase;
        }

        .cover-title-input{
            text-align:center;
            font-weight:700;
            font-size:18px;
            text-transform:uppercase;
            border: none;
            background: transparent;
            width: 100%;
            resize: none;
            outline: none;
            margin-bottom:18px;
        }

        .cover-grid row g-3 {
            text-transform: uppercase;
        }

        .muted-label {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 12px;
            color: #4b5563;
        }

        .cover-grid{
            max-width:700px;
            margin:0 auto;
        }

        .section-card{
            border:1px solid #d9e2ef;
            border-radius:14px;
            margin-bottom:18px;
            overflow:hidden;
            background:#fff;
        }

        .section-title{
            background:var(--blue);
            color:#fff;
            font-weight:700;
            padding:10px 14px;
            text-transform:uppercase;
            letter-spacing:.3px;
            font-size:14px;
        }

        .section-body{
            padding:14px;
        }

        .table-clean{
            width:100%;
            border-collapse: collapse;
        }

        .table-clean th,
        .table-clean td{
            border:1px solid #111;
            padding:10px;
            vertical-align: top;
            font-size:14px;
            text-transform: uppercase;
        }

        .table-clean th{
            background:#eef4ff;
            text-align:center;
        }

        .glossary-list{
            display:grid;
            grid-template-columns: 1fr;
            gap:10px;
        }

        .glossary-item{
            border:1px solid #e2e8f0;
            border-radius:12px;
            padding:12px;
            background:#fbfdff;
        }

        .glossary-item strong{
            color:var(--blue);
            display:block;
            margin-bottom:4px;
            text-transform: uppercase;
        }

        .step-block{
            border:1px solid #dbe4ef;
            border-radius:12px;
            padding:14px;
            margin-bottom:14px;
            background:#fff;
        }

        .step-block h6{
            font-weight:700;
            color:var(--blue);
            margin-bottom:10px;
            text-transform:uppercase;
            font-size:14px;
        }

        .convenciones{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap:10px;
        }

        .tag-box-input{
            border-radius:10px;
            padding:10px 12px;
            font-weight:700;
            font-size:13px;
            text-transform:uppercase;
            border:1px solid #dbe5f0;
            background:#f8fbff;
            text-align:center;
            width: 100%;
            outline: none;
        }

        .btn-agregar-punto {
            background-color: #10B981;
            color: white;
            border: none;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-agregar-punto:hover {
            background-color: #059669;
        }

        @media print{
            body{ background:#fff; }
            .page-wrap{ max-width:100%; margin:0; padding:0; }
            .sst-toolbar{ display:none !important; }
            .sheet{ border:none; box-shadow:none; border-radius:0; }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    
    <div class="sst-toolbar">
        <h1 class="sst-toolbar-title">PROCEDIMIENTO REQUISITOS LEGALES</h1>
        <div class="sst-toolbar-actions">
            <a href="../../index.php" class="btn btn-secondary btn-sm">VOLVER</a>
            <button type="button" id="btn-guardar-sst" class="btn btn-success btn-sm">
                <i class="fa-solid fa-save"></i> GUARDAR
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print"></i> IMPRIMIR
            </button>
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
                                    <img src="<?= $logoEmpresaUrl ?>" alt="LOGO EMPRESA" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                                <?php else: ?>
                                    LOGO EMPRESA
                                <?php endif; ?>
                            </div>
                        </td>
                        <td rowspan="3" style="width:52%;">
                            <textarea name="header_sistema_gestion" class="title-main-input" rows="2"><?= e(strtoupper(getValue('header_sistema_gestion', 'SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO', $datosCampos))) ?></textarea>
                        </td>
                        <td style="width:15%; font-weight:700;">VERSIÓN</td>
                        <td style="width:15%;">
                            <input type="text" name="meta_version" class="meta-input" value="<?= e(strtoupper(getValue('meta_version', '0', $datosCampos))) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">CÓDIGO</td>
                        <td>
                            <input type="text" name="meta_codigo" class="meta-input" value="<?= e(strtoupper(getValue('meta_codigo', 'AN-XX-SST-20', $datosCampos))) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">FECHA</td>
                        <td>
                            <input type="date" name="meta_fecha" class="meta-input" value="<?= e(getValue('meta_fecha', date('Y-m-d'), $datosCampos)) ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <input type="text" name="header_procedimiento_titulo" class="code-box-input" value="<?= e(strtoupper(getValue('header_procedimiento_titulo', 'PROCEDIMIENTO DE IDENTIFICACIÓN, EVALUACIÓN Y SEGUIMIENTO A REQUISITOS LEGALES', $datosCampos))) ?>">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="doc-body">
                <div class="doc-cover">
                    <div class="cover-logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="LOGO EMPRESA" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </div>

                    <textarea name="cover_titulo" class="cover-title-input" rows="2"><?= e(strtoupper(getValue('cover_titulo', 'PROCEDIMIENTO DE IDENTIFICACIÓN, EVALUACIÓN Y SEGUIMIENTO A REQUISITOS LEGALES', $datosCampos))) ?></textarea>

                    <div class="cover-grid row g-3">
                        <div class="col-md-6">
                            <label class="muted-label">VERSIÓN</label>
                            <input type="text" name="cover_version" class="line-input" value="<?= e(strtoupper(getValue('cover_version', '0', $datosCampos))) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="muted-label">FECHA</label>
                            <input type="date" name="cover_fecha" class="line-input" value="<?= e(getValue('cover_fecha', date('Y-m-d'), $datosCampos)) ?>">
                        </div>
                        <div class="col-12">
                            <label class="muted-label">NOMBRE DE LA EMPRESA</label>
                            <input type="text" name="cover_empresa" class="line-input" value="<?= e(strtoupper(getValue('cover_empresa', $nombreEmpresaLogeada, $datosCampos))) ?>" placeholder="NOMBRE DE LA EMPRESA">
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">OBJETIVO</div>
                    <div class="section-body">
                        <textarea name="txt_objetivo" class="form-control" rows="4"><?= e(strtoupper(getValue('txt_objetivo', 'IDENTIFICAR, TENER ACCESO A LOS REQUISITOS LEGALES Y OTROS QUE EN MATERIA DE SEGURIDAD Y SALUD EN EL TRABAJO (SST), VERIFICANDO EL CUMPLIMIENTO DE AQUELLOS QUE APLICAN A LAS ACTIVIDADES Y SERVICIOS DESARROLLADOS POR LA COMPAÑÍA. ASÍ MISMO, FIJAR LOS LINEAMIENTOS PARA MANTENER ACTUALIZADA LA INFORMACIÓN Y COORDINAR LAS COMUNICACIONES RELACIONADAS, CON EL FIN DE ASEGURAR EL CUMPLIMIENTO DE LOS REQUISITOS LEGALES Y DE OTRA ÍNDOLE EN SEGURIDAD Y SALUD EN EL TRABAJO.', $datosCampos))) ?></textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">ALCANCE</div>
                    <div class="section-body">
                        <textarea name="txt_alcance" class="form-control" rows="3"><?= e(strtoupper(getValue('txt_alcance', 'EL ALCANCE DE ESTE DOCUMENTO APLICA A LA IDENTIFICACIÓN, ACTUALIZACIÓN, VERIFICACIÓN Y COMUNICACIÓN DE LOS REQUISITOS LEGALES Y OTROS APLICABLES EN SEGURIDAD Y SALUD EN EL TRABAJO.', $datosCampos))) ?></textarea>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">RESPONSABILIDADES</div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table-clean" id="tabla-responsabilidades">
                                <thead>
                                    <tr>
                                        <th style="width:30%;">RESPONSABLE</th>
                                        <th>RESPONSABILIDADES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="resp_cargo_1" class="line-input" value="<?= e(strtoupper(getValue('resp_cargo_1', 'ENCARGADO DEL SG-SST', $datosCampos))) ?>">
                                        </td>
                                        <td>
                                            <textarea name="resp_desc_1" class="form-control" rows="4"><?= e(strtoupper(getValue('resp_desc_1', "IDENTIFICAR, EVALUAR CUMPLIMIENTO Y MANTENER ACTUALIZADOS LOS REQUISITOS LEGALES APLICABLES EN MATERIA DE SST.\nALIMENTAR LA MATRIZ DE REQUISITOS LEGALES Y OTROS.\nREALIZAR LA EVALUACIÓN DEL CUMPLIMIENTO LEGAL.", $datosCampos))) ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="resp_cargo_2" class="line-input" value="<?= e(strtoupper(getValue('resp_cargo_2', 'ENCARGADO DEL SG-SST, ASESOR EXTERNO', $datosCampos))) ?>">
                                        </td>
                                        <td>
                                            <textarea name="resp_desc_2" class="form-control" rows="2"><?= e(strtoupper(getValue('resp_desc_2', 'EVALUAR EL CUMPLIMIENTO LEGAL.', $datosCampos))) ?></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn-agregar-punto" onclick="agregarFilaResponsabilidad()">➕ AGREGAR NUEVO RESPONSABLE</button>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">GLOSARIO</div>
                    <div class="section-body">
                        <div class="glossary-list" id="lista-glosario">
                            <div class="glossary-item">
                                <input type="text" name="glos_tit_articulo" class="line-input fw-bold mb-1 text-primary border-0 bg-transparent p-0" value="<?= e(strtoupper(getValue('glos_tit_articulo', 'ARTÍCULO', $datosCampos))) ?>">
                                <textarea name="glos_articulo" class="form-control" rows="2"><?= e(strtoupper(getValue('glos_articulo', 'CADA UNA DE LAS PARTES MÁS O MENOS INDEPENDIENTES EN QUE SE DIVIDE UN ESCRITO JURÍDICO, COMO UNA LEY O REGLAMENTO.', $datosCampos))) ?></textarea>
                            </div>
                            <div class="glossary-item">
                                <input type="text" name="glos_tit_circular" class="line-input fw-bold mb-1 text-primary border-0 bg-transparent p-0" value="<?= e(strtoupper(getValue('glos_tit_circular', 'CIRCULAR', $datosCampos))) ?>">
                                <textarea name="glos_circular" class="form-control" rows="2"><?= e(strtoupper(getValue('glos_circular', 'ESCRITO DIRIGIDO A VARIAS PERSONAS PARA COMUNICAR ALGO.', $datosCampos))) ?></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn-agregar-punto" onclick="agregarItemGlosario()">➕ AGREGAR TÉRMINO AL GLOSARIO</button>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">PROCEDIMIENTO</div>
                    <div class="section-body">

                        <div class="step-block">
                            <input type="text" name="paso1_titulo" class="line-input fw-bold mb-2 text-primary border-0 bg-transparent p-0" style="font-size:14px;" value="<?= e(strtoupper(getValue('paso1_titulo', '1. IDENTIFICACIÓN DE LOS REQUISITOS LEGALES', $datosCampos))) ?>">
                            <textarea name="paso1_desc" class="form-control mb-3" rows="3"><?= e(strtoupper(getValue('paso1_desc', 'EL ENCARGADO DEL SG-SST IDENTIFICA LOS REQUISITOS LEGALES Y DE OTRA ÍNDOLE APLICABLES EN SST.', $datosCampos))) ?></textarea>

                            <label class="muted-label">FUENTES DE INFORMACIÓN PARA ACTUALIZACIÓN Y CONSULTA</label>
                            <div class="table-responsive">
                                <table class="table-clean" id="tabla-fuentes">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th>ENTIDAD / FUENTE</th>
                                            <th>ENLACE / REFERENCIA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center N-fila">1</td>
                                            <td><input type="text" name="fuente_entidad_1" class="line-input border-0 bg-transparent p-1" value="<?= e(strtoupper(getValue('fuente_entidad_1', 'MINISTERIO DEL TRABAJO', $datosCampos))) ?>"></td>
                                            <td><input type="text" name="fuente_enlace_1" class="line-input border-0 bg-transparent p-1" value="<?= e(strtoupper(getValue('fuente_enlace_1', 'WWW.MINTRABAJO.COM.CO', $datosCampos))) ?>"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center N-fila">2</td>
                                            <td><input type="text" name="fuente_entidad_2" class="line-input border-0 bg-transparent p-1" value="<?= e(strtoupper(getValue('fuente_entidad_2', 'MINISTERIO DE TRANSPORTE', $datosCampos))) ?>"></td>
                                            <td><input type="text" name="fuente_enlace_2" class="line-input border-0 bg-transparent p-1" value="<?= e(strtoupper(getValue('fuente_enlace_2', 'WWW.MINTRANSPORTE.GOV.CO', $datosCampos))) ?>"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn-agregar-punto" onclick="agregarFilaFuente()">➕ AGREGAR NUEVA FUENTE</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // LÓGICA DINÁMICA PARA REPETICIÓN DE TABLAS Y ELEMENTOS
    let contadorResp = 2;
    function agregarFilaResponsabilidad() {
        contadorResp++;
        const tbody = document.querySelector("#tabla-responsabilidades tbody");
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td>
                <input type="text" name="resp_cargo_${contadorResp}" class="line-input" placeholder="INGRESE CARGO RESPONSABLE">
            </td>
            <td>
                <textarea name="resp_desc_${contadorResp}" class="form-control" rows="3" placeholder="DESCRIPCIÓN DE LA RESPONSABILIDAD"></textarea>
            </td>
        `;
        tbody.appendChild(nuevaFila);

        // TRANSFORMAR AUTOMÁTICAMENTE LA ENTRADA A MAYÚSCULAS
        nuevaFila.querySelectorAll("input, textarea").forEach(el => {
            el.addEventListener("input", (e) => e.target.value = e.target.value.toUpperCase());
        });
    }

    function agregarItemGlosario() {
        const lista = document.getElementById("lista-glosario");
        const nuevoItem = document.createElement("div");
        nuevoItem.className = "glossary-item mt-2";
        
        const txtTitulo = prompt("INGRESE EL NUEVO TÉRMINO/CONCEPTO:");
        if (txtTitulo && txtTitulo.trim() !== "") {
            const index = lista.children.length + 1;
            nuevoItem.innerHTML = `
                <input type="text" name="glos_tit_dinamico_${index}" class="line-input fw-bold mb-1 text-primary border-0 bg-transparent p-0" value="${txtTitulo.toUpperCase()}">
                <textarea name="glos_desc_dinamico_${index}" class="form-control" rows="2" placeholder="DEFINICIÓN DEL TÉRMINO"></textarea>
            `;
            lista.appendChild(nuevoItem);
            
            nuevoItem.querySelector("textarea").addEventListener("input", (e) => e.target.value = e.target.value.toUpperCase());
        }
    }

    function agregarFilaFuente() {
        const tbody = document.querySelector("#tabla-fuentes tbody");
        const totalFilas = tbody.children.length + 1;
        const nuevaFila = document.createElement("tr");
        nuevaFila.innerHTML = `
            <td class="text-center">${totalFilas}</td>
            <td><input type="text" name="fuente_entidad_${totalFilas}" class="line-input border-0 bg-transparent p-1" placeholder="NUEVA ENTIDAD"></td>
            <td><input type="text" name="fuente_enlace_${totalFilas}" class="line-input border-0 bg-transparent p-1" placeholder="WWW.EJEMPLO.COM"></td>
        `;
        tbody.appendChild(nuevaFila);

        nuevaFila.querySelectorAll("input").forEach(el => {
            el.addEventListener("input", (e) => e.target.value = e.target.value.toUpperCase());
        });
    }

    // FORZAR MAYÚSCULAS DESDE EL INCIO EN LOS INPUTS YA EXISTENTES
    document.querySelectorAll("input, textarea").forEach(el => {
        if(el.type !== 'date') {
            el.addEventListener("input", (e) => e.target.value = e.target.value.toUpperCase());
        }
    });
</script>

</body>
</html>