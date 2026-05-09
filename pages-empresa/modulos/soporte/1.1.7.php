<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
$idItem = 5; // Ajustar al ID real del ítem 1.1.7

// --- Lógica de Logo Dinámico ---
$logoUrl = "";
if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresaId", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// --- Carga de Datos Existentes ---
$resForm = $api->solicitar("formularios-dinamicos/empresa/$empresaId/item/$idItem", "GET", null, $token);
$datos = [];
$camposCrudos = $resForm['data']['data']['campos'] ?? null;
if (is_string($camposCrudos)) $datos = json_decode($camposCrudos, true) ?: [];

function oldv($key, $default = '') {
    global $datos;
    return isset($datos[$key]) ? htmlspecialchars((string)$datos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SST Manager - Soporte 1.1.7</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --primary-blue: #1a4175; --header-bg: #dde7f5; --line: #000; }
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; padding: 20px; font-size: 12px; }
        .format-container { background: #fff; max-width: 1000px; margin: auto; border: 1px solid var(--line); box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        
        /* TOOLBAR (image_69cc91.png) */
        .toolbar { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: var(--header-bg); border-bottom: 1px solid var(--line); }
        .toolbar h1 { font-size: 16px; color: var(--primary-blue); font-weight: bold; margin: 0; }
        .btn-nav { border: none; padding: 6px 16px; border-radius: 6px; font-weight: bold; color: white; cursor: pointer; font-size: 13px; }

        /* SELECTOR DE PESTAÑAS */
        .selector-tabs { padding: 15px; text-align: center; background: #f1f1f1; border-bottom: 1px solid #ddd; }
        .hidden-section { display: none; }

        /* TABLAS ESTILO EXCEL */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: 1px solid var(--line); padding: 6px; }
        .bg-blue-soft { background: #cfe2f7; font-weight: bold; text-align: center; }
        .panel-title { background: var(--primary-blue); color: white; text-align: center; font-weight: bold; padding: 8px; text-transform: uppercase; }
        
        .score-input { width: 100%; border: none; outline: none; text-align: center; font-weight: bold; background: transparent; }
        .percent-cell { background: #fff; font-weight: 900; text-align: center; font-size: 14px; }

        @media print { 
            .no-print, .selector-tabs { display: none !important; }
            .format-container { border: none; box-shadow: none; width: 100%; }
            .hidden-section { display: block !important; margin-bottom: 30px; } /* Imprime ambos si están llenos */
        }
    </style>
</head>
<body>

<div class="format-container">
    <div class="toolbar no-print">
        <h1>Seguimiento COPASST - COCOLAB (1.1.7)</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-secondary btn-sm" onclick="history.back()">Atrás</button>
            <button class="btn btn-success btn-sm" id="btnGuardar"><i class="fa-solid fa-save"></i> Guardar Acta</button>
            <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir</button>
        </div>
    </div>

    <form id="formSoporte">
        <table>
            <tr>
                <td rowspan="2" style="width: 180px; text-align: center;">
                    <?php if ($logoUrl): ?> <img src="<?= $logoUrl ?>" style="max-height: 55px;"> <?php else: ?> [LOGO] <?php endif; ?>
                </td>
                <td style="font-weight: bold; text-align: center; font-size: 14px;">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                <td style="width: 120px; text-align: center; font-weight: bold;">Versión: 01</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center; text-transform: uppercase;">ACTA DE SEGUIMIENTO COMITÉS</td>
                <td style="text-align: center;">24/04/2026</td>
            </tr>
        </table>

        <div class="selector-tabs no-print">
            <button type="button" class="btn btn-outline-primary me-2" onclick="showSection('copasst')">Gestionar COPASST</button>
            <button type="button" class="btn btn-outline-primary" onclick="showSection('cocolab')">Gestionar COCOLAB</button>
        </div>

        <div id="section_copasst" class="hidden-section">
            <div class="panel-title">SEGUIMIENTO COPASST</div>
            <table id="tabla_copasst">
                <tr class="bg-blue-soft">
                    <th style="text-align: left;">REQUISITOS DE GESTIÓN</th>
                    <th style="width: 80px;">CUMPLE (1/0)</th>
                </tr>
                <tr><td>Actas de Conformación firmadas por R.L</td><td><input type="number" name="c_1" class="score-input" min="0" max="1" value="<?= oldv('c_1', '0') ?>"></td></tr>
                <tr><td>Capacitación de funciones del comité</td><td><input type="number" name="c_2" class="score-input" min="0" max="1" value="<?= oldv('c_2', '0') ?>"></td></tr>
                <tr><td>Actas de reunión Mensual vigentes</td><td><input type="number" name="c_3" class="score-input" min="0" max="1" value="<?= oldv('c_3', '0') ?>"></td></tr>
                <tr><td>Investigación de accidentes e incidentes</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en SG SST según decreto 1072 del 2015.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en investigación de accidentes e incidentes.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en inspecciones planeadas y divulgación del programa.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en Programas de Vigilancia Epidemiológica.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación del curso de 50 horas en SST a todos los miembros.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Divulgación del Plan de trabajo anual.Divulgación del Plan de trabajo anual.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Consolidado de los planes de acción de las investigaciones.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Seguimiento de las inspecciones realizadas y planes de acción.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Registro de divulgación de políticas del SGSST (salud ocupacional, política de no alcohol, tabaco y sustancias psicoactivas).</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Registro de divulgación matriz de objetivos, metas e indicadores del SGSST.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Registro de Divulgación presupuesto.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Registro de divulgación del manual de SG SST.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Planeación de Auditoria.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Revisión informe rendición de cuentas.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Cronograma de reuniones Copasst.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Seguimiento a las investigación de accidentes.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr class="bg-blue-soft">
                    <td>PORCENTAJE DE CUMPLIMIENTO COPASST</td>
                    <td class="percent-cell" id="total_copasst">0%</td>
                </tr>
            </table>
        </div>

        <div id="section_cocolab" class="hidden-section">
            <div class="panel-title">SEGUIMIENTO COCOLAB</div>
            <table id="tabla_cocolab">
                <tr class="bg-blue-soft">
                    <th style="text-align: left;">REQUISITOS DE GESTIÓN</th>
                    <th style="width: 80px;">CUMPLE (1/0)</th>
                </tr>
                <tr><td>Actas de Conformación COCOLAB</td><td><input type="number" name="l_1" class="score-input" min="0" max="1" value="<?= oldv('l_1', '0') ?>"></td></tr>
                <tr><td>Capacitación en acoso laboral</td><td><input type="number" name="l_2" class="score-input" min="0" max="1" value="<?= oldv('l_2', '0') ?>"></td></tr>
                <tr><td>Actas de reunión trimestral</td><td><input type="number" name="l_3" class="score-input" min="0" max="1" value="<?= oldv('l_3', '0') ?>"></td></tr>
                <tr><td>Capacitación en SG SST según decreto 1072 del 2015.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en acoso laboral.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en modalidades de acoso laboral.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación en Programas de Vigilancia Epidemiológica.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Capacitación del curso de 50 horas en SST a todos los miembros.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Divulgación del Plan de trabajo anual.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Consolidado de los planes de acción.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Seguimiento a posibles casos de acoso laboral.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Registro de divulgación de políticas del SGSST (salud ocupacional, política de no alcohol, tabaco y sustancias psicoactivas).</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr><td>Cronograma de reuniones COCOLAB.</td><td><input type="number" name="c_4" class="score-input" min="0" max="1" value="<?= oldv('c_4', '0') ?>"></td></tr>
                <tr class="bg-blue-soft">
                    <td>PORCENTAJE DE CUMPLIMIENTO COCOLAB</td>
                    <td class="percent-cell" id="total_cocolab">0%</td>
                </tr>
            </table>
        </div>

        <table style="margin-top: 20px; border: none;">
            <tr style="height: 80px; text-align: center; vertical-align: bottom;">
                <td style="border:none;"><div style="border-top: 1px solid #000; width: 80%; margin: auto;">PRESIDENTE COMITÉ</div></td>
                <td style="border:none;"><div style="border-top: 1px solid #000; width: 80%; margin: auto;">RESPONSABLE SG-SST</div></td>
            </tr>
        </table>
    </form>
</div>

<script>
function showSection(tipo) {
    document.getElementById('section_copasst').classList.add('hidden-section');
    document.getElementById('section_cocolab').classList.add('hidden-section');
    document.getElementById('section_' + tipo).classList.remove('hidden-section');
}

function calcular() {
    const calcularPorId = (tablaId, displayId) => {
        const inputs = document.querySelectorAll(`#${tablaId} .score-input`);
        let suma = 0, total = 0;
        inputs.forEach(i => {
            if(i.value !== "") { suma += parseInt(i.value || 0); total++; }
        });
        const pct = total > 0 ? Math.round((suma/total)*100) : 0;
        document.getElementById(displayId).innerText = pct + '%';
    };
    calcularPorId('tabla_copasst', 'total_copasst');
    calcularPorId('tabla_cocolab', 'total_cocolab');
}

document.querySelectorAll('.score-input').forEach(i => {
    i.addEventListener('input', function() {
        if(this.value > 1) this.value = 1;
        if(this.value < 0) this.value = 0;
        calcular();
    });
});

document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const formData = Object.fromEntries(new FormData(document.getElementById('formSoporte')).entries());
    btn.disabled = true;

    try {
        const response = await fetch("../../../public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify({ id_empresa: <?= $empresaId ?>, id_item_sst: <?= $idItem ?>, datos: formData })
        });
        const res = await response.json();
        if (res.ok) Swal.fire('Éxito', 'Gestión guardada correctamente', 'success');
        else Swal.fire('Error', 'No se pudo guardar', 'error');
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión', 'error');
    } finally { btn.disabled = false; }
});

window.onload = () => { showSection('copasst'); calcular(); };
</script>
</body>
</html>