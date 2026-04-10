<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos (Ej: 28 para "Listado Maestro")
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 28; 

// --- Lógica de Empresa Optimizada (Logo) ---
$logoEmpresaUrl = "";

if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE A LA API
$resFormulario = $api->solicitar("formularios-dinamicos/empresa/$empresa/item/$idItem", "GET", null, $token);
$datosCampos = [];
$camposCrudos = null;

if (isset($resFormulario['data']['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['data']['campos'];
} elseif (isset($resFormulario['data']['campos'])) {
    $camposCrudos = $resFormulario['data']['campos'];
} elseif (isset($resFormulario['campos'])) {
    $camposCrudos = $resFormulario['campos'];
}

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true);
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

// DATOS BASE DEL SISTEMA
$documentos = [
    ['tipo'=>'Políticas', 'nombre'=>'Políticas del SG SST', 'version'=>'0', 'codigo'=>'PO-SST-01', 'fecha'=>'1/1/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Planes', 'nombre'=>'Plan de trabajo', 'version'=>'0', 'codigo'=>'PL-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Planes', 'nombre'=>'Plan de Evacuación Medica MEDEVAC', 'version'=>'0', 'codigo'=>'PL-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Programas', 'nombre'=>'Programa de capacitaciones', 'version'=>'0', 'codigo'=>'PR-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Inspecciones', 'version'=>'0', 'codigo'=>'PR-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Gestión de Residuos', 'version'=>'0', 'codigo'=>'PR-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia Epidemiológica PVE Ergonomía', 'version'=>'0', 'codigo'=>'PR-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Mantenimiento', 'version'=>'0', 'codigo'=>'PR-SST-06', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Prevención de Consumo de alcohol, cigarrillo y sustancias psicoactivas', 'version'=>'0', 'codigo'=>'PR-SST-07', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia Epidemiológica PVE Auditivo', 'version'=>'0', 'codigo'=>'PR-SST-08', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia Epidemiológica PVE Psicosocial', 'version'=>'0', 'codigo'=>'PR-SST-09', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Riesgo Mecánico', 'version'=>'0', 'codigo'=>'PR-SST-10', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de Emergencias', 'version'=>'0', 'codigo'=>'PR-SST-11', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de vigilancia epidemiológica de fomento de estilos de vida y trabajo saludable', 'version'=>'0', 'codigo'=>'PR-SST-12', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de prevención de riesgo vial', 'version'=>'0', 'codigo'=>'PR-SST-13', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Programas', 'nombre'=>'Programa de auditorías', 'version'=>'0', 'codigo'=>'PR-SST-14', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Manuales', 'nombre'=>'Manual de control de Documentos', 'version'=>'0', 'codigo'=>'MA-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Manuales', 'nombre'=>'Manual SG-SST', 'version'=>'0', 'codigo'=>'MA-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Registros', 'nombre'=>'Registro de Asistencia', 'version'=>'0', 'codigo'=>'RE-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Seguimiento a capacitaciones', 'version'=>'0', 'codigo'=>'RE-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de planificación del cambio', 'version'=>'0', 'codigo'=>'RE-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Ambiental', 'version'=>'0', 'codigo'=>'RE-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de identificación de acciones de mejora y correctivas', 'version'=>'0', 'codigo'=>'RE-SST-05', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inspección de uso y mantenimiento dotación y elementos de protección personal', 'version'=>'0', 'codigo'=>'RE-SST-06', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Gerencial', 'version'=>'0', 'codigo'=>'RE-SST-07', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Extintor', 'version'=>'0', 'codigo'=>'RE-SST-08', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de Inspección Botiquín', 'version'=>'0', 'codigo'=>'RE-SST-09', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato Investigación de Accidente e Incidente', 'version'=>'0', 'codigo'=>'RE-SST-10', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formatos de Inspecciones - Locativo', 'version'=>'0', 'codigo'=>'RE-SST-11', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formatos de Inspecciones - Sustancias Químicas', 'version'=>'0', 'codigo'=>'RE-SST-12', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inspección de uso y mantenimiento dotación y elementos de protección personal', 'version'=>'0', 'codigo'=>'RE-SST-13', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inspección de herramientas genéricas', 'version'=>'0', 'codigo'=>'RE-SST-14', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de Chequeo Personas Jurídicas', 'version'=>'0', 'codigo'=>'RE-SST-15', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de Chequeo Personas Naturales', 'version'=>'0', 'codigo'=>'RE-SST-16', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Entrega de dotación y EPP', 'version'=>'0', 'codigo'=>'RE-SST-17', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato para la adquisición de compras', 'version'=>'0', 'codigo'=>'RE-SST-18', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato Plan de auditoría', 'version'=>'0', 'codigo'=>'RE-SST-19', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato acta de reunión de COPASST', 'version'=>'0', 'codigo'=>'RE-SST-21', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lecciones aprendidas', 'version'=>'0', 'codigo'=>'RE-SST-22', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Observación de comportamiento', 'version'=>'0', 'codigo'=>'RE-SST-23', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de verificación de transporte de productos químicos', 'version'=>'0', 'codigo'=>'RE-SST-24', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Evaluación Inducción', 'version'=>'0', 'codigo'=>'RE-SST-25', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Tarjeta de reporte de actos y condiciones inseguras', 'version'=>'0', 'codigo'=>'RE-SST-26', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Estadísticas de ausentismo', 'version'=>'0', 'codigo'=>'RE-SST-27', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Ficha técnica del indicador', 'version'=>'0', 'codigo'=>'RE-SST-28', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'MEDEVAC', 'version'=>'0', 'codigo'=>'RE-SST-29', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de chequeo auditoría', 'version'=>'0', 'codigo'=>'RE-SST-30', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Lista de chequeo simulacro', 'version'=>'0', 'codigo'=>'RE-SST-31', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Formato de inscripción de brigadas', 'version'=>'0', 'codigo'=>'RE-SST-32', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Estadísticas de Accidentalidad', 'version'=>'0', 'codigo'=>'RE-SST-33', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Registro de MEDEVAC', 'version'=>'0', 'codigo'=>'RE-SST-34', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Registros', 'nombre'=>'Inventario general de equipos y elementos de emergencia', 'version'=>'0', 'codigo'=>'RE-SST-35', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Anexos', 'nombre'=>'Diagnóstico inicial', 'version'=>'0', 'codigo'=>'AN-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Presupuesto', 'version'=>'0', 'codigo'=>'AN-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de inducción', 'version'=>'0', 'codigo'=>'AN-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Listado Maestro de documentos', 'version'=>'0', 'codigo'=>'AN-SST-05', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de Objetivos e indicadores', 'version'=>'0', 'codigo'=>'AN-SST-06', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de identificación de requisitos legales', 'version'=>'0', 'codigo'=>'AN-SST-07', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz para la identificación de peligros y la valoración de Riesgos', 'version'=>'0', 'codigo'=>'AN-SST-08', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento Gestión de cambio', 'version'=>'0', 'codigo'=>'AN-SST-09', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para realizar EMO', 'version'=>'0', 'codigo'=>'AN-SST-10', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Profesiograma', 'version'=>'0', 'codigo'=>'AN-SST-11', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de auditoría interna', 'version'=>'0', 'codigo'=>'AN-SST-12', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para la Identificación de Peligros', 'version'=>'0', 'codigo'=>'AN-SST-13', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Encuesta para perfil sociodemográfico', 'version'=>'0', 'codigo'=>'AN-SST-14', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Análisis Encuesta para perfil sociodemográfico', 'version'=>'0', 'codigo'=>'AN-SST-15', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de Investigación de Accidentes', 'version'=>'0', 'codigo'=>'AN-SST-19', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de Idn_Ev_Seg RQ Legales', 'version'=>'0', 'codigo'=>'AN-SST-20', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de EPP por cargos', 'version'=>'0', 'codigo'=>'AN-SST-21', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para la gestión del cambio', 'version'=>'0', 'codigo'=>'AN-SST-22', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de participación, consulta y comunicación', 'version'=>'0', 'codigo'=>'AN-SST-23', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de revisión por la dirección y rendición de cuentas', 'version'=>'0', 'codigo'=>'AN-SST-24', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Seguimiento de pago de aportes al Sistema de Seguridad Social', 'version'=>'0', 'codigo'=>'AN-SST-26', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Perfiles de cargo', 'version'=>'0', 'codigo'=>'AN-SST-28', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz Actos y condiciones inseguras/ acciones correctivas y/o preventivas', 'version'=>'0', 'codigo'=>'AN-SST-29', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Matriz de Seguimiento de Exámenes Médicos', 'version'=>'0', 'codigo'=>'AN-SST-30', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento para compras en SST', 'version'=>'0', 'codigo'=>'AN-SST-31', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Caracterización de la accidentalidad', 'version'=>'0', 'codigo'=>'AN-SST-32', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Perfil del representante de la alta dirección para el SG SST', 'version'=>'0', 'codigo'=>'AN-SST-33', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Anexos', 'nombre'=>'Procedimiento de acciones correctivas y preventivas', 'version'=>'0', 'codigo'=>'AN-SST-35', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Actas', 'nombre'=>'Conformación COPASST', 'version'=>'0', 'codigo'=>'AC-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Conformación COCOLAB', 'version'=>'0', 'codigo'=>'AC-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Conformación de brigadas', 'version'=>'0', 'codigo'=>'AC-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Acta de reunión y seguimiento de actividades', 'version'=>'0', 'codigo'=>'AC-SST-04', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Actas', 'nombre'=>'Carta de nombramiento representante SG- SST Alta gerencia', 'version'=>'0', 'codigo'=>'AC-SST-05', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],

    ['tipo'=>'Informes', 'nombre'=>'Informe de auditoría', 'version'=>'0', 'codigo'=>'IN-SST-01', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Informes', 'nombre'=>'Informe de Señalización', 'version'=>'0', 'codigo'=>'IN-SST-02', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
    ['tipo'=>'Informes', 'nombre'=>'Informe de Rendición de cuentas', 'version'=>'0', 'codigo'=>'IN-SST-03', 'fecha'=>'XX/XX/2025', 'ubicacion'=>'Plataforma y Pc Administrador'],
];

$grupos = [];
foreach ($documentos as $doc) {
    $grupos[$doc['tipo']][] = $doc;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2.5.1-2 - Listado Maestro de Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --blue:#1f5fa8;
            --head:#89a6d6;
            --line:#8f8f8f;
            --bg:#eef2f7;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            background:var(--bg);
            font-family:Arial, Helvetica, sans-serif;
            color:#111;
        }

        .wrap{
            max-width:1100px;
            margin:16px auto;
            padding:0 10px;
        }

        .toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            margin-bottom:12px;
            flex-wrap:wrap;
            background: #d9dde2;
            padding: 10px 16px;
            border: 1px solid #c8cdd3;
            border-radius: 6px;
        }
        .btn-action{
            border:1px solid #cfd6e4;
            background:#fff;
            color: #2f62b6;
            padding:6px 12px;
            border-radius:6px;
            font-weight:800;
            cursor:pointer;
            font-size:12px;
        }
        .btn-action:hover { background: #eef4ff; }
        .btn-primary-action{
            border-color:#1b4fbd;
            background:#1b4fbd;
            color:#fff;
            padding:6px 12px;
            border-radius:6px;
            font-weight:800;
            cursor:pointer;
            font-size:12px;
        }
        .btn-primary-action:hover { background: #0f3484; }
        .btn-success-action {
            border: 1px solid #198754;
            background: #198754;
            color: #fff;
            padding:6px 12px;
            border-radius:6px;
            font-weight:800;
            cursor:pointer;
            font-size:12px;
        }
        .btn-success-action:hover { background: #146c43; }
        .tiny{ font-size:11px; color:#6b7280; font-weight:700; }

        .sheet{
            background:#fff;
            border:2px solid var(--blue);
            box-shadow:0 10px 20px rgba(0,0,0,.08);
            padding:10px;
        }

        .top-scroll{
            overflow-x:auto;
            overflow-y:hidden;
            height:18px;
            margin-bottom:6px;
        }

        .top-scroll-inner{
            height:1px;
        }

        .table-wrap{
            overflow:auto;
            max-height:78vh;
            border:1px solid #ccd5e0;
        }

        table.master{
            width:max-content;
            min-width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:12px;
            background:#fff;
        }

        .master td,.master th{
            border:1px solid var(--line);
            padding:4px 6px;
            vertical-align:middle;
        }

        .master thead th{
            background:var(--head);
            color:#111;
            font-weight:900;
            text-align:center;
        }

        .head-box{
            background:#fff !important;
            font-weight:800;
            text-align:center;
        }

        .logo-cell{
            width:150px;
            padding: 4px !important;
        }

        .logo-box{
            height:72px;
            border:2px dashed rgba(0,0,0,.22);
            display:flex;
            align-items:center;
            justify-content:center;
            color:rgba(0,0,0,.35);
            font-size:11px;
            font-weight:800;
            text-align:center;
            padding: 2px;
        }

        .title-main{
            font-weight:900;
            text-align:center;
            font-size:13px;
        }

        .title-sub{
            font-weight:900;
            text-align:center;
            font-size:12px;
        }

        .type-cell{
            font-weight:800;
            text-align:center;
            background:#fbfbfb;
        }

        .edit{
            width:100%;
            min-width:0;
            border:none;
            outline:none;
            background:transparent;
            font-size:12px;
            padding:0;
            color:#111;
        }

        .edit.center{
            text-align:center;
        }

        .w-tipo{ width:160px; }
        .w-nombre{ width:430px; }
        .w-version{ width:60px; }
        .w-codigo{ width:110px; }
        .w-fecha{ width:150px; }
        .w-ubicacion{ width:220px; }

        @media print{
            body{ background:#fff; }
            .toolbar, .top-scroll{ display:none !important; }
            .sheet{ box-shadow:none; border:2px solid #000; }
            .table-wrap{
                max-height:none;
                overflow:visible;
                border:none;
            }
        }
    </style>
    <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>
<div class="wrap">

    <div class="toolbar print-hide">
        <div style="display:flex; gap:8px;">
            <button class="btn-action" type="button" onclick="history.back()">← Atrás</button>
            <button class="btn-action" type="button" onclick="window.location.reload()">Recargar</button>
            <button class="btn-success-action" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn-primary-action" type="button" onclick="window.print()">Imprimir PDF</button>
        </div>
        <div class="tiny text-end">
            <span style="font-size: 14px; font-weight: 900; color: #0f2f5c;">LISTADO MAESTRO DOCUMENTOS</span><br>
            Usuario: <strong><?= e($_SESSION["usuario"] ?? "Usuario") ?></strong> · <span id="hoyTxt"></span>
        </div>
    </div>

    <form id="form-sst-dinamico">
        <div class="sheet">

            <div class="top-scroll" id="topScroll">
                <div class="top-scroll-inner" id="topScrollInner"></div>
            </div>

            <div class="table-wrap" id="tableWrap">
                <table class="master" id="masterTable">
                    <colgroup>
                        <col class="w-tipo">
                        <col class="w-nombre">
                        <col class="w-version">
                        <col class="w-codigo">
                        <col class="w-fecha">
                        <col class="w-ubicacion">
                    </colgroup>

                    <thead>
                        <tr>
                            <th rowspan="2" class="logo-cell head-box">
                                <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? '' : 'border:none; background:transparent;' ?>">
                                    <?php if(!empty($logoEmpresaUrl)): ?>
                                        <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 65px; object-fit: contain;">
                                    <?php else: ?>
                                        LOGO<br>EMPRESA
                                    <?php endif; ?>
                                </div>
                            </th>
                            <th colspan="4" class="head-box title-main">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</th>
                            <th class="head-box">0</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="head-box title-sub">LISTADO MAESTRO DE DOCUMENTOS</th>
                            <th class="head-box">AN-SST-05</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="head-box"></th>
                            <th class="head-box"><input type="date" name="meta_fecha" id="metaFecha" style="border:none; font-size:12px; font-weight:900; outline:none; background:transparent; text-align:center; width:100%;"></th>
                        </tr>
                        <tr>
                            <th>TIPO DE DOCUMENTO</th>
                            <th>NOMBRE DEL DOCUMENTO</th>
                            <th>VERSIÓN</th>
                            <th>CÓDIGO</th>
                            <th>FECHA</th>
                            <th>UBICACIÓN</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($grupos as $tipo => $items): ?>
                            <?php $rowspan = count($items); ?>
                            <?php foreach ($items as $index => $item): ?>
                                <tr>
                                    <?php if ($index === 0): ?>
                                        <td rowspan="<?php echo $rowspan; ?>" class="type-cell">
                                            <?php echo htmlspecialchars($tipo); ?>
                                        </td>
                                    <?php endif; ?>

                                    <td>
                                        <input type="hidden" name="doc_tipo[]" value="<?php echo htmlspecialchars($tipo); ?>">
                                        <input name="doc_nombre[]" class="edit" type="text" value="<?php echo htmlspecialchars($item['nombre']); ?>">
                                    </td>
                                    <td>
                                        <input name="doc_version[]" class="edit center" type="text" value="<?php echo htmlspecialchars($item['version']); ?>">
                                    </td>
                                    <td>
                                        <input name="doc_codigo[]" class="edit center" type="text" value="<?php echo htmlspecialchars($item['codigo']); ?>">
                                    </td>
                                    <td>
                                        <input name="doc_fecha[]" class="edit center" type="text" value="<?php echo htmlspecialchars($item['fecha']); ?>">
                                    </td>
                                    <td>
                                        <input name="doc_ubicacion[]" class="edit" type="text" value="<?php echo htmlspecialchars($item['ubicacion']); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>

                        <?php for($i=0; $i<10; $i++): ?>
                            <tr>
                                <td class="type-cell"><input name="doc_tipo[]" class="edit center" type="text" value="" placeholder="Ej: Registros"></td>
                                <td><input name="doc_nombre[]" class="edit" type="text" value=""></td>
                                <td><input name="doc_version[]" class="edit center" type="text" value=""></td>
                                <td><input name="doc_codigo[]" class="edit center" type="text" value=""></td>
                                <td><input name="doc_fecha[]" class="edit center" type="text" value=""></td>
                                <td><input name="doc_ubicacion[]" class="edit" type="text" value=""></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
    const topScroll = document.getElementById('topScroll');
    const topScrollInner = document.getElementById('topScrollInner');
    const tableWrap = document.getElementById('tableWrap');
    const masterTable = document.getElementById('masterTable');

    function syncTopScrollWidth() {
        topScrollInner.style.width = masterTable.scrollWidth + 'px';
    }

    topScroll.addEventListener('scroll', function () {
        tableWrap.scrollLeft = topScroll.scrollLeft;
    });

    tableWrap.addEventListener('scroll', function () {
        topScroll.scrollLeft = tableWrap.scrollLeft;
    });

    window.addEventListener('load', syncTopScrollWidth);
    window.addEventListener('resize', syncTopScrollWidth);

    // Poner fecha de hoy por defecto si está vacía
    function setHoy(){
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,"0");
        const dd = String(d.getDate()).padStart(2,"0");
        document.getElementById("hoyTxt").textContent = `${y}/${m}/${dd}`;
        
        const fmeta = document.getElementById("metaFecha");
        if (fmeta && !fmeta.value) fmeta.value = `${y}-${m}-${dd}`;
    }
    setHoy();

    // --- LÓGICA DE CARGADO DE DATOS DESDE PHP ---
    document.addEventListener('DOMContentLoaded', function () {
        let datosGuardados = <?= json_encode($datosCampos ?: new stdClass()) ?>;
        if (typeof datosGuardados === 'string') {
            try { datosGuardados = JSON.parse(datosGuardados); } catch(e) {}
        }

        if (datosGuardados && Object.keys(datosGuardados).length > 0) {
            for (const [key, value] of Object.entries(datosGuardados)) {
                if (Array.isArray(value)) {
                    let campos = document.querySelectorAll(`[name="${key}[]"]`);
                    value.forEach((val, i) => {
                        if (campos[i]) campos[i].value = typeof val === 'string' ? val.replace(/\\n/g, '\n') : val;
                    });
                } else {
                    const campo = document.querySelector(`[name="${key}"]`);
                    if (campo) {
                        campo.value = typeof value === 'string' ? value.replace(/\\n/g, '\n') : value;
                    }
                }
            }
        }
    });

    // --- LÓGICA DE GUARDADO ---
    document.getElementById('btnGuardar').addEventListener('click', async function() {
        const btn = this;
        const form = document.getElementById('form-sst-dinamico');
        const formData = new FormData(form);
        const datosJSON = {};

        for (const [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                const cleanKey = key.replace('[]', '');
                if (!datosJSON[cleanKey]) datosJSON[cleanKey] = [];
                datosJSON[cleanKey].push(value);
            } else {
                datosJSON[key] = value;
            }
        }

        const originalText = btn.innerHTML;
        btn.innerHTML = 'Guardando...';
        btn.disabled = true;

        try {
            const token = "<?= $token ?>";
            const urlAPI = "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar";

            const response = await fetch(urlAPI, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    id_empresa: <?= $empresa ?>,
                    id_item_sst: <?= $idItem ?>,
                    datos: datosJSON
                })
            });

            const result = await response.json();

            if (result.ok) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Listado Maestro guardado correctamente',
                    icon: 'success',
                    confirmButtonColor: '#198754'
                });
            } else {
                Swal.fire({
                    title: 'Error al guardar',
                    text: result.error || "No se pudo completar la operación.",
                    icon: 'error',
                    confirmButtonColor: '#1b4fbd'
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                title: 'Error de conexión',
                text: 'No se pudo contactar al servidor para guardar.',
                icon: 'error',
                confirmButtonColor: '#1b4fbd'
            });
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>

<script src="../../../assets/js/soporte-toolbar-unificado.js"></script>
</body>
</html>