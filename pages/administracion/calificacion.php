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

          <div class="col-md-6">
            <label class="fw-bold small text-muted text-uppercase">Nombre calificación</label>
            <input id="nombre" class="form-control" type="text" placeholder="Ej: Cumple / No cumple">
          </div>

          <div class="col-md-3">
            <button type="button" class="btn btn-outline-secondary w-100" id="btnModo">
              AGREGAR CALIFICACIÓN
            </button>
          </div>

          <div class="col-md-3"></div>
        </div>

        <!-- DETALLE -->
        <div class="row g-3 mt-0 hidden" id="detalle">
          <div class="col-md-6">
            <label class="fw-bold small text-muted text-uppercase">Descripción</label>
            <input id="descripcion" class="form-control" type="text" placeholder="Ej: Evidencia requerida">
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Valor</label>
            <input id="valor" class="form-control" type="number" min="0" step="0.01" placeholder="Ej: 10">
          </div>

          <div class="col-md-3 text-center">
            <label class="fw-bold small d-block text-muted text-uppercase">Estado</label>
            <label class="switch mt-1">
              <input type="checkbox" id="status" checked>
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <div class="mt-3">
          <button type="button" class="btn btn-success px-4 shadow-sm" id="btnAgregar">Guardar</button>
          <button type="button" class="btn btn-outline-secondary px-4" id="btnCancelar">Limpiar</button>
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
              <td colspan="3" class="text-center text-muted py-4">Cargando...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  const API_URL = "http://localhost/SSTMANAGER-BACKEND/public/index.php?table=calificaciones";

  let modoDetalle = false;
  const $ = (id) => document.getElementById(id);

  function getEstadoDetalle() {
    return $("status").checked ? "Activo" : "Inactivo";
  }

  // Cambia headers segun modo
  function renderHeader() {
    if (!modoDetalle) {
      $("thead").innerHTML = `
        <tr>
          <th width="80">ID</th>
          <th>NOMBRE</th>
          <th class="text-center">ESTADO</th>
        </tr>
      `;
    } else {
      $("thead").innerHTML = `
        <tr>
          <th width="80">ID</th>
          <th>CALIFICACIÓN</th>
          <th>DESCRIPCIÓN</th>
          <th>VALOR</th>
          <th class="text-center">ESTADO</th>
        </tr>
      `;
    }
  }

  // Render body segun modo
  function renderRows(data) {
    const tbody = $("tbody");

    if (!Array.isArray(data) || data.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="${modoDetalle ? 5 : 3}" class="text-center text-muted py-4">
            No hay registros.
          </td>
        </tr>
      `;
      return;
    }

    if (!modoDetalle) {
      // MODO NORMAL: muestra solo padre
      tbody.innerHTML = data.map(r => `
        <tr>
          <td>${r.id ?? r.id_calificacion ?? ""}</td>
          <td>${r.nombre ?? ""}</td>
          <td class="text-center">${r.estado ?? "Activo"}</td>
        </tr>
      `).join("");
      return;
    }

    // MODO DETALLE: si una calificación tiene varios items, muestra una fila por item
    const filas = [];
    data.forEach(r => {
      const id = r.id ?? r.id_calificacion ?? "";
      const nombre = r.nombre ?? "";

      const items = Array.isArray(r.items) ? r.items : [];

      if (items.length === 0) {
        // si no hay detalle, igual mostramos la calificación (sin columnas detalle)
        filas.push(`
          <tr>
            <td>${id}</td>
            <td>${nombre}</td>
            <td></td>
            <td></td>
            <td class="text-center">${r.estado ?? "Activo"}</td>
          </tr>
        `);
      } else {
        items.forEach(it => {
          filas.push(`
            <tr>
              <td>${id}</td>
              <td>${nombre}</td>
              <td>${it.descripcion ?? ""}</td>
              <td>${it.valor ?? ""}</td>
              <td class="text-center">${it.estado ?? r.estado ?? "Activo"}</td>
            </tr>
          `);
        });
      }
    });

    tbody.innerHTML = filas.join("");
  }

  async function cargarTabla() {
    renderHeader();
    const tbody = $("tbody");

    tbody.innerHTML = `
      <tr>
        <td colspan="${modoDetalle ? 5 : 3}" class="text-center text-muted py-4">
          Cargando...
        </td>
      </tr>
    `;

    try {
      const res = await fetch(API_URL, { method: "GET", mode: "cors" });
      const data = await res.json();
      renderRows(data);
    } catch (e) {
      tbody.innerHTML = `
        <tr>
          <td colspan="${modoDetalle ? 5 : 3}" class="text-center text-danger py-4">
            Error cargando: ${e.message}
          </td>
        </tr>
      `;
    }
  }

  // BOTÓN: activa modo detalle y muestra inputs ocultos
  $("btnModo").addEventListener("click", async () => {
    modoDetalle = true;
    $("detalle").classList.remove("hidden");
    await cargarTabla();
  });

  // Limpiar
  $("btnCancelar").addEventListener("click", () => {
    limpiar();
    // opcional: volver a modo normal al limpiar
    modoDetalle = false;
    $("detalle").classList.add("hidden");
    cargarTabla();
  });

  function limpiar() {
    document.querySelectorAll("#formCalificacion input").forEach(el => {
      if (el.type === "checkbox") el.checked = true;
      else el.value = "";
    });
  }

  // Guardar
  $("btnAgregar").onclick = async () => {
    const nombre = $("nombre").value.trim();
    if (!nombre) return Swal.fire("Falta el nombre", "Ingresa el nombre.", "warning");

    const payload = { nombre, estado: "Activo" };

    if (modoDetalle) {
      const desc = $("descripcion").value.trim();
      const valor = $("valor").value;

      if (!desc || valor === "") {
        return Swal.fire("Campos incompletos", "Completa descripción y valor.", "warning");
      }

      payload.items = [{
        descripcion: desc,
        valor: parseFloat(valor),
        estado: getEstadoDetalle()
      }];
    }

    try {
      const res = await fetch(API_URL, {
        method: "POST",
        mode: "cors",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      if (!res.ok) {
        return Swal.fire("Error", data.error || "No se pudo guardar.", "error");
      }

      Swal.fire("✅ Guardado", `ID: ${data.id}`, "success");
      limpiar();
      await cargarTabla();

    } catch (e) {
      Swal.fire("Error", e.message, "error");
    }
  };

  // Al cargar la página: modo normal
  document.addEventListener("DOMContentLoaded", () => {
    modoDetalle = false;
    $("detalle").classList.add("hidden");
    cargarTabla();
  });
</script>


</body>
</html>
