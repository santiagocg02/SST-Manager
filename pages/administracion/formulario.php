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
</head>

<body class="cal-wrap">
  <div class="container-fluid">

    <h2 class="mb-4">
      <i class="fa-solid fa-rectangle-list me-2" style="color: var(--primary-blue);"></i>Formularios
    </h2>

    <div class="bg-white p-4 rounded card-shadow mb-4 border">
      <form id="formFormularios">
        <input type="hidden" id="id_formulario">

        <div class="row g-3 align-items-end">

          <div class="col-md-6">
            <label class="fw-bold small text-muted text-uppercase">Nombre formulario</label>
            <input type="text" id="nombre" class="form-control" placeholder="Ej: Lista de Chequeo SST">
          </div>

          <div class="col-md-4">
            <label class="fw-bold small text-muted text-uppercase">Tipo norma</label>
            <select id="tipo_norma" class="form-select">
              <option value="" selected disabled>Seleccione</option>
              <option value="Guía RUC">Guía RUC</option>
              <option value="Resolución 0312 / 1072">Resolución 0312 / 1072</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="fw-bold small d-block text-muted text-uppercase">Estado</label>
            <label class="switch mt-1">
              <input type="checkbox" id="estado" checked>
              <span class="slider"></span>
            </label>
          </div>

        </div>

        <div class="mt-3">
          <button class="btn btn-success px-4 shadow-sm" id="btnGuardar" type="button">
            <i class="fa-solid fa-save me-2"></i>Guardar
          </button>
          <button class="btn btn-outline-secondary px-4" id="btnLimpiar" type="button">Limpiar</button>
        </div>
      </form>
    </div>

    <div class="card-shadow border overflow-hidden">
      <div class="table-scroll-container">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark text-uppercase small">
            <tr>
              <th>Nombre</th>
              <th>Tipo norma</th>
              <th class="text-center">Estado</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody id="tablaBody">
            <tr>
              <td colspan="4" class="text-center text-muted py-4">Cargando...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <script>
    const $ = (id) => document.getElementById(id);

    // ✅ Ajusta esto a tu ruta real del backend
    const API_BASE = "http://localhost/sstmanager-backend/public/index.php?table=formularios";

    function setLoading(msg="Cargando...") {
      $("tablaBody").innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">${msg}</td></tr>`;
    }

    function renderTabla(rows) {
      const tbody = $("tablaBody");
      tbody.innerHTML = "";

      if (!Array.isArray(rows) || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">No hay registros.</td></tr>`;
        return;
      }

      rows.forEach(r => {
        const estadoTxt = (parseInt(r.estado) === 1) ? "Activo" : "Inactivo";
        const estadoClass = (parseInt(r.estado) === 1) ? "status-label-active" : "status-label-inactive";

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="fw-semibold">${escapeHtml(r.nombre ?? "")}</td>
          <td>${escapeHtml(r.tipo_norma ?? "")}</td>
          <td class="text-center">
            <span class="${estadoClass}">${estadoTxt}</span>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-light border shadow-sm" type="button"
              data-id="${r.id_formulario}"
              data-nombre="${escapeAttr(r.nombre ?? "")}"
              data-tipo="${escapeAttr(r.tipo_norma ?? "")}"
              data-estado="${parseInt(r.estado) === 1 ? "1":"0"}"
              onclick="cargarEdicion(this)">
              <i class="fa-solid fa-pencil text-warning"></i>
            </button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    function limpiar() {
      $("id_formulario").value = "";
      $("nombre").value = "";
      $("tipo_norma").value = "";
      $("estado").checked = true;

      $("btnGuardar").innerHTML = `<i class="fa-solid fa-save me-2"></i>Guardar`;
      $("btnGuardar").className = "btn btn-success px-4 shadow-sm";
    }

    function cargarEdicion(btn) {
      $("id_formulario").value = btn.dataset.id;
      $("nombre").value = btn.dataset.nombre;
      $("tipo_norma").value = btn.dataset.tipo;
      $("estado").checked = (btn.dataset.estado === "1");

      $("btnGuardar").innerHTML = `<i class="fa-solid fa-sync me-2"></i>Actualizar`;
      $("btnGuardar").className = "btn btn-primary px-4 shadow-sm";

      window.scrollTo({ top: 0, behavior: "smooth" });
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
      const tipo = $("tipo_norma").value;
      const estado = $("estado").checked ? 1 : 0;

      if (!nombre || !tipo) {
        alert("Completa Nombre y Tipo norma.");
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

        limpiar();
        listar();
      } catch (e) {
        console.error(e);
        alert(e.message || "Error");
      }
    }

    function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, s => ({
        "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
      }[s]));
    }
    function escapeAttr(str) {
      return String(str).replace(/"/g, "&quot;");
    }

    $("btnLimpiar").addEventListener("click", limpiar);
    $("btnGuardar").addEventListener("click", guardarOActualizar);

    listar();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
