<?php
session_start();
require_once '../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
  header("Location: ../index.php");
  exit;
}

$api   = new ConexionAPI();
$token = $_SESSION["token"];
$mensaje = "";

/* =========================
   GET: tablas para selects dependientes
   ciclos_phva:   id_ciclo, nombre
   categorias:    id, descripcion, estado, id_ciclo
   categoria_tipos: id, categoria_id, descripcion, estado
========================= */
$resCiclos = $api->solicitar("index.php?table=ciclos_phva", "GET", null, $token);
$ciclos = (($resCiclos["status"] ?? 0) == 200 && is_array($resCiclos["data"] ?? null)) ? $resCiclos["data"] : [];

$resCategorias = $api->solicitar("index.php?table=categorias", "GET", null, $token);
$categorias = (($resCategorias["status"] ?? 0) == 200 && is_array($resCategorias["data"] ?? null)) ? $resCategorias["data"] : [];

$resTipos = $api->solicitar("index.php?table=categoria_tipos", "GET", null, $token);
$tipos = (($resTipos["status"] ?? 0) == 200 && is_array($resTipos["data"] ?? null)) ? $resTipos["data"] : [];

/* Index para mostrar en tabla: ciclo/categoria/tipo desde id_categorias (FK a categoria_tipos.id) */
$cicloIndex = [];
foreach ($ciclos as $c) {
  $cicloIndex[(string)($c["id_ciclo"] ?? "")] = $c["nombre"] ?? "";
}

$categoriaIndex = [];
foreach ($categorias as $c) {
  $categoriaIndex[(string)($c["id"] ?? "")] = [
    "descripcion" => $c["descripcion"] ?? "",
    "id_ciclo" => (string)($c["id_ciclo"] ?? ""),
    "estado" => (int)($c["estado"] ?? 0)
  ];
}

$tipoIndex = [];
foreach ($tipos as $t) {
  $tipoIndex[(string)($t["id"] ?? "")] = [
    "descripcion" => $t["descripcion"] ?? "",
    "categoria_id" => (string)($t["categoria_id"] ?? ""),
    "estado" => (int)($t["estado"] ?? 0)
  ];
}

/* =========================
   POST: Crear / Actualizar item
   Tabla item:
   id_detalle (PK), id_categorias (FK -> categoria_tipos.id),
   item_estandar, item, criterio, modo_verificacion, estado (1/0)
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $idDetalle = $_POST["id_detalle"] ?? "";

  $idCategoriasTipo = (int)($_POST["id_categorias"] ?? 0); // FK a categoria_tipos.id
  $itemEstandar = trim($_POST["item_estandar"] ?? "");
  $item = trim($_POST["item"] ?? "");
  $criterio = trim($_POST["criterio"] ?? "");
  $modo = trim($_POST["modo_verificacion"] ?? "");
  $estado = isset($_POST["estado"]) ? 1 : 0;

  if ($idCategoriasTipo <= 0) {
    $mensaje = "Debe seleccionar Ciclo PHVA, Categoría y Tipo.";
  } elseif ($item === "") {
    $mensaje = "El campo ITEM es obligatorio.";
  } else {
    $datosEnviar = [
      "id_categorias" => $idCategoriasTipo,
      "item_estandar" => $itemEstandar,
      "item" => $item,
      "criterio" => $criterio,
      "modo_verificacion" => $modo,
      "estado" => $estado
    ];

    $endpoint = "index.php?table=item" . (!empty($idDetalle) ? "&id=" . urlencode($idDetalle) : "");
    $metodo = !empty($idDetalle) ? "PUT" : "POST";

    $resultado = $api->solicitar($endpoint, $metodo, $datosEnviar, $token);

    if (($resultado["status"] ?? 0) == 200 || ($resultado["status"] ?? 0) == 201) {
      header("Location: item1072.php");
      exit;
    } else {
      $mensaje = "Error: " . json_encode($resultado);
    }
  }
}

/* =========================
   GET: Listar items
========================= */
$resItems = $api->solicitar("index.php?table=item", "GET", null, $token);
$lista = (($resItems["status"] ?? 0) == 200 && is_array($resItems["data"] ?? null)) ? $resItems["data"] : [];

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SST Manager - Item 1072</title>

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- MISMO CSS de Modulos -->
  <link rel="stylesheet" href="../../assets/css/main-style.css">
</head>

