<?php
session_start();
require_once '../../../../includes/ConexionAPI.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}

$api = new ConexionAPI();
$token = $_SESSION["token"];
$empresaId = $_SESSION["id_empresa"] ?? 0;
// Supongamos un ID por defecto para este formato del PESV si no viene por GET
$idItem = isset($_GET['item']) ? (int)$_GET['item'] : 55; 

// --- Lógica de Logo ---
$logoUrl = "";
if ($empresaId > 0) {
    $resEmpresa = $api->solicitar("index.php?table=empresas&id=$empresaId", "GET", null, $token);
    if (isset($resEmpresa['data'][0])) {
        $logoUrl = $resEmpresa['data'][0]['logo_url'] ?? '';
    }
}

// --- Carga de Datos Dinámicos ---
$resForm = $api->solicitar("formularios-dinamicos/empresa/$empresaId/item/$idItem", "GET", null, $token);
$datos = [];
$camposCrudos = $resForm['data']['data']['campos'] ?? $resForm['data']['campos'] ?? null;
if (is_string($camposCrudos)) $datos = json_decode($camposCrudos, true) ?: [];
elseif (is_array($camposCrudos)) $datos = $camposCrudos;

function oldv($key, $default = '') {
    global $datos;
    return isset($datos[$key]) ? htmlspecialchars((string)$datos[$key], ENT_QUOTES, 'UTF-8') : $default;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carta de Nombramiento - Representante de la Alta Dirección PESV</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== ESTILOS BASE / CONTENEDOR WEB ===== */
        :root {
            --primary-blue: #1a4175;
            --text-color: #000000;
            --border-color: #000000;
        }
        
        * {
            box-sizing: border-box;
        }

        body { 
            background-color: #f4f7f9; 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px; 
            color: var(--text-color);
        }

        /* Contenedor emula una hoja A4 */
        .format-container { 
            background: #fff; 
            max-width: 850px; 
            margin: 20px auto; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 0;
        }

        /* ===== TOP BAR (ACCIONES NO IMPRIMIBLES) ===== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #dbe3ef;
            padding: 12px 20px;
            border-bottom: 1px solid #b8c7dc;
        }

        .title-left {
            font-size: 16px;
            font-weight: 600;
            color: #1a4175;
        }

        .actions-right {
            display: flex;
            gap: 10px;
        }

        .btn-top {
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-top:hover { opacity: 0.9; }
        .btn-back { background: #6c757d; }
        .btn-save { background: #198754; }
        .btn-print { background: #0d6efd; }

        /* ===== DISEÑO DEL FORMATO DOCUMENTAL ===== */
        .document-body {
            padding: 40px 50px;
        }

        /* Estructura de Tabla de Membrete */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }

        .header-table td {
            border: 1px solid var(--border-color);
            padding: 6px 10px;
            font-size: 11px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        .header-title {
            font-size: 14px;
            color: #000;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            font-size: 12px;
            font-weight: normal;
            margin-top: 4px;
        }

        /* Títulos Centrales */
        .empresa-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .input-empresa {
            font-size: 18px;
            font-weight: bold;
            color: #e65c00; /* Color naranja de la imagen */
            text-align: center;
            border: none;
            border-bottom: 1px dashed #ccc;
            width: 70%;
            text-transform: uppercase;
            outline: none;
            padding: 4px;
        }

        .certifica-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        /* Cuerpo de Cláusulas y Funciones */
        .certifica-text {
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 25px;
            text-align: justify;
        }

        /* Campos editables integrados en el texto inline */
        .inline-input {
            border: none;
            border-bottom: 1px solid #000;
            font-size: 13px;
            font-weight: bold;
            padding: 2px 5px;
            outline: none;
            background: transparent;
        }
        .input-name { color: #ff0000; width: 220px; text-align: center; }
        .input-cc { color: #ff0000; width: 140px; text-align: center; }

        /* Lista de Funciones con Chulitos */
        .funciones-container {
            margin-bottom: 50px;
        }

        .funcion-row {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            font-size: 13px;
            line-height: 1.5;
            text-align: justify;
        }

        .funcion-check {
            display: table-cell;
            width: 35px;
            font-size: 16px;
            font-weight: bold;
            color: #000;
            vertical-align: top;
            padding-top: 1px;
        }

        .funcion-text {
            display: table-cell;
            vertical-align: top;
        }

        /* Sección de Firmas */
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 70px;
        }

        .firmas-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            font-size: 13px;
        }

        .linea-firma {
            width: 75%;
            border-top: 1px solid #000;
            margin: 0 auto 8px auto;
        }

        .cargo-firma {
            font-weight: normal;
            color: #000;
        }

        /* ===== CONFIGURACIÓN PARA IMPRESIÓN ===== */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .format-container {
                border: none;
                box-shadow: none;
                max-width: 100%;
                margin: 0;
            }
            .document-body {
                padding: 10mm 15mm; /* Márgenes estándar de impresión */
            }
            .inline-input {
                border-bottom: none; /* Elimina líneas de campo al imprimir si ya están llenas */
            }
            .input-empresa {
                border-bottom: none;
            }
        }
    </style>
</head>
<body>

