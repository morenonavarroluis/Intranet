<?php
require "cone.php";

 session_start();

//  Verificacion para el inicio de sesion.
if ($_POST) {
    $USER = $_POST['USER'];
    $password = $_POST['PASSWORD'];
// Otro código de tu sistema aquí...
    $sql = "SELECT IDDATOS, PASSWORD, USER, EMAIL, IDROLS, telefono, ASSIGNED_AREA,  NAME, SURNAME, CEDULA FROM user_datos WHERE USER='$USER' ";
    $resultado = mysqli_query($conn,$sql);

    $num = $resultado->num_rows;

    // if (strlen($_POST['PASSWORD']) <= 8){
    //     echo "La contraseña tiene que ser al menos de 8 caracteres";
    
    //      }else{
    if ($num > 0) {
        $row = $resultado->fetch_assoc();
        $password_bd = $row['PASSWORD'];
        $pass_c = sha1($password);
 

        if ($password_bd == $pass_c) {
            $_SESSION['IDDATOS'] = $row['IDDATOS'];
            $_SESSION['USER'] = $row['USER'];
            $_SESSION['IDROLS'] = $row['IDROLS'];
			$_SESSION['NAME'] = $row['NAME'];
            $_SESSION['SURNAME'] = $row['SURNAME'];
            $_SESSION['CEDULA'] = $row['CEDULA'];
           
            $_SESSION['PASSWORD'] = $row['PASSWORD'];
            $_SESSION['telefono'] = $row['telefono']; 
            $_SESSION['EMAIL'] = $row['EMAIL'];
            $_SESSION['ASSIGNED_AREA'] = $row['ASSIGNED_AREA'];
        //    Comprobación de inicion de sesión y roles
            if (isset($_SESSION['IDROLS'])) {
                switch ($_SESSION['IDROLS']) {
                   case 1:
                        header("Location: admin/index.php");
                        break;
                    case 2:
                        header("Location: usuario/index.php");
                        break;
                    case 3:
                        header("Location: tecnico/soporte.php");
                        break;
                    case 4:
                        header("Location: rrhh/usuarios.php");
                        break;
                   
                    default:
                           echo  "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script language='JavaScript'>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Rol no existente',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
              }).then(() => {
                location.assign('index.php');
              });
    });
        </script>";
    
                        break;
                }

            }
        } else {
            // Envia un mensaje de alerta por si el password no coincide
             echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'La contraseña no coincide',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        timer: 1500
                      }).then(() => {

                        location.assign('index.php');

                      });
            });
                </script>";
        }
    } else {
        // Envia un mensaje de alerta por si el usuario no coincide no coincide
        echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'El usuario no coincide',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        timer: 1500
                      }).then(() => {

                        location.assign('index.php');

                      });
            });
                </script>";
    }
}

// }


?>