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
$empresa = $_SESSION["id_empresa"] ?? 0;

if (in_array(strtolower($rolSesion), ['master', 'administrador']) && !empty($_REQUEST["id_empresa"])) {
    $_SESSION["id_empresa"] = (int)$_REQUEST["id_empresa"];
    $empresa = $_SESSION["id_empresa"]; 
}
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

/**
 * Funciones de Validación de Permisos
 */
function puedeVer($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true; 
    $id = (int)$idModulo;
    return isset($permisos[$id]) && ($permisos[$id]['ver'] == 1);
}

function puedeCrear($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    $id = (int)$idModulo;
    return isset($permisos[$id]) && (int)($permisos[$id]['crear'] ?? 0) === 1;
}

function puedeEditar($idModulo, $rol, $permisos) {
    if ($rol === "Master") return true;
    $id = (int)$idModulo;
    return isset($permisos[$id]) && (int)($permisos[$id]['editar'] ?? 0) === 1;
}

// 3. CARGAR PERFILES
$resPerfiles = $api->solicitar("index.php?table=perfiles", "GET", null, $token);
$listaPerfiles = (isset($resPerfiles['status']) && $resPerfiles['status'] == 200) ? $resPerfiles['data'] : [];

if (!empty($empresa)) {
    $listaPerfiles = array_filter($listaPerfiles, function($p) use ($empresa) {
        return isset($p['id_empresa']) && $p['id_empresa'] == $empresa;
    });
}

$resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
$todasLasEmpresas = (isset($resEmpresas['status']) && $resEmpresas['status'] == 200) ? $resEmpresas['data'] : [];

$listaEmpresas = array_filter($todasLasEmpresas, function($emp) use ($empresa) {
    return isset($emp['id_empresa']) && $emp['id_empresa'] == $empresa;
});

