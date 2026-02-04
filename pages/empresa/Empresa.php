<?php
session_start();
require_once '../../includes/ConexionAPI.php'; 

// 1. VALIDACIÓN DE SESIÓN (Simulada para el mockup, usa la real en producción)
$token = $_SESSION["token"] ?? 'mock_token';
$rolSesion = $_SESSION["rol"] ?? 'Admin';
$perfilIdSesion = $_SESSION["id_perfil"] ?? 1;

// Mock de permisos para que el mockup sea funcional
$misPermisos = [
    13 => ['ver' => 1, 'crear' => 1, 'editar' => 1, 'eliminar' => 1]
];

function puedeVer($idModulo, $rol, $permisos) { return true; }
function puedeCrear($idModulo, $rol, $permisos) { return true; }
function puedeEditar($idModulo, $rol, $permisos) { return true; }

$MOD_EMPRESA = 13;

// DATOS QUEMADOS PARA EL MOCKUP
$listaEmpresas = [
    [
        "id_empresa" => 101, 
        "nombre_empresa" => "CONSTRUCTORA BOLÍVAR S.A.", 
        "nit" => "860.003.123-5", 
        "direccion" => "Av. El Dorado #68-10", 
        "telefono" => "601 400 5000", 
        "email" => "contacto@cbolivar.com.co",
        "representante" => "Carlos Alberto Pérez",
        "doc_rl" => "79.456.123"
    ],
    [
        "id_empresa" => 102, 
        "nombre_empresa" => "SERVICIOS LOGÍSTICOS S.A.S", 
        "nit" => "900.555.888-2", 
        "direccion" => "Zona Franca Bodega 4", 
        "telefono" => "602 333 4455", 
        "email" => "hseq@logistica.com",
        "representante" => "Martha Lucía Gómez",
        "doc_rl" => "52.789.001"
    ]
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Mockup Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <style>
        .section-header { 
            background: #eee; padding: 6px 12px; font-weight: bold; 
            border-left: 4px solid #0d6efd; margin: 15px 0 10px 0;
            text-transform: uppercase; font-size: 0.75rem; 
        }
        .label-custom { font-size: 0.7rem; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 2px; }
        .btn-round { border-radius: 25px; padding: 8px 25px; font-weight: bold; }
        .table-workers th { background: #f8f9fa; font-size: 0.75rem; text-align: center; }
        .btn-sst { background-color: #28a745; color: white; border-radius: 20px; font-weight: 600; font-size: 0.8rem; border: none; padding: 5px 15px; }
        .btn-sst:hover { background-color: #218838; color: white; }
    </style>
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-industry me-2" style="color: #0b4f7a;"></i>Gestión de Empresas</h2>
        <button class="btn btn-success shadow-sm" onclick="abrirModalCrear()">
            <i class="fa-solid fa-plus me-1"></i> Nueva Empresa
        </button>
    </div>

    <div class="card-shadow border overflow-hidden">
        <div class="table-scroll-container">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Empresa / Razón Social</th>
                        <th>NIT</th>
                        <th>Representante</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaEmpresas as $emp): ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $emp['id_empresa'] ?></td>
                        <td class="fw-bold"><?= $emp['nombre_empresa'] ?></td>
                        <td><?= $emp['nit'] ?></td>
                        <td><?= $emp['representante'] ?></td>
                        <td class="text-center">
                            <button class="btn btn-sst" onclick="abrirModalSST('<?= $emp['nombre_empresa'] ?>')">
                                <i class="fa-solid fa-user-shield me-1"></i> Asocie Personal SST
                            </button>
                            <button class="btn btn-sm btn-light border ms-1 shadow-sm" onclick="cargarParaEditar('<?= base64_encode(json_encode($emp)) ?>')">
                                <i class="fa-solid fa-pencil text-warning"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFormEmpresa" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="tituloModalEmpresa">Registrar Empresa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <form id="formEmpresaMaster">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="label-custom">Nombre empresa</label>
                            <input type="text" id="m_nombre" class="form-control form-control-sm" placeholder="Ej: Mi Empresa S.A.S">
                        </div>
                        <div class="col-md-2">
                            <label class="label-custom">T.C</label>
                            <select class="form-select form-select-sm"><option>NIT</option><option>CC</option></select>
                        </div>
                        <div class="col-md-5">
                            <label class="label-custom">Número documento</label>
                            <input type="text" id="m_nit" class="form-control form-control-sm" placeholder="900.000.000-1">
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-4">
                            <label class="label-custom">Dirección</label>
                            <input type="text" id="m_dir" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="label-custom">Teléfono</label>
                            <input type="text" id="m_tel" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="label-custom">Correo / Email</label>
                            <input type="email" id="m_email" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="section-header">Información Representante Legal</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="label-custom">Nombre R.L.</label>
                            <input type="text" id="m_rl" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="label-custom">Documento R.L.</label>
                            <input type="text" id="m_doc_rl" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="section-header">Distribución de Trabajadores</div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-workers">
                            <thead>
                                <tr>
                                    <th>Género</th><th>Directos</th><th>Contratistas</th><th>Aprendices</th><th>Brigadistas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-center">Hombres</td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-center">Mujeres</td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="0"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success btn-round">Guardar Empresa</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSST" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Asociar Personal SST</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <div class="mb-3">
                    <label class="label-custom">Nombre empresa</label>
                    <input type="text" id="sst_display" class="form-control border-0 bg-light" value="DATOS QUEMADOS S.A." readonly>
                </div>
                <div class="mb-2"><label class="label-custom">Proyecto Rige</label><input type="text" class="form-control form-control-sm" value="IMPLEMENTACIÓN ESTÁNDARES MÍNIMOS"></div>
                <div class="mb-2"><label class="label-custom">Profesional SST</label><input type="text" class="form-control form-control-sm" value="JUAN SEBASTIÁN PINEDA"></div>
                <div class="mb-2"><label class="label-custom">Correo SST</label><input type="email" class="form-control form-control-sm" value="profesional.sst@ejemplo.com"></div>
                <div class="mb-2"><label class="label-custom">Teléfono SST</label><input type="text" class="form-control form-control-sm" value="315 888 9900"></div>
                <div class="mb-3"><label class="label-custom">Firma SST</label><input type="text" class="form-control form-control-sm" value="JS_PINEDA_SIGN"></div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center pb-4 gap-2">
                <button type="button" class="btn btn-success btn-round px-4">GUARDAR</button>
                <button type="button" class="btn btn-primary btn-round px-4" style="background: #1a3a5a;" data-bs-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalForm = new bootstrap.Modal(document.getElementById('modalFormEmpresa'));
    const modalSST = new bootstrap.Modal(document.getElementById('modalSST'));

    function abrirModalCrear() {
        document.getElementById('formEmpresaMaster').reset();
        document.getElementById('tituloModalEmpresa').innerText = "Registrar Nueva Empresa";
        modalForm.show();
    }

    function cargarParaEditar(base64) {
        const d = JSON.parse(atob(base64));
        document.getElementById('tituloModalEmpresa').innerText = "Editar Empresa: " + d.nombre_empresa;
        document.getElementById('m_nombre').value = d.nombre_empresa;
        document.getElementById('m_nit').value = d.nit;
        document.getElementById('m_dir').value = d.direccion;
        document.getElementById('m_tel').value = d.telefono;
        document.getElementById('m_email').value = d.email;
        document.getElementById('m_rl').value = d.representante;
        document.getElementById('m_doc_rl').value = d.doc_rl;
        modalForm.show();
    }

    function abrirModalSST(nombre) {
        document.getElementById('sst_display').value = nombre;
        modalSST.show();
    }
</script>
</body>
</html>