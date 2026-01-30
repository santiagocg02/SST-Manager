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

// 1. CARGAR PERFILES (GET)
$resPerfiles = $api->solicitar("index.php?table=perfiles", "GET", null, $token);
$listaPerfiles = (isset($resPerfiles['status']) && $resPerfiles['status'] == 200) ? $resPerfiles['data'] : [];

// 2. PROCESAR FORMULARIO (POST/PUT)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $id = $_POST["id"] ?? "";
    $rol = $_POST["rol"] ?? "Usuario";

    // Lógica solicitada: Si es Master, el perfil es forzosamente 1 y empresa es null
    $id_perfil_final = ($rol === "Master") ? 1 : ($_POST["id_perfil"] ?: null);
    $id_empresa_final = ($rol === "Master") ? 1 : ($_POST["id_empresa"] ?: null);

    $datosEnviar = [
        "nombre"           => trim($_POST["nombre"] ?? ""),
        "apellido"         => trim($_POST["apellido"] ?? ""),
        "email"            => trim($_POST["email"] ?? ""),
        "tipo_documento"   => $_POST["tipo_documento"] ?? "CC",
        "numero_documento" => trim($_POST["numero_documento"] ?? ""),
        "password"         => !empty($_POST["password"]) ? $_POST["password"] : null,
        "rol"              => $rol,
        "id_empresa"       => $id_empresa_final,
        "id_perfil"        => $id_perfil_final,
        "estado"           => isset($_POST["status"]) ? 1 : 0
    ];

    $endpoint = "index.php?table=usuarios" . (!empty($id) ? "&id=$id" : "");
    $metodo = !empty($id) ? "PUT" : "POST";
    $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);
// Añade esto justo debajo para depurar:
if (isset($resultado['status']) && $resultado['status'] != 200 && $resultado['status'] != 201) {
    // Esto mostrará en pantalla el error exacto que la base de datos le da a la API
    echo "<pre>";
    print_r($resultado); 
    echo "</pre>";
    die(); // Detiene la ejecución para que puedas leerlo
}

    if (isset($resultado['status']) && ($resultado['status'] == 200 || $resultado['status'] == 201)) {
        $_SESSION['alerta_exito'] = !empty($id) ? "Usuario actualizado correctamente." : "Usuario creado con éxito.";
        header("Location: usuarios.php");
        exit;
    } else {
        $mensaje_error = "Error: " . ($resultado['error'] ?? "No se pudo procesar la solicitud");
    }
}

// 3. CARGAR LISTA DE USUARIOS
$respuestaGet = $api->solicitar("index.php?table=usuarios", "GET", null, $token);
$listaUsuarios = (isset($respuestaGet['status']) && $respuestaGet['status'] == 200) ? $respuestaGet['data'] : [];
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
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-user-shield me-2" style="color: var(--primary-blue);"></i>Gestión de Usuarios</h2>

    <?php if($mensaje_error) echo "<div class='alert alert-danger'>$mensaje_error</div>"; ?>

    <form method="POST" id="formUsuario" class="bg-white p-4 rounded shadow-sm mb-4 border">
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
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Vacío para no cambiar">
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
                    <option value="Usuario">Usuario</option>
                    <option value="Admin">Admin</option>
                    <option value="Master">Master</option>
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
                <label class="form-label fw-bold small text-uppercase">Empresa ID</label>
                <input type="number" id="id_empresa" name="id_empresa" class="form-control">
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
    // OCULTAR/MOSTRAR CAMPOS SEGÚN ROL
    function verificarRol(rol) {
        const esMaster = (rol === 'Master');
        const divPerfil = document.getElementById('div_perfil');
        const divEmpresa = document.getElementById('div_empresa');
        const selectPerfil = document.getElementById('id_perfil');
        const inputEmpresa = document.getElementById('id_empresa');

        if(esMaster) {
            divPerfil.style.display = 'none';
            divEmpresa.style.display = 'none';
            selectPerfil.value = "1"; // Forzamos perfil 1 para Master
            inputEmpresa.value = "";
        } else {
            divPerfil.style.display = 'block';
            divEmpresa.style.display = 'block';
        }
    }

    // CARGAR DATOS PARA EDITAR
    function cargar(base64Data) {
        try {
            const d = JSON.parse(atob(base64Data));
            
            document.getElementById('id').value = d.id;
            document.getElementById('nombre').value = d.datos_personales.nombre;
            document.getElementById('apellido').value = d.datos_personales.apellido;
            document.getElementById('email').value = d.datos_personales.correo;
            document.getElementById('tipo_documento').value = d.identificacion.tipo;
            document.getElementById('numero_documento').value = d.identificacion.numero;
            document.getElementById('rol').value = d.seguridad.rol_sistema;
            
            // Lógica de perfil al cargar
            if (d.seguridad.rol_sistema === 'Master') {
                document.getElementById('id_perfil').value = "1";
            } else {
                document.getElementById('id_perfil').value = d.seguridad.perfil ? d.seguridad.perfil.id : "";
            }

            document.getElementById('id_empresa').value = (d.organizacion && d.organizacion.id_empresa) ? d.organizacion.id_empresa : "";
            document.getElementById('status').checked = (d.estado_cuenta.activo === true);
            
            // UI Update
            document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-sync me-2"></i>Actualizar Usuario';
            document.getElementById('btnGuardar').className = "btn btn-primary px-4 shadow-sm";
            
            verificarRol(d.seguridad.rol_sistema);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (e) {
            console.error("Error al cargar datos:", e);
        }
    }

    function limpiarForm() {
        document.getElementById('formUsuario').reset();
        document.getElementById('id').value = "";
        document.getElementById('btnGuardar').innerHTML = '<i class="fa-solid fa-save me-2"></i>Guardar Usuario';
        document.getElementById('btnGuardar').className = "btn btn-success px-4 shadow-sm";
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