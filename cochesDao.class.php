<?php
require_once("conexion.class.php");
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
    // --- MARCAS ---
    public function getMarcas()
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM marcas");
        $marcas = [];
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $marcas[] = new Marca($fila->id, $fila->nombre);
        }
        return $marcas;
    }

    public function getMarcaPorId(int $marcaId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT * FROM marcas WHERE id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$marcaId]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);
        return $fila ? new Marca($fila->id, $fila->nombre) : null;
    }

    // --- MODELOS ---
    public function getModelos($marcaId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $stmt = $conexion->prepare("SELECT * FROM modelos WHERE marca_id = ?");
        $stmt->execute([$marcaId]);
        $modelos = [];
        while ($fila = $stmt->fetch(PDO::FETCH_OBJ)) {
            $modelos[] = new Modelo(
                $fila->id,
                $fila->marca_id,
                $fila->nombre,
                new DateTime($fila->anno),
                $fila->imagen_url
            );
        }
        return $modelos;
    }

    public function getModeloCompleto(int $modeloId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT * FROM modelos WHERE id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$modeloId]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);
        return $fila ? new Modelo($fila->id, $fila->marca_id, $fila->nombre, new DateTime($fila->anno), $fila->imagen_url) : null;
    }

    public function getTipoVehiculoPorModelo(int $modeloId): ?TipoVehiculo
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = $conexion->prepare(
            "SELECT t.tipo_vehiculo 
             FROM tipovehiculo t
             INNER JOIN modelos m ON t.id = m.tipovehiculo_id
             WHERE m.id = ?"
        );
        $consulta->execute([$modeloId]);
        $fila = $consulta->fetch(PDO::FETCH_OBJ);
        return $fila ? TipoVehiculo::from($fila->tipo_vehiculo) : null;
    }

    // --- COMBUSTIBLES ---
    public function getCombustibles()
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM combustibles");
        $combustibles = [];
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $combustibles[] = new Combustible($fila->id, $fila->autonomia, tipoCombustible::from($fila->tipo));
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
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$modeloId]);
        $combustibles = [];
        while ($fila = $stmt->fetch(PDO::FETCH_OBJ)) {
            $combustibles[] = new Combustible($fila->id, $fila->autonomia, tipoCombustible::from($fila->tipo));
        }
        return $combustibles;
    }

    // --- USUARIOS ---
    public function getUsuarios()
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $resultado = $conexion->query("SELECT * FROM usuarios");
        $usuarios = [];
        while ($fila = $resultado->fetch(PDO::FETCH_OBJ)) {
            $usuarios[] = new Usuario($fila->id, $fila->nombre, $fila->contrasena);
        }
        return $usuarios;
    }

    // --- VALORACIONES ---
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
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$modeloId]);
        $valoraciones = [];
        while ($fila = $stmt->fetch(PDO::FETCH_OBJ)) {
            $valoraciones[] = new Valoracion(
                $fila->id, 
                $fila->modelo_id, 
                $fila->usuario_id, 
                $fila->puntuacion, 
                $fila->comentario, 
                new DateTime($fila->fecha),
                $fila->usuario_nombre  // Pasar el nombre del usuario
            );
        }
        return $valoraciones;
    }

    public function getValoracionesPorUsuario(int $usuarioId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "
            SELECT v.*, m.nombre as modelo_nombre, mar.nombre as marca_nombre, u.nombre as usuario_nombre
            FROM valoraciones v
            INNER JOIN modelos m ON v.modelo_id = m.id
            INNER JOIN marcas mar ON m.marca_id = mar.id
            INNER JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.usuario_id = ?
            ORDER BY v.fecha DESC
        ";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$usuarioId]);
        $valoraciones = [];
        while ($fila = $stmt->fetch(PDO::FETCH_OBJ)) {
            $valoraciones[] = new Valoracion(
                $fila->id, 
                $fila->modelo_id, 
                $fila->usuario_id, 
                $fila->puntuacion, 
                $fila->comentario, 
                new DateTime($fila->fecha),
                $fila->usuario_nombre
            );
        }
        return $valoraciones;
    }

    public function getValoracionUsuarioModelo(int $usuarioId, int $modeloId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT v.*, u.nombre as usuario_nombre FROM valoraciones v 
                 INNER JOIN usuarios u ON v.usuario_id = u.id 
                 WHERE v.usuario_id = ? AND v.modelo_id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$usuarioId, $modeloId]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);
        return $fila ? new Valoracion(
            $fila->id, 
            $fila->modelo_id, 
            $fila->usuario_id, 
            $fila->puntuacion, 
            $fila->comentario, 
            new DateTime($fila->fecha),
            $fila->usuario_nombre
        ) : null;
    }

    public function usuarioYaValoroModelo(int $usuarioId, int $modeloId): bool
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT COUNT(*) as total FROM valoraciones WHERE usuario_id = ? AND modelo_id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$usuarioId, $modeloId]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);
        return $fila->total > 0;
    }

    public function getValoracionPorId(int $valoracionId)
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT v.*, u.nombre as usuario_nombre FROM valoraciones v 
                 INNER JOIN usuarios u ON v.usuario_id = u.id 
                 WHERE v.id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$valoracionId]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);
        return $fila ? new Valoracion(
            $fila->id, 
            $fila->modelo_id, 
            $fila->usuario_id, 
            $fila->puntuacion, 
            $fila->comentario, 
            new DateTime($fila->fecha),
            $fila->usuario_nombre
        ) : null;
    }

    public function agregarValoracion(int $modeloId, int $usuarioId, int $puntuacion, string $comentario): bool
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "INSERT INTO valoraciones (modelo_id, usuario_id, puntuacion, comentario, fecha) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conexion->prepare($consulta);
        return $stmt->execute([$modeloId, $usuarioId, $puntuacion, $comentario]);
    }

    public function actualizarValoracion(int $valoracionId, int $puntuacion, string $comentario): bool
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "UPDATE valoraciones SET puntuacion = ?, comentario = ?, fecha = NOW() WHERE id = ?";
        $stmt = $conexion->prepare($consulta);
        return $stmt->execute([$puntuacion, $comentario, $valoracionId]);
    }

    public function eliminarValoracion(int $valoracionId): bool
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "DELETE FROM valoraciones WHERE id = ?";
        $stmt = $conexion->prepare($consulta);
        return $stmt->execute([$valoracionId]);
    }

    public function getPuntuacionMedia(int $modeloId): float
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $consulta = "SELECT AVG(puntuacion) as media FROM valoraciones WHERE modelo_id = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$modeloId]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);
        return $fila ? round($fila->media, 1) : 0.0;
    }

    // --- CHECK SI LA VALORACION PERTENECE AL USUARIO ---
    public function perteneceUsuario(int $valoracionId, int $usuarioId): bool
    {
        $conexion = Conexion::getInstancia()->getConexion();
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM valoraciones WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$valoracionId, $usuarioId]);
        return $stmt->fetchColumn() > 0;
    }
}