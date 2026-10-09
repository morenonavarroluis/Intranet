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
      <li class="nav-item">
        <a class="nav-link" href="caso_soporte.php"><i class="bi bi-ticket-detailed"></i><span>Casos de Soporte</span></a>
      </li>
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