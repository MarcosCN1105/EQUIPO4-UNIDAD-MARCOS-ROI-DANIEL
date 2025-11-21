<?php
class Usuario {
    private int $id;
    private string $nombre;
    private string $contrasena;

    public function __construct(int $id, string $nombre, string $contrasena) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->contrasena = $contrasena;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getContrasena(): string {
        return $this->contrasena;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setContrasena(string $contrasena): void {
        $this->contrasena = $contrasena;
    }
}
?>