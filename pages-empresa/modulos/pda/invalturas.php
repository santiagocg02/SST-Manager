<?php
session_start();

require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

// LOGO DINÁMICO
$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
$logoUrl = "";

if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("empresas/$empresaId", "GET", null, $token);
    $logoUrl = $resEmpresa['data']['logo_url'] ?? "";
}
?>

<!doctype html>
<html lang="es">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>SSTMANAGER - INVENTARIO ALTURAS EDITABLE</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

:root{
    --primary:#0d4d8b;
    --primary-hover:#0a3b69;
    --bg:#f4f7fb;
    --border:#dbe4f0;
    --success:#198754;
    --warning:#d6a400;
    --danger:#dc3545;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:var(--bg);
}

.main-page{
    padding:20px;
}

/* LAYOUT */
.layout{
    display:grid;
    grid-template-columns:280px 1fr;
    gap:20px;
}

/* CARD */
.soft-card{
    background:#fff;
    border-radius:24px;
    border:1px solid var(--border);
    box-shadow:0 4px 18px rgba(0,0,0,.07);
}

/* LEFT PANEL */
.left-panel{
    padding:25px;
    height: fit-content;
}

.left-title{
    font-size:30px;
    line-height:1.1;
    font-weight:900;
    color:#111;
}

.left-sub{
    color:#6c757d;
    margin-top:8px;
    font-size: 14px;
}

