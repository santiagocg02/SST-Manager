<?php
session_start();
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
  <title>1.1.4 | Anexos COPASST / Vigía</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --blue:#1f5fa8;
      --line:#111;
      --soft:#eef3fb;
      --gray:#f6f7fb;
    }
    body{ background:#e9edf3; }
    .wrap{ max-width: 1100px; margin: 16px auto; padding: 0 10px; }

    .toolbar{
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      margin-bottom: 12px;
    }

    /* Hoja A4 */
    .sheet{
      background:#fff;
      border:2px solid var(--blue);
      box-shadow: 0 10px 20px rgba(0,0,0,.08);
      padding: 14px;
      margin-bottom: 16px;
    }
    .page-break{ page-break-after: always; }

    /* Cabecera formato */
    table.format{
      width:100%;
      border-collapse:collapse;
      table-layout:fixed;
      font-size:12px;
    }
    .format td, .format th{
      border:1px solid var(--line);
      padding:6px 8px;
      vertical-align:middle;
    }
    .title{
      font-weight:900;
      text-align:center;
      font-size:13px;
    }
    .subtitle{
      font-weight:900;
      text-align:center;
      font-size:12px;
    }
    .code-box{
      text-align:center;
      font-weight:900;
      font-size:12px;
    }
    .badge-mod{
      display:inline-block;
      border:1px solid #c9d7ff;
      padding:3px 8px;
      border-radius:999px;
      background:#f4f7ff;
      font-weight:800;
      font-size:11px;
    }
    .logo-box{
      border:2px dashed rgba(0,0,0,.35);
      height:68px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:900;
      color:rgba(0,0,0,.35);
      text-align:center;
      font-size: 11px;
    }

    /* Secciones */
 .sec-h{
  background:#d9e1ea;
  border:1px solid #b8c2cc;
  color:#10233c;
  font-weight:900;
  text-transform:uppercase;
  padding:10px 14px;
  font-size:15px;
  letter-spacing:.2px;
  margin-bottom:10px;
}

    /* Inputs estilo formato */
   .in{
  width:100%;
  height:34px;
  border:1px solid #b7b7b7;
  border-radius:8px;
  background:#f8f8f8;
  padding:6px 10px;
  outline:none;
  box-sizing:border-box;
}
    .in.center{ text-align:center; }
    .in.right{ text-align:right; }
    .in.inline{
      display:inline-block;
      width:auto;
      min-width: 140px;
      vertical-align:middle;
    }

    /* Tablas “formulario” */
    table.formtbl{
  width:100%;
  border-collapse:collapse;
  table-layout:fixed;
  font-size:12px;
  margin-top:0;
  background:#fff;
}
.formtbl th,
.formtbl td{
  border:1px solid #2a2a2a;
  padding:7px 8px;
  vertical-align:middle;
}
.formtbl th{
  background:#f1f3f6;
  text-align:center;
  font-weight:900;
  color:#14253d;
  font-size:12px;
}

.formtbl{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
  background:#fff;
}
    .small{ font-size:11px; }
    .center{ text-align:center; }
    .right{ text-align:right; }
    .bold{ font-weight:900; }

    .sign-line{
      margin-top: 14px;
      display:flex;
      gap:20px;
      justify-content:space-between;
      align-items:flex-end;
    }
    .sig{
      width:48%;
    }
    .sig .line{
      border-top:1px solid #111;
      margin-top: 30px;
    }
    .sig .lbl{
      text-align:center;
      font-size:12px;
      margin-top:6px;
      font-weight:700;
    }
    .small.muted{
  font-size:11px;
  margin-top:8px;
}

    @media print{
      body{ background:#fff; }
      .toolbar{ display:none !important; }
      .sheet{ box-shadow:none; margin-bottom:0; border:2px solid #000; }
      .page-break{ page-break-after: always; }
    }
  </style>
</head>

<body>
<div class="wrap">

  <div class="toolbar">
    <div class="d-flex gap-2 flex-wrap">
      <a href="../planear.php" class="btn btn-outline-secondary btn-sm">← Volver</a>
      <button class="btn btn-primary btn-sm" onclick="window.print()">Imprimir / PDF</button>
    </div>
    <div class="small text-muted fw-semibold">Soporte: Anexos COPASST / Vigía</div>
  </div>

  <!-- ===================== ANEXO 1 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">SISTEMA DE GESTIÓN EN SEGURIDAD Y SALUD EN EL TRABAJO</td>
        <td class="code-box">0</td>
        <td class="code-box">ANEXO 1</td>
      </tr>
      <tr>
        <td class="subtitle">CONFORMACIÓN COPASST</td>
        <td class="code-box"><input class="in center" placeholder="AAAA-MM-DD"></td>
        <td class="center"><span class="badge-mod">PLANEAR</span></td>
      </tr>
    </table>

    <div class="sec-h">CONVOCATORIA ELECCIONES DEL COMITÉ PARITARIO DE SEGURIDAD Y SALUD EN EL TRABAJO</div>

    <div class="p">
      Ciudad: <input class="in inline" placeholder="Ciudad">
      <span class="ms-3">Fecha: <input class="in inline center" placeholder="AAAA-MM-DD"></span>
    </div>

    <div class="p">
      El Gerente / Representante Legal de
      <input class="in inline" style="min-width:260px" placeholder="Nombre de la empresa">
      , convoca a todos los trabajadores para elegir sus representantes al Comité Paritario de Seguridad y Salud en el Trabajo, principales y suplentes.
    </div>

    <div class="p">
      La elección se llevará a cabo en las instalaciones de
      <input class="in inline" style="min-width:260px" placeholder="Lugar / empresa">
      , el día <input class="in inline center" placeholder="AAAA-MM-DD">
      a las <input class="in inline center" placeholder="HH:MM">.
    </div>

    <div class="p bold">Contamos con su participación activa.</div>

    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Nombre Gerente / Representante Legal</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma</div>
      </div>
    </div>
  </div>

  <!-- ===================== ANEXO 2 + 3 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">INSCRIPCIÓN CANDIDATOS AL COMITÉ PARITARIO DE SST</td>
        <td class="code-box">ANEXO 2</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">CONFORMACIÓN COPASST</td>
        <td class="code-box">Responsable</td>
        <td class="code-box"><input class="in center" placeholder="Nombre"></td>
      </tr>
    </table>

    
    <div class="sec-h">INSCRIPCIÓN CANDIDATOS AL COMITÉ PARITARIO DE SEGURIDAD Y SALUD EN EL TRABAJO</div>
    <div class="p">
      Periodo: <input class="in inline center" placeholder="AAAA-MM-DD">
    </div>
    <table class="formtbl">
      <colgroup>
      <col style="width:70px">
      <col>
      <col style="width:160px">
      <col style="width:160px">
      <col style="width:160px">
    </colgroup>
    <thead>
      <tr>
        <th>No.</th>
        <th>Nombres y Apellidos</th>
        <th>Cédula</th>
        <th>Cargo</th>
        <th>Área</th>
      </tr>
    </thead>
    <tbody>
      <?php for($r=1;$r<=10;$r++): ?>
        <tr>
          <td class="center"><?= $r ?></td>
          <td><input class="in" placeholder=""></td>
          <td><input class="in center" placeholder=""></td>
          <td><input class="in" placeholder=""></td>
          <td><input class="in" placeholder=""></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>
    
    <div class="p">
      Fecha de cierre: <input class="in inline center" placeholder="AAAA-MM-DD">
      Responsable: <input class="in inline center">
    </div>
    
      <<div class="mt-3">
  <div class="sec-h">Anexo 3 / Tarjetón de votación</div>

  <table class="formtbl">
    <colgroup>
      <col>
      <col style="width:220px">
      <col style="width:220px">
    </colgroup>
    <thead>
      <tr>
        <th>NOMBRE</th>
        <th>CARGO</th>
        <th>ÁREA</th>
      </tr>
    </thead>
    <tbody>
      <?php for($r=1;$r<=10;$r++): ?>
      <tr>
        <td><input class="in" placeholder=""></td>
        <td><input class="in" placeholder=""></td>
        <td><input class="in" placeholder=""></td>
      </tr>
      <?php endfor; ?>
    </tbody>
  </table>

  <div class="small muted mt-2">
    (Este tarjetón es para diligenciamiento / referencia del proceso de votación)
  </div>
</div>

  <!-- ===================== ANEXO 4 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">ACTA APERTURA ELECCIONES CANDIDATOS COPASST</td>
        <td class="code-box">ANEXO 4</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">PERIODO: <input class="in inline center" placeholder="AAAA-MM"> – <input class="in inline center" placeholder="AAAA-MM"></td>
        <td class="code-box">Empresa</td>
        <td class="code-box"><input class="in center" placeholder="Nombre empresa"></td>
      </tr>
    </table>

    <div class="p">
      Siendo las <input class="in inline center" placeholder="HH:MM"> del día <input class="in inline center" placeholder="DD-MM-AAAA">,
      se da apertura al proceso de votación para la elección de los representantes al Comité Paritario de Seguridad y Salud en el Trabajo.
    </div>

    <div class="p">
      La votación se llevará a cabo por medio virtual y el cierre será el
      <input class="in inline center" placeholder="DD-MM-AAAA"> a las <input class="in inline center" placeholder="HH:MM">.
    </div>

    <div class="sec-h">Veedores del proceso</div>
    <div class="p">1) <input class="in" placeholder="Nombre completo, área y cédula"></div>
    <div class="p">2) <input class="in" placeholder="Nombre completo, área y cédula"></div>

    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma jurado votación — Nombre</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma jurado votación — Nombre</div>
      </div>
    </div>
  </div>

  <!-- ===================== ANEXO 5 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">REGISTRO ASISTENCIA VOTACIÓN – COPASST</td>
        <td class="code-box">ANEXO 5</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">PERIODO: <input class="in inline center" placeholder="AAAA-MM-DD"></td>
        <td class="code-box">Fecha</td>
        <td class="code-box"><input class="in center" placeholder="AAAA-MM-DD"></td>
      </tr>
    </table>

    <div class="p">Empresa: <input class="in inline" style="min-width:300px" placeholder=""></div>

    <table class="formtbl mt-2">
      <colgroup>
        <col style="width:70px">
        <col>
        <col style="width:160px">
        <col style="width:180px">
        <col style="width:220px">
      </colgroup>
      <thead>
        <tr>
          <th>No.</th>
          <th>Nombre y Apellidos</th>
          <th>Cédula</th>
          <th>Área</th>
          <th>Firma</th>
        </tr>
      </thead>
      <tbody>
        <?php for($r=1;$r<=12;$r++): ?>
        <tr>
          <td class="center"><?= $r ?></td>
          <td><input class="in" placeholder=""></td>
          <td><input class="in center" placeholder=""></td>
          <td><input class="in" placeholder=""></td>
          <td><input class="in" placeholder=""></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>
  </div>

  <!-- ===================== ANEXO 6 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">ACTA CIERRE VOTACIONES AL COMITÉ DE SST</td>
        <td class="code-box">ANEXO 6</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">PERIODO: <input class="in inline center" placeholder="AAAA-MM-DD"></td>
        <td class="code-box">Fecha</td>
        <td class="code-box"><input class="in center" placeholder="AAAA-MM-DD"></td>
      </tr>
    </table>

    <div class="p">
      Siendo las <input class="in inline center" placeholder="HH:MM">, del día <input class="in inline center" placeholder="DD-MM-AAAA">,
      la empresa <input class="in inline" style="min-width:260px" placeholder="Nombre empresa">,
      da por terminado el proceso de votación para elección de representantes del Trabajador al Comité Paritario de SST.
    </div>

    <div class="p bold">Como veedores de votación, se encargaron:</div>
    <div class="p">1) <input class="in" placeholder="Nombre completo"></div>
    <div class="p">2) <input class="in" placeholder="Nombre completo"></div>

    <div class="sec-h">Resultados obtenidos</div>
    <table class="formtbl">
      <colgroup>
        <col>
        <col style="width:160px">
      </colgroup>
      <thead>
        <tr>
          <th>Candidato</th>
          <th class="center">Votos</th>
        </tr>
      </thead>
      <tbody>
        <?php for($r=1;$r<=10;$r++): ?>
        <tr>
          <td><input class="in" placeholder=""></td>
          <td><input class="in center" placeholder="0"></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <div class="sec-h">Elegidos</div>
    <table class="formtbl">
      <colgroup>
        <col>
        <col style="width:220px">
      </colgroup>
      <thead>
        <tr>
          <th>Nombre</th>
          <th class="center">Rol</th>
        </tr>
      </thead>
      <tbody>
        <tr><td><input class="in" placeholder=""></td><td class="center"><input class="in center" placeholder="Principal"></td></tr>
        <tr><td><input class="in" placeholder=""></td><td class="center"><input class="in center" placeholder="Suplente"></td></tr>
        <tr><td><input class="in" placeholder=""></td><td class="center"><input class="in center" placeholder="Principal"></td></tr>
        <tr><td><input class="in" placeholder=""></td><td class="center"><input class="in center" placeholder="Suplente"></td></tr>
      </tbody>
    </table>

    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Nombre jurado</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Nombre jurado</div>
      </div>
    </div>
    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma</div>
      </div>
    </div>
  </div>

  <!-- ===================== ANEXO 7 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">ACTA DE CONSTITUCIÓN DEL COPASST</td>
        <td class="code-box">ANEXO 7</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">Constitución del Comité Paritario de SST</td>
        <td class="code-box">Fecha</td>
        <td class="code-box"><input class="in center" placeholder="DD-MM-AAAA"></td>
      </tr>
    </table>

    <div class="p">
      El día <input class="in inline center" placeholder="DD-MM-AAAA">, se eligió el Comité Paritario de Seguridad y Salud en el Trabajo (COPASST) o Vigía SST de la empresa
      <input class="in inline" style="min-width:260px" placeholder="Nombre empresa">,
      dando cumplimiento a la Resolución 2013 de 1986 y al Decreto 1295 de 1994.
    </div>

    <div class="p">La modalidad utilizada para su elección fue: <span class="bold">Votación</span></div>

    <div class="sec-h">Representantes por parte del trabajador</div>
    <table class="formtbl">
      <colgroup>
        <col>
        <col style="width:220px">
        <col style="width:220px">
      </colgroup>
      <thead>
        <tr>
          <th>Nombre</th>
          <th class="center">Rol</th>
          <th class="center">Área / Cargo</th>
        </tr>
      </thead>
      <tbody>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Principal"></td><td><input class="in"></td></tr>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Suplente"></td><td><input class="in"></td></tr>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Principal"></td><td><input class="in"></td></tr>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Suplente"></td><td><input class="in"></td></tr>
      </tbody>
    </table>

    <div class="sec-h">Representantes por parte de la empresa</div>
    <div class="p">
      El representante legal / gerente designa a las siguientes personas:
    </div>
    <table class="formtbl">
      <colgroup>
        <col>
        <col style="width:220px">
        <col style="width:220px">
      </colgroup>
      <thead>
        <tr>
          <th>Nombre</th>
          <th class="center">Rol</th>
          <th class="center">Área / Cargo</th>
        </tr>
      </thead>
      <tbody>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Principal"></td><td><input class="in"></td></tr>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Suplente"></td><td><input class="in"></td></tr>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Principal"></td><td><input class="in"></td></tr>
        <tr><td><input class="in"></td><td><input class="in center" placeholder="Suplente"></td><td><input class="in"></td></tr>
      </tbody>
    </table>

    <div class="p mt-3">
      Como presidente del Comité quedó designado:
      <input class="in inline" style="min-width:260px" placeholder="Nombre">
    </div>

    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Representante Legal</div>
      </div>
    </div>
  </div>

  <!-- ===================== ANEXO 8 ===================== -->
  <div class="sheet page-break">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">ACTA DE REUNIÓN MENSUAL COPASST</td>
        <td class="code-box">ANEXO 8</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">FORMATO ACTA DE REUNIÓN MENSUAL COMITÉ PARITARIO SST</td>
        <td class="code-box">Fecha</td>
        <td class="code-box"><input class="in center" placeholder="DD-MM-AAAA"></td>
      </tr>
    </table>

    <div class="p bold">Nombre empresa: <input class="in inline" style="min-width:340px" placeholder="NOMBRE EMPRESA"></div>

    <div class="row g-2">
      <div class="col-md-6">
        <div class="p">Hora de inicio: <input class="in inline center" placeholder="HH:MM"></div>
      </div>
      <div class="col-md-6">
        <div class="p">Hora de finalización: <input class="in inline center" placeholder="HH:MM"></div>
      </div>
    </div>

    <div class="p">Acta de reunión No.: <input class="in inline center" placeholder="_____"></div>

    <div class="sec-h">Asistentes e invitados</div>
    <table class="formtbl">
      <colgroup>
        <col>
        <col style="width:260px">
      </colgroup>
      <thead>
        <tr>
          <th>Nombre</th>
          <th class="center">Firma</th>
        </tr>
      </thead>
      <tbody>
        <?php for($r=1;$r<=8;$r++): ?>
        <tr>
          <td><input class="in" placeholder=""></td>
          <td><input class="in" placeholder=""></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <div class="sec-h">Orden del día</div>
    <?php for($r=1;$r<=4;$r++): ?>
      <div class="p"><?= $r ?>) <input class="in" placeholder=""></div>
    <?php endfor; ?>

    <div class="sec-h">Desarrollo de la reunión</div>
    <textarea class="in" rows="6" placeholder="Escriba aquí el desarrollo..."></textarea>

    <div class="sec-h">Definición de tareas</div>
    <div class="small muted mb-2">Convenciones del estado: (A. Abierta – C. Cerrada – P. Proceso)</div>
    <table class="formtbl">
      <colgroup>
        <col>
        <col style="width:140px">
        <col style="width:140px">
        <col style="width:130px">
      </colgroup>
      <thead>
        <tr>
          <th>Tarea / Actividad</th>
          <th class="center">Responsable</th>
          <th class="center">Fecha</th>
          <th class="center">Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php for($r=1;$r<=8;$r++): ?>
        <tr>
          <td><input class="in" placeholder=""></td>
          <td><input class="in center" placeholder=""></td>
          <td><input class="in center" placeholder="DD-MM-AAAA"></td>
          <td><input class="in center" placeholder="A/C/P"></td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <div class="p mt-3">
      Fecha próxima reunión: <input class="in inline center" placeholder="DD-MM-AAAA">
      <span class="ms-3">Hora próxima reunión: <input class="in inline center" placeholder="HH:MM"></span>
    </div>

    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma del Presidente</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Firma del Secretario</div>
      </div>
    </div>
  </div>

  <!-- ===================== ACTA VIGÍA + EVALUACIÓN ===================== -->
  <div class="sheet">
    <table class="format mb-2">
      <colgroup>
        <col style="width:230px">
        <col>
        <col style="width:150px">
        <col style="width:160px">
      </colgroup>
      <tr>
        <td rowspan="2"><div class="logo-box">TU LOGO<br>AQUÍ</div></td>
        <td class="title">ACTA DE NOMBRAMIENTO DEL VIGÍA EN SST</td>
        <td class="code-box">FORMATO</td>
        <td class="code-box"><span class="badge-mod">PLANEAR</span></td>
      </tr>
      <tr>
        <td class="subtitle">Designación Vigía SST</td>
        <td class="code-box">Fecha</td>
        <td class="code-box"><input class="in center" placeholder="DD/MM/AA"></td>
      </tr>
    </table>

    <div class="p">
      Nombre de la empresa: <input class="in inline" style="min-width:320px" placeholder="">
      <span class="ms-3">NIT: <input class="in inline center" placeholder=""></span>
    </div>

    <div class="p">
      En cumplimiento a lo establecido en la Resolución 2013 de 1986, el representante legal
      <input class="in inline" style="min-width:260px" placeholder="Nombre del Gerente / RL">
      de la empresa <input class="in inline" style="min-width:260px" placeholder="Nombre empresa">
      designa como Vigía en SST al Señor(a):
    </div>

    <div class="p">
      Nombre: <input class="in inline" style="min-width:320px" placeholder="Nombre del vigía">
      <span class="ms-3">Cargo: <input class="in inline" style="min-width:240px" placeholder=""></span>
    </div>

    <div class="p bold">Y como suplente al Señor(a):</div>
    <div class="p">
      Nombre: <input class="in inline" style="min-width:320px" placeholder="Nombre del suplente">
      <span class="ms-3">Cargo: <input class="in inline" style="min-width:240px" placeholder=""></span>
    </div>

    <div class="p">
      Por un periodo de dos (2) años comprendido entre:
      Inicio <input class="in inline center" placeholder="DD-MM-AAAA">
      y Finalización <input class="in inline center" placeholder="DD-MM-AAAA">,
      de conformidad con el Decreto 1295 de 1994.
    </div>

    <div class="p">
      La presente se firma el <input class="in inline center" placeholder="DD/MM/AA">.
    </div>

    <div class="sign-line">
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Representante Legal</div>
      </div>
      <div class="sig">
        <div class="line"></div>
        <div class="lbl">Vigía SST</div>
      </div>
    </div>

    <div class="sec-h">Evaluación de conocimientos (post-capacitación)</div>

    <div class="p bold">¿Qué es el Comité Paritario de Seguridad y Salud en el Trabajo?</div>
    <textarea class="in" rows="3" placeholder=""></textarea>

    <div class="p bold mt-3">Escriba FALSO (F) ó VERDADERO (V) según corresponda:</div>

    <table class="formtbl mt-2">
      <colgroup>
        <col>
        <col style="width:120px">
      </colgroup>
      <thead>
        <tr>
          <th>Enunciado</th>
          <th class="center">F / V</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>El COPASST debe estar conformado por igual número de representantes del empleador y de los trabajadores.</td>
          <td class="center"><input class="in center" placeholder="F/V"></td>
        </tr>
        <tr>
          <td>El COPASST debe velar por el desarrollo del SGSST de la empresa.</td>
          <td class="center"><input class="in center" placeholder="F/V"></td>
        </tr>
        <tr>
          <td>El presidente lo elige el comité en votación y el secretario lo elige el Representante Legal.</td>
          <td class="center"><input class="in center" placeholder="F/V"></td>
        </tr>
        <tr>
          <td>El COPASST debe solicitar periódicamente informes sobre accidentalidad y enfermedades laborales.</td>
          <td class="center"><input class="in center" placeholder="F/V"></td>
        </tr>
      </tbody>
    </table>

    <div class="p bold mt-3">Relacione con una línea:</div>
    <div class="p muted">(Espacio para actividad / relación)</div>
    <div style="height:70px; border:1px dashed rgba(0,0,0,.25); border-radius:8px;"></div>

    <div class="p mt-3">
      Firma del trabajador: <input class="in inline" style="min-width:340px" placeholder="">
    </div>

    <div class="text-center mt-3 small text-muted">…Gracias por su participación…</div>
  </div>

</div>
</body>
</html>