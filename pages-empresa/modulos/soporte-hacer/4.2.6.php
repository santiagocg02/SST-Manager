<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN A LA API
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos para este formato (ej. 55)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 55;

// --- Lógica de Empresa (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE
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

// Función para leer datos desde la API
function oldv($key, $default = '')
{
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] !== '') ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$datos = [
    'version'             => oldv('version', '0'),
    'codigo'              => oldv('codigo', 'AN-SST-21'),
    'fecha_documento'     => oldv('fecha_documento', date('Y-m-d')),
    'actualizado_por'     => oldv('actualizado_por', ''),
    'fecha_actualizacion' => oldv('fecha_actualizacion', ''),
    'nota_r'              => oldv('nota_r', 'R: Requerido'),
    'nota_s'              => oldv('nota_s', 'S: Según necesidad'),
];

// Estructura base para cuando no hay datos guardados
$itemsDefecto = [
    ['area'=>'PROTECCIÓN PARA LA CABEZA','titulo'=>'Casco de seguridad Tipo E y G Clase 1','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'NTC 1523, ANSI Z89.1','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LA CABEZA','titulo'=>'Barbuquejo 3 puntas','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LA CABEZA','titulo'=>'Capuchón algodón','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN VISUAL Y FACIAL','titulo'=>'Gafas de seguridad antiempañante lente claro','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'ANSI Z87.1','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN VISUAL Y FACIAL','titulo'=>'Careta en malla','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 3610','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN RESPIRATORIA','titulo'=>'Respirador 3M medio rostro para gases y vapores','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 1584, NTC 1728, NTC 1733, Z-81','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN RESPIRATORIA','titulo'=>'Filtro 3M 6003 vapores orgánicos y gases ácidos','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 1584, NTC 1728, NTC 1733, Z-81','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN RESPIRATORIA','titulo'=>'Filtro 3M 2097 para humos metálicos y material particulado','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 1584 / NTC 1728','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN RESPIRATORIA','titulo'=>'Mascarilla 3M 8210V','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'Aprobado NIOSH bajo especificación N95 de la norma 42CFR84','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN RESPIRATORIA','titulo'=>'Mascarilla desechable','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN AUDITIVA','titulo'=>'Protector auditivo de copa adaptable al casco','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 3852','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN AUDITIVA','titulo'=>'Protector auditivo de inserción de silicona','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 2272, ANSI S3.19, Z-84','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guantes carnaza reforzado 5 dedos en vaqueta','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'NTC 2190','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guantes de vaqueta reforzado largo','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 2190','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guantes carnaza soldador con resistencia a altas temperaturas','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'CE EN 388 y EN 407 Categoría 2','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guantes nitrilo largos calibre 18','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'BS-EN 420-1994, BS-EN 388-1994, BS-EN 374-1994','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guantes nitrilo cortos calibre 18','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'BS-EN 420-1994, BS-EN 388-1994, BS-EN 374-1994','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guante industrial eterna calibre 25 en la palma','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN DE MANOS','titulo'=>'Guante de nylon recubierto en nitrilo','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'CE EN4131','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Overol enterizo antifluido manga larga','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'ISO 13034:1997 / ASTM / NTC 3583 / NTC 4615','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Delantal plástico PVC','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Delantal de carnaza - peto','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 4615','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Canilleras','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Impermeable de dos piezas','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'N/A','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Arnés multipropósito para trabajo en alturas','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'ANSI Z359.1:1992 / Z-349.1 / A 10.14','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN CORPORAL','titulo'=>'Eslinga','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'ANSI Z359.1:1992 / Z-349.1 / A 10.14','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LOS PIES','titulo'=>'Botas de seguridad con puntera','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'NTC 2396, NTC 2257, ANSI Z41, Z-41 y Z-195','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LOS PIES','titulo'=>'Botas de seguridad sin puntera','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 2396, NTC 2257, ANSI Z41, Z-41 y Z-196','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LOS PIES','titulo'=>'Botas de caucho con puntera de seguridad','cargo'=>'','tipo'=>'R','imagen'=>'','norma'=>'NTC 2396, NTC 2257, ANSI Z41, Z-41 y Z-195','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LOS PIES','titulo'=>'Botas de caucho sin puntera de seguridad','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 2396, NTC 2257, ANSI Z41, Z-41 y Z-195','uso'=>'','reposicion'=>'','tiempo'=>''],
    ['area'=>'PROTECCIÓN PARA LOS PIES','titulo'=>'Bota tipo soldador','cargo'=>'','tipo'=>'S','imagen'=>'','norma'=>'NTC 2396, NTC 2257, ANSI Z41, Z-41 y Z-195','uso'=>'','reposicion'=>'','tiempo'=>''],
];

// Reconstruir la matriz desde API o por defecto
$items = [];
foreach ($itemsDefecto as $i => $d) {
    $items[$i] = [
        'area'       => oldv("items[$i][area]", $d['area']),
        'titulo'     => oldv("items[$i][titulo]", $d['titulo']),
        'cargo'      => oldv("items[$i][cargo]", $d['cargo']),
        'tipo'       => oldv("items[$i][tipo]", $d['tipo']),
        'imagen'     => oldv("items[$i][imagen]", $d['imagen']),
        'norma'      => oldv("items[$i][norma]", $d['norma']),
        'uso'        => oldv("items[$i][uso]", $d['uso']),
        'reposicion' => oldv("items[$i][reposicion]", $d['reposicion']),
        'tiempo'     => oldv("items[$i][tiempo]", $d['tiempo']),
    ];
}

$areas = [];
foreach ($items as $i => $item) {
    $areas[$item['area']][] = ['index' => $i, 'data' => $item];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.2.6 Matriz de Elementos de Protección Personal</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1800px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:20px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}
        .contenido{padding:18px}
        
        table{width:100%;border-collapse:collapse;table-layout:fixed}
        .encabezado td,.encabezado th,.tabla-matriz td,.tabla-matriz th,.tabla-datos td,.tabla-datos th{
            border:1px solid #6b6b6b;
            padding:5px;
            vertical-align:middle;
            word-break:break-word;
            overflow-wrap:anywhere;
        }
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{
            width:140px;height:65px;display:flex;align-items:center;justify-content:center;
            margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center
        }
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px;font-weight:700}

        .seccion-title{
            margin:18px 0 8px;
            font-size:13px;
            color:#213b67;
            font-style:italic;
            font-weight:700;
        }

        input[type="text"], textarea, select{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px 4px;
            font-size:12px;
            line-height:1.25;
        }

        textarea{
            resize:vertical;
            min-height:56px;
            white-space:pre-wrap;
        }

        .tabla-wrap{
            width:100%;
            overflow-x:auto;
        }

        .tabla-matriz{
            min-width:1900px;
            width:1900px;
        }

        .tabla-matriz thead th{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            font-size:11px;
            line-height:1.2;
        }

        .area-head{
            background:#dbe8fb !important;
            color:#213b67 !important;
            font-weight:700;
            text-align:left !important;
            font-size:12px !important;
        }

        .tipo-cell{
            text-align:center;
            font-weight:700;
        }

        .tipo-r{color:#b91c1c; font-weight:700;}
        .tipo-s{color:#1d4ed8; font-weight:700;}

        .img-ref{min-height:48px;}

        .tabla-datos td:first-child{
            width:20%;
            font-weight:700;
            background:#f8fafc;
        }

        .nota-box{
            border:1px solid #6b6b6b;
            padding:10px 12px;
            font-size:12px;
            line-height:1.5;
            background:#fff;
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar, .print-hide{display:none !important}
            .contenedor{box-shadow:none;border:none}
            .contenido{padding:6px}
            .tabla-wrap{overflow:visible}
            .tabla-matriz{min-width:auto;width:100%}
            input, textarea, select{border:none !important;box-shadow:none !important}
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>4.2.6 Matriz de Elementos de Protección Personal</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="form426">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:64%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:18%;font-weight:700;"><?php echo $datos['version']; ?></td>
                </tr>
                <tr>
                    <td class="subtitulo">MATRIZ DE ELEMENTOS DE PROTECCIÓN PERSONAL</td>
                    <td style="font-weight:700;"><?php echo $datos['codigo']; ?><br><?php echo $datos['fecha_documento']; ?></td>
                </tr>
            </table>

            <div class="seccion-title">1. Información general</div>
            <table class="tabla-datos">
                <tr>
                    <td>ACTUALIZADO POR</td>
                    <td><input type="text" name="actualizado_por" value="<?php echo $datos['actualizado_por']; ?>"></td>
                </tr>
                <tr>
                    <td>FECHA DE ACTUALIZACIÓN</td>
                    <td><input type="text" name="fecha_actualizacion" value="<?php echo $datos['fecha_actualizacion']; ?>"></td>
                </tr>
                <tr>
                    <td>NOTA TIPO R</td>
                    <td><input type="text" name="nota_r" value="<?php echo $datos['nota_r']; ?>"></td>
                </tr>
                <tr>
                    <td>NOTA TIPO S</td>
                    <td><input type="text" name="nota_s" value="<?php echo $datos['nota_s']; ?>"></td>
                </tr>
            </table>

            <div class="seccion-title">2. Matriz de EPP</div>
            <div class="tabla-wrap">
                <table class="tabla-matriz">
                    <thead>
                        <tr>
                            <th style="width:11%;">ÁREA</th>
                            <th style="width:15%;">TÍTULO</th>
                            <th style="width:10%;">CARGO / OCUPACIÓN</th>
                            <th style="width:4%;">TIPO</th>
                            <th style="width:8%;">IMAGEN / REFERENCIA</th>
                            <th style="width:13%;">NORMA</th>
                            <th style="width:17%;">USO</th>
                            <th style="width:13%;">CRITERIO DE REPOSICIÓN</th>
                            <th style="width:9%;">TIEMPO DE REPOSICIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($areas as $area => $rows): ?>
                            <tr>
                                <td colspan="9" class="area-head"><?php echo htmlspecialchars($area, ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php foreach ($rows as $row): ?>
                                <?php $i = $row['index']; $item = $row['data']; ?>
                                <tr>
                                    <td style="display:none;"><input type="hidden" name="items[<?php echo $i; ?>][area]" value="<?php echo htmlspecialchars($item['area'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                    <td><?php echo htmlspecialchars($item['area'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><textarea name="items[<?php echo $i; ?>][titulo]"><?php echo htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                    <td><input type="text" name="items[<?php echo $i; ?>][cargo]" value="<?php echo htmlspecialchars($item['cargo'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                    <td class="tipo-cell">
                                        <select name="items[<?php echo $i; ?>][tipo]" class="<?php echo strtoupper($item['tipo']) === 'R' ? 'tipo-r' : 'tipo-s'; ?>">
                                            <option value="R" <?php echo strtoupper($item['tipo']) === 'R' ? 'selected' : ''; ?>>R</option>
                                            <option value="S" <?php echo strtoupper($item['tipo']) === 'S' ? 'selected' : ''; ?>>S</option>
                                        </select>
                                    </td>
                                    <td class="img-ref"><textarea name="items[<?php echo $i; ?>][imagen]"><?php echo htmlspecialchars($item['imagen'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                    <td><textarea name="items[<?php echo $i; ?>][norma]"><?php echo htmlspecialchars($item['norma'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                    <td><textarea name="items[<?php echo $i; ?>][uso]"><?php echo htmlspecialchars($item['uso'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                    <td><textarea name="items[<?php echo $i; ?>][reposicion]"><?php echo htmlspecialchars($item['reposicion'], ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                    <td><input type="text" name="items[<?php echo $i; ?>][tiempo]" value="<?php echo htmlspecialchars($item['tiempo'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="seccion-title">3. Notas</div>
            <div class="nota-box">
                <strong><?php echo htmlspecialchars($datos['nota_r'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                <strong><?php echo htmlspecialchars($datos['nota_s'], ENT_QUOTES, 'UTF-8'); ?></strong><br><br>
                Especial para proteger frente a exposición a partículas, sustancias químicas, vapores orgánicos, material particulado, humos metálicos y trabajos en alturas. Esta matriz puede ser ajustada según la necesidad de cada cargo y actividad.
            </div>
        </form>
    </div>
</div>

<script>
// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form426');
    const formData = new FormData(form);
    
    // Convertimos el formulario en un objeto JSON limpio
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
                id_empresa: <?= $empresa ?>,
                id_item_sst: <?= $idItem ?>,
                datos: datosJSON
            })
        });

        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'La Matriz de EPP se ha guardado correctamente.',
                icon: 'success',
                confirmButtonColor: '#198754'
            }).then(() => {
                window.location.reload();
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