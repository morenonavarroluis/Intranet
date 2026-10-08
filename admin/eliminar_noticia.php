<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$ROL = $_SESSION['IDROLS'];
if ($ROL != 1 && $ROL != 4) {
    header("Location: index.php");
    exit;
}

$cod = intval($_POST['cod_imagen'] ?? 0);

if ($cod <= 0) {
    header("Location: cargar_noticia.php?error=1");
    exit;
}

// Obtener nombre de la imagen para eliminarla del disco
$stmt = $conn->prepare("SELECT imagen FROM imagenes WHERE cod_imagen = ?");
$stmt->bind_param("i", $cod);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    $ruta = __DIR__ . "/../imagenes/" . $row['imagen'];
    if (!empty($row['imagen']) && file_exists($ruta)) {
        @unlink($ruta);
    }
}

// Eliminar de la BD
$stmt = $conn->prepare("DELETE FROM imagenes WHERE cod_imagen = ?");
$stmt->bind_param("i", $cod);
$stmt->execute();

header("Location: cargar_noticia.php?deleted=1");
exit;
?>