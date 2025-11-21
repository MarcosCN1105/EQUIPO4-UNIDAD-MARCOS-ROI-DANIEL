<?php
require_once("conexion.class.php"); // ya está en la misma carpeta, correcto

class ValoracionesDao
{
    public function getValoracionesPorModelo(int $modeloId): array
    {
        $db = Conexion::getInstancia()->getConexion();
        $sql = "SELECT v.*, u.nombre AS usuario
                FROM valoraciones v
                INNER JOIN usuarios u ON v.usuario_id = u.id
                WHERE modelo_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$modeloId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarValoracion(int $modeloId, int $usuarioId, int $puntuacion, string $comentario): bool
    {
        $db = Conexion::getInstancia()->getConexion();
        try {
            $sql = "INSERT INTO valoraciones (modelo_id, usuario_id, puntuacion, comentario, fecha)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$modeloId, $usuarioId, $puntuacion, $comentario]);
        } catch (PDOException $e) {
            return false; // User ya ha valorado ese modelo
        }
    }
}
