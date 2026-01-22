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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Formulario RUC</title>

  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
  /* IMPORTANTE: que el alto se base en el iframe, no en 100vh del navegador */
  html, body { height: 100%; margin: 0; }

 .ruc-wrap{
  padding: 18px;
  background:#f4f4f1;
  min-height: 100%;
  box-sizing: border-box;
  overflow-y: auto;   /* solo scroll vertical */
  overflow-x: hidden; /* evita que se corte/scroll horizontal */
  width: 100%;
}


.ruc-grid{
  display: grid;
  grid-template-columns: repeat(4, minmax(180px, 1fr));
  gap: 16px;
  align-items: end;
  width: 100%;
  grid-template-columns: repeat(4, minmax(0, 1fr)); /* importante: minmax(0,1fr) */
}


    @media (max-width: 1100px){
  .ruc-grid{ grid-template-columns: repeat(2, minmax(200px, 1fr)); }
  .ruc-actions{ grid-column: 1 / -1; justify-content: flex-end; }
}

    @media (max-width: 700px){
    .ruc-grid{ grid-template-columns: 1fr; }
    .ruc-actions{ justify-content: stretch; }
    .btn-pill{ width: 100%; }
    }


  .ruc-field label { font-weight:700; letter-spacing:.4px; font-size:14px; }

  .ruc-field input, .ruc-field select {
    width:100%;
    height: 40px;
    border: 2px solid #2b2b2b;
    background:#efefef;
    padding: 0 10px;
    border-radius: 2px;
    box-sizing: border-box;
  }

  .ruc-actions {
    display:flex;
    gap:16px;
    justify-content:flex-end;
    align-items:center;
    margin-top: 10px;
  }

  .btn-pill { border:0; padding: 10px 34px; border-radius: 999px; font-weight:700; cursor:pointer; }
  .btn-add { background:#2fb14a; color:#fff; }
  .btn-cancel { background:#0b4f7a; color:#fff; }

  .ruc-table {
    margin-top: 16px;
    border-radius: 4px;
    overflow:hidden;
    border: 1px solid #cfcfcf;
    width: 100%;
  }

  .ruc-table table { width:100%; border-collapse: collapse; background:#d9d9d9; width: 100%; table-layout: fixed; /* reparte mejor el ancho */ }
  .ruc-table th, .ruc-table td { border-right: 2px solid #f0f0f0; padding: 12px; vertical-align: top; }
  .ruc-table th { background:#d0d0d0; font-weight:800; }

  /* QUITAMOS la altura fija que te genera huecos/limitaciones */
  .ruc-table td { height: auto; } /* antes: 260px */
</style>

</head>

<body style="margin:0;">
  <div class="ruc-wrap">

    <div class="ruc-grid">
      <div class="ruc-field">
        <label>ITEM DEL ESTANDAR</label>
        <input type="text" id="itemEstandar" placeholder="">
      </div>

      <div class="ruc-field">
        <label>FRECUENCIA</label>
        <select id="frecuencia">
          <option value="" selected></option>
          <option>Diaria</option>
          <option>Semanal</option>
          <option>Mensual</option>
          <option>Trimestral</option>
          <option>Semestral</option>
          <option>Anual</option>
        </select>
      </div>

      <div class="ruc-field">
        <label>RESPONSABLE</label>
        <select id="responsable">
          <option value="" selected></option>
          <option>Administrador</option>
          <option>SST</option>
          <option>Talento Humano</option>
          <option>Operaciones</option>
        </select>
      </div>

      <div class="ruc-field">
        <label>RESCURSO</label>
        <select id="recurso">
          <option value="" selected></option>
          <option>Humano</option>
          <option>Tecnológico</option>
          <option>Financiero</option>
          <option>Infraestructura</option>
        </select>
      </div>

      <div class="ruc-field">
        <label>ESTANDAR</label>
        <select id="estandar">
          <option value="" selected></option>
          <option>RUC</option>
          <option>SST</option>
          <option>Calidad</option>
        </select>
      </div>

      <div class="ruc-field">
        <label>SOPORTE</label>
        <input type="text" id="soporte" placeholder="">
      </div>

      <div class="ruc-field">
        <label>ESTADO</label>
        <select id="estado">
          <option value="" selected></option>
          <option>Activo</option>
          <option>Pendiente</option>
          <option>Inactivo</option>
        </select>
      </div>

      <div class="ruc-actions" style="grid-column: 3 / span 2;">
        <button class="btn-pill btn-add" id="btnAgregar" type="button">Agregar</button>
        <button class="btn-pill btn-cancel" href="menu-admin.php" id="btnCancelar" type="button">Cancelar</button>
      </div>
    </div>

    <div class="ruc-table">
      <table>
        <thead>
          <tr>
            <th style="width:22%;">ITEM DEL<br>ESTANDAR</th>
            <th style="width:22%;">FRECUENCIA</th>
            <th style="width:22%;">ESTANDAR</th>
            <th style="width:22%;">RESPONSABLE</th>
            <th style="width:12%;">ESTADO</th>
          </tr>
        </thead>
        <tbody id="tablaBody">
          <!-- filas -->
        </tbody>
      </table>
    </div>

  </div>

  <script>
    const $ = (id) => document.getElementById(id);

    const btnAgregar = $("btnAgregar");
    const btnCancelar = $("btnCancelar");
    const tablaBody = $("tablaBody");

    function limpiar() {
      ["itemEstandar","frecuencia","responsable","recurso","estandar","soporte","estado"]
        .forEach(id => $(id).value = "");
    }

    btnCancelar.addEventListener("click", limpiar);

    btnAgregar.addEventListener("click", () => {
      const item = $("itemEstandar").value.trim();
      const frecuencia = $("frecuencia").value;
      const estandar = $("estandar").value;
      const responsable = $("responsable").value;
      const estado = $("estado").value;

      if (!item || !frecuencia || !estandar || !responsable || !estado) {
        alert("Completa los campos obligatorios: Item, Frecuencia, Estándar, Responsable y Estado.");
        return;
      }

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${item}</td>
        <td>${frecuencia}</td>
        <td>${estandar}</td>
        <td>${responsable}</td>
        <td>${estado}</td>
      `;
      tablaBody.appendChild(tr);
      limpiar();
    });
  </script>
</body>
</html>
