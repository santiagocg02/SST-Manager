<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../index.php');
    exit;
}

function post($key, $default = ''){
    return isset($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
}

$pictogramas = [
    'corrosion'  => ['nombre' => 'Corrosión', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/3/3b/GHS-pictogram-acid.svg'],
    'comburente' => ['nombre' => 'Comburente', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/9/96/GHS-pictogram-rondflam.svg'],
    'ambiente'   => ['nombre' => 'Peligro ambiental', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/0/0b/GHS-pictogram-pollu.svg'],
    'irritante'  => ['nombre' => 'Irritante', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/3/3f/GHS-pictogram-exclam.svg'],
    'explosivo'  => ['nombre' => 'Explosivo', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/1/10/GHS-pictogram-explos.svg'],
    'inflamable' => ['nombre' => 'Inflamable', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/e/e7/GHS-pictogram-flamme.svg'],
    'gas'        => ['nombre' => 'Gas a presión', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/2/21/GHS-pictogram-bottle.svg'],
    'salud'      => ['nombre' => 'Peligro grave para la salud', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/8/8e/GHS-pictogram-silhouette.svg'],
    'toxico'     => ['nombre' => 'Toxicidad aguda', 'img' => 'https://upload.wikimedia.org/wikipedia/commons/4/4e/GHS-pictogram-skull.svg'],
];

$seleccionados = isset($_POST['pictogramas']) && is_array($_POST['pictogramas']) ? $_POST['pictogramas'] : [];

$nombre_quimico = post('nombre_quimico');
$palabra_advertencia = post('palabra_advertencia');
$codigo_h = post('codigo_h');
$codigo_p = post('codigo_p');
$telefono_emergencia = post('telefono_emergencia');
$cas = post('cas');

$fabricante_nombre = post('fabricante_nombre');
$fabricante_telefono = post('fabricante_telefono');
$fabricante_direccion = post('fabricante_direccion');
$onu = post('onu');

$usuario_nombre = post('usuario_nombre');
$usuario_email = post('usuario_email');
$usuario_email_confirma = post('usuario_email_confirma');
$usuario_pais = post('usuario_pais');
$usuario_estado = post('usuario_estado');

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = 'Datos cargados correctamente. Vista previa generada.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>4.2.3 Etiquetas SGA</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif}
body{background:#f2f4f7;padding:20px;color:#111}
.contenedor{
    max-width:1100px;
    margin:0 auto;
    background:#fff;
    border:1px solid #bfc7d1;
    box-shadow:0 4px 18px rgba(0,0,0,.08);
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
    border-bottom:1px solid #c8d3e2;
}
.toolbar h1{
    font-size:20px;
    color:#213b67;
    font-weight:700;
}
.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}
.btn{
    border:none;
    padding:10px 18px;
    border-radius:8px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
    transition:.2s ease;
}
.btn:hover{transform:translateY(-1px);opacity:.95}
.btn-atras{background:#6c757d;color:#fff}
.btn-guardar{background:#198754;color:#fff}
.btn-imprimir{background:#0d6efd;color:#fff}
.contenido{padding:20px}
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
.titulo{
    text-align:center;
    font-size:28px;
    font-weight:700;
    margin-bottom:8px;
    color:#111;
}
.subtitulo{
    text-align:center;
    font-size:15px;
    color:#4b5563;
    margin-bottom:24px;
}
.seccion{
    margin-top:26px;
}
.seccion h3{
    text-align:center;
    margin-bottom:14px;
    font-size:20px;
    color:#213b67;
}
.grupo{
    margin-bottom:16px;
}
label{
    display:block;
    font-weight:700;
    font-size:13px;
    margin-bottom:6px;
}
input[type="text"],
input[type="email"],
input[type="tel"],
select,
textarea{
    width:100%;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:14px;
    outline:none;
    background:#fff;
}
textarea{
    resize:vertical;
    min-height:90px;
    white-space:pre-wrap;
    word-break:break-word;
}
.check{
    display:flex;
    align-items:center;
    gap:8px;
    margin-top:2px;
    margin-bottom:16px;
}
.check input{
    width:16px;
    height:16px;
    accent-color:#0d6efd;
}
.grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}
.pictos-select{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
    gap:18px;
    margin-top:18px;
}
.picto-card{
    border:2px solid #d9e2ef;
    border-radius:14px;
    padding:12px 10px;
    background:#fff;
    cursor:pointer;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:10px;
    min-height:190px;
    text-align:center;
    position:relative;
    transition:.2s ease;
}
.picto-card:hover{
    border-color:#0d6efd;
    transform:translateY(-2px);
    box-shadow:0 6px 14px rgba(0,0,0,.08);
}
.picto-card input{
    position:absolute;
    opacity:0;
    pointer-events:none;
}
.picto-card.activo{
    border-color:#198754;
    background:#f3fbf6;
    box-shadow:0 0 0 2px rgba(25,135,84,.12);
}
.picto-img-wrap{
    width:96px;
    height:96px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}
.picto-img-wrap img{
    width:90px;
    height:90px;
    object-fit:contain;
    display:block;
}
.picto-card span{
    font-size:12px;
    font-weight:700;
    line-height:1.25;
    color:#213b67;
}
.footer-btn{
    text-align:right;
    margin-top:28px;
}
.preview{
    margin-top:34px;
    border:1px solid #bfc7d1;
    border-radius:14px;
    background:#fafbfc;
    padding:18px;
}
.preview h3{
    text-align:center;
    color:#213b67;
    margin-bottom:16px;
}
.etiqueta{
    max-width:820px;
    margin:0 auto;
    background:#fff;
    border:2px solid #111;
    padding:18px;
}
.etiqueta h4{
    font-size:28px;
    text-align:center;
    font-weight:700;
    margin-bottom:10px;
}
.etiqueta .adv{
    text-align:center;
    font-size:24px;
    font-weight:700;
    margin:10px 0 18px;
    color:#b91c1c;
}
.etiqueta-pictos{
    display:grid;
    grid-template-columns:repeat(3, 96px);
    gap:14px;
    justify-content:center;
    align-items:center;
    margin:18px 0;
}
.etiqueta-pictos img{
    width:90px;
    height:90px;
    object-fit:contain;
}
.etiqueta-bloque{
    margin-top:14px;
    padding-top:10px;
    border-top:1px solid #d1d5db;
}
.etiqueta-bloque strong{
    display:block;
    margin-bottom:6px;
}
.etiqueta p{
    font-size:14px;
    line-height:1.45;
    white-space:pre-wrap;
    word-break:break-word;
}
.etiqueta-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
    margin-top:12px;
}
.muted{
    color:#6b7280;
}
@media (max-width:800px){
    .grid-2{grid-template-columns:1fr}
    .etiqueta-grid{grid-template-columns:1fr}
    .etiqueta-pictos{grid-template-columns:repeat(2, 96px)}
}
@media print{
    body{background:#fff;padding:0}
    .toolbar,.footer-btn,.check,.subtitulo{display:none}
    .contenedor{box-shadow:none;border:none;max-width:100%}
    .contenido{padding:8px}
    .preview{border:none;background:#fff;padding:0}
    .seccion{display:none}
    .save-msg{display:none}
    .etiqueta{border:2px solid #111;max-width:100%}
}
</style>
</head>
<body>
<div class="contenedor">
    <div class="toolbar">
        <h1>4.2.3 Etiquetas SGA</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form423">Guardar / Generar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir etiqueta</button>
        </div>
    </div>

    <div class="contenido">
        <?php if ($mensaje): ?>
            <div class="save-msg"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="titulo">GENERA TU ETIQUETA SGA</div>
        <div class="subtitulo">Seleccione los pictogramas completos y diligencie los datos para generar la vista previa de la etiqueta.</div>

        <form id="form423" method="POST" action="">
            <div class="seccion">
                <h3>Datos de la etiqueta</h3>

                <div class="grupo">
                    <label for="nombre_quimico">Nombre del químico *</label>
                    <input type="text" id="nombre_quimico" name="nombre_quimico" value="<?php echo $nombre_quimico; ?>">
                </div>

                <div class="grupo">
                    <label for="palabra_advertencia">Palabra de advertencia *</label>
                    <select id="palabra_advertencia" name="palabra_advertencia">
                        <option value="">Seleccione una palabra</option>
                        <option value="PELIGRO" <?php echo $palabra_advertencia === 'PELIGRO' ? 'selected' : ''; ?>>PELIGRO</option>
                        <option value="ATENCIÓN" <?php echo $palabra_advertencia === 'ATENCIÓN' ? 'selected' : ''; ?>>ATENCIÓN</option>
                    </select>
                </div>

                <div class="grupo">
                    <label for="codigo_h">Pegar aquí códigos de peligro</label>
                    <textarea id="codigo_h" name="codigo_h" placeholder="Pega aquí los códigos 'H' de tu Hoja o Ficha de Seguridad"><?php echo $codigo_h; ?></textarea>
                </div>

                <div class="check">
                    <input type="checkbox" id="mostrar_h">
                    <label for="mostrar_h" style="margin:0;font-weight:400;">Mostrar buscador de códigos "H"</label>
                </div>

                <div class="grupo">
                    <label for="codigo_p">Pegar aquí códigos de prudencia</label>
                    <textarea id="codigo_p" name="codigo_p" placeholder="Pega aquí los códigos 'P' de tu Hoja o Ficha de Seguridad"><?php echo $codigo_p; ?></textarea>
                </div>

                <div class="check">
                    <input type="checkbox" id="mostrar_p">
                    <label for="mostrar_p" style="margin:0;font-weight:400;">Mostrar buscador de códigos "P"</label>
                </div>

                <div class="grid-2">
                    <div class="grupo">
                        <label for="telefono_emergencia">Teléfono de emergencia</label>
                        <input type="text" id="telefono_emergencia" name="telefono_emergencia" value="<?php echo $telefono_emergencia; ?>">
                    </div>

                    <div class="grupo">
                        <label for="cas">Número de CAS</label>
                        <input type="text" id="cas" name="cas" value="<?php echo $cas; ?>">
                    </div>
                </div>
            </div>

            <div class="seccion">
                <h3>Datos del fabricante</h3>

                <div class="grupo">
                    <label for="fabricante_nombre">Nombre del fabricante</label>
                    <input type="text" id="fabricante_nombre" name="fabricante_nombre" value="<?php echo $fabricante_nombre; ?>">
                </div>

                <div class="grid-2">
                    <div class="grupo">
                        <label for="fabricante_telefono">Teléfono</label>
                        <input type="text" id="fabricante_telefono" name="fabricante_telefono" value="<?php echo $fabricante_telefono; ?>">
                    </div>

                    <div class="grupo">
                        <label for="onu">Número de ONU</label>
                        <input type="text" id="onu" name="onu" value="<?php echo $onu; ?>">
                    </div>
                </div>

                <div class="grupo">
                    <label for="fabricante_direccion">Dirección</label>
                    <input type="text" id="fabricante_direccion" name="fabricante_direccion" value="<?php echo $fabricante_direccion; ?>">
                </div>
            </div>

            <div class="seccion">
                <h3>Pictogramas</h3>

                <div class="pictos-select" id="pictosSelect">
                    <?php foreach ($pictogramas as $key => $pic): ?>
                        <?php $checked = in_array($key, $seleccionados, true) ? 'checked' : ''; ?>
                        <label class="picto-card <?php echo $checked ? 'activo' : ''; ?>">
                            <input type="checkbox" name="pictogramas[]" value="<?php echo $key; ?>" <?php echo $checked; ?>>
                            <div class="picto-img-wrap">
                                <img src="<?php echo $pic['img']; ?>" alt="<?php echo htmlspecialchars($pic['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <span><?php echo $pic['nombre']; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="seccion">
                <h3>Datos del usuario</h3>

                <div class="grupo">
                    <label for="usuario_nombre">Nombre *</label>
                    <input type="text" id="usuario_nombre" name="usuario_nombre" value="<?php echo $usuario_nombre; ?>">
                </div>

                <div class="grupo">
                    <label for="usuario_email">Correo electrónico *</label>
                    <input type="email" id="usuario_email" name="usuario_email" value="<?php echo $usuario_email; ?>">
                </div>

                <div class="grupo">
                    <label for="usuario_email_confirma">Confirmar correo electrónico *</label>
                    <input type="email" id="usuario_email_confirma" name="usuario_email_confirma" value="<?php echo $usuario_email_confirma; ?>">
                </div>

                <div class="grid-2">
                    <div class="grupo">
                        <label for="usuario_pais">País *</label>
                        <select id="usuario_pais" name="usuario_pais">
                            <option value="">Selecciona un país</option>
                            <option value="Colombia" <?php echo $usuario_pais === 'Colombia' ? 'selected' : ''; ?>>Colombia</option>
                            <option value="México" <?php echo $usuario_pais === 'México' ? 'selected' : ''; ?>>México</option>
                            <option value="Perú" <?php echo $usuario_pais === 'Perú' ? 'selected' : ''; ?>>Perú</option>
                            <option value="Chile" <?php echo $usuario_pais === 'Chile' ? 'selected' : ''; ?>>Chile</option>
                            <option value="Ecuador" <?php echo $usuario_pais === 'Ecuador' ? 'selected' : ''; ?>>Ecuador</option>
                        </select>
                    </div>

                    <div class="grupo">
                        <label for="usuario_estado">Estado</label>
                        <input type="text" id="usuario_estado" name="usuario_estado" value="<?php echo $usuario_estado; ?>">
                    </div>
                </div>
            </div>

            <div class="footer-btn">
                <button class="btn btn-guardar" type="submit">GENERAR ETIQUETA</button>
            </div>
        </form>

        <div class="preview">
            <h3>Vista previa de la etiqueta</h3>

            <div class="etiqueta">
                <h4><?php echo $nombre_quimico !== '' ? $nombre_quimico : 'NOMBRE DEL QUÍMICO'; ?></h4>

                <?php if ($palabra_advertencia !== ''): ?>
                    <div class="adv"><?php echo $palabra_advertencia; ?></div>
                <?php endif; ?>

                <?php if (!empty($seleccionados)): ?>
                    <div class="etiqueta-pictos">
                        <?php foreach ($seleccionados as $pic): ?>
                            <?php if (isset($pictogramas[$pic])): ?>
                                <img src="<?php echo $pictogramas[$pic]['img']; ?>" alt="<?php echo htmlspecialchars($pictogramas[$pic]['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted" style="text-align:center;">No hay pictogramas seleccionados.</p>
                <?php endif; ?>

                <div class="etiqueta-bloque">
                    <strong>Códigos de peligro (H)</strong>
                    <p><?php echo $codigo_h !== '' ? $codigo_h : 'Sin información registrada.'; ?></p>
                </div>

                <div class="etiqueta-bloque">
                    <strong>Códigos de prudencia (P)</strong>
                    <p><?php echo $codigo_p !== '' ? $codigo_p : 'Sin información registrada.'; ?></p>
                </div>

                <div class="etiqueta-grid">
                    <div class="etiqueta-bloque">
                        <strong>Teléfono de emergencia</strong>
                        <p><?php echo $telefono_emergencia !== '' ? $telefono_emergencia : 'No registrado'; ?></p>
                    </div>

                    <div class="etiqueta-bloque">
                        <strong>Número CAS</strong>
                        <p><?php echo $cas !== '' ? $cas : 'No registrado'; ?></p>
                    </div>
                </div>

                <div class="etiqueta-grid">
                    <div class="etiqueta-bloque">
                        <strong>Fabricante</strong>
                        <p><?php echo $fabricante_nombre !== '' ? $fabricante_nombre : 'No registrado'; ?></p>
                    </div>

                    <div class="etiqueta-bloque">
                        <strong>Teléfono fabricante</strong>
                        <p><?php echo $fabricante_telefono !== '' ? $fabricante_telefono : 'No registrado'; ?></p>
                    </div>
                </div>

                <div class="etiqueta-bloque">
                    <strong>Dirección del fabricante</strong>
                    <p><?php echo $fabricante_direccion !== '' ? $fabricante_direccion : 'No registrada'; ?></p>
                </div>

                <div class="etiqueta-grid">
                    <div class="etiqueta-bloque">
                        <strong>Número ONU</strong>
                        <p><?php echo $onu !== '' ? $onu : 'No registrado'; ?></p>
                    </div>

                    <div class="etiqueta-bloque">
                        <strong>Generado por</strong>
                        <p><?php echo $usuario_nombre !== '' ? $usuario_nombre : 'No registrado'; ?></p>
                    </div>
                </div>

                <div class="etiqueta-grid">
                    <div class="etiqueta-bloque">
                        <strong>Correo electrónico</strong>
                        <p><?php echo $usuario_email !== '' ? $usuario_email : 'No registrado'; ?></p>
                    </div>

                    <div class="etiqueta-bloque">
                        <strong>Ubicación</strong>
                        <p>
                            <?php
                            $ubicacion = trim(($usuario_pais !== '' ? $usuario_pais : '') . ($usuario_estado !== '' ? ' / ' . $usuario_estado : ''));
                            echo $ubicacion !== '' ? $ubicacion : 'No registrada';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.picto-card').forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');

        function syncCard(){
            card.classList.toggle('activo', checkbox.checked);
        }

        card.addEventListener('click', function(e){
            if (e.target.tagName.toLowerCase() !== 'input') {
                checkbox.checked = !checkbox.checked;
                syncCard();
                e.preventDefault();
            }
        });

        syncCard();
    });
});
</script>
</body>
</html>