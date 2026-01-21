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
/* Estructura del Switch */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

/* El slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
}

/* Colores de estado */
input:checked + .slider {
  background-color: #28a745; /* Verde para activo */
}

input:focus + .slider {
  box-shadow: 0 0 1px #28a745;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

/* Forma redondeada */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.switch-container {
  display: flex;
  align-items: center;
  gap: 10px;
}
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
    .phva-field textarea,
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
      <label>CICLO PHVA</label>
      <select id="cicloPHVA">
        <option value=""></option>
        <option>Planear</option>
        <option>Hacer</option>
        <option>Verificar</option>
        <option>Actuar</option>
      </select>
    </div>

    <div class="phva-field">
      <label>CATEGORIA</label>
      <select id="estandar">
        <option value=""></option>
        <option>RUC</option>
        <option>SST</option>
        <option>Calidad</option>
      </select>
    </div>

    <div class="phva-field">
      <label>TIPO</label>
      <select id="estandar">
        <option value=""></option>
        <option>RUC</option>
        <option>SST</option>
        <option>Calidad</option>
      </select>
    </div>

    <div class="phva-field">
      <label>ESTADO</label>
      <div class="switch-container">
      <label class="switch">
        <input type="checkbox" id="status" checked>
        <span class="slider round"></span>
        </label>
        <span id="status-label">Activo</span>
      </div>
    </div>

    <div class="phva-field">
      <label>ITEM DEL ESTANDAR</label>
      <textarea 
        id="itemEstandar" 
        name="itemEstandar" 
        rows="6" 
        style="resize: vertical; height: 98px;"
        placeholder="Ej: 1.1.1">
      </textarea>
    </div>

    <div class="phva-field">
      <label>ITEM</label>
      <textarea 
        id="item" 
        name="item" 
        rows="6" 
        style="resize: vertical; height: 98px;"
        placeholder="Ej: 1.1.1">
      </textarea>
    </div>

    <div class="phva-field">
      <label>CRITERIO</label>
      <textarea 
        id="criterio" 
        name="criterio" 
        rows="6" 
        style="resize: vertical; height: 98px;"
        placeholder="Ej: 1.1.1">
      </textarea>
    </div>

    <div class="phva-field">
      <label>MODO DE VERIFICACION</label>
      <textarea 
        id="modo" 
        name="modo" 
        rows="6" 
        style="resize: vertical; height: 98px;"
        placeholder="Ej: 1.1.1">
      </textarea>
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
          <th style="width:10%;">CICLO PHVA </th>
          <th style="width:15%;">CATEGORIA</th>
          <th style="width:18%;">TIPO </th>
          <th style="width:18%;">ITEM DEL<br>ESTANDAR</th>
          <th style="width:18%;">ITEM </th>
          <th style="width:18%;">CRITERIO </th>
          <th style="width:18%;">MODO DE VERIFICACION </th>
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
