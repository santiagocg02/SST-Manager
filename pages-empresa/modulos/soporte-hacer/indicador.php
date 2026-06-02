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
// Ajusta el ID de este ítem según tu base de datos para Indicadores SST
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 48; 

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores SST</title>
    
    <!-- Librerías añadidas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1550px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:20px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}

        .contenido{padding:18px}
        table{width:100%;border-collapse:collapse}
        .encabezado td,.encabezado th,.tabla-ficha td,.tabla-ficha th,.tabla-formulada td,.tabla-formulada th{
            border:1px solid #6b6b6b;padding:6px;vertical-align:middle
        }
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{width:140px;height:65px;display:flex;align-items:center;justify-content:center;margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center}
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px}

        .selector-wrap{margin-top:14px;display:flex;gap:12px;align-items:center;flex-wrap:wrap}
        .selector-wrap label{font-weight:700}
        .selector-wrap select{
            min-width:320px;padding:10px 12px;border:1px solid #aeb9cb;border-radius:8px;background:#fff;font-size:14px
        }

        .grid-main{display:grid;grid-template-columns:1fr;gap:18px;margin-top:16px}

        .tabla-ficha td:first-child{width:220px;font-weight:700;background:#f8fafc}
        .tabla-ficha input,.tabla-ficha textarea{
            width:100%;border:none;outline:none;background:transparent;padding:2px 4px;font-size:13px
        }
        .tabla-ficha textarea{
            resize:none;overflow:hidden;min-height:60px;white-space:pre-wrap;word-break:break-word
        }

        .seccion-title{
            margin:14px 0 8px;
            font-size:13px;
            color:#d97900;
            font-style:italic;
            font-weight:700;
        }

        .tabla-formulada{
            font-size:12px;
        }
        .tabla-formulada thead th{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            line-height:1.2;
        }
        .tabla-formulada td{text-align:center}
        .tabla-formulada td:first-child{font-weight:700;background:#f8fafc}
        .tabla-formulada input{
            width:100%;border:none;outline:none;background:transparent;padding:4px 2px;font-size:12px;text-align:center
        }
        .readonly{
            background:#f4f6fa !important;
            color:#1f3b68;
            font-weight:700;
        }

        .formula-box{
            border:1px solid #6b6b6b;
            border-top:none;
            padding:10px 14px;
            background:#fff;
        }
        .formula-head{
            background:#8eaadb;
            color:#fff;
            font-size:12px;
            font-weight:700;
            text-align:center;
            padding:6px;
            border:1px solid #6b6b6b;
            border-bottom:none;
        }
        .formula-text{
            text-align:center;
            font-size:14px;
            line-height:1.6;
            padding:8px 0;
        }

        .range-box{
            border:1px solid #6b6b6b;
            border-top:none;
        }
        .range-head{
            display:grid;
            grid-template-columns:1.1fr 1fr 1fr;
            border-bottom:1px solid #6b6b6b;
        }
        .range-head div{
            padding:6px;
            font-size:12px;
            font-weight:700;
            text-align:center;
        }
        .range-head div:nth-child(1){background:#f8fafc;text-align:left}
        .range-head div:nth-child(2){color:#70ad47}
        .range-head div:nth-child(3){color:#c00000}
        .range-values{
            display:grid;
            grid-template-columns:1.1fr 1fr 1fr;
        }
        .range-values div{
            padding:8px;
            min-height:34px;
            font-size:12px;
            text-align:center;
            border-right:1px solid #6b6b6b;
        }
        .range-values div:last-child{border-right:none}
        .range-values div:first-child{text-align:left;font-weight:700;background:#f8fafc}

        .chart-wrap{
            border:1px solid #6b6b6b;
            border-top:none;
            padding:14px;
            background:#fff;
        }
        #chartIndicador{
            width:100% !important;
            height:320px !important;
        }

        .analisis-head{
            background:#8eaadb;
            color:#fff;
            font-size:12px;
            font-weight:700;
            text-align:center;
            padding:6px;
            border:1px solid #6b6b6b;
            border-top:none;
            border-bottom:none;
        }
        .analisis-box{
            border:1px solid #6b6b6b;
            min-height:90px;
            padding:10px;
            font-size:13px;
            line-height:1.5;
            background:#fff;
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar,.selector-wrap, .print-hide {display:none !important;}
            .contenedor{box-shadow:none;border:none}
            .contenido{padding:8px}
            input, textarea, select{border:none !important;box-shadow:none !important}
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>Indicadores SST</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="formIndicadores">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%; padding:0;">
                        <!-- Logo dinámico de la empresa -->
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:64%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:18%;font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo" id="subtituloFormato">INDICADORES SST</td>
                    <td style="font-weight:700;">RE-SST-IND<br>XX/XX/2025</td>
                </tr>
            </table>

            <div class="selector-wrap print-hide">
                <label for="selectorIndicador">Seleccione el indicador</label>
                <select id="selectorIndicador">
                    <option value="plan_trabajo">PLAN DE TRABAJO SST</option>
                    <option value="tasa_prevalencia">TASA DE PREVALENCIA</option>
                </select>
            </div>

            <div class="grid-main">
                <div>
                    <div class="seccion-title">Solo seleccione el nombre del indicador, tabla formulada</div>
                    <table class="tabla-ficha">
                        <tr><td>PROCESO</td><td><input type="text" id="f_proceso"></td></tr>
                        <tr><td>RESPONSABLE DEL PROCESO</td><td><input type="text" id="f_responsable"></td></tr>
                        <tr><td>INDICADOR #</td><td><input type="text" id="f_indicador_num"></td></tr>
                        <tr><td>PERIORICIDAD</td><td><input type="text" id="f_periodicidad"></td></tr>
                        <tr><td>FUENTE DE LA INFORMACIÓN</td><td><input type="text" id="f_fuente"></td></tr>
                        <tr><td>PERSONAS QUE DEBEN CONOCER</td><td><input type="text" id="f_personas"></td></tr>
                        <tr><td>OBJETIVO</td><td><textarea id="f_objetivo"></textarea></td></tr>
                    </table>

                    <div class="seccion-title">Tabla formulada</div>
                    <table class="tabla-formulada" id="tablaFormulada">
                        <thead>
                            <tr id="theadIndicador"></tr>
                        </thead>
                        <tbody id="tbodyIndicador"></tbody>
                        <tfoot>
                            <tr id="tfootIndicador"></tr>
                        </tfoot>
                    </table>

                    <div class="formula-head">FORMULA</div>
                    <div class="formula-box">
                        <div class="formula-text" id="formulaTexto"></div>
                    </div>

                    <div class="range-box">
                        <div class="range-head">
                            <div>SENTIDO DEL INDICADOR</div>
                            <div>ASCENDENTE</div>
                            <div>DESCENDENTE</div>
                        </div>
                        <div class="range-values">
                            <div id="sentidoIndicador">DESCENDENTE</div>
                            <div id="ascendenteTexto"></div>
                            <div id="descendenteTexto"></div>
                        </div>
                    </div>

                    <div class="chart-wrap">
                        <canvas id="chartIndicador"></canvas>
                    </div>

                    <div class="analisis-head">ANALISIS TENDENCIAL</div>
                    <div class="analisis-box" id="analisisTendencial"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const MESES = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

const CONFIGS = {
    plan_trabajo: {
        titulo: 'PLAN DE TRABAJO SST',
        proceso: 'SST',
        responsable: 'JOSÉ G.',
        indicador_num: '1',
        periodicidad: 'TRIMESTRAL',
        fuente: 'PLAN DE TRABAJO',
        personas: 'GERENCIA',
        objetivo: 'Dar cumplimiento al plan anual de trabajo del Sistema de Gestión de Seguridad y Salud en el Trabajo.',
        formula: 'Número de actividades cumplidas del plan anual de trabajo × 100 / Número de actividades programadas en el plan anual de trabajo',
        sentido: 'ASCENDENTE',
        ascendente: 'Cumplimiento superior a la meta',
        descendente: 'Cumplimiento inferior a la meta',
        chartType: 'bar_line',
        yMax: 100,
        columns: [
            { key: 'mes', label: 'MES', type: 'text', readonly: true },
            { key: 'cumplidas', label: 'Número de actividades cumplidas del plan anual de trabajo', type: 'number' },
            { key: 'programadas', label: 'Número de actividades programadas en el plan anual de trabajo', type: 'number' },
            { key: 'ejecucion', label: 'EJECUCIÓN', type: 'computed' },
            { key: 'meta', label: 'META', type: 'numberPercent' },
            { key: 'pte', label: 'PTE X CUMPLIR', type: 'computed' }
        ],
        rows: [
            { mes:'ENERO', cumplidas:10, programadas:15, meta:70 },
            { mes:'FEBRERO', cumplidas:10, programadas:17, meta:70 },
            { mes:'MARZO', cumplidas:10, programadas:15, meta:70 },
            { mes:'ABRIL', cumplidas:10, programadas:17, meta:70 },
            { mes:'MAYO', cumplidas:10, programadas:15, meta:70 },
            { mes:'JUNIO', cumplidas:10, programadas:17, meta:70 },
            { mes:'JULIO', cumplidas:10, programadas:15, meta:70 },
            { mes:'AGOSTO', cumplidas:10, programadas:17, meta:70 },
            { mes:'SEPTIEMBRE', cumplidas:10, programadas:15, meta:70 },
            { mes:'OCTUBRE', cumplidas:10, programadas:17, meta:70 },
            { mes:'NOVIEMBRE', cumplidas:10, programadas:15, meta:70 },
            { mes:'DICIEMBRE', cumplidas:10, programadas:17, meta:70 }
        ],
        compute(row){
            const cumplidas = toNumber(row.cumplidas);
            const programadas = toNumber(row.programadas);
            const meta = toNumber(row.meta);
            const ejecucion = programadas > 0 ? (cumplidas / programadas) * 100 : 0;
            const pte = Math.max(meta - ejecucion, 0);
            return { ejecucion, pte };
        },
        analysis(rows){
            const ejecuciones = rows.map(r => r.ejecucion);
            const promedio = average(ejecuciones);
            const max = Math.max(...ejecuciones);
            const min = Math.min(...ejecuciones);
            const mesMax = rows[ejecuciones.indexOf(max)].mes;
            const mesMin = rows[ejecuciones.indexOf(min)].mes;
            return `El promedio de ejecución del periodo es ${formatPercent(promedio)}. El mejor desempeño se presentó en ${mesMax} con ${formatPercent(max)} y el menor en ${mesMin} con ${formatPercent(min)}. Se recomienda proponer acciones para la mejora continua y cerrar la brecha frente a la meta mensual.`;
        }
    },

    tasa_prevalencia: {
        titulo: 'TASA DE PREVALENCIA',
        proceso: 'SST',
        responsable: 'JOSÉ G.',
        indicador_num: '14',
        periodicidad: 'TRIMESTRAL',
        fuente: 'REGISTRO DE VARIABLES',
        personas: 'GERENCIA',
        objetivo: 'Medir la prevalencia de enfermedad laboral en la población trabajadora del periodo.',
        formula: '(Número de casos nuevos y antiguos de enfermedad laboral en el periodo “Z” / Promedio de trabajadores en el periodo “Z”) × 100.000',
        sentido: 'DESCENDENTE',
        ascendente: 'Mayor ocurrencia de enfermedad laboral',
        descendente: 'Menor ocurrencia de enfermedad laboral',
        chartType: 'bar_line',
        yMax: null,
        columns: [
            { key: 'mes', label: 'MES', type: 'text', readonly: true },
            { key: 'casos', label: 'Número de casos nuevos y antiguos de enfermedad laboral en el periodo “Z”', type: 'number' },
            { key: 'trabajadores', label: 'Promedio de trabajadores en el periodo “Z”', type: 'number' },
            { key: 'ejecucion', label: 'EJECUCIÓN', type: 'computed' },
            { key: 'meta', label: 'META', type: 'number' },
            { key: 'pte', label: 'PTE X CUMPLIR', type: 'computed' }
        ],
        rows: [
            { mes:'ENERO', casos:4, trabajadores:100, meta:0 },
            { mes:'FEBRERO', casos:1, trabajadores:100, meta:0 },
            { mes:'MARZO', casos:1, trabajadores:100, meta:0 },
            { mes:'ABRIL', casos:3, trabajadores:100, meta:0 },
            { mes:'MAYO', casos:2, trabajadores:100, meta:0 },
            { mes:'JUNIO', casos:1, trabajadores:100, meta:0 },
            { mes:'JULIO', casos:4, trabajadores:100, meta:0 },
            { mes:'AGOSTO', casos:2, trabajadores:100, meta:0 },
            { mes:'SEPTIEMBRE', casos:3, trabajadores:100, meta:0 },
            { mes:'OCTUBRE', casos:4, trabajadores:100, meta:0 },
            { mes:'NOVIEMBRE', casos:5, trabajadores:100, meta:0 },
            { mes:'DICIEMBRE', casos:0, trabajadores:100, meta:0 }
        ],
        compute(row){
            const casos = toNumber(row.casos);
            const trabajadores = toNumber(row.trabajadores);
            const meta = toNumber(row.meta);
            const ejecucion = trabajadores > 0 ? (casos / trabajadores) * 100000 : 0;
            const pte = Math.max(ejecucion - meta, 0);
            return { ejecucion, pte };
        },
        analysis(rows){
            const ejecuciones = rows.map(r => r.ejecucion);
            const totalCasos = rows.reduce((acc, r) => acc + toNumber(r.casos), 0);
            const promedioTrab = average(rows.map(r => toNumber(r.trabajadores)));
            const promedioTasa = average(ejecuciones);
            const max = Math.max(...ejecuciones);
            const mesMax = rows[ejecuciones.indexOf(max)].mes;
            return `Durante el periodo se registran ${totalCasos} casos en un promedio de ${formatNumber(promedioTrab, 0)} trabajadores. La tasa promedio es ${formatNumber(promedioTasa, 0)} y el valor más alto se presentó en ${mesMax} con ${formatNumber(max, 0)}. Al ser un indicador descendente, la meta es sostenerlo lo más cercano posible a 0.`;
        }
    }
};

let chartIndicador = null;
let currentKey = 'plan_trabajo';

// INICIALIZAR DATOS DESDE LA BASE DE DATOS AL CARGAR
const datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;

Object.keys(CONFIGS).forEach(indKey => {
    const cfg = CONFIGS[indKey];
    // Recuperar datos de las Fichas si existen
    if (datosGuardados[`${indKey}_proceso`] !== undefined) cfg.proceso = datosGuardados[`${indKey}_proceso`];
    if (datosGuardados[`${indKey}_responsable`] !== undefined) cfg.responsable = datosGuardados[`${indKey}_responsable`];
    if (datosGuardados[`${indKey}_indicador_num`] !== undefined) cfg.indicador_num = datosGuardados[`${indKey}_indicador_num`];
    if (datosGuardados[`${indKey}_periodicidad`] !== undefined) cfg.periodicidad = datosGuardados[`${indKey}_periodicidad`];
    if (datosGuardados[`${indKey}_fuente`] !== undefined) cfg.fuente = datosGuardados[`${indKey}_fuente`];
    if (datosGuardados[`${indKey}_personas`] !== undefined) cfg.personas = datosGuardados[`${indKey}_personas`];
    if (datosGuardados[`${indKey}_objetivo`] !== undefined) cfg.objetivo = datosGuardados[`${indKey}_objetivo`];

    // Recuperar datos de las filas
    cfg.rows.forEach((row, i) => {
        cfg.columns.forEach(col => {
            if (col.key !== 'mes' && col.type !== 'computed') {
                const savedVal = datosGuardados[`${indKey}_${i}_${col.key}`];
                if (savedVal !== undefined) {
                    row[col.key] = savedVal;
                }
            }
        });
    });
});

function toNumber(value){
    const n = parseFloat(value);
    return isNaN(n) ? 0 : n;
}

function formatNumber(value, decimals = 0){
    return Number(value).toLocaleString('es-CO', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function formatPercent(value, decimals = 0){
    return formatNumber(value, decimals) + '%';
}

function average(arr){
    if (!arr.length) return 0;
    return arr.reduce((a,b)=>a+b,0) / arr.length;
}

function setFicha(cfg){
    document.getElementById('subtituloFormato').textContent = cfg.titulo;
    document.getElementById('f_proceso').value = cfg.proceso;
    document.getElementById('f_responsable').value = cfg.responsable;
    document.getElementById('f_indicador_num').value = cfg.indicador_num;
    document.getElementById('f_periodicidad').value = cfg.periodicidad;
    document.getElementById('f_fuente').value = cfg.fuente;
    document.getElementById('f_personas').value = cfg.personas;
    document.getElementById('f_objetivo').value = cfg.objetivo;
    document.getElementById('formulaTexto').textContent = cfg.formula;
    document.getElementById('sentidoIndicador').textContent = cfg.sentido;
    document.getElementById('ascendenteTexto').textContent = cfg.ascendente;
    document.getElementById('descendenteTexto').textContent = cfg.descendente;
}

function renderTable(cfg){
    const thead = document.getElementById('theadIndicador');
    const tbody = document.getElementById('tbodyIndicador');
    const tfoot = document.getElementById('tfootIndicador');

    thead.innerHTML = '';
    tbody.innerHTML = '';
    tfoot.innerHTML = '';

    cfg.columns.forEach(col => {
        const th = document.createElement('th');
        th.textContent = col.label;
        thead.appendChild(th);
    });

    cfg.rows.forEach((row, rowIndex) => {
        const tr = document.createElement('tr');

        cfg.columns.forEach(col => {
            const td = document.createElement('td');

            if (col.type === 'text' && col.readonly) {
                td.textContent = row[col.key];
            } else {
                const input = document.createElement('input');
                input.type = 'number';
                input.step = 'any';
                input.dataset.row = rowIndex;
                input.dataset.key = col.key;

                if (col.type === 'computed') {
                    input.className = 'readonly';
                    input.readOnly = true;
                } else {
                    input.value = row[col.key] ?? 0;
                }

                td.appendChild(input);
            }
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });

    const footCells = cfg.columns.map((col, i) => {
        if (i === 0) return '<th>Tabla formulada</th>';
        if (col.key === 'ejecucion') return '<th id="tfootEjecucion">0</th>';
        if (col.key === 'meta') return '<th id="tfootMeta">0</th>';
        if (col.key === 'pte') return '<th id="tfootPte">0</th>';
        return `<th id="tfoot_${col.key}">0</th>`;
    });

    tfoot.innerHTML = footCells.join('');
}

function writeComputedValues(cfg, rows){
    rows.forEach((row, rowIndex) => {
        const computed = cfg.compute(row);

        Object.keys(computed).forEach(key => {
            const input = document.querySelector(`input[data-row="${rowIndex}"][data-key="${key}"]`);
            if (!input) return;

            if (cfg.key === 'plan_trabajo' && (key === 'ejecucion' || key === 'pte')) {
                input.value = formatPercent(computed[key], 0);
            } else if (cfg.key === 'tasa_prevalencia' && key === 'ejecucion') {
                input.value = formatNumber(computed[key], 0);
            } else {
                input.value = formatNumber(computed[key], 0);
            }

            row[key] = computed[key];
        });
    });
}

function updateFooters(cfg, rows){
    const sum = (key) => rows.reduce((acc, row) => acc + toNumber(row[key]), 0);

    cfg.columns.forEach(col => {
        if (col.key === 'mes') return;
        const el = document.getElementById(`tfoot_${col.key}`);
        if (!el) return;

        if (col.key === 'trabajadores') {
            el.textContent = formatNumber(average(rows.map(r => r.trabajadores)), 0);
        } else {
            el.textContent = formatNumber(sum(col.key), 0);
        }
    });

    const ejecucionProm = average(rows.map(r => toNumber(r.ejecucion)));
    const metaProm = average(rows.map(r => toNumber(r.meta)));
    const pteProm = average(rows.map(r => toNumber(r.pte)));

    const elE = document.getElementById('tfootEjecucion');
    const elM = document.getElementById('tfootMeta');
    const elP = document.getElementById('tfootPte');

    if (elE) {
        elE.textContent = cfg.key === 'plan_trabajo'
            ? `Promedio ${formatPercent(ejecucionProm, 0)}`
            : formatNumber(ejecucionProm, 0);
    }
    if (elM) {
        elM.textContent = cfg.key === 'plan_trabajo'
            ? formatPercent(metaProm, 0)
            : formatNumber(metaProm, 0);
    }
    if (elP) {
        elP.textContent = cfg.key === 'plan_trabajo'
            ? formatPercent(pteProm, 0)
            : formatNumber(pteProm, 0);
    }
}

function renderChart(cfg, rows){
    const labels = rows.map(r => r.mes);
    const ejecucion = rows.map(r => toNumber(r.ejecucion));
    const meta = rows.map(r => toNumber(r.meta));

    if (chartIndicador) chartIndicador.destroy();

    chartIndicador = new Chart(document.getElementById('chartIndicador'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Ejecución',
                    data: ejecucion
                },
                {
                    type: 'line',
                    label: 'Meta',
                    data: meta,
                    tension: 0.25
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: cfg.yMax || undefined
                }
            }
        }
    });
}

function recalc(){
    const cfg = CONFIGS[currentKey];
    writeComputedValues(cfg, cfg.rows);
    updateFooters(cfg, cfg.rows);
    renderChart(cfg, cfg.rows);
    document.getElementById('analisisTendencial').textContent = cfg.analysis(cfg.rows);
}

function loadIndicator(key){
    currentKey = key;
    const cfg = CONFIGS[key];
    cfg.key = key;
    setFicha(cfg);
    renderTable(cfg);
    recalc();
}

// SINCRONIZAR DATOS DEL DOM AL OBJETO CONFIGS EN TIEMPO REAL
document.addEventListener('input', function(e){
    const cfg = CONFIGS[currentKey];
    
    // Capturar cambios en la ficha
    if (e.target.id === 'f_proceso') cfg.proceso = e.target.value;
    else if (e.target.id === 'f_responsable') cfg.responsable = e.target.value;
    else if (e.target.id === 'f_indicador_num') cfg.indicador_num = e.target.value;
    else if (e.target.id === 'f_periodicidad') cfg.periodicidad = e.target.value;
    else if (e.target.id === 'f_fuente') cfg.fuente = e.target.value;
    else if (e.target.id === 'f_personas') cfg.personas = e.target.value;
    else if (e.target.id === 'f_objetivo') cfg.objetivo = e.target.value;

    // Capturar cambios en la tabla
    else if (e.target.matches('#tbodyIndicador input')) {
        const rowIndex = e.target.dataset.row;
        const colKey = e.target.dataset.key;
        if (rowIndex !== undefined && colKey !== undefined) {
            cfg.rows[rowIndex][colKey] = toNumber(e.target.value);
            recalc();
        }
    }
});

document.getElementById('selectorIndicador').addEventListener('change', function(){
    loadIndicator(this.value);
});

// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Guardando...';
    btn.disabled = true;

    // Construir el JSON plano a partir de todos los indicadores en CONFIGS
    const payload = {};
    
    Object.keys(CONFIGS).forEach(indKey => {
        const cfg = CONFIGS[indKey];
        
        // Guardar la Ficha
        payload[`${indKey}_proceso`] = cfg.proceso || '';
        payload[`${indKey}_responsable`] = cfg.responsable || '';
        payload[`${indKey}_indicador_num`] = cfg.indicador_num || '';
        payload[`${indKey}_periodicidad`] = cfg.periodicidad || '';
        payload[`${indKey}_fuente`] = cfg.fuente || '';
        payload[`${indKey}_personas`] = cfg.personas || '';
        payload[`${indKey}_objetivo`] = cfg.objetivo || '';

        // Guardar las Filas
        cfg.rows.forEach((row, i) => {
            cfg.columns.forEach(col => {
                if (col.key !== 'mes' && col.type !== 'computed') {
                    payload[`${indKey}_${i}_${col.key}`] = row[col.key] || 0;
                }
            });
        });
    });

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
                datos: payload
            })
        });

        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Todos los indicadores fueron guardados correctamente.',
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
// ----------------------------------------------------

document.addEventListener('DOMContentLoaded', function(){
    loadIndicator(document.getElementById('selectorIndicador').value);
});
</script>
</body>
</html>