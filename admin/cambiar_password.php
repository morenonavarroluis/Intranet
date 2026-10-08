<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$ID      = $_SESSION['IDDATOS'];
$actual  = $_POST['password_actual']    ?? '';
$nueva   = $_POST['password_nueva']     ?? '';
$confirm = $_POST['password_confirmar'] ?? '';

if ($nueva !== $confirm) {
    echo "<script>alert('Las contraseñas no coinciden'); location='perfil.php';</script>";
    exit;
}

if (strlen($nueva) < 8 || !preg_match('/[A-Z]/', $nueva) || !preg_match('/[0-9]/', $nueva)) {
    echo "<script>alert('La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número'); location='perfil.php';</script>";
    exit;
}

// Verificar contraseña actual
$stmt = $conn->prepare("SELECT PASSWORD FROM user_datos WHERE IDDATOS=?");
$stmt->bind_param("i", $ID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$hashBD = $row['PASSWORD'];
$valida = false;

if (strlen($hashBD) === 40) {
    $valida = hash_equals($hashBD, sha1($actual));
} else {
    $valida = password_verify($actual, $hashBD);
}

if (!$valida) {
    echo "<script>alert('La contraseña actual es incorrecta'); location='perfil.php';</script>";
    exit;
}

// Actualizar con BCRYPT
$nuevoHash = password_hash($nueva, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE user_datos SET PASSWORD=? WHERE IDDATOS=?");
$stmt->bind_param("si", $nuevoHash, $ID);
$stmt->execute();

header("Location: perfil.php?pass=1");
exit;
?>