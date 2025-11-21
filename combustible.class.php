<?php
class Combustible {
    private int $id;
    private int $autonomia;
    private tipoCombustible $combustible;

    // Constructor
    public function __construct(int $id, int $autonomia, tipoCombustible $combustible) {
        $this->id = $id;
        $this->autonomia = $autonomia;
        $this->combustible = $combustible;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getAutonomia(): int {
        return $this->autonomia;
    }

    public function getCombustible(): tipoCombustible {
        return $this->combustible;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setAutonomia(int $autonomia): void {
        $this->autonomia = $autonomia;
    }

    public function setCombustible(tipoCombustible $combustible): void {
        $this->combustible = $combustible;
    }
}
?>
