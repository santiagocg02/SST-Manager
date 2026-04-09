<?php
session_start();
require_once '../../includes/ConexionAPI.php';

// 1. VALIDACIÓN DE SESIÓN Y CARGA DE PERMISOS
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../index.php"); exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$rolSesion = $_SESSION["rol"] ?? '';
$perfilIdSesion = $_SESSION["id_perfil"] ?? 0;

$misPermisos = [];
if ($rolSesion !== "Master") {
    $resPermisos = $api->solicitar("perfiles/permisos/$perfilIdSesion/check-all", "GET", null, $token);
    $datosFinales = $resPermisos['data'] ?? $resPermisos;
    if (is_array($datosFinales)) {
        foreach ($datosFinales as $perm) {
            if (isset($perm['id_modulo'])) {
                $misPermisos[(int)$perm['id_modulo']] = [
                    'ver' => (int)($perm['ver'] ?? 0),
                    'crear' => (int)($perm['crear'] ?? 0),
                    'editar' => (int)($perm['editar'] ?? 0)
                ];
            }
        }
    }
}

// 2. FUNCIONES DE VALIDACIÓN
function puede($mod, $accion, $rol, $permisos) {
    if ($rol === "Master") return true;
    return isset($permisos[$mod]) && (int)($permisos[$mod][$accion] ?? 0) === 1;
}

$MOD_EMPRESA = 13;
$MOD_SST = 15;

// 3. CARGA DE DATOS REALES DESDE API
$resEmpresas = $api->solicitar("index.php?table=empresas", "GET", null, $token);
$listaEmpresas = ($resEmpresas['status'] == 200) ? ($resEmpresas['data'] ?? []) : [];

$resPlanes = $api->solicitar("index.php?table=planes", "GET", null, $token);
$listaPlanes = ($resPlanes['status'] == 200) ? ($resPlanes['data'] ?? []) : [];

