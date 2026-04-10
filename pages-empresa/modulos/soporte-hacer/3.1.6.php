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
// Ajusta el ID de este ítem según tu BD (ej: 49 para Recomendaciones Médicas)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 49; 

// --- Lógica de Empresa (Logo y Datos desde tu API) ---
$logoEmpresaUrl = "";
$nombreEmpresaDefault = "MS InnovaTech"; 
$ciudadDefault = "Cali"; // Como la API no trae ciudad, dejamos una por defecto
$firmaNombreDefault = "Miguel Alonso Estepa Bonilla";
$firmaCcDefault = "CC.";
$firmaRlUrl = ""; // Aquí guardaremos la URL de la firma

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        // Tu API a veces manda el objeto directo o dentro de un array, validamos ambos:
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        
        // Mapeo EXACTO con los campos que me indicaste de tu API:
        if (!empty($empData['nombre_empresa'])) $nombreEmpresaDefault = $empData['nombre_empresa'];
        if (!empty($empData['nombre_rl'])) $firmaNombreDefault = $empData['nombre_rl'];
        if (!empty($empData['documento_rl'])) $firmaCcDefault = "CC. " . $empData['documento_rl'];
        if (!empty($empData['firma_rl'])) $firmaRlUrl = $empData['firma_rl'];
        
        // Por si en un futuro agregas el logo a la API
        if (!empty($empData['logo_url'])) $logoEmpresaUrl = $empData['logo_url'];
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE (Formatos Dinámicos)
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? $resFormulario['campos'] ?? null;

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

