<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$ROL_SESION = $_SESSION['IDROLS'];
if ($ROL_SESION != 1 && $ROL_SESION != 4) {
    header("Location: index.php");
    exit;
}

$accion   = $_POST['accion'] ?? '';
$id       = intval($_POST['id'] ?? 0);
$name     = trim($_POST['name'] ?? '');
$surname  = trim($_POST['surname'] ?? '');
$cedula   = trim($_POST['cedula'] ?? '');
$user     = trim($_POST['user'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$area     = trim($_POST['area'] ?? '');
$rol      = intval($_POST['rol'] ?? 2);

// ==================== VALIDACIONES ====================
if (empty($name) || empty($surname) || empty($cedula) || empty($user) || empty($area)) {
    echo "<script>alert('Todos los campos obligatorios deben estar completos'); location='usuarios.php';</script>";
    exit;
}

if (strlen($user) > 8) {
    echo "<script>alert('El usuario no puede tener más de 8 caracteres'); location='usuarios.php';</script>";
    exit;
}

// ==================== CREAR ====================
if ($accion === 'crear') {
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (strlen($password) < 6) {
        echo "<script>alert('La contraseña debe tener al menos 6 caracteres'); location='usuarios.php';</script>";
        exit;
    }
    if ($password !== $password2) {
        echo "<script>alert('Las contraseñas no coinciden'); location='usuarios.php';</script>";
        exit;
    }

    // Verificar duplicados
    $stmt = $conn->prepare("SELECT IDDATOS FROM user_datos WHERE USER=? OR CEDULA=?");
    $stmt->bind_param("ss", $user, $cedula);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "<script>alert('El usuario o la cédula ya están registrados'); location='usuarios.php';</script>";
        exit;
    }

    // Hash de contraseña
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Foto por defecto
    $fotoRuta = 'images/Canaima.png';

    // Procesar foto si se subió
    if (!empty($_FILES['foto']['name'])) {
        $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['foto']['type'], $permitidos) && $_FILES['foto']['size'] <= 2*1024*1024) {
            $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombreF  = "user_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $carpeta  = __DIR__ . "/images/usuarios/";

            if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $nombreF)) {
                $fotoRuta = 'images/usuarios/' . $nombreF;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO user_datos 
        (USER, PASSWORD, NAME, SURNAME, CEDULA, EMAIL, telefono, ASSIGNED_AREA, IDROLS, foto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssis", $user, $hash, $name, $surname, $cedula, $email, $telefono, $area, $rol, $fotoRuta);

    if ($stmt->execute()) {
        header("Location: usuarios.php?created=1");
    } else {
        header("Location: usuarios.php?error=1");
    }
    exit;
}

// ==================== EDITAR ====================
if ($accion === 'editar') {
    if ($id <= 0) {
        header("Location: usuarios.php?error=1");
        exit;
    }

    // Si se subió foto nueva
    if (!empty($_FILES['foto']['name'])) {
        $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['foto']['type'], $permitidos) && $_FILES['foto']['size'] <= 2*1024*1024) {
            $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombreF  = "user_" . $id . "_" . time() . "." . $ext;
            $carpeta  = __DIR__ . "/images/usuarios/";

            if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $nombreF)) {
                $fotoRuta = 'images/usuarios/' . $nombreF;
                $stmt = $conn->prepare("UPDATE user_datos SET foto=? WHERE IDDATOS=?");
                $stmt->bind_param("si", $fotoRuta, $id);
                $stmt->execute();
            }
        }
    }

    $stmt = $conn->prepare("UPDATE user_datos 
        SET NAME=?, SURNAME=?, EMAIL=?, telefono=?, ASSIGNED_AREA=?, IDROLS=? 
        WHERE IDDATOS=?");
    $stmt->bind_param("sssssii", $name, $surname, $email, $telefono, $area, $rol, $id);

    if ($stmt->execute()) {
        header("Location: usuarios.php?updated=1");
    } else {
        header("Location: usuarios.php?error=1");
    }
    exit;
}

header("Location: usuarios.php");
exit;
?>