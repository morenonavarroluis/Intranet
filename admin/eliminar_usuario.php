<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$ROL_SESION = $_SESSION['IDROLS'];
$ID_SESION  = $_SESSION['IDDATOS'];

// Solo admin
if ($ROL_SESION != 1) {
    header("Location: index.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);

// No puede eliminarse a sí mismo
if ($id <= 0 || $id == $ID_SESION) {
    header("Location: usuarios.php?error=1");
    exit;
}

// Eliminar foto del disco si existe
$stmt = $conn->prepare("SELECT foto FROM user_datos WHERE IDDATOS=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row && !empty($row['foto']) && $row['foto'] !== 'images/Canaima.png') {
    $ruta = __DIR__ . "/" . $row['foto'];
    if (file_exists($ruta)) @unlink($ruta);
}

// Eliminar usuario
$stmt = $conn->prepare("DELETE FROM user_datos WHERE IDDATOS=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: usuarios.php?deleted=1");
exit;
?>