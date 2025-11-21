<?php
require_once 'conexion.class.php';
require_once 'cochesDao.class.php';

if ($_POST['modelo_id'] && $_POST['usuario_id'] && $_POST['puntuacion']) {
    $cochesDao = new cochesDao();
    
    $modeloId = (int)$_POST['modelo_id'];
    $usuarioId = (int)$_POST['usuario_id'];
    $puntuacion = (int)$_POST['puntuacion'];
    $comentario = $_POST['comentario'] ?? '';
    
    $exito = $cochesDao->agregarValoracion($modeloId, $usuarioId, $puntuacion, $comentario);
    
    if ($exito) {
        header('Location: index.php?success=1');
    } else {
        header('Location: index.php?error=1');
    }
    exit;
}

header('Location: index.php');