<?php
session_start();

require_once 'conexion.class.php';
require_once 'cochesDao.class.php';

$cochesDao = new cochesDao();
$usuarioId = $_SESSION['usuario_id'] ?? null;

if (!$usuarioId) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: perfil.php?error=no_pertenece");
    exit;
}

// Determinar si la valoración está en sesión o en BD
$valoracion = null;
$enSesion = false;

if (isset($_SESSION["crear"])) {
    foreach ($_SESSION["crear"] as &$valoracionTemp) {
        if ($valoracionTemp["id_temp"] == $id) {
            $valoracion = &$valoracionTemp;
            $enSesion = true;
            break;
        }
    }
}

if (!$enSesion) {
    $valoracion = $cochesDao->getValoracionPorId($id);
    if (!$valoracion || !$cochesDao->perteneceUsuario($id, $usuarioId)) {
        header("Location: perfil.php?error=no_pertenece");
        exit;
    }
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $puntuacion = isset($_POST['puntuacion']) ? (int)$_POST['puntuacion'] : null;
    $comentario = $_POST['comentario'] ?? '';

    if ($enSesion) {
        $valoracion['puntuacion'] = $puntuacion;
        $valoracion['comentario'] = $comentario;
        header("Location: perfil.php?success=editada_sesion");
        exit;
    } else {
        $cochesDao->actualizarValoracion($id, $puntuacion, $comentario);
        header("Location: perfil.php?success=editada");
        exit;
    }
}

// Para mostrar en el formulario
$puntuacionValoracion = $enSesion ? $valoracion['puntuacion'] : $valoracion->getPuntuacion();
$comentarioValoracion = $enSesion ? $valoracion['comentario'] : $valoracion->getComentario();

// Obtener info del modelo y marca
$modelo = $enSesion 
    ? $cochesDao->getModeloCompleto($valoracion['modelo_id']) 
    : $cochesDao->getModeloCompleto($valoracion->getModeloId());
$marca = $cochesDao->getMarcaPorId($modelo->getMarcaId());
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Valoración - CarReviews</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #333; margin:0; padding:0; }
        .container { max-width: 800px; margin: 50px auto; background:white; padding:30px; border-radius:20px; box-shadow:0 15px 35px rgba(0,0,0,0.1); }
        h1 { text-align:center; margin-bottom:30px; }
        label { display:block; margin:15px 0 5px; font-weight:600; }
        input[type=number], textarea { width:100%; padding:10px; border-radius:10px; border:1px solid #ccc; font-size:1rem; }
        button { margin-top:20px; padding:12px 25px; border:none; border-radius:10px; background:#667eea; color:white; font-size:1rem; cursor:pointer; }
        button:hover { background:#5a67d8; }
        .back-link { display:block; margin-top:15px; text-align:center; color:#4299e1; text-decoration:none; }
    </style>
</head>
<body>
<div class="container">
    <h1>Editar Valoración</h1>

    <p><strong>Modelo:</strong> <?= htmlspecialchars($marca->getNombre()) ?> - <?= htmlspecialchars($modelo->getNombre()) ?> (<?= $modelo->getAnno()->format('Y') ?>)</p>

    <form method="POST">
        <label for="puntuacion">Puntuación (1-10)</label>
        <input type="number" id="puntuacion" name="puntuacion" min="1" max="10" required value="<?= htmlspecialchars($puntuacionValoracion) ?>">

        <label for="comentario">Comentario</label>
        <textarea id="comentario" name="comentario" rows="5"><?= htmlspecialchars($comentarioValoracion) ?></textarea>

        <button type="submit">Guardar cambios</button>
    </form>

    <a class="back-link" href="perfil.php">← Volver a mi perfil</a>
</div>
</body>
</html>
