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
// Ajusta el ID de este ítem según tu base de datos (Ej: 33)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 33; 

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
    <title>2.8.1-2 Reporte de actos y condiciones inseguras</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body{
            background:#f2f6fb;
            font-family:Arial, Helvetica, sans-serif;
            margin:0;
            color:#111;
        }

        .page{
            max-width:1100px;
            margin: 16px auto 60px;
            background:white;
            padding:20px;
            box-shadow:0 10px 25px rgba(0,0,0,.1);
            border-radius: 8px;
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

        .table-sst{
            width:100%;
            border-collapse:collapse;
        }

        .table-sst td, .table-sst th{
            border:1px solid #111;
            padding:8px;
            font-size:13px;
            vertical-align: middle;
        }

        .header-blue{
            background:#1f5fa8;
            color:white;
            font-weight:bold;
            text-align:center;
        }

        input[type=text], input[type=date]{
            width:100%;
            border:none;
            outline:none;
            font-family: Arial, sans-serif;
            font-size: 13px;
            background: transparent;
        }
        
        input[type=text]:focus, input[type=date]:focus, textarea:focus {
            background: #f8fbff;
        }

        textarea{
            width:100%;
            border:none;
            outline:none;
            resize:vertical;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .checkbox, .radio{
            width:16px;
            height:16px;
            cursor: pointer;
            margin-right: 4px;
            vertical-align: middle;
        }

        label {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

        .logo{
            height:80px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:1px dashed #aaa;
            color: #777;
            font-weight: bold;
        }

        .level-alto{background:#ff3b3b;color:white;font-weight:bold;text-align:center;}
        .level-medio{background:#ffd700;font-weight:bold;text-align:center; color:#111;}
        .level-bajo{background:#2ecc71;color:white;font-weight:bold;text-align:center;}

        @media print{
            body{ background:#fff; margin:0; padding:0; }
            .page{ max-width:100%; box-shadow:none; padding:0; margin:0; }
            .toolbar, .print-hide{ display:none !important; }
        }

    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>

<body>

<div class="page">

    <div class="toolbar print-hide">
        <div style="display:flex; gap:8px;">
            <button class="btn-action" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-action" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-success-action" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-primary-action" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
        <div class="tiny text-end">
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">REPORTE ACTOS Y CONDICIONES</span><br>
            Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <table class="table-sst">

            <tr>
                <td rowspan="2" style="width:140px;">
                    <div class="logo" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                        <?php if(!empty($logoEmpresaUrl)): ?>
                            <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 75px; object-fit: contain;">
                        <?php else: ?>
                            LOGO
                        <?php endif; ?>
                    </div>
                </td>
                <td colspan="3" style="text-align:center;font-weight:bold; font-size:14px;">
                    SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                </td>
                <td style="width:80px;text-align:center; font-weight:bold;">
                    <input type="text" name="meta_version" style="text-align:center; font-weight:bold;" value="0">
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight:bold; text-align:center; font-size:12px;">
                    FORMATO PARA EL REPORTE DE ACTOS Y CONDICIONES INSEGURAS Y AUTOREPORTE CONDICIONES EN SALUD
                </td>
                <td style="text-align:center; font-weight:bold;">RE-SST-26</td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">I. IDENTIFICACIÓN</td>
            </tr>
            <tr>
                <td style="width:160px; font-weight:bold;">Fecha</td>
                <td colspan="2"><input type="date" name="fecha_reporte" id="metaFecha"></td>
                <td style="width:120px; font-weight:bold;">Consecutivo</td>
                <td><input type="text" name="consecutivo" placeholder="N°"></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Nombre de quien reporta</td>
                <td colspan="4"><input type="text" name="nombre_reporta" placeholder="Nombre completo"></td>
            </tr>
            <tr>
                <td colspan="4">
                    <label style="margin-right:15px;"><input type="radio" name="tipo_vinculacion" value="Funcionario" class="radio"> Funcionario</label>
                    <label style="margin-right:15px;"><input type="radio" name="tipo_vinculacion" value="Contratista" class="radio"> Contratista</label>
                    <label style="margin-right:15px;"><input type="radio" name="tipo_vinculacion" value="Visitante" class="radio"> Visitante</label>
                    <label><input type="radio" name="tipo_vinculacion" value="Otro" class="radio"> Otro</label>
                </td>
                <td><input type="text" name="tipo_vinculacion_otro" placeholder="¿Cuál?"></td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">II. DESCRIPCIÓN DEL REPORTE</td>
            </tr>
            <tr>
                <td colspan="5">
                    <textarea name="desc_reporte" rows="4" placeholder="Describa el evento, acto o condición identificada..."></textarea>
                </td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">III. ACTOS INSEGUROS</td>
            </tr>
            <tr>
                <td colspan="4">No uso o uso inapropiado de elementos de protección personal</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="No uso o uso inapropiado de EPP" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Realizar labores de mantenimiento sin señalizar</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Mantenimiento sin señalizar" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Realizar labores de aseo y limpieza sin señalizar</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Aseo sin señalizar" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Hacer bromas o juegos pesados</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Bromas o juegos pesados" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Agarrar o manipular objetos de forma insegura</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Manipulacion insegura de objetos" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Usar las manos en lugar de herramientas</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Usar manos por herramientas" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Errores en conducción de vehículos</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Errores conducción" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Trabajar en alturas sin elementos adecuados</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Alturas sin elementos" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Otro</td>
                <td style="text-align:center;"><input type="checkbox" name="actos_inseguros[]" value="Otro" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="5">¿Cuál? <input type="text" name="acto_inseguro_otro" style="width: 80%; display:inline-block; border-bottom:1px solid #ccc;"></td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">IV. CONDICIONES INSEGURAS</td>
            </tr>
            <tr>
                <td colspan="4">Áreas sin señalización de emergencia</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Sin senalizacion de emergencia" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Ruido excesivo</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Ruido excesivo" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Espacios inadecuados de circulación</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Espacios circulacion inadecuados" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Ventilación inadecuada</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Ventilacion inadecuada" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Iluminación deficiente</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Iluminacion deficiente" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Materiales almacenados incorrectamente</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Materiales almacenados incorrectamente" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Techos en mal estado</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Techos mal estado" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="4">Otro</td>
                <td style="text-align:center;"><input type="checkbox" name="condiciones_inseguras[]" value="Otro" class="checkbox"></td>
            </tr>
            <tr>
                <td colspan="5">¿Cuál? <input type="text" name="condicion_insegura_otro" style="width: 80%; display:inline-block; border-bottom:1px solid #ccc;"></td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">V. AUTOREPORTE CONDICIONES DE SALUD</td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="salud[]" value="Nervioso" class="checkbox"> Nervioso</label></td>
                <td colspan="3"><label><input type="checkbox" name="salud[]" value="Cardiovascular" class="checkbox"> Cardiovascular</label></td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="salud[]" value="Osteomuscular" class="checkbox"> Osteomuscular</label></td>
                <td colspan="3"><label><input type="checkbox" name="salud[]" value="Digestivo" class="checkbox"> Digestivo</label></td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="salud[]" value="Tegumentario" class="checkbox"> Tegumentario (Piel)</label></td>
                <td colspan="3"><label><input type="checkbox" name="salud[]" value="Respiratorio" class="checkbox"> Respiratorio</label></td>
            </tr>
            <tr>
                <td colspan="5">
                    <strong>Diagnóstico o sintomatología:</strong><br>
                    <textarea name="salud_diagnostico" rows="3" placeholder="Describa sus síntomas..."></textarea>
                </td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">VI. RIESGO ASOCIADO</td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="riesgos_asociados[]" value="Fisicos" class="checkbox"> Físicos</label></td>
                <td colspan="3"><label><input type="checkbox" name="riesgos_asociados[]" value="Psicosociales" class="checkbox"> Psicosociales</label></td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="riesgos_asociados[]" value="Quimicos" class="checkbox"> Químicos</label></td>
                <td colspan="3"><label><input type="checkbox" name="riesgos_asociados[]" value="Naturales" class="checkbox"> Naturales</label></td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="riesgos_asociados[]" value="Biologicos" class="checkbox"> Biológicos</label></td>
                <td colspan="3"><label><input type="checkbox" name="riesgos_asociados[]" value="Condiciones de seguridad" class="checkbox"> Condiciones de seguridad</label></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center; font-weight:bold; background:#eef4ff;">Nivel de criticidad</td>
            </tr>
            <tr>
                <td colspan="2" class="level-alto">
                    <label><input type="radio" name="criticidad" value="ALTO" class="radio"> ALTO</label>
                </td>
                <td colspan="2" class="level-medio">
                    <label><input type="radio" name="criticidad" value="MEDIO" class="radio"> MEDIO</label>
                </td>
                <td class="level-bajo">
                    <label><input type="radio" name="criticidad" value="BAJO" class="radio"> BAJO</label>
                </td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">VII. ACCIONES PROPUESTAS</td>
            </tr>
            <tr>
                <td colspan="5">
                    <textarea name="acciones_propuestas" rows="4" placeholder="Indique las acciones sugeridas para mitigar el acto/condición..."></textarea>
                </td>
            </tr>

            <tr class="header-blue">
                <td colspan="5">VIII. SOPORTES DE CIERRE</td>
            </tr>
            <tr>
                <td colspan="2"><label><input type="checkbox" name="soportes[]" value="Fotografias" class="checkbox"> Fotografías</label></td>
                <td colspan="2"><label><input type="checkbox" name="soportes[]" value="Informe" class="checkbox"> Informe</label></td>
                <td>Otros: <input type="text" name="soportes_otros" style="width:70%; border-bottom:1px solid #ccc;"></td>
            </tr>

        </table>
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

        const fmeta = document.getElementById("metaFecha");
        if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;
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
                    // Para checkboxes agrupados (arrays)
                    let checkboxes = document.querySelectorAll(`input[name="${key}[]"][type="checkbox"]`);
                    if (checkboxes.length > 0) {
                        checkboxes.forEach(cb => {
                            if(value.includes(cb.value)) cb.checked = true;
                        });
                    } else {
                        // Para inputs de texto en array (si los hubiera)
                        let campos = document.querySelectorAll(`[name="${key}[]"]`);
                        value.forEach((val, i) => {
                            if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                        });
                    }
                } else {
                    let campo = document.querySelector(`[name="${key}"]`);
                    if (!campo) {
                        // Buscar si es un Radio Button
                        let radio = document.querySelector(`input[name="${key}"][value="${value}"]`);
                        if(radio) radio.checked = true;
                    } else if (campo.type === 'checkbox') {
                        campo.checked = value == '1' || value === true || value === 'on';
                    } else {
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

        // Recolectar datos y manejar arrays de checkboxes
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
                    text: 'Reporte guardado correctamente',
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