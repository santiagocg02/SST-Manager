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

// 1. CARGAR PERFILES (GET) - Ajustado a tus nuevas llaves
$resPerfiles = $api->solicitar("index.php?table=perfiles", "GET", null, $token);
$listaPerfiles = ($resPerfiles['status'] == 200) ? $resPerfiles['data'] : [];

// 2. PROCESAR FORMULARIO (POST/PUT)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";
    $datosEnviar = [
        "nombre"           => trim($_POST["nombre"] ?? ""),
        "apellido"         => trim($_POST["apellido"] ?? ""),
        "email"            => trim($_POST["email"] ?? ""),
        "tipo_documento"   => $_POST["tipo_documento"] ?? "CC",
        "numero_documento" => trim($_POST["numero_documento"] ?? ""),
        "password"         => !empty($_POST["password"]) ? $_POST["password"] : null,
        "rol"              => $_POST["rol"] ?? "Usuario",
        "id_empresa"       => $_POST["id_empresa"] ?: null,
        "id_perfil"        => ($_POST["rol"] == "Master") ? null : ($_POST["id_perfil"] ?: null),
        "estado"           => isset($_POST["status"]) ? 1 : 0
    ];

    if (!empty($datosEnviar["email"])) {
        $endpoint = "index.php?table=usuarios" . (!empty($id) ? "&id=$id" : "");
        $metodo = !empty($id) ? "PUT" : "POST";
        $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

        if ($resultado['status'] == 200 || $resultado['status'] == 201) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $mensaje = "Error: " . json_encode($resultado);
        }
    }
}

// 3. CARGAR LISTA DE USUARIOS
$respuestaGet = $api->solicitar("index.php?table=usuarios", "GET", null, $token);
$listaUsuarios = ($respuestaGet['status'] == 200) ? $respuestaGet['data'] : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="../../assets/css/main-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="cal-wrap">
    <div class="container-fluid">
        <h2 class="mb-4"><i class="fa-solid fa-user-shield me-2"></i>Gestión de Usuarios</h2>

        <?php if($mensaje) echo "<div class='alert alert-danger'>$mensaje</div>"; ?>

        <form method="POST" id="formUsuario" class="bg-white p-4 rounded shadow-sm mb-4 border">
            <input type="hidden" id="id" name="id">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label-custom">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Email / Login</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Vacío para no cambiar">
                </div>

                <div class="col-md-2">
                    <label class="form-label-custom">Tipo Doc.</label>
                    <select id="tipo_documento" name="tipo_documento" class="form-select">
                        <option value="CC">CC</option>
                        <option value="NIT">NIT</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-custom">Documento</label>
                    <input type="text" id="numero_documento" name="numero_documento" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label-custom">Rol Sistema</label>
                    <select id="rol" name="rol" class="form-select" onchange="verificarRol(this.value)">
                        <option value="Usuario">Usuario</option>
                        <option value="Admin">Admin</option>
                        <option value="Master">Master</option>
                    </select>
                </div>
                
                <div class="col-md-3" id="div_perfil">
                    <label class="form-label-custom text-primary">Perfil Asociado</label>
                    <select id="id_perfil" name="id_perfil" class="form-select border-primary">
                        <option value="">-- Seleccionar Perfil --</option>
                        <?php foreach ($listaPerfiles as $per): ?>
                            <option value="<?= $per['id_perfil'] ?>">
                                <?= htmlspecialchars($per['nombre_perfil']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label-custom">Empresa ID</label>
                    <input type="number" id="id_empresa" name="id_empresa" class="form-control">
                </div>
                <div class="col-md-1 text-center">
                    <label class="form-label-custom d-block">Estado</label>
                    <label class="switch mt-1">
                        <input type="checkbox" id="status" name="status" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" id="btnGuardar" class="btn btn-success px-4">Guardar</button>
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarForm()">Limpiar</button>
            </div>
        </form>

        <div class="cal-table-container shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-uppercase small">
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE COMPLETO</th>
                        <th>CORREO</th>
                        <th>IDENTIFICACIÓN</th>
                        <th>ROL</th>
                        <th>PERFIL</th>
                        <th>ESTADO</th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaUsuarios as $u): ?>
                    <tr>
                        <td class="text-muted"><?= $u['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($u['datos_personales']['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($u['datos_personales']['correo']) ?></td>
                        <td><small><?= $u['identificacion']['tipo'] ?>: <?= $u['identificacion']['numero'] ?></small></td>
                        <td><span class="badge bg-light text-dark border"><?= $u['seguridad']['rol_sistema'] ?></span></td>
                        <td><span class="text-primary small"><?= $u['seguridad']['perfil']['nombre'] ?? 'N/A' ?></span></td>
                        <td>
                            <span class="badge <?= ($u['estado_cuenta']['activo'])?'bg-success':'bg-danger' ?>">
                                <?= ($u['estado_cuenta']['activo'])?'Activo':'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light border" onclick="cargar('<?= base64_encode(json_encode($u)) ?>')">
                                <i class="fa-solid fa-pencil text-warning"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function verificarRol(rol) {
            document.getElementById('div_perfil').style.display = (rol === 'Master') ? 'none' : 'block';
        }

        function cargar(base64Data) {
            try {
                const d = JSON.parse(atob(base64Data));
                console.log("Datos recibidos para editar:", d);

                document.getElementById('id').value = d.id;
                document.getElementById('nombre').value = d.datos_personales.nombre;
                document.getElementById('apellido').value = d.datos_personales.apellido;
                document.getElementById('email').value = d.datos_personales.correo;
                document.getElementById('tipo_documento').value = d.identificacion.tipo;
                document.getElementById('numero_documento').value = d.identificacion.numero;
                document.getElementById('rol').value = d.seguridad.rol_sistema;
                
                // Mapeo del ID del perfil desde el objeto anidado
                if (d.seguridad.perfil) {
                    document.getElementById('id_perfil').value = d.seguridad.perfil.id;
                } else {
                    document.getElementById('id_perfil').value = "";
                }

                document.getElementById('id_empresa').value = d.organizacion.id_empresa || "";
                document.getElementById('status').checked = (d.estado_cuenta.activo === true);
                
                document.getElementById('btnGuardar').textContent = "Actualizar Usuario";
                document.getElementById('btnGuardar').className = "btn btn-primary px-4";
                
                verificarRol(d.seguridad.rol_sistema);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                console.error("Error al decodificar datos:", e);
            }
        }

        function limpiarForm() {
            document.getElementById('formUsuario').reset();
            document.getElementById('id').value = "";
            document.getElementById('btnGuardar').textContent = "Guardar";
            document.getElementById('btnGuardar').className = "btn btn-success px-4";
            verificarRol('Usuario');
        }
    </script>
</body>
</html>