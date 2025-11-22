<?php
session_start();
require_once 'cochesDao.class.php';

$cochesDao = new cochesDao();
$usuarioId = $_SESSION['usuario_id'] ?? null;

if (!$usuarioId) {
    header("Location: login.php");
    exit;
}

// Recoger datos del formulario
$modeloId = $_POST['modelo_id'] ?? null;
$puntuacion = $_POST['puntuacion'] ?? null;
$comentario = $_POST['comentario'] ?? '';

if ($modeloId && $puntuacion !== null) {
    // Crear sesión "crear" si no existe
    if (!isset($_SESSION["crear"])) {
        $_SESSION["crear"] = [];
    }

    // Añadir valoracion a la sesión
    $_SESSION["crear"][] = [
        "id_temp" => uniqid(), // identificador temporal
        "usuario_id" => $usuarioId,
        "modelo_id" => $modeloId,
        "puntuacion" => $puntuacion,
        "comentario" => $comentario,
        "fecha" => date("Y-m-d H:i:s")
    ];

    header("Location: perfil.php?success=valoracion_guardada_sesion");
    exit;
}
header("Location: perfil.php?error=datos_incompletos");
