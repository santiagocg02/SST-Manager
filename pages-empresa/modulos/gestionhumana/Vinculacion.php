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
    <title>SST Manager - Ingreso de Personal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../../assets/css/base.css">
    <link rel="stylesheet" href="../../../assets/css/planear.css">

    <style>
        :root {
            --color-corporativo-azul: #003366;
            --color-corporativo-verde: #198754;
            --bg-light: #f4f7f6;
        }

        body { background-color: var(--bg-light); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        /* Hero Header */
        .planear-hero {
            background: linear-gradient(135deg, var(--color-corporativo-azul) 0%, #004080 100%);
            color: white;
            border-radius: 12px;
        }

        /* Botones */
        .btn-crear-corporativo {
            background-color: var(--color-corporativo-verde);
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            transition: all 0.3s;
        }
        .btn-crear-corporativo:hover {
            background-color: #146c43;
            color: white;
            transform: translateY(-1px);
        }

        .btn-actualizar {
            color: var(--color-corporativo-azul);
            border: 1px solid var(--color-corporativo-azul);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .gestion-salida-banner {
        background-color: #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        border-left: 5px solid #6c757d;
    }
    .text-xs { font-size: 0.7rem; }

        /* Buscador */
        .search-wrapper {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 50px;
            padding: 8px 18px;
            display: flex;
            align-items: center;
        }
        .search-wrapper input { border: none; outline: none; width: 100%; margin-left: 10px; }

        /* Tabla */
        .table-vinculacion thead th {
            background-color: #f8f9fa;
            color: var(--color-corporativo-azul);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            border-bottom: 3px solid var(--color-corporativo-verde);
        }

        /* Modal Styles */
        .modal-expediente-header {
            background-color: var(--color-corporativo-azul);
            color: white;
        }
        .doc-card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .doc-card:hover { transform: scale(1.02); }
        .doc-title-sec {
            color: var(--color-corporativo-verde);
            font-size: 0.8rem;
            font-weight: 800;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

<div class="planear-page-scroll p-4">
    <div class="page-wrap container-fluid">

        <div class="row mb-4">
            <div class="col-12">
                <div class="planear-hero p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 class="mb-1 text-white"><i class="fa-solid fa-user-check me-2"></i> Ingreso de Personal</h4>
                            <div class="opacity-75">Gestión Humana · Expedientes y Vinculaciones</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 align-items-center mb-4">
            <div class="col-auto">
                <button type="button" class="btn btn-crear-corporativo rounded-pill shadow-sm" onclick="window.location.href='Crearvinculacion.php'">
                    <i class="fa-solid fa-plus me-2"></i> CREAR INGRESO
                </button>
            </div>
            <div class="col">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre, cédula o cargo...">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-light border" id="resetBtn">Limpiar</button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle table-vinculacion">
                    <thead>
                        <tr>
                            <th class="ps-4">Cód.</th>
                            <th>Información del Trabajador</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                            <th class="text-center">Expediente</th>
                        </tr>
                    </thead>
                    <tbody id="vinculacionBody">
                        <tr>
                            <td class="ps-4 fw-bold text-muted">001</td>
                            <td>
                                <div class="fw-bold text-dark">ANDRÉS GIOVANNY ALEGRÍA</div>
                                <div class="small text-muted">CC 1.144.123.456 · Ingeniero de Sistemas</div>
                            </td>
                            <td><span class="badge bg-success px-3 rounded-pill">Vinculado</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-actualizar px-3 rounded-pill">Actualizar</button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-link text-primary p-0" onclick="verExpediente('ANDRÉS GIOVANNY ALEGRÍA')">
                                    <img src="../../../assets/img/icons/folder.png" alt="doc" width="25" onerror="this.src='https://cdn-icons-png.flaticon.com/512/716/716784.png'">
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExpediente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: var(--color-corporativo-azul);">
                <h5 class="modal-title">
                    <i class="fa-solid fa-folder-tree me-2"></i> Expediente Digital: <span id="nombreTrabajadorModal" class="fw-light"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <div class="row g-3">
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card doc-card h-100 p-3 shadow-sm">
                            <div class="doc-title-sec">IDENTIDAD Y HOJA DE VIDA</div>
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fa-solid fa-file-pdf text-danger me-1"></i> Cédula (CC)</span>
                                    <button class="btn btn-sm btn-light border"><i class="fa-solid fa-eye"></i></button>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><i class="fa-solid fa-file-pdf text-danger me-1"></i> Hoja de Vida (HV)</span>
                                    <button class="btn btn-sm btn-light border"><i class="fa-solid fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card doc-card h-100 p-3 shadow-sm">
                            <div class="doc-title-sec">AUTORIZACIONES Y CONTRATOS</div>
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Contrato / Otro Sí</span>
                                    <button class="btn btn-sm btn-link p-0 text-primary">Ver doc.</button>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Actas Entrega Equipos</span>
                                    <button class="btn btn-sm btn-link p-0 text-primary">Ver doc.</button>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Cambio de Cargos</span>
                                    <span class="badge bg-light text-dark border">Pendiente</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-2">
                        <div class="card doc-card h-100 p-3 shadow-sm">
                            <div class="doc-title-sec">SEGURIDAD SOCIAL Y SALUD</div>
                            <div class="small mb-2">
                                <p class="mb-1 text-xs"><i class="fa-solid fa-circle-check text-success me-1"></i> EPS / Pensión / ARL</p>
                            </div>
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                    <span>Incapacidades</span>
                                    <span class="badge bg-success-subtle text-success">3 Cargadas</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                    <span class="text-muted">Manual EPS</span>
                                    <button class="btn btn-sm p-0"><i class="fa-solid fa-download"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-2">
                        <div class="card doc-card h-100 p-3 shadow-sm">
                            <div class="doc-title-sec">OPERACIONES Y DESARROLLO</div>
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item px-0 py-1 border-0">
                                    <div class="fw-bold text-xs">Dotación e Inventario</div>
                                    <button class="btn btn-sm btn-link p-0 text-xs">Ver registros</button>
                                </div>
                                <div class="list-group-item px-0 py-1 border-0">
                                    <div class="fw-bold text-xs">Certificaciones Lab.</div>
                                    <button class="btn btn-sm btn-link p-0 text-xs">Generar</button>
                                </div>
                                <div class="list-group-item px-0 py-1 border-0">
                                    <div class="fw-bold text-xs text-danger">Llamados Atención</div>
                                    <button class="btn btn-sm btn-link p-0 text-danger text-xs">Ver (0)</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-2">
                        <div class="card doc-card h-100 p-3 shadow-sm">
                            <div class="doc-title-sec">CONSULTAS</div>
                            <div class="d-grid gap-1">
                                <button class="btn btn-xs btn-outline-dark text-start p-1" style="font-size: 0.65rem;"><i class="fa-solid fa-shield-halved me-1"></i> Policía</button>
                                <button class="btn btn-xs btn-outline-dark text-start p-1" style="font-size: 0.65rem;"><i class="fa-solid fa-scale-balanced me-1"></i> Procuraduría</button>
                            </div>
                        </div>
                    </div>

                </div> <div class="gestion-salida-banner d-flex justify-content-between align-items-center shadow-sm">
                    <div>
                        <h6 class="mb-0 text-secondary"><i class="fa-solid fa-door-open me-2"></i> Gestión de Salida</h6>
                        <small class="text-muted">Documentación para cierre de ciclo (Solo para Retirados)</small>
                    </div>
                    <div class="d-flex gap-3">
                        <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-file-signature me-1"></i> Renuncia</button>
                        <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-file-contract me-1"></i> Liq. Contrato</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-crear-corporativo px-4 rounded-pill shadow-sm"><i class="fa-solid fa-file-arrow-down me-1"></i> Descargar Todo (.zip)</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Buscador en tiempo real
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#vinculacionBody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Abrir Modal de Expediente
    function verExpediente(nombre) {
        document.getElementById('nombreTrabajadorModal').innerText = nombre;
        const modal = new bootstrap.Modal(document.getElementById('modalExpediente'));
        modal.show();
    }

    // Reset
    document.getElementById('resetBtn').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        location.reload();
    });
</script>

</body>
</html>