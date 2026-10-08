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

$ID         = $_SESSION['IDDATOS'];
$nombre     = trim($_POST['nombre'] ?? '');
$comentario = trim($_POST['comentario'] ?? '');

if (empty($nombre) || empty($comentario)) {
    echo "<script>alert('Título y comentario son obligatorios'); location='cargar_noticia.php';</script>";
    exit;
}

// Procesar imagen
if (empty($_FILES['imagen']['name'])) {
    echo "<script>alert('Debes subir una imagen'); location='cargar_noticia.php';</script>";
    exit;
}

$permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($_FILES['imagen']['type'], $permitidos)) {
    echo "<script>alert('Formato de imagen no permitido'); location='cargar_noticia.php';</script>";
    exit;
}

if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
    echo "<script>alert('La imagen supera los 5MB'); location='cargar_noticia.php';</script>";
    exit;
}

$ext     = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
$nombreF = "noticia_" . time() . "_" . rand(1000, 9999) . "." . $ext;

// Ruta de la carpeta de imágenes (fuera de la carpeta admin)
$carpetaDestino = __DIR__ . "/../imagenes/";
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0755, true);
}

$rutaCompleta = $carpetaDestino . $nombreF;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
    echo "<script>alert('Error al subir la imagen'); location='cargar_noticia.php';</script>";
    exit;
}

// Guardar en BD (solo el nombre del archivo)
$stmt = $conn->prepare("INSERT INTO imagenes (imagen, nombre, comentario, IDDATOS, fecha_publicacion) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("sssi", $nombreF, $nombre, $comentario, $ID);

if ($stmt->execute()) {
    header("Location: cargar_noticia.php?ok=1");
} else {
    // Si falla, eliminar imagen subida
    @unlink($rutaCompleta);
    header("Location: cargar_noticia.php?error=1");
}
exit;
?>