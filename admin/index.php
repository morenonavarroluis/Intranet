<?php

 
include('../cone.php');

session_start();
if (!isset($_SESSION['IDDATOS'])) {
  header("Location: index.php");
}
$ID = $_SESSION['IDDATOS']; 
$USER = $_SESSION['USER'];
$NAME = $_SESSION['NAME'];
$primeraN = substr($NAME, 0, 1);
$APE = $_SESSION['SURNAME'];
$primeraA = substr($APE, 0, 8);
$ROL = $_SESSION['IDROLS'];
$CEDULA = $_SESSION['CEDULA'];
$area  = $_SESSION['ASSIGNED_AREA'];
 
 

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Industria Canaima</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
<link rel="stylesheet" href="inicio.css">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<link rel="shortcut icon" href="images/Canaima.png" type="image/x-icon">
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="images/Canaima.png" alt="">
        <span class="d-none d-lg-block">Industria Canaima</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">


        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <?php 
                                     
                                     if (isset($_SESSION['IDDATOS'])) {
                  
                                     }else{
                                         ?>
                                         <script type="text/javascript">
                                             window.location = "./";
                                         </script>
                                         <?php 
                                     }
                                  
                                     $consulta = mysqli_query($conn, "SELECT CEDULA , foto FROM user_datos WHERE CEDULA = '$CEDULA';");
                                     $valores = mysqli_fetch_array($consulta);
                                     $foto = $valores['foto'];
                                      ?>
            <img src="<?php echo $foto; ?>" alt="" width="35" height="35" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo  $primeraN .".   ". $primeraA  ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            
              <h6><?php echo  $NAME ?></h6>
              <span><?php echo  $area ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>Mi Perfil</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>ayuda</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="index.php">
          <i class="bi bi-grid"></i>
          <span>inicio</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>solicitud</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="soporte_tecnico.php">
              <i class="bi bi-circle"></i><span>Soporte Tecnico</span>
            </a>
          </li>
          <?php
          $sql1 = "SELECT IDDATOS FROM user_datos WHERE IDDATOS = '$ID' ";
             $resulta = mysqli_query($conn,$sql1);
          
             $mostre = mysqli_fetch_assoc($resulta) 
             ?>
          <li>
       <a  href="Constancia_de_trabajo.php?edi=<?php echo $mostre['IDDATOS'];?>"</a> 
              <i class="bi bi-circle"></i><span>Contancia de trabajo</span>
            </a>
          </li>
          <li>
            <a href="recibo.php">
              <i class="bi bi-circle"></i><span>Recibo de pago</span>
            </a>
          </li>
         
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Descargas</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a  href="./pdf/vacaciones.xls" >
              <i class="bi bi-circle"></i><span>planilla de vacaciones</span>
            </a>
          </li>
          <li>
            <a href="./pdf/permiso.docx">
              <i class="bi bi-circle"></i><span>planilla de permisos</span>
            </a>
          </li>
          <!-- <li>
            <a href="forms-editors.html">
              <i class="bi bi-circle"></i><span>Form Editors</span>
            </a>
          </li>
          <li>
            <a href="forms-validation.html">
              <i class="bi bi-circle"></i><span>Form Validation</span>
            </a>
          </li> -->
        </ul>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Biblioteca Digital</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="./pdf/103_Manual_Canaimit.pdf">
              <i class="bi bi-circle"></i><span> Manual de canaima</span>
            </a>
          </li>
          <li>
            <a href="./pdf/para_el_usuario.docx">
              <i class="bi bi-circle"></i><span>Cuidado de la canaima</span>
            </a>
          </li>
          <li>
            <a href="./pdf/guia linux.pdf">
              <i class="bi bi-circle"></i><span>Guia de linux </span>
            </a>
          </li>
          <li>
            <a href="./pdf/Linux Desde Cero.pdf">
              <i class="bi bi-circle"></i><span>Linux desde cero</span>
            </a>
          </li>
          <li>
            <a href="./pdf/fundamentos de linux.pdf">
              <i class="bi bi-circle"></i><span>fundamentos de linux</span>
            </a>
          </li>
          <li>
            <a href="./pdf/administrador de linux.pdf">
              <i class="bi bi-circle"></i><span>Administrador de linux</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart"></i><span>web</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="https://bdvenlinea.banvenez.com">
              <i class="bi bi-circle"></i><span>Banco de Venezuela</span>
            </a>
          </li>
          <li>
            <a href="https://bicentenarioenlinea.bicentenariobu.com.ve/?p=1">
              <i class="bi bi-circle"></i><span>Banco Bicentenario</span>
            </a>
          </li>
          <li>
            <a href="https://btenlinea.bt.com.ve/lg">
              <i class="bi bi-circle"></i><span>Banco del Tesoro</span>
            </a>
          </li>
          <li>
            <a href="https://www.eluniversal.com">
              <i class="bi bi-circle"></i><span>Periodico Universal</span>
            </a>
          </li>
          <li>
            <a href="https://dolartoday.com/calculadora/">
              <i class="bi bi-circle"></i><span>calcular dolar</span>
            </a>
          </li>
          <li>
            <a href="https://www.banesconline.com/mantis/Website/Login.aspx">
              <i class="bi bi-circle"></i><span>Banco Banesco</span>
            </a>
          </li>
          <li>
            <a href="https://www.provincial.com/personas.html">
              <i class="bi bi-circle"></i><span>Banco Provincial</span>
            </a>
          </li>
          <li>
            <a href="https://persona.patria.org.ve/login/clave/">
              <i class="bi bi-circle"></i><span>Patria</span>
            </a>
          </li>
        </ul>
      </li><!-- End Charts Nav -->
      
   
      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="perfil.php">
          <i class="bi bi-person"></i>
          <span>Perfil</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="caso_soporte.php">
          <i class="bi bi-question-circle"></i>
          <span>Casos de Soporte</span>
        </a>
      </li><!-- End F.A.Q Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="usuarios.php">
          <i class="bi bi-envelope"></i>
          <span>Gestion de usuarios</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="cargar_noticia.php">
          <i class="bi bi-envelope"></i>
          <span>Cargar Noticia</span>
        </a>
      </li><!-- End Contact Page Nav -->

    
      <li class="nav-item">
        <a class="nav-link collapsed" href="../logout.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Cerrar</span>
        </a>
      </li><!-- End Login Page Nav -->

     
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Noticias</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Inicio</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
<!-----------------------------SESION DE NOTICIAS   ------------------------------------------------------------->
            <!-- Sales Card -->
 
            <?php
    
    $query = "SELECT cod_imagen,imagen, nombre FROM imagenes  ";
    $resultado = mysqli_query($conn,$query);
 
    ?>
 
 
                             
     <?php foreach($resultado as $row) { ?>
      <div class="card" style="width: 18rem; margin:0 30px 20px 0">
         <div class="contenedor-imagenes">
            
             <div class="imagen">
             <img  src="../imagenes/<?php echo $row['imagen']; ?>" style="width: 18rem;" >
             <a  data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $row['cod_imagen'];?>">   
             <div class="overlay">
                <h2 ><?php echo $row['nombre']; ?></h2>
                    
                </div>
                </a>
             </div>
             <?php  include "modal_noticias.php";  ?>  
          </div>
        </div>
          <?php } ?>
          
 <!-----------------------------FIN DE NOTICIAS   ------------------------------------------------------------->
            <!-- Revenue Card -->
          
          

          

           

          

          </div>
        </div><!-- End Left side columns -->
       
        <!-- Right side columns -->
        <div class="col-lg-4">

          <!-- Recent Activity -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">cumpleañeros del día<span>| hoy </span></h5>

              <div class="activity">

                <div class="activity-item d-flex">
                  <div class="activite-label">32 min</div>
                  <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                  <div class="activity-content">
                    ------------------------
                    <!-- Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a> beatae -->
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">56 min</div>
                  <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                  <div class="activity-content">
                  ---------------------------
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">2 hrs</div>
                  <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                  <div class="activity-content">
                 ---------------------------
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">1 day</div>
                  <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                  <div class="activity-content">
                    -------------
                    <!-- Tempore autem saepe <a href="#" class="fw-bold text-dark">occaecati voluptatem</a> tempore -->
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">2 days</div>
                  <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                  <div class="activity-content">
                    -----------------
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">4 weeks</div>
                  <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                  <div class="activity-content">
                  ----------------
                  </div>
                </div><!-- End activity item-->

              </div>

            </div>
          </div><!-- End Recent Activity -->

       

        

      </div>
   
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Desarrollado por Industria Canaima</span></strong>. Reservados todos los derechos
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
    <a href="https://bootstrapmade.com/"> RIF: G-20010288-8</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>