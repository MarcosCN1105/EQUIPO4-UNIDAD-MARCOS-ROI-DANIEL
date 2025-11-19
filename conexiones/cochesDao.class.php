<?php

require_once("marca.class.php");
require_once("modelo.class.php");

class cochesDao
{
    public function getMarcas()
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM marcas");
        $marcas = [];
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $marca = new Marca(
                $fila->id,
                $fila->nombre
            );
            $marcas[] = $marca;
        }
        return $marcas;
    }

    public function getModelos($marcaId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->prepare("SELECT * FROM modelos WHERE marca_id = ?");
        $resultado->execute([$marcaId]);
        $modelos = [];
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
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

    public function getCombustibles()
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM combustibles");
        $combustibles = [];

        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $combustible = new Combustible(
                $fila->id,
                $fila->autonomia,
                TipoGasolina::from($fila->tipo)
            );
            $combustibles[] = $combustible;
        }

        return $combustibles;
    }

    public function getCombustiblesPorModelo(int $modeloId)
    {
        $conexion = Conexion::getInstancia()->getConexion();

        $consulta = "
        SELECT c.*
        FROM combustibles c
        INNER JOIN modelo_combustible mc ON c.id = mc.combustible_id
        WHERE mc.modelo_id = ?
    ";

        $resultado = $conexion->prepare($consulta);
        $resultado->execute([$modeloId]);

        $combustibles = [];

        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $combustible = new Combustible(
                $fila->id,
                $fila->autonomia,
                TipoGasolina::from($fila->tipo)
            );
            $combustibles[] = $combustible;
        }

        return $combustibles;
    }


    public function getTipoVehiculoPorModelo(int $modeloId): ?TipoVehiculo {
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = $conexion->prepare(
        "SELECT t.tipo_vehiculo 
         FROM tipovehiculo t
         INNER JOIN modelos m ON t.id = m.tipovehiculo_id
         WHERE m.id = ?"
    );
    $consulta->execute([$modeloId]);
    $fila = $consulta->fetch(PDO::FETCH_OBJ);

    if ($fila) {
        return TipoVehiculo::from($fila->tipo_vehiculo);
    }

    return null; // Si no existe el modelo
}


}
