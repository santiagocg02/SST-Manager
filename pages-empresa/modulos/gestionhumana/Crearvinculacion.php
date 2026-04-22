<?php
session_start();
require_once '../../../includes/ConexionAPI.php'; 

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["token"])) {
    header("Location: ../../../index.php");
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SST Manager - Formulario Ingreso</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --admin-green: #198754;
            --admin-blue: #003366;
            --bg-gray: #f4f7f6;
        }

        body { background-color: var(--bg-gray); font-family: sans-serif; }

        .form-container {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        /* Estilo de las Pestañas (Tabs) */
        .nav-tabs .nav-link {
            color: #666;
            font-weight: 600;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            margin-right: 5px;
        }
        .nav-tabs .nav-link.active {
            background-color: var(--admin-blue) !important;
            color: white !important;
            border-color: var(--admin-blue);
        }

        /* Estilo de Inputs */
        label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 3px;
            display: block;
        }
        .form-control, .form-select {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            font-size: 0.9rem;
            border-radius: 4px;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: var(--admin-green);
            box-shadow: none;
        }

        .section-header {
            background: #e9ecef;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .btn-cancelar { background-color: #ff3333; color: white; font-weight: bold; border: none; padding: 10px 30px; }
        .btn-aceptar { background-color: var(--admin-green); color: white; font-weight: bold; border: none; padding: 10px 30px; }
    </style>
</head>
<body>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-uppercase" style="color: var(--admin-blue);">Ingreso</h4>
        <div class="d-flex gap-4">
            <div>
                <label>Fecha contrato</label>
                <input type="date" class="form-control form-control-sm">
            </div>
            <div>
                <label>Número contrato</label>
                <input type="text" class="form-control form-control-sm" placeholder="Autogenerado">
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" id="vinculacionTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos">Datos básicos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="adicional-tab" data-bs-toggle="tab" data-bs-target="#adicional">Información adicional</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="dependientes-tab" data-bs-toggle="tab" data-bs-target="#dependientes">Dependientes</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="afiliaciones-tab" data-bs-toggle="tab" data-bs-target="#afiliaciones">Afiliaciones</button>
        </li>
    </ul>

    <div class="form-container">
        <form id="formVinculacion">
            <div class="tab-content" id="myTabContent">
                
                <div class="tab-pane fade show active" id="datos">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Empleado</label>
                            <select class="form-select"><option>Seleccione...</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Tipo contrato</label>
                            <select class="form-select"><option>Término Indefinido</option></select>
                        </div>
                        <div class="col-md-2">
                            <label>Salario básico</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label>Tipo personal</label>
                            <select class="form-select"><option>Operativo</option></select>
                        </div>

                        <div class="col-md-3">
                            <label>Tipo liquidación</label>
                            <select class="form-select"><option>Mensual</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Config liquidación</label>
                            <select class="form-select"><option>Estándar</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Fecha inicio nómina</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="ley50">
                                <label class="form-check-label" for="ley50">Ley 50</label>
                            </div>
                            <input type="date" class="form-control ms-2" title="Fecha Fin Contrato">
                        </div>

                        <div class="col-md-3">
                            <label>Régimen laboral</label>
                            <select class="form-select"><option>Privado</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Cargo</label>
                            <select class="form-select"><option>Seleccione...</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Sub Cargo</label>
                            <select class="form-select"><option>Seleccione...</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Forma de pago</label>
                            <select class="form-select"><option>Transferencia</option></select>
                        </div>

                        <div class="col-md-3">
                            <label>Banco</label>
                            <select class="form-select"><option>Bancolombia</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Número cuenta</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Tipo de cuenta</label>
                            <select class="form-select"><option>Ahorros</option></select>
                        </div>
                        <div class="col-md-2">
                            <label>Grupo</label>
                            <select class="form-select"><option>General</option></select>
                        </div>
                        <div class="col-md-2">
                            <label>Departamento</label>
                            <select class="form-select"><option>Seleccione...</option></select>
                        </div>

                        <div class="col-md-3">
                            <label>Reportar a</label>
                            <select class="form-select"><option>Seleccione...</option></select>
                        </div>
                        <div class="col-md-3">
                            <label>Sede</label>
                            <select class="form-select"><option>Principal</option></select>
                        </div>
                        <div class="col-md-2">
                            <label>Días vacaciones</label>
                            <input type="number" class="form-control" value="15">
                        </div>
                        <div class="col-md-2">
                            <label>Año</label>
                            <select class="form-select"><option>2026</option></select>
                        </div>
                        <div class="col-md-2">
                            <label>Centro Costo</label>
                            <select class="form-select"><option>Administrativo</option></select>
                        </div>

                        <div class="col-md-12 mt-4 text-primary fw-bold">PROYECCIÓN DE PRESTACIONES</div>
                        <div class="col-md-3">
                            <label>Fecha inicial cesantías</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Saldo Cesantías</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha inicial Prima</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Fecha inicial Vacación</label>
                            <input type="date" class="form-control">
                        </div>

                        <div class="col-md-12 mt-2">
                             <a href="#" class="text-decoration-none small italic"><i class="fa-solid fa-file-pdf"></i> ver hoja de vida</a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="adicional">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label>Subtipo Aportante (seleccione numerla)</label>
                            <select class="form-select"><option>00 - No aplica</option></select>
                        </div>
                        <div class="col-md-12">
                            <label>Observación</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="section-header">Informativo: Periodo de vacaciones a fecha de este contrato</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Fecha inicio vacaciones</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Fecha fin vacaciones</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tiempoParcial">
                                <label class="form-check-label" for="tiempoParcial">¿Vínculo de tiempo diario no completo?</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Horas de medio tiempo</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Cod actividad Economica</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exterior">
                                <label class="form-check-label" for="exterior">Fecha laboral en el exterior</label>
                            </div>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="dependientes">
                    <p class="text-muted p-4 text-center">No hay dependientes registrados para este contrato.</p>
                </div>

                <div class="tab-pane fade" id="afiliaciones">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Fecha novedad</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Tipo novedad</label>
                            <select class="form-select"><option>Ingreso Entidad</option></select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="soi">
                                <label class="form-check-label" for="soi">No reportar en soi</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>Descripción</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label>Salud</label>
                            <select class="form-select"><option>Seleccione EPS...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label>Caja</label>
                            <select class="form-select"><option>Seleccione Caja...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label>Cesantías</label>
                            <select class="form-select"><option>Seleccione Fondo...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label>Pensión</label>
                            <select class="form-select"><option>Seleccione Fondo...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label>Arl</label>
                            <select class="form-select"><option>Seleccione ARL...</option></select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-5">
                <button type="button" class="btn btn-cancelar rounded-pill">Cancelar</button>
                <button type="submit" class="btn btn-aceptar rounded-pill">Aceptar</button>
            </div>
        </form>
    </div>

    <div class="mt-4">
        <button class="btn btn-light border shadow-sm"><i class="fa-solid fa-arrow-left"></i></button>
        <button class="btn btn-light border shadow-sm ms-2"><i class="fa-solid fa-arrow-right"></i></button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>