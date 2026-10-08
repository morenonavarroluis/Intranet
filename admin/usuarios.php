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

// Solo administradores y RRHH
if ($ROL != 1 && $ROL != 4) {
    header("Location: index.php");
    exit;
}

// Foto usuario actual
$consulta = mysqli_query($conn, "SELECT CEDULA, foto FROM user_datos WHERE CEDULA = '$CEDULA'");
$valores  = mysqli_fetch_array($consulta);
$foto     = $valores['foto'] ?? 'images/Canaima.png';

// ==================== FILTROS ====================
$filtroRol    = $_GET['rol']    ?? '';
$filtroArea   = $_GET['area']   ?? '';
$busqueda     = trim($_GET['q'] ?? '');

$where = [];
if ($filtroRol !== '')  $where[] = "u.IDROLS = " . intval($filtroRol);
if ($filtroArea !== '') $where[] = "u.ASSIGNED_AREA = '" . mysqli_real_escape_string($conn, $filtroArea) . "'";
if ($busqueda !== '') {
    $b = mysqli_real_escape_string($conn, $busqueda);
    $where[] = "(u.NAME LIKE '%$b%' OR u.SURNAME LIKE '%$b%' OR u.USER LIKE '%$b%' OR u.CEDULA LIKE '%$b%' OR u.EMAIL LIKE '%$b%')";
}
$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ==================== CONSULTA ====================
$sql = "SELECT u.*, r.NOMBRE_ROL 
        FROM user_datos u
        LEFT JOIN roles r ON u.IDROLS = r.IDROLS
        $whereSQL
        ORDER BY u.IDDATOS DESC";
$usuarios = mysqli_query($conn, $sql);

// ==================== ESTADÍSTICAS ====================
$stats = [
    'total'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM user_datos"))['t'],
    'admin'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM user_datos WHERE IDROLS = 1"))['t'],
    'tecnico' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM user_datos WHERE IDROLS = 3"))['t'],
    'rrhh'    => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM user_datos WHERE IDROLS = 4"))['t'],
];

// Áreas únicas para filtro
$areasUnicas = mysqli_query($conn, "SELECT DISTINCT ASSIGNED_AREA FROM user_datos WHERE ASSIGNED_AREA IS NOT NULL AND ASSIGNED_AREA != '' ORDER BY ASSIGNED_AREA");

