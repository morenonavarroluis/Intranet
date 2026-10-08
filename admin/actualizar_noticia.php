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

$cod       = intval($_POST['cod_imagen']);
$nombre    = trim($_POST['nombre'] ?? '');
$comentario= trim($_POST['comentario'] ?? '');

if (empty($nombre) || empty($comentario)) {
    echo "<script>alert('Todos los campos son obligatorios'); location='cargar_noticia.php';</script>";
    exit;
}

// Obtener imagen actual
$sqlActual = "SELECT imagen FROM imagenes WHERE cod_imagen = ?";
$stmt = $conn->prepare($sqlActual);
$stmt->bind_param("i", $cod);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    header("Location: cargar_noticia.php?error=1");
    exit;
}

$nuevaImagen = $row['imagen'];

// Si se subió una nueva imagen
if (!empty($_FILES['imagen']['name'])) {
    $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($_FILES['imagen']['type'], $permitidos)) {
        echo "<script>alert('Formato no permitido'); location='cargar_noticia.php';</script>";
        exit;
    }
    if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
        echo "<script>alert('Imagen demasiado grande'); location='cargar_noticia.php';</script>";
        exit;
    }

    $ext      = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreF  = "noticia_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $carpeta  = __DIR__ . "/../imagenes/";

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreF)) {
        // Eliminar imagen anterior
        if (!empty($row['imagen']) && file_exists($carpeta . $row['imagen'])) {
            @unlink($carpeta . $row['imagen']);
        }
        $nuevaImagen = $nombreF;
    }
}

// Actualizar BD
$sql = "UPDATE imagenes SET imagen=?, nombre=?, comentario=? WHERE cod_imagen=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nuevaImagen, $nombre, $comentario, $cod);

if ($stmt->execute()) {
    header("Location: cargar_noticia.php?ok=1");
} else {
    header("Location: cargar_noticia.php?error=1");
}
exit;
?>