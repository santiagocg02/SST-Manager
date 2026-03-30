<?php
session_start();
if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RE-SST-18 | Especificaciones de las compras en SST</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --blue-main:#5f8fbe;
            --blue-soft:#dbe8f5;
            --blue-dark:#2b5d8a;
            --line:#8f9aa5;
            --line-soft:#bcc6cf;
            --header:#eef2f6;
            --paper:#ffffff;
            --page:#f3f6fa;
            --text:#1f2937;
            --muted:#6b7280;
            --danger:#d62828;
            --btn:#0d6efd;
            --btn-hover:#0b5ed7;
        }

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            background:var(--page);
            font-family:Arial, Helvetica, sans-serif;
            color:var(--text);
        }

        .page-wrap{
            padding:20px;
        }

        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:16px;
        }

        .topbar-left,
        .topbar-right{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn-ui{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:9px 16px;
            border-radius:10px;
            border:1px solid var(--btn);
            background:var(--btn);
            color:#fff;
            text-decoration:none;
            font-size:14px;
            font-weight:700;
            transition:.2s ease;
            cursor:pointer;
            box-shadow:0 4px 14px rgba(13,110,253,.15);
        }

        .btn-ui:hover{
            background:var(--btn-hover);
            border-color:var(--btn-hover);
            color:#fff;
        }

        .btn-ui.secondary{
            background:#fff;
            color:var(--btn);
        }

        .btn-ui.secondary:hover{
            background:#eef5ff;
            color:var(--btn-hover);
        }

        .badge-format{
            font-size:12px;
            color:var(--muted);
            background:#fff;
            border:1px solid #d8dee6;
            padding:7px 12px;
            border-radius:999px;
            font-weight:700;
        }

        .sheet-card{
            background:var(--paper);
            border:1px solid #d7dee6;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 12px 28px rgba(31,41,55,.08);
        }

        .sheet-header{
            padding:14px 18px;
            background:linear-gradient(135deg, #f8fbff 0%, #eef4fb 100%);
            border-bottom:1px solid #dde6ef;
        }

        .sheet-header-title{
            margin:0;
            font-size:16px;
            font-weight:800;
            color:var(--blue-dark);
        }

        .sheet-header-subtitle{
            margin:4px 0 0;
            font-size:12px;
            color:var(--muted);
        }

        .sheet-scroll{
            width:100%;
            overflow:auto;
            background:#fff;
        }

        .sheet{
            min-width:1180px;
            background:#fff;
        }

        table.form-sheet{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            background:#fff;
        }

        .form-sheet th,
        .form-sheet td{
            border:1px solid var(--line-soft);
            vertical-align:middle;
            padding:0;
        }

        .top-cell{
            background:var(--header);
            text-align:center;
            font-weight:700;
            font-size:12px;
            padding:8px 10px !important;
            height:34px;
        }

        .top-title{
            background:var(--header);
            text-align:center;
            font-weight:800;
            font-size:15px;
            padding:10px 14px !important;
            line-height:1.2;
            text-transform:uppercase;
        }

        .top-subtitle{
            background:var(--header);
            text-align:center;
            font-weight:700;
            font-size:13px;
            padding:10px 14px !important;
            text-transform:uppercase;
        }

        .logo-box{
            background:var(--header);
            text-align:center;
            color:#b6bcc3;
            font-weight:800;
            height:102px;
        }

        .logo-box-inner{
            height:100%;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .logo-placeholder{
            border:2px dashed #c9d1d9;
            padding:10px 16px;
            line-height:1.05;
            font-size:15px;
        }

        .head-row th{
            background:var(--blue-soft);
            color:#111827;
            font-size:13px;
            font-weight:800;
            text-align:center;
            padding:9px 8px !important;
        }

        .section-row td{
            background:var(--blue-main);
            color:#0f172a;
            font-size:14px;
            font-weight:800;
            text-align:center;
            padding:7px 10px !important;
        }

        .item-cell{
            text-align:center;
            font-weight:800;
            font-size:13px;
            background:#fbfcfd;
            padding:10px 6px !important;
        }

        .editable{
            background:#fff;
            min-height:54px;
            position:relative;
        }

        .editable input,
        .editable textarea{
            width:100%;
            height:100%;
            border:none;
            outline:none;
            background:transparent;
            color:var(--text);
            font-size:13px;
            padding:10px 10px;
            line-height:1.35;
        }

        .editable textarea{
            resize:none;
            min-height:54px;
        }

        .editable input:focus,
        .editable textarea:focus{
            background:#f8fbff;
        }

        .note-cell{
            color:var(--danger);
            font-size:12px;
            line-height:1.45;
            padding:12px 14px !important;
            font-weight:700;
            background:#fff;
        }

        .footer-help{
            padding:12px 16px;
            border-top:1px solid #e3e8ee;
            background:#fafcff;
            font-size:12px;
            color:var(--muted);
        }

        .w-item{ width:90px; }
        .w-desc{ width:290px; }
        .w-esp{ width:470px; }
        .w-norma{ width:330px; }

        @media (max-width: 768px){
            .page-wrap{
                padding:10px;
            }

            .sheet-header-title{
                font-size:14px;
            }

            .top-title{
                font-size:13px;
            }

            .top-subtitle{
                font-size:12px;
            }
        }

        @media print{
            @page{
                size:portrait;
                margin:10mm;
            }

            body{
                background:#fff !important;
            }

            .page-wrap{
                padding:0 !important;
            }

            .topbar,
            .sheet-header,
            .footer-help{
                display:none !important;
            }

            .sheet-card{
                border:none !important;
                border-radius:0 !important;
                box-shadow:none !important;
            }

            .sheet-scroll{
                overflow:visible !important;
            }

            .sheet{
                min-width:100% !important;
            }

            .editable input,
            .editable textarea{
                font-size:12px !important;
                background:transparent !important;
            }
        }
    </style>
  <link rel="stylesheet" href="../../../assets/css/soporte-unificado.css">
</head>
<body>

<div class="page-wrap">
    <div class="topbar">
        <div class="topbar-left">
            <a href="../planear.php" class="btn-ui">← Volver a Planear</a>
            <button type="button" class="btn-ui secondary" onclick="window.print()">🖨 Imprimir</button>
        </div>
        <div class="topbar-right">
            <span class="badge-format">Formato 2.9.1-2 · RE-SST-18</span>
        </div>
    </div>

    <div class="sheet-card">
        <div class="sheet-header">
            <h1 class="sheet-header-title">Especificaciones de las compras en SST</h1>
            <p class="sheet-header-subtitle">Formato editable con presentación profesional para el módulo Planear</p>
        </div>

        <div class="sheet-scroll">
            <div class="sheet">
                <table class="form-sheet">
                    <colgroup>
                        <col class="w-item">
                        <col class="w-desc">
                        <col class="w-esp">
                        <col class="w-norma">
                    </colgroup>

                    <tr>
                        <td rowspan="3" class="logo-box">
                            <div class="logo-box-inner">
                                <div class="logo-placeholder">
                                    TU LOGO<br>AQUÍ
                                </div>
                            </div>
                        </td>
                        <td colspan="2" class="top-title">SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO</td>
                        <td class="top-cell">0</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="top-subtitle">ESPECIFICACIONES DE LAS COMPRAS EN SST</td>
                        <td class="top-cell">RE-SST-18</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="top-cell">&nbsp;</td>
                        <td class="top-cell">XX/XX/2025</td>
                    </tr>

                    <tr class="head-row">
                        <th>Ítem</th>
                        <th>Descripción</th>
                        <th>Especificaciones</th>
                        <th>Normas específicas</th>
                    </tr>

                    <tr class="section-row">
                        <td colspan="4">Equipos de protección personal</td>
                    </tr>

                    <tr>
                        <td class="item-cell">a)</td>
                        <td class="editable"><input type="text" value="Gafas de seguridad"></td>
                        <td class="editable"><textarea rows="4">En policarbonato, liviana, anti-impacto, filtro UV 99,9%, resistencia a impactos, abrasión y salpicaduras de líquidos irritantes.</textarea></td>
                        <td class="editable"><textarea rows="4">ANSI Z87.1:2010</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">b)</td>
                        <td class="editable"><input type="text" value="Protector respiratorio"></td>
                        <td class="editable"><textarea rows="4">Respirador para partículas N95, protección contra polvo y partículas sin presencia de aceite.</textarea></td>
                        <td class="editable"><textarea rows="4">NIOSH bajo la especificación N95 de la norma 42CFR84.</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">c)</td>
                        <td class="editable"><input type="text" value="Guantes de impacto"></td>
                        <td class="editable"><textarea rows="4">Guante de alta sensibilidad con una alta resistencia, aplicaciones de peso medio.</textarea></td>
                        <td class="editable"><textarea rows="5">EN 420 Requisitos generales.
EN 388 Protección contra riesgo mecánico (3143X).</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">d)</td>
                        <td class="editable"><input type="text" value="Protectores auditivos de inserción"></td>
                        <td class="editable"><textarea rows="4">Polímero hipoalergénico, premoldeados, con tres falanges que se adaptan a la cavidad auditiva.</textarea></td>
                        <td class="editable"><textarea rows="4">ANSI S3.19-1974</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">e)</td>
                        <td class="editable"><input type="text" value="Botas de seguridad"></td>
                        <td class="editable"><textarea rows="4">Dieléctricas, antideslizantes, con puntera, livianas y resistentes a hidrocarburos.</textarea></td>
                        <td class="editable"><textarea rows="5">NTC ISO 20345, Numeral 8.2.3
ASTM F2413-05, Numeral 5.5.8.1
NTC ISO 20344:2007</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">f)</td>
                        <td class="editable"><input type="text" value="Guantes de hilaza con látex"></td>
                        <td class="editable"><textarea rows="3">Resistencia mecánica leve.</textarea></td>
                        <td class="editable"><textarea rows="3">EN 420 Requisitos generales.</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">g)</td>
                        <td class="editable"><input type="text" value="Gafas de seguridad"></td>
                        <td class="editable"><textarea rows="4">Gafas de protección ante proyección de partículas con protección frontal y lateral en material de policarbonato.</textarea></td>
                        <td class="editable"><textarea rows="4">ANSI Z87.1</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">h)</td>
                        <td class="editable"><input type="text" value="Guantes de seguridad"></td>
                        <td class="editable"><textarea rows="4">En poliuretano, diseñadas para procesos industriales y mantenimiento.</textarea></td>
                        <td class="editable"><textarea rows="4">EN166 CE
EN 388</textarea></td>
                    </tr>

                    <tr class="section-row">
                        <td colspan="4">Equipos emergencias</td>
                    </tr>

                    <tr>
                        <td class="item-cell">a)</td>
                        <td class="editable"><input type="text" value="Collarín"></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">b)</td>
                        <td class="editable"><input type="text" value="Extintor"></td>
                        <td class="editable"><textarea rows="4">Polvo químico seco BC, agente limpio, gas carbónico CO2.</textarea></td>
                        <td class="editable"><textarea rows="4">NFPA 10</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">c)</td>
                        <td class="editable"><input type="text" value="Camilla"></td>
                        <td class="editable"><textarea rows="4">Camilla rígida de 6.5 kg, de alta resistencia, resistente al agua, con arnés reflectivo y soporte hasta 180 kg.</textarea></td>
                        <td class="editable"><textarea rows="4">NTC 2885</textarea></td>
                    </tr>

                    <tr class="section-row">
                        <td colspan="4">Productos químicos</td>
                    </tr>

                    <tr>
                        <td class="item-cell">a)</td>
                        <td class="editable"><input type="text" value="Hojas de seguridad"></td>
                        <td class="editable"><textarea rows="3">Merakem - inhibidor de corrosión</textarea></td>
                        <td class="editable"><textarea rows="4">Documento bajo los criterios de peligro y las regulaciones controladas de los productos (CPR).</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">b)</td>
                        <td class="editable"><input type="text" value="Hojas de seguridad"></td>
                        <td class="editable"><textarea rows="3">Ikempol - polímero floculante</textarea></td>
                        <td class="editable"><textarea rows="4">Documento bajo los criterios de peligro y las regulaciones controladas de los productos (CPR).</textarea></td>
                    </tr>

                    <tr>
                        <td class="item-cell">c)</td>
                        <td class="editable"><input type="text" value="Fichas técnicas"></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                    </tr>

                    <tr class="section-row">
                        <td colspan="4">Equipos</td>
                    </tr>

                    <tr>
                        <td class="item-cell">&nbsp;</td>
                        <td class="editable"><input type="text" value=""></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                    </tr>

                    <tr class="section-row">
                        <td colspan="4">Maquinaria</td>
                    </tr>

                    <tr>
                        <td class="item-cell">&nbsp;</td>
                        <td class="editable"><input type="text" value=""></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                        <td class="editable"><textarea rows="3"></textarea></td>
                    </tr>

                    <tr>
                        <td colspan="4" class="note-cell">
                            NOTA. En esta matriz se deben incluir todos los requisitos y estándares de seguridad y salud necesarios para máquinas, herramientas, EPP, elementos de emergencia y todos aquellos equipos que se consideren necesarios en la organización al realizar las compras.
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer-help">
            Puedes editar directamente cada celda del formato antes de imprimirlo o integrarlo con guardado en base de datos.
        </div>
    </div>
</div>

</body>
</html>