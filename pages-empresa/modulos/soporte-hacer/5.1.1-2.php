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

$items = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : [
    ['actividad' => 'Programa de emergencias', 'soporte' => 'emergencia.php', 'calificacion' => ''],
    ['actividad' => 'Formato inscripción de brigadas', 'soporte' => '', 'calificacion' => ''],
    ['actividad' => 'Estructura brigadas SST', 'soporte' => '', 'calificacion' => ''],
    ['actividad' => 'MEDEVAC', 'soporte' => '', 'calificacion' => ''],
    ['actividad' => 'Registro MEDEVAC', 'soporte' => '', 'calificacion' => ''],
    ['actividad' => 'Inventario de equipos y elementos de primeros auxilios', 'soporte' => '', 'calificacion' => ''],
    ['actividad' => 'Entrenamientos realizados', 'soporte' => '', 'calificacion' => ''],
    ['actividad' => 'Lista de chequeo simulacro', 'soporte' => '', 'calificacion' => ''],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>5.1.1-2</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
        body{background:#f2f4f7;padding:20px;color:#111}
        .contenedor{
            max-width:1100px;
            margin:0 auto;
            background:#fff;
            border:1px solid #bfc7d1;
            box-shadow:0 4px 18px rgba(0,0,0,.08)
        }
        .toolbar{
            position:sticky;
            top:0;
            z-index:100;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:12px;
            padding:14px 18px;
            background:#dde7f5;
            border-bottom:1px solid #c8d3e2
        }
        .toolbar h1{font-size:20px;color:#213b67;font-weight:700}
        .acciones{display:flex;gap:10px;flex-wrap:wrap}
        .btn{
            border:none;
            padding:10px 18px;
            border-radius:8px;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            transition:.2s ease
        }
        .btn:hover{transform:translateY(-1px);opacity:.95}
        .btn-atras{background:#6c757d;color:#fff}

        .contenido{padding:22px}
        .save-msg{
            margin:0 0 15px 0;
            padding:10px 14px;
            border-radius:8px;
            background:#e9f7ef;
            color:#166534;
            border:1px solid #b7e4c7;
            font-size:14px;
            font-weight:700;
        }

        .tabla-card{
            width:100%;
            border:1px solid #d7dbe3;
            border-radius:16px;
            overflow:hidden;
            background:#fff;
        }

        .tabla-head,
        .fila{
            display:grid;
            grid-template-columns:110px 1.5fr 140px 1.8fr;
            align-items:center;
        }

        .tabla-head{
            background:#eef3fb;
            border-bottom:1px solid #d8dfeb;
            min-height:56px;
            font-weight:700;
            color:#143d86;
            letter-spacing:.5px;
        }

        .tabla-head div{
            padding:0 18px;
            font-size:15px;
        }

        .fila{
            min-height:128px;
            border-bottom:1px solid #e5e7eb;
            background:#fff;
        }

        .fila:last-child{
            border-bottom:none;
        }

        .celda{
            padding:18px;
        }

        .item-dot{
            display:flex;
            align-items:center;
            gap:12px;
            color:#1d4ed8;
            font-weight:700;
            font-size:18px;
        }

        .dot{
            width:10px;
            height:10px;
            border-radius:50%;
            background:#3b82f6;
            flex:0 0 auto;
        }

        .actividad-input{
            width:100%;
            border:none;
            outline:none;
            background:transparent;
            font-size:20px;
            line-height:1.45;
            color:#1f2937;
            resize:none;
            min-height:76px;
            white-space:pre-wrap;
            overflow-wrap:anywhere;
        }

        .soporte-wrap{
            display:flex;
            justify-content:center;
        }

        .soporte-btn{
            width:40px;
            height:40px;
            border:2px solid #2563ff;
            border-radius:14px;
            background:#f8fbff;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#2563ff;
            cursor:pointer;
            transition:.2s ease;
        }

        .soporte-btn:hover{
            background:#edf4ff;
            transform:translateY(-1px);
        }

        .soporte-btn svg{
            width:18px;
            height:18px;
            display:block;
        }

        .soporte-hidden{
            display:none;
        }

        .calificaciones{
            display:flex;
            flex-wrap:wrap;
            gap:12px 14px;
            align-items:center;
        }

        .pill{
            position:relative;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:112px;
            height:44px;
            border-radius:999px;
            background:#fff;
            border:1.8px solid #d1d5db;
            cursor:pointer;
            padding:0 18px 0 42px;
            font-size:15px;
            font-weight:700;
            color:#111827;
            user-select:none;
            transition:.2s ease;
        }

        .pill::before{
            content:"";
            position:absolute;
            left:16px;
            top:50%;
            transform:translateY(-50%);
            width:16px;
            height:16px;
            border-radius:50%;
            border:1.8px solid #d1d5db;
            background:#fff;
        }

        .pill input{
            position:absolute;
            opacity:0;
            pointer-events:none;
        }

        .pill.si{
            border-color:#b7dbc9;
        }
        .pill.proceso{
            border-color:#f0cf74;
        }
        .pill.no{
            border-color:#efb1b1;
        }
        .pill.na{
            border-color:#d1d5db;
        }

        .pill.activa.si{
            background:#f1fbf5;
            border-color:#9ed0b3;
        }
        .pill.activa.proceso{
            background:#fff9ea;
            border-color:#ebc85c;
        }
        .pill.activa.no{
            background:#fff4f4;
            border-color:#e59a9a;
        }
        .pill.activa.na{
            background:#f8fafc;
            border-color:#c8ced8;
        }

        .pill.activa::before{
            background:#fff;
            box-shadow:inset 0 0 0 5px #cfd8e3;
        }

        .pill.activa.si::before{
            box-shadow:inset 0 0 0 5px #8cc0a7;
        }
        .pill.activa.proceso::before{
            box-shadow:inset 0 0 0 5px #e7c155;
        }
        .pill.activa.no::before{
            box-shadow:inset 0 0 0 5px #de8f8f;
        }

        .nota-soporte{
            margin-top:6px;
            text-align:center;
            font-size:11px;
            color:#6b7280;
            word-break:break-word;
        }

        @media (max-width: 950px){
            .tabla-head{
                display:none;
            }

            .fila{
                grid-template-columns:1fr;
                min-height:auto;
                padding:10px 0;
            }

            .celda{
                padding:12px 16px;
            }

            .actividad-input{
                font-size:18px;
                min-height:60px;
            }

            .calificaciones{
                justify-content:flex-start;
            }
        }

        @media print{
            body{background:#fff;padding:0}
            .toolbar{display:none}
            .contenedor{box-shadow:none;border:none}
            .contenido{padding:10px}
        }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>5.1.1-2</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
        </div>
    </div>

    <div class="contenido">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form51112" method="POST" action="" enctype="multipart/form-data">
            <div class="tabla-card">
                <div class="tabla-head">
                    <div>ÍTEM</div>
                    <div>ACTIVIDAD</div>
                    <div>SOPORTE</div>
                    <div>CALIFICACIÓN</div>
                </div>

                <?php foreach ($items as $i => $item): ?>
                    <?php $cal = $item['calificacion'] ?? ''; ?>
                    <div class="fila">
                        <div class="celda">
                            <div class="item-dot">
                                <span class="dot"></span>
                            </div>
                        </div>

                        <div class="celda">
                            <textarea class="actividad-input" name="items[<?php echo $i; ?>][actividad]"><?php echo htmlspecialchars($item['actividad'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="celda">
                            <div class="soporte-wrap">
                                <label class="soporte-btn" title="Cargar soporte">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"/>
                                        <path d="M14 2v5h5"/>
                                        <path d="M9 13h6"/>
                                        <path d="M9 17h6"/>
                                        <path d="M9 9h2"/>
                                    </svg>
                                    <input class="soporte-hidden" type="text" name="items[<?php echo $i; ?>][soporte]" value="<?php echo htmlspecialchars($item['soporte'], ENT_QUOTES, 'UTF-8'); ?>">
                                </label>
                            </div>
                            <div class="nota-soporte"><?php echo htmlspecialchars($item['soporte'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>

                        <div class="celda">
                            <div class="calificaciones">
                                <label class="pill si <?php echo $cal === 'si' ? 'activa' : ''; ?>">
                                    <input type="radio" name="items[<?php echo $i; ?>][calificacion]" value="si" <?php echo $cal === 'si' ? 'checked' : ''; ?>>
                                    SI
                                </label>

                                <label class="pill proceso <?php echo $cal === 'proceso' ? 'activa' : ''; ?>">
                                    <input type="radio" name="items[<?php echo $i; ?>][calificacion]" value="proceso" <?php echo $cal === 'proceso' ? 'checked' : ''; ?>>
                                    PROCESO
                                </label>

                                <label class="pill no <?php echo $cal === 'no' ? 'activa' : ''; ?>">
                                    <input type="radio" name="items[<?php echo $i; ?>][calificacion]" value="no" <?php echo $cal === 'no' ? 'checked' : ''; ?>>
                                    NO
                                </label>

                                <label class="pill na <?php echo $cal === 'na' ? 'activa' : ''; ?>">
                                    <input type="radio" name="items[<?php echo $i; ?>][calificacion]" value="na" <?php echo $cal === 'na' ? 'checked' : ''; ?>>
                                    N/A
                                </label>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('change', function(e){
    if(e.target.matches('.pill input[type="radio"]')){
        const name = e.target.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
            const pill = radio.closest('.pill');
            if(pill){
                pill.classList.toggle('activa', radio.checked);
            }
        });
    }
});
</script>
</body>
</html>