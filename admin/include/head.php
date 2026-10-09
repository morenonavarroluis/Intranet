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