.logo-box{
    margin-top:30px;
    border:2px dashed #dbe4f0;
    border-radius:20px;
    min-height:180px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

.logo-box img{
    max-width:100%;
    max-height:140px;
    object-fit:contain;
}

.plan-btn{
    width:100%;
    margin-top:20px;
    border:none;
    border-radius:14px;
    padding:15px;
    background:var(--primary);
    color:#fff;
    font-weight:700;
    transition:.2s;
}

.plan-btn:hover{
    background:var(--primary-hover);
}

.print-btn{
    background: #6c757d;
    margin-top: 10px;
}

.print-btn:hover{
    background: #5c636a;
}

/* CENTER PANEL */
.center-panel{
    padding:25px;
    overflow: hidden; 
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.topbar-left h2{
    margin:0;
    font-size:28px;
    font-weight:900;
    color:#111;
    text-transform: uppercase;
}

/* TABLA EDITABLE */
.table-editable th {
    background-color: #f8f9fa;
    color: var(--primary);
    font-size: 13px;
    text-transform: uppercase;
    font-weight: 800;
    vertical-align: middle;
    text-align: center;
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
}

.table-editable td {
    vertical-align: middle;
    padding: 10px 8px;
}

.table-editable .form-control, 
.table-editable .form-select {
    font-size: 13px;
    border: 1px solid transparent;
    background-color: #f8f9fa;
    border-radius: 8px;
    transition: 0.3s;
}

.table-editable .form-control:focus, 
.table-editable .form-select:focus {
    border-color: var(--primary);
    background-color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(13, 77, 139, 0.1);
}

.table-editable textarea.form-control {
    resize: vertical;
    min-height: 40px;
    overflow-y: hidden; /* Ocultar scroll para la impresión automática */
}

.btn-add-row {
    background-color: var(--success);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 10px 20px;
    font-weight: bold;
    font-size: 14px;
    transition: 0.2s;
}

.btn-add-row:hover {
    background-color: #146c43;
    color: white;
}

.btn-delete {
    color: var(--danger);
    background: rgba(220, 53, 69, 0.1);
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.btn-delete:hover {
    background: var(--danger);
    color: white;
}

/* =========================================
   REGLAS ESPECÍFICAS PARA IMPRESIÓN
   ========================================= */
@media print {
    @page {
        size: landscape; /* Fuerza formato horizontal */
        margin: 10mm;
    }

    body {
        background: #fff;
    }

    /* Ocultamos elementos innecesarios */
    .left-panel, 
    .btn-add-row, 
    .btn-delete, 
    th:last-child, /* Oculta la cabecera de "Quitar" */
    td:last-child /* Oculta las celdas de "Quitar" */
    {
        display: none !important;
    }

    .layout {
        display: block; /* Rompe el grid para usar todo el ancho */
    }

    .center-panel {
        border: none;
        box-shadow: none;
        padding: 0;
    }

    /* Ajustes visuales de la tabla al imprimir */
    .table-responsive {
        overflow: visible !important;
    }

    .table-editable {
        border-collapse: collapse !important;
        width: 100% !important;
    }

    .table-editable th, .table-editable td {
        border: 1px solid #ccc !important;
        padding: 6px !important;
    }

    .table-editable th {
        background-color: #eee !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Convertir visualmente los inputs en texto plano */
    .table-editable .form-control, 
    .table-editable .form-select {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        height: auto !important;
        appearance: none !important; /* Quita la flechita del select */
        -moz-appearance: none !important;
        -webkit-appearance: none !important;
        color: #000 !important;
        font-size: 11px !important; /* Tamaño más pequeño para que quepa todo */
        white-space: pre-wrap !important;
    }
}

/* RESPONSIVE PANTALLA */
@media(max-width:1200px){
    .layout{
        grid-template-columns:1fr;
    }
}

</style>
</head>

<body>

<div class="main-page">

<div class="layout">

    <!-- LEFT -->
    <div class="soft-card left-panel">
        <div class="left-title">
            INVENTARIO
        </div>
        <div class="left-sub">
            Programa de Prevención y Protección contra Caídas (Edición)
        </div>

        <div class="logo-box">
            <?php if ($logoUrl): ?>
                <img src="<?= $logoUrl ?>" alt="Logo">
            <?php else: ?>
                <h4 style="color:#b0b7c3;">TU LOGO AQUÍ</h4>
            <?php endif; ?>
        </div>

        <button class="plan-btn" onclick="guardarDatos()">
            <i class="fa-solid fa-floppy-disk me-2"></i> Guardar
        </button>

        <button class="plan-btn print-btn" onclick="imprimirInventario()">
            <i class="fa-solid fa-print me-2"></i> Imprimir Tabla
        </button>
    </div>

    <!-- CENTER -->
    <div class="soft-card center-panel">

        <div class="topbar">
            <div class="topbar-left">
                <h2 id="tituloPrint">EDICIÓN DE TAREAS EN ALTURAS</h2>
            </div>
            <div>
                <button class="btn-add-row" onclick="agregarFila()">
                    <i class="fa-solid fa-plus me-2"></i> Agregar Nueva Fila
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-editable" id="tablaInventario">
                <thead>
                    <tr>
                        <th style="width: 40px;">Ítem</th>
                        <th style="min-width: 140px;">Área / Proceso</th>
                        <th style="min-width: 160px;">Tarea</th>
                        <th style="min-width: 180px;">Descripción</th>
                        <th style="min-width: 100px;">Altura Aprox.</th>
                        <th style="min-width: 100px;">Frecuencia</th>
                        <th style="min-width: 160px;">Personal Involucrado</th>
                        <th style="min-width: 180px;">Riesgos Asociados</th>
                        <th style="min-width: 180px;">Controles Existentes</th>
                        <th style="min-width: 90px;">Realizado</th>
                        <th style="width: 50px;">Quitar</th>
                    </tr>
                </thead>
                <tbody id="tbodyInventario">
                    <!-- Fila Inicial -->
                    <tr>
                        <td class="text-center fw-bold text-muted item-number">1</td>
                        <td><input type="text" class="form-control" placeholder="Ej. Producción"></td>
                        <td><input type="text" class="form-control" placeholder="Ej. Soldadura en altura"></td>
                        <td><textarea class="form-control" rows="1" placeholder="Breve descripción..."></textarea></td>
                        <td><input type="text" class="form-control" placeholder="Ej. 2 - 8 m"></td>
                        <td>
                            <select class="form-select">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Frecuente">Frecuente</option>
                                <option value="Ocasional">Ocasional</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" placeholder="Ej. Soldadores"></td>
                        <td><textarea class="form-control" rows="1" placeholder="Ej. Caídas..."></textarea></td>
                        <td><textarea class="form-control" rows="1" placeholder="Ej. Arnés..."></textarea></td>
                        <td>
                            <select class="form-select">
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-delete" onclick="eliminarFila(this)" title="Eliminar Fila">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const tbody = document.getElementById("tbodyInventario");

    function actualizarNumeros() {
        const filas = tbody.querySelectorAll("tr");
        filas.forEach((fila, index) => {
            fila.querySelector(".item-number").innerText = index + 1;
        });
    }

    function agregarFila() {
        const nuevaFila = document.createElement("tr");
        
        nuevaFila.innerHTML = `
            <td class="text-center fw-bold text-muted item-number"></td>
            <td><input type="text" class="form-control" placeholder="Ej. Producción"></td>
            <td><input type="text" class="form-control" placeholder="Ej. Soldadura en altura"></td>
            <td><textarea class="form-control" rows="1" placeholder="Breve descripción..."></textarea></td>
            <td><input type="text" class="form-control" placeholder="Ej. 2 - 8 m"></td>
            <td>
                <select class="form-select">
                    <option value="" disabled selected>Seleccione...</option>
                    <option value="Frecuente">Frecuente</option>
                    <option value="Ocasional">Ocasional</option>
                </select>
            </td>
            <td><input type="text" class="form-control" placeholder="Ej. Soldadores"></td>
            <td><textarea class="form-control" rows="1" placeholder="Ej. Caídas..."></textarea></td>
            <td><textarea class="form-control" rows="1" placeholder="Ej. Arnés..."></textarea></td>
            <td>
                <select class="form-select">
                    <option value="Si">Sí</option>
                    <option value="No">No</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn-delete" onclick="eliminarFila(this)" title="Eliminar Fila">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(nuevaFila);
        actualizarNumeros();
        asignarAutoResize();
    }

    function eliminarFila(boton) {
        if (tbody.querySelectorAll("tr").length > 1) {
            const fila = boton.closest("tr");
            fila.remove();
            actualizarNumeros();
        } else {
            alert("Debe haber al menos una fila en el inventario.");
        }
    }

    function guardarDatos() {
        alert("Botón de guardar presionado. Aquí iría la conexión con la base de datos.");
    }

    // --- LÓGICA DE IMPRESIÓN Y AUTO-RESIZE ---
    
    // Función para que los textareas crezcan solos al escribir
    function asignarAutoResize() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(ta => {
            ta.removeEventListener('input', autoResize); // Evitar duplicados
            ta.addEventListener('input', autoResize);
        });
    }

    function autoResize(e) {
        e.target.style.height = 'auto';
        e.target.style.height = e.target.scrollHeight + 'px';
    }

    function imprimirInventario() {
        // Asegurarnos de que todos los textareas tienen el alto correcto antes de imprimir
        document.querySelectorAll('textarea').forEach(ta => {
            ta.style.height = 'auto';
            ta.style.height = ta.scrollHeight + 'px';
        });

        // Cambiamos el título a algo más formal para el PDF/Impresión
        const titulo = document.getElementById("tituloPrint");
        const tituloOriginal = titulo.innerText;
        titulo.innerText = "INVENTARIO DE TAREAS EN ALTURAS";

        window.print();

        // Restaurar el título original después
        setTimeout(() => {
            titulo.innerText = tituloOriginal;
        }, 1000);
    }

    // Inicializamos el autoresize para la primera fila cargada
    asignarAutoResize();

</script>

</body>
</html>