<?php
session_start();

// 1. SECUENCIA DE CONEXIÓN A LA API
require_once '../../../includes/ConexionAPI.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../../index.php');
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"] ?? "";
$empresa = (int)($_SESSION["id_empresa"] ?? 0);
// Ajusta el ID de este ítem según tu base de datos para este formato (ej. 51)
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 51;

// --- Lógica de Empresa (Logo) ---
$logoEmpresaUrl = "";
if ($empresa > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresa", "GET", null, $token);
    if (isset($resEmpresa['data']) && !empty($resEmpresa['data'])) {
        $empData = isset($resEmpresa['data'][0]) ? $resEmpresa['data'][0] : $resEmpresa['data'];
        $logoEmpresaUrl = $empData['logo_url'] ?? '';
    }
}

// 2. SOLICITAMOS LOS DATOS GUARDADOS PREVIAMENTE
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

// Función para leer datos desde la API
function oldv($key, $default = '')
{
    global $datosCampos;
    return (isset($datosCampos[$key]) && $datosCampos[$key] !== '') ? htmlspecialchars((string)$datosCampos[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$datos = [
    'version'         => oldv('version', '0'),
    'codigo'          => oldv('codigo', 'RE-SST-24'),
    'fecha_documento' => oldv('fecha_documento', 'XX/XX/2025'),
    'dia'             => oldv('dia', ''),
    'mes'             => oldv('mes', ''),
    'anio'            => oldv('anio', ''),
    'proceso'         => oldv('proceso', ''),
    'nombre'          => oldv('nombre', ''),
    'cargo'           => oldv('cargo', ''),
    'trabajador'      => oldv('trabajador', ''),
];

$categorias = [
    'FÍSICOS' => [
        'Ruido',
        'Iluminación',
        'Vibraciones',
        'Calor',
        'Frío',
        'Radiaciones ionizantes (rayos x, gama, beta, alfa y neutrones)',
        'Radiaciones no ionizantes (ultravioleta, visible, infrarroja)',
        'Presiones barométricas anormales',
    ],
    'QUÍMICO' => [
        'Sólidos (fibras, polvos orgánicos e inorgánicos, humo metálico y no metálico)',
        'Líquidos (nieblas, rocíos, corrosivos, disolventes, inflamables, etc.)',
        'Gases (metano, dióxido de carbono)',
        'Vapores (gasolina, agua, etc.) / manipulación de químicos',
    ],
    'BIOMECÁNICOS' => [
        'Organización del trabajo (secuencias, ritmos, rutas, jornadas, turnos, rotaciones, descansos)',
        'Almacenamiento y movilización de materiales',
        'Técnicas de manipulación y levantamiento de cargas',
        'Diseño de puesto de trabajo (relación máquina, herramienta y materiales, superficie de trabajo, silla, ubicación de controles inadecuados)',
        'Posturas incorrectas',
        'Postura mantenida',
        'Posiciones de rodillas o de cuclillas por 20 minutos o más',
        'Posturas prolongadas',
        'Posturas forzadas o por fuera de los ángulos de confort',
        'Requerimientos excesivos de fuerza',
        'Requerimientos de fuerza superior a la capacidad del individuo',
        'La fuerza se realiza asociada a cargas estáticas altas',
        'Requerimientos de fuerza asociados a cargas dinámicas altas',
        'Requerimientos excesivos de movimiento',
        'El movimiento se realiza sobre una carga estática alta',
        'Repetitividad de movimientos',
        'Asociación de repetitividad y fuerza',
    ],
    'BIOLÓGICOS' => [
        'Microorganismos',
        'Sustancias animales y vegetales',
        'Parásitos',
        'Animales y vectores',
        'Presencia de productos descompuestos',
        'Desconocimiento de normas de conservación, clasificación, empaque y almacenamiento',
        'Empaques defectuosos y sin fecha de vencimiento',
        'Presencia de productos alimenticios a nivel del suelo',
        'Riesgo de infección: trabajo con productos contaminados',
    ],
    'MECÁNICOS' => [
        'Caídas de altura',
        'Atrapamiento',
        'Caídas de objetos',
        'Proyecciones',
        'Fricciones',
        'Caídas a nivel',
        'Choque',
        'Cortes',
        'Maquinaria sin anclaje',
        'Punto de transmisión de fuerza sin protección',
        'Punto de operación sin protección',
        'Máquinas, equipos y herramientas defectuosas',
        'Herramientas manuales defectuosas',
        'Herramientas eléctricas defectuosas',
        'Herramientas neumáticas defectuosas',
        'Mecanismos en movimiento, elementos móviles, cortantes',
        'Deficiencia en mantenimiento preventivo',
        'Almacenamiento y movilización de materiales',
        'Arrumes elevados sin estibas',
        'Cargas elevadas no trabadas',
        'Cargas apoyadas contra muros',
        'Carencia de ayudas mecánicas para levantamiento de cargas',
        'Existencia de equipos que generan chispa',
        'Gabinetes sin o con elementos defectuosos',
    ],
    'ELÉCTRICOS' => [
        'Electricidad estática',
        'Líneas conductoras sin entubar',
        'Contacto con líneas o puntos energizados',
        'Sin polo a tierra',
        'Empalmes defectuosos',
        'Cajas, interruptores, tomas, terminales, cables, tacos, empalmes y acometidas en mal estado',
        'Sin instalaciones eléctricas de seguridad',
        'Instalaciones eléctricas sobrecargadas',
    ],
    'LOCATIVOS' => [
        'Sin señalización ni demarcación',
        'Falta de orden y aseo',
        'Almacenamiento inadecuado',
        'Superficies de trabajo defectuosas',
        'Escaleras y rampas inadecuadas',
        'Techos defectuosos',
        'Ventilación insuficiente',
        'Sin sistemas de extinción de incendios',
        'Extintores defectuosos',
        'Sin salidas de emergencia, obstaculizadas o sin señalización',
        'Inadecuada selección de extintor de acuerdo al material combustible',
        'Pisos, barandas, escaleras defectuosas',
        'Muros, puertas, ventanas defectuosas',
        'Hacinamiento (relación espacio con puestos de trabajo)',
        'Deficiente espacio destinado para la actividad',
        'Mal uso del espacio',
        'Áreas de circulación insuficiente',
        'Áreas de circulación obstruidas',
    ],
    'PSICOLABORALES - ADMINISTRATIVO' => [
        'Jornadas prolongadas de trabajo / trabajo nocturno',
        'Ritmo intenso de trabajo / monotonía',
        'Insatisfacción en el trabajo',
        'Inestabilidad económica',
        'Ausencia de manuales de operación o de funcionamiento',
        'Ausencia de normas de seguridad',
        'Incentivos por producción',
        'Sobrecarga de trabajo cualitativa o cuantitativa',
        'Conflictos de autoridad',
        'Perfil psicológico del trabajador',
        'Contexto extra laboral',
        'Carga mental',
        'Hábitos y costumbres inadecuadas',
        'Poca conciencia preventiva',
        'Insatisfacción',
        'Poca motivación',
        'Poca habilidad y aptitud de aprendizaje',
        'Deficiencias físicas',
        'Talla, peso y fuerza inapropiadas',
        'Tiempo de reacción lento',
        'Disturbios emocionales',
        'Inducción y entrenamiento deficiente',
        'Estándares (normas) y procedimientos de trabajo inadecuados',
        'Carencia de subsistemas de información',
        'Carencia de recursos para el control efectivo de los factores de riesgo',
        'Adquisiciones sin visto bueno',
        'Selección inadecuada del personal',
        'Falta de programas de mantenimiento',
        'Sin brigadas contra incendios o sin capacitación',
        'Incumplimiento a los requisitos del cliente',
        'Error o desacierto estratégico',
    ],
    'TECNOLÓGICOS / FÍSICO-QUÍMICOS' => [
        'Incendio',
        'Explosión',
        'Incendio y explosión',
        'Incompatibilidad físico-química en materias primas',
        'Presencia de sustancias, materiales o productos de fácil combustión',
    ],
    'PÚBLICOS' => [
        'Delincuencia común',
        'Delincuencia organizada',
        'Hurto o robo',
        'Lesiones personales',
        'Asonada o motín',
        'Secuestro o extorsión',
        'Acciones de grupos al margen de la ley',
        'Actos mal intencionados de terceros',
        'Incumplimiento de normas de tránsito',
        'Contrabando',
        'Tráfico de drogas',
        'Piratería terrestre',
        'Lavado de activos',
        'Terrorismo',
    ],
    'IMPACTOS AMBIENTALES' => [
        'Agotamiento recurso natural (no renovable)',
        'Agotamiento recurso natural (renovable)',
        'Contaminación agua',
        'Contaminación agua y suelo',
        'Contaminación aire',
        'Contaminación del suelo',
    ],
];

$firma_trabajador = oldv('firma_trabajador', '');
$indice = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.1.2-2 Identificación de Factores de Riesgo</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1500px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:18px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}
        .contenido{padding:18px}
        
        table{width:100%;border-collapse:collapse;table-layout:fixed}
        .encabezado td,.encabezado th,.tabla-datos td,.tabla-datos th,.tabla-riesgos td,.tabla-riesgos th{
            border:1px solid #6b6b6b;
            padding:4px 5px;
            vertical-align:middle;
            overflow-wrap:anywhere;
            word-break:break-word;
        }

        .encabezado td,.encabezado th{text-align:center}
        .logo-box{
            width:140px;height:65px;display:flex;align-items:center;justify-content:center;
            margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center
        }
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px;font-weight:700;line-height:1.25}

        .tabla-datos td{
            font-size:12px;
            padding:5px;
        }
        .tabla-datos td.label{
            width:12%;
            font-weight:700;
            background:#f8fafc;
        }
        .tabla-datos input{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px;
            font-size:12px;
        }

        .descripcion-box{
            border:1px solid #6b6b6b;
            border-top:none;
            padding:7px 10px;
            text-align:center;
            font-size:13px;
            font-weight:700;
            background:#f8fafc;
        }

        .tabla-riesgos th,
        .tabla-riesgos td{
            font-size:12px;
            line-height:1.2;
        }

        .tabla-riesgos .cat{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            font-weight:700;
        }

        .tabla-riesgos .subhead{
            background:#8eaadb;
            color:#fff;
            text-align:center;
            font-weight:700;
            font-size:11px;
        }

        .tabla-riesgos td.factor{
            text-align:left;
        }

        .tabla-riesgos td.sel,
        .tabla-riesgos td.obs{
            text-align:center;
        }

        .tabla-riesgos input[type="text"],
        .tabla-riesgos textarea{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px 2px;
            font-size:12px;
            line-height:1.2;
        }

        .tabla-riesgos textarea{
            resize:vertical;
            min-height:28px;
            white-space:pre-wrap;
            word-break:break-word;
            overflow-wrap:anywhere;
        }

        .check-x{
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .check-x input[type="checkbox"]{
            width:18px;
            height:18px;
            cursor:pointer;
            accent-color:#0d6efd;
        }

        .firma-box{
            height:44px;
        }

        .firma-label{
            text-align:center;
            font-weight:700;
            font-size:12px;
            background:#f8fafc;
        }

        .w-factor{width:47%}
        .w-sel{width:7%}
        .w-controles{width:23%}
        .w-obs{width:23%}

        @media (max-width: 980px){
            .toolbar{position:static}
            body{padding:10px}
        }

        @page{
            size: portrait;
            margin: 8mm;
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar, .print-hide{display:none !important}
            .contenedor{box-shadow:none;border:none;max-width:100%}
            .contenido{padding:5px}
            .tabla-riesgos th,
            .tabla-riesgos td,
            .tabla-datos td,
            .encabezado td{
                font-size:10px !important;
                padding:2px 3px !important;
            }
            .tabla-riesgos textarea{
                min-height:22px !important;
                font-size:10px !important;
            }
            .tabla-riesgos input[type="text"],
            .tabla-datos input{
                font-size:10px !important;
            }
            .check-x input[type="checkbox"]{
                width:14px;
                height:14px;
            }
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>4.1.2-2 Identificación de Factores de Riesgo en los Puestos de Trabajo</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Datos</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <form id="form4122">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%; padding:0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">IDENTIFICACIÓN DE FACTORES DE RIESGO EN LOS PUESTOS DE TRABAJO</td>
                    <td style="width:22%;font-weight:700;"><?php echo $datos['version']; ?></td>
                </tr>
                <tr>
                    <td class="subtitulo">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="font-weight:700;">
                        <?php echo $datos['codigo']; ?><br>
                        <?php echo $datos['fecha_documento']; ?>
                    </td>
                </tr>
            </table>

            <table class="tabla-datos" style="margin-top:0;">
                <tr>
                    <td class="label">Fecha:</td>
                    <td><input type="text" name="dia" value="<?php echo $datos['dia']; ?>" placeholder="DD"></td>
                    <td><input type="text" name="mes" value="<?php echo $datos['mes']; ?>" placeholder="MM"></td>
                    <td><input type="text" name="anio" value="<?php echo $datos['anio']; ?>" placeholder="AAAA"></td>
                    <td class="label">Proceso:</td>
                    <td><input type="text" name="proceso" value="<?php echo $datos['proceso']; ?>"></td>
                    <td class="label">Nombre:</td>
                    <td><input type="text" name="nombre" value="<?php echo $datos['nombre']; ?>"></td>
                    <td class="label">Cargo:</td>
                    <td><input type="text" name="cargo" value="<?php echo $datos['cargo']; ?>"></td>
                </tr>
            </table>

            <div class="descripcion-box">
                Con ayuda de la siguiente descripción de factores de riesgo e impactos ambientales seleccione cuáles se presentan en su puesto de trabajo por medio de una X.
            </div>

            <table class="tabla-riesgos">
                <colgroup>
                    <col class="w-factor">
                    <col class="w-sel">
                    <col class="w-controles">
                    <col class="w-obs">
                </colgroup>

                <?php foreach ($categorias as $categoria => $items): ?>
                    <tr>
                        <th class="cat"><?php echo $categoria; ?></th>
                        <th class="subhead">SELECCIÓN</th>
                        <th class="subhead">CONTROLES DE LA EMPRESA</th>
                        <th class="subhead">OBSERVACIONES</th>
                    </tr>

                    <?php foreach ($items as $item): ?>
                        <?php
                        $seleccion = oldv("factores[$indice][seleccion]") === 'X';
                        $controles = oldv("factores[$indice][controles]");
                        $observaciones = oldv("factores[$indice][observaciones]");
                        ?>
                        <tr>
                            <td class="factor">
                                <?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>
                                <input type="hidden" name="factores[<?php echo $indice; ?>][factor]" value="<?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="factores[<?php echo $indice; ?>][categoria]" value="<?php echo htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>">
                            </td>
                            <td class="sel">
                                <div class="check-x">
                                    <input type="checkbox" name="factores[<?php echo $indice; ?>][seleccion]" value="X" <?php echo $seleccion ? 'checked' : ''; ?>>
                                </div>
                            </td>
                            <td>
                                <textarea name="factores[<?php echo $indice; ?>][controles]"><?php echo $controles; ?></textarea>
                            </td>
                            <td class="obs">
                                <textarea name="factores[<?php echo $indice; ?>][observaciones]"><?php echo $observaciones; ?></textarea>
                            </td>
                        </tr>
                        <?php $indice++; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <tr>
                    <td class="firma-box"></td>
                    <td colspan="3" class="firma-box"></td>
                </tr>
                <tr>
                    <td class="firma-label">NOMBRE DEL TRABAJADOR</td>
                    <td colspan="3" class="firma-label">FIRMA DEL TRABAJADOR</td>
                </tr>
                <tr>
                    <td><input type="text" name="trabajador" value="<?php echo $datos['trabajador']; ?>"></td>
                    <td colspan="3"><input type="text" name="firma_trabajador" value="<?php echo $firma_trabajador; ?>"></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>
// ----------------------------------------------------
// INTEGRACIÓN DE GUARDADO CON FETCH API Y SWEETALERT2
// ----------------------------------------------------
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('form4122');
    const formData = new FormData(form);
    
    // Convertimos el formulario en un objeto JSON limpio
    const datosJSON = Object.fromEntries(formData.entries());

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
                text: 'La identificación de factores de riesgo se ha guardado correctamente.',
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
// ----------------------------------------------------
</script>
</body>
</html>