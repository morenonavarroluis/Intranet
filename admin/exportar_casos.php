<?php
include('../cone.php');
session_start();

if (!isset($_SESSION['IDDATOS'])) {
    header("Location: ../index.php");
    exit;
}

$ROL = $_SESSION['IDROLS'];
if ($ROL != 1 && $ROL != 3) {
    header("Location: index.php");
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=casos_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

fputcsv($output, ['ID', 'Título', 'Solicitante', 'Área', 'Fecha', 'Prioridad', 'Estado', 'Solución']);

$sql = "SELECT r.ID_REPORT, r.TITLE, r.name_surname, a.nombre_area, r.CREATION_DATE,
               n.nombre_nivel, s.nombre_status, r.SOLUTION
        FROM report r
        LEFT JOIN areas a ON r.area = a.id_area
        LEFT JOIN niveles n ON r.ID_LEVEL = n.id_nivel
        LEFT JOIN status_report s ON r.STATUS = s.id_status
        ORDER BY r.CREATION_DATE DESC";

$resultado = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($resultado)) {
    fputcsv($output, [
        $row['ID_REPORT'],
        $row['TITLE'],
        $row['name_surname'],
        $row['nombre_area'],
        $row['CREATION_DATE'],
        $row['nombre_nivel'],
        $row['nombre_status'],
        $row['SOLUTION']
    ]);
}
fclose($output);
exit;
?>