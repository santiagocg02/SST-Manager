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
  <title>SST Manager - Item 1072</title>

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- MISMO CSS de Modulos -->
  <link rel="stylesheet" href="../../assets/css/main-style.css">
</head>

<body class="cal-wrap">
  <div class="container-fluid">

    <h2 class="mb-4">
      <i class="fa-solid fa-table-cells-large me-2" style="color: var(--primary-blue);"></i>
      Item 1072
    </h2>

    <!-- CARD FORM -->
    <form id="formItem1072" class="bg-white p-4 rounded card-shadow mb-4 border">
      <div class="row g-3 align-items-end">

        <div class="col-md-3">
          <label class="fw-bold small text-muted">CICLO PHVA</label>
          <select id="cicloPHVA" class="form-select" required>
            <option value="" selected disabled>Seleccione</option>
            <option>Planear</option>
            <option>Hacer</option>
            <option>Verificar</option>
            <option>Actuar</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="fw-bold small text-muted">CATEGORÍA</label>
          <select id="categoria" class="form-select" required>
            <option value="" selected disabled>Seleccione</option>
            <option>RUC</option>
            <option>SST</option>
            <option>Calidad</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="fw-bold small text-muted">TIPO</label>
          <select id="tipo" class="form-select" required>
            <option value="" selected disabled>Seleccione</option>
            <option>RUC</option>
            <option>SST</option>
            <option>Calidad</option>
          </select>
        </div>

        <div class="col-md-1 text-center">
          <label class="fw-bold small d-block text-muted">ESTADO</label>
          <label class="switch mt-1">
            <input type="checkbox" id="status" checked>
            <span class="slider"></span>
          </label>
        </div>

        <div class="col-md-3">
          <label class="fw-bold small text-muted">ITEM DEL ESTÁNDAR</label>
          <input type="text" id="itemEstandar" class="form-control" placeholder="Ej: 1.1.1" required>
        </div>

        <div class="col-md-3">
          <label class="fw-bold small text-muted">ITEM</label>
          <input type="text" id="item" class="form-control" placeholder="Ej: 1.1.1" required>
        </div>

        <div class="col-md-3">
          <label class="fw-bold small text-muted">CRITERIO</label>
          <input type="text" id="criterio" class="form-control" placeholder="Ej: ..." required>
        </div>

        <div class="col-md-3">
          <label class="fw-bold small text-muted">MODO DE VERIFICACIÓN</label>
          <input type="text" id="modo" class="form-control" placeholder="Ej: ..." required>
        </div>

      </div>

      <!-- Botones a la izquierda (igual a modulos) -->
      <div class="mt-3">
        <button type="button" id="btnAgregar" class="btn btn-success px-4 shadow-sm">Guardar</button>
        <button type="button" class="btn btn-outline-secondary px-4" id="btnLimpiar">Limpiar</button>
      </div>
    </form>

    <!-- TABLE -->
    <div class="card-shadow border overflow-hidden">
      <div class="table-scroll-container">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark text-uppercase small">
            <tr>
              <th>CICLO PHVA</th>
              <th>CATEGORÍA</th>
              <th>TIPO</th>
              <th>ITEM DEL ESTÁNDAR</th>
              <th>ITEM</th>
              <th>CRITERIO</th>
              <th>MODO DE VERIFICACIÓN</th>
              <th>ESTADO</th>
            </tr>
          </thead>
          <tbody id="tablaBody">
            <tr id="rowEmpty">
              <td colspan="8" class="text-center text-muted py-4">No hay registros.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <script>
    const $ = (id) => document.getElementById(id);

    const btnAgregar = $("btnAgregar");
    const btnLimpiar = $("btnLimpiar");
    const tablaBody = $("tablaBody");

    function limpiar() {
      $("formItem1072").reset();
      $("status").checked = true;
    }

    btnLimpiar.addEventListener("click", limpiar);

    btnAgregar.addEventListener("click", () => {
      const ciclo = $("cicloPHVA").value;
      const categoria = $("categoria").value;
      const tipo = $("tipo").value;
      const itemEstandar = $("itemEstandar").value.trim();
      const item = $("item").value.trim();
      const criterio = $("criterio").value.trim();
      const modo = $("modo").value.trim();
      const estado = $("status").checked ? "Activo" : "Inactivo";

      if (!ciclo || !categoria || !tipo || !itemEstandar || !item || !criterio || !modo) {
        alert("Completa todos los campos obligatorios.");
        return;
      }

      const empty = document.getElementById("rowEmpty");
      if (empty) empty.remove();

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${ciclo}</td>
        <td>${categoria}</td>
        <td>${tipo}</td>
        <td>${itemEstandar}</td>
        <td>${item}</td>
        <td>${criterio}</td>
        <td>${modo}</td>
        <td>
          <span class="${estado === "Activo" ? "status-label-active" : "status-label-inactive"}">
            ${estado}
          </span>
        </td>
      `;

      tablaBody.appendChild(tr);
      limpiar();
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
