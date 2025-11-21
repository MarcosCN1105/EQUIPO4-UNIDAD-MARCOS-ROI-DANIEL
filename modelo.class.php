<?php
declare(strict_types=1);

class Modelo
{
    private int $id;
    private int $marcaId;
    private string $nombre;
    private DateTime $anno;   // formato 'YYYY-MM-DD'

    public function __construct(int $id, int $marcaId, string $nombre, DateTime $anno)
    {
        $this->id = $id;
        $this->marcaId = $marcaId;
        $this->nombre = $nombre;
        $this->anno = $anno;   
    }

    // --- Getters ---
    public function getId(): int
    {
        return $this->id;
    }

    public function getMarcaId(): int
    {
        return $this->marcaId;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getAnno(): DateTime
    {
        return $this->anno;
    }


    // --- Setters ---
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setMarcaId(int $marcaId): void
    {
        $this->marcaId = $marcaId;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

     public function setAnno(DateTime $anno): void
    {
        $this->anno = $anno;
    }
   
}
