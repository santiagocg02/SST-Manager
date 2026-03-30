<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../../../index.php");
  exit;
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>1.1.7 | Seguimiento COPASST y COCOLAB</title>

<style>
  :root{
    --blue:#2f62b6;
    --blue-soft:#cfe2f7;
    --line:#000;
    --bg:#ffffff;
  }

  *{ box-sizing:border-box; }

  body{
    margin:0;
    background:#fff;
    font-family: Arial, Helvetica, sans-serif;
    font-size:12px;
    color:#111;
  }

  .sheet{
    width:1200px;
    margin:0 auto;
    padding:16px 14px 24px;
    background:#fff;
  }

  .toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
    gap:10px;
  }

  .toolbar-left{
    display:flex;
    gap:8px;
  }

  .btn{
    border:1px solid #cfd6e4;
    background:#fff;
    padding:7px 10px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
  }

  .btn.primary{
    background:#eef4ff;
    border-color:#bcd2ff;
    color:#1241a6;
  }

  .tiny{
    font-size:10px;
    color:#6b7280;
    font-weight:700;
  }

  .grid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:36px;
    align-items:start;
  }

  .panel-title{
    font-size:14px;
    font-weight:900;
    color:var(--blue);
    text-transform:uppercase;
    margin:2px 0 14px;
    letter-spacing:.2px;
  }

  table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
  }

  th, td{
    border:1px solid var(--line);
    padding:0;
    vertical-align:middle;
  }

  .main-table col:first-child,
  .meet-table col:first-child{
    width:auto;
  }

  .main-table col:last-child,
  .meet-table col:last-child{
    width:80px;
  }

  .year-row th{
    background:var(--blue-soft);
    color:#111;
    font-weight:900;
    text-align:center;
    padding:6px 8px;
    font-size:12px;
  }

  .head-cumple{
    background:#fff !important;
    color:#111 !important;
  }

  .item-cell{
    padding:7px 8px;
    line-height:1.15;
    min-height:36px;
  }

  .center{
    text-align:center;
  }

  .meet-title{
    margin:6px 0 6px;
    text-align:center;
    font-weight:900;
    color:var(--blue);
    text-transform:uppercase;
    text-decoration:underline;
    font-size:13px;
  }

  .month-cell{
    padding:8px 8px;
    text-align:center;
    font-weight:400;
  }

  .total-label{
    padding:7px 8px;
    text-align:center;
    font-weight:900;
    background:#fff;
  }

  .percent-cell{
    text-align:center;
    font-weight:900;
    font-size:12px;
    background:#fff;
  }

  .score-input{
    width:100%;
    height:34px;
    border:0;
    outline:none;
    text-align:center;
    font-family:inherit;
    font-size:12px;
    background:transparent;
  }

  .score-input::-webkit-outer-spin-button,
  .score-input::-webkit-inner-spin-button{
    -webkit-appearance:none;
    margin:0;
  }


  .spacer{
    height:14px;
  }

  @media print{
    .toolbar{ display:none !important; }
    .sheet{
      width:auto;
      padding:0;
    }
  }
