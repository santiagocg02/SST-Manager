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
$nombreE = $_SESSION["nombre_empresa"] ?? "Empresa Demostración";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SST Manager - Capacitaciones</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="../../../assets/css/base.css">
  <link rel="stylesheet" href="../../../assets/css/planear.css">

  <style>
    :root {
        --color-corporativo-azul: #003366;
        --color-corporativo-verde: #198754;
    }

    body { background-color: #f4f7f6; }

    .planear-hero {
        background: linear-gradient(135deg, var(--color-corporativo-azul) 0%, #004080 100%);
        color: white;
        border: none;
    }
    .sheet-title { color: #ffffff !important; }
    .sheet-subtitle { color: rgba(255,255,255,0.8) !important; }

    .btn-crear-corporativo {
        background-color: var(--color-corporativo-verde);
        color: white;
        border: none;
        font-weight: 600;
        padding: 8px 20px;
        transition: all 0.3s;
    }
    .btn-crear-corporativo:hover {
        background-color: #146c43;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .search-wrapper {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 4px 12px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .search-wrapper input {
        border: none;
        outline: none;
        padding: 5px 10px;
        width: 100%;
    }

    .table-capacitacion thead th {
        background-color: #f8f9fa;
        color: var(--color-corporativo-azul);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--color-corporativo-verde);
    }

    .btn-pdf {
        color: #dc3545;
        border: 1px solid #dc3545;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: lowercase;
        border-radius: 4px;
        background: transparent;
    }
    .btn-pdf:hover {
        background-color: #dc3545;
        color: white;
    }

    .btn-actualizar {
        color: var(--color-corporativo-azul);
        border: 1px solid var(--color-corporativo-azul);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: lowercase;
        border-radius: 4px;
    }
    .btn-actualizar:hover {
        background-color: var(--color-corporativo-azul);
        color: white;
    }
  </style>
</head>

<body>

<div class="planear-page-scroll p-4">
  <div class="page-wrap container-fluid">

    <div class="row mb-4">
      <div class="col-12">
        <div class="planear-hero card-soft p-4 shadow-sm">
          <div class="hero-inner d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <h4 class="sheet-title mb-1">
                <i class="fa-solid fa-chalkboard-user me-2"></i> Capacitaciones
              </h4>
              <div class="sheet-subtitle">Hacer · Registro de Formación y Listas de Asistencia</div>
            </div>
            <div class="text-end d-none d-md-block">
                <small class="d-block opacity-75">Empresa en sesión:</small>
                <span class="fw-bold"><?= htmlspecialchars($nombreE) ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 align-items-center mb-4">
        <div class="col-auto">
            <button type="button" class="btn btn-crear-corporativo shadow-sm" onclick="window.location.href='CrearCapacitacion.php'">
                <i class="fa-solid fa-plus me-2"></i> + crear Capacitación
            </button>
        </div>
        <div class="col">
            <div class="search-wrapper ms-md-3">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" id="searchInput" placeholder="Buscar por tema o descripción...">
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary border-dashed" id="resetBtn">Reset</button>
            <button class="btn btn-light border ms-2"><i class="fa-solid fa-filter me-1"></i> Filtro</button>
        </div>
    </div>

    <div class="card-soft bg-white shadow-sm border-0 overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle table-capacitacion">
          <thead>
            <tr>
              <th class="ps-4">Fecha</th>
              <th>Información de la Capacitación</th>
              <th>Horario</th>
              <th class="text-center">Asistencia</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody id="capacitacionBody">
            <tr>
                <td class="ps-4 fw-bold text-muted">21/04/2026</td>
                <td>
                    <div class="fw-bold text-dark">MANEJO DE EXTINTORES Y FUEGO</div>
                    <div class="small text-muted">Referencia: Uso de equipos tipo ABC en planta principal.</div>
                </td>
                <td>
                    <span class="badge bg-light text-dark border"><i class="fa-regular fa-clock me-1"></i> 08:00 - 10:00</span>
                </td>
                <td class="text-center">
                    <a href="#" class="btn btn-sm btn-pdf px-3"><i class="fa-solid fa-file-pdf"></i> pdf</a>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-actualizar px-3">actualizar</button>
                </td>
            </tr>

            <?php for($i=0; $i<3; $i++): ?>
            <tr style="height: 60px;">
                <td colspan="5"></td>
            </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4 d-flex justify-content-between align-items-center">
        <div class="text-muted small">Mostrando registros de capacitaciones realizadas</div>
        <div class="btn-group shadow-sm">
            <button class="btn btn-white border bg-white px-3"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="btn btn-white border bg-white px-3"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const input = document.getElementById('searchInput');
    input.addEventListener('keyup', function() {
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('#capacitacionBody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if(row.children[1]) { 
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    });

    document.getElementById('resetBtn').addEventListener('click', () => {
        input.value = '';
        location.reload();
    });
</script>

</body>
</html>