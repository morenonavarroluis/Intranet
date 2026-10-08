<?php
include('../cone.php');
include('../permisos.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// ✅ ANTES: if ($ROL_SESION != 1 && $ROL_SESION != 4)
// ✅ AHORA:
exigirPermiso('usuarios.password', $conn, 'index.php');

$id  = intval($_POST['id'] ?? 0);
$p1  = $_POST['password']  ?? '';
$p2  = $_POST['password2'] ?? '';

if ($id <= 0 || strlen($p1) < 6 || $p1 !== $p2) {
    header("Location: usuarios.php?error=1");
    exit;
}

$hash = password_hash($p1, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE user_datos SET PASSWORD=? WHERE IDDATOS=?");
$stmt->bind_param("si", $hash, $id);
$stmt->execute();

header("Location: usuarios.php?pass=1");
exit;
?>