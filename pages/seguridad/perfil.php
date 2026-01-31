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
    $datos = [
        "nombre_perfil" => trim($_POST["nombre_perfil"]),
        "descripcion"   => trim($_POST["descripcion"] ?? ""),
        "estado"        => isset($_POST["status"]) ? 1 : 0
    ];

    $endpoint = "index.php?table=perfiles" . (!empty($id) ? "&id=$id" : "");
    $metodo = !empty($id) ? "PUT" : "POST";
    $resultado = $api->solicitar($endpoint, $metodo, $datos, $token);

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        header("Location: perfil.php");
        exit;
    } else {
        $mensaje = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 3. CARGA DE DATOS PARA LA TABLA Y MODAL
$resPerfiles = $api->solicitar("index.php?table=perfiles", "GET", null, $token);
$listaPerfiles = (isset($resPerfiles['status']) && $resPerfiles['status'] == 200) ? $resPerfiles['data'] : [];

$resModulos = $api->solicitar("index.php?table=modulos", "GET", null, $token);
$listaModulosMaestra = (isset($resModulos['status']) && $resModulos['status'] == 200) ? $resModulos['data'] : [];
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
    <script>
        // PERMISOS PASADOS A JS
        const PER_PUEDE_CREAR  = <?= puedeCrear($MOD_PERFILES, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const PER_PUEDE_EDITAR = <?= puedeEditar($MOD_PERFILES, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const ACC_PUEDE_VER    = <?= puedeVer($MOD_PERMISOS, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const ACC_PUEDE_CREAR  = <?= puedeCrear($MOD_PERMISOS, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
    </script>
</head> 
<body class="cal-wrap">

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-user-shield me-2"></i>Gestión de Perfiles y Accesos</h2>

    <?php if($mensaje): echo "<div class='alert alert-danger'>$mensaje</div>"; endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="POST" id="formPerfil">
                <input type="hidden" id="id_perfil" name="id_perfil">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1 text-uppercase">Nombre del Perfil</label>
                        <input type="text" id="nombre_perfil" name="nombre_perfil" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold small mb-1 text-uppercase">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control">
                    </div>
                    <div class="col-md-2 text-center">
                        <label class="fw-bold small d-block mb-1 text-uppercase">Estado</label>
                        <label class="switch mt-1">
                            <input type="checkbox" id="status" name="status" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" id="btnGuardar" class="btn btn-success flex-grow-1">
                            <i class="fa-solid fa-save me-2"></i>Guardar
                        </button>
                        <button type="button" id="btnLimpiar" class="btn btn-secondary" onclick="limpiarFormulario()" style="display:none;">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-shadow border overflow-hidden">
        <div class="table-scroll-container">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th width="80" class="ps-3">ID</th>
                        <th>Nombre del Perfil</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaPerfiles as $p): ?>
                    <tr>
                        <td class="ps-3"><?= $p['id_perfil'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($p['nombre_perfil']) ?></td>
                        <td class="text-center">
                            <?php if (puedeVer($MOD_PERMISOS, $rolSesion, $misPermisos)): ?>
                                <button class="btn btn-sm btn-primary px-3" 
                                        onclick="abrirModalPermisos('<?= $p['id_perfil'] ?>', '<?= htmlspecialchars($p['nombre_perfil']) ?>')">
                                    <i class="fa-solid fa-lock me-1"></i> Permisos
                                </button>
                            <?php endif; ?>

                            <?php if (puedeEditar($MOD_PERFILES, $rolSesion, $misPermisos)): ?>
                                <button class="btn btn-sm btn-light border ms-1" 
                                        onclick="cargarPerfil('<?= base64_encode(json_encode($p)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
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
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloModal">Configuración de Accesos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <input type="hidden" id="perm_id_perfil">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="text-center table-light small fw-bold">
                        <tr>
                            <th class="text-start p-3">MÓDULO / FUNCIÓN</th>
                            <th width="90">VER</th><th width="90">CREAR</th><th width="90">EDITAR</th><th width="90">ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $padres = array_filter($listaModulosMaestra, fn($m) => empty($m['id_padre']));
                        $hijos = array_filter($listaModulosMaestra, fn($m) => !empty($m['id_padre']));
                        foreach ($padres as $pad): 
                        ?>
                            <tr class="table-secondary fw-bold"><td colspan="5" class="ps-3"><?= $pad['nombre_modulo'] ?></td></tr>
                            <?php foreach ($hijos as $hij): if ($hij['id_padre'] == $pad['id_modulo']): ?>
                               <tr>
                                <td class="ps-5 py-2"><?= $hij['nombre_modulo'] ?></td>
                                <?php foreach (['ver', 'crear', 'editar', 'eliminar'] as $a): ?>
                                    <td class="text-center">
                                        <label class="switch">
                                            <input type="checkbox" class="check-perm" data-modulo="<?= $hij['id_modulo'] ?>" data-padre="<?= $pad['id_modulo'] ?>" data-perm="<?= $a ?>">
                                            <span class="slider round"></span>
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
                <button type="button" class="btn btn-success px-4" id="btnSincronizar" onclick="guardarPermisos()">
                    <i class="fa-solid fa-sync me-2"></i>Sincronizar Permisos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const API_URL = "/sstmanager-backend/public/index.php"; 
    const modalPerm = new bootstrap.Modal(document.getElementById('modalPermisos'));

    // Validación inicial de creación de perfiles
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
        
        // Controlar botón Sincronizar y checkboxes
        document.getElementById('btnSincronizar').style.display = ACC_PUEDE_CREAR ? 'block' : 'none';

        fetch(`${API_URL}?table=perfiles&action=permisos&id=${id}`, {
            headers: { 'Authorization': 'Bearer <?= $token ?>' }
        })
        .then(res => res.json())
        .then(data => {
            const list = data.data || data;
            document.querySelectorAll('.check-perm').forEach(c => {
                c.checked = false;
                c.disabled = !ACC_PUEDE_CREAR; // Bloquea si no puede sincronizar
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
                Swal.fire({ title: 'Éxito', text: 'Permisos sincronizados', icon: 'success' }).then(() => modalPerm.hide());
            }
        });
    }; 
</script>
</body>
</html>