</style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="sheet">

  <div class="toolbar">
    <div class="toolbar-left">
     <button class="btn" type="button" onclick="window.location.href='../planear.php'">← Atrás</button>
      <button class="btn primary" type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
    <div class="tiny">
      Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong>
    </div>
  </div>

  <div class="grid">

    <!-- IZQUIERDA -->
    <div>
      <div class="panel-title">SEGUIMIENTO COPASST</div>

      <table id="copasstSeguimiento" class="main-table">
        <colgroup>
          <col>
          <col>
        </colgroup>
        <tr>
          <th class="head-cumple"></th>
          <th class="head-cumple center">CUMPLE</th>
        </tr>
        <tr class="year-row">
          <th>2025</th>
          <th>1</th>
        </tr>

        <tr><td class="item-cell">Actas de Conformación del ultimo Copasst Firmada por el R.L</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="item-cell">Capacitación de las funciones del COPASST</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="item-cell">Actas de reunión Mensual</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en SG SST según decreto 1072 del 2015</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en investigación de accidentes e incidentes</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en inspecciones planeadas y divulgación del programa</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en Programas de Vigilancia Epidemiológica</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación del curso de 50 horas en SST a todos los miembros</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Divulgación del Plan de trabajo anual</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Consolidado de los planes de acción de las investigaciones</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Seguimiento de las inspecciones realizadas y planes de acción</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Registro de divulgación de políticas del SGSST (salud ocupacional, política de no alcohol, tabaco y sustancias psicoactivas)</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Registro de divulgación matriz de objetivos, metas e indicadores del SGSST</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Registro de Divulgación presupuesto</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Registro de divulgación del manual de SG SST</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Planeación de Auditoria</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Revisión informe rendición de cuentas</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Cronograma de reuniones Copasst</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Seguimiento a las investigación de accidentes</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>

        <tr>
          <td class="total-label"></td>
          <td class="percent-cell" id="copasstSeguimientoTotal">0%</td>
        </tr>
      </table>

      <div class="spacer"></div>

      <div class="meet-title">REUNIONES COPASST</div>

      <table id="copasstReuniones" class="meet-table">
        <colgroup>
          <col>
          <col>
        </colgroup>
        <tr><td class="month-cell">ENERO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">FEBRERO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">MARZO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">ABRIL</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">MAYO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">JUNIO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">JULIO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">AGOSTO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">SEPTIEMBRE</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="month-cell">OCTUBRE</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="month-cell">NOVIEMBRE</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="month-cell">DICIEMBRE</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>

        <tr>
          <td class="total-label"></td>
          <td class="percent-cell" id="copasstReunionesTotal">0%</td>
        </tr>
      </table>
    </div>

    <!-- DERECHA -->
    <div>
      <div class="panel-title center">SEGUIMIENTO COCOLAB</div>

      <table id="cocolabSeguimiento" class="main-table">
        <colgroup>
          <col>
          <col>
        </colgroup>
        <tr>
          <th class="head-cumple"></th>
          <th class="head-cumple center">CUMPLE</th>
        </tr>
        <tr class="year-row">
          <th>2025</th>
          <th>1</th>
        </tr>

        <tr><td class="item-cell">Actas de Conformación del ultimo COCOLAB Firmada por el R.L</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="item-cell">Capacitación de las funciones del COCOLAB</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="item-cell">Actas de reunión trimestral</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en SG SST según decreto 1072 del 2015</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en acosto laboral</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en modalidades de acoso laboral</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación en Programas de Vigilancia Epidemiológica</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Capacitación del curso de 50 horas en SST a todos los miembros</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Divulgación del Plan de trabajo anual</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Consolidado de los planes de acción</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Seguimiento a posibles casos de acoso laboral</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Registro de divulgación de políticas del SGSST (salud ocupacional, política de no alcohol, tabaco y sustancias psicoactivas)</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="item-cell">Cronograma de reuniones COCOLAB</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>

        <!-- filas en blanco para simetría -->
        <tr><td class="item-cell">&nbsp;</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="item-cell">&nbsp;</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="item-cell">&nbsp;</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="item-cell">&nbsp;</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="item-cell">&nbsp;</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="item-cell">&nbsp;</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>

        <tr>
          <td class="total-label"></td>
          <td class="percent-cell" id="cocolabSeguimientoTotal">0%</td>
        </tr>
      </table>

      <div class="spacer"></div>

      <div class="meet-title">REUNIONES COCOLAB</div>

      <table id="cocolabReuniones" class="meet-table">
        <colgroup>
          <col>
          <col>
        </colgroup>
        <tr><td class="month-cell">ENERO</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">FEBRERO</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">MARZO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">ABRIL</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">MAYO</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">JUNIO</td><td><input class="score-input" type="number" min="0" max="1" value="1"></td></tr>
        <tr><td class="month-cell">JULIO</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">AGOSTO</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">SEPTIEMBRE</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>
        <tr><td class="month-cell">OCTUBRE</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">NOVIEMBRE</td><td><input class="score-input" type="number" min="0" max="1" value=""></td></tr>
        <tr><td class="month-cell">DICIEMBRE</td><td><input class="score-input" type="number" min="0" max="1" value="0"></td></tr>

        <tr>
          <td class="total-label"></td>
          <td class="percent-cell" id="cocolabReunionesTotal">0%</td>
        </tr>
      </table>
    </div>

  </div>
</div>

<script>
  function normalizarValor(valor){
    if (valor === null || valor === undefined) return null;
    const txt = String(valor).trim();
    if (txt === '') return null;
    const num = parseInt(txt, 10);
    if (isNaN(num)) return null;
    return num === 1 ? 1 : 0;
  }

  function calcularPorcentaje(tablaId, totalId){
    const tabla = document.getElementById(tablaId);
    if (!tabla) return;

    const inputs = tabla.querySelectorAll('.score-input');
    let suma = 0;
    let contador = 0;

    inputs.forEach(input => {
      const valor = normalizarValor(input.value);
      if (valor !== null) {
        contador++;
        suma += valor;
      }
    });

    const porcentaje = contador > 0 ? Math.round((suma / contador) * 100) : 0;
    document.getElementById(totalId).textContent = porcentaje + '%';
  }

  function recalcularTodo(){
    calcularPorcentaje('copasstSeguimiento', 'copasstSeguimientoTotal');
    calcularPorcentaje('copasstReuniones', 'copasstReunionesTotal');
    calcularPorcentaje('cocolabSeguimiento', 'cocolabSeguimientoTotal');
    calcularPorcentaje('cocolabReuniones', 'cocolabReunionesTotal');
  }

  document.querySelectorAll('.score-input').forEach(input => {
    input.addEventListener('input', function(){
      if (this.value !== '' && this.value !== '0' && this.value !== '1') {
        this.value = this.value > 0 ? '1' : '0';
      }
      recalcularTodo();
    });

    input.addEventListener('blur', function(){
      if (this.value === '') {
        recalcularTodo();
        return;
      }
      this.value = this.value === '1' ? '1' : '0';
      recalcularTodo();
    });
  });

  recalcularTodo();
</script>


<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>