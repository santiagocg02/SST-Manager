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
  <title>Tamaño Empresa</title>

  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    html, body { height: 100%; margin: 0; }

    .empresa-wrap{
      padding: 18px;
      background:#f4f4f1;
      min-height: 100%;
      box-sizing: border-box;
      overflow-y: auto;
      overflow-x: hidden;
      width: 100%;
    }

    .empresa-grid{
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
      align-items: end;
      width: 100%;
    }

    @media (max-width: 1100px){
      .empresa-grid{ grid-template-columns: repeat(2, minmax(200px, 1fr)); }
      .empresa-actions{ grid-column: 1 / -1; justify-content: flex-end; }
    }

    @media (max-width: 700px){
      .empresa-grid{ grid-template-columns: 1fr; }
      .empresa-actions{ justify-content: stretch; }
      .btn-pill{ width: 100%; }
    }

    .empresa-field label{
      font-weight:700;
      letter-spacing:.4px;
      font-size:14px;
    }

    .empresa-field input,
    .empresa-field select{
      width:100%;
      height: 40px;
      border: 2px solid #2b2b2b;
      background:#efefef;
      padding: 0 10px;
      border-radius: 2px;
      box-sizing: border-box;
    }

    .empresa-actions{
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

    .empresa-table{
      margin-top: 16px;
      border-radius: 4px;
      overflow:hidden;
      border: 1px solid #cfcfcf;
      width: 100%;
    }

    .empresa-table table{
      width:100%;
      border-collapse: collapse;
      background:#d9d9d9;
      table-layout: fixed;
    }

    .empresa-table th,
    .empresa-table td{
      border-right: 2px solid #f0f0f0;
      padding: 12px;
      vertical-align: top;
    }

    .empresa-table th{ background:#d0d0d0; font-weight:800; }
    .empresa-table td{ height:auto; }
  </style>
</head>

<body>
<div class="empresa-wrap">

  <div class="empresa-grid">

    <div class="empresa-field">
      <label>TAMAÑO EMPRESA</label>
      <select id="tamanoEmpresa">
        <option value=""></option>
        <option>Micro</option>
        <option>Pequeña</option>
        <option>Mediana</option>
        <option>Grande</option>
      </select>
    </div>

    <div class="empresa-field">
      <label>CANTIDAD EMPLEADOS</label>
      <input type="number" id="cantidadEmpleados">
    </div>

    <div class="empresa-field">
      <label>CANTIDAD POR SEDE</label>
      <input type="number" id="cantidadSede">
    </div>

    <div class="empresa-field">
      <label>Aplicación</label>
      <select id="aplicacion">
        <option value=""></option>
        <option>Operativa</option>
        <option>Administrativa</option>
        <option>Mixta</option>
      </select>
    </div>

    <div class="empresa-field">
      <label>ESTADO</label>
      <select id="estado">
        <option value=""></option>
        <option>Activo</option>
        <option>Pendiente</option>
        <option>Inactivo</option>
      </select>
    </div>

    <div class="empresa-actions" style="grid-column: 3 / span 2;">
      <button class="btn-pill btn-add" id="btnAgregar" type="button">Agregar</button>
      <button class="btn-pill btn-cancel" id="btnCancelar" type="button">Cancelar</button>
    </div>

  </div>

  <div class="empresa-table">
    <table>
      <thead>
        <tr>
          <th style="width:20%;">TAMAÑP EMPRESA</th>
          <th style="width:20%;">CANTIDAD EMPLEADOS</th>
          <th style="width:20%;">CANTIDAD POR SEDE</th>
          <th style="width:20%;">APLICACION</th>
          <th style="width:20%;">ESTADO</th>
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
    ["tamanoEmpresa","cantidadEmpleados","cantidadSede","aplicacion","estado"]
      .forEach(id => $(id).value = "");
  }

  btnCancelar.addEventListener("click", limpiar);

  btnAgregar.addEventListener("click", () => {
    const tamano = $("tamanoEmpresa").value;
    const empleados = $("cantidadEmpleados").value;
    const sede = $("cantidadSede").value;
    const aplicacion = $("aplicacion").value;
    const estado = $("estado").value;

    if (!tamano || !empleados || !sede || !aplicacion || !estado) {
      alert("Completa todos los campos.");
      return;
    }

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${tamano}</td>
      <td>${empleados}</td>
      <td>${sede}</td>
      <td>${aplicacion}</td>
      <td>${estado}</td>
    `;
    tablaBody.appendChild(tr);
    limpiar();
  });
</script>

</body>
</html>
