<?php 

class Usuario{
    private $id;
    private $nombre;
    private $contrasena;

    public function __construct($id, $nombre, $contrasena) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->contrasena = $contrasena;
    }

    /* Getters */
    public function getId()
    {
        return $this->id;
    }

    
    public function getNombre()
    {
        return $this->nombre;
    }

    
    public function getContrasena()
    {
        return $this->contrasena;
    }


    /* Setters */

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;

        return $this;
    }
}