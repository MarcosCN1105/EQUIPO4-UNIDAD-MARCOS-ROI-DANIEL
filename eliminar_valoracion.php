<?php
session_start();

// Redirigir al login si no está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexion.class.php';
require_once 'cochesDao.class.php';

$cochesDao = new cochesDao();

// Obtener la valoración a eliminar
$valoracionId = $_GET['id'] ?? 0;
$valoracion = $cochesDao->getValoracionPorId($valoracionId);

// Verificar que la valoración existe y pertenece al usuario
if (!$valoracion || $valoracion->getUsuarioId() != $_SESSION['usuario_id']) {
    header('Location: perfil.php?error=no_pertenece');
    exit;
}

// Obtener información del modelo
$modelo = $cochesDao->getModeloCompleto($valoracion->getModeloId());
$marca = $cochesDao->getMarcaPorId($modelo->getMarcaId());

// Procesar la eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar'])) {
        $exito = $cochesDao->eliminarValoracion($valoracionId);
        
        if ($exito) {
            header('Location: perfil.php?success=eliminada');
            exit;
        } else {
            header('Location: perfil.php?error=eliminacion');
            exit;
        }
    } elseif (isset($_POST['cancelar'])) {
        header('Location: perfil.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Valoración - CarReviews</title>
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

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .warning-message {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
        }

        .warning-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .valoracion-info {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        .modelo-nombre {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .puntuacion {
            font-size: 1.2rem;
            color: #d69e2e;
            margin-bottom: 10px;
        }

        .comentario {
            color: #4a5568;
            font-style: italic;
            line-height: 1.5;
        }

        .btn-confirmar {
            background: #e53e3e;
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
            margin-bottom: 10px;
        }

        .btn-confirmar:hover {
            background: #c53030;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(229, 62, 62, 0.3);
        }

        .btn-cancelar {
            background: #a0aec0;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-cancelar:hover {
            background: #718096;
            transform: translateY(-2px);
        }

        .nav-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .nav-links a {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: #edf2f7;
            color: #4a5568;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: #e2e8f0;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 1.8rem;
            }
            
            .nav-links {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>CarReviews</h1>
            <p>Eliminar Valoración</p>
        </div>

        <div class="warning-message">
            <div class="warning-icon">⚠️</div>
            <h2 style="margin-bottom: 10px;">¿Estás seguro?</h2>
            <p>Esta acción no se puede deshacer. La valoración se eliminará permanentemente.</p>
        </div>

        <div class="valoracion-info">
            <div class="modelo-nombre">
                <?= htmlspecialchars($marca->getNombre()) ?> - <?= htmlspecialchars($modelo->getNombre()) ?>
            </div>
            <div class="puntuacion">
                Puntuación: <?= str_repeat('★', $valoracion->getPuntuacion()) ?><?= str_repeat('☆', 10 - $valoracion->getPuntuacion()) ?>
                (<?= $valoracion->getPuntuacion() ?>/10)
            </div>
            <?php if ($valoracion->getComentario()): ?>
                <div class="comentario">
                    "<?= htmlspecialchars($valoracion->getComentario()) ?>"
                </div>
            <?php endif; ?>
            <div style="margin-top: 10px; color: #718096; font-size: 0.9rem;">
                Fecha: <?= $valoracion->getFecha()->format('d/m/Y H:i') ?>
            </div>
        </div>

        <form method="post">
            <button type="submit" name="confirmar" class="btn-confirmar">
                🗑️ Sí, Eliminar Valoración
            </button>
            <button type="submit" name="cancelar" class="btn-cancelar">
                ↩️ Cancelar
            </button>
        </form>

        <div class="nav-links">
            <a href="perfil.php">← Volver al Perfil</a>
            <a href="index.php">🏠 Ir al Inicio</a>
        </div>
    </div>
</body>
</html>