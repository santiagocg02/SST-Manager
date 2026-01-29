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
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="cal-wrap">
    <div class="container-fluid">
        <h2 class="mb-4"><i class="fa-solid fa-folder-tree me-2" style="color: var(--primary-blue);"></i>Gestión de Módulos</h2>

        <?php if($mensaje) echo "<div class='alert alert-danger'>$mensaje</div>"; ?>

        <form method="POST" id="formModulo" class="bg-white p-4 rounded card-shadow mb-4 border">
            <input type="hidden" id="id" name="id">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="fw-bold small text-muted">NOMBRE MÓDULO</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small text-muted">PERTENECE A</label>
                    <select id="id_padre" name="id_padre" class="form-select">
                        <option value="0">-- Módulo Principal --</option>
                        <?php foreach ($listaModulos as $m) if(!$m['id_padre']) echo "<option value='{$m['id_modulo']}'>".htmlspecialchars($m['nombre_modulo'])."</option>"; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold small text-muted">DESCRIPCIÓN</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="fw-bold small text-muted">ICONO</label>
                    <input type="text" id="icono" name="icono" class="form-control" placeholder="fa-solid fa-user">
                </div>
                <div class="col-md-1 text-center">
                    <label class="fw-bold small d-block text-muted">ESTADO</label>
                    <label class="switch mt-1">
                        <input type="checkbox" id="status" name="status" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" id="btnGuardar" class="btn btn-success px-4 shadow-sm">Guardar</button>
                <button type="button" class="btn btn-outline-secondary px-4" onclick="limpiarForm()">Limpiar</button>
            </div>
        </form>

        <div class="card-shadow border overflow-hidden">
            <div class="table-scroll-container">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark text-uppercase small">
                        <tr>
                            <th width="50"></th>
                            <th>TIPO</th>
                            <th class="text-center">ICONO</th>
                            <th>NOMBRE</th>
                            <th>DESCRIPCIÓN</th>
                            <th>ESTADO</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $padres = array_filter($listaModulos, fn($x) => !$x['id_padre']);
                        foreach ($padres as $p): 
                            $hijosFiltrados = array_filter($listaModulos, fn($h) => $h['id_padre'] == $p['id_modulo']);
                        ?>
                        <tr onclick="toggleHijos('<?= $p['id_modulo'] ?>', this)" style="cursor:pointer">
                            <td class="text-center">
                                <i class="fa-solid fa-chevron-down small text-muted chevron-icon"></i>
                            </td>
                            <td><span class="badge bg-primary px-3">MÓDULO</span></td>
                            <td class="text-center">
                                <i class="<?= htmlspecialchars($p['icono'] ?: 'fa-solid fa-folder') ?>" style="color: var(--primary-blue);"></i>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($p['nombre_modulo']) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($p['descripcion']) ?></small></td>
                            <td>
                                <span class="<?= ($p['estado']==1) ? 'status-label-active' : 'status-label-inactive' ?>">
                                    <?= ($p['estado']==1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border shadow-sm" onclick="event.stopPropagation(); cargar('<?= base64_encode(json_encode($p)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                            </td>
                        </tr>
                        <?php foreach ($hijosFiltrados as $h): ?>
                        <tr class="row-hijo hijo-de-<?= $p['id_modulo'] ?>">
                            <td></td>
                            <td><span class="badge bg-secondary px-3">FUNCIÓN</span></td>
                            <td class="text-center">
                                <i class="<?= htmlspecialchars($h['icono'] ?: 'fa-solid fa-circle-dot') ?> fa-xs text-muted"></i>
                            </td>
                            <td class="ps-4">
                                <i class="fa-solid fa-arrow-right-long me-2 opacity-25"></i><?= htmlspecialchars($h['nombre_modulo']) ?>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($h['descripcion']) ?></small></td>
                            <td>
                                <span class="<?= ($h['estado']==1) ? 'status-label-active' : 'status-label-inactive' ?> small">
                                    <?= ($h['estado']==1) ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border shadow-sm" onclick="cargar('<?= base64_encode(json_encode($h)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleHijos(id, tr) {
            // Alternar filas hijas
            document.querySelectorAll('.hijo-de-' + id).forEach(el => el.classList.toggle('show'));
            
            // Rotar el icono de la flecha
            const icon = tr.querySelector('.chevron-icon');
            if(icon) icon.classList.toggle('rotate-icon');
        }

        function cargar(base64Data) {
            try {
                const d = JSON.parse(atob(base64Data));
                document.getElementById('id').value = d.id_modulo;
                document.getElementById('nombre').value = d.nombre_modulo;
                document.getElementById('descripcion').value = d.descripcion || "";
                document.getElementById('icono').value = d.icono || "";
                document.getElementById('id_padre').value = d.id_padre || "0";
                document.getElementById('status').checked = (d.estado == 1);
                document.getElementById('btnGuardar').textContent = "Actualizar Módulo";
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                console.error("Error al cargar datos:", e);
            }
        }

        function limpiarForm() {
            document.getElementById('formModulo').reset();
            document.getElementById('id').value = "";
            document.getElementById('btnGuardar').textContent = "Guardar Módulo";
        }
    </script>
</body>
</html>