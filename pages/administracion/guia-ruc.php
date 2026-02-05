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

$misPermisos = [];
$ID_MODULO = 9; // ID asignado para Guía RUC

// Carga de permisos (omitido si es Master)
if ($rolSesion !== "Master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    $datosFinales = $resPermisos['data'] ?? $resPermisos;
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

// Funciones de Validación
function puedeVer($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true; 
    return isset($permisos[(int)$idModulo]) && ($permisos[(int)$idModulo]['ver'] == 1);
}
function puedeCrear($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    return isset($permisos[(int)$idModulo]) && ($permisos[(int)$idModulo]['crear'] == 1);
}
function puedeEditar($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    return isset($permisos[(int)$idModulo]) && ($permisos[(int)$idModulo]['editar'] == 1);
}

// Bloqueo de acceso si no puede ver el módulo
if (!puedeVer($ID_MODULO, $rolSesion, $misPermisos)) {
    echo "No tienes permisos para acceder a este módulo.";
    exit;
}

// 2. PROCESAR GUARDADO (POST/PUT)
$mensaje_error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["categoria"])) {
    $id = $_POST["id"] ?? "";
    $datosEnviar = [
        "categoria"     => $_POST["categoria"],
        "num_item"      => trim($_POST["num_item"]),
        "requisito"     => trim($_POST["requisito"]),
        "descripcion"   => trim($_POST["descripcion"]),
        "observaciones" => trim($_POST["observaciones"]),
        "estado"        => isset($_POST["status"]) ? 1 : 0
    ];

    $endpoint = "index.php?table=guia_ruc" . (!empty($id) ? "&id=$id" : "");
    $metodo = !empty($id) ? "PUT" : "POST";
    $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        $_SESSION['alerta_exito'] = !empty($id) ? "Registro actualizado." : "Registro creado.";
        header("Location: guia_ruc.php");
        exit;
    } else {
        $mensaje_error = "Error: " . ($resultado['error'] ?? "No se pudo guardar.");
    }
}

// 3. CARGAR LISTA (GET)
$respuestaGet = $api->solicitar("index.php?table=guia_ruc", "GET", null, $token);
$listaGuia = (isset($respuestaGet['status']) && $respuestaGet['status'] == 200) ? $respuestaGet['data'] : [];
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SST Manager - Guía RUC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const PUEDE_CREAR = <?= puedeCrear($ID_MODULO, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
        const PUEDE_EDITAR = <?= puedeEditar($ID_MODULO, $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
    </script>
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-clipboard-list me-2" style="color: var(--primary-blue);"></i>Guía RUC</h2>

    <?php if($mensaje_error) echo "<div class='alert alert-danger'>$mensaje_error</div>"; ?>

    <form method="POST" id="formGuiaRuc" class="bg-white p-4 rounded shadow-sm mb-4 border">
        <input type="hidden" id="id" name="id">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase">Categoría</label>
                <select id="categoria" name="categoria" class="form-select" required>
                    <option value="" selected disabled>Seleccione</option>
                    <option>Liderazgo y compromiso</option>
                    <option>Desarrollo y ejecución del SSTA</option>
                    <option>Evaluación y monitoreo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase">Número Ítem</label>
                <input type="text" id="num_item" name="num_item" class="form-control" placeholder="Ej: 1.1.1" required>
            </div>
            <div class="col-md-7">
                <label class="form-label fw-bold small text-uppercase">Requisito</label>
                <input type="text" id="requisito" name="requisito" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small text-uppercase">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="1"></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase">Observaciones</label>
                <input type="text" id="observaciones" name="observaciones" class="form-control">
            </div>
            <div class="col-md-2 text-center">
                <label class="form-label fw-bold small d-block text-uppercase">Estado</label>
                <label class="switch mt-1">
                    <input type="checkbox" id="status" name="status" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" id="btnGuardar" class="btn btn-success px-4 shadow-sm">
                <i class="fa-solid fa-save me-2"></i>Guardar Registro
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="limpiarForm()">Limpiar</button>
        </div>
    </form>

    <div class="card-shadow border overflow-hidden">
        <div class="table-scroll-container">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="ps-3">Categoría</th>
                        <th>N° Ítem</th>
                        <th>Requisito</th>
                        <th>Descripción</th>
                        <th>Observaciones</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaGuia)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay registros.</td></tr>
                    <?php else: foreach ($listaGuia as $item): ?>
                        <tr>
                            <td class="ps-3 fw-bold small"><?= htmlspecialchars($item['categoria']) ?></td>
                            <td><?= htmlspecialchars($item['num_item']) ?></td>
                            <td><?= htmlspecialchars($item['requisito']) ?></td>
                            <td class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['descripcion']) ?></td>
                            <td><?= htmlspecialchars($item['observaciones']) ?></td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?= ($item['estado']) ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ($item['estado']) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border shadow-sm" onclick="cargar('<?= base64_encode(json_encode($item)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (!PUEDE_CREAR) document.getElementById('btnGuardar').style.display = 'none';
    });

    function cargar(base64Data) {
        try {
            const d = JSON.parse(atob(base64Data));
            const btn = document.getElementById('btnGuardar');

            document.getElementById('id').value = d.id;
            document.getElementById('categoria').value = d.categoria;
            document.getElementById('num_item').value = d.num_item;
            document.getElementById('requisito').value = d.requisito;
            document.getElementById('descripcion').value = d.descripcion;
            document.getElementById('observaciones').value = d.observaciones;
            document.getElementById('status').checked = (parseInt(d.estado) === 1);
            
            btn.innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar Registro';
            btn.className = "btn btn-primary px-4 shadow-sm";
            btn.style.display = PUEDE_EDITAR ? 'inline-block' : 'none';
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) { console.error("Error al cargar:", e); }
    }

    function limpiarForm() {
        const btn = document.getElementById('btnGuardar');
        document.getElementById('formGuiaRuc').reset();
        document.getElementById('id').value = "";
        btn.innerHTML = '<i class="fa-solid fa-save me-2"></i>Guardar Registro';
        btn.className = "btn btn-success px-4 shadow-sm";
        btn.style.display = PUEDE_CREAR ? 'inline-block' : 'none';
    }
</script>

<?php if (isset($_SESSION['alerta_exito'])): ?>
    <script>
        Swal.fire({ title: '¡SST Manager!', text: '<?= $_SESSION['alerta_exito'] ?>', icon: 'success', confirmButtonColor: '#0b4f7a', timer: 3000 });
    </script>
    <?php unset($_SESSION['alerta_exito']); ?>
<?php endif; ?>

</body>
</html>