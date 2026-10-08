<?php
include('../cone.php');
include('../permisos.php');   // <-- NUEVO
session_start();

if (!isset($_SESSION['IDDATOS'])) {
    header("Location: ../index.php");
    exit;
}

$ID       = $_SESSION['IDDATOS'];
$USER     = $_SESSION['USER'];
$NAME     = $_SESSION['NAME'];
$APE      = $_SESSION['SURNAME'];
$CEDULA   = $_SESSION['CEDULA'];
$area     = $_SESSION['ASSIGNED_AREA'];
$ROL      = $_SESSION['IDROLS'];

// Foto usuario
$consulta = mysqli_query($conn, "SELECT CEDULA, foto FROM user_datos WHERE CEDULA = '$CEDULA'");
$valores  = mysqli_fetch_array($consulta);
$foto     = $valores['foto'] ?? 'images/Canaima.png';

// Noticias
$queryNoticias = "SELECT cod_imagen, imagen, nombre, comentario, fecha_publicacion 
                  FROM imagenes ORDER BY fecha_publicacion DESC LIMIT 6";
$resultadoNoticias = mysqli_query($conn, $queryNoticias);

// Estadísticas SOLO si tiene permiso
if (tienePermiso('dashboard.stats', $conn)) {
    $stats = [
        'usuarios'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM user_datos"))['total'],
        'reportes'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM report"))['total'],
        'pendientes' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM report WHERE STATUS = 3"))['total'],
        'noticias'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM imagenes"))['total'],
    ];
} else {
    // Stats personales para usuarios normales
    $stats = [
        'usuarios'   => null,
        'reportes'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM report WHERE ID_NAME = '$ID'"))['total'],
        'pendientes' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM report WHERE ID_NAME = '$ID' AND STATUS = 3"))['total'],
        'noticias'   => null,
    ];
}

