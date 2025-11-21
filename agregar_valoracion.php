<?php
session_start();

// Verificar que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexion.class.php';
require_once 'cochesDao.class.php';

if ($_POST['modelo_id'] && $_POST['puntuacion']) {
    $cochesDao = new cochesDao();
    
    $modeloId = (int)$_POST['modelo_id'];
    $usuarioId = (int)$_SESSION['usuario_id'];
    $puntuacion = (int)$_POST['puntuacion'];
    $comentario = $_POST['comentario'] ?? '';
    
    // Verificar si ya valoró este modelo
    if ($cochesDao->usuarioYaValoroModelo($usuarioId, $modeloId)) {
        header('Location: index.php?error=ya_valorado');
        exit;
    }
    
    $exito = $cochesDao->agregarValoracion($modeloId, $usuarioId, $puntuacion, $comentario);
    
    if ($exito) {
        header('Location: index.php?success=1');
    } else {
        header('Location: index.php?error=1');
    }
    exit;
}

header('Location: index.php');