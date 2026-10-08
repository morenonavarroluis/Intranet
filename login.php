<?php
require "cone.php";
session_start();

// Si ya está logueado, redirigir al admin
if (isset($_SESSION['IDDATOS'])) {
    header("Location: admin/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $USER     = trim($_POST['USER'] ?? '');
    $password = $_POST['PASSWORD'] ?? '';

    if (empty($USER) || empty($password)) {
        mostrarAlerta('error', 'Completa todos los campos');
        exit;
    }

    // Consulta preparada
    $stmt = $conn->prepare("SELECT IDDATOS, PASSWORD, USER, EMAIL, IDROLS, telefono, 
                                   ASSIGNED_AREA, NAME, SURNAME, CEDULA 
                            FROM user_datos WHERE USER = ? LIMIT 1");
    $stmt->bind_param("s", $USER);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        mostrarAlerta('error', 'El usuario no existe');
        exit;
    }

    $row = $resultado->fetch_assoc();
    $password_bd = $row['PASSWORD'];

    // ✅ Acepta SHA1 y BCRYPT
    $password_valida = false;

    if (strlen($password_bd) === 40) {
        if (hash_equals($password_bd, sha1($password))) {
            $password_valida = true;
            // Migrar a BCRYPT
            $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
            $upd = $conn->prepare("UPDATE user_datos SET PASSWORD = ? WHERE IDDATOS = ?");
            $upd->bind_param("si", $nuevoHash, $row['IDDATOS']);
            $upd->execute();
        }
    } else {
        if (password_verify($password, $password_bd)) {
            $password_valida = true;
        }
    }

    if (!$password_valida) {
        mostrarAlerta('error', 'La contraseña no coincide');
        exit;
    }

    // Crear sesión
    $_SESSION['IDDATOS']       = $row['IDDATOS'];
    $_SESSION['USER']          = $row['USER'];
    $_SESSION['IDROLS']        = $row['IDROLS'];
    $_SESSION['NAME']          = $row['NAME'];
    $_SESSION['SURNAME']       = $row['SURNAME'];
    $_SESSION['CEDULA']        = $row['CEDULA'];
    $_SESSION['PASSWORD']      = $row['PASSWORD'];
    $_SESSION['telefono']      = $row['telefono'];
    $_SESSION['EMAIL']         = $row['EMAIL'];
    $_SESSION['ASSIGNED_AREA'] = $row['ASSIGNED_AREA'];

    session_regenerate_id(true);

    // ✅ TODOS van al mismo lugar: admin/index.php
    header("Location: admin/index.php");
    exit;
}

function mostrarAlerta($icono, $titulo) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '$icono',
            title: '$titulo',
            timer: 1500,
            showConfirmButton: false
        }).then(() => { location.assign('index.php'); });
    });
    </script>";
}
?>