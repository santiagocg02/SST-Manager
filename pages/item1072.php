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
  <title>Formulario PHVA</title>

  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    html, body { height: 100%; margin: 0; }

    .phva-wrap{
      padding: 18px;
      background:#f4f4f1;
      min-height: 100%;
      box-sizing: border-box;
      overflow-y: auto;
      overflow-x: hidden;
      width: 100%;
    }

    .phva-grid{
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 16px;
      align-items: end;
      width: 100%;
    }

    @media (max-width: 1100px){
      .phva-grid{ grid-template-columns: repeat(2, minmax(200px, 1fr)); }
      .phva-actions{ grid-column: 1 / -1; justify-content: flex-end; }
    }

    @media (max-width: 700px){
      .phva-grid{ grid-template-columns: 1fr; }
      .phva-actions{ justify-content: stretch; }
      .btn-pill{ width: 100%; }
    }

    .phva-field label{
      font-weight:700;
      letter-spacing:.4px;
      font-size:14px;
    }

    .phva-field input,
    .phva-field select{
      width:100%;
      height: 40px;
      border: 2px solid #2b2b2b;
      background:#efefef;
      padding: 0 10px;
      border-radius: 2px;
      box-sizing: border-box;
    }

    .phva-actions{
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

    .phva-table{
      margin-top: 16px;
      border-radius: 4px;
      overflow:hidden;
      border: 1px solid #cfcfcf;
      width: 100%;
    }

    .phva-table table{
      width:100%;
      border-collapse: collapse;
      background:#d9d9d9;
      table-layout: fixed;
    }

    .phva-table th,
    .phva-table td{
      border-right: 2px solid #f0f0f0;
      padding: 12px;
      vertical-align: top;
    }

    .phva-table th{ background:#d0d0d0; font-weight:800; }
    .phva-table td{ height:auto; }
  </style>
</head>

<body>
<div class="phva-wrap">

  <div class="phva-grid">

    <div class="phva-field">
      <label>ITEM DEL ESTANDAR</label>
      <input type="text" id="itemEstandar">
    </div>

    <div class="phva-field">
      <label>FRECUENCIA</label>
      <select id="frecuencia">
        <option value=""></option>
        <option>Diaria</option>
        <option>Semanal</option>
        <option>Mensual</option>
        <option>Trimestral</option>
        <option>Semestral</option>
        <option>Anual</option>
      </select>
    </div>

    <div class="phva-field">
      <label>RESPONSABLE</label>
      <select id="responsable">
        <option value=""></option>
        <option>Administrador</option>
        <option>SST</option>
        <option>Talento Humano</option>
        <option>Operaciones</option>
      </select>
    </div>

    <div class="phva-field">
      <label>RESCURSO</label>
      <select id="recurso">
        <option value=""></option>
        <option>Humano</option>
        <option>Tecnológico</option>
        <option>Financiero</option>
        <option>Infraestructura</option>
      </select>
    </div>

    <div class="phva-field">
      <label>ESTANDAR</label>
      <select id="estandar">
        <option value=""></option>
        <option>RUC</option>
        <option>SST</option>
        <option>Calidad</option>
      </select>
    </div>

    <div class="phva-field">
      <label>CICLO AL PHVA</label>
      <select id="cicloPHVA">
        <option value=""></option>
        <option>Planear</option>
        <option>Hacer</option>
        <option>Verificar</option>
        <option>Actuar</option>
      </select>
    </div>

    <div class="phva-field">
      <label>SOPORTE</label>
      <input type="text" id="soporte">
    </div>

    <div class="phva-field">
      <label>ESTADO</label>
      <select id="estado">
        <option value=""></option>
        <option>Activo</option>
        <option>Pendiente</option>
        <option>Inactivo</option>
      </select>
    </div>

    <div class="phva-actions" style="grid-column: 3 / span 2;">
      <button class="btn-pill btn-add" id="btnAgregar" type="button">Agregar</button>
      <button class="btn-pill btn-cancel" id="btnCancelar" type="button">Cancelar</button>
    </div>

  </div>

  <div class="phva-table">
    <table>
      <thead>
        <tr>
          <th style="width:22%;">ITEM DEL<br>ESTANDAR</th>
          <th style="width:22%;">FRECUENCIA</th>
          <th style="width:22%;">ESTANDAR</th>
          <th style="width:22%;">CICLO AL PHVA</th>
          <th style="width:12%;">ESTADO</th>
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
    ["itemEstandar","frecuencia","responsable","recurso","estandar","cicloPHVA","soporte","estado"]
      .forEach(id => $(id).value = "");
  }

  btnCancelar.addEventListener("click", limpiar);

  btnAgregar.addEventListener("click", () => {
    const item = $("itemEstandar").value.trim();
    const frecuencia = $("frecuencia").value;
    const estandar = $("estandar").value;
    const ciclo = $("cicloPHVA").value;
    const estado = $("estado").value;

    if (!item || !frecuencia || !estandar || !ciclo || !estado) {
      alert("Completa los campos obligatorios: Item, Frecuencia, Estándar, Ciclo PHVA y Estado.");
      return;
    }

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${item}</td>
      <td>${frecuencia}</td>
      <td>${estandar}</td>
      <td>${ciclo}</td>
      <td>${estado}</td>
    `;
    tablaBody.appendChild(tr);
    limpiar();
  });
</script>

</body>
</html>
