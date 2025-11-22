<?php
session_start();
require_once 'cochesDao.class.php';

$cochesDao = new cochesDao();
$usuarioId = $_SESSION['usuario_id'] ?? null;

if ($usuarioId && isset($_SESSION['crear'])) {
    foreach ($_SESSION['crear'] as $valoracion) {
        // Verificar que los datos mínimos existan
        $modeloId   = $valoracion['modelo_id'] ?? null;
        $puntuacion = $valoracion['puntuacion'] ?? null;
        $comentario = $valoracion['comentario'] ?? '';

        if ($modeloId && $puntuacion !== null) {
            // Insertar en la base de datos
            $cochesDao->agregarValoracion($modeloId, $usuarioId, $puntuacion, $comentario);
        }
    }
}

// Destruir la sesión después de guardar las valoraciones
session_destroy();

// Redirigir al login con mensaje de logout
header('Location: login.php?logout=1');
exit;
