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

// PROCESAR FORMULARIO (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";
    $datosEnviar = [
        "nombre_modulo" => trim($_POST["nombre"] ?? ""),
        "descripcion"   => trim($_POST["descripcion"] ?? ""),
        "icono"         => trim($_POST["icono"] ?? ""),
        "id_padre"      => ($_POST["id_padre"] == "0") ? null : $_POST["id_padre"],
        "estado"        => isset($_POST["status"]) ? 1 : 0
    ];

    if (!empty($datosEnviar["nombre_modulo"])) {
        // Usar ruta real para evitar error 404
        $endpoint = "index.php?table=modulos" . (!empty($id) ? "&id=$id" : "");
        $metodo = !empty($id) ? "PUT" : "POST";
        $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

        if ($resultado['status'] == 200 || $resultado['status'] == 201) {
            header("Location: modulo.php");
            exit;
        } else {
            $mensaje = "Error: " . json_encode($resultado);
        }
    }
}

// CARGAR DATOS (GET)
$respuestaGet = $api->solicitar("index.php?table=modulos", "GET", null, $token);
$listaModulos = ($respuestaGet['status'] == 200) ? $respuestaGet['data'] : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión de Módulos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cal-wrap { padding:20px; background:#f4f4f1; min-height:100vh; }
        .row-hijo { display: none; background: #fafafa; }
        .row-hijo.show { display: table-row; }
        .indent { padding-left: 45px !important; }
        .switch { position: relative; display: inline-block; width: 44px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #ccc; border-radius: 34px; transition: .4s; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 4px; bottom: 4px; background: white; border-radius: 50%; transition: .4s; }
        input:checked + .slider { background: #28a745; }
        input:checked + .slider:before { transform: translateX(22px); }
        .cal-table-container { background: #fff; border-radius: 8px; border: 1px solid #ddd; overflow: hidden; }
    </style>
</head>
<body class="cal-wrap">
    <div class="container-fluid">
        <h2 class="mb-4"><i class="fa-solid fa-folder-tree me-2"></i>Gestión de Módulos</h2>

        <?php if($mensaje) echo "<div class='alert alert-danger'>$mensaje</div>"; ?>

        <form method="POST" id="formModulo" class="bg-white p-4 rounded shadow-sm mb-4 border">
            <input type="hidden" id="id" name="id">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="fw-bold small">NOMBRE MÓDULO</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small">PERTENECE A</label>
                    <select id="id_padre" name="id_padre" class="form-select">
                        <option value="0">-- Módulo Principal --</option>
                        <?php foreach ($listaModulos as $m) if(!$m['id_padre']) echo "<option value='{$m['id_modulo']}'>".htmlspecialchars($m['nombre_modulo'])."</option>"; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small">DESCRIPCIÓN</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="fw-bold small">ICONO</label>
                    <input type="text" id="icono" name="icono" class="form-control" placeholder="fa-solid fa-user">
                </div>
                <div class="col-md-1 text-center">
                    <label class="fw-bold small d-block">ESTADO</label>
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
                <thead class="table-light">
                    <tr><th width="50"></th><th>TIPO</th><th class="text-center">ICONO</th><th>NOMBRE</th><th>DESCRIPCIÓN</th><th>ESTADO</th><th>ACCIONES</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $padres = array_filter($listaModulos, fn($x) => !$x['id_padre']);
                    foreach ($padres as $p): 
                        $hijosFiltrados = array_filter($listaModulos, fn($h) => $h['id_padre'] == $p['id_modulo']);
                    ?>
                    <tr onclick="toggleHijos('<?= $p['id_modulo'] ?>')" style="cursor:pointer">
                        <td class="text-center"><i class="fa-solid fa-chevron-down small text-muted"></i></td>
                        <td><span class="badge bg-primary">MÓDULO</span></td>
                        <td class="text-center"><i class="<?= htmlspecialchars($p['icono'] ?: 'fa-solid fa-folder') ?>"></i></td>
                        <td class="fw-bold"><?= htmlspecialchars($p['nombre_modulo']) ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($p['descripcion']) ?></small></td>
                        <td class="text-success fw-bold"><?= ($p['estado']==1)?'Activo':'Inactivo' ?></td>
                        <td>
                            <button class="btn btn-sm btn-light border" onclick="event.stopPropagation(); cargar('<?= base64_encode(json_encode($p)) ?>')">
                                <i class="fa-solid fa-pencil text-warning"></i>
                            </button>
                        </td>
                    </tr>
                    <?php foreach ($hijosFiltrados as $h): ?>
                    <tr class="row-hijo hijo-de-<?= $p['id_modulo'] ?>">
                        <td></td>
                        <td><span class="badge bg-secondary">FUNCIÓN</span></td>
                        <td class="text-center"><i class="<?= htmlspecialchars($h['icono'] ?: 'fa-solid fa-circle-dot') ?> fa-xs"></i></td>
                        <td class="indent"><i class="fa-solid fa-arrow-right-long me-2 opacity-25"></i><?= htmlspecialchars($h['nombre_modulo']) ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($h['descripcion']) ?></small></td>
                        <td><span class="text-<?= ($h['estado']==1)?'success':'danger' ?> small fw-bold"><?= ($h['estado']==1)?'Activo':'Inactivo' ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-light border" onclick="cargar('<?= base64_encode(json_encode($h)) ?>')">
                                <i class="fa-solid fa-pencil text-warning"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleHijos(id) {
            document.querySelectorAll('.hijo-de-' + id).forEach(el => el.classList.toggle('show'));
        }

        // Decodificamos el objeto desde base64 para cargar el formulario sin errores de sintaxis
        function cargar(base64Data) {
            try {
                const d = JSON.parse(atob(base64Data));
                console.log("Cargando datos para editar:", d);

                document.getElementById('id').value = d.id_modulo;
                document.getElementById('nombre').value = d.nombre_modulo;
                document.getElementById('descripcion').value = d.descripcion || "";
                document.getElementById('icono').value = d.icono || "";
                document.getElementById('id_padre').value = d.id_padre || "0";
                document.getElementById('status').checked = (d.estado == 1);
                document.getElementById('btnGuardar').textContent = "Actualizar";
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                console.error("Error al cargar los datos:", e);
            }
        }

        function limpiarForm() {
            document.getElementById('formModulo').reset();
            document.getElementById('id').value = "";
            document.getElementById('btnGuardar').textContent = "Guardar";
        }
    </script>
</body>
</html>