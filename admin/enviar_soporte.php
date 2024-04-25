<?php  


include('../cone.php');
session_start();

date_default_timezone_set('America/caracas');
     $fecha = date('Y-m-d');

$ROL = $_SESSION['IDDATOS'];
$TITLE = $_POST['TITLE'];
$area = $_POST ['area'];
$name_surname = $_POST['name_surname'];



   $sql= "INSERT INTO `report` (`ID_REPORT`, `TITLE`, `name_surname`,`area`, `ID_NAME`, `CREATION_DATE`, `DATE_FINAL`, `FECHA_SOLUTION`, `STATUS`, `ID_LEVEL`, `SOLUTION`) VALUES (NULL, '$TITLE', '$name_surname', '$area' ,'$ROL', '$fecha', NULL, NULL, '3', '3', NULL)";
      
   $result = mysqli_query($conn, $sql);

   if ($result) {
        
  
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script language='JavaScript'>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'success',
        title: 'Se Envio tu solicitud correctamente',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK',
        timer: 1500
        }).then(() => {

        location.assign('soporte_tecnico.php');

        });
  });
    </script>";
  }else {
    echo "
   <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
   <script language='JavaScript'>
   document.addEventListener('DOMContentLoaded', function() {
     Swal.fire({
       icon: 'error',
       title: 'su solicitud no se envio',
       showCancelButton: false,
       confirmButtonColor: '#3085d6',
       confirmButtonText: 'OK',
       timer: 1500
       }).then(() => {

           location.assign('soporte_tecnico.php');

      });
 });
   </script>";
 }



?>