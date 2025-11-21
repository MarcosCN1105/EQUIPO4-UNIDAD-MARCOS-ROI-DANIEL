<?php
class modeloCombustible {
    private int $modelo_id;
    private int $combustible_id;

    // Constructor
    public function __construct(int $modelo_id, int $combustible_id) {
        $this->modelo_id = $modelo_id;
        $this->combustible_id = $combustible_id;
    }

    // Getters
    public function getModeloId(): int {
        return $this->modelo_id;
    }

    public function getCombustibleId(): int {
        return $this->combustible_id;
    }

    // Setters
    public function setModeloId(int $modelo_id): void {
        $this->modelo_id = $modelo_id;
    }

    public function setCombustibleId(int $combustible_id): void {
        $this->combustible_id = $combustible_id;
    }
}
?>
