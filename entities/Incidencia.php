<?php
class Incidencia
{
    private int $id;
    private int $pedido_id;
    private string $incidencia;
    private string $numero_pedido;
    private string $fecha_hora;
    private string $username;

    public function __construct(int $id, int $pedido_id, string $incidencia, string $numero_pedido = '', string $fecha_hora = '', string $username = '')
    {
        $this->id = $id;
        $this->pedido_id = $pedido_id;
        $this->incidencia = $incidencia;
        $this->numero_pedido = $numero_pedido;
        $this->fecha_hora = $fecha_hora;
        $this->username = $username;
    }

    public function getId(): int { return $this->id; }
    public function getPedidoId(): int { return $this->pedido_id; }
    public function getIncidencia(): string { return $this->incidencia; }
    public function getNumeroPedido(): string { return $this->numero_pedido; }
    public function getFechaHora(): string { return $this->fecha_hora; }
    public function getUsername(): string { return $this->username; }
}
