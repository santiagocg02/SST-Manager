<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN A LA API
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token   = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos para el MEDEVAC (ej. 57)
$idItem  = isset($_GET['item']) ? (int)$_GET['item'] : 57;

// --- Lógica de Empresa (Logo) ---
$logoEmpresaUrl = "";
$nombreSST = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
        $nombreSST = $empData['nombre_sst'] ?? $empData['responsable_sst'] ?? '';
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
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

// Función para leer datos desde la API
function oldv($key, $default = '') {
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] !== '') ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDEVAC | Plan de Evacuación Médica</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1100px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:18px;color:#213b67;font-weight:700; text-transform: uppercase;}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}
        .contenido{padding:30px}

        table{width:100%;border-collapse:collapse;table-layout:fixed}
        .encabezado td,.encabezado th,.tabla-datos td,.tabla-datos th{
            border:1px solid #6b6b6b;
            padding:8px;
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
        .subtitulo{font-size:13px;font-weight:700}

        .bg-blue-soft { background: #cfe2f7; font-weight: bold; }

        .seccion-title{
            background: #1a4175;
            color: #fff;
            padding: 8px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 13px;
        }

        .tabla-datos th {
            background: #cfe2f7;
            text-align: center;
            font-size: 12px;
        }

        .tabla-datos td {
            font-size: 12px;
        }

        input[type="text"], textarea {
            width: 100%;
            max-width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding: 4px;
            font-size: 12px;
            line-height: 1.3;
        }

        .center-text { text-align: center; }

        textarea {
            resize: vertical;
            min-height: 60px;
            white-space: pre-wrap;
        }

        .obs-box {
            border: 1px solid #6b6b6b;
            padding: 10px;
            background: #fff;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar, .print-hide { display: none !important; }
            .contenedor { border: none; box-shadow: none; max-width: 100%; margin: 0; }
            .contenido { padding: 10px; }
            input, textarea { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>Plan de Evacuación Médica (MEDEVAC)</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="formMedevac">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width: 25%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal bg-blue-soft" style="width: 55%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width: 20%; font-weight:700;">Versión: 02</td>
                </tr>
                <tr>
                    <td class="subtitulo bg-blue-soft">PROCEDIMIENTO DE RESPUESTA MÉDICA - MEDEVAC</td>
                    <td style="font-weight:700;">FR-SST-18<br>Fecha: <?= date('d/m/Y') ?></td>
                </tr>
            </table>

            <div class="seccion-title">Información del Centro de Trabajo</div>
            <table class="tabla-datos">
                <tr>
                    <th style="width: 25%;">Ubicación / Proyecto:</th>
                    <td style="width: 40%;"><input type="text" name="ubicacion" value="<?= oldv('ubicacion') ?>" placeholder="Nombre de la sede o proyecto"></td>
                    <th style="width: 15%;">Responsable:</th>
                    <td style="width: 20%;"><input type="text" name="responsable" value="<?= oldv('responsable', $nombreSST) ?>"></td>
                </tr>
            </table>

            <div class="seccion-title">Centros Asistenciales y de Emergencia</div>
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th style="width: 30%;">Entidad / Centro Médico</th>
                        <th style="width: 10%;">Nivel</th>
                        <th style="width: 30%;">Dirección</th>
                        <th style="width: 15%;">Teléfono de Emergencia</th>
                        <th style="width: 15%;">Tiempo Estimado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1; $i<=3; $i++): ?>
                    <tr>
                        <td><input type="text" name="entidad_<?= $i ?>" value="<?= oldv('entidad_'.$i) ?>"></td>
                        <td><input type="text" class="center-text" name="nivel_<?= $i ?>" value="<?= oldv('nivel_'.$i) ?>"></td>
                        <td><input type="text" name="dir_<?= $i ?>" value="<?= oldv('dir_'.$i) ?>"></td>
                        <td><input type="text" class="center-text" name="tel_<?= $i ?>" value="<?= oldv('tel_'.$i) ?>"></td>
                        <td><input type="text" class="center-text" name="tiempo_<?= $i ?>" value="<?= oldv('tiempo_'.$i) ?>"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="seccion-title">Protocolo de Comunicación (En caso de Accidente)</div>
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th style="width: 8%;">PASO</th>
                        <th style="width: 52%;">ACCIÓN A REALIZAR</th>
                        <th style="width: 40%;">CONTACTO / CARGO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center-text" style="font-weight: bold;">1</td>
                        <td>Asegurar el área y prestar primeros auxilios básicos.</td>
                        <td class="center-text">Primer respondiente / Brigadista</td>
                    </tr>
                    <tr>
                        <td class="center-text" style="font-weight: bold;">2</td>
                        <td>Comunicar al Jefe de SST o Coordinador de Emergencias.</td>
                        <td><input type="text" class="center-text" name="contacto_paso_2" value="<?= oldv('contacto_paso_2', $nombreSST) ?>"></td>
                    </tr>
                    <tr>
                        <td class="center-text" style="font-weight: bold;">3</td>
                        <td>Llamar a la línea de emergencias o ambulancia.</td>
                        <td><input type="text" class="center-text" name="contacto_paso_3" value="<?= oldv('contacto_paso_3', 'Línea 123 / ARL') ?>"></td>
                    </tr>
                </tbody>
            </table>

            <div class="seccion-title">Observaciones Especiales</div>
            <div class="obs-box">
                <textarea name="observaciones"><?= oldv('observaciones', 'Indicar rutas alternas, convenios específicos con clínicas o tipos de transporte disponibles.') ?></textarea>
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
    const form = document.getElementById('formMedevac');
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
                text: 'El Plan MEDEVAC se ha guardado correctamente.',
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