// 4. PROCESAR FORMULARIO (POST/PUT)
$mensaje_error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $id = $_POST["id"] ?? "";
    $rolForm = $_POST["rol"] ?? "Usuario";

    $id_perfil_final = ($rolForm === "Master") ? 1 : ($_POST["id_perfil"] ?: null);
    $id_empresa_final = ($rolForm === "Master") ? 1 : ($_POST["id_empresa"] ?: null);

    $datosEnviar = [
        "nombre"           => trim($_POST["nombre"] ?? ""),
        "apellido"         => trim($_POST["apellido"] ?? ""),
        "email"            => trim($_POST["email"] ?? ""),
        "tipo_documento"   => $_POST["tipo_documento"] ?? "CC",
        "numero_documento" => trim($_POST["numero_documento"] ?? ""),
        "password"         => !empty($_POST["password"]) ? $_POST["password"] : null,
        "rol"              => $rolForm,
        "id_empresa"       => $id_empresa_final,
        "id_perfil"        => $id_perfil_final,
        "estado"           => isset($_POST["status"]) ? 1 : 0
    ];

    $endpoint = "index.php?table=usuarios" . (!empty($id) ? "&id=$id" : "");
    $metodo = !empty($id) ? "PUT" : "POST";
    $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        $_SESSION['alerta_exito'] = !empty($id) ? "Usuario actualizado correctamente." : "Usuario creado con éxito.";
        header("Location: usuarios.php");
        exit;
    } else {
        $mensaje_error = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 5. CARGAR LISTA DE USUARIOS
$respuestaGet = $api->solicitar("index.php?table=usuarios", "GET", null, $token);
$todosLosUsuarios = (isset($respuestaGet['status']) && $respuestaGet['status'] == 200) ? $respuestaGet['data'] : [];

$listaUsuarios = array_filter($todosLosUsuarios, function($u) use ($empresa) {
    $idEmpresaUsuario = $u['organizacion']['id_empresa'] ?? null;
    return $idEmpresaUsuario == $empresa;
});

$listaUsuarios = array_values($listaUsuarios);
$ID_MODULO = 5; 
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SST Manager - Gestión de Usuarios</title>
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
    <h2 class="mb-4"><i class="fa-solid fa-user-shield me-2" style="color: var(--primary-blue);"></i>Gestión de Usuarios</h2>

    <?php if($mensaje_error) echo "<div class='alert alert-danger'>$mensaje_error</div>"; ?>

    <form method="POST" id="formUsuario" autocomplete="off" class="bg-white p-4 rounded shadow-sm mb-4 border">
        <input type="hidden" id="id" name="id">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase">Apellido</label>
                <input type="text" id="apellido" name="apellido" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase">Email / Login</label>
                <input type="email" id="email" name="email" class="form-control" required autocomplete="new-password">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Vacío para no cambiar" autocomplete="new-password">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase">Tipo Doc.</label>
                <select id="tipo_documento" name="tipo_documento" class="form-select">
                    <option value="CC">CC</option>
                    <option value="NIT">NIT</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase">Documento</label>
                <input type="text" id="numero_documento" name="numero_documento" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase">Rol Sistema</label>
                <select id="rol" name="rol" class="form-select" onchange="verificarRol(this.value)">
                        <option value="Admin-user">Admin-user</option>    
                        <option value="Usuario">Usuario</option>                    
                </select>
            </div>
            
            <div class="col-md-3" id="div_perfil">
                <label class="form-label fw-bold small text-uppercase text-primary">Perfil Asociado</label>
                <select id="id_perfil" name="id_perfil" class="form-select border-primary">
                    <option value="">-- Seleccionar Perfil --</option>
                    <?php foreach ($listaPerfiles as $per): ?>
                        <option value="<?= $per['id_perfil'] ?>">
                            <?= htmlspecialchars($per['nombre_perfil']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2" id="div_empresa">
                <label class="form-label fw-bold small text-uppercase text-primary">Empresa</label>
                <select id="id_empresa" name="id_empresa" class="form-select border-primary" required>
                    <?php if (empty($listaEmpresas)): ?>
                        <option value="">-- Sin empresa --</option>
                    <?php else: ?>
                        <?php foreach ($listaEmpresas as $emp): ?>
                            <option value="<?= $emp['id_empresa'] ?>" selected>
                                <?= htmlspecialchars($emp['nombre_empresa']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-1 text-center">
                <label class="form-label fw-bold small d-block text-uppercase">Estado</label>
                <label class="switch mt-1">
                    <input type="checkbox" id="status" name="status" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" id="btnGuardar" class="btn btn-success px-4 shadow-sm">
                <i class="fa-solid fa-save me-2"></i>Guardar Usuario
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="limpiarForm()">Limpiar</button>
        </div>
    </form>

    <div class="card-shadow border overflow-hidden">
        <div class="table-scroll-container">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Identificación</th>
                        <th>Rol</th>
                        <th>Perfil</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaUsuarios as $u): ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $u['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($u['datos_personales']['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($u['datos_personales']['correo']) ?></td>
                        <td><small><?= $u['identificacion']['tipo'] ?>: <?= $u['identificacion']['numero'] ?></small></td>
                        <td><span class="badge bg-light text-dark border"><?= $u['seguridad']['rol_sistema'] ?></span></td>
                        <td><span class="text-primary small fw-bold"><?= $u['seguridad']['perfil']['nombre'] ?? 'N/A' ?></span></td>
                        <td class="text-center">
                            <span class="badge rounded-pill <?= ($u['estado_cuenta']['activo']) ? 'bg-success' : 'bg-danger' ?>">
                                <?= ($u['estado_cuenta']['activo']) ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light border shadow-sm" onclick="cargar('<?= base64_encode(json_encode($u)) ?>')">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Al cargar la página, forzamos la limpieza del formulario para evitar autocompletado residual
        limpiarForm();

        if (!PUEDE_CREAR) {
            document.getElementById('btnGuardar').style.display = 'none';
        }
    });

    function verificarRol(rol) {
        const esMaster = (rol === 'Master');
        const divPerfil = document.getElementById('div_perfil');
        const divEmpresa = document.getElementById('div_empresa');
        const selectPerfil = document.getElementById('id_perfil');
        const inputEmpresa = document.getElementById('id_empresa');

        if(esMaster) {
            divPerfil.style.display = 'none';
            divEmpresa.style.display = 'none';
            selectPerfil.value = "1";
            inputEmpresa.value = "1";
        } else {
            divPerfil.style.display = 'block';
            divEmpresa.style.display = 'block';
        }
    }

    function cargar(base64Data) {
        try {
            const d = JSON.parse(atob(base64Data));
            const btn = document.getElementById('btnGuardar');

            document.getElementById('id').value = d.id;
            document.getElementById('nombre').value = d.datos_personales.nombre;
            document.getElementById('apellido').value = d.datos_personales.apellido;
            document.getElementById('email').value = d.datos_personales.correo;
            document.getElementById('tipo_documento').value = d.identificacion.tipo;
            document.getElementById('numero_documento').value = d.identificacion.numero;
            document.getElementById('rol').value = d.seguridad.rol_sistema;
            
            if (d.seguridad.rol_sistema === 'Master') {
                document.getElementById('id_perfil').value = "1";
            } else {
                document.getElementById('id_perfil').value = d.seguridad.perfil ? d.seguridad.perfil.id : "";
            }

            document.getElementById('id_empresa').value = (d.organizacion && d.organizacion.id_empresa) ? d.organizacion.id_empresa : "";
            document.getElementById('status').checked = (d.estado_cuenta.activo === true);
            
            btn.innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar Usuario';
            btn.className = "btn btn-primary px-4 shadow-sm";
            btn.style.display = PUEDE_EDITAR ? 'inline-block' : 'none';
            
            verificarRol(d.seguridad.rol_sistema);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) {
            console.error("Error al cargar datos:", e);
        }
    }

    function limpiarForm() {
        const btn = document.getElementById('btnGuardar');
        const form = document.getElementById('formUsuario');
        
        form.reset();
        document.getElementById('id').value = "";
        
        // Limpieza manual de campos críticos para mayor seguridad
        document.getElementById('email').value = "";
        document.getElementById('password').value = "";
        
        btn.innerHTML = '<i class="fa-solid fa-save me-2"></i>Guardar Usuario';
        btn.className = "btn btn-success px-4 shadow-sm";
        
        btn.style.display = PUEDE_CREAR ? 'inline-block' : 'none';

        verificarRol('Usuario');
    }
</script>

<?php if (isset($_SESSION['alerta_exito'])): ?>
<script>
    Swal.fire({
        title: '¡SST Manager!',
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