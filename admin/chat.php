<?php
include('../cone.php');
include('../permisos.php');
session_start();

if (!isset($_SESSION['IDDATOS'])) {
    header("Location: ../index.php");
    exit;
}

exigirPermiso('chat.usar', $conn, 'index.php');

$ID       = $_SESSION['IDDATOS'];
$NAME     = $_SESSION['NAME'];
$APE      = $_SESSION['SURNAME'];
$CEDULA   = $_SESSION['CEDULA'];

// Foto usuario actual
$consulta = mysqli_query($conn, "SELECT foto FROM user_datos WHERE IDDATOS = '$ID'");
$foto = mysqli_fetch_assoc($consulta)['foto'] ?? 'images/Canaima.png';

// Usuarios disponibles para chatear (excluyendo al actual)
$sqlUsuarios = "SELECT IDDATOS, USER, NAME, SURNAME, foto, ASSIGNED_AREA 
                FROM user_datos 
                WHERE IDDATOS != '$ID' 
                ORDER BY NAME ASC";
$usuarios = mysqli_query($conn, $sqlUsuarios);

// Conversación activa (si viene por GET)
$idDestino = intval($_GET['u'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat Interno | Industria Canaima</title>
  <link rel="shortcut icon" href="images/Canaima.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #667eea;
      --primary-dark: #5568d3;
      --secondary: #764ba2;
      --dark: #1e293b;
      --gray: #64748b;
      --light: #f1f5f9;
      --border: #e2e8f0;
    }
    * { font-family: 'Inter', sans-serif; }
    body { background: #f8fafc; margin: 0; overflow: hidden; }

    /* HEADER */
    .header {
      background: #fff; height: 70px; padding: 0 1.5rem;
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      display: flex; align-items: center; justify-content: space-between;
      border-bottom: 1px solid var(--border);
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .header .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--dark); font-weight: 700; }
    .header .logo img { height: 40px; }
    .header .profile-btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.75rem; border-radius: 10px; text-decoration: none; color: var(--dark); }
    .header .profile-btn:hover { background: var(--light); }
    .header .profile-btn img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }

    /* LAYOUT CHAT */
    .chat-wrapper {
      display: flex;
      height: calc(100vh - 70px);
      margin-top: 70px;
    }

    /* SIDEBAR IZQUIERDO - Lista de usuarios */
    .chat-sidebar {
      width: 320px;
      background: #fff;
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
    }
    .chat-sidebar .sidebar-header {
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--border);
      background: #fff;
    }
    .chat-sidebar .sidebar-header h5 {
      font-size: 1.1rem; font-weight: 700; margin: 0 0 0.75rem;
      display: flex; align-items: center; gap: 0.5rem;
    }
    .chat-sidebar .search-box {
      position: relative;
    }
    .chat-sidebar .search-box input {
      width: 100%; padding: 0.5rem 1rem 0.5rem 2.5rem;
      border: 1px solid var(--border); border-radius: 10px;
      background: var(--light); font-size: 0.875rem;
    }
    .chat-sidebar .search-box i {
      position: absolute; left: 0.85rem; top: 50%;
      transform: translateY(-50%); color: var(--gray);
    }

    .chat-user-list {
      flex: 1; overflow-y: auto; padding: 0.5rem;
    }
    .chat-user-list::-webkit-scrollbar { width: 6px; }
    .chat-user-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

    .chat-user-item {
      display: flex; align-items: center; gap: 0.75rem;
      padding: 0.75rem; border-radius: 10px;
      text-decoration: none; color: var(--dark);
      transition: background 0.15s; cursor: pointer;
      position: relative;
    }
    .chat-user-item:hover { background: var(--light); }
    .chat-user-item.active { background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.1)); }
    .chat-user-item img { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
    .chat-user-item .info { flex: 1; min-width: 0; }
    .chat-user-item .info strong {
      display: block; font-size: 0.875rem; font-weight: 600;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .chat-user-item .info small {
      font-size: 0.75rem; color: var(--gray);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      display: block;
    }
    .chat-user-item .badge-unread {
      background: var(--primary); color: #fff;
      font-size: 0.7rem; font-weight: 700;
      padding: 0.15rem 0.45rem; border-radius: 10px;
    }

    /* ÁREA PRINCIPAL DEL CHAT */
    .chat-main {
      flex: 1; display: flex; flex-direction: column;
      background: #f8fafc;
    }

    .chat-main-header {
      padding: 1rem 1.5rem;
      background: #fff;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 1rem;
    }
    .chat-main-header img {
      width: 44px; height: 44px; border-radius: 50%; object-fit: cover;
    }
    .chat-main-header .info h6 { font-size: 1rem; font-weight: 700; margin: 0; }
    .chat-main-header .info small { font-size: 0.8rem; color: var(--gray); }

    .chat-messages {
      flex: 1; overflow-y: auto; padding: 1.5rem;
      display: flex; flex-direction: column; gap: 0.75rem;
    }
    .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

    .message {
      max-width: 70%;
      padding: 0.75rem 1rem;
      border-radius: 14px;
      font-size: 0.9rem;
      line-height: 1.5;
      word-wrap: break-word;
      position: relative;
    }
    .message.sent {
      align-self: flex-end;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      border-bottom-right-radius: 4px;
    }
    .message.received {
      align-self: flex-start;
      background: #fff;
      color: var(--dark);
      border: 1px solid var(--border);
      border-bottom-left-radius: 4px;
    }
    .message .time {
      font-size: 0.7rem;
      opacity: 0.75;
      display: block;
      margin-top: 0.35rem;
      text-align: right;
    }

    .chat-input-area {
      padding: 1rem 1.5rem;
      background: #fff;
      border-top: 1px solid var(--border);
      display: flex; gap: 0.75rem;
      align-items: flex-end;
    }
    .chat-input-area textarea {
      flex: 1;
      border: 1.5px solid var(--border);
      border-radius: 12px;
      padding: 0.65rem 1rem;
      font-size: 0.9rem;
      resize: none;
      max-height: 120px;
      font-family: inherit;
    }
    .chat-input-area textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .chat-input-area button {
      width: 46px; height: 46px;
      border-radius: 12px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff; border: none;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem;
      cursor: pointer;
      transition: all 0.2s;
    }
    .chat-input-area button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(102,126,234,0.4);
    }

    /* PANTALLA VACÍA */
    .chat-empty {
      flex: 1; display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      color: var(--gray);
    }
    .chat-empty i { font-size: 5rem; opacity: 0.2; margin-bottom: 1rem; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .chat-sidebar {
        width: 80px;
      }
      .chat-sidebar .sidebar-header h5 span,
      .chat-user-item .info,
      .chat-sidebar .search-box { display: none; }
      .chat-user-item { justify-content: center; padding: 0.5rem; }
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header class="header">
  <a href="index.php" class="logo">
    <img src="images/Canaima.png" alt="Canaima">
    <span class="d-none d-md-inline">Industria Canaima</span>
  </a>

  <a href="index.php" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px;">
    <i class="bi bi-arrow-left"></i> Volver al Dashboard
  </a>

  <a href="perfil.php" class="profile-btn">
    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar">
    <div class="d-none d-md-block">
      <strong style="font-size: 0.85rem;"><?php echo htmlspecialchars($NAME); ?></strong>
    </div>
  </a>
</header>

<!-- CHAT -->
<div class="chat-wrapper">

  <!-- Lista de usuarios -->
  <aside class="chat-sidebar">
    <div class="sidebar-header">
      <h5><i class="bi bi-chat-dots-fill text-primary"></i> <span>Chat Interno</span></h5>
      <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="buscarUsuario" placeholder="Buscar usuario..." oninput="filtrarUsuarios()">
      </div>
    </div>

    <div class="chat-user-list" id="listaUsuarios">
      <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
        <a href="?u=<?php echo $u['IDDATOS']; ?>" 
           class="chat-user-item <?php echo $idDestino == $u['IDDATOS'] ? 'active' : ''; ?>"
           data-nombre="<?php echo strtolower($u['NAME'] . ' ' . $u['SURNAME'] . ' ' . $u['USER']); ?>">
          <img src="<?php echo htmlspecialchars($u['foto'] ?? 'images/Canaima.png'); ?>"
               onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($u['NAME']); ?>&background=667eea&color=fff'">
          <div class="info">
            <strong><?php echo htmlspecialchars($u['NAME'] . ' ' . $u['SURNAME']); ?></strong>
            <small><?php echo htmlspecialchars($u['ASSIGNED_AREA'] ?? 'Sin área'); ?></small>
          </div>
          <span class="badge-unread" id="badge-<?php echo $u['IDDATOS']; ?>" style="display:none;">0</span>
        </a>
      <?php endwhile; ?>
    </div>
  </aside>

  <!-- Área de mensajes -->
  <main class="chat-main">
    <?php if ($idDestino > 0): 
      // Info del destinatario
      $sqlDest = mysqli_query($conn, "SELECT * FROM user_datos WHERE IDDATOS = '$idDestino'");
      $dest = mysqli_fetch_assoc($sqlDest);
      
      if (!$dest) {
        echo '<div class="chat-empty"><i class="bi bi-person-x"></i><p>Usuario no encontrado</p></div>';
      } else {
    ?>
      <div class="chat-main-header">
        <img src="<?php echo htmlspecialchars($dest['foto'] ?? 'images/Canaima.png'); ?>"
             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($dest['NAME']); ?>&background=667eea&color=fff'">
        <div class="info">
          <h6><?php echo htmlspecialchars($dest['NAME'] . ' ' . $dest['SURNAME']); ?></h6>
          <small><i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i> En línea</small>
        </div>
      </div>

      <div class="chat-messages" id="chatMessages">
        <!-- Los mensajes se cargan aquí por AJAX -->
        <div class="text-center text-muted py-3">
          <small>Cargando mensajes...</small>
        </div>
      </div>

      <div class="chat-input-area">
        <textarea id="inputMensaje" rows="1" 
                  placeholder="Escribe un mensaje..."
                  onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault(); enviarMensaje();}"></textarea>
        <button onclick="enviarMensaje()">
          <i class="bi bi-send-fill"></i>
        </button>
      </div>

    <?php } else: ?>
      <div class="chat-empty">
        <i class="bi bi-chat-square-text"></i>
        <h4>Selecciona un usuario para chatear</h4>
        <p>Elige a alguien de la lista de la izquierda</p>
      </div>
    <?php endif; ?>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const ID_ACTUAL = <?php echo $ID; ?>;
  const ID_DESTINO = <?php echo $idDestino; ?>;

  // Auto-resize textarea
  const textarea = document.getElementById('inputMensaje');
  if (textarea) {
    textarea.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
  }

  // Cargar mensajes
  function cargarMensajes() {
    if (ID_DESTINO <= 0) return;

    fetch(`chat_api.php?accion=listar&u=${ID_DESTINO}`)
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const cont = document.getElementById('chatMessages');
          if (data.mensajes.length === 0) {
            cont.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-chat-dots" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2">No hay mensajes aún. ¡Escribe el primero!</p></div>';
            return;
          }

          let html = '';
          data.mensajes.forEach(m => {
            const clase = m.id_emisor == ID_ACTUAL ? 'sent' : 'received';
            const hora = new Date(m.fecha).toLocaleTimeString('es-VE', {hour: '2-digit', minute: '2-digit'});
            html += `<div class="message ${clase}">
                      ${escapeHtml(m.mensaje)}
                      <span class="time">${hora}</span>
                    </div>`;
          });
          cont.innerHTML = html;
          cont.scrollTop = cont.scrollHeight;
        }
      })
      .catch(e => console.error('Error:', e));
  }

  // Enviar mensaje
  function enviarMensaje() {
    const txt = document.getElementById('inputMensaje').value.trim();
    if (!txt || ID_DESTINO <= 0) return;

    fetch('chat_api.php?accion=enviar', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `destino=${ID_DESTINO}&mensaje=${encodeURIComponent(txt)}`
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        document.getElementById('inputMensaje').value = '';
        document.getElementById('inputMensaje').style.height = 'auto';
        cargarMensajes();
      }
    });
  }

  // Escapar HTML
  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // Filtrar usuarios
  function filtrarUsuarios() {
    const q = document.getElementById('buscarUsuario').value.toLowerCase();
    document.querySelectorAll('.chat-user-item').forEach(el => {
      const nombre = el.dataset.nombre;
      el.style.display = nombre.includes(q) ? 'flex' : 'none';
    });
  }

  // Polling: cargar mensajes cada 3 segundos
  if (ID_DESTINO > 0) {
    cargarMensajes();
    setInterval(cargarMensajes, 3000);
  }
</script>
</body>
</html>