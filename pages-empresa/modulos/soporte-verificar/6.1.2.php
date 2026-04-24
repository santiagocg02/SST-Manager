<?php
session_start();
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 612;

// LOGO
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    $empData = $resEmpresa['data'][0] ?? $resEmpresa['data'] ?? [];
    $logoEmpresaUrl = $empData['logo_url'] ?? '';
}

// DATOS
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = $resFormulario['data']['campos'] ?? null;

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true) ?: [];
}

function val($key, $default){
    global $datosCampos;
    return htmlspecialchars($datosCampos[$key] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>6.1.2 Auditoría Interna</title>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* MISMO DISEÑO */
body{background:#f2f4f7;padding:20px;font-family:Arial;}
.contenedor{max-width:1200px;margin:auto;background:#fff;border:1px solid #ccc;}
.toolbar{display:flex;justify-content:space-between;padding:15px;background:#dde7f5;}
.btn{padding:10px 15px;border:none;border-radius:6px;font-weight:bold;cursor:pointer;}
.btn-guardar{background:#198754;color:#fff;}
.btn-imprimir{background:#0d6efd;color:#fff;}
.btn-atras{background:#6c757d;color:#fff;}
.formulario{padding:20px;}
textarea,input{width:100%;border:1px solid #ccc;padding:10px;border-radius:5px;}
.seccion{margin-top:20px;}
.seccion h3{border-bottom:2px solid #d9e2f2;padding-bottom:5px;}
.logo-box{height:60px;display:flex;align-items:center;justify-content:center;}
@media print {.toolbar{display:none;}}
</style>
</head>

<body>

<div class="contenedor">

<div class="toolbar">
<h2>6.1.2 Auditoría Interna</h2>
<div>
<button class="btn btn-atras" onclick="history.back()">Atrás</button>
<button class="btn btn-guardar" id="btnGuardar">Guardar</button>
<button class="btn btn-imprimir" onclick="window.print()">Imprimir</button>
</div>
</div>

<div class="formulario">
<form id="formAuditoria">

<!-- ENCABEZADO -->
<table border="1" width="100%">
<tr>
<td width="20%">
<div class="logo-box">
<?php if($logoEmpresaUrl): ?>
<img src="<?= $logoEmpresaUrl ?>" style="max-height:60px;">
<?php else: ?>LOGO<?php endif; ?>
</div>
</td>
<td><b>SISTEMA DE GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO</b><br>PROCEDIMIENTO DE AUDITORIA INTERNA</td>
<td>Fecha: <?= date('d-m-Y') ?></td>
</tr>
</table>

<!-- PORTADA -->
<div class="seccion">
<h3 style="text-align:center;">PROCEDIMIENTO AUDITORIA INTERNA</h3>
<input name="empresa" value="<?= val('empresa','NOMBRE DE LA EMPRESA') ?>" style="text-align:center;margin-top:20px;">
<input name="fecha_portada" value="<?= val('fecha_portada','FECHA') ?>" style="text-align:center;margin-top:10px;">
</div>

<!-- 1 OBJETIVO -->
<div class="seccion">
<h3>1. OBJETIVO</h3>
<textarea name="objetivo"><?= val('objetivo',"Establecer la metodología para la realización de Auditorías Internas, con el fin de evaluar el cumplimiento y eficacia del SG SST en la organización.") ?></textarea>
</div>

<!-- 2 ALCANCE -->
<div class="seccion">
<h3>2. ALCANCE</h3>
<textarea name="alcance"><?= val('alcance',"Este procedimiento aplica para todas las áreas de la organización. Así mismo, se encuentra sujeto al requerimiento Auditoría Interna del Decreto 1072/ 2015.") ?></textarea>

<textarea name="alcance_lista"><?= val('alcance_lista',"1. El cumplimiento de la política de seguridad y salud en el trabajo
2. El resultado de los indicadores de estructura, proceso y resultado
3. La participación de los trabajadores
4. La rendición de cuentas
5. Comunicación del SG-SST
6. Planificación y aplicación del SG-SST
7. Gestión del cambio
8. SST en adquisiciones
9. Proveedores y contratistas
10. Supervisión de resultados
11. Investigación de incidentes
12. Desarrollo del proceso de auditoría
13. Evaluación por la alta dirección") ?></textarea>
</div>

<!-- 3 DEFINICIONES -->
<div class="seccion">
<h3>3. DEFINICIONES</h3>
<textarea name="definiciones"><?= val('definiciones',"Encargado del SG SST: programar auditorías.

Auditoría: Examen sistemático para evaluar cumplimiento.

Acción correctiva: Acción para eliminar causas.

Auditor: Persona competente para auditar.

Hallazgos: Resultados de evaluación.

Informe de auditoría: Documento con resultados.

Plan de auditoría: Actividades planificadas.

Programa de auditoría: Auditorías programadas.

SG-SST: Sistema de Gestión de Seguridad y Salud en el Trabajo.") ?></textarea>
</div>

<!-- RESPONSABLES -->
<div class="seccion">
<h3>RESPONSABLES</h3>
<textarea name="responsables"><?= val('responsables',"Encargado SST: elaborar procedimiento.

Gerente: aprobar procedimiento.

Auditor líder:
1. Elegir equipo auditor
2. Preparar plan anual
3. Representar ante gerencia
4. Coordinar auditoría
5. Tomar decisiones
6. Presentar informe

Todo el personal debe cumplir el procedimiento.") ?></textarea>
</div>

<!-- PROCEDIMIENTO -->
<div class="seccion">
<h3>4. PROCEDIMIENTO</h3>
<textarea name="procedimiento"><?= val('procedimiento',"Las Auditorías Internas están encaminadas a determinar si el SG SST:

• Es conforme con la ley
• Se implementa y mantiene eficazmente") ?></textarea>
</div>

<!-- DOCUMENTOS -->
<div class="seccion">
<h3>4.1 DOCUMENTOS</h3>
<textarea name="documentos"><?= val('documentos',"• Decreto 1072 de 2015
• Resolución 0312 de 2019
• Requisitos legales
• Documentos del sistema") ?></textarea>
</div>

<!-- ALCANCE LEGAL -->
<div class="seccion">
<h3>5. ALCANCE LEGAL</h3>
<textarea name="alcance_legal"><?= val('alcance_legal',"Decreto 1072 del 2015 y Resolución 0312 de 2019") ?></textarea>
</div>

<!-- FRECUENCIA -->
<div class="seccion">
<h3>6. FRECUENCIA</h3>
<input name="frecuencia" value="<?= val('frecuencia',"Mínimo una vez al año") ?>">
</div>

<!-- ETAPAS -->
<div class="seccion">
<h3>7. ETAPAS DE AUDITORÍA</h3>

<textarea name="inicio"><?= val('inicio',"Inicio: planificación del programa de auditoría.") ?></textarea>

<textarea name="preparacion"><?= val('preparacion',"Preparación: revisión de documentos y plan de auditoría.") ?></textarea>

<textarea name="ejecucion"><?= val('ejecucion',"Ejecución:
- Reunión de apertura
- Recolección de evidencias
- Entrevistas
- Generación de hallazgos
- Reunión de cierre") ?></textarea>

<textarea name="informe"><?= val('informe',"Informe:
- Plan de auditoría
- Resumen
- Observaciones
- No conformidades") ?></textarea>

<textarea name="seguimiento"><?= val('seguimiento',"Seguimiento:
Verificar acciones correctivas hasta cierre total.") ?></textarea>

<textarea name="competencia"><?= val('competencia',"Competencia:
Auditores deben ser profesionales con diplomado.") ?></textarea>

<textarea name="documentos_rel"><?= val('documentos_rel',"Documentos relacionados:
• Plan de auditoría
• Informe de auditoría") ?></textarea>

</div>

</form>
</div>

</div>

<script>
// AUTO RESIZE
document.querySelectorAll("textarea").forEach(t=>{
    t.style.height = t.scrollHeight+"px";
    t.addEventListener("input",()=>{t.style.height="auto";t.style.height=t.scrollHeight+"px";});
});

// GUARDAR
document.getElementById("btnGuardar").addEventListener("click", async ()=>{
    const datos = Object.fromEntries(new FormData(document.getElementById("formAuditoria")));

    const res = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar",{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization":"Bearer <?= $token ?>"
        },
        body: JSON.stringify({
            id_empresa: <?= $empresa ?>,
            id_item_sst: <?= $idItem ?>,
            datos: datos
        })
    });

    const r = await res.json();

    Swal.fire(r.ok ? "Guardado!" : "Error", "", r.ok ? "success":"error");
});
</script>

</body>
</html>