// Nombre del rol
$roles = [1 => 'Administrador', 2 => 'Usuario', 3 => 'Técnico', 4 => 'RRHH'];
$nombreRol = $roles[$ROL] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Industria Canaima</title>
  <link rel="shortcut icon" href="images/Canaima.png" type="image/x-icon">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #667eea;
      --primary-dark: #5568d3;
      --secondary: #764ba2;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #3b82f6;
      --dark: #1e293b;
      --gray: #64748b;
      --light: #f1f5f9;
      --border: #e2e8f0;
    }

    * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    body { background: #f8fafc; color: var(--dark); margin: 0; }

    .header {
      background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      padding: 0 1.5rem; height: 70px;
      position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
      display: flex; align-items: center; justify-content: space-between;
      border-bottom: 1px solid var(--border);
    }
    .header .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--dark); font-weight: 700; font-size: 1.1rem; }
    .header .logo img { height: 40px; }
    .header .search-form { flex: 1; max-width: 400px; margin: 0 2rem; position: relative; }
    .header .search-form input { width: 100%; padding: 0.6rem 1rem 0.6rem 2.75rem; border: 1px solid var(--border); border-radius: 10px; background: var(--light); font-size: 0.9rem; }
    .header .search-form input:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    .header .search-form i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray); }
    .header .profile-btn { display: flex; align-items: center; gap: 0.75rem; padding: 0.4rem 0.75rem; border-radius: 10px; text-decoration: none; color: var(--dark); transition: background 0.2s; }
    .header .profile-btn:hover { background: var(--light); }
    .header .profile-btn img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); }
    .header .profile-btn .info { display: flex; flex-direction: column; line-height: 1.2; }
    .header .profile-btn .info strong { font-size: 0.85rem; font-weight: 600; }
    .header .profile-btn .info small { font-size: 0.75rem; color: var(--gray); }

    .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: 260px; background: #fff; border-right: 1px solid var(--border); overflow-y: auto; padding: 1.25rem 0.75rem; transition: transform 0.3s; z-index: 900; }
    .sidebar::-webkit-scrollbar { width: 6px; }
    .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav .nav-heading { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gray); padding: 0.75rem 0.75rem 0.5rem; font-weight: 600; }
    .sidebar-nav .nav-item { margin-bottom: 0.15rem; }
    .sidebar-nav .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; color: var(--gray); text-decoration: none; border-radius: 8px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; }
    .sidebar-nav .nav-link:hover { background: var(--light); color: var(--primary); }
    .sidebar-nav .nav-link.active { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: #fff; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
    .sidebar-nav .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; }

    .main { margin-left: 260px; margin-top: 70px; padding: 1.75rem; min-height: calc(100vh - 70px); }

    .page-header { margin-bottom: 1.75rem; }
    .page-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-header .breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
    .page-header .breadcrumb a { color: var(--primary); text-decoration: none; }
    .page-header .breadcrumb-item.active { color: var(--gray); }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card { background: #fff; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border); transition: all 0.3s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .stat-card .icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; flex-shrink: 0; }
    .stat-card .icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-card .icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card .icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card .icon.info    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-card .info h4 { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .stat-card .info span { font-size: 0.8rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.03em; font-weight: 500; }

    .news-section-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .news-section-title h2 { font-size: 1.15rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem; }
    .news-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border); transition: all 0.3s; cursor: pointer; }
    .news-card:hover { transform: translateY(-5px); box-shadow: 0 12px 28px rgba(0,0,0,0.1); }
    .news-card .img-container { position: relative; height: 170px; overflow: hidden; background: var(--light); }
    .news-card .img-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .news-card:hover .img-container img { transform: scale(1.08); }
    .news-card .img-container .overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%); display: flex; align-items: flex-end; padding: 1rem; color: #fff; }
    .news-card .img-container .overlay h3 { font-size: 1rem; font-weight: 600; margin: 0; color: #fff; }
    .news-card .card-body { padding: 1rem; }
    .news-card .card-body p { font-size: 0.85rem; color: var(--gray); margin: 0 0 0.5rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .news-card .card-body .date { font-size: 0.75rem; color: var(--gray); display: flex; align-items: center; gap: 0.35rem; }

    .side-panel { background: #fff; border-radius: 14px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border); margin-bottom: 1.25rem; }
    .side-panel h5 { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .quick-action { display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem; border-radius: 10px; text-decoration: none; color: var(--dark); transition: all 0.2s; border: 1px solid var(--border); margin-bottom: 0.5rem; }
    .quick-action:hover { background: var(--light); border-color: var(--primary); color: var(--primary); transform: translateX(4px); }
    .quick-action i { width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .quick-action span { font-size: 0.875rem; font-weight: 500; }

    .footer { margin-left: 260px; padding: 1.5rem; text-align: center; color: var(--gray); font-size: 0.85rem; border-top: 1px solid var(--border); background: #fff; }

    .toggle-sidebar { display: none; background: transparent; border: none; font-size: 1.5rem; color: var(--dark); cursor: pointer; }
    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.show { transform: translateX(0); box-shadow: 0 0 30px rgba(0,0,0,0.15); }
      .main, .footer { margin-left: 0; }
      .toggle-sidebar { display: block; }
      .header .search-form { display: none; }
    }
  </style>
  <script>
  // Verificar mensajes no leídos cada 10 segundos
  function verificarChat() {
    fetch('chat_api.php?accion=no_leidos')
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          const total = Object.values(data.no_leidos).reduce((a,b) => a+b, 0);
          const badge = document.getElementById('badgeChatGlobal');
          if (badge) {
            if (total > 0) {
              badge.textContent = total;
              badge.style.display = 'inline-block';
            } else {
              badge.style.display = 'none';
            }
          }
        }
      })
      .catch(() => {});
  }
  setInterval(verificarChat, 10000);
  verificarChat();
</script>
</head>
<body>

<!-- HEADER -->
<header class="header">
  <div class="d-flex align-items-center gap-3">
    <button class="toggle-sidebar" onclick="document.querySelector('.sidebar').classList.toggle('show')">
      <i class="bi bi-list"></i>
    </button>
    <a href="index.php" class="logo">
      <img src="images/logo.jpeg" alt="Canaima">
      <span class="d-none d-md-inline">Industria Canaima</span>
    </a>
  </div>

  <div class="dropdown">
    <a href="#" class="profile-btn" data-bs-toggle="dropdown">
      <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar">
      <div class="info d-none d-md-block">
        <strong><?php echo htmlspecialchars($NAME); ?></strong>
        <small><?php echo htmlspecialchars($nombreRol); ?></small>
      </div>
      <i class="bi bi-chevron-down d-none d-md-inline" style="font-size: 0.75rem;"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius: 10px; border: none; padding: 0.5rem;">
      <li class="px-3 py-2 border-bottom">
        <strong class="d-block"><?php echo htmlspecialchars($NAME . ' ' . $APE); ?></strong>
        <small class="text-muted"><?php echo htmlspecialchars($area); ?></small>
      </li>
      <li><a class="dropdown-item py-2" href="perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item py-2 text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
    </ul>
  </div>
</header>

<!-- SIDEBAR CON PERMISOS -->
<aside class="sidebar">
  <ul class="sidebar-nav">

    <!-- Dashboard: TODOS -->
    <?php if (tienePermiso('dashboard.ver', $conn)): ?>
      <li class="nav-item">
        <a class="nav-link active" href="index.php">
          <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if (tienePermiso('chat.ver', $conn)): ?>
      <li class="nav-item">
        <a class="nav-link" href="chat.php">
          <i class="bi bi-chat-dots"></i>
          <span>Chat Interno</span>
          <span class="badge bg-danger ms-auto" id="badgeChatGlobal" style="display:none;">0</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- Sección SOLICITUDES -->
    <?php if (tienePermiso('soporte.crear', $conn) || tienePermiso('recursos.constancia', $conn) || tienePermiso('recursos.recibo', $conn)): ?>
      <li class="nav-heading">Solicitudes</li>

      <?php if (tienePermiso('soporte.crear', $conn)): ?>
        <li class="nav-item">
          <a class="nav-link" href="soporte_tecnico.php">
            <i class="bi bi-headset"></i><span>Soporte Técnico</span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (tienePermiso('recursos.constancia', $conn)): ?>
        <li class="nav-item">
          <a class="nav-link" href="Constancia_de_trabajo.php?edi=<?php echo $ID; ?>">
            <i class="bi bi-file-earmark-text"></i><span>Constancia de Trabajo</span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (tienePermiso('recursos.recibo', $conn)): ?>
        <li class="nav-item">
          <a class="nav-link" href="recibo.php">
            <i class="bi bi-receipt"></i><span>Recibo de Pago</span>
          </a>
        </li>
      <?php endif; ?>
    <?php endif; ?>

    <!-- Gestión de casos: SOLO técnicos y admins -->
    <?php if (tienePermiso('soporte.ver_todas', $conn)): ?>
      <li class="nav-item">
        <a class="nav-link" href="caso_soporte.php">
          <i class="bi bi-ticket-detailed"></i><span>Gestión de Casos</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- Biblioteca / Descargas -->
    <?php if (tienePermiso('recursos.descargar', $conn)): ?>
      <li class="nav-heading">Recursos</li>
      <li class="nav-item">
        <a class="nav-link" href="./pdf/vacaciones.xls">
          <i class="bi bi-download"></i><span>Planilla de Vacaciones</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./pdf/permiso.docx">
          <i class="bi bi-download"></i><span>Planilla de Permisos</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./pdf/103_Manual_Canaimit.pdf">
          <i class="bi bi-book"></i><span>Manual Canaima</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- Administración (Usuarios y Noticias) -->
    <?php if (tienePermiso('usuarios.ver', $conn) || tienePermiso('noticias.crear', $conn)): ?>
      <li class="nav-heading">Administración</li>

      <?php if (tienePermiso('usuarios.ver', $conn)): ?>
        <li class="nav-item">
          <a class="nav-link" href="usuarios.php">
            <i class="bi bi-people-fill"></i><span>Gestión de Usuarios</span>
          </a>
        </li>
      <?php endif; ?>

      <?php if (tienePermiso('noticias.crear', $conn)): ?>
        <li class="nav-item">
          <a class="nav-link" href="cargar_noticia.php">
            <i class="bi bi-newspaper"></i><span>Cargar Noticia</span>
          </a>
        </li>
      <?php endif; ?>
    <?php endif; ?>

    <!-- Perfil: TODOS -->
    <li class="nav-heading">Cuenta</li>
    <li class="nav-item">
      <a class="nav-link" href="perfil.php">
        <i class="bi bi-person-circle"></i><span>Mi Perfil</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-danger" href="../logout.php">
        <i class="bi bi-box-arrow-right"></i><span>Cerrar Sesión</span>
      </a>
    </li>
  </ul>
</aside>

<!-- MAIN -->
<main class="main">

  <div class="page-header">
    <h1>¡Bienvenido, <?php echo htmlspecialchars($NAME); ?>! 👋</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>

  <!-- Stats: SOLO si tiene permiso de stats globales -->
  <div class="stats-grid">
    <?php if ($stats['usuarios'] !== null): ?>
      <div class="stat-card">
        <div class="icon primary"><i class="bi bi-people-fill"></i></div>
        <div class="info">
          <h4><?php echo $stats['usuarios']; ?></h4>
          <span>Usuarios</span>
        </div>
      </div>
    <?php endif; ?>

    <div class="stat-card">
      <div class="icon info"><i class="bi bi-ticket-detailed"></i></div>
      <div class="info">
        <h4><?php echo $stats['reportes']; ?></h4>
        <span><?php echo $stats['usuarios'] !== null ? 'Reportes' : 'Mis Solicitudes'; ?></span>
      </div>
    </div>

    <div class="stat-card">
      <div class="icon warning"><i class="bi bi-clock-history"></i></div>
      <div class="info">
        <h4><?php echo $stats['pendientes']; ?></h4>
        <span>Pendientes</span>
      </div>
    </div>

    <?php if ($stats['noticias'] !== null): ?>
      <div class="stat-card">
        <div class="icon success"><i class="bi bi-newspaper"></i></div>
        <div class="info">
          <h4><?php echo $stats['noticias']; ?></h4>
          <span>Noticias</span>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="row g-4">
    <!-- Noticias -->
    <div class="col-lg-8">
      <div class="news-section-title">
        <h2><i class="bi bi-newspaper text-primary"></i> Últimas Noticias</h2>
        <?php if (tienePermiso('noticias.crear', $conn)): ?>
          <a href="cargar_noticia.php" class="btn btn-sm btn-primary" style="border-radius: 8px;">
            <i class="bi bi-plus-lg"></i> Nueva
          </a>
        <?php endif; ?>
      </div>

      <div class="news-grid">
        <?php if (mysqli_num_rows($resultadoNoticias) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($resultadoNoticias)): ?>
            <div class="news-card" data-bs-toggle="modal" data-bs-target="#modalNoticia<?php echo $row['cod_imagen']; ?>">
              <div class="img-container">
                <img src="../imagenes/<?php echo htmlspecialchars($row['imagen']); ?>" 
                     alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                     onerror="this.src='https://via.placeholder.com/400x200/667eea/ffffff?text=Noticia'">
                <div class="overlay">
                  <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                </div>
              </div>
              <div class="card-body">
                <p><?php echo htmlspecialchars($row['comentario']); ?></p>
                <div class="date">
                  <i class="bi bi-calendar3"></i>
                  <?php echo isset($row['fecha_publicacion']) ? date('d/m/Y', strtotime($row['fecha_publicacion'])) : 'Sin fecha'; ?>
                </div>
              </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalNoticia<?php echo $row['cod_imagen']; ?>" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius: 14px; border: none; overflow: hidden;">
                  <div class="modal-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;">
                    <h5 class="modal-title"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <img src="../imagenes/<?php echo htmlspecialchars($row['imagen']); ?>" 
                       style="width: 100%; max-height: 400px; object-fit: cover;">
                  <div class="modal-body p-4">
                    <p style="font-size: 0.95rem; line-height: 1.7; color: #475569;">
                      <?php echo nl2br(htmlspecialchars($row['comentario'])); ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="text-center py-5 w-100" style="color: var(--gray);">
            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.4;"></i>
            <p class="mt-3">No hay noticias publicadas todavía</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="col-lg-4">
      <div class="side-panel">
        <h5><i class="bi bi-lightning-charge-fill text-primary"></i> Acciones Rápidas</h5>

        <?php if (tienePermiso('soporte.crear', $conn)): ?>
          <a href="soporte_tecnico.php" class="quick-action">
            <i class="bi bi-headset"></i><span>Nueva Solicitud</span>
          </a>
        <?php endif; ?>

        <?php if (tienePermiso('recursos.constancia', $conn)): ?>
          <a href="Constancia_de_trabajo.php?edi=<?php echo $ID; ?>" class="quick-action">
            <i class="bi bi-file-earmark-text"></i><span>Constancia</span>
          </a>
        <?php endif; ?>

        <?php if (tienePermiso('usuarios.ver', $conn)): ?>
          <a href="usuarios.php" class="quick-action">
            <i class="bi bi-people"></i><span>Usuarios</span>
          </a>
        <?php endif; ?>

        <?php if (tienePermiso('noticias.crear', $conn)): ?>
          <a href="cargar_noticia.php" class="quick-action">
            <i class="bi bi-newspaper"></i><span>Publicar Noticia</span>
          </a>
        <?php endif; ?>

        <a href="perfil.php" class="quick-action">
          <i class="bi bi-person-circle"></i><span>Mi Perfil</span>
        </a>
      </div>
    </div>
  </div>

</main>

<footer class="footer">
  <strong>Industria Canaima C.A.</strong> © <?php echo date('Y'); ?> — Todos los derechos reservados
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>