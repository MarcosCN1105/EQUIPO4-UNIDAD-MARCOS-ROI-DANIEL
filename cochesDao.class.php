<?php
require_once("marca.class.php");
require_once("modelo.class.php");
require_once("combustible.class.php");
require_once("modeloCombustible.class.php");
require_once("tipoCombustible.enum.php");
require_once("tipoVehiculo.enum.php");
require_once("valoracion.class.php");
require_once("usuario.class.php");

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
                tipoCombustible::from($fila->tipo)
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
                tipoCombustible::from($fila->tipo)
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

        return null;
    }

    public function getValoracionesPorModelo(int $modeloId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "
            SELECT v.*, u.nombre as usuario_nombre 
            FROM valoraciones v 
            INNER JOIN usuarios u ON v.usuario_id = u.id 
            WHERE v.modelo_id = ? 
            ORDER BY v.fecha DESC
        ";
        
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([$modeloId]);
        $valoraciones = [];
        
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $valoracion = new Valoracion(
                $fila->id,
                $fila->modelo_id,
                $fila->usuario_id,
                $fila->puntuacion,
                $fila->comentario,
                new DateTime($fila->fecha)
            );
            $valoraciones[] = $valoracion;
        }
        
        return $valoraciones;
    }

    public function getModeloCompleto(int $modeloId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "
            SELECT m.*, mar.nombre as marca_nombre 
            FROM modelos m 
            INNER JOIN marcas mar ON m.marca_id = mar.id 
            WHERE m.id = ?
        ";
        
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([$modeloId]);
        $fila = $resultado->fetch(PDO::FETCH_OBJ);
        
        if ($fila) {
            return new Modelo(
                $fila->id,
                $fila->marca_id,
                $fila->nombre,
                new DateTime($fila->anno)
            );
        }
        
        return null;
    }

    public function agregarValoracion(int $modeloId, int $usuarioId, int $puntuacion, string $comentario): bool
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "
            INSERT INTO valoraciones (modelo_id, usuario_id, puntuacion, comentario, fecha) 
            VALUES (?, ?, ?, ?, NOW())
        ";
        
        $resultado = $conexion->prepare($consulta);
        return $resultado->execute([$modeloId, $usuarioId, $puntuacion, $comentario]);
    }

    public function getPuntuacionMedia(int $modeloId): float
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT AVG(puntuacion) as media FROM valoraciones WHERE modelo_id = ?";
        
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([$modeloId]);
        $fila = $resultado->fetch(PDO::FETCH_OBJ);
        
        return $fila ? round($fila->media, 1) : 0.0;
    }

    public function getUsuarios()
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM usuarios");
        $usuarios = [];
        
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $usuario = new Usuario(
                $fila->id,
                $fila->nombre,
                $fila->contrasena
            );
            $usuarios[] = $usuario;
        }
        
        return $usuarios;
    }

    public function getValoracionesPorUsuario(int $usuarioId)
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "
        SELECT v.*, m.nombre as modelo_nombre, mar.nombre as marca_nombre
        FROM valoraciones v 
        INNER JOIN modelos m ON v.modelo_id = m.id 
        INNER JOIN marcas mar ON m.marca_id = mar.id 
        WHERE v.usuario_id = ? 
        ORDER BY v.fecha DESC
    ";
    
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$usuarioId]);
    $valoraciones = [];
    
    while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
        $valoracion = new Valoracion(
            $fila->id,
            $fila->modelo_id,
            $fila->usuario_id,
            $fila->puntuacion,
            $fila->comentario,
            new DateTime($fila->fecha)
        );
        $valoraciones[] = $valoracion;
    }
    
    return $valoraciones;
}

public function getMarcaPorId(int $marcaId)
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "SELECT * FROM marcas WHERE id = ?";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$marcaId]);
    $fila = $resultado->fetch(PDO::FETCH_OBJ);
    
    if ($fila) {
        return new Marca($fila->id, $fila->nombre);
    }
    
    return null;
}

public function usuarioYaValoroModelo(int $usuarioId, int $modeloId): bool
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "SELECT COUNT(*) as total FROM valoraciones WHERE usuario_id = ? AND modelo_id = ?";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$usuarioId, $modeloId]);
    $fila = $resultado->fetch(PDO::FETCH_OBJ);
    
    return $fila->total > 0;
}

public function getValoracionUsuarioModelo(int $usuarioId, int $modeloId)
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "SELECT * FROM valoraciones WHERE usuario_id = ? AND modelo_id = ?";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$usuarioId, $modeloId]);
    $fila = $resultado->fetch(PDO::FETCH_OBJ);
    
    if ($fila) {
        return new Valoracion(
            $fila->id,
            $fila->modelo_id,
            $fila->usuario_id,
            $fila->puntuacion,
            $fila->comentario,
            new DateTime($fila->fecha)
        );
    }
    
    return null;
}

public function getValoracionPorId(int $valoracionId)
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "SELECT * FROM valoraciones WHERE id = ?";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$valoracionId]);
    $fila = $resultado->fetch(PDO::FETCH_OBJ);
    
    if ($fila) {
        return new Valoracion(
            $fila->id,
            $fila->modelo_id,
            $fila->usuario_id,
            $fila->puntuacion,
            $fila->comentario,
            new DateTime($fila->fecha)
        );
    }
    
    return null;
}

public function actualizarValoracion(int $valoracionId, int $puntuacion, string $comentario): bool
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "UPDATE valoraciones SET puntuacion = ?, comentario = ?, fecha = NOW() WHERE id = ?";
    $resultado = $conexion->prepare($consulta);
    return $resultado->execute([$puntuacion, $comentario, $valoracionId]);
}

public function eliminarValoracion(int $valoracionId): bool
{
    $conexion = Conexion::getInstancia()->getConexion();
    $consulta = "DELETE FROM valoraciones WHERE id = ?";
    $resultado = $conexion->prepare($consulta);
    return $resultado->execute([$valoracionId]);
}
}