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
$primeraN = substr($NAME, 0, 1);
$primeraA = substr($APE, 0, 8);
$ROL      = $_SESSION['IDROLS'];
$CEDULA   = $_SESSION['CEDULA'];
$area     = $_SESSION['ASSIGNED_AREA'];

// Foto del usuario
$consulta = mysqli_query($conn, "SELECT CEDULA, foto FROM user_datos WHERE CEDULA = '$CEDULA'");
$valores  = mysqli_fetch_array($consulta);
$foto     = $valores['foto'] ?? 'images/Canaima.png';

// Últimas 5 solicitudes del usuario
$sqlHistorial = "SELECT r.ID_REPORT, r.TITLE, r.CREATION_DATE, r.STATUS, 
                        s.nombre_status, n.nombre_nivel
                 FROM report r
                 LEFT JOIN status_report s ON r.STATUS = s.id_status
                 LEFT JOIN niveles n ON r.ID_LEVEL = n.id_nivel
                 WHERE r.ID_NAME = '$ID'
                 ORDER BY r.CREATION_DATE DESC
                 LIMIT 5";
$historial = mysqli_query($conn, $sqlHistorial);

// Departamentos (con iconos)
$departamentos = [
    1  => ['nombre' => 'Presidencia',                'icon' => 'bi-building'],
    2  => ['nombre' => 'Proyecto',                   'icon' => 'bi-kanban'],
    3  => ['nombre' => 'Consultoría Jurídica',       'icon' => 'bi-briefcase'],
    4  => ['nombre' => 'Planificación y Presupuesto','icon' => 'bi-graph-up'],
    5  => ['nombre' => 'Gestión Humana',             'icon' => 'bi-people'],
    6  => ['nombre' => 'Procura',                    'icon' => 'bi-cart'],
    7  => ['nombre' => 'Administración y Finanzas',  'icon' => 'bi-cash-coin'],
    8  => ['nombre' => 'TIC',                        'icon' => 'bi-cpu'],
    9  => ['nombre' => 'Atención al Ciudadano',      'icon' => 'bi-headset'],
    10 => ['nombre' => 'Comercialización',           'icon' => 'bi-shop'],
    11 => ['nombre' => 'Seguridad',                  'icon' => 'bi-shield'],
    12 => ['nombre' => 'Seguridad Integral',         'icon' => 'bi-shield-check'],
    13 => ['nombre' => 'Producción',                 'icon' => 'bi-gear'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Solicitud de Soporte | Industria Canaima</title>
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
    .page-header { margin-bottom: 1.75rem; }
    .page-header h1 {
      font-size: 1.75rem; font-weight: 700;
      margin-bottom: 0.25rem; color: var(--dark);
    }
    .page-header .breadcrumb {
      background: transparent; padding: 0; margin: 0;
      font-size: 0.875rem;
    }
    .page-header .breadcrumb a { color: var(--primary); text-decoration: none; }
    .page-header .breadcrumb-item.active { color: var(--gray); }

    /* ============ FORM CARD ============ */
    .form-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      border: 1px solid var(--border);
      overflow: hidden;
    }

    .form-card-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      padding: 1.75rem 2rem;
    }
    .form-card-header h2 {
      font-size: 1.35rem; font-weight: 700;
      margin: 0 0 0.35rem; display: flex;
      align-items: center; gap: 0.65rem;
    }
    .form-card-header p {
      margin: 0; opacity: 0.9; font-size: 0.9rem;
    }

    .form-card-body { padding: 2rem; }

    /* ============ FORM FIELDS ============ */
    .form-label {
      font-weight: 600; font-size: 0.85rem;
      color: var(--dark); margin-bottom: 0.5rem;
      display: flex; align-items: center; gap: 0.4rem;
    }
    .form-label .required { color: var(--danger); }

    .form-control, .form-select {
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.9rem;
      transition: all 0.2s;
      background: #fff;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    textarea.form-control { min-height: 130px; resize: vertical; }

    .form-text {
      font-size: 0.78rem; color: var(--gray);
      margin-top: 0.35rem;
    }

    /* ============ INFO DEL USUARIO ============ */
    .user-info-card {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
      border: 1px solid rgba(102, 126, 234, 0.2);
      border-radius: 12px;
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.75rem;
    }
    .user-info-card img {
      width: 55px; height: 55px; border-radius: 50%;
      object-fit: cover; border: 3px solid #fff;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    .user-info-card .info { flex: 1; }
    .user-info-card .info h4 {
      font-size: 1rem; font-weight: 700;
      margin: 0 0 0.15rem; color: var(--dark);
    }
    .user-info-card .info p {
      margin: 0; font-size: 0.83rem; color: var(--gray);
    }

    /* ============ BOTONES ============ */
    .btn-primary-custom {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 0.85rem 2rem;
      border-radius: 10px;
      font-size: 0.95rem;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-primary-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
      color: #fff;
    }
    .btn-secondary-custom {
      background: #fff;
      border: 1.5px solid var(--border);
      color: var(--gray);
      font-weight: 600;
      padding: 0.85rem 1.5rem;
      border-radius: 10px;
      font-size: 0.95rem;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-secondary-custom:hover {
      background: var(--light);
      color: var(--dark);
      border-color: var(--gray);
    }

    /* ============ HISTORIAL ============ */
    .history-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      overflow: hidden;
    }
    .history-card-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .history-card-header h5 {
      font-size: 1rem; font-weight: 700;
      margin: 0; display: flex;
      align-items: center; gap: 0.5rem;
    }

    .history-item {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--light);
      display: flex;
      align-items: flex-start;
      gap: 0.85rem;
      transition: background 0.2s;
    }
    .history-item:last-child { border-bottom: none; }
    .history-item:hover { background: #fafbfc; }

    .history-item .status-dot {
      width: 10px; height: 10px;
      border-radius: 50%;
      margin-top: 6px;
      flex-shrink: 0;
    }
    .status-pendiente { background: var(--warning); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }
    .status-proceso   { background: var(--info);    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
    .status-resuelto  { background: var(--success); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
    .status-cerrado   { background: var(--gray);    box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.15); }
    .status-rechazado { background: var(--danger);  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }

    .history-item .content { flex: 1; min-width: 0; }
    .history-item .content h6 {
      font-size: 0.875rem; font-weight: 600;
      margin: 0 0 0.25rem; color: var(--dark);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .history-item .content .meta {
      display: flex; gap: 0.75rem;
      font-size: 0.75rem; color: var(--gray);
      flex-wrap: wrap;
    }

    .badge-status {
      font-size: 0.7rem;
      font-weight: 600;
      padding: 0.3rem 0.65rem;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .badge-pendiente { background: #fef3c7; color: #92400e; }
    .badge-proceso   { background: #dbeafe; color: #1e40af; }
    .badge-resuelto  { background: #d1fae5; color: #065f46; }
    .badge-cerrado   { background: #e5e7eb; color: #374151; }
    .badge-rechazado { background: #fee2e2; color: #991b1b; }

    /* ============ EMPTY STATE ============ */
    .empty-state {
      text-align: center;
      padding: 2.5rem 1rem;
      color: var(--gray);
    }
    .empty-state i {
      font-size: 3rem; opacity: 0.3;
      display: block; margin-bottom: 0.75rem;
    }

    /* ============ INFO CARD (tips) ============ */
    .tips-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      padding: 1.25rem 1.5rem;
      margin-bottom: 1.25rem;
    }
    .tips-card h5 {
      font-size: 0.95rem; font-weight: 700;
      margin: 0 0 0.85rem;
      display: flex; align-items: center; gap: 0.5rem;
    }
    .tip-item {
      display: flex; align-items: flex-start; gap: 0.65rem;
      padding: 0.5rem 0;
      font-size: 0.82rem;
      color: var(--gray);
      line-height: 1.5;
    }
    .tip-item i {
      color: var(--primary);
      font-size: 1rem;
      margin-top: 1px;
      flex-shrink: 0;
    }

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
      background: transparent;
      border: none;
      font-size: 1.5rem;
      color: var(--dark);
      cursor: pointer;
    }

    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.show { transform: translateX(0); box-shadow: 0 0 30px rgba(0,0,0,0.15); }
      .main, .footer { margin-left: 0; }
      .toggle-sidebar { display: block; }
      .header .search-form { display: none; }
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

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar">
  <ul class="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link" href="index.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    </li>

    <li class="nav-heading">Solicitudes</li>
    <li class="nav-item">
      <a class="nav-link active" href="soporte_tecnico.php"><i class="bi bi-headset"></i><span>Soporte Técnico</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="Constancia_de_trabajo.php?edi=<?php echo $ID; ?>"><i class="bi bi-file-earmark-text"></i><span>Constancia de Trabajo</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="recibo.php"><i class="bi bi-receipt"></i><span>Recibo de Pago</span></a>
    </li>

    <li class="nav-heading">Recursos</li>
    <li class="nav-item">
      <a class="nav-link" href="./pdf/vacaciones.xls"><i class="bi bi-download"></i><span>Planilla de Vacaciones</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="./pdf/permiso.docx"><i class="bi bi-download"></i><span>Planilla de Permisos</span></a>
    </li>

    <li class="nav-heading">Biblioteca Digital</li>
    <li class="nav-item">
      <a class="nav-link" href="./pdf/103_Manual_Canaimit.pdf"><i class="bi bi-book"></i><span>Manual Canaima</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="./pdf/guia linux.pdf"><i class="bi bi-book"></i><span>Guía de Linux</span></a>
    </li>

    <li class="nav-heading">Enlaces Web</li>
    <li class="nav-item">
      <a class="nav-link" href="https://bdvenlinea.banvenez.com" target="_blank"><i class="bi bi-bank"></i><span>Banco de Venezuela</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="https://persona.patria.org.ve/login/clave/" target="_blank"><i class="bi bi-shield-check"></i><span>Patria</span></a>
    </li>

    <li class="nav-heading">Administración</li>
    <li class="nav-item">
      <a class="nav-link" href="perfil.php"><i class="bi bi-person-circle"></i><span>Mi Perfil</span></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="caso_soporte.php"><i class="bi bi-ticket-detailed"></i><span>Casos de Soporte</span></a>
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
    <h1>Solicitud de Soporte Técnico</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
        <li class="breadcrumb-item"><a href="#">Solicitudes</a></li>
        <li class="breadcrumb-item active">Soporte Técnico</li>
      </ol>
    </nav>
  </div>

  <div class="row g-4">
    <!-- Formulario principal -->
    <div class="col-lg-8">

      <div class="form-card">
        <div class="form-card-header">
          <h2><i class="bi bi-tools"></i> Nueva Solicitud</h2>
          <p>Completa el formulario y nuestro equipo de soporte te atenderá lo antes posible.</p>
        </div>

        <div class="form-card-body">

          <!-- Info del usuario -->
          <div class="user-info-card">
            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar">
            <div class="info">
              <h4><?php echo htmlspecialchars($NAME . ' ' . $APE); ?></h4>
              <p>
                <i class="bi bi-envelope me-1"></i>
                <?php echo htmlspecialchars($_SESSION['EMAIL']); ?>
                &nbsp;·&nbsp;
                <i class="bi bi-telephone me-1"></i>
                <?php echo htmlspecialchars($_SESSION['telefono']); ?>
              </p>
            </div>
          </div>

          <form method="POST" action="enviar_soporte.php" id="formSoporte">
            <input type="hidden" name="name_surname" value="<?php echo htmlspecialchars($NAME . ' ' . $APE); ?>">

            <!-- Departamento -->
            <div class="mb-4">
              <label class="form-label">
                <i class="bi bi-building"></i>
                Departamento <span class="required">*</span>
              </label>
              <select class="form-select" name="area" required>
                <option value="">— Selecciona un departamento —</option>
                <?php foreach ($departamentos as $id => $dep): ?>
                  <option value="<?php echo $id; ?>" <?php echo ($area === $dep['nombre']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($dep['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Selecciona el departamento donde se encuentra el equipo con la falla.
              </div>
            </div>

            <!-- Descripción -->
            <div class="mb-4">
              <label class="form-label">
                <i class="bi bi-chat-left-text"></i>
                Descripción de la falla <span class="required">*</span>
              </label>
              <textarea
                name="TITLE"
                class="form-control"
                id="txtDescripcion"
                maxlength="250"
                rows="5"
                required
                placeholder="Describe con detalle el problema: ¿qué ocurre?, ¿cuándo comenzó?, ¿qué intentaste hacer?"
              ></textarea>
              <div class="d-flex justify-content-between">
                <div class="form-text">
                  <i class="bi bi-lightbulb me-1"></i>
                  Sé lo más específico posible para una mejor atención.
                </div>
                <div class="form-text">
                  <span id="contador">0</span> / 250
                </div>
              </div>
            </div>

            <!-- Botones -->
            <div class="d-flex flex-wrap gap-2 justify-content-end pt-3 border-top">
              <a href="index.php" class="btn-secondary-custom">
                <i class="bi bi-x-lg"></i> Cancelar
              </a>
              <button type="submit" class="btn-primary-custom">
                <i class="bi bi-send-fill"></i> Enviar Solicitud
              </button>
            </div>
          </form>

        </div>
      </div>

    </div>

    <!-- Panel lateral -->
    <div class="col-lg-4">

      <!-- Consejos -->
      <div class="tips-card">
        <h5><i class="bi bi-lightbulb-fill text-warning"></i> Consejos para tu solicitud</h5>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Indica el número de inventario del equipo si lo conoces.</span>
        </div>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Describe qué estabas haciendo cuando ocurrió la falla.</span>
        </div>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Menciona si el problema afecta tu trabajo diario.</span>
        </div>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Si tienes capturas de pantalla, adjúntalas en un correo posterior.</span>
        </div>
      </div>

      <!-- Historial -->
      <div class="history-card">
        <div class="history-card-header">
          <h5><i class="bi bi-clock-history text-primary"></i> Tus últimas solicitudes</h5>
        </div>

        <?php if ($historial && mysqli_num_rows($historial) > 0): ?>
          <?php while ($h = mysqli_fetch_assoc($historial)): 
            $statusClass = 'status-pendiente';
            $badgeClass  = 'badge-pendiente';
            switch ((int)$h['STATUS']) {
              case 2: $statusClass = 'status-proceso';   $badgeClass = 'badge-proceso';   break;
              case 3: $statusClass = 'status-pendiente'; $badgeClass = 'badge-pendiente'; break;
              case 4: $statusClass = 'status-resuelto';  $badgeClass = 'badge-resuelto';  break;
              case 5: $statusClass = 'status-cerrado';   $badgeClass = 'badge-cerrado';   break;
              case 6: $statusClass = 'status-rechazado'; $badgeClass = 'badge-rechazado'; break;
            }
          ?>
            <div class="history-item">
              <div class="status-dot <?php echo $statusClass; ?>"></div>
              <div class="content">
                <h6><?php echo htmlspecialchars($h['TITLE']); ?></h6>
                <div class="meta">
                  <span><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y', strtotime($h['CREATION_DATE'])); ?></span>
                  <span class="badge-status <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($h['nombre_status']); ?></span>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p class="mb-0">Aún no has enviado solicitudes</p>
          </div>
        <?php endif; ?>
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
  // Contador de caracteres
  const txt = document.getElementById('txtDescripcion');
  const contador = document.getElementById('contador');
  if (txt && contador) {
    txt.addEventListener('input', function() {
      contador.textContent = this.value.length;
    });
  }
</script>
<script