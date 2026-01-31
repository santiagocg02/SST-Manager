<?php
session_start();
require_once '../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../index.php");
  exit;
}

// Si luego conectas API aquí, ya tienes $api y $token listos:
$api = new ConexionAPI();
$token = $_SESSION["token"];
$mensaje = "";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>SST Manager - Calificación</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/main-style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Para ocultar el detalle (sin depender de tu CSS) */
    .hidden { display: none !important; }
  </style>
</head>

<body class="cal-wrap">
  <div class="container-fluid">

    <h2 class="mb-4">
      <i class="fa-solid fa-clipboard-check me-2" style="color: var(--primary-blue);"></i>
      Calificación
    </h2>

    <?php if($mensaje): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- CARD FORM -->
    <div class="bg-white p-4 rounded card-shadow mb-4 border">
      <form id="formCalificacion" onsubmit="return false;">
        <div class="row g-3 align-items-end">

          <!-- Nombre -->
          <div class="col-md-6">
            <label class="fw-bold small text-muted text-uppercase">Nombre calificación</label>
            <input id="nombre" class="form-control" type="text" placeholder="Ej: Cumple / No cumple">
          </div>

          <!-- Botón mostrar detalle -->
          <div class="col-md-3">
            <button type="button" class="btn btn-outline-secondary w-100" id="btnModo">
              AGREGAR CALIFICACIÓN
            </button>
          </div>

          <!-- Espacio -->
          <div class="col-md-3"></div>

        </div>

        <!-- DETALLE (OCULTO AL INICIO) -->
        <div class="row g-3 mt-0 hidden" id="detalle">

          <div class="col-md-6">
            <label class="fw-bold small text-muted text-uppercase">Descripción</label>
            <input id="descripcion" class="form-control" type="text" placeholder="Ej: Evidencia requerida">
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Valor</label>
            <input id="valor" class="form-control" type="number" min="0" placeholder="Ej: 10">
          </div>

          <div class="col-md-3 text-center">
            <label class="fw-bold small d-block text-muted text-uppercase">Estado</label>
            <label class="switch mt-1">
              <input type="checkbox" id="status" checked>
              <span class="slider"></span>
            </label>
          </div>

        </div>

        <!-- BOTONES (A LA IZQUIERDA) -->
        <div class="mt-3">
          <button type="button" class="btn btn-success px-4 shadow-sm" id="btnAgregar">
            Guardar
          </button>
          <button type="button" class="btn btn-outline-secondary px-4" id="btnCancelar">
            Limpiar
          </button>
        </div>
      </form>
    </div>

    <!-- TABLE -->
    <div class="card-shadow border overflow-hidden">
      <div class="table-scroll-container">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark text-uppercase small" id="thead">
            <tr>
              <th width="80">ID</th>
              <th>NOMBRE</th>
              <th class="text-center">ESTADO</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr>
              <td colspan="4" class="text-center text-muted py-4">
                No hay registros.
              </td>
            </tr>
          </tbody>

        </table>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let modoDetalle = false;
    let autoId = 1;

    const $ = (id) => document.getElementById(id);

    // Mostrar el detalle SOLO cuando se hace clic
    $("btnModo").addEventListener("click", () => {
      modoDetalle = true;
      $("detalle").classList.remove("hidden");

      // Cambia el encabezado de tabla a modo detalle
      $("thead").innerHTML = `
        <tr>
          <th>CALIFICACIÓN</th>
          <th>DESCRIPCIÓN</th>
          <th>VALOR</th>
          <th class="text-center">ESTADO</th>
        </tr>
      `;
    });

    $("btnAgregar").onclick = () => {
  const nombre = $("nombre").value.trim();

  if (!nombre) return alert("Ingresa el nombre de la calificación");

  const tbody = $("tbody");

  // ✅ quitar "No hay registros"
  if (
    tbody.children.length === 1 &&
    tbody.children[0].querySelector("td[colspan]")
  ) {
    tbody.innerHTML = "";
  }

  if (!modoDetalle) {
    tbody.innerHTML += `
      <tr>
        <td>${autoId++}</td>
        <td>${nombre}</td>
        <td></td>
      </tr>`;
  } else {
    const desc = $("descripcion").value.trim();
    const valor = $("valor").value.trim();
    const estado = $("estado").value;

    if (!desc || !valor || !estado)
      return alert("Completa descripción, valor y estado");

    tbody.innerHTML += `
      <tr>
        <td>${nombre}</td>
        <td>${desc}</td>
        <td>${valor}</td>
        <td>${estado}</td>
      </tr>`;
  }

  limpiar();
};


    $("btnCancelar").addEventListener("click", limpiar);

    function limpiar() {
      // Limpiar inputs
      document.querySelectorAll("#formCalificacion input").forEach(el => {
        if (el.type === "checkbox") el.checked = true;
        else el.value = "";
      });
    }
  </script>

</body>
</html>
