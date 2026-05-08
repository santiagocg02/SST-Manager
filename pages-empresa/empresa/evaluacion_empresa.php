<?php
session_start();
$token = $_SESSION["token"] ?? "";
$idEmpresa = (int)($_GET['id'] ?? $_SESSION['id_empresa'] ?? 0);
$cantEmpleados = (int)($_SESSION['cant_directos'] ?? 10); 
$claseRiesgo = (int)($_SESSION['clase_riesgo'] ?? 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SST Manager - Autoevaluación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-eval { font-size: 0.85rem; border-collapse: separate; border-spacing: 0 8px; }
        .table-eval tbody tr { box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-radius: 8px; transition: all 0.2s; background-color: #fff;}
        
        /* Colores de estado para las filas */
        .bg-pendiente { border-left: 6px solid #adb5bd !important; }
        .bg-cumple { border-left: 6px solid #198754 !important; background-color: #f8fff9 !important; }
        .bg-nocumple { border-left: 6px solid #dc3545 !important; background-color: #fff8f8 !important; }
        .bg-noaplica { border-left: 6px solid #0dcaf0 !important; background-color: #f0fcfc !important; }

        .sticky-header { position: sticky; top: 0; background: #fff; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 1rem; border-radius: 0 0 15px 15px; }
        .form-select-sm { font-weight: 700; border-radius: 8px; }
        textarea.form-control-sm { border-radius: 8px; border: 1px solid #eee; }

        /* Estilos para el resumen tipo Excel */
        .tabla-resumen td { border: 1px solid #dee2e6; vertical-align: middle; }
        .bg-critico { background-color: #dc3545 !important; color: white; }
        .bg-moderado { background-color: #ffc107 !important; color: black; }
        .bg-aceptable { background-color: #198754 !important; color: white; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-3">
    <div class="card border-0 bg-transparent">
        <div class="sticky-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-5 d-flex align-items-center">
                    <a href="../../pages-empresa/bienvenidaes.php" class="btn btn-outline-secondary btn-sm rounded-pill me-3 px-3 shadow-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver
                    </a>
                    <div>
                        <h4 class="mb-0 text-dark fw-bold">
                            <i class="fa-solid fa-clipboard-check text-primary me-2"></i>Autoevaluación
                        </h4>
                        <span id="info-empresa" class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">Cargando...</span>
                    </div>
                </div>
                <div class="col-md-7 text-end" id="contador-progreso"></div>
            </div>
        </div>

        <div class="table-responsive px-2">
            <table class="table table-eval align-middle mb-5">
                <thead class="text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3" style="width: 25%;">Ciclo / Estándar</th>
                        <th style="width: 40%;">Descripción y Criterio</th>
                        <th style="width: 15%; text-align: center;">Cumplimiento</th>
                        <th style="width: 20%;">Observaciones</th>
                    </tr>
                </thead>
                
                <tbody id="tabla-items"></tbody>
                
                <tfoot id="tabla-resumen" class="tabla-resumen fw-bold bg-white shadow-sm" style="font-size: 0.9rem;">
                    </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const API_URL = "http://localhost/sstmanager-backend/public/index.php";
    const ID_EMPRESA = <?= $idEmpresa ?>;
    const TOKEN = "<?= $token ?>";
    const CONFIG_POST = { id_empresa: ID_EMPRESA, cantidad_empleados: <?= $cantEmpleados ?>, clase_riesgo: <?= $claseRiesgo ?> };

    let itemsMemoria = [];

    document.addEventListener('DOMContentLoaded', () => { 
        if (ID_EMPRESA) cargarDatos(); 
    });

    async function cargarDatos() {
        try {
            const r = await fetch(`${API_URL}?table=evaluaciones&id=${ID_EMPRESA}`, {
                headers: { 'Authorization': `Bearer ${TOKEN}` }
            });
            const res = await r.json();
            
            if (res.ok) {
                itemsMemoria = res.formulario;
                if (res.info_general) {
                    document.getElementById('info-empresa').innerText = `${res.info_general.estandar_aplicado}`;
                }
                renderizar();
                actualizarProgreso();
            } else { 
                await crearEvaluacion(); 
            }
        } catch (e) { 
            console.error("Error al cargar datos:", e); 
        }
    }

    async function crearEvaluacion() {
        try {
            const r = await fetch(`${API_URL}?table=evaluaciones`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
                body: JSON.stringify(CONFIG_POST)
            });
            if ((await r.json()).ok) cargarDatos();
        } catch (e) {
            console.error("Error al crear evaluación:", e);
        }
    }

    function renderizar() {
        const tbody = document.getElementById('tabla-items');
        tbody.innerHTML = '';
        
        itemsMemoria.forEach(i => {
            let estado = parseInt(i.cumple);
            
            let clase = 'bg-pendiente';
            if (estado === 1) clase = 'bg-cumple';
            else if (estado === 2) clase = 'bg-nocumple';
            else if (estado === 0) clase = 'bg-noaplica';

            const tr = document.createElement('tr');
            tr.className = clase;
            tr.id = `row-${i.id_detalle}`;
            tr.innerHTML = `
                <td class="ps-3 border-end">
                    <span class="d-block fw-bold text-primary" style="font-size:0.7rem">${i.ciclo}</span>
                    <span class="text-muted small">${i.item_estandar}</span>
                </td>
                <td class="border-end">
                    <div class="fw-bold mb-1">${i.descripcion_item}</div>
                    <div class="text-muted small">${i.criterio} <br> <strong class="text-primary mt-1 d-inline-block">Valor: ${i.evaluacion}%</strong></div>
                </td>
                <td class="border-end text-center bg-light">
                    <select class="form-select form-select-sm mx-auto" style="max-width: 150px;" onchange="guardar(${i.id_detalle}, this.value, 'cumple')">
                        <option value="-1" ${estado === -1 || isNaN(estado) ? 'selected' : ''}>PENDIENTE</option>
                        <option value="0" ${estado === 0 ? 'selected' : ''}>NO APLICA</option>
                        <option value="1" ${estado === 1 ? 'selected' : ''}>CUMPLE</option>
                        <option value="2" ${estado === 2 ? 'selected' : ''}>NO CUMPLE</option>
                    </select>
                </td>
                <td>
                    <textarea class="form-control form-control-sm" rows="2" onblur="guardar(${i.id_detalle}, this.value, 'observaciones')" placeholder="Hallazgos...">${i.observaciones || ''}</textarea>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    async function guardar(id, val, campo) {
        const index = itemsMemoria.findIndex(x => x.id_detalle == id);
        if (index === -1) return;
        
        itemsMemoria[index][campo] = campo === 'cumple' ? parseInt(val) : val;

        if (campo === 'cumple') {
            const estado = parseInt(val);
            const row = document.getElementById(`row-${id}`);
            
            if (row) {
                if (estado === 1) row.className = 'bg-cumple';
                else if (estado === 2) row.className = 'bg-nocumple';
                else if (estado === 0) row.className = 'bg-noaplica';
                else row.className = 'bg-pendiente';
            }
            actualizarProgreso();
        }

        try {
            await fetch(`${API_URL}?table=evaluaciones&action=calificar&id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
                body: JSON.stringify({ 
                    cumple: parseInt(itemsMemoria[index].cumple), 
                    observaciones: itemsMemoria[index].observaciones || '' 
                })
            });
        } catch (e) { console.error("Error al guardar en BD:", e); }
    }

    function actualizarProgreso() {
        // 1. Contadores
        const cumple = itemsMemoria.filter(x => parseInt(x.cumple) === 1).length;
        const noCumple = itemsMemoria.filter(x => parseInt(x.cumple) === 2).length;
        const noAplica = itemsMemoria.filter(x => parseInt(x.cumple) === 0).length;
        const pendientes = itemsMemoria.filter(x => parseInt(x.cumple) === -1 || isNaN(parseInt(x.cumple))).length;
        const totalEvaluados = cumple + noCumple + noAplica;
        
        // 2. Sumatoria de Puntajes (Lógica oficial Res. 0312)
        let puntajeObtenido = 0;
        let pesoMaximoPosible = 0;

        itemsMemoria.forEach(i => {
            const estado = parseInt(i.cumple);
            const peso = parseFloat(i.evaluacion) || 0;
            
            // Sumamos todos los pesos para saber el % total del formulario actual
            pesoMaximoPosible += peso;

            // Puntos ganados (Cumple o No Aplica)
            if (estado === 1 || estado === 0) {
                puntajeObtenido += peso;
            }
        });

        // 3. Cálculo del Porcentaje Real
        let porcentajeFinal = 0;
        if (pesoMaximoPosible > 0) {
            // Se le suman los puntos de los ítems que NO aplican por tamaño de empresa
            let puntosFaltantes = 100 - pesoMaximoPosible; 
            
            // Solo sumar si tienen valores correctos, para evitar saltos raros
            porcentajeFinal = Math.round(puntajeObtenido + puntosFaltantes);
            
            // Límite de seguridad
            if (porcentajeFinal > 100) porcentajeFinal = 100;
        }

        // 4. Clasificación de acuerdo a Resolución 0312
        let clasificacionTexto = "PENDIENTE";
        let clasificacionColor = "bg-secondary";
        
        if (totalEvaluados > 0 || porcentajeFinal > 0) {
            if (porcentajeFinal <= 60) {
                clasificacionTexto = "CRÍTICO";
                clasificacionColor = "bg-critico";
            } else if (porcentajeFinal > 60 && porcentajeFinal <= 85) {
                clasificacionTexto = "MODERADAMENTE ACEPTABLE";
                clasificacionColor = "bg-moderado";
            } else {
                clasificacionTexto = "ACEPTABLE";
                clasificacionColor = "bg-aceptable";
            }
        }

        // 5. Renderizar Header Flotante
        const contenedor = document.getElementById('contador-progreso');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="d-flex align-items-center justify-content-end gap-4">
                    <div class="d-flex gap-3 text-center">
                        <div>
                            <span class="badge bg-success px-3 py-2 fs-6 rounded shadow-sm">${cumple}</span>
                            <small class="d-block mt-1 text-muted fw-bold" style="font-size:0.65rem;">CUMPLE</small>
                        </div>
                        <div>
                            <span class="badge bg-danger px-3 py-2 fs-6 rounded shadow-sm">${noCumple}</span>
                            <small class="d-block mt-1 text-muted fw-bold" style="font-size:0.65rem;">NO CUMPLE</small>
                        </div>
                        <div>
                            <span class="badge bg-info px-3 py-2 fs-6 rounded shadow-sm text-dark">${noAplica}</span>
                            <small class="d-block mt-1 text-muted fw-bold" style="font-size:0.65rem;">NO APLICA</small>
                        </div>
                        <div>
                            <span class="badge bg-secondary px-3 py-2 fs-6 rounded shadow-sm">${pendientes}</span>
                            <small class="d-block mt-1 text-muted fw-bold" style="font-size:0.65rem;">PENDIENTE</small>
                        </div>
                    </div>
                    <div class="ms-3 text-end" style="min-width: 140px;">
                        <h1 class="mb-0 fw-bold text-primary" style="font-size: 2.2rem; line-height: 1;">${porcentajeFinal}%</h1>
                        <div class="progress mt-2 shadow-sm" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar ${clasificacionColor.replace('bg-critico','bg-danger').replace('bg-moderado','bg-warning').replace('bg-aceptable','bg-success')}" role="progressbar" style="width: ${porcentajeFinal}%;" aria-valuenow="${porcentajeFinal}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>`;
        }

        // 6. Renderizar Footer Tipo Excel
        const tfoot = document.getElementById('tabla-resumen');
        if (tfoot) {
            tfoot.innerHTML = `
                <tr>
                    <td colspan="2" class="text-end text-uppercase bg-light">TOTAL CUMPLE</td>
                    <td class="text-center fs-5 text-success">${cumple}</td>
                    <td class="text-center text-uppercase bg-light">PORCENTAJE DE CUMPLIMIENTO</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end text-uppercase bg-light">TOTAL NO CUMPLE</td>
                    <td class="text-center fs-5 text-danger">${noCumple}</td>
                    <td rowspan="2" class="text-center align-middle fs-1 fw-bold">${porcentajeFinal}%</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end text-uppercase bg-light">TOTAL CRITERIOS EVALUADOS</td>
                    <td class="text-center fs-5">${itemsMemoria.length}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end text-uppercase bg-light">CLASIFICACIÓN</td>
                    <td class="text-center fs-5 ${clasificacionColor}">${clasificacionTexto}</td>
                </tr>
            `;
        }
    }
</script>
</body>
</html>