<?php

class Valoracion {

    private $id;
    private $modelo_id;
    private $usuario_id;
    private $puntuacion;
    private $comentario;
    private $fecha;

    public function __construct($id, $modelo_id, $usuario_id, $puntuacion, $comentario, $fecha) {
        $this->id = $id;
        $this->modelo_id = $modelo_id;
        $this->usuario_id = $usuario_id;
        $this->puntuacion = $puntuacion;
        $this->comentario = $comentario;
        $this->fecha = $fecha;
    }

    /* Getters */

    public function getId() {
        return $this->id;
    }

    public function getModeloId() {
        return $this->modelo_id;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function getPuntuacion() {
        return $this->puntuacion;
    }

    public function getComentario() {
        return $this->comentario;
    }

    public function getFecha() {
        return $this->fecha;
    }

    /* Setters */

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setModeloId($modelo_id) {
        $this->modelo_id = $modelo_id;
        return $this;
    }

    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
        return $this;
    }

    public function setPuntuacion($puntuacion) {
        $this->puntuacion = $puntuacion;
        return $this;
    }

    public function setComentario($comentario) {
        $this->comentario = $comentario;
        return $this;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
        return $this;
    }
}
