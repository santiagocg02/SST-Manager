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
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 54;

// LOGO EMPRESA
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);

    if (isset($resEmpresa['data'][0])) {
        $logoEmpresaUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// DATOS FORMULARIO
$resFormulario = $api->solicitar(
    "formularios-dinamicos/empresa/$empresa/item/$idItem",
    "GET",
    null,
    $token
);

$datosCampos = [];

$camposCrudos =
    $resFormulario['data']['data']['campos']
    ?? $resFormulario['data']['campos']
    ?? null;

if (is_string($camposCrudos)) {
    $datosCampos = json_decode($camposCrudos, true) ?: [];
} elseif (is_array($camposCrudos)) {
    $datosCampos = $camposCrudos;
}

function oldv($key, $default = '')
{
    global $datosCampos;

    return isset($datosCampos[$key])
        ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8')
        : $default;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>6.1.3.1 Acta de Revisión por Alta Dirección</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f2f4f7;
            padding: 20px;
        }

        .contenedor {
            max-width: 1200px;
            background: #fff;
            border: 1px solid #bfc7d1;
            margin: auto;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .08);
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            background: #dde7f5;
            border-bottom: 1px solid #c8d3e2;
        }

        .toolbar h1 {
            font-size: 20px;
            color: #1a4175;
            font-weight: 700;
        }

        .acciones {
            display: flex;
            gap: 10px;
        }

        .btn {
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
        }

        .btn-atras {
            background: #6c757d;
        }

        .btn-guardar {
            background: #198754;
        }

        .btn-imprimir {
            background: #0d6efd;
        }

        .btn-add-fila {
            background: #1a4175;
            color: #fff;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            margin-left: 15px;
        }

        .formulario-body {
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 12px;
            vertical-align: top;
        }

        .bg-gris {
            background: #f2f2f2;
            font-weight: bold;
        }

        .bg-blue-light {
            background: #dde7f5;
            font-weight: bold;
            text-align: center;
        }

        input,
        textarea,
        select {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 12px;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 70px;
            line-height: 1.5;
        }

        .flex-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #dde7f5;
            padding-bottom: 6px;
        }

        h3 {
            font-size: 14px;
            color: #1a4175;
            text-transform: uppercase;
        }

        .texto {
            font-size: 12px;
            line-height: 1.7;
            margin-bottom: 15px;
            text-align: justify;
        }

        .firma-box {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }

        .firma {
            width: 45%;
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: center;
        }

        @media print {
            .print-hide {
                display: none !important;
            }

            .contenedor {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

<div class="contenedor">

    <div class="toolbar print-hide">
        <h1>Acta Revisión Alta Dirección SG-SST (6.1.3.1)</h1>

        <div class="acciones">
            <button class="btn btn-atras" onclick="history.back()">Atrás</button>

            <button class="btn btn-guardar" id="btnGuardar">
                Guardar Acta
            </button>

            <button class="btn btn-imprimir" onclick="window.print()">
                Imprimir
            </button>
        </div>
    </div>

    <div class="formulario-body">

        <form id="form6131">

            <!-- ENCABEZADO -->

            <table>
                <tr>
                    <td rowspan="2" style="width:220px;text-align:center;">
                        <img src="<?= $logoEmpresaUrl ?>" style="max-height:70px;">
                    </td>

                    <td style="font-weight:bold;font-size:14px;text-align:center;">
                        SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO
                    </td>

                    <td style="width:120px;text-align:center;">
                        0
                    </td>
                </tr>

                <tr>
                    <td style="font-weight:bold;text-align:center;">
                        ACTA DE REVISIÓN POR ALTA DIRECCIÓN AL SG-SST
                    </td>

                    <td style="text-align:center;">
                        RE-SST-19<br>
                        24/04/2026
                    </td>
                </tr>
            </table>

            <!-- DATOS -->

            <table>
                <tr>
                    <td class="bg-gris">EMPRESA</td>
                    <td colspan="3">
                        <input type="text" name="empresa" value="<?= oldv('empresa') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="bg-gris">NIT</td>
                    <td>
                        <input type="text" name="nit" value="<?= oldv('nit') ?>">
                    </td>

                    <td class="bg-gris">PERÍODO REVISADO</td>
                    <td>
                        <input type="text" name="periodo" value="<?= oldv('periodo') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="bg-gris">FECHA REUNIÓN</td>
                    <td>
                        <input type="date" name="fecha_reunion" value="<?= oldv('fecha_reunion') ?>">
                    </td>

                    <td class="bg-gris">PRÓXIMA REUNIÓN</td>
                    <td>
                        <input type="date" name="proxima_reunion" value="<?= oldv('proxima_reunion') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="bg-gris">HORA INICIO</td>
                    <td>
                        <input type="time" name="hora_inicio" value="<?= oldv('hora_inicio') ?>">
                    </td>

                    <td class="bg-gris">HORA FINALIZACIÓN</td>
                    <td>
                        <input type="time" name="hora_fin" value="<?= oldv('hora_fin') ?>">
                    </td>
                </tr>

                <tr>
                    <td class="bg-gris">ACTA No.</td>

                    <td colspan="3">
                        <input type="text" name="acta_no" value="<?= oldv('acta_no') ?>">
                    </td>
                </tr>
            </table>

            <!-- ORDEN DEL DIA -->

            <div class="flex-header">
                <h3>Orden del Día</h3>
            </div>

            <div class="texto">
                <ol style="padding-left:20px;line-height:1.9;">
                    <li>Constancia de asistencia.</li>
                    <li>Revisión de eficacia SG-SST.</li>
                    <li>Plan anual de trabajo.</li>
                    <li>Suficiencia de recursos.</li>
                    <li>Capacidad del SG-SST.</li>
                    <li>Necesidad de cambios.</li>
                    <li>Evaluación de seguimiento.</li>
                    <li>Indicadores y auditorías.</li>
                    <li>Nuevas prioridades.</li>
                    <li>Prevención y control.</li>
                    <li>Encuesta trabajadores.</li>
                    <li>Toma de decisiones.</li>
                    <li>Participación trabajadores.</li>
                    <li>Cumplimiento normativo.</li>
                    <li>Cumplimiento de metas.</li>
                    <li>Inspecciones.</li>
                    <li>Condiciones de trabajo.</li>
                    <li>Matriz de peligros.</li>
                    <li>Resultados revisión.</li>
                    <li>Recomendaciones.</li>
                    <li>Aprobación acta.</li>
                </ol>
            </div>

            <!-- INFORMACION ENTRADA -->

            <div class="flex-header">
                <h3>Información de Entrada</h3>
            </div>

            <textarea name="informacion_entrada"><?= oldv('informacion_entrada') ?></textarea>

            <!-- ASISTENCIA -->

            <div class="flex-header">
                <h3>1. Constancia de Asistencia</h3>

                <button type="button"
                        class="btn-add-fila print-hide"
                        onclick="agregarFila('tbodyAsistencia')">
                    + AGREGAR FILA
                </button>
            </div>

            <table>
                <thead>
                <tr class="bg-blue-light">
                    <th>Nombre Completo</th>
                    <th>Identificación</th>
                    <th>Cargo</th>
                    <th>Firma</th>
                </tr>
                </thead>

                <tbody id="tbodyAsistencia">
                <tr>
                    <td><input type="text" name="nombre[]"></td>
                    <td><input type="text" name="identificacion[]"></td>
                    <td><input type="text" name="cargo[]"></td>
                    <td style="background:#eee;"></td>
                </tr>
                </tbody>
            </table>

            <!-- EFECTIVIDAD -->

            <div class="flex-header">
                <h3>2. Intervenciones Ejecutadas</h3>
            </div>

            <table>
                <thead>
                <tr class="bg-blue-light">
                    <th>INTERVENCIÓN</th>
                    <th>SÍ</th>
                    <th>NO</th>
                </tr>
                </thead>

                <tbody>

                <?php
                $intervenciones = [
                    "Políticas, Objetivos y Metas",
                    "Accidentalidad y enfermedad laboral",
                    "Participación y consulta",
                    "Acciones correctivas",
                    "Seguimiento gerencial",
                    "Cambios SG-SST",
                    "Programas de gestión",
                    "Cumplimiento legal",
                    "Necesidad de recursos"
                ];

                foreach ($intervenciones as $i => $item):
                ?>

                    <tr>
                        <td><?= $item ?></td>

                        <td style="text-align:center;">
                            <input type="checkbox" name="si_<?= $i ?>">
                        </td>

                        <td style="text-align:center;">
                            <input type="checkbox" name="no_<?= $i ?>">
                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

            <!-- PLAN TRABAJO -->

            <div class="flex-header">
                <h3>3. Cumplimiento Plan Anual</h3>
            </div>

            <textarea name="cumplimiento_plan"><?= oldv('cumplimiento_plan') ?></textarea>

            <!-- RECURSOS -->

            <div class="flex-header">
                <h3>4. Suficiencia de Recursos</h3>
            </div>

            <textarea name="suficiencia_recursos"><?= oldv('suficiencia_recursos') ?></textarea>

            <!-- CAMBIOS -->

            <div class="flex-header">
                <h3>5. Cambios Requeridos al SG-SST</h3>
            </div>

            <textarea name="cambios_sgsst"><?= oldv('cambios_sgsst') ?></textarea>

            <!-- MEDIDAS -->

            <div class="flex-header">
                <h3>6. Evaluación Medidas Seguimiento</h3>

                <button type="button"
                        class="btn-add-fila print-hide"
                        onclick="agregarFila('tbodySeguimiento')">
                    + AGREGAR FILA
                </button>
            </div>

            <table>
                <thead>
                <tr class="bg-blue-light">
                    <th>Actividad</th>
                    <th>Medida Seguimiento</th>
                    <th>Calificación</th>
                    <th>Recomendaciones</th>
                </tr>
                </thead>

                <tbody id="tbodySeguimiento">
                <tr>
                    <td><textarea name="actividad[]"></textarea></td>
                    <td><textarea name="medida[]"></textarea></td>
                    <td><input type="text" name="calificacion[]"></td>
                    <td><textarea name="recomendacion[]"></textarea></td>
                </tr>
                </tbody>
            </table>

            <!-- AUDITORIAS -->

            <div class="flex-header">
                <h3>7. Indicadores y Auditorías</h3>
            </div>

            <textarea name="indicadores_auditorias"><?= oldv('indicadores_auditorias') ?></textarea>

            <!-- PRIORIDADES -->

            <div class="flex-header">
                <h3>8. Nuevas Prioridades</h3>
            </div>

            <textarea name="prioridades"><?= oldv('prioridades') ?></textarea>

            <!-- PREVENCION -->

            <div class="flex-header">
                <h3>9. Prevención y Control</h3>
            </div>

            <textarea name="prevencion_control"><?= oldv('prevencion_control') ?></textarea>

            <!-- DECISIONES -->

            <div class="flex-header">
                <h3>10. Acciones y Decisiones</h3>

                <button type="button"
                        class="btn-add-fila print-hide"
                        onclick="agregarFila('tbodyAcciones')">
                    + AGREGAR FILA
                </button>
            </div>

            <table>
                <thead>
                <tr class="bg-blue-light">
                    <th>Acción</th>
                    <th>Plazo</th>
                    <th>Responsable</th>
                    <th>Fecha</th>
                </tr>
                </thead>

                <tbody id="tbodyAcciones">
                <tr>
                    <td><textarea name="accion[]"></textarea></td>
                    <td><input type="text" name="plazo[]"></td>
                    <td><input type="text" name="responsable[]"></td>
                    <td><input type="date" name="fecha[]"></td>
                </tr>
                </tbody>
            </table>

            <!-- PARTICIPACION -->

            <div class="flex-header">
                <h3>11. Participación Trabajadores</h3>
            </div>

            <textarea name="participacion"><?= oldv('participacion') ?></textarea>

            <!-- NORMATIVIDAD -->

            <div class="flex-header">
                <h3>12. Cumplimiento Normativo</h3>
            </div>

            <textarea name="cumplimiento_normativo"><?= oldv('cumplimiento_normativo') ?></textarea>

            <!-- METAS -->

            <div class="flex-header">
                <h3>13. Metas y Objetivos</h3>
            </div>

            <textarea name="metas_objetivos"><?= oldv('metas_objetivos') ?></textarea>

            <!-- INSPECCIONES -->

            <div class="flex-header">
                <h3>14. Inspecciones</h3>
            </div>

            <textarea name="inspecciones"><?= oldv('inspecciones') ?></textarea>

            <!-- CONDICIONES -->

            <div class="flex-header">
                <h3>15. Condiciones de Trabajo</h3>
            </div>

            <textarea name="condiciones"><?= oldv('condiciones') ?></textarea>

            <!-- MATRIZ -->

            <div class="flex-header">
                <h3>16. Matriz de Peligros</h3>
            </div>

            <textarea name="matriz_peligros"><?= oldv('matriz_peligros') ?></textarea>

            <!-- RESULTADOS -->

            <div class="flex-header">
                <h3>17. Resultados Revisión</h3>
            </div>

            <textarea name="resultados_revision"><?= oldv('resultados_revision') ?></textarea>

            <!-- RECOMENDACIONES -->

            <div class="flex-header">
                <h3>18. Recomendaciones</h3>
            </div>

            <textarea name="recomendaciones"><?= oldv('recomendaciones') ?></textarea>

            <!-- FIRMAS -->

            <div class="firma-box">

                <div class="firma">
                    <strong>Representante Legal</strong><br>
                    C.C.
                </div>

                <div class="firma">
                    <strong>Responsable SG-SST</strong><br>
                    Licencia No.
                </div>

            </div>

        </form>

    </div>

</div>

<script>

function agregarFila(idTbody)
{
    const tbody = document.getElementById(idTbody);

    const tr = document.createElement('tr');

    if(idTbody === 'tbodyAsistencia')
    {
        tr.innerHTML = `
            <td><input type="text" name="nombre[]"></td>
            <td><input type="text" name="identificacion[]"></td>
            <td><input type="text" name="cargo[]"></td>
            <td style="background:#eee;"></td>
        `;
    }

    if(idTbody === 'tbodySeguimiento')
    {
        tr.innerHTML = `
            <td><textarea name="actividad[]"></textarea></td>
            <td><textarea name="medida[]"></textarea></td>
            <td><input type="text" name="calificacion[]"></td>
            <td><textarea name="recomendacion[]"></textarea></td>
        `;
    }

    if(idTbody === 'tbodyAcciones')
    {
        tr.innerHTML = `
            <td><textarea name="accion[]"></textarea></td>
            <td><input type="text" name="plazo[]"></td>
            <td><input type="text" name="responsable[]"></td>
            <td><input type="date" name="fecha[]"></td>
        `;
    }

    tbody.appendChild(tr);
}

document.getElementById('btnGuardar').addEventListener('click', async function(){

    const btn = this;

    const formData = new FormData(document.getElementById('form6131'));

    const datosJSON = Object.fromEntries(formData.entries());

    btn.innerText = 'Guardando...';
    btn.disabled = true;

    try {

        const response = await fetch(
            "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar",
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer <?= $token ?>'
                },

                body: JSON.stringify({
                    id_empresa: <?= $empresa ?>,
                    id_item_sst: <?= $idItem ?>,
                    datos: datosJSON
                })
            }
        );

        const res = await response.json();

        if(res.ok)
        {
            Swal.fire(
                'Éxito',
                'Acta guardada correctamente',
                'success'
            );
        }
        else
        {
            Swal.fire(
                'Error',
                res.error || 'No se pudo guardar',
                'error'
            );
        }

    } catch(e){

        Swal.fire(
            'Error',
            'Fallo de conexión',
            'error'
        );

    } finally {

        btn.innerText = 'Guardar Acta';
        btn.disabled = false;
    }

});

</script>

</body>
</html>