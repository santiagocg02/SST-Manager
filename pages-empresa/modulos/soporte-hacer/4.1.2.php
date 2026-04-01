<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../index.php');
    exit;
}

function post($key, $default = '')
{
    return isset($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$datos = [
    'version' => post('version', '0'),
    'codigo' => post('codigo', 'AN-SST-08'),
    'fecha_documento' => post('fecha_documento', 'XX/XX/2025'),
    'elaboro' => post('elaboro'),
    'cargo' => post('cargo'),
    'fecha_elaboracion' => post('fecha_elaboracion'),
    'fecha_actualizacion' => post('fecha_actualizacion'),
];

$filas = isset($_POST['matriz']) && is_array($_POST['matriz']) ? $_POST['matriz'] : [];
if (empty($filas)) {
    $filas = [
        [
            'proceso' => '',
            'zona_lugar' => '',
            'actividades' => '',
            'tareas' => '',
            'rutinaria' => '',
            'peligro_desc' => '',
            'peligro_clas' => '',
            'efectos_posibles' => '',
            'ctrl_fuente' => '',
            'ctrl_medio' => '',
            'ctrl_individuo' => '',
            'nd' => '',
            'ne' => '',
            'np' => '',
            'interp_np' => '',
            'nc' => '',
            'nr' => '',
            'interp_nr' => '',
            'aceptabilidad' => '',
            'expuestos' => '',
            'peor_consecuencia' => '',
            'eliminacion' => '',
            'sustitucion' => '',
            'ingenieria' => '',
            'administrativos' => '',
            'epp' => '',
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.1.2 Matriz para la Identificación de Peligros y Valoración de Riesgos GTC-45</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{max-width:1700px;margin:0 auto;background:#fff;border:1px solid #bfc7d1;box-shadow:0 4px 18px rgba(0,0,0,.08)}
        .toolbar{position:sticky;top:0;z-index:100;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#dde7f5;border-bottom:1px solid #c8d3e2}
        .toolbar h1{font-size:16px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{border:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s ease}
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-guardar{background:#198754;color:#fff}
        .btn-atras{background:#6c757d;color:#fff}
        .btn-imprimir{background:#0d6efd;color:#fff}
        .btn-agregar{background:#0d6efd;color:#fff}
        .contenido{padding:18px}
        .save-msg{
            margin:0 0 15px 0;padding:10px 14px;border-radius:8px;background:#e9f7ef;color:#166534;
            border:1px solid #b7e4c7;font-size:14px;font-weight:700;
        }

        table{width:100%;border-collapse:collapse;table-layout:fixed}
        .encabezado td,.encabezado th,.tabla-datos td,.tabla-datos th,.tabla-matriz td,.tabla-matriz th{
            border:1px solid #6b6b6b;
            padding:4px;
            vertical-align:middle;
            word-break:break-word;
            overflow-wrap:anywhere;
        }
        .encabezado td,.encabezado th{text-align:center}
        .logo-box{
            width:140px;height:65px;border:2px dashed #c8c8c8;display:flex;align-items:center;justify-content:center;
            margin:auto;color:#999;font-weight:bold;font-size:14px;text-align:center
        }
        .titulo-principal{font-size:16px;font-weight:700}
        .subtitulo{font-size:14px;font-weight:700;line-height:1.25}

        .tabla-datos td:first-child{
            width:25%;
            font-weight:700;
            background:#f8fafc;
        }
        .tabla-datos input{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:4px;
            font-size:13px;
        }

        .franja-titulo{
            margin-top:14px;
            margin-bottom:0;
            border:1px solid #2d61bf;
            border-bottom:none;
            background:#fff;
            text-align:center;
            padding:10px;
            font-size:15px;
            font-weight:700;
            color:#3c6fd4;
            font-style:italic;
            letter-spacing:.2px;
        }

        .tabla-wrap{
            width:100%;
            overflow-x:auto;
            border:1px solid #6b6b6b;
            border-top:none;
        }

        .tabla-matriz{
            min-width:1850px;
            width:1850px;
            table-layout:fixed;
        }

        .tabla-matriz thead th{
            text-align:center;
            font-size:10px;
            line-height:1.15;
            padding:5px 3px;
            writing-mode:vertical-rl;
            transform:rotate(180deg);
            white-space:normal;
        }
        .tabla-matriz thead th.group{
            writing-mode:horizontal-tb;
            transform:none;
            font-size:12px;
            padding:10px 4px;
            color:#fff;
            background:#0f73bd;
        }
        .tabla-matriz thead th.group-dark{
            background:#0e5a96;
        }
        .tabla-matriz thead th.group-vertical{
            background:#0e5a96;
            color:#fff;
            width:36px;
        }
        .tabla-matriz thead tr.sub th{
            background:#f8f8f8;
            color:#222;
            font-weight:700;
        }

        .tabla-matriz td{
            font-size:11px;
            padding:3px;
            text-align:center;
            min-height:34px;
        }

        .tabla-matriz textarea,
        .tabla-matriz input,
        .tabla-matriz select{
            width:100%;
            max-width:100%;
            border:none;
            outline:none;
            background:transparent;
            padding:3px 2px;
            font-size:11px;
            line-height:1.2;
        }

        .tabla-matriz textarea{
            resize:vertical;
            min-height:42px;
            white-space:pre-wrap;
            word-break:break-word;
            overflow-wrap:anywhere;
        }

        .texto-izq textarea,
        .texto-izq input{
            text-align:left;
        }

        .riesgo-I{background:#ff1f1f !important;color:#111;font-weight:700}
        .riesgo-II{background:#fff200 !important;color:#111;font-weight:700}
        .riesgo-III{background:#00ff00 !important;color:#111;font-weight:700}
        .riesgo-IV{background:#19a9e5 !important;color:#111;font-weight:700}

        .np-ma{background:#ff1f1f !important;color:#111;font-weight:700}
        .np-a{background:#f39c12 !important;color:#111;font-weight:700}
        .np-m{background:#009900 !important;color:#fff;font-weight:700}
        .np-b{background:#19a9e5 !important;color:#111;font-weight:700}

        .acciones-tabla{
            margin-top:10px;
            display:flex;
            justify-content:flex-end;
        }

        .nota{
            margin-top:8px;
            font-size:12px;
            color:#4b5563;
        }

        @media (max-width: 980px){
            .toolbar{position:static}
            body{padding:10px}
        }

        @page{
            size: landscape;
            margin: 8mm;
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar,.acciones-tabla,.nota{display:none}
            .contenedor{box-shadow:none;border:none;max-width:100%}
            .contenido{padding:5px}
            .tabla-wrap{overflow:visible;border:1px solid #6b6b6b;border-top:none}
            .tabla-matriz{
                width:100% !important;
                min-width:100% !important;
                table-layout:fixed !important;
            }
            .tabla-matriz thead th{
                font-size:8px !important;
                padding:2px !important;
            }
            .tabla-matriz thead th.group{
                font-size:9px !important;
                padding:5px 2px !important;
            }
            .tabla-matriz td{
                font-size:8px !important;
                padding:2px !important;
            }
            .tabla-matriz textarea,
            .tabla-matriz input,
            .tabla-matriz select{
                font-size:8px !important;
                padding:1px !important;
            }
            .tabla-matriz textarea{
                min-height:24px !important;
            }
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>4.1.2 Matriz para la Identificación de Peligros y la Valoración de Riesgos GTC-45</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form412">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="contenido">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form412" method="POST" action="">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:18%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:58%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:24%;font-weight:700;"><?php echo $datos['version']; ?></td>
                </tr>
                <tr>
                    <td class="subtitulo">MATRIZ PARA LA IDENTIFICACIÓN DE PELIGROS Y LA VALORACIÓN DE RIESGOS GTC-45</td>
                    <td style="font-weight:700;">
                        <?php echo $datos['codigo']; ?><br>
                        <?php echo $datos['fecha_documento']; ?>
                    </td>
                </tr>
            </table>

            <table class="tabla-datos" style="margin-top:8px;">
                <tr>
                    <td>NOMBRE QUIEN ELABORÓ</td>
                    <td><input type="text" name="elaboro" value="<?php echo $datos['elaboro']; ?>"></td>
                    <td>CARGO</td>
                    <td><input type="text" name="cargo" value="<?php echo $datos['cargo']; ?>"></td>
                </tr>
                <tr>
                    <td>FECHA DE ELABORACIÓN</td>
                    <td><input type="date" name="fecha_elaboracion" value="<?php echo $datos['fecha_elaboracion']; ?>"></td>
                    <td>FECHA DE ACTUALIZACIÓN</td>
                    <td><input type="date" name="fecha_actualizacion" value="<?php echo $datos['fecha_actualizacion']; ?>"></td>
                </tr>
            </table>

            <div class="franja-titulo">VALORACIÓN DE RIESGOS Y DETERMINACIÓN DE CONTROLES</div>

            <div class="tabla-wrap">
                <table class="tabla-matriz" id="tablaMatriz">
                    <thead>
                        <tr>
                            <th class="group" colspan="8">IDENTIFICACIÓN DE PELIGROS</th>
                            <th class="group group-dark" colspan="3">MEDIDAS DE CONTROL</th>
                            <th class="group group-dark" colspan="7">EVALUACIÓN DEL RIESGO</th>
                            <th class="group-vertical" rowspan="2">Valoración del riesgo</th>
                            <th class="group" colspan="2">CRITERIO PARA CONTROLES</th>
                            <th class="group" colspan="5">MEDIDAS DE INTERVENCIÓN</th>
                        </tr>
                        <tr class="sub">
                            <th>PROCESO</th>
                            <th>ZONA / LUGAR</th>
                            <th>ACTIVIDADES</th>
                            <th>TAREAS</th>
                            <th>TIPO DE TAREA</th>
                            <th>PELIGRO DESCRIPCIÓN</th>
                            <th>CLASIFICACIÓN</th>
                            <th>EFECTOS POSIBLES</th>

                            <th>FUENTE</th>
                            <th>MEDIO</th>
                            <th>INDIVIDUO</th>

                            <th>ND</th>
                            <th>NE</th>
                            <th>NP</th>
                            <th>INTERPRETACIÓN NP</th>
                            <th>NC</th>
                            <th>NR</th>
                            <th>INTERPRETACIÓN DEL NIVEL DE RIESGO</th>

                            <th>No. EXPUESTOS</th>
                            <th>PEOR CONSECUENCIA</th>

                            <th>ELIMINACIÓN</th>
                            <th>SUSTITUCIÓN</th>
                            <th>CONTROLES DE INGENIERÍA</th>
                            <th>CONTROLES ADMINISTRATIVOS, SEÑALIZACIÓN, ADVERTENCIA</th>
                            <th>EQUIPOS Y ELEMENTOS DE PROTECCIÓN PERSONAL</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMatriz">
                        <?php foreach ($filas as $i => $fila): ?>
                            <tr>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][proceso]"><?php echo htmlspecialchars($fila['proceso'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][zona_lugar]"><?php echo htmlspecialchars($fila['zona_lugar'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][actividades]"><?php echo htmlspecialchars($fila['actividades'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][tareas]"><?php echo htmlspecialchars($fila['tareas'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td>
                                    <select name="matriz[<?php echo $i; ?>][rutinaria]">
                                        <option value=""></option>
                                        <option value="RUTINARIA" <?php echo (($fila['rutinaria'] ?? '') === 'RUTINARIA') ? 'selected' : ''; ?>>RUTINARIA</option>
                                        <option value="NO RUTINARIA" <?php echo (($fila['rutinaria'] ?? '') === 'NO RUTINARIA') ? 'selected' : ''; ?>>NO RUTINARIA</option>
                                    </select>
                                </td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][peligro_desc]"><?php echo htmlspecialchars($fila['peligro_desc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][peligro_clas]"><?php echo htmlspecialchars($fila['peligro_clas'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][efectos_posibles]"><?php echo htmlspecialchars($fila['efectos_posibles'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>

                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][ctrl_fuente]"><?php echo htmlspecialchars($fila['ctrl_fuente'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][ctrl_medio]"><?php echo htmlspecialchars($fila['ctrl_medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][ctrl_individuo]"><?php echo htmlspecialchars($fila['ctrl_individuo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>

                                <td><input type="number" step="any" class="campo-nd" name="matriz[<?php echo $i; ?>][nd]" value="<?php echo htmlspecialchars($fila['nd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="number" step="any" class="campo-ne" name="matriz[<?php echo $i; ?>][ne]" value="<?php echo htmlspecialchars($fila['ne'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="number" step="any" class="campo-np" name="matriz[<?php echo $i; ?>][np]" value="<?php echo htmlspecialchars($fila['np'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" class="campo-interp-np" name="matriz[<?php echo $i; ?>][interp_np]" value="<?php echo htmlspecialchars($fila['interp_np'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="number" step="any" class="campo-nc" name="matriz[<?php echo $i; ?>][nc]" value="<?php echo htmlspecialchars($fila['nc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="number" step="any" class="campo-nr" name="matriz[<?php echo $i; ?>][nr]" value="<?php echo htmlspecialchars($fila['nr'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><input type="text" class="campo-interp-nr" name="matriz[<?php echo $i; ?>][interp_nr]" value="<?php echo htmlspecialchars($fila['interp_nr'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>

                                <td><input type="text" class="campo-aceptabilidad" name="matriz[<?php echo $i; ?>][aceptabilidad]" value="<?php echo htmlspecialchars($fila['aceptabilidad'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>

                                <td><input type="number" step="any" name="matriz[<?php echo $i; ?>][expuestos]" value="<?php echo htmlspecialchars($fila['expuestos'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][peor_consecuencia]"><?php echo htmlspecialchars($fila['peor_consecuencia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>

                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][eliminacion]"><?php echo htmlspecialchars($fila['eliminacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][sustitucion]"><?php echo htmlspecialchars($fila['sustitucion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][ingenieria]"><?php echo htmlspecialchars($fila['ingenieria'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][administrativos]"><?php echo htmlspecialchars($fila['administrativos'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                <td class="texto-izq"><textarea name="matriz[<?php echo $i; ?>][epp]"><?php echo htmlspecialchars($fila['epp'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="acciones-tabla">
                <button type="button" class="btn btn-agregar" id="agregarFila">Agregar fila</button>
            </div>
            <div class="nota">La matriz inicia con una fila. Puedes seguir agregando filas sin afectar el diseño.</div>
        </form>
    </div>
</div>

<script>
(function(){
    const tbody = document.getElementById('tbodyMatriz');
    const btnAgregar = document.getElementById('agregarFila');

    function interpretarNP(np){
        if (np >= 24) return {texto:'MA', clase:'np-ma'};
        if (np >= 10) return {texto:'A', clase:'np-a'};
        if (np >= 6) return {texto:'M', clase:'np-m'};
        if (np >= 2) return {texto:'B', clase:'np-b'};
        return {texto:'', clase:''};
    }

    function interpretarNR(nr){
        if (nr >= 600) return {texto:'I', clase:'riesgo-I', acepta:'Inaceptable'};
        if (nr >= 150) return {texto:'II', clase:'riesgo-II', acepta:'No Aceptable o Aceptable con control específico'};
        if (nr >= 40) return {texto:'III', clase:'riesgo-III', acepta:'Mejorable'};
        if (nr > 0) return {texto:'IV', clase:'riesgo-IV', acepta:'Permisible'};
        return {texto:'', clase:'', acepta:''};
    }

    function limpiarClases(el, clases){
        clases.forEach(c => el.classList.remove(c));
    }

    function recalcularFila(tr){
        const nd = parseFloat(tr.querySelector('.campo-nd')?.value) || 0;
        const ne = parseFloat(tr.querySelector('.campo-ne')?.value) || 0;
        const nc = parseFloat(tr.querySelector('.campo-nc')?.value) || 0;

        const npInput = tr.querySelector('.campo-np');
        const interpNpInput = tr.querySelector('.campo-interp-np');
        const nrInput = tr.querySelector('.campo-nr');
        const interpNrInput = tr.querySelector('.campo-interp-nr');
        const aceptaInput = tr.querySelector('.campo-aceptabilidad');

        const np = nd * ne;
        const nr = np * nc;

        npInput.value = np > 0 ? np : '';
        nrInput.value = nr > 0 ? nr : '';

        const npData = interpretarNP(np);
        const nrData = interpretarNR(nr);

        interpNpInput.value = npData.texto;
        interpNrInput.value = nrData.texto;
        aceptaInput.value = nrData.acepta;

        limpiarClases(interpNpInput, ['np-ma','np-a','np-m','np-b']);
        limpiarClases(interpNrInput, ['riesgo-I','riesgo-II','riesgo-III','riesgo-IV']);
        limpiarClases(aceptaInput, ['riesgo-I','riesgo-II','riesgo-III','riesgo-IV']);

        if (npData.clase) interpNpInput.classList.add(npData.clase);
        if (nrData.clase) {
            interpNrInput.classList.add(nrData.clase);
            aceptaInput.classList.add(nrData.clase);
        }
    }

    function recalcularTodo(){
        tbody.querySelectorAll('tr').forEach(recalcularFila);
    }

    function crearFila(index){
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="texto-izq"><textarea name="matriz[${index}][proceso]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][zona_lugar]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][actividades]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][tareas]"></textarea></td>
            <td>
                <select name="matriz[${index}][rutinaria]">
                    <option value=""></option>
                    <option value="RUTINARIA">RUTINARIA</option>
                    <option value="NO RUTINARIA">NO RUTINARIA</option>
                </select>
            </td>
            <td class="texto-izq"><textarea name="matriz[${index}][peligro_desc]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][peligro_clas]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][efectos_posibles]"></textarea></td>

            <td class="texto-izq"><textarea name="matriz[${index}][ctrl_fuente]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][ctrl_medio]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][ctrl_individuo]"></textarea></td>

            <td><input type="number" step="any" class="campo-nd" name="matriz[${index}][nd]"></td>
            <td><input type="number" step="any" class="campo-ne" name="matriz[${index}][ne]"></td>
            <td><input type="number" step="any" class="campo-np" name="matriz[${index}][np]"></td>
            <td><input type="text" class="campo-interp-np" name="matriz[${index}][interp_np]"></td>
            <td><input type="number" step="any" class="campo-nc" name="matriz[${index}][nc]"></td>
            <td><input type="number" step="any" class="campo-nr" name="matriz[${index}][nr]"></td>
            <td><input type="text" class="campo-interp-nr" name="matriz[${index}][interp_nr]"></td>

            <td><input type="text" class="campo-aceptabilidad" name="matriz[${index}][aceptabilidad]"></td>

            <td><input type="number" step="any" name="matriz[${index}][expuestos]"></td>
            <td class="texto-izq"><textarea name="matriz[${index}][peor_consecuencia]"></textarea></td>

            <td class="texto-izq"><textarea name="matriz[${index}][eliminacion]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][sustitucion]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][ingenieria]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][administrativos]"></textarea></td>
            <td class="texto-izq"><textarea name="matriz[${index}][epp]"></textarea></td>
        `;
        return tr;
    }

    btnAgregar.addEventListener('click', function(){
        const index = tbody.querySelectorAll('tr').length;
        tbody.appendChild(crearFila(index));
    });

    document.addEventListener('input', function(e){
        if (e.target.closest('#tbodyMatriz')) {
            const tr = e.target.closest('tr');
            if (tr) recalcularFila(tr);
        }
    });

    recalcularTodo();
})();
</script>
</body>
</html>