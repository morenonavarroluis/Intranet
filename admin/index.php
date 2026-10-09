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
<?php include('include/head.php'); ?>
<body>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>
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

<?php include('include/footer.php'); ?>