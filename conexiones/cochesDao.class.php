<?php 
require_once("marca.class.php");
require_once("modelo.class.php");

class cochesDao{
    public function getMarcas(){
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM marcas");
        $marcas = [];
        while($fila=$resultado->fetch(PDO::FETCH_OBJ)){
            $marca = new Marca(
                $fila->id,
                $fila->nombre
            );
            $marcas[] = $marca;
        }
        return $marcas;
    }

    public function getModelos($marcaId){
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->prepare("SELECT * FROM modelos WHERE marca_id = ?");
        $resultado->execute([$marcaId]);
        $modelos = [];
        while($fila=$resultado->fetch(PDO::FETCH_OBJ)){
            $modelo = new Modelo(
                $fila->id,
                $fila->marca_id,
                $fila->nombre,
                new DateTime($fila->anno)
            );
            $modelos[] = $modelo;
        }
        return $modelos;
    }
}
?>