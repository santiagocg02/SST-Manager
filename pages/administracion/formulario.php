<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php");
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SST Manager - Formularios</title>

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- MISMO CSS de Modulos -->
  <link rel="stylesheet" href="../../assets/css/main-style.css">
</head>

<body class="cal-wrap">
  <div class="container-fluid">

    <h2 class="mb-4">
      <i class="fa-solid fa-rectangle-list me-2" style="color: var(--primary-blue);"></i>Formularios
    </h2>

    <!-- ================= FORMULARIO (CARD) ================= -->
    <div class="bg-white p-4 rounded card-shadow mb-4 border">
      <form id="formFormularios">
        <div class="row g-3 align-items-end">

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Nombre formulario</label>
            <input type="text" id="nombreFormulario" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Tipo norma</label>
            <select id="tipoNorma" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>ruc/1072</option>
              <option>sst</option>
              <option>calidad</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Item</label>
            <select id="item" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>Item 1072</option>
              <option>Guía RUC</option>
              <option>PHVA</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Rango calificación</label>
            <select id="rangoCalificacion" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>0 - 25</option>
              <option>26 - 50</option>
              <option>51 - 75</option>
              <option>76 - 100</option>
            </select>
          </div>

          <!-- ✅ SWITCH Estado (igual módulos) -->
          <div class="col-md-3">
            <label class="fw-bold small d-block text-muted text-uppercase">Estado</label>
            <label class="switch mt-1">
              <input type="checkbox" id="status" checked>
              <span class="slider"></span>
            </label>
          </div>

        </div>

        <!-- BOTONES IZQUIERDA -->
        <div class="mt-3">
          <button class="btn btn-success px-4 shadow-sm" id="btnGuardar" type="button">Guardar</button>
          <button class="btn btn-outline-secondary px-4" id="btnLimpiar" type="button">Limpiar</button>
        </div>
      </form>
    </div>

    <!-- ================= TABLA (FUERA DEL CARD) ================= -->
    <div class="card-shadow border overflow-hidden">
      <div class="table-scroll-container">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark text-uppercase small">
            <tr>
              <th>Nombre formulario</th>
              <th>Tipo norma</th>
              <th>Item</th>
              <th>Rango calificación</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="tablaBody">
            <tr>
              <td colspan="5" class="text-center text-muted py-4">No hay registros.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <script>
    const $ = (id) => document.getElementById(id);

    function limpiar(){
      ["nombreFormulario","tipoNorma","item","rangoCalificacion"].forEach(id => $(id).value = "");
      $("status").checked = true;
    }

    $("btnLimpiar").addEventListener("click", limpiar);

    $("btnGuardar").addEventListener("click", () => {
      const nombre = $("nombreFormulario").value.trim();
      const tipo = $("tipoNorma").value;
      const item = $("item").value;
      const rango = $("rangoCalificacion").value;
      const estadoTxt = $("status").checked ? "Activo" : "Inactivo";

      if (!nombre || !tipo || !item || !rango) {
        alert("Completa todos los campos obligatorios.");
        return;
      }

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="fw-semibold">${nombre}</td>
        <td>${tipo}</td>
        <td>${item}</td>
        <td>${rango}</td>
        <td>
          <span class="${$("status").checked ? "status-label-active" : "status-label-inactive"}">
            ${estadoTxt}
          </span>
        </td>
      `;

      const tbody = $("tablaBody");
      if (tbody.children.length === 1 && tbody.children[0].querySelector("td[colspan]")) tbody.innerHTML = "";
      tbody.appendChild(tr);

      limpiar();
    });
  </script>
</body>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