<body class="cal-wrap">
<div class="container-fluid">

  <h2 class="mb-4">
    <i class="fa-solid fa-table-cells-large me-2" style="color: var(--primary-blue);"></i>
    Item 1072
  </h2>

  <?php if(!empty($mensaje)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <!-- CARD FORM (mantener formato actual con filtros dependientes) -->
  <form method="POST" id="formItem1072" class="bg-white p-4 rounded card-shadow mb-4 border">
    <input type="hidden" id="id_detalle" name="id_detalle">
    <!-- ✅ Se llena al seleccionar TIPO (categoria_tipos.id) -->
    <input type="hidden" id="id_categorias" name="id_categorias">

    <div class="row g-3 align-items-end">

      <div class="col-md-3">
        <label class="fw-bold small text-muted">CICLO PHVA</label>
        <select id="cicloPHVA" class="form-select" required></select>
      </div>

      <div class="col-md-3">
        <label class="fw-bold small text-muted">CATEGORÍA</label>
        <select id="categoria" class="form-select" required></select>
      </div>

      <div class="col-md-3">
        <label class="fw-bold small text-muted">TIPO</label>
        <select id="tipo" class="form-select" required></select>
      </div>

      <div class="col-md-1 text-center">
        <label class="fw-bold small d-block text-muted">ESTADO</label>
        <label class="switch mt-1">
          <input type="checkbox" id="estado" name="estado" checked>
          <span class="slider"></span>
        </label>
      </div>

      <div class="col-md-3">
        <label class="fw-bold small text-muted">ITEM DEL ESTÁNDAR</label>
        <input type="text" id="item_estandar" name="item_estandar" class="form-control" placeholder="Ej: 1.1.1">
      </div>

      <div class="col-md-3">
        <label class="fw-bold small text-muted">ITEM</label>
        <input type="text" id="item" name="item" class="form-control" placeholder="Nombre del requisito" required>
      </div>

      <div class="col-md-3">
        <label class="fw-bold small text-muted">CRITERIO</label>
        <input type="text" id="criterio" name="criterio" class="form-control" placeholder="Ej: ...">
      </div>

      <div class="col-md-3">
        <label class="fw-bold small text-muted">MODO DE VERIFICACIÓN</label>
        <input type="text" id="modo_verificacion" name="modo_verificacion" class="form-control" placeholder="Ej: ...">
      </div>

    </div>

    <div class="mt-3 d-flex gap-2">
      <button type="submit" id="btnGuardar" class="btn btn-success px-4 shadow-sm">Guardar</button>
      <button type="button" class="btn btn-outline-secondary px-4" onclick="limpiarForm()">Limpiar</button>
    </div>
  </form>

  <!-- TABLE -->
  <div class="card-shadow border overflow-hidden">
    <div class="table-scroll-container">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-dark text-uppercase small">
        <tr>
          <th>CICLO PHVA</th>
          <th>CATEGORÍA</th>
          <th>TIPO</th>
          <th>ITEM DEL ESTÁNDAR</th>
          <th>ITEM</th>
          <th>CRITERIO</th>
          <th>MODO DE VERIFICACIÓN</th>
          <th>ESTADO</th>
          <th class="text-center" width="120">ACCIONES</th>
        </tr>
        </thead>

        <tbody>
        <?php if(empty($lista)): ?>
          <tr>
            <td colspan="9" class="text-center text-muted py-4">No hay registros.</td>
          </tr>
        <?php else: ?>
          <?php foreach($lista as $row): ?>
            <?php
              $estadoNum = (int)($row["estado"] ?? 0);
              $estadoTxt = ($estadoNum === 1) ? "Activo" : "Inactivo";
              $estadoCls = ($estadoNum === 1) ? "status-label-active" : "status-label-inactive";

              // FK hacia categoria_tipos.id
              $tipoId = (string)($row["id_categorias"] ?? "");
              $tipoRow = $tipoIndex[$tipoId] ?? null;

              $tipoTxt = $tipoRow["descripcion"] ?? "";
              $catId = $tipoRow["categoria_id"] ?? "";
              $catRow = $categoriaIndex[(string)$catId] ?? null;

              $catTxt = $catRow["descripcion"] ?? "";
              $cicloId = $catRow["id_ciclo"] ?? "";
              $cicloTxt = $cicloIndex[(string)$cicloId] ?? "";
            ?>
            <tr>
              <td><?= htmlspecialchars($cicloTxt) ?></td>
              <td><?= htmlspecialchars($catTxt) ?></td>
              <td><?= htmlspecialchars($tipoTxt) ?></td>
              <td><?= htmlspecialchars($row["item_estandar"] ?? "") ?></td>
              <td class="fw-bold"><?= htmlspecialchars($row["item"] ?? "") ?></td>
              <td><?= htmlspecialchars($row["criterio"] ?? "") ?></td>
              <td><?= htmlspecialchars($row["modo_verificacion"] ?? "") ?></td>
              <td><span class="<?= $estadoCls ?>"><?= $estadoTxt ?></span></td>
              <td class="text-center">
                <button type="button"
                        class="btn btn-sm btn-light border shadow-sm"
                        onclick="cargar('<?= base64_encode(json_encode($row)) ?>')">
                  <i class="fa-solid fa-pencil text-warning"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>

      </table>
    </div>
  </div>

</div>

<script>
/**
 * DATA REAL (según tus columnas)
 * ciclos_phva: id_ciclo, nombre
 * categorias: id, descripcion, estado, id_ciclo
 * categoria_tipos: id, categoria_id, descripcion, estado
 */
const CICLOS = <?= json_encode($ciclos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const CATEGORIAS = <?= json_encode($categorias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const TIPOS = <?= json_encode($tipos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

const $ = (id) => document.getElementById(id);

const selCiclo = $("cicloPHVA");
const selCat   = $("categoria");
const selTipo  = $("tipo");
const hidTipoId = $("id_categorias");

const norm = (v) => String(v ?? "").trim();

function resetSelect(select, placeholder="Seleccione") {
  select.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
}

function fillSelect(select, rows, valueKey, textKey, placeholder="Seleccione") {
  resetSelect(select, placeholder);
  rows.forEach(r => {
    const opt = document.createElement("option");
    opt.value = norm(r[valueKey]);
    opt.textContent = norm(r[textKey]);
    select.appendChild(opt);
  });
}

// ✅ Solo activos
const CICLOS_OK = CICLOS.filter(x => true); // ciclos no tienen estado en tu captura
const CATEGORIAS_OK = CATEGORIAS.filter(x => Number(x.estado ?? 0) === 1);
const TIPOS_OK = TIPOS.filter(x => Number(x.estado ?? 0) === 1);

function initFiltros() {
  // Ciclo: value=id_ciclo, text=nombre
  fillSelect(selCiclo, CICLOS_OK, "id_ciclo", "nombre", "Seleccione");
  resetSelect(selCat, "Seleccione");
  resetSelect(selTipo, "Seleccione");
  hidTipoId.value = "";
}

// 1) CICLO -> CATEGORÍAS
selCiclo.addEventListener("change", () => {
  const idCiclo = norm(selCiclo.value);

  const cats = CATEGORIAS_OK.filter(c => norm(c.id_ciclo) === idCiclo);
  // Categoria: value=id, text=descripcion
  fillSelect(selCat, cats, "id", "descripcion", "Seleccione");

  resetSelect(selTipo, "Seleccione");
  hidTipoId.value = "";
});

// 2) CATEGORÍA -> TIPOS
selCat.addEventListener("change", () => {
  const idCategoria = norm(selCat.value);

  const tipos = TIPOS_OK.filter(t => norm(t.categoria_id) === idCategoria);
  // Tipo: value=id, text=descripcion
  fillSelect(selTipo, tipos, "id", "descripcion", "Seleccione");

  hidTipoId.value = "";
});

// 3) TIPO -> set hidden FK (categoria_tipos.id)
selTipo.addEventListener("change", () => {
  hidTipoId.value = norm(selTipo.value);
});

/**
 * Editar:
 * - llena campos del item
 * - ubica el tipo (categoria_tipos.id) y setea ciclo/categoria/tipo
 */
function cargar(base64Data) {
  const d = JSON.parse(atob(base64Data));

  $("id_detalle").value = d.id_detalle ?? "";
  $("item_estandar").value = d.item_estandar ?? "";
  $("item").value = d.item ?? "";
  $("criterio").value = d.criterio ?? "";
  $("modo_verificacion").value = d.modo_verificacion ?? "";
  $("estado").checked = (Number(d.estado) === 1);

  const tipoId = String(d.id_categorias ?? "").trim();
  if (!tipoId) return;

  // Buscar tipo -> categoria_id -> id_ciclo
  const tipoRow = TIPOS_OK.find(t => String(t.id) === tipoId);
  if (!tipoRow) return;

  const catId = String(tipoRow.categoria_id ?? "").trim();
  const catRow = CATEGORIAS_OK.find(c => String(c.id) === catId);
  if (!catRow) return;

  const cicloId = String(catRow.id_ciclo ?? "").trim();

  // 1) set ciclo
  selCiclo.value = cicloId;
  selCiclo.dispatchEvent(new Event("change"));

  // 2) set categoria
  selCat.value = catId;
  selCat.dispatchEvent(new Event("change"));

  // 3) set tipo
  selTipo.value = tipoId;
  selTipo.dispatchEvent(new Event("change"));

  $("btnGuardar").textContent = "Actualizar";
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function limpiarForm() {
  $("formItem1072").reset();
  $("id_detalle").value = "";
  $("id_categorias").value = "";
  $("estado").checked = true;
  $("btnGuardar").textContent = "Guardar";
  initFiltros();
}

initFiltros();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