// 3. FUNCIÓN PARA LEER DATOS
function oldv($key, $default = '') {
    global $datosCampos;
    if (isset($datosCampos[$key]) && $datosCampos[$key] !== '') {
        return htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars((string)$default, ENT_QUOTES, 'UTF-8');
}

$recomendacionesDefault = [
    'Requiere uso de corrección visual permanente obligatoria para laborar. (Uso de lentes con filtro UV)',
    'Controles por optometría',
    'Acondicionamiento físico nutricional',
    'Mantener ergonomía de columna',
    'Practicar hábitos de vida saludables',
    'Dieta baja en grasas y harinas',
    'Ejercicio cardiovascular',
    'Realizar pausas activas ocupacionales según cronograma de la empresa',
    'Hacer uso de EPP y dotación suministrados por la compañía'
];

// Generar párrafo por defecto dinámico usando $nombreEmpresaDefault
$meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
$fechaActualText = date('d') . " de " . $meses[date('n')-1] . " de " . date('Y');
$parrafoDefault = "Teniendo en cuenta los exámenes médicos ocupacionales realizados por la IPS a solicitud de $nombreEmpresaDefault, el día $fechaActualText, se generaron las siguientes recomendaciones por parte del médico ocupacional, las cuales usted debe atender y entregar el soporte médico al área de Recursos Humanos.";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.6 - Recomendaciones Médicas Laborales</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f2f4f7;
            padding:20px;
            color:#111;
        }

        .contenedor{
            max-width:1100px;
            margin:0 auto;
            background:#fff;
            border:1px solid #bfc7d1;
            box-shadow:0 4px 18px rgba(0,0,0,.08);
        }

        .toolbar{
            position:sticky;
            top:0;
            z-index:100;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:12px;
            padding:14px 18px;
            background:#dde7f5;
            border-bottom:1px solid #c8d3e2;
        }

        .toolbar h1{
            font-size:20px;
            color:#213b67;
            font-weight:700;
        }

        .acciones{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn{
            border:none;
            padding:10px 18px;
            border-radius:8px;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            transition:.2s ease;
        }

        .btn:hover{
            transform:translateY(-1px);
            opacity:.95;
        }

        .btn-guardar{ background:#198754; color:#fff; }
        .btn-atras{ background:#6c757d; color:#fff; }
        .btn-imprimir{ background:#0d6efd; color:#fff; }

        .formulario{
            padding:18px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .encabezado td, .encabezado th{
            border:1px solid #6b6b6b;
            padding:10px;
            text-align:center;
            vertical-align:middle;
        }

        .logo-box{
            width:140px;
            height:65px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            color:#999;
            font-weight:bold;
            font-size:14px;
            text-align:center;
        }

        .titulo-principal{
            font-size:16px;
            font-weight:700;
        }

        .subtitulo{
            font-size:14px;
        }

        .carta{
            margin-top:18px;
            padding:26px 32px;
            border:1px solid #d7dde8;
            background:#fff;
        }

        .fila-derecha{
            display:flex;
            justify-content:flex-end;
            margin-bottom:20px;
        }

        .input-inline{
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:4px 6px;
            font-size:15px;
        }

        .input-inline:focus, .input-linea:focus, .rec-item input:focus, .firma input:focus, .textarea-ref:focus, .textarea-parrafo:focus {
            background: #f8fbff;
        }

        .input-ciudad{ min-width:130px; text-align:center; }
        .input-fecha{ min-width:220px; text-align:center; }

        .bloque{
            margin-bottom:18px;
        }

        .bloque p{
            font-size:15px;
            line-height:1.7;
            margin-bottom:12px;
            text-align:justify;
        }

        .etiqueta{
            font-weight:700;
            display:block;
            margin-bottom:6px;
        }

        .input-linea{
            width:100%;
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:6px 4px;
            font-size:15px;
        }

        .referencia{
            display:grid;
            grid-template-columns:130px 1fr;
            gap:10px;
            align-items:start;
            margin:18px 0;
        }

        .referencia label{
            font-weight:700;
            padding-top:6px;
        }

        .textarea-ref,
        .textarea-parrafo{
            width:100%;
            border:1px solid #b7b7b7;
            border-radius:6px;
            outline:none;
            padding:10px;
            font-size:15px;
            line-height:1.6;
            resize:none;
            overflow:hidden;
            min-height:64px;
            white-space:pre-wrap;
            word-break:break-word;
            background: transparent;
        }

        .textarea-parrafo{
            min-height:120px;
        }

        .recomendaciones{
            margin:12px 0 18px 0;
        }

        .rec-item{
            display:grid;
            grid-template-columns:34px 1fr;
            gap:10px;
            align-items:start;
            margin-bottom:8px;
        }

        .rec-item span{
            font-size:18px;
            line-height:1.6;
        }

        .rec-item input{
            width:100%;
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:6px 4px;
            font-size:15px;
            line-height:1.5;
        }

        .firma{
            margin-top:34px;
            max-width:380px;
        }

        .linea-firma{
            border-top:1px solid #000;
            margin-bottom:10px;
            width:100%;
        }

        .firma input{
            width:100%;
            border:none;
            border-bottom:1px solid #8c8c8c;
            outline:none;
            background:transparent;
            padding:6px 4px;
            font-size:15px;
            margin-bottom:8px;
        }

        .footer-info{
            margin-top:28px;
            text-align:center;
            font-size:13px;
            color:#444;
            border-top:2px solid #2d57ff;
            padding-top:12px;
        }

        @media print{
            body{
                background:#fff;
                padding:0;
            }

            .toolbar, .print-hide{
                display:none !important;
            }

            .contenedor{
                box-shadow:none;
                border:none;
            }

            .formulario{
                padding:8px;
            }

            .carta{
                border:none;
                padding:10px 4px;
            }

            .input-inline,
            .input-linea,
            .rec-item input,
            .firma input,
            .textarea-ref,
            .textarea-parrafo{
                border:none !important;
                background:transparent !important;
                padding-left:0;
                padding-right:0;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>3.1.6 - Recomendaciones Médicas Laborales</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Carta</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
    </div>

    <div class="formulario">
        <form id="form316">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">Versión: 2</td>
                </tr>
                <tr>
                    <td class="subtitulo">RECOMENDACIONES MÉDICAS LABORALES</td>
                    <td style="font-weight:700;">AN-PM-SST-01<br>Fecha: <?= date('d-m-Y') ?></td>
                </tr>
            </table>

            <div class="carta">

                <div class="fila-derecha">
                    <div>
                        <input class="input-inline input-ciudad" type="text" name="ciudad" value="<?= oldv('ciudad', $ciudadDefault) ?>">
                        ,
                        <input class="input-inline input-fecha" type="text" name="fecha_carta" value="<?= oldv('fecha_carta', $fechaActualText) ?>">
                    </div>
                </div>

                <div class="bloque">
                    <label class="etiqueta">Señor(a):</label>
                    <input class="input-linea" type="text" name="senor" value="<?= oldv('senor', 'Nombre del trabajador') ?>" placeholder="Nombre del trabajador">

                    <input class="input-linea" type="text" name="destinatario" value="<?= oldv('destinatario', 'Cargo o Dependencia') ?>" style="margin-top:8px;" placeholder="Cargo o Dependencia">
                </div>

                <div class="referencia">
                    <label>Referencia:</label>
                    <textarea class="textarea-ref" name="referencia"><?= oldv('referencia', 'SEGUIMIENTO A RECOMENDACIONES MÉDICAS DE EXÁMENES MÉDICOS OCUPACIONALES') ?></textarea>
                </div>

                <div class="bloque">
                    <p>Reciba un cordial saludo,</p>

                    <textarea class="textarea-parrafo" name="parrafo_principal"><?= oldv('parrafo_principal', $parrafoDefault) ?></textarea>

                    <p style="margin-top:14px;">
                        <input class="input-inline" type="text" name="plazo" value="<?= oldv('plazo', 'Estas recomendaciones se deben atender en un plazo máximo de 1 mes.') ?>" style="width:100%;">
                    </p>
                </div>

                <div class="recomendaciones">
                    <?php for ($i = 0; $i < 9; $i++): ?>
                        <div class="rec-item">
                            <span>•</span>
                            <input type="text" name="rec_<?= $i ?>" value="<?= oldv("rec_$i", $recomendacionesDefault[$i] ?? '') ?>">
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="bloque">
                    <p>Atentamente,</p>
                </div>

                <div class="firma">
                    <?php if(!empty($firmaRlUrl)): ?>
                        <img src="<?= htmlspecialchars($firmaRlUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Firma Representante" style="max-height: 80px; margin-bottom: 5px; display: block;">
                    <?php endif; ?>
                    
                    <div class="linea-firma"></div>
                    <input type="text" name="firma_nombre" value="<?= oldv('firma_nombre', $firmaNombreDefault) ?>">
                    <input type="text" name="firma_cc" value="<?= oldv('firma_cc', $firmaCcDefault) ?>">
                    <input type="text" name="firma_cargo" value="<?= oldv('firma_cargo', 'Representante Legal') ?>">
                    <input type="text" name="firma_fecha" value="<?= oldv('firma_fecha', 'Fecha de emisión: ' . date('d/m/Y')) ?>">
                </div>

                <div class="footer-info">
                    Generado por Sistema de Gestión - <?= $nombreEmpresaDefault ?>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Auto ajuste de textareas
function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea').forEach(textarea => {
        autoResizeTextarea(textarea);
        textarea.addEventListener('input', function () {
            autoResizeTextarea(this);
        });
    });
});

// Guardado del formulario vía Fetch
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form316');
    const formData = new FormData(form);
    
    // Construir el objeto JSON
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
                text: 'La carta de recomendaciones ha sido guardada correctamente.',
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