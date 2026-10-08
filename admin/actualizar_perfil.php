<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$ID       = $_SESSION['IDDATOS'];
$name     = trim($_POST['name'] ?? '');
$surname  = trim($_POST['surname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if (empty($name) || empty($surname)) {
    echo "<script>alert('Nombre y apellido son obligatorios'); location='perfil.php';</script>";
    exit;
}

// Actualizar datos básicos
$stmt = $conn->prepare("UPDATE user_datos SET NAME=?, SURNAME=?, EMAIL=?, telefono=? WHERE IDDATOS=?");
$stmt->bind_param("ssssi", $name, $surname, $email, $telefono, $ID);
$stmt->execute();

// Procesar foto
if (!empty($_FILES['foto']['name'])) {
    $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['foto']['type'], $permitidos) && $_FILES['foto']['size'] <= 2*1024*1024) {
        $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreF  = "user_" . $ID . "_" . time() . "." . $ext;
        $carpeta  = __DIR__ . "/images/usuarios/";

        if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $nombreF)) {
            $rutaRel = 'images/usuarios/' . $nombreF;
            $stmt = $conn->prepare("UPDATE user_datos SET foto=? WHERE IDDATOS=?");
            $stmt->bind_param("si", $rutaRel, $ID);
            $stmt->execute();
        }
    }
}

// Actualizar sesión
$_SESSION['NAME']     = $name;
$_SESSION['SURNAME']  = $surname;
$_SESSION['EMAIL']    = $email;
$_SESSION['telefono'] = $telefono;

header("Location: perfil.php?ok=1");
exit;
?>