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

// Solo técnicos (3) y admins (1) pueden gestionar
if ($ROL != 1 && $ROL != 3) {
    header("Location: index.php");
    exit;
}

// Foto usuario
$consulta = mysqli_query($conn, "SELECT CEDULA, foto FROM user_datos WHERE CEDULA = '$CEDULA'");
$valores  = mysqli_fetch_array($consulta);
$foto     = $valores['foto'] ?? 'images/Canaima.png';

// ==================== FILTROS ====================
$filtroStatus  = $_GET['status']   ?? '';
$filtroNivel   = $_GET['nivel']    ?? '';
$filtroArea    = $_GET['area']     ?? '';
$busqueda      = trim($_GET['q']   ?? '');

// ==================== CONSULTA PRINCIPAL ====================
$where = [];
if ($filtroStatus !== '') $where[] = "r.STATUS = " . intval($filtroStatus);
if ($filtroNivel !== '')  $where[] = "r.ID_LEVEL = " . intval($filtroNivel);
if ($filtroArea !== '')   $where[] = "r.area = " . intval($filtroArea);
if ($busqueda !== '') {
    $busquedaSafe = mysqli_real_escape_string($conn, $busqueda);
    $where[] = "(r.TITLE LIKE '%$busquedaSafe%' OR r.name_surname LIKE '%$busquedaSafe%')";
}

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT r.ID_REPORT, r.TITLE, r.name_surname, r.area, r.ID_NAME,
               r.CREATION_DATE, r.FECHA_SOLUTION, r.STATUS, r.ID_LEVEL, r.SOLUTION,
               s.nombre_status,
               n.nombre_nivel,
               a.nombre_area,
               u.USER, u.NAME, u.SURNAME, u.EMAIL, u.telefono
        FROM report r
        LEFT JOIN status_report s ON r.STATUS = s.id_status
        LEFT JOIN niveles n ON r.ID_LEVEL = n.id_nivel
        LEFT JOIN areas a ON r.area = a.id_area
        LEFT JOIN user_datos u ON r.ID_NAME = u.IDDATOS
        $whereSQL
        ORDER BY 
            CASE r.STATUS 
                WHEN 3 THEN 1
                WHEN 2 THEN 2
                WHEN 4 THEN 3
                WHEN 5 THEN 4
                WHEN 6 THEN 5
                ELSE 6
            END,
            CASE r.ID_LEVEL WHEN 1 THEN 1 WHEN 2 THEN 2 ELSE 3 END,
            r.CREATION_DATE DESC";

$resultado = mysqli_query($conn, $sql);

// ==================== ESTADÍSTICAS ====================
$stats = [
    'total'     => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM report"))['t'],
    'pendiente' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM report WHERE STATUS = 3"))['t'],
    'proceso'   => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM report WHERE STATUS = 2"))['t'],
    'resuelto'  => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM report WHERE STATUS = 4"))['t'],
];

