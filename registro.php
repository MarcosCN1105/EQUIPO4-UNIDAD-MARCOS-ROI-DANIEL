<?php
session_start();
require_once 'conexion.class.php';

// Si ya está logueado, redirigir al index
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
    
    // Validaciones
    if (empty($nombre)) {
        $errores[] = "El nombre de usuario es obligatorio";
    } elseif (strlen($nombre) < 3) {
        $errores[] = "El nombre debe tener al menos 3 caracteres";
    }
    
    if (empty($contrasena)) {
        $errores[] = "La contraseña es obligatoria";
    } elseif (strlen($contrasena) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if ($contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    if (empty($errores)) {
        $conexion = Conexion::getInstancia()->getConexion();
        
        // Verificar si el usuario ya existe
        $consulta = $conexion->prepare("SELECT id FROM usuarios WHERE nombre = ?");
        $consulta->execute([$nombre]);
        
        if ($consulta->fetch()) {
            $errores[] = "El nombre de usuario ya existe";
        } else {
            // Crear nuevo usuario
            $insert = $conexion->prepare("INSERT INTO usuarios (nombre, contrasena) VALUES (?, ?)");
            if ($insert->execute([$nombre, $contrasena])) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errores[] = "Error al crear la cuenta. Inténtalo de nuevo.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - CarReviews</title>
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

        .register-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
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

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .error-list {
            background: #fed7d7;
            color: #c53030;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #feb2b2;
        }

        .error-list ul {
            margin-left: 20px;
        }

        .error-list li {
            margin-bottom: 5px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #718096;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .password-requirements {
            background: #f7fafc;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
        }

        .password-requirements h4 {
            color: #4a5568;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .password-requirements ul {
            color: #718096;
            font-size: 0.85rem;
            margin-left: 20px;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>CarReviews</h1>
            <p>Crea tu cuenta</p>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="error-list">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="nombre">Nombre de usuario</label>
                <input type="text" id="nombre" name="nombre" required 
                       placeholder="Elige un nombre de usuario"
                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required 
                       placeholder="Crea una contraseña segura">
                
                <div class="password-requirements">
                    <h4>Requisitos de contraseña:</h4>
                    <ul>
                        <li>Mínimo 6 caracteres</li>
                        <li>Recomendado usar letras y números</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmar_contrasena">Confirmar contraseña</label>
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required 
                       placeholder="Repite tu contraseña">
            </div>

            <button type="submit" name="register" class="btn-register">
                Crear Cuenta
            </button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</body>
</html>