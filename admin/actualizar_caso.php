<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$ROL = $_SESSION['IDROLS'];
if ($ROL != 1 && $ROL != 3) {
    header("Location: index.php");
    exit;
}

date_default_timezone_set('America/Caracas');

$id       = intval($_POST['id_report']);
$status   = intval($_POST['status']);
$nivel    = intval($_POST['nivel']);
$solucion = trim($_POST['solucion'] ?? '');

$fechaSolucion = ($status == 4 || $status == 5) ? date('Y-m-d') : null;

$stmt = $conn->prepare("UPDATE report 
    SET STATUS = ?, ID_LEVEL = ?, SOLUTION = ?, FECHA_SOLUTION = COALESCE(?, FECHA_SOLUTION) 
    WHERE ID_REPORT = ?");
$stmt->bind_param("iissi", $status, $nivel, $solucion, $fechaSolucion, $id);

if ($stmt->execute()) {
    header("Location: caso_soporte.php?ok=1");
} else {
    header("Location: caso_soporte.php?error=1");
}
exit;
?>