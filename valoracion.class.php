<?php
class Valoracion {
    private int $id;
    private int $modeloId;
    private int $usuarioId;
    private int $puntuacion;
    private ?string $comentario;
    private DateTime $fecha;

    public function __construct(int $id, int $modeloId, int $usuarioId, int $puntuacion, ?string $comentario, DateTime $fecha) {
        $this->id = $id;
        $this->modeloId = $modeloId;
        $this->usuarioId = $usuarioId;
        $this->puntuacion = $puntuacion;
        $this->comentario = $comentario;
        $this->fecha = $fecha;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getModeloId(): int {
        return $this->modeloId;
    }

    public function getUsuarioId(): int {
        return $this->usuarioId;
    }

    public function getPuntuacion(): int {
        return $this->puntuacion;
    }

    public function getComentario(): ?string {
        return $this->comentario;
    }

    public function getFecha(): DateTime {
        return $this->fecha;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setModeloId(int $modeloId): void {
        $this->modeloId = $modeloId;
    }

    public function setUsuarioId(int $usuarioId): void {
        $this->usuarioId = $usuarioId;
    }

    public function setPuntuacion(int $puntuacion): void {
        $this->puntuacion = $puntuacion;
    }

    public function setComentario(?string $comentario): void {
        $this->comentario = $comentario;
    }

    public function setFecha(DateTime $fecha): void {
        $this->fecha = $fecha;
    }
}
?>