<?php
session_start();
require_once '../../includes/ConexionAPI.php'; 

// 1. VALIDACIÓN DE SESIÓN BÁSICA
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$rolSesion = $_SESSION["rol"] ?? '';
$perfilIdSesion = $_SESSION["id_perfil"] ?? 0;
$mensaje = "";
$idEmpresaSesion = $_SESSION["id_empresa"] ?? null;

// --- CARGA DE MATRIZ DE PERMISOS PARA EL USUARIO ACTUAL ---
$misPermisos = [];
if ($rolSesion !== "Master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    $datosFinales = isset($resPermisos['data']) ? $resPermisos['data'] : $resPermisos;

    if (is_array($datosFinales)) {
        foreach ($datosFinales as $perm) {
            if (isset($perm['id_modulo'])) {
                $idM = (int)$perm['id_modulo'];
                $misPermisos[$idM] = [
                    'ver'      => (int)($perm['ver'] ?? 0),
                    'crear'    => (int)($perm['crear'] ?? 0),
                    'editar'   => (int)($perm['editar'] ?? 0),
                    'eliminar' => (int)($perm['eliminar'] ?? 0)
                ];
            }
        }
    }
}

// Funciones de validación de seguridad
function puedeVer($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true; 
    return isset($permisos[(int)$idModulo]) && ($permisos[(int)$idModulo]['ver'] == 1);
}

function puedeCrear($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    return isset($permisos[(int)$idModulo]) && (int)($permisos[(int)$idModulo]['crear'] ?? 0) === 1;
}

function puedeEditar($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    return isset($permisos[(int)$idModulo]) && (int)($permisos[(int)$idModulo]['editar'] ?? 0) === 1;
}

// DEFINICIÓN DE IDS DE MÓDULO SEGÚN REQUERIMIENTO
$MOD_PERFILES = 3;
$MOD_PERMISOS = 14;

