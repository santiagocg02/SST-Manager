<?php
// 3.1.1.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.1 Perfil Sociodemográfico</title>
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
            max-width:1200px;
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

        .btn-guardar{
            background:#198754;
            color:#fff;
        }

        .btn-atras{
            background:#6c757d;
            color:#fff;
        }

        .btn-imprimir{
            background:#0d6efd;
            color:#fff;
        }

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
            border:2px dashed #c8c8c8;
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
            font-size:15px;
        }

        .link-online{
            display:inline-block;
            margin-bottom:14px;
            color:#0d6efd;
            font-size:14px;
            text-decoration:underline;
        }

        .datos-grid{
            display:grid;
            grid-template-columns:220px 1fr;
            gap:24px;
            margin-bottom:26px;
        }

        .datos-labels div,
        .datos-inputs div{
            border:1px solid #6b6b6b;
            height:52px;
            display:flex;
            align-items:center;
            padding:10px;
        }

        .datos-labels div{
            font-weight:700;
        }

        .seccion-instruccion{
            margin:18px 0 22px;
            font-size:17px;
        }

        .grid-preguntas{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:28px 40px;
        }

        .bloque{
            break-inside:avoid;
        }

        .bloque h3{
            font-size:16px;
            margin-bottom:8px;
            text-transform:uppercase;
        }

        .opcion{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;
            margin:6px 0;
            font-size:15px;
        }

        .opcion label{
            flex:1;
            line-height:1.4;
            cursor:pointer;
        }

        .opcion input[type="checkbox"],
        .opcion input[type="radio"]{
            width:18px;
            height:18px;
            margin-top:2px;
        }

        .inline-campos{
            margin-left:24px;
            margin-top:6px;
            display:grid;
            gap:6px;
            max-width:280px;
        }

        .linea-campo{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .linea-campo input{
            flex:1;
            border:none;
            border-bottom:1px solid #333;
            outline:none;
            padding:3px 2px;
        }

        .footer-ley{
            margin-top:28px;
            padding-top:18px;
            border-top:2px solid #2d57ff;
            font-size:14px;
            line-height:1.5;
        }

        .marca-agua{
            position:fixed;
            inset:0;
            display:flex;
            align-items:center;
            justify-content:center;
            pointer-events:none;
            font-size:90px;
            color:rgba(0,0,0,.12);
            font-weight:700;
            user-select:none;
        }

        @media (max-width: 900px){
            .grid-preguntas{
                grid-template-columns:1fr;
            }

            .datos-grid{
                grid-template-columns:1fr;
                gap:12px;
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
        }
    </style>
</head>
<body>

<div class="marca-agua">Página 1</div>

<div class="contenedor">
    <div class="toolbar">
        <h1>3.1.1 - Encuesta Perfil Sociodemográfico</h1>
        <div class="acciones">
            <button class="btn btn-atras" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" onclick="document.getElementById('formPerfil').submit()">Guardar</button>
            <button class="btn btn-imprimir" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <form id="formPerfil" action="guardar_3.1.1.php" method="POST">
            
            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">0</td>
                </tr>
                <tr>
                    <td class="subtitulo">ENCUESTA PARA EL PERFIL SOCIODEMOGRÁFICO</td>
                    <td style="font-weight:700;">AN-SST-14<br>XX/XX/2025</td>
                </tr>
            </table>

            <p class="texto-info">
                Esta encuesta hace parte del Sistema de Gestión en Seguridad y Salud en el Trabajo y el contenido de la misma es información clasificada.
            </p>

            <a href="#" class="link-online">Clic para realizar en línea, duplica el formulario</a>

            <div class="datos-grid">
                <div class="datos-labels">
                    <div>Nombre</div>
                    <div>Cargo</div>
                    <div>Fecha:</div>
                </div>
                <div class="datos-inputs">
                    <div><input type="text" name="nombre" style="width:100%; border:none; outline:none;"></div>
                    <div><input type="text" name="cargo" style="width:100%; border:none; outline:none;"></div>
                    <div><input type="date" name="fecha" style="width:100%; border:none; outline:none;"></div>
                </div>
            </div>

            <div class="seccion-instruccion">Seleccione la respuesta que le corresponda:</div>

            <div class="grid-preguntas">
                <div class="bloque">
                    <h3>1. Edad</h3>
                    <?php
                    $edades = ['Menor de 18 año', '18 - 27 años', '28 - 37 años', '38 - 47 años', '48 años o mas'];
                    foreach($edades as $i => $item){
                        echo '<div class="opcion"><label>a'.($i>0?'.':'').'</label></div>';
                    }
                    ?>
                    <div class="opcion"><label><input type="radio" name="edad" value="Menor de 18 año"> Menor de 18 año</label></div>
                    <div class="opcion"><label><input type="radio" name="edad" value="18 - 27 años"> 18 - 27 años</label></div>
                    <div class="opcion"><label><input type="radio" name="edad" value="28 - 37 años"> 28 - 37 años</label></div>
                    <div class="opcion"><label><input type="radio" name="edad" value="38 - 47 años"> 38 - 47 años</label></div>
                    <div class="opcion"><label><input type="radio" name="edad" value="48 años o mas"> 48 años o mas</label></div>
                </div>

                <div class="bloque">
                    <h3>2. Estado civil</h3>
                    <div class="opcion"><label><input type="radio" name="estado_civil" value="Soltero(a)"> Soltero(a)</label></div>
                    <div class="opcion"><label><input type="radio" name="estado_civil" value="Casado(a)/union libre"> Casado(a)/union libre</label></div>
                    <div class="opcion"><label><input type="radio" name="estado_civil" value="Separado(a)/Divorciado"> Separado(a)/Divorciado</label></div>
                    <div class="opcion"><label><input type="radio" name="estado_civil" value="Viudo(a)"> Viudo(a)</label></div>
                </div>

                <div class="bloque">
                    <h3>3. Género</h3>
                    <div class="opcion"><label><input type="radio" name="genero" value="Masculino"> Masculino</label></div>
                    <div class="opcion"><label><input type="radio" name="genero" value="Femenino"> Femenino</label></div>
                </div>

                <div class="bloque">
                    <h3>4. Número de personas a cargo</h3>
                    <div class="opcion"><label><input type="radio" name="personas_cargo" value="Ninguna"> Ninguna</label></div>
                    <div class="opcion"><label><input type="radio" name="personas_cargo" value="1 - 3 personas"> 1 - 3 personas</label></div>
                    <div class="opcion"><label><input type="radio" name="personas_cargo" value="4 - 6 personas"> 4 - 6 personas</label></div>
                    <div class="opcion"><label><input type="radio" name="personas_cargo" value="Más de 6 personas"> Más de 6 personas</label></div>
                </div>

                <div class="bloque">
                    <h3>5. Nivel de escolaridad</h3>
                    <div class="opcion"><label><input type="radio" name="escolaridad" value="Primaria"> Primaria</label></div>
                    <div class="opcion"><label><input type="radio" name="escolaridad" value="Secundaria"> Secundaria</label></div>
                    <div class="opcion"><label><input type="radio" name="escolaridad" value="Técnico / Tecnólogo"> Técnico / Tecnólogo</label></div>
                    <div class="opcion"><label><input type="radio" name="escolaridad" value="Universitario"> Universitario</label></div>
                    <div class="opcion"><label><input type="radio" name="escolaridad" value="Especialista / Maestría"> Especialista / Maestría</label></div>
                </div>

                <div class="bloque">
                    <h3>6. Tenencia de vivienda</h3>
                    <div class="opcion"><label><input type="radio" name="vivienda" value="Propia"> Propia</label></div>
                    <div class="opcion"><label><input type="radio" name="vivienda" value="Arrendada"> Arrendada</label></div>
                    <div class="opcion"><label><input type="radio" name="vivienda" value="Familiar"> Familiar</label></div>
                    <div class="opcion"><label><input type="radio" name="vivienda" value="Compartida con otra(s) familia(s)"> Compartida con otra(s) familia(s)</label></div>
                </div>

                <div class="bloque">
                    <h3>7. Uso del tiempo libre</h3>
                    <div class="opcion"><label><input type="checkbox" name="tiempo_libre[]" value="Otro trabajo"> Otro trabajo</label></div>
                    <div class="opcion"><label><input type="checkbox" name="tiempo_libre[]" value="Labores domésticas"> Labores domésticas</label></div>
                    <div class="opcion"><label><input type="checkbox" name="tiempo_libre[]" value="Recreación y deporte"> Recreación y deporte</label></div>
                    <div class="opcion"><label><input type="checkbox" name="tiempo_libre[]" value="Estudio"> Estudio</label></div>
                    <div class="opcion"><label><input type="checkbox" name="tiempo_libre[]" value="Ninguno"> Ninguno</label></div>
                </div>

                <div class="bloque">
                    <h3>8. Promedio de ingresos (S.M.L.)</h3>
                    <div class="opcion"><label><input type="radio" name="ingresos" value="Mínimo legal"> Mínimo legal (S.M.L.)</label></div>
                    <div class="opcion"><label><input type="radio" name="ingresos" value="Entre 1 a 3"> Entre 1 a 3 S.M.L.</label></div>
                    <div class="opcion"><label><input type="radio" name="ingresos" value="Entre 4 a 5"> Entre 4 a 5 S.M.L.</label></div>
                    <div class="opcion"><label><input type="radio" name="ingresos" value="Entre 5 y 6"> Entre 5 y 6 S.M.L.</label></div>
                    <div class="opcion"><label><input type="radio" name="ingresos" value="Más de 7"> Más de 7 S.M.L.</label></div>
                </div>

                <div class="bloque">
                    <h3>9. Antigüedad en la empresa</h3>
                    <div class="opcion"><label><input type="radio" name="ant_empresa" value="Menos de 1 año"> Menos de 1 año</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_empresa" value="De 1 a 5 años"> De 1 a 5 años</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_empresa" value="De 5 a 10 años"> De 5 a 10 años</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_empresa" value="De 10 a 15 años"> De 10 a 15 años</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_empresa" value="Más de 15 años"> Más de 15 años</label></div>
                </div>

                <div class="bloque">
                    <h3>10. Antigüedad en el cargo actual</h3>
                    <div class="opcion"><label><input type="radio" name="ant_cargo" value="Menos de 1 año"> Menos de 1 año</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_cargo" value="De 1 a 5 años"> De 1 a 5 años</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_cargo" value="De 5 a 10 años"> De 5 a 10 años</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_cargo" value="De 10 a 15 años"> De 10 a 15 años</label></div>
                    <div class="opcion"><label><input type="radio" name="ant_cargo" value="Más de 15 años"> Más de 15 años</label></div>
                </div>

                <div class="bloque">
                    <h3>11. Tipo de contratación</h3>
                    <div class="opcion"><label><input type="radio" name="contratacion" value="A termino fijo"> A termino fijo</label></div>
                    <div class="opcion"><label><input type="radio" name="contratacion" value="Indefinido"> Indefinido</label></div>
                    <div class="opcion"><label><input type="radio" name="contratacion" value="Por obra o labor"> Por obra o labor</label></div>
                    <div class="opcion"><label><input type="radio" name="contratacion" value="Prestación de Servicios"> Prestación de Servicios</label></div>
                    <div class="opcion"><label><input type="radio" name="contratacion" value="Honorarios/servicios profesionales"> Honorarios/servicios profesionales</label></div>
                </div>

                <div class="bloque">
                    <h3>12. Ha participado en actividades de salud realizadas por la empresa</h3>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Cardiovasculares y visuales"> Cardiovasculares y visuales</label></div>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Salud oral"> Salud oral</label></div>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Exámenes de laboratorio/otros"> Exámenes de laboratorio/otros</label></div>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Exámenes periódicos"> Exámenes periódicos</label></div>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Gimnasia laboral"> Gimnasia laboral</label></div>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Capacitaciones en Seguridad y Salud en el Trabajo"> Capacitaciones en Seguridad y Salud en el Trabajo</label></div>
                    <div class="opcion"><label><input type="checkbox" name="actividades_salud[]" value="Ninguna"> Ninguna</label></div>
                </div>

                <div class="bloque">
                    <h3>13. Consume bebidas alcohólicas</h3>
                    <div class="opcion"><label><input type="radio" name="alcohol" value="No"> No</label></div>
                    <div class="opcion"><label><input type="radio" name="alcohol" value="Si"> Si</label></div>

                    <div class="inline-campos">
                        <div class="linea-campo"><span>Semanal</span><input type="text" name="alcohol_semanal"></div>
                        <div class="linea-campo"><span>Mensual</span><input type="text" name="alcohol_mensual"></div>
                        <div class="linea-campo"><span>Quincenal</span><input type="text" name="alcohol_quincenal"></div>
                        <div class="linea-campo"><span>Ocasional</span><input type="text" name="alcohol_ocasional"></div>
                    </div>
                </div>

                <div class="bloque">
                    <h3>14. Fuma</h3>
                    <div class="opcion"><label><input type="radio" name="fuma" value="Si"> Si</label></div>
                    <div class="opcion"><label><input type="radio" name="fuma" value="No"> No</label></div>

                    <div class="inline-campos">
                        <div class="linea-campo"><span>Promedio diario</span><input type="text" name="fuma_promedio"></div>
                    </div>
                </div>

                <div class="bloque">
                    <h3>15. Consentimiento informado</h3>
                    <div class="opcion"><label><input type="radio" name="consentimiento" value="No"> No</label></div>
                    <div class="opcion"><label><input type="radio" name="consentimiento" value="Si"> Si</label></div>
                </div>

                <div class="bloque">
                    <h3>16. Practica algún deporte</h3>
                    <div class="opcion"><label><input type="radio" name="deporte" value="No"> No</label></div>
                    <div class="opcion"><label><input type="radio" name="deporte" value="Si"> Si</label></div>

                    <div class="inline-campos">
                        <div class="linea-campo"><span>Diario</span><input type="text" name="deporte_diario"></div>
                        <div class="linea-campo"><span>Semanal</span><input type="text" name="deporte_semanal"></div>
                        <div class="linea-campo"><span>Quincenal</span><input type="text" name="deporte_quincenal"></div>
                        <div class="linea-campo"><span>Mensual</span><input type="text" name="deporte_mensual"></div>
                        <div class="linea-campo"><span>Ocasional</span><input type="text" name="deporte_ocasional"></div>
                    </div>
                </div>
            </div>

            <div class="footer-ley">
                Ley 1581 de 2012: De protección de datos personales, es una ley que complementa la regulación vigente para la protección del derecho fundamental que tienen todas las personas naturales a autorizar la información personal que es almacenada en bases de datos o archivos, así como su posterior actualización y rectificación.
            </div>
        </form>
    </div>
</div>

</body>
</html>