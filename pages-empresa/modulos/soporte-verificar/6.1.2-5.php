<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 53; 

// --- Lógica de Logo ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// --- Carga de Datos ---
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['data']['campos'] ?? $resFormulario['data']['campos'] ?? null;
if (is_string($camposCrudos)) $datosCampos = json_decode($camposCrudos, true) ?: [];
elseif (is_array($camposCrudos)) $datosCampos = $camposCrudos;

function oldv($key, $default = '') {
    global $datosCampos;
    return isset($datosCampos[$key]) ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>6.1.3 Acta de Revisión por la Alta Dirección</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *{ box-sizing:border-box; margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; }
        body{ background:#f2f4f7; padding:20px; }
        .contenedor{ max-width:1100px; background:#fff; border:1px solid #bfc7d1; margin:0 auto; box-shadow:0 4px 18px rgba(0,0,0,.08); }
        
        .toolbar{ 
            display:flex; 
            justify-content:space-between; 
            align-items:center; 
            padding:14px 20px; 
            background:#dde7f5; 
            border-bottom:1px solid #c8d3e2; 
        }
        .toolbar h1{ font-size:20px; color:#1a4175; font-weight:700; }
        
        .acciones{ display:flex; gap:10px; }
        .btn{ border:none; padding:10px 22px; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; color:#fff; transition:.2s ease; }
        .btn-atras{ background:#6c757d; }
        .btn-guardar{ background:#198754; }
        .btn-imprimir{ background:#0d6efd; }

        /* Estilo para los botones de agregar fila */
        .btn-add-fila { background: #1a4175; color: white; padding: 5px 12px; font-size: 11px; border-radius: 4px; margin-left: 15px; border: none; cursor: pointer; }

        .formulario-body{ padding:30px; }

        table{ width:100%; border-collapse:collapse; margin-bottom: 20px; table-layout: fixed; }
        th, td{ border:1px solid #000; padding:10px; font-size:12px; vertical-align: top; }
        .bg-gris{ background: #f2f2f2; font-weight: bold; }
        .bg-blue-light{ background: #dde7f5; font-weight: bold; text-align: center; }

        input, textarea{ width:100%; border:none; outline:none; background:transparent; font-family: inherit; font-size: 12px; }
        textarea{ resize: vertical; min-height: 50px; line-height: 1.5; }

        .flex-header { display: flex; align-items: center; margin-bottom: 10px; border-bottom: 2px solid #dde7f5; padding-bottom: 5px; }
        h3{ font-size: 14px; color: #1a4175; text-transform: uppercase; margin: 0; }

        @media print{ .print-hide{ display:none !important; } .contenedor{ border:none; box-shadow:none; } }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>Acta de Revisión por la Alta Dirección (6.1.3)</h1>
        <div class="acciones">
            <button class="btn btn-atras" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" id="btnGuardar">Guardar Acta</button>
            <button class="btn btn-imprimir" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario-body">
        <form id="form613">
            <table>
                <tr>
                    <td rowspan="2" style="width:220px; text-align:center;">
                        <img src="<?= $logoEmpresaUrl ?>" style="max-height:60px;">
                    </td>
                    <td style="font-weight:bold; font-size:14px; text-align:center;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:120px; text-align:center;">0</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; text-align:center;">ACTA DE REVISIÓN POR LA ALTA DIRECCIÓN</td>
                    <td style="text-align:center;">RE-SST-19<br>24/04/2026</td>
                </tr>
            </table>

            <table>
                <tr>
                    <td class="bg-gris">EMPRESA:</td>
                    <td colspan="3"><input type="text" name="empresa_nombre" value="<?= oldv('empresa_nombre') ?>"></td>
                </tr>
                <tr>
                    <td class="bg-gris">PERÍODO REVISADO:</td>
                    <td><input type="text" name="periodo_revisado" value="<?= oldv('periodo_revisado') ?>"></td>
                    <td class="bg-gris">FECHA REUNIÓN:</td>
                    <td><input type="date" name="fecha_reunion" value="<?= oldv('fecha_reunion') ?>"></td>
                </tr>
                <tr>
                    <td class="bg-gris">HORA INICIO:</td>
                    <td><input type="time" name="hora_inicio" value="<?= oldv('hora_inicio') ?>"></td>
                    <td class="bg-gris">HORA FIN:</td>
                    <td><input type="time" name="hora_fin" value="<?= oldv('hora_fin') ?>"></td>
                </tr>
                <tr>
                    <td class="bg-gris">ACTA No:</td>
                    <td colspan="3"><input type="text" name="acta_num" value="<?= oldv('acta_num') ?>"></td>
                </tr>
            </table>

            <h3>Orden del Día</h3>
            <div style="font-size: 11px; margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; background: #fafafa;">
                <ol style="padding-left: 20px; line-height: 1.6;">
                    <li>Constancia de asistencia de la alta dirección.</li>
                    <li>Revisión de eficacia de estrategias y objetivos.</li>
                    <li>Cumplimiento del plan de trabajo anual.</li>
                    <li>Análisis de suficiencia de recursos.</li>
                    <li>Análisis de necesidad de cambios al SG-SST.</li>
                    <li>Resultado de indicadores y auditorías anteriores.</li>
                    <li>Información sobre medidas de prevención y control.</li>
                    <li>Divulgación de encuesta de trabajadores.</li>
                    <li>Toma de decisiones y mejora continua.</li>
                </ol>
            </div>

            <div class="flex-header">
                <h3>1. Asistencia de la Alta Dirección</h3>
                <button type="button" class="btn-add-fila print-hide" onclick="agregarFila('tbodyAsistencia')">+ AGREGAR FILA</button>
            </div>
            <table>
                <thead>
                    <tr class="bg-blue-light">
                        <th>Nombre Completo</th>
                        <th>Identificación</th>
                        <th>Cargo</th>
                        <th>Firma (Física)</th>
                    </tr>
                </thead>
                <tbody id="tbodyAsistencia">
                    <tr>
                        <td><input type="text" name="asist_nom[]" value="<?= oldv('asist_nom_1') ?>"></td>
                        <td><input type="text" name="asist_id[]" value="<?= oldv('asist_id_1') ?>"></td>
                        <td><input type="text" name="asist_cargo[]" value="<?= oldv('asist_cargo_1') ?>"></td>
                        <td style="background: #eee;"></td>
                    </tr>
                </tbody>
            </table>

            <h3>2. Revisión de Intervenciones y Efectividad</h3>
            <table>
                <thead>
                    <tr class="bg-blue-light">
                        <th style="width: 70%;">Intervenciones Ejecutadas</th>
                        <th colspan="2">Efectividad</th>
                    </tr>
                    <tr class="bg-blue-light">
                        <th></th>
                        <th>SÍ</th>
                        <th>NO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $intervenciones = [
                        "Políticas, Objetivos y Metas",
                        "Análisis estadístico de accidentalidad y enfermedad laboral",
                        "Resultado de participación y consulta",
                        "Estado de acciones correctivas y de mejora",
                        "Acciones de seguimiento de revisiones previas",
                        "Cambios que podrían afectar el SG-SST",
                        "Desempeño de programas de gestión",
                        "Evolución y cumplimiento legal",
                        "Necesidad de recursos"
                    ];
                    foreach($intervenciones as $idx => $text): $id = $idx + 1;
                    ?>
                    <tr>
                        <td><?= $text ?></td>
                        <td style="text-align:center;"><input type="checkbox" name="ef_si_<?= $id ?>" value="1" <?= oldv("ef_si_$id") == '1' ? 'checked' : '' ?>></td>
                        <td style="text-align:center;"><input type="checkbox" name="ef_no_<?= $id ?>" value="1" <?= oldv("ef_no_$id") == '1' ? 'checked' : '' ?>></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>3. Análisis de Indicadores y Auditorías</h3>
            <table>
                <tr>
                    <td class="bg-gris">Variación de Indicadores:</td>
                    <td><textarea name="variacion_indicadores" placeholder="Indicar diferencia entre porcentajes..."><?= oldv('variacion_indicadores') ?></textarea></td>
                </tr>
                <tr>
                    <td class="bg-gris">Resultado Auditoría:</td>
                    <td>
                        <select name="resultado_auditoria" style="width: 100%; border: none; font-size: 12px;">
                            <option value="">Seleccione...</option>
                            <option value="Aceptable" <?= oldv('resultado_auditoria') == 'Aceptable' ? 'selected' : '' ?>>Aceptable</option>
                            <option value="Medianamente Aceptable" <?= oldv('resultado_auditoria') == 'Medianamente Aceptable' ? 'selected' : '' ?>>Medianamente Aceptable</option>
                            <option value="Crítico" <?= oldv('resultado_auditoria') == 'Crítico' ? 'selected' : '' ?>>Crítico</option>
                        </select>
                    </td>
                </tr>
            </table>

            <div class="flex-header">
                <h3>4. Toma de Decisiones y Acciones Propuestas</h3>
                <button type="button" class="btn-add-fila print-hide" onclick="agregarFila('tbodyAcciones')">+ AGREGAR FILA</button>
            </div>
            <table>
                <thead>
                    <tr class="bg-blue-light">
                        <th>Acción Propuesta</th>
                        <th>Plazo</th>
                        <th>Responsable</th>
                        <th>Fecha Propuesta</th>
                    </tr>
                </thead>
                <tbody id="tbodyAcciones">
                    <tr>
                        <td><textarea name="accion_prop[]"><?= oldv('accion_prop_1') ?></textarea></td>
                        <td><input type="text" name="plazo[]" value="<?= oldv('plazo_1') ?>"></td>
                        <td><input type="text" name="resp[]" value="<?= oldv('resp_1') ?>"></td>
                        <td><input type="date" name="fecha_prop[]" value="<?= oldv('fecha_prop_1') ?>"></td>
                    </tr>
                </tbody>
            </table>

            <h3>5. Resultados y Recomendaciones</h3>
            <p style="font-size: 11px; margin-bottom: 5px;"><strong>Resultados de la revisión:</strong></p>
            <textarea name="resultados_finales"><?= oldv('resultados_finales') ?></textarea>
            
            <p style="font-size: 11px; margin-top: 15px; margin-bottom: 5px;"><strong>Recomendaciones de mejora:</strong></p>
            <textarea name="recomendaciones_mejora"><?= oldv('recomendaciones_mejora') ?></textarea>

            <div style="margin-top: 40px; display: flex; justify-content: space-between;">
                <div style="width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 10px;">
                    <p style="font-size: 12px; font-weight: bold;">Representante Legal</p>
                    <p style="font-size: 10px;">C.C.</p>
                </div>
                <div style="width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 10px;">
                    <p style="font-size: 12px; font-weight: bold;">Responsable SG-SST</p>
                    <p style="font-size: 10px;">Licencia No.</p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Función para agregar filas dinámicamente
function agregarFila(idTbody) {
    const tbody = document.getElementById(idTbody);
    const tr = document.createElement('tr');

    if (idTbody === 'tbodyAsistencia') {
        tr.innerHTML = `
            <td><input type="text" name="asist_nom[]"></td>
            <td><input type="text" name="asist_id[]"></td>
            <td><input type="text" name="asist_cargo[]"></td>
            <td style="background: #eee;"></td>
        `;
    } else if (idTbody === 'tbodyAcciones') {
        tr.innerHTML = `
            <td><textarea name="accion_prop[]"></textarea></td>
            <td><input type="text" name="plazo[]"></td>
            <td><input type="text" name="resp[]"></td>
            <td><input type="date" name="fecha_prop[]"></td>
        `;
    }
    tbody.appendChild(tr);
}

document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = new FormData(document.getElementById('form613'));
    const datosJSON = Object.fromEntries(formData.entries());

    btn.innerText = 'Guardando...';
    btn.disabled = true;

    try {
        const response = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= $token ?>'
            },
            body: JSON.stringify({
                id_empresa: <?= $empresa ?>,
                id_item_sst: <?= $idItem ?>,
                datos: datosJSON
            })
        });

        const res = await response.json();
        if (res.ok) {
            Swal.fire('¡Éxito!', 'Acta de revisión guardada correctamente.', 'success');
        } else {
            Swal.fire('Error', res.error || 'No se pudo completar la operación.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión con el servidor.', 'error');
    } finally {
        btn.innerText = 'Guardar Acta';
        btn.disabled = false;
    }
});
</script>

</body>
</html>