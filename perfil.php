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

// Obtener valoraciones del usuario en BD
$usuarioId = $_SESSION['usuario_id'];
$valoracionesUsuario = $cochesDao->getValoracionesPorUsuario($usuarioId);

// Mensajes
$mensaje_exito = '';
$mensaje_error = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'editada') {
        $mensaje_exito = "Valoración actualizada correctamente";
    } elseif ($_GET['success'] == 'eliminada') {
        $mensaje_exito = "Valoración eliminada correctamente";
    } elseif ($_GET['success'] == 'editada_sesion') {
        $mensaje_exito = "Valoración actualizada en sesión (se guardará al cerrar sesión)";
    } elseif ($_GET['success'] == 'eliminada_sesion') {
        $mensaje_exito = "Valoración eliminada de la sesión";
    } elseif ($_GET['success'] == 'valoracion_guardada_sesion') {
        $mensaje_exito = "Valoración añadida a la sesión correctamente";
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; color: white; position: relative; }

        .header h1 { font-size: 3rem; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .header p { opacity: 0.9; }

        .logo { font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; background: linear-gradient(135deg, #fff, #e2e8f0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .user-info { position: absolute; top: 20px; right: 20px; color: white; text-align: right; }

        .card {
            background: white; border-radius: 20px; padding: 30px; margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .valoraciones-grid { display: grid; gap: 20px; margin-top: 20px; }

        .valoracion-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            border-left: 4px solid #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .valoracion-card-temporal {
            border-left: 4px solid #48bb78 !important;
            background: #f0fff4 !important;
        }

        .valoracion-header { display: flex; justify-content: space-between; flex-wrap: wrap; margin-bottom: 15px; }
        
        .modelo-info { font-weight: 600; font-size: 1.2rem; }
        .puntuacion-valoracion { font-size: 1.5rem; font-weight: 600; color: #ffc107; }

        .acciones { display: flex; gap: 10px; margin-top: 15px; }
        .btn-editar { background: #4299e1; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; }
        .btn-eliminar { background: #f56565; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; }

        .success-message {
            background: #48bb78; color: white; padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 20px;
        }

        .error-message {
            background: #fed7d7; color: #c53030; padding: 12px; border-radius: 8px;
            text-align: center; border: 1px solid #feb2b2; margin-bottom: 20px;
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <div class="logo">CarReviews</div>
        <h1>Mi Perfil</h1>
        <p>Gestiona tus valoraciones</p>

        <div class="user-info">
            Hola, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
            <br>
            <a style="color: white;" href="index.php">Inicio</a> |
            <a style="color: white;" href="logout.php">Cerrar sesión</a>
        </div>
    </div>

    <?php if (!empty($mensaje_exito)): ?>
        <div class="success-message"><?= htmlspecialchars($mensaje_exito) ?></div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="error-message"><?= htmlspecialchars($mensaje_error) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Mis Valoraciones</h2>

        <!-- 🔵 VALORACIONES TEMPORALES EN SESIÓN -->
        <?php if (isset($_SESSION["crear"]) && !empty($_SESSION["crear"])): ?>
            <h3 style="margin-top: 25px; color: #2f855a;">Valoraciones pendientes de guardar</h3>

            <div class="valoraciones-grid">
                <?php foreach ($_SESSION["crear"] as $valoracionTemp): 
                    $modelo = $cochesDao->getModeloCompleto($valoracionTemp["modelo_id"]);
                    $marca = $cochesDao->getMarcaPorId($modelo->getMarcaId());
                ?>
                    <div class="valoracion-card valoracion-card-temporal">
                        <div class="valoracion-header">
                            <div class="modelo-info">
                                <?= htmlspecialchars($marca->getNombre()) ?> -
                                <?= htmlspecialchars($modelo->getNombre()) ?>
                                <span style="font-size: 0.9rem; color: #718096;">
                                    (<?= $modelo->getAnno()->format('Y') ?>)
                                </span>
                            </div>

                            <div class="puntuacion-valoracion">
                                <?= str_repeat('★', $valoracionTemp["puntuacion"]) ?>
                                <?= str_repeat('☆', 10 - $valoracionTemp["puntuacion"]) ?>
                            </div>
                        </div>

                        <div class="fecha-valoracion">🕒 Pendiente de almacenar</div>

                        <div class="comentario">
                            <?= nl2br(htmlspecialchars($valoracionTemp["comentario"] ?? "Sin comentario")) ?>
                        </div>

                        <div class="acciones">
                            <a class="btn-editar" href="editar_valoracion.php?id=<?= $valoracionTemp["id_temp"] ?>">✏️ Editar</a>
                            <a class="btn-eliminar" href="eliminar_valoracion.php?id=<?= $valoracionTemp["id_temp"] ?>"
                               onclick="return confirm('¿Eliminar esta valoración pendiente?')">🗑️ Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- 🔵 VALORACIONES DEFINITIVAS EN BD -->
        <?php if (empty($valoracionesUsuario)): ?>
            <p style="text-align:center; margin-top:20px;">No tienes valoraciones en la base de datos.</p>
        <?php else: ?>
            <div class="valoraciones-grid">
                <?php foreach ($valoracionesUsuario as $valoracion):
                    $modelo = $cochesDao->getModeloCompleto($valoracion->getModeloId());
                    $marca  = $cochesDao->getMarcaPorId($modelo->getMarcaId());
                ?>
                    <div class="valoracion-card">
                        <div class="valoracion-header">
                            <div class="modelo-info">
                                <?= htmlspecialchars($marca->getNombre()) ?> -
                                <?= htmlspecialchars($modelo->getNombre()) ?>
                                (<?= $modelo->getAnno()->format('Y') ?>)
                            </div>

                            <div class="puntuacion-valoracion">
                                <?= str_repeat('★', $valoracion->getPuntuacion()) ?>
                                <?= str_repeat('☆', 10 - $valoracion->getPuntuacion()) ?>
                            </div>
                        </div>

                        <div class="fecha-valoracion">
                            📅 <?= $valoracion->getFecha()->format('d/m/Y H:i') ?>
                        </div>

                        <div class="comentario">
                            <?= nl2br(htmlspecialchars($valoracion->getComentario() ?? 'Sin comentario')) ?>
                        </div>

                        <div class="acciones">
                            <a class="btn-editar" href="editar_valoracion.php?id=<?= $valoracion->getId() ?>">✏️ Editar</a>
                            <a class="btn-eliminar" href="eliminar_valoracion.php?id=<?= $valoracion->getId() ?>" onclick="return confirm('¿Eliminar esta valoración?')">🗑️ Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
