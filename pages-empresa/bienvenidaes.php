<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php");
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    body {
      display: flex;
      align-items: center;     /* centro vertical */
      justify-content: center; /* centro horizontal */
      
    }

    .bienvenida {
      text-align: center;
    }

    .bienvenida h5 {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 8px 0; /* elimina margen superior */
    }

    .bienvenida p {
      font-size: 15px;
      color: #6c757d;
      margin: 0;
    }
  </style>
</head>

<body>
  <div class="bienvenida">
    <h5>Bienvenido al menú cliente</h5>
    <p>Seleccione una opción del menú de la izquierda para comenzar.</p>
  </div>
</body>
</html>


