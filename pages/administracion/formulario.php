<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../index.php");
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SST Manager - Formularios</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/main-style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    td.actions-cell { vertical-align: middle; }
  </style>
</head>

<body class="cal-wrap">
  <div class="container-fluid">

    <h2 class="mb-4">
      <i class="fa-solid fa-rectangle-list me-2" style="color: var(--primary-blue);"></i>
      Formularios
    </h2>

    <!-- CARD FORM (COMO IMAGEN 1: NOMBRE + ESTADO + GUARDAR/LIMPIAR) -->
    <div class="bg-white p-4 rounded card-shadow mb-4 border">
      <form id="formFormularios" onsubmit="return false;">
        <input type="hidden" id="id_formulario">

        <div class="row g-3 align-items-end">
          <div class="col-md-8">
            <label class="fw-bold small text-muted text-uppercase">Nombre formulario</label>
            <input type="text" id="nombre" class="form-control" placeholder="Ej: Lista de Chequeo SST">
          </div>

          <div class="col-md-4">
            <label class="fw-bold small d-block text-muted text-uppercase">Estado</label>
            <label class="switch mt-1">
              <input type="checkbox" id="estado" checked>
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <!-- Botones a la izquierda -->
        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-success px-4 shadow-sm" id="btnGuardar" type="button">
            <i class="fa-solid fa-save me-2"></i>Guardar
          </button>

          <button class="btn btn-outline-secondary px-4" id="btnLimpiar" type="button">
            Limpiar
          </button>
        </div>

        <!-- Hidden: si tu backend exige tipo_norma, lo enviamos con default -->
        <input type="hidden" id="tipo_norma" value="Guía RUC">
      </form>
    </div>

    <!-- TABLE (NOMBRE / ESTADO / ACCIONES) -->
    <div class="card-shadow border overflow-hidden">
      <div class="table-scroll-container">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark text-uppercase small">
            <tr>
              <th>NOMBRE</th>
              <th class="text-center">ESTADO</th>
              <th class="text-center" style="width:240px;">ACCIONES</th>
            </tr>
          </thead>
          <tbody id="tablaBody">
            <tr>
              <td colspan="3" class="text-center text-muted py-4">Cargando...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL: ASOCIAR ITEMS (HOOK LISTO) -->
  <div class="modal fade" id="modalAsociar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fa-solid fa-link me-2"></i> Asocie items al formulario
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="asoc_id_formulario">

          <div class="alert alert-info mb-3">
            Aquí puedes asociar items al formulario seleccionado.
            <br>
            <strong>Nota:</strong> pásame el endpoint/tabla de relación y te dejo esto 100% funcional (listar + guardar).
          </div>

          <!-- placeholder UI -->
          <div class="row g-3">
            <div class="col-md-6">
              <label class="fw-bold small text-muted text-uppercase">Tipo de items</label>
              <select id="asoc_tipo" class="form-select">
                <option value="guia_ruc">Guía RUC</option>
                <option value="item1072">Item 1072</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="fw-bold small text-muted text-uppercase">Buscar</label>
              <input id="asoc_buscar" class="form-control" placeholder="Escribe para filtrar...">
            </div>
          </div>

          <div class="mt-3 border rounded p-2" style="max-height:280px; overflow:auto;">
            <div class="text-muted small">
              (Aquí cargaríamos los items para seleccionar. Falta conectar endpoint.)
            </div>
            <ul class="list-group mt-2" id="asoc_lista">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Ejemplo Item 1
                <input type="checkbox">
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Ejemplo Item 2
                <input type="checkbox">
              </li>
            </ul>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" id="btnGuardarAsociacion" type="button">
            <i class="fa-solid fa-save me-2"></i>Guardar asociación
          </button>
        </div>

      </div>
    </div>
  </div>

  <script>
    const $ = (id) => document.getElementById(id);

    // ✅ tu API actual
    const API_BASE = "http://localhost/sstmanager-backend/public/index.php?table=formularios";

    function setLoading(msg="Cargando...") {
      $("tablaBody").innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">${msg}</td></tr>`;
    }

    function escapeHtml(str) {
      return String(str ?? "").replace(/[&<>"']/g, s => ({
        "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
      }[s]));
    }
    function escapeAttr(str) {
      return String(str ?? "").replace(/"/g, "&quot;");
    }

    function estadoBadge(estado) {
      const on = (parseInt(estado) === 1);
      const cls = on ? "status-label-active" : "status-label-inactive";
      return `<span class="${cls}">${on ? "Activo" : "Inactivo"}</span>`;
    }

    function limpiar() {
      $("id_formulario").value = "";
      $("nombre").value = "";
      $("estado").checked = true;

      $("btnGuardar").innerHTML = `<i class="fa-solid fa-save me-2"></i>Guardar`;
      $("btnGuardar").className = "btn btn-success px-4 shadow-sm";
    }

    function cargarEdicion(btn) {
      $("id_formulario").value = btn.dataset.id;
      $("nombre").value = btn.dataset.nombre;
      $("estado").checked = (btn.dataset.estado === "1");

      $("btnGuardar").innerHTML = `<i class="fa-solid fa-sync me-2"></i>Actualizar`;
      $("btnGuardar").className = "btn btn-primary px-4 shadow-sm";

      window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function abrirAsociarItems(idFormulario) {
      $("asoc_id_formulario").value = idFormulario;
      const modal = new bootstrap.Modal(document.getElementById("modalAsociar"));
      modal.show();
    }

    function renderTabla(rows) {
      const tbody = $("tablaBody");
      tbody.innerHTML = "";

      if (!Array.isArray(rows) || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">No hay registros.</td></tr>`;
        return;
      }

      rows.forEach(r => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="fw-semibold">${escapeHtml(r.nombre ?? "")}</td>

          <td class="text-center">
            ${estadoBadge(r.estado)}
          </td>

          <td class="text-center actions-cell">
            <div class="d-inline-flex align-items-center justify-content-center gap-2">

              <!-- BOTÓN VERDE (como tu imagen 1) -->
              <button class="btn btn-success btn-sm rounded-pill d-flex align-items-center gap-2"
                type="button"
                title="Asocie items"
                onclick="abrirAsociarItems('${escapeAttr(r.id_formulario)}')">
                <i class="fa-solid fa-link"></i>
                Asocie items
              </button>

              <!-- EDITAR -->
              <button class="btn btn-sm btn-light border shadow-sm" type="button"
                data-id="${escapeAttr(r.id_formulario)}"
                data-nombre="${escapeAttr(r.nombre ?? "")}"
                data-estado="${parseInt(r.estado) === 1 ? "1":"0"}"
                onclick="cargarEdicion(this)"
                title="Editar">
                <i class="fa-solid fa-pencil text-warning"></i>
              </button>

            </div>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    async function listar() {
      setLoading("Cargando...");
      try {
        const res = await fetch(API_BASE, { headers: { "Accept": "application/json" } });
        const json = await res.json();
        if (!json || json.status !== 200) throw new Error(json?.error || "Error listando");
        renderTabla(json.data || []);
      } catch (e) {
        console.error(e);
        setLoading("Error cargando datos");
      }
    }

    async function guardarOActualizar() {
      const id = parseInt($("id_formulario").value || "0", 10);
      const nombre = $("nombre").value.trim();
      const estado = $("estado").checked ? 1 : 0;

      const tipo = $("tipo_norma").value || "Guía RUC";

      if (!nombre) {
        Swal.fire("Falta el nombre", "Completa el Nombre del formulario.", "warning");
        return;
      }

      const payload = { nombre, tipo_norma: tipo, estado };

      const url = id > 0
        ? `${API_BASE}&action=update&id=${encodeURIComponent(id)}`
        : API_BASE;

      try {
        const res = await fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json", "Accept": "application/json" },
          body: JSON.stringify(payload)
        });

        const json = await res.json();
        if (!json || (json.status !== 200 && json.status !== 201)) {
          throw new Error(json?.error || "No se pudo guardar");
        }

        Swal.fire("✅ Listo", id > 0 ? "Formulario actualizado." : "Formulario creado.", "success");
        limpiar();
        listar();
      } catch (e) {
        console.error(e);
        Swal.fire("Error", e.message || "Error", "error");
      }
    }

    // Hook del modal (aún sin endpoint real)
    $("btnGuardarAsociacion").addEventListener("click", () => {
      Swal.fire("Pendiente", "Pásame el endpoint de asociación de items y te lo dejo funcionando completo.", "info");
    });

    $("btnLimpiar").addEventListener("click", limpiar);
    $("btnGuardar").addEventListener("click", guardarOActualizar);

    // Exponer funciones usadas inline
    window.cargarEdicion = cargarEdicion;
    window.abrirAsociarItems = abrirAsociarItems;

    listar();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