<div class="format-container">
    
    <div class="top-bar no-print">
        <div class="title-left">
            <i class="fa-solid fa-file-signature"></i> Nombramiento Representante Alta Dirección (PESV)
        </div>
        <div class="actions-right">
            <button onclick="history.back()" class="btn-top btn-back">
                <i class="fa-solid fa-arrow-left"></i> Atrás
            </button>
            <button class="btn-top btn-save" id="btnGuardar">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
            </button>
            <button onclick="window.print()" class="btn-top btn-print">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <form id="formPESV">
        <div class="document-body">
            
            <table class="header-table">
                <tr>
                    <td rowspan="2" style="width: 20%; min-width: 140px;">
                        <?php if ($logoUrl): ?>
                            <img src="<?= $logoUrl ?>" style="max-height: 55px; max-width: 100%;">
                        <?php else: ?>
                            <div style="font-size: 9px; color: #999; font-weight: normal;">LOGO EMPRESA</div>
                        <?php endif; ?>
                    </td>
                    <td class="header-title" style="width: 60%;">
                        PLAN ESTRATÉGICO DE SEGURIDAD VIAL
                        <div class="header-subtitle">CARTA DE NOMBRAMIENTO, REPRESENTANTE POR LA ALTA DIRECCIÓN</div>
                    </td>
                    <td style="width: 20%; font-size: 10px;">
                        Versión: 0<br>
                        <span style="font-weight: normal;">AC-XX-SST-05</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: normal; font-size: 10px; padding-right: 15px;">
                        Fecha: <input type="text" name="fecha_formato" value="<?= oldv('fecha_formato', date('d/m/Y')) ?>" style="width: 75px; border:none; font-size:10px; text-align:right; outline:none;">
                    </td>
                </tr>
            </table>

            <div class="empresa-section">
                <input type="text" name="nombre_empresa" class="input-empresa" value="<?= oldv('nombre_empresa', 'NOMBRE DE LA EMPRESA') ?>" placeholder="EMPRESA">
            </div>

            <div class="certifica-title">CERTIFICA:</div>

            <div class="certifica-text">
                Que <input type="text" name="nombre_representante" class="inline-input input-name" value="<?= oldv('nombre_representante', 'NOMBRE') ?>" placeholder="Nombre Completo">, 
                con C.C <input type="text" name="cedula_representante" class="inline-input input-cc" value="<?= oldv('cedula_representante', 'C.C') ?>" placeholder="Número de Cédula"> 
                ha sido designada como representante de la Dirección para el Plan estratégico de seguridad vial, y se le han asignado las funciones, responsabilidades y autoridades para:
            </div>

            <div class="funciones-container">
                
                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Planear, organizar, dirigir, desarrollar y aplicar el PESV, y realizar por lo menos una vez al año su evaluación.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Asegurar que los requisitos del PESV se establezcan, implementen y mantengan, de acuerdo con lo indicado en el Decreto 1079 de 2015, Resolución 40595 del 2022 y demás normas asociadas.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Informar a la alta Dirección sobre el funcionamiento y los resultados del PESV.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Promover la participación de todos los miembros de la empresa en la implementación del PESV.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Asegurarse de que se promueva la toma de conciencia de la conformidad con los requisitos del PESV.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Programar las auditorías internas necesarias para el mantenimiento y mejora continua del PESV.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Diligenciar el reporte de autogestión anual y los resultados de la medición de los indicadores.</div>
                </div>

                <div class="funcion-row">
                    <div class="funcion-check">✓</div>
                    <div class="funcion-text">Analizar los indicadores de siniestralidad vial, las investigaciones internas de accidentes de tránsito y realizar seguimiento a los planes de acción.</div>
                </div>

            </div>

            <table class="firmas-table">
                <tr>
                    <td>
                        <div class="linea-firma"></div>
                        <div class="cargo-firma">Representante Legal</div>
                    </td>
                    <td>
                        <div class="linea-firma"></div>
                        <div class="cargo-firma">Encargado PESV</div>
                    </td>
                </tr>
            </table>

        </div>
    </form>
</div>

<script>
document.getElementById('btnGuardar').addEventListener('click', async function() {
    const btn = this;
    const form = document.getElementById('formPESV');
    const formData = new FormData(form);
    const datosJSON = Object.fromEntries(formData.entries());

    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...'; 
    btn.disabled = true;

    try {
        const response = await fetch("http://localhost/sstmanager-backend/public/formularios-dinamicos/guardar", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Authorization': 'Bearer <?= $token ?>' 
            },
            body: JSON.stringify({ 
                id_empresa: <?= $empresaId ?>, 
                id_item_sst: <?= $idItem ?>, 
                datos: datosJSON 
            })
        });

        const res = await response.json();
        if (res.ok || response.ok) {
            Swal.fire('¡Guardado!', 'La Carta de Nombramiento PESV ha sido actualizada con éxito.', 'success');
        } else {
            Swal.fire('Error', res.error || 'No se pudo guardar el documento.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'Fallo de conexión o error de red.', 'error');
    } finally {
        btn.innerHTML = originalText; 
        btn.disabled = false;
    }
});
</script>

</body>
</html>