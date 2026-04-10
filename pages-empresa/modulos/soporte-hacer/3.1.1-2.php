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
// Ajusta el ID de este ítem según tu base de datos para la encuesta sociodemográfica
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 44; 

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.1-2 - Análisis Encuesta Perfil Sociodemográfico</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f2f4f7;
            padding:20px;
            color:#111;
        }

        .contenedor{
            max-width:1400px;
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

        .btn:hover{
            transform:translateY(-1px);
            opacity:.95;
        }

        .btn-guardar{ background:#198754; color:#fff; }
        .btn-atras{ background:#6c757d; color:#fff; }
        .btn-imprimir{ background:#0d6efd; color:#fff; }

        .formulario{
            padding:18px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        .encabezado td, .encabezado th{
            border:1px solid #6b6b6b;
            padding:10px;
            text-align:center;
            vertical-align:middle;
        }

        .logo-box{
            width:140px;
            height:65px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin:auto;
            color:#999;
            font-weight:bold;
            font-size:14px;
            text-align:center;
        }

        .titulo-principal{
            font-size:16px;
            font-weight:700;
        }

        .subtitulo{
            font-size:14px;
        }

        .texto-info{
            margin:14px 0 18px;
            font-size:14px;
            font-style:italic;
        }

        .grid-analisis{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:14px;
        }

        .bloque{
            border:1px solid #6b6b6b;
            min-height:290px;
            display:flex;
            flex-direction:column;
        }

        .bloque-top{
            display:grid;
            grid-template-columns:1fr 1.2fr;
            gap:10px;
            padding:10px;
            min-height:190px;
        }

        .bloque-left h3{
            font-size:15px;
            margin-bottom:6px;
            text-transform:uppercase;
        }

        .lista-opciones{
            list-style:none;
            padding:0;
            margin:0 0 8px 0;
            font-size:13px;
            line-height:1.4;
        }

        .lista-opciones li{
            margin-bottom:2px;
        }

        .mini-tabla{
            width:100%;
            margin-top:6px;
            border-collapse:collapse;
            font-size:12px;
        }

        .mini-tabla td, .mini-tabla th{
            border:1px solid #777;
            padding:3px 5px;
            text-align:center;
        }

        .mini-tabla input{
            width:100%;
            border:none;
            outline:none;
            text-align:center;
            font-size:12px;
            padding:2px;
            background:transparent;
        }

        .chart-box{
            border:1px solid #8aa8db;
            min-height:160px;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:8px;
            background:#fff;
        }

        .chart-box canvas{
            width:100% !important;
            height:150px !important;
        }

        .analisis-texto{
            border-top:1px solid #6b6b6b;
            padding:10px;
            font-size:13px;
            line-height:1.45;
            min-height:82px;
            background:#fcfcfc;
        }

        .footer-ley{
            margin-top:18px;
            padding:12px 10px 18px;
            border-top:2px solid #2d57ff;
            font-size:13px;
            line-height:1.45;
            text-align:center;
        }

        .top-actions{
            display:flex;
            justify-content:flex-end;
            margin-bottom:10px;
        }

        .btn-small{
            padding:8px 12px;
            border-radius:6px;
            border:1px solid #bfc7d1;
            background:#fff;
            cursor:pointer;
            font-weight:700;
        }

        @media (max-width: 1100px){
            .grid-analisis{
                grid-template-columns:1fr;
            }

            .bloque-top{
                grid-template-columns:1fr;
            }
        }

        @media print{
            body{
                background:#fff;
                padding:0;
            }

            .toolbar{
                display:none;
            }

            .contenedor{
                box-shadow:none;
                border:none;
            }

            .formulario{
                padding:8px;
            }

            .top-actions{
                display:none;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar print-hide">
        <h1>3.1.1-2 - Análisis Encuesta Perfil Sociodemográfico</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="button" id="btnGuardar">Guardar Cambios</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <form id="formAnalisis">
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%; padding: 0;">
                        <div class="logo-box" style="<?= empty($logoEmpresaUrl) ? 'border: 2px dashed #c8c8c8;' : 'border: none;' ?>">
                            <?php if(!empty($logoEmpresaUrl)): ?>
                                <img src="<?= $logoEmpresaUrl ?>" alt="Logo Empresa" style="max-width: 100%; max-height: 60px; object-fit: contain;">
                            <?php else: ?>
                                TU LOGO<br>AQUÍ
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">ANÁLISIS ENCUESTA PARA EL PERFIL SOCIODEMOGRÁFICO</td>
                    <td style="font-weight:700;">AN-SST-15<br><?= date('d/m/Y') ?></td>
                </tr>
            </table>

            <p class="texto-info print-hide">Ingresa los datos de la tabulación para el análisis</p>

            <div class="top-actions print-hide">
                <button type="button" class="btn-small" onclick="recalcularTodo()">Actualizar análisis y gráficas</button>
            </div>

            <div class="grid-analisis">

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>1. EDAD</h3>
                            <ul class="lista-opciones">
                                <li>a. Menor de 18 años</li>
                                <li>b. 18 - 27 años</li>
                                <li>c. 28 - 37 años</li>
                                <li>d. 38 - 47 años</li>
                                <li>e. 48 años o más</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="edad_1" name="edad_1" value="0"></td><td id="edad_1_pct">0%</td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="edad_2" name="edad_2" value="4"></td><td id="edad_2_pct">0%</td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="edad_3" name="edad_3" value="2"></td><td id="edad_3_pct">0%</td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="edad_4" name="edad_4" value="1"></td><td id="edad_4_pct">0%</td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="edad_5" name="edad_5" value="0"></td><td id="edad_5_pct">0%</td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="edad_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_edad"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_edad"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>2. ESTADO CIVIL</h3>
                            <ul class="lista-opciones">
                                <li>a. Soltero(a)</li>
                                <li>b. Casado(a)/unión libre</li>
                                <li>c. Separado(a)/Divorciado</li>
                                <li>d. Viudo(a)</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="civil_1" name="civil_1" value="3"></td><td id="civil_1_pct">0%</td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="civil_2" name="civil_2" value="2"></td><td id="civil_2_pct">0%</td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="civil_3" name="civil_3" value="1"></td><td id="civil_3_pct">0%</td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="civil_4" name="civil_4" value="1"></td><td id="civil_4_pct">0%</td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="civil_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_civil"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_civil"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>3. GÉNERO</h3>
                            <ul class="lista-opciones">
                                <li>a. Masculino</li>
                                <li>b. Femenino</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="genero_1" name="genero_1" value="2"></td><td id="genero_1_pct">0%</td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="genero_2" name="genero_2" value="5"></td><td id="genero_2_pct">0%</td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="genero_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_genero"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_genero"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>4. NÚMERO DE PERSONAS A CARGO</h3>
                            <ul class="lista-opciones">
                                <li>a. Ninguna</li>
                                <li>b. 1 - 3 personas</li>
                                <li>c. 4 - 6 personas</li>
                                <li>d. Más de 6 personas</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="cargo_1" name="cargo_1" value="4"></td><td id="cargo_1_pct">0%</td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="cargo_2" name="cargo_2" value="3"></td><td id="cargo_2_pct">0%</td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="cargo_3" name="cargo_3" value="0"></td><td id="cargo_3_pct">0%</td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="cargo_4" name="cargo_4" value="0"></td><td id="cargo_4_pct">0%</td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="cargo_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_cargo"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_cargo"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>5. NIVEL DE ESCOLARIDAD</h3>
                            <ul class="lista-opciones">
                                <li>a. Primaria</li>
                                <li>b. Secundaria</li>
                                <li>c. Técnico / Tecnólogo</li>
                                <li>d. Universitario</li>
                                <li>e. Especialista / Maestría</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="esc_1" name="esc_1" value="0"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="esc_2" name="esc_2" value="1"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="esc_3" name="esc_3" value="4"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="esc_4" name="esc_4" value="1"></td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="esc_5" name="esc_5" value="1"></td></tr>
                                <tr><th>TOTAL</th><th id="esc_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_esc"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_esc"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>6. TENENCIA DE VIVIENDA</h3>
                            <ul class="lista-opciones">
                                <li>a. Propia</li>
                                <li>b. Arrendada</li>
                                <li>c. Familiar</li>
                                <li>d. Compartida con otra(s) familia(s)</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="viv_1" name="viv_1" value="1"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="viv_2" name="viv_2" value="4"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="viv_3" name="viv_3" value="1"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="viv_4" name="viv_4" value="1"></td></tr>
                                <tr><th>TOTAL</th><th id="viv_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_viv"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_viv"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>7. USO DEL TIEMPO LIBRE</h3>
                            <ul class="lista-opciones">
                                <li>a. Otro trabajo</li>
                                <li>b. Labores domésticas</li>
                                <li>c. Recreación y deporte</li>
                                <li>d. Estudio</li>
                                <li>e. Ninguno</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="tiempo_1" name="tiempo_1" value="2"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="tiempo_2" name="tiempo_2" value="2"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="tiempo_3" name="tiempo_3" value="3"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="tiempo_4" name="tiempo_4" value="2"></td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="tiempo_5" name="tiempo_5" value="0"></td></tr>
                                <tr><th>TOTAL</th><th id="tiempo_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_tiempo"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_tiempo"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>8. PROMEDIO DE INGRESOS (S.M.L.)</h3>
                            <ul class="lista-opciones">
                                <li>a. Mínimo Legal (S.M.L.)</li>
                                <li>b. Entre 1 a 3 S.M.L.</li>
                                <li>c. Entre 4 a 5 S.M.L.</li>
                                <li>d. Entre 5 y 6 S.M.L.</li>
                                <li>e. Más de 7 S.M.L.</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="ing_1" name="ing_1" value="2"></td><td id="ing_1_pct">0%</td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="ing_2" name="ing_2" value="5"></td><td id="ing_2_pct">0%</td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="ing_3" name="ing_3" value="0"></td><td id="ing_3_pct">0%</td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="ing_4" name="ing_4" value="0"></td><td id="ing_4_pct">0%</td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="ing_5" name="ing_5" value="0"></td><td id="ing_5_pct">0%</td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="ing_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_ing"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_ing"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>9. ANTIGÜEDAD EN LA EMPRESA</h3>
                            <ul class="lista-opciones">
                                <li>a. Menos de 1 año</li>
                                <li>b. De 1 a 5 años</li>
                                <li>c. De 5 a 10 años</li>
                                <li>d. De 10 a 15 años</li>
                                <li>e. Más de 15 años</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="antemp_1" name="antemp_1" value="1"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="antemp_2" name="antemp_2" value="6"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="antemp_3" name="antemp_3" value="0"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="antemp_4" name="antemp_4" value="0"></td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="antemp_5" name="antemp_5" value="0"></td></tr>
                                <tr><th>TOTAL</th><th id="antemp_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_antemp"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_antemp"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>10. ANTIGÜEDAD EN EL CARGO ACTUAL</h3>
                            <ul class="lista-opciones">
                                <li>a. Menos de 1 año</li>
                                <li>b. De 1 a 5 años</li>
                                <li>c. De 5 a 10 años</li>
                                <li>d. De 10 a 15 años</li>
                                <li>e. Más de 15 años</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="antcar_1" name="antcar_1" value="4"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="antcar_2" name="antcar_2" value="3"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="antcar_3" name="antcar_3" value="0"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="antcar_4" name="antcar_4" value="0"></td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="antcar_5" name="antcar_5" value="0"></td></tr>
                                <tr><th>TOTAL</th><th id="antcar_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_antcar"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_antcar"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>11. TIPO DE CONTRATACIÓN</h3>
                            <ul class="lista-opciones">
                                <li>a. A término fijo</li>
                                <li>b. Indefinido</li>
                                <li>c. Por obra o labor</li>
                                <li>d. Prestación de servicios</li>
                                <li>e. Honorarios/servicios profesionales</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="contr_1" name="contr_1" value="3"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="contr_2" name="contr_2" value="4"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="contr_3" name="contr_3" value="0"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="contr_4" name="contr_4" value="0"></td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="contr_5" name="contr_5" value="0"></td></tr>
                                <tr><th>TOTAL</th><th id="contr_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_contr"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_contr"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>12. HA PARTICIPADO EN ACTIVIDADES DE SALUD REALIZADAS POR LA EMPRESA</h3>
                            <ul class="lista-opciones">
                                <li>a. Cardiovasculares y visuales</li>
                                <li>b. Salud oral</li>
                                <li>c. Exámenes de laboratorio/otros</li>
                                <li>d. Exámenes periódicos</li>
                                <li>e. Gimnasia laboral</li>
                                <li>f. Capacitaciones en SST</li>
                                <li>g. Ninguna</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>a</td><td><input type="number" min="0" id="salud_1" name="salud_1" value="0"></td></tr>
                                <tr><td>b</td><td><input type="number" min="0" id="salud_2" name="salud_2" value="0"></td></tr>
                                <tr><td>c</td><td><input type="number" min="0" id="salud_3" name="salud_3" value="0"></td></tr>
                                <tr><td>d</td><td><input type="number" min="0" id="salud_4" name="salud_4" value="0"></td></tr>
                                <tr><td>e</td><td><input type="number" min="0" id="salud_5" name="salud_5" value="0"></td></tr>
                                <tr><td>f</td><td><input type="number" min="0" id="salud_6" name="salud_6" value="3"></td></tr>
                                <tr><td>g</td><td><input type="number" min="0" id="salud_7" name="salud_7" value="0"></td></tr>
                                <tr><th>TOTAL</th><th id="salud_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_salud"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_salud"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>13. CONSUME BEBIDAS ALCOHÓLICAS</h3>
                            <ul class="lista-opciones">
                                <li>a. No</li>
                                <li>b. Sí</li>
                                <li>Semanal</li>
                                <li>Mensual</li>
                                <li>Quincenal</li>
                                <li>Ocasional</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>No</td><td><input type="number" min="0" id="alcohol_no" name="alcohol_no" value="3"></td></tr>
                                <tr><td>Sí</td><td><input type="number" min="0" id="alcohol_si" name="alcohol_si" value="4"></td></tr>
                                <tr><td>Semanal</td><td><input type="number" min="0" id="alcohol_sem" name="alcohol_sem" value="0"></td></tr>
                                <tr><td>Mensual</td><td><input type="number" min="0" id="alcohol_men" name="alcohol_men" value="2"></td></tr>
                                <tr><td>Quincenal</td><td><input type="number" min="0" id="alcohol_qui" name="alcohol_qui" value="2"></td></tr>
                                <tr><td>Ocasional</td><td><input type="number" min="0" id="alcohol_oca" name="alcohol_oca" value="3"></td></tr>
                                <tr><th>TOTAL</th><th id="alcohol_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_alcohol"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_alcohol"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>14. FUMA</h3>
                            <ul class="lista-opciones">
                                <li>a. Sí</li>
                                <li>b. No</li>
                                <li>Promedio diario</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>Sí</td><td><input type="number" min="0" id="fuma_si" name="fuma_si" value="1"></td><td id="fuma_si_pct">0%</td></tr>
                                <tr><td>No</td><td><input type="number" min="0" id="fuma_no" name="fuma_no" value="6"></td><td id="fuma_no_pct">0%</td></tr>
                                <tr><td>Promedio diario</td><td colspan="2"><input type="text" id="fuma_promedio" name="fuma_promedio" value=""></td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="fuma_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_fuma"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_fuma"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>15. CONSENTIMIENTO INFORMADO</h3>
                            <ul class="lista-opciones">
                                <li>a. No</li>
                                <li>b. Sí</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>No</td><td><input type="number" min="0" id="cons_no" name="cons_no" value="0"></td></tr>
                                <tr><td>Sí</td><td><input type="number" min="0" id="cons_si" name="cons_si" value="7"></td></tr>
                                <tr><th>TOTAL</th><th id="cons_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_cons"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_cons"></div>
                </div>

                <div class="bloque">
                    <div class="bloque-top">
                        <div>
                            <h3>16. PRACTICA ALGÚN DEPORTE</h3>
                            <ul class="lista-opciones">
                                <li>a. No</li>
                                <li>b. Sí</li>
                                <li>Diario</li>
                                <li>Semanal</li>
                                <li>Quincenal</li>
                                <li>Mensual</li>
                                <li>Ocasional</li>
                            </ul>
                            <table class="mini-tabla">
                                <tr><td>No</td><td><input type="number" min="0" id="dep_no" name="dep_no" value="2"></td><td id="dep_no_pct">0%</td></tr>
                                <tr><td>Sí</td><td><input type="number" min="0" id="dep_si" name="dep_si" value="5"></td><td id="dep_si_pct">0%</td></tr>
                                <tr><td>Diario</td><td><input type="number" min="0" id="dep_dia" name="dep_dia" value="0"></td><td></td></tr>
                                <tr><td>Semanal</td><td><input type="number" min="0" id="dep_sem" name="dep_sem" value="2"></td><td></td></tr>
                                <tr><td>Quincenal</td><td><input type="number" min="0" id="dep_qui" name="dep_qui" value="0"></td><td></td></tr>
                                <tr><td>Mensual</td><td><input type="number" min="0" id="dep_men" name="dep_men" value="2"></td><td></td></tr>
                                <tr><td>Ocasional</td><td><input type="number" min="0" id="dep_oca" name="dep_oca" value="1"></td><td></td></tr>
                                <tr><th colspan="2">TOTAL</th><th id="dep_total">0</th></tr>
                            </table>
                        </div>
                        <div class="chart-box"><canvas id="chart_dep"></canvas></div>
                    </div>
                    <div class="analisis-texto" id="analisis_dep"></div>
                </div>

            </div>

            <div class="footer-ley">
                Ley 1581 de 2012: De protección de datos personales, es una ley que complementa la regulación vigente para la protección del derecho fundamental que tienen todas las personas naturales a autorizar la información personal que es almacenada en bases de datos o archivos, así como su posterior actualización y rectificación.
            </div>
        </form>
    </div>
</div>

<script>
const charts = {};

function n(id){
    const el = document.getElementById(id);
    return Math.max(0, parseInt(el?.value || 0, 10) || 0);
}

function txt(id){
    return document.getElementById(id)?.value?.trim() || '';
}

function pct(valor, total){
    if (!total) return 0;
    return Math.round((valor / total) * 100);
}

function setText(id, value){
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function plural(n, singular, pluralForm = null){
    return n === 1 ? singular : (pluralForm || singular + 's');
}

function maxIndex(arr){
    let idx = 0;
    for(let i=1;i<arr.length;i++){
        if(arr[i] > arr[idx]) idx = i;
    }
    return idx;
}

function updateChart(chartId, type, labels, data, titleText){
    const ctx = document.getElementById(chartId);
    if (!ctx) return;

    if (charts[chartId]) {
        charts[chartId].data.labels = labels;
        charts[chartId].data.datasets[0].data = data;
        charts[chartId].options.plugins.title.text = titleText;
        charts[chartId].update();
        return;
    }

    charts[chartId] = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: '#4a76a8',
                borderWidth: 1
            }]
        },
        options: {
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend:{ display:false },
                title:{ display:true, text:titleText }
            },
            scales: type === 'pie' ? {} : {
                y: {
                    beginAtZero:true,
                    ticks:{ precision:0 }
                }
            }
        }
    });
}

function recalcularEdad(){
    const labels = ['Menor de 18', '18 - 27', '28 - 37', '38 - 47', '48 o más'];
    const data = [n('edad_1'), n('edad_2'), n('edad_3'), n('edad_4'), n('edad_5')];
    const total = data.reduce((a,b)=>a+b,0);

    data.forEach((v,i)=>setText(`edad_${i+1}_pct`, pct(v,total) + '%'));
    setText('edad_total', total);

    updateChart('chart_edad', 'bar', labels, data, '1. EDAD');

    setText(
        'analisis_edad',
        total === 0
            ? 'Análisis: No hay datos registrados para edad.'
            : `Análisis: Con base en la información registrada se evidencia que ${data[1]} ${plural(data[1],'trabajador','trabajadores')} tienen entre 18 a 27 años (${pct(data[1],total)}%), ${data[2]} ${plural(data[2],'trabajador','trabajadores')} tienen entre 28 a 37 años (${pct(data[2],total)}%) y ${data[3] + data[4]} ${plural(data[3] + data[4],'trabajador','trabajadores')} tienen 38 años o más (${pct(data[3] + data[4],total)}%).`
    );
}

function recalcularCivil(){
    const labels = ['Soltero(a)', 'Casado(a)/unión libre', 'Separado(a)/Divorciado', 'Viudo(a)'];
    const data = [n('civil_1'), n('civil_2'), n('civil_3'), n('civil_4')];
    const total = data.reduce((a,b)=>a+b,0);

    data.forEach((v,i)=>setText(`civil_${i+1}_pct`, pct(v,total) + '%'));
    setText('civil_total', total);

    updateChart('chart_civil', 'bar', labels, data, '2. ESTADO CIVIL');

    setText(
        'analisis_civil',
        total === 0
            ? 'Análisis: No hay datos registrados para estado civil.'
            : `Análisis: Con relación al estado civil de la población trabajadora, se evidencia que ${data[0]} son solteros(a), ${data[1]} están casados(a) o viven en unión libre, ${data[2]} están separados(a)/divorciados y ${data[3]} son viudos(a).`
    );
}

function recalcularGenero(){
    const labels = ['Masculino', 'Femenino'];
    const data = [n('genero_1'), n('genero_2')];
    const total = data.reduce((a,b)=>a+b,0);

    data.forEach((v,i)=>setText(`genero_${i+1}_pct`, pct(v,total) + '%'));
    setText('genero_total', total);

    updateChart('chart_genero', 'bar', labels, data, '3. GÉNERO');

    setText(
        'analisis_genero',
        total === 0
            ? 'Análisis: No hay datos registrados para género.'
            : `Análisis: Respecto a la distribución por sexo, se evidencia que el ${pct(data[1],total)}% corresponde al sexo femenino (${data[1]} ${plural(data[1],'trabajador','trabajadores')}) y el ${pct(data[0],total)}% al sexo masculino (${data[0]} ${plural(data[0],'trabajador','trabajadores')}).`
    );
}

function recalcularCargo(){
    const labels = ['Ninguna', '1 - 3 personas', '4 - 6 personas', 'Más de 6 personas'];
    const data = [n('cargo_1'), n('cargo_2'), n('cargo_3'), n('cargo_4')];
    const total = data.reduce((a,b)=>a+b,0);

    data.forEach((v,i)=>setText(`cargo_${i+1}_pct`, pct(v,total) + '%'));
    setText('cargo_total', total);

    updateChart('chart_cargo', 'bar', labels, data, '4. NÚMERO DE PERSONAS A CARGO');

    setText(
        'analisis_cargo',
        total === 0
            ? 'Análisis: No hay datos registrados para personas a cargo.'
            : `Análisis: En relación al número de personas a cargo, se evidenció que el ${pct(data[0],total)}% no tiene personas a cargo y el ${pct(data[1],total)}% tiene entre 1 a 3 personas a su cargo.`
    );
}

function recalcularEscolaridad(){
    const labels = ['Primaria', 'Secundaria', 'Técnico/Tecnólogo', 'Universitario', 'Especialista/Maestría'];
    const data = [n('esc_1'), n('esc_2'), n('esc_3'), n('esc_4'), n('esc_5')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('esc_total', total);

    updateChart('chart_esc', 'bar', labels, data, '5. NIVEL DE ESCOLARIDAD');

    setText(
        'analisis_esc',
        total === 0
            ? 'Análisis: No hay datos registrados para escolaridad.'
            : `Análisis: En lo que concierne a la formación académica, ${data[0]} cursaron primaria, ${data[1]} secundaria, ${data[2]} técnico o tecnólogo, ${data[3]} universitario y ${data[4]} especialización o maestría.`
    );
}

function recalcularVivienda(){
    const labels = ['Propia', 'Arrendada', 'Familiar', 'Compartida'];
    const data = [n('viv_1'), n('viv_2'), n('viv_3'), n('viv_4')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('viv_total', total);

    updateChart('chart_viv', 'bar', labels, data, '6. TENENCIA DE VIVIENDA');

    setText(
        'analisis_viv',
        total === 0
            ? 'Análisis: No hay datos registrados para vivienda.'
            : `Análisis: Acerca de la tenencia de la vivienda, ${data[1]} ${plural(data[1],'trabajador','trabajadores')} pagan arriendo, ${data[0]} viven en casa propia, ${data[2]} en casa familiar y ${data[3]} en vivienda compartida con otras familias.`
    );
}

function recalcularTiempo(){
    const labels = ['Otro trabajo', 'Labores domésticas', 'Recreación y deporte', 'Estudio', 'Ninguno'];
    const data = [n('tiempo_1'), n('tiempo_2'), n('tiempo_3'), n('tiempo_4'), n('tiempo_5')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('tiempo_total', total);

    updateChart('chart_tiempo', 'bar', labels, data, '7. USO DEL TIEMPO LIBRE');

    setText(
        'analisis_tiempo',
        total === 0
            ? 'Análisis: No hay datos registrados para uso del tiempo libre.'
            : `Análisis: En cuanto a lo que hace la población trabajadora en su tiempo libre, ${data[0]} tienen otro trabajo, ${data[1]} se dedican a labores domésticas, ${data[3]} estudian, ${data[2]} realizan recreación y deporte y ${data[4]} no reportan actividades.`
    );
}

function recalcularIngresos(){
    const labels = ['Mínimo legal', 'Entre 1 a 3', 'Entre 4 a 5', 'Entre 5 y 6', 'Más de 7'];
    const data = [n('ing_1'), n('ing_2'), n('ing_3'), n('ing_4'), n('ing_5')];
    const total = data.reduce((a,b)=>a+b,0);

    data.forEach((v,i)=>setText(`ing_${i+1}_pct`, pct(v,total) + '%'));
    setText('ing_total', total);

    updateChart('chart_ing', 'bar', labels, data, '8. PROMEDIO DE INGRESOS (S.M.L.)');

    setText(
        'analisis_ing',
        total === 0
            ? 'Análisis: No hay datos registrados para ingresos.'
            : `Análisis: Sobre el promedio de los ingresos mensuales, se concluye que el ${pct(data[1],total)}% recibe entre 1 a 3 S.M.L.V. y el ${pct(data[0],total)}% un salario mínimo.`
    );
}

function recalcularAntEmp(){
    const labels = ['Menos de 1 año', 'De 1 a 5 años', 'De 5 a 10 años', 'De 10 a 15 años', 'Más de 15 años'];
    const data = [n('antemp_1'), n('antemp_2'), n('antemp_3'), n('antemp_4'), n('antemp_5')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('antemp_total', total);

    updateChart('chart_antemp', 'bar', labels, data, '9. ANTIGÜEDAD EN LA EMPRESA');

    setText(
        'analisis_antemp',
        total === 0
            ? 'Análisis: No hay datos registrados para antigüedad en la empresa.'
            : `Análisis: En la antigüedad dentro de la empresa, ${data[0]} ${plural(data[0],'trabajador','trabajadores')} tienen menos de 1 año y ${data[1]} tienen entre 1 a 5 años.`
    );
}

function recalcularAntCar(){
    const labels = ['Menos de 1 año', 'De 1 a 5 años', 'De 5 a 10 años', 'De 10 a 15 años', 'Más de 15 años'];
    const data = [n('antcar_1'), n('antcar_2'), n('antcar_3'), n('antcar_4'), n('antcar_5')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('antcar_total', total);

    updateChart('chart_antcar', 'bar', labels, data, '10. ANTIGÜEDAD EN EL CARGO ACTUAL');

    setText(
        'analisis_antcar',
        total === 0
            ? 'Análisis: No hay datos registrados para antigüedad en el cargo.'
            : `Análisis: En el cargo actual, ${data[0]} ${plural(data[0],'trabajador','trabajadores')} tienen menos de 1 año y ${data[1]} tienen entre 1 a 5 años de antigüedad.`
    );
}

function recalcularContr(){
    const labels = ['Término fijo', 'Indefinido', 'Obra o labor', 'Prestación de servicios', 'Honorarios'];
    const data = [n('contr_1'), n('contr_2'), n('contr_3'), n('contr_4'), n('contr_5')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('contr_total', total);

    updateChart('chart_contr', 'bar', labels, data, '11. TIPO DE CONTRATACIÓN');

    setText(
        'analisis_contr',
        total === 0
            ? 'Análisis: No hay datos registrados para tipo de contratación.'
            : `Análisis: Con relación al tipo de vinculación laboral, se evidencia que ${data[0]} ${plural(data[0],'trabajador','trabajadores')} están con contrato a término fijo y ${data[1]} con contrato a término indefinido.`
    );
}

function recalcularSalud(){
    const labels = ['Cardio/visuales', 'Salud oral', 'Laboratorio/otros', 'Periódicos', 'Gimnasia', 'Capacitaciones', 'Ninguna'];
    const data = [n('salud_1'), n('salud_2'), n('salud_3'), n('salud_4'), n('salud_5'), n('salud_6'), n('salud_7')];
    const total = data.reduce((a,b)=>a+b,0);
    setText('salud_total', total);

    updateChart('chart_salud', 'bar', labels, data, '12. ACTIVIDADES DE SALUD');

    const idx = maxIndex(data);
    setText(
        'analisis_salud',
        total === 0
            ? 'Análisis: No hay datos registrados para actividades de salud.'
            : `Análisis: En las actividades de salud realizadas por la empresa, la mayor participación se presenta en "${labels[idx]}" con ${data[idx]} registros.`
    );
}

function recalcularAlcohol(){
    const no = n('alcohol_no');
    const si = n('alcohol_si');
    const semanal = n('alcohol_sem');
    const mensual = n('alcohol_men');
    const quincenal = n('alcohol_qui');
    const ocasional = n('alcohol_oca');
    const total = no + si;

    setText('alcohol_total', total);

    updateChart('chart_alcohol', 'bar', ['No', 'Sí'], [no, si], '13. CONSUME BEBIDAS ALCOHÓLICAS');

    setText(
        'analisis_alcohol',
        total === 0
            ? 'Análisis: No hay datos registrados para consumo de alcohol.'
            : `Análisis: En relación al consumo de bebidas alcohólicas, ${si} ${plural(si,'trabajador','trabajadores')} reportan consumo y ${no} no consumen. La frecuencia reportada es: semanal ${semanal}, mensual ${mensual}, quincenal ${quincenal} y ocasional ${ocasional}.`
    );
}

function recalcularFuma(){
    const si = n('fuma_si');
    const no = n('fuma_no');
    const total = si + no;

    setText('fuma_si_pct', pct(si,total) + '%');
    setText('fuma_no_pct', pct(no,total) + '%');
    setText('fuma_total', total);

    updateChart('chart_fuma', 'bar', ['Sí', 'No'], [si, no], '14. FUMA');

    const promedio = txt('fuma_promedio');
    setText(
        'analisis_fuma',
        total === 0
            ? 'Análisis: No hay datos registrados para tabaquismo.'
            : `Análisis: Se observa que el ${pct(no,total)}% de la población trabajadora no fuma y el ${pct(si,total)}% sí lo hace.${promedio ? ' Promedio diario reportado: ' + promedio + '.' : ''}`
    );
}

function recalcularConsentimiento(){
    const no = n('cons_no');
    const si = n('cons_si');
    const total = no + si;

    setText('cons_total', total);

    updateChart('chart_cons', 'bar', ['No', 'Sí'], [no, si], '15. CONSENTIMIENTO INFORMADO');

    setText(
        'analisis_cons',
        total === 0
            ? 'Análisis: No hay datos registrados para consentimiento informado.'
            : `Análisis: El ${pct(si,total)}% de la población trabajadora dio su consentimiento para analizar la información suministrada en el formato de encuesta de perfil sociodemográfico.`
    );
}

function recalcularDeporte(){
    const no = n('dep_no');
    const si = n('dep_si');
    const dia = n('dep_dia');
    const sem = n('dep_sem');
    const qui = n('dep_qui');
    const men = n('dep_men');
    const oca = n('dep_oca');
    const total = no + si;

    setText('dep_no_pct', pct(no,total) + '%');
    setText('dep_si_pct', pct(si,total) + '%');
    setText('dep_total', total);

    updateChart('chart_dep', 'bar', ['No', 'Sí'], [no, si], '16. PRACTICA ALGÚN DEPORTE');

    setText(
        'analisis_dep',
        total === 0
            ? 'Análisis: No hay datos registrados para práctica deportiva.'
            : `Análisis: Se observa que el ${pct(si,total)}% de los trabajadores practica algún deporte y el ${pct(no,total)}% no realiza actividad física. Frecuencia reportada: diario ${dia}, semanal ${sem}, quincenal ${qui}, mensual ${men} y ocasional ${oca}.`
    );
}

function recalcularTodo(){
    recalcularEdad();
    recalcularCivil();
    recalcularGenero();
    recalcularCargo();
    recalcularEscolaridad();
    recalcularVivienda();
    recalcularTiempo();
    recalcularIngresos();
    recalcularAntEmp();
    recalcularAntCar();
    recalcularContr();
    recalcularSalud();
    recalcularAlcohol();
    recalcularFuma();
    recalcularConsentimiento();
    recalcularDeporte();
}

// 3. CARGAR DATOS GUARDADOS DESDE LA API AL INICIAR
document.addEventListener('DOMContentLoaded', function() {
    const datosGuardados = <?= json_encode($datosCampos ?: []) ?>;
    
    // Poblar los inputs con los datos si existen
    if (datosGuardados && Object.keys(datosGuardados).length > 0) {
        for (const [key, value] of Object.entries(datosGuardados)) {
            const input = document.getElementsByName(key)[0];
            if (input) {
                input.value = value;
            }
        }
    }
    
    // Iniciar gráficas y análisis
    recalcularTodo();
});

// 4. ACTUALIZAR EN TIEMPO REAL
document.addEventListener('input', function(e){
    if (e.target.matches('input')) {
        recalcularTodo();
    }
});

// 5. GUARDAR DATOS EN LA BASE DE DATOS
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formAnalisis');
    const formData = new FormData(form);
    
    // Convertir FormData a un objeto JSON
    const datosJSON = {};
    for (const [key, value] of formData.entries()) {
        datosJSON[key] = value;
    }

    const textoOriginal = btn.innerHTML;
    btn.innerHTML = 'Guardando...';
    btn.disabled = true;

    try {
        const urlAPI = "http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar";
        const response = await fetch(urlAPI, {
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
        });

        const result = await response.json();

        if (result.ok) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Análisis guardado correctamente.',
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
        btn.innerHTML = textoOriginal;
        btn.disabled = false;
    }
});

</script>

</body>
</html>