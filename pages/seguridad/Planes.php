<?php
session_start();
require_once '../../includes/ConexionAPI.php'; 

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$mensaje = "";

// 2. LÓGICA: Procesar Plan (Creación y Actualización)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nombre_plan"])) {
    $id = $_POST["id_plan"] ?? "";
    $datos = [
        "nombre_plan"     => trim($_POST["nombre_plan"]),
        "descripcion"     => trim($_POST["descripcion"] ?? ""),
        "limite_usuarios" => (int)$_POST["limite_usuarios"],
        "precio_mensual"  => (float)$_POST["precio_mensual"],
        "estado"          => isset($_POST["status"]) ? 1 : 0
    ];

    // Ajuste de endpoint a 'planes' según la imagen
    $endpoint = "index.php?table=planes" . (!empty($id) ? "&id=$id" : "");
    $metodo = !empty($id) ? "PUT" : "POST";
    $resultado = $api->solicitar($endpoint, $metodo, $datos, $token);

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        header("Location: planes.php"); // Cambiar al nombre de este archivo si es necesario
        exit;
    } else {
        $mensaje = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 3. CARGAR DATOS
$resPlanes = $api->solicitar("index.php?table=planes", "GET", null, $token);
$listaPlanes = (isset($resPlanes['status']) && $resPlanes['status'] == 200) ? $resPlanes['data'] : [];

$resModulos = $api->solicitar("index.php?table=modulos", "GET", null, $token);
$listaModulosMaestra = (isset($resModulos['status']) && $resModulos['status'] == 200) ? $resModulos['data'] : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión de Planes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cal-wrap { padding:20px; background:#f4f4f1; min-height:100vh; }
        .switch { position: relative; display: inline-block; width: 44px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #ccc; border-radius: 34px; transition: .4s; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 4px; bottom: 4px; background: white; border-radius: 50%; transition: .4s; }
        input:checked + .slider { background: #28a745; }
        input:checked + .slider:before { transform: translateX(22px); }
        .row-modulo-padre { background-color: #e9ecef !important; font-weight: bold; color: #0b4f7a; }
        .indent-hijo { padding-left: 45px !important; }
        .table-permissions thead th { background: #0b4f7a; color: white; position: sticky; top: 0; z-index: 10; }
        .modal-header-custom {
    background-color: #0b4f7a; /* Azul corporativo */
    color: #ffffff;            /* Texto blanco para contraste */
    padding: 15px;
    border-bottom: 2px solid #003366;
}

/* Estilo para el texto específico */
.modal-header-custom h5 {
    font-weight: bold;
    margin: 0;
}
    </style>
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-layer-group me-2"></i>Gestión de Planes y Accesos</h2>

    <?php if($mensaje) echo "<div class='alert alert-danger'>$mensaje</div>"; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="POST" id="formPlan">
                <input type="hidden" id="id_plan" name="id_plan">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1 text-uppercase">Nombre del Plan</label>
                        <input type="text" id="nombre_plan" name="nombre_plan" class="form-control" placeholder="Ej: Básico" required>
                    </div>
                    <div class="col-md-5">
                        <label class="fw-bold small mb-1 text-uppercase">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control" placeholder="Ej: Ideal para microempresas">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1 text-uppercase">Límite Usuarios</label>
                        <input type="number" id="limite_usuarios" name="limite_usuarios" class="form-control" value="5" required>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small mb-1 text-uppercase">Precio Mensual</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" id="precio_mensual" name="precio_mensual" class="form-control" value="0.00" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold small d-block mb-1 text-uppercase">Estado</label>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <label class="switch">
                                <input type="checkbox" id="status" name="status" checked>
                                <span class="slider"></span>
                            </label>
                            <span id="status-label" class="small fw-bold text-success">Activo</span>
                        </div>
                    </div>
                    <div class="col-md-10 text-end align-self-end">
                        <button type="submit" id="btnGuardar" class="btn btn-success px-5">
                            <i class="fa-solid fa-save me-2"></i>Guardar Plan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded shadow-sm border overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark text-uppercase small">
                <tr>
                    <th width="50" class="ps-3">ID</th>
                    <th>Plan</th>
                    <th>Descripción</th>
                    <th class="text-center">Usuarios</th>
                    <th class="text-end">Precio</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($listaPlanes)): ?>
                    <?php foreach ($listaPlanes as $p): ?>
                        <?php 
                            $_id      = $p['id_plan'] ?? '';
                            $_nombre  = $p['nombre_plan'] ?? '';
                            $_desc    = $p['descripcion'] ?? '';
                            $_limite  = $p['limite_usuarios'] ?? 0;
                            $_precio  = $p['precio_mensual'] ?? 0;
                            $_status  = ($p['estado'] == 1) ? 1 : 0;
                        ?>
                        <tr>
                            <td class="ps-3"><?= $_id ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($_nombre) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($_desc) ?></small></td>
                            <td class="text-center"><?= $_limite ?></td>
                            <td class="text-end fw-bold">$<?= number_format($_precio, 2) ?></td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?= ($_status == 1) ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ($_status == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" 
                                        onclick="abrirModalPermisos('<?= $_id ?>', '<?= htmlspecialchars($_nombre) ?>')">
                                    <i class="fa-solid fa-lock me-1"></i> Accesos
                                </button>
                                <button class="btn btn-sm btn-light border ms-1" 
                                        onclick="cargarPlan('<?= base64_encode(json_encode($p)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay planes configurados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalPermisos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="tituloModal">Configuración de Accesos por Plan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <input type="hidden" id="perm_id_plan">
                <table class="table table-sm table-bordered mb-0 table-permissions">
                    <thead class="text-center table-light small fw-bold">
                        <tr>
                            <th class="text-start p-3">MÓDULO / FUNCIÓN</th>
                            <th width="90">VER</th>
                            <th width="90">CREAR</th>
                            <th width="90">EDITAR</th>
                            <th width="90">ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $padres = array_filter($listaModulosMaestra, fn($m) => empty($m['id_padre']));
                        $hijos = array_filter($listaModulosMaestra, fn($m) => !empty($m['id_padre']));
                        
                        foreach ($padres as $pad): 
                        ?>
                            <tr class="row-modulo-padre">
                                <td colspan="5" class="p-2 ps-3"><?= htmlspecialchars($pad['nombre_modulo']) ?></td>
                            </tr>
                            <?php foreach ($hijos as $hij): if ($hij['id_padre'] == $pad['id_modulo']): ?>
                                <tr>
                                    <td class="indent-hijo py-2"><?= htmlspecialchars($hij['nombre_modulo']) ?></td>
                                    <td class="text-center"><input type="checkbox" class="check-perm" data-modulo="<?= $hij['id_modulo'] ?>" data-perm="ver"></td>
                                    <td class="text-center"><input type="checkbox" class="check-perm" data-modulo="<?= $hij['id_modulo'] ?>" data-perm="crear"></td>
                                    <td class="text-center"><input type="checkbox" class="check-perm" data-modulo="<?= $hij['id_modulo'] ?>" data-perm="editar"></td>
                                    <td class="text-center"><input type="checkbox" class="check-perm" data-modulo="<?= $hij['id_modulo'] ?>" data-perm="eliminar"></td>
                                </tr>
                            <?php endif; endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-success px-4" onclick="guardarPermisos()">
                    <i class="fa-solid fa-sync me-2"></i>Sincronizar Accesos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalPerm = new bootstrap.Modal(document.getElementById('modalPermisos'));

    window.cargarPlan = function(base64) {
        try {
            const d = JSON.parse(atob(base64));
            document.getElementById('id_plan').value = d.id_plan || "";
            document.getElementById('nombre_plan').value = d.nombre_plan || "";
            document.getElementById('descripcion').value = d.descripcion || "";
            document.getElementById('limite_usuarios').value = d.limite_usuarios || 0;
            document.getElementById('precio_mensual').value = d.precio_mensual || 0;
            document.getElementById('status').checked = (parseInt(d.estado) === 1);
            document.getElementById('status-label').textContent = (parseInt(d.estado) === 1) ? "Activo" : "Inactivo";
            
            document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar Plan';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) { console.error("Error al cargar plan:", e); }
    };

    window.abrirModalPermisos = function(id, nombre) {
        document.getElementById('perm_id_plan').value = id;
        document.getElementById('tituloModal').innerHTML = `Accesos para Plan: <strong>${nombre}</strong>`;
        document.querySelectorAll('.check-perm').forEach(c => c.checked = false);

        // Ajustar endpoint según tu estructura de API para planes
        fetch(`../../api/index.php?table=planes&action=permisos&id=${id}`, {
            headers: { 'Authorization': 'Bearer <?= $token ?>' }
        })
        .then(res => res.json())
        .then(data => {
            if(Array.isArray(data)) {
                data.forEach(p => {
                    const marcar = (tipo, val) => {
                        const ck = document.querySelector(`[data-modulo="${p.id_modulo}"][data-perm="${tipo}"]`);
                        if(ck) ck.checked = (parseInt(val) === 1);
                    };
                    marcar('ver', p.ver); 
                    marcar('crear', p.crear);
                    marcar('editar', p.editar); 
                    marcar('eliminar', p.eliminar);
                });
            }
            modalPerm.show();
        })
        .catch(err => {
            console.error("Error cargando permisos:", err);
            modalPerm.show();
        });
    };

    window.guardarPermisos = function() {
        const idPlan = document.getElementById('perm_id_plan').value;
        const matrix = [];
        const checks = document.querySelectorAll('.check-perm');
        const modulosIds = [...new Set([...checks].map(c => c.dataset.modulo))];
        
        modulosIds.forEach(mId => {
            matrix.push({
                id_modulo: mId,
                ver: document.querySelector(`[data-modulo="${mId}"][data-perm="ver"]`).checked ? 1 : 0,
                crear: document.querySelector(`[data-modulo="${mId}"][data-perm="crear"]`).checked ? 1 : 0,
                editar: document.querySelector(`[data-modulo="${mId}"][data-perm="editar"]`).checked ? 1 : 0,
                eliminar: document.querySelector(`[data-modulo="${mId}"][data-perm="eliminar"]`).checked ? 1 : 0
            });
        });

        fetch(`../../api/index.php?table=planes&action=permisos&id=${idPlan}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= $token ?>' 
            },
            body: JSON.stringify({ permisos: matrix })
        })
        .then(res => res.json())
        .then(res => {
            if(res.ok) {
                alert(res.mensaje || "Accesos sincronizados!");
                modalPerm.hide();
            } else {
                alert("Error: " + (res.error || "No se pudo sincronizar"));
            }
        })
        .catch(err => console.error("Error:", err));
    };

    document.getElementById("status").addEventListener('change', function() {
        const label = document.getElementById("status-label");
        label.textContent = this.checked ? "Activo" : "Inactivo";
        label.className = this.checked ? "small fw-bold text-success" : "small fw-bold text-danger";
    });
</script>
</body>
</html>