// Áreas para filtro
$areas = mysqli_query($conn, "SELECT id_area, nombre_area FROM areas ORDER BY nombre_area");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Casos | Industria Canaima</title>
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
    .stat-card .icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card .icon.info    { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-card .icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card .info h4 { font-size: 1.5rem; font-weight: 700; margin: 0; color: var(--dark); }
    .stat-card .info span { font-size: 0.8rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.03em; font-weight: 500; }

    /* ============ FILTERS ============ */
    .filters-card { background: #fff; border-radius: 14px; border: 1px solid var(--border); padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .filters-card .form-control, .filters-card .form-select { border: 1.5px solid var(--border); border-radius: 10px; padding: 0.6rem 1rem; font-size: 0.875rem; transition: all 0.2s; }
    .filters-card .form-control:focus, .filters-card .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
    .filters-card label { font-size: 0.75rem; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.4rem; }
    .btn-filter { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border: none; border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600; font-size: 0.875rem; transition: all 0.3s; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(102, 126, 234, 0.35); color: #fff; }
    .btn-clear { background: #fff; border: 1.5px solid var(--border); color: var(--gray); border-radius: 10px; padding: 0.6rem 1.25rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
    .btn-clear:hover { background: var(--light); color: var(--dark); }

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

    .table-custom .case-title { font-weight: 600; color: var(--dark); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .table-custom .case-user { font-size: 0.8rem; color: var(--gray); margin-top: 0.15rem; }
    .table-custom .case-id { font-family: 'Courier New', monospace; font-size: 0.8rem; background: var(--light); padding: 0.15rem 0.5rem; border-radius: 6px; color: var(--gray); font-weight: 600; }

    /* ============ BADGES ============ */
    .badge-status { font-size: 0.7rem; font-weight: 700; padding: 0.4rem 0.75rem; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; display: inline-flex; align-items: center; gap: 0.3rem; }
    .badge-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .badge-pendiente { background: #fef3c7; color: #92400e; }
    .badge-proceso   { background: #dbeafe; color: #1e40af; }
    .badge-resuelto  { background: #d1fae5; color: #065f46; }
    .badge-cerrado   { background: #e5e7eb; color: #374151; }
    .badge-rechazado { background: #fee2e2; color: #991b1b; }

    .badge-nivel { font-size: 0.7rem; font-weight: 600; padding: 0.3rem 0.65rem; border-radius: 6px; text-transform: uppercase; }
    .badge-nivel.alta  { background: #fee2e2; color: #991b1b; }
    .badge-nivel.media { background: #fef3c7; color: #92400e; }
    .badge-nivel.baja  { background: #e0e7ff; color: #3730a3; }

    /* ============ ACTION BUTTONS ============ */
    .btn-action { width: 32px; height: 32px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.2s; cursor: pointer; }
    .btn-action.view   { background: #eff6ff; color: #1e40af; }
    .btn-action.view:hover   { background: #3b82f6; color: #fff; }
    .btn-action.edit   { background: #fef3c7; color: #92400e; }
    .btn-action.edit:hover   { background: #f59e0b; color: #fff; }

    /* ============ EMPTY STATE ============ */
    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray); }
    .empty-state i { font-size: 3.5rem; opacity: 0.3; display: block; margin-bottom: 1rem; }

    /* ============ MODAL ============ */
    .modal-content { border-radius: 14px; border: none; }
    .modal-header { border-radius: 14px 14px 0 0; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .modal-header.gradient { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; border: none; }
    .modal-header.gradient .btn-close { filter: invert(1); }
    .modal-body { padding: 1.5rem; }
    .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }

    .detail-row { display: grid; grid-template-columns: 140px 1fr; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid var(--light); }
    .detail-row:last-child { border-bottom: none; }
    .detail-row .label { font-size: 0.75rem; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.03em; }
    .detail-row .value { font-size: 0.9rem; color: var(--dark); }

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
      <a class="nav-link" href="Constancia_de_trabajo.php?edi=<?php echo $ID; ?>"><i class="bi bi-file-earmark-text"></i><span>Constancia de Trabajo</span></a>
    </li>

    <li class="nav-heading">Administración</li>
    <li class="nav-item">
      <a class="nav-link active" href="caso_soporte.php"><i class="bi bi-ticket-detailed"></i><span>Casos de Soporte</span></a>
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
      <h1>Gestión de Casos de Soporte</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item active">Casos de Soporte</li>
        </ol>
      </nav>
    </div>
    <a href="exportar_casos.php" class="btn-filter">
      <i class="bi bi-download"></i> Exportar CSV
    </a>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="icon primary"><i class="bi bi-ticket-detailed"></i></div>
      <div class="info">
        <h4><?php echo $stats['total']; ?></h4>
        <span>Total</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="icon warning"><i class="bi bi-hourglass-split"></i></div>
      <div class="info">
        <h4><?php echo $stats['pendiente']; ?></h4>
        <span>Pendientes</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="icon info"><i class="bi bi-arrow-repeat"></i></div>
      <div class="info">
        <h4><?php echo $stats['proceso']; ?></h4>
        <span>En Proceso</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="icon success"><i class="bi bi-check-circle"></i></div>
      <div class="info">
        <h4><?php echo $stats['resuelto']; ?></h4>
        <span>Resueltos</span>
      </div>
    </div>
  </div>

  <!-- Filtros -->
  <div class="filters-card">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label>Buscar</label>
        <input type="text" name="q" class="form-control" placeholder="Título o usuario..." value="<?php echo htmlspecialchars($busqueda); ?>">
      </div>
      <div class="col-md-2">
        <label>Estado</label>
        <select name="status" class="form-select">
          <option value="">Todos</option>
          <option value="3" <?php echo $filtroStatus === '3' ? 'selected' : ''; ?>>Pendiente</option>
          <option value="2" <?php echo $filtroStatus === '2' ? 'selected' : ''; ?>>En Proceso</option>
          <option value="4" <?php echo $filtroStatus === '4' ? 'selected' : ''; ?>>Resuelto</option>
          <option value="5" <?php echo $filtroStatus === '5' ? 'selected' : ''; ?>>Cerrado</option>
          <option value="6" <?php echo $filtroStatus === '6' ? 'selected' : ''; ?>>Rechazado</option>
        </select>
      </div>
      <div class="col-md-2">
        <label>Prioridad</label>
        <select name="nivel" class="form-select">
          <option value="">Todas</option>
          <option value="1" <?php echo $filtroNivel === '1' ? 'selected' : ''; ?>>Alta</option>
          <option value="2" <?php echo $filtroNivel === '2' ? 'selected' : ''; ?>>Media</option>
          <option value="3" <?php echo $filtroNivel === '3' ? 'selected' : ''; ?>>Baja</option>
        </select>
      </div>
      <div class="col-md-2">
        <label>Área</label>
        <select name="area" class="form-select">
          <option value="">Todas</option>
          <?php while ($a = mysqli_fetch_assoc($areas)): ?>
            <option value="<?php echo $a['id_area']; ?>" <?php echo $filtroArea == $a['id_area'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($a['nombre_area']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn-filter flex-grow-1">
          <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="caso_soporte.php" class="btn-clear">
          <i class="bi bi-x-lg"></i>
        </a>
      </div>
    </form>
  </div>

  <!-- Tabla -->
  <div class="table-card">
    <div class="table-card-header">
      <h5><i class="bi bi-list-ul text-primary"></i> Casos Registrados</h5>
      <span class="text-muted" style="font-size: 0.85rem;">
        <?php echo mysqli_num_rows($resultado); ?> resultados
      </span>
    </div>

    <?php if (mysqli_num_rows($resultado) > 0): ?>
      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th>ID</th>
              <th>Caso</th>
              <th>Solicitante</th>
              <th>Área</th>
              <th>Fecha</th>
              <th>Prioridad</th>
              <th>Estado</th>
              <th style="text-align:center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($r = mysqli_fetch_assoc($resultado)):
              // Badge estado
              $badgeStatus = 'badge-pendiente';
              switch ((int)$r['STATUS']) {
                case 2: $badgeStatus = 'badge-proceso';   break;
                case 3: $badgeStatus = 'badge-pendiente'; break;
                case 4: $badgeStatus = 'badge-resuelto';  break;
                case 5: $badgeStatus = 'badge-cerrado';   break;
                case 6: $badgeStatus = 'badge-rechazado'; break;
              }

              // Badge nivel
              $badgeNivel = 'baja';
              $nivelTexto = $r['nombre_nivel'] ?? 'Baja';
              if (strtolower($nivelTexto) === 'alta')  $badgeNivel = 'alta';
              if (strtolower($nivelTexto) === 'media') $badgeNivel = 'media';
            ?>
              <tr>
                <td><span class="case-id">#<?php echo str_pad($r['ID_REPORT'], 4, '0', STR_PAD_LEFT); ?></span></td>
                <td>
                  <div class="case-title" title="<?php echo htmlspecialchars($r['TITLE']); ?>">
                    <?php echo htmlspecialchars($r['TITLE']); ?>
                  </div>
                </td>
                <td>
                  <div style="font-weight: 500;"><?php echo htmlspecialchars($r['name_surname'] ?? 'N/A'); ?></div>
                </td>
                <td><span style="font-size: 0.8rem; color: var(--gray);"><?php echo htmlspecialchars($r['nombre_area'] ?? 'N/A'); ?></span></td>
                <td>
                  <div style="font-size: 0.85rem;"><?php echo date('d/m/Y', strtotime($r['CREATION_DATE'])); ?></div>
                </td>
                <td><span class="badge-nivel <?php echo $badgeNivel; ?>"><?php echo htmlspecialchars($nivelTexto); ?></span></td>
                <td><span class="badge-status <?php echo $badgeStatus; ?>"><?php echo htmlspecialchars($r['nombre_status']); ?></span></td>
                <td style="text-align:center;">
                  <button class="btn-action view" title="Ver detalle"
                          onclick='verDetalle(<?php echo json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn-action edit" title="Actualizar"
                          onclick='abrirEditar(<?php echo json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                    <i class="bi bi-pencil"></i>
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5 class="mt-3">No se encontraron casos</h5>
        <p class="mb-0">Prueba ajustando los filtros o la búsqueda</p>
      </div>
    <?php endif; ?>
  </div>

</main>

<!-- ============ MODAL: VER DETALLE ============ -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header gradient">
        <h5 class="modal-title"><i class="bi bi-ticket-detailed me-2"></i> Detalle del Caso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="detail-row">
          <div class="label">ID</div>
          <div class="value" id="d_id">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Título</div>
          <div class="value" id="d_titulo">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Descripción</div>
          <div class="value" id="d_descripcion">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Solicitante</div>
          <div class="value" id="d_usuario">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Área</div>
          <div class="value" id="d_area">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Prioridad</div>
          <div class="value" id="d_nivel">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Estado</div>
          <div class="value" id="d_estado">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Fecha Creación</div>
          <div class="value" id="d_fecha">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Fecha Solución</div>
          <div class="value" id="d_fecha_sol">—</div>
        </div>
        <div class="detail-row">
          <div class="label">Solución</div>
          <div class="value" id="d_solucion">—</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:10px;">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ============ MODAL: ACTUALIZAR ============ -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="actualizar_caso.php" id="formEditar">
        <div class="modal-header gradient">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Actualizar Caso <span id="e_id_span"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_report" id="e_id">

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Estado</label>
            <select name="status" id="e_status" class="form-select" required>
              <option value="3">Pendiente</option>
              <option value="2">En Proceso</option>
              <option value="4">Resuelto</option>
              <option value="5">Cerrado</option>
              <option value="6">Rechazado</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Prioridad</label>
            <select name="nivel" id="e_nivel" class="form-select" required>
              <option value="1">Alta</option>
              <option value="2">Media</option>
              <option value="3">Baja</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600; font-size:0.85rem;">Solución / Comentario</label>
            <textarea name="solucion" id="e_solucion" class="form-control" rows="4" placeholder="Describe la solución aplicada o el motivo del rechazo..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:10px;">Cancelar</button>
          <button type="submit" class="btn-filter">
            <i class="bi bi-check-lg"></i> Guardar
          </button>
        </div>
      </form>
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
  // ============ VER DETALLE ============
  function verDetalle(caso) {
    document.getElementById('d_id').textContent          = '#' + String(caso.ID_REPORT).padStart(4, '0');
    document.getElementById('d_titulo').textContent      = caso.TITLE || '—';
    document.getElementById('d_descripcion').textContent = caso.TITLE || '—';
    document.getElementById('d_usuario').textContent     = caso.name_surname || '—';
    document.getElementById('d_area').textContent        = caso.nombre_area || '—';
    document.getElementById('d_nivel').textContent       = caso.nombre_nivel || '—';
    document.getElementById('d_estado').textContent      = caso.nombre_status || '—';
    document.getElementById('d_fecha').textContent       = caso.CREATION_DATE || '—';
    document.getElementById('d_fecha_sol').textContent   = caso.FECHA_SOLUTION || 'Pendiente';
    document.getElementById('d_solucion').textContent    = caso.SOLUTION || 'Sin solución registrada';

    new bootstrap.Modal(document.getElementById('modalDetalle')).show();
  }

  // ============ ABRIR EDITAR ============
  function abrirEditar(caso) {
    document.getElementById('e_id').value       = caso.ID_REPORT;
    document.getElementById('e_id_span').textContent = '#' + String(caso.ID_REPORT).padStart(4, '0');
    document.getElementById('e_status').value   = caso.STATUS;
    document.getElementById('e_nivel').value    = caso.ID_LEVEL;
    document.getElementById('e_solucion').value = caso.SOLUTION || '';

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
  }
</script>
</body>
</html>