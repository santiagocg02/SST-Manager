<?php
session_start();
require_once '../../includes/ConexionAPI.php'; 

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$mensaje_error = "";

// 1. LÓGICA DE PROCESAMIENTO DEL PLAN (POST/PUT)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nombre_plan"])) { 
    $id = $_POST["id_plan"] ?? "";
    $datos = [
        "nombre_plan"     => trim($_POST["nombre_plan"]),
        "descripcion"     => trim($_POST["descripcion"] ?? ""),
        "limite_usuarios" => (int)$_POST["limite_usuarios"],
        "precio_mensual"  => (float)$_POST["precio_mensual"],
        "estado"          => isset($_POST["status"]) ? 1 : 0
    ];

    $endpoint = "index.php?table=planes" . (!empty($id) ? "&id=$id" : ""); 
    $metodo = !empty($id) ? "PUT" : "POST";
    
    $resultado = $api->solicitar($endpoint, $metodo, $datos, $token);

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        // --- NUEVO: GUARDAR MENSAJE EN SESIÓN ---
        $_SESSION['alerta_exito'] = !empty($id) ? "El plan ha sido actualizado correctamente." : "El plan ha sido creado con éxito.";
        header("Location: planes.php");
        exit;
    } else {
        $mensaje_error = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 2. CARGA DE DATOS INICIALES
$resPlanes = $api->solicitar("index.php?table=planes", "GET", null, $token); 
$listaPlanes = (isset($resPlanes['status']) && $resPlanes['status'] == 200) ? $resPlanes['data'] : [];

$resModulos = $api->solicitar("index.php?table=modulos", "GET", null, $token);
$listaModulosMaestra = (isset($resModulos['status']) && $resModulos['status'] == 200) ? $resModulos['data'] : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SST Manager - Gestión de Planes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-layer-group me-2" style="color: var(--primary-blue);"></i>Gestión de Planes</h2>

    <?php if($mensaje_error) echo "<div class='alert alert-danger'>$mensaje_error</div>"; ?>

    <div class="card card-shadow mb-4">
        <div class="card-body">
            <form method="POST" id="formPlan">
                <input type="hidden" id="id_plan" name="id_plan">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1 text-uppercase">Nombre del Plan</label>
                        <input type="text" id="nombre_plan" name="nombre_plan" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1 text-uppercase">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1 text-uppercase">Límite Usuarios</label>
                        <input type="number" id="limite_usuarios" name="limite_usuarios" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1 text-uppercase">Precio Mensual</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" id="precio_mensual" name="precio_mensual" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="fw-bold small d-block mb-1 text-uppercase">Estado</label>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <label class="switch">
                                <input type="checkbox" id="status" name="status" checked>
                                <span class="slider"></span>
                            </label>
                            <span id="status-label" class="status-label-active small fw-bold text-success">Activo</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" id="btnGuardar" class="btn btn-success w-100 shadow-sm">
                            <i class="fa-solid fa-save me-2"></i>Guardar Plan
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
                    <th width="60" class="ps-3">ID</th>
                    <th>Plan</th>
                    <th>Descripción</th>
                    <th class="text-center">Usuarios</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaPlanes as $p): ?>
                <tr>
                    <td class="ps-3"><?= $p['id_plan'] ?></td>
                    <td class="fw-bold" style="color: var(--primary-blue);"><?= htmlspecialchars($p['nombre_plan']) ?></td>
                    <td><small class="text-muted"><?= htmlspecialchars($p['descripcion'] ?? '') ?></small></td>
                    <td class="text-center"><?= $p['limite_usuarios'] ?></td>
                    <td class="text-end fw-bold">$<?= number_format($p['precio_mensual'], 2) ?></td>
                    <td class="text-center">
                        <span class="badge rounded-pill <?= ($p['estado'] == 1) ? 'bg-success' : 'bg-danger' ?>">
                            <?= ($p['estado'] == 1) ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="abrirModalPermisos('<?= $p['id_plan'] ?>', '<?= htmlspecialchars($p['nombre_plan']) ?>')">
                            <i class="fa-solid fa-lock me-1"></i> Accesos
                        </button>
                        <button class="btn btn-sm btn-light border ms-1" onclick="cargarPlan('<?= base64_encode(json_encode($p)) ?>')">
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
<div class="modal fade" id="modalPermisos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-custom shadow-sm">
                <h5 class="modal-title" id="tituloModal">Configuración de Accesos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <input type="hidden" id="perm_id_plan">
                <table class="table table-sm table-bordered mb-0 table-permissions">
                    <thead class="text-center bg-light small fw-bold">
                        <tr>
                            <th class="text-start p-3">MÓDULO / FUNCIÓN</th>
                            <th width="100">ACCESO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $padres = array_filter($listaModulosMaestra, fn($m) => empty($m['id_padre']));
                        $hijos = array_filter($listaModulosMaestra, fn($m) => !empty($m['id_padre']));
                        foreach ($padres as $pad):  
                        ?>
                            <tr class="row-modulo-padre table-light">
                                <td colspan="2" class="p-2 ps-3 fw-bold text-uppercase text-primary">
                                    <i class="<?= $pad['icono'] ?? 'fa-solid fa-folder' ?> me-2"></i>
                                    <?= htmlspecialchars($pad['nombre_modulo']) ?>
                                </td>
                            </tr>
                            <?php foreach ($hijos as $hij): if ($hij['id_padre'] == $pad['id_modulo']): ?>
                               <tr>
                                <td class="indent-hijo py-2 ps-5 border-start"><?= htmlspecialchars($hij['nombre_modulo']) ?></td>
                                <td class="text-center bg-white">
                                    <label class="switch">
                                        <input type="checkbox" class="check-perm" 
                                               data-modulo="<?= $hij['id_modulo'] ?>" 
                                               data-padre="<?= $pad['id_modulo'] ?>" 
                                               data-perm="ver">
                                        <span class="slider"></span>
                                    </label>
                                </td>
                            </tr>
                            <?php endif; endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success px-4 shadow-sm" onclick="guardarPermisos()">
                    <i class="fa-solid fa-sync me-2"></i>Sincronizar Accesos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const API_URL = "/sstmanager-backend/public/index.php"; 
    const modalPerm = new bootstrap.Modal(document.getElementById('modalPermisos'));

    // CARGAR PLAN EN EL FORMULARIO
    window.cargarPlan = function(base64) {
        try {
            const d = JSON.parse(atob(base64));
            document.getElementById('id_plan').value = d.id_plan;
            document.getElementById('nombre_plan').value = d.nombre_plan;
            document.getElementById('descripcion').value = d.descripcion || "";
            document.getElementById('limite_usuarios').value = d.limite_usuarios;
            document.getElementById('precio_mensual').value = d.precio_mensual;
            
            const isActive = (parseInt(d.estado) === 1);
            document.getElementById('status').checked = isActive;
            document.getElementById('status-label').textContent = isActive ? "Activo" : "Inactivo";
            document.getElementById('status-label').className = isActive ? "small fw-bold text-success" : "small fw-bold text-danger";
            
            document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar Plan';
            document.getElementById('btnGuardar').classList.replace('btn-success', 'btn-warning');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) { console.error("Error cargando plan:", e); }
    };

    // ABRIR MODAL Y CARGAR ACCESOS (GET)
    window.abrirModalPermisos = function(id, nombre) {
        document.getElementById('perm_id_plan').value = id;
        document.getElementById('tituloModal').innerHTML = `Accesos para Plan: <span class="text-primary">${nombre}</span>`;
        document.querySelectorAll('.check-perm').forEach(c => c.checked = false);

        fetch(`${API_URL}?table=planes&action=permisos&id=${id}`, {
            headers: { 'Authorization': 'Bearer <?= $token ?>' }
        })
        .then(res => res.json())
        .then(data => {
            if(Array.isArray(data)) {
                data.forEach(p => {
                    const ck = document.querySelector(`[data-modulo="${p.id_modulo}"][data-perm="ver"]`);
                    if(ck) ck.checked = (parseInt(p.ver) === 1);
                });
            }
            modalPerm.show();
        })
        .catch(err => {
            console.error("Error al obtener accesos:", err);
            modalPerm.show(); 
        });
    };

    // GUARDAR (SINCRONIZAR) ACCESOS (POST)
    window.guardarPermisos = function() {
        const idPlan = document.getElementById('perm_id_plan').value;
        const matrix = [];
        const padresAActivar = new Set();

        document.querySelectorAll('.check-perm:checked').forEach(ck => {
            const mId = parseInt(ck.dataset.modulo);
            matrix.push({ "id_modulo": mId, "ver": 1 });
            if(ck.dataset.padre) padresAActivar.add(parseInt(ck.dataset.padre));
        });

        padresAActivar.forEach(pId => {
            if(!matrix.find(m => m.id_modulo === pId)) matrix.push({ "id_modulo": pId, "ver": 1 });
        });

        fetch(`${API_URL}?table=planes&action=permisos&id=${idPlan}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Authorization': 'Bearer <?= $token ?>' 
            },
            body: JSON.stringify(matrix)
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 200 || res.ok) {
                Swal.fire({ 
                    title: '¡Sincronizado!', 
                    text: res.mensaje || 'Accesos actualizados', 
                    icon: 'success', 
                    confirmButtonColor: '#0b4f7a' 
                }).then(() => modalPerm.hide());
            } else { 
                Swal.fire('Error', res.error || 'No se pudo guardar', 'error'); 
            }
        })
        .catch(err => Swal.fire('Error', 'Fallo de conexión con la API', 'error'));
    };

    document.getElementById("status").addEventListener('change', function() {
        const label = document.getElementById("status-label");
        label.textContent = this.checked ? "Activo" : "Inactivo";
        label.className = this.checked ? "small fw-bold text-success" : "small fw-bold text-danger";
    });
</script>

<?php if (isset($_SESSION['alerta_exito'])): ?>
<script>
    Swal.fire({
        title: '¡Excelente!',
        text: '<?= $_SESSION['alerta_exito'] ?>',
        icon: 'success',
        confirmButtonColor: '#0b4f7a',
        timer: 3000
    });
</script>
<?php unset($_SESSION['alerta_exito']); ?>
<?php endif; ?>

</body>
</html>