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
<title>Calificación</title>

<link rel="stylesheet" href="../assets/css/style.css">

<style>
html, body { height:100%; margin:0; }

.cal-wrap{
  padding:18px;
  background:#f4f4f1;
  min-height:100%;
  box-sizing:border-box;
}

.cal-top{
  display:grid;
  grid-template-columns:420px 320px;
  gap:16px;
  align-items:end;
}

.cal-detail{
  margin-top:14px;
  display:grid;
  grid-template-columns:420px 240px 240px;
  gap:16px;
}

.hidden{ display:none; }

.cal-field label{
  font-weight:700;
  font-size:14px;
}

.cal-field input,
.cal-field select{
  width:100%;
  height:40px;
  border:2px solid #2b2b2b;
  background:#efefef;
  padding:0 10px;
}

.btn-gray{
  height:40px;
  background:#d8d8d8;
  border:0;
  font-weight:700;
  cursor:pointer;
}

.cal-actions{
  margin-top:16px;
  display:flex;
  gap:16px;
  justify-content:center;
}

.btn-add{
  background:#2fb14a;
  color:#fff;
  padding:10px 34px;
  border-radius:999px;
  border:0;
  font-weight:700;
}

.btn-cancel{
  background:#0b4f7a;
  color:#fff;
  padding:10px 34px;
  border-radius:999px;
  border:0;
  font-weight:700;
}

.cal-table{
  margin-top:16px;
  border:1px solid #cfcfcf;
}

.cal-table table{
  width:100%;
  border-collapse:collapse;
  background:#d9d9d9;
}

.cal-table th,
.cal-table td{
  padding:12px;
  border-right:2px solid #f0f0f0;
}

.cal-table th{ background:#d0d0d0; font-weight:800; }
</style>
</head>

<body>
<div class="cal-wrap">

  <!-- FILA PRINCIPAL -->
  <div class="cal-top">
    <div class="cal-field">
      <label>NOMBRE CALIFICACION</label>
      <input id="nombre">
    </div>

    <button class="btn-gray" id="btnModo">AGREGAR CALIFICACION</button>
  </div>

  <!-- DETALLE (aparece luego) -->
  <div class="cal-detail hidden" id="detalle">
    <div class="cal-field">
      <label>DESCRIPCION</label>
      <input id="descripcion">
    </div>

    <div class="cal-field">
      <label>VALOR</label>
      <input type="number" id="valor">
    </div>

    <div class="cal-field">
      <label>ESTADO</label>
      <select id="estado">
        <option></option>
        <option>Activo</option>
        <option>Inactivo</option>
      </select>
    </div>
  </div>

  <!-- BOTONES -->
  <div class="cal-actions">
    <button class="btn-add" id="btnAgregar">Agregar</button>
    <button class="btn-cancel" href="menu-admin.php" id="btnCancelar">Cancelar</button>
  </div>

  <!-- TABLA ÚNICA -->
  <div class="cal-table">
    <table>
      <thead id="thead">
        <tr>
          <th>ID</th>
          <th>NOMBRE</th>
          <th>ESTADO</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
  </div>

</div>

<script>
let modoDetalle = false;
let autoId = 1;

const $ = id => document.getElementById(id);

$("btnModo").onclick = () => {
  modoDetalle = true;
  $("detalle").classList.remove("hidden");

  $("thead").innerHTML = `
    <tr>
      <th>CALIFICACION</th>
      <th>DESCRIPCION</th>
      <th>VALOR</th>
      <th>ESTADO</th>
    </tr>`;
};

$("btnAgregar").onclick = () => {
  const nombre = $("nombre").value.trim();

  if (!nombre) return alert("Ingresa el nombre de la calificación");

  if (!modoDetalle) {
    // TABLA INICIAL
    $("tbody").innerHTML += `
      <tr>
        <td>${autoId++}</td>
        <td>${nombre}</td>
        <td></td>
      </tr>`;
  } else {
    // TABLA DETALLE
    const desc = $("descripcion").value.trim();
    const valor = $("valor").value.trim();
    const estado = $("estado").value;

    if (!desc || !valor || !estado)
      return alert("Completa descripción, valor y estado");

    $("tbody").innerHTML += `
      <tr>
        <td>${nombre}</td>
        <td>${desc}</td>
        <td>${valor}</td>
        <td>${estado}</td>
      </tr>`;
  }

  limpiar();
};

$("btnCancelar").onclick = limpiar;

function limpiar(){
  document.querySelectorAll("input, select").forEach(e => e.value="");
}
</script>

</body>
</html>
