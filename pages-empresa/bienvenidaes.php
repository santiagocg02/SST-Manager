<?php
session_start();

// 1. Validación de seguridad
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit;
}

// 2. Conexión a la API para traer el nombre exacto de la empresa
require_once '../includes/ConexionAPI.php'; 
$api = new ConexionAPI();

$idEmpresa = (int)($_SESSION["id_empresa"] ?? 0);
$token     = $_SESSION["token"] ?? "";

$nombreEmpresa = "Empresa S.A.S";

// 3. Filtrar la empresa por ID
if ($idEmpresa > 0) {
    $resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
    $todasLasEmpresas = (isset($resEmpresas['data'])) ? $resEmpresas['data'] : [];

    foreach ($todasLasEmpresas as $emp) {
        if ((int)($emp['id_empresa'] ?? 0) === $idEmpresa) {
            $nombreEmpresa = $emp['nombre_empresa'] ?? 'Sin Empresa';
            break;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SSTManager - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================
           MAGIA FLEXBOX PARA CENTRAR TODO EL BODY
           ========================================= */
        body { 
            background-color: #f4f7f6; 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0;
            padding: 40px 20px;
            display: flex;               /* Activa Flexbox */
            justify-content: center;     /* Centra horizontalmente todo el bloque */
            align-items: flex-start;     /* Alinea arriba (para que no se vaya al medio vertical si hay poco contenido) */
            min-height: 100vh;
        }
        
        .dashboard-fade { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        /* Contenedor principal que agrupa título y tarjetas */
        .dashboard-wrapper {
            width: 100%;
            max-width: 850px; /* Controla el ancho máximo del dashboard */
        }

        .welcome-section { margin-bottom: 40px; }
        .welcome-section h1 { font-size: 3.5rem; font-weight: 900; color: #1a1a1a; margin: 0; letter-spacing: -1px; text-transform: uppercase; }
        .welcome-section h2 { font-size: 1.4rem; color: #5a7184; margin-top: 5px; text-transform: uppercase; font-weight: 500; }
        
        /* Grilla del Dashboard */
        .dashboard-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; /* Dos columnas iguales */
            gap: 30px; 
        }
        
        /* Tarjeta Azul - Clickable */
        .card-cumplimiento {
            background: linear-gradient(145deg, #1a3a5a, #0d253d);
            color: white; border-radius: 20px;
            padding: 40px 30px; text-align: center;
            box-shadow: 0 10px 30px rgba(26,58,90,0.2);
            transition: all 0.3s ease;
            cursor: pointer; 
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-cumplimiento:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 35px rgba(26,58,90,0.3); 
            background: linear-gradient(145deg, #1f456b, #113152);
        }
        .card-cumplimiento .value { font-size: 5rem; font-weight: 800; line-height: 1; margin: 15px 0; }
        .card-cumplimiento .hover-hint { font-size: 0.85rem; color: #a1b5cc; opacity: 0; transition: opacity 0.3s; margin-top: 10px;}
        .card-cumplimiento:hover .hover-hint { opacity: 1; }
        
        /* Contenedor de Alertas e Incidentes */
        .stats-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
        }

        .stat-box {
            background: white; border-radius: 20px; padding: 25px 30px;
            display: flex; align-items: center; justify-content: space-between;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.3s;
        }
        .stat-box:hover { transform: translateX(8px); background-color: #fdfdfd; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
        
        .stat-info { display: flex; align-items: center; gap: 15px; }
        .stat-info i { font-size: 2.5rem; }
        .stat-info h4 { margin: 0; font-size: 1.4rem; font-weight: 600; color: #333; }
        .stat-box .number { font-size: 3rem; font-weight: 800; color: #1a1a1a; }
        
        .text-orange { color: #f39c12; }
        .text-red { color: #e74c3c; }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .welcome-section h1 { font-size: 2.8rem; }
        }
    </style>
</head>
<body class="dashboard-fade">

    <div class="dashboard-wrapper">
        
        <div class="welcome-section">
            <h1>BIENVENIDO</h1>
            <h2><?= htmlspecialchars($nombreEmpresa) ?></h2>
        </div>

        <div class="dashboard-grid">
            <div class="card-cumplimiento" onclick="irAutoevaluacion()" title="Ir a Autoevaluación">
                <p class="mb-0 text-uppercase fw-bold" style="letter-spacing: 1px; color:#a1b5cc;">Cumplimiento</p>
                <div class="value" id="val-porcentaje">--%</div>
                <div class="hover-hint"><i class="fa-solid fa-hand-pointer me-1"></i> Clic para ir a evaluar</div>
            </div>

            <div class="stats-column">
                <div class="stat-box">
                    <div class="stat-info">
                        <i class="fa-solid fa-triangle-exclamation text-orange"></i>
                        <h4>Alertas</h4>
                    </div>
                    <div class="number" id="val-alertas">0</div>
                </div>

                <div class="stat-box">
                    <div class="stat-info">
                        <i class="fa-solid fa-circle-xmark text-red"></i>
                        <h4>Incidentes</h4>
                    </div>
                    <div class="number" id="val-incidentes">0</div>
                </div>
            </div>
        </div>
        
    </div>

    <script>
        // Sincronización automática de datos con la API
        async function actualizarDashboard() {
            const id = "<?= $idEmpresa ?>";
            const tk = "<?= $token ?>";

            if (id === "0") return;

            try {
                const response = await fetch(`http://localhost/sstmanager-backend/public/index.php?table=evaluaciones&id=${id}`, {
                    headers: { 'Authorization': `Bearer ${tk}` }
                });
                const res = await response.json();

                if (res.ok) {
                    const items = res.formulario;
                    const c = items.filter(x => parseInt(x.cumple) === 1).length;
                    const n = items.filter(x => parseInt(x.cumple) === 2).length;
                    const p = items.filter(x => parseInt(x.cumple) === 0).length;
                    
                    const total = items.length;
                    const porc = total > 0 ? Math.round((c / total) * 100) : 0;

                    document.getElementById('val-porcentaje').innerText = porc + '%';
                    document.getElementById('val-alertas').innerText = n;
                    document.getElementById('val-incidentes').innerText = p;
                }
            } catch (e) {
                console.error("Error Dashboard:", e);
                document.getElementById('val-porcentaje').innerText = "0%";
            }
        }

        // Navegación a la Autoevaluación
        function irAutoevaluacion() {
            const id = "<?= $idEmpresa ?>";
            if (id !== "0") {
                // Navega dentro del iframe del menú
                window.location.href = `empresa/evaluacion_empresa.php?id=${id}`;
            }
        }

        document.addEventListener('DOMContentLoaded', actualizarDashboard);
    </script>
</body>
</html>