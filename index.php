<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Intranet - Iniciar Sesión</title>
  <link rel="shortcut icon" href="1.svg" type="image/x-icon">
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .login-card {
      border: none;
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-width: 900px;
      width: 100%;
    }
    .login-card .row {
      min-height: 500px;
    }
    .login-form {
      padding: 3rem 2rem;
    }
    .login-form .form-control {
      border-radius: 0.5rem;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border: 1px solid #ddd;
      transition: all 0.3s;
    }
    .login-form .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .login-form .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      z-index: 10;
    }
    .login-form .form-group {
      position: relative;
    }
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 0.5rem;
      padding: 0.75rem;
      font-weight: 600;
      color: #fff;
      transition: all 0.3s;
    }
    .btn-login:hover {
      opacity: 0.9;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
      color: #fff;
    }
    .login-image {
      background: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover no-repeat;
      position: relative;
    }
    .login-image::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
    }
    .login-image .content {
      position: relative;
      z-index: 1;
      color: #fff;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%;
    }
    .login-image .content h2 {
      font-weight: 700;
      margin-bottom: 1rem;
    }
    .login-image .content p {
      opacity: 0.9;
    }
    @media (max-width: 768px) {
      .login-image {
        display: none;
      }
      .login-card .row {
        min-height: auto;
      }
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="card login-card mx-auto">
          <div class="row g-0">
            <!-- Columna del formulario -->
            <div class="col-lg-6">
              <div class="login-form">
                <div class="text-center mb-4">
                  <!-- Reemplaza este ícono por tu propio logo si lo deseas -->
                  <i class="fas fa-user-circle fa-4x" style="color: #667eea;"></i>
                  <h3 class="mt-3 fw-bold">Bienvenido</h3>
                  <p class="text-muted">Por favor, ingresa tus credenciales</p>
                </div>

                <form action="login.php" method="POST">
                  <div class="form-group mb-4">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="USER" maxlength="8" required class="form-control" placeholder="Usuario">
                  </div>

                  <div class="form-group mb-4">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="PASSWORD" required maxlength="8" class="form-control" placeholder="Contraseña">
                  </div>

                  <div class="d-grid mb-3">
                    <button class="btn btn-login" type="submit">
                      <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                    </button>
                  </div>

                  <div class="text-center">
                    <a href="reset.php" class="text-decoration-none" style="color: #667eea;">
                      ¿Olvidaste tu contraseña?
                    </a>
                  </div>
                </form>

                <p class="text-muted text-center mt-4 mb-0 small">
                  &copy; <?php echo date('Y'); ?> Todos los derechos reservados
                </p>
              </div>
            </div>

            <!-- Columna de la imagen -->
            <div class="col-lg-6 login-image">
              <div class="content">
                <h2>Sistema de Intranet</h2>
                <p>Accede a todas las herramientas y recursos de la organización desde un solo lugar.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (opcional, para componentes interactivos) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>