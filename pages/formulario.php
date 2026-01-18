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
  <title>Formularios</title>

  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    html, body { height: 100%; margin: 0; }

    .form-wrap{
      padding: 18px;
      background:#f4f4f1;
      min-height: 100%;
      box-sizing: border-box;
      overflow-y: auto;
      overflow-x: hidden;
      width: 100%;
    }

    .form-grid{
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
      align-items: end;
      width: 100%;
    }

    @media (max-width: 1100px){
      .form-grid{ grid-template-columns: repeat(2, minmax(200px, 1fr)); }
      .form-actions{ grid-column: 1 / -1; justify-content: flex-end; }
    }

    @media (max-width: 700px){
      .form-grid{ grid-template-columns: 1fr; }
      .form-actions{ justify-content: stretch; }
      .btn-pill{ width: 100%; }
    }

    .form-field label{
      font-weight:700;
      letter-spacing:.4px;
      font-size:14px;
    }

    .form-field input,
    .form-field select{
      width:100%;
      height: 40px;
      border: 2px solid #2b2b2b;
      background:#efefef;
      padding: 0 10px;
      border-radius: 2px;
      box-sizing: border-box;
    }

    .estado-field{
      margin-top: 6px;
    }

    .form-actions{
      display:flex;
      gap:16px;
      justify-content:flex-end;
      align-items:center;
      margin-top: 10px;
    }

    .btn-pill{
      border:0;
      padding: 10px 34px;
      border-radius: 999px;
      font-weight:700;
      cursor:pointer;
    }

    .btn-add{ background:#2fb14a; color:#fff; }
    .btn-cancel{ background:#0b4f7a; color:#fff; }

    .form-table{
      margin-top: 16px;
      border-radius: 4px;
      overflow:hidden;
      border: 1px solid #cfcfcf;
      width: 100%;
    }

    .form-table table{
      width:100%;
      border-collapse: collapse;
      background:#d9d9d9;
      table-layout: fixed;
    }

    .form-table th,
    .form-table td{
      border-right: 2px solid #f0f0f0;
      padding: 12px;
      vertical-align: top;
    }

    .form-table th{ background:#d0d0d0; font-weight:800; }
  </style>
</head>

<body>
<div class="form-wrap">

  <div class="form-grid">

    <div class="form-field">
      <label>NOMBRE FORMULARIO</label>
      <input type="text" id="nombreFormulario">
    </div>

    <div class="form-field">
      <label>TIPO NORMA</label>
      <select id="tipoNorma">
        <option value=""></option>
        <option>ruc/1072</option>
        <option>sst</option>
        <option>calidad</option>
      </select>
    </div>

    <div class="form-field">
      <label>ITEM</label>
      <select id="item">
        <option value=""></option>
        <option>Item 1072</option>
        <option>Guía RUC</option>
        <option>PHVA</option>
      </select>
    </div>

    <div class="form-field">
      <label>RANGO CALIFICACION</label>
      <select id="rangoCalificacion">
        <option value=""></option>
        <option>0 - 25</option>
        <option>26 - 50</option>
        <option>51 - 75</option>
        <option>76 - 100</option>
      </select>
    </div>

    <div class="form-field estado-field">
      <label>ESTADO</label>
      <select id="estado">
        <option value=""></option>
        <option>Activo</option>
        <option>Pendiente</option>
        <option>Inactivo</option>
      </select>
    </div>

    <div class="form-actions" style="grid-column: 3 / span 2;">
      <button class="btn-pill btn-add" id="btnAgregar" type="button">Agregar</button>
      <button class="btn-pill btn-cancel" id="btnCancelar" type="button">Cancelar</button>
    </div>

  </div>

  <div class="form-table">
    <table>
      <thead>
        <tr>
          <th style="width:20%;">NOMBRE<br>FORMULARIO</th>
          <th style="width:20%;">TIPO NORMA</th>
          <th style="width:25%;">ITEM</th>
          <th style="width:20%;">RANGO CALIFIACION</th>
          <th style="width:15%;">ESTADO</th>
        </tr>
      </thead>
      <tbody id="tablaBody"></tbody>
    </table>
  </div>

</div>

<script>
  const $ = (id) => document.getElementById(id);

  const btnAgregar = $("btnAgregar");
  const btnCancelar = $("btnCancelar");
  const tablaBody = $("tablaBody");

  function limpiar(){
    ["nombreFormulario","tipoNorma","item","rangoCalificacion","estado"]
      .forEach(id => $(id).value = "");
  }

  btnCancelar.addEventListener("click", limpiar);

  btnAgregar.addEventListener("click", () => {
    const nombre = $("nombreFormulario").value.trim();
    const tipo = $("tipoNorma").value;
    const item = $("item").value;
    const rango = $("rangoCalificacion").value;
    const estado = $("estado").value;

    if (!nombre || !tipo || !item || !rango || !estado) {
      alert("Completa todos los campos obligatorios.");
      return;
    }

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${nombre}</td>
      <td>${tipo}</td>
      <td>${item}</td>
      <td>${rango}</td>
      <td>${estado}</td>
    `;
    tablaBody.appendChild(tr);
    limpiar();
  });
</script>

</body>
</html>
