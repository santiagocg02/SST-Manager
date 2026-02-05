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

    // ✅ Alineado con Model/DB: tipo_empresa(tamano_empresa, empleados_desde, empleados_hasta, estado)
    $tamano  = trim($_POST["tamano_empresa"] ?? "");
    $desde   = isset($_POST["empleados_desde"]) ? (int)$_POST["empleados_desde"] : null;
    $hasta   = isset($_POST["empleados_hasta"]) ? (int)$_POST["empleados_hasta"] : null;
    $estado  = isset($_POST["estado"]) ? 1 : 0; // checkbox

    // Validaciones básicas (coherentes con controller)
    if ($tamano === "") {
        $mensaje = "El tamaño de empresa es obligatorio.";
    } elseif ($desde === null || $hasta === null) {
        $mensaje = "Los rangos de empleados son obligatorios.";
    } elseif ($desde > $hasta) {
        $mensaje = "El valor 'Desde' no puede ser mayor al valor 'Hasta'.";
    } else {
        $datosEnviar = [
            "tamano_empresa"  => $tamano,
            "empleados_desde" => $desde,
            "empleados_hasta" => $hasta,
            "estado"          => $estado
        ];

        // Nota: tu router actual es index.php?table=tipo_empresa (&id=...)
        $endpoint = "index.php?table=tipo_empresa" . (!empty($id) ? "&id=" . urlencode($id) : "");
        $metodo   = !empty($id) ? "PUT" : "POST";

        $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

        if (($resultado['status'] ?? 0) == 200 || ($resultado['status'] ?? 0) == 201) {
            header("Location: tipo-empresa.php");
            exit;
        } else {
            $mensaje = "Error: " . json_encode($resultado);
        }
    }
}

// GET (listar)
$respuestaGet = $api->solicitar("index.php?table=tipo_empresa", "GET", null, $token);
$lista = (($respuestaGet['status'] ?? 0) == 200 && isset($respuestaGet['data']) && is_array($respuestaGet['data']))
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
                <label class="fw-bold small text-muted">CANTIDAD EMPLEADOS DESDE</label>
                <input type="number" id="empleados_desde" name="empleados_desde" class="form-control" min="0" required>
            </div>

            <div class="col-md-3">
                <label class="fw-bold small text-muted">CANTIDAD EMPLEADOS HASTA</label>
                <input type="number" id="empleados_hasta" name="empleados_hasta" class="form-control" min="0" required>
            </div>

            <div class="col-md-2 text-center">
                <label class="fw-bold small d-block text-muted">ESTADO</label>
                <label class="switch mt-1">
                    <!-- ✅ name="estado" para que coincida con DB/controller -->
                    <input type="checkbox" id="status" name="estado" checked>
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
                            <td><?= htmlspecialchars($row["empleados_desde"] ?? "") ?></td>
                            <td><?= htmlspecialchars($row["empleados_hasta"] ?? "") ?></td>
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

    // ✅ id correcto de tu tabla
    document.getElementById('id').value = d.id_config ?? d.id ?? "";

    document.getElementById('tamano_empresa').value = d.tamano_empresa ?? "";
    document.getElementById('empleados_desde').value = d.empleados_desde ?? "";
    document.getElementById('empleados_hasta').value = d.empleados_hasta ?? "";

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
