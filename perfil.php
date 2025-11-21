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

// Obtener las valoraciones del usuario actual
$usuarioId = $_SESSION['usuario_id'];
$valoracionesUsuario = $cochesDao->getValoracionesPorUsuario($usuarioId);

// Mostrar mensajes de éxito/error
$mensaje_exito = '';
$mensaje_error = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'editada') {
        $mensaje_exito = "Valoración actualizada correctamente";
    } elseif ($_GET['success'] == 'eliminada') {
        $mensaje_exito = "Valoración eliminada correctamente";
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'no_pertenece') {
        $mensaje_error = "No tienes permisos para modificar esta valoración";
    } elseif ($_GET['error'] == 'eliminacion') {
        $mensaje_error = "Error al eliminar la valoración";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - CarReviews</title>
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
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
            position: relative;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        .valoraciones-grid {
            display: grid;
            gap: 20px;
            margin-top: 30px;
        }

        .valoracion-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .valoracion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .valoracion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .modelo-info {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.2rem;
        }

        .puntuacion-valoracion {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffc107;
        }

        .fecha-valoracion {
            color: #718096;
            font-size: 0.9rem;
        }

        .comentario {
            line-height: 1.6;
            color: #4a5568;
            margin-bottom: 15px;
        }

        .acciones {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-editar, .btn-eliminar {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-editar {
            background: #4299e1;
            color: white;
        }

        .btn-editar:hover {
            background: #3182ce;
        }

        .btn-eliminar {
            background: #f56565;
            color: white;
        }

        .btn-eliminar:hover {
            background: #e53e3e;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #fff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            text-align: right;
        }

        .user-info .welcome {
            font-size: 0.9rem;
        }

        .user-info .logout {
            color: white;
            font-size: 0.8rem;
            text-decoration: none;
            opacity: 0.8;
            margin-left: 15px;
        }

        .user-info .logout:hover {
            opacity: 1;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .nav-links a:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .user-stats {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 20px;
            }
            
            .valoracion-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .user-info {
                position: static;
                text-align: center;
                margin-top: 15px;
            }
            
            .nav-links {
                justify-content: center;
            }
        }

        .success-message {
            background: #48bb78;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #feb2b2;
            animation: slideIn 0.5s ease;
        }

        .puntuacion-promedio {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            margin-top: 20px;
        }

        .puntuacion-numero {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #ffd700;
        }

        .estrellas {
            font-size: 2rem;
            letter-spacing: 5px;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">CarReviews</div>
            <h1>Mi Perfil</h1>
            <p>Gestiona tus valoraciones y actividad</p>
            
            <div class="user-info">
                <div class="welcome">Hola, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong></div>
                <div class="nav-links">
                    <a href="index.php">🏠 Inicio</a>
                    <a href="logout.php">🚪 Cerrar sesión</a>
                </div>
            </div>
        </div>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="success-message">
                <?= htmlspecialchars($mensaje_exito) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje_error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($mensaje_error) ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas del usuario -->
        <div class="user-stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($valoracionesUsuario) ?></div>
                <div class="stat-label">Valoraciones Totales</div>
            </div>
            
            <?php
            // Calcular puntuación promedio
            $puntuacionTotal = 0;
            foreach ($valoracionesUsuario as $valoracion) {
                $puntuacionTotal += $valoracion->getPuntuacion();
            }
            $puntuacionPromedio = count($valoracionesUsuario) > 0 ? round($puntuacionTotal / count($valoracionesUsuario), 1) : 0;
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?= $puntuacionPromedio ?></div>
                <div class="stat-label">Puntuación Promedio</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?= date('Y') ?></div>
                <div class="stat-label">Miembro desde</div>
            </div>
        </div>

        <!-- Lista de valoraciones del usuario -->
        <div class="card">
            <h2 style="margin-bottom: 25px; color: #2d3748;">Mis Valoraciones</h2>
            
            <?php if (empty($valoracionesUsuario)): ?>
                <div class="empty-state">
                    <div class="icon">📝</div>
                    <h3 style="color: #4a5568; margin-bottom: 10px;">No has realizado valoraciones</h3>
                    <p style="color: #718096;">Comienza a compartir tus experiencias con diferentes modelos de coches</p>
                    <a href="index.php" style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                        Empezar a Valorar
                    </a>
                </div>
            <?php else: ?>
                <div class="valoraciones-grid">
                    <?php foreach ($valoracionesUsuario as $valoracion): 
                        $modelo = $cochesDao->getModeloCompleto($valoracion->getModeloId());
                        $marca = $cochesDao->getMarcaPorId($modelo->getMarcaId());
                    ?>
                        <div class="valoracion-card">
                            <div class="valoracion-header">
                                <div class="modelo-info">
                                    <?= htmlspecialchars($marca->getNombre()) ?> - <?= htmlspecialchars($modelo->getNombre()) ?>
                                    <span style="font-size: 0.9rem; color: #718096; font-weight: normal;">
                                        (<?= $modelo->getAnno()->format('Y') ?>)
                                    </span>
                                </div>
                                <div class="puntuacion-valoracion">
                                    <?= str_repeat('★', $valoracion->getPuntuacion()) ?><?= str_repeat('☆', 10 - $valoracion->getPuntuacion()) ?>
                                    <span style="font-size: 1rem; color: #718096; margin-left: 10px;">
                                        (<?= $valoracion->getPuntuacion() ?>/10)
                                    </span>
                                </div>
                            </div>
                            
                            <div class="fecha-valoracion">
                                📅 <?= $valoracion->getFecha()->format('d/m/Y H:i') ?>
                            </div>
                            
                            <div class="comentario">
                                <?= nl2br(htmlspecialchars($valoracion->getComentario() ?? 'Sin comentario')) ?>
                            </div>
                            
                            <div class="acciones">
                                <a href="editar_valoracion.php?id=<?= $valoracion->getId() ?>" class="btn-editar">
                                    ✏️ Editar
                                </a>
                                <a href="eliminar_valoracion.php?id=<?= $valoracion->getId() ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta valoración?')">
                                    🗑️ Eliminar
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>