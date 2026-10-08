<?php
include('../cone.php');
include('../permisos.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['IDDATOS'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

if (!tienePermiso('chat.usar', $conn)) {
    echo json_encode(['success' => false, 'error' => 'Sin permiso']);
    exit;
}

$ID     = intval($_SESSION['IDDATOS']);
$accion = $_GET['accion'] ?? '';

// ==================== ENVIAR ====================
if ($accion === 'enviar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $destino = intval($_POST['destino'] ?? 0);
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($destino <= 0 || empty($mensaje)) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }

    // Buscar o crear conversación
    $u1 = min($ID, $destino);
    $u2 = max($ID, $destino);

    $stmt = $conn->prepare("SELECT id_conversacion FROM chat_conversaciones WHERE id_usuario_1 = ? AND id_usuario_2 = ?");
    $stmt->bind_param("ii", $u1, $u2);
    $stmt->execute();
    $conv = $stmt->get_result()->fetch_assoc();

    if (!$conv) {
        $stmt = $conn->prepare("INSERT INTO chat_conversaciones (id_usuario_1, id_usuario_2) VALUES (?, ?)");
        $stmt->bind_param("ii", $u1, $u2);
        $stmt->execute();
        $idConv = $stmt->insert_id;
    } else {
        $idConv = $conv['id_conversacion'];
        // Actualizar última actividad
        $conn->query("UPDATE chat_conversaciones SET ultima_actividad = NOW() WHERE id_conversacion = $idConv");
    }

    $stmt = $conn->prepare("INSERT INTO chat_mensajes (id_conversacion, id_emisor, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $idConv, $ID, $mensaje);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id_mensaje' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar']);
    }
    exit;
}

// ==================== LISTAR ====================
if ($accion === 'listar') {
    $destino = intval($_GET['u'] ?? 0);
    if ($destino <= 0) {
        echo json_encode(['success' => false, 'error' => 'Destino inválido']);
        exit;
    }

    $u1 = min($ID, $destino);
    $u2 = max($ID, $destino);

    $stmt = $conn->prepare("SELECT id_conversacion FROM chat_conversaciones WHERE id_usuario_1 = ? AND id_usuario_2 = ?");
    $stmt->bind_param("ii", $u1, $u2);
    $stmt->execute();
    $conv = $stmt->get_result()->fetch_assoc();

    if (!$conv) {
        echo json_encode(['success' => true, 'mensajes' => []]);
        exit;
    }

    $idConv = $conv['id_conversacion'];

    // Marcar como leídos los que van hacia mí
    $conn->query("UPDATE chat_mensajes SET leido = 1 WHERE id_conversacion = $idConv AND id_emisor = $destino AND leido = 0");

    $stmt = $conn->prepare("SELECT id_mensaje, id_emisor, mensaje, fecha 
                            FROM chat_mensajes 
                            WHERE id_conversacion = ? 
                            ORDER BY fecha ASC 
                            LIMIT 200");
    $stmt->bind_param("i", $idConv);
    $stmt->execute();
    $res = $stmt->get_result();

    $mensajes = [];
    while ($m = $res->fetch_assoc()) {
        $mensajes[] = $m;
    }

    echo json_encode(['success' => true, 'mensajes' => $mensajes]);
    exit;
}

// ==================== NO-LEÍDOS ====================
if ($accion === 'no_leidos') {
    $stmt = $conn->prepare("
        SELECT cc.id_conversacion, 
               IF(cc.id_usuario_1 = ?, cc.id_usuario_2, cc.id_usuario_1) AS otro_usuario,
               COUNT(cm.id_mensaje) AS no_leidos
        FROM chat_conversaciones cc
        INNER JOIN chat_mensajes cm ON cc.id_conversacion = cm.id_conversacion
        WHERE (cc.id_usuario_1 = ? OR cc.id_usuario_2 = ?)
          AND cm.id_emisor != ?
          AND cm.leido = 0
        GROUP BY cc.id_conversacion
    ");
    $stmt->bind_param("iiii", $ID, $ID, $ID, $ID);
    $stmt->execute();
    $res = $stmt->get_result();

    $out = [];
    while ($r = $res->fetch_assoc()) {
        $out[$r['otro_usuario']] = (int)$r['no_leidos'];
    }

    echo json_encode(['success' => true, 'no_leidos' => $out]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acción no válida']);
?>