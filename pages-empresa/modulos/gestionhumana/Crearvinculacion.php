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

        .btn-cancelar { background-color: #ff3333; color: white; font-weight: bold; border: none; padding: 10px 30px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;}
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
            <button class="nav-link" id="dependientes-tab" data-bs-toggle="tab" data-bs-target="#dependientes">Dependientes / Documentos</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="afiliaciones-tab" data-bs-toggle="tab" data-bs-target="#afiliaciones">Afiliaciones</button>
        </li>
    </ul>

    <div class="form-container">
        <form id="formVinculacion" enctype="multipart/form-data">
            <div class="tab-content" id="myTabContent">
                
                <div class="tab-pane fade show active" id="datos">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label>Tipo Doc.</label>
                            <select class="form-select">
                                <option value="CC" selected>CC</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PPT">PPT</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Numero de Identificacion</label>
                            <input type="text" class="form-control" placeholder="Número de documento">
                        </div>
                        <div class="col-md-5">
                            <label>Nombres y Apellidos</label>
                            <input type="text" class="form-control" placeholder="Nombre completo">
                        </div>
                        <div class="col-md-2">
                            <label>Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" onchange="calcularEdad(this.value)">
                        </div>
                        <div class="col-md-2">
                            <label>Edad</label>
                            <input type="text" class="form-control" id="edad_empleado" readonly placeholder="0 años">
                        </div>

                        <div class="col-md-4">
                            <label>Dirección</label>
                            <input type="text" class="form-control" placeholder="Calle/Carrera">
                        </div>
                        <div class="col-md-2">
                            <label>Barrio</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Teléfono</label>
                            <input type="tel" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Correo electrónico</label>
                            <input type="email" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label>Fecha de ingreso</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Cargo</label>
                            <input type="text" class="form-control" placeholder="Nombre del cargo">
                        </div>
                        <div class="col-md-3">
                            <label>Salario básico</label>
                            <input type="number" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label>Tipo contrato</label>
                            <select class="form-select"><option>Término Indefinido</option></select>
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
                            <label>Fecha inicio nómina</label>
                            <input type="date" class="form-control">
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
                            <button type="button" class="btn btn-link p-0 text-decoration-none small italic" onclick="abrirContrato()">
                                <i class="fa-solid fa-file-pdf"></i> Ver y editar contrato digitalizado
                            </button>
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
                       
                    </div>
                </div>

                <div class="tab-pane fade" id="dependientes">
                    <div class="row g-3 mt-2">
                        <div class="col-md-12 mb-2">
                            <div class="section-header">Carga de Documentos del Empleado</div>
                        </div>
                        <div class="col-md-6">
                            <label>Cédula de Ciudadanía</label>
                            <input type="file" class="form-control" name="doc_cedula" accept=".pdf,image/*">
                        </div>
                        <div class="col-md-6">
                            <label>Libreta Militar</label>
                            <input type="file" class="form-control" name="doc_libreta" accept=".pdf,image/*">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Exámenes Médicos</label>
                            <input type="file" class="form-control" name="doc_examen" accept=".pdf,image/*">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Contrato Firmado</label>
                            <input type="file" class="form-control" name="doc_contrato" accept=".pdf,image/*">
                        </div>
                        <div class="col-md-12 mt-3">
                            <small class="text-muted"><i class="fa-solid fa-circle-info"></i> Formatos permitidos: PDF o Imágenes (JPG, PNG).</small>
                        </div>
                    </div>
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
                        <div class="col-md-6">
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

            </div>

            <div class="d-flex justify-content-end gap-3 mt-5">
                <a href="../../../pages-empresa/modulos/gestionhumana/Vinculacion.php" target="contentFrame" class="btn btn-cancelar rounded-pill">Cancelar</a>
                <button type="submit" class="btn btn-aceptar rounded-pill">Aceptar</button>
            </div>
        </form>
    </div>

    <div class="mt-4">
        <button class="btn btn-light border shadow-sm"><i class="fa-solid fa-arrow-left"></i></button>
        <button class="btn btn-light border shadow-sm ms-2"><i class="fa-solid fa-arrow-right"></i></button>
    </div>
</div>

<div class="modal fade" id="modalContrato" tabindex="-1" aria-labelledby="modalContratoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--admin-blue);">
                <h5 class="modal-title" id="modalContratoLabel">
                    <i class="fa-solid fa-file-signature me-2"></i>Editor de Contrato Digital
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe src="about:blank" id="iframeContrato" style="width: 100%; height: 80vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Función original de edad
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

    // Nueva función para abrir el modal del contrato
    function abrirContrato() {
        // Definimos la URL de tu editor
        const url = "editor_contrato.php";
        
        // Asignamos la URL al iframe
        document.getElementById('iframeContrato').src = url;
        
        // Inicializamos y mostramos el modal
        var myModal = new bootstrap.Modal(document.getElementById('modalContrato'));
        myModal.show();
    }

    // Limpiar el iframe al cerrar el modal para no consumir memoria innecesaria
    document.getElementById('modalContrato').addEventListener('hidden.bs.modal', function () {
        document.getElementById('iframeContrato').src = 'about:blank';
    });
</script>

</body>
</html>