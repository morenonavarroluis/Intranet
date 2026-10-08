<?php
/**
 * Helper de permisos - Industria Canaima
 * Incluir DESPUÉS de cone.php y session_start()
 */

if (!function_exists('tienePermiso')) {

    function tienePermiso($permiso, $conn) {
        if (!isset($_SESSION['IDROLS'])) return false;

        // Admin siempre tiene todo
        if ($_SESSION['IDROLS'] == 1) return true;

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

        // Permisos extra del usuario (grant/deny)
        if (isset($_SESSION['IDDATOS'])) {
            $iduser = intval($_SESSION['IDDATOS']);

            $stmt = $conn->prepare("
                SELECT upe.TIPO
                FROM usuario_permiso_extra upe
                INNER JOIN permisos p ON upe.IDPERMISO = p.IDPERMISO
                WHERE upe.IDDATOS = ? AND p.SLUG = ?
            ");
            $stmt->bind_param("is", $iduser, $permiso);
            $stmt->execute();
            $extra = $stmt->get_result()->fetch_assoc();

            if ($extra) {
                $resultado = ($extra['TIPO'] === 'grant');
            }
        }

        $cache[$key] = $resultado;
        return $resultado;
    }

    function exigirPermiso($permiso, $conn, $redirect = 'index.php') {
        if (!tienePermiso($permiso, $conn)) {
            header("Location: $redirect");
            exit;
        }
    }

    function obtenerPermisos($conn) {
        if (!isset($_SESSION['IDROLS'])) return [];
        if ($_SESSION['IDROLS'] == 1) {
            $res = mysqli_query($conn, "SELECT SLUG FROM permisos WHERE ACTIVO = 1");
            $out = [];
            while ($r = mysqli_fetch_assoc($res)) $out[] = $r['SLUG'];
            return $out;
        }

        $idrol = intval($_SESSION['IDROLS']);
        $stmt = $conn->prepare("
            SELECT DISTINCT p.SLUG
            FROM rol_permiso rp
            INNER JOIN permisos p ON rp.IDPERMISO = p.IDPERMISO
            WHERE rp.IDROL = ? AND p.ACTIVO = 1
        ");
        $stmt->bind_param("i", $idrol);
        $stmt->execute();
        $res = $stmt->get_result();

        $out = [];
        while ($r = $res->fetch_assoc()) $out[] = $r['SLUG'];
        return $out;
    }
}
?>