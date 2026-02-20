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
    .btn-icon{display:inline-flex;align-items:center;gap:8px}
    .btn-pill{border-radius:999px}
    .hidden{display:none!important}
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

    <!-- CARD FORM: SOLO NOMBRE + GUARDAR/LIMPIAR -->
    <div class="bg-white p-4 rounded card-shadow mb-4 border">
      <form id="formCalificacion" onsubmit="return false;">

        <div class="row g-3">
          <div class="col-md-12">
            <label class="fw-bold small text-muted text-uppercase">Nombre calificación</label>
            <input id="nombre" class="form-control" type="text" placeholder="Ej: Cumple / No cumple">
          </div>
        </div>

        <!-- BOTONES ABAJO A LA IZQUIERDA -->
        <div class="mt-3 d-flex gap-2">
          <button type="button" class="btn btn-success px-4 shadow-sm btn-icon" id="btnGuardarPadre">
            <i class="fa-solid fa-floppy-disk"></i>
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
              <th>CALIFICACIÓN</th>
              <th>DESCRIPCIÓN</th>
              <th width="120">VALOR</th>
              <th class="text-center" width="120">ESTADO</th>
              <th class="text-center" width="170">ACCIONES</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Cargando...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- MODAL (Nombre + Detalle) -->
  <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">
            <i class="fa-solid fa-square-pen me-2"></i>
            Calificación
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-12">
              <label class="fw-bold small text-muted text-uppercase">Nombre calificación</label>
              <input id="modalNombre" class="form-control" type="text" placeholder="Ej: Cumple / No cumple">
            </div>

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
              <div class="small text-muted mt-1" id="lblEstadoModal">Activo</div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <!-- VERDE (mismo estilo que agregar calificación) -->
          <button class="btn btn-success btn-icon" id="btnGuardarModal">
            <i class="fa-solid fa-floppy-disk"></i>
            Guardar
          </button>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const API_URL = "http://localhost/SSTMANAGER-BACKEND/public/index.php?table=calificaciones";
    const $ = (id) => document.getElementById(id);

    const modalEl = document.getElementById("modalDetalle");
    const modal = new bootstrap.Modal(modalEl);

    // context:
    // add_detail: agrega/actualiza detalle para una calificación existente
    // edit: edita nombre + detalle
    // edit_no_item: edita solo nombre
    let editContext = null;

    function esc(str) {
      return String(str ?? "").replace(/[&<>"']/g, m => ({
        "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
      })[m]);
    }

    function getEstadoDetalle(){ return $("status").checked ? "Activo" : "Inactivo"; }
    function syncEstadoLabel(){ $("lblEstadoModal").textContent = getEstadoDetalle(); }
    $("status").addEventListener("change", syncEstadoLabel);

    function limpiarModal(){
      $("modalNombre").value = "";
      $("descripcion").value = "";
      $("valor").value = "";
      $("status").checked = true;
      syncEstadoLabel();
    }

    function limpiarForm(){ $("nombre").value = ""; }

    function badgeEstado(estado){
      const st = (estado ?? "Activo").toString().toLowerCase();
      const ok = st.includes("activo");
      return `<span class="badge ${ok ? "bg-success" : "bg-secondary"}">${ok ? "Activo" : "Inactivo"}</span>`;
    }

    // ================== TABLA ==================
    function renderRows(data){
      const tbody = $("tbody");

      if (!Array.isArray(data) || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No hay registros.</td></tr>`;
        return;
      }

      const filas = [];

      data.forEach(r=>{
        const idCal = r.id ?? r.id_calificacion ?? "";
        const nombre = r.nombre ?? "";
        const items = Array.isArray(r.items) ? r.items : [];

        // Para UI simple: mostramos 1ra fila de detalle si existe
        const it = items[0] ?? null;

        const desc = it?.descripcion ?? "";
        const val  = it?.valor ?? "";
        const est  = it?.estado ?? r.estado ?? "Activo";
        const idDet = it?.id_detalle ?? it?.id ?? "";

        filas.push(`
          <tr>
            <td>${esc(idCal)}</td>
            <td>${esc(nombre)}</td>
            <td>${esc(desc || "—")}</td>
            <td>${esc(val || "—")}</td>
            <td class="text-center">${badgeEstado(est)}</td>
            <td class="d-flex align-items-center justify-content-center gap-2">

              <!-- BOTÓN AGREGAR CALIFICACIÓN -->
              <button class="btn btn-success btn-sm rounded-pill d-flex align-items-center gap-2"
                      title="Agregar calificación"
                      onclick="abrirModalAgregarDetalle('${esc(idCal)}','${esc(nombre)}','${esc(idDet)}','${esc(desc)}','${esc(val)}','${esc(est)}')">

                <i class="fa-solid fa-clipboard-check"></i>
                Agregar
              </button>

              <!-- BOTÓN EDITAR -->
              <button class="btn btn-sm btn-light border shadow-sm" type="button"
                      title="Editar"
                      onclick="abrirModalEditar('${esc(idCal)}','${esc(idDet)}','${esc(nombre)}','${esc(desc)}','${esc(val)}','${esc(est)}')">

                 <i class="fa-solid fa-pencil text-warning"></i>
              </button>

            </td>
          </tr>
        `);
      });

      tbody.innerHTML = filas.join("");
    }

    async function cargarTabla(){
      const tbody = $("tbody");
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Cargando...</td></tr>`;

      try{
        const res = await fetch(API_URL, { method:"GET", mode:"cors" });
        const data = await res.json();
        renderRows(data);
      }catch(e){
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Error cargando: ${esc(e.message)}</td></tr>`;
      }
    }

    // ================== BOTONES FORM ==================
    // Guardar (PADRE): crea calificación sin detalle
    $("btnGuardarPadre").onclick = async () => {
      const nombre = $("nombre").value.trim();
      if(!nombre) return Swal.fire("Falta el nombre","Ingresa el nombre.","warning");

      const payload = { nombre, estado: "Activo" };

      try{
        const res = await fetch(API_URL, {
          method:"POST",
          mode:"cors",
          headers:{ "Content-Type":"application/json" },
          body: JSON.stringify(payload)
        });

        const data = await res.json();
        if(!res.ok) return Swal.fire("Error", data.error || "No se pudo guardar.", "error");

        Swal.fire("✅ Guardado", `ID: ${data.id}`, "success");
        limpiarForm();
        await cargarTabla();
      }catch(e){
        Swal.fire("Error", e.message, "error");
      }
    };

    $("btnCancelar").onclick = () => limpiarForm();

    // ================== MODALES ==================

    // Botón verde en ACCIONES: abre modal para agregar/actualizar detalle
    window.abrirModalAgregarDetalle = (idCal, nombre, idDet, desc, valor, estado) => {
      editContext = { mode: "add_detail", id_calificacion: idCal, id_detalle: idDet || null };

      $("modalTitle").innerHTML = `<i class="fa-solid fa-user me-2"></i> SST · Detalle de calificación`;
      $("btnGuardarModal").className = "btn btn-success btn-icon";
      $("btnGuardarModal").innerHTML = `<i class="fa-solid fa-plus me-1"></i> Guardar`;

      limpiarModal();
      $("modalNombre").value = nombre ?? "";

      // Si ya existe detalle, precarga para editarlo
      $("descripcion").value = desc ?? "";
      $("valor").value = valor ?? "";
      $("status").checked = (estado ?? "Activo").toLowerCase().includes("activo");
      syncEstadoLabel();

      modal.show();
    };

    // Editar (lápiz): nombre + detalle
    window.abrirModalEditar = (idCal, idDet, nombre, desc, valor, estado) => {
      // Si no hay detalle, se comporta como editar solo nombre (y permite dejar desc/valor vacíos)
      editContext = { mode: idDet ? "edit" : "edit_no_item", id_calificacion: idCal, id_detalle: idDet || null };

      $("modalTitle").innerHTML = `<i class="fa-solid fa-pen-to-square me-2"></i> Editar calificación`;
      $("btnGuardarModal").className = "btn btn-success btn-icon";
      $("btnGuardarModal").innerHTML = `<i class="fa-solid fa-floppy-disk me-1"></i> Actualizar`;

      limpiarModal();
      $("modalNombre").value = nombre ?? "";

      $("descripcion").value = desc ?? "";
      $("valor").value = valor ?? "";
      $("status").checked = (estado ?? "Activo").toLowerCase().includes("activo");
      syncEstadoLabel();

      modal.show();
    };

    // Guardar modal (según contexto)
    $("btnGuardarModal").onclick = async () => {
      const nombre = $("modalNombre").value.trim();
      if(!nombre) return Swal.fire("Falta el nombre","Ingresa el nombre.","warning");

      const desc = $("descripcion").value.trim();
      const valor = $("valor").value;

      const urlPut = (id) => API_URL + "&id=" + encodeURIComponent(id);

      try{
        // 1) Add/Update detalle en calificación existente
        if(editContext?.mode === "add_detail"){
          if(!desc || valor === "") return Swal.fire("Campos incompletos","Completa descripción y valor.","warning");

          const payload = {
            nombre,
            items: [{
              ...(editContext.id_detalle ? { id_detalle: editContext.id_detalle } : {}),
              descripcion: desc,
              valor: parseFloat(valor),
              estado: getEstadoDetalle()
            }]
          };

          const res = await fetch(urlPut(editContext.id_calificacion), {
            method:"PUT",
            mode:"cors",
            headers:{ "Content-Type":"application/json" },
            body: JSON.stringify(payload)
          });

          const data = await res.json();
          if(!res.ok) return Swal.fire("Error", data.error || "No se pudo guardar.", "error");

          modal.hide();
          Swal.fire("✅ Guardado", "Detalle actualizado.", "success");
          await cargarTabla();
          return;
        }

        // 2) Edit solo nombre
        if(editContext?.mode === "edit_no_item"){
          const payload = { nombre };

          const res = await fetch(urlPut(editContext.id_calificacion), {
            method:"PUT",
            mode:"cors",
            headers:{ "Content-Type":"application/json" },
            body: JSON.stringify(payload)
          });

          const data = await res.json();
          if(!res.ok) return Swal.fire("Error", data.error || "No se pudo actualizar.", "error");

          modal.hide();
          Swal.fire("✅ Actualizado", "Nombre actualizado.", "success");
          await cargarTabla();
          return;
        }

        // 3) Edit nombre + detalle
        if(editContext?.mode === "edit"){
          if(!desc || valor === "") return Swal.fire("Campos incompletos","Completa descripción y valor.","warning");

          const payload = {
            nombre,
            items: [{
              id_detalle: editContext.id_detalle,
              descripcion: desc,
              valor: parseFloat(valor),
              estado: getEstadoDetalle()
            }]
          };

          const res = await fetch(urlPut(editContext.id_calificacion), {
            method:"PUT",
            mode:"cors",
            headers:{ "Content-Type":"application/json" },
            body: JSON.stringify(payload)
          });

          const data = await res.json();
          if(!res.ok) return Swal.fire("Error", data.error || "No se pudo actualizar.", "error");

          modal.hide();
          Swal.fire("✅ Actualizado", "Cambios guardados.", "success");
          await cargarTabla();
          return;
        }

        // fallback (no debería)
        Swal.fire("Ups", "No hay contexto de operación.", "warning");

      }catch(e){
        Swal.fire("Error", e.message, "error");
      }
    };

    document.addEventListener("DOMContentLoaded", () => {
      syncEstadoLabel();
      cargarTabla();
    });
  </script>

</body>
</html>
