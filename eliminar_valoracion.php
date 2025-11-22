<?php
session_start();
require_once 'cochesDao.class.php';

$cochesDao = new cochesDao();
$usuarioId = $_SESSION['usuario_id'] ?? null;
$id = $_GET['id'] ?? null;

if (!$usuarioId || !$id) {
    header("Location: perfil.php?error=no_pertenece");
    exit;
}

// Revisar si la valoración está en session["crear"]
if (isset($_SESSION["crear"])) {
    foreach ($_SESSION["crear"] as $key => $valoracion) {
        if ($valoracion["id_temp"] === $id) {
            unset($_SESSION["crear"][$key]);
            $_SESSION["crear"] = array_values($_SESSION["crear"]);
            header("Location: perfil.php?success=eliminada_sesion");
            exit;
        }
    }
}

// Si no está en sesión, eliminar en la base de datos
if ($cochesDao->perteneceUsuario($id, $usuarioId)) {
    $cochesDao->eliminarValoracion($id);
    header("Location: perfil.php?success=eliminada");
    exit;
}

header("Location: perfil.php?error=no_pertenece");
