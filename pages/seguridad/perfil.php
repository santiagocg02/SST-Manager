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

// 2. LÓGICA: Procesar Perfil (Creación y Actualización)
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

    // Ajuste: ConexionAPI suele devolver el código HTTP en 'status'
    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        header("Location: perfil.php");
        exit;
    } else {
        $mensaje = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 3. CARGAR DATOS
// Nota: Si tu ConexionAPI mete el body en 'data', mantenemos la lógica. 
// Si devuelve el body directo, ajustamos:
$resPerfiles = $api->solicitar("index.php?table=perfiles", "GET", null, $token);
$listaPerfiles = (isset($resPerfiles['status']) && $resPerfiles['status'] == 200) 
                 ? $resPerfiles['data'] : [];

$resModulos = $api->solicitar("index.php?table=modulos", "GET", null, $token);
$listaModulosMaestra = (isset($resModulos['status']) && $resModulos['status'] == 200) 
                        ? $resModulos['data'] : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión de Perfiles</title>
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
        .table-custom thead th {
    background-color: #0b4f7a !important; /* Azul profesional */
    color: #ffffff !important;            /* Texto blanco */
    border-bottom: 2px solid #003366;   /* Línea de separación sutil */
    text-transform: uppercase;           /* Mantiene el estilo de la imagen */
    padding: 12px 15px;
    font-weight: 600;
}
    </style>
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-user-shield me-2"></i>Gestión de Perfiles y Accesos</h2>

    <?php if($mensaje) echo "<div class='alert alert-danger'>$mensaje</div>"; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="POST" id="formPerfil">
                <input type="hidden" id="id_perfil" name="id_perfil">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="fw-bold small mb-1 text-uppercase">Nombre del Perfil</label>
                        <input type="text" id="nombre_perfil" name="nombre_perfil" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="fw-bold small mb-1 text-uppercase">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control">
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
                    <div class="col-md-2">
                        <button type="submit" id="btnGuardar" class="btn btn-success w-100">
                            <i class="fa-solid fa-save me-2"></i>Guardar
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
                    <th width="80" class="ps-3">ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($listaPerfiles)): ?>
                    <?php foreach ($listaPerfiles as $p): ?>
                        <?php 
                            $_id     = $p['id_perfil'] ?? '';
                            $_nombre = $p['nombre_perfil'] ?? '';
                            $_desc   = $p['descripcion'] ?? '';
                            $_status = ($p['estado'] == 1) ? 1 : 0;
                        ?>
                        <tr>
                            <td class="ps-3"><?= $_id ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($_nombre) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($_desc) ?></small></td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?= ($_status == 1) ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ($_status == 1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary px-3" 
                                        onclick="abrirModalPermisos('<?= $_id ?>', '<?= htmlspecialchars($_nombre) ?>')">
                                    <i class="fa-solid fa-lock me-1"></i> Permisos
                                </button>
                                <button class="btn btn-sm btn-light border ms-1" 
                                        onclick="cargarPerfil('<?= base64_encode(json_encode($p)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay perfiles disponibles.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalPermisos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="tituloModal">Configuración de Accesos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <input type="hidden" id="perm_id_perfil">
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
                    <i class="fa-solid fa-sync me-2"></i>Sincronizar Permisos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalPerm = new bootstrap.Modal(document.getElementById('modalPermisos'));

    window.cargarPerfil = function(base64) {
        try {
            const d = JSON.parse(atob(base64));
            document.getElementById('id_perfil').value = d.id_perfil || "";
            document.getElementById('nombre_perfil').value = d.nombre_perfil || "";
            document.getElementById('descripcion').value = d.descripcion || "";
            document.getElementById('status').checked = (parseInt(d.estado) === 1);
            document.getElementById('status-label').textContent = (parseInt(d.estado) === 1) ? "Activo" : "Inactivo";
            
            document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) { console.error("Error al cargar perfil:", e); }
    };

    window.abrirModalPermisos = function(id, nombre) {
        document.getElementById('perm_id_perfil').value = id;
        document.getElementById('tituloModal').innerHTML = `Accesos para: <strong>${nombre}</strong>`;
        document.querySelectorAll('.check-perm').forEach(c => c.checked = false);

        fetch(`../../api/index.php?table=perfiles&action=permisos&id=${id}`, {
            headers: { 'Authorization': 'Bearer <?= $token ?>' }
        })
        .then(res => res.json())
        .then(data => {
            // Ajuste: 'data' ahora es el array directo devuelto por el controlador
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
        const idPerfil = document.getElementById('perm_id_perfil').value;
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

        fetch(`../../api/index.php?table=perfiles&action=permisos&id=${idPerfil}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= $token ?>' 
            },
            body: JSON.stringify({ permisos: matrix })
        })
        .then(res => res.json())
        .then(res => {
            // Ajuste: El controlador devuelve { ok: true, mensaje: "..." }
            if(res.ok) {
                alert(res.mensaje || "Permisos actualizados!");
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