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
  <title>SST Manager - Guía RUC</title>

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/main-style.css">
</head>

<body class="cal-wrap">
  <div class="container-fluid">

    <h2 class="mb-4">
      <i class="fa-solid fa-clipboard-list me-2" style="color: var(--primary-blue);"></i>Guía RUC
    </h2>

    <!-- ================= FORMULARIO (CARD) ================= -->
    <div class="bg-white p-4 rounded card-shadow mb-4 border">
      <form id="formGuiaRuc">
        <div class="row g-3 align-items-end">

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Item del estándar</label>
            <input type="text" id="itemEstandar" class="form-control" placeholder="">
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Frecuencia</label>
            <select id="frecuencia" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>Diaria</option>
              <option>Semanal</option>
              <option>Mensual</option>
              <option>Trimestral</option>
              <option>Semestral</option>
              <option>Anual</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Responsable</label>
            <select id="responsable" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>Administrador</option>
              <option>SST</option>
              <option>Talento Humano</option>
              <option>Operaciones</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Recurso</label>
            <select id="recurso" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>Humano</option>
              <option>Tecnológico</option>
              <option>Financiero</option>
              <option>Infraestructura</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Estándar</label>
            <select id="estandar" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option>RUC</option>
              <option>SST</option>
              <option>Calidad</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="fw-bold small text-muted text-uppercase">Soporte</label>
            <input type="text" id="soporte" class="form-control" placeholder="">
          </div>

          <!-- ✅ ESTE ES EL HUECO (debajo de RESPONSABLE como tu recuadro) -->
          <div class="col-md-3">
            <label class="fw-bold small d-block text-muted text-uppercase">Estado</label>
            <label class="switch mt-1">
              <input type="checkbox" id="status" checked>
              <span class="slider"></span>
            </label>
          </div>

          <!-- col-md-3 vacío para conservar grid alineado (opcional) -->
          <div class="col-md-3"></div>

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
              <th>Item del estándar</th>
              <th>Frecuencia</th>
              <th>Estándar</th>
              <th>Responsable</th>
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

    function limpiar() {
      ["itemEstandar","frecuencia","responsable","recurso","estandar","soporte"]
        .forEach(id => $(id).value = "");
      $("status").checked = true;
    }

    $("btnLimpiar").addEventListener("click", limpiar);

    $("btnGuardar").addEventListener("click", () => {
      const item = $("itemEstandar").value.trim();
      const frecuencia = $("frecuencia").value;
      const estandar = $("estandar").value;
      const responsable = $("responsable").value;
      const estadoTxt = $("status").checked ? "Activo" : "Inactivo";

      if (!item || !frecuencia || !estandar || !responsable) {
        alert("Completa los campos obligatorios: Item, Frecuencia, Estándar y Responsable.");
        return;
      }

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="fw-semibold">${item}</td>
        <td>${frecuencia}</td>
        <td>${estandar}</td>
        <td>${responsable}</td>
        <td>
          <span class="${$("status").checked ? "status-label-active" : "status-label-inactive"}">
            ${estadoTxt}
          </span>
        </td>
      `;

      const tbody = $("tablaBody");
      // si está el "No hay registros." lo quitamos
      if (tbody.children.length === 1 && tbody.children[0].querySelector("td[colspan]")) tbody.innerHTML = "";
      tbody.appendChild(tr);

      limpiar();
    });
  </script>
</body>

</html>