$resSST = $api->solicitar("index.php?table=personal_sst", "GET", null, $token);
$listaSST = ($resSST['status'] == 200) ? ($resSST['data'] ?? []) : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>SST Manager - Gestión de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/main-style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .label-custom { font-size: 0.7rem; font-weight: 700; color: #555; text-transform: uppercase; }
        .section-header { background: #f4f6f9; padding: 5px 10px; font-size: 0.75rem; font-weight: bold; border-left: 4px solid #0b4f7a; margin: 10px 0; }
        .card-shadow { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border-radius: 10px; background: #fff; }
        .img-preview { max-height: 80px; border: 1px solid #ddd; padding: 3px; border-radius: 5px; margin-top: 5px; display: none; background: #fafafa; }
    </style>
</head>
<body class="cal-wrap">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-industry me-2" style="color: #0b4f7a;"></i>Gestión de Empresas</h2>
        <?php if(puede($MOD_EMPRESA, 'crear', $rolSesion, $misPermisos)): ?>
            <button class="btn btn-success shadow-sm" onclick="abrirModalCrear()">
                <i class="fa-solid fa-plus me-1"></i> Nueva Empresa
            </button>
        <?php endif; ?>
    </div>

    <div class="card-shadow border overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark small text-uppercase">
                <tr>
                    <th class="ps-3">NIT / Doc</th>
                    <th>Empresa / Razón Social</th>
                    <th>Representante</th>
                    <th>Contacto</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaEmpresas as $e): ?>
                <tr>
                    <td class="ps-3 small text-muted"><?= htmlspecialchars($e['numero_documento'] ?? '') ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($e['nombre_empresa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($e['nombre_rl'] ?? 'N/A') ?></td>
                    <td class="small">
                        <i class="fa-solid fa-envelope me-1 text-muted"></i> <?= htmlspecialchars($e['email_contacto'] ?? 'N/A') ?><br>
                        <i class="fa-solid fa-phone me-1 text-muted"></i> <?= htmlspecialchars($e['telefono'] ?? 'N/A') ?>
                    </td>
                    <td class="text-center">
                        <?php if(puede($MOD_SST, 'crear', $rolSesion, $misPermisos) || puede($MOD_SST, 'editar', $rolSesion, $misPermisos)): ?>
                            <button class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="abrirModalSST(<?= (int)($e['id_empresa'] ?? 0) ?>, '<?= htmlspecialchars($e['nombre_empresa'] ?? '') ?>')">
                                <i class="fa-solid fa-user-shield me-1"></i> SST
                            </button>
                        <?php endif; ?>

                        <?php if(puede($MOD_EMPRESA, 'editar', $rolSesion, $misPermisos)): ?>
                            <button class="btn btn-sm btn-light border ms-1 shadow-sm" onclick="cargarParaEditar('<?= base64_encode(json_encode($e)) ?>')">
                                <i class="fa-solid fa-pencil text-warning"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($listaEmpresas)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron empresas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalFormEmpresa" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="tituloModalEmpresa">Registrar Empresa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <form id="formEmpresa">
                    <input type="hidden" id="id_empresa" name="id_empresa">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="label-custom">Nombre empresa / Razón Social</label>
                            <input type="text" name="nombre_empresa" id="nombre_empresa" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="label-custom">T.C</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select form-select-sm">
                                <option value="NIT">NIT</option>
                                <option value="CC">CC</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="label-custom">Número documento</label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="section-header">Ubicación y Contacto</div>
                    <div class="row g-2">
                        <div class="col-md-4"><label class="label-custom">Dirección</label><input type="text" name="direccion" id="direccion" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="label-custom">Teléfono</label><input type="text" name="telefono" id="telefono" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="label-custom">Correo / Email</label><input type="email" name="email_contacto" id="email_contacto" class="form-control form-control-sm"></div>
                    </div>

                    <div class="section-header">Información Representante Legal</div>
                    <div class="row g-2">
                        <div class="col-md-6"><label class="label-custom">Nombre R.L.</label><input type="text" name="nombre_rl" id="nombre_rl" class="form-control form-control-sm"></div>
                        <div class="col-md-6"><label class="label-custom">Documento R.L.</label><input type="text" name="documento_rl" id="documento_rl" class="form-control form-control-sm"></div>
                    </div>

                    <div class="section-header">Multimedia (Logo y Firma)</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="label-custom">Logo de la Empresa</label>
                            <input type="file" id="input_logo" class="form-control form-control-sm" accept="image/*" onchange="convertirBase64(this, 'logo_url', 'prev_logo')">
                            <input type="hidden" name="logo_url" id="logo_url">
                            <div class="text-center"><img id="prev_logo" class="img-preview"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="label-custom">Firma Representante Legal</label>
                            <input type="file" id="input_firma" class="form-control form-control-sm" accept="image/*" onchange="convertirBase64(this, 'firma_rl', 'prev_firma')">
                            <input type="hidden" name="firma_rl" id="firma_rl">
                            <div class="text-center"><img id="prev_firma" class="img-preview"></div>
                        </div>
                    </div>

                    <div class="section-header">Distribución de Trabajadores</div>
                    <div class="row g-2 text-center">
                        <div class="col-md-3"><label class="label-custom">Directos</label><input type="number" name="cant_directos" id="cant_directos" class="form-control form-control-sm" value="0"></div>
                        <div class="col-md-3"><label class="label-custom">Contratistas</label><input type="number" name="cant_contratistas" id="cant_contratistas" class="form-control form-control-sm" value="0"></div>
                        <div class="col-md-3"><label class="label-custom">Aprendices</label><input type="number" name="cant_aprendices" id="cant_aprendices" class="form-control form-control-sm" value="0"></div>
                        <div class="col-md-3"><label class="label-custom">Temporales</label><input type="number" name="cant_brigadistas" id="cant_brigadistas" class="form-control form-control-sm" value="0"></div>
                    </div>

                    <div class="section-header">Clasificación y Plan</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="label-custom">Plan de Servicio</label>
                            <select name="id_plan" id="id_plan" class="form-select form-select-sm" required>
                                <option value="">Seleccione un plan...</option>
                                <?php foreach ($listaPlanes as $p): ?>
                                    <option value="<?= $p['id_plan'] ?>"><?= htmlspecialchars($p['nombre_plan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="label-custom">Nivel de Riesgo</label>
                            <select name="nivel_riesgo" id="nivel_riesgo" class="form-select form-select-sm" required>
                                <option value="">Seleccione el nivel...</option>
                                <option value="I">I (Mínimo)</option>
                                <option value="II">II (Bajo)</option>
                                <option value="III">III (Medio)</option>
                                <option value="IV">IV (Alto)</option>
                                <option value="V">V (Máximo)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success px-4" onclick="guardarEmpresa()">
                    <i class="fa-solid fa-save me-2"></i>Guardar Empresa
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSST" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Gestionar Personal SST</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <form id="formSST">
                    <input type="hidden" id="id_personal_sst" name="id_personal_sst">
                    <input type="hidden" id="sst_id_empresa" name="id_empresa">
                    
                    <div class="mb-3">
                        <label class="label-custom">Nombre empresa</label>
                        <input type="text" id="sst_nombre_empresa" class="form-control border-0 bg-light fw-bold" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="label-custom">Proyecto Rige</label>
                        <input type="text" id="proyecto_rige" name="proyecto_rige" class="form-control form-control-sm" value="IMPLEMENTACIÓN ESTÁNDARES MÍNIMOS" required>
                    </div>
                    <div class="mb-2">
                        <label class="label-custom">Profesional SST</label>
                        <input type="text" id="nombre_profesional" name="nombre_profesional" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="label-custom">Correo SST</label>
                        <input type="email" id="correo_sst" name="correo_sst" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="label-custom">Teléfono SST</label>
                        <input type="text" id="telefono_sst" name="telefono_sst" class="form-control form-control-sm">
                    </div>
                    
                    <div class="mb-3 border-top pt-2 mt-3">
                        <label class="label-custom">Firma Profesional SST</label>
                        <input type="file" id="input_firma_sst" class="form-control form-control-sm" accept="image/*" onchange="convertirBase64(this, 'firma_sst_url', 'prev_firma_sst')">
                        <input type="hidden" name="firma_sst_url" id="firma_sst_url">
                        <div class="text-center mt-1">
                            <img id="prev_firma_sst" class="img-preview" style="max-height: 60px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center pb-4 gap-2">
                <button type="button" class="btn btn-success px-4" onclick="guardarSST()">GUARDAR</button>
                <button type="button" class="btn btn-primary px-4" style="background: #1a3a5a;" data-bs-dismiss="modal">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const modalEmpresa = new bootstrap.Modal(document.getElementById('modalFormEmpresa'));
  const modalSST = new bootstrap.Modal(document.getElementById('modalSST'));
  const API_URL = "http://localhost/sstmanager-backend/public/index.php";

  const listaSSTMacro = <?= json_encode($listaSST) ?>;
  const puedeCrearSST = <?= puede($MOD_SST, 'crear', $rolSesion, $misPermisos) ? 'true' : 'false' ?>;
  const puedeEditarSST = <?= puede($MOD_SST, 'editar', $rolSesion, $misPermisos) ? 'true' : 'false' ?>;

  // LÓGICA BASE 64 (Reutilizable para todos los inputs file)
  function convertirBase64(input, hiddenId, imgPrevId) {
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const base64String = e.target.result;
        document.getElementById(hiddenId).value = base64String;
        const prev = document.getElementById(imgPrevId);
        prev.src = base64String;
        prev.style.display = 'inline-block';
      };
      reader.readAsDataURL(file);
    }
  }

  // ==========================================
  // FUNCIONES EMPRESA
  // ==========================================
  function abrirModalCrear() {
    document.getElementById('formEmpresa').reset();
    document.getElementById('id_empresa').value = "";
    
    // Limpiar Base64 e imágenes de Empresa
    document.getElementById('logo_url').value = "";
    document.getElementById('firma_rl').value = "";
    document.getElementById('prev_logo').style.display = 'none';
    document.getElementById('prev_firma').style.display = 'none';
    
    document.getElementById('tituloModalEmpresa').innerText = "Registrar Nueva Empresa";
    modalEmpresa.show();
  }

  function cargarParaEditar(base64) {
    try {
      const d = JSON.parse(atob(base64));
      document.getElementById('tituloModalEmpresa').innerText = "Editar Empresa: " + (d.nombre_empresa ?? '');

      document.getElementById('id_empresa').value = d.id_empresa ?? "";
      document.getElementById('nombre_empresa').value = d.nombre_empresa ?? "";
      document.getElementById('tipo_documento').value = d.tipo_documento || 'NIT';
      document.getElementById('numero_documento').value = d.numero_documento ?? "";
      document.getElementById('direccion').value = d.direccion || "";
      document.getElementById('telefono').value = d.telefono || "";
      document.getElementById('email_contacto').value = d.email_contacto || "";
      document.getElementById('nombre_rl').value = d.nombre_rl || "";
      document.getElementById('documento_rl').value = d.documento_rl || "";
      document.getElementById('cant_directos').value = d.cant_directos || 0;
      document.getElementById('cant_contratistas').value = d.cant_contratistas || 0;
      document.getElementById('cant_aprendices').value = d.cant_aprendices || 0;
      document.getElementById('cant_brigadistas').value = d.cant_brigadistas || 0;
      document.getElementById('id_plan').value = d.id_plan || "";
      document.getElementById('nivel_riesgo').value = d.nivel_riesgo || "";

      // Cargar imágenes de Empresa si existen
      const logo = d.logo_url || "";
      const firma = d.firma_rl || "";
      
      document.getElementById('logo_url').value = logo;
      const pLogo = document.getElementById('prev_logo');
      if(logo) { pLogo.src = logo; pLogo.style.display = 'inline-block'; } else { pLogo.style.display = 'none'; }

      document.getElementById('firma_rl').value = firma;
      const pFirma = document.getElementById('prev_firma');
      if(firma) { pFirma.src = firma; pFirma.style.display = 'inline-block'; } else { pFirma.style.display = 'none'; }

      // Limpiar inputs visuales
      document.getElementById('input_logo').value = "";
      document.getElementById('input_firma').value = "";

      modalEmpresa.show();
    } catch (e) { console.error("Error al cargar datos:", e); }
  }

  async function guardarEmpresa() {
    const form = document.getElementById('formEmpresa');
    if (!form.checkValidity()) return form.reportValidity();

    const data = Object.fromEntries(new FormData(form).entries());
    const id = document.getElementById('id_empresa').value;
    const url = `${API_URL}?table=empresas${id ? '&id=' + id : ''}`;
    const method = id ? 'PUT' : 'POST';

    try {
      const resp = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
        body: JSON.stringify(data)
      });
      const res = await resp.json();
      if (res.id || res.ok) {
        Swal.fire('¡Éxito!', 'Empresa guardada', 'success').then(() => location.reload());
      } else {
        Swal.fire('Error', res.error || 'No se pudo guardar', 'error');
      }
    } catch (e) { Swal.fire('Error', 'No se pudo conectar', 'error'); }
  }

  // ==========================================
  // FUNCIONES SST
  // ==========================================
  function abrirModalSST(idEmpresa, nombreEmpresa) {
    document.getElementById('formSST').reset();
    document.getElementById('id_personal_sst').value = ""; 
    document.getElementById('sst_id_empresa').value = idEmpresa;
    document.getElementById('sst_nombre_empresa').value = nombreEmpresa;

    // ✅ Limpiar Base64 e imagen de SST
    document.getElementById('firma_sst_url').value = "";
    document.getElementById('prev_firma_sst').style.display = 'none';
    document.getElementById('input_firma_sst').value = "";

    const sstData = listaSSTMacro.find(sst => sst.id_empresa == idEmpresa);
    
    if (puedeCrearSST) {
      if (sstData) llenarDatosSST(sstData);
      modalSST.show();
    } else if (puedeEditarSST && sstData) {
      llenarDatosSST(sstData);
      modalSST.show();
    } else {
      Swal.fire('Acceso denegado', 'Permisos insuficientes.', 'warning');
    }
  }

  function llenarDatosSST(sstData) {
      document.getElementById('id_personal_sst').value = sstData.id_personal_sst || ""; 
      document.getElementById('proyecto_rige').value = sstData.proyecto_rige || 'IMPLEMENTACIÓN ESTÁNDARES MÍNIMOS';
      document.getElementById('nombre_profesional').value = sstData.nombre_profesional || '';
      document.getElementById('correo_sst').value = sstData.correo_sst || '';
      document.getElementById('telefono_sst').value = sstData.telefono_sst || '';
      
      // ✅ Cargar firma SST si existe
      const firmaSST = sstData.firma_sst_url || "";
      document.getElementById('firma_sst_url').value = firmaSST;
      const pFirmaSST = document.getElementById('prev_firma_sst');
      
      if(firmaSST) { 
          pFirmaSST.src = firmaSST; 
          pFirmaSST.style.display = 'inline-block'; 
      } else { 
          pFirmaSST.style.display = 'none'; 
      }
  }

  async function guardarSST() {
    const form = document.getElementById('formSST');
    if (!form.checkValidity()) return form.reportValidity();
    
    const data = Object.fromEntries(new FormData(form).entries());
    const id = document.getElementById('id_personal_sst').value;
    const url = id ? `${API_URL}?table=personal_sst&id=${id}` : `${API_URL}?table=personal_sst`;
    
    try {
      const resp = await fetch(url, {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer <?= $token ?>' },
        body: JSON.stringify(data)
      });
      const res = await resp.json();
      if (res.id || res.ok) {
          Swal.fire('¡Éxito!', 'SST guardado', 'success').then(() => location.reload());
      } else {
          Swal.fire('Error', res.error || 'No se pudo guardar SST', 'error');
      }
    } catch (e) { Swal.fire('Error', 'Error de red', 'error'); }
  }
</script>
</body>
</html>