<?php
include('../cone.php');
include('../permisos.php');
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

// Traer datos actualizados del usuario
$sqlUser = "SELECT * FROM user_datos WHERE IDDATOS = '$ID'";
$resUser = mysqli_query($conn, $sqlUser);
$U = mysqli_fetch_assoc($resUser);

$foto = $U['foto'] ?? 'images/Canaima.png';

// Estadísticas
$stats = [
    'reportes'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM report WHERE ID_NAME = '$ID'"))['total'],
    'resueltos'  => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM report WHERE ID_NAME = '$ID' AND STATUS = 4"))['total'],
    'pendientes' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM report WHERE ID_NAME = '$ID' AND STATUS = 3"))['total'],
];

// Nombre del rol
$roles = [1 => 'Administrador', 2 => 'Usuario', 3 => 'Técnico', 4 => 'RRHH'];
$nombreRol = $roles[$U['IDROLS']] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil | Industria Canaima</title>
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

    body {
      background: #f8fafc;
      color: var(--dark);
      margin: 0;
    }

    /* ============ HEADER ============ */
    .header {
      background: #fff;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      padding: 0 1.5rem;
      height: 70px;
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
    }

    .header .logo {
      display: flex; align-items: center; gap: 0.75rem;
      text-decoration: none; color: var(--dark);
      font-weight: 700; font-size: 1.1rem;
    }
    .header .logo img { height: 40px; }

    .header .search-form {
      flex: 1; max-width: 400px;
      margin: 0 2rem; position: relative;
    }
    .header .search-form input {
      width: 100%;
      padding: 0.6rem 1rem 0.6rem 2.75rem;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: var(--light);
      font-size: 0.9rem;
    }
    .header .search-form input:focus {
      outline: none; border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .header .search-form i {
      position: absolute; left: 1rem; top: 50%;
      transform: translateY(-50%); color: var(--gray);
    }

    .header .profile-btn {
      display: flex; align-items: center; gap: 0.75rem;
      padding: 0.4rem 0.75rem; border-radius: 10px;
      text-decoration: none; color: var(--dark);
      transition: background 0.2s;
    }
    .header .profile-btn:hover { background: var(--light); }
    .header .profile-btn img {
      width: 38px; height: 38px; border-radius: 50%;
      object-fit: cover; border: 2px solid var(--primary);
    }
    .header .profile-btn .info { display: flex; flex-direction: column; line-height: 1.2; }
    .header .profile-btn .info strong { font-size: 0.85rem; font-weight: 600; }
    .header .profile-btn .info small { font-size: 0.75rem; color: var(--gray); }

    /* ============ SIDEBAR ============ */
    .sidebar {
      position: fixed; top: 70px; left: 0; bottom: 0;
      width: 260px; background: #fff;
      border-right: 1px solid var(--border);
      overflow-y: auto;
      padding: 1.25rem 0.75rem;
      transition: transform 0.3s; z-index: 900;
    }
    .sidebar::-webkit-scrollbar { width: 6px; }
    .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav .nav-heading {
      font-size: 0.7rem; text-transform: uppercase;
      letter-spacing: 0.05em; color: var(--gray);
      padding: 0.75rem 0.75rem 0.5rem; font-weight: 600;
    }
    .sidebar-nav .nav-item { margin-bottom: 0.15rem; }
    .sidebar-nav .nav-link {
      display: flex; align-items: center; gap: 0.75rem;
      padding: 0.65rem 0.85rem;
      color: var(--gray); text-decoration: none;
      border-radius: 8px; font-size: 0.875rem;
      font-weight: 500; transition: all 0.2s;
    }
    .sidebar-nav .nav-link:hover { background: var(--light); color: var(--primary); }
    .sidebar-nav .nav-link.active {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: #fff;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .sidebar-nav .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; }

    /* ============ MAIN ============ */
    .main {
      margin-left: 260px; margin-top: 70px;
      padding: 1.75rem; min-height: calc(100vh - 70px);
    }

    /* ============ PAGE HEADER ============ */
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 {
      font-size: 1.75rem; font-weight: 700;
      margin-bottom: 0.25rem;
    }
    .page-header .breadcrumb {
      background: transparent; padding: 0; margin: 0;
      font-size: 0.875rem;
    }
    .page-header .breadcrumb a { color: var(--primary); text-decoration: none; }
    .page-header .breadcrumb-item.active { color: var(--gray); }

    /* ============ PROFILE HEADER ============ */
    .profile-header {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--border);
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 1.5rem;
    }

    .profile-banner {
      height: 160px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      position: relative;
    }
    .profile-banner::after {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .profile-info-block {
      padding: 0 2rem 1.75rem;
      position: relative;
    }

    .profile-avatar-wrapper {
      margin-top: -60px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .profile-avatar {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      border: 5px solid #fff;
      object-fit: cover;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      background: #fff;
    }

    .profile-details {
      flex: 1;
      min-width: 200px;
      padding-top: 1rem;
    }
    .profile-details h2 {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0 0 0.35rem;
    }
    .profile-details .role-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
      color: var(--primary);
      padding: 0.35rem 0.85rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      border: 1px solid rgba(102, 126, 234, 0.2);
    }
    .profile-details .area {
      color: var(--gray);
      font-size: 0.875rem;
      margin-top: 0.35rem;
    }

    /* ============ STATS MINI ============ */
    .mini-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      padding: 1.25rem 2rem;
      border-top: 1px solid var(--border);
      background: #fafbfc;
    }
    .mini-stat {
      text-align: center;
      padding: 0.75rem;
      border-radius: 10px;
      background: #fff;
      border: 1px solid var(--border);
    }
    .mini-stat h4 {
      font-size: 1.4rem;
      font-weight: 700;
      margin: 0 0 0.15rem;
      color: var(--primary);
    }
    .mini-stat span {
      font-size: 0.75rem;
      color: var(--gray);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      font-weight: 600;
    }

    /* ============ TABS ============ */
    .profile-tabs {
      background: #fff;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      overflow: hidden;
    }

    .nav-tabs-custom {
      display: flex;
      border-bottom: 1px solid var(--border);
      padding: 0 1rem;
      background: #fafbfc;
      overflow-x: auto;
    }
    .nav-tabs-custom .nav-link {
      border: none;
      background: transparent;
      color: var(--gray);
      font-weight: 600;
      font-size: 0.875rem;
      padding: 1rem 1.25rem;
      position: relative;
      white-space: nowrap;
      transition: color 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .nav-tabs-custom .nav-link:hover { color: var(--primary); }
    .nav-tabs-custom .nav-link.active {
      color: var(--primary);
      background: transparent;
    }
    .nav-tabs-custom .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: -1px;
      left: 1.25rem;
      right: 1.25rem;
      height: 2px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 2px 2px 0 0;
    }

    .tab-content-custom { padding: 2rem; }

    /* ============ FORM ============ */
    .form-label {
      font-weight: 600; font-size: 0.85rem;
      color: var(--dark); margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 0.7rem 1rem;
      font-size: 0.9rem;
      transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    .form-control:disabled {
      background: var(--light);
      cursor: not-allowed;
    }

    /* ============ INFO ROWS ============ */
    .info-row {
      display: grid;
      grid-template-columns: 180px 1fr;
      gap: 1rem;
      padding: 0.85rem 0;
      border-bottom: 1px solid var(--light);
      align-items: center;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--gray);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .info-row .value {
      font-size: 0.9rem;
      color: var(--dark);
      font-weight: 500;
    }

    /* ============ PASSWORD STRENGTH ============ */
    .password-strength {
      display: flex;
      gap: 0.25rem;
      margin-top: 0.5rem;
    }
    .password-strength .bar {
      flex: 1;
      height: 4px;
      background: var(--border);
      border-radius: 2px;
      transition: background 0.3s;
    }
    .password-strength .bar.active.weak   { background: var(--danger); }
    .password-strength .bar.active.medium { background: var(--warning); }
    .password-strength .bar.active.strong { background: var(--success); }

    .password-hint {
      font-size: 0.75rem;
      color: var(--gray);
      margin-top: 0.35rem;
    }

    /* ============ BUTTONS ============ */
    .btn-gradient {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 0.75rem 1.75rem;
      border-radius: 10px;
      font-size: 0.9rem;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-gradient:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
      color: #fff;
    }

    /* ============ AVATAR UPLOAD ============ */
    .avatar-upload {
      position: relative;
      display: inline-block;
    }
    .avatar-upload img {
      width: 100px; height: 100px; border-radius: 50%;
      object-fit: cover; border: 4px solid #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .avatar-upload .btn-camera {
      position: absolute;
      bottom: 0; right: 0;
      width: 32px; height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      border: 3px solid #fff;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 0.8rem;
      transition: transform 0.2s;
    }
    .avatar-upload .btn-camera:hover { transform: scale(1.1); }

    /* ============ FOOTER ============ */
    .footer {
      margin-left: 260px;
      padding: 1.5rem;
      text-align: center;
      color: var(--gray);
      font-size: 0.85rem;
      border-top: 1px solid var(--border);
      background: #fff;
    }

    /* ============ RESPONSIVE ============ */
    .toggle-sidebar {
      display: none;
      background: transparent; border: none;
      font-size: 1.5rem; color: var(--dark); cursor: pointer;
    }

    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.show { transform: translateX(0); box-shadow: 0 0 30px rgba(0,0,0,0.15); }
      .main, .footer { margin-left: 0; }
      .toggle-sidebar { display: block; }
      .header .search-form { display: none; }
      .info-row { grid-template-columns: 1fr; gap: 0.25rem; }
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
      <li><a class="dropdown-item py-2" href="#"><i class="bi bi-question-circle me-2"></i> Ayuda</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item py-2 text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
    </ul>
  </div>
</header>

<?php include 'include/sidebar.php'; ?>

<!-- ============ MAIN ============ -->
<main class="main">

  <!-- Page Header -->
  <div class="page-header">
    <h1>Mi Perfil</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item active">Perfil</li>
      </ol>
    </nav>
  </div>

  <!-- Profile Header Card -->
  <div class="profile-header">
    <div class="profile-banner"></div>
    <div class="profile-info-block">
      <div class="profile-avatar-wrapper">
        <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar" class="profile-avatar">
        <div class="profile-details">
          <h2><?php echo htmlspecialchars($NAME . ' ' . $APE); ?></h2>
          <span class="role-badge">
            <i class="bi bi-award-fill"></i> <?php echo htmlspecialchars($nombreRol); ?>
          </span>
          <div class="area"><i class="bi bi-building me-1"></i><?php echo htmlspecialchars($area); ?></div>
        </div>
      </div>
    </div>

    <!-- Mini stats -->
    <div class="mini-stats">
      <div class="mini-stat">
        <h4><?php echo $stats['reportes']; ?></h4>
        <span>Reportes</span>
      </div>
      <div class="mini-stat">
        <h4><?php echo $stats['resueltos']; ?></h4>
        <span>Resueltos</span>
      </div>
      <div class="mini-stat">
        <h4><?php echo $stats['pendientes']; ?></h4>
        <span>Pendientes</span>
      </div>
    </div>
  </div>

  <!-- Tabs Card -->
  <div class="profile-tabs">
    <div class="nav-tabs-custom" role="tablist">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">
        <i class="bi bi-person-vcard"></i> Información
      </button>
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edit" type="button">
        <i class="bi bi-pencil-square"></i> Editar Perfil
      </button>
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-password" type="button">
        <i class="bi bi-shield-lock"></i> Cambiar Contraseña
      </button>
    </div>

    <div class="tab-content">
      <!-- ============ TAB INFO ============ -->
      <div class="tab-pane fade show active tab-content-custom" id="tab-info">
        <h5 class="mb-3" style="font-weight: 700;">Información Personal</h5>

        <div class="info-row">
          <div class="label"><i class="bi bi-person"></i> Nombre Completo</div>
          <div class="value"><?php echo htmlspecialchars($U['NAME'] . ' ' . $U['SURNAME']); ?></div>
        </div>
        <div class="info-row">
          <div class="label"><i class="bi bi-credit-card"></i> Cédula</div>
          <div class="value"><?php echo htmlspecialchars($U['CEDULA']); ?></div>
        </div>
        <div class="info-row">
          <div class="label"><i class="bi bi-at"></i> Usuario</div>
          <div class="value"><?php echo htmlspecialchars($U['USER']); ?></div>
        </div>
        <div class="info-row">
          <div class="label"><i class="bi bi-envelope"></i> Correo</div>
          <div class="value"><?php echo htmlspecialchars($U['EMAIL']); ?></div>
        </div>
        <div class="info-row">
          <div class="label"><i class="bi bi-telephone"></i> Teléfono</div>
          <div class="value"><?php echo htmlspecialchars($U['telefono']); ?></div>
        </div>
        <div class="info-row">
          <div class="label"><i class="bi bi-building"></i> Área</div>
          <div class="value"><?php echo htmlspecialchars($U['ASSIGNED_AREA']); ?></div>
        </div>
        <div class="info-row">
          <div class="label"><i class="bi bi-shield-check"></i> Rol</div>
          <div class="value"><?php echo htmlspecialchars($nombreRol); ?></div>
        </div>
      </div>

      <!-- ============ TAB EDIT ============ -->
      <div class="tab-pane fade tab-content-custom" id="tab-edit">
        <h5 class="mb-4" style="font-weight: 700;">Editar Información</h5>

        <form method="POST" action="actualizar_perfil.php" enctype="multipart/form-data">
          <div class="row g-4 mb-4">
            <div class="col-md-4 text-center">
              <label class="form-label d-block mb-3">Foto de Perfil</label>
              <div class="avatar-upload">
                <img src="<?php echo htmlspecialchars($foto); ?>" id="previewFoto" alt="Avatar">
                <label for="inputFoto" class="btn-camera" title="Cambiar foto">
                  <i class="bi bi-camera-fill"></i>
                </label>
                <input type="file" id="inputFoto" name="foto" accept="image/*" hidden onchange="previewImage(event)">
              </div>
              <p class="text-muted small mt-2 mb-0">JPG, PNG o GIF · Máx 2MB</p>
            </div>

            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($U['NAME']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Apellido</label>
                  <input type="text" class="form-control" name="surname" value="<?php echo htmlspecialchars($U['SURNAME']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Correo Electrónico</label>
                  <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($U['EMAIL']); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Teléfono</label>
                  <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($U['telefono']); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Cédula</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($U['CEDULA']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Usuario</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($U['USER']); ?>" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="text-end border-top pt-3">
            <button type="reset" class="btn btn-light me-2" style="border-radius:10px;">Cancelar</button>
            <button type="submit" class="btn-gradient">
              <i class="bi bi-check-lg"></i> Guardar Cambios
            </button>
          </div>
        </form>
      </div>

      <!-- ============ TAB PASSWORD ============ -->
      <div class="tab-pane fade tab-content-custom" id="tab-password">
        <h5 class="mb-4" style="font-weight: 700;">Cambiar Contraseña</h5>

        <form method="POST" action="cambiar_password.php" id="formPass">
          <div class="row g-3" style="max-width: 500px;">

            <div class="col-12">
              <label class="form-label">Contraseña Actual</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" class="form-control" name="password_actual" required>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Nueva Contraseña</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-key text-muted"></i></span>
                <input type="password" class="form-control" name="password_nueva" id="nueva" required oninput="medirFuerza(this.value)">
              </div>
              <div class="password-strength">
                <div class="bar" id="bar1"></div>
                <div class="bar" id="bar2"></div>
                <div class="bar" id="bar3"></div>
                <div class="bar" id="bar4"></div>
              </div>
              <div class="password-hint" id="hintPass">Mínimo 8 caracteres, una mayúscula y un número</div>
            </div>

            <div class="col-12">
              <label class="form-label">Confirmar Nueva Contraseña</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-key-fill text-muted"></i></span>
                <input type="password" class="form-control" name="password_confirmar" id="confirmar" required oninput="validarCoincidencia()">
              </div>
              <div class="password-hint" id="hintMatch"></div>
            </div>

            <div class="col-12 text-end border-top pt-3 mt-3">
              <button type="submit" class="btn-gradient">
                <i class="bi bi-shield-check"></i> Actualizar Contraseña
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

</main>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <strong>Industria Canaima C.A.</strong> © <?php echo date('Y'); ?> — Todos los derechos reservados
  <br>
  <small class="text-muted">RIF: G-20010288-8</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Preview imagen antes de subir
  function previewImage(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = ev => document.getElementById('previewFoto').src = ev.target.result;
      reader.readAsDataURL(file);
    }
  }

  // Medidor de fuerza de contraseña
  function medirFuerza(pass) {
    let score = 0;
    if (pass.length >= 8) score++;
    if (/[A-Z]/.test(pass)) score++;
    if (/[0-9]/.test(pass)) score++;
    if (/[^A-Za-z0-9]/.test(pass)) score++;

    const bars = ['bar1', 'bar2', 'bar3', 'bar4'];
    bars.forEach(id => document.getElementById(id).className = 'bar');

    let nivel = '';
    for (let i = 0; i < score; i++) {
      document.getElementById(bars[i]).classList.add('active');
    }
    if (score <= 2) nivel = 'weak';
    else if (score === 3) nivel = 'medium';
    else nivel = 'strong';

    bars.slice(0, score).forEach(id => document.getElementById(id).classList.add(nivel));

    const hint = document.getElementById('hintPass');
    if (score <= 2) { hint.textContent = '⚠️ Contraseña débil'; hint.style.color = '#ef4444'; }
    else if (score === 3) { hint.textContent = '🟡 Contraseña aceptable'; hint.style.color = '#f59e0b'; }
    else { hint.textContent = '✅ Contraseña fuerte'; hint.style.color = '#10b981'; }
  }

  function validarCoincidencia() {
    const n = document.getElementById('nueva').value;
    const c = document.getElementById('confirmar').value;
    const hint = document.getElementById('hintMatch');
    if (!c) { hint.textContent = ''; return; }
    if (n === c) { hint.textContent = '✅ Las contraseñas coinciden'; hint.style.color = '#10b981'; }
    else { hint.textContent = '❌ Las contraseñas no coinciden'; hint.style.color = '#ef4444'; }
  }
</script>
</body>
</html>