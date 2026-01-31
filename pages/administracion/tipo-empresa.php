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

// POST (guardar/actualizar)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? "";

    $datosEnviar = [
        "tamano_empresa"       => trim($_POST["tamano_empresa"] ?? ""),
        "cantidad_empleados"   => trim($_POST["cantidad_empleados"] ?? ""),
        "cantidad_por_sede"    => trim($_POST["cantidad_por_sede"] ?? ""),
        "aplicacion"           => trim($_POST["aplicacion"] ?? ""),
        "estado"               => isset($_POST["status"]) ? 1 : 0
    ];

    if (!empty($datosEnviar["tamano_empresa"])) {
        $endpoint = "index.php?table=tipo_empresa" . (!empty($id) ? "&id=$id" : "");
        $metodo   = !empty($id) ? "PUT" : "POST";

        $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

        if ($resultado['status'] == 200 || $resultado['status'] == 201) {
            header("Location: tipo-empresa.php");
            exit;
        } else {
            $mensaje = "Error: " . json_encode($resultado);
        }
    } else {
        $mensaje = "El tamaño de empresa es obligatorio.";
    }
}

// GET (listar)
$respuestaGet = $api->solicitar("index.php?table=tipo_empresa", "GET", null, $token);
$lista = ($respuestaGet['status'] == 200 && isset($respuestaGet['data']) && is_array($respuestaGet['data']))
    ? $respuestaGet['data']
    : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión Tipos de Empresa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- MISMO CSS que usa modulo.php -->
    <link rel="stylesheet" href="../../assets/css/main-style.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="cal-wrap">
<div class="container-fluid">

    <h2 class="mb-4">
        <i class="fa-solid fa-table-cells-large me-2" style="color: var(--primary-blue);"></i>
        Gestión Tipos de Empresa
    </h2>

    <?php if(!empty($mensaje)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- FORM CARD (igual a módulos) -->
    <form method="POST" id="formTipoEmpresa" class="bg-white p-4 rounded card-shadow mb-4 border">
        <input type="hidden" id="id" name="id">

        <div class="row g-3">
            <div class="col-md-3">
                <label class="fw-bold small text-muted">TAMAÑO EMPRESA</label>
                <select id="tamano_empresa" name="tamano_empresa" class="form-select" required>
                    <option value="" selected disabled>Seleccione</option>
                    <option value="Micro">Micro</option>
                    <option value="Pequeña">Pequeña</option>
                    <option value="Mediana">Mediana</option>
                    <option value="Grande">Grande</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold small text-muted">CANTIDAD EMPLEADOS DESDE </label>
                <input type="number" id="cantidad_empleados" name="cantidad_empleados" class="form-control" min="0">
            </div>

            <div class="col-md-3">
                <label class="fw-bold small text-muted">CANTIDAD EMPLEADOS HASTA</label>
                <input type="number" id="cantidad_por_sede" name="cantidad_por_sede" class="form-control" min="0">
            </div>

            <div class="col-md-2 text-center">
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
                    <th>TAMAÑO EMPRESA</th>
                    <th>CANTIDAD EMPLEADOS DESDE</th>
                    <th>CANTIDAD EMPLEADOS HASTA</th>
                    <th>ESTADO</th>
                    <th class="text-center" width="120">ACCIONES</th>
                </tr>
                </thead>

                <tbody>
                <?php if(empty($lista)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay registros.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($lista as $row): ?>
                        <?php
                        $estadoNum = (int)($row["estado"] ?? 0);
                        $estadoTxt = ($estadoNum === 1) ? "Activo" : "Inactivo";
                        $estadoCls = ($estadoNum === 1) ? "status-label-active" : "status-label-inactive";
                        ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($row["tamano_empresa"] ?? "") ?></td>
                            <td><?= htmlspecialchars($row["cantidad_empleados"] ?? "") ?></td>
                            <td><?= htmlspecialchars($row["cantidad_por_sede"] ?? "") ?></td>
                            <td>
                                <span class="<?= $estadoCls ?>"><?= $estadoTxt ?></span>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-light border shadow-sm"
                                        onclick="cargar('<?= base64_encode(json_encode($row)) ?>')">
                                    <i class="fa-solid fa-pencil text-warning"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function cargar(base64Data) {
        const d = JSON.parse(atob(base64Data));

        // Ajusta el id real si tu API usa otro nombre:
        document.getElementById('id').value = d.id_tipo_empresa ?? d.id ?? "";

        document.getElementById('tamano_empresa').value = d.tamano_empresa ?? "";
        document.getElementById('cantidad_empleados').value = d.cantidad_empleados ?? "";
        document.getElementById('cantidad_por_sede').value = d.cantidad_por_sede ?? "";
        document.getElementById('status').checked = (Number(d.estado) === 1);

        document.getElementById('btnGuardar').textContent = "Actualizar";
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function limpiarForm() {
        document.getElementById('formTipoEmpresa').reset();
        document.getElementById('id').value = "";
        document.getElementById('btnGuardar').textContent = "Guardar";
    }
</script>
</body>
</html>
