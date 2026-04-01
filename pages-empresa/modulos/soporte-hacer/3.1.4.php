<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header('Location: ../../index.php');
    exit;
}

function old_val($key, $default = '')
{
    return isset($_POST[$key]) ? htmlspecialchars((string)$_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3.1.4 - Procedimiento para Exámenes Médicos Ocupacionales</title>
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

        .portada{
            margin-top:20px;
            border:1px solid #cfd7e3;
            padding:40px 30px;
            text-align:center;
        }

        .portada h2{
            font-size:26px;
            margin-bottom:30px;
            color:#1f3b68;
        }

        .portada h3{
            font-size:22px;
            margin-bottom:20px;
        }

        .portada .empresa{
            margin-top:60px;
            font-size:18px;
            font-weight:700;
        }

        .portada .fecha{
            margin-top:10px;
            font-size:16px;
        }

        .toc,
        .seccion{
            margin-top:24px;
        }

        .seccion h3,
        .toc h3{
            font-size:18px;
            margin-bottom:12px;
            color:#213b67;
            border-bottom:2px solid #d9e2f2;
            padding-bottom:8px;
        }

        .toc ul{
            list-style:none;
            padding-left:0;
        }

        .toc li{
            padding:6px 0;
            border-bottom:1px dashed #d6d6d6;
            font-size:14px;
        }

        .campo{
            margin-bottom:14px;
        }

        .campo label{
            display:block;
            font-weight:700;
            margin-bottom:6px;
            font-size:14px;
        }

       .campo input,
.campo textarea{
    width:100%;
    padding:10px;
    border:1px solid #b7b7b7;
    border-radius:6px;
    outline:none;
    font-size:14px;
    line-height:1.5;
}

.campo input{
    min-height:44px;
}

.campo textarea{
    resize:none;
    overflow:hidden;
    min-height:140px;
    white-space:pre-wrap;
    word-break:break-word;
}

.input-portada{
    border:none !important;
    border-bottom:1px solid #999 !important;
    text-align:center;
    font-size:18px !important;
    font-weight:700;
    width:300px !important;
    background:transparent;
}

.input-portada-fecha{
    border:none !important;
    border-bottom:1px solid #999 !important;
    text-align:center;
    font-size:16px !important;
    width:260px !important;
    background:transparent;
}
        .campo textarea.alta{
            min-height:240px;
        }

        .campo textarea.media{
            min-height:180px;
        }

        .flujo-box{
            border:1px solid #cfd7e3;
            background:#fafcff;
            padding:18px;
            border-radius:10px;
        }

        .flujo{
            display:grid;
            grid-template-columns:repeat(2, 1fr);
            gap:14px;
        }

        .flujo-item{
            border:1px solid #bfcce0;
            background:#fff;
            border-radius:10px;
            padding:14px;
            font-size:14px;
            line-height:1.4;
            min-height:78px;
        }

        .nota{
            margin-top:18px;
            padding:12px;
            border:1px solid #d9d9d9;
            background:#fafafa;
            font-size:13px;
            line-height:1.5;
            font-weight:700;
        }

        .footer-info{
            margin-top:26px;
            text-align:center;
            font-size:13px;
            color:#444;
            border-top:2px solid #2d57ff;
            padding-top:12px;
        }

        @media (max-width: 900px){
            .flujo{
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

            .portada{
                border:none;
            }

            .campo input,
            .campo textarea{
                border:none;
                padding:4px 0;
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="toolbar">
        <h1>3.1.4 - Procedimiento para Exámenes Médicos Ocupacionales</h1>
        <div class="acciones">
            <button class="btn btn-atras" type="button" onclick="history.back()">Atrás</button>
            <button class="btn btn-guardar" type="submit" form="form314">Guardar</button>
            <button class="btn btn-imprimir" type="button" onclick="window.print()">Imprimir</button>
        </div>
    </div>

    <div class="formulario">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="save-msg">Datos guardados correctamente en memoria del formulario.</div>
        <?php endif; ?>

        <form id="form314" method="POST" action="">

            <table class="encabezado">
                <tr>
                    <td rowspan="2" style="width:20%;">
                        <div class="logo-box">TU LOGO<br>AQUÍ</div>
                    </td>
                    <td class="titulo-principal" style="width:60%;">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                    <td style="width:20%; font-weight:700;">Versión: 1</td>
                </tr>
                <tr>
                    <td class="subtitulo">PROCEDIMIENTO PARA EXÁMENES MÉDICOS OCUPACIONALES</td>
                    <td style="font-weight:700;">AN-XX-SST-10<br>Fecha: XX-XX-20XX</td>
                </tr>
            </table>

            <div class="portada">
                <h2>PROCEDIMIENTO PARA REALIZAR EXÁMENES MÉDICOS OCUPACIONALES</h2>
                <h3>Versión 1</h3>
                <div class="empresa">
                    <input type="text" class="input-portada" name="empresa" value="<?= old_val('empresa', 'EMPRESA') ?>">
                </div>
                <div class="fecha">
                    <input type="text" class="input-portada-fecha" name="fecha_portada" value="<?= old_val('fecha_portada', 'Noviembre de 20XX') ?>">
                </div>
            </div>

            <div class="toc">
                <h3>Tabla de Contenido</h3>
                <ul>
                    <li>INTRODUCCIÓN</li>
                    <li>1. OBJETIVO</li>
                    <li>2. ALCANCE</li>
                    <li>3. RESPONSABILIDAD</li>
                    <li>4. DEFINICIONES</li>
                    <li>5. LEGISLACIÓN APLICABLE</li>
                    <li>6. PROCEDIMIENTO EXÁMENES OCUPACIONALES</li>
                    <li>6.2. Requisitos para realizar los exámenes médicos ocupacionales</li>
                    <li>6.3. Flujograma exámenes médicos ocupacionales</li>
                    <li>7. PROFESIOGRAMA</li>
                    <li>8. Matriz de Exámenes Ocupacionales</li>
                </ul>
            </div>

            <div class="seccion">
                <h3>INTRODUCCIÓN</h3>
                <div class="campo">
                    <textarea class="alta" name="introduccion"><?= old_val('introduccion', 'Las evaluaciones ocupacionales son actos médicos que buscan el bienestar del trabajador de manera individual y que orientan las acciones de gestión para mejorar las condiciones de salud y de trabajo, interviniendo el ambiente laboral y asegurando un adecuado monitoreo de las condiciones de salud de los trabajadores expuestos.

La práctica de exámenes médicos ocupacionales es una de las principales actividades de Medicina Preventiva y del Trabajo y constituye un instrumento importante en la elaboración del diagnóstico de las condiciones de salud de la población trabajadora.

Los exámenes médicos ocupacionales deberán contribuir al diagnóstico temprano, antes de que aparezcan manifestaciones clínicas de enfermedades de posible origen laboral y de enfermedades de origen común agravadas por las condiciones de trabajo.

La naturaleza de las valoraciones médicas es de total obligatoriedad de cumplimiento por parte del empleador y de los trabajadores a los cuales se les aplica este proceso de evaluación.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>1. OBJETIVO</h3>
                <div class="campo">
                    <textarea name="objetivo"><?= old_val('objetivo', 'Definir un procedimiento aplicable para realizar los exámenes ocupacionales de ingreso, periódicos y de egreso para los trabajadores.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>2. ALCANCE</h3>
                <div class="campo">
                    <textarea name="alcance"><?= old_val('alcance', 'Aplica a los trabajadores de todas las áreas y procesos de la empresa.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>3. RESPONSABILIDAD</h3>
                <div class="campo">
                    <textarea class="media" name="responsabilidad"><?= old_val('responsabilidad', 'Es responsabilidad del área de talento humano y seguridad y salud en el trabajo de la empresa promulgar, difundir y ejecutar las actividades del presente documento.

Igualmente deberá revisar anualmente el procedimiento y actualizarlo de acuerdo con los cambios en la legislación aplicable y/o cambios al interior de los procesos de la empresa.

Es responsabilidad de los niveles directivos dar cumplimiento a este procedimiento y disponer de los recursos necesarios para su implementación.

Es responsabilidad de los trabajadores cumplir con la citación al examen y con las recomendaciones para el cuidado de su salud emitidas durante este acto médico.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>4. DEFINICIONES</h3>
                <div class="campo">
                    <textarea class="alta" name="definiciones"><?= old_val('definiciones', 'De acuerdo con la Resolución 2346 del 11 de Julio de 2007, las evaluaciones médicas ocupacionales que debe realizar el empleador en forma obligatoria son como mínimo: evaluación médica pre-ocupacional o de pre-ingreso, evaluaciones médicas ocupacionales periódicas y evaluación médica post-ocupacional o de egreso.

Examen Médico Ocupacional: Acto médico mediante el cual se interroga y examina a un trabajador, con el fin de monitorear la exposición a factores de riesgo y determinar la existencia de consecuencias por dicha exposición.

Examen de Ingreso: Se realiza para determinar las condiciones de salud física, mental y social del trabajador antes de su contratación.

Examen Periódico: Se realiza con el fin de monitorear la exposición a factores de riesgo e identificar posibles alteraciones del estado de salud del trabajador.

Examen de Egreso: Se realiza al trabajador cuando termina la relación laboral para valorar y registrar las condiciones de salud en las que se retira.

Evaluación médica por cambio de ocupación: Se realiza cuando el trabajador cambia de funciones, tareas o exposición a nuevos factores de riesgo.

Historia clínica ocupacional: Conjunto único de documentos privados, obligatorios y sometidos a reserva, donde se registran cronológicamente las condiciones de salud de una persona y los actos médicos ejecutados en su atención.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>5. LEGISLACIÓN APLICABLE</h3>
                <div class="campo">
                    <textarea class="alta" name="legislacion"><?= old_val('legislacion', 'Ley 1562 de 2012.
Resolución 2578 de 2012.
Resolución 1409 de 2012.
Resolución 1918 de 2009.
Resolución 2346 de 2007.
Resolución 2646 de 2008.
Resolución 1013 de 2008.
Resolución 2844 de 2007.
Decreto 1295 de 1994.
Resolución 6398 de 1991.
Resolución 1016 de 1989.
Decreto 614 de 1984.
Ley 23 de 1981.
Código Sustantivo del Trabajo.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>6. PROCEDIMIENTO EXÁMENES OCUPACIONALES</h3>
                <div class="campo">
                    <textarea name="procedimiento"><?= old_val('procedimiento', 'Para la implementación del proceso de evaluaciones médicas ocupacionales, es necesario tener en cuenta el cumplimiento de los requisitos generales con base en la normatividad vigente y requisitos legales.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>6.2. Requisitos para realizar los exámenes médicos ocupacionales</h3>
                <div class="campo">
                    <textarea class="alta" name="requisitos"><?= old_val('requisitos', '• Verificar que las evaluaciones médicas ocupacionales sean realizadas por médicos especialistas en medicina del trabajo o salud ocupacional.
• Informar al médico que realice las evaluaciones sobre el profesiograma y los factores de riesgo a los que está o estará expuesto el trabajador.
• Informar a los trabajadores sobre el trámite para la realización de las evaluaciones médicas ocupacionales.
• Solicitar al trabajador su consentimiento informado.
• Garantizar la remisión del trabajador a la EPS respectiva si se encuentra una presunta enfermedad laboral o enfermedad común que requiera manejo y seguimiento específico.
• El médico evaluador deberá entregar al trabajador copia de cada una de las evaluaciones médicas ocupacionales practicadas.
• El médico especialista deberá generar el certificado médico de aptitud individual como resultado de la valoración.
• Asegurar el cumplimiento de la normatividad vigente en relación con el manejo, reserva y confidencialidad de la historia clínica ocupacional.
• Toda persona natural o jurídica que realice evaluaciones médicas ocupacionales deberá entregar al empleador un informe sobre el diagnóstico general de salud de la población trabajadora valorada.
• Los exámenes de ingreso, periódicos y de egreso deberán cumplir con los criterios establecidos previamente por la empresa.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>6.3. Flujograma exámenes médicos ocupacionales</h3>
                <div class="flujo-box">
                    <div class="flujo">
                        <div class="flujo-item">1. Requerimiento de nuevo personal o cambio de ocupación o evaluación periódica.</div>
                        <div class="flujo-item">2. Emisión de la orden de examen ocupacional.</div>
                        <div class="flujo-item">3. Programación del examen ocupacional y de los paraclínicos con el proveedor específico.</div>
                        <div class="flujo-item">4. Ejecución de los paraclínicos.</div>
                        <div class="flujo-item">5. Evaluación médica ocupacional con los resultados de los paraclínicos.</div>
                        <div class="flujo-item">6. Recepción del concepto médico de aptitud e informe del diagnóstico de salud.</div>
                        <div class="flujo-item">7. Evaluación por parte de Talento Humano y del área de trabajo del aspirante.</div>
                        <div class="flujo-item">8. Implementación de recomendaciones, restricciones y seguimiento a su cumplimiento y/o remisión a EPS/ARL.</div>
                        <div class="flujo-item">9. IPS responsable guarda y custodia las historias clínicas según el procedimiento.</div>
                    </div>
                </div>
            </div>

            <div class="seccion">
                <h3>7. PROFESIOGRAMA</h3>
                <div class="campo">
                    <textarea class="media" name="profesiograma"><?= old_val('profesiograma', 'La adecuación óptima del puesto de trabajo al trabajador representa una enorme importancia y un gran reto para la empresa. La empresa debe realizar una adecuada elaboración del perfil de cargos, donde se registren por cargo u oficio los requerimientos físicos y de salud que debe cumplir el aspirante o el trabajador para ese cargo específico.

El profesiograma consolidará información sobre los riesgos ocupacionales a los que está o estará expuesto el trabajador y el tipo y contenido de las evaluaciones médicas ocupacionales y pruebas complementarias que se le deben realizar.') ?></textarea>
                </div>
            </div>

            <div class="seccion">
                <h3>8. Matriz de Exámenes Ocupacionales</h3>
                <div class="campo">
                    <textarea name="matriz"><?= old_val('matriz', 'Generar y controlar desde el sistema la matriz correspondiente entregada por la IPS responsable de practicar los exámenes médicos ocupacionales.') ?></textarea>
                </div>
            </div>

            <div class="nota">
                Este procedimiento desarrolla el contenido base del documento cargado por el usuario para el formato 3.1.4 y deja editable cada sección del procedimiento. :contentReference[oaicite:1]{index=1}
            </div>

            <div class="footer-info">
                Carrera 41 No 33B – 18 Barzal Alto PBX: (6) 6836680 Meta - Colombia
            </div>

        </form>
    </div>
</div>

<script>
function autoResizeTextarea(el) {
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
}

function autoResizeInput(el) {
    const temp = document.createElement('span');
    const styles = window.getComputedStyle(el);

    temp.style.position = 'absolute';
    temp.style.visibility = 'hidden';
    temp.style.whiteSpace = 'pre';
    temp.style.font = styles.font;
    temp.style.fontSize = styles.fontSize;
    temp.style.fontWeight = styles.fontWeight;
    temp.style.letterSpacing = styles.letterSpacing;
    temp.textContent = el.value || el.placeholder || '';

    document.body.appendChild(temp);

    const newWidth = Math.max(temp.offsetWidth + 20, 120);
    el.style.width = newWidth + 'px';

    document.body.removeChild(temp);
}

document.addEventListener('DOMContentLoaded', function () {
    const textareas = document.querySelectorAll('textarea');
    const autoInputs = document.querySelectorAll('.input-portada, .input-portada-fecha');

    textareas.forEach(textarea => {
        autoResizeTextarea(textarea);

        textarea.addEventListener('input', function () {
            autoResizeTextarea(this);
        });
    });

    autoInputs.forEach(input => {
        autoResizeInput(input);

        input.addEventListener('input', function () {
            autoResizeInput(this);
        });
    });
});
</script>

</body>
</html>