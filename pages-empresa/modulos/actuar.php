<?php
session_start();

require_once '../../includes/ConexionAPI.php';

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

<title>SSTMANAGER - ACTUAR</title>

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
    grid-template-columns:280px 1fr 320px;
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
}

.left-title{
    font-size:34px;
    line-height:1.1;
    font-weight:900;
    color:#111;
}

.left-sub{
    color:#6c757d;
    margin-top:8px;
}

.circle-wrap{
    width:200px;
    height:200px;
    border-radius:50%;
    border:16px solid #dfe5ef;
    margin:35px auto;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
}

.circle-wrap h1{
    font-size:56px;
    color:var(--primary);
    font-weight:900;
    margin:0;
}

.circle-wrap span{
    color:#6c757d;
    font-size:18px;
}

.legend{
    margin-top:20px;
}

.legend-item{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:18px;
    font-size:17px;
    font-weight:600;
}

.dot{
    width:16px;
    height:16px;
    border-radius:50%;
}

.dot.ok{ background:var(--success); }
.dot.warn{ background:var(--warning); }
.dot.no{ background:var(--danger); }

.plan-btn{
    width:100%;
    margin-top:30px;
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

/* CENTER */

.center-panel{
    padding:25px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
    flex-wrap:wrap;
}

.topbar-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.topbar-left h2{
    margin:0;
    font-size:36px;
    font-weight:900;
    color:#111;
}

.badge-count{
    background:var(--primary);
    color:#fff;
    border-radius:10px;
    padding:5px 12px;
    font-weight:700;
}

.search-area{
    display:flex;
    gap:10px;
}

.search-area input{
    width:280px;
    border-radius:12px;
    border:1px solid var(--border);
    padding:12px 15px;
    outline:none;
}

.search-area button{
    border-radius:12px;
    border:1px solid var(--border);
    background:#fff;
    padding:12px 18px;
    font-weight:700;
}

/* ENCABEZADO TABLA */

.table-head{
    display:grid;
    grid-template-columns:110px 1fr 90px 330px;
    gap:18px;
    padding:0 20px 18px;
    margin-bottom:8px;
    border-bottom:1px solid var(--border);
    font-size:14px;
    font-weight:900;
    color:#0d4d8b;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.table-head div{
    display:flex;
    align-items:center;
}

.table-head .text-center{
    justify-content:center;
}

/* RESPONSIVE */

@media(max-width:1450px){
    .table-head{
        display:none;
    }
}

/* ITEMS */

.items{
    display:flex;
    flex-direction:column;
    gap:16px;
}

.item-card{
    background:#fff;
    border-radius:20px;
    border:1px solid var(--border);
    padding:20px;
    display:grid;
    grid-template-columns:110px 1fr 90px 330px;
    gap:18px;
    align-items:center;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
    transition:.2s;
}

.item-card:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

.item-number{
    font-size:28px;
    font-weight:900;
    color:#111;
}

.item-activity{
    font-size:20px;
    line-height:1.3;
    color:#222;
}

.support-btn{
    width:52px;
    height:52px;
    border-radius:14px;
    border:2px solid #2f72ff;
    background:#fff;
    color:#2f72ff;
    font-size:22px;
}

.status-wrap{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.status-option{
    border-radius:30px;
    padding:10px 16px;
    border:2px solid;
    font-weight:800;
    cursor:pointer;
    font-size:13px;
    user-select:none;
}

.status-option input{
    display:none;
}

.status-si{
    border-color:var(--success);
    color:var(--success);
}

.status-proceso{
    border-color:var(--warning);
    color:#7d6100;
}

.status-no{
    border-color:var(--danger);
    color:var(--danger);
}

.status-na{
    border-color:#adb5bd;
    color:#6c757d;
}

/* RIGHT */

.right-panel{
    padding:25px;
}

.right-title{
    font-size:30px;
    font-weight:900;
    margin-bottom:25px;
}

.resume-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:22px;
}

.resume-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.resume-icon{
    width:44px;
    height:44px;
    border-radius:12px;
    background:#edf4ff;
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
}

.resume-check{
    width:22px;
    height:22px;
    border-radius:50%;
    background:#28a745;
}

.logo-box{
    margin-top:35px;
    border:2px dashed #dbe4f0;
    border-radius:20px;
    min-height:220px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

.logo-box img{
    max-width:100%;
    max-height:180px;
    object-fit:contain;
}

/* RESPONSIVE */

@media(max-width:1450px){
    .layout{
        grid-template-columns:1fr;
    }
    .item-card{
        grid-template-columns:1fr;
    }
    .item-number{
        font-size:22px;
    }
    .item-activity{
        font-size:18px;
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
            ACTUAR
        </div>
        <div class="left-sub">
            Mejora continua del PESV
        </div>

        <div class="circle-wrap">
            <h1 id="scorePct">0%</h1>
            <span><span id="scorePts">0</span> / <span id="maxPts">0</span> pts</span>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="dot ok"></div>
                <span>Sí (2)</span>
            </div>
            <div class="legend-item">
                <div class="dot warn"></div>
                <span>En proceso (1)</span>
            </div>
            <div class="legend-item">
                <div class="dot no"></div>
                <span>No (0)</span>
            </div>
        </div>

        <button class="plan-btn">
            Aplicar Mejoras
        </button>
    </div>

    <!-- CENTER -->
    <div class="soft-card center-panel">

        <div class="topbar">
            <div class="topbar-left">
                <h2>Mejora continua del PESV</h2>
                <div class="badge-count" id="countBadge">0</div>
            </div>

            <div class="search-area">
                <input type="text" id="searchInput" placeholder="Buscar ítem o actividad...">
                <button id="resetBtn">RESET</button>
            </div>
        </div>

        <div class="table-head">
            <div>ÍTEM</div>
            <div>ACTIVIDAD</div>
            <div class="text-center">SOPORTE DOCUMENTAL</div>
            <div class="text-center">ESTADO</div>
        </div>

        <div class="items" id="body"></div>

    </div>

    <!-- RIGHT -->
    <div class="soft-card right-panel">

        <div class="right-title">
            RESUMEN DE PROCESOS
        </div>

        <!-- Títulos adaptados a la temática de Actuar -->
        <div class="resume-item">
            <div class="resume-left">
                <div class="resume-icon">
                    <i class="fa-solid fa-wrench"></i>
                </div>
                <div>Acciones Preventivas</div>
            </div>
            <div class="resume-check"></div>
        </div>

        <div class="resume-item">
            <div class="resume-left">
                <div class="resume-icon">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>Acciones Correctivas</div>
            </div>
            <div class="resume-check"></div>
        </div>

        <div class="resume-item">
            <div class="resume-left">
                <div class="resume-icon">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
                <div>Mejora Continua</div>
            </div>
            <div class="resume-check"></div>
        </div>

        <div class="logo-box">
            <?php if ($logoUrl): ?>
                <img src="<?= $logoUrl ?>" alt="Logo">
            <?php else: ?>
                <h2 style="color:#b0b7c3;">TU LOGO AQUÍ</h2>
            <?php endif; ?>
        </div>

    </div>

</div>
</div>

<!-- OFFCANVAS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="soporteDrawer" style="width:min(1100px,95vw);">
    <div class="offcanvas-body p-0 position-relative">
        <button type="button"
                class="btn-close position-absolute top-0 end-0 m-3"
                data-bs-dismiss="offcanvas"></button>
        <iframe id="frame"
                style="width:100%;height:100%;border:0;"></iframe>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

// ITEMS DE ACTUAR
const actuarItems = [
    {item:"7.1.1 →", actividad:"Procedimiento de Mejora continua, acciones preventivas y correctivas", soporte:"7.1.1.php"},
    {item:"7.1.1 →", actividad:"Acciones correctivas - oportunidades de mejora", soporte:"7.1.1-2.php"},
    {item:"7.1.3 → 7.1.4", actividad:"Matriz de seguimiento y cierre de hallazgos", soporte:"7.1.3-4.php"}
];

// RENDER
const body = document.getElementById("body");

function render(data){
    body.innerHTML = "";
    document.getElementById("countBadge").innerText = data.length;

    data.forEach((r,i)=>{
        const disabled = r.soporte ? "" : "disabled";

        body.innerHTML += `
        <div class="item-card">
            <div class="item-number" style="font-size: 20px;">
                ${r.item}
            </div>
            <div class="item-activity">
                ${r.actividad}
            </div>
            <div>
                <button class="support-btn"
                        data-file="${r.soporte}"
                        ${disabled}>
                    <i class="fa-regular fa-file-lines"></i>
                </button>
            </div>
            <div class="status-wrap">
                <label class="status-option status-si">
                    <input type="radio" name="cal_${i}" value="2">
                    SI
                </label>
                <label class="status-option status-proceso">
                    <input type="radio" name="cal_${i}" value="1">
                    PROCESO
                </label>
                <label class="status-option status-no">
                    <input type="radio" name="cal_${i}" value="0">
                    NO
                </label>
                <label class="status-option status-na">
                    <input type="radio" name="cal_${i}" value="na">
                    N/A
                </label>
            </div>
        </div>
        `;
    });

    calcular();
}

// CALCULO
document.addEventListener("change", calcular);

function calcular(){
    let score = 0;
    let max = 0;

    document.querySelectorAll(".item-card").forEach(card=>{
        const val = card.querySelector("input:checked")?.value;

        if(val !== undefined){
            if(val !== "na") max += 2;
            if(val === "2") score += 2;
            else if(val === "1") score += 1;
        }
    });

    let pct = max === 0 ? 0 : Math.round((score/max)*100);

    document.getElementById("scorePct").innerText = pct + "%";
    document.getElementById("scorePts").innerText = score;
    document.getElementById("maxPts").innerText = max;
}

// BUSCADOR
document.getElementById("searchInput").addEventListener("input", e=>{
    let q = e.target.value.toLowerCase();
    render(actuarItems.filter(x =>
        (x.item + x.actividad).toLowerCase().includes(q)
    ));
});

// RESET
document.getElementById("resetBtn").onclick = ()=>{
    document.getElementById("searchInput").value = "";
    render(actuarItems);
};

// OFFCANVAS
const drawer = new bootstrap.Offcanvas(
    document.getElementById("soporteDrawer")
);
const frame = document.getElementById("frame");

document.addEventListener("click",(e)=>{
    const btn = e.target.closest("button[data-file]");

    if(btn && btn.dataset.file){
        // Ruta para la carpeta de soportes de Actuar
        frame.src = `./soporte-actuar/${btn.dataset.file}`;
        drawer.show();
    }
});

// LIMPIAR IFRAME AL CERRAR
document.getElementById("soporteDrawer").addEventListener("hidden.bs.offcanvas", () => {
    frame.src = "";
});

// INIT
render(actuarItems);

</script>

</body>
</html>