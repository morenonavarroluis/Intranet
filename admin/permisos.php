<?php
function tienePermiso($permiso, $conn) {
    if (!isset($_SESSION['IDROLS'])) return false;
    if ($_SESSION['IDROLS'] == 1) return true; // Admin tiene todo

    static $cache = [];
    $idrol = intval($_SESSION['IDROLS']);
    $key = $idrol . '|' . $permiso;
    if (isset($cache[$key])) return $cache[$key];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM rol_permiso rp
        INNER JOIN permisos p ON rp.IDPERMISO = p.IDPERMISO
        WHERE rp.IDROL = ? AND p.SLUG = ? AND p.ACTIVO = 1
    ");
    $stmt->bind_param("is", $idrol, $permiso);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc()['total'] > 0;

    $cache[$key] = $resultado;
    return $resultado;
}

function exigirPermiso($permiso, $conn, $redirect = 'index.php') {
    if (!tienePermiso($permiso, $conn)) {
        header("Location: $redirect");
        exit;
    }
}