// Lista de áreas (para select del formulario)
$areasFormulario = [
    'Presidencia', 'Proyecto', 'Consultoria Juridica',
    'Planificación y Presupuesto', 'Gestion Humana', 'Procura',
    'Administración y Finanzas', 'Tic', 'Atencion al ciudadano',
    'Comercializacion', 'Seguridad', 'Seguridad Integral', 'Producción'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Usuarios | Industria Canaima</title>
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
      padding: 0 1.5rem; height: 70px;
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
    .page-header { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; }
    .page-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
    .page-header .breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
    .page-header .breadcrumb a { color: var(--primary); text-decoration: none; }
    .page-header .breadcrumb-item.active { color: var(--gray); }

    /* ============ STATS GRID ============ */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card { background: #fff; border-radius: 14px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid var(--border); transition: all 0.3s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .stat-card .icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; flex-shrink: 0; }
    .stat-card .icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-card .icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card .icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card .icon.info    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-card .info h4 { font-size: 1.5rem; font-weight: 700; margin: 0; color: var(--dark); }
    .stat-card .info span { font-size: 0.8rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.03em; font-weight: 500; }

    /* ============ BUTTONS ============ */
    .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: #fff; font-weight: 600; padding: 0.65rem 1.25rem; border-radius: 10px; font-size: 0.875rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); color: #fff; }
    .btn-clear { background: #fff; border: 1.5px solid var(--border); color: var(--gray); border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
    .btn-clear:hover { background: var(--light); color: var(--dark); }

    /* ============ FILTERS ============ */
    .filters-card { background: #fff; border-radius: 14px; border: 1px solid var(--border); padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .filters-card .form-control, .filters-card .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 0.6rem 1rem; font-size: 0.875rem; }
    .filters-card .form-control:focus, .filters-card .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    .filters-card label { font-size: 0.75rem; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem; }

    /* ============ TABLE ============ */
    .table-card { background: #fff; border-radius: 14px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .table-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
    .table-card-header h5 { font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem; }

    .table-custom { width: 100%; margin: 0; }
    .table-custom thead th { background: #fafbfc; border-bottom: 1px solid var(--border); padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
    .table-custom tbody td { padding: 1rem 1.25rem; border-bottom: 1px solid var(--light); font-size: 0.875rem; vertical-align: middle; }
    .table-custom tbody tr { transition: background 0.15s; }
    .table-custom tbody tr:hover { background: #fafbfc; }
    .table-custom tbody tr:last-child td { border-bottom: none; }

    .user-cell { display: flex; align-items: center; gap: 0.75rem; }
    .user-cell img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
    .user-cell .user-info { line-height: 1.3; }
    .user-cell .user-info strong { font-size: 0.875rem; font-weight: 600; display: block; }
    .user-cell .user-info small { font-size: 0.75rem; color: var(--gray); }

    /* ============ BADGES ============ */
    .badge-role { font-size: 0.7rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.04em; display: inline-block; }
    .badge-admin   { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
    .badge-user    { background: #dbeafe; color: #1e40af; }
    .badge-tecnico { background: #fef3c7; color: #92400e; }
    .badge-rrhh    { background: #d1fae5; color: #065f46; }

    /* ============ ACTION BUTTONS ============ */
    .btn-action { width: 32px; height: 32px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.2s; cursor: pointer; }
    .btn-action.edit   { background: #eff6ff; color: #1e40af; }
    .btn-action.edit:hover   { background: #3b82f6; color: #fff; }
    .btn-action.key    { background: #fef3c7; color: #92400e; }
    .btn-action.key:hover    { background: #f59e0b; color: #fff; }
    .btn-action.delete { background: #fee2e2; color: #991b1b; }
    .btn-action.delete:hover { background: #ef4444; color: #fff; }

    /* ============ EMPTY STATE ============ */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray); }
    .empty-state i { font-size: 3.5rem; opacity: 0.3; display: block; margin-bottom: 1rem; }

    /* ============ MODAL ============ */
    .modal-content { border-radius: 14px; border: none; }
    .modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .modal-header.gradient { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border: none; border-radius: 14px 14px 0 0; }
    .modal-header.gradient .btn-close { filter: invert(1); }
    .modal-body { padding: 1.5rem; }
    .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }

    .form-label { font-weight: 600; font-size: 0.85rem; color: var(--dark); margin-bottom: 0.4rem; }
    .form-label .required { color: var(--danger); }
    .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 0.7rem 1rem; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
    .form-control:disabled { background: var(--light); }

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
      .table-custom { display: block; overflow-x: auto; white-space: nowrap; }
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
      <a class="nav-link" href="cargar_noticia.php"><i class="bi bi-newspaper"></i><span>Cargar Noticia</span></a>
    </li>

    <li class="nav-heading">Administración</li>
    <li class="nav-item">
      <a class="nav-link active" href="usuarios.php"><i class="bi bi-people-fill"></i><span>Gestión de Usuarios</span></a>
    </li>
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
    <div>
      <h1>Gestión de Usuarios</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item active">Usuarios</li>
        </ol>
      </nav>
    </div>
    <button class="btn-gradient" onclick="abrirCrear()">
      <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
    </button>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="icon primary"><i class="bi bi-people-fill"></i></div>
      <div class="info">
        <h4><?php echo $stats['total']; ?></h4>
        <span>Total</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="icon warning"><i class="bi bi-shield-fill-check"></i></div>
      <div class="info">
        <h4><?php echo $stats['admin']; ?></h4>
        <span>Admins</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="icon info"><i class="bi bi-wrench-adjustable"></i></div>
      <div class="info">
        <h4><?php echo $stats['tecnico']; ?></h4>
        <span>Técnicos</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="icon success"><i class="bi bi-person-badge-fill"></i></div>
      <div class="info">
        <h4><?php echo $stats['rrhh']; ?></h4>
        <span>RRHH</span>
      </div>
    </div>
  </div>

  <!-- Filtros -->
  <div class="filters-card">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label>Buscar</label>
        <input type="text" name="q" class="form-control" placeholder="Nombre, usuario, cédula o correo..."
               value="<?php echo htmlspecialchars($busqueda); ?>">
      </div>
      <div class="col-md-2">
        <label>Rol</label>
        <select name="rol" class="form-select">
          <option value="">Todos</option>
          <option value="1" <?php echo $filtroRol === '1' ? 'selected' : ''; ?>>Administrador</option>
          <option value="2" <?php echo $filtroRol === '2' ? 'selected' : ''; ?>>Usuario</option>
          <option value="3" <?php echo $filtroRol === '3' ? 'selected' : ''; ?>>Técnico</option>
          <option value="4" <?php echo $filtroRol === '4' ? 'selected' : ''; ?>>RRHH</option>
        </select>
      </div>
      <div class="col-md-3">
        <label>Área</label>
        <select name="area" class="form-select">
          <option value="">Todas</option>
          <?php while ($a = mysqli_fetch_assoc($areasUnicas)): ?>
            <option value="<?php echo htmlspecialchars($a['ASSIGNED_AREA']); ?>"
                    <?php echo $filtroArea === $a['ASSIGNED_AREA'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($a['ASSIGNED_AREA']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn-gradient flex-grow-1 justify-content-center">
          <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="usuarios.php" class="btn-clear"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>

  <!-- Tabla -->
  <div class="table-card">
    <div class="table-card-header">
      <h5><i class="bi bi-people text-primary"></i> Usuarios Registrados</h5>
      <span class="text-muted" style="font-size: 0.85rem;">
        <?php echo mysqli_num_rows($usuarios); ?> resultados
      </span>
    </div>

    <?php if (mysqli_num_rows($usuarios) > 0): ?>
      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Cédula</th>
              <th>Contacto</th>
              <th>Área</th>
              <th>Rol</th>
              <th style="text-align:center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($u = mysqli_fetch_assoc($usuarios)):
              $badgeRole = 'badge-user';
              switch ((int)$u['IDROLS']) {
                case 1: $badgeRole = 'badge-admin'; break;
                case 2: $badgeRole = 'badge-user'; break;
                case 3: $badgeRole = 'badge-tecnico'; break;
                case 4: $badgeRole = 'badge-rrhh'; break;
              }
            ?>
              <tr>
                <td>
                  <div class="user-cell">
                    <img src="<?php echo htmlspecialchars($u['foto'] ?? 'images/Canaima.png'); ?>"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($u['NAME'] . '+' . $u['SURNAME']); ?>&background=667eea&color=fff'"
                         alt="Avatar">
                    <div class="user-info">
                      <strong><?php echo htmlspecialchars($u['NAME'] . ' ' . $u['SURNAME']); ?></strong>
                      <small>@<?php echo htmlspecialchars($u['USER']); ?></small>
                    </div>
                  </div>
                </td>
                <td><?php echo htmlspecialchars($u['CEDULA']); ?></td>
                <td>
                  <div style="font-size: 0.8rem;">
                    <div><i class="bi bi-envelope me-1 text-muted"></i><?php echo htmlspecialchars($u['EMAIL'] ?? '—'); ?></div>
                    <div><i class="bi bi-telephone me-1 text-muted"></i><?php echo htmlspecialchars($u['telefono'] ?? '—'); ?></div>
                  </div>
                </td>
                <td><span style="font-size: 0.8rem; color: var(--gray);"><?php echo htmlspecialchars($u['ASSIGNED_AREA'] ?? '—'); ?></span></td>
                <td>
                  <span class="badge-role <?php echo $badgeRole; ?>">
                    <?php echo htmlspecialchars($u['NOMBRE_ROL'] ?? 'Usuario'); ?>
                  </span>
                </td>
                <td style="text-align:center;">
                  <button class="btn-action edit" title="Editar"
                          onclick='abrirEditar(<?php echo json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action key" title="Cambiar contraseña"
                          onclick='abrirPassword(<?php echo $u['IDDATOS']; ?>, "<?php echo htmlspecialchars(addslashes($u["NAME"] . " " . $u["SURNAME"]), ENT_QUOTES); ?>")'>
                    <i class="bi bi-key"></i>
                  </button>
                  <?php if ($u['IDDATOS'] != $ID): ?>
                    <button class="btn-action delete" title="Eliminar"
                            onclick="confirmarEliminar(<?php echo $u['IDDATOS']; ?>, '<?php echo htmlspecialchars(addslashes($u["NAME"] . " " . $u["SURNAME"]), ENT_QUOTES); ?>')">
                      <i class="bi bi-trash"></i>
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-people"></i>
        <h5 class="mt-3">No se encontraron usuarios</h5>
        <p class="mb-0">Prueba ajustando los filtros o la búsqueda</p>
      </div>
    <?php endif; ?>
  </div>

</main>

<!-- ============ MODAL CREAR/EDITAR ============ -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="guardar_usuario.php" id="formUsuario" enctype="multipart/form-data">
        <div class="modal-header gradient">
          <h5 class="modal-title" id="modalTitulo">
            <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="f_id">
          <input type="hidden" name="accion" id="f_accion" value="crear">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre <span class="required">*</span></label>
              <input type="text" class="form-control" name="name" id="f_name" required maxlength="50">
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellido <span class="required">*</span></label>
              <input type="text" class="form-control" name="surname" id="f_surname" required maxlength="50">
            </div>

            <div class="col-md-6">
              <label class="form-label">Cédula <span class="required">*</span></label>
              <input type="text" class="form-control" name="cedula" id="f_cedula" required maxlength="20" placeholder="V-12345678">
            </div>
            <div class="col-md-6">
              <label class="form-label">Usuario <span class="required">*</span></label>
              <input type="text" class="form-control" name="user" id="f_user" required maxlength="8">
              <small class="text-muted">Máximo 8 caracteres</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" class="form-control" name="email" id="f_email" maxlength="100">
            </div>
            <div class="col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" class="form-control" name="telefono" id="f_telefono" maxlength="20">
            </div>

            <div class="col-md-6">
              <label class="form-label">Área <span class="required">*</span></label>
              <select class="form-select" name="area" id="f_area" required>
                <option value="">— Seleccionar —</option>
                <?php foreach ($areasFormulario as $a): ?>
                  <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rol <span class="required">*</span></label>
              <select class="form-select" name="rol" id="f_rol" required>
                <option value="1">Administrador</option>
                <option value="2" selected>Usuario</option>
                <option value="3">Técnico</option>
                <option value="4">RRHH</option>
              </select>
            </div>

            <!-- Solo visible al crear -->
            <div class="col-md-6" id="grupoPass">
              <label class="form-label">Contraseña <span class="required">*</span></label>
              <input type="password" class="form-control" name="password" id="f_password" minlength="6" maxlength="20">
              <small class="text-muted">Mínimo 6 caracteres</small>
            </div>
            <div class="col-md-6" id="grupoPass2">
              <label class="form-label">Confirmar Contraseña <span class="required">*</span></label>
              <input type="password" class="form-control" name="password2" id="f_password2" minlength="6" maxlength="20">
            </div>

            <div class="col-md-6">
              <label class="form-label">Foto (opcional)</label>
              <input type="file" class="form-control" name="foto" accept="image/*">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-clear" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn-gradient">
            <i class="bi bi-check-lg"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============ MODAL PASSWORD ============ -->
<div class="modal fade" id="modalPassword" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="cambiar_password_admin.php" id="formPassAdmin">
        <div class="modal-header gradient">
          <h5 class="modal-title"><i class="bi bi-key-fill me-2"></i> Cambiar Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="p_id">

          <div class="text-center mb-3">
            <p class="text-muted mb-0">Cambiando contraseña de:</p>
            <strong id="p_nombre" style="font-size: 1rem;"></strong>
          </div>

          <div class="mb-3">
            <label class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" name="password" id="p_pass" required minlength="6" maxlength="20">
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" name="password2" id="p_pass2" required minlength="6" maxlength="20">
          </div>

          <div class="alert alert-warning mb-0" style="border-radius: 10px; font-size: 0.85rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            El usuario deberá usar la nueva contraseña en su próximo inicio de sesión.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-clear" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn-gradient">
            <i class="bi bi-shield-check"></i> Actualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============ MODAL ELIMINAR ============ -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <div style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h5 class="fw-bold mb-2">¿Eliminar usuario?</h5>
        <p class="text-muted mb-4">
          Estás a punto de eliminar a <strong id="eliminarNombre"></strong>.<br>
          Esta acción no se puede deshacer.
        </p>

        <form method="POST" action="eliminar_usuario.php">
          <input type="hidden" name="id" id="eliminar_id">
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn-clear" data-bs-dismiss="modal">Cancelar</button>
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
  // ============ CREAR ============
  function abrirCrear() {
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario';
    document.getElementById('formUsuario').reset();
    document.getElementById('f_id').value = '';
    document.getElementById('f_accion').value = 'crear';
    document.getElementById('f_cedula').disabled = false;
    document.getElementById('f_user').disabled = false;
    document.getElementById('grupoPass').style.display = '';
    document.getElementById('grupoPass2').style.display = '';
    document.getElementById('f_password').required = true;
    document.getElementById('f_password2').required = true;

    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
  }

  // ============ EDITAR ============
  function abrirEditar(u) {
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Editar Usuario';
    document.getElementById('f_id').value       = u.IDDATOS;
    document.getElementById('f_accion').value   = 'editar';
    document.getElementById('f_name').value     = u.NAME;
    document.getElementById('f_surname').value  = u.SURNAME;
    document.getElementById('f_cedula').value   = u.CEDULA;
    document.getElementById('f_user').value     = u.USER;
    document.getElementById('f_email').value    = u.EMAIL || '';
    document.getElementById('f_telefono').value = u.telefono || '';
    document.getElementById('f_area').value     = u.ASSIGNED_AREA || '';
    document.getElementById('f_rol').value      = u.IDROLS;

    // En edición, cédula y usuario no se cambian
    document.getElementById('f_cedula').disabled = true;
    document.getElementById('f_user').disabled = true;

    // Ocultar campos de contraseña
    document.getElementById('grupoPass').style.display = 'none';
    document.getElementById('grupoPass2').style.display = 'none';
    document.getElementById('f_password').required = false;
    document.getElementById('f_password2').required = false;

    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
  }

  // ============ CAMBIAR PASSWORD ============
  function abrirPassword(id, nombre) {
    document.getElementById('p_id').value = id;
    document.getElementById('p_nombre').textContent = nombre;
    document.getElementById('formPassAdmin').reset();
    new bootstrap.Modal(document.getElementById('modalPassword')).show();
  }

  // ============ ELIMINAR ============
  function confirmarEliminar(id, nombre) {
    document.getElementById('eliminar_id').value = id;
    document.getElementById('eliminarNombre').textContent = nombre;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
  }

  // ============ VALIDAR PASSWORDS ============
  document.getElementById('formUsuario').addEventListener('submit', function(e) {
    const p1 = document.getElementById('f_password');
    const p2 = document.getElementById('f_password2');
    if (p1.value && p1.value !== p2.value) {
      e.preventDefault();
      alert('⚠️ Las contraseñas no coinciden');
    }
  });

  document.getElementById('formPassAdmin').addEventListener('submit', function(e) {
    const p1 = document.getElementById('p_pass');
    const p2 = document.getElementById('p_pass2');
    if (p1.value !== p2.value) {
      e.preventDefault();
      alert('⚠️ Las contraseñas no coinciden');
    }
  });
</script>
</body>
</html>