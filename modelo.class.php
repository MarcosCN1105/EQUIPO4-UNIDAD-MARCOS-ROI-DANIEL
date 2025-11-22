<?php
declare(strict_types=1);

class Modelo
{
    private int $id;
    private int $marcaId;
    private string $nombre;
    private DateTime $anno;   // formato 'YYYY-MM-DD'
    private string $img;

    public function __construct(int $id, int $marcaId, string $nombre, DateTime $anno, string $img)
    {
        $this->id = $id;
        $this->marcaId = $marcaId;
        $this->nombre = $nombre;
        $this->anno = $anno;   
        $this->img = $img;
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

    public function getImg()
    {
        return $this->img;
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
   
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }
}