// 2. PROCESAR FORMULARIO DE PERFIL (POST/PUT - Módulo 3)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nombre_perfil"])) {
    $id = $_POST["id_perfil"] ?? "";
    
    // Garantizar que se asigne el id_empresa correctamente
    $idEmpGuardar = ($rolSesion === "Master" && empty($idEmpresaSesion)) ? null : (int)$idEmpresaSesion;

    $datos = [
        "nombre_perfil" => trim($_POST["nombre_perfil"]),
        "descripcion"   => trim($_POST["descripcion"] ?? ""),
        "estado"        => isset($_POST["status"]) ? 1 : 0,
        "id_empresa"    => $idEmpGuardar 
    ];

    $endpoint = "index.php?table=perfiles" . (!empty($id) ? "&id=$id" : "");
    $metodo = !empty($id) ? "PUT" : "POST";
    
    if(!empty($id)) unset($datos["id_empresa"]); 

    $resultado = $api->solicitar($endpoint, $metodo, $datos, $token);

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        header("Location: perfil.php");
        exit;
    } else {
        $mensaje = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 3. CARGA DE DATOS PARA LA TABLA Y MODAL
$urlPerfiles = "index.php?table=perfiles";

if ($rolSesion !== "Master") {
    $urlPerfiles .= "&id_empresa=" . $idEmpresaSesion;
}

$resPerfiles = $api->solicitar($urlPerfiles, "GET", null, $token);
$listaPerfiles = (isset($resPerfiles['status']) && $resPerfiles['status'] == 200) ? $resPerfiles['data'] : [];

$resModulos = $api->solicitar("index.php?table=modulos", "GET", null, $token);
$listaModulosMaestra = (isset($resModulos['status']) && $resModulos['status'] == 200) ? $resModulos['data'] : [];

// --- DICCIONARIO DE EMPRESAS Y PLANES ---
$modulosPlanIds = [];
$mapaEmpresas = []; 

if (!empty($idEmpresaSesion)) {
    // A. Consultar empresas para armar el mapa
    $resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
    $todasLasEmpresas = (isset($resEmpresas['status']) && $resEmpresas['status'] == 200) ? ($resEmpresas['data'] ?? []) : [];
    $idPlanEmpresa = 0;
    
    foreach ($todasLasEmpresas as $emp) {
        $mapaEmpresas[$emp['id_empresa']] = $emp['nombre_empresa'];
        if (isset($emp['id_empresa']) && $emp['id_empresa'] == $idEmpresaSesion) {
            $idPlanEmpresa = (int)($emp['id_plan'] ?? 0);
        }
    }

    // B. Filtrar los perfiles
    $listaPerfiles = array_filter($listaPerfiles, function($p) use ($idEmpresaSesion) {
        return isset($p['id_empresa']) && $p['id_empresa'] == $idEmpresaSesion;
    });

    // C. Extraer los IDs de los módulos permitidos por ese plan
    if ($idPlanEmpresa > 0) {
        $resPlan = $api->solicitar("planes/permisos/$idPlanEmpresa", "GET", null, $token);
        $datosPlan = $resPlan['data'] ?? $resPlan;
        if (is_array($datosPlan)) {
            foreach ($datosPlan as $p) {
                $modulosPlanIds[] = (int)$p['id_modulo'];
            }
        }
    }

    // D. Filtrar la lista maestra de módulos para el modal
    if (!empty($modulosPlanIds)) {
        $listaModulosMaestra = array_filter($listaModulosMaestra, function($m) use ($modulosPlanIds) {
            return in_array((int)$m['id_modulo'], $modulosPlanIds);
        });
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión de Perfiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Ajuste de Switch interno si no está en tu main-style.css */
        .switch { position: relative; display: inline-block; width: 44px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #1fa339; }
        input:checked + .slider:before { transform: translateX(22px); }
    </style>
    <script>
        const PER_PUEDE_CREAR  = <?= puedeCrear($MOD_PERFILES, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const PER_PUEDE_EDITAR = <?= puedeEditar($MOD_PERFILES, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const ACC_PUEDE_VER    = <?= puedeVer($MOD_PERMISOS, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const ACC_PUEDE_CREAR  = <?= puedeCrear($MOD_PERMISOS, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
    </script>
</head> 
<body class="bg-light">

    <div class="sticky-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a href="../../pages-empresa/bienvenidaes.php" class="btn-volver-sst me-3">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver
                </a>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-user-shield text-primary me-2"></i>Gestión de Perfiles y Accesos
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        
        <?php if($mensaje): echo "<div class='alert alert-danger'>$mensaje</div>"; endif; ?>

        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form method="POST" id="formPerfil">
                    <input type="hidden" id="id_perfil" name="id_perfil">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="fw-bold small mb-1 text-muted text-uppercase">Nombre del Perfil</label>
                            <input type="text" id="nombre_perfil" name="nombre_perfil" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small mb-1 text-muted text-uppercase">Descripción</label>
                            <input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 text-center">
                            <label class="fw-bold small d-block mb-1 text-muted text-uppercase">Estado</label>
                            <label class="switch mt-1">
                                <input type="checkbox" id="status" name="status" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" id="btnGuardar" class="btn btn-primary flex-grow-1" style="background-color: #004176; border: none; border-radius: 8px;">
                                <i class="fa-solid fa-save me-2"></i>Guardar
                            </button>
                            <button type="button" id="btnLimpiar" class="btn btn-secondary" onclick="limpiarFormulario()" style="display:none; border-radius: 8px;">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-5" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #e4e8ee; color: #333;" class="text-uppercase small fw-bold">
                        <tr>
                            <th width="80" class="ps-4 py-3">ID</th>
                            <th class="py-3">Nombre del Perfil</th>
                            <th class="py-3">Empresa</th>
                            <th class="text-center py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaPerfiles as $p): ?>
                        <tr>
                            <td class="ps-4 text-muted">#<?= $p['id_perfil'] ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($p['nombre_perfil']) ?></td>
                            <td class="small text-muted">
                                <?= (isset($p['id_empresa']) && isset($mapaEmpresas[$p['id_empresa']])) ? htmlspecialchars($mapaEmpresas[$p['id_empresa']]) : 'Sistema (Global)' ?>
                            </td>
                            <td class="text-center">
                                <?php if (puedeVer($MOD_PERMISOS, $rolSesion, $misPermisos)): ?>
                                    <button class="btn btn-sm text-primary" style="background-color: #e6f0fa; border-radius: 8px;"
                                            onclick="abrirModalPermisos('<?= $p['id_perfil'] ?>', '<?= htmlspecialchars($p['nombre_perfil']) ?>')" title="Permisos">
                                        <i class="fa-solid fa-lock"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if (puedeEditar($MOD_PERFILES, $rolSesion, $misPermisos)): ?>
                                    <button class="btn btn-sm text-warning ms-1" style="background-color: #fff8e1; border-radius: 8px;"
                                            onclick="cargarPerfil('<?= base64_encode(json_encode($p)) ?>')" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPermisos" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header" style="background-color: #004176; color: white;">
                    <h5 class="modal-title" id="tituloModal">Configuración de Accesos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <input type="hidden" id="perm_id_perfil">
                    <table class="table table-sm mb-0">
                        <thead class="text-center small fw-bold" style="background-color: #f4f7f6;">
                            <tr>
                                <th class="text-start p-3">MÓDULO / FUNCIÓN</th>
                                <th width="90" class="py-3">VER</th>
                                <th width="90" class="py-3">CREAR</th>
                                <th width="90" class="py-3">EDITAR</th>
                                <th width="90" class="py-3">ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $padres = array_filter($listaModulosMaestra, fn($m) => empty($m['id_padre']));
                            $hijos = array_filter($listaModulosMaestra, fn($m) => !empty($m['id_padre']));
                            foreach ($padres as $pad): 
                            ?>
                                <tr style="background-color: #e4e8ee;"><td colspan="5" class="ps-4 fw-bold py-2 text-dark"><?= $pad['nombre_modulo'] ?></td></tr>
                                <?php foreach ($hijos as $hij): if ($hij['id_padre'] == $pad['id_modulo']): ?>
                                   <tr>
                                    <td class="ps-5 py-2 text-muted"><?= $hij['nombre_modulo'] ?></td>
                                    <?php foreach (['ver', 'crear', 'editar', 'eliminar'] as $a): ?>
                                        <td class="text-center align-middle">
                                            <label class="switch" style="transform: scale(0.8);">
                                                <input type="checkbox" class="check-perm" data-modulo="<?= $hij['id_modulo'] ?>" data-padre="<?= $pad['id_modulo'] ?>" data-perm="<?= $a ?>">
                                                <span class="slider"></span>
                                            </label>
                                        </td>
                                    <?php endforeach; ?>
                                   </tr>
                                <?php endif; endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-success px-4 rounded-pill" id="btnSincronizar" onclick="guardarPermisos()">
                        <i class="fa-solid fa-sync me-2"></i>Guardar Permisos
                    </button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const API_URL = "/sstmanager-backend/public/index.php"; 
    const modalPerm = new bootstrap.Modal(document.getElementById('modalPermisos'));

    document.addEventListener("DOMContentLoaded", function() {
        if (!PER_PUEDE_CREAR) document.getElementById('btnGuardar').style.display = 'none';
    });

    window.cargarPerfil = function(base64) {
        const d = JSON.parse(atob(base64));
        const btn = document.getElementById('btnGuardar');
        document.getElementById('id_perfil').value = d.id_perfil;
        document.getElementById('nombre_perfil').value = d.nombre_perfil;
        document.getElementById('descripcion').value = d.descripcion || "";
        document.getElementById('status').checked = (parseInt(d.estado) === 1);
        
        btn.innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar';
        btn.style.display = PER_PUEDE_EDITAR ? 'block' : 'none';
        document.getElementById('btnLimpiar').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    window.limpiarFormulario = function() {
        const btn = document.getElementById('btnGuardar');
        document.getElementById('formPerfil').reset();
        document.getElementById('id_perfil').value = "";
        btn.innerHTML = '<i class="fa-solid fa-save me-2"></i>Guardar';
        btn.style.display = PER_PUEDE_CREAR ? 'block' : 'none';
        document.getElementById('btnLimpiar').style.display = 'none';
    };

    window.abrirModalPermisos = function(id, nombre) {
        if (!ACC_PUEDE_VER) return;
        document.getElementById('perm_id_perfil').value = id;
        document.getElementById('tituloModal').innerHTML = `Accesos para: <strong>${nombre}</strong>`;
        
        document.getElementById('btnSincronizar').style.display = ACC_PUEDE_CREAR ? 'block' : 'none';

        fetch(`${API_URL}?table=perfiles&action=permisos&id=${id}`, {
            headers: { 'Authorization': 'Bearer <?= $token ?>' }
        })
        .then(res => res.json())
        .then(data => {
            const list = data.data || data;
            document.querySelectorAll('.check-perm').forEach(c => {
                c.checked = false;
                c.disabled = !ACC_PUEDE_CREAR; 
            });
            if(Array.isArray(list)) {
                list.forEach(p => {
                    ['ver', 'crear', 'editar', 'eliminar'].forEach(acc => {
                        const ck = document.querySelector(`[data-modulo="${p.id_modulo}"][data-perm="${acc}"]`);
                        if(ck) ck.checked = (parseInt(p[acc]) === 1);
                    });
                });
            }
            modalPerm.show();
        });
    };

    window.guardarPermisos = function() {
        if (!ACC_PUEDE_CREAR) return;
        const idPerfil = document.getElementById('perm_id_perfil').value;
        const matrix = [];
        const padresAActivar = new Set();
        const modulosIds = [...new Set([...document.querySelectorAll('.check-perm')].map(c => c.dataset.modulo))];

        modulosIds.forEach(mId => {
            const rowChecks = document.querySelectorAll(`[data-modulo="${mId}"]`);
            let item = { "id_modulo": parseInt(mId), "ver": 0, "crear": 0, "editar": 0, "eliminar": 0 };
            let marked = false;
            rowChecks.forEach(ck => {
                if(ck.checked) {
                    item[ck.dataset.perm] = 1; marked = true;
                    if(ck.dataset.padre) padresAActivar.add(parseInt(ck.dataset.padre));
                }
            });
            if(marked) matrix.push(item);
        });

        padresAActivar.forEach(pId => {
            let p = matrix.find(m => m.id_modulo === pId);
            if(!p) matrix.push({ "id_modulo": pId, "ver": 1, "crear": 0, "editar": 0, "eliminar": 0 });
            else p.ver = 1;
        });

        fetch(`${API_URL}?table=perfiles&action=permisos&id=${idPerfil}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
            body: JSON.stringify(matrix)
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 200 || res.ok) {
                Swal.fire({ title: 'Éxito', text: 'Permisos sincronizados', icon: 'success', confirmButtonColor: '#1fa339' }).then(() => modalPerm.hide());
            }
        });
    }; 
</script>
</body>
</html>