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
        
        .doc-group-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--admin-blue);
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 10px;
            margin-top: 15px;
            font-weight: bold;
        }
        
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

        .btn-cancelar { background-color: #6c757d; color: white; font-weight: bold; border: none; padding: 10px 30px; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.3s ease; text-decoration: none;}
        .btn-cancelar:hover { background-color: var(--admin-blue); color: white; }
        
        .btn-aceptar { background: linear-gradient(145deg, #198754, #146c43); color: white; font-weight: bold; border: none; padding: 10px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.15); transition: all 0.3s ease; }
        .btn-aceptar:hover { background: #146c43; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transform: translateY(-1px); }

        .table-checklist th { background-color: var(--admin-blue); color: white; font-size: 0.85rem; }
        .table-checklist td { font-size: 0.85rem; vertical-align: middle; }
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
        <li class="nav-item"><button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos">Datos básicos</button></li>
        <li class="nav-item"><button class="nav-link" id="adicional-tab" data-bs-toggle="tab" data-bs-target="#adicional">Información adicional</button></li>
        <li class="nav-item"><button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos">Documentos</button></li>
        <li class="nav-item"><button class="nav-link" id="afiliaciones-tab" data-bs-toggle="tab" data-bs-target="#afiliaciones">Afiliaciones</button></li>
        <li class="nav-item"><button class="nav-link text-primary fw-bold" id="novedades-tab" data-bs-toggle="tab" data-bs-target="#novedades"><i class="fa-solid fa-bell me-1"></i> Novedades</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" style="color: var(--admin-blue);" id="disciplinario-tab" data-bs-toggle="tab" data-bs-target="#disciplinario"><i class="fa-solid fa-gavel me-1"></i> Proceso Disciplinario</button></li>
    </ul>

    <div class="form-container">
        <form id="formVinculacion" enctype="multipart/form-data">
            <div class="tab-content" id="myTabContent">
                
                <!-- PESTAÑA 1: DATOS BÁSICOS -->
                <div class="tab-pane fade show active" id="datos">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Tipo Doc.</label>
                            <select class="form-select">
                                <option value="CC" selected>CC</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PPT">PPT</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Número de Identificación</label>
                            <input type="text" class="form-control" placeholder="Número de documento">
                        </div>
                        <div class="col-md-6">
                            <label>Nombres y Apellidos</label>
                            <input type="text" class="form-control" placeholder="Nombre completo">
                        </div>
                        
                        <div class="col-md-3">
                            <label>Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" onchange="calcularEdad(this.value)">
                        </div>
                        <div class="col-md-2">
                            <label>Edad</label>
                            <input type="text" class="form-control" id="edad_empleado" readonly placeholder="0 años">
                        </div>
                        <div class="col-md-3">
                            <label>Género</label>
                            <select class="form-select">
                                <option selected disabled>Seleccione...</option>
                                <option value="F">Femenino (F)</option>
                                <option value="M">Masculino (M)</option>
                                <option value="LGTBIQ+">LGTBIQ+</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-3 doc-group-title">Lugar de Nacimiento</div>
                        <div class="col-md-4">
                            <label>País</label>
                            <input type="text" class="form-control" placeholder="Ej. Colombia">
                        </div>
                        <div class="col-md-4">
                            <label>Departamento</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Ciudad</label>
                            <input type="text" class="form-control">
                        </div>

                        <div class="col-md-12 mt-3 doc-group-title">Contacto y Residencia</div>
                        <div class="col-md-3">
                            <label>Teléfono</label>
                            <input type="tel" class="form-control">
                        </div>
                        <div class="col-md-9">
                            <label>Correo electrónico</label>
                            <input type="email" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>País de Residencia</label>
                            <input type="text" class="form-control" placeholder="Ej. Colombia">
                        </div>
                        <div class="col-md-4">
                            <label>Departamento</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Ciudad</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label>Dirección</label>
                            <input type="text" class="form-control" placeholder="Calle/Carrera">
                        </div>
                        <div class="col-md-4">
                            <label>Barrio</label>
                            <input type="text" class="form-control">
                        </div>

                        <div class="col-md-12 mt-3 doc-group-title">Información Socio-demográfica</div>
                        <div class="col-md-3">
                            <label>Tipo de vivienda</label>
                            <select class="form-select">
                                <option selected disabled>Seleccione...</option>
                                <option value="Propia">Propia</option>
                                <option value="Arrendada">Arrendada</option>
                                <option value="Familiar">Familiar</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Estrato</label>
                            <select class="form-select">
                                <option selected disabled>Seleccione...</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Estado civil</label>
                            <select class="form-select">
                                <option selected disabled>Seleccione...</option>
                                <option value="Soltero(a)">Soltero(a)</option>
                                <option value="Casado(a)">Casado(a)</option>
                                <option value="Unión Libre">Unión Libre</option>
                                <option value="Divorciado(a)">Divorciado(a)</option>
                                <option value="Viudo(a)">Viudo(a)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label>¿Con quién vive?</label>
                            <select class="form-select" id="con_quien_vive" onchange="toggleHijos()">
                                <option selected disabled>Seleccione...</option>
                                <option value="Solo">Solo</option>
                                <option value="Padres">Padres</option>
                                <option value="Esposa/Esposo">Esposa/Esposo</option>
                                <option value="Hijos">Solo Hijos</option>
                                <option value="Esposa e hijos">Esposa e hijos</option>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label>¿Cuántos hijos?</label>
                            <input type="number" class="form-control" id="cuantos_hijos" disabled placeholder="0" min="1" oninput="generarFilasHijos()">
                        </div>

                        <div class="col-md-9 mt-3 d-none" id="cont_doc_dependientes">
                            <label class="text-primary"><i class="fa-solid fa-file-upload"></i> Cargar Soportes Familiares (Cédula cónyuge / Registros civiles)</label>
                            <input type="file" class="form-control" name="doc_soportes_familiares" accept=".pdf,image/*" multiple>
                            <small class="text-muted">Puede seleccionar múltiples archivos simultáneamente.</small>
                        </div>

                        <div class="col-md-12 d-none" id="cont_detalles_hijos">
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-sm mb-0 bg-white shadow-sm rounded">
                                    <thead class="table-light text-secondary">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">#</th>
                                            <th style="width: 15%;">Edad</th>
                                            <th style="width: 20%;">Género</th>
                                            <th style="width: 20%;">¿Discapacidad?</th>
                                            <th style="width: 40%;">Detalle de Discapacidad</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_detalles_hijos">
                                        <!-- Se inyectan las filas vía JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3 doc-group-title">Información Médica</div>
                        <div class="col-md-3">
                            <label>¿Alergias?</label>
                            <div class="mt-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="alergias" id="alergiaSi" value="si" onchange="toggleAlergias()">
                                    <label class="form-check-label" for="alergiaSi">Sí</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="alergias" id="alergiaNo" value="no" checked onchange="toggleAlergias()">
                                    <label class="form-check-label" for="alergiaNo">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label>¿Cuál(es)?</label>
                            <input type="text" class="form-control" id="cualAlergia" disabled placeholder="Especifique la alergia">
                        </div>

                        <div class="col-md-12 mt-3 doc-group-title">Datos Contratación</div>
                        <div class="col-md-2">
                            <label>Estado</label>
                            <select class="form-select bg-white fw-bold" id="estado_empleado" style="color: var(--admin-blue);">
                                <option value="Activo" selected>Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Retirado">Retirado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Fecha terminación</label>
                            <input type="date" class="form-control" id="fecha_terminacion" disabled>
                            <small class="text-danger fw-bold" id="msg_terminacion" style="font-size: 0.7rem; display:none;">¡Habilitado por retiro!</small>
                        </div>
                        <div class="col-md-2">
                            <label id="lbl_fecha_ingreso">Fecha de ingreso</label>
                            <input type="date" class="form-control" id="fecha_ingreso">
                        </div>
                        <div class="col-md-3">
                            <label>Cargo</label>
                            <input type="text" class="form-control" placeholder="Nombre del cargo">
                        </div>
                        <div class="col-md-3">
                            <label>Salario básico</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label>Tipo contrato</label>
                            <select class="form-select"><option>Término Indefinido</option></select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label>Tipo personal</label>
                            <select class="form-select"><option>Operativo</option></select>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 2: ADICIONAL (MODIFICADA CON LOS NUEVOS CAMPOS) -->
                <div class="tab-pane fade" id="adicional">
                    <div class="row g-3">
                        
                        <!-- NUEVO: EXPERIENCIA Y ANTIGÜEDAD -->
                        <div class="col-md-12 mt-2">
                            <div class="section-header">Experiencia y Antigüedad</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Experiencia en el cargo</label>
                                    <select class="form-select" name="experiencia_cargo">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="Menos de 6 meses">Menos de 6 meses</option>
                                        <option value="Entre 6 meses y 1 año">Entre 6 meses y 1 año</option>
                                        <option value="Entre 1 y 3 años">Entre 1 y 3 años</option>
                                        <option value="Más de 3 años">Más de 3 años</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Antigüedad en la empresa</label>
                                    <select class="form-select" name="antiguedad_empresa">
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="Menos de 6 meses">Menos de 6 meses</option>
                                        <option value="Entre 6 meses y 1 año">Entre 6 meses y 1 año</option>
                                        <option value="Entre 1 y 3 años">Entre 1 y 3 años</option>
                                        <option value="Más de 3 años">Más de 3 años</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- HÁBITOS, USO DE TIEMPO LIBRE Y CONSUMO -->
                        <div class="col-md-12 mt-4">
                            <div class="section-header">Hábitos y Estilo de Vida</div>
                            <div class="row g-3">
                                
                                <!-- NUEVO: USO DEL TIEMPO LIBRE (CHECKBOXES) -->
                                <div class="col-md-6">
                                    <label>Uso del tiempo libre (Seleccione varias si aplica)</label>
                                    <div class="d-flex flex-wrap gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tiempo_libre[]" value="Tiene otro trabajo" id="tl_trabajo">
                                            <label class="form-check-label" for="tl_trabajo">Tiene otro trabajo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tiempo_libre[]" value="Descansa" id="tl_descansa">
                                            <label class="form-check-label" for="tl_descansa">Descansa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tiempo_libre[]" value="Recreación y deporte" id="tl_deporte">
                                            <label class="form-check-label" for="tl_deporte">Recreación y deporte</label>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" value="Otro" id="tl_otro" onchange="document.getElementById('tl_otro_cual').disabled = !this.checked">
                                            <label class="form-check-label" for="tl_otro">Otro, ¿cuál?</label>
                                        </div>
                                        <input type="text" class="form-control form-control-sm w-auto" id="tl_otro_cual" disabled placeholder="Especifique">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>Hábitos de consumo (Alimenticios/Otros)</label>
                                    <textarea class="form-control" rows="2" placeholder="Ej: Dieta balanceada, comida rápida, etc..."></textarea>
                                </div>
                                
                                <div class="col-md-3 mt-3">
                                    <label>¿Fuma?</label>
                                    <div class="mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fuma" id="fumaSi" value="si" onchange="toggleFrecuencia('fuma')">
                                            <label class="form-check-label" for="fumaSi">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fuma" id="fumaNo" value="no" checked onchange="toggleFrecuencia('fuma')">
                                            <label class="form-check-label" for="fumaNo">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label>Frecuencia (Fuma)</label>
                                    <input type="text" class="form-control" id="frecuenciaFuma" disabled placeholder="Ej: Diario, Social...">
                                </div>

                                <div class="col-md-3 mt-3">
                                    <label>¿Bebe alcohol?</label>
                                    <div class="mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bebe" id="bebeSi" value="si" onchange="toggleFrecuencia('bebe')">
                                            <label class="form-check-label" for="bebeSi">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bebe" id="bebeNo" value="no" checked onchange="toggleFrecuencia('bebe')">
                                            <label class="form-check-label" for="bebeNo">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <label>Frecuencia (Bebe)</label>
                                    <input type="text" class="form-control" id="frecuenciaBebe" disabled placeholder="Ej: Fines de semana...">
                                </div>
                            </div>
                        </div>

                        <!-- NUEVO: CONDICIONES DE SALUD, ENTORNO Y SST -->
                        <div class="col-md-12 mt-4">
                            <div class="section-header border-danger" style="border-left: 5px solid;">Condiciones de Salud, Entorno y SST</div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label>¿Cuántas personas conforman su núcleo familiar?</label>
                                    <input type="number" class="form-control" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label>¿De cuántas personas está usted a cargo?</label>
                                    <input type="number" class="form-control" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label>¿Cuántas comidas ingiere al día?</label>
                                    <input type="number" class="form-control" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label>¿Toma algún medicamento? ¿Cuál?</label>
                                    <input type="text" class="form-control" placeholder="Ninguno / Especifique">
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>¿Padece de alguna enfermedad? ¿Cuál?</label>
                                    <input type="text" class="form-control" placeholder="Ninguna / Especifique">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label>¿Con qué frecuencia se realiza un chequeo médico?</label>
                                    <input type="text" class="form-control" placeholder="Ej: Anual, Semestral...">
                                </div>

                                <!-- Incapacidades Generales -->
                                <div class="col-md-3 mt-3">
                                    <label>¿Ha estado incapacitado en el último año?</label>
                                    <div class="mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="incap_ano" id="incap_ano_si" value="si" onchange="toggleInputsSalud('incapacidad')">
                                            <label class="form-check-label" for="incap_ano_si">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="incap_ano" id="incap_ano_no" value="no" checked onchange="toggleInputsSalud('incapacidad')">
                                            <label class="form-check-label" for="incap_ano_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9 mt-3">
                                    <label>Si se ha incapacitado, ¿cuál fue la causa?</label>
                                    <input type="text" class="form-control" id="causa_incapacidad" disabled placeholder="Especifique causa">
                                </div>

                                <!-- Incapacidades por AT -->
                                <div class="col-md-4 mt-3">
                                    <label>¿Ha tenido alguna incapacidad al sufrir un accidente de trabajo?</label>
                                    <div class="mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="incap_at" id="incap_at_si" value="si" onchange="toggleInputsSalud('at')">
                                            <label class="form-check-label" for="incap_at_si">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="incap_at" id="incap_at_no" value="no" checked onchange="toggleInputsSalud('at')">
                                            <label class="form-check-label" for="incap_at_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 mt-3">
                                    <label>¿De qué tipo?</label>
                                    <input type="text" class="form-control" id="tipo_incap_at" disabled placeholder="Especifique el tipo">
                                </div>

                                <!-- Capacitación SST -->
                                <div class="col-md-4 mt-3">
                                    <label>¿Ha recibido capacitación en Seguridad y Salud en el Trabajo?</label>
                                    <div class="mt-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cap_sst" id="cap_sst_si" value="si" onchange="toggleInputsSalud('sst')">
                                            <label class="form-check-label" for="cap_sst_si">Sí</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="cap_sst" id="cap_sst_no" value="no" checked onchange="toggleInputsSalud('sst')">
                                            <label class="form-check-label" for="cap_sst_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 mt-3">
                                    <label>Si contestó Sí, ¿qué aspectos conoce?</label>
                                    <input type="text" class="form-control" id="aspectos_sst" disabled placeholder="Especifique los temas">
                                </div>
                            </div>
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

                        <div class="col-md-6 mt-4">
                            <div class="section-header">Beneficios Adicionales</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Póliza de seguros de vida</label>
                                    <select class="form-select">
                                        <option selected disabled>Seleccione...</option>
                                        <option value="Si">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Servicios funerarios</label>
                                    <select class="form-select">
                                        <option selected disabled>Seleccione...</option>
                                        <option value="Si">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 3: DOCUMENTOS -->
                <div class="tab-pane fade" id="documentos">
                    <div class="row g-3 mt-2">
                        
                        <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center">
                            <div class="section-header w-100 mb-0 d-flex justify-content-between align-items-center">
                                <span>Expediente Digital: Carga y Generación de Documentos</span>
                                <div>
                                    <button type="button" class="btn btn-warning btn-sm me-2 fw-bold" onclick="validarDocumentos()">
                                        <i class="fa-solid fa-check-double"></i> Validar Carga vs Checklist
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalChecklist">
                                        <i class="fa-solid fa-list-check"></i> Llenar FOR-GH-04
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 doc-group-title">Identidad y Hoja de Vida</div>
                        <div class="col-md-6">
                            <label>Hoja de Vida con Foto</label>
                            <input type="file" class="form-control" name="doc_hv" id="doc_hv" accept=".pdf">
                        </div>
                        <div class="col-md-6">
                            <label>Fotocopia de C.C. Ampliada al 150%</label>
                            <input type="file" class="form-control" name="doc_cedula" id="doc_cedula" accept=".pdf,image/*">
                        </div>

                        <!-- SECCIÓN REQUISITOS GRUPO FAMILIAR -->
                        <div class="col-md-12 doc-group-title mt-4 bg-light p-2 border rounded text-dark">
                            <i class="fa-solid fa-people-roof text-primary"></i> Requisitos para Afiliación de Beneficiarios (EPS / CAJA)
                        </div>
                        
                        <div class="col-md-12 mb-2">
                            <label class="fw-bold">¿A quién desea afiliar como beneficiario? (Las opciones se marcan solas según la pestaña Datos Básicos)</label>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_conyuge" onchange="toggleDocsBeneficiarios()">
                                    <label class="form-check-label" for="chk_conyuge">Cónyuge / Esposo(a)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_hijos" onchange="toggleDocsBeneficiarios()">
                                    <label class="form-check-label" for="chk_hijos">Hijos / Hijastros</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_padres" onchange="toggleDocsBeneficiarios()">
                                    <label class="form-check-label" for="chk_padres">Padres (> 60 años)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_hermanos" onchange="toggleDocsBeneficiarios()">
                                    <label class="form-check-label" for="chk_hermanos">Hermanos Huérfanos</label>
                                </div>
                            </div>
                        </div>

                        <!-- Bloques Dinámicos de Familiares -->
                        <div class="col-md-12 d-none" id="docs_req_conyuge">
                            <div class="row g-3 p-3 border rounded mb-2 border-primary" style="background-color: #f8fbff;">
                                <div class="col-md-12 fw-bold text-primary mb-1"><i class="fa-solid fa-ring"></i> Documentos Cónyuge / Esposo(a)</div>
                                <div class="col-md-6">
                                    <label>Copia Documento Identificación (Legible)</label>
                                    <input type="file" class="form-control" name="doc_cedula_conyuge" accept=".pdf,image/*">
                                </div>
                                <div class="col-md-6">
                                    <label>Acta/Registro Matrimonio o Decl. Extrajuicio (Legible)</label>
                                    <input type="file" class="form-control" name="doc_matrimonio_conyuge" accept=".pdf,image/*">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="docs_req_hijos">
                            <div class="row g-3 p-3 border rounded mb-2 border-success" style="background-color: #f8fff9;">
                                <div class="col-md-12 fw-bold text-success mb-1"><i class="fa-solid fa-children"></i> Documentos Hijos / Hijastros</div>
                                <div class="col-md-3">
                                    <label>Registro Civil Nacimiento (Todos)</label>
                                    <input type="file" class="form-control" name="doc_rc_hijos[]" accept=".pdf,image/*" multiple>
                                    <small class="text-muted" style="font-size: 0.7rem;">Documento original y legible</small>
                                </div>
                                <div class="col-md-3">
                                    <label>Tarjeta Identidad (Mayores 7 años)</label>
                                    <input type="file" class="form-control" name="doc_ti_hijos[]" accept=".pdf,image/*" multiple>
                                </div>
                                <div class="col-md-3">
                                    <label>Certificado Estudio (Mayores 12 años)</label>
                                    <input type="file" class="form-control" name="doc_estudio_hijos[]" accept=".pdf,image/*" multiple>
                                    <small class="text-muted" style="font-size: 0.7rem;">Documento original</small>
                                </div>
                                <div class="col-md-3">
                                    <label>Custodia ICBF/Fiscalía (Solo Hijastros)</label>
                                    <input type="file" class="form-control" name="doc_custodia_hijastros[]" accept=".pdf,image/*" multiple>
                                    <small class="text-muted" style="font-size: 0.7rem;">Documento original y legible</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="docs_req_padres">
                            <div class="row g-3 p-3 border rounded mb-2 border-warning" style="background-color: #fffdf8;">
                                <div class="col-md-12 fw-bold text-warning" style="color: #d39e00 !important; margin-bottom: 4px;"><i class="fa-solid fa-person-cane"></i> Documentos Padres (> 60 años)</div>
                                <div class="col-md-4">
                                    <label>Registro Civil Trabajador (Donde figuren padres)</label>
                                    <input type="file" class="form-control" name="doc_rc_trabajador_padres" accept=".pdf,image/*">
                                    <small class="text-muted" style="font-size: 0.7rem;">Documento Original</small>
                                </div>
                                <div class="col-md-4">
                                    <label>Fotocopia Cédula de los Padres (Legible)</label>
                                    <input type="file" class="form-control" name="doc_cedula_padres[]" accept=".pdf,image/*" multiple>
                                </div>
                                <div class="col-md-4">
                                    <label>Certificado EPS (Debe decir Beneficiario)</label>
                                    <input type="file" class="form-control" name="doc_eps_padres[]" accept=".pdf,image/*" multiple>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="docs_req_hermanos">
                            <div class="row g-3 p-3 border rounded mb-2 border-secondary" style="background-color: #fcfcfc;">
                                <div class="col-md-12 fw-bold text-secondary mb-1"><i class="fa-solid fa-users"></i> Documentos Hermanos Huérfanos</div>
                                <div class="col-md-4">
                                    <label>Reg. Civiles Defunción Padre/Madre</label>
                                    <input type="file" class="form-control" name="doc_defuncion_padres[]" accept=".pdf,image/*" multiple>
                                    <small class="text-muted" style="font-size: 0.7rem;">Solo documento original</small>
                                </div>
                                <div class="col-md-4">
                                    <label>Reg. Civil Nacimiento Hermanos (Original)</label>
                                    <input type="file" class="form-control" name="doc_rc_hermanos[]" accept=".pdf,image/*" multiple>
                                </div>
                                <div class="col-md-4">
                                    <label>Certificado Estudio (Hermanos > 12 años)</label>
                                    <input type="file" class="form-control" name="doc_estudio_hermanos[]" accept=".pdf,image/*" multiple>
                                </div>
                            </div>
                        </div>
                        <!-- FIN SECCIÓN REQUISITOS GRUPO FAMILIAR -->

                        <div class="col-md-12 doc-group-title mt-4">Certificaciones y Estudios</div>
                        <div class="col-md-6">
                            <label>Certificaciones Laborales / Cartas Recomendación</label>
                            <input type="file" class="form-control" name="doc_cert_laborales" accept=".pdf">
                        </div>
                        <div class="col-md-6">
                            <label>Certificaciones de Estudios (Diploma, cursos)</label>
                            <input type="file" class="form-control" name="doc_cert_estudios" accept=".pdf">
                        </div>

                        <div class="col-md-12 doc-group-title">Autorizaciones y Consentimientos</div>
                        <div class="col-md-3">
                            <label>Ficha Técnica Aspirante</label>
                            <input type="file" class="form-control" name="doc_ficha_aspirante" id="doc_ficha_aspirante" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Declaración Estado de Salud</label>
                            <input type="file" class="form-control" name="doc_estado_salud" id="doc_estado_salud" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Autorización Pruebas Varias</label>
                            <input type="file" class="form-control" name="doc_pruebas_varias" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Autorización Datos Biométricos</label>
                            <input type="file" class="form-control" name="doc_biometricos" accept=".pdf">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label>Tratamiento Datos Personales (GDPR)</label>
                            <input type="file" class="form-control" name="doc_gdpr" id="doc_gdpr" accept=".pdf">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label>Autorización Explotación Imagen</label>
                            <input type="file" class="form-control" name="doc_imagen" id="doc_imagen" accept=".pdf">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label>Cláusula de Confidencialidad</label>
                            <input type="file" class="form-control" name="doc_confidencialidad" id="doc_confidencialidad" accept=".pdf">
                        </div>

                        <div class="col-md-12 doc-group-title">Consultas y Antecedentes</div>
                        <div class="col-md-3">
                            <label>Certificado Contraloría</label>
                            <input type="file" class="form-control" name="doc_contraloria" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Certificado Procuraduría</label>
                            <input type="file" class="form-control" name="doc_procuraduria" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Certificado Policía Nacional</label>
                            <input type="file" class="form-control" name="doc_policia" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Certificado Inhabilidades Sex.</label>
                            <input type="file" class="form-control" name="doc_inhabilidades" accept=".pdf">
                        </div>

                        <div class="col-md-12 doc-group-title">Soportes de Afiliaciones</div>
                        <div class="col-md-3">
                            <label>Soporte Afiliación a EPS</label>
                            <input type="file" class="form-control" name="doc_eps" id="doc_eps" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Soporte Afiliación a ARL</label>
                            <input type="file" class="form-control" name="doc_arl" id="doc_arl" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Soporte Afiliación a AFP</label>
                            <input type="file" class="form-control" name="doc_afp" id="doc_afp" accept=".pdf">
                        </div>
                        <div class="col-md-3">
                            <label>Soporte Afiliación a Caja</label>
                            <input type="file" class="form-control" name="doc_caja" id="doc_caja" accept=".pdf">
                        </div>

                        <div class="col-md-12 doc-group-title mt-4 text-primary border-primary">Trámites Bancarios y Nómina</div>
                        <div class="col-md-6">
                            <label>Certificado Bancario</label>
                            <input type="file" class="form-control" name="doc_certificado_bancario" accept=".pdf">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary fw-bold w-100" onclick="abrirModalDocumento('apertura_cuenta')">
                                <i class="fa-solid fa-envelope-open-text me-1"></i> Generar Carta Apertura de Cuenta
                            </button>
                        </div>

                        <div class="col-md-12 doc-group-title mt-4 text-success border-success">Trámites Cesantías (Carpeta 2026 OBARA)</div>
                        <div class="col-md-6">
                            <label>Soporte / Autorización Retiro Cesantías</label>
                            <input type="file" class="form-control" name="doc_retiro_cesantias" accept=".pdf">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-success fw-bold w-100" onclick="abrirModalDocumento('retiro_cesantias')">
                                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Generar Carta Retiro Cesantías
                            </button>
                        </div>
                        <div class="col-md-12 mt-2">
                            <small class="text-muted"><i class="fa-solid fa-circle-info"></i> Nota: Si el empleado está Inactivo/Retirado en sus Datos Básicos, la carta usará automáticamente la causal de "Terminación de Contrato" y tomará las fechas de Ingreso y Terminación.</small>
                        </div>

                        <div class="col-md-12 doc-group-title mt-4">Puesto de Trabajo y Contractuales</div>
                        <div class="col-md-4">
                            <label>Acta Reglamento Interno</label>
                            <input type="file" class="form-control" name="doc_reglamento" accept=".pdf">
                        </div>
                        <div class="col-md-4">
                            <label>Formato Inducción Puesto</label>
                            <input type="file" class="form-control" name="doc_induccion" accept=".pdf">
                        </div>
                        <div class="col-md-4">
                            <label>Formato Perfil del Cargo</label>
                            <input type="file" class="form-control" name="doc_perfil_cargo" accept=".pdf">
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="p-3 border rounded bg-white shadow-sm h-100">
                                <label class="text-primary mb-2"><i class="fa-solid fa-file-signature me-1"></i> Contrato / Otro Sí</label>
                                <div class="d-flex gap-2">
                                    <input type="file" class="form-control form-control-sm w-50" name="doc_contrato" id="doc_contrato" accept=".pdf">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-50 fw-bold" onclick="abrirModalDocumento('contrato')">
                                        Generar / Editar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="p-3 border rounded bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                                <label class="text-success mb-2"><i class="fa-solid fa-certificate me-1"></i> Certificación Laboral</label>
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button" class="btn btn-sm btn-outline-success fw-bold px-4" onclick="abrirModalDocumento('certificado')">
                                        Generar Certificado
                                    </button>
                                    <span class="text-muted" style="font-size: 0.75rem;">Generación automática</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 doc-group-title mt-4 text-danger border-danger">Documentos de Retiro / Terminación</div>
                        <div class="col-md-6">
                            <label class="text-danger">Carta de Terminación de Contrato</label>
                            <input type="file" class="form-control" name="doc_carta_terminacion" id="doc_carta_terminacion" accept=".pdf" onchange="verificarDocumentosRetiro()">
                        </div>
                        <div class="col-md-6">
                            <label class="text-danger">Respuesta a Renuncia</label>
                            <input type="file" class="form-control" name="doc_respuesta_renuncia" id="doc_respuesta_renuncia" accept=".pdf" onchange="verificarDocumentosRetiro()">
                        </div>

                    </div>
                </div>

                <!-- PESTAÑA 4: AFILIACIONES -->
                <div class="tab-pane fade" id="afiliaciones">
                    <div class="row g-3 mt-2">
                        <div class="col-md-12 mb-2">
                            <div class="section-header">Entidades Actuales de Afiliación</div>
                        </div>

                        <div class="col-md-4">
                            <label>Salud (EPS)</label>
                            <select class="form-select"><option>Seleccione EPS...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label>Caja de Compensación</label>
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
                        <div class="col-md-4">
                            <label>Nivel de Riesgo</label>
                            <select class="form-select">
                                <option selected disabled>Seleccione nivel...</option>
                                <option value="1">Clase I</option>
                                <option value="2">Clase II</option>
                                <option value="3">Clase III</option>
                                <option value="4">Clase IV</option>
                                <option value="5">Clase V</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 5: NOVEDADES -->
                <div class="tab-pane fade" id="novedades">
                    <div class="row g-3 mt-2">
                        <div class="col-md-12 mb-2">
                            <div class="section-header text-primary">Gestión de Novedades y Traslados</div>
                        </div>

                        <div class="col-md-3">
                            <label>Fecha de registro</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label>Tipo novedad</label>
                            <select class="form-select" id="tipo_novedad" onchange="gestionarNovedades()">
                                <option selected disabled>Seleccione una opción...</option>
                                <option value="traslado_eps">Traslado de EPS</option>
                                <option value="traslado_pension">Traslado de Pensión</option>
                                <option value="traslado_cesantias">Traslado de Cesantías</option>
                                <option value="cambio_cargos">Cambio de cargos</option>
                                <option value="permisos">Permisos (abrir solicitud de permisos)</option>
                                <option value="licencia_doc">Licencias de maternidad, paternidad y luto (cargar documento)</option>
                                <option value="licencia_norem">Licencias no remunerada</option>
                                <option value="vacaciones">Vacaciones</option>
                                <option value="incapacidad">Incapacidad</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 d-none" id="cont_traslado_eps">
                            <div class="row g-3 p-3 mt-2 border rounded" style="background-color: #e2eef7;">
                                <div class="col-md-6">
                                    <label>EPS Actual</label>
                                    <select class="form-select text-muted"><option>EPS Actual (Cargada BD)</option></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-primary"><i class="fa-solid fa-arrow-right-arrow-left"></i> Nueva EPS</label>
                                    <select class="form-select">
                                        <option selected disabled>Seleccione nueva EPS...</option>
                                        <option value="nueva_eps">Nueva EPS</option>
                                        <option value="sanitas">Sanitas</option>
                                        <option value="sura">EPS Sura</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="cont_traslado_pension">
                            <div class="row g-3 p-3 mt-2 border rounded" style="background-color: #e2eef7;">
                                <div class="col-md-6">
                                    <label>Fondo de Pensión Actual</label>
                                    <select class="form-select text-muted"><option>Fondo Actual (Cargado BD)</option></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-primary"><i class="fa-solid fa-arrow-right-arrow-left"></i> Nuevo Fondo de Pensión</label>
                                    <select class="form-select">
                                        <option selected disabled>Seleccione nuevo fondo...</option>
                                        <option value="porvenir">Porvenir</option>
                                        <option value="proteccion">Protección</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="cont_traslado_cesantias">
                            <div class="row g-3 p-3 mt-2 border rounded" style="background-color: #e2eef7;">
                                <div class="col-md-6">
                                    <label>Fondo de Cesantías Actual</label>
                                    <select class="form-select text-muted"><option>Fondo Actual (Cargado BD)</option></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-primary"><i class="fa-solid fa-arrow-right-arrow-left"></i> Nuevo Fondo de Cesantías</label>
                                    <select class="form-select">
                                        <option selected disabled>Seleccione nuevo fondo...</option>
                                        <option value="fna">Fondo Nacional del Ahorro</option>
                                        <option value="porvenir">Porvenir</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="cont_cambio_cargo">
                            <div class="row g-3 p-3 mt-2 border rounded" style="background-color: #e2eef7;">
                                <div class="col-md-4">
                                    <label>Cargo Actual</label>
                                    <input type="text" class="form-control text-muted" value="Cargo Actual (Cargado BD)" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-primary"><i class="fa-solid fa-briefcase"></i> Nuevo Cargo</label>
                                    <input type="text" class="form-control" placeholder="Escriba el nuevo cargo">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-danger">Fecha inicio nuevo cargo</label>
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 d-none" id="cont_fechas_novedad">
                            <div class="row g-3 p-3 mt-2 border rounded bg-white shadow-sm">
                                <div class="col-md-12 doc-group-title m-0 pb-2">Rango de la Novedad</div>
                                <div class="col-md-4">
                                    <label class="text-danger">Fecha Inicial</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="text-danger">Fecha Fin</label>
                                    <input type="date" class="form-control">
                                </div>

                                <div class="col-md-12 d-none mt-3" id="cont_cie10">
                                    <label class="text-primary"><i class="fa-solid fa-stethoscope"></i> Diagnóstico (CIE-10)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Ej: J00 - Rinofaringitis aguda (Resfriado común)">
                                        <button class="btn btn-outline-primary" type="button"><i class="fa-solid fa-magnifying-glass"></i> Buscar BD</button>
                                    </div>
                                    <small class="text-muted">Busque por código CIE-10 o descripción del diagnóstico médico.</small>
                                </div>
                                
                                <div class="col-md-8 d-none mt-3" id="cont_archivo_novedad">
                                    <label><i class="fa-solid fa-paperclip"></i> Soporte / Documento</label>
                                    <input type="file" class="form-control" accept=".pdf,image/*">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <label>Descripción / Observaciones Adicionales</label>
                            <textarea class="form-control" rows="3" placeholder="Detalle la novedad o justificación..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 6: PROCESO DISCIPLINARIO -->
                <div class="tab-pane fade" id="disciplinario">
                    <div class="row g-3 mt-2">
                        <div class="col-md-12 mb-2">
                            <div class="section-header" style="color: var(--admin-blue); border: 1px solid var(--admin-blue); background-color: #fff;">Gestión de Procesos Disciplinarios</div>
                            <p class="text-muted small mb-0"><i class="fa-solid fa-circle-info me-1"></i> Esta sección no actualiza estados, únicamente guarda un registro histórico de llamados de atención y procedimientos de descargos del empleado.</p>
                        </div>

                        <!-- SECCIÓN: Llamados de Atención -->
                        <div class="col-md-12 doc-group-title">Llamados de Atención</div>
                        <div class="col-md-12 bg-light p-3 border rounded mb-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label>Fecha de Llamado</label>
                                    <input type="date" class="form-control" id="fecha_llamado">
                                </div>
                                <div class="col-md-6">
                                    <label>Documento Firmado (Soporte)</label>
                                    <input type="file" class="form-control" id="doc_llamado" accept=".pdf,image/*">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100" onclick="agregarLlamado()">
                                        <i class="fa-solid fa-plus"></i> Agregar Histórico
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover bg-white table-sm" id="tabla_llamados">
                                    <thead class="table-light text-secondary">
                                        <tr>
                                            <th style="width: 20%;" class="fw-bold">Fecha</th>
                                            <th style="width: 60%;" class="fw-bold">Documento Cargado</th>
                                            <th style="width: 20%;" class="text-center fw-bold">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="fila_vacia_llamados">
                                            <td colspan="3" class="text-center text-muted py-2">No hay llamados de atención registrados en el histórico.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SECCIÓN: Procedimientos de Descargos -->
                        <div class="col-md-12 doc-group-title">Procedimientos de Descargos</div>
                        <div class="col-md-12 bg-light p-3 border rounded mb-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label>Fecha de Proceso</label>
                                    <input type="date" class="form-control" id="fecha_descargo">
                                </div>
                                <div class="col-md-3">
                                    <label>Tipo de Proceso</label>
                                    <select class="form-select" id="tipo_descargo">
                                        <option value="" disabled selected>Seleccione fase...</option>
                                        <option value="Citación">Citación</option>
                                        <option value="Descargo">Descargo</option>
                                        <option value="Respuesta">Respuesta</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Documento Firmado (Soporte)</label>
                                    <input type="file" class="form-control" id="doc_descargo" accept=".pdf,image/*">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary w-100" onclick="agregarDescargo()">
                                        <i class="fa-solid fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover bg-white table-sm" id="tabla_descargos">
                                    <thead class="table-light text-secondary">
                                        <tr>
                                            <th style="width: 20%;" class="fw-bold">Fecha</th>
                                            <th style="width: 20%;" class="fw-bold">Fase del Proceso</th>
                                            <th style="width: 40%;" class="fw-bold">Documento Cargado</th>
                                            <th style="width: 20%;" class="text-center fw-bold">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="fila_vacia_descargos">
                                            <td colspan="4" class="text-center text-muted py-2">No hay procedimientos de descargos registrados.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- CONTENEDOR OCULTO PARA INPUTS DISCIPLINARIOS -->
                        <div id="cont_inputs_disciplinarios" class="d-none"></div>

                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-5">
                <a href="../../../pages-empresa/modulos/gestionhumana/Vinculacion.php" class="btn btn-cancelar rounded-pill">Cancelar</a>
                <button type="submit" class="btn btn-aceptar rounded-pill">Aceptar e Ingresar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Generador / Editor de Documentos -->
<div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--admin-blue);">
                <h5 class="modal-title" id="modalDocumentoLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="about:blank" id="iframeDocumento" style="width: 100%; height: 80vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Lista de Chequeo FOR-GH-04 -->
<div class="modal fade" id="modalChecklist" tabindex="-1" aria-labelledby="modalChecklistLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--admin-blue);">
                <h5 class="modal-title" id="modalChecklistLabel"><i class="fa-solid fa-list-check me-2"></i> FOR-GH-04: Lista de Chequeo Colaborador</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formChecklist">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-checklist align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">ÍTEM A VERIFICAR</th>
                                    <th class="text-center" style="width: 10%;">SI</th>
                                    <th class="text-center" style="width: 10%;">NO</th>
                                    <th class="text-center" style="width: 10%;">N.A</th>
                                    <th style="width: 30%;">OBSERVACIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-secondary fw-bold"><td colspan="5">A. AZ CARPETA TRABAJADOR</td></tr>
                                <?php
                                    $items_a = [
                                        "Hoja de Vida con Foto", "Fotocopia de C.C. Ampliada al 150%", "Contrato firmado por ambas partes",
                                        "Documentos beneficiarios (Registros, TI, Cedulas)", "Certificaciones Laborales, cartas de recomendación",
                                        "Certificaciones de estudios (Diploma, cursos, capacitaciones)", "Ficha Tecnica del Aspirante",
                                        "Declaracion Estado de Salud", "Autorizacion de Pruebas Varias", "Autorizacion de Tratamiento de Datos Biometricos",
                                        "Autorizacion Tratamiento de Datos Personales", "Autorizacion Explotacion Imagen", "Clausula de Confidencialidad",
                                        "Certificado Contraloria", "Certificado Procuraduria", "Certificado Inhabilidades Sexuales",
                                        "Certificado Policia Nacional", "Acta de Socializacion y entrega del Reglamento Interno de Trabajo"
                                    ];
                                    foreach ($items_a as $i => $item) {
                                        echo "<tr>
                                                <td>{$item}</td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_a_{$i}' value='SI'></td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_a_{$i}' value='NO'></td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_a_{$i}' value='NA'></td>
                                                <td><input type='text' class='form-control form-control-sm' name='obs_a_{$i}'></td>
                                              </tr>";
                                    }
                                ?>

                                <tr class="table-secondary fw-bold"><td colspan="5">B. AFILIACIONES TRABAJADOR</td></tr>
                                <?php
                                    $items_b = ["Afiliación a EPS", "Afiliación a ARL", "Afiliación a AFP", "Afiliación a Caja de Compensación"];
                                    foreach ($items_b as $i => $item) {
                                        echo "<tr>
                                                <td>{$item}</td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_b_{$i}' value='SI'></td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_b_{$i}' value='NO'></td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_b_{$i}' value='NA'></td>
                                                <td><input type='text' class='form-control form-control-sm' name='obs_b_{$i}'></td>
                                              </tr>";
                                    }
                                ?>

                                <tr class="table-secondary fw-bold"><td colspan="5">C. DOCUMENTOS DEL PUESTO DE TRABAJO</td></tr>
                                <?php
                                    $items_c = ["Formato Induccion puesto de Trabajo", "Formato perfil del Cargo"];
                                    foreach ($items_c as $i => $item) {
                                        echo "<tr>
                                                <td>{$item}</td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_c_{$i}' value='SI'></td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_c_{$i}' value='NO'></td>
                                                <td class='text-center'><input class='form-check-input' type='radio' name='chk_c_{$i}' value='NA'></td>
                                                <td><input type='text' class='form-control form-control-sm' name='obs_c_{$i}'></td>
                                              </tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Guardar Checklist</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function calcularEdad(fechaNacimiento) {
        if (!fechaNacimiento) return;
        const hoy = new Date();
        const cumple = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - cumple.getFullYear();
        const m = hoy.getMonth() - cumple.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < cumple.getDate())) {
            edad--;
        }
        document.getElementById('edad_empleado').value = edad + (edad === 1 ? " año" : " años");
    }

    function toggleAlergias() {
        const siChecked = document.getElementById('alergiaSi').checked;
        const inputCual = document.getElementById('cualAlergia');
        inputCual.disabled = !siChecked;
        if (!siChecked) {
            inputCual.value = ""; 
        }
    }

    // Toggle para Condiciones de Salud (Sí/No) habilitar campos de texto
    function toggleInputsSalud(tipo) {
        if(tipo === 'incapacidad') {
            const siChecked = document.getElementById('incap_ano_si').checked;
            document.getElementById('causa_incapacidad').disabled = !siChecked;
            if(!siChecked) document.getElementById('causa_incapacidad').value = '';
        } else if(tipo === 'at') {
            const siChecked = document.getElementById('incap_at_si').checked;
            document.getElementById('tipo_incap_at').disabled = !siChecked;
            if(!siChecked) document.getElementById('tipo_incap_at').value = '';
        } else if(tipo === 'sst') {
            const siChecked = document.getElementById('cap_sst_si').checked;
            document.getElementById('aspectos_sst').disabled = !siChecked;
            if(!siChecked) document.getElementById('aspectos_sst').value = '';
        }
    }

    function toggleHijos() {
        const selectVive = document.getElementById('con_quien_vive').value;
        const inputHijos = document.getElementById('cuantos_hijos');
        const contDetallesHijos = document.getElementById('cont_detalles_hijos');
        const tbodyHijos = document.getElementById('tbody_detalles_hijos');
        const contDocDependientes = document.getElementById('cont_doc_dependientes');

        const chkConyuge = document.getElementById('chk_conyuge');
        const chkHijos = document.getElementById('chk_hijos');
        const chkPadres = document.getElementById('chk_padres');

        chkConyuge.checked = false;
        chkHijos.checked = false;
        
        if (selectVive === 'Esposa/Esposo') {
            chkConyuge.checked = true;
            contDocDependientes.classList.remove('d-none');
        } else if (selectVive === 'Esposa e hijos') {
            chkConyuge.checked = true;
            chkHijos.checked = true;
            contDocDependientes.classList.remove('d-none');
        } else if (selectVive === 'Hijos') {
            chkHijos.checked = true;
            contDocDependientes.classList.remove('d-none');
        } else if (selectVive === 'Padres') {
            chkPadres.checked = true;
            contDocDependientes.classList.add('d-none');
        } else {
            contDocDependientes.classList.add('d-none');
        }

        toggleDocsBeneficiarios(); 

        if (selectVive === 'Esposa e hijos' || selectVive === 'Hijos') {
            inputHijos.disabled = false;
            contDetallesHijos.classList.remove('d-none');
        } else {
            inputHijos.disabled = true;
            inputHijos.value = "";
            contDetallesHijos.classList.add('d-none');
            tbodyHijos.innerHTML = ""; 
        }
    }

    function generarFilasHijos() {
        const numHijos = parseInt(document.getElementById('cuantos_hijos').value) || 0;
        const tbody = document.getElementById('tbody_detalles_hijos');
        tbody.innerHTML = ""; 

        if (numHijos > 0 && numHijos <= 15) { 
            for (let i = 1; i <= numHijos; i++) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center align-middle fw-bold">${i}</td>
                    <td><input type="number" class="form-control form-control-sm" name="edad_hijo_${i}" min="0" placeholder="Años"></td>
                    <td>
                        <select class="form-select form-select-sm" name="genero_hijo_${i}">
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="F">Femenino (F)</option>
                            <option value="M">Masculino (M)</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="discapacidad_hijo_${i}">
                            <option value="No" selected>No</option>
                            <option value="Si">Sí</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm" name="desc_discapacidad_hijo_${i}" placeholder="¿Cuál? (Opcional)"></td>
                `;
                tbody.appendChild(tr);
            }
        }
    }

    function toggleDocsBeneficiarios() {
        document.getElementById('docs_req_conyuge').classList.toggle('d-none', !document.getElementById('chk_conyuge').checked);
        document.getElementById('docs_req_hijos').classList.toggle('d-none', !document.getElementById('chk_hijos').checked);
        document.getElementById('docs_req_padres').classList.toggle('d-none', !document.getElementById('chk_padres').checked);
        document.getElementById('docs_req_hermanos').classList.toggle('d-none', !document.getElementById('chk_hermanos').checked);
    }

    function toggleFrecuencia(tipo) {
        if (tipo === 'fuma') {
            const siChecked = document.getElementById('fumaSi').checked;
            const inputFrecuencia = document.getElementById('frecuenciaFuma');
            inputFrecuencia.disabled = !siChecked;
            if (!siChecked) inputFrecuencia.value = "";
        } else if (tipo === 'bebe') {
            const siChecked = document.getElementById('bebeSi').checked;
            const inputFrecuencia = document.getElementById('frecuenciaBebe');
            inputFrecuencia.disabled = !siChecked;
            if (!siChecked) inputFrecuencia.value = "";
        }
    }

    function verificarDocumentosRetiro() {
        const cartaTerminacion = document.getElementById('doc_carta_terminacion').files.length > 0;
        const respuestaRenuncia = document.getElementById('doc_respuesta_renuncia').files.length > 0;
        const estadoEmpleado = document.getElementById('estado_empleado');
        const fechaTerminacion = document.getElementById('fecha_terminacion');
        const msgTerminacion = document.getElementById('msg_terminacion');

        if (cartaTerminacion || respuestaRenuncia) {
            estadoEmpleado.value = "Retirado";
            fechaTerminacion.disabled = false;
            msgTerminacion.style.display = 'block';
            
            var triggerEl = document.querySelector('#vinculacionTabs button[data-bs-target="#datos"]');
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            fechaTerminacion.focus();
        } else {
            estadoEmpleado.value = "Activo";
            fechaTerminacion.disabled = true;
            fechaTerminacion.value = "";
            msgTerminacion.style.display = 'none';
        }
    }

    function validarDocumentos() {
        const hv = document.getElementById('doc_hv').files.length > 0;
        const cedula = document.getElementById('doc_cedula').files.length > 0;
        const contrato = document.getElementById('doc_contrato').files.length > 0;
        const eps = document.getElementById('doc_eps').files.length > 0;
        const arl = document.getElementById('doc_arl').files.length > 0;
        const afp = document.getElementById('doc_afp').files.length > 0;
        const ficha = document.getElementById('doc_ficha_aspirante').files.length > 0;
        const salud = document.getElementById('doc_estado_salud').files.length > 0;
        const gdpr = document.getElementById('doc_gdpr').files.length > 0;

        let faltantes = [];

        if (!hv) faltantes.push("Hoja de Vida");
        if (!cedula) faltantes.push("Fotocopia Cédula");
        if (!contrato) faltantes.push("Contrato Firmado");
        if (!eps) faltantes.push("Soporte Afiliación EPS");
        if (!arl) faltantes.push("Soporte Afiliación ARL");
        if (!afp) faltantes.push("Soporte Afiliación Pensión (AFP)");
        if (!ficha) faltantes.push("Ficha Técnica del Aspirante");
        if (!salud) faltantes.push("Declaración Estado de Salud");
        if (!gdpr) faltantes.push("Tratamiento de Datos (GDPR)");

        if (faltantes.length > 0) {
            alert("⚠️ Faltan los siguientes documentos básicos por cargar:\n\n- " + faltantes.join("\n- ") + "\n\nPor favor adjúntelos o verifique la Lista de Chequeo.");
        } else {
            alert("✅ ¡Excelente! Todos los documentos básicos requeridos han sido cargados.");
        }
    }

    function abrirModalDocumento(tipoDoc) {
        const modalLabel = document.getElementById('modalDocumentoLabel');
        const iframe = document.getElementById('iframeDocumento');
        
        if (tipoDoc === 'contrato') {
            modalLabel.innerHTML = '<i class="fa-solid fa-file-signature me-2"></i> Editor de Contrato Digital';
            iframe.src = "editor_contrato.php";
        } else if (tipoDoc === 'certificado') {
            modalLabel.innerHTML = '<i class="fa-solid fa-certificate me-2"></i> Generador de Certificación Laboral';
            iframe.src = "certificaionlaboral.php";
        } else if (tipoDoc === 'apertura_cuenta') {
            modalLabel.innerHTML = '<i class="fa-solid fa-envelope-open-text me-2"></i> Carta Apertura de Cuenta Nómina';
            iframe.src = "carta_apertura.php";
        } else if (tipoDoc === 'retiro_cesantias') {
            modalLabel.innerHTML = '<i class="fa-solid fa-file-invoice-dollar me-2"></i> Carta Retiro de Cesantías';
            iframe.src = "carta_cesantias.php";
        }
        
        var modalElement = document.getElementById('modalDocumento');
        var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        myModal.show();
    }

    document.getElementById('modalDocumento').addEventListener('hidden.bs.modal', function () {
        document.getElementById('iframeDocumento').src = 'about:blank';
    });

    function gestionarNovedades() {
        const seleccion = document.getElementById('tipo_novedad').value;
        
        document.getElementById('cont_traslado_eps').classList.add('d-none');
        document.getElementById('cont_traslado_pension').classList.add('d-none');
        document.getElementById('cont_traslado_cesantias').classList.add('d-none');
        document.getElementById('cont_cambio_cargo').classList.add('d-none');
        document.getElementById('cont_fechas_novedad').classList.add('d-none');
        document.getElementById('cont_cie10').classList.add('d-none');
        document.getElementById('cont_archivo_novedad').classList.add('d-none');

        if (seleccion === 'traslado_eps') {
            document.getElementById('cont_traslado_eps').classList.remove('d-none');
        } else if (seleccion === 'traslado_pension') {
            document.getElementById('cont_traslado_pension').classList.remove('d-none');
        } else if (seleccion === 'traslado_cesantias') {
            document.getElementById('cont_traslado_cesantias').classList.remove('d-none');
        } else if (seleccion === 'cambio_cargos') {
            document.getElementById('cont_cambio_cargo').classList.remove('d-none');
        } else if (['permisos', 'licencia_doc', 'licencia_norem', 'vacaciones', 'incapacidad'].includes(seleccion)) {
            document.getElementById('cont_fechas_novedad').classList.remove('d-none');
            
            if (seleccion === 'incapacidad') {
                document.getElementById('cont_cie10').classList.remove('d-none');
                document.getElementById('cont_archivo_novedad').classList.remove('d-none');
            }
            if (seleccion === 'licencia_doc') {
                document.getElementById('cont_archivo_novedad').classList.remove('d-none');
            }
        }
    }

    function agregarLlamado() {
        const fecha = document.getElementById('fecha_llamado').value;
        const inputFile = document.getElementById('doc_llamado');
        const tbody = document.querySelector('#tabla_llamados tbody');
        const filaVacia = document.getElementById('fila_vacia_llamados');
        const contenedorOculto = document.getElementById('cont_inputs_disciplinarios');

        if (!fecha || inputFile.files.length === 0) {
            alert("Por favor, ingrese la fecha y adjunte el documento firmado.");
            return;
        }

        const archivo = inputFile.files[0];
        const rowId = 'llamado_' + Date.now();

        const inputReal = inputFile.cloneNode(true);
        inputReal.name = "doc_llamados_atencion[]"; 
        inputReal.id = 'hidden_' + rowId;
        contenedorOculto.appendChild(inputReal);
        
        const inputFecha = document.createElement('input');
        inputFecha.type = 'hidden';
        inputFecha.name = "fecha_llamados_atencion[]";
        inputFecha.value = fecha;
        inputFecha.id = 'hidden_fecha_' + rowId;
        contenedorOculto.appendChild(inputFecha);

        if (filaVacia) filaVacia.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = rowId;
        tr.innerHTML = `
            <td class="align-middle">${fecha}</td>
            <td class="align-middle"><i class="fa-solid fa-file-pdf text-danger me-2"></i> ${archivo.name}</td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFilaDisciplinario('${rowId}', 'llamado')">
                    <i class="fa-solid fa-trash"></i> Quitar
                </button>
            </td>
        `;
        tbody.appendChild(tr);

        document.getElementById('fecha_llamado').value = "";
        inputFile.value = "";
    }

    function agregarDescargo() {
        const fecha = document.getElementById('fecha_descargo').value;
        const tipo = document.getElementById('tipo_descargo').value;
        const inputFile = document.getElementById('doc_descargo');
        const tbody = document.querySelector('#tabla_descargos tbody');
        const filaVacia = document.getElementById('fila_vacia_descargos');
        const contenedorOculto = document.getElementById('cont_inputs_disciplinarios');

        if (!fecha || !tipo || inputFile.files.length === 0) {
            alert("Por favor, ingrese la fecha, seleccione el tipo de proceso y adjunte el documento firmado.");
            return;
        }

        const archivo = inputFile.files[0];
        const rowId = 'descargo_' + Date.now();

        const inputReal = inputFile.cloneNode(true);
        inputReal.name = "doc_descargos[]"; 
        inputReal.id = 'hidden_' + rowId;
        contenedorOculto.appendChild(inputReal);

        const inputFecha = document.createElement('input');
        inputFecha.type = 'hidden';
        inputFecha.name = "fecha_descargos[]";
        inputFecha.value = fecha;
        inputFecha.id = 'hidden_fecha_' + rowId;
        contenedorOculto.appendChild(inputFecha);

        const inputTipo = document.createElement('input');
        inputTipo.type = 'hidden';
        inputTipo.name = "tipo_descargos[]";
        inputTipo.value = tipo;
        inputTipo.id = 'hidden_tipo_' + rowId;
        contenedorOculto.appendChild(inputTipo);

        if (filaVacia) filaVacia.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = rowId;
        tr.innerHTML = `
            <td class="align-middle">${fecha}</td>
            <td class="align-middle fw-bold">${tipo}</td>
            <td class="align-middle"><i class="fa-solid fa-file-pdf text-danger me-2"></i> ${archivo.name}</td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFilaDisciplinario('${rowId}', 'descargo')">
                    <i class="fa-solid fa-trash"></i> Quitar
                </button>
            </td>
        `;
        tbody.appendChild(tr);

        document.getElementById('fecha_descargo').value = "";
        document.getElementById('tipo_descargo').value = "";
        inputFile.value = "";
    }

    function eliminarFilaDisciplinario(rowId, tipoProceso) {
        document.getElementById(rowId).remove();
        
        if(document.getElementById('hidden_' + rowId)) document.getElementById('hidden_' + rowId).remove();
        if(document.getElementById('hidden_fecha_' + rowId)) document.getElementById('hidden_fecha_' + rowId).remove();
        if(document.getElementById('hidden_tipo_' + rowId)) document.getElementById('hidden_tipo_' + rowId).remove();

        if (tipoProceso === 'llamado') {
            const tbody = document.querySelector('#tabla_llamados tbody');
            if (tbody.querySelectorAll('tr').length <= 1) { 
                document.getElementById('fila_vacia_llamados').style.display = '';
            }
        } else {
            const tbody = document.querySelector('#tabla_descargos tbody');
            if (tbody.querySelectorAll('tr').length <= 1) {
                document.getElementById('fila_vacia_descargos').style.display = '';
            }
        }
    }
</script>

</body>
</html>