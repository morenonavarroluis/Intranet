<?php
include('../cone.php');
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

// Solo admin (1) y RRHH (4) pueden cargar noticias
if ($ROL != 1 && $ROL != 4) {
    header("Location: index.php");
    exit;
}

// Foto usuario
$consulta = mysqli_query($conn, "SELECT CEDULA, foto FROM user_datos WHERE CEDULA = '$CEDULA'");
$valores  = mysqli_fetch_array($consulta);
$foto     = $valores['foto'] ?? 'images/Canaima.png';

// Listar noticias existentes
$sqlNoticias = "SELECT i.cod_imagen, i.imagen, i.nombre, i.comentario, i.fecha_publicacion,
                       u.USER, u.NAME, u.SURNAME
                FROM imagenes i
                LEFT JOIN user_datos u ON i.IDDATOS = u.IDDATOS
                ORDER BY i.fecha_publicacion DESC";
$noticias = mysqli_query($conn, $sqlNoticias);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Noticias | Industria Canaima</title>
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

    /* ============ HEADER ============ */
    .header {
      background: #fff;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      padding: 0 1.5rem;
      height: 70px;
      position: fixed; top: 0; left: 0; right: 0;
      z-index: 1000;
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

    /* ============ SIDEBAR ============ */
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

    /* ============ MAIN ============ */
    .main { margin-left: 260px; margin-top: 70px; padding: 1.75rem; min-height: calc(100vh - 70px); }

    /* ============ PAGE HEADER ============ */
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-header .breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
    .page-header .breadcrumb a { color: var(--primary); text-decoration: none; }
    .page-header .breadcrumb-item.active { color: var(--gray); }

    /* ============ FORM CARD ============ */
    .form-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border); overflow: hidden; margin-bottom: 1.5rem; }
    .form-card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 1.75rem 2rem; }
    .form-card-header h2 { font-size: 1.35rem; font-weight: 700; margin: 0 0 0.35rem; display: flex; align-items: center; gap: 0.65rem; }
    .form-card-header p { margin: 0; opacity: 0.9; font-size: 0.9rem; }
    .form-card-body { padding: 2rem; }

    /* ============ FORM FIELDS ============ */
    .form-label { font-weight: 600; font-size: 0.85rem; color: var(--dark); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem; }
    .form-label .required { color: var(--danger); }
    .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
    textarea.form-control { min-height: 130px; resize: vertical; }

    /* ============ IMAGE UPLOAD ============ */
    .image-upload-area {
      border: 2px dashed var(--border);
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
      background: #fafbfc;
      position: relative;
    }
    .image-upload-area:hover {
      border-color: var(--primary);
      background: rgba(102, 126, 234, 0.03);
    }
    .image-upload-area.has-image {
      padding: 0;
      border-style: solid;
      border-color: var(--primary);
      background: #000;
    }
    .image-upload-area input[type="file"] { display: none; }
    .upload-placeholder i {
      font-size: 3rem;
      color: var(--primary);
      opacity: 0.6;
      margin-bottom: 0.75rem;
      display: block;
    }
    .upload-placeholder h6 { font-weight: 600; margin-bottom: 0.35rem; color: var(--dark); }
    .upload-placeholder p { font-size: 0.8rem; color: var(--gray); margin: 0; }
    .image-preview {
      width: 100%;
      max-height: 320px;
      object-fit: cover;
      border-radius: 10px;
      display: block;
    }
    .btn-remove-img {
      position: absolute;
      top: 10px; right: 10px;
      background: rgba(239, 68, 68, 0.9);
      color: #fff;
      border: none;
      width: 34px; height: 34px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      transition: transform 0.2s;
      z-index: 10;
    }
    .btn-remove-img:hover { transform: scale(1.1); }

    /* ============ BUTTONS ============ */
    .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: #fff; font-weight: 600; padding: 0.85rem 2rem; border-radius: 10px; font-size: 0.95rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); color: #fff; }
    .btn-cancel { background: #fff; border: 1.5px solid var(--border); color: var(--gray); font-weight: 600; padding: 0.85rem 1.5rem; border-radius: 10px; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-cancel:hover { background: var(--light); color: var(--dark); }

    /* ============ NEWS LIST ============ */
    .news-list-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
    .news-list-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
    .news-list-header h5 { font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .news-count { font-size: 0.85rem; color: var(--gray); }

    .news-row { display: flex; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--light); transition: background 0.15s; align-items: center; }
    .news-row:last-child { border-bottom: none; }
    .news-row:hover { background: #fafbfc; }

    .news-thumb {
      width: 100px; height: 75px;
      border-radius: 10px;
      object-fit: cover;
      flex-shrink: 0;
      background: var(--light);
      border: 1px solid var(--border);
    }

    .news-content { flex: 1; min-width: 0; }
    .news-content h6 {
      font-size: 0.95rem; font-weight: 700;
      margin: 0 0 0.35rem; color: var(--dark);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .news-content p {
      font-size: 0.82rem; color: var(--gray);
      margin: 0 0 0.5rem; line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .news-meta {
      display: flex; gap: 0.85rem;
      font-size: 0.75rem; color: var(--gray);
      flex-wrap: wrap;
    }
    .news-meta span { display: inline-flex; align-items: center; gap: 0.3rem; }

    .news-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }
    .btn-action { width: 34px; height: 34px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.2s; cursor: pointer; }
    .btn-action.edit { background: #fef3c7; color: #92400e; }
    .btn-action.edit:hover { background: #f59e0b; color: #fff; }
    .btn-action.delete { background: #fee2e2; color: #991b1b; }
    .btn-action.delete:hover { background: #ef4444; color: #fff; }

    /* ============ EMPTY STATE ============ */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray); }
    .empty-state i { font-size: 3.5rem; opacity: 0.3; display: block; margin-bottom: 1rem; }

    /* ============ COUNTER ============ */
    .char-counter { font-size: 0.75rem; color: var(--gray); text-align: right; }

    /* ============ FOOTER ============ */
    .footer { margin-left: 260px; padding: 1.5rem; text-align: center; color: var(--gray); font-size: 0.85rem; border-top: 1px solid var(--border); background: #fff; }

    /* ============ RESPONSIVE ============ */
    .toggle-sidebar { display: none; background: transparent; border: none; font-size: 1.5rem; color: var(--dark); cursor: pointer; }
    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.show { transform: translateX(0); box-shadow: 0 0 30px rgba(0,0,0,0.15); }
      .main, .footer { margin-left: 0; }
      .toggle-sidebar { display: block; }
      .header .search-form { display: none; }
    }
    @media (max-width: 768px) {
      .news-row { flex-direction: column; align-items: flex-start; }
      .news-thumb { width: 100%; height: 140px; }
    }
  </style>
</head>
<body>

<!-- ============ HEADER ============ -->
<header class="header">
  <div class="d-flex align-items-center gap-3">
    <button class="toggle-sidebar" onclick="document.querySelector('.sidebar').classList.toggle('show')">
      <i class="bi bi-list"></i>
    </button>
    <a href="index.php" class="logo">
      <img src="images/Canaima.png" alt="Canaima">
      <span class="d-none d-md-inline">Industria Canaima</span>
    </a>
  </div>

  <form class="search-form" method="POST" action="#">
    <i class="bi bi-search"></i>
    <input type="text" name="query" placeholder="Buscar en el sistema...">
  </form>

  <div class="dropdown">
    <a href="#" class="profile-btn" data-bs-toggle="dropdown">
      <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar">
      <div class="info d-none d-md-block">
        <strong><?php echo htmlspecialchars($NAME); ?></strong>
        <small><?php echo htmlspecialchars($area); ?></small>
      </div>
      <i class="bi bi-chevron-down d-none d-md-inline" style="font-size: 0.75rem;"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius: 10px; border: none; padding: 0.5rem;">
      <li class="px-3 py-2 border-bottom">
        <strong class="d-block"><?php echo htmlspecialchars($NAME . ' ' . $APE); ?></strong>
        <small class="text-muted"><?php echo htmlspecialchars($area); ?></small>
      </li>
      <li><a class="dropdown-item py-2" href="perfil.php"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
      <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2"></i> Configuración</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item py-2 text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
    </ul>
  </div>
</header>

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar">
  <ul class="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link" href="index.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    </li>

    <li class="nav-heading">Solicitudes</li>
    <li class="nav-item">
      <a class="nav-link" href="soporte_tecnico.php"><i class="bi bi-headset"></i><span>Soporte Técnico</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="caso_soporte.php"><i class="bi bi-ticket-detailed"></i><span>Casos de Soporte</span></a>
    </li>

    <li class="nav-heading">Contenido</li>
    <li class="nav-item">
      <a class="nav-link active" href="cargar_noticia.php"><i class="bi bi-newspaper"></i><span>Cargar Noticia</span></a>
    </li>

    <li class="nav-heading">Administración</li>
    <li class="nav-item">
      <a class="nav-link" href="perfil.php"><i class="bi bi-person-circle"></i><span>Mi Perfil</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i><span>Cerrar Sesión</span></a>
    </li>
  </ul>
</aside>

<!-- ============ MAIN ============ -->
<main class="main">

  <!-- Page Header -->
  <div class="page-header">
    <h1>Gestión de Noticias</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active">Cargar Noticia</li>
      </ol>
    </nav>
  </div>

  <div class="row g-4">
    <!-- Formulario -->
    <div class="col-lg-7">
      <div class="form-card">
        <div class="form-card-header">
          <h2><i class="bi bi-plus-circle-fill"></i> Nueva Noticia</h2>
          <p>Completa el formulario para publicar una noticia en el sistema.</p>
        </div>

        <div class="form-card-body">
          <form method="POST" action="guardar_noticia.php" enctype="multipart/form-data" id="formNoticia">

            <!-- Título -->
            <div class="mb-4">
              <label class="form-label">
                <i class="bi bi-type"></i>
                Título de la noticia <span class="required">*</span>
              </label>
              <input type="text" name="nombre" class="form-control" maxlength="150" required
                     placeholder="Ej: Nueva actualización del sistema Canaima">
            </div>

            <!-- Comentario -->
            <div class="mb-4">
              <label class="form-label">
                <i class="bi bi-chat-left-text"></i>
                Comentario / Descripción <span class="required">*</span>
              </label>
              <textarea name="comentario" id="txtComentario" class="form-control" maxlength="500" rows="5" required
                        placeholder="Escribe el contenido completo de la noticia..."></textarea>
              <div class="char-counter"><span id="contador">0</span> / 500</div>
            </div>

            <!-- Imagen -->
            <div class="mb-4">
              <label class="form-label">
                <i class="bi bi-image"></i>
                Imagen <span class="required">*</span>
              </label>

              <div class="image-upload-area" id="uploadArea" onclick="document.getElementById('inputImagen').click()">
                <input type="file" id="inputImagen" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp" required onchange="previewImagen(event)">

                <div class="upload-placeholder" id="placeholder">
                  <i class="bi bi-cloud-arrow-up-fill"></i>
                  <h6>Haz clic para subir una imagen</h6>
                  <p>Formatos permitidos: JPG, PNG, GIF, WEBP · Máx 5MB</p>
                </div>

                <button type="button" class="btn-remove-img d-none" id="btnRemove" onclick="eliminarImagen(event)">
                  <i class="bi bi-x-lg"></i>
                </button>

                <img src="" alt="" id="preview" class="image-preview d-none">
              </div>
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="reset" class="btn-cancel" onclick="resetForm()">
                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
              </button>
              <button type="submit" class="btn-gradient">
                <i class="bi bi-send-fill"></i> Publicar Noticia
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Lista de noticias -->
    <div class="col-lg-5">
      <div class="news-list-card">
        <div class="news-list-header">
          <h5><i class="bi bi-collection text-primary"></i> Noticias Publicadas</h5>
          <span class="news-count"><?php echo mysqli_num_rows($noticias); ?> total</span>
        </div>

        <?php if (mysqli_num_rows($noticias) > 0): ?>
          <div style="max-height: 700px; overflow-y: auto;">
            <?php while ($n = mysqli_fetch_assoc($noticias)): ?>
              <div class="news-row">
                <img src="../imagenes/<?php echo htmlspecialchars($n['imagen']); ?>"
                     class="news-thumb" alt="Noticia"
                     onerror="this.src='https://via.placeholder.com/100x75/667eea/ffffff?text=N'">

                <div class="news-content">
                  <h6 title="<?php echo htmlspecialchars($n['nombre']); ?>">
                    <?php echo htmlspecialchars($n['nombre']); ?>
                  </h6>
                  <p><?php echo htmlspecialchars($n['comentario']); ?></p>
                  <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i>
                      <?php echo date('d/m/Y', strtotime($n['fecha_publicacion'])); ?>
                    </span>
                    <span><i class="bi bi-person"></i>
                      <?php echo htmlspecialchars($n['NAME'] ?? 'N/A'); ?>
                    </span>
                  </div>
                </div>

                <div class="news-actions">
                  <button class="btn-action edit" title="Editar"
                          onclick='abrirEditar(<?php echo json_encode($n, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action delete" title="Eliminar"
                          onclick="confirmarEliminar(<?php echo $n['cod_imagen']; ?>, '<?php echo htmlspecialchars(addslashes($n['nombre']), ENT_QUOTES); ?>')">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h6 class="mt-2">Aún no hay noticias</h6>
            <p class="mb-0" style="font-size: 0.85rem;">Publica la primera usando el formulario</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</main>

<!-- ============ MODAL EDITAR ============ -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px; border: none;">
      <form method="POST" action="actualizar_noticia.php" enctype="multipart/form-data">
        <div class="modal-header gradient" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border: none; border-radius: 14px 14px 0 0;">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Noticia</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="cod_imagen" id="edit_id">

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Título</label>
            <input type="text" name="nombre" id="edit_nombre" class="form-control" maxlength="150" required>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Comentario</label>
            <textarea name="comentario" id="edit_comentario" class="form-control" rows="4" maxlength="500" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Imagen actual</label>
            <img src="" id="edit_preview" style="width: 100%; max-height: 220px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border);">
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Cambiar imagen (opcional)</label>
            <input type="file" name="imagen" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="text-muted">Deja vacío para mantener la imagen actual</small>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--border);">
          <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn-gradient">
            <i class="bi bi-check-lg"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============ MODAL ELIMINAR ============ -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 14px; border: none;">
      <div class="modal-body text-center p-4">
        <div style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h5 class="fw-bold mb-2">¿Eliminar noticia?</h5>
        <p class="text-muted mb-4">
          Estás a punto de eliminar <strong id="eliminarNombre"></strong>.<br>
          Esta acción no se puede deshacer.
        </p>

        <form method="POST" action="eliminar_noticia.php" id="formEliminar">
          <input type="hidden" name="cod_imagen" id="eliminar_id">
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn-gradient" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
              <i class="bi bi-trash"></i> Sí, eliminar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <strong>Industria Canaima C.A.</strong> © <?php echo date('Y'); ?> — Todos los derechos reservados
  <br>
  <small class="text-muted">RIF: G-20010288-8</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // ============ CONTADOR ============
  document.getElementById('txtComentario').addEventListener('input', function() {
    document.getElementById('contador').textContent = this.value.length;
  });

  // ============ PREVIEW IMAGEN ============
  function previewImagen(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Validar tamaño (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('⚠️ La imagen es muy grande. Máximo 5MB.');
      e.target.value = '';
      return;
    }

    // Validar tipo
    const tipos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!tipos.includes(file.type)) {
      alert('⚠️ Formato no válido. Usa JPG, PNG, GIF o WEBP.');
      e.target.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = ev => {
      document.getElementById('preview').src = ev.target.result;
      document.getElementById('preview').classList.remove('d-none');
      document.getElementById('placeholder').classList.add('d-none');
      document.getElementById('btnRemove').classList.remove('d-none');
      document.getElementById('uploadArea').classList.add('has-image');
    };
    reader.readAsDataURL(file);
  }

  // ============ ELIMINAR PREVIEW ============
  function eliminarImagen(e) {
    e.stopPropagation();
    document.getElementById('inputImagen').value = '';
    document.getElementById('preview').classList.add('d-none');
    document.getElementById('preview').src = '';
    document.getElementById('placeholder').classList.remove('d-none');
    document.getElementById('btnRemove').classList.add('d-none');
    document.getElementById('uploadArea').classList.remove('has-image');
  }

  // ============ RESET ============
  function resetForm() {
    setTimeout(() => {
      document.getElementById('preview').classList.add('d-none');
      document.getElementById('preview').src = '';
      document.getElementById('placeholder').classList.remove('d-none');
      document.getElementById('btnRemove').classList.add('d-none');
      document.getElementById('uploadArea').classList.remove('has-image');
      document.getElementById('contador').textContent = '0';
    }, 10);
  }

  // ============ EDITAR ============
  function abrirEditar(noticia) {
    document.getElementById('edit_id').value         = noticia.cod_imagen;
    document.getElementById('edit_nombre').value     = noticia.nombre;
    document.getElementById('edit_comentario').value = noticia.comentario;
    document.getElementById('edit_preview').src      = '../imagenes/' + noticia.imagen;

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
  }

  // ============ ELIMINAR ============
  function confirmarEliminar(id, nombre) {
    document.getElementById('eliminar_id').value = id;
    document.getElementById('eliminarNombre').textContent = '"' + nombre + '"';
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
  }
</script>
</body>
</html>