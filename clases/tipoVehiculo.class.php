<?php
class VehiculoTipo {
    private int $id;
    private TipoVehiculo $tipo;

    // Constructor
    public function __construct(int $id, TipoVehiculo $tipo) {
        $this->id = $id;
        $this->tipo = $tipo;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getTipo(): TipoVehiculo {
        return $this->tipo;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setTipo(TipoVehiculo $tipo): void {
        $this->tipo = $tipo;
    }
}
?>
