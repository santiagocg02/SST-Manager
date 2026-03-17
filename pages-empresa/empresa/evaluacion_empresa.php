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
        .table-eval tbody tr { box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-radius: 8px; transition: all 0.2s; }
        .bg-pendiente { border-left: 6px solid #adb5bd !important; background-color: #fff; }
        .bg-cumple { border-left: 6px solid #28a745 !important; background-color: #f8fff9; }
        .bg-nocumple { border-left: 6px solid #dc3545 !important; background-color: #fff8f8; }
        .sticky-header { position: sticky; top: 0; background: #fff; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 1rem; border-radius: 0 0 15px 15px; }
        .form-select-sm { font-weight: 700; border-radius: 8px; }
        textarea.form-control-sm { border-radius: 8px; border: 1px solid #eee; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-3">
    <div class="card border-0 bg-transparent">
        <div class="sticky-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-7 d-flex align-items-center">
            <a href="../../pages-empresa/bienvenidaes.php" class="btn btn-outline-secondary btn-sm rounded-pill me-3 px-3 shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
            <div>
                <h4 class="mb-0 text-dark fw-bold">
                    <i class="fa-solid fa-clipboard-check text-primary me-2"></i>Autoevaluación de Estándares
                </h4>
                <span id="info-empresa" class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1">Verificando...</span>
            </div>
        </div>
        <div class="col-md-5 text-end" id="contador-progreso"></div>
    </div>
</div>

        <div class="table-responsive px-2">
            <table class="table table-eval align-middle">
                <thead class="text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3">Ciclo / Estándar</th>
                        <th>Descripción y Criterio</th>
                        <th style="width: 180px;">Calificación</th>
                        <th style="width: 300px;">Observaciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-items"></tbody>
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

    document.addEventListener('DOMContentLoaded', () => { if (ID_EMPRESA) cargarDatos(); });

    async function cargarDatos() {
        try {
            const r = await fetch(`${API_URL}?table=evaluaciones&id=${ID_EMPRESA}`, {
                headers: { 'Authorization': `Bearer ${TOKEN}` }
            });
            const res = await r.json();
            if (res.ok) {
                itemsMemoria = res.formulario;
                if (res.info_general) document.getElementById('info-empresa').innerText = `${res.info_general.estandar_aplicado} `;
                renderizar();
                actualizarProgreso();
            } else { await crearEvaluacion(); }
        } catch (e) { console.error("Error:", e); }
    }

    async function crearEvaluacion() {
        const r = await fetch(`${API_URL}?table=evaluaciones`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
            body: JSON.stringify(CONFIG_POST)
        });
        if ((await r.json()).ok) cargarDatos();
    }

    function renderizar() {
        const tbody = document.getElementById('tabla-items');
        tbody.innerHTML = '';
        itemsMemoria.forEach(i => {
            let clase = (i.cumple == 1) ? 'bg-cumple' : (i.cumple == 2 ? 'bg-nocumple' : 'bg-pendiente');
            const tr = document.createElement('tr');
            tr.className = clase;
            tr.id = `row-${i.id_detalle}`;
            tr.innerHTML = `
                <td class="ps-3">
                    <span class="d-block fw-bold text-primary" style="font-size:0.7rem">${i.ciclo}</span>
                    <span class="text-muted small">${i.item_estandar}</span>
                </td>
                <td>
                    <div class="fw-bold mb-1">${i.descripcion_item}</div>
                    <div class="text-muted small">${i.criterio}</div>
                </td>
                <td>
                    <select class="form-select form-select-sm" onchange="guardar(${i.id_detalle}, this.value, 'cumple')">
                        <option value="0" ${i.cumple == 0 ? 'selected' : ''}>PENDIENTE</option>
                        <option value="1" ${i.cumple == 1 ? 'selected' : ''}>CUMPLE</option>
                        <option value="2" ${i.cumple == 2 ? 'selected' : ''}>NO CUMPLE</option>
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
        itemsMemoria[index][campo] = val;

        if (campo === 'cumple') {
            const row = document.getElementById(`row-${id}`);
            if (row) row.className = (val == 1) ? 'bg-cumple' : (val == 2 ? 'bg-nocumple' : 'bg-pendiente');
            actualizarProgreso();
        }

        try {
            await fetch(`${API_URL}?table=evaluaciones&action=calificar&id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
                body: JSON.stringify({ cumple: parseInt(itemsMemoria[index].cumple), observaciones: itemsMemoria[index].observaciones || '' })
            });
        } catch (e) { console.error("Error al guardar:", e); }
    }

    function actualizarProgreso() {
        const total = itemsMemoria.length;
        const cumple = itemsMemoria.filter(x => parseInt(x.cumple) === 1).length;
        const noCumple = itemsMemoria.filter(x => parseInt(x.cumple) === 2).length;
        const pendientes = itemsMemoria.filter(x => parseInt(x.cumple) === 0).length;
        const porcentaje = total > 0 ? Math.round(((cumple + noCumple) / total) * 100) : 0;
        
        const contenedor = document.getElementById('contador-progreso');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="d-flex gap-2 me-2">
                        <div class="text-center"><span class="badge bg-success d-block">${cumple}</span><small style="font-size:0.6rem">CUMPLE</small></div>
                        <div class="text-center"><span class="badge bg-danger d-block">${noCumple}</span><small style="font-size:0.6rem">NO CUMPLE</small></div>
                        <div class="text-center"><span class="badge bg-secondary d-block">${pendientes}</span><small style="font-size:0.6rem">PENDIENTE</small></div>
                    </div>
                    <div class="text-end">
                        <h3 class="mb-0 fw-bold text-primary">${porcentaje}%</h3>
                        <div class="progress" style="width:120px; height:8px;"><div class="progress-bar bg-primary" style="width:${porcentaje}%"></div></div>
                    </div>
                </div>`;
        }
    }
</script>
</body>
</html>