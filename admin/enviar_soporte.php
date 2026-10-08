<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

date_default_timezone_set('America/Caracas');

$ID           = $_SESSION['IDDATOS'];
$TITLE        = trim($_POST['TITLE'] ?? '');
$area         = intval($_POST['area'] ?? 0);
$name_surname = trim($_POST['name_surname'] ?? '');
$fecha        = date('Y-m-d');

if (empty($TITLE) || $area <= 0) {
    echo "<script>alert('Todos los campos son obligatorios'); location='soporte_tecnico.php';</script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO report 
    (TITLE, name_surname, area, ID_NAME, CREATION_DATE, STATUS, ID_LEVEL) 
    VALUES (?, ?, ?, ?, ?, 3, 3)");
$stmt->bind_param("ssiis", $TITLE, $name_surname, $area, $ID, $fecha);

if ($stmt->execute()) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '¡Solicitud enviada!',
            text: 'Tu solicitud fue registrada correctamente',
            timer: 2000,
            showConfirmButton: false
        }).then(() => { location.assign('soporte_tecnico.php'); });
    });
    </script>";
} else {
    echo "<script>alert('Error al enviar la solicitud'); location='soporte_tecnico.php';</script>";
}
exit;
?>