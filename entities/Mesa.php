<?php

class Mesa
{
    private int $id;
    private int $numeroMesa;
    private int $capacidadOcupantes;
    private bool $ocupada;

    public function __construct(int $id, int $numeroMesa, int $capacidadOcupantes, bool $ocupada)
    {
        $this->id = $id;
        $this->numeroMesa = $numeroMesa;
        $this->capacidadOcupantes = $capacidadOcupantes;
        $this->ocupada = $ocupada;
    }

    public function getId(): int { return $this->id; }
    public function getNumeroMesa(): int { return $this->numeroMesa; }
    public function getCapacidadOcupantes(): int { return $this->capacidadOcupantes; }
    public function isOcupada(): bool { return $this->ocupada; }
}
