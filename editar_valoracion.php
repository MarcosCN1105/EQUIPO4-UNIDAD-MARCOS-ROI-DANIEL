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
$error = '';
$success = '';

// Obtener la valoración a editar
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

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $puntuacion = (int)$_POST['puntuacion'];
    $comentario = $_POST['comentario'] ?? '';
    
    if ($puntuacion < 1 || $puntuacion > 10) {
        $error = "La puntuación debe estar entre 1 y 10";
    } else {
        $exito = $cochesDao->actualizarValoracion($valoracionId, $puntuacion, $comentario);
        
        if ($exito) {
            header('Location: perfil.php?success=editada');
            exit;
        } else {
            $error = "Error al actualizar la valoración";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Valoración - CarReviews</title>
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

        .modelo-info {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .modelo-info h2 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .modelo-info p {
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

        input, textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-editar {
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

        .btn-editar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
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
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .btn-cancelar:hover {
            background: #718096;
            transform: translateY(-2px);
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
            <p>Editar Valoración</p>
        </div>

        <div class="modelo-info">
            <h2><?= htmlspecialchars($marca->getNombre()) ?> - <?= htmlspecialchars($modelo->getNombre()) ?></h2>
            <p>Modelo del año <?= $modelo->getAnno()->format('Y') ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="puntuacion">Puntuación (1-10)</label>
                <input type="number" name="puntuacion" id="puntuacion" min="1" max="10" required 
                       value="<?= htmlspecialchars($valoracion->getPuntuacion()) ?>"
                       placeholder="Escribe una puntuación del 1 al 10">
            </div>

            <div class="form-group">
                <label for="comentario">Comentario (opcional)</label>
                <textarea name="comentario" id="comentario" rows="4" 
                          placeholder="Comparte tu experiencia con este vehículo..."><?= htmlspecialchars($valoracion->getComentario() ?? '') ?></textarea>
            </div>

            <button type="submit" name="editar" class="btn-editar">
                Actualizar Valoración
            </button>
        </form>

        <div class="nav-links">
            <a href="perfil.php">← Volver al Perfil</a>
            <a href="index.php">🏠 Ir al Inicio</a>
        </div>
    </div>
</body>
</html>