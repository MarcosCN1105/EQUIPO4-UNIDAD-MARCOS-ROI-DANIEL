<?php
require_once 'conexion.class.php';
require_once 'cochesDao.class.php';

header('Content-Type: application/json');

if (isset($_GET['marca_id'])) {
    $marcaId = (int)$_GET['marca_id'];
    $cochesDao = new cochesDao();
    $modelos = $cochesDao->getModelos($marcaId);
    
    $modelosArray = [];
    foreach ($modelos as $modelo) {
        $modelosArray[] = [
            'id' => $modelo->getId(),
            'nombre' => $modelo->getNombre(),
            'anno' => $modelo->getAnno()->format('Y')
        ];
    }
    
    echo json_encode($modelosArray);
} else {
    echo json_encode([]);
}
?>