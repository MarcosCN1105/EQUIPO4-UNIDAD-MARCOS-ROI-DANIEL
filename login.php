<?php
session_start();
require_once 'conexion.class.php';

// Si ya está logueado, redirigir al index
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $nombre = $_POST['nombre'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = ?");
    $consulta->execute([$nombre]);
    $usuario = $consulta->fetch(PDO::FETCH_OBJ);
    
    if ($usuario && $contrasena === $usuario->contrasena) {
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nombre'] = $usuario->nombre;
        header('Location: index.php');
        exit;
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CarReviews</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .logo p {
            color: #718096;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.95rem;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #feb2b2;
        }

        .success-message {
            background: #c6f6d5;
            color: #276749;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #9ae6b4;
        }

        .demo-accounts {
            margin-top: 30px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .demo-accounts h3 {
            color: #4a5568;
            margin-bottom: 15px;
            font-size: 1rem;
            text-align: center;
        }

        .account-list {
            display: grid;
            gap: 10px;
        }

        .account-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .username {
            font-weight: 600;
            color: #4a5568;
        }

        .password {
            color: #718096;
            font-family: monospace;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #718096;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>CarReviews</h1>
            <p>Inicia sesión en tu cuenta</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="success-message">
                Has cerrado sesión correctamente
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="success-message">
                ¡Cuenta creada correctamente! Ya puedes iniciar sesión
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="nombre">Usuario</label>
                <input type="text" id="nombre" name="nombre" required 
                       placeholder="Introduce tu nombre de usuario"
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required 
                       placeholder="Introduce tu contraseña">
            </div>

            <button type="submit" name="login" class="btn-login">
                Iniciar Sesión
            </button>
        </form>

        <div class="demo-accounts">
            <h3>Cuentas de demostración</h3>
            <div class="account-list">
                <div class="account-item">
                    <span class="username">Laura</span>
                    <span class="password">laura2025</span>
                </div>
                <div class="account-item">
                    <span class="username">Pedro</span>
                    <span class="password">pedro2025</span>
                </div>
                <div class="account-item">
                    <span class="username">Sofia</span>
                    <span class="password">sofia2025</span>
                </div>
                <div class="account-item">
                    <span class="username">Miguel</span>
                    <span class="password">miguel2025</span>
                </div>
            </div>
        </div>

        <